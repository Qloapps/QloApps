# Database Operations & Security

> **SQL queries, prepared statements, and security patterns for QloApps**

## 🔒 Security-First Principles

1. **Always escape SQL inputs** - Use `pSQL()`, type casting, `bqSQL()`
2. **Never trust user input** - Validate before querying
3. **Use prepared statements when possible** - ObjectModel handles this
4. **Apply shop/hotel restrictions** - Multi-shop and hotel permissions
5. **Check return values** - Database operations can fail

---

## 🗄️ Query Methods

### Core Database Methods

**Reference**: `classes/db/Db.php`

```php
// Get single value
$count = Db::getInstance()->getValue('
    SELECT COUNT(*)
    FROM `'._DB_PREFIX_.'qlo_qym_booking`
    WHERE `id_customer` = '.(int)$id_customer.'
');

// Get single row
$booking = Db::getInstance()->getRow('
    SELECT *
    FROM `'._DB_PREFIX_.'qlo_qym_booking`
    WHERE `id_booking` = '.(int)$id_booking.'
');

// Get multiple rows
$bookings = Db::getInstance()->executeS('
    SELECT *
    FROM `'._DB_PREFIX_.'qlo_qym_booking`
    WHERE `id_customer` = '.(int)$id_customer.'
    ORDER BY `date_add` DESC
');

// Execute without result (INSERT, UPDATE, DELETE)
$result = Db::getInstance()->execute('
    UPDATE `'._DB_PREFIX_.'qlo_qym_booking`
    SET `status` = '.(int)$status.'
    WHERE `id_booking` = '.(int)$id_booking.'
');

// Insert with auto-increment ID
Db::getInstance()->insert('qlo_qym_booking', array(
    'id_customer' => (int)$id_customer,
    'total_price' => (float)$total_price,
    'status' => 1,
    'date_add' => date('Y-m-d H:i:s'),
));
$id_booking = Db::getInstance()->Insert_ID();

// Delete
Db::getInstance()->delete('qlo_qym_booking', '`id_booking` = '.(int)$id_booking);
```

---

## 🛡️ Input Escaping

### Type Casting (Preferred for Numbers)

```php
✅ Correct - Type casting:
$id_booking = (int)Tools::getValue('id_booking');
$price = (float)Tools::getValue('price');
$active = (bool)Tools::getValue('active');

WHERE `id_booking` = '.(int)$id_booking.'
```

### pSQL() for Strings

```php
✅ Correct - pSQL for strings:
$name = pSQL(Tools::getValue('name'));
$email = pSQL(Tools::getValue('email'));

WHERE `name` = "'.pSQL($name).'"
WHERE `email` = "'.pSQL($email).'"
```

### bqSQL() for Table/Column Names

```php
✅ Correct - bqSQL for identifiers:
$table = bqSQL(Tools::getValue('table'));
$column = bqSQL(Tools::getValue('column'));

SELECT * FROM `'._DB_PREFIX_.bqSQL($table).'`
ORDER BY `'.bqSQL($column).'` DESC
```

### Common Mistakes

```php
❌ Wrong - No escaping:
WHERE `name` = "'.$_GET['name'].'"  // SQL injection!

❌ Wrong - Wrong escaping method:
WHERE `id_booking` = "'.pSQL($id_booking).'"  // Should use (int)

❌ Wrong - Using variables in table prefix:
FROM `'.$_DB_PREFIX_.'table`  // Use constant _DB_PREFIX_
```

---

## 🏨 Hotel Permissions

### Hotel Restriction in Queries

**Pattern from hotelreservationsystem**:

```php
// Get hotel IDs accessible to current admin
public static function addHotelRestriction($front = false, $share = false)
{
    $context = Context::getContext();
    
    if ($front) {
        // Front office - use session hotel
        if (isset($context->cookie->id_hotel)) {
            return ' AND hbi.`id` = '.(int)$context->cookie->id_hotel;
        }
    } else {
        // Back office - check employee permissions
        if (!$context->employee->isSuperAdmin()) {
            $hotels = HotelBranchInformation::getProfileAccessedHotels(
                $context->employee->id_profile,
                1
            );
            
            if ($hotels) {
                $hotel_ids = array_column($hotels, 'id_hotel');
                return ' AND hbi.`id` IN ('.implode(',', array_map('intval', $hotel_ids)).')';
            } else {
                return ' AND hbi.`id` = 0';  // No access
            }
        }
    }
    
    return '';  // Super admin - full access
}
```

### Using Hotel Restriction

```php
✅ Correct - Apply hotel restriction:
$sql = 'SELECT b.*
    FROM `'._DB_PREFIX_.'qlo_qym_booking` b
    INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (b.`id_cart` = hbd.`id_cart`)
    INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON (hbd.`id_hotel` = hbi.`id`)
    WHERE b.`id_customer` = '.(int)$id_customer
    .HotelBranchInformation::addHotelRestriction(false);

$bookings = Db::getInstance()->executeS($sql);
```

---

## 🏪 Multi-Shop Context

### Shop Restriction

**Reference**: `classes/shop/Shop.php`

```php
// Add shop restriction to query
Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c')
Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o')
Shop::addSqlRestriction(false, 'p')  // Current shop only
```

### Example with Shop Context

```php
$sql = 'SELECT c.*
    FROM `'._DB_PREFIX_.'customer` c
    WHERE c.`email` = "'.pSQL($email).'"
    '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c');

$customer = Db::getInstance()->getRow($sql);
```

---

## 📋 Common Query Patterns

### SELECT with JOIN

```php
$bookings = Db::getInstance()->executeS('
    SELECT 
        b.`id_booking`,
        b.`booking_reference`,
        b.`total_price`,
        c.`firstname`,
        c.`lastname`,
        hbd.`room_num`,
        hbd.`date_from`,
        hbd.`date_to`
    FROM `'._DB_PREFIX_.'qlo_qym_booking` b
    INNER JOIN `'._DB_PREFIX_.'customer` c ON (b.`id_customer` = c.`id_customer`)
    INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (b.`id_cart` = hbd.`id_cart`)
    WHERE b.`id_customer` = '.(int)$id_customer.'
    ORDER BY b.`date_add` DESC
');
```

### INSERT

```php
// Method 1: Using insert()
$result = Db::getInstance()->insert('qlo_qym_booking', array(
    'id_customer' => (int)$id_customer,
    'booking_reference' => pSQL($reference),
    'total_price' => (float)$total_price,
    'status' => 1,
    'date_add' => date('Y-m-d H:i:s'),
    'date_upd' => date('Y-m-d H:i:s'),
));

if ($result) {
    $id_booking = Db::getInstance()->Insert_ID();
}

// Method 2: Raw SQL
$result = Db::getInstance()->execute('
    INSERT INTO `'._DB_PREFIX_.'qlo_qym_booking`
    (`id_customer`, `booking_reference`, `total_price`, `status`, `date_add`)
    VALUES
    ('.(int)$id_customer.', "'.pSQL($reference).'", '.(float)$total_price.', 1, NOW())
');
```

### UPDATE

```php
// Method 1: Using update()
$result = Db::getInstance()->update('qlo_qym_booking', array(
    'status' => (int)$new_status,
    'date_upd' => date('Y-m-d H:i:s'),
), '`id_booking` = '.(int)$id_booking);

// Method 2: Raw SQL
$result = Db::getInstance()->execute('
    UPDATE `'._DB_PREFIX_.'qlo_qym_booking`
    SET 
        `status` = '.(int)$new_status.',
        `date_upd` = NOW()
    WHERE `id_booking` = '.(int)$id_booking.'
');
```

### DELETE

```php
// Method 1: Using delete()
$result = Db::getInstance()->delete('qlo_qym_booking', '`id_booking` = '.(int)$id_booking);

// Method 2: Raw SQL
$result = Db::getInstance()->execute('
    DELETE FROM `'._DB_PREFIX_.'qlo_qym_booking`
    WHERE `id_booking` = '.(int)$id_booking.'
');
```

### COUNT

```php
$count = Db::getInstance()->getValue('
    SELECT COUNT(*)
    FROM `'._DB_PREFIX_.'qlo_qym_booking`
    WHERE `id_customer` = '.(int)$id_customer.'
    AND `status` = 1
');
```

### EXISTS Check

```php
$exists = (bool)Db::getInstance()->getValue('
    SELECT 1
    FROM `'._DB_PREFIX_.'qlo_qym_booking`
    WHERE `booking_reference` = "'.pSQL($reference).'"
    LIMIT 1
');

if ($exists) {
    // Reference already exists
}
```

---

## 🔄 Transactions

### Using Transactions

```php
try {
    Db::getInstance()->beginTransaction();
    
    // Insert booking
    Db::getInstance()->insert('qlo_qym_booking', array(
        'id_customer' => (int)$id_customer,
        'total_price' => (float)$total_price,
        'date_add' => date('Y-m-d H:i:s'),
    ));
    $id_booking = Db::getInstance()->Insert_ID();
    
    // Insert booking details
    foreach ($rooms as $room) {
        Db::getInstance()->insert('qlo_qym_booking_detail', array(
            'id_booking' => (int)$id_booking,
            'id_room' => (int)$room['id_room'],
            'date_from' => pSQL($room['date_from']),
            'date_to' => pSQL($room['date_to']),
        ));
    }
    
    Db::getInstance()->commit();
    return $id_booking;
    
} catch (Exception $e) {
    Db::getInstance()->rollBack();
    throw $e;
}
```

---

## 📊 Complex Query Examples

### Aggregation with GROUP BY

```php
$stats = Db::getInstance()->executeS('
    SELECT 
        DATE(b.`date_add`) as booking_date,
        COUNT(*) as total_bookings,
        SUM(b.`total_price`) as total_revenue,
        AVG(b.`total_price`) as avg_booking_price
    FROM `'._DB_PREFIX_.'qlo_qym_booking` b
    WHERE b.`date_add` >= "'.pSQL($date_from).'"
    AND b.`date_add` <= "'.pSQL($date_to).'"
    GROUP BY DATE(b.`date_add`)
    ORDER BY booking_date ASC
');
```

### Subquery

```php
$customers = Db::getInstance()->executeS('
    SELECT 
        c.`id_customer`,
        c.`firstname`,
        c.`lastname`,
        (
            SELECT COUNT(*)
            FROM `'._DB_PREFIX_.'qlo_qym_booking` b
            WHERE b.`id_customer` = c.`id_customer`
            AND b.`status` = 1
        ) as total_bookings
    FROM `'._DB_PREFIX_.'customer` c
    HAVING total_bookings > 0
    ORDER BY total_bookings DESC
');
```

### LEFT JOIN with NULL Check

```php
$rooms = Db::getInstance()->executeS('
    SELECT 
        r.`id_room`,
        r.`room_number`,
        b.`id_booking`
    FROM `'._DB_PREFIX_.'htl_room_information` r
    LEFT JOIN `'._DB_PREFIX_.'qlo_qym_booking_detail` bd ON (
        r.`id_room` = bd.`id_room`
        AND "'.pSQL($check_date).'" BETWEEN bd.`date_from` AND bd.`date_to`
    )
    LEFT JOIN `'._DB_PREFIX_.'qlo_qym_booking` b ON (bd.`id_booking` = b.`id_booking`)
    WHERE r.`id_hotel` = '.(int)$id_hotel.'
    AND b.`id_booking` IS NULL  -- Available rooms only
');
```

---

## ✅ Query Best Practices

### 1. Always Use Table Prefix

```php
✅ Correct:
FROM `'._DB_PREFIX_.'qlo_qym_booking`

❌ Wrong:
FROM `qlo_qym_booking`  // Breaks with custom prefix
```

### 2. Always Escape/Cast Inputs

```php
✅ Correct:
WHERE `id_booking` = '.(int)$id_booking.'
WHERE `name` = "'.pSQL($name).'"

❌ Wrong:
WHERE `id_booking` = '.$id_booking.'
WHERE `name` = "'.$name.'"
```

### 3. Check Return Values

```php
✅ Correct:
$result = Db::getInstance()->execute($sql);
if (!$result) {
    throw new Exception('Database error');
}

❌ Wrong:
Db::getInstance()->execute($sql);  // Ignores failure
```

### 4. Use Indexed Columns in WHERE

```php
✅ Correct - Using indexed id_customer:
WHERE `id_customer` = '.(int)$id_customer.'

⚠️ Slow - Using non-indexed booking_reference:
WHERE `booking_reference` = "'.pSQL($reference).'"
// Add index in install() if used frequently
```

### 5. Limit Results When Appropriate

```php
✅ Correct:
SELECT * FROM ... LIMIT 100

❌ Wrong:
SELECT * FROM ...  // Could return millions of rows
```

### 6. Use Specific Columns

```php
✅ Correct:
SELECT `id_booking`, `booking_reference`, `total_price` FROM ...

⚠️ Acceptable but slower:
SELECT * FROM ...  // Fetches all columns
```

---

## 🎯 Repository Pattern Example

**File**: `modules/qloyourmodule/classes/QymBookingRepository.php`

```php
class QymBookingRepository
{
    /**
     * Get bookings by customer ID
     */
    public static function getByCustomer($id_customer, $limit = null, $offset = 0)
    {
        $sql = 'SELECT b.*
            FROM `'._DB_PREFIX_.'qlo_qym_booking` b
            WHERE b.`id_customer` = '.(int)$id_customer.'
            ORDER BY b.`date_add` DESC';
        
        if ($limit) {
            $sql .= ' LIMIT '.(int)$offset.', '.(int)$limit;
        }
        
        return Db::getInstance()->executeS($sql);
    }
    
    /**
     * Get booking by reference
     */
    public static function getByReference($reference)
    {
        return Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'qlo_qym_booking`
            WHERE `booking_reference` = "'.pSQL($reference).'"
        ');
    }
    
    /**
     * Check if reference exists
     */
    public static function referenceExists($reference, $exclude_id = null)
    {
        $sql = 'SELECT 1
            FROM `'._DB_PREFIX_.'qlo_qym_booking`
            WHERE `booking_reference` = "'.pSQL($reference).'"';
        
        if ($exclude_id) {
            $sql .= ' AND `id_booking` != '.(int)$exclude_id;
        }
        
        $sql .= ' LIMIT 1';
        
        return (bool)Db::getInstance()->getValue($sql);
    }
    
    /**
     * Get bookings by date range
     */
    public static function getByDateRange($date_from, $date_to, $id_hotel = null)
    {
        $sql = 'SELECT b.*
            FROM `'._DB_PREFIX_.'qlo_qym_booking` b';
        
        if ($id_hotel) {
            $sql .= ' INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (b.`id_cart` = hbd.`id_cart`)
                INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON (hbd.`id_hotel` = hbi.`id`)';
        }
        
        $sql .= ' WHERE b.`date_add` >= "'.pSQL($date_from).'"
            AND b.`date_add` <= "'.pSQL($date_to).'"';
        
        if ($id_hotel) {
            $sql .= ' AND hbi.`id` = '.(int)$id_hotel;
        }
        
        $sql .= ' ORDER BY b.`date_add` DESC';
        
        return Db::getInstance()->executeS($sql);
    }
    
    /**
     * Get booking statistics
     */
    public static function getStats($date_from, $date_to)
    {
        return Db::getInstance()->getRow('
            SELECT 
                COUNT(*) as total_bookings,
                SUM(`total_price`) as total_revenue,
                AVG(`total_price`) as avg_booking_price,
                MAX(`total_price`) as max_booking_price
            FROM `'._DB_PREFIX_.'qlo_qym_booking`
            WHERE `date_add` >= "'.pSQL($date_from).'"
            AND `date_add` <= "'.pSQL($date_to).'"
            AND `status` = 1
        ');
    }
}
```

---

## 🔗 Related Documentation

- [models-repositories.md](./models-repositories.md) - ObjectModel (preferred over raw SQL)
- [security-validation.md](./security-validation.md) - Input validation before queries
- [architecture.md](./architecture.md) - QymModuleDb class for install/uninstall

---

**Next**: Review [security-validation.md](./security-validation.md) for comprehensive security patterns
