<?php
/**
 * 2007-2018 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

require_once _PS_MODULE_DIR_.'paypal/api/ApiPaypalPlus.php';

define('URL_PPP_CREATE_TOKEN', '/v1/oauth2/token');
define('URL_PPP_CREATE_PAYMENT', '/v1/payments/payment');
define('URL_PPP_LOOK_UP', '/v1/payments/payment/');
define('URL_PPP_WEBPROFILE', '/v1/payment-experience/web-profiles');
define('URL_PPP_EXECUTE_PAYMENT', '/v1/payments/payment/');
define('URL_PPP_EXECUTE_REFUND', '/v1/payments/sale/');

define('URL_PPP_PATCH', '/v1/payments/payment/');


class CallApiPaypalPlus extends ApiPaypalPlus
{
    protected $cart = null;
    protected $customer = null;
    public $id_payment;

    public function setParams($params)
    {
        $this->cart = new Cart($params['cart']->id);
        $this->customer = new Customer($params['cookie']->id_customer);
    }

    public function getApprovalUrl()
    {
        /*
         * Récupération du token
         */
        $accessToken = $this->getToken(URL_PPP_CREATE_TOKEN, array('grant_type' => 'client_credentials'));

        if ($accessToken != false) {

            $result = json_decode($this->createPayment($this->customer, $this->cart, $accessToken));

            if (isset($result->links)) {

                foreach ($result->links as $link) {

                    if ($link->rel == 'approval_url') {
                        $this->id_payment = $result->id;
                        return $link->href;
                    }
                }
            }
        }
        return false;
    }

    public function lookUpPayment($paymentId)
    {

        if ($paymentId == 'NULL') {
            return false;
        }

        $accessToken = $this->refreshToken();

        $header = array(
            'Content-Type:application/json',
            'Authorization:Bearer '.$accessToken,
        );

        return $this->sendByCURL(URL_PPP_LOOK_UP.$paymentId, false, $header);
    }

    public function executePayment($payer_id, $paymentId)
    {

        if ($payer_id == 'NULL' || $paymentId == 'NULL') {
            return false;
        }

        $accessToken = $this->refreshToken();

        $header = array(
            'Content-Type:application/json',
            'Authorization:Bearer '.$accessToken,
        );

        $data = array('payer_id' => $payer_id);
        $response = $this->sendByCURL(URL_PPP_EXECUTE_PAYMENT.$paymentId.'/execute/', json_encode($data), $header);

        return $response;
    }

    public function executeRefund($paymentId, $data)
    {

        if ($paymentId == 'NULL' || !is_object($data)) {
            return false;
        }

        $accessToken = $this->refreshToken();

        $header = array(
            'Content-Type:application/json',
            'Authorization:Bearer '.$accessToken,
        );

        return $this->sendByCURL(URL_PPP_EXECUTE_REFUND.$paymentId.'/refund', json_encode($data), $header);
    }

    public function patch($id_payment, $address)
    {
        /*
        $totalCartWithTax = $cart->getOrderTotal(true);
        $totalCartWithoutTax = $cart->getOrderTotal(false);
        $total_tax = $totalCartWithTax - $totalCartWithoutTax;
        if ($cart->gift) {
            if (version_compare(_PS_VERSION_, '1.5.3.0', '>=')) {
                $giftWithoutTax = $cart->getGiftWrappingPrice(false);
            } else {
                $giftWithoutTax = (float) (Configuration::get('PS_GIFT_WRAPPING_PRICE'));
            }

        } else {
            $giftWithoutTax = 0;
        }
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $totalShippingCostWithoutTax = $cart->getOrderShippingCost(null, false);
        } else {
            $totalShippingCostWithoutTax = $cart->getTotalShippingCost(null, false);
        }

        $oCurrency = new Currency($cart->id_currency);
        $country = new Country((int) $address->id_country);
        $iso_code = $country->iso_code;

        $state = new State($address->id_state);
        $shipping_address = new stdClass();
        $shipping_address->recipient_name = $address->alias;
        $shipping_address->type = 'residential';
        $shipping_address->line1 = $address->address1;
        $shipping_address->line2 = $address->address2;
        $shipping_address->city = $address->city;
        $shipping_address->country_code = $iso_code;
        $shipping_address->postal_code = $address->postcode;
        $shipping_address->state = ($state->iso_code == null) ? '' : $state->iso_code;
        $shipping_address->phone = $address->phone;


        $payment = new stdClass();

        $payment->transactions = array();
        $payment->transactions[0] = new stdClass();
        $payment->transactions[0]->item_list = new stdClass();
        $payment->transactions[0]->item_list->shipping_address = $shipping_address;
        $payment->transactions[0]->amount = new stdClass();
        $payment->transactions[0]->amount->total = number_format($totalCartWithTax, 2);
        $payment->transactions[0]->amount->currency = $oCurrency->iso_code;
        $payment->transactions[0]->amount->details = new stdClass();;
        $payment->transactions[0]->amount->details->shipping = number_format($totalShippingCostWithoutTax, 2);
        $payment->transactions[0]->amount->details->tax = number_format($total_tax, 2);
        $payment->transactions[0]->amount->details->handling_fee = number_format($giftWithoutTax, 2);
        $payment->transactions[0]->amount->details->subtotal = number_format($totalCartWithoutTax - $totalShippingCostWithoutTax - $giftWithoutTax, 2);

        */

        $country = new Country((int) $address->id_country);

        $state = new State($address->id_state);

        $payment = array(0 => new stdClass);
        $payment[0]->op = 'add';
        $payment[0]->path = '/transactions/0/item_list/shipping_address';
        $payment[0]->value = new stdClass();
        $payment[0]->value->line1 = $address->address1;
        $payment[0]->value->city = $address->city;
        $payment[0]->value->recipient_name = $address->firstname.' '.$address->lastname;//$address->alias;
        $payment[0]->value->state = ($state->iso_code == null) ? '' : $state->iso_code;
        $payment[0]->value->country_code = $country->iso_code;
        $payment[0]->value->postal_code = $address->postcode;

        $accessToken = $this->refreshToken();
        $header = array(
            'Content-Type:application/json',
            'Authorization:Bearer '.$accessToken,
        );
        $body = str_replace('\/transactions\/0\/item_list\/shipping_address', '/transactions/0/item_list/shipping_address', json_encode($payment));
        return $this->sendByCURL(URL_PPP_PATCH.$id_payment, $body, $header, false, 'PATCH');
    }
}
