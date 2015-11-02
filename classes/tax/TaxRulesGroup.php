<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


class TaxRulesGroupCore extends ObjectModel
{
    public $name;

    /** @var bool active state */
    public $active;

    public $deleted = 0;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'tax_rules_group',
        'primary' => 'id_tax_rules_group',
        'fields' => array(
            'name' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
            'active' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'deleted' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>    array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>    array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    protected $webserviceParameters = array(
    'objectsNodeName' => 'tax_rule_groups',
    'objectNodeName' => 'tax_rule_group',
        'fields' => array(
        ),
    );

    protected static $_taxes = array();


    public function update($null_values = false)
    {
        if (!$this->deleted && $this->isUsed()) {
            $current_tax_rules_group = new TaxRulesGroup((int)$this->id);
            if ((!$new_tax_rules_group = $current_tax_rules_group->duplicateObject()) || !$current_tax_rules_group->historize($new_tax_rules_group)) {
                return false;
            }

            $this->id = (int)$new_tax_rules_group->id;
        }

        return parent::update($null_values);
    }

    /**
     * Save the object with the field deleted to true
     *
     *  @return bool
     */
    public function historize(TaxRulesGroup $tax_rules_group)
    {
        $this->deleted = true;

        return parent::update() &&
        Db::getInstance()->execute('
		INSERT INTO '._DB_PREFIX_.'tax_rule
		(id_tax_rules_group, id_country, id_state, zipcode_from, zipcode_to, id_tax, behavior, description)
		(
			SELECT '.(int)$tax_rules_group->id.', id_country, id_state, zipcode_from, zipcode_to, id_tax, behavior, description
			FROM '._DB_PREFIX_.'tax_rule
			WHERE id_tax_rules_group='.(int)$this->id.'
		)') &&
        Db::getInstance()->execute('
		UPDATE '._DB_PREFIX_.'product
		SET id_tax_rules_group='.(int)$tax_rules_group->id.'
		WHERE id_tax_rules_group='.(int)$this->id) &&
        Db::getInstance()->execute('
		UPDATE '._DB_PREFIX_.'product_shop
		SET id_tax_rules_group='.(int)$tax_rules_group->id.'
		WHERE id_tax_rules_group='.(int)$this->id) &&
        Db::getInstance()->execute('
		UPDATE '._DB_PREFIX_.'carrier
		SET id_tax_rules_group='.(int)$tax_rules_group->id.'
		WHERE id_tax_rules_group='.(int)$this->id) &&
        Db::getInstance()->execute('
		UPDATE '._DB_PREFIX_.'carrier_tax_rules_group_shop
		SET id_tax_rules_group='.(int)$tax_rules_group->id.'
		WHERE id_tax_rules_group='.(int)$this->id);
    }

    public function getIdTaxRuleGroupFromHistorizedId($id_tax_rule)
    {
        $params = Db::getInstance()->getRow('
		SELECT id_country, id_state, zipcode_from, zipcode_to, id_tax, behavior
		FROM '._DB_PREFIX_.'tax_rule
		WHERE id_tax_rule='.(int)$id_tax_rule
        );

        return Db::getInstance()->getValue('
		SELECT id_tax_rule
		FROM '._DB_PREFIX_.'tax_rule
		WHERE
			id_tax_rules_group = '.(int)$this->id.' AND
			id_country='.(int)$params['id_country'].' AND id_state='.(int)$params['id_state'].' AND id_tax='.(int)$params['id_tax'].' AND
			zipcode_from=\''.pSQL($params['zipcode_from']).'\' AND zipcode_to=\''.pSQL($params['zipcode_to']).'\' AND behavior='.(int)$params['behavior']
        );
    }

    public static function getTaxRulesGroups($only_active = true)
    {
        return Db::getInstance()->executeS('
			SELECT DISTINCT g.id_tax_rules_group, g.name, g.active
			FROM `'._DB_PREFIX_.'tax_rules_group` g'
            .Shop::addSqlAssociation('tax_rules_group', 'g').' WHERE deleted = 0'
            .($only_active ? ' AND g.`active` = 1' : '').'
			ORDER BY name ASC');
    }

    /**
    * @return array an array of tax rules group formatted as $id => $name
    */
    public static function getTaxRulesGroupsForOptions()
    {
        $tax_rules[] = array('id_tax_rules_group' => 0, 'name' => Tools::displayError('No tax'));
        return array_merge($tax_rules, TaxRulesGroup::getTaxRulesGroups());
    }

    public function delete()
    {
        $res = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'tax_rule` WHERE `id_tax_rules_group`='.(int)$this->id);
        return (parent::delete() && $res);
    }
    /**
    * @return array
    */
    public static function getAssociatedTaxRatesByIdCountry($id_country)
    {
        $rows = Db::getInstance()->executeS('
			SELECT rg.`id_tax_rules_group`, t.`rate`
			FROM `'._DB_PREFIX_.'tax_rules_group` rg
			LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.`id_tax_rules_group` = rg.`id_tax_rules_group`)
			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
			WHERE tr.`id_country` = '.(int)$id_country.'
			AND tr.`id_state` = 0
			AND 0 between `zipcode_from` AND `zipcode_to`'
        );

        $res = array();
        foreach ($rows as $row) {
            $res[$row['id_tax_rules_group']] = $row['rate'];
        }

        return $res;
    }

    /**
    * Returns the tax rules group id corresponding to the name
    *
    * @param string $name
    * @return int id of the tax rules
    */
    public static function getIdByName($name)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_tax_rules_group`
			FROM `'._DB_PREFIX_.'tax_rules_group` rg
			WHERE `name` = \''.pSQL($name).'\''
        );
    }

    public function hasUniqueTaxRuleForCountry($id_country, $id_state, $id_tax_rule = false)
    {
        $rules = TaxRule::getTaxRulesByGroupId((int)Context::getContext()->language->id, (int)$this->id);
        foreach ($rules as $rule) {
            if ($rule['id_country'] == $id_country && $id_state == $rule['id_state'] && !$rule['behavior'] && (int)$id_tax_rule != $rule['id_tax_rule']) {
                return true;
            }
        }

        return false;
    }

    public function isUsed()
    {
        return Db::getInstance()->getValue('
		SELECT `id_tax_rules_group`
		FROM `'._DB_PREFIX_.'order_detail`
		WHERE `id_tax_rules_group` = '.(int)$this->id
        );
    }

    /**
    * @deprecated since 1.5
    */
    public static function getTaxesRate($id_tax_rules_group, $id_country, $id_state, $zipcode)
    {
        Tools::displayAsDeprecated();
        $rate = 0;
        foreach (TaxRulesGroup::getTaxes($id_tax_rules_group, $id_country, $id_state, $zipcode) as $tax) {
            $rate += (float)$tax->rate;
        }

        return $rate;
    }

    /**
     * Return taxes associated to this para
     * @deprecated since 1.5
     */
    public static function getTaxes($id_tax_rules_group, $id_country, $id_state, $id_county)
    {
        Tools::displayAsDeprecated();
        return array();
    }
}
