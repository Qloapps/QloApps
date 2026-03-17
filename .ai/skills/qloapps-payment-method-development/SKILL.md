---
skill_name: qloapps-payment-method-development
author: QloApps
tags:
  - payment
  - gateway
  - paymentmodule
  - online-payment
  - offline-payment
  - transactions
prerequisites:
  - PHP 8.1+
  - QloApps 1.7.0+ installed
  - Understanding of payment flows
  - Gateway account (for online payments)
related_skills:
  - qloapps-addon-development
reference_files:
  - modules/bankwire/bankwire.php
  - modules/cheque/cheque.php
  - modules/qlopaypalcommerce/qlopaypalcommerce.php
  - classes/PaymentModule.php
---

# QloApps Payment Method Development

> **Create payment gateway modules for QloApps - both offline and online payment methods**

## ⚡ Quick Commands

```bash
# Payment module structure
modules/qlopaymentname/
├── qlopaymentname.php           # Main PaymentModule class
├── controllers/front/
│   ├── payment.php              # Payment confirmation page
│   ├── validation.php           # Order processing (offline)
│   ├── callback.php             # Payment callback (online)
│   └── webhook.php              # Payment webhook (online)
├── classes/                     # Helper/Service classes
├── views/templates/
│   ├── front/payment.tpl        # Payment option display
│   └── hook/payment_return.tpl  # Success/failure message
└── LICENSE.md

# Common operations
- Create offline payment    → See [payment-patterns.md](./payment-patterns.md#offline-payments)
- Create online payment     → See [payment-patterns.md](./payment-patterns.md#online-payments)
- Setup API credentials     → See [integration-api.md](./integration-api.md)
- Process payment           → See [controllers-transactions.md](./controllers-transactions.md)
- Handle webhooks           → See [integration-api.md](./integration-api.md#webhooks)
```

---

## 🚀 Quick Start (60 seconds)

This skill teaches you to create **payment gateway modules** for QloApps:
- ✅ Offline payments (bank transfer, cheque, cash on delivery)
- ✅ Online payments (credit cards, payment gateways, etc.)
- ✅ API integration with payment gateways
- ✅ Webhook handling for payment notifications
- ✅ Transaction management and refunds

**Quick Module Comparison:**

| Type | Example | Controllers | API | Complexity |
|------|---------|-------------|-----|------------|
| **Offline** | Bankwire, Cheque | payment, validation | No | Low |
| **Online** | PayPal, Credit Card Gateways | payment, validation, callback, webhook | Yes | Medium-High |

---

## ✅ When to Use This Skill

Use this skill when:
- Creating payment gateway integration
- Adding payment method to QloApps
- Integrating third-party payment processors
- Building custom payment solutions

**Don't use this skill when:**
- Creating general QloApps modules → Use [qloapps-addon-development](../qloapps-addon-development/SKILL.md)
- Building stats modules → Use qloapps-stats-development
- Creating shipping modules → Use qloapps-carrier-development

---

## 🎯 Payment Module Types

### **Offline Payments (Remote Payments)**
Customer pays outside the system (bank transfer, check, cash).

**Characteristics:**
- No real-time processing
- Order created in "Awaiting Payment" state
- Email with payment instructions
- Admin manually confirms payment
- `payment_type = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT`

**Examples:** Bankwire, Cheque

---

### **Online Payments (Real-time Processing)**
Immediate payment processing via gateway API.

**Characteristics:**
- Real-time authorization/capture
- API integration required
- Webhook for payment notifications
- Auto order status update
- `payment_type = OrderPayment::PAYMENT_TYPE_ONLINE`

**Examples:** PayPal Commerce, Credit Card gateways

---

## 📦 Essential Components

### **PaymentModule Class**
All payment modules extend `PaymentModule`:

```php
class YourPayment extends PaymentModule
{
    public $payment_type = OrderPayment::PAYMENT_TYPE_ONLINE; // or REMOTE_PAYMENT

    public function __construct()
    {
        $this->name = 'yourpayment';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'YourName';
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        parent::__construct();

        $this->displayName = $this->l('Your Payment Method');
        $this->description = $this->l('Accept payments via Your Gateway');
    }
}
```

### **Required Hooks**
```php
public function install()
{
    return parent::install()
        && $this->registerHook('payment')           // Display payment option
        && $this->registerHook('paymentReturn')     // Show confirmation message
        && $this->registerHook('displayPaymentEU'); // EU payment display
}

public function hookPayment($params)
{
    // Display payment option on checkout
    return $this->display(__FILE__, 'payment.tpl');
}

public function hookPaymentReturn($params)
{
    // Display success/failure message
    $this->smarty->assign('status', 'ok');
    return $this->display(__FILE__, 'payment_return.tpl');
}
```

### **validateOrder() Method**
Core method to create order:

```php
$this->validateOrder(
    $cart->id,                          // Cart ID
    Configuration::get('PS_OS_PAYMENT'), // Order state
    $total,                             // Amount paid
    $this->displayName,                 // Payment method name
    $message,                           // Optional message
    $extraVars,                         // Extra variables (transaction_id, etc.)
    $currency->id,                      // Currency ID
    false,                              // Don't touch amount
    $customer->secure_key              // Customer secure key
);
```

---

## 🗂️ Skill Components

### 📐 **Module Structure & Setup**
→ See [module-structure.md](./module-structure.md)
- Payment module folder structure
- Required files and folders
- Install/uninstall implementation
- Currency restrictions
- Payment-specific hooks

### 💳 **Payment Patterns**
→ See [payment-patterns.md](./payment-patterns.md)
- PaymentModule class deep dive
- Offline payment pattern (bankwire, cheque)
- Online payment pattern (credit card gateways, PayPal)
- Advance payment support
- Payment type selection

### 🔌 **Integration & API**
→ See [integration-api.md](./integration-api.md)
- Admin configuration forms
- API credentials (test/live)
- Sandbox vs production modes
- Webhook setup and management
- Gateway API integration
- Authentication patterns

### 🎮 **Controllers & Transactions**
→ See [controllers-transactions.md](./controllers-transactions.md)
- Payment controller
- Validation controller
- Callback controller (online payments)
- Webhook/notify controller
- validateOrder() deep dive
- Transaction recording
- Refund management
- Order states
- Security best practices

---

## 📖 Quick Reference

### Offline Payment Checklist
- [ ] Create main module class (extends PaymentModule)
- [ ] Set `payment_type = REMOTE_PAYMENT`
- [ ] Create payment.php controller (show confirmation page)
- [ ] Create validation.php controller (create order)
- [ ] Register payment hooks
- [ ] Create payment.tpl template
- [ ] Add configuration for payment details
- [ ] Setup email template with instructions

### Online Payment Checklist
- [ ] Create main module class (extends PaymentModule)
- [ ] Set `payment_type = ONLINE`
- [ ] Create configuration form (API keys, test/live mode)
- [ ] Create payment.php controller
- [ ] Create callback.php controller (handle return)
- [ ] Create webhook.php controller (handle notifications)
- [ ] Create helper/service classes for API
- [ ] Implement API authentication
- [ ] Setup webhook URL
- [ ] Handle payment success/failure
- [ ] Implement refund capability
- [ ] Create transaction logging

### Security Checklist
- [ ] Never store full credit card data
- [ ] Use HTTPS for all payment pages
- [ ] Validate secure_key before order creation
- [ ] Verify webhook signatures
- [ ] Use Configuration::get() for API keys
- [ ] Sanitize all user inputs
- [ ] Log payment transactions
- [ ] Handle errors gracefully

---

## 💡 Payment Module Template

**Offline Payment (Simple):**
```php
<?php
class Bankwire extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'bankwire';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Your Name';
        $this->currencies = true;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Bank Wire');
        $this->description = $this->l('Accept payments via bank transfer');
        $this->payment_type = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT;
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('payment')
            && $this->registerHook('paymentReturn');
    }

    public function hookPayment($params)
    {
        if (!$this->active) {
            return;
        }
        return $this->display(__FILE__, 'payment.tpl');
    }
}
```

**Online Payment (Advanced):**
```php
<?php
include_once 'classes/HelperClass.php';

class PaymentGateway extends PaymentModule
{
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
        $this->payment_type = OrderPayment::PAYMENT_TYPE_ONLINE;
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('payment')
            && $this->registerHook('paymentReturn')
            && $this->createTables();
    }

    public function getContent()
    {
        // Configuration form for API keys, test/live mode
        return $this->renderConfigForm();
    }

    public function hookPayment($params)
    {
        if (!$this->active || !$this->isConfigured()) {
            return;
        }

        $this->context->smarty->assign([
            'gateway_public_key' => $this->getPublicKey(),
    }
}
```

---

## 🔍 Common Patterns

### Currency Check
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

### Advance Payment Support
```php
// In payment/validation controller
if ($cart->is_advance_payment) {
    $total = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
} else {
    $total = $cart->getOrderTotal(true, Cart::BOTH);
}
```

### Order Restriction Validation
```php
// Check order restrictions before payment
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

## ⚠️ Common Pitfalls

### ❌ Pitfall 1: Storing Credit Card Data
**What goes wrong**: PCI compliance violation, security risk

**✅ Correct approach**: Use tokenization - gateway handles card data, you store only token

### ❌ Pitfall 2: Missing Webhook Signature Verification
**What goes wrong**: Fraudulent payment notifications

**✅ Correct approach**: Always verify webhook signatures from gateway

### ❌ Pitfall 3: No Test/Live Mode Separation
**What goes wrong**: Test transactions in production

**✅ Correct approach**: Separate test and live API keys, mode switcher in config

### ❌ Pitfall 4: Creating Order Before Payment Confirmation
**What goes wrong**: Unpaid orders in system

**✅ Correct approach**:
- Offline: Create order in "Awaiting Payment" state
- Online: Create order only after payment success callback/webhook

---

## 📚 Reference Modules

Study these implementations:

| Module | Type | Key Features | Learn From |
|--------|------|--------------|------------|
| **bankwire** | Offline | Simple config, email instructions | Basic offline pattern |
| **cheque** | Offline | Minimal setup | Minimal offline example |
| **qlopaypalcommerce** | Online | OAuth, webhooks, refunds | Complex API integration |


**File References:**
- Core PaymentModule: `classes/PaymentModule.php`
- Order creation: `classes/order/Order.php`
- Order payment: `classes/order/OrderPayment.php`

---

## 🐛 Troubleshooting

### Issue 1: Payment Option Not Showing
**Symptoms**: Payment method missing at checkout

**Check:**
- Module is active
- Hook registered: `hookPayment`
- Currency is enabled for module
- Module configuration completed (for online payments)

### Issue 2: Webhook Not Working
**Symptoms**: Payment confirmed but order status not updating

**Check:**
- Webhook URL correctly registered with gateway
- Webhook signature verification passing
- Webhook controller accessible (no 404)
- Check module logs for webhook errors

### Issue 3: Order Creation Fails
**Symptoms**: Payment processed but no order created

**Check:**
- Cart still exists and not already converted
- Customer secure_key matches
- Order state exists
- All required validateOrder() parameters provided
- Check QloApps error logs

---

## 📝 Development Workflow

### Phase 1: Planning (15 minutes)
1. Choose payment type (offline vs online)
2. Review gateway documentation
3. Get test account/credentials
4. Plan module structure

### Phase 2: Basic Setup (30 minutes)
1. Create module folder structure
2. Create main module file
3. Implement install/uninstall
4. Add configuration form (if online)
5. Register payment hooks

### Phase 3: Payment Flow (60-120 minutes)
1. Create payment controller
2. Create validation controller
3. Implement validateOrder() call
4. Test basic order creation
5. Add payment templates

### Phase 4: Advanced (Online Only) (60-180 minutes)
1. Integrate gateway API
2. Setup webhook handling
3. Implement callback controller
4. Add transaction logging
5. Implement refund capability
6. Test all payment scenarios

### Phase 5: Testing (30-60 minutes)
1. Test with different currencies
2. Test advance payment
3. Test payment success/failure
4. Test webhook notifications
5. Test refunds (if applicable)

---

## 🔗 Related Skills

- [qloapps-addon-development](../qloapps-addon-development/SKILL.md) - General module development
- qloapps-webservice-development - API development for integrations

---

## 📖 Additional Resources

- **QloApps DevDocs**: https://devdocs.qloapps.com/
- **Payment Gateway Documentation**: Check your specific gateway
- **PCI Compliance**: https://www.pcisecuritystandards.org/

---

## 💬 Next Steps

1. **Understand payment types** → Read [payment-patterns.md](./payment-patterns.md)
2. **Setup module structure** → Review [module-structure.md](./module-structure.md)
3. **Configure gateway** → Study [integration-api.md](./integration-api.md)
4. **Implement payment flow** → Follow [controllers-transactions.md](./controllers-transactions.md)

**Ready to build your payment gateway? Start with [module-structure.md](./module-structure.md)!** 💳
