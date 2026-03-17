# Coding Conventions & Standards

> **Strict coding rules, prefix patterns, and naming conventions for QloApps module development**

## 📝 Core Principles

1. **All variables in camelCase** - No exceptions
2. **Strict PREFIX rules** - See below for all entity types
3. **No HTML in PHP files** - Always use .tpl files
4. **No CSS/JS in TPL files** - Separate .css and .js files
5. **Yoda conditions** where applicable
6. **Comments for complex logic** - Always explain "why", not "what"
7. **License headers on ALL files** - Every single file

---

## 🎯 PREFIX RULES (CRITICAL)

**Reference**: Prefix Rules in Module Development.md

### Calculating Module Prefix

**Example Module**: "QloApps Exit Popup"
**Short Form**: Take first letters → **qep**

### Prefix Application by Entity Type

| Entity | Prefix Format | Example |
|--------|---------------|---------|
| **Module Folder** | `qlo` (lowercase) | `qloexitpopup` |
| **Module Main File** | `qlo` (lowercase) | `qloexitpopup.php` |
| **Module Main Class** | `Qlo` (PascalCase) | `QloExitPopup` |
| **Database Tables** | `{short}_` (lowercase) | `qlo_qep_popup_content` |
| **Model Classes** | `{Short}` (PascalCase) | `QepPopupContent` |
| **Model Class Files** | `{Short}` (PascalCase) | `QepPopupContent.php` |
| **Constants** | `{SHORT}_` (UPPERCASE) | `QEP_BOOKING_TYPE` |
| **Configuration** | `{SHORT}_` (UPPERCASE) | `QEP_CUSTOM_STATUS` |
| **JavaScript Variables** | `{short}` (lowercase) | `qep_custom_status` |
| **CSS Classes** | `qlo-{short}-` (kebab-case) | `qlo-qep-popup` |
| **CSS/JS Files** | `qlo_` (snake_case) | `qlo_module_admin.css` |
| **TPL Files (partials)** | `qlo_` (snake_case) | `qlo_cart_booking.tpl` |

###  Examples in Practice

#### Module: "QloApps Booking Manager"
**Short Form**: qbm

```php
// Folder & Files
modules/qlobookingmanager/                     // Module folder
modules/qlobookingmanager/qlobookingmanager.php  // Main file

// Main Class
class QloBookingManager extends Module         // Main class

// Database Tables
_DB_PREFIX_.'qbm_bookings'                     // Table name
_DB_PREFIX_.'qbm_booking_details'              // Table name

// Model Classes & Files
QbmBookings                                     // Class name
QbmBookings.php                                 // File name

// Constants & Config
QBM_STATUS_PENDING                              // Constant
QBM_DEFAULT_LIMIT                               // Constant
Configuration::updateValue('QBM_ENABLED', 1);  // Configuration

// JavaScript
var qbm_booking_id = 123;                       // Variable
function qbm_validateForm() {}                  // Function

// CSS & Files
.qlo-qbm-booking-card {}                        // CSS class
qlo_booking_manager_front.css                   // CSS file
qlo_booking_manager_admin.js                    // JS file
qlo_cart_booking_list.tpl                       // TPL partial
```

---

## 🔤 Naming Conventions

### PHP Variables

**Rule**: camelCase - Always

```php
✅ Correct:
$bookingId = 123;
$customerFirstName = 'John';
$isRoomAvailable = true;
$hotelBranchInfo = array();

❌ Wrong:
$booking_id = 123;          // snake_case not allowed
$customerfirstname = 'John'; // no separation
$IsRoomAvailable = true;    // PascalCase not allowed
$hotel-branch-info = array(); // kebab-case not allowed
```

### PHP Functions & Methods

**Rule**: camelCase

```php
✅ Correct:
public function getBookingById($idBooking)
public function validateRoomAvailability($dateFrom, $dateTo)
private function calculateTotalPrice()

❌ Wrong:
public function get_booking_by_id()    // snake_case
public function GetBookingById()       // PascalCase
```

### Class Names

**Rule**: PascalCase with module prefix

```php
✅ Correct:
class QbmBookings extends ObjectModel
class QbmBookingDetails extends ObjectModel
class QbmHelper

❌ Wrong:
class Bookings                    // No prefix
class qbm_Bookings                // Wrong case
class qbm_bookings                // Wrong case
```

### Constants

**Rule**: UPPER_SNAKE_CASE with module prefix

```php
✅ Correct:
define('QBM_STATUS_PENDING', 1);
define('QBM_STATUS_CONFIRMED', 2);
define('QBM_DEFAULT_ROOMS', 10);

❌ Wrong:
define('qbm_status_pending', 1);     // Lowercase
define('STATUS_PENDING', 1);         // No prefix
define('QbmStatusPending', 1);       // PascalCase
```

### Configuration Variables

**Rule**: UPPER_SNAKE_CASE with module prefix

```php
✅ Correct:
Configuration::updateValue('QBM_ENABLED', 1);
Configuration::get('QBM_DEFAULT_STATUS');

❌ Wrong:
Configuration::updateValue('qbm_enabled', 1);  // Lowercase
Configuration::updateValue('ENABLED', 1);      // No prefix
```

### Database Table Names

**Rule**: lowercase with _DB_PREFIX_ and module prefix

```php
✅ Correct:
_DB_PREFIX_.'qbm_bookings'
_DB_PREFIX_.'qbm_booking_details'
_DB_PREFIX_.'qbm_room_types'

❌ Wrong:
_DB_PREFIX_.'Bookings'              // No module prefix, wrong case
_DB_PREFIX_.'QBM_Bookings'          // Wrong case
_DB_PREFIX_.'booking'               // No module prefix
```

### CSS Class Names

**Rule**: kebab-case with qlo-{module_prefix}- prefix

```css
✅ Correct:
.qlo-qbm-booking-card {}
.qlo-qbm-room-details {}
.qlo-qbm-price-summary {}

❌ Wrong:
.bookingCard {}              /* No prefix */
.qlo_qbm_booking_card {}     /* snake_case */
.qloQbmBookingCard {}        /* camelCase */
```

### CSS/JS File Names

**Rule**: snake_case with qlo_ prefix

```
✅ Correct:
qlo_booking_manager_front.css
qlo_booking_manager_admin.js
qlo_room_selection.css

❌ Wrong:
booking-manager-front.css    // No prefix, wrong case
qloBookingManager.css        // camelCase
QloBookingManager.css        // PascalCase
```

### Template File Names

**Rule**: For partials - snake_case with qlo_ prefix; For main - descriptive

```
✅ Correct Partials:
qlo_cart_booking_list.tpl
qlo_room_type_details.tpl
qlo_price_breakdown.tpl

✅ Correct Main Templates:
display.tpl
bookings.tpl
settings.tpl

❌ Wrong:
cart-booking-list.tpl        // No prefix, kebab-case
CartBookingList.tpl          // PascalCase
```

---

## 📜 License Header (MANDATORY)

**Rule**: Every file must have license header at the top

**Template for PHP Files**:

```php
<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/
```

**Template for JavaScript Files**:

```javascript
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* [... rest of license ...]
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/
```

**Template for CSS Files**:

```css
/**
* NOTICE OF LICENSE
*
* [... license content ...]
*/
```

**Reference**: See any file in `modules/hotelreservationsystem/`

---

## 🎨 Code Style Standards

### 1. No HTML in PHP Files

**Rule**: ALL HTML must be in .tpl files

```php
❌ WRONG - HTML in PHP:
public function hookDisplayHeader()
{
    return '<div class="my-module">
        <h1>Title</h1>
        <p>Content</p>
    </div>';
}

✅ CORRECT - Use template:
public function hookDisplayHeader()
{
    $this->smarty->assign(array(
        'title' => 'Title',
        'content' => 'Content'
    ));

    return $this->display(__FILE__, 'display-header.tpl');
}

// In views/templates/hook/display-header.tpl:
<div class="my-module">
    <h1>{$title|escape:'html':'UTF-8'}</h1>
    <p>{$content|escape:'html':'UTF-8'}</p>
</div>
```

### 2. No CSS/JS in TPL Files

**Rule**: Separate .css and .js files, load via hooks

```smarty
❌ WRONG - CSS in TPL:
<style>
.my-class {
    color: red;
}
</style>

<script>
function myFunction() {
    // code
}
</script>

✅ CORRECT - Separate files:
{* In hookDisplayHeader or hookActionFrontControllerSetMedia *}
$this->context->controller->addCSS($this->_path.'views/css/qlo_module.css');
$this->context->controller->addJS($this->_path.'views/js/qlo_module.js');
```

### 3. Yoda Conditions

**Rule**: Put constant/literal on the left side

```php
✅ Yoda Conditions (Preferred):
if (1 == $status) {}
if ('active' == $type) {}
if (null === $value) {}
if (true === $isEnabled) {}

❌ Regular Conditions:
if ($status == 1) {}          // Can accidentally assign with =
if ($type == 'active') {}
if ($value === null) {}
```

**Why**: Prevents accidental assignment (`=` instead of `==`)

**When to use**: Everywhere possible, especially with constants and literals

### 4. Comments for Complex Logic

**Rule**: Add comments explaining "why", not "what"

```php
✅ Good Comments:
// Check if booking is within valid date range to prevent backdated reservations
if ($bookingDate < date('Y-m-d')) {
    return false;
}

// Calculate discounted price using hotel-specific pricing rules
// See HotelPricingRules.php for discount calculation logic
$finalPrice = $this->calculateDiscountedPrice($basePrice, $discountRules);

❌ Bad Comments:
// Check if date is less than today
if ($bookingDate < date('Y-m-d')) {}  // "What" is obvious from code

// Calculate price
$finalPrice = $this->calculatePrice();  // Not helpful
```

### 5. Standard AJAX Response Format

**Rule**: Use consistent JSON response structure

```php
✅ Standard Response:
die(json_encode(array(
    'success' => true,           // or false
    'errors' => array(),         // Array of error messages
    'msg' => 'Success message',  // User-friendly message
    'data' => array(             // Response data
        'booking_id' => 123,
        'total' => 99.99
    )
)));

❌ Inconsistent Response:
die(json_encode(array('status' => 'ok', 'id' => 123)));  // Different structure
echo 'Success';  // Not JSON
```

### 6. JavaScript Variables from PHP

**Rule**: Assign JS variables from PHP controller, not hardcoded

```php
✅ Correct - Assign from PHP:
// In controller
Media::addJsDef(array(
    'qbm_booking_id' => $idBooking,
    'qbm_ajax_url' => $this->context->link->getModuleLink('qlobookingmanager', 'ajax'),
    'qbm_currency_sign' => $this->context->currency->sign
));

// In JavaScript
var bookingId = qbm_booking_id;

❌ Wrong - Hardcoded:
// In JavaScript
var ajaxUrl = '/modules/qlobookingmanager/ajax.php';  // Hardcoded path
```

---

## ✅ Coding Standards Checklist

Before committing code:

### Naming
- [ ] All variables in camelCase
- [ ] All class names in PascalCase with prefix
- [ ] All constants in UPPER_SNAKE_CASE with prefix
- [ ] All table names lowercase with prefix
- [ ] CSS classes in kebab-case with prefix
- [ ] CSS/JS files in snake_case with qlo_ prefix

### Code Structure
- [ ] No HTML in PHP files
- [ ] No CSS in TPL files
- [ ] No JS in TPL files
- [ ] Yoda conditions applied where suitable
- [ ] Comments added for complex logic
- [ ] License header on all files

### File Organization
- [ ] index.php in every folder
- [ ] Files in correct folders (admin/front/global)
- [ ] Proper file naming conventions
- [ ] No files outside module folder

### Standards
- [ ] AJAX responses use standard JSON format
- [ ] JS variables assigned from PHP
- [ ] All user text uses $this->l() for translation
- [ ] Proper escaping in templates

---

## 📚 Examples from hotelreservationsystem

Study these for correct patterns:

| Pattern | File Reference |
|---------|----------------|
| Main class structure | `modules/hotelreservationsystem/hotelreservationsystem.php` |
| Constants definition | `modules/hotelreservationsystem/define.php` |
| ObjectModel class | `modules/hotelreservationsystem/classes/HotelBookingDetail.php` |
| Database class | `modules/hotelreservationsystem/classes/HotelReservationSystemDb.php` |
| Admin controller | `modules/hotelreservationsystem/controllers/admin/` |
| CSS naming | `modules/hotelreservationsystem/views/css/` |
| JS naming | `modules/hotelreservationsystem/views/js/` |

---

## 🔗 Related Documentation

- [architecture.md](./architecture.md) - File structure
- [templates-views.md](./templates-views.md) - TPL, CSS, JS details
- [security-validation.md](./security-validation.md) - Input validation

---

**Next**: Review [hooks-system.md](./hooks-system.md) for integration patterns
