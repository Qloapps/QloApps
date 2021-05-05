<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once dirname(__FILE__).'/../../../config/config.inc.php';
include_once _PS_MODULE_DIR_.'paypal/paypal.php';

/*
 * Instant payment notification class.
 * (wait for PayPal payment confirmation, then validate order)
 */
class PayPalNotifier extends PayPal
{
    public function confirmOrder($custom)
    {
        $cart = new Cart((int) $custom['id_cart']);

        $cart_details = $cart->getSummaryDetails(null, true);
        $cart_hash = sha1(serialize($cart->nbProducts()));

        $this->context->cart = $cart;
        $address = new Address((int) $cart->id_address_invoice);
        $this->context->country = new Country((int) $address->id_country);
        $this->context->customer = new Customer((int) $cart->id_customer);
        $this->context->language = new Language((int) $cart->id_lang);
        $this->context->currency = new Currency((int) $cart->id_currency);

        if (isset($cart->id_shop)) {
            $this->context->shop = new Shop($cart->id_shop);
        }

        $result = $this->getResult();

        if (strcmp(trim($result), "VERIFIED") == 0) {
            $currency_decimals = is_array($this->context->currency) ? (int) $this->context->currency['decimals'] : (int) $this->context->currency->decimals;
            $this->decimals = $currency_decimals * _PS_PRICE_DISPLAY_PRECISION_;

            $message = null;
            $mc_gross = Tools::ps_round(Tools::getValue('mc_gross'), $this->decimals);

            $cart_details = $cart->getSummaryDetails(null, true);

            $shipping = $cart_details['total_shipping_tax_exc'];
            $subtotal = $cart_details['total_price_without_tax'] - $cart_details['total_shipping_tax_exc'];
            $tax = $cart_details['total_tax'];

            $total_price = Tools::ps_round($shipping + $subtotal + $tax, $this->decimals);

            if ($this->comp($mc_gross, $total_price, 2) !== 0) {
                $payment = (int) Configuration::get('PS_OS_ERROR');
                $message = $this->l('Price paid on paypal is not the same that on PrestaShop.').'<br />';
            } elseif ($custom['hash'] != $cart_hash) {
                $payment = (int) Configuration::get('PS_OS_ERROR');
                $message = $this->l('Cart changed, please retry.').'<br />';
            } else {
                $payment = (int) Configuration::get('PS_OS_PAYMENT');
                $message = $this->l('Payment accepted.').'<br />';
            }

            $customer = new Customer((int) $cart->id_customer);
            $transaction = PayPalOrder::getTransactionDetails(false);

            if (_PS_VERSION_ < '1.5') {
                $shop = null;
            } else {
                $shop_id = $this->context->shop->id;
                $shop = new Shop($shop_id);
            }

            $this->validateOrder($cart->id, $payment, $total_price, $this->displayName, $message, $transaction, $cart->id_currency, false, $customer->secure_key, $shop);
        }
    }

    public function getResult()
    {
        if ((int) Configuration::get('PAYPAL_SANDBOX') == 1) {
            $action_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_notify-validate';
        } else {
            $action_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_notify-validate';
        }

        $request = '&'.http_build_query($_POST, '&');
        return Tools::file_get_contents($action_url.$request);
    }
}

if ($custom = Tools::getValue('custom')) {
    $notifier = new PayPalNotifier();
    $result = json_decode($custom, true);
    $notifier->confirmOrder($result);
}
