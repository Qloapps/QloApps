# Payment Module Structure

Complete guide to payment module folder structure, required files, and installation process.

---

## Module Folder Structure

### **Offline Payment Module** (Basic)
```
modules/qlopaymentname/
├── qlopaymentname.php              # Main module class
├── config.xml                      # Module metadata
├── index.php                       # Security (empty file)
├── logo.png                        # Module logo (PNG)
├── logo.gif                        # Module logo (GIF)
├── LICENSE.md                      # License file
├── Readme.md                       # Documentation
├── CHANGELOG.txt                   # Version history
├── controllers/
│   ├── front/
│   │   ├── payment.php             # Payment confirmation page
│   │   ├── validation.php          # Order creation
│   │   └── index.php               # Security
│   └── index.php                   # Security
├── views/
│   ├── templates/
│   │   ├── front/
│   │   │   ├── payment_execution.tpl  # Payment page
│   │   │   └── index.php
│   │   └── hook/
│   │       ├── payment.tpl         # Payment option display
│   │       ├── payment_return.tpl  # Success message
│   │       ├── infos.tpl           # Admin info block
│   │       └── index.php
│   ├── js/
│   │   └── front/
│   │       └── payment.js          # Frontend JS
│   ├── css/
│   │   └── payment.css             # Styles
│   └── index.php
├── translations/
│   └── index.php
└── upgrade/
    └── index.php
```

### **Online Payment Module** (Advanced)
```
modules/qlopaymentname/
├── qlopaymentname.php              # Main module class
├── config.xml
├── index.php
├── logo.png
├── logo.gif
├── LICENSE.md
├── Readme.md
├── CHANGELOG.txt
├── classes/                        # Helper/Service classes
│   ├── PaymentHelper.php           # API helper
│   ├── PaymentService.php          # Gateway service
│   ├── PaymentDb.php               # Database operations
│   ├── PaymentTransaction.php      # Transaction model
│   ├── PaymentRefund.php           # Refund handler
│   └── index.php
├── controllers/
│   ├── admin/                      # Admin controllers (optional)
│   │   ├── AdminTransactions.php  # View transactions
│   │   └── index.php
│   ├── front/
│   │   ├── payment.php             # Payment page
│   │   ├── validation.php          # (Optional for online)
│   │   ├── callback.php            # Payment return/callback
│   │   ├── webhook.php             # Webhook handler
│   │   ├── notify.php              # Alternative notification endpoint
│   │   └── index.php
│   └── index.php
├── views/
│   ├── templates/
│   │   ├── admin/                  # Admin templates
│   │   │   └── config.tpl
│   │   ├── front/
│   │   │   ├── payment.tpl
│   │   │   └── payment_return.tpl
│   │   └── hook/
│   │       ├── payment.tpl
│   │       └── payment_return.tpl
│   ├── js/
│   │   ├── admin/
│   │   │   └── config.js
│   │   └── front/
│   │       └── payment.js
│   ├── css/
│   │   └── payment.css
│   └── index.php
├── libs/                           # Third-party SDK
│   ├── gateway-sdk/                # Example: Payment Gateway SDK
│   └── index.php
├── logs/                           # Payment logs
│   └── index.php
├── translations/
│   └── index.php
└── upgrade/
    ├── upgrade-1.0.1.php
    └── index.php
```

---

## Mandatory Files

### **1. Main Module File** (`qlopaymentname.php`)

```php
<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class QloPaymentName extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'qlopaymentname';
        $this->tab = 'payments_gateways';              // Important: payment tab
        $this->version = '1.0.0';
        $this->author = 'Your Name';
        $this->need_instance = 0;
        $this->bootstrap = true;
        
        // Currency settings
        $this->currencies = true;                       // Enable currency restrictions
        $this->currencies_mode = 'checkbox';            // Allow multiple currencies
        
        parent::__construct();
        
        $this->displayName = $this->l('Payment Method Name');
        $this->description = $this->l('Accept payments via gateway');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        
        // Set payment type
        $this->payment_type = OrderPayment::PAYMENT_TYPE_ONLINE;  // or REMOTE_PAYMENT
        
        // Check configuration
        if (!Configuration::get('PAYMENT_NAME_API_KEY')) {
            $this->warning = $this->l('API credentials must be configured.');
        }
        
        // Check currency
        $currencies = Currency::checkPaymentCurrencies($this->id);
        if (!$currencies || !count($currencies)) {
            $this->warning = $this->l('No currency has been set for this module.');
        }
    }
    
    public function install()
    {
        return parent::install()
            && $this->registerHook('payment')
            && $this->registerHook('paymentReturn')
            && $this->registerHook('displayPaymentEU');
    }
    
    public function uninstall()
    {
        return Configuration::deleteByName('PAYMENT_NAME_API_KEY')
            && parent::uninstall();
    }
}
```

### **2. config.xml**

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<module>
    <name>qlopaymentname</name>
    <displayName><![CDATA[Payment Method Name]]></displayName>
    <version><![CDATA[1.0.0]]></version>
    <description><![CDATA[Accept payments via gateway]]></description>
    <author><![CDATA[Your Name]]></author>
    <tab><![CDATA[payments_gateways]]></tab>
    <is_configurable>1</is_configurable>
    <need_instance>0</need_instance>
    <limited_countries></limited_countries>
</module>
```

### **3. LICENSE.md**

```markdown
# License

This module is licensed under the Open Software License version 3.0 (OSL-3.0).

Full license text: https://opensource.org/license/osl-3-0-php

Copyright (c) 2024 Your Name
```

### **4. Readme.md**

```markdown
# Payment Method Name

Payment gateway module for QloApps.

## Features
- Accept online/offline payments
- Sandbox and production mode
- Refund support
- Transaction logging

## Installation
1. Upload module to `/modules/` folder
2. Install from QloApps admin panel
3. Configure API credentials
4. Enable currencies

## Configuration
Go to Modules > Payment > Configure

- API Key: Your gateway API key
- Test Mode: Enable for testing

## Support
support@yourcompany.com
```

### **5. CHANGELOG.txt**

```
2024-02-27 - v1.0.0
  * Initial release
  * Payment processing
  * Webhook support
```

---

## Install/Uninstall Implementation

### **Basic Install** (Offline Payment)

```php
public function install()
{
    // Call parent install first
    if (!parent::install()) {
        return false;
    }
    
    // Register hooks
    if (!$this->registerHook('payment')
        || !$this->registerHook('paymentReturn')
        || !$this->registerHook('displayPaymentEU')
    ) {
        return false;
    }
    
    // Set default configuration values
    if (!Configuration::updateValue('PAYMENT_NAME_ENABLED', 1)
        || !Configuration::updateValue('PAYMENT_NAME_DETAILS', '')
        || !Configuration::updateValue('PAYMENT_NAME_OWNER', '')
    ) {
        return false;
    }
    
    return true;
}

public function uninstall()
{
    // Delete configuration
    if (!Configuration::deleteByName('PAYMENT_NAME_ENABLED')
        || !Configuration::deleteByName('PAYMENT_NAME_DETAILS')
        || !Configuration::deleteByName('PAYMENT_NAME_OWNER')
    ) {
        return false;
    }
    
    // Call parent uninstall
    if (!parent::uninstall()) {
        return false;
    }
    
    return true;
}
```

### **Advanced Install** (Online Payment with Database)

```php
public function install()
{
    if (!parent::install()) {
        return false;
    }
    
    // Register hooks
    if (!$this->registerHook('payment')
        || !$this->registerHook('paymentReturn')
        || !$this->registerHook('displayPaymentEU')
        || !$this->registerHook('actionFrontControllerSetMedia')  // For JS/CSS
    ) {
        return false;
    }
    
    // Create database tables
    include_once dirname(__FILE__).'/classes/PaymentDb.php';
    $objPaymentDb = new PaymentDb();
    
    if (!$objPaymentDb->createTables()) {
        return false;
    }
    
    // Set default configuration
    if (!Configuration::updateValue('PAYMENT_NAME_LIVE_MODE', 0)
        || !Configuration::updateValue('PAYMENT_NAME_TEST_API_KEY', '')
        || !Configuration::updateValue('PAYMENT_NAME_LIVE_API_KEY', '')
    ) {
        return false;
    }
    
    return true;
}

public function uninstall()
{
    // Drop database tables
    include_once dirname(__FILE__).'/classes/PaymentDb.php';
    $objPaymentDb = new PaymentDb();
    
    if (!$objPaymentDb->dropTables()) {
        return false;
    }
    
    // Delete configuration
    if (!Configuration::deleteByName('PAYMENT_NAME_LIVE_MODE')
        || !Configuration::deleteByName('PAYMENT_NAME_TEST_API_KEY')
        || !Configuration::deleteByName('PAYMENT_NAME_LIVE_API_KEY')
        || !Configuration::deleteByName('PAYMENT_NAME_TEST_WEBHOOK_ID')
        || !Configuration::deleteByName('PAYMENT_NAME_LIVE_WEBHOOK_ID')
    ) {
        return false;
    }
    
    if (!parent::uninstall()) {
        return false;
    }
    
    return true;
}
```

---

## Payment-Specific Hooks

### **Required Hooks**

#### **1. hookPayment** - Display payment option

```php
public function hookPayment($params)
{
    // Check if module is active
    if (!$this->active) {
        return;
    }
    
    // Check if configured (online payments)
    if (!$this->isConfigured()) {
        return;
    }
    
    // Check currency
    if (!$this->checkCurrency($params['cart'])) {
        return;
    }
    
    // Assign variables to template
    $this->context->smarty->assign(array(
        'this_path' => $this->_path,
        'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
    ));
    
    return $this->display(__FILE__, 'payment.tpl');
}
```

#### **2. hookPaymentReturn** - Display confirmation message

```php
public function hookPaymentReturn($params)
{
    if (!$this->active) {
        return;
    }
    
    $objOrder = $params['objOrder'];
    $state = $objOrder->getCurrentState();
    
    // Check if payment successful
    if ($state == Configuration::get('PS_OS_PAYMENT_ACCEPTED')) {
        $success = 1;
    } else {
        $success = 0;
    }
    
    // Get order total
    if ($objOrder->is_advance_payment) {
        $total = $objOrder->advance_paid_amount;
    } else {
        $total = $objOrder->total_paid;
    }
    
    $this->smarty->assign(array(
        'total_to_pay' => Tools::displayPrice($total, $params['currencyObj'], false),
        'status' => $success,
        'id_order' => $objOrder->id,
    ));
    
    return $this->display(__FILE__, 'payment_return.tpl');
}
```

#### **3. hookDisplayPaymentEU** - EU payment display

```php
public function hookDisplayPaymentEU($params)
{
    if (!$this->active) {
        return;
    }
    
    if (!$this->checkCurrency($params['cart'])) {
        return;
    }
    
    $payment_options = array(
        'cta_text' => $this->l('Pay with').' '.$this->displayName,
        'logo' => Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/logo.png'),
        'action' => $this->context->link->getModuleLink($this->name, 'payment', array(), true)
    );
    
    return $payment_options;
}
```

### **Optional Hooks** (Online Payments)

#### **4. hookActionFrontControllerSetMedia** - Add JS/CSS

```php
public function hookActionFrontControllerSetMedia()
{
    $controller = Tools::getValue('controller');
    
    if ('orderopc' == $controller) {
        // Add gateway SDK (if needed)
        $this->context->controller->addJS('https://gateway.example.com/sdk.js', array('server' => 'remote'));
        
        // Add module JS
        $this->context->controller->addJS($this->local_path.'views/js/front/payment.js');
        
        // Add module CSS
        $this->context->controller->addCSS($this->_path.'views/css/payment.css');
        
        // Pass variables to JS
        Media::addJsDef(array(
            'PAYMENT_PUBLIC_KEY' => $this->getPublicKey(),
            'payment_callback_link' => $this->context->link->getModuleLink($this->name, 'callback')
        ));
    }
}
```

---

## Currency Restrictions

### **Setup in install()**

Currency restrictions are automatically handled by `parent::install()` which calls:
- `addCheckboxCurrencyRestrictionsForModule()` if `$this->currencies_mode = 'checkbox'`
- `addRadioCurrencyRestrictionsForModule()` if `$this->currencies_mode = 'radio'`

### **Check Currency in Hook**

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

---

## Database Setup (Online Payments)

### **PaymentDb.php Class**

```php
<?php
class PaymentDb
{
    public function createTables()
    {
        $sql = array();
        
        // Transactions table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'payment_transaction` (
            `id_transaction` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_order` INT(11) UNSIGNED NOT NULL,
            `id_cart` INT(11) UNSIGNED NOT NULL,
            `transaction_id` VARCHAR(255) NOT NULL,
            `amount` DECIMAL(20,6) NOT NULL,
            `currency` VARCHAR(3) NOT NULL,
            `status` VARCHAR(50) NOT NULL,
            `payment_method` VARCHAR(100),
            `gateway_response` TEXT,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_transaction`),
            KEY `id_order` (`id_order`),
            KEY `transaction_id` (`transaction_id`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        
        // Refunds table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'payment_refund` (
            `id_refund` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_transaction` INT(11) UNSIGNED NOT NULL,
            `id_order` INT(11) UNSIGNED NOT NULL,
            `refund_id` VARCHAR(255) NOT NULL,
            `amount` DECIMAL(20,6) NOT NULL,
            `reason` TEXT,
            `status` VARCHAR(50) NOT NULL,
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_refund`),
            KEY `id_transaction` (`id_transaction`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
        
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function dropTables()
    {
        $sql = array(
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'payment_transaction`',
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'payment_refund`'
        );
        
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        
        return true;
    }
}
```

---

## Security Files

### **index.php** (In every folder)

```php
<?php
/**
* Security index file
*
* Prevents directory listing
*/
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Location: ../');
exit;
```

---

## File Naming Conventions

### **Class Files**
- `PaymentHelper.php` - Helper class with static methods
- `PaymentService.php` - API service class
- `PaymentDb.php` - Database operations
- `PaymentTransaction.php` - Transaction ObjectModel
- `PaymentRefund.php` - Refund ObjectModel

### **Controller Files**
- `payment.php` - Show payment page
- `validation.php` - Process order (offline)
- `callback.php` - Handle payment return (online)
- `webhook.php` - Handle webhooks (online)
- `notify.php` - Alternative notification endpoint

### **Template Files**
- `payment.tpl` - Payment option display
- `payment_execution.tpl` - Payment confirmation page
- `payment_return.tpl` - Success/failure message
- `infos.tpl` - Admin information block

### **Asset Files**
- `payment.js` - Frontend JavaScript
- `payment.css` - Styles

---

## Upgrade Scripts

### **upgrade-1.0.1.php**

```php
<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_1($module)
{
    // Add new configuration
    Configuration::updateValue('PAYMENT_NAME_NEW_FEATURE', 0);
    
    // Alter database table
    $sql = 'ALTER TABLE `'._DB_PREFIX_.'payment_transaction` 
            ADD COLUMN `new_field` VARCHAR(255) AFTER `status`';
    
    if (!Db::getInstance()->execute($sql)) {
        return false;
    }
    
    return true;
}
```

---

## Best Practices

### **Module Structure**
✅ Keep all payment logic within module folder
✅ Use classes folder for helper/service classes
✅ Separate admin and front controllers
✅ Keep third-party SDKs in libs folder
✅ Include index.php in every folder

### **Installation**
✅ Always call `parent::install()` first
✅ Register all required hooks
✅ Create database tables if needed
✅ Set default configuration values
✅ Check for errors and return false on failure

### **Uninstallation**
✅ Drop database tables
✅ Delete all configuration values
✅ Call `parent::uninstall()` last
✅ Don't delete customer data (orders, transactions)

### **Currency**
✅ Set `$this->currencies = true`
✅ Choose currencies_mode: 'checkbox' or 'radio'
✅ Always check currency before showing payment option

---

**Next:** Learn about [payment patterns →](./payment-patterns.md)
