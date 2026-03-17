# Payment Controllers and Transactions

Complete guide for implementing payment controllers, handling transactions, and managing order states in QloApps payment modules.

---

## Controller Types

### Payment Controller (Offline Payments)

Displays payment information and initiates payment flow.

**Example - Bankwire:**

```php
// controllers/front/payment.php
class BankwirePaymentModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Assign bank details to template
        $this->context->smarty->assign(array(
            'bankwire_owner' => Configuration::get('BANK_WIRE_OWNER'),
            'bankwire_details' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
            'bankwire_address' => nl2br(Configuration::get('BANK_WIRE_ADDRESS')),
            'total' => $this->context->cart->getOrderTotal(true, Cart::BOTH),
        ));

        $this->setTemplate('payment_execution.tpl');
    }
}
```

### Validation Controller (Offline Payments)

Processes payment and creates order for offline payment methods.

**Example - Bankwire:**

```php
// controllers/front/validation.php
class BankwireValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;

        // Validate cart
        if ($cart->id_customer == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check payment module is available
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'bankwire') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }

        // Validate service products in cart
        ServiceProductCartDetail::validateServiceProductsInCart();

        // Check order restrict conditions
        if (Module::isInstalled('hotelreservationsystem')
            && Module::isEnabled('hotelreservationsystem')
        ) {
            require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
            if (HotelOrderRestrictDate::validateOrderRestrictDateOnPayment($this)) {
                Tools::redirect('index.php?controller=order-opc');
            }
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $currency = $this->context->currency;

        // Calculate total (advance payment or full)
        if ($cart->is_advance_payment) {
            $total = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
        } else {
            $total = $cart->getOrderTotal(true, Cart::BOTH);
        }

        // Mail variables for order confirmation
        $mailVars = array(
            '{bankwire_owner}' => Configuration::get('BANK_WIRE_OWNER'),
            '{bankwire_details}' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
            '{bankwire_address}' => nl2br(Configuration::get('BANK_WIRE_ADDRESS'))
        );

        // Create order
        $this->module->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_AWAITING_PAYMENT'),
            $total,
            $this->module->displayName,
            NULL,
            $mailVars,
            (int)$currency->id,
            false,
            $customer->secure_key
        );

        // Redirect to order confirmation
        Tools::redirect(
            'index.php?controller=order-confirmation&id_cart='.$cart->id.
            '&id_module='.$this->module->id.
            '&id_order='.$this->module->currentOrder.
            '&key='.$customer->secure_key
        );
    }
}
```

---

## Online Payment Controllers

### Payment Controller (Online Payments)

Handles payment initiation and communication with payment gateway.

**Example - PayPal Commerce:**

```php
// controllers/front/payment.php
class QloPaypalCommercePaymentModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();

        $cart = $this->context->cart;

        // Validate cart
        if ($cart->id_customer == 0 || !$this->module->active) {
            Tools::redirect($this->context->link->getPageLink('order-opc', true, null));
        }

        // Check payment method availability
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'qlopaypalcommerce') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'payment'));
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect($this->context->link->getPageLink('order-opc', true, null));
        }
    }

    public function initContent()
    {
        parent::initContent();

        // Validate secure token
        if ($this->module->secure_key != Tools::getValue('token')) {
            die('Invalid token.');
        }

        if (Tools::isSubmit('action')) {
            $action = (int)Tools::getValue('action');
            switch ($action) {
                case 1: // Create order
                    $this->createPayPalOrder();
                    break;
                case 2: // Capture order
                    $this->capturePayPalOrder();
                    break;
                case 3: // Cancel
                    $this->handleCancellation();
                    break;
            }
        }
        die;
    }

    private function createPayPalOrder()
    {
        $json = Tools::file_get_contents('php://input');
        $orderDetails = Tools::jsonDecode($json, true);

        $cart = $this->context->cart;

        WkPaypalCommerceHelper::logMsg('payment', 'Payment initiated...', true);
        WkPaypalCommerceHelper::logMsg('payment', 'Cart ID: '. $cart->id);

        $ppCommerce = new PayPalCommerce();
        header('Content-Type: application/json');

        // Create order with PayPal
        $orderDetails['original'] = $this->getOrderDetails();
        $ppOrderData = $ppCommerce->orders->create($orderDetails);

        WkPaypalCommerceHelper::logMsg('payment', 'Payment response data: ', true);
        WkPaypalCommerceHelper::logMsg('payment', Tools::jsonEncode($ppOrderData));

        die(Tools::jsonEncode($ppOrderData));
    }

    private function capturePayPalOrder()
    {
        $orderID = Tools::getValue('order_id');
        $ppCommerce = new PayPalCommerce();
        $returnData = $ppCommerce->orders->get($orderID);

        $cart = $this->context->cart;
        $customer = new Customer($cart->id_customer);

        if (isset($returnData['data']['id'])) {
            $paypalOrderID = $returnData['data']['id'];

            // Save order data
            $this->saveOrderData($returnData);

            $currency = $this->context->currency;

            // Calculate total
            if ($cart->is_advance_payment) {
                $total = (float)$cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
            } else {
                $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
            }

            // Determine order status based on payment status
            if ($returnData['data']['status'] == 'COMPLETED') {
                if ($cart->is_advance_payment) {
                    $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                } else {
                    $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                }
            } else {
                $orderStatus = Configuration::get('PS_OS_AWAITING_PAYMENT');
            }

            // Create order
            $this->module->validateOrder(
                $cart->id,
                $orderStatus,
                $total,
                $this->module->l('PayPal Checkout', 'payment'),
                null,
                null,
                (int)$currency->id,
                false,
                $customer->secure_key
            );

            // Update order reference in transaction table
            $objOrder = new Order($this->module->currentOrder);
            WkPayPalCommerceOrder::updateOrderReference(
                $paypalOrderID,
                $objOrder->reference
            );

            // Redirect to confirmation
            $orderLink = $this->context->link->getPageLink(
                'order-confirmation',
                null,
                (int)$this->context->language->id,
                array(
                    'id_cart' => (int)$cart->id,
                    'id_module' => (int)$this->module->id,
                    'id_order' => (int)$this->module->currentOrder,
                    'key' => $customer->secure_key,
                )
            );
            Tools::redirect($orderLink);
        }
    }
}
```

### Process Payment Controller (Online Gateway)

Handles return from payment gateway after payment.

```php
// controllers/front/processpayment.php
class PaymentGatewayProcessPaymentModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $secretKey = PaymentGatewayService::getSecretKey();
        try {
            if (!empty($secretKey)) {
                if ($sessionId = Tools::getValue('session_id')) {
                    PaymentGatewayService::initializeGateway($secretKey);
                    
                    // Retrieve session from gateway (adapt to your gateway's SDK)
                    // Example: $session = \Gateway\Session::retrieve($sessionId);
                    if ($session) {
                        $this->module->logger->log('payment done', FileLogger::DEBUG);
                        $this->module->logger->log('session Id: '.$sessionId, FileLogger::DEBUG);

                        $objCart = new Cart((int) $session->metadata->id_cart);
                        $objCustomer = new Customer((int) $objCart->id_customer);

                        // Create order only if not already created
                        if ($objCart->OrderExists() == false) {
                            PaymentHelper::createOrder($session, $objCart);
                        }

                        // Redirect to confirmation page
                        Tools::redirect(
                            $this->context->link->getPageLink('order-confirmation').
                            '?id_cart='.$objCart->id.
                            '&id_module='.$this->module->id.
                            '&id_order='.Order::getOrderByCartId((int) $objCart->id).
                            '&success=1&key='.$objCustomer->secure_key
                        );
                    }
                }
            }
        } catch (Gateway_CardError $e) {
            PaymentGatewayService::createErrorLog($e, false);
        } catch (Gateway_ApiConnectionError $e) {
            PaymentGatewayService::createErrorLog($e, false);
        } catch (Gateway_InvalidRequestError $e) {
            PaymentGatewayService::createErrorLog($e, false);
        }

        Tools::redirect(
            $this->context->link->getPageLink('order-opc', true, null, array('payment_err' => 1))
        );
    }
}
```

---

## Webhook/Notification Controllers

### Webhook Controller (PayPal)

Receives asynchronous notifications from payment gateway.

```php
// controllers/front/webhook.php (Sandbox)
// controllers/front/callback.php (Production)
class QloPaypalCommerceWebhookModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Get webhook headers
        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_UPPER);

        // Get webhook payload
        $json = Tools::file_get_contents('php://input');

        $payload = array();

        // Extract signature verification data
        if ($headers) {
            $payload['transmission_id'] = $headers['PAYPAL-TRANSMISSION-ID'];
            $payload['cert_url'] = $headers['PAYPAL-CERT-URL'];
            $payload['transmission_time'] = $headers['PAYPAL-TRANSMISSION-TIME'];
            $payload['auth_algo'] = $headers['PAYPAL-AUTH-ALGO'];
            $payload['transmission_sig'] = $headers['PAYPAL-TRANSMISSION-SIG'];
            $payload['webhook_id'] = Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_WEBHOOK_ID');
        }

        if ($json) {
            $payload['webhook_event'] = Tools::jsonDecode($json, true);
        }

        if ($payload) {
            WkPaypalCommerceHelper::logMsg('webhook', 'Webhook initiated...', true);
            WkPaypalCommerceHelper::logMsg('webhook', 'Webhook payload data: ');
            WkPaypalCommerceHelper::logMsg('webhook', Tools::jsonEncode($payload));

            // Verify webhook signature
            $validateSig = WkPaypalCommerceHelper::validateWebhookSig($payload);

            WkPaypalCommerceHelper::logMsg('webhook', 'Webhook respose data: ');
            WkPaypalCommerceHelper::logMsg('webhook', Tools::jsonEncode($validateSig));

            if (isset($validateSig['verification_status'])
                && $validateSig['verification_status'] == 'SUCCESS'
            ) {
                $eventData = Tools::jsonDecode($json, true);
                $objWebhook = new WkPaypalCommerceWebhook();

                // Handle different event types
                switch ($eventData['event_type']) {
                    case 'CHECKOUT.ORDER.APPROVED':
                        $objWebhook->orderCompleted($eventData);
                        break;
                    case 'CHECKOUT.ORDER.COMPLETED':
                        $objWebhook->orderCompleted($eventData);
                        break;
                    case 'PAYMENT.CAPTURE.COMPLETED':
                        $objWebhook->captureCompleted($eventData);
                        break;
                    case 'PAYMENT.CAPTURE.DENIED':
                        $objWebhook->captureDenied($eventData);
                        break;
                    case 'PAYMENT.CAPTURE.PENDING':
                        $objWebhook->capturePending($eventData);
                        break;
                    case 'PAYMENT.CAPTURE.REFUNDED':
                        $objWebhook->captureRefunded($eventData);
                        break;
                    case 'PAYMENT.CAPTURE.REVERSED':
                        $objWebhook->captureRefunded($eventData);
                        break;
                }
            }
        }

        header("HTTP/1.1 200 OK");
        die;
    }
}
```

### Notify Controller (Online Gateway)

```php
// controllers/front/notify.php
class PaymentGatewayNotifyModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $mode = Tools::getValue('mode');

        // Get appropriate credentials based on mode
        if ($mode == 'live') {
            $secretKey = Configuration::get('PAYMENT_GATEWAY_LIVE_SECRET_KEY');
            $endpoint_secret = Configuration::get('PAYMENT_GATEWAY_LIVE_WEBHOOK_SECRET_KEY');
        } else {
            $secretKey = Configuration::get('PAYMENT_GATEWAY_TEST_SECRET_KEY');
            $endpoint_secret = Configuration::get('PAYMENT_GATEWAY_TEST_WEBHOOK_SECRET_KEY');
        }

        PaymentGatewayService::initializeGateway($secretKey);

        // Get webhook payload and signature
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_GATEWAY_SIGNATURE']; // Adapt to your gateway's header name
        $event = null;

        try {
            // Verify webhook signature (adapt to your gateway's verification method)
            // Example: $event = \Gateway\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
            $event = $this->verifyWebhookSignature($payload, $sig_header, $endpoint_secret);
        } catch(\UnexpectedValueException $e) {
            http_response_code(400);
            exit();
        } catch(\Exception $e) {
            http_response_code(400);
            exit();
        }

        $this->module->logger->log('Webhook : '.json_encode($event), FileLogger::DEBUG);

        // Handle different event types (adapt to your gateway's event names)
        switch ($event->type) {
            case 'payment.completed': // Generic event name
                $session = $event->data->object;
                $objCart = new Cart((int) $session->metadata->id_cart);

                if (!$objCart->orderExists()) {
                    $this->context->currency = new Currency($objCart->id_currency);
                    PaymentHelper::createOrder($session, $objCart);
                } else {
                    // Update existing order status
                    $this->updateOrderStatus($session);
                }
                break;

            case 'refund.updated':
                $refund = $event->data->object;
                $this->handleRefundUpdate($refund);
                break;

            default:
                $this->module->logger->log('Unknown webhook event:'.$event->type, FileLogger::DEBUG);
        }

        http_response_code(200);
        exit();
    }

    private function updateOrderStatus($session)
    {
        $transactionId = PaymentTransaction::getTransactionIdByPaymentIntent(
            $session->payment_intent
        );

        if (Validate::isLoadedObject($objTransaction = new PaymentTransaction($transactionId))) {
            $objCart = new Cart($objTransaction->id_cart);

            // Retrieve payment details from gateway (adapt to your gateway's SDK)
            // Example: $payment = \Gateway\Payment::retrieve($session->payment_intent);
            if ($payment) {
                // Determine status based on payment status
                if ($payment->status == 'succeeded') {
                    if ($objCart->is_advance_payment) {
                        $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                    } else {
                        $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                    }
                    $transactionStatus = PaymentTransaction::TRANSACTION_STATUS_SUCCESS;
                } elseif ($payment->status == 'canceled') {
                    $orderStatus = Configuration::get('PS_OS_CANCELED');
                    $transactionStatus = PaymentTransaction::TRANSACTION_STATUS_CANCELLED;
                } else {
                    $orderStatus = Configuration::get('_PS_OS_PROCESSING_');
                    $transactionStatus = PaymentTransaction::TRANSACTION_STATUS_PROCESSING;
                }

                // Update transaction status
                $objTransaction->status = $transactionStatus;
                $objTransaction->save();

                // Update all orders with this reference
                $orders = Order::getByReference($objTransaction->order_reference);
                foreach($orders as $order) {
                    PaymentGatewayService::updatePaymentStatus(
                        (int)$orderStatus,
                        $order->id_order
                    );
                }
            }
        }
    }
}
```

---

## validateOrder() Method Deep Dive

### Complete Parameter List

```php
public function validateOrder(
    $id_cart,                // Cart ID
    $id_order_state,         // Order state ID
    $amount_paid,            // Amount paid by customer
    $payment_method = 'Unknown',  // Payment method name
    $message = null,         // Optional message
    $extra_vars = array(),   // Extra variables (transaction_id, etc.)
    $currency_special = null,  // Currency override
    $dont_touch_amount = false,  // Skip amount rounding
    $secure_key = false,     // Customer secure key
    ?Shop $shop = null,      // Shop object
    $send_mails = true       // Send confirmation emails
)
```

### Usage Examples

**Offline Payment (Bankwire):**

```php
$this->module->validateOrder(
    $cart->id,
    Configuration::get('PS_OS_AWAITING_PAYMENT'),  // Awaiting payment status
    $total,
    $this->module->displayName,
    NULL,  // No message
    $mailVars,  // Email template variables
    (int)$currency->id,
    false,  // Don't touch amount
    $customer->secure_key
);
```

**Online Payment with Transaction ID (PayPal):**

```php
$this->module->validateOrder(
    $cart->id,
    $orderStatus,  // Status determined by payment result
    $total,
    $this->module->l('PayPal Checkout', 'payment'),
    null,  // No message
    null,  // No extra mail vars
    (int)$currency->id,
    false,
    $customer->secure_key
);
```

**Online Payment with Transaction ID:**

```php
$extra_vars['transaction_id'] = $chargeId;

$module->validateOrder(
    (int) $objCart->id,
    $orderStatus,
    $cartTotalAmount,
    $module->l('Payment Gateway Payment', 'processpayment'),
    null,
    $extra_vars,  // Contains transaction_id
    (int) $objCart->id_currency,
    false,
    $objCustomer->secure_key
);
```

### Order Reference Access

After `validateOrder()`, access created order:

```php
$this->module->validateOrder(/* ... */);

// Access created order ID
$orderId = $this->module->currentOrder;

// Access order reference
$orderReference = $this->module->currentOrderReference;

// Get Order object
$objOrder = new Order($this->module->currentOrder);
```

---

## Transaction Recording

### Create Transaction Record (Online Gateway)

```php
// PaymentHelper.php
public static function createOrder($session, $objCart)
{
    $module = ModuleCore::getInstanceByName('paymentgateway');

    // Retrieve payment details from gateway (adapt to your gateway's SDK)
    // Example: $payment = \Gateway\Payment::retrieve($session->payment_intent);
    if ($payment) {
        $chargeId = $payment->charge_id; // Adapt to your gateway's field names
        $paymentAmount = ((float) $session->amount_total / 100); // Adapt currency formatting

        // Determine order status based on payment status
        if ($payment->status == 'succeeded') {
            if ($objCart->is_advance_payment) {
                $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
            } else {
                $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
            }
            $transactionStatus = PaymentTransaction::TRANSACTION_STATUS_SUCCESS;
        } elseif ($payment->status == 'canceled') {
            $orderStatus = Configuration::get('PS_OS_CANCELED');
            $transactionStatus = PaymentTransaction::TRANSACTION_STATUS_CANCELLED;
        } else {
            $orderStatus = Configuration::get('_PS_OS_PROCESSING_');
            $transactionStatus = PaymentTransaction::TRANSACTION_STATUS_PROCESSING;
        }

        $objCustomer = new Customer((int) $objCart->id_customer);
        $extra_vars['transaction_id'] = $chargeId;

        // Create order using validateOrder
        if ($module->validateOrder(
            (int) $objCart->id,
            $orderStatus,
            $cartTotalAmount,
            $module->l('Payment Gateway Payment', 'processpayment'),
            null,
            $extra_vars,
            (int) $objCart->id_currency,
            false,
            $objCustomer->secure_key
        )) {
            // Save transaction record in custom table
            $objTransaction = new PaymentTransaction();
            $objTransaction->id_payment_intent = $payment->id;
            $objTransaction->id_transaction = isset($chargeId) ? $chargeId : false;
            $objTransaction->id_customer = $objCustomer->id;
            $objTransaction->id_currency = $objCart->id_currency;
            $objTransaction->id_cart = $objCart->id;
            $objTransaction->order_reference = $module->currentOrderReference;
            $objTransaction->amount = $paymentAmount;
            $objTransaction->status = $transactionStatus;
            $objTransaction->save();
            return true;
        }
    }
}
```

### Save Transaction Data (PayPal)

```php
// payment.php controller
private function saveOrderData($orderData)
{
    if ($orderData) {
        $purchaseUnits = $orderData['data']['purchase_units'];
        foreach ($purchaseUnits as $purchase) {
            $transaction_id = $purchase['payments']['captures'][0]['id'];
            $payment_status = $purchase['payments']['captures'][0]['status'];
            $payment_total = $purchase['payments']['captures'][0]['amount']['value'];
            $payment_curr = $purchase['payments']['captures'][0]['amount']['currency_code'];

            $cart = $this->context->cart;
            $currency = Currency::getCurrency((int) $cart->id_currency);

            // Calculate cart total
            if ($cart->is_advance_payment) {
                $cartTotalAmountTI = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
            } else {
                $cartTotalAmountTI = $cart->getOrderTotal(true, Cart::BOTH);
            }

            $orderObj = new WkPayPalCommerceOrder();
            $orderObj->order_reference = '';  // Will be updated after order creation
            $orderObj->id_cart = (int)$cart->id;
            $orderObj->id_currency = (int)$cart->id_currency;
            $orderObj->environment = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
            $orderObj->id_customer = (int)$cart->id_customer;
            $orderObj->order_total = (float)$cartTotalAmountTI;
            $orderObj->checkout_currency = $currency['iso_code'];

            // PayPal Returned Data
            $orderObj->pp_paid_total = (float)$payment_total;
            $orderObj->pp_paid_currency = $payment_curr;
            $orderObj->pp_reference_id = $purchase['reference_id'];
            $orderObj->pp_order_id = $orderData['data']['id'];
            $orderObj->pp_transaction_id = $transaction_id;
            $orderObj->pp_payment_status = $payment_status;
            $orderObj->response = Tools::jsonEncode($orderData);
            $orderObj->order_date = date('Y-m-d H:i:s');
            $orderObj->save();
        }

        return true;
    }

    return false;
}
```

---

## Refund Management

### Refund ObjectModel (PayPal)

```php
// WkPaypalCommerceRefund.php
class WkPaypalCommerceRefund extends ObjectModel
{
    public $order_trans_id;
    public $paypal_refund_id;
    public $refund_amount;
    public $refund_reason;
    public $refund_type;
    public $currency_code;
    public $response;
    public $refund_status;

    const WK_PAYPAL_COMMERCE_REFUND_TYPE_FULL = 1;
    const WK_PAYPAL_COMMERCE_REFUND_TYPE_PARTIAL = 2;

    public static $definition = array(
        'table' =>  'wk_paypal_commerce_refund',
        'primary'   =>  'id_paypal_commerce_refund',
        'fields'    =>  array(
            'order_trans_id'   =>  array('type'  =>  self::TYPE_INT),
            'paypal_refund_id' =>  array('type'  =>  self::TYPE_STRING),
            'refund_amount' =>  array('type'  =>  self::TYPE_FLOAT),
            'currency_code' =>  array('type'  =>  self::TYPE_STRING),
            'refund_reason' =>  array('type'  =>  self::TYPE_STRING),
            'refund_type' =>  array('type'  =>  self::TYPE_INT),
            'response' =>  array('type'  =>  self::TYPE_STRING),
            'refund_status'    =>  array('type'  =>  self::TYPE_STRING),
            'date_add'  =>  array('type'  =>  self::TYPE_DATE),
            'date_upd'  =>  array('type'  =>  self::TYPE_DATE),
        )
    );

    public static function getTotalRefundedAmount($idTrans, $formatted = false)
    {
        $totalRefund = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT SUM(pcr.`refund_amount`) AS sum_amount_refunded, pco.`id_currency`
            FROM `'._DB_PREFIX_.'wk_paypal_commerce_refund` pcr
            LEFT JOIN `'._DB_PREFIX_.'wk_paypal_commerce_order` pco
            ON pcr.`order_trans_id` = pco.`id_paypal_commerce_order`
            WHERE pcr.`order_trans_id` = ' .(int)$idTrans.'
            GROUP BY pcr.`order_trans_id`'
        );

        if ($totalRefund) {
            $formattedAmt = Tools::displayPrice(
                $totalRefund['sum_amount_refunded'],
                new Currency((int)$totalRefund['id_currency'])
            );
            return $formatted ? $formattedAmt : $totalRefund['sum_amount_refunded'];
        }
        return false;
    }
}
```

### Refund ObjectModel (Generic)

```php
// PaymentRefund.php
class PaymentRefund extends ObjectModel
{
    public $id_payment_transaction;
    public $id_refund;
    public $refunded_amount;
    public $refund_type;
    public $remark;
    public $status;

    const REFUND_STATUS_REFUNDED = 1;
    const REFUND_STATUS_PROCESSING = 2;
    const REFUND_STATUS_CANCELLED = 3;

    const REFUND_TYPE_FULL = 1;
    const REFUND_TYPE_PARTIAL = 2;

    public static $definition = array(
        'table' => 'payment_gateway_refund',
        'primary' => 'id_payment_refund',
        'fields' => array(
            'id_payment_transaction' => array('type' => self::TYPE_STRING, 'required' => true),
            'id_refund' => array('type' => self::TYPE_STRING, 'required' => true),
            'refunded_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'refund_type' => array('type' => self::TYPE_INT),
            'remark' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );

    public static function getTransactionRefundedAmount($idTransaction)
    {
        return Db::getInstance()->getValue(
            'SELECT IFNULL(SUM(refunded_amount), 0) FROM '._DB_PREFIX_.'payment_gateway_refund
            where `id_payment_transaction` = '.(int)$idTransaction
        );
    }
}
```

---

## Order States

### Common Order State Constants

```php
// Payment accepted
Configuration::get('PS_OS_PAYMENT_ACCEPTED')

// Awaiting payment (offline methods)
Configuration::get('PS_OS_AWAITING_PAYMENT')

// Partial payment accepted (advance payment)
Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED')

// Payment error
Configuration::get('PS_OS_ERROR')

// Canceled
Configuration::get('PS_OS_CANCELED')

// Processing (custom state)
Configuration::get('_PS_OS_PROCESSING_')
```

### Order State Logic

```php
// Determine order status based on payment and cart type
if ($paymentIntent->status == 'succeeded') {
    if ($objCart->is_advance_payment) {
        $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
    } else {
        $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
    }
} elseif ($paymentIntent->status == 'canceled') {
    $orderStatus = Configuration::get('PS_OS_CANCELED');
} elseif ($paymentIntent->status == 'requires_payment_method') {
    $orderStatus = Configuration::get('PS_OS_ERROR');
} else {
    $orderStatus = Configuration::get('_PS_OS_PROCESSING_');
}
```

### Update Order Status

```php
// PaymentGatewayService.php
public static function updatePaymentStatus($id_order_state, $id_order)
{
    $order = new Order($id_order);
    $currentOrderState = $order->getCurrentOrderState();

    if ($currentOrderState->id != $id_order_state) {
        $useExistPayment = false;
        if (!$order->hasInvoice()) {
            $useExistPayment = true;
        }

        $orderHistory = new OrderHistory();
        $orderHistory->id_order = (int)$id_order;
        $orderHistory->changeIdOrderState(
            (int)$id_order_state,
            $order,
            $useExistPayment
        );
        $orderHistory->addWithemail(true, null);
    }
}
```

---

## Security Best Practices

### Secure Key Validation

**Always validate secure key before creating order:**

```php
// In validateOrder() method (PaymentModule.php)
if ($secure_key !== false && $secure_key != $this->context->cart->secure_key) {
    PrestaShopLogger::addLog('PaymentModule::validateOrder - Secure key does not match', 3);
    die(Tools::displayError('Error processing order. Secure key does not match.'));
}

// In controller
$customer = new Customer($cart->id_customer);
$this->module->validateOrder(
    $cart->id,
    $orderStatus,
    $total,
    $payment_method,
    null,
    null,
    (int)$currency->id,
    false,
    $customer->secure_key  // Pass customer's secure key
);
```

### Webhook Signature Verification

**Generic Webhook Verification:**

```php
// In PaymentGatewayHelper.php or similar service class
public static function validateWebhookSignature($payload, $signature, $secret)
{
    // Implement according to your gateway's verification method
    // Example patterns:
    
    // Pattern 1: Hash-based verification (HMAC)
    $computed_signature = hash_hmac('sha256', $payload, $secret);
    if (hash_equals($signature, $computed_signature)) {
        return true;
    }
    
    // Pattern 2: Using gateway's SDK
    // try {
    //     $event = \Gateway\Webhook::constructEvent($payload, $signature, $secret);
    //     return true;
    // } catch(\Exception $e) {
    //     return false;
    // }
    
    return false;
}

// In webhook controller
$payload = @file_get_contents('php://input');
$signature = $_SERVER['HTTP_GATEWAY_SIGNATURE']; // Adapt header name
$secret = Configuration::get('PAYMENT_GATEWAY_WEBHOOK_SECRET');

if (!PaymentGatewayHelper::validateWebhookSignature($payload, $signature, $secret)) {
    http_response_code(400);
    exit();
}

// Signature verified, process webhook
$event = Tools::jsonDecode($payload, true);
// Process event...
```

**PayPal Webhook Verification:**

```php
public static function validateWebhookSig($postData)
{
    $apiResp = array();
    $accessToken = self::getAccessToken();

    if ($postData && $accessToken['success']) {
        $wkEnvironment = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
        $base_url = ($wkEnvironment == 'sandbox')
            ? PayPalHelper::WK_PAYPAL_SANDBOX_URL
            : PayPalHelper::WK_PAYPAL_LIVE_URL;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $base_url . "/v1/notifications/verify-webhook-signature",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => Tools::jsonEncode($postData),
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $accessToken['access_token'],
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $apiResp = Tools::jsonDecode($response, true);
    }

    return $apiResp;
}

// In webhook controller
$validateSig = WkPaypalCommerceHelper::validateWebhookSig($payload);

if (isset($validateSig['verification_status'])
    && $validateSig['verification_status'] == 'SUCCESS'
) {
    // Process webhook
}
```

### Cart Validation

```php
// Check cart belongs to customer
if ($cart->id_customer == 0 || !$this->module->active) {
    Tools::redirect('index.php?controller=order&step=1');
}

// Check customer is loaded
$customer = new Customer($cart->id_customer);
if (!Validate::isLoadedObject($customer)) {
    Tools::redirect('index.php?controller=order&step=1');
}

// Check payment method is authorized
$authorized = false;
foreach (Module::getPaymentModules() as $module) {
    if ($module['name'] == $this->module->name) {
        $authorized = true;
        break;
    }
}

if (!$authorized) {
    die($this->module->l('This payment method is not available.'));
}
```

### Prevent Duplicate Orders

```php
// Check if order already exists for cart
if ($objCart->OrderExists() == false) {
    // Create new order
    PaymentHelper::createOrder($session, $objCart);
} else {
    // Order already exists, update status if needed
    $this->updateOrderStatus($session);
}
```

### Token Validation

```php
// Validate module security token
if ($this->module->secure_key != Tools::getValue('token')) {
    die('Invalid token.');
}
```

---

## Best Practices Summary

1. **Always validate secure_key** when creating orders
2. **Verify webhook signatures** before processing notifications
3. **Check cart and customer validity** before processing payment
4. **Prevent duplicate orders** by checking `OrderExists()`
5. **Log all transactions** for debugging and audit trails
6. **Handle errors gracefully** with try-catch blocks
7. **Use appropriate order states** based on payment status and advance payment
8. **Record transaction IDs** in `extra_vars` array
9. **Return HTTP 200** from webhooks to acknowledge receipt
10. **Validate amount** matches cart total before creating order
