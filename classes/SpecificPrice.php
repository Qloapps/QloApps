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
    protected static $_cache_priorities = array();

    public function add($autodate = true, $nullValues = false)
    {
        if (parent::add($autodate, $nullValues)) {
            // Flush cache when we adding a new specific price
            SpecificPrice::$_specificPriceCache = array();
            Product::flushPriceCache();
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
            SpecificPrice::$_specificPriceCache = array();
            Product::flushPriceCache();
            return true;
        }
        return false;
    }

    public function delete()
    {
        if (parent::delete()) {
            // Flush cache when we deletind a new specific price
            SpecificPrice::$_specificPriceCache = array();
            Product::flushPriceCache();
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', SpecificPrice::isCurrentlyUsed($this->def['table']));
            return true;
        }
        return false;
    }

    public static function getByProductId($id_product, $id_product_attribute = false, $id_cart = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE `id_product` = '.(int)$id_product.
            ($id_product_attribute ? ' AND id_product_attribute = '.(int)$id_product_attribute : '').'
			AND id_cart = '.(int)$id_cart);
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

    public static function getSpecificPrice($id_product, $id_shop, $id_currency, $id_country, $id_group, $quantity, $id_product_attribute = null, $id_customer = 0, $id_cart = 0, $real_quantity = 0)
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
            $now = date('Y-m-d H:i:00');
            $query = '
			SELECT *, '.SpecificPrice::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer).'
				FROM `'._DB_PREFIX_.'specific_price` USE INDEX (id_product_2)
				WHERE `id_product` IN (0, '.(int)$id_product.')
				AND `id_product_attribute` IN (0, '.(int)$id_product_attribute.')
				AND `id_shop` IN (0, '.(int)$id_shop.')
				AND `id_currency` IN (0, '.(int)$id_currency.')
				AND `id_country` IN (0, '.(int)$id_country.')
				AND `id_group` IN (0, '.(int)$id_group.')
				AND `id_customer` IN (0, '.(int)$id_customer.')
				AND
				(
					(`from` = \'0000-00-00 00:00:00\' OR \''.$now.'\' >= `from`)
					AND
					(`to` = \'0000-00-00 00:00:00\' OR \''.$now.'\' <= `to`)
				)
				AND id_cart IN (0, '.(int)$id_cart.')
				AND IF(`from_quantity` > 1, `from_quantity`, 0) <= ';

            $query .= (Configuration::get('PS_QTY_DISCOUNT_ON_COMBINATION') || !$id_cart || !$real_quantity) ? (int)$quantity : max(1, (int)$real_quantity);
            $query .= ' ORDER BY `id_product_attribute` DESC, `from_quantity` DESC, `id_specific_price_rule` ASC, `score` DESC, `to` DESC, `from` DESC';

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

        $now = date('Y-m-d H:i:00');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *,
					'.SpecificPrice::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer).'
				FROM `'._DB_PREFIX_.'specific_price` USE INDEX (id_product_2)
				WHERE
					`id_product` IN(0, '.(int)$id_product.') AND
					'.(!$all_combinations ? '`id_product_attribute` IN(0, '.(int)$id_product_attribute.') AND ' : '').'
					`id_shop` IN(0, '.(int)$id_shop.') AND
					`id_currency` IN(0, '.(int)$id_currency.') AND
					`id_country` IN(0, '.(int)$id_country.') AND
					`id_group` IN(0, '.(int)$id_group.') AND
					`id_customer` IN(0, '.(int)$id_customer.')
					AND
					(
						(`from` = \'0000-00-00 00:00:00\' OR \''.$now.'\' >= `from`)
						AND
						(`to` = \'0000-00-00 00:00:00\' OR \''.$now.'\' <= `to`)
					)
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

        $now = date('Y-m-d H:i:00');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT *,
					'.SpecificPrice::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer).'
			FROM `'._DB_PREFIX_.'specific_price` USE INDEX (id_product_2)
			WHERE
					`id_product` IN(0, '.(int)$id_product.') AND
					`id_product_attribute` IN(0, '.(int)$id_product_attribute.') AND
					`id_shop` IN(0, '.(int)$id_shop.') AND
					`id_currency` IN(0, '.(int)$id_currency.') AND
					`id_country` IN(0, '.(int)$id_country.') AND
					`id_group` IN(0, '.(int)$id_group.') AND
					`id_customer` IN(0, '.(int)$id_customer.') AND
					`from_quantity` >= '.(int)$quantity.'
					AND
					(
						(`from` = \'0000-00-00 00:00:00\' OR \''.$now.'\' >= `from`)
						AND
						(`to` = \'0000-00-00 00:00:00\' OR \''.$now.'\' <= `to`)
					)
					ORDER BY `from_quantity` DESC, `score` DESC, `to` DESC, `from` DESC
		');
    }

    public static function getProductIdByDate($id_shop, $id_currency, $id_country, $id_group, $beginning, $ending, $id_customer = 0, $with_combination_id = false)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT SQL_NO_CACHE `id_product`, `id_product_attribute`
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE	`id_shop` IN(0, '.(int)$id_shop.') AND
					`id_currency` IN(0, '.(int)$id_currency.') AND
					`id_country` IN(0, '.(int)$id_country.') AND
					`id_group` IN(0, '.(int)$id_group.') AND
					`id_customer` IN(0, '.(int)$id_customer.') AND
					`from_quantity` = 1 AND
					(
						(`from` = \'0000-00-00 00:00:00\' OR \''.pSQL($beginning).'\' >= `from`)
						AND
						(`to` = \'0000-00-00 00:00:00\' OR \''.pSQL($ending).'\' <= `to`)
					)
					AND
					`reduction` > 0
		', false, false);
        $ids_product = array();
        while ($row = Db::getInstance()->nextRow($result)) {
            $ids_product[] = $with_combination_id ? array('id_product' => (int)$row['id_product'], 'id_product_attribute' => (int)$row['id_product_attribute']) : (int)$row['id_product'];
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
