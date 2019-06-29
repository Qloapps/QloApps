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

class FeatureValueCore extends ObjectModel
{
    /** @var int Group id which attribute belongs */
    public $id_feature;

    /** @var string Name */
    public $value;

    /** @var bool Custom */
    public $custom = 0;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'feature_value',
        'primary' => 'id_feature_value',
        'multilang' => true,
        'fields' => array(
            'id_feature' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'custom' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

            /* Lang fields */
            'value' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
        ),
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'product_feature_values',
        'objectNodeName' => 'product_feature_value',
        'fields' => array(
            'id_feature' => array('xlink_resource'=> 'product_features'),
        ),
    );

    /**
     * Get all values for a given feature
     *
     * @param bool $id_feature Feature id
     * @return array Array with feature's values
     */
    public static function getFeatureValues($id_feature)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'feature_value`
			WHERE `id_feature` = '.(int)$id_feature
        );
    }

    /**
     * Get all values for a given feature and language
     *
     * @param int $id_lang Language id
     * @param bool $id_feature Feature id
     * @return array Array with feature's values
     */
    public static function getFeatureValuesWithLang($id_lang, $id_feature, $custom = false)
    {
        return Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'feature_value` v
			LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` vl
				ON (v.`id_feature_value` = vl.`id_feature_value` AND vl.`id_lang` = '.(int)$id_lang.')
			WHERE v.`id_feature` = '.(int)$id_feature.'
				'.(!$custom ? 'AND (v.`custom` IS NULL OR v.`custom` = 0)' : '').'
			ORDER BY vl.`value` ASC
		');
    }

    /**
     * Get all language for a given value
     *
     * @param bool $id_feature_value Feature value id
     * @return array Array with value's languages
     */
    public static function getFeatureValueLang($id_feature_value)
    {
        return Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'feature_value_lang`
			WHERE `id_feature_value` = '.(int)$id_feature_value.'
			ORDER BY `id_lang`
		');
    }

    /**
     * Select the good lang in tab
     *
     * @param array $lang Array with all language
     * @param int $id_lang Language id
     * @return string String value name selected
     */
    public static function selectLang($lang, $id_lang)
    {
        foreach ($lang as $tab) {
            if ($tab['id_lang'] == $id_lang) {
                return $tab['value'];
            }
        }
    }

    public static function addFeatureValueImport($id_feature, $value, $id_product = null, $id_lang = null, $custom = false)
    {
        $id_feature_value = false;
        if (!is_null($id_product) && $id_product) {
            $id_feature_value = Db::getInstance()->getValue('
				SELECT fp.`id_feature_value`
				FROM '._DB_PREFIX_.'feature_product fp
				INNER JOIN '._DB_PREFIX_.'feature_value fv USING (`id_feature_value`)
				WHERE fp.`id_feature` = '.(int)$id_feature.'
				AND fv.`custom` = '.(int)$custom.'
				AND fp.`id_product` = '.(int)$id_product);

            if ($custom && $id_feature_value && !is_null($id_lang) && $id_lang) {
                Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'feature_value_lang
				SET `value` = \''.pSQL($value).'\'
				WHERE `id_feature_value` = '.(int)$id_feature_value.'
				AND `value` != \''.pSQL($value).'\'
				AND `id_lang` = '.(int)$id_lang);
            }
        }

        if (!$custom) {
            $id_feature_value = Db::getInstance()->getValue('
				SELECT fv.`id_feature_value`
				FROM '._DB_PREFIX_.'feature_value fv
				LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.`id_feature_value` = fv.`id_feature_value` AND fvl.`id_lang` = '.(int)$id_lang.')
				WHERE `value` = \''.pSQL($value).'\'
				AND fv.`id_feature` = '.(int)$id_feature.'
				AND fv.`custom` = 0
				GROUP BY fv.`id_feature_value`');
        }

        if ($id_feature_value) {
            return (int)$id_feature_value;
        }

        // Feature doesn't exist, create it
        $feature_value = new FeatureValue();
        $feature_value->id_feature = (int)$id_feature;
        $feature_value->custom = (bool)$custom;
        $feature_value->value = array_fill_keys(Language::getIDs(false), $value);
        $feature_value->add();

        return (int)$feature_value->id;
    }

    public function add($autodate = true, $nullValues = false)
    {
        $return = parent::add($autodate, $nullValues);
        if ($return) {
            Hook::exec('actionFeatureValueSave', array('id_feature_value' => $this->id));
        }
        return $return;
    }

    public function delete()
    {
        /* Also delete related products */
        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'feature_product`
			WHERE `id_feature_value` = '.(int)$this->id
        );
        $return = parent::delete();

        if ($return) {
            Hook::exec('actionFeatureValueDelete', array('id_feature_value' => $this->id));
        }
        return $return;
    }

    public function update($nullValues = false)
    {
        $return = parent::update($nullValues);
        if ($return) {
            Hook::exec('actionFeatureValueSave', array('id_feature_value' => $this->id));
        }
        return $return;
    }
}
