# Templates, Views, CSS & JavaScript

> **Smarty templates, stylesheets, and JavaScript files with strict separation rules**

## 🎨 Core Principles

1. **No HTML in PHP files** - ALL HTML in .tpl files
2. **No CSS in TPL files** - Separate .css files
3. **No JavaScript in TPL files** - Separate .js files
4. **File naming**: `qlo_` prefix for CSS/JS, partials use `qlo_` in TPL
5. **Escaping**: Always use `|escape:'html':'UTF-8'` in templates

---

## 📄 Smarty Templates

### Template Structure

**Location**: `views/templates/`

```
views/templates/
├── admin/              # Admin templates
│   ├── settings.tpl
│   └── list.tpl
├── front/              # Front templates
│   ├── display.tpl
│   └── booking.tpl
└── hook/               # Hook templates
    ├── qlo_display_header.tpl
    └── qlo_cart_summary.tpl
```

### Basic Template Example

**Reference**: `modules/hotelreservationsystem/views/templates/hook/display-nav.tpl`

```smarty
{**
 * [LICENSE HEADER]
 *}

<div class="qlo-qym-booking-display">
    <h2>{l s='My Bookings' mod='qloyourmodule'}</h2>
    
    {if isset($bookings) && count($bookings)}
        <ul class="booking-list">
            {foreach from=$bookings item=booking}
                <li class="booking-item">
                    <span class="booking-ref">{$booking.booking_reference|escape:'html':'UTF-8'}</span>
                    <span class="booking-price">{displayPrice price=$booking.total_price}</span>
                    <span class="booking-status">
                        {if $booking.status == 1}
                            {l s='Confirmed' mod='qloyourmodule'}
                        {else}
                            {l s='Pending' mod='qloyourmodule'}
                        {/if}
                    </span>
                </li>
            {/foreach}
        </ul>
    {else}
        <p class="no-bookings">{l s='No bookings found' mod='qloyourmodule'}</p>
    {/if}
</div>
```

---

## 🔤 Smarty Syntax Essentials

### Variables & Escaping

```smarty
{* Display variable with HTML escaping *}
{$variable|escape:'html':'UTF-8'}

{* Display variable without escaping (when variable contains safe HTML) *}
{$html_content nofilter}

{* Access array/object properties *}
{$booking.booking_reference|escape:'html':'UTF-8'}
{$customer->firstname|escape:'html':'UTF-8'}

{* Check if variable exists *}
{if isset($variable)}
    {$variable|escape:'html':'UTF-8'}
{/if}
```

### Translations

```smarty
{* Simple translation *}
{l s='Hello World' mod='qloyourmodule'}

{* Translation with variables (sprintf style) *}
{l s='Hello %s' sprintf=[$customer_name] mod='qloyourmodule'}

{* Multiple variables *}
{l s='Booking %s costs %s' sprintf=[$reference, $price] mod='qloyourmodule'}
```

### Conditions

```smarty
{* If/else *}
{if $status == 1}
    <span class="confirmed">{l s='Confirmed' mod='qloyourmodule'}</span>
{elseif $status == 2}
    <span class="pending">{l s='Pending' mod='qloyourmodule'}</span>
{else}
    <span class="cancelled">{l s='Cancelled' mod='qloyourmodule'}</span>
{/if}

{* Check if empty *}
{if isset($bookings) && count($bookings)}
    {* Has bookings *}
{else}
    {* No bookings *}
{/if}
```

### Loops

```smarty
{* Foreach loop *}
{foreach from=$bookings item=booking}
    <div class="booking">
        {$booking.booking_reference|escape:'html':'UTF-8'}
    </div>
{/foreach}

{* Foreach with key *}
{foreach from=$bookings key=index item=booking}
    <div class="booking-{$index}">
        {$booking.booking_reference|escape:'html':'UTF-8'}
    </div>
{/foreach}

{* Foreach with counter *}
{foreach from=$bookings item=booking name=bookingLoop}
    {if $smarty.foreach.bookingLoop.first}
        {* First item *}
    {/if}
    
    {* Item content *}
    
    {if $smarty.foreach.bookingLoop.last}
        {* Last item *}
    {/if}
{/foreach}
```

### Including Templates

```smarty
{* Include another template *}
{include file='./qlo_booking_summary.tpl'}

{* Include with variables *}
{include file='./qlo_booking_item.tpl' booking=$booking}
```

### Price Formatting

```smarty
{* Format price with currency *}
{displayPrice price=$booking.total_price}

{* Format price without currency *}
{$booking.total_price|string_format:"%.2f"}
```

### Links

```smarty
{* Module link *}
<a href="{$link->getModuleLink('qloyourmodule', 'display')|escape:'html':'UTF-8'}">
    {l s='View Bookings' mod='qloyourmodule'}
</a>

{* Module link with parameters *}
<a href="{$link->getModuleLink('qloyourmodule', 'display', ['id_booking' => $booking.id_booking])|escape:'html':'UTF-8'}">
    {l s='View Details' mod='qloyourmodule'}
</a>

{* Page link *}
<a href="{$link->getPageLink('contact')|escape:'html':'UTF-8'}">
    {l s='Contact Us' mod='qloyourmodule'}
</a>
```

---

## 🎨 CSS Files

### File Organization

```
views/css/
├── admin/
│   ├── qlo_module_admin.css
│   └── qlo_settings_page.css
├── front/
│   ├── qlo_module_front.css
│   └── qlo_booking_form.css
└── global/
    └── qlo_module_global.css
```

### CSS File Example

**File**: `views/css/front/qlo_module_front.css`

```css
/**
 * [LICENSE HEADER]
 */

/* Module container */
.qlo-qym-booking-display {
    padding: 20px;
    background: #f5f5f5;
    border-radius: 4px;
}

.qlo-qym-booking-display h2 {
    margin-bottom: 15px;
    font-size: 24px;
    color: #333;
}

/* Booking list */
.qlo-qym-booking-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.qlo-qym-booking-item {
    padding: 15px;
    background: #fff;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.qlo-qym-booking-item:hover {
    border-color: #999;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Booking details */
.qlo-qym-booking-ref {
    font-weight: bold;
    color: #333;
}

.qlo-qym-booking-price {
    float: right;
    color: #27ae60;
    font-size: 18px;
}

.qlo-qym-booking-status {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 3px;
    font-size: 12px;
}

.qlo-qym-booking-status.confirmed {
    background: #27ae60;
    color: #fff;
}

.qlo-qym-booking-status.pending {
    background: #f39c12;
    color: #fff;
}

.qlo-qym-booking-status.cancelled {
    background: #e74c3c;
    color: #fff;
}

/* Responsive */
@media (max-width: 768px) {
    .qlo-qym-booking-display {
        padding: 10px;
    }
    
    .qlo-qym-booking-price {
        float: none;
        display: block;
        margin-top: 10px;
    }
}
```

### Loading CSS in Hooks

```php
public function hookDisplayHeader()
{
    // Conditional loading
    if ($this->context->controller->php_self == 'module-qloyourmodule-display') {
        $this->context->controller->addCSS($this->_path.'views/css/front/qlo_module_front.css');
    }
}

public function hookActionFrontControllerSetMedia()
{
    // Global front CSS
    $this->context->controller->addCSS($this->_path.'views/css/global/qlo_module_global.css');
}

public function hookDisplayBackOfficeHeader()
{
    // Admin CSS
    $this->context->controller->addCSS($this->_path.'views/css/admin/qlo_module_admin.css');
}
```

---

## 💻 JavaScript Files

### File Organization

```
views/js/
├── admin/
│   ├── qlo_module_admin.js
│   └── qlo_settings.js
├── front/
│   ├── qlo_module_front.js
│   └── qlo_booking_form.js
└── global/
    └── qlo_module_global.js
```

### JavaScript File Example

**File**: `views/js/front/qlo_module_front.js`

```javascript
/**
 * [LICENSE HEADER]
 */

$(document).ready(function() {
    // Cancel booking
    $(document).on('click', '.qlo-qym-cancel-booking', function(e) {
        e.preventDefault();
        
        if (!confirm(qym_i18n.confirm_cancel)) {
            return false;
        }
        
        var idBooking = $(this).data('id-booking');
        
        $.ajax({
            url: qym_ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'cancelBooking',
                id_booking: idBooking,
                token: qym_token
            },
            success: function(response) {
                if (response.success) {
                    alert(response.msg);
                    location.reload();
                } else {
                    alert(response.errors.join('\n'));
                }
            },
            error: function() {
                alert(qym_i18n.ajax_error);
            }
        });
    });
    
    // Load booking details
    $('.qlo-qym-booking-item').on('click', function() {
        var idBooking = $(this).data('id-booking');
        loadBookingDetails(idBooking);
    });
});

function loadBookingDetails(idBooking) {
    $.ajax({
        url: qym_ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'getBookingDetails',
            id_booking: idBooking,
            token: qym_token
        },
        success: function(response) {
            if (response.success) {
                displayBookingDetails(response.data);
            } else {
                alert(response.errors.join('\n'));
            }
        }
    });
}

function displayBookingDetails(booking) {
    var html = '<div class="booking-details">';
    html += '<h3>' + booking.reference + '</h3>';
    html += '<p>Total: ' + booking.total + '</p>';
    html += '</div>';
    
    $('#booking-details-container').html(html);
}
```

### Assigning JS Variables from PHP

**In controller**:

```php
public function setMedia()
{
    parent::setMedia();
    
    // Add JS file
    $this->addJS($this->module->getPathUri().'views/js/qlo_module_front.js');
    
    // Assign JS variables
    Media::addJsDef(array(
        'qym_ajax_url' => $this->context->link->getModuleLink('qloyourmodule', 'ajax'),
        'qym_token' => Tools::getToken(false),
        'qym_customer_id' => $this->context->customer->id,
        'qym_currency_sign' => $this->context->currency->sign,
        'qym_i18n' => array(
            'confirm_cancel' => $this->l('Are you sure you want to cancel this booking?'),
            'ajax_error' => $this->l('An error occurred. Please try again.'),
            'loading' => $this->l('Loading...'),
        )
    ));
}
```

**In JavaScript**:

```javascript
// Use assigned variables
console.log(qym_ajax_url);
console.log(qym_token);
console.log(qym_i18n.confirm_cancel);
```

---

## ✅ Template Best Practices

### 1. Always Escape Variables

```smarty
✅ Correct:
{$variable|escape:'html':'UTF-8'}

❌ Wrong:
{$variable}  {* Unsafe! *}
```

### 2. Check Variables Exist

```smarty
✅ Correct:
{if isset($variable) && $variable}
    {$variable|escape:'html':'UTF-8'}
{/if}

❌ Wrong:
{$variable|escape:'html':'UTF-8'}  {* Error if not set *}
```

### 3. Use Module Translation

```smarty
✅ Correct:
{l s='Text' mod='qloyourmodule'}

❌ Wrong:
Text  {* Not translatable *}
```

### 4. Conditional CSS/JS Loading

```php
✅ Correct:
if ($this->context->controller->php_self == 'module-qloyourmodule-display') {
    $this->context->controller->addCSS(...);
}

❌ Wrong:
// Always load on all pages
$this->context->controller->addCSS(...);
```

### 5. Standard AJAX Response Handling

```javascript
✅ Correct:
success: function(response) {
    if (response.success) {
        // Handle success
        alert(response.msg);
    } else {
        // Handle errors
        alert(response.errors.join('\n'));
    }
}

❌ Wrong:
success: function(response) {
    alert(response);  // Inconsistent
}
```

---

## 🎯 Common Template Patterns

### Form with CSRF Protection

```smarty
<form action="{$link->getModuleLink('qloyourmodule', 'process')|escape:'html':'UTF-8'}" method="post">
    <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}">
    
    <div class="form-group">
        <label>{l s='Name' mod='qloyourmodule'}</label>
        <input type="text" name="name" value="{$name|escape:'html':'UTF-8'}" required>
    </div>
    
    <button type="submit" class="btn btn-primary">
        {l s='Submit' mod='qloyourmodule'}
    </button>
</form>
```

### Pagination

```smarty
{if $total_pages > 1}
    <ul class="pagination">
        {for $page=1 to $total_pages}
            <li class="{if $page == $current_page}active{/if}">
                <a href="{$link->getModuleLink('qloyourmodule', 'display', ['page' => $page])|escape:'html':'UTF-8'}">
                    {$page}
                </a>
            </li>
        {/for}
    </ul>
{/if}
```

### Modal/Popup

```smarty
<div id="booking-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>{l s='Booking Details' mod='qloyourmodule'}</h2>
        <div id="modal-body">
            {* Dynamic content loaded via AJAX *}
        </div>
    </div>
</div>
```

---

## 🔗 Related Documentation

- [coding-conventions.md](./coding-conventions.md) - File naming rules
- [controllers.md](./controllers.md) - Assigning template variables
- [security-validation.md](./security-validation.md) - Escaping and tokens

---

**Next**: Review [database-operations.md](./database-operations.md) for SQL patterns
