# AGENTS.md - QloApps v1.6.1 Development Guide

## Project Overview

QloApps is an open-source hotel reservation system and booking engine built on PHP. It enables users to launch hotel booking websites and manage online reservations.

## Skills
A skill is a set of local instructions to follow stored in a `SKILL.md` file. Add skills here to make them available in this session.

All skill paths in this file are defined relative to the current project root unless stated otherwise. Resolve them against the active workspace absolute path before loading a skill.

Supporting skills may exist anywhere inside the current project. When a supporting skill is referenced by name, scan the current project for its `SKILL.md` file before loading it.

### Available skills
- e2e-tests: Autonomous Playwright QA workflow with strict `playwright-cli` usage. (file: {project_root}/skills/e2e-tests/SKILL.md)
- qloapps-module-development: QloApps module development workflow. (file: {project_root}/skills/qloapps-module-development/SKILL.md)

### Supporting Installed Skills
- playwright-cli: Supporting browser interaction and selector discovery skill. Discover it by scanning the current project for `**/playwright-cli/SKILL.md`.

### Skill Loading Rules

On every request, first check whether the task matches one of the available local skills before proceeding without a skill.

Use these rules:

1. Determine `{project_root}` from the active workspace folder and resolve every skill file to that absolute path before loading it.
2. For any e2e testing request, load `{project_root}/skills/e2e-tests/SKILL.md` first.
3. Treat these requests as e2e testing by default: Playwright tests, browser automation, UI testing, login flow tests, checkout flow tests, reservation flow tests, regression tests, selector discovery, flaky UI test fixes, screenshot-based validation, and end-to-end test debugging.
4. If the e2e task requires opening pages, discovering selectors, filling forms, taking snapshots, or any direct browser interaction, scan the current project for `**/playwright-cli/SKILL.md` and load the discovered `playwright-cli` skill.
5. If multiple `playwright-cli` skill files are found, prefer the match closest to the project root.
6. For QloApps module creation, module structure changes, hook registration, module controllers, module templates, or module packaging, load `{project_root}/skills/qloapps-module-development/SKILL.md`.
7. If multiple skills apply, load the domain skill first, then the supporting skill. For e2e browser work, that means `e2e-tests` first and the discovered `playwright-cli` skill second.
8. Do not guess selectors or browser steps for e2e work. When an e2e skill is loaded, follow its workflow exactly.
9. If a request does not match any listed skill, continue with normal repository instructions.

### E2E Priority Rule

When a request mentions e2e, Playwright, browser tests, UI automation, test creation, or test debugging, automatically prefer the `e2e-tests` skill over general instructions.

If the task involves interacting with the application in a browser, a discovered `playwright-cli` skill is mandatory support context for that task when present in the project scan.

## Technology Stack

- **Language**: PHP 5.6+ to PHP 7.4
- **Database**: MySQL 5.1+ to 5.7
- **Template Engine**: Smarty
- **Architecture**: MVC (Model-View-Controller) based on PrestaShop framework
- **License**: OSL-3.0 (Core), AFL-3.0 (Modules)

## Directory Structure

```
QloApps161/
├── adminhtl/           # Admin panel files
│   ├── tabs/           # Admin controller tabs
│   └── themes/         # Admin themes
├── cache/              # Cache storage
├── classes/            # Core PHP classes (Models)
│   ├── ObjectModel.php # Base model class
│   ├── Context.php     # Application context
│   ├── Tools.php       # Utility functions
│   └── ...
├── config/             # Configuration files
│   ├── config.inc.php  # Main configuration
│   ├── defines.inc.php # Constants definitions
│   └── smarty.*.inc.php # Smarty configurations
├── controllers/        # Controllers
│   ├── admin/          # Admin controllers
│   └── front/          # Frontend controllers
├── css/                # Stylesheets
├── docs/               # Documentation
├── img/                # Images
├── installdev/         # Installation files
├── js/                 # JavaScript files
├── localization/       # Localization files
├── log/                # Log files
├── mails/              # Email templates
├── modules/            # Modules/Addons
├── override/           # Class overrides
├── pdf/                # PDF templates
├── tests/              # Unit tests
├── themes/             # Frontend themes
│   └── hotel-reservation-theme/
├── tools/              # Utility libraries
│   ├── smarty/         # Smarty template engine
│   └── tcpdf/          # PDF library
├── translations/       # Translation files
├── upload/             # Uploaded files
├── webservice/         # API/WebService
├── index.php           # Application entry point
├── init.php            # Initialization
├── header.php          # Header include
└── footer.php          # Footer include
```

## Key Classes

- **ObjectModel**: Base class for all models (`classes/ObjectModel.php`)
- **Context**: Application context singleton (`classes/Context.php`)
- **Tools**: Utility helper class (`classes/Tools.php`)
- **Dispatcher**: URL routing (`classes/Dispatcher.php`)
- **Controller**: Base controller class
- **Module**: Base module class for addons

## Coding Standards

### PHP Code Style

- Follow PSR-2 coding standards
- Use 4 spaces for indentation (no tabs)
- Opening PHP tag: `<?php`
- Class names: PascalCase (e.g., `CustomerModel`)
- Method names: camelCase (e.g., `getCustomerById()`)
- Constants: UPPER_SNAKE_CASE (e.g., `TYPE_INT`)
- Add license header at the top of PHP files

### File Naming

- Class files: Match class name (e.g., `Customer.php` for `Customer` class)
- Controllers: `{Name}Controller.php`
- Module files: `{modulename}.php`

### Database

- Table prefix: Configurable (default `ps_`)
- Use ObjectModel classes for database operations
- Use `Db::getInstance()` for direct queries when needed

## Module Development

Modules are located in `/modules/`. Each module follows this structure:

```
modules/{modulename}/
├── {modulename}.php    # Main module class
├── views/
│   ├── templates/      # Smarty templates
│   ├── css/            # Module CSS
│   └── js/             # Module JavaScript
├── classes/            # Module-specific classes
├── translations/       # Module translations
└── logo.png            # Module icon
```

### Creating a Module

1. Create directory in `/modules/`
2. Create main PHP file extending `Module` class
3. Implement required methods: `install()`, `uninstall()`
4. Register hooks for integration
5. Create templates in `views/templates/`

## Hooks System

QloApps uses a hook system for extensibility:

```php
// Register a hook
$this->registerHook('displayHeader');

// Implement hook method
public function hookDisplayHeader($params)
{
    // Your code
}

// Call hooks in templates
{hook h='displayHeader'}
```

## Template Development

- Smarty templates use `.tpl` extension
- Main theme: `themes/hotel-reservation-theme/`
- Compile/cache directories: `cache/smarty/`

### Smarty Syntax

```smarty
{$variable}                    <!-- Variable output -->
{l s='String to translate'}    <!-- Translatable string -->
{if condition}...{/if}         <!-- Conditionals -->
{foreach $items as $item}...{/foreach} <!-- Loops -->
{include file='path/to/file.tpl'}     <!-- Include -->
{hook h='hookName'}            <!-- Hook call -->
```

## Running Tests

```bash
cd tests
composer install
../vendor/bin/phpunit
```

## Debugging

- Enable debug mode in `config/config.inc.php`: `_PS_MODE_DEV_ = true`
- Check logs in `/log/` directory
- Use `Tools::d()` and `Tools::p()` for debugging

## Configuration

Key configuration files:

- `config/config.inc.php`: Main configuration
- `config/defines.inc.php`: Path and constant definitions
- `config/settings.inc.php`: Database credentials (auto-generated)

## Common Commands

### Clear Cache

```bash
rm -rf cache/smarty/compile/*
rm -rf cache/smarty/cache/*
```

### Install Dependencies

```bash
composer install
```

## Development Workflow

1. Create feature branch from main
2. Make changes following coding standards
3. Test changes thoroughly
4. Run tests if applicable
5. Submit pull request with clear description

## Version Information

- Current Version: 1.6.1
- Repository: https://github.com/webkul/hotelcommerce

## Support

- Documentation: https://qloapps.com/qlo-reservation-system
- Forum: https://forums.qloapps.com/
- Email: support@qloapps.com
