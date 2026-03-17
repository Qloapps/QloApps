---
skill_name: qloapps-addon-development
author: QloApps
tags:
  - module
  - addon
  - hooks
  - objectmodel
  - development
prerequisites:
  - PHP 8.1+
  - QloApps 1.7.0+ installed
  - Basic MVC understanding
  - Git repository setup
related_skills:
  - qloapps-hook-development
  - qloapps-objectmodel-development
  - qloapps-security-audit
reference_files:
  - modules/hotelreservationsystem/hotelreservationsystem.php
  - modules/hotelreservationsystem/classes/
  - modules/hotelreservationsystem/controllers/
---

# QloApps Addon Development

> **Create production-ready feature modules for QloApps using hooks-first architecture**

## ⚡ Quick Commands

```bash
# Module structure
modules/qlomodulename/qlomodulename.php    # Main module class
modules/qlomodulename/classes/             # ObjectModel classes
modules/qlomodulename/controllers/         # Admin/Front controllers
modules/qlomodulename/views/templates/     # Smarty templates

# Common operations
- Add database table      → See [architecture.md](./architecture.md#database-setup)
- Register hook           → See [hooks-system.md](./hooks-system.md#registering-hooks)
- Create admin page       → See [controllers.md](./controllers.md#admin-controllers)
- Add security validation → See [security-validation.md](./security-validation.md)
- Deploy module           → See [deployment.md](./deployment.md#distribution)
```

---

## 🚀 Quick Start (60 seconds)

This skill teaches you to create **complete QloApps modules** that:
- ✅ Work entirely within module folder (no core modifications)
- ✅ Use hooks for integration (hooks-first approach)
- ✅ Follow Webkul coding standards
- ✅ Include proper security, multilang, and permissions

**Quick Module Structure:**
```
modules/qlomodulename/
├── qlomodulename.php          # Main class
├── config.xml                  # Metadata
├── define.php                  # Constants
├── classes/                    # ObjectModel classes
├── controllers/admin/          # Admin controllers
├── controllers/front/          # Front controllers
├── views/templates/            # Smarty templates
├── views/css/                  # Stylesheets
├── views/js/                   # JavaScript
├── LICENSE.md                  # OSL-3.0 license
└── README.md                   # Documentation
```

---

## ✅ When to Use This Skill

Use this skill when:
- Creating new features for QloApps
- Building custom hotel management functionality
- Extending booking/reservation capabilities
- Adding admin dashboard features
- Creating customer-facing features

**Don't use this skill when:**
- Creating statistics modules → Use [qloapps-stats-addon-development](../qloapps-stats-addon-development/skill.md)
- Creating payment gateways → Use [qloapps-payment-gateway-development](../qloapps-payment-gateway-development/skill.md)
- Modifying core directly → Use [qloapps-core-development](../qloapps-core-development/skill.md)

---

## ⚠️ CRITICAL DEVELOPMENT PRINCIPLES

### **🔒 Module-Only Development (Non-Negotiable)**

**Rule**: All module work stays inside `modules/{modulename}/` folder

❌ **NEVER**:
- Modify core files directly
- Create files outside module folder
- Change existing QloApps files

✅ **ALWAYS**:
- Work within your module directory
- Use hooks for integration
- Keep module self-contained

**Exception**: Only if absolutely no alternative exists, document root-level file requirements in README.md for manual user placement.

---

### **🎣 Hooks-First Architecture (The Right Way)**

**Priority Order:**
```
1. Use existing hooks        ← Start here (90% of cases)
2. Create custom hooks        ← When existing hooks don't work
3. Use overrides              ← Last resort (avoid if possible)
```

#### **Approach 1: Use Existing Hooks** (Preferred - 90%)
```php
public function install()
{
    return parent::install()
        && $this->registerHook('displayHeader')
        && $this->registerHook('actionObjectOrderAddAfter');
}

public function hookDisplayHeader($params)
{
    $this->context->controller->addCSS($this->_path.'views/css/module.css');
}
```

#### **Approach 2: Custom Hooks** (When needed - 8%)
When existing hooks don't satisfy requirements:

**Requirements for Custom Hooks:**
- ✅ Hook can be useful for other modules
- ✅ Increases overall system flexibility
- ✅ Documented in module README for user to place in core

**Example:**
```php
// In your module's README.md
## Manual Installation Step
Add this hook to `classes/Cart.php` after line 123:

Hook::exec('actionAfterCartValidation', array('cart' => $this));

This allows modules to extend cart validation functionality.
```

**User must manually add hook to core file** - Document clearly!

#### **Approach 3: Overrides** (Last resort - 2%)
**Only when**:
- No hooks work
- Custom hooks aren't feasible
- No other solution exists

**⚠️ Warning**: Overrides can conflict with other modules' overrides!

```php
// override/classes/Cart.php
class Cart extends CartCore
{
    public function myCustomMethod()
    {
        // Minimal changes only
        return parent::myCustomMethod();
    }
}
```

After creating override: Delete `cache/class_index.php`

---

## 🗂️ Skill Components

This skill is divided into specialized guides for targeted learning:

### 📐 **Architecture & File Structure**
→ See [architecture.md](./architecture.md)
- Complete module directory structure
- File organization best practices
- Mandatory files (LICENSE, README, CHANGELOG)
- Reference: `modules/hotelreservationsystem/`

### 📝 **Coding Standards & Conventions**
→ See [coding-conventions.md](./coding-conventions.md)
- Prefix rules (qlo*, module-specific prefixes)
- Variable naming (camelCase, constants)
- Yoda conditions
- License headers
- No HTML in PHP files rule

### 🎣 **Hooks System Integration**
→ See [hooks-system.md](./hooks-system.md)
- Hook registration and implementation
- Common hooks reference
- Custom hook creation process
- Hook vs Override decision tree

### 💾 **Models & Database Operations**
→ See [models-repositories.md](./models-repositories.md)
- ObjectModel class creation
- Database table definitions
- CRUD operations
- Multilang/multishop support
- Query security (pSQL, type casting)

### 🎮 **Controllers (Admin & Front)**
→ See [controllers.md](./controllers.md)
- AdminController patterns
- FrontController patterns
- ModuleAdminController
- ModuleFrontController
- Form handling and validation
- Employee permissions

### 🎨 **Templates, CSS & JavaScript**
→ See [templates-views.md](./templates-views.md)
- Smarty template structure
- CSS/JS file organization
- File naming (qlo_*.css, qlo_*.js)
- No HTML in PHP rule
- No CSS/JS in TPL files rule
- Asset loading in hooks

### 🗄️ **Database Operations & Queries**
→ See [database-operations.md](./database-operations.md)
- Table creation patterns
- Query security practices
- Type casting all variables
- Using HotelReservationSystemDb pattern
- Hotel permission checks

### 🔒 **Security & Validation**
→ See [security-validation.md](./security-validation.md)
- SQL injection prevention
- XSS protection (escape:'html':'UTF-8')
- CSRF tokens for forms
- Front ajax validation tokens
- Input validation before save
- Employee permission checks

### 🚀 **Deployment & Distribution**

### 🚀 **Deployment & Upgrades**
→ See [deployment.md](./deployment.md)
- Install/uninstall process
- Upgrade script creation
- Configuration management
- Module versioning
- Git project rules

---

## 📖 Quick Reference

### Module Creation Checklist
- [ ] Choose module name and calculate prefix
- [ ] Create module folder structure
- [ ] Add main module class file
- [ ] Add mandatory files (LICENSE.md, README.md, CHANGELOG.txt, index.php)
- [ ] Define constants in define.php
- [ ] Create database classes (if needed)
- [ ] Implement install/uninstall
- [ ] Register hooks (hooks-first!)
- [ ] Create controllers (if needed)
- [ ] Create templates
- [ ] Add CSS/JS files (qlo_* naming)
- [ ] Implement security measures
- [ ] Add multilanguage support
- [ ] Test thoroughly
- [ ] Create upgrade scripts
- [ ] Push to Git daily

### Prefix Calculation Example
**Module Name**: QloApps Exit Popup
**Short Form**: qep
**Prefixes**:
- Folder/Main File/Main Class: `qlo` → `qloexitpopup`, `QloExitPopup`
- Tables: `qep_` → `qlo_qep_popup_content`
- Classes: `Qep` → `QepPopupContent`
- Constants/Config: `QEP_` → `QEP_CUSTOM_STATUS`
- JavaScript: `qep` → `qep_custom_status`

### Security Quick Checks
```php
// ✅ Always do this
$id = (int)Tools::getValue('id');
$name = pSQL(Tools::getValue('name'));
$query = 'SELECT * FROM `'._DB_PREFIX_.'table` WHERE id = '.$id;

// In templates
{$variable|escape:'html':'UTF-8'}

// Front ajax
if (!$this->isTokenValid()) {
    die(json_encode(['success' => false]));
}
```

---

## 🎯 Development Workflow

### Phase 1: Planning (15 minutes)
1. Define module purpose and features
2. Calculate module prefix from name
3. Identify required hooks
4. Design database schema (get mentor approval!)
5. Plan multilang requirements

### Phase 2: Setup (15 minutes)
1. Create module folder structure
2. Create main module file
3. Add all mandatory files
4. Setup Git repository (push daily!)
5. Create define.php with constants

### Phase 3: Database (30 minutes if needed)
1. Create database class (PrefixModuleNameDb.php)
2. Create ObjectModel classes
3. Implement install SQL
4. Test table creation
5. Add multilang field support

### Phase 4: Core Development (60-120 minutes)
1. Implement install/uninstall
2. Register and implement hooks
3. Create admin controllers (if needed)
4. Create front controllers (if needed)
5. Build templates with proper escaping
6. Add CSS/JS files
7. Implement security measures
8. Add employee permission checks

### Phase 5: Testing (30 minutes)
1. Test all features thoroughly
2. Test with different languages
3. Test with different employee permissions
4. Fix any issues found

### Phase 6: Documentation & Deployment (20 minutes)
1. Update README.md
2. Update CHANGELOG.txt
3. Document any manual steps (custom hooks placement)
4. Create upgrade script template
5. Final Git push
6. Module entry on api.qloapps.com

---

## 💡 Module Template Example

**Reference**: `modules/hotelreservationsystem/hotelreservationsystem.php`

```php
<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once 'define.php';

class QloYourModule extends Module
{
    public function __construct()
    {
        $this->name = 'qloyourmodule';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Your Module Name');
        $this->description = $this->l('Module description');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        // Create tables using Db class
        include_once dirname(__FILE__).'/classes/QymModuleDb.php';
        $objModuleDb = new QymModuleDb();

        return parent::install()
            && $objModuleDb->createTables()
            && $objModuleDb->installDefaultData()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        include_once dirname(__FILE__).'/classes/QymModuleDb.php';
        $objModuleDb = new QymModuleDb();

        return $objModuleDb->dropTables()
            && $objModuleDb->deleteConfigurations()
            && parent::uninstall();
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path.'views/css/qlo_module_front.css');
        $this->context->controller->addJS($this->_path.'views/js/qlo_module_front.js');
    }
}
```

---

## ⚠️ Common Pitfalls & Solutions

### ❌ Pitfall 1: Modifying Core Files Directly
**What goes wrong**: Module becomes incompatible with QloApps updates, conflicts with other modules

**✅ Correct approach**: Use hooks! If hooks don't work, create custom hook and document placement in README

### ❌ Pitfall 2: HTML in PHP Files
**What goes wrong**: Violates coding standards, difficult to maintain

**✅ Correct approach**: All HTML in .tpl files, load via templates

### ❌ Pitfall 3: Forgetting Prefix Rules
**What goes wrong**: Naming conflicts, fails coding review

**✅ Correct approach**: Follow strict prefix rules in [coding-conventions.md](./coding-conventions.md)

### ❌ Pitfall 4: No SQL Escaping
**What goes wrong**: SQL injection vulnerabilities

**✅ Correct approach**:
```php
$id = (int)Tools::getValue('id');  // Type cast
$name = pSQL(Tools::getValue('name'));  // String escape
```

### ❌ Pitfall 5: Skipping Permission Checks
**What goes wrong**: Unauthorized access to restricted data

**✅ Correct approach**:
```php
// In admin controllers
if (!$this->tabAccess['edit']) {
    $this->errors[] = $this->l('You do not have permission');
    return;
}

// For hotel-specific data
HotelBranchInformation::addHotelRestriction($this->table, 'hbi');
```

---

## 🔍 Validation Checklist

Before completing module development:

### Functionality
- [ ] Module installs without errors
- [ ] Module uninstalls cleanly (removes all data)
- [ ] All features work as expected
- [ ] No errors in QloApps logs

### Code Quality
- [ ] No HTML in PHP files
- [ ] No CSS/JS in TPL files
- [ ] All variables in camelCase
- [ ] Prefix rules followed correctly
- [ ] Yoda conditions applied where suitable
- [ ] Comments added for complex logic
- [ ] License headers on all files
- [ ] index.php in every folder

### Security
- [ ] All inputs validated and type-cast
- [ ] SQL queries use pSQL() for strings
- [ ] Templates use {$var|escape:'html':'UTF-8'}
- [ ] Front ajax uses validation tokens
- [ ] Employee permissions checked
- [ ] Hotel access permissions checked (if applicable)

### Database
- [ ] Table names use _DB_PREFIX_ and module prefix
- [ ] Queries only select needed fields (no SELECT *)
- [ ] All query variables properly type-cast
- [ ] Tables created in install, dropped in uninstall
- [ ] Multilang data handled for new languages

### Multilanguage
- [ ] All user-facing text uses $this->l()
- [ ] Multilang fields supported in forms
- [ ] Tested with multiple languages
- [ ] Mail templates created for all languages

### Files & Structure
- [ ] LICENSE.md present (OSL-3.0)
- [ ] README.md complete with all documentation
- [ ] CHANGELOG.txt updated
- [ ] config.xml present
- [ ] logo.png and logo.gif present
- [ ] All file/folder names follow conventions

### Testing
- [ ] Tested with different employee roles
- [ ] Tested with multilanguage
- [ ] Tested with multishop (if applicable)
- [ ] All edge cases covered

### Deployment
- [ ] Upgrade script template created
- [ ] Git repository updated daily
- [ ] Module entry on api.qloapps.com
- [ ] ERP page updated

---

## 📚 Reference Files

Study these existing implementations:

| File | Purpose | Key Concepts |
|------|---------|--------------|
| `modules/hotelreservationsystem/hotelreservationsystem.php` | Main module class | Structure, hooks, install/uninstall |
| `modules/hotelreservationsystem/define.php` | Module constants | Constant definition pattern |
| `modules/hotelreservationsystem/classes/` | ObjectModel classes | Database models, CRUD operations |
| `modules/hotelreservationsystem/classes/HotelReservationSystemDb.php` | Database class | Table creation, install/uninstall |
| `modules/hotelreservationsystem/controllers/admin/` | Admin controllers | CRUD, forms, permissions |

---

## 🔗 Related Skills

- [qloapps-hook-development](../qloapps-hook-development/skill.md) - Deep dive into hooks
- [qloapps-objectmodel-development](../qloapps-objectmodel-development/skill.md) - Database models
- [qloapps-admin-controller-development](../qloapps-admin-controller-development/skill.md) - Admin CRUD
- [qloapps-security-audit](../qloapps-security-audit/skill.md) - Security best practices
- [qloapps-testing](../qloapps-testing/skill.md) - Testing strategies

---

## 🐛 Troubleshooting

### Issue 1: Module Won't Install
**Symptoms**: Error during installation

**Common Causes**:
- Database table creation fails
- Hook registration fails
- Missing parent::install() call

**Solution**:
```php
public function install()
{
    // Always call parent first
    if (!parent::install()) {
        return false;
    }

    // Then your custom logic
    // Return false if anything fails
}
```

### Issue 2: Hooks Not Firing
**Symptoms**: Hook methods not executing

**Solution**:
1. Check hook is registered in install()
2. Verify method name matches: `hook{HookName}`
3. Re-install module
4. Check QloApps hook exists in `classes/Hook.php`

### Issue 3: Override Not Working
**Symptoms**: Override class not loading

**Solution**:
1. Delete `cache/class_index.php`
2. Verify class extends {ClassName}Core
3. Check file is in correct override folder

---

## � Specific Tasks

**Core Development**
- [Module Architecture](./architecture.md) - Folder structure, required files, database setup
- [Coding Conventions](./coding-conventions.md) - Prefix rules, naming standards, file organization
- [Hooks System](./hooks-system.md) - Integration patterns, existing hooks, custom hooks

**Data & Logic**
- [Models & Repositories](./models-repositories.md) - ObjectModel pattern, CRUD operations
- [Controllers](./controllers.md) - Admin/Front controllers, AJAX, form handling
- [Database Operations](./database-operations.md) - SQL queries, security, hotel restrictions

**Frontend & Security**
- [Templates & Views](./templates-views.md) - Smarty templates, CSS/JS, asset loading
- [Security & Validation](./security-validation.md) - Input validation, CSRF tokens, XSS prevention

**Release Management**
- [Deployment](./deployment.md) - Install/uninstall, upgrades, versioning, distribution

---

## �📖 Additional Resources

- **QloApps DevDocs**: https://devdocs.qloapps.com/
- **Module Git Project Rules**: Internal Webkul documentation

---

## 📝 Version History

### 1.0.0 (2026-02-26)
- Initial version
- Module-only development principle
- Hooks-first architecture
- Complete coding standards from hotelreservationsystem
- Security best practices
- Multilang and permission support

---

## 💬 Next Steps

1. **Start with architecture** → Read [architecture.md](./architecture.md)
2. **Learn prefix rules** → Study [coding-conventions.md](./coding-conventions.md)
3. **Master hooks** → Review [hooks-system.md](./hooks-system.md)
4. **Build your first module** → Follow this guide step-by-step
5. **Validate thoroughly** → Use checklist before deployment

**Ready to build? Start with Phase 1: Planning!** 🚀
