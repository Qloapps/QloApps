<?php
/**
* 2010-2016 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkPaypalAdaptivePaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $wkPaypalHelper;
    private $APIUserName;
    private $APIPassword;
    private $APISignature;
    private $APIAppID;
    private $APIRequestFormat;
    private $APIResponseFormat;
    private $SandboxStatus;

    public function __construct()
    {
        parent::__construct();
        $this->APIUserName = Configuration::get('APP_USERNAME');
        $this->APIPassword = Configuration::get('APP_PASSWORD');
        $this->APISignature = Configuration::get('APP_SIGNATURE');
        $this->APIAppID = Configuration::get('APP_ID');
        $this->APIRequestFormat = 'NV';
        $this->APIResponseFormat = 'JSON';
        $this->SandboxStatus = Configuration::get('WK_PAYPAL_SANDBOX');
        $this->wkPaypalHelper = new WkPaypalHelper();
    }

    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        $context = Context::getContext();

        parent::initContent();
        if ($this->context->customer->id) {
            if ($this->module->active) {
                $id_cart = $this->context->cart->id;
                if (isset($this->context->cart->id_shop)) {
                    $id_shop = $this->context->cart->id_shop;
                } else {
                    $id_shop = 0;
                }
                $context->cookie->__unset('c_id_cart');
                $context->cookie->__unset('c_amount_paid');
                $context->cookie->__set('c_id_cart', $id_cart);
                $cart = new Cart($id_cart);

                if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT')) {
                    $obj_customer_adv = new HotelCustomerAdvancedPayment();
                    $cart_total_price = $obj_customer_adv->getOrdertTotal($this->context->cart->id, $this->context->cart->id_guest);
                    $total_amount = $obj_customer_adv->getOrdertTotal($this->context->cart->id, $this->context->cart->id_guest);
                } else {
                    $cart_total_price = $cart->getTotalCart($id_cart);
                    $total_amount = $cart->getOrderTotal(true);
                }

                $context->cookie->__set('c_amount_paid', $cart_total_price);
                // paypal api start
                $sandbox = '';
                if ($this->SandboxStatus == 1) {
                    $sandbox = 'sandbox.';
                }

                $obj_currency = new Currency($this->context->cart->id_currency);

                $url = trim('https://svcs.'.$sandbox.'paypal.com/AdaptivePayments/Pay');
                $bodyparams = array(
                    'requestEnvelope.errorLanguage' => 'en_US',
                    'reverseAllParallelPaymentsOnError' => 'true',
                    'actionType' => 'PAY',
                    'currencyCode' => $obj_currency->iso_code,
                    'cancelUrl' => $this->context->link->getPageLink('order.php', ''),
                    'returnUrl' => $this->context->link->getPageLink('order-confirmation.php', null, null, array('id_cart' => (int)$id_cart, 'key' => $this->context->customer->secure_key, 'id_module' => $this->module->id)),
                    'ipnNotificationUrl' => $this->context->link->getModuleLink('wkpaypaladaptive', 'validation', array('id_cart' => (int)$id_cart, 'id_shop' => (int)$id_shop), Configuration::get('PS_SSL_ENABLED')),
                );

                //add reciver list in the body parameters
                $isPaymentError = 0;
                if ($total_amount < 0) {
                    $isPaymentError = 1;
                }

                if ($isPaymentError) {
                    $this->context->smarty->assign('exception', $this->module->l('There is some error in payment please contact our customer service department.', 'payment'));
                    $this->setTemplate('paypal_error.tpl');
                } else {
                    $bodyparams = $this->wkPaypalHelper->getReciverListDetails($total_amount, $bodyparams);
                    // convert payload array into url encoded query string
                    $body_data = http_build_query($bodyparams, '', chr(38));
                    try {
                        //Create cURL request
                        $response = $resultSetOptionArray = $this->cURLRequest($url, $body_data);
                        
                        //set paypal redirect url in checkout session
                        if (!empty($response['responseEnvelope']['ack']) && $response['responseEnvelope']['ack'] == 'Success') {
                            if (!empty($response['payKey'])) {
                                $setPaymentOptionUrl = trim("https://svcs.".$sandbox."paypal.com/AdaptivePayments/SetPaymentOptions");

                                $bodyparams = array (
                                    'requestEnvelope.errorLanguage' => 'en_US',
                                    'requestEnvelope.detailLevel' => 'ReturnAll',
                                    'payKey' => $response['payKey'],
                                    'senderOptions.referrerCode' => 'Webkul_SP',
                                );

                                $body_data = http_build_query($bodyparams, '', chr(38));

                                //Create cURL request
                                $resultSetOptionArray = $this->cURLRequest($setPaymentOptionUrl, $body_data);
                                if (!empty($resultSetOptionArray['responseEnvelope']['ack']) && $resultSetOptionArray['responseEnvelope']['ack'] == 'Success') {
                                    //set url to approve the transaction
                                    $payPalURL = "https://www.".$sandbox."paypal.com/webscr?cmd=_ap-payment&paykey=" . $response["payKey"];
                                    $this->context->smarty->assign('payPalURL', $payPalURL);
                                    $this->setTemplate('redirect_pay.tpl');
                                } elseif (isset($resultSetOptionArray['responseEnvelope']['ack']) && $resultSetOptionArray['responseEnvelope']['ack'] == 'Failure') {
                                    if (isset($resultSetOptionArray['error']) && is_array($resultSetOptionArray['error'])) {
                                        $this->context->smarty->assign('paypal_errors', $resultSetOptionArray['error']);
                                        $this->setTemplate('paypal_error.tpl');
                                    }
                                } else {
                                    $this->setTemplate('paypal_error.tpl');
                                }
                            }
                        } else {
                            $this->context->smarty->assign('paypal_errors', $response['error']);
                            $this->setTemplate('paypal_error.tpl');
                        }
                    } catch (Exception $e) {
                        $this->context->smarty->assign('exception', $e->getMessage());
                        $this->setTemplate('paypal_error.tpl');
                    }
                }
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function cURLRequest($url, $body_data)
    {
        //create request and add headers
        $headers = array(
            'X-PAYPAL-SECURITY-USERID: '.$this->APIUserName,
            'X-PAYPAL-SECURITY-PASSWORD: '.$this->APIPassword,
            'X-PAYPAL-SECURITY-SIGNATURE: '.$this->APISignature,
            'X-PAYPAL-REQUEST-DATA-FORMAT: '.$this->APIRequestFormat,
            'X-PAYPAL-RESPONSE-DATA-FORMAT: '.$this->APIResponseFormat,
            'X-PAYPAL-APPLICATION-ID: '.$this->APIAppID,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        return json_decode(curl_exec($ch), true);
    }
}
