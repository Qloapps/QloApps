# Deployment & Distribution

> **Install/uninstall, upgrades, versioning, and distribution best practices**

## 📦 Module Distribution

### Distribution Checklist

```markdown
## Pre-Release Checklist
- [ ] Version number updated in main module file
- [ ] CHANGELOG.md updated
- [ ] README.md complete and accurate
- [ ] LICENSE file included
- [ ] All files have license headers
- [ ] index.php in all folders
- [ ] No development/test files included
- [ ] No .git folder or .gitignore in zip
- [ ] Module name matches folder name
- [ ] Config.xml version matches module version

## Package Structure
- [ ] qloyourmodule/
  - [ ] qloyourmodule.php (main file)
  - [ ] index.php
  - [ ] logo.png (16x16, 32x32, 45x45, 57x57)
  - [ ] LICENSE
  - [ ] README.md
  - [ ] CHANGELOG.md
  - [ ] config.xml
  - [ ] classes/ (with index.php)
  - [ ] controllers/ (with index.php)
  - [ ] views/ (with index.php in all subdirs)
  - [ ] translations/ (if any exist)
```

---

## 🔧 Install & Uninstall

### Install Method Pattern

**Reference**: `modules/hotelreservationsystem/hotelreservationsystem.php`

```php
public function install()
{
    // 1. Install parent (registers module)
    if (!parent::install()) {
        return false;
    }

    // 2. Create database tables
    if (!$this->installDb()) {
        return false;
    }

    // 3. Install default configuration
    if (!$this->installConfiguration()) {
        return false;
    }

    // 4. Register hooks
    if (!$this->registerHooks()) {
        return false;
    }

    // 5. Install admin tabs
    if (!$this->installTabs()) {
        return false;
    }

    // 6. Install demo data (optional)
    // if (!$this->installDemoData()) {
    //     return false;
    // }

    return true;
}

protected function installDb()
{
    require_once dirname(__FILE__).'/classes/QymModuleDb.php';
    $db = new QymModuleDb();
    return $db->createTables();
}

protected function installConfiguration()
{
    $configs = array(
        'QYM_ENABLE_MODULE' => 1,
        'QYM_MAX_BOOKING_DAYS' => 30,
        'QYM_CANCELLATION_HOURS' => 24,
    );

    foreach ($configs as $key => $value) {
        if (!Configuration::updateValue($key, $value)) {
            return false;
        }
    }

    return true;
}

protected function registerHooks()
{
    $hooks = array(
        'displayHeader',
        'displayNav',
        'displayCustomerAccount',
        'actionObjectOrderAddAfter',
    );

    foreach ($hooks as $hook) {
        if (!$this->registerHook($hook)) {
            return false;
        }
    }

    return true;
}

protected function installTabs()
{
    $tabs = array(
        array(
            'class_name' => 'AdminQloYourModule',
            'name' => 'Your Module',
            'id_parent' => (int)Tab::getIdFromClassName('AdminParentHotels'),
        ),
        array(
            'class_name' => 'AdminQymBookings',
            'name' => 'Bookings',
            'id_parent' => (int)Tab::getIdFromClassName('AdminQloYourModule'),
        ),
    );

    foreach ($tabs as $tab_data) {
        if (!$this->installTab($tab_data)) {
            return false;
        }
    }

    return true;
}

protected function installTab($tab_data)
{
    $tab = new Tab();
    $tab->class_name = $tab_data['class_name'];
    $tab->id_parent = $tab_data['id_parent'];
    $tab->module = $this->name;
    $tab->position = Tab::getNewLastPosition($tab_data['id_parent']);

    // Set name for all languages
    foreach (Language::getLanguages(false) as $language) {
        $tab->name[$language['id_lang']] = $this->l($tab_data['name']);
    }

    return $tab->add();
}
```

### Uninstall Method Pattern

```php
public function uninstall()
{
    // 1. Remove admin tabs
    if (!$this->uninstallTabs()) {
        return false;
    }

    // 2. Remove configuration values
    if (!$this->uninstallConfiguration()) {
        return false;
    }

    // 3. Drop database tables (optional - consider data retention)
    // if (!$this->uninstallDb()) {
    //     return false;
    // }

    // 4. Remove uploaded files (optional - consider data retention)
    // if (!$this->removeFiles()) {
    //     return false;
    // }

    // 5. Uninstall parent (unregisters hooks, removes module entry)
    if (!parent::uninstall()) {
        return false;
    }

    return true;
}

protected function uninstallTabs()
{
    $tabs = array(
        'AdminQloYourModule',
        'AdminQymBookings',
    );

    foreach ($tabs as $class_name) {
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            if (!$tab->delete()) {
                return false;
            }
        }
    }

    return true;
}

protected function uninstallConfiguration()
{
    $configs = array(
        'QYM_ENABLE_MODULE',
        'QYM_MAX_BOOKING_DAYS',
        'QYM_CANCELLATION_HOURS',
    );

    foreach ($configs as $key) {
        if (!Configuration::deleteByName($key)) {
            return false;
        }
    }

    return true;
}

protected function uninstallDb()
{
    require_once dirname(__FILE__).'/classes/QymModuleDb.php';
    $db = new QymModuleDb();
    return $db->dropTables();
}

protected function removeFiles()
{
    $upload_dir = _PS_MODULE_DIR_.$this->name.'/uploads/';

    if (is_dir($upload_dir)) {
        return $this->deleteDirectory($upload_dir);
    }

    return true;
}

protected function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!$this->deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) {
            return false;
        }
    }

    return rmdir($dir);
}
```

---

## 🔄 Module Upgrades

### Upgrade System

**File**: `upgrade/upgrade-1.1.0.php`

```php
<?php
/**
 * NOTICE OF LICENSE
 * ...
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to version 1.1.0
 * @param QloYourModule $module
 * @return bool
 */
function upgrade_module_1_1_0($module)
{
    // 1. Add new configuration
    Configuration::updateValue('QYM_NEW_FEATURE', 1);

    // 2. Add new database column
    $sql = 'ALTER TABLE `'._DB_PREFIX_.'qlo_qym_booking`
        ADD COLUMN `cancellation_reason` TEXT NULL AFTER `status`';

    if (!Db::getInstance()->execute($sql)) {
        return false;
    }

    // 3. Add new database table
    $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'qlo_qym_booking_note` (
        `id_booking_note` int(11) NOT NULL AUTO_INCREMENT,
        `id_booking` int(11) NOT NULL,
        `note` TEXT NOT NULL,
        `date_add` datetime NOT NULL,
        PRIMARY KEY (`id_booking_note`)
    ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

    if (!Db::getInstance()->execute($sql)) {
        return false;
    }

    // 4. Migrate existing data (if needed)
    $bookings = Db::getInstance()->executeS('
        SELECT `id_booking`, `old_field`
        FROM `'._DB_PREFIX_.'qlo_qym_booking`
        WHERE `old_field` IS NOT NULL
    ');

    foreach ($bookings as $booking) {
        Db::getInstance()->update('qlo_qym_booking', array(
            'new_field' => pSQL($booking['old_field']),
        ), '`id_booking` = '.(int)$booking['id_booking']);
    }

    // 5. Register new hooks
    if (!$module->registerHook('displayFooter')) {
        return false;
    }

    // 6. Update module cache
    Tools::clearCache(Context::getContext()->smarty, $module->getTemplatePath('module:qloyourmodule/views/templates/hook/display_nav.tpl'));

    return true;
}
```

### Upgrade File Naming

```
upgrade/
├── index.php
├── install-1.0.0.php      # Optional: Run on first install
├── upgrade-1.0.1.php      # Upgrade from 1.0.0 to 1.0.1
├── upgrade-1.1.0.php      # Upgrade from 1.0.x to 1.1.0
└── upgrade-2.0.0.php      # Upgrade from 1.x to 2.0.0
```

### Version Comparison

```php
// In main module file
public function __construct()
{
    $this->name = 'qloyourmodule';
    $this->tab = 'administration';
    $this->version = '1.1.0';
    $this->author = 'Your Name';
    $this->need_instance = 0;
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l('Your Module');
    $this->description = $this->l('Module description');
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

    // Check if upgrade needed
    $this->checkUpgrade();
}

protected function checkUpgrade()
{
    $installed_version = Configuration::get('QYM_MODULE_VERSION');

    if ($installed_version != $this->version) {
        // Upgrade needed
        Configuration::updateValue('QYM_MODULE_VERSION', $this->version);
    }
}
```

---

## 📋 Versioning Strategy

### Semantic Versioning

**Format**: `MAJOR.MINOR.PATCH`

- **MAJOR**: Breaking changes, incompatible API changes
- **MINOR**: New features, backwards-compatible
- **PATCH**: Bug fixes, backwards-compatible

**Examples**:
- `1.0.0` - Initial release
- `1.0.1` - Bug fix
- `1.1.0` - New feature added
- `2.0.0` - Breaking changes

### CHANGELOG.md Format

```markdown
# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-01-15

### Added
- New cancellation reason field in booking form
- Booking notes feature for admin
- Email notification on booking cancellation

### Changed
- Improved booking list performance with database indexes
- Updated booking form UI for better mobile experience

### Fixed
- Fixed date validation issue in Safari browser
- Fixed permission check for hotel-specific bookings
- Fixed XSS vulnerability in booking notes

### Security
- Added CSRF token validation to cancellation endpoint
- Improved SQL injection prevention in search queries

## [1.0.1] - 2025-01-01

### Fixed
- Fixed installation error on MySQL 8.0
- Fixed translation loading issue

## [1.0.0] - 2024-12-15

### Added
- Initial release
- Booking management system
- Customer booking interface
- Admin reporting
```

---

## 📦 Creating Distribution Package

### Manual Packaging

```bash
# 1. Navigate to modules directory
cd /path/to/qloapps/modules/

# 2. Create zip (exclude development files)
zip -r qloyourmodule-1.1.0.zip qloyourmodule/ \
    -x "qloyourmodule/.git/*" \
    -x "qloyourmodule/.gitignore" \
    -x "qloyourmodule/node_modules/*" \
    -x "qloyourmodule/.DS_Store" \
    -x "qloyourmodule/tests/*"
```

### Automated Build Script

**File**: `build.sh`

```bash
#!/bin/bash

# Build script for QloYourModule

MODULE_NAME="qloyourmodule"
VERSION=$(grep "this->version = " ${MODULE_NAME}.php | cut -d"'" -f2)
BUILD_DIR="build"
PACKAGE_NAME="${MODULE_NAME}-${VERSION}.zip"

echo "Building ${MODULE_NAME} version ${VERSION}..."

# Create build directory
mkdir -p ${BUILD_DIR}
rm -rf ${BUILD_DIR}/${MODULE_NAME}

# Copy module files
cp -r . ${BUILD_DIR}/${MODULE_NAME}

# Remove development files
rm -rf ${BUILD_DIR}/${MODULE_NAME}/.git
rm -rf ${BUILD_DIR}/${MODULE_NAME}/.gitignore
rm -rf ${BUILD_DIR}/${MODULE_NAME}/node_modules
rm -rf ${BUILD_DIR}/${MODULE_NAME}/tests
rm -rf ${BUILD_DIR}/${MODULE_NAME}/build.sh
rm -rf ${BUILD_DIR}/${MODULE_NAME}/build

# Create zip
cd ${BUILD_DIR}
zip -r ${PACKAGE_NAME} ${MODULE_NAME}/
cd ..

echo "Package created: ${BUILD_DIR}/${PACKAGE_NAME}"
```

---

## 🔒 Security Considerations

### Production Configuration

```php
// In module configuration
public function getContent()
{
    // Disable demo features in production
    if (!Configuration::get('PS_DEMO_MODE')) {
        // Remove demo data options
    }

    // Disable debug output
    if (!_PS_MODE_DEV_) {
        // Remove debug messages
    }

    // ...rest of configuration
}
```

### Sensitive Data Handling

```php
// Never include in package:
// - API keys (use Configuration instead)
// - Database credentials (use QloApps config)
// - Test data
// - Debug files

// Good: Store in database
Configuration::updateValue('QYM_API_KEY', $api_key);

// Bad: Hardcode in module
// private $api_key = 'sk_live_abc123';
```

---

## 📝 Documentation Requirements

### README.md Template

```markdown
# QloYourModule

Module description and purpose.

## Features

- Feature 1
- Feature 2
- Feature 3

## Requirements

- QloApps 1.7.0+
- PHP 8.1 - 8.4
- MySQL 5.7+ / MariaDB 10.2+

## Installation

1. Download the module zip file
2. Go to Back Office > Modules > Module Manager
3. Click "Upload a module"
4. Select the zip file
5. Click "Install"
6. Configure the module settings

## Configuration

1. Go to Back Office > Hotels > Your Module
2. Configure settings:
   - Enable module
   - Set maximum booking days
   - Configure cancellation policy
3. Click "Save"

## Usage

### For Customers

1. Navigate to My Account > My Bookings
2. View booking history
3. Cancel bookings (if allowed)

### For Administrators

1. Go to Back Office > Hotels > Your Module > Bookings
2. View all bookings
3. Manage bookings
4. Export reports

## Hooks Used

- `displayHeader` - Load CSS/JS
- `displayNav` - Display booking link in navigation
- `displayCustomerAccount` - Display booking link in customer account
- `actionObjectOrderAddAfter` - Process booking after order

## Database Tables

- `qlo_qym_booking` - Main booking table
- `qlo_qym_booking_detail` - Booking details

## Support

For support, contact: support@example.com

## License

This module is licensed under the Open Software License (OSL 3.0).

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.
```

---

## 🧪 Pre-Release Testing

### Final Testing Checklist

```markdown
## Installation Testing
- [ ] Install on clean QloApps 1.7.0
- [ ] Install on QloApps with other modules active
- [ ] Verify all tables created
- [ ] Verify all configuration created
- [ ] Verify all hooks registered
- [ ] Verify all admin tabs created

## Upgrade Testing
- [ ] Install version 1.0.0
- [ ] Upgrade to version 1.1.0
- [ ] Verify data migrated correctly
- [ ] Verify new features work
- [ ] Verify existing features still work

## Uninstall Testing
- [ ] Uninstall module
- [ ] Verify tables removed (if configured)
- [ ] Verify configuration removed
- [ ] Verify admin tabs removed
- [ ] Verify no errors after uninstall
- [ ] Reinstall works correctly

## Compatibility Testing
- [ ] Test on PHP 8.1
- [ ] Test on PHP 8.2
- [ ] Test on PHP 8.3
- [ ] Test on PHP 8.4
- [ ] Test on MySQL 5.7
- [ ] Test on MySQL 8.0
- [ ] Test on MariaDB
- [ ] Test with default theme
- [ ] Test with custom theme
```

---

## 🌐 Distribution Platforms

### Webkul Store

1. Create seller account
2. Submit module for review
3. Provide documentation
4. Set pricing
5. Publish

### GitHub Release

```bash
# Tag version
git tag -a v1.1.0 -m "Version 1.1.0"
git push origin v1.1.0

# Create GitHub release
# - Go to GitHub > Releases > New Release
# - Select tag v1.1.0
# - Upload zip package
# - Add release notes from CHANGELOG.md
# - Publish release
```

---

## 🔗 Related Documentation

- [security-validation.md](./security-validation.md) - Security testing
- [architecture.md](./architecture.md) - Module structure
- [SKILL.md](./SKILL.md) - Development overview

---

## ✅ Final Checklist

```markdown
- [ ] Version number updated
- [ ] CHANGELOG.md updated
- [ ] README.md complete
- [ ] License headers in all files
- [ ] index.php in all folders
- [ ] No development files in package
- [ ] Package created and tested
- [ ] Documentation complete
- [ ] Support contact information correct
```

---

**Ready for deployment!** 🚀
