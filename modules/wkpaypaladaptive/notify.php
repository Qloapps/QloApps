<?php
include dirname(__FILE__).'/../../config/config.inc.php';
//include(dirname(__FILE__).'/../../header.php');
include dirname(__FILE__).'/wkpaypaladaptive.php';
define('DEBUG', 1);
define('LOG_FILE', './ipn.log');
/* This file is NOT in use currently, IPN is manage by front controller validation.php */
class WkPayPalValidation extends WkPayPalAdaptive
{
    public $ssl = true;
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
        $this->APIRequestFormat = 'JSON';
        $this->APIResponseFormat = 'JSON';
        $this->SandboxStatus = Configuration::get('WK_PAYPAL_SANDBOX');
    }

    public function initContent()
    {
        $this->wkpaypal = new WkPayPalAdaptive();
        if ($this->wkpaypal->active) {
            $raw_post_data = file_get_contents('php://input');
            $raw_post_array = explode('&', $raw_post_data);
            $myPost = array();
            foreach ($raw_post_array as $keyval) {
                $keyval = explode('=', $keyval);
                if (count($keyval) == 2) {
                    $myPost[$keyval[0]] = urldecode($keyval[1]);
                }
            }

            // read the post from PayPal system and add 'cmd'
            $req = 'cmd=_notify-validate';
            if (function_exists('get_magic_quotes_gpc')) {
                $get_magic_quotes_exists = true;
            }
            foreach ($myPost as $key => $value) {
                if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                    $value = urlencode(stripslashes($value));
                } else {
                    $value = urlencode($value);
                }
                $req .= "&$key=$value";
            }

            // Post IPN data back to PayPal to validate the IPN data is genuine
            // Without this step anyone can fake IPN data

            /* Step 1 - Double-check that the order sent by PayPal is valid one */
            if ($this->SandboxStatus == 1) {
                $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            } else {
                $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
            }

            $ch = curl_init($paypal_url);
            if ($ch == false) {
                return false;
            }

            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

            if (DEBUG == true) {
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
            }

            // CONFIG: Optional proxy configuration
            //curl_setopt($ch, CURLOPT_PROXY, $proxy);
            //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);

            // Set TCP timeout to 30 seconds
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
            $res = curl_exec($ch);
            if (curl_errno($ch) != 0) {
                if (DEBUG == true) {
                    error_log(date('[Y-m-d H:i e] ')."Can't connect to PayPal to validate IPN message: ".curl_error($ch).PHP_EOL, 3, LOG_FILE);
                }
                curl_close($ch);
                exit;
            } else {
                // Log the entire HTTP response if debug is switched on.
                if (DEBUG == true) {
                    error_log(date('[Y-m-d H:i e] ').'HTTP request of validation request:'.curl_getinfo($ch, CURLINFO_HEADER_OUT)." for IPN payload: $req".PHP_EOL, 3, LOG_FILE);
                    error_log(date('[Y-m-d H:i e] ')."HTTP response of validation request: $res".PHP_EOL, 3, LOG_FILE);
                }
                curl_close($ch);
            }

            // Inspect IPN validation result and act accordingly
            // Split response headers and payload, a better way for strcmp
            $tokens = explode("\r\n\r\n", trim($res));
            $res = trim(end($tokens));
            error_log(date('[Y-m-d H:i e] ').":---: Verified IPN POST SHARMA Details :---: $res ".PHP_EOL, 3, LOG_FILE);
            if (strcmp($res, 'VERIFIED') == 0) {
                error_log(date('[Y-m-d H:i e] ').":---: Verified IPN POST DHEERAJ Details :---: $_POST ".PHP_EOL, 3, LOG_FILE);
                $this->_paymentAdaptiveValidation();
            } elseif (strcmp($res, 'INVALID') == 0) {
                // log for manual investigation
                // Add business logic here which deals with invalid IPN messages
                if (DEBUG == true) {
                    error_log(date('[Y-m-d H:i e] ')."Invalid IPN: $req".PHP_EOL, 3, LOG_FILE);
                }
            } else {
                error_log(date('[Y-m-d H:i e] ')."Invalid PayPal order, please contact our Customer service.".PHP_EOL, 3, LOG_FILE);
            }
        }
    }

    public function _getPaymentDetails($pay_key)
    {
        $sandbox = '';
        if ($this->SandboxStatus == 1) {
            $sandbox = 'sandbox';
        }
        $url = trim("https://svcs.".$sandbox.".paypal.com/AdaptivePayments/PaymentDetails");
        $bodyparams = array (
                'payKey' => $pay_key,
                'requestEnvelope' => array(
                    'errorLanguage' => 'en_US',
                    'detailLevel' => 'ReturnAll',
                )
            );

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
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyparams));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        return Tools::jsonDecode(curl_exec($ch),true);
    }

    public function _paymentAdaptiveValidation()
    {
        $errors = array();

        /* Step 2 - Check the custom field returned by PayPal id_cart & id_shop */
        $id_cart = Tools::getValue('id_cart');
        $id_shop = Tools::getValue('id_shop');

        if (!$id_cart) {
            $errors[] = $this->wkpaypal->l('Custom field id_cart not found');
        } else if (!$id_shop){
            $errors[] = $this->wkpaypal->l('Custom field id_shop not found');
        } else {
            /* Step 3 - Check the shopping cart, the currency used to place the order and the amount really paid by the customer */
            $context = Context::getContext();

            // Setup context shop
            $shop = new Shop((int)$id_shop);
            $context->shop = $shop;

            // Setup context cart
            $cart = new Cart((int)$id_cart);
            $context->cart = $cart;

            if (!Validate::isLoadedObject($cart)) {
                $errors[] = $this->wkpaypal->l('Invalid Cart ID');
            } else {
                $pay_key = Tools::getValue('pay_key');
                if ($pay_key && !empty($pay_key)) {
                    // Get Payment details by Pay key
                    $payment = $this->_getPaymentDetails($pay_key);
                    error_log(date('[Y-m-d H:i e] ')."Payment DEtails: ".Tools::jsonEncode($payment).PHP_EOL, 3, LOG_FILE);
                    $currency = new Currency((int)Currency::getIdByIsoCode($payment['currencyCode']));
                    if (!Validate::isLoadedObject($currency) || $currency->id != $cart->id_currency) {
                        $errors[] = $this->wkpaypal->l('Invalid Currency ID').' '.($currency->id.'|'.$cart->id_currency);
                    } else {
                        /* Forcing the context currency to the order currency */
                        $context->currency = $currency;
                        // trying to get total paid
                        /*if (Tools::getValue('mc_gross') != $cart->getOrderTotal(true)) {
                            $errors[] = $this->paypal_usa->l('Invalid Amount paid');
                        } else {*/
                            /* Step 4 - Determine the order status in accordance with the response from PayPal */
                            $payment_status = $payment['status'];
                            if (Tools::strtoupper($payment_status) == 'COMPLETED') {
                                $order_status = (int) Configuration::get('PS_OS_PAYMENT');
                            } elseif (Tools::strtoupper($payment_status) == 'PENDING') {
                                $order_status = (int) Configuration::get('PS_OS_PAYPAL');
                            } elseif (Tools::strtoupper($payment_status) == 'REFUNDED') {
                                $order_status = (int) Configuration::get('PS_OS_REFUND');
                            } else {
                                $order_status = (int) Configuration::get('PS_OS_ERROR');
                            }

                            /* Step 5a - If the order already exists, it may be an update sent by PayPal - we need to update the order status */
                            if ($cart->OrderExists()) {
                                $order = new Order((int)Order::getOrderByCartId($cart->id));
                                $new_history = new OrderHistory();
                                $new_history->id_order = (int)$order->id;
                                $new_history->changeIdOrderState((int)$order_status, $order, true);
                                $new_history->addWithemail(true);
                            } else {
                                /* Step 5b - Else, it is a new order that we need to create in the database */
                                $customer = new Customer((int)$cart->id_customer);
                                $context->customer = $customer;
                                $message =
                                'actionType: '.$payment['actionType'].'
                                currencyCode: '.$payment['currencyCode'].'
                                feesPayer: '.$payment['feesPayer'].'
                                memo: '.$payment['memo'].'
                                payKey: '.$payment['payKey'].'
                                paymentInfoList: '.Tools::jsonEncode($payment['paymentInfoList']).'
                                responseEnvelope: '.Tools::jsonEncode($payment['responseEnvelope']).'
                                senderEmail: '.$payment['senderEmail'].'
                                shippingAddress: '.Tools::jsonEncode($payment['shippingAddress']).'
                                sender: '.$payment['sender'].'
                                status: '.$payment['status'].'
                                trackingId: '.$payment['trackingId'].'
                                verify_sign: '.Tools::getValue('verify_sign').'
                                Mode: '.(Tools::getValue('test_ipn') ? 'Test (Sandbox)' : 'Live');
                                if ($this->wkpaypal->validateOrder((int)$cart->id, (int)$order_status, $cart->getOrderTotal(true), $this->wkpaypal->displayName, $message, array(), null, false, $customer->secure_key, $shop)) {
                                    $this->addTransactionDetails($payment);
                                }
                            }
                            /* Important: we need to send back "OK" to PayPal */
                            die('OK');
                        //}
                    }
                }
            }
            /* Not displayed to the customer (IPN is viewed/called only by PayPal */
                $errors = Tools::jsonEncode($errors);
            error_log(date('[Y-m-d H:i e] ')."Errors: $errors".PHP_EOL, 3, LOG_FILE);
            d($errors);
        }        
    }

    public function addTransactionDetails($payment)
    {
        $obj_txn = new WkPaypalTransaction();
        if ($id = $obj_txn->getIdByPayKey($payment['payKey'])) {
            $obj_txn = new WkPaypalTransaction($id);
            $obj_txn->status = $payment['status'];
            $obj_txn->sender_email = $payment['senderEmail'];
            $obj_txn->action_type = $payment['actionType'];
            //$obj_txn->transaction_id = Tools::getValue('transaction[0].id');
            //$obj_txn->transaction_status = Tools::getValue('transaction[0].status');
            //$obj_txn->payment_request_date = Tools::getValue('payment_request_date');
            $obj_txn->save();
        }
    }
}

$validation = new WkPayPalValidation();
$validation->initContent();
