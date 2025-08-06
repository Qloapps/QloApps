<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AttributeGroupCore extends ObjectModel
{
    /** @var string Name */
    public $name;
    public $is_color_group;
    public $position;
    public $group_type;

    /** @var string Public Name */
    public $public_name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'attribute_group',
        'primary' => 'id_attribute_group',
        'multilang' => true,
        'fields' => array(
            'is_color_group' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'group_type' =>    array('type' => self::TYPE_STRING, 'required' => true),
            'position' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            /* Lang fields */
            'name' =>            array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'public_name' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
        ),
    );


    protected $webserviceParameters = array(
        'objectsNodeName' => 'product_options',
        'objectNodeName' => 'product_option',
        'fields' => array(),
        'associations' => array(
            'product_option_values' => array(
                'resource' => 'product_option_value',
                'fields' => array(
                    'id' => array()
                ),
            ),
        ),
    );

    public function add($autodate = true, $nullValues = false)
    {
        if ($this->group_type == 'color') {
            $this->is_color_group = 1;
        } else {
            $this->is_color_group = 0;
        }

        if ($this->position <= 0) {
            $this->position = AttributeGroup::getHigherPosition() + 1;
        }

        $return = parent::add($autodate, true);
        Hook::exec('actionAttributeGroupSave', array('id_attribute_group' => $this->id));
        return $return;
    }

    public function update($nullValues = false)
    {
        if ($this->group_type == 'color') {
            $this->is_color_group = 1;
        } else {
            $this->is_color_group = 0;
        }

        $return = parent::update($nullValues);
        Hook::exec('actionAttributeGroupSave', array('id_attribute_group' => $this->id));
        return $return;
    }

    public static function cleanDeadCombinations()
    {
        $attribute_combinations = Db::getInstance()->executeS('
			SELECT pac.`id_attribute`, pa.`id_product_attribute`
			FROM `'._DB_PREFIX_.'product_attribute` pa
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		');
        $to_remove = array();
        foreach ($attribute_combinations as $attribute_combination) {
            if ((int)$attribute_combination['id_attribute'] == 0) {
                $to_remove[] = (int)$attribute_combination['id_product_attribute'];
            }
        }
        $return = true;
        if (!empty($to_remove)) {
            foreach ($to_remove as $remove) {
                $combination = new Combination($remove);
                $return &= $combination->delete();
            }
        }
        return $return;
    }

    public function delete()
    {
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {
            /* Select children in order to find linked combinations */
            $attribute_ids = Db::getInstance()->executeS('
				SELECT `id_attribute`
				FROM `'._DB_PREFIX_.'attribute`
				WHERE `id_attribute_group` = '.(int)$this->id
            );
            if ($attribute_ids === false) {
                return false;
            }
            /* Removing attributes to the found combinations */
            $to_remove = array();
            foreach ($attribute_ids as $attribute) {
                $to_remove[] = (int)$attribute['id_attribute'];
            }
            if (!empty($to_remove) && Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'product_attribute_combination`
				WHERE `id_attribute`
					IN ('.implode(', ', $to_remove).')') === false) {
                return false;
            }
            /* Remove combinations if they do not possess attributes anymore */
            if (!AttributeGroup::cleanDeadCombinations()) {
                return false;
            }
            /* Also delete related attributes */
            if (count($to_remove)) {
                if (!Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'attribute_lang`
				WHERE `id_attribute`	IN ('.implode(',', $to_remove).')') ||
                !Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'attribute_shop`
				WHERE `id_attribute`	IN ('.implode(',', $to_remove).')') ||
                !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)$this->id)) {
                    return false;
                }
            }
            $this->cleanPositions();
        }
        $return = parent::delete();
        if ($return) {
            Hook::exec('actionAttributeGroupDelete', array('id_attribute_group' => $this->id));
        }
        return $return;
    }

    /**
     * Get all attributes for a given language / group
     *
     * @param int $id_lang Language id
     * @param bool $id_attribute_group Attribute group id
     * @return array Attributes
     */
    public static function getAttributes($id_lang, $id_attribute_group)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }
        return Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'attribute` a
			'.Shop::addSqlAssociation('attribute', 'a').'
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
			WHERE a.`id_attribute_group` = '.(int)$id_attribute_group.'
			ORDER BY `position` ASC
		');
    }

    /**
     * Get all attributes groups for a given language
     *
     * @param int $id_lang Language id
     * @return array Attributes groups
     */
    public static function getAttributesGroups($id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        return Db::getInstance()->executeS('
			SELECT DISTINCT agl.`name`, ag.*, agl.*
			FROM `'._DB_PREFIX_.'attribute_group` ag
			'.Shop::addSqlAssociation('attribute_group', 'ag').'
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int)$id_lang.')
			ORDER BY `name` ASC
		');
    }

    /**
     * Delete several objects from database
     *
     * return boolean Deletion result
     */
    public function deleteSelection($selection)
    {
        /* Also delete Attributes */
        foreach ($selection as $value) {
            $obj = new AttributeGroup($value);
            if (!$obj->delete()) {
                return false;
            }
        }
        return true;
    }

    public function setWsProductOptionValues($values)
    {
        $ids = array();
        foreach ($values as $value) {
            $ids[] = intval($value['id']);
        }
        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'attribute`
			WHERE `id_attribute_group` = '.(int)$this->id.'
			AND `id_attribute` NOT IN ('.implode(',', $ids).')'
        );
        $ok = true;
        foreach ($values as $value) {
            $result = Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'attribute`
				SET `id_attribute_group` = '.(int)$this->id.'
				WHERE `id_attribute` = '.(int)$value['id']
            );
            if ($result === false) {
                $ok = false;
            }
        }
        return $ok;
    }

    public function getWsProductOptionValues()
    {
        $result = Db::getInstance()->executeS('
			SELECT a.id_attribute AS id
			FROM `'._DB_PREFIX_.'attribute` a
			'.Shop::addSqlAssociation('attribute', 'a').'
			WHERE a.id_attribute_group = '.(int)$this->id
        );
        return $result;
    }

    /**
     * Move a group attribute
     * @param bool $way Up (1)  or Down (0)
     * @param int $position
     * @return bool Update result
     */
    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT ag.`position`, ag.`id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute_group` ag
			WHERE ag.`id_attribute_group` = '.(int)Tools::getValue('id_attribute_group', 1).'
			ORDER BY ag.`position` ASC'
        )) {
            return false;
        }

        foreach ($res as $group_attribute) {
            if ((int)$group_attribute['id_attribute_group'] == (int)$this->id) {
                $moved_group_attribute = $group_attribute;
            }
        }

        if (!isset($moved_group_attribute) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute_group`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
                ? '> '.(int)$moved_group_attribute['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_group_attribute['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute_group`
			SET `position` = '.(int)$position.'
			WHERE `id_attribute_group`='.(int)$moved_group_attribute['id_attribute_group'])
        );
    }

    /**
     * Reorder group attribute position
     * Call it after deleting a group attribute.
     *
     * @return bool $return
     */
    public static function cleanPositions()
    {
        $return = true;

        $sql = '
			SELECT `id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute_group`
			ORDER BY `position`';
        $result = Db::getInstance()->executeS($sql);

        $i = 0;
        foreach ($result as $value) {
            $return = Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'attribute_group`
				SET `position` = '.(int)$i++.'
				WHERE `id_attribute_group` = '.(int)$value['id_attribute_group']
            );
        }
        return $return;
    }

    /**
     * getHigherPosition
     *
     * Get the higher group attribute position
     *
     * @return int $position
     */
    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
				FROM `'._DB_PREFIX_.'attribute_group`';
        $position = DB::getInstance()->getValue($sql);
        return (is_numeric($position)) ? $position : -1;
    }
}
