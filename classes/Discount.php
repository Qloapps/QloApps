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

/**
 * @deprecated 1.5.0.1
 */
class DiscountCore extends CartRule
{
    const PERCENT = 1;
    const AMOUNT = 2;
    const FREE_SHIPPING = 3;

    public function __get($key)
    {
        Tools::displayAsDeprecated();

        if ($key == 'id_group') {
            return 0;
        }
        if ($key == 'id_discount_type') {
            if ($this->free_shipping) {
                return Discount::FREE_SHIPPING;
            }
            if ($this->reduction_percent > 0) {
                return Discount::PERCENT;
            }
            if ($this->reduction_amount > 0) {
                return Discount::AMOUNT;
            }
        }
        if ($key == 'name') {
            return $this->code;
        }
        if ($key == 'value') {
            if ($this->reduction_percent > 0) {
                return $this->reduction_percent;
            }
            if ($this->reduction_amount > 0) {
                return $this->reduction_amount;
            }
        }
        if ($key == 'cumulable') {
            return $this->cart_rule_restriction;
        }
        if ($key == 'cumulable_reduction') {
            return false;
        }
        if ($key == 'minimal') {
            return $this->minimum_amount;
        }
        if ($key == 'include_tax') {
            return $this->reduction_tax;
        }
        if ($key == 'behavior_not_exhausted') {
            return $this->partial_use;
        }
        if ($key == 'cart_display') {
            return true;
        }

        return $this->{$key};
    }

    public function __set($key, $value)
    {
        Tools::displayAsDeprecated();

        if ($key == 'id_discount_type') {
            if ($value == Discount::FREE_SHIPPING) {
                $this->free_shipping = true;
                $this->reduction_percent = false;
                $this->reduction_amount = false;
            }
            if ($value == Discount::PERCENT) {
                $this->free_shipping = false;
                $this->reduction_percent = true;
                $this->reduction_amount = false;
            }
            if ($value == Discount::AMOUNT) {
                $this->free_shipping = false;
                $this->reduction_percent = false;
                $this->reduction_amount = true;
            }
        }

        if ($key == 'code') {
            $this->name[Configuration::get('PS_LANG_DEFAULT')] = $value;
        }

        if ($key == 'value') {
            if ($this->reduction_percent) {
                $this->reduction_percent = $value;
            }
            if ($this->reduction_amount) {
                $this->reduction_amount = $value;
            }
        }
        if ($key == 'cumulable') {
            $this->cart_rule_restriction = 1;
        }
        if ($key == 'minimal') {
            $this->minimum_amount = $value;
        }
        if ($key == 'include_tax') {
            $this->reduction_tax = $value;
        }
        if ($key == 'behavior_not_exhausted') {
            $this->partial_use = $value;
        }

        $this->{$key} = $value;
    }

    public function __call($method, $args)
    {
        Tools::displayAsDeprecated();
        $obj = $this->parent;
        if (in_array($method, array('add', 'update', 'getIdByName', 'getCustomerDiscounts', 'getValue', 'discountExists', 'createOrderDiscount', 'getVouchersToCartDisplay', 'display'))) {
            $obj = $this;
        }
        return call_user_func_array(array($obj, $method), $args);
    }

    /**
     * @deprecated 1.5.0.1
     */
    public function add($autodate = true, $nullValues = false, $categories = null)
    {
        $r = parent::add($autodate, $nullValues);
        // Todo : manage categories
        return $r;
    }

    /**
     * @deprecated 1.5.0.1
     */
    public function update($autodate = true, $nullValues = false, $categories = null)
    {
        $r = parent::update($autodate, $nullValues);
        // Todo : manage categories
        return $r;
    }

    /**
     * @deprecated 1.5.0.1
     */
    public static function getIdByName($code)
    {
        return parent::getIdByCode($code);
    }

    /**
     * @deprecated 1.5.0.1
     */
    public static function getCustomerDiscounts($id_lang, $id_customer, $active = false, $includeGenericOnes = true, $hasStock = false, Cart $cart = null)
    {
        return parent::getCustomerCartRules($id_lang, $id_customer, $active, $includeGenericOnes, $hasStock, $cart);
    }

    /**
     * @deprecated 1.5.0.1
     */
    public static function getVouchersToCartDisplay($id_lang, $id_customer)
    {
        return CartRule::getCustomerCartRules($id_lang, $id_customer);
    }

    /**
     * @deprecated 1.5.0.1
     */
    public function getValue($nb_discounts = 0, $order_total_products = 0, $shipping_fees = 0, $id_cart = false, $useTax = true, Currency $currency = null, Shop $shop = null)
    {
        $context = Context::getContext();
        if ((int)$id_cart) {
            $context->cart = new Cart($id_cart);
        }
        if (Validate::isLoadedObject($currency)) {
            $context->currency = $currency;
        }
        if (Validate::isLoadedObject($shop)) {
            $context->shop = $shop;
        }
        return parent::getContextualValue($useTax, $context);
    }

    /**
     * @deprecated 1.5.0.1
     */
    public static function discountExists($discountName, $id_discount = 0)
    {
        return parent::cartRuleExists($discountName);
    }

    /**
     * @deprecated 1.5.0.1
     * @param Order $order
     * @return Discount
     */
    public static function createOrderDiscount($order, $productList, $qtyList, $name, $shipping_cost = false, $id_category = 0, $subcategory = 0)
    {
        $products = $order->getProducts(false, $productList, $qtyList);

        // Totals are stored in the order currency (or at least should be)
        $total = $order->getTotalProductsWithTaxes($products);
        $discounts = $order->getDiscounts(true);
        $total_tmp = $total;
        foreach ($discounts as $discount) {
            if ($discount['id_discount_type'] == Discount::PERCENT) {
                $total -= $total_tmp * ($discount['value'] / 100);
            } elseif ($discount['id_discount_type'] == Discount::AMOUNT) {
                $total -= ($discount['value'] * ($total_tmp / $order->total_products_wt));
            }
        }
        if ($shipping_cost) {
            $total += $order->total_shipping;
        }

        // create discount
        $voucher = new Discount();
        $voucher->id_discount_type = Discount::AMOUNT;
        foreach (Language::getIDs((bool)$order) as $id_lang) {
            $voucher->description[$id_lang] = strval($name).(int)($order->id);
        }
        $voucher->value = (float)($total);
        $voucher->name = 'V0C'.(int)($order->id_customer).'O'.(int)($order->id);
        $voucher->id_customer = (int)($order->id_customer);
        $voucher->id_currency = (int)($order->id_currency);
        $voucher->quantity = 1;
        $voucher->quantity_per_user = 1;
        $voucher->cumulable = 1;
        $voucher->cumulable_reduction = 1;
        $voucher->minimal = (float)($voucher->value);
        $voucher->active = 1;
        $voucher->cart_display = 1;

        $now = time();
        $voucher->date_from = date('Y-m-d H:i:s', $now);
        $voucher->date_to = date('Y-m-d H:i:s', $now + (3600 * 24 * 365.25)); /* 1 year */
        if (!$voucher->validateFieldsLang(false) || !$voucher->add()) {
            return false;
        }
        // set correct name
        $voucher->name = 'V'.(int)($voucher->id).'C'.(int)($order->id_customer).'O'.$order->id;
        if (!$voucher->update()) {
            return false;
        }

        return $voucher;
    }

    /**
     * @deprecated 1.5.0.1
     */
    public static function display($value, $type, $currency = null)
    {
        if ((float)$value && (int)$type) {
            if ($type == 1) {
                return $value.chr(37);
            } // ASCII #37 --> % (percent)
            elseif ($type == 2) {
                return Tools::displayPrice($value, $currency);
            }
        }
        return ''; // return a string because it's a display method
    }
}
