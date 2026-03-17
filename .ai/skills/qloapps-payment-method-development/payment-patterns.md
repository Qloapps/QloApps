# Payment Patterns

Understanding PaymentModule class, offline payments, online payments, and choosing the right pattern for your gateway.

---

## PaymentModule Class

All payment modules extend the `PaymentModule` class from `classes/PaymentModule.php`.

### **Key Properties**

```php
abstract class PaymentModuleCore extends Module
{
    public $currentOrder;                    // Order ID created by validateOrder()
    public $currentOrderReference;           // Order reference
    public $currencies = true;               // Enable currency restrictions
    public $currencies_mode = 'checkbox';    // 'checkbox' or 'radio'
    public $payment_type = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT;
    public $validateOrderAmount = true;      // Validate amount matches cart total
}
```

### **Payment Types**

```php
// In classes/order/OrderPayment.php
class OrderPayment extends ObjectModel
{
    const PAYMENT_TYPE_ONLINE = 1;              // Real-time processing
    const PAYMENT_TYPE_REMOTE_PAYMENT = 2;      // Offline payment
    const PAYMENT_TYPE_PAY_AT_HOTEL = 3;        // Pay at property
}
```

**Choose payment type:**
- `PAYMENT_TYPE_ONLINE` → Credit cards, PayPal, payment gateways
- `PAYMENT_TYPE_REMOTE_PAYMENT` → Bank transfer, cheque, cash
- `PAYMENT_TYPE_PAY_AT_HOTEL` → Payment on arrival (QloApps specific)

---

## Offline Payments (Remote Payments)

Payment happens outside the system. Order created in "Awaiting Payment" state.

### **Pattern Overview**

```
Customer → Checkout → Payment Option → Confirm → Order Created (Awaiting Payment) →
Email with Instructions → Customer Pays Offline → Admin Confirms Payment
```

### **Reference Implementation: Bankwire**

**Main class:** `modules/bankwire/bankwire.php`

```php
class Bankwire extends PaymentModule
{
    public $details;   // Bank details
    public $owner;     // Account owner
    public $address;   // Bank address

    public function __construct()
    {
        $this->name = 'bankwire';
        $this->tab = 'payments_gateways';
        $this->version = '1.1.7';
        $this->author = 'Webkul';
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        // Load saved configuration
        $config = Configuration::getMultiple(array(
            'BANK_WIRE_DETAILS',
            'BANK_WIRE_OWNER',
            'BANK_WIRE_ADDRESS'
        ));

        if (!empty($config['BANK_WIRE_OWNER'])) {
            $this->owner = $config['BANK_WIRE_OWNER'];
        }
        if (!empty($config['BANK_WIRE_DETAILS'])) {
            $this->details = $config['BANK_WIRE_DETAILS'];
        }
        if (!empty($config['BANK_WIRE_ADDRESS'])) {
            $this->address = $config['BANK_WIRE_ADDRESS'];
        }

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Bank wire');
        $this->description = $this->l('Accept payments via bank wire transfer');

        // Set payment type
        $this->payment_type = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT;

        // Warnings
        if (!isset($this->owner) || !isset($this->details) || !isset($this->address)) {
            $this->warning = $this->l('Account details must be configured.');
        }

        $currencies = Currency::checkPaymentCurrencies($this->id);
        if (!$currencies || !count($currencies)) {
            $this->warning = $this->l('No currency has been set for this module.');
        }
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('payment')
            && $this->registerHook('displayPaymentEU')
            && $this->registerHook('paymentReturn');
    }

    public function uninstall()
    {
        return Configuration::deleteByName('BANK_WIRE_DETAILS')
            && Configuration::deleteByName('BANK_WIRE_OWNER')
            && Configuration::deleteByName('BANK_WIRE_ADDRESS')
            && parent::uninstall();
    }

    // Configuration form
    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            }
        }

        return $this->renderForm();
    }

    private function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue('BANK_WIRE_DETAILS')) {
                $this->_postErrors[] = $this->l('Account details are required.');
            }
            if (!Tools::getValue('BANK_WIRE_OWNER')) {
                $this->_postErrors[] = $this->l('Account owner is required.');
            }
        }
    }

    private function _postProcess()
    {
        Configuration::updateValue('BANK_WIRE_DETAILS', Tools::getValue('BANK_WIRE_DETAILS'));
        Configuration::updateValue('BANK_WIRE_OWNER', Tools::getValue('BANK_WIRE_OWNER'));
        Configuration::updateValue('BANK_WIRE_ADDRESS', Tools::getValue('BANK_WIRE_ADDRESS'));

        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    // Hook: Display payment option
    public function hookPayment($params)
    {
        if (!$this->active) {
            return;
        }

        $this->smarty->assign(array(
            'this_path' => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
        ));

        return $this->display(__FILE__, 'payment.tpl');
    }

    // Hook: Display confirmation message
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $this->smarty->assign(array(
            'status' => 'ok',
            'bankwire_owner' => Configuration::get('BANK_WIRE_OWNER'),
            'bankwire_details' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
            'bankwire_address' => nl2br(Configuration::get('BANK_WIRE_ADDRESS'))
        ));

        return $this->display(__FILE__, 'payment_return.tpl');
    }

    // Extra mail content with payment instructions
    public function getExtraMailContent($id_order_state, $order)
    {
        if (Configuration::get('PS_OS_AWAITING_PAYMENT') == $id_order_state) {
            $this->context->smarty->assign(array(
                'bankwire_owner' => Configuration::get('BANK_WIRE_OWNER'),
                'bankwire_details' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
                'bankwire_address' => nl2br(Configuration::get('BANK_WIRE_ADDRESS')),
                'lang' => new Language($order->id_lang),
                'total_paid' => Tools::displayPrice($order->total_paid, $this->context->currency, false)
            ));

            return array(
                '{extra_mail_content_html}' => $this->context->smarty->fetch(
                    $this->local_path.'views/templates/mail/mail_template_html.tpl'
                ),
                '{extra_mail_content_txt}' => $this->context->smarty->fetch(
                    $this->local_path.'views/templates/mail/mail_template_text.tpl'
                )
            );
        }
        return array();
    }
}
```

### **Payment Controller** (`controllers/front/payment.php`)

```php
class BankwirePaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $display_column_left = false;

    public function initContent()
    {
        parent::initContent();

        $cart = $this->context->cart;

        // Check currency
        if (!$this->module->checkCurrency($cart)) {
            Tools::redirect('index.php?controller=order');
        }

        // Calculate total (advance payment support)
        if ($cart->is_advance_payment) {
            $total = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
        } else {
            $total = $cart->getOrderTotal(true, Cart::BOTH);
        }

        // Validate service products
        ServiceProductCartDetail::validateServiceProductsInCart();

        // Check order restrictions
        $restrict_order = false;
        if (Module::isInstalled('hotelreservationsystem')
            && Module::isEnabled('hotelreservationsystem')
        ) {
            require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
            if (HotelOrderRestrictDate::validateOrderRestrictDateOnPayment($this)) {
                $restrict_order = true;
            }
        }

        if (count($this->errors)) {
            $restrict_order = true;
        }

        $this->context->smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'cust_currency' => $cart->id_currency,
            'currencies' => $this->module->getCurrency((int) $cart->id_currency),
            'total' => $total,
            'this_path' => $this->module->getPathUri(),
            'restrict_order' => $restrict_order
        ));

        $this->setTemplate('payment_execution.tpl');
    }
}
```

### **Validation Controller** (`controllers/front/validation.php`)

```php
class BankwireValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;

        // Security checks
        if ($cart->id_customer == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check payment module is authorized
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

        // Validate service products
        ServiceProductCartDetail::validateServiceProductsInCart();

        // Check order restrictions
        if (Module::isInstalled('hotelreservationsystem')
            && Module::isEnabled('hotelreservationsystem')
        ) {
            require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
            if (HotelOrderRestrictDate::validateOrderRestrictDateOnPayment($this)) {
                Tools::redirect('index.php?controller=order-opc');
            }
        }

        // Validate customer
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $currency = $this->context->currency;

        // Calculate total
        if ($cart->is_advance_payment) {
            $total = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
        } else {
            $total = $cart->getOrderTotal(true, Cart::BOTH);
        }

        // Prepare email variables
        $mailVars = array(
            '{bankwire_owner}' => Configuration::get('BANK_WIRE_OWNER'),
            '{bankwire_details}' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
            '{bankwire_address}' => nl2br(Configuration::get('BANK_WIRE_ADDRESS'))
        );

        // Create order - Awaiting Payment state
        $this->module->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_AWAITING_PAYMENT'),  // Awaiting Payment state
            $total,
            $this->module->displayName,
            NULL,
            $mailVars,
            (int)$currency->id,
            false,
            $customer->secure_key
        );

        // Redirect to confirmation
        Tools::redirect(
            'index.php?controller=order-confirmation'.
            '&id_cart='.$cart->id.
            '&id_module='.$this->module->id.
            '&id_order='.$this->module->currentOrder.
            '&key='.$customer->secure_key
        );
    }
}
```

### **Offline Payment Checklist**

✅ Extend `PaymentModule`
✅ Set `payment_type = PAYMENT_TYPE_REMOTE_PAYMENT`
✅ Create configuration form for payment details
✅ Implement payment controller (show confirmation)
✅ Implement validation controller (create order)
✅ Use `PS_OS_AWAITING_PAYMENT` order state
✅ Add `getExtraMailContent()` for email instructions
✅ Create email templates with payment details
✅ Register hooks: payment, paymentReturn

---

## Online Payments (Real-time Processing)

Payment processed immediately via gateway API. Order created after payment confirmation.

### **Pattern Overview**

```
Customer → Checkout → Payment Option → Gateway Payment → Callback/Webhook →
Order Created (Payment Accepted) → Confirmation Email
```

### **Reference Implementation: Online Payment Gateway**

**Main class:** `modules/paymentgateway/paymentgateway.php`

```php
include_once 'libs/init.php';  // Payment Gateway SDK
include_once 'classes/PaymentGatewayRequiredClasses.php';

class PaymentGateway extends PaymentModule
{
    public $debugging = false;
    public $logger;
    private $postErrors = array();

    public function __construct()
    {
        $this->name = 'paymentgateway';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Your Name';
        $this->currencies = true;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Payment Gateway');
        $this->description = $this->l('Accept online payments via Payment Gateway');

        // Set payment type
        $this->payment_type = OrderPayment::PAYMENT_TYPE_ONLINE;

        $this->setLogger();
    }

    // Configuration form
    public function getContent()
    {
        $html = '';
        if (Tools::isSubmit('btnConfigSubmit')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
            } else {
                $html .= $this->displayError($this->postErrors);
            }
        }
        $html .= $this->renderForm();

        return $html;
    }

    public function renderForm()
    {
        $fieldsForm = array();
        $fieldsForm[]['form'] = array(
            'legend' => array(
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
                ),
                array(
                    'label' => $this->l('Test Secret Key'),
                    'name' => 'PAYMENT_GATEWAY_TEST_SECRET_KEY',
                    'type' => 'text',
                    'required' => true,
                ),
                array(
                    'label' => $this->l('Test Publishable Key'),
                    'name' => 'PAYMENT_GATEWAY_TEST_PUBLISHABLE_KEY',
                    'type' => 'text',
                    'required' => true,
                ),
                array(
                    'label' => $this->l('Live Secret Key'),
                    'name' => 'PAYMENT_GATEWAY_LIVE_SECRET_KEY',
                    'type' => 'text',
                    'required' => true,
                ),
                array(
                    'label' => $this->l('Live Publishable Key'),
                    'name' => 'PAYMENT_GATEWAY_LIVE_PUBLISHABLE_KEY',
                    'type' => 'text',
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        $helper = new HelperForm();
        // ... setup helper ...

        return $helper->generateForm($fieldsForm);
    }

    private function postValidation()
    {
        $mode = Tools::getValue('PAYMENT_GATEWAY_LIVE_MODE');

        if ($mode) {
            // Validate live keys
            if (!Tools::getValue('PAYMENT_GATEWAY_LIVE_SECRET_KEY')) {
                $this->postErrors[] = $this->l('Live secret key required');
            }
        } else {
            // Validate test keys
            if (!Tools::getValue('PAYMENT_GATEWAY_TEST_SECRET_KEY')) {
                $this->postErrors[] = $this->l('Test secret key required');
            }
        }
    }

    public function postProcess()
    {
        // Handle webhook changes when API keys change
        if (Tools::getValue('PAYMENT_GATEWAY_LIVE_MODE')) {
            if (Configuration::get('PAYMENT_GATEWAY_LIVE_SECRET_KEY') != Tools::getValue('PAYMENT_GATEWAY_LIVE_SECRET_KEY')) {
                // Remove old webhook
                if (Configuration::get('PAYMENT_GATEWAY_LIVE_WEBHOOK_ID')) {
                    $this->removeWebhookUrl(
                        Configuration::get('PAYMENT_GATEWAY_LIVE_WEBHOOK_ID'),
                        Configuration::get('PAYMENT_GATEWAY_LIVE_SECRET_KEY')
                    );
                }
                // Create new webhook
                $this->setWebhookUrl(
                    Tools::getValue('PAYMENT_GATEWAY_LIVE_SECRET_KEY'),
                    PaymentGatewayService::WEBHOOK_LIVE
                );
            }
        }

        // Save configuration
        Configuration::updateValue('PAYMENT_GATEWAY_LIVE_MODE', Tools::getValue('PAYMENT_GATEWAY_LIVE_MODE'));
        Configuration::updateValue('PAYMENT_GATEWAY_TEST_SECRET_KEY', Tools::getValue('PAYMENT_GATEWAY_TEST_SECRET_KEY'));
        Configuration::updateValue('PAYMENT_GATEWAY_LIVE_SECRET_KEY', Tools::getValue('PAYMENT_GATEWAY_LIVE_SECRET_KEY'));
        // ... etc ...
    }

    // Add JS/CSS for payment
    public function hookActionFrontControllerSetMedia()
    {
        $controller = Tools::getValue('controller');

        if ('orderopc' == $controller) {
            // Gateway SDK
            $this->context->controller->addJS('https://gateway.example.com/sdk.js', array('server' => 'remote'));

            // Module JS/CSS
            $this->context->controller->addJS($this->local_path.'views/js/front/payment_checkout.js');
            $this->context->controller->addCSS($this->_path.'views/css/payment.css');

            // Pass variables to JS
            Media::addJsDef(array(
                'PAYMENT_GATEWAY_PUBLISHABLE_KEY' => PaymentGatewayService::getPublicKey(),
                'payment_gateway_token' => Tools::getToken(false),
                'payment_check_process_link' => $this->context->link->getModuleLink('paymentgateway', 'checkprocess')
            ));
        }
    }

    // Display payment option
    public function hookDisplayPayment()
    {
        if (Configuration::get('PAYMENT_GATEWAY_CONFIG_ENABLED')) {
            return $this->display(__FILE__, 'payment.tpl');
        }
    }

    // Display confirmation
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $objOrder = $params['objOrder'];
        $state = $objOrder->getCurrentState();

        if ($state == Configuration::get('PS_OS_PAYMENT_ACCEPTED')) {
            $success = 1;
        } else {
            $success = 0;
        }

        if ($objOrder->is_advance_payment) {
            $order_total = $objOrder->advance_paid_amount;
        } else {
            $order_total = $objOrder->total_paid;
        }

        $this->smarty->assign(array(
            'total_to_pay' => Tools::displayPrice($order_total, $params['currencyObj'], false),
            'status' => $success,
            'id_order' => $objOrder->id,
        ));

        return $this->display(__FILE__, 'payment_return.tpl');
    }
}
```

### **Helper/Service Classes**

**PaymentGatewayService.php:**
```php
class PaymentGatewayService
{
    const WEBHOOK_TEST = 1;
    const WEBHOOK_LIVE = 2;

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
        // Initialize your gateway's SDK
        // Example: \Gateway\Client::setApiKey($secretKey);
        // Example: \Gateway\Client::setAppInfo($moduleInstance->displayName, $moduleInstance->version);
    }
}
```

### **Online Payment Checklist**

✅ Extend `PaymentModule`
✅ Set `payment_type = PAYMENT_TYPE_ONLINE`
✅ Create configuration for test/live API keys
✅ Include gateway SDK in libs folder
✅ Create service/helper classes for API
✅ Implement payment controller (show gateway form)
✅ Implement callback controller (handle return)
✅ Implement webhook controller (handle notifications)
✅ Use `PS_OS_PAYMENT_ACCEPTED` after successful payment
✅ Create transaction logging
✅ Implement refund capability
✅ Register hooks: payment, paymentReturn, actionFrontControllerSetMedia

---

## Advance Payment Support

QloApps supports advance/partial payments for bookings.

### **Check if Advance Payment**

```php
if ($cart->is_advance_payment) {
    $total = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
} else {
    $total = $cart->getOrderTotal(true, Cart::BOTH);
}
```

### **In Order**

```php
if ($objOrder->is_advance_payment) {
    $order_total = $objOrder->advance_paid_amount;
} else {
    $order_total = $objOrder->total_paid;
}
```

---

## Choosing the Right Pattern

### **Use Offline Pattern When:**
- ✅ Payment happens outside system (bank transfer, check)
- ✅ No API integration needed
- ✅ Admin manually confirms payment
- ✅ Simple configuration (just payment details)
- ✅ Examples: Bankwire, Cheque, Cash on Delivery

### **Use Online Pattern When:**
- ✅ Real-time payment processing
- ✅ Gateway API available
- ✅ Immediate payment confirmation needed
- ✅ Refunds through API
- ✅ Examples: PayPal, Credit Card gateways, online processors

---

## Common Patterns for Both

### **checkCurrency()**

```php
public function checkCurrency($cart)
{
    $currency_order = new Currency($cart->id_currency);
    $currencies_module = $this->getCurrency($cart->id_currency);

    if (is_array($currencies_module)) {
        foreach ($currencies_module as $currency_module) {
            if ($currency_order->id == $currency_module['id_currency']) {
                return true;
            }
        }
    }
    return false;
}
```

### **Service Product Validation**

```php
// Always validate service products
ServiceProductCartDetail::validateServiceProductsInCart();
```

### **Order Restriction Check**

```php
if (Module::isInstalled('hotelreservationsystem')
    && Module::isEnabled('hotelreservationsystem')
) {
    require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
    if (HotelOrderRestrictDate::validateOrderRestrictDateOnPayment($this)) {
        Tools::redirect('index.php?controller=order-opc');
    }
}
```

---

**Next:** Learn about [API integration →](./integration-api.md)
