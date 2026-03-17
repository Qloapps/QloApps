# Module Architecture & File Structure

> **Complete module directory structure and organization patterns for QloApps addons**

## 📐 Standard Module Structure

**Reference**: `modules/hotelreservationsystem/`

```
modules/qlomodulename/
├── qlomodulename.php             # Main module class (REQUIRED)
├── config.xml                     # Module metadata (REQUIRED)
├── define.php                     # Module constants
├── index.php                      # Security file (REQUIRED)
├── LICENSE.md                     # OSL-3.0 license (REQUIRED)
├── README.md                      # Documentation (REQUIRED)
├── CHANGELOG.txt                  # Version history (REQUIRED)
├── logo.png                       # Module icon 200x200 (REQUIRED)
├── logo.gif                       # Module icon animated (optional)
├──

 classes/                       # ObjectModel & helper classes
│   ├── index.php
│   ├── QymModuleDb.php           # Database operations class
│   ├── QymModelOne.php           # ObjectModel classes
│   ├── QymModelTwo.php
│   └── QymHelper.php             # Helper functions
├── controllers/                   # Controllers
│   ├── index.php
│   ├── admin/                    # Admin controllers
│   │   ├── index.php
│   │   ├── AdminQymController.php
│   │   └── AdminQymSettingsController.php
│   └── front/                    # Front controllers
│       ├── index.php
│       └── display.php
├── views/                         # Templates, CSS, JS
│   ├── index.php
│   ├── templates/
│   │   ├── index.php
│   │   ├── admin/                # Admin templates
│   │   │   ├── index.php
│   │   │   └── settings.tpl
│   │   ├── front/                # Front templates
│   │   │   ├── index.php
│   │   │   └── display.tpl
│   │   └── hook/                 # Hook templates
│   │       ├── index.php
│   │       ├── display-header.tpl
│   │       └── display-footer.tpl
│   ├── css/                      # Stylesheets
│   │   ├── index.php
│   │   ├── admin/
│   │   │   ├── index.php
│   │   │   └── qlo_module_admin.css
│   │   ├── front/
│   │   │   ├── index.php
│   │   │   └── qlo_module_front.css
│   │   └── global/
│   │       ├── index.php
│   │       └── qlo_module_global.css
│   ├── js/                       # JavaScript files
│   │   ├── index.php
│   │   ├── admin/
│   │   │   ├── index.php
│   │   │   └── qlo_module_admin.js
│   │   ├── front/
│   │   │   ├── index.php
│   │   │   └── qlo_module_front.js
│   │   └── global/
│   │       ├── index.php
│   │       └── qlo_module_global.js
│   └── img/                      # Images
│       ├── index.php
│       └── icon.png
├── mails/                        # Email templates (if needed)
│   ├── index.php
│   ├── en/
│   │   ├── index.php
│   │   ├── notification.html
│   │   └── notification.txt
│   └── fr/
│       ├── index.php
│       ├── notification.html
│       └── notification.txt
├── translations/                  # Translation files
│   ├── index.php
│   └── en.php
├── upgrade/                       # Upgrade scripts
│   ├── index.php
│   └── upgrade-1.1.0.php
├── lib/                          # Third-party libraries (if needed)
│   ├── index.php
│   └── vendorname/
│       └── library files
└── documents/                     # Documentation files
    ├── index.php
    └── user-guide.pdf
```

---

## 📄 Required Files (MANDATORY)

### 1. Main Module File: `qlomodulename.php`

**Purpose**: Main module class with install/uninstall and hooks

**Pattern**:
- File name must match folder name
- Class name is PascalCase version of file name
- Must extend `Module` class

```php
<?php
/**
 * [LICENSE HEADER - See coding-conventions.md]
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once 'define.php';

class QloModuleName extends Module
{
    public function __construct()
    {
        $this->name = 'qlomodulename';
        $this->tab = 'administration';  // or 'front_office_features', etc.
        $this->version = '1.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        
        parent::__construct();
        
        $this->displayName = $this->l('Module Display Name');
        $this->description = $this->l('Module description');
        $this->confirmUninstall = $this->l('Are you sure?');
    }
}
```

### 2. config.xml

**Purpose**: Module metadata for QloApps

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<module>
    <name>qlomodulename</name>
    <displayName><![CDATA[Module Display Name]]></displayName>
    <version><![CDATA[1.0.0]]></version>
    <description><![CDATA[Module description]]></description>
    <author><![CDATA[Webkul]]></author>
    <tab><![CDATA[administration]]></tab>
    <is_configurable>1</is_configurable>
    <need_instance>0</need_instance>
    <limited_countries></limited_countries>
</module>
```

### 3. LICENSE.md

**Purpose**: Open Software License 3.0 (OSL-3.0)

Copy from: `modules/hotelreservationsystem/LICENSE.md`

**Must include**:
- Full OSL-3.0 license text
- Copyright information
- Links to license source

### 4. README.md

**Purpose**: Module documentation for users and developers

**Must include**:
- Module description
- Installation instructions
- Configuration guide
- Usage examples
- **Manual installation steps** (if custom hooks needed!)
- Troubleshooting
- Support contact

**Template**:
```markdown
# Module Name

## Description
Brief description of what this module does.

## Requirements
- QloApps 1.7.0+
- PHP 8.1+

## Installation
1. Upload module to /modules/
2. Install from Modules page
3. Configure settings

## Manual Steps (if any)
⚠️ **Important**: This module requires custom hook placement:

Add this code to `classes/Cart.php` at line 123:
\`\`\`php
Hook::exec('actionAfterCartValidation', array('cart' => $this));
\`\`\`

## Configuration
Description of configuration options

## Support
support@qloapps.com
```

### 5. CHANGELOG.txt

**Purpose**: Version history and changes

**Format**:
```
# v1.0.0 - 2026-02-26
- Initial release
- Feature 1
- Feature 2

# v1.1.0 - 2026-03-01
- Added feature 3
- Fixed bug in feature 1
- Improved performance
```

### 6. logo.png

**Purpose**: Module icon shown in admin modules list

**Specifications**:
- Size: 200x200 pixels
- Format: PNG
- Transparent background preferred

### 7. index.php (in EVERY folder)

**Purpose**: Prevent directory listing

**Content** (same for all):
```php
<?php
/**
 * [LICENSE HEADER]
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

## 🏗️ Optional But Recommended Files

### define.php

**Purpose**: Module-wide constants

**Reference**: `modules/hotelreservationsystem/define.php`

```php
<?php
/**
 * [LICENSE HEADER]
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

// Module constants
define('QYM_MODULE_DIR', _PS_MODULE_DIR_.'qlomodulename/');
define('QYM_VIEWS_DIR', QYM_MODULE_DIR.'views/');
define('QYM_CLASSES_DIR', QYM_MODULE_DIR.'classes/');

// Status constants
define('QYM_STATUS_ACTIVE', 1);
define('QYM_STATUS_INACTIVE', 0);

// Other module-specific constants
define('QYM_DEFAULT_LIMIT', 10);
```

### Database Class: `classes/QymModuleDb.php`

**Purpose**: Handle all database operations (create/drop tables, configs)

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
    public function createTables()
    {
        $sql = array();
        
        // Table 1
        $sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'qym_table_name` (
            `id_table` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `field_one` VARCHAR(255) NOT NULL,
            `field_two` INT(11) NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_table`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
        
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function dropTables()
    {
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'qym_table_name`';
        
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function installDefaultData()
    {
        // Set default configurations
        Configuration::updateValue('QYM_CONFIG_NAME', 'default_value');
        
        return true;
    }
    
    public function deleteConfigurations()
    {
        Configuration::deleteByName('QYM_CONFIG_NAME');
        
        return true;
    }
}
```

---

## 📂 Folder Organization Rules

### classes/

**Contains**:
- ObjectModel classes for database tables
- Helper classes
- Database class (*Db.php)
- Service classes

**Naming**:
- Class files: `QymClassName.php` (PascalCase with module prefix)
- Class names: `QymClassName`

**Example**:
```
classes/
├── QymModuleDb.php          # Database operations
├── QymBooking.php           # ObjectModel for bookings
├── QymSettings.php          # ObjectModel for settings
└── QymHelper.php            # Helper functions
```

### controllers/admin/

**Contains**: Admin controllers for back-office management

**Naming**: `AdminQym{Feature}Controller.php`

**Example**:
```
controllers/admin/
├── AdminQymBookingsController.php
├── AdminQymSettingsController.php
└── AdminQymReportsController.php
```

### controllers/front/

**Contains**: Front controllers for customer-facing pages

**Naming**: `{action}.php` (lowercase)

**Example**:
```
controllers/front/
├── display.php              # ModuleFrontController
├── booking.php
└── confirmation.php
```

### views/templates/

**Contains**: Smarty template files

**Organization**:
- `admin/` - Admin templates
- `front/` - Front templates
- `hook/` - Hook templates

**Naming for partials**: `qlo_{description}.tpl`

**Example**:
```
views/templates/
├── admin/
│   └── settings.tpl
├── front/
│   ├── display.tpl
│   └── booking.tpl
└── hook/
    ├── qlo_cart_booking.tpl
    └── qlo_room_details.tpl
```

### views/css/ & views/js/

**Organization**:
- `admin/` - Admin-only files
- `front/` - Front-only files
- `global/` - Used in both admin and front

**Naming**: `qlo_{description}.{css|js}`

**Example**:
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

views/js/
├── admin/
│   └── qlo_module_admin.js
├── front/
│   └── qlo_module_front.js
└── global/
    └── qlo_module_global.js
```

---

## ✅ File Creation Checklist

When creating new module:

### Phase 1: Core Files
- [ ] Create module folder: `modules/qlomodulename/`
- [ ] Create main file: `qlomodulename.php`
- [ ] Create config.xml
- [ ] Create define.php
- [ ] Create index.php (root level)

### Phase 2: Mandatory Files
- [ ] Copy LICENSE.md (OSL-3.0)
- [ ] Create README.md with manual steps (if any)
- [ ] Create CHANGELOG.txt
- [ ] Add logo.png (200x200)
- [ ] Add logo.gif (optional)

### Phase 3: Structure
- [ ] Create `classes/` folder + index.php
- [ ] Create `classes/QymModuleDb.php`
- [ ] Create `controllers/` folder + index.php
- [ ] Create `controllers/admin/` + index.php
- [ ] Create `controllers/front/` + index.php
- [ ] Create `views/` folder + index.php
- [ ] Create `views/templates/` structure + index.php files
- [ ] Create `views/css/` structure + index.php files
- [ ] Create `views/js/` structure + index.php files

### Phase 4: Additional (as needed)
- [ ] Create `mails/` if sending emails
- [ ] Create `translations/` folder
- [ ] Create `upgrade/` folder
- [ ] Create `lib/` if using third-party libraries
- [ ] Create `documents/` for user guides

---

## 🔍 Architecture Best Practices

### 1. Separation of Concerns

**Principle**: Each file/folder has ONE clear responsibility

✅ **Good**:
```
classes/QymBooking.php        → Only booking database operations
controllers/admin/AdminQymBookingsController.php  → Only booking admin UI
views/templates/admin/bookings.tpl  → Only booking display
```

❌ **Bad**:
```
qlomodulename.php  → Everything mixed together
```

### 2. No Code Outside Module Folder

**Rule**: Everything stays in `modules/qlomodulename/`

**Exception handling**: If you absolutely must place files in root or other locations, document it clearly in README.md with manual installation steps.

### 3. Follow QloApps Standards

- Use QloApps's folder structure conventions
- Use bootstrap = true for admin controllers
- Follow template inheritance patterns
- Use QloApps's helper classes

### 4. Index Files Everywhere

**Rule**: EVERY folder must have `index.php`

**Why**: Prevents directory listing, security

**Check**:
```bash
find modules/qlomodulename/ -type d ! -exec test -e '{}/index.php' \; -print
```

This should return no results (all folders have index.php)

---

## 📖 Reference Implementation

**Study**: `modules/hotelreservationsystem/`

Key observations:
- Well-organized class structure
- Clear separation of concerns
- All mandatory files present
- Proper naming conventions
- Index files in every folder
- Clean hooks implementation

---

## 🔗 Related Documentation

- [coding-conventions.md](./coding-conventions.md) - Naming rules and standards
- [deployment.md](./deployment.md) - Install/uninstall patterns
- [models-repositories.md](./models-repositories.md) - Class creation

---

**Next**: Review [coding-conventions.md](./coding-conventions.md) for naming and prefix rules
