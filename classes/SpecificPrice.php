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

class SpecificPriceCore extends ObjectModel
{
    public $id_product;
    public $id_specific_price_rule = 0;
    public $id_cart = 0;
    public $id_product_attribute;
    public $id_shop;
    public $id_shop_group;
    public $id_currency;
    public $id_country;
    public $id_group;
    public $id_customer;
    public $price;
    public $from_quantity;
    public $reduction;
    public $reduction_tax = 1;
    public $reduction_type;
    public $from;
    public $to;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'specific_price',
        'primary' => 'id_specific_price',
        'fields' => array(
            'id_shop_group' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_specific_price_rule' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_country' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_group' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'price' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'from_quantity' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'reduction' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'reduction_tax' =>            array('type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true),
            'reduction_type' =>        array('type' => self::TYPE_STRING, 'validate' => 'isReductionType', 'required' => true),
            'from' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'to' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'specific_prices',
        'objectNodeName' => 'specific_price',
            'fields' => array(
            'id_shop_group' =>            array('xlink_resource' => 'shop_groups'),
            'id_shop' =>                array('xlink_resource' => 'shops', 'required' => true),
            'id_cart' =>                array('xlink_resource' => 'carts', 'required' => true),
            'id_product' =>            array('xlink_resource' => 'products', 'required' => true),
            'id_product_attribute' =>        array('xlink_resource' => 'product_attributes'),
            'id_currency' =>            array('xlink_resource' => 'currencies', 'required' => true),
            'id_country' =>            array('xlink_resource' => 'countries', 'required' => true),
            'id_group' =>                array('xlink_resource' => 'groups', 'required' => true),
            'id_customer' =>            array('xlink_resource' => 'customers', 'required' => true),
            ),
    );


    protected static $_specificPriceCache = array();
    protected static $_filterOutCache = array();
    protected static $_cache_priorities = array();
    protected static $_no_specific_values = array();

    /**
     * Flush local cache
     */
    protected function flushCache() {
        SpecificPrice::$_specificPriceCache = array();
        SpecificPrice::$_filterOutCache = array();
        SpecificPrice::$_cache_priorities = array();
        SpecificPrice::$_no_specific_values = array();
        Product::flushPriceCache();
    }

    public function add($autodate = true, $nullValues = false)
    {
        if (parent::add($autodate, $nullValues)) {
            // Flush cache when we adding a new specific price
            $this->flushCache();
            // Set cache of feature detachable to true
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', '1');
            return true;
        }
        return false;
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)) {
            // Flush cache when we updating a new specific price
            $this->flushCache();
            return true;
        }
        return false;
    }

    public function delete()
    {
        if (parent::delete()) {
            // Flush cache when we deletind a new specific price
            $this->flushCache();
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', SpecificPrice::isCurrentlyUsed($this->def['table']));
            return true;
        }
        return false;
    }

    public static function getByProductId($id_product, $id_product_attribute = false, $id_cart = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
			'SELECT *
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE 1 '.self::filterOutField('id_product', $id_product).
            ($id_product_attribute ? ' AND id_product_attribute = '.(int) $id_product_attribute : '').'
			AND id_cart = '.(int) $id_cart.'
            GROUP BY `id_specific_price_rule`'
        );
    }

    public static function deleteByIdCart($id_cart, $id_product = false, $id_product_attribute = false)
    {
        return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'specific_price`
			WHERE id_cart='.(int)$id_cart.
            ($id_product ? ' AND id_product='.(int)$id_product.' AND id_product_attribute='.(int)$id_product_attribute : ''));
    }

    public static function getIdsByProductId($id_product, $id_product_attribute = false, $id_cart = 0)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT `id_specific_price`
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE `id_product` = '.(int)$id_product.'
			AND id_product_attribute='.(int)$id_product_attribute.'
			AND id_cart='.(int)$id_cart);
    }

    /**
     * score generation for quantity discount
     */
    protected static function _getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer)
    {
        $select = '(';

        $priority = SpecificPrice::getPriority($id_product);
        foreach (array_reverse($priority) as $k => $field) {
            if (!empty($field)) {
                $select .= ' IF (`'.bqSQL($field).'` = '.(int)$$field.', '.pow(2, $k + 1).', 0) + ';
            }
        }

        return rtrim($select, ' +').') AS `score`';
    }

    public static function getPriority($id_product)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return explode(';', Configuration::get('PS_SPECIFIC_PRICE_PRIORITIES'));
        }

        if (!isset(SpecificPrice::$_cache_priorities[(int)$id_product])) {
            SpecificPrice::$_cache_priorities[(int)$id_product] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `priority`, `id_specific_price_priority`
				FROM `'._DB_PREFIX_.'specific_price_priority`
				WHERE `id_product` = '.(int)$id_product.'
				ORDER BY `id_specific_price_priority` DESC
			');
        }

        $priority = SpecificPrice::$_cache_priorities[(int)$id_product];

        if (!$priority) {
            $priority = Configuration::get('PS_SPECIFIC_PRICE_PRIORITIES');
        }
        $priority = 'id_customer;'.$priority;

        return preg_split('/;/', $priority);
    }

    /**
     * Remove or add a field value to a query if values are present in the database (cache friendly)
     *
     * @param string $field_name
     * @param int $field_value
     * @param int $threshold
     * @return string
     * @throws PrestaShopDatabaseException
     */
    protected static function filterOutField($field_name, $field_value, $threshold = 1000)
    {
        $query_extra = 'AND `'.$field_name.'` = 0 ';
        if ($field_value == 0 || array_key_exists($field_name, self::$_no_specific_values)) {
            return $query_extra;
        }
        $key_cache     = __FUNCTION__.'-'.$field_name.'-'.$threshold;
        $specific_list = array();
        if (!array_key_exists($key_cache, SpecificPrice::$_filterOutCache)) {
            $query_count    = 'SELECT COUNT(DISTINCT `'.$field_name.'`) FROM `'._DB_PREFIX_.'specific_price` WHERE `'.$field_name.'` != 0';
            $specific_count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_count);
            if ($specific_count == 0) {
                self::$_no_specific_values[$field_name] = true;

                return $query_extra;
            }
            if ($specific_count < $threshold) {
                $query             = 'SELECT DISTINCT `'.$field_name.'` FROM `'._DB_PREFIX_.'specific_price` WHERE `'.$field_name.'` != 0';
                $tmp_specific_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                foreach ($tmp_specific_list as $key => $value) {
                    $specific_list[] = $value[$field_name];
                }
            }
            SpecificPrice::$_filterOutCache[$key_cache] = $specific_list;
        } else {
            $specific_list = SpecificPrice::$_filterOutCache[$key_cache];
        }

        // $specific_list is empty if the threshold is reached
        if (empty($specific_list) || in_array($field_value, $specific_list)) {
            $query_extra = 'AND `'.$field_name.'` '.self::formatIntInQuery(0, $field_value).' ';
        }

        return $query_extra;
    }

    /**
     * Remove or add useless fields value depending on the values in the database (cache friendly)
     *
     * @param int|null $id_product
     * @param int|null $id_product_attribute
     * @param int|null $id_cart
     * @param string|null $beginning
     * @param string|null $ending
     * @return string
     */
    protected static function computeExtraConditions($id_product, $id_product_attribute, $id_customer, $id_cart, $beginning = null, $ending = null)
    {
        $first_date = date('Y-m-d 00:00:00');
        $last_date = date('Y-m-d 23:59:59');
        $now = date('Y-m-d H:i:00');
        if ($beginning === null) {
            $beginning = $now;
        }
        if ($ending === null) {
            $ending = $now;
        }
        $id_customer = (int)$id_customer;
        $id_cart = (int)$id_cart;

        $query_extra = '';

        if ($id_product !== null) {
            $query_extra .= self::filterOutField('id_product', $id_product);
        }

        if ($id_customer !== null) {
            $query_extra .= self::filterOutField('id_customer', $id_customer);
        }

        if ($id_product_attribute !== null) {
            $query_extra .= self::filterOutField('id_product_attribute', $id_product_attribute);
        }

        if ($id_cart !== null) {
            $query_extra .= self::filterOutField('id_cart', $id_cart);
        }

        if ($ending == $now && $beginning == $now) {
            $key = __FUNCTION__.'-'.$first_date.'-'.$last_date;
            if (!array_key_exists($key, SpecificPrice::$_filterOutCache)) {
                $query_from_count    = 'SELECT 1 FROM `'._DB_PREFIX_.'specific_price` WHERE `from` BETWEEN \''.$first_date.'\' AND \''.$last_date.'\'';
                $from_specific_count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_from_count);

                $query_to_count                       = 'SELECT 1 FROM `'._DB_PREFIX_.'specific_price` WHERE `to` BETWEEN \''.$first_date.'\' AND \''.$last_date.'\'';

                $to_specific_count                    = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_to_count);
                SpecificPrice::$_filterOutCache[$key] = array($from_specific_count, $to_specific_count);
            } else {
                list($from_specific_count, $to_specific_count) = SpecificPrice::$_filterOutCache[$key];
            }
        } else {
            $from_specific_count = $to_specific_count = 1;
        }

        // if the from and to is not reached during the current day, just change $ending & $beginning to any date of the day to improve the cache
        if (!$from_specific_count && !$to_specific_count) {
            $ending = $beginning = $first_date;
        }

        $query_extra .= ' AND (`from` = \'0000-00-00 00:00:00\' OR \''.$beginning.'\' >= `from`)'
                       .' AND (`to` = \'0000-00-00 00:00:00\' OR \''.$ending.'\' <= `to`)';

        return $query_extra;
    }

    private static function formatIntInQuery($first_value, $second_value) {
        $first_value = (int)$first_value;
        $second_value = (int)$second_value;
        if ($first_value != $second_value) {
            return 'IN ('.$first_value.', '.$second_value.')';
        } else {
            return ' = '.$first_value;
        }
    }

    public static function getSpecificPrice($id_product, $id_shop, $id_currency, $id_country, $id_group, $quantity, $id_product_attribute = null, $id_customer = 0, $id_cart = 0, $real_quantity = 0, $id_room = 0)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }
        /*
        ** The date is not taken into account for the cache, but this is for the better because it keeps the consistency for the whole script.
        ** The price must not change between the top and the bottom of the page
        */

        $key = ((int)$id_product.'-'.(int)$id_shop.'-'.(int)$id_currency.'-'.(int)$id_country.'-'.(int)$id_group.'-'.(int)$quantity.'-'.(int)$id_product_attribute.'-'.(int)$id_cart.'-'.(int)$id_customer.'-'.(int)$real_quantity);
        if (!array_key_exists($key, SpecificPrice::$_specificPriceCache)) {
            $query_extra = self::computeExtraConditions($id_product, $id_product_attribute, $id_customer, $id_cart);
            $query = '
			SELECT *, '.SpecificPrice::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer).'
				FROM `'._DB_PREFIX_.'specific_price`
				WHERE
                `id_shop` '.self::formatIntInQuery(0, $id_shop).' AND
                `id_currency` '.self::formatIntInQuery(0, $id_currency).' AND
                `id_country` '.self::formatIntInQuery(0, $id_country).' AND
                `id_group` '.self::formatIntInQuery(0, $id_group).' '.$query_extra.'
				AND IF(`from_quantity` > 1, `from_quantity`, 0) <= ';

            $query .= (Configuration::get('PS_QTY_DISCOUNT_ON_COMBINATION') || !$id_cart || !$real_quantity) ? (int)$quantity : max(1, (int)$real_quantity);
            $query .= ' ORDER BY `id_product_attribute` DESC, `from_quantity` DESC, `score` DESC, `to` DESC, `from` DESC';

            SpecificPrice::$_specificPriceCache[$key] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        }
        return SpecificPrice::$_specificPriceCache[$key];
    }

    public static function setPriorities($priorities)
    {
        $value = '';
        if (is_array($priorities)) {
            foreach ($priorities as $priority) {
                $value .= pSQL($priority).';';
            }
        }

        SpecificPrice::deletePriorities();

        return Configuration::updateValue('PS_SPECIFIC_PRICE_PRIORITIES', rtrim($value, ';'));
    }

    public static function deletePriorities()
    {
        return Db::getInstance()->execute('
		TRUNCATE `'._DB_PREFIX_.'specific_price_priority`
		');
    }

    public static function setSpecificPriority($id_product, $priorities)
    {
        $value = '';
        foreach ($priorities as $priority) {
            $value .= pSQL($priority).';';
        }

        return Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'specific_price_priority` (`id_product`, `priority`)
		VALUES ('.(int)$id_product.',\''.pSQL(rtrim($value, ';')).'\')
		ON DUPLICATE KEY UPDATE `priority` = \''.pSQL(rtrim($value, ';')).'\'
		');
    }

    public static function getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_product_attribute = null, $all_combinations = false, $id_customer = 0)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }

        $query_extra = self::computeExtraConditions($id_product, ((!$all_combinations)?$id_product_attribute:null), $id_customer, null);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *,
					'.SpecificPrice::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer).'
				FROM `'._DB_PREFIX_.'specific_price`
				WHERE
					`id_shop` '.self::formatIntInQuery(0, $id_shop).' AND
					`id_currency` '.self::formatIntInQuery(0, $id_currency).' AND
					`id_country` '.self::formatIntInQuery(0, $id_country).' AND
					`id_group` '.self::formatIntInQuery(0, $id_group).' '.$query_extra.'
					ORDER BY `from_quantity` ASC, `id_specific_price_rule` ASC, `score` DESC, `to` DESC, `from` DESC
		', false, false);

        $targeted_prices = array();
        $last_quantity = array();

        while ($specific_price = Db::getInstance()->nextRow($result)) {
            if (!isset($last_quantity[(int)$specific_price['id_product_attribute']])) {
                $last_quantity[(int)$specific_price['id_product_attribute']] = $specific_price['from_quantity'];
            } elseif ($last_quantity[(int)$specific_price['id_product_attribute']] == $specific_price['from_quantity']) {
                continue;
            }

            $last_quantity[(int)$specific_price['id_product_attribute']] = $specific_price['from_quantity'];
            if ($specific_price['from_quantity'] > 1) {
                $targeted_prices[] = $specific_price;
            }
        }

        return $targeted_prices;
    }

    public static function getQuantityDiscount($id_product, $id_shop, $id_currency, $id_country, $id_group, $quantity, $id_product_attribute = null, $id_customer = 0)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }



        $query_extra = self::computeExtraConditions($id_product, $id_product_attribute, $id_customer, null);
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT *,
					'.SpecificPrice::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer).'
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE
					`id_shop` '.self::formatIntInQuery(0, $id_shop).' AND
					`id_currency` '.self::formatIntInQuery(0, $id_currency).' AND
					`id_country` '.self::formatIntInQuery(0, $id_country).' AND
					`id_group` '.self::formatIntInQuery(0, $id_group).' AND
					`from_quantity` >= '.(int)$quantity.' '.$query_extra.'
					ORDER BY `from_quantity` DESC, `score` DESC, `to` DESC, `from` DESC
		');
    }

    public static function getProductIdByDate($id_shop, $id_currency, $id_country, $id_group, $beginning, $ending, $id_customer = 0, $with_combination_id = false)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }

        $query_extra = self::computeExtraConditions(null, null, $id_customer, null, $beginning, $ending);
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT `id_product`, `id_product_attribute`
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE	`id_shop` '.self::formatIntInQuery(0, $id_shop).' AND
					`id_currency` '.self::formatIntInQuery(0, $id_currency).' AND
					`id_country` '.self::formatIntInQuery(0, $id_country).' AND
					`id_group` '.self::formatIntInQuery(0, $id_group).' AND
					`from_quantity` = 1 AND
					`reduction` > 0
		'.$query_extra);
        $ids_product = array();
        foreach($results as $key => $value) {
            $ids_product[] = $with_combination_id ? array('id_product' => (int)$value['id_product'], 'id_product_attribute' => (int)$value['id_product_attribute']) : (int)$value['id_product'];
        }

        return $ids_product;
    }

    public static function deleteByProductId($id_product)
    {
        if (Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product` = '.(int)$id_product)) {
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', SpecificPrice::isCurrentlyUsed('specific_price'));
            return true;
        }
        return false;
    }

    public function duplicate($id_product = false)
    {
        if ($id_product) {
            $this->id_product = (int)$id_product;
        }
        unset($this->id);
        return $this->add();
    }

    /**
     * This method is allow to know if a feature is used or active
     * @since 1.5.0.1
     * @return bool
     */
    public static function isFeatureActive()
    {
        static $feature_active = null;

        if ($feature_active === null) {
            $feature_active = Configuration::get('PS_SPECIFIC_PRICE_FEATURE_ACTIVE');
        }
        return $feature_active;
    }

    public static function exists($id_product, $id_product_attribute, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, $rule = false)
    {
        $rule = ' AND `id_specific_price_rule`'.(!$rule ? '=0' : '!=0');
        return (int)Db::getInstance()->getValue('SELECT `id_specific_price`
												FROM '._DB_PREFIX_.'specific_price
												WHERE `id_product`='.(int)$id_product.' AND
													`id_product_attribute`='.(int)$id_product_attribute.' AND
													`id_shop`='.(int)$id_shop.' AND
													`id_group`='.(int)$id_group.' AND
													`id_country`='.(int)$id_country.' AND
													`id_currency`='.(int)$id_currency.' AND
													`id_customer`='.(int)$id_customer.' AND
													`from_quantity`='.(int)$from_quantity.' AND
													`from` >= \''.pSQL($from).'\' AND
													`to` <= \''.pSQL($to).'\''.$rule);
    }
}
