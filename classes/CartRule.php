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

class CartRuleCore extends ObjectModel
{
    /* Filters used when retrieving the cart rules applied to a cart of when calculating the value of a reduction */
    const FILTER_ACTION_ALL = 1;
    const FILTER_ACTION_SHIPPING = 2;
    const FILTER_ACTION_REDUCTION = 3;
    const FILTER_ACTION_GIFT = 4;
    const FILTER_ACTION_ALL_NOCAP = 5;

    const BO_ORDER_CODE_PREFIX = 'BO_ORDER_';

    /* This variable controls that a free gift is offered only once, even when multi-shippping is activated and the same product is delivered in both addresses */
    protected static $only_one_gift = array();

    public $id;
    public $name;
    public $id_customer;
    public $date_from;
    public $date_to;
    public $description;
    public $quantity = 1;
    public $quantity_per_user = 1;
    public $priority = 1;
    public $partial_use = 1;
    public $code;
    public $minimum_amount;
    public $minimum_amount_tax;
    public $minimum_amount_currency;
    public $minimum_amount_shipping;
    public $country_restriction;
    public $carrier_restriction;
    public $group_restriction;
    public $cart_rule_restriction;
    public $product_restriction;
    public $shop_restriction;
    public $free_shipping;
    public $reduction_percent;
    public $reduction_amount;
    public $reduction_tax;
    public $reduction_currency;
    public $reduction_product;
    public $gift_product;
    public $gift_product_attribute;
    public $highlight;
    public $active = 1;
    public $date_add;
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'cart_rule',
        'primary' => 'id_cart_rule',
        'multilang' => true,
        'fields' => array(
            'id_customer' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_from' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'description' =>            array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65534),
            'quantity' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'quantity_per_user' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'priority' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'partial_use' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'code' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 254),
            'minimum_amount' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'minimum_amount_tax' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'minimum_amount_currency' =>array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'minimum_amount_shipping' =>array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'country_restriction' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'carrier_restriction' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'group_restriction' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'cart_rule_restriction' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'product_restriction' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'shop_restriction' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'free_shipping' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'reduction_percent' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPercentage'),
            'reduction_amount' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'reduction_tax' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'reduction_currency' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'reduction_product' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'gift_product' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'gift_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'highlight' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            /* Lang fields */
            'name' =>                    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 254),
        ),
    );

    /**
     * @see ObjectModel::add()
     */
    public function add($autodate = true, $null_values = false)
    {
        if (!$this->reduction_currency) {
            $this->reduction_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        }

        if (!parent::add($autodate, $null_values)) {
            return false;
        }

        Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', '1');
        return true;
    }

    public function update($null_values = false)
    {
        Cache::clean('getContextualValue_'.$this->id.'_*');

        if (!$this->reduction_currency) {
            $this->reduction_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        }

        return parent::update($null_values);
    }

    /**
     * @see ObjectModel::delete()
     */
    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', CartRule::isCurrentlyUsed($this->def['table'], true));

        $r = Db::getInstance()->delete('cart_cart_rule', '`id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->delete('cart_rule_carrier', '`id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->delete('cart_rule_shop', '`id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->delete('cart_rule_group', '`id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->delete('cart_rule_country', '`id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->delete('cart_rule_combination', '`id_cart_rule_1` = '.(int)$this->id.' OR `id_cart_rule_2` = '.(int)$this->id);
        $r &= Db::getInstance()->delete('cart_rule_product_rule_group', '`id_cart_rule` = '.(int)$this->id);
        $r &= Db::getInstance()->delete('cart_rule_product_rule', 'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`
			WHERE `'._DB_PREFIX_.'cart_rule_product_rule`.`id_product_rule_group` = `'._DB_PREFIX_.'cart_rule_product_rule_group`.`id_product_rule_group`)');
        $r &= Db::getInstance()->delete('cart_rule_product_rule_value', 'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_product_rule`
			WHERE `'._DB_PREFIX_.'cart_rule_product_rule_value`.`id_product_rule` = `'._DB_PREFIX_.'cart_rule_product_rule`.`id_product_rule`)');

        return $r;
    }

    /**
     * Copy conditions from one cart rule to an other
     *
     * @param int $id_cart_rule_source
     * @param int $id_cart_rule_destination
     */
    public static function copyConditions($id_cart_rule_source, $id_cart_rule_destination)
    {
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_shop` (`id_cart_rule`, `id_shop`)
		(SELECT '.(int)$id_cart_rule_destination.', id_shop FROM `'._DB_PREFIX_.'cart_rule_shop` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_carrier` (`id_cart_rule`, `id_carrier`)
		(SELECT '.(int)$id_cart_rule_destination.', id_carrier FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_group` (`id_cart_rule`, `id_group`)
		(SELECT '.(int)$id_cart_rule_destination.', id_group FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_country` (`id_cart_rule`, `id_country`)
		(SELECT '.(int)$id_cart_rule_destination.', id_country FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`)
		(SELECT '.(int)$id_cart_rule_destination.', IF(id_cart_rule_1 != '.(int)$id_cart_rule_source.', id_cart_rule_1, id_cart_rule_2) FROM `'._DB_PREFIX_.'cart_rule_combination`
		WHERE `id_cart_rule_1` = '.(int)$id_cart_rule_source.' OR `id_cart_rule_2` = '.(int)$id_cart_rule_source.')');

        // Todo : should be changed soon, be must be copied too
        // Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_cart_rule` = '.(int)$this->id);
        // Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');

        // Copy products/category filters
        $products_rules_group_source = Db::getInstance()->ExecuteS('
		SELECT id_product_rule_group,quantity FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`
		WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.' ');

        foreach ($products_rules_group_source as $product_rule_group_source) {
            Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
			VALUES ('.(int)$id_cart_rule_destination.','.(int)$product_rule_group_source['quantity'].')');
            $id_product_rule_group_destination = Db::getInstance()->Insert_ID();

            $products_rules_source = Db::getInstance()->ExecuteS('
			SELECT id_product_rule,type FROM `'._DB_PREFIX_.'cart_rule_product_rule`
			WHERE `id_product_rule_group` = '.(int)$product_rule_group_source['id_product_rule_group'].' ');

            foreach ($products_rules_source as $product_rule_source) {
                Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
				VALUES ('.(int)$id_product_rule_group_destination.',"'.pSQL($product_rule_source['type']).'")');
                $id_product_rule_destination = Db::getInstance()->Insert_ID();

                $products_rules_values_source = Db::getInstance()->ExecuteS('
				SELECT id_item FROM `'._DB_PREFIX_.'cart_rule_product_rule_value`
				WHERE `id_product_rule` = '.(int)$product_rule_source['id_product_rule'].' ');

                foreach ($products_rules_values_source as $product_rule_value_source) {
                    Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`)
					VALUES ('.(int)$id_product_rule_destination.','.(int)$product_rule_value_source['id_item'].')');
                }
            }
        }
    }

    /**
     * Retrieves the id associated to the given code
     *
     * @param string $code
     * @return int|bool
     */
    public static function getIdByCode($code)
    {
        if (!Validate::isCleanHtml($code)) {
            return false;
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_cart_rule` FROM `'._DB_PREFIX_.'cart_rule` WHERE `code` = \''.pSQL($code).'\'');
    }

    /**
     * @param           $id_lang
     * @param           $id_customer
     * @param bool      $active
     * @param bool      $includeGeneric
     * @param bool      $inStock
     * @param Cart|null $cart
     * @param bool      $free_shipping_only
     * @param bool      $highlight_only
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getCustomerCartRules($id_lang, $id_customer, $active = false, $includeGeneric = true, $inStock = false, Cart $cart = null, $free_shipping_only = false, $highlight_only = false)
    {
        if (!CartRule::isFeatureActive()) {
            return array();
        }

        $sql_part1 = '* FROM `'._DB_PREFIX_.'cart_rule` cr
				LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (cr.`id_cart_rule` = crl.`id_cart_rule` AND crl.`id_lang` = '.(int)$id_lang.')';

        $sql_part2 = ' AND cr.date_from < "'.date('Y-m-d H:i:s').'"
				AND cr.date_to > "'.date('Y-m-d H:i:s').'"
				'.($active ? 'AND cr.`active` = 1' : '').'
				'.($inStock ? 'AND cr.`quantity` > 0' : '');

        if ($free_shipping_only) {
            $sql_part2 .= ' AND free_shipping = 1 AND carrier_restriction = 1';
        }

        if ($highlight_only) {
            $sql_part2 .= ' AND highlight = 1 AND code NOT LIKE "'.pSQL(CartRule::BO_ORDER_CODE_PREFIX).'%"';
        }

        $sql = '(SELECT SQL_NO_CACHE '.$sql_part1.' WHERE cr.`id_customer` = '.(int)$id_customer.' '.$sql_part2.')';
        $sql .= ' UNION (SELECT '.$sql_part1.' WHERE cr.`group_restriction` = 1 '.$sql_part2.')';
        if ($includeGeneric && (int)$id_customer != 0) {
            $sql .= ' UNION (SELECT '.$sql_part1.' WHERE cr.`id_customer` = 0 '.$sql_part2.')';
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (empty($result)) {
            return array();
        }

        // Remove cart rule that does not match the customer groups
        $customerGroups = Customer::getGroupsStatic($id_customer);

        foreach ($result as $key => $cart_rule) {
            if ($cart_rule['group_restriction']) {
                $cartRuleGroups = Db::getInstance()->executeS('SELECT id_group FROM '._DB_PREFIX_.'cart_rule_group WHERE id_cart_rule = '.(int)$cart_rule['id_cart_rule']);
                foreach ($cartRuleGroups as $cartRuleGroup) {
                    if (in_array($cartRuleGroup['id_group'], $customerGroups)) {
                        continue 2;
                    }
                }
                unset($result[$key]);
            }
        }

        foreach ($result as &$cart_rule) {
            if ($cart_rule['quantity_per_user']) {
                $quantity_used = Order::getDiscountsCustomer((int)$id_customer, (int)$cart_rule['id_cart_rule']);
                if (isset($cart) && isset($cart->id)) {
                    $quantity_used += $cart->getDiscountsCustomer((int)$cart_rule['id_cart_rule']);
                }
                $cart_rule['quantity_for_user'] = $cart_rule['quantity_per_user'] - $quantity_used;
            } else {
                $cart_rule['quantity_for_user'] = 0;
            }
        }
        unset($cart_rule);

        foreach ($result as $key => $cart_rule) {
            if ($cart_rule['shop_restriction']) {
                $cartRuleShops = Db::getInstance()->executeS('SELECT id_shop FROM '._DB_PREFIX_.'cart_rule_shop WHERE id_cart_rule = '.(int)$cart_rule['id_cart_rule']);
                foreach ($cartRuleShops as $cartRuleShop) {
                    if (Shop::isFeatureActive() && ($cartRuleShop['id_shop'] == Context::getContext()->shop->id)) {
                        continue 2;
                    }
                }
                unset($result[$key]);
            }
        }

        if (isset($cart) && isset($cart->id)) {
            foreach ($result as $key => $cart_rule) {
                if ($cart_rule['product_restriction']) {
                    $cr = new CartRule((int)$cart_rule['id_cart_rule']);
                    $r = $cr->checkProductRestrictions(Context::getContext(), false, false);
                    if ($r !== false) {
                        continue;
                    }
                    unset($result[$key]);
                }
            }
        }

        $result_bak = $result;
        $result = array();
        $country_restriction = false;
        foreach ($result_bak as $key => $cart_rule) {
            if ($cart_rule['country_restriction']) {
                $country_restriction = true;
                $countries = Db::getInstance()->ExecuteS('
                    SELECT `id_country`
                    FROM `'._DB_PREFIX_.'address`
                    WHERE `id_customer` = '.(int)$id_customer.'
                    AND `deleted` = 0'
                );

                if (is_array($countries) && count($countries)) {
                    foreach ($countries as $country) {
                        $id_cart_rule = (bool)Db::getInstance()->getValue('
                            SELECT crc.id_cart_rule
                            FROM '._DB_PREFIX_.'cart_rule_country crc
                            WHERE crc.id_cart_rule = '.(int)$cart_rule['id_cart_rule'].'
                            AND crc.id_country = '.(int)$country['id_country']);
                        if ($id_cart_rule) {
                            $result[] = $result_bak[$key];
                            break;
                        }
                    }
                }
            }
            else {
                $result[] = $result_bak[$key];
            }
        }

        if (!$country_restriction)
             $result = $result_bak;

        // Retrocompatibility with 1.4 discounts
        foreach ($result as &$cart_rule) {
            $cart_rule['value'] = 0;
            $cart_rule['minimal'] = Tools::convertPriceFull($cart_rule['minimum_amount'], new Currency($cart_rule['minimum_amount_currency']), Context::getContext()->currency);
            $cart_rule['cumulable'] = !$cart_rule['cart_rule_restriction'];
            $cart_rule['id_discount_type'] = false;
            if ($cart_rule['free_shipping']) {
                $cart_rule['id_discount_type'] = Discount::FREE_SHIPPING;
            } elseif ($cart_rule['reduction_percent'] > 0) {
                $cart_rule['id_discount_type'] = Discount::PERCENT;
                $cart_rule['value'] = $cart_rule['reduction_percent'];
            } elseif ($cart_rule['reduction_amount'] > 0) {
                $cart_rule['id_discount_type'] = Discount::AMOUNT;
                $cart_rule['value'] = $cart_rule['reduction_amount'];
            }
        }
        unset($cart_rule);

        return $result;
    }

    /**
     * @param $id_customer
     * @return bool
     */
    public function usedByCustomer($id_customer)
    {
        return (bool)Db::getInstance()->getValue('
		SELECT id_cart_rule
		FROM `'._DB_PREFIX_.'order_cart_rule` ocr
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON ocr.`id_order` = o.`id_order`
		WHERE ocr.`id_cart_rule` = '.(int)$this->id.'
		AND o.`id_customer` = '.(int)$id_customer);
    }

    /**
     * @param $name
     * @return bool
     */
    public static function cartRuleExists($name)
    {
        if (!CartRule::isFeatureActive()) {
            return false;
        }

        return (bool)Db::getInstance()->getValue('
		SELECT `id_cart_rule`
		FROM `'._DB_PREFIX_.'cart_rule`
		WHERE `code` = \''.pSQL($name).'\'');
    }

    /**
     * @param $id_customer
     * @return bool
     */
    public static function deleteByIdCustomer($id_customer)
    {
        $return = true;
        $cart_rules = new PrestaShopCollection('CartRule');
        $cart_rules->where('id_customer', '=', $id_customer);
        foreach ($cart_rules as $cart_rule) {
            $return &= $cart_rule->delete();
        }
        return $return;
    }

    /**
     * @return array
     */
    public function getProductRuleGroups()
    {
        if (!Validate::isLoadedObject($this) || $this->product_restriction == 0) {
            return array();
        }

        $productRuleGroups = array();
        $result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'cart_rule_product_rule_group WHERE id_cart_rule = '.(int)$this->id);
        foreach ($result as $row) {
            if (!isset($productRuleGroups[$row['id_product_rule_group']])) {
                $productRuleGroups[$row['id_product_rule_group']] = array('id_product_rule_group' => $row['id_product_rule_group'], 'quantity' => $row['quantity']);
            }
            $productRuleGroups[$row['id_product_rule_group']]['product_rules'] = $this->getProductRules($row['id_product_rule_group']);
        }
        return $productRuleGroups;
    }

    /**
     * @param $id_product_rule_group
     * @return array ('type' => ? , 'values' => ?)
     */
    public function getProductRules($id_product_rule_group)
    {
        if (!Validate::isLoadedObject($this) || $this->product_restriction == 0) {
            return array();
        }

        $productRules = array();
        $results = Db::getInstance()->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'cart_rule_product_rule pr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_product_rule_value prv ON pr.id_product_rule = prv.id_product_rule
		WHERE pr.id_product_rule_group = '.(int)$id_product_rule_group);
        foreach ($results as $row) {
            if (!isset($productRules[$row['id_product_rule']])) {
                $productRules[$row['id_product_rule']] = array('type' => $row['type'], 'values' => array());
            }
            $productRules[$row['id_product_rule']]['values'][] = $row['id_item'];
        }
        return $productRules;
    }

    /**
     * Check if this cart rule can be applied
     *
     * @param Context $context
     * @param bool $alreadyInCart Check if the voucher is already on the cart
     * @param bool $display_error Display error
     * @return bool|mixed|string
     */
    public function checkValidity(Context $context, $alreadyInCart = false, $display_error = true, $check_carrier = true)
    {
        if (!CartRule::isFeatureActive()) {
            return false;
        }

        if (!$this->active) {
            return (!$display_error) ? false : Tools::displayError('This voucher is disabled');
        }
        if (!$this->quantity) {
            return (!$display_error) ? false : Tools::displayError('This voucher has already been used');
        }
        if (strtotime($this->date_from) > time()) {
            return (!$display_error) ? false : Tools::displayError('This voucher is not valid yet');
        }
        if (strtotime($this->date_to) < time()) {
            return (!$display_error) ? false : Tools::displayError('This voucher has expired');
        }

        if ($context->cart->id_customer) {
            $quantityUsed = Db::getInstance()->getValue('
			SELECT count(*)
			FROM '._DB_PREFIX_.'orders o
			LEFT JOIN '._DB_PREFIX_.'order_cart_rule od ON o.id_order = od.id_order
			WHERE o.id_customer = '.$context->cart->id_customer.'
			AND od.id_cart_rule = '.(int)$this->id.'
			AND '.(int)Configuration::get('PS_OS_ERROR').' != o.current_state
			');
            if ($quantityUsed + 1 > $this->quantity_per_user) {
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher anymore (usage limit reached)');
            }
        }

        // Get an intersection of the customer groups and the cart rule groups (if the customer is not logged in, the default group is Visitors)
        if ($this->group_restriction) {
            $id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crg.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_group crg
			WHERE crg.id_cart_rule = '.(int)$this->id.'
			AND crg.id_group '.($context->cart->id_customer ? 'IN (SELECT cg.id_group FROM '._DB_PREFIX_.'customer_group cg WHERE cg.id_customer = '.(int)$context->cart->id_customer.')' : '= '.(int)Configuration::get('PS_UNIDENTIFIED_GROUP')));
            if (!$id_cart_rule) {
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher');
            }
        }

        // Check if the customer delivery address is usable with the cart rule
        if ($this->country_restriction) {
            if (!$context->cart->id_address_delivery) {
                return (!$display_error) ? false : Tools::displayError('You must choose a delivery address before applying this voucher to your order');
            }
            $id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_country crc
			WHERE crc.id_cart_rule = '.(int)$this->id.'
			AND crc.id_country = (SELECT a.id_country FROM '._DB_PREFIX_.'address a WHERE a.id_address = '.(int)$context->cart->id_address_delivery.' LIMIT 1)');
            if (!$id_cart_rule) {
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher in your country of delivery');
            }
        }

        // Check if the carrier chosen by the customer is usable with the cart rule
        if ($this->carrier_restriction && $check_carrier) {
            if (!$context->cart->id_carrier) {
                return (!$display_error) ? false : Tools::displayError('You must choose a carrier before applying this voucher to your order');
            }
            $id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_carrier crc
			INNER JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)
			WHERE crc.id_cart_rule = '.(int)$this->id.'
			AND c.id_carrier = '.(int)$context->cart->id_carrier);
            if (!$id_cart_rule) {
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with this carrier');
            }
        }

        // Check if the cart rules appliy to the shop browsed by the customer
        if ($this->shop_restriction && $context->shop->id && Shop::isFeatureActive()) {
            $id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crs.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_shop crs
			WHERE crs.id_cart_rule = '.(int)$this->id.'
			AND crs.id_shop = '.(int)$context->shop->id);
            if (!$id_cart_rule) {
                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher');
            }
        }

        // Check if the products chosen by the customer are usable with the cart rule
        if ($this->product_restriction) {
            $r = $this->checkProductRestrictions($context, false, $display_error, $alreadyInCart);
            if ($r !== false && $display_error) {
                return $r;
            } elseif (!$r && !$display_error) {
                return false;
            }
        }

        // Check if the cart rule is only usable by a specific customer, and if the current customer is the right one
        if ($this->id_customer && $context->cart->id_customer != $this->id_customer) {
            if (!Context::getContext()->customer->isLogged()) {
                return (!$display_error) ? false : (Tools::displayError('You cannot use this voucher').' - '.Tools::displayError('Please log in first'));
            }
            return (!$display_error) ? false : Tools::displayError('You cannot use this voucher');
        }

        if ($this->minimum_amount && $check_carrier) {
            // Minimum amount is converted to the contextual currency
            $minimum_amount = $this->minimum_amount;
            if ($this->minimum_amount_currency != Context::getContext()->currency->id) {
                $minimum_amount = Tools::convertPriceFull($minimum_amount, new Currency($this->minimum_amount_currency), Context::getContext()->currency);
            }

            $cartTotal = $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_PRODUCTS);
            if ($this->minimum_amount_shipping) {
                $cartTotal += $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_SHIPPING);
            }
            $products = $context->cart->getProducts();
            $cart_rules = $context->cart->getCartRules();

            foreach ($cart_rules as &$cart_rule) {
                if ($cart_rule['gift_product']) {
                    foreach ($products as $key => &$product) {
                        if (empty($product['gift']) && $product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute']) {
                            $cartTotal = Tools::ps_round($cartTotal - $product[$this->minimum_amount_tax ? 'price_wt' : 'price'], (int)$context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        }
                    }
                }
            }

            if ($cartTotal < $minimum_amount) {
                return (!$display_error) ? false : Tools::displayError('You have not reached the minimum amount required to use this voucher');
            }
        }

        /* This loop checks:
            - if the voucher is already in the cart
            - if a non compatible voucher is in the cart
            - if there are products in the cart (gifts excluded)
            Important note: this MUST be the last check, because if the tested cart rule has priority over a non combinable one in the cart, we will switch them
        */
        $nb_products = Cart::getNbProducts($context->cart->id);
        $otherCartRules = array();
        if ($check_carrier) {
            $otherCartRules = $context->cart->getCartRules();
        }
        if (count($otherCartRules)) {
            foreach ($otherCartRules as $otherCartRule) {
                if ($otherCartRule['id_cart_rule'] == $this->id && !$alreadyInCart) {
                    return (!$display_error) ? false : Tools::displayError('This voucher is already in your cart');
                }
                if ($otherCartRule['gift_product']) {
                    --$nb_products;
                }

                if ($this->cart_rule_restriction && $otherCartRule['cart_rule_restriction'] && $otherCartRule['id_cart_rule'] != $this->id) {
                    $combinable = Db::getInstance()->getValue('
					SELECT id_cart_rule_1
					FROM '._DB_PREFIX_.'cart_rule_combination
					WHERE (id_cart_rule_1 = '.(int)$this->id.' AND id_cart_rule_2 = '.(int)$otherCartRule['id_cart_rule'].')
					OR (id_cart_rule_2 = '.(int)$this->id.' AND id_cart_rule_1 = '.(int)$otherCartRule['id_cart_rule'].')');
                    if (!$combinable) {
                        $cart_rule = new CartRule((int)$otherCartRule['id_cart_rule'], $context->cart->id_lang);
                        // The cart rules are not combinable and the cart rule currently in the cart has priority over the one tested
                        if ($cart_rule->priority <= $this->priority) {
                            return (!$display_error) ? false : Tools::displayError('This voucher is not combinable with an other voucher already in your cart:').' '.$cart_rule->name;
                        }
                        // But if the cart rule that is tested has priority over the one in the cart, we remove the one in the cart and keep this new one
                        else {
                            $context->cart->removeCartRule($cart_rule->id);
                        }
                    }
                }
            }
        }

        if (!$nb_products) {
            return (!$display_error) ? false : Tools::displayError('Cart is empty');
        }

        if (!$display_error) {
            return true;
        }
    }

    protected function checkProductRestrictions(Context $context, $return_products = false, $display_error = true, $already_in_cart = false)
    {
        $selected_products = array();

        // Check if the products chosen by the customer are usable with the cart rule
        if ($this->product_restriction) {
            $product_rule_groups = $this->getProductRuleGroups();
            foreach ($product_rule_groups as $id_product_rule_group => $product_rule_group) {
                $eligible_products_list = array();
                if (isset($context->cart) && is_object($context->cart) && is_array($products = $context->cart->getProducts())) {
                    foreach ($products as $product) {
                        $eligible_products_list[] = (int)$product['id_product'].'-'.(int)$product['id_product_attribute'];
                    }
                }
                if (!count($eligible_products_list)) {
                    return (!$display_error) ? false : Tools::displayError('You cannot use this voucher in an empty cart');
                }

                $product_rules = $this->getProductRules($id_product_rule_group);
                foreach ($product_rules as $product_rule) {
                    switch ($product_rule['type']) {
                        case 'attributes':
                            $cart_attributes = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, pac.`id_attribute`, cp.`id_product_attribute`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON cp.id_product_attribute = pac.id_product_attribute
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligible_products_list)).')
							AND cp.id_product_attribute > 0');
                            $count_matching_products = 0;
                            $matching_products_list = array();
                            foreach ($cart_attributes as $cart_attribute) {
                                if (in_array($cart_attribute['id_attribute'], $product_rule['values'])) {
                                    $count_matching_products += $cart_attribute['quantity'];
                                    if ($already_in_cart && $this->gift_product == $cart_attribute['id_product']
                                        && $this->gift_product_attribute == $cart_attribute['id_product_attribute']) {
                                        --$count_matching_products;
                                    }
                                    $matching_products_list[] = $cart_attribute['id_product'].'-'.$cart_attribute['id_product_attribute'];
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            $eligible_products_list = CartRule::array_uintersect($eligible_products_list, $matching_products_list);
                            break;
                        case 'products':
                            $cart_products = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`
							FROM `'._DB_PREFIX_.'cart_product` cp
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligible_products_list)).')');
                            $count_matching_products = 0;
                            $matching_products_list = array();
                            foreach ($cart_products as $cart_product) {
                                if (in_array($cart_product['id_product'], $product_rule['values'])) {
                                    $count_matching_products += $cart_product['quantity'];
                                    if ($already_in_cart && $this->gift_product == $cart_product['id_product']) {
                                        --$count_matching_products;
                                    }
                                    $matching_products_list[] = $cart_product['id_product'].'-0';
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            $eligible_products_list = CartRule::array_uintersect($eligible_products_list, $matching_products_list);
                            break;
                        case 'categories':
                            $cart_categories = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, cp.`id_product_attribute`, catp.`id_category`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'category_product` catp ON cp.id_product = catp.id_product
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligible_products_list)).')
							AND cp.`id_product` <> '.(int)$this->gift_product);
                            $count_matching_products = 0;
                            $matching_products_list = array();
                            foreach ($cart_categories as $cart_category) {
                                if (in_array($cart_category['id_category'], $product_rule['values'])
                                    /**
                                     * We also check that the product is not already in the matching product list,
                                     * because there are doubles in the query results (when the product is in multiple categories)
                                     */
                                    && !in_array($cart_category['id_product'].'-'.$cart_category['id_product_attribute'], $matching_products_list)) {
                                    $count_matching_products += $cart_category['quantity'];
                                    $matching_products_list[] = $cart_category['id_product'].'-'.$cart_category['id_product_attribute'];
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            // Attribute id is not important for this filter in the global list, so the ids are replaced by 0
                            foreach ($matching_products_list as &$matching_product) {
                                $matching_product = preg_replace('/^([0-9]+)-[0-9]+$/', '$1-0', $matching_product);
                            }
                            $eligible_products_list = CartRule::array_uintersect($eligible_products_list, $matching_products_list);
                            break;
                        case 'manufacturers':
                            $cart_manufacturers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_manufacturer`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligible_products_list)).')');
                            $count_matching_products = 0;
                            $matching_products_list = array();
                            foreach ($cart_manufacturers as $cart_manufacturer) {
                                if (in_array($cart_manufacturer['id_manufacturer'], $product_rule['values'])) {
                                    $count_matching_products += $cart_manufacturer['quantity'];
                                    $matching_products_list[] = $cart_manufacturer['id_product'].'-0';
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            $eligible_products_list = CartRule::array_uintersect($eligible_products_list, $matching_products_list);
                            break;
                        case 'suppliers':
                            $cart_suppliers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_supplier`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligible_products_list)).')');
                            $count_matching_products = 0;
                            $matching_products_list = array();
                            foreach ($cart_suppliers as $cart_supplier) {
                                if (in_array($cart_supplier['id_supplier'], $product_rule['values'])) {
                                    $count_matching_products += $cart_supplier['quantity'];
                                    $matching_products_list[] = $cart_supplier['id_product'].'-0';
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            $eligible_products_list = CartRule::array_uintersect($eligible_products_list, $matching_products_list);
                            break;
                    }

                    if (!count($eligible_products_list)) {
                        return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
                    }
                }
                $selected_products = array_merge($selected_products, $eligible_products_list);
            }
        }

        if ($return_products) {
            return $selected_products;
        }
        return (!$display_error) ? true : false;
    }

    protected static function array_uintersect($array1, $array2)
    {
        $intersection = array();
        foreach ($array1 as $value1) {
            foreach ($array2 as $value2) {
                if (CartRule::array_uintersect_compare($value1, $value2) == 0) {
                    $intersection[] = $value1;
                    break 1;
                }
            }
        }
        return $intersection;
    }

    protected static function array_uintersect_compare($a, $b)
    {
        if ($a == $b) {
            return 0;
        }

        $asplit = explode('-', $a);
        $bsplit = explode('-', $b);
        if ($asplit[0] == $bsplit[0] && (!(int)$asplit[1] || !(int)$bsplit[1])) {
            return 0;
        }

        return 1;
    }

    /**
     * The reduction value is POSITIVE
     *
     * @param bool $use_tax
     * @param Context $context
     * @param bool $use_cache Allow using cache to avoid multiple free gift using multishipping
     * @return float|int|string
     */
    public function getContextualValue($use_tax, Context $context = null, $filter = null, $package = null, $use_cache = true)
    {
        if (!CartRule::isFeatureActive()) {
            return 0;
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$filter) {
            $filter = CartRule::FILTER_ACTION_ALL;
        }

        $all_products = $context->cart->getProducts();
        $package_products = (is_null($package) ? $all_products : $package['products']);

        $reduction_value = 0;

        $cache_id = 'getContextualValue_'.(int)$this->id.'_'.(int)$use_tax.'_'.(int)$context->cart->id.'_'.(int)$filter;
        foreach ($package_products as $product) {
            $cache_id .= '_'.(int)$product['id_product'].'_'.(int)$product['id_product_attribute'].(isset($product['in_stock']) ? '_'.(int)$product['in_stock'] : '');
        }

        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }

        $all_cart_rules_ids = $context->cart->getOrderedCartRulesIds();

        $cart_amount_ti = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $cart_amount_te = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        // Free shipping on selected carriers
        if ($this->free_shipping && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_SHIPPING))) {
            if (!$this->carrier_restriction) {
                $reduction_value += $context->cart->getOrderTotal($use_tax, Cart::ONLY_SHIPPING, is_null($package) ? null : $package['products'], is_null($package) ? null : $package['id_carrier']);
            } else {
                $data = Db::getInstance()->executeS('
					SELECT crc.id_cart_rule, c.id_carrier
					FROM '._DB_PREFIX_.'cart_rule_carrier crc
					INNER JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)
					WHERE crc.id_cart_rule = '.(int)$this->id.'
					AND c.id_carrier = '.(int)$context->cart->id_carrier);

                if ($data) {
                    foreach ($data as $cart_rule) {
                        $reduction_value += $context->cart->getCarrierCost((int)$cart_rule['id_carrier'], $use_tax, $context->country);
                    }
                }
            }
        }

        if (in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_REDUCTION))) {
            // Discount (%) on the whole order
            if ($this->reduction_percent && $this->reduction_product == 0) {
                // Do not give a reduction on free products!
                $order_total = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package_products);
                foreach ($context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT) as $cart_rule) {
                    $order_total -= Tools::ps_round($cart_rule['obj']->getContextualValue($use_tax, $context, CartRule::FILTER_ACTION_GIFT, $package), _PS_PRICE_COMPUTE_PRECISION_);
                }

                $reduction_value += $order_total * $this->reduction_percent / 100;
            }

            // Discount (%) on a specific product
            if ($this->reduction_percent && $this->reduction_product > 0) {
                foreach ($package_products as $product) {
                    if ($product['id_product'] == $this->reduction_product) {
                        $reduction_value += ($use_tax ? $product['total_wt'] : $product['total']) * $this->reduction_percent / 100;
                    }
                }
            }

            // Discount (%) on the cheapest product
            if ($this->reduction_percent && $this->reduction_product == -1) {
                $minPrice = false;
                $cheapest_product = null;
                foreach ($all_products as $product) {
                    $price = $product['price'];
                    if ($use_tax) {
                        // since later on we won't be able to know the product the cart rule was applied to,
                        // use average cart VAT for price_wt
                        $price *= (1 + $context->cart->getAverageProductsTaxRate());
                    }

                    if ($price > 0 && ($minPrice === false || $minPrice > $price)) {
                        $minPrice = $price;
                        $cheapest_product = $product['id_product'].'-'.$product['id_product_attribute'];
                    }
                }

                // Check if the cheapest product is in the package
                $in_package = false;
                foreach ($package_products as $product) {
                    if ($product['id_product'].'-'.$product['id_product_attribute'] == $cheapest_product || $product['id_product'].'-0' == $cheapest_product) {
                        $in_package = true;
                    }
                }
                if ($in_package) {
                    $reduction_value += $minPrice * $this->reduction_percent / 100;
                }
            }

            // Discount (%) on the selection of products
            if ($this->reduction_percent && $this->reduction_product == -2) {
                $selected_products_reduction = 0;
                $selected_products = $this->checkProductRestrictions($context, true);
                if (is_array($selected_products)) {
                    foreach ($package_products as $product) {
                        if (in_array($product['id_product'].'-'.$product['id_product_attribute'], $selected_products)
                            || in_array($product['id_product'].'-0', $selected_products)) {
                            $price = $product['price'];
                            if ($use_tax) {
                                $infos = Product::getTaxesInformations($product, $context);
                                $tax_rate = $infos['rate'] / 100;
                                $price *= (1 + $tax_rate);
                            }

                            $selected_products_reduction += $price * $product['cart_quantity'];
                        }
                    }
                }
                $reduction_value += $selected_products_reduction * $this->reduction_percent / 100;
            }

            // Discount (¤)
            if ((float)$this->reduction_amount > 0) {
                $prorata = 1;
                if (!is_null($package) && count($all_products)) {
                    $total_products = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS);
                    if ($total_products) {
                        $prorata = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package['products']) / $total_products;
                    }
                }

                $reduction_amount = $this->reduction_amount;
                // If we need to convert the voucher value to the cart currency
                if (isset($context->currency) && $this->reduction_currency != $context->currency->id) {
                    $voucherCurrency = new Currency($this->reduction_currency);

                    // First we convert the voucher value to the default currency
                    if ($reduction_amount == 0 || $voucherCurrency->conversion_rate == 0) {
                        $reduction_amount = 0;
                    } else {
                        $reduction_amount /= $voucherCurrency->conversion_rate;
                    }

                    // Then we convert the voucher value in the default currency into the cart currency
                    $reduction_amount *= $context->currency->conversion_rate;
                    $reduction_amount = Tools::ps_round($reduction_amount, _PS_PRICE_COMPUTE_PRECISION_);
                }

                // If it has the same tax application that you need, then it's the right value, whatever the product!
                if ($this->reduction_tax == $use_tax) {
                    // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
                    if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                        $cart_amount = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS);
                        $reduction_amount = min($reduction_amount, $cart_amount);
                    }
                    $reduction_value += $prorata * $reduction_amount;
                } else {
                    if ($this->reduction_product > 0) {
                        foreach ($context->cart->getProducts() as $product) {
                            if ($product['id_product'] == $this->reduction_product) {
                                $product_price_ti = $product['price_wt'];
                                $product_price_te = $product['price'];
                                $product_vat_amount = $product_price_ti - $product_price_te;

                                if ($product_vat_amount == 0 || $product_price_te == 0) {
                                    $product_vat_rate = 0;
                                } else {
                                    $product_vat_rate = $product_vat_amount / $product_price_te;
                                }

                                if ($this->reduction_tax && !$use_tax) {
                                    $reduction_value += $prorata * $reduction_amount / (1 + $product_vat_rate);
                                } elseif (!$this->reduction_tax && $use_tax) {
                                    $reduction_value += $prorata * $reduction_amount * (1 + $product_vat_rate);
                                }
                            }
                        }
                    }
                    // Discount (¤) on the whole order
                    elseif ($this->reduction_product == 0) {
                        $cart_amount_te = null;
                        $cart_amount_ti = null;
                        $cart_average_vat_rate = $context->cart->getAverageProductsTaxRate($cart_amount_te, $cart_amount_ti);

                        // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
                        if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                            $reduction_amount = min($reduction_amount, $this->reduction_tax ? $cart_amount_ti : $cart_amount_te);
                        }

                        if ($this->reduction_tax && !$use_tax) {
                            $reduction_value += $prorata * $reduction_amount / (1 + $cart_average_vat_rate);
                        } elseif (!$this->reduction_tax && $use_tax) {
                            $reduction_value += $prorata * $reduction_amount * (1 + $cart_average_vat_rate);
                        }
                    }
                    /*
                     * Reduction on the cheapest or on the selection is not really meaningful and has been disabled in the backend
                     * Please keep this code, so it won't be considered as a bug
                     * elseif ($this->reduction_product == -1)
                     * elseif ($this->reduction_product == -2)
                    */
                }

                // Take care of the other cart rules values if the filter allow it
                if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                    // Cart values
                    $cart = Context::getContext()->cart;

                    if (!Validate::isLoadedObject($cart)) {
                        $cart = new Cart();
                    }

                    $cart_average_vat_rate = $cart->getAverageProductsTaxRate();
                    $current_cart_amount = $use_tax ? $cart_amount_ti : $cart_amount_te;

                    foreach ($all_cart_rules_ids as $current_cart_rule_id) {
                        if ((int)$current_cart_rule_id['id_cart_rule'] == (int)$this->id) {
                            break;
                        }

                        $previous_cart_rule = new CartRule((int)$current_cart_rule_id['id_cart_rule']);
                        $previous_reduction_amount = $previous_cart_rule->reduction_amount;

                        if ($previous_cart_rule->reduction_tax && !$use_tax) {
                            $previous_reduction_amount = $prorata * $previous_reduction_amount / (1 + $cart_average_vat_rate);
                        } elseif (!$previous_cart_rule->reduction_tax && $use_tax) {
                            $previous_reduction_amount = $prorata * $previous_reduction_amount * (1 + $cart_average_vat_rate);
                        }

                        $current_cart_amount = max($current_cart_amount - (float)$previous_reduction_amount, 0);
                    }

                    $reduction_value = min($reduction_value, $current_cart_amount);
                }
            }
        }

        // Free gift
        if ((int)$this->gift_product && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_GIFT))) {
            $id_address = (is_null($package) ? 0 : $package['id_address']);
            foreach ($package_products as $product) {
                if ($product['id_product'] == $this->gift_product && ($product['id_product_attribute'] == $this->gift_product_attribute || !(int)$this->gift_product_attribute)) {
                    // The free gift coupon must be applied to one product only (needed for multi-shipping which manage multiple product lists)
                    if (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product])
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == $id_address
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0
                        || $id_address == 0
                        || !$use_cache) {
                        $reduction_value += ($use_tax ? $product['price_wt'] : $product['price']);
                        if ($use_cache && (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product]) || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0)) {
                            CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] = $id_address;
                        }
                        break;
                    }
                }
            }
        }

        Cache::store($cache_id, $reduction_value);
        return $reduction_value;
    }

    /**
     * Make sure caches are empty
     * Must be called before calling multiple time getContextualValue()
     */
    public static function cleanCache()
    {
        self::$only_one_gift = array();
    }

    /**
     * @param int    $offset
     * @param int    $limit
     * @param string $search
     * @return array
     */
    protected function getCartRuleCombinations($offset = null, $limit = null, $search = '')
    {
        $array = array();
        if ($offset !== null && $limit !== null) {
            $sql_limit = ' LIMIT '.(int)$offset.', '.(int)($limit+1);
        } else {
            $sql_limit = '';
        }

        $array['selected'] = Db::getInstance()->executeS('
		SELECT cr.*, crl.*, 1 as selected
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')
		WHERE cr.id_cart_rule != '.(int)$this->id.($search ? ' AND crl.name LIKE "%'.pSQL($search).'%"' : '').'
		AND (
			cr.cart_rule_restriction = 0
			OR EXISTS (
				SELECT 1
				FROM '._DB_PREFIX_.'cart_rule_combination
				WHERE cr.id_cart_rule = '._DB_PREFIX_.'cart_rule_combination.id_cart_rule_1 AND '.(int)$this->id.' = id_cart_rule_2
			)
			OR EXISTS (
				SELECT 1
				FROM '._DB_PREFIX_.'cart_rule_combination
				WHERE cr.id_cart_rule = '._DB_PREFIX_.'cart_rule_combination.id_cart_rule_2 AND '.(int)$this->id.' = id_cart_rule_1
			)
		) ORDER BY cr.id_cart_rule'.$sql_limit);

        $array['unselected'] = Db::getInstance()->executeS('
		SELECT cr.*, crl.*, 1 as selected
		FROM '._DB_PREFIX_.'cart_rule cr
		INNER JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')
		LEFT JOIN '._DB_PREFIX_.'cart_rule_combination crc1 ON (cr.id_cart_rule = crc1.id_cart_rule_1 AND crc1.id_cart_rule_2 = '.(int)$this->id.')
		LEFT JOIN '._DB_PREFIX_.'cart_rule_combination crc2 ON (cr.id_cart_rule = crc2.id_cart_rule_2 AND crc2.id_cart_rule_1 = '.(int)$this->id.')
		WHERE cr.cart_rule_restriction = 1
		AND cr.id_cart_rule != '.(int)$this->id.($search ? ' AND crl.name LIKE "%'.pSQL($search).'%"' : '').'
		AND crc1.id_cart_rule_1 IS NULL
		AND crc2.id_cart_rule_1 IS NULL  ORDER BY cr.id_cart_rule'.$sql_limit);
        return $array;
    }

    /**
     * @param string $type
     * @param bool   $active_only
     * @param bool   $i18n
     * @param int    $offset
     * @param int    $limit
     * @param string $search_cart_rule_name
     * @return array|bool
     * @throws PrestaShopDatabaseException
     */
    public function getAssociatedRestrictions($type, $active_only, $i18n, $offset = null, $limit = null, $search_cart_rule_name = '')
    {
        $array = array('selected' => array(), 'unselected' => array());

        if (!in_array($type, array('country', 'carrier', 'group', 'cart_rule', 'shop'))) {
            return false;
        }

        $shop_list = '';
        if ($type == 'shop') {
            $shops = Context::getContext()->employee->getAssociatedShops();
            if (count($shops)) {
                $shop_list = ' AND t.id_shop IN ('.implode(',', array_map('intval', $shops)).') ';
            }
        }

        if ($offset !== null && $limit !== null) {
            $sql_limit = ' LIMIT '.(int)$offset.', '.(int)($limit+1);
        } else {
            $sql_limit = '';
        }

        if (!Validate::isLoadedObject($this) || $this->{$type.'_restriction'} == 0) {
            $array['selected'] = Db::getInstance()->executeS('
			SELECT t.*'.($i18n ? ', tl.*' : '').', 1 as selected
			FROM `'._DB_PREFIX_.$type.'` t
			'.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
			WHERE 1
			'.($active_only ? 'AND t.active = 1' : '').'
			'.(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').'
			'.($type == 'cart_rule' ? 'AND t.id_cart_rule != '.(int)$this->id : '').
            $shop_list.
            (in_array($type, array('carrier', 'shop')) ? ' ORDER BY t.name ASC ' : '').
            (in_array($type, array('country', 'group', 'cart_rule')) && $i18n ? ' ORDER BY tl.name ASC ' : '').
            $sql_limit);
        } else {
            if ($type == 'cart_rule') {
                $array = $this->getCartRuleCombinations($offset, $limit, $search_cart_rule_name);
            } else {
                $resource = Db::getInstance()->query('
				SELECT t.*'.($i18n ? ', tl.*' : '').', IF(crt.id_'.$type.' IS NULL, 0, 1) as selected
				FROM `'._DB_PREFIX_.$type.'` t
				'.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
				LEFT JOIN (SELECT id_'.$type.' FROM `'._DB_PREFIX_.'cart_rule_'.$type.'` WHERE id_cart_rule = '.(int)$this->id.') crt ON t.id_'.($type == 'carrier' ? 'reference' : $type).' = crt.id_'.$type.'
				WHERE 1 '.($active_only ? ' AND t.active = 1' : '').
                $shop_list
                .(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').
                (in_array($type, array('carrier', 'shop')) ? ' ORDER BY t.name ASC ' : '').
                (in_array($type, array('country', 'group', 'cart_rule')) && $i18n ? ' ORDER BY tl.name ASC ' : '').
                $sql_limit,
                false);
                while ($row = Db::getInstance()->nextRow($resource)) {
                    $array[($row['selected'] || $this->{$type.'_restriction'} == 0) ? 'selected' : 'unselected'][] = $row;
                }
            }
        }
        return $array;
    }

    public static function autoRemoveFromCart($context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (!CartRule::isFeatureActive() || !Validate::isLoadedObject($context->cart)) {
            return array();
        }

        static $errors = array();
        foreach ($context->cart->getCartRules() as $cart_rule) {
            if ($error = $cart_rule['obj']->checkValidity($context, true)) {
                $context->cart->removeCartRule($cart_rule['obj']->id);
                $context->cart->update();
                $errors[] = $error;
            }
        }
        return $errors;
    }

    /**
     * @param Context|null $context
     * @return mixed
     */
    public static function autoAddToCart(Context $context = null)
    {
        if ($context === null) {
            $context = Context::getContext();
        }
        if (!CartRule::isFeatureActive() || !Validate::isLoadedObject($context->cart)) {
            return;
        }

        $sql = '
		SELECT SQL_NO_CACHE cr.*
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_shop crs ON cr.id_cart_rule = crs.id_cart_rule
		'.(!$context->customer->id && Group::isFeatureActive() ? ' LEFT JOIN '._DB_PREFIX_.'cart_rule_group crg ON cr.id_cart_rule = crg.id_cart_rule' : '').'
		LEFT JOIN '._DB_PREFIX_.'cart_rule_carrier crca ON cr.id_cart_rule = crca.id_cart_rule
		'.($context->cart->id_carrier ? 'LEFT JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crca.id_carrier AND c.deleted = 0)' : '').'
		LEFT JOIN '._DB_PREFIX_.'cart_rule_country crco ON cr.id_cart_rule = crco.id_cart_rule
		WHERE cr.active = 1
		AND cr.code = ""
		AND cr.quantity > 0
		AND cr.date_from < "'.date('Y-m-d H:i:s').'"
		AND cr.date_to > "'.date('Y-m-d H:i:s').'"
		AND (
			cr.id_customer = 0
			'.($context->customer->id ? 'OR cr.id_customer = '.(int)$context->cart->id_customer : '').'
		)
		AND (
			cr.`carrier_restriction` = 0
			'.($context->cart->id_carrier ? 'OR c.id_carrier = '.(int)$context->cart->id_carrier : '').'
		)
		AND (
			cr.`shop_restriction` = 0
			'.((Shop::isFeatureActive() && $context->shop->id) ? 'OR crs.id_shop = '.(int)$context->shop->id : '').'
		)
		AND (
			cr.`group_restriction` = 0
			'.($context->customer->id ? 'OR EXISTS (
				SELECT 1
				FROM `'._DB_PREFIX_.'customer_group` cg
				INNER JOIN `'._DB_PREFIX_.'cart_rule_group` crg ON cg.id_group = crg.id_group
				WHERE cr.`id_cart_rule` = crg.`id_cart_rule`
				AND cg.`id_customer` = '.(int)$context->customer->id.'
				LIMIT 1
			)' : (Group::isFeatureActive() ? 'OR crg.`id_group` = '.(int)Configuration::get('PS_UNIDENTIFIED_GROUP') : '')).'
		)
		AND (
			cr.`reduction_product` <= 0
			OR EXISTS (
				SELECT 1
				FROM `'._DB_PREFIX_.'cart_product`
				WHERE `'._DB_PREFIX_.'cart_product`.`id_product` = cr.`reduction_product` AND `id_cart` = '.(int)$context->cart->id.'
			)
		)
		AND NOT EXISTS (SELECT 1 FROM '._DB_PREFIX_.'cart_cart_rule WHERE cr.id_cart_rule = '._DB_PREFIX_.'cart_cart_rule.id_cart_rule
																			AND id_cart = '.(int)$context->cart->id.')
		ORDER BY priority';
        $result = Db::getInstance()->executeS($sql, true, false);
        if ($result) {
            $cart_rules = ObjectModel::hydrateCollection('CartRule', $result);
            if ($cart_rules) {
                foreach ($cart_rules as $cart_rule) {
                    /** @var CartRule $cart_rule */
                    if ($cart_rule->checkValidity($context, false, false)) {
                        $context->cart->addCartRule($cart_rule->id);
                    }
                }
            }
        }
    }

    /**
     * @return bool
     */
    public static function isFeatureActive()
    {
        static $is_feature_active = null;
        if ($is_feature_active === null) {
            $is_feature_active = (bool)Configuration::get('PS_CART_RULE_FEATURE_ACTIVE');
        }
        return $is_feature_active;
    }

    /* When an entity associated to a product rule (product, category, attribute, supplier, manufacturer...) is deleted, the product rules must be updated */
    public static function cleanProductRuleIntegrity($type, $list)
    {
        // Type must be available in the 'type' enum of the table cart_rule_product_rule
        if (!in_array($type, array('products', 'categories', 'attributes', 'manufacturers', 'suppliers'))) {
            return false;
        }

        // This check must not be removed because this var is used a few lines below
        $list = (is_array($list) ? implode(',', array_map('intval', $list)) : (int)$list);
        if (!preg_match('/^[0-9,]+$/', $list)) {
            return false;
        }

        // Delete associated restrictions on cart rules
        Db::getInstance()->execute('
		DELETE crprv
		FROM `'._DB_PREFIX_.'cart_rule_product_rule` crpr
		LEFT JOIN `'._DB_PREFIX_.'cart_rule_product_rule_value` crprv ON crpr.`id_product_rule` = crprv.`id_product_rule`
		WHERE crpr.`type` = "'.pSQL($type).'"
		AND crprv.`id_item` IN ('.$list.')'); // $list is checked a few lines above

        // Delete the product rules that does not have any values
        if (Db::getInstance()->Affected_Rows() > 0) {
            Db::getInstance()->delete('cart_rule_product_rule', 'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_product_rule_value`
																							WHERE `'._DB_PREFIX_.'cart_rule_product_rule`.`id_product_rule` = `'._DB_PREFIX_.'cart_rule_product_rule_value`.`id_product_rule`)');
        }
        // If the product rules were the only conditions of a product rule group, delete the product rule group
        if (Db::getInstance()->Affected_Rows() > 0) {
            Db::getInstance()->delete('cart_rule_product_rule_group', 'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_product_rule`
																						WHERE `'._DB_PREFIX_.'cart_rule_product_rule`.`id_product_rule_group` = `'._DB_PREFIX_.'cart_rule_product_rule_group`.`id_product_rule_group`)');
        }

        // If the product rule group were the only restrictions of a cart rule, update de cart rule restriction cache
        if (Db::getInstance()->Affected_Rows() > 0) {
            Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'cart_rule` cr
				LEFT JOIN `'._DB_PREFIX_.'cart_rule_product_rule_group` crprg ON cr.id_cart_rule = crprg.id_cart_rule
				SET product_restriction = IF(crprg.id_product_rule_group IS NULL, 0, 1)');
        }

        return true;
    }

    /**
     * @param $name
     * @param $id_lang
     * @return array
     */
    public static function getCartsRuleByCode($name, $id_lang, $extended = false)
    {
        $sql_base = 'SELECT cr.*, crl.*
						FROM '._DB_PREFIX_.'cart_rule cr
						LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)$id_lang.')';
        if ($extended) {
            return Db::getInstance()->executeS('('.$sql_base.' WHERE code LIKE \'%'.pSQL($name).'%\') UNION ('.$sql_base.' WHERE name LIKE \'%'.pSQL($name).'%\')');
        } else {
            return Db::getInstance()->executeS($sql_base.' WHERE code LIKE \'%'.pSQL($name).'%\'');
        }
    }
}
