<?php
/* You can debug the data by the DEBUG true in the module ipn.log file*/
define('DEBUG', true);
define('LOG_FILE', _PS_MODULE_DIR_.'wkpaypaladaptive/ipn.log');
class WkPaypalAdaptiveValidationModuleFrontController extends ModuleFrontController
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
        $this->mppaypal = new WkPayPalAdaptive();
        if ($this->mppaypal->active) {
            /* Step 1 - Double-check that the order sent by PayPal is valid one */
            // Post IPN data back to PayPal to validate the IPN data is genuine
            // Without this step anyone can fake IPN data
            $result = $this->_checkPayPalOrder();

            // Inspect IPN validation result and act accordingly
            // Split response headers and payload, a better way for strcmp
            $tokens = explode("\r\n\r\n", trim($result));
            $result = trim(end($tokens));
            if (strcmp($result, 'VERIFIED') == 0) {
                $this->_createPayPalAdaptiveOrder();
            } elseif (strcmp($result, 'INVALID') == 0) {
                if (DEBUG == true) {
                    $pay_key = Tools::getValue('pay_key');
                    error_log(date('[Y-m-d H:i e] ')."Invalid IPN pay key: $pay_key".PHP_EOL, 3, LOG_FILE);
                }
            } else {
                error_log(date('[Y-m-d H:i e] ')."Invalid PayPal order, please contact our Customer service.".PHP_EOL, 3, LOG_FILE);
            }
        }
    }

    public function _createPayPalAdaptiveOrder()
    {
        $errors = array();

        /* Step 2 - Check the custom field returned by PayPal id_cart & id_shop */
        $id_cart = Tools::getValue('id_cart');
        $id_shop = Tools::getValue('id_shop');

        if (!$id_cart) {
            $errors[] = $this->mppaypal->l('Custom field id_cart not found');
        } elseif (!$id_shop) {
            $errors[] = $this->mppaypal->l('Custom field id_shop not found');
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
                $errors[] = $this->mppaypal->l('Invalid Cart ID');
            } else {
                $pay_key = Tools::getValue('pay_key');
                if ($pay_key && !empty($pay_key)) {
                    // Get Payment details by Pay key
                    $payment = $this->_getPaymentDetails($pay_key);
                    
                    if (DEBUG == true) {
                    	error_log(date('[Y-m-d H:i e] ')."Payment Details: ".Tools::jsonEncode($payment).PHP_EOL, 3, LOG_FILE);
                	}
                    $currency = new Currency((int)Currency::getIdByIsoCode($payment['currencyCode']));
                    if (!Validate::isLoadedObject($currency) || $currency->id != $cart->id_currency) {
                        $errors[] = $this->mppaypal->l('Invalid Currency ID').' '.($currency->id.'|'.$cart->id_currency);
                    } else {
                        /* Forcing the context currency to the order currency */
                        $context->currency = $currency;
                        /* Step 4 - Determine the order status in accordance with the response from PayPal */
                        $payment_status = $payment['status'];

                        // Check status of the payment to set order status
                        if (Tools::strtoupper($payment_status) == 'PROCESSING') {
                            $order_status = (int) Configuration::get('PS_OS_PAYPAL');
                        } elseif (Tools::strtoupper($payment_status) == 'PENDING') {
                            $order_status = (int) Configuration::get('PS_OS_PAYPAL');
                        } elseif (Tools::strtoupper($payment_status) == 'COMPLETED') {
                            $order_status = (int) Configuration::get('PS_OS_PAYMENT');
                        } elseif (Tools::strtoupper($payment_status) == 'REFUNDED') {
                            $order_status = (int) Configuration::get('PS_OS_REFUND');
                        } else {
                            $order_status = (int) Configuration::get('PS_OS_ERROR');
                        }

                        /*END*/

                        /* Step 5a - If the order already exists, it may be an update sent by PayPal - we need to update the order status */
                        if ($cart->OrderExists()) {
                            $order = new Order((int)Order::getOrderByCartId($cart->id));
                            $currentOrderState = (int) $order->getCurrentState();
                            if ($currentOrderState != $order_status) {
                                $reference = $order->reference;
                                $paypalTransaction = new WkPaypalTransaction();
                                if ($orders = $paypalTransaction->getOrdersByReference($reference)) {
                                    foreach ($orders as $mpOrder) {
                                        $order = new Order($mpOrder['id_order']);
                                        $new_history = new OrderHistory();
                                        $new_history->id_order = (int)$mpOrder['id_order'];
                                        $new_history->changeIdOrderState((int)$order_status, $order, true);
                                        $new_history->addWithemail(true);    
                                    }
                                } else {
                                    $new_history = new OrderHistory();
                                    $new_history->id_order = (int)$order->id;
                                    $new_history->changeIdOrderState((int)$order_status, $order, true);
                                    $new_history->addWithemail(true);
                                }
                            }
                        } else {
                            /* Step 5b - Else, it is a new order that we need to create in the database */
                            $customer = new Customer((int)$cart->id_customer);
                            $context->customer = $customer;

                            if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT')) {
                                $obj_customer_adv = new HotelCustomerAdvancedPayment();
                                $order_total = $obj_customer_adv->getOrdertTotal($this->context->cart->id, $this->context->cart->id_guest);
                            } else {
                                $order_total = $cart->getOrderTotal(true, Cart::BOTH);
                            }

                            if ($this->mppaypal->validateOrder((int)$cart->id, (int)$order_status, $order_total, $this->mppaypal->displayName, null, array(), null, false, $customer->secure_key, $shop)) {
                                $this->addTransactionDetails($payment);
                            }
                        }
                        /* Important: we need to send back "OK" to PayPal */
                        die('OK');
                    }
                }
            }
            /* Not displayed to the customer (IPN is viewed/called only by PayPal */
            if (!empty($errors)) {
            	$errors = Tools::jsonEncode($errors);
	            error_log(date('[Y-m-d H:i e] ')."Errors: $errors".PHP_EOL, 3, LOG_FILE);
	            d($errors);
            }
        }
    }

    public function _createRequestParams($raw_post_data)
    {
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }

        // read the post from PayPal system and add 'cmd'
        $params = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $params .= "&$key=$value";
        }

        return $params;
    }

    public function _checkPayPalOrder()
    {
        $raw_post_data = file_get_contents('php://input');
        // Create Request Parameter
        $params = $this->_createRequestParams($raw_post_data);

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
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
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
        $result = curl_exec($ch);
        if (curl_errno($ch) != 0) {
            if (DEBUG == true) {
                error_log(date('[Y-m-d H:i e] ')."Can't connect to PayPal to validate IPN message: ".curl_error($ch).PHP_EOL, 3, LOG_FILE);
            }
            curl_close($ch);
            exit;
        } else {
            // Log the entire HTTP response if debug is switched on.
            if (DEBUG == true) {
                error_log(date('[Y-m-d H:i e] ').'HTTP request of validation request:'.curl_getinfo($ch, CURLINFO_HEADER_OUT)." for IPN payload: ".PHP_EOL, 3, LOG_FILE);
                error_log(date('[Y-m-d H:i e] ')."HTTP response of validation request: $result".PHP_EOL, 3, LOG_FILE);
            }
            curl_close($ch);
        }

        return $result;
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
        $response = Tools::jsonDecode(curl_exec($ch),true);
        $response = $this->validateDelayedPaymentStatus($response); // for delayed chained payment
        $response = $this->checkAndSetRefundStatus($response); // for refund

        return $response;
    }

    public function validateDelayedPaymentStatus($paymentInfo)
    {
        $paymentInfo['paypalPaymentMethod'] = 1; // set default parallel payment method
        if (array_key_exists('paymentInfoList', $paymentInfo)) {
            if (array_key_exists('paymentInfo', $paymentInfo['paymentInfoList'])) {
                foreach ($paymentInfo['paymentInfoList']['paymentInfo'] as $receiver) {
                    if ($receiver['receiver']['primary'] == 'true') {
                        if ($paymentInfo['actionType'] == 'PAY_PRIMARY') { // for delayed chained payment
                            $paymentInfo['paypalPaymentMethod'] = 3;
                            $paymentInfo['status'] = $receiver['transactionStatus'];
                        } else {
                            $paymentInfo['paypalPaymentMethod'] = 2;
                        }
                        break;
                    }
                }
            }
        }

        return $paymentInfo;
    }

    public function checkAndSetRefundStatus($paymentInfo)
    {
        if (array_key_exists('paymentInfoList', $paymentInfo)) {
            if (array_key_exists('paymentInfo', $paymentInfo['paymentInfoList'])) {
                foreach ($paymentInfo['paymentInfoList']['paymentInfo'] as $receiver) {
                    if ($receiver['transactionStatus'] == 'REFUNDED') {
                        $paymentInfo['status'] = $receiver['transactionStatus'];
                        break;
                    }
                }
            }
        }

        return $paymentInfo;
    }

    public function addTransactionDetails($payment)
    {
        $obj_txn = new WkPaypalTransaction();
        $obj_txn->pay_key = $payment['payKey'];
        $obj_txn->id_cart = Tools::getValue('id_cart');
        $obj_txn->action_type = $payment['actionType'];
        $obj_txn->payment_method = $payment['paypalPaymentMethod'];
        $obj_txn->currency_code = $payment['currencyCode'];
        $obj_txn->sender_email = $payment['senderEmail'];
        $obj_txn->status = $payment['status'];
        $obj_txn->memo = $payment['memo'];
        $obj_txn->payment_info = Tools::jsonEncode($payment['paymentInfoList']);
        $obj_txn->is_refunded = 0;
        if ($payment['status'] == 'COMPLETED' && $payment['actionType'] == 'PAY_PRIMARY') {
            $obj_txn->is_delayed_paid = 0;
        } else {
            $obj_txn->is_delayed_paid = 1;
        }
        $obj_txn->save();
    }
}
