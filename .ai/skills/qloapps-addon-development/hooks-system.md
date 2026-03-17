# Hooks System - Integration Without Core Modification

> **Hooks-first architecture: The primary method for extending QloApps functionality**

## 🎯 The Hooks-First Philosophy

**Core Principle**: Never modify core files - use hooks for ALL integrations

### Priority Order (The Right Way)

```
1. ✅ Use Existing Hooks        → 90% of cases
2. ⚠️ Create Custom Hooks       → 8% of cases (when existing hooks insufficient)
3. ❌ Use Overrides             → 2% of cases (absolute last resort)
```

---

## 🔌 What Are Hooks?

Hooks are **integration points** in QloApps code that allow modules to inject functionality without modifying core files.

**Two Types**:
- **Display Hooks**: Return HTML/content for display (`display*`)
- **Action Hooks**: Execute code on events (`action*`)

**Reference**: `classes/Hook.php` - All available hooks

---

## 📖 Using Existing Hooks

### Step 1: Register Hook in install()

```php
public function install()
{
    return parent::install()
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayFooter')
        && $this->registerHook('actionObjectOrderAddAfter');
}
```

### Step 2: Implement Hook Method

**Naming Convention**: `hook{HookName}`

```php
// Display Hook - Return HTML
public function hookDisplayHeader($params)
{
    // Add CSS/JS files
    $this->context->controller->addCSS($this->_path.'views/css/qlo_module_front.css');
    $this->context->controller->addJS($this->_path.'views/js/qlo_module_front.js');

    // Optionally return HTML
    $this->smarty->assign(array(
        'data' => $this->getData()
    ));

    return $this->display(__FILE__, 'display-header.tpl');
}

// Action Hook - Execute code
public function hookActionObjectOrderAddAfter($params)
{
    $order = $params['object'];

    // Perform actions after order creation
    $this->processOrderData($order);

    // Action hooks don't return anything
}
```

---

## 📋 Common Hooks Reference

**Reference**: Study `modules/hotelreservationsystem/hotelreservationsystem.php`

### Display Hooks (Frontend)

| Hook | Location | Common Use |
|------|----------|------------|
| `displayHeader` | `<head>` section | Add CSS/JS, meta tags |
| `displayNav` | Top navigation | Add menu items, customer info |
| `displayTop` | After header | Banners, notifications |
| `displayLeftColumn` | Left sidebar | Widgets, filters |
| `displayRightColumn` | Right sidebar | Widgets, ads |
| `displayFooter` | Footer section | Links, scripts |
| `displayProductButtons` | Product page | Custom buttons |
| `displayShoppingCartFooter` | Cart page | Additional info |

**Example from hotelreservationsystem**:
```php
// Line 74-77
public function hookDisplayHeader()
{
    $this->context->controller->addCSS($this->_path.'/views/css/HotelReservationFront.css');
    $this->context->controller->addJS($this->_path.'/views/js/HotelReservationFront.js');
}

// Line 94-102
public function hookDisplayNav()
{
    $this->smarty->assign(array(
        'phone' => Configuration::get('WK_CUSTOMER_SUPPORT_PHONE_NUMBER'),
        'email' => Configuration::get('WK_CUSTOMER_SUPPORT_EMAIL'),
    ));

    return $this->display(__FILE__, 'display-nav.tpl');
}
```

### Action Hooks (Backend Events)

| Hook | When Fired | Common Use |
|------|------------|------------|
| `actionObjectOrderAddAfter` | After order created | Process booking, send notifications |
| `actionObjectOrderUpdateAfter` | After order updated | Update related data |
| `actionObjectOrderDeleteAfter` | After order deleted | Cleanup related data |
| `actionAuthentication` | User logs in | Track login, set session data |
| `actionCustomerAccountAdd` | New customer registered | Welcome email, setup account |
| `actionCartSave` | Cart saved | Update cart-related data |
| `actionAdminControllerSetMedia` | Admin page loads | Add admin CSS/JS |
| `actionFrontControllerSetMedia` | Front page loads | Add front CSS/JS |

**Example**:
```php
public function hookActionObjectOrderAddAfter($params)
{
    $order = $params['object'];  // Order object
    $idOrder = $order->id;

    // Process booking after order creation
    $objBooking = new QbmBooking();
    $objBooking->id_order = $idOrder;
    $objBooking->status = QBM_STATUS_PENDING;
    $objBooking->save();

    // Send confirmation email
    $this->sendConfirmationEmail($order);
}
```

### Admin Hooks

| Hook | Location | Common Use |
|------|----------|------------|
| `displayAdminStatsModules` | Stats dashboard | Display statistics |
| `displayAdminOrderLeft` | Order page (left) | Additional order info |
| `displayAdminOrderRight` | Order page (right) | Custom actions |
| `displayAdminProductsExtra` | Product page | Extra product fields |
| `displayBackOfficeHeader` | Admin header | Add admin CSS/JS |

---

## 🎣 Hook Parameters

Most hooks receive `$params` array with context data.

**Common Parameters**:
```php
public function hookActionObjectOrderAddAfter($params)
{
    $order = $params['object'];          // The main object (Order, Customer, etc.)
    $cookie = $params['cookie'];         // Cookie object (if available)
    $cart = $params['cart'];             // Cart object (if available)
}

public function hookDisplayHeader($params)
{
    // Access context
    $this->context->cart;        // Current cart
    $this->context->customer;    // Current customer
    $this->context->language;    // Current language
    $this->context->controller;  // Current controller
}
```

**To find available parameters**: Check the `Hook::exec()` call in core files where the hook is executed.

---

## 🆕 Creating Custom Hooks

**When to Use**:
- ✅ Existing hooks don't satisfy requirement
- ✅ Hook will be useful for other modules
- ✅ Increases system flexibility

**Requirements**:
1. Hook must be generally useful (not just for your module)
2. Document hook placement in module README.md
3. User must manually add hook to core file

### Process

#### Step 1: Design Hook

Choose hook name following convention:
- Display hooks: `displayModuleName{Location}`
- Action hooks: `actionAfter{Event}` or `actionBefore{Event}`

```php
// Example: Hook after cart validation
Hook::exec('actionAfterCartValidation', array(
    'cart' => $this,
    'id_cart' => $this->id
));
```

#### Step 2: Document in README.md

**Critical**: User must manually add hook to core file

```markdown
## Manual Installation Steps

⚠️ **IMPORTANT**: This module requires a custom hook for cart validation.

### Add Custom Hook

1. Open `classes/Cart.php`
2. Find the `validateCart()` method (approximately line 456)
3. Add this code **after line 465** (after cart validation logic):

\`\`\`php
// Allow modules to hook after cart validation
Hook::exec('actionAfterCartValidation', array(
    'cart' => $this,
    'id_cart' => $this->id
));
\`\`\`

4. Save the file

### Why This Hook?

This hook allows modules to extend cart validation functionality without
overriding the Cart class. It can be used by any module that needs to
perform actions after cart validation.

### Benefits

- Other modules can also use this hook
- No override conflicts
- Clean integration
```

#### Step 3: Use Hook in Your Module

```php
public function install()
{
    return parent::install()
        && $this->registerHook('actionAfterCartValidation');
}

public function hookActionAfterCartValidation($params)
{
    $cart = $params['cart'];
    $idCart = $params['id_cart'];

    // Your custom logic
    $this->validateCustomRules($cart);
}
```

### Custom Hook Guidelines

✅ **Do**:
- Choose descriptive hook names
- Document clearly in README
- Explain hook benefits for other modules
- Provide exact file and line number

❌ **Don't**:
- Create hooks for single-module use only
- Place hooks randomly without documentation
- Assume user will figure it out

---

## ⚠️ When Hooks Are Not Enough: Overrides

**Last Resort Only** - Use when:
- ❌ No existing hook works
- ❌ Custom hook isn't feasible
- ❌ Absolutely no other solution

### Override Risks

**Problems**:
- **Conflicts**: Two modules overriding same class = one breaks
- **Updates**: Core updates may break overrides
- **Maintenance**: Hard to track and debug

### Creating Override (If You Must)

**Example**: Override Cart class

```php
// override/classes/Cart.php
<?php

class Cart extends CartCore
{
    /**
     * Override method - keep changes minimal
     */
    public function myMethod()
    {
        // Call parent first if possible
        $result = parent::myMethod();

        // Your minimal changes
        $this->customLogic();

        return $result;
    }
}
```

**After creating override**:
1. Delete `cache/class_index.php`
2. Test thoroughly
3. Document in README.md:

```markdown
## Override Warning

⚠️ This module overrides the following core classes:
- `classes/Cart.php`

**Compatibility Risk**: May conflict with other modules that override the same class.

**Alternative**: Consider requesting a core hook from QloApps team for future versions.
```

---

## 🔍 Hook Development Checklist

Before implementing hook-based feature:

### Planning
- [ ] Identified specific functionality needed
- [ ] Checked existing hooks list
- [ ] Found appropriate existing hook OR
- [ ] Designed custom hook that's generally useful

### Implementation
- [ ] Registered hook in `install()`
- [ ] Created `hook{HookName}` method
- [ ] Tested hook execution
- [ ] Used proper return values (HTML for display, nothing for action)

### Custom Hooks (if created)
- [ ] Hook name follows conventions
- [ ] Documented in README.md with exact placement
- [ ] Provided file path and line number
- [ ] Explained benefits for other modules
- [ ] Tested after manual placement

### Override (if absolutely necessary)
- [ ] Documented why override is necessary
- [ ] Kept changes minimal
- [ ] Called parent methods where possible
- [ ] Documented in README with warning
- [ ] Deleted `cache/class_index.php`

---

## 💡 Examples from hotelreservationsystem

Study these implementations:

```php
// Display Hook with CSS/JS
public function hookDisplayHeader()
{
    // Remove old bookings
    if (isset($this->context->cart->id) && $this->context->cart->id) {
        $objHotelCartBookingData = new HotelCartBookingData();
        $objHotelCartBookingData->removeBackdateRoomsFromCart($this->context->cart->id);
    }

    // Add assets
    $this->context->controller->addCSS($this->_path.'/views/css/HotelReservationFront.css');
    $this->context->controller->addJS($this->_path.'/views/js/HotelReservationFront.js');
}

// Display Hook with Template
public function hookDisplayNav()
{
    $this->smarty->assign(array(
        'phone' => Configuration::get('WK_CUSTOMER_SUPPORT_PHONE_NUMBER'),
        'email' => Configuration::get('WK_CUSTOMER_SUPPORT_EMAIL'),
    ));

    return $this->display(__FILE__, 'display-nav.tpl');
}

// Action Hook for Media
public function hookActionFrontControllerSetMedia()
{
    if (Configuration::get('WK_CUSTOMER_SUPPORT_PHONE_NUMBER') != ''
        || Configuration::get('WK_CUSTOMER_SUPPORT_EMAIL') != ''
    ) {
        $this->context->controller->addCSS($this->getPathUri().'views/css/hook/display-nav.css');
    }
}
```

---

## 🔗 Related Documentation

- [SKILL.md](./SKILL.md) - Hooks-first philosophy overview
- [security-validation.md](./security-validation.md) - Securing hook implementations
- [templates-views.md](./templates-views.md) - Returning templates from hooks

---

**Next**: Learn [models-repositories.md](./models-repositories.md) for database operations
