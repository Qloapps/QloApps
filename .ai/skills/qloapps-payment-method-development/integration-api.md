# Payment Gateway API Integration

Complete guide for integrating third-party payment gateway APIs with QloApps payment modules.

---

## Configuration Form Structure

### Test/Live Mode Configuration

All payment gateways should support both sandbox and production environments.

**PayPal Commerce Example:**

```php
// qlopaypalcommerce.php
public function renderForm()
{
    $fields_form['form'] = array(
        'legend' => array(
            'icon' => 'icon-cog',
            'title' => $this->l('PayPal Payment Configuration'),
        ),
        'input' => array(
            array(
                'type' => 'select',
                'required' => true,
                'label' => $this->l('Transaction Environment'),
                'name' => 'WK_PAYPAL_COMMERCE_PAYMENT_MODE',
                'options' => array(
                    'query' => array(
                        array('id' => 'sandbox', 'name' => $this->l('Sandbox')),
                        array('id' => 'production', 'name' => $this->l('Production')),
                    ),
                    'id' => 'id',
                    'name' => 'name',
                ),
                'hint' => $this->l('Select PayPal payment environment either sandbox or production.'),
            ),
            array(
                'label' => $this->l('Merchant ID'),
                'name' => 'WK_PAYPAL_COMMERCE_MERCHANT_ID',
                'size' => 60,
                'type' => 'text',
                'required' => true,
            ),
            array(
                'label' => $this->l('Client ID'),
                'name' => 'WK_PAYPAL_COMMERCE_CLIENT_ID',
                'size' => 100,
                'type' => 'text',
                'required' => true,
            ),
            array(
                'label' => $this->l('Client Secret'),
                'name' => 'WK_PAYPAL_COMMERCE_CLIENT_SECRET',
                'size' => 100,
                'type' => 'text',
                'required' => true,
            ),
        ),
    );
}
```

**Generic Online Payment Gateway Example:**

```php
// paymentgateway.php
$fieldsForm[]['form'] = array(
    'legend' => array(
        'icon' => 'icon-cog',
        'title' => $this->l('Payment Gateway Configuration'),
    ),
    'input' => array(
        array(
            'type' => 'switch',
            'label' => $this->l('Enable live mode'),
            'name' => 'PAYMENT_GATEWAY_LIVE_MODE',
            'is_bool' => true,
            'values' => array(
                array('id' => 'active_on', 'value' => 1),
                array('id' => 'active_off', 'value' => 0),
            ),
            'hint' => $this->l('Enable live mode, to get real transactions.'),
        ),
        array(
            'label' => $this->l('API Test Secret Key'),
            'required' => true,
            'name' => 'PAYMENT_GATEWAY_TEST_SECRET_KEY',
            'size' => 60,
            'type' => 'text',
        ),
        array(
            'label' => $this->l('API Test Publishable Key'),
            'required' => true,
            'name' => 'PAYMENT_GATEWAY_TEST_PUBLISHABLE_KEY',
            'size' => 60,
            'type' => 'text',
        ),
        array(
            'label' => $this->l('API Live Secret Key'),
            'required' => true,
            'name' => 'PAYMENT_GATEWAY_LIVE_SECRET_KEY',
            'size' => 60,
            'type' => 'text',
        ),
        array(
            'label' => $this->l('API Live Publishable Key'),
            'required' => true,
            'name' => 'PAYMENT_GATEWAY_LIVE_PUBLISHABLE_KEY',
            'size' => 60,
            'type' => 'text',
        ),
    ),
);
```

---

## Environment Setup Helper

### Switching Between Sandbox and Production

**Pattern - Helper Class Method:**

```php
// PaymentGatewayService.php
class PaymentGatewayService
{
    public static function getSecretKey()
    {
        if (!Configuration::get('PAYMENT_GATEWAY_LIVE_MODE')) {
            return Configuration::get('PAYMENT_GATEWAY_TEST_SECRET_KEY');
        } else {
            return Configuration::get('PAYMENT_GATEWAY_LIVE_SECRET_KEY');
        }
    }

    public static function getPublishableKey()
    {
        if (!Configuration::get('PAYMENT_GATEWAY_LIVE_MODE')) {
            return Configuration::get('PAYMENT_GATEWAY_TEST_PUBLISHABLE_KEY');
        } else {
            return Configuration::get('PAYMENT_GATEWAY_LIVE_PUBLISHABLE_KEY');
        }
    }

    public static function initializeGateway($secretKey)
    {
        $moduleInstance = Module::getInstanceByName('paymentgateway');
        // Initialize your gateway SDK here
        // Example: \Gateway\Client::setApiKey($secretKey);
        // Example: \Gateway\Client::setAppInfo($moduleInstance->displayName, $moduleInstance->version);
    }
}
```

**PayPal Pattern:**

```php
// WkPaypalCommerceHelper.php
public static function getAccessToken()
{
    $wkClientID = trim(Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID'));
    $wkClientSecret = trim(Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_SECRET'));
    $wkEnvironment = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
    
    $base_url = ($wkEnvironment == 'sandbox') 
        ? PayPalHelper::WK_PAYPAL_SANDBOX_URL 
        : PayPalHelper::WK_PAYPAL_LIVE_URL;

    // ... API call
}
```

---

## API Authentication

### API Key Authentication

```php
// Initialize Payment Gateway API with secret key
PaymentGatewayService::initializeGateway($secretKey);

// All subsequent calls use this key
// Example: $session = \Gateway\Session::retrieve($sessionId);
// Example: $payment = \Gateway\Payment::retrieve($paymentId);
```

### OAuth Token Authentication (PayPal)

```php
// WkPaypalCommerceHelper.php
public static function getAccessToken()
{
    $apiResp = array();
    $wkClientID = trim(Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID'));
    $wkClientSecret = trim(Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_SECRET'));
    $wkEnvironment = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');

    $base_url = ($wkEnvironment == 'sandbox') 
        ? PayPalHelper::WK_PAYPAL_SANDBOX_URL 
        : PayPalHelper::WK_PAYPAL_LIVE_URL;

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $base_url . "/" . PayPalHelper::WK_PAYPAL_ACCESS_TOKEN_URI,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=client_credentials",
        CURLOPT_HTTPHEADER => array(
            "PayPal-Partner-Attribution-Id: " . PayPalHelper::WK_PAYPAL_COMMERCE_ATTRIBUTION_ID,
            "authorization: Basic " . base64_encode($wkClientID . ":" . $wkClientSecret),
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        throw new PrestaShopException(sprintf('cURL Error #: %s', $err));
    } else {
        $accessToken = Tools::jsonDecode($response, true);
        if (isset($accessToken['error'])) {
            $apiResp['success'] = false;
            $apiResp['message'] = $accessToken['error_description'];
        } else {
            $apiResp['success'] = true;
            $apiResp['access_token'] = $accessToken['access_token'];
        }
    }

    return $apiResp;
}
```

---

## Webhook Management

### Creating Webhook URLs

**Generate Module Link for Webhook:**

```php
// For sandbox
$webhookUrl = Context::getContext()->link->getModuleLink(
    'qlopaypalcommerce', 
    'webhook',  // controller name
    array(), 
    true
);

// For production
$webhookUrl = Context::getContext()->link->getModuleLink(
    'qlopaypalcommerce', 
    'callback', 
    array(), 
    true
);
```

**Register Webhook with Gateway:**

```php
// WkPaypalCommerceHelper.php
public static function createWebhookUrl($token)
{
    $apiResp = array();
    if ($token) {
        $wkEnvironment = Tools::getValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
        if ($wkEnvironment == 'sandbox') {
            $base_url = PayPalHelper::WK_PAYPAL_SANDBOX_URL;
            $webhookUrl = Context::getContext()->link->getModuleLink(
                'qlopaypalcommerce', 'webhook', array(), true
            );
        } else {
            $base_url = PayPalHelper::WK_PAYPAL_LIVE_URL;
            $webhookUrl = Context::getContext()->link->getModuleLink(
                'qlopaypalcommerce', 'callback', array(), true
            );
        }

        $postData = array(
            'url' => $webhookUrl,
            'event_types' => array(
                array('name' => 'CHECKOUT.ORDER.APPROVED'),
                array('name' => 'CHECKOUT.ORDER.COMPLETED'),
                array('name' => 'PAYMENT.CAPTURE.COMPLETED'),
                array('name' => 'PAYMENT.CAPTURE.DENIED'),
                array('name' => 'PAYMENT.CAPTURE.PENDING'),
                array('name' => 'PAYMENT.CAPTURE.REFUNDED'),
                array('name' => 'PAYMENT.CAPTURE.REVERSED'),
            )
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $base_url . "/v1/notifications/webhooks",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => Tools::jsonEncode($postData),
            CURLOPT_HTTPHEADER => array(
                "PayPal-Partner-Attribution-Id: " . PayPalHelper::WK_PAYPAL_COMMERCE_ATTRIBUTION_ID,
                "authorization: Bearer " . $token,
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $webhookResponse = Tools::jsonDecode($response, true);
        
        if (isset($webhookResponse['id'])) {
            $apiResp['success'] = true;
            $apiResp['webhook_id'] = $webhookResponse['id'];
        }
    }

    return $apiResp;
}
```

### Deleting Webhooks

```php
public static function deleteWebhookUrl()
{
    $accessToken = self::getAccessToken();
    if ($accessToken['success']) {
        $wkEnvironment = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
        if ($wkEnvironment == 'sandbox') {
            $base_url = PayPalHelper::WK_PAYPAL_SANDBOX_URL;
            $webhookId = Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_WEBHOOK_ID');
        } else {
            $base_url = PayPalHelper::WK_PAYPAL_LIVE_URL;
            $webhookId = Configuration::get('WK_PAYPAL_COMMERCE_LIVE_WEBHOOK_ID');
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $base_url . "/v1/notifications/webhooks/" . $webhookId,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $accessToken['access_token'],
            ),
        ));

        curl_exec($curl);
        curl_close($curl);
    }
}
```

---

## API Communication Helper Classes

### Wrapper Class Pattern

**PayPal Commerce:**

```php
// libs/PayPalCommerce.php
include_once(dirname(__FILE__) . '/Orders.php');

class PayPalCommerce {
    public $orders = null;

    public function __construct() {
        $this->orders = new PayPalOrders();
    }
}

// Usage in controller
$ppCommerce = new PayPalCommerce();
$ppOrderData = $ppCommerce->orders->create($orderDetails);
$returnData = $ppCommerce->orders->capture($orderID);
$returnData = $ppCommerce->orders->get($orderID);
```

**Using Official Gateway SDK:**

```php
// Include gateway library
include_once 'libs/init.php';

// Initialize with API key
PaymentGatewayService::initializeGateway($secretKey);

// Use SDK classes directly
// Example: $session = \Gateway\Session::retrieve($sessionId);
// Example: $payment = \Gateway\Payment::retrieve($paymentId);
// Example: $refund = \Gateway\Refund::create(['charge' => $chargeId, 'amount' => $amount]);
```

---

## Error Handling

### Try-Catch Pattern

```php
// processpayment.php
try {
    if (!empty($secretKey)) {
        PaymentGatewayService::initializeGateway($secretKey);
        
        // Retrieve payment session (adapt to your gateway's SDK)
        // Example: $session = \Gateway\Session::retrieve($sessionId);
        
        if ($session) {
            // Process payment
            PaymentHelper::createOrder($session, $objCart);
        }
    }
} catch (Gateway_CardError $e) {
    // Card declined
    $errorData = $e->getDetails();
    PaymentGatewayService::createErrorLog($e, false);
} catch (Gateway_ApiConnectionError $e) {
    // Network problem
    $errorData = $e->getDetails();
    PaymentGatewayService::createErrorLog($e, false);
} catch (Gateway_InvalidRequestError $e) {
    // Invalid request
    $errorData = $e->getDetails();
    PaymentGatewayService::createErrorLog($e, false);
} catch (Gateway_ApiError $e) {
    // Gateway error
    $errorData = $e->getDetails();
    PaymentGatewayService::createErrorLog($e, false);
}
```

### Error Logging Service

```php
// PaymentGatewayService.php
public static function createErrorLog($exception, $check = false)
{
    $moduleInstance = Module::getInstanceByName('paymentgateway');
    if ($check) {
        $error = $exception->getMessage();
    } else {
        // Adapt to your gateway's error structure
        $errorData = $exception->getDetails(); // or getJsonBody(), etc.
        $error = isset($errorData['error']) ? $errorData['error'] : $exception->getMessage();
    }

    $moduleInstance->logger->log($error, FileLogger::ERROR);
    return $error;
}
```

### Custom File Logger

```php
// WkPaypalCommerceHelper.php
public static function logMsg($logType, $logMsg, $newLine = false)
{
    $file = fopen(dirname(__FILE__).'/../log/'.$logType.'.log', 'a');
    $error_msg = $newLine ? "\r\n\n" : "\n";
    $error_msg .= date('d-m-Y H:i:s').'  ----  '.$logMsg;
    fwrite($file, $error_msg);
    fclose($file);
    return true;
}

// Usage
WkPaypalCommerceHelper::logMsg('payment', 'Payment initiated...', true);
WkPaypalCommerceHelper::logMsg('payment', 'Environment: '. $environment);
WkPaypalCommerceHelper::logMsg('payment', 'Cart ID: '. $cart->id);
WkPaypalCommerceHelper::logMsg('payment', Tools::jsonEncode($orderDetails));
```

---

## API Request Patterns

### cURL POST Request

```php
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $base_url . "/v1/endpoint",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => Tools::jsonEncode($postData),
    CURLOPT_HTTPHEADER => array(
        "authorization: Bearer " . $token,
        "cache-control: no-cache",
        "content-type: application/json"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    throw new PrestaShopException(sprintf('cURL Error #: %s', $err));
} else {
    $responseData = Tools::jsonDecode($response, true);
    // Process response
}
```

### JSON Request Body

```php
// Read raw POST data
$json = Tools::file_get_contents('php://input');
$data = Tools::jsonDecode($json, true);

// Process request
if (isset($data['orderID'])) {
    $orderId = $data['orderID'];
}
```

---

## Payment Data Structure

### Order Creation Payload (PayPal)

```php
// getOrderDetails() method
$orderData = array();
$orderData['intent'] = 'CAPTURE';

$cart = $this->context->cart;
$customer = new Customer((int)$cart->id_customer);

$orderData['payer'] = array(
    'name' => array(
        'given_name' => $customer->firstname,
        'surname' => $customer->lastname
    ),
    'email_address' => $customer->email
);

// Add address if available
if ($bilAddr = $ppHelper->getSimpleAddress($cart->id_customer, $cart->id_address_invoice)) {
    $orderData['payer']['address'] = array(
        'address_line_1' => $bilAddr['address1'],
        'address_line_2' => $bilAddr['address2'],
        'admin_area_2' => $bilAddr['city'],
        'admin_area_1' => $bilAddr['state'],
        'postal_code' => $bilAddr['postcode'],
        'country_code' => $bilAddr['country_iso'],
    );
}

// Purchase units
$orderData['purchase_units'] = array(/* ... */);

// Application context
$orderData['application_context'] = array(
    'return_url' => $this->context->link->getModuleLink(/* ... */),
    'cancel_url' => $this->context->link->getModuleLink(/* ... */),
);
```

---

## Best Practices

### Configuration Validation

```php
public function getContent()
{
    if (!$this->checkPaypalCommerceConfigured()) {
        $this->context->controller->warnings[] = $this->l(
            'PayPal Merchant ID, Email, Client ID and Secret must be configured.'
        );
    }

    if (!count(Currency::checkPaymentCurrencies($this->id))) {
        $this->context->controller->warnings[] = $this->l(
            'No currency has been set for this module.'
        );
    }
}
```

### Environment Constants

```php
// PayPalHelper.php
class PayPalHelper
{
    const WK_PAYPAL_SANDBOX_URL = 'https://api-m.sandbox.paypal.com';
    const WK_PAYPAL_LIVE_URL = 'https://api-m.paypal.com';
    const WK_PAYPAL_ACCESS_TOKEN_URI = 'v1/oauth2/token';
    const WK_PAYPAL_COMMERCE_ATTRIBUTION_ID = 'Webkul_SP_QloApps';
}
```

### Secure Credential Storage

- Store API credentials in `Configuration` table using `Configuration::updateValue()`
- Use descriptive configuration keys: `MODULE_NAME_SETTING_NAME`
- Never expose credentials in frontend JavaScript
- Use separate keys for test/live environments

### API Response Validation

```php
if (isset($responseData['error']) && !empty($responseData['error'])) {
    $apiResp['success'] = false;
    $apiResp['message'] = $responseData['error_description'];
} else {
    $apiResp['success'] = true;
    $apiResp['data'] = $responseData;
}
```

### Timeout Handling

```php
curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 30 second timeout
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); // 10 second connection timeout
```
