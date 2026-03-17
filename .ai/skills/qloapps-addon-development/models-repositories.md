# Models & Repositories - ObjectModel Pattern

> **Database-backed classes using QloApps ObjectModel architecture**

## 🎯 ObjectModel Overview

**ObjectModel** is the base class for all database entities in QloApps. It provides automatic CRUD operations, validation, multilanguage support, and more.

**Reference**: `classes/ObjectModel.php` (core), `modules/hotelreservationsystem/classes/` (examples)

---

## 📋 Creating ObjectModel Classes

### Step 1: Create Class File

**Location**: `classes/QymModelName.php`

**Reference**: `modules/hotelreservationsystem/classes/HotelBookingDetail.php`

```php
<?php
/**
 * [LICENSE HEADER]
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QymBooking extends ObjectModel
{
    // Properties matching database columns
    public $id_booking;
    public $id_customer;
    public $booking_reference;
    public $total_price;
    public $status;
    public $date_add;
    public $date_upd;
    
    /**
     * Model definition
     */
    public static $definition = array(
        'table' => 'qym_booking',                    // Without _DB_PREFIX_
        'primary' => 'id_booking',                    // Primary key
        'multilang' => false,                         // Set true for multilang fields
        'fields' => array(
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true
            ),
            'booking_reference' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 50,
                'required' => true
            ),
            'total_price' => array(
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => true
            ),
            'status' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'required' => true
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
        ),
    );
    
    /**
     * Get booking by reference
     */
    public function getByReference($reference)
    {
        $sql = 'SELECT `id_booking`
                FROM `'._DB_PREFIX_.'qym_booking`
                WHERE `booking_reference` = "'.pSQL($reference).'"';
        
        $idBooking = Db::getInstance()->getValue($sql);
        
        if ($idBooking) {
            return new QymBooking($idBooking);
        }
        
        return false;
    }
}
```

---

## 🔧 Field Types & Validation

### Field Types

| Type | PHP Type | Database Type | Example |
|------|----------|---------------|---------|
| `self::TYPE_INT` | Integer | INT | IDs, counters |
| `self::TYPE_FLOAT` | Float | DECIMAL/FLOAT | Prices, percentages |
| `self::TYPE_STRING` | String | VARCHAR | Names, references |
| `self::TYPE_HTML` | String | TEXT | HTML content |
| `self::TYPE_BOOL` | Boolean | TINYINT(1) | Flags, switches |
| `self::TYPE_DATE` | Date string | DATETIME | Timestamps |

### Validation Methods

**Reference**: `classes/Validate.php`

| Validator | Purpose | Example |
|-----------|---------|---------|
| `isUnsignedId` | Positive integer ID | `id_customer` |
| `isUnsignedInt` | Positive integer | `quantity`, `status` |
| `isInt` | Any integer | `delta`, `offset` |
| `isFloat` | Decimal number | `price`, `discount` |
| `isPrice` | Valid price | `total_price` |
| `isGenericName` | Generic text | `name`, `reference` |
| `isCleanHtml` | Safe HTML | `description` |
| `isEmail` | Email address | `customer_email` |
| `isDate` | Valid date | `date_add`, `date_upd` |
| `isPhoneNumber` | Phone number | `phone` |

### Field Options

```php
'field_name' => array(
    'type' => self::TYPE_STRING,           // Required
    'validate' => 'isGenericName',         // Required
    'required' => true,                    // Optional (default: false)
    'size' => 255,                         // Optional for strings
    'default' => 'default_value',          // Optional
    'allow_null' => false,                 // Optional (default: false)
),
```

---

## 🌍 Multilanguage Support

For fields that need translation (names, descriptions):

```php
class QymCategory extends ObjectModel
{
    public $id_category;
    public $position;
    public $active;
    public $date_add;
    public $date_upd;
    
    // Multilang fields
    public $name;
    public $description;
    
    public static $definition = array(
        'table' => 'qym_category',
        'primary' => 'id_category',
        'multilang' => true,                    // Enable multilang
        'fields' => array(
            'position' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool'
            ),
            // Multilang fields
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,                 // Mark as multilang
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 128
            ),
            'description' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,                 // Mark as multilang
                'validate' => 'isCleanHtml'
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate'
            ),
        ),
    );
}
```

**Database Tables for Multilang**:
- Main table: `qlo_qym_category` (non-lang fields)
- Lang table: `qlo_qym_category_lang` (lang fields + id_lang)

---

## 💾 CRUD Operations

### Create (Insert)

```php
$booking = new QymBooking();
$booking->id_customer = 123;
$booking->booking_reference = 'BK-' . time();
$booking->total_price = 199.99;
$booking->status = QYM_STATUS_PENDING;

if ($booking->save()) {
    $idBooking = $booking->id;  // Auto-generated ID
    echo "Booking created: " . $idBooking;
} else {
    echo "Error creating booking";
}
```

### Read (Select)

```php
// By ID
$booking = new QymBooking(123);

if (Validate::isLoadedObject($booking)) {
    echo $booking->booking_reference;
    echo $booking->total_price;
}

// Check if exists
if (!Validate::isLoadedObject($booking)) {
    die('Booking not found');
}
```

### Update

```php
$booking = new QymBooking(123);

if (Validate::isLoadedObject($booking)) {
    $booking->status = QYM_STATUS_CONFIRMED;
    $booking->total_price = 249.99;
    
    if ($booking->update()) {
        echo "Booking updated";
    } else {
        echo "Error updating";
    }
}
```

### Delete

```php
$booking = new QymBooking(123);

if (Validate::isLoadedObject($booking)) {
    if ($booking->delete()) {
        echo "Booking deleted";
    } else {
        echo "Error deleting";
    }
}
```

---

## 🔍 Custom Methods

Add custom methods for business logic:

```php
class QymBooking extends ObjectModel
{
    // ... definition ...
    
    /**
     * Get all bookings for customer
     */
    public static function getCustomerBookings($idCustomer, $idLang = null)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        
        $sql = 'SELECT b.*
                FROM `'._DB_PREFIX_.'qym_booking` b
                WHERE b.`id_customer` = '.(int)$idCustomer.'
                ORDER BY b.`date_add` DESC';
        
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
    
    /**
     * Get total bookings count
     */
    public static function getTotalBookings($status = null)
    {
        $sql = 'SELECT COUNT(*)
                FROM `'._DB_PREFIX_.'qym_booking`';
        
        if ($status !== null) {
            $sql .= ' WHERE `status` = '.(int)$status;
        }
        
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
    
    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled()
    {
        if ($this->status == QYM_STATUS_CANCELLED) {
            return false;
        }
        
        // Check if within cancellation period
        $checkInDate = strtotime($this->check_in_date);
        $now = time();
        $hoursUntilCheckIn = ($checkInDate - $now) / 3600;
        
        return $hoursUntilCheckIn >= 24;  // 24 hours before check-in
    }
    
    /**
     * Update status
     */
    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        return $this->update();
    }
}
```

---

## 🗄️ Database Class Pattern

**Purpose**: Centralize table creation, deletion, and data management

**Reference**: `modules/hotelreservationsystem/classes/HotelReservationSystemDb.php`

```php
<?php
/**
 * [LICENSE HEADER]
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class QymModuleDb
{
    /**
     * Create all module tables
     */
    public function createTables()
    {
        $sql = array();
        
        // Bookings table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'qym_booking` (
            `id_booking` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_customer` INT(11) UNSIGNED NOT NULL,
            `booking_reference` VARCHAR(50) NOT NULL,
            `total_price` DECIMAL(20,6) NOT NULL,
            `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_booking`),
            KEY `id_customer` (`id_customer`),
            KEY `booking_reference` (`booking_reference`),
            KEY `status` (`status`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
        
        // Booking details table
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'qym_booking_detail` (
            `id_booking_detail` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_booking` INT(11) UNSIGNED NOT NULL,
            `room_type` VARCHAR(100) NOT NULL,
            `quantity` INT(11) UNSIGNED NOT NULL DEFAULT 1,
            `price` DECIMAL(20,6) NOT NULL,
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_booking_detail`),
            KEY `id_booking` (`id_booking`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
        
        // Multilang table example
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'qym_category` (
            `id_category` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `position` INT(11) UNSIGNED NOT NULL DEFAULT 0,
            `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_category`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
        
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'qym_category_lang` (
            `id_category` INT(11) UNSIGNED NOT NULL,
            `id_lang` INT(11) UNSIGNED NOT NULL,
            `name` VARCHAR(128) NOT NULL,
            `description` TEXT,
            PRIMARY KEY (`id_category`, `id_lang`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
        
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Drop all module tables
     */
    public function dropTables()
    {
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'qym_booking_detail`';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'qym_booking`';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'qym_category_lang`';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'qym_category`';
        
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Install default/sample data
     */
    public function installDefaultData()
    {
        // Set configurations
        Configuration::updateValue('QYM_ENABLED', 1);
        Configuration::updateValue('QYM_DEFAULT_STATUS', QYM_STATUS_PENDING);
        
        // Insert sample data if needed
        $booking = new QymBooking();
        $booking->id_customer = 1;
        $booking->booking_reference = 'SAMPLE-001';
        $booking->total_price = 99.99;
        $booking->status = QYM_STATUS_PENDING;
        $booking->save();
        
        return true;
    }
    
    /**
     * Delete all configurations
     */
    public function deleteConfigurations()
    {
        Configuration::deleteByName('QYM_ENABLED');
        Configuration::deleteByName('QYM_DEFAULT_STATUS');
        
        return true;
    }
}
```

---

## 🏗️ Using in Module Install/Uninstall

```php
// In main module file: qlomodulename.php

public function install()
{
    include_once dirname(__FILE__).'/classes/QymModuleDb.php';
    $objDb = new QymModuleDb();
    
    return parent::install()
        && $objDb->createTables()
        && $objDb->installDefaultData()
        && $this->registerHook('displayHeader');
}

public function uninstall()
{
    include_once dirname(__FILE__).'/classes/QymModuleDb.php';
    $objDb = new QymModuleDb();
    
    return $objDb->dropTables()
        && $objDb->deleteConfigurations()
        && parent::uninstall();
}
```

---

## ✅ ObjectModel Best Practices

### 1. Always Validate Before Use

```php
✅ Correct:
$booking = new QymBooking($idBooking);
if (Validate::isLoadedObject($booking)) {
    // Use $booking safely
}

❌ Wrong:
$booking = new QymBooking($idBooking);
echo $booking->total_price;  // Might be empty object!
```

### 2. Use Static Methods for Queries

```php
✅ Correct:
class QymBooking extends ObjectModel
{
    public static function getActiveBookings()
    {
        // Query logic
    }
}

// Usage
$bookings = QymBooking::getActiveBookings();

❌ Wrong:
$booking = new QymBooking();
$bookings = $booking->getActiveBookings();  // Inefficient
```

### 3. Type Cast All Query Variables

```php
✅ Correct:
$sql = 'WHERE `id_customer` = '.(int)$idCustomer;
$sql = 'WHERE `name` = "'.pSQL($name).'"';

❌ Wrong:
$sql = 'WHERE `id_customer` = '.$idCustomer;  // SQL injection risk!
```

### 4. Query Only Required Fields

```php
✅ Correct:
$sql = 'SELECT `id_booking`, `booking_reference`, `total_price`
        FROM `'._DB_PREFIX_.'qym_booking`';

❌ Wrong:
$sql = 'SELECT * FROM `'._DB_PREFIX_.'qym_booking`';  // Inefficient
```

---

## 🔗 Related Documentation

- [database-operations.md](./database-operations.md) - SQL queries and security
- [deployment.md](./deployment.md) - Table creation in install
- [architecture.md](./architecture.md) - Class file organization

---

**Next**: Learn [controllers.md](./controllers.md) for CRUD interfaces
