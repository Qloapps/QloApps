# Controllers - Admin & Front

> **Creating CRUD interfaces and customer-facing pages with proper permissions**

## 🎮 Controller Types

QloApps has different controller types for different purposes:

| Type | Purpose | Base Class | Location |
|------|---------|------------|----------|
| **Admin Controller** | Back-office CRUD | `ModuleAdminController` | `controllers/admin/` |
| **Front Controller** | Customer pages | `ModuleFrontController` | `controllers/front/` |
| **Module Controller** | Module-specific logic | Extends above | Module controllers folder |

---

## 🔧 Admin Controllers

**Purpose**: Create, Read, Update, Delete (CRUD) interfaces in back-office

**Reference**: `modules/hotelreservationsystem/controllers/admin/`

### Basic Admin Controller Structure

```php
<?php
/**
 * [LICENSE HEADER]
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminQymBookingsController extends ModuleAdminController
{
    public function __construct()
    {
        // Model setup
        $this->bootstrap = true;
        $this->table = 'qym_booking';                    // Without prefix
        $this->className = 'QymBooking';                 // ObjectModel class
        $this->lang = false;                             // true if multilang
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        
        parent::__construct();
        
        // List configuration
        $this->fields_list = array(
            'id_booking' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'booking_reference' => array(
                'title' => $this->l('Reference'),
                'filter_key' => 'a!booking_reference'
            ),
            'customer_name' => array(
                'title' => $this->l('Customer'),
                'filter' => false,
                'search' => false
            ),
            'total_price' => array(
                'title' => $this->l('Total'),
                'type' => 'price',
                'currency' => true,
                'align' => 'right'
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'list' => array(
                    QYM_STATUS_PENDING => $this->l('Pending'),
                    QYM_STATUS_CONFIRMED => $this->l('Confirmed'),
                    QYM_STATUS_CANCELLED => $this->l('Cancelled')
                ),
                'filter_key' => 'a!status',
                'align' => 'center'
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'type' => 'datetime',
                'align' => 'right'
            ),
        );
        
        // Enable filters, sorting, pagination
        $this->_select = 'CONCAT(c.firstname, " ", c.lastname) as customer_name';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'customer` c ON (a.id_customer = c.id_customer)';
        $this->_where = 'AND a.status != '.QYM_STATUS_DELETED;
        $this->_orderBy = 'id_booking';
        $this->_orderWay = 'DESC';
    }
    
    /**
     * Render list page
     */
    public function renderList()
    {
        // Check permissions
        if (!$this->tabAccess['view']) {
            $this->errors[] = $this->l('You do not have permission to view this.');
            return;
        }
        
        // Add custom toolbar buttons
        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new booking')
        );
        
        return parent::renderList();
    }
    
    /**
     * Render form page (add/edit)
     */
    public function renderForm()
    {
        // Check permissions
        if (!$this->tabAccess['edit']) {
            $this->errors[] = $this->l('You do not have permission to edit this.');
            return;
        }
        
        // Form fields
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Booking Details'),
                'icon' => 'icon-calendar'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Reference'),
                    'name' => 'booking_reference',
                    'required' => true,
                    'col' => 4
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Customer'),
                    'name' => 'id_customer',
                    'required' => true,
                    'options' => array(
                        'query' => Customer::getCustomers(),
                        'id' => 'id_customer',
                        'name' => 'firstname'
                    ),
                    'col' => 4
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Total Price'),
                    'name' => 'total_price',
                    'required' => true,
                    'suffix' => $this->context->currency->sign,
                    'col' => 2
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Status'),
                    'name' => 'status',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array('id' => QYM_STATUS_PENDING, 'name' => $this->l('Pending')),
                            array('id' => QYM_STATUS_CONFIRMED, 'name' => $this->l('Confirmed')),
                            array('id' => QYM_STATUS_CANCELLED, 'name' => $this->l('Cancelled'))
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'col' => 3
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );
        
        return parent::renderForm();
    }
    
    /**
     * Process delete action
     */
    public function processDelete()
    {
        // Check permissions
        if (!$this->tabAccess['delete']) {
            $this->errors[] = $this->l('You do not have permission to delete this.');
            return false;
        }
        
        return parent::processDelete();
    }
}
```

### Hotel Permission Checks

**For hotel-specific data**:

```php
public function __construct()
{
    // ... setup ...
    
    parent::__construct();
    
    // Apply hotel restrictions for employees
    HotelBranchInformation::addHotelRestriction(false, $this->table, 'hbi');
}

public function renderList()
{
    // Check hotel access
    $idHotel = Tools::getValue('id_hotel');
    if ($idHotel && !HotelBranchInformation::isHotelAccessible($idHotel)) {
        $this->errors[] = $this->l('You do not have access to this hotel.');
        return;
    }
    
    return parent::renderList();
}
```

---

## 🌐 Front Controllers

**Purpose**: Customer-facing pages (booking forms, account pages, etc.)

**Reference**: `modules/hotelreservationsystem/controllers/front/`

### Basic Front Controller

```php
<?php
/**
 * [LICENSE HEADER]
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QloYourModuleDisplayModuleFrontController extends ModuleFrontController
{
    public $ssl = true;  // Force HTTPS
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Initialize page
     */
    public function init()
    {
        parent::init();
        
        // Check if customer is logged in (if required)
        if (!$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('qloyourmodule', 'display')));
        }
    }
    
    /**
     * Set page template and assign variables
     */
    public function initContent()
    {
        parent::initContent();
        
        // Get data
        $idCustomer = $this->context->customer->id;
        $bookings = QymBooking::getCustomerBookings($idCustomer);
        
        // Assign to Smarty
        $this->context->smarty->assign(array(
            'bookings' => $bookings,
            'customer' => $this->context->customer,
            'module_dir' => $this->module->getPathUri()
        ));
        
        // Set template
        $this->setTemplate('module:qloyourmodule/views/templates/front/display.tpl');
    }
    
    /**
     * Set page meta (title, description)
     */
    public function setMedia()
    {
        parent::setMedia();
        
        // Add module CSS/JS
        $this->addCSS($this->module->getPathUri().'views/css/qlo_module_front.css');
        $this->addJS($this->module->getPathUri().'views/js/qlo_module_front.js');
        
        // Add JS variables
        Media::addJsDef(array(
            'qym_ajax_url' => $this->context->link->getModuleLink('qloyourmodule', 'ajax'),
            'qym_customer_id' => $this->context->customer->id
        ));
    }
    
    /**
     * Set page title and breadcrumb
     */
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        
        $breadcrumb['links'][] = array(
            'title' => $this->l('My Bookings'),
            'url' => $this->context->link->getModuleLink('qloyourmodule', 'display')
        );
        
        return $breadcrumb;
    }
}
```

### AJAX Controller

**For AJAX requests**:

```php
<?php
/**
 * [LICENSE HEADER]
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QloYourModuleAjaxModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function initContent()
    {
        // This is AJAX, no template needed
    }
    
    public function displayAjax()
    {
        $action = Tools::getValue('action');
        
        // Validate token (CRITICAL for security)
        if (!$this->isTokenValid()) {
            die(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Invalid token'))
            )));
        }
        
        switch ($action) {
            case 'getBookingDetails':
                $this->ajaxGetBookingDetails();
                break;
            
            case 'cancelBooking':
                $this->ajaxCancelBooking();
                break;
            
            default:
                die(json_encode(array(
                    'success' => false,
                    'errors' => array($this->l('Invalid action'))
                )));
        }
    }
    
    protected function ajaxGetBookingDetails()
    {
        $idBooking = (int)Tools::getValue('id_booking');
        $booking = new QymBooking($idBooking);
        
        if (!Validate::isLoadedObject($booking)) {
            die(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Booking not found'))
            )));
        }
        
        // Check ownership
        if ($booking->id_customer != $this->context->customer->id) {
            die(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Access denied'))
            )));
        }
        
        die(json_encode(array(
            'success' => true,
            'data' => array(
                'id_booking' => $booking->id,
                'reference' => $booking->booking_reference,
                'total' => $booking->total_price,
                'status' => $booking->status
            )
        )));
    }
    
    protected function ajaxCancelBooking()
    {
        // Validate before processing
        $idBooking = (int)Tools::getValue('id_booking');
        $booking = new QymBooking($idBooking);
        
        if (!Validate::isLoadedObject($booking)) {
            die(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Booking not found'))
            )));
        }
        
        // Check permissions
        if ($booking->id_customer != $this->context->customer->id) {
            die(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Access denied'))
            )));
        }
        
        // Business logic
        if (!$booking->canBeCancelled()) {
            die(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Booking cannot be cancelled'))
            )));
        }
        
        // Process cancellation
        if ($booking->updateStatus(QYM_STATUS_CANCELLED)) {
            die(json_encode(array(
                'success' => true,
                'msg' => $this->l('Booking cancelled successfully')
            )));
        } else {
            die(json_encode(array(
                'success' => false,
                'errors' => array($this->l('Error cancelling booking'))
            )));
        }
    }
    
    /**
     * Validate CSRF token
     */
    protected function isTokenValid()
    {
        $token = Tools::getValue('token');
        $expectedToken = Tools::getToken(false);  // Get token for front
        
        return $token === $expectedToken;
    }
}
```

---

## 🔐 Permission Checks

### Admin Controllers

```php
// In controller methods
if (!$this->tabAccess['view']) {
    $this->errors[] = $this->l('No view permission');
    return;
}

if (!$this->tabAccess['edit']) {
    $this->errors[] = $this->l('No edit permission');
    return false;
}

if (!$this->tabAccess['delete']) {
    $this->errors[] = $this->l('No delete permission');
    return false;
}

if (!$this->tabAccess['add']) {
    $this->errors[] = $this->l('No add permission');
    return;
}
```

### Front Controllers

```php
// Check if customer is logged in
if (!$this->context->customer->isLogged()) {
    Tools::redirect('index.php?controller=authentication&back='.urlencode($this->getCurrentURL()));
}

// Check ownership
if ($object->id_customer != $this->context->customer->id) {
    $this->errors[] = $this->l('Access denied');
    return;
}
```

---

## 📋 Form Handling

### Processing Form Submission

```php
public function postProcess()
{
    // Check if form submitted
    if (Tools::isSubmit('submitAddqym_booking')) {
        // Validate first
        $errors = $this->validateForm();
        
        if (count($errors)) {
            $this->errors = array_merge($this->errors, $errors);
            return;
        }
        
        // Then save
        $idBooking = (int)Tools::getValue('id_booking');
        
        if ($idBooking) {
            $booking = new QymBooking($idBooking);
        } else {
            $booking = new QymBooking();
        }
        
        $booking->booking_reference = pSQL(Tools::getValue('booking_reference'));
        $booking->id_customer = (int)Tools::getValue('id_customer');
        $booking->total_price = (float)Tools::getValue('total_price');
        $booking->status = (int)Tools::getValue('status');
        
        if ($booking->save()) {
            $this->confirmations[] = $this->l('Booking saved successfully');
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
        } else {
            $this->errors[] = $this->l('Error saving booking');
        }
    }
    
    return parent::postProcess();
}

protected function validateForm()
{
    $errors = array();
    
    if (!Tools::getValue('booking_reference')) {
        $errors[] = $this->l('Reference is required');
    }
    
    if (!(int)Tools::getValue('id_customer')) {
        $errors[] = $this->l('Customer is required');
    }
    
    $price = (float)Tools::getValue('total_price');
    if ($price <= 0) {
        $errors[] = $this->l('Price must be greater than 0');
    }
    
    return $errors;
}
```

---

## ✅ Controller Best Practices

### 1. Always Validate Before Save

```php
✅ Correct:
$errors = $this->validateForm();
if (count($errors)) {
    // Show errors
    return;
}
// Then save

❌ Wrong:
// Save directly without validation
```

### 2. Type Cast All Inputs

```php
✅ Correct:
$idBooking = (int)Tools::getValue('id_booking');
$reference = pSQL(Tools::getValue('reference'));

❌ Wrong:
$idBooking = Tools::getValue('id_booking');  // No type cast!
```

### 3. Check Permissions First

```php
✅ Correct:
public function renderList()
{
    if (!$this->tabAccess['view']) {
        return;
    }
    // Render logic
}

❌ Wrong:
// No permission check
```

### 4. Use Standard JSON Response

```php
✅ Correct:
die(json_encode(array(
    'success' => true,
    'errors' => array(),
    'msg' => 'Success message',
    'data' => array(...)
)));

❌ Wrong:
echo 'Success';  // Not JSON
```

---

## 🔗 Related Documentation

- [security-validation.md](./security-validation.md) - Input validation, CSRF tokens
- [templates-views.md](./templates-views.md) - Templates for controllers
- [models-repositories.md](./models-repositories.md) - ObjectModel usage

---

**Next**: Review [templates-views.md](./templates-views.md) for Smarty templates
