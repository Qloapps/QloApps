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

class GroupCore extends ObjectModel
{
    public $id;

    /** @var string Lastname */
    public $name;

    /** @var string Reduction */
    public $reduction;

    /** @var int Price display method (tax inc/tax exc) */
    public $price_display_method;

    /** @var bool Show prices */
    public $show_prices = 1;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'group',
        'primary' => 'id_group',
        'multilang' => true,
        'fields' => array(
            'reduction' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'price_display_method' =>    array('type' => self::TYPE_INT, 'validate' => 'isPriceDisplayMethod', 'required' => true),
            'show_prices' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            /* Lang fields */
            'name' =>                    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
        ),
    );

    protected static $cache_reduction = array();
    protected static $group_price_display_method = array();

    protected $webserviceParameters = array();

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        if ($this->id && !isset(Group::$group_price_display_method[$this->id])) {
            self::$group_price_display_method[$this->id] = $this->price_display_method;
        }
    }

    public static function getGroups($id_lang, $id_shop = false)
    {
        $shop_criteria = '';
        if ($id_shop) {
            $shop_criteria = Shop::addSqlAssociation('group', 'g');
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT g.`id_group`, g.`reduction`, g.`price_display_method`, gl.`name`
		FROM `'._DB_PREFIX_.'group` g
		LEFT JOIN `'._DB_PREFIX_.'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = '.(int)$id_lang.')
		'.$shop_criteria.'
		ORDER BY g.`id_group` ASC');
    }

    public function getCustomers($count = false, $start = 0, $limit = 0, $shop_filtering = false)
    {
        if ($count) {
            return Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'customer_group` cg
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (cg.`id_customer` = c.`id_customer`)
			WHERE cg.`id_group` = '.(int)$this->id.'
			'.($shop_filtering ? Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) : '').'
			AND c.`deleted` != 1');
        }
        return Db::getInstance()->executeS('
		SELECT cg.`id_customer`, c.*
		FROM `'._DB_PREFIX_.'customer_group` cg
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (cg.`id_customer` = c.`id_customer`)
		WHERE cg.`id_group` = '.(int)$this->id.'
		AND c.`deleted` != 1
		'.($shop_filtering ? Shop::addSqlRestriction(Shop::SHARE_CUSTOMER) : '').'
		ORDER BY cg.`id_customer` ASC
		'.($limit > 0 ? 'LIMIT '.(int)$start.', '.(int)$limit : ''));
    }

    public static function getReduction($id_customer = null)
    {
        if (!isset(self::$cache_reduction['customer'][(int)$id_customer])) {
            $id_group = $id_customer ? Customer::getDefaultGroupId((int)$id_customer) : (int)Group::getCurrent()->id;
            self::$cache_reduction['customer'][(int)$id_customer] = Group::getReductionByIdGroup($id_group);
        }
        return self::$cache_reduction['customer'][(int)$id_customer];
    }

    public static function getReductionByIdGroup($id_group)
    {
        if (!isset(self::$cache_reduction['group'][$id_group])) {
            self::$cache_reduction['group'][$id_group] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `reduction`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);
        }
        return self::$cache_reduction['group'][$id_group];
    }

    public static function getPriceDisplayMethodByGroup($id_group)
    {
        if (!isset(Group::$group_price_display_method[$id_group])) {
            self::$group_price_display_method[$id_group] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `price_display_method`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);
        }
        return self::$group_price_display_method[$id_group];
    }

    public static function getDefaultPriceDisplayMethod()
    {
        return Group::getPriceDisplayMethodByGroup((int)Configuration::get('PS_CUSTOMER_GROUP'));
    }

    public static function getPriceDisplayMethod($id_group)
    {
        if (Group::isFeatureActive()) {
            return Group::getPriceDisplayMethodByGroup($id_group);
        }
        return Group::getDefaultPriceDisplayMethod();
    }

    public function add($autodate = true, $null_values = false)
    {
        Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', '1');
        if (parent::add($autodate, $null_values)) {
            Category::assignGroupToAllCategories((int)$this->id);
            Carrier::assignGroupToAllCarriers((int)$this->id);
            return true;
        }
        return false;
    }

    public function update($autodate = true, $null_values = false)
    {
        if (!Configuration::getGlobalValue('PS_GROUP_FEATURE_ACTIVE') && $this->reduction > 0) {
            Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', 1);
        }
        return parent::update($autodate, $null_values);
    }

    public function delete()
    {
        if ($this->id == (int)Configuration::get('PS_CUSTOMER_GROUP')) {
            return false;
        }
        if (parent::delete()) {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_group` = '.(int)$this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_group` = '.(int)$this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE `id_group` = '.(int)$this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE `id_group` = '.(int)$this->id);
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_group_reduction_cache` WHERE `id_group` = '.(int)$this->id);
            $this->truncateModulesRestrictions($this->id);

            // Add default group (id 3) to customers without groups
            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'customer_group` (
				SELECT c.id_customer, '.(int)Configuration::get('PS_CUSTOMER_GROUP').' FROM `'._DB_PREFIX_.'customer` c
				LEFT JOIN `'._DB_PREFIX_.'customer_group` cg
				ON cg.id_customer = c.id_customer
				WHERE cg.id_customer IS NULL)');

            // Set to the customer the default group
            // Select the minimal id from customer_group
            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customer` cg
				SET id_default_group =
					IFNULL((
						SELECT min(id_group) FROM `'._DB_PREFIX_.'customer_group`
						WHERE id_customer = cg.id_customer),
						'.(int)Configuration::get('PS_CUSTOMER_GROUP').')
				WHERE `id_default_group` = '.(int)$this->id);

            return true;
        }
        return false;
    }

    /**
     * This method is allow to know if a feature is used or active
     * @since 1.5.0.1
     * @return bool
     */
    public static function isFeatureActive()
    {
        static $ps_group_feature_active = null;
        if ($ps_group_feature_active === null) {
            $ps_group_feature_active = Configuration::get('PS_GROUP_FEATURE_ACTIVE');
        }
        return $ps_group_feature_active;
    }

    /**
     * This method is allow to know if there are other groups than the default ones
     * @since 1.5.0.1
     * @param $table
     * @param $has_active_column
     * @return bool
     */
    public static function isCurrentlyUsed($table = null, $has_active_column = false)
    {
        return (bool)(Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'group`') > 3);
    }

    /**
     * Truncate all modules restrictions for the group
     *
     * @param int $id_group
     * @return bool
     */
    public static function truncateModulesRestrictions($id_group)
    {
        return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'module_group`
		WHERE `id_group` = '.(int)$id_group);
    }

    /**
     * Truncate all restrictions by module
     *
     * @param int $id_module
     * @return bool
     */
    public static function truncateRestrictionsByModule($id_module)
    {
        return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'module_group`
		WHERE `id_module` = '.(int)$id_module);
    }

    /**
     * Adding restrictions modules to the group with id $id_group
     * @param $id_group
     * @param $modules
     * @param array $shops
     * @return bool
     */
    public static function addModulesRestrictions($id_group, $modules, $shops = array(1))
    {
        if (!is_array($modules) || !count($modules) || !is_array($shops) || !count($shops)) {
            return false;
        }

        // Delete all record for this group
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_group` WHERE `id_group` = '.(int)$id_group);

        $sql = 'INSERT INTO `'._DB_PREFIX_.'module_group` (`id_module`, `id_shop`, `id_group`) VALUES ';
        foreach ($modules as $module) {
            foreach ($shops as $shop) {
                $sql .= '("'.(int)$module.'", "'.(int)$shop.'", "'.(int)$id_group.'"),';
            }
        }
        $sql = rtrim($sql, ',');

        return (bool)Db::getInstance()->execute($sql);
    }

    /**
     * Add restrictions for a new module.
     * We authorize every groups to the new module
     *
     * @param int $id_module
     * @param array $shops
     * @return bool
     */
    public static function addRestrictionsForModule($id_module, $shops = array(1))
    {
        if (!is_array($shops) || !count($shops)) {
            return false;
        }

        $res = true;
        foreach ($shops as $shop) {
            $res &= Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_group` (`id_module`, `id_shop`, `id_group`)
			(SELECT '.(int)$id_module.', '.(int)$shop.', id_group FROM `'._DB_PREFIX_.'group`)');
        }
        return $res;
    }

    /**
     * Return current group object
     * Use context
     *
     * @return Group Group object
     */
    public static function getCurrent()
    {
        static $groups = array();
        static $ps_unidentified_group = null;
        static $ps_customer_group = null;

        if ($ps_unidentified_group === null) {
            $ps_unidentified_group = Configuration::get('PS_UNIDENTIFIED_GROUP');
        }

        if ($ps_customer_group === null) {
            $ps_customer_group = Configuration::get('PS_CUSTOMER_GROUP');
        }

        $customer = Context::getContext()->customer;
        if (Validate::isLoadedObject($customer)) {
            $id_group = (int)$customer->id_default_group;
        } else {
            $id_group = (int)$ps_unidentified_group;
        }

        if (!isset($groups[$id_group])) {
            $groups[$id_group] = new Group($id_group);
        }

        if (!$groups[$id_group]->isAssociatedToShop(Context::getContext()->shop->id)) {
            $id_group = (int)$ps_customer_group;
            if (!isset($groups[$id_group])) {
                $groups[$id_group] = new Group($id_group);
            }
        }

        return $groups[$id_group];
    }

    /**
     * Light back office search for Group
     *
     * @param string $query Searched string
     * @return array Corresponding groups
     */
    public static function searchByName($query)
    {
        return Db::getInstance()->getRow('
			SELECT g.*, gl.*
			FROM `'._DB_PREFIX_.'group` g
			LEFT JOIN `'._DB_PREFIX_.'group_lang` gl
				ON (g.`id_group` = gl.`id_group`)
			WHERE `name` = \''.pSQL($query).'\'
		');
    }

    public function searchGroupByName($query)
    {
        return Db::getInstance()->executeS('
			SELECT g.*, gl.*
			FROM `'._DB_PREFIX_.'group` g
			LEFT JOIN `'._DB_PREFIX_.'group_lang` gl
				ON (g.`id_group` = gl.`id_group`)
			WHERE `name` LIKE \'%'.pSQL($query).'%\'
		');
    }

}
