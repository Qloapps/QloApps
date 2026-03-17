# Security & Input Validation

> **Input validation, CSRF protection, XSS prevention, and SQL injection defense**

## 🔒 Security-First Mindset

**Core Principle**: **NEVER TRUST USER INPUT**

1. **Validate before save** - Check types, formats, ranges
2. **Escape on output** - Prevent XSS in templates
3. **Protect forms with tokens** - CSRF prevention
4. **Cast database inputs** - SQL injection prevention
5. **Check permissions** - Authorization on every action

---

## ✅ Input Validation

### Validation Before Save

**Pattern from hotelreservationsystem**:

```php
// In controller processAdd()/processUpdate()
protected function processAdd()
{
    // 1. Validate FIRST
    if (!$this->validateFields()) {
        return false;
    }
    
    // 2. Then save
    return parent::processAdd();
}

protected function validateFields()
{
    $errors = array();
    
    // Required fields
    if (!($name = Tools::getValue('name'))) {
        $errors[] = $this->l('Name is required');
    }
    
    // Format validation
    if (!Validate::isGenericName($name)) {
        $errors[] = $this->l('Invalid name format');
    }
    
    // Email validation
    if ($email = Tools::getValue('email')) {
        if (!Validate::isEmail($email)) {
            $errors[] = $this->l('Invalid email address');
        }
    }
    
    // Numeric validation
    if (!($price = Tools::getValue('price')) || !Validate::isPrice($price)) {
        $errors[] = $this->l('Invalid price');
    }
    
    // Range validation
    if ($quantity = Tools::getValue('quantity')) {
        if (!Validate::isInt($quantity) || $quantity < 0 || $quantity > 1000) {
            $errors[] = $this->l('Quantity must be between 0 and 1000');
        }
    }
    
    // Date validation
    if ($date = Tools::getValue('date')) {
        if (!Validate::isDate($date)) {
            $errors[] = $this->l('Invalid date format');
        }
    }
    
    // Object exists validation
    if ($id_customer = Tools::getValue('id_customer')) {
        $customer = new Customer((int)$id_customer);
        if (!Validate::isLoadedObject($customer)) {
            $errors[] = $this->l('Customer not found');
        }
    }
    
    if (count($errors)) {
        $this->errors = array_merge($this->errors, $errors);
        return false;
    }
    
    return true;
}
```

---

## 🛡️ Core Validation Methods

### Validate Class Methods

**Reference**: `classes/Validate.php`

```php
// String validation
Validate::isGenericName($value)      // Letters, numbers, spaces, basic punctuation
Validate::isName($value)             // Person names (letters, hyphens, spaces)
Validate::isAddress($value)          // Street addresses
Validate::isMessage($value)          // Message content
Validate::isCleanHtml($value)        // HTML content (strips dangerous tags)

// Numeric validation
Validate::isInt($value)              // Integer
Validate::isUnsignedInt($value)      // Positive integer
Validate::isFloat($value)            // Float/decimal
Validate::isPrice($value)            // Price format
Validate::isPercentage($value)       // 0-100

// Format validation
Validate::isEmail($value)            // Email address
Validate::isPhoneNumber($value)      // Phone number
Validate::isUrl($value)              // URL
Validate::isDate($value)             // YYYY-MM-DD
Validate::isDateFormat($value)       // YYYY-MM-DD HH:MM:SS

// Object validation
Validate::isLoadedObject($object)    // Object loaded from DB
Validate::isUnsignedId($value)       // Valid database ID

// Module validation
Validate::isModuleName($value)       // Valid module name
Validate::isFileName($value)         // Valid filename

// Boolean
Validate::isBool($value)             // Boolean value
```

### Custom Validation

```php
class QymBooking extends ObjectModel
{
    public $booking_reference;
    public $check_in_date;
    public $check_out_date;
    
    public function validateFields($die = true, $error_return = false)
    {
        // Standard validation
        if (!parent::validateFields($die, $error_return)) {
            return false;
        }
        
        // Custom: Check-out after check-in
        if (strtotime($this->check_out_date) <= strtotime($this->check_in_date)) {
            $this->validateFieldsLang($die, $error_return);
            if ($die) {
                die(Tools::displayError('Check-out date must be after check-in date'));
            }
            return false;
        }
        
        // Custom: Booking reference format
        if (!preg_match('/^QYM-[0-9]{6}$/', $this->booking_reference)) {
            if ($die) {
                die(Tools::displayError('Invalid booking reference format'));
            }
            return false;
        }
        
        return true;
    }
}
```

---

## 🔑 CSRF Protection

### Admin Form Tokens

**Pattern**: Every admin form MUST have token

```php
// In AdminController
protected function initForm()
{
    $this->fields_form = array(
        'form' => array(
            'legend' => array(
                'title' => $this->l('Booking Information'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Reference'),
                    'name' => 'booking_reference',
                    'required' => true,
                ),
                // ... more fields
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        ),
    );
}

// Token automatically included by renderForm()
// Automatically validated by QloApps in processAdd()/processUpdate()
```

### Front Controller Form Tokens

```php
// In ModuleFrontController
public function initContent()
{
    parent::initContent();
    
    // Generate token
    $token = Tools::getToken(false);
    
    $this->context->smarty->assign(array(
        'token' => $token,
        'action_url' => $this->context->link->getModuleLink('qloyourmodule', 'process'),
    ));
    
    $this->setTemplate('module:qloyourmodule/views/templates/front/booking_form.tpl');
}

// In processing controller
public function postProcess()
{
    // Validate token
    if (!Tools::getToken(false) || Tools::getValue('token') != Tools::getToken(false)) {
        $this->errors[] = $this->l('Invalid token. Please try again.');
        return;
    }
    
    // Process form...
}
```

**Template**:

```smarty
<form action="{$action_url|escape:'html':'UTF-8'}" method="post">
    <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}">
    
    {* Form fields *}
    
    <button type="submit">{l s='Submit' mod='qloyourmodule'}</button>
</form>
```

---

## 🎭 AJAX Security

### AJAX Token Pattern

**Pattern from hotelreservationsystem**:

```php
// In AJAX controller
class QloYourModuleAjaxModuleFrontController extends ModuleFrontController
{
    public function displayAjax()
    {
        // 1. Validate token FIRST
        if (!$this->isTokenValid()) {
            $this->ajaxRender(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Invalid token')),
            )));
            return;
        }
        
        // 2. Get action
        $action = Tools::getValue('action');
        
        // 3. Validate action exists
        if (!$action || !method_exists($this, 'process'.ucfirst($action))) {
            $this->ajaxRender(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Invalid action')),
            )));
            return;
        }
        
        // 4. Execute action
        $this->{'process'.ucfirst($action)}();
    }
    
    protected function isTokenValid()
    {
        $token = Tools::getValue('token');
        
        // Front office: validate against static token
        if (Tools::getToken(false) != $token) {
            return false;
        }
        
        return true;
    }
    
    protected function processCancelBooking()
    {
        // 5. Validate inputs
        $id_booking = (int)Tools::getValue('id_booking');
        if (!$id_booking) {
            $this->ajaxRender(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Invalid booking ID')),
            )));
            return;
        }
        
        // 6. Validate object
        $booking = new QymBooking($id_booking);
        if (!Validate::isLoadedObject($booking)) {
            $this->ajaxRender(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Booking not found')),
            )));
            return;
        }
        
        // 7. Validate ownership
        if ($booking->id_customer != $this->context->customer->id) {
            $this->ajaxRender(json_encode(array(
                'success' => false,
                'errors' => array($this->l('You do not have permission to cancel this booking')),
            )));
            return;
        }
        
        // 8. Process cancellation
        $booking->status = 0;
        if ($booking->save()) {
            $this->ajaxRender(json_encode(array(
                'success' => true,
                'msg' => $this->l('Booking cancelled successfully'),
            )));
        } else {
            $this->ajaxRender(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Failed to cancel booking')),
            )));
        }
    }
}
```

### Admin AJAX Token

```php
// In AdminController AJAX
public function ajaxProcess()
{
    // Validate admin token
    if (!Tools::getAdminTokenLite('AdminModuleName')) {
        die(json_encode(array(
            'success' => false,
            'errors' => array($this->l('Invalid token')),
        )));
    }
    
    // Process request...
}
```

**JavaScript**:

```javascript
$.ajax({
    url: admin_ajax_url,
    type: 'POST',
    data: {
        ajax: true,
        action: 'processAction',
        token: admin_token  // From Media::addJsDef()
    },
    success: function(response) {
        // Handle response
    }
});
```

---

## 🚫 XSS Prevention

### Template Escaping

```smarty
✅ Always escape user input:
{$user_input|escape:'html':'UTF-8'}

✅ Escape in attributes:
<input type="text" value="{$value|escape:'html':'UTF-8'}">

✅ Escape URLs:
<a href="{$url|escape:'html':'UTF-8'}">Link</a>

✅ Escape JavaScript:
<script>
    var userInput = "{$input|escape:'javascript':'UTF-8'}";
</script>

❌ Never output raw user input:
{$user_input}  // Dangerous!
```

### PHP Escaping

```php
// HTML output
echo Tools::safeOutput($user_input);

// Display error with escaping
$this->errors[] = Tools::displayError($user_message);

// Display confirmation with escaping
$this->confirmations[] = $this->l('Saved: ').Tools::safeOutput($name);
```

---

## 💉 SQL Injection Prevention

### Input Escaping (Review)

```php
✅ Type casting for numbers:
WHERE `id_booking` = '.(int)$id_booking.'
WHERE `price` = '.(float)$price.'

✅ pSQL() for strings:
WHERE `name` = "'.pSQL($name).'"
WHERE `email` = "'.pSQL($email).'"

✅ bqSQL() for identifiers:
ORDER BY `'.bqSQL($column).'`

❌ Never use raw input:
WHERE `id` = '.$_GET['id'].'  // SQL injection!
```

### ObjectModel Automatic Protection

```php
✅ Preferred - ObjectModel handles escaping:
$booking = new QymBooking();
$booking->booking_reference = Tools::getValue('booking_reference');  // Auto-escaped
$booking->total_price = Tools::getValue('total_price');  // Auto-validated
$booking->save();  // Safe
```

---

## 👮 Permission Checks

### Admin Permission Patterns

```php
// In AdminController
public function initContent()
{
    // Check view permission
    if (!$this->tabAccess['view']) {
        $this->errors[] = $this->l('You do not have permission to view this.');
        return;
    }
    
    parent::initContent();
}

protected function postProcess()
{
    // Check edit permission
    if (!$this->tabAccess['edit']) {
        $this->errors[] = $this->l('You do not have permission to edit this.');
        return;
    }
    
    parent::postProcess();
}

public function processDelete()
{
    // Check delete permission
    if (!$this->tabAccess['delete']) {
        $this->errors[] = $this->l('You do not have permission to delete this.');
        return false;
    }
    
    return parent::processDelete();
}
```

### Hotel Permission Checks

```php
// In controller/model
protected function hasHotelAccess($id_hotel)
{
    $employee = $this->context->employee;
    
    // Super admin has full access
    if ($employee->isSuperAdmin()) {
        return true;
    }
    
    // Check profile hotel access
    $hotels = HotelBranchInformation::getProfileAccessedHotels(
        $employee->id_profile,
        1  // Active only
    );
    
    $hotel_ids = array_column($hotels, 'id_hotel');
    
    return in_array($id_hotel, $hotel_ids);
}

// Use in action
public function postProcess()
{
    $id_hotel = (int)Tools::getValue('id_hotel');
    
    if (!$this->hasHotelAccess($id_hotel)) {
        $this->errors[] = $this->l('You do not have access to this hotel.');
        return;
    }
    
    // Process...
}
```

### Customer Ownership Checks

```php
// In front controller
protected function isCustomerBooking($id_booking)
{
    $booking = new QymBooking($id_booking);
    
    if (!Validate::isLoadedObject($booking)) {
        return false;
    }
    
    return ($booking->id_customer == $this->context->customer->id);
}

// Use in action
public function postProcess()
{
    $id_booking = (int)Tools::getValue('id_booking');
    
    if (!$this->isCustomerBooking($id_booking)) {
        $this->errors[] = $this->l('You do not have permission to modify this booking.');
        return;
    }
    
    // Process...
}
```

---

## 📋 Validation Checklist

### Form Submission Checklist

```php
protected function processForm()
{
    // ✅ 1. Check logged in (if required)
    if (!$this->context->customer->isLogged()) {
        $this->errors[] = $this->l('You must be logged in');
        return;
    }
    
    // ✅ 2. Validate token (CSRF protection)
    if (Tools::getValue('token') != Tools::getToken(false)) {
        $this->errors[] = $this->l('Invalid token');
        return;
    }
    
    // ✅ 3. Validate required fields
    if (!Tools::getValue('name')) {
        $this->errors[] = $this->l('Name is required');
        return;
    }
    
    // ✅ 4. Validate format
    $email = Tools::getValue('email');
    if (!Validate::isEmail($email)) {
        $this->errors[] = $this->l('Invalid email');
        return;
    }
    
    // ✅ 5. Validate object exists (if editing)
    if ($id = Tools::getValue('id_booking')) {
        $booking = new QymBooking((int)$id);
        if (!Validate::isLoadedObject($booking)) {
            $this->errors[] = $this->l('Booking not found');
            return;
        }
    }
    
    // ✅ 6. Check ownership/permissions
    if (isset($booking) && $booking->id_customer != $this->context->customer->id) {
        $this->errors[] = $this->l('Access denied');
        return;
    }
    
    // ✅ 7. Business logic validation
    $check_in = Tools::getValue('check_in');
    $check_out = Tools::getValue('check_out');
    if (strtotime($check_out) <= strtotime($check_in)) {
        $this->errors[] = $this->l('Check-out must be after check-in');
        return;
    }
    
    // ✅ 8. Save with error handling
    try {
        if (!isset($booking)) {
            $booking = new QymBooking();
        }
        
        $booking->booking_reference = Tools::getValue('booking_reference');
        $booking->total_price = (float)Tools::getValue('total_price');
        // ... set fields
        
        if (!$booking->save()) {
            $this->errors[] = $this->l('Failed to save booking');
            return;
        }
        
        $this->confirmations[] = $this->l('Booking saved successfully');
        
    } catch (Exception $e) {
        $this->errors[] = $e->getMessage();
    }
}
```

---

## 🎯 Common Security Patterns

### File Upload Validation

```php
protected function processFileUpload()
{
    // 1. Check file uploaded
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $this->errors[] = $this->l('No file uploaded');
        return false;
    }
    
    $file = $_FILES['file'];
    
    // 2. Validate size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        $this->errors[] = $this->l('File too large (max 5MB)');
        return false;
    }
    
    // 3. Validate extension
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'pdf');
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowed_extensions)) {
        $this->errors[] = $this->l('Invalid file type');
        return false;
    }
    
    // 4. Validate filename
    if (!Validate::isFileName($file['name'])) {
        $this->errors[] = $this->l('Invalid filename');
        return false;
    }
    
    // 5. Generate safe filename
    $safe_filename = md5(uniqid()).'.'.$extension;
    $upload_path = _PS_MODULE_DIR_.'qloyourmodule/uploads/'.$safe_filename;
    
    // 6. Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        $this->errors[] = $this->l('Failed to upload file');
        return false;
    }
    
    return $safe_filename;
}
```

### Rate Limiting

```php
protected function checkRateLimit()
{
    $ip = Tools::getRemoteAddr();
    $cache_key = 'qym_rate_limit_'.$ip;
    
    // Get current count
    $count = (int)Cache::retrieve($cache_key);
    
    // Check limit (10 requests per minute)
    if ($count >= 10) {
        $this->errors[] = $this->l('Too many requests. Please try again later.');
        return false;
    }
    
    // Increment counter
    Cache::store($cache_key, $count + 1, 60);  // 60 seconds TTL
    
    return true;
}
```

---

## 🔗 Related Documentation

- [database-operations.md](./database-operations.md) - SQL escaping patterns
- [controllers.md](./controllers.md) - Permission checks in controllers
- [templates-views.md](./templates-views.md) - Template escaping

---

**Next**: Review [deployment.md](./deployment.md) for deployment preparation
