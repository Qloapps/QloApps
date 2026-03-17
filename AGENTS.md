# AGENTS.md

## Overview

This document defines development guidelines for AI agents contributing to the QloApps codebase. QloApps is a hotel booking and property management platform built on PHP with an MVC architecture. These guidelines ensure code quality, security, and architectural consistency.

## Platform Architecture

QloApps follows a modular MVC architecture with the following core principles:

### MVC Pattern
- **Models**: Extend `ObjectModel` for database-backed entities
- **Views**: Use Smarty templates; avoid inline HTML in controllers
- **Controllers**: Extend `FrontController`, `AdminController`, or module variants
- Separate business logic from presentation logic

### Core Technologies
- **PHP**: 8.1 - 8.4
- **Database**: MySQL 5.7 - 8.4, MariaDB 10.x
- **Template Engine**: Smarty 3.x
- **Caching**: File-based and Redis-compatible
- **Web Server**: Apache 2.4+, Nginx 1.18+

### Database Layer
- Use `Db::getInstance()` for all database operations
- Always use table prefix `_DB_PREFIX_`
- Escape values with `pSQL()` for strings and `(int)` for IDs
- Respect multi-shop context with `Shop::addSqlRestriction()`
- Use `bqSQL()` for table and column names

## Module Development Guidelines

### Module Structure
All extensions must be module-based and follow this structure:

```
modules/{modulename}/
  ├── {modulename}.php        # Main module class
  ├── config.xml              # Module metadata
  ├── classes/                # Module-specific models
  ├── controllers/
  │   ├── admin/
  │   └── front/
  ├── views/
  │   ├── templates/
  │   ├── js/
  │   └── css/
  ├── translations/
  └── upgrade/
```

### Module Lifecycle
- Implement `install()` for setup and hook registration
- Implement `uninstall()` for complete cleanup
- Create `upgrade_module_{version}()` for version upgrades
- Handle database schema changes with migration scripts
- Support multi-shop and multi-language configurations

### Module Configuration
- Use the `Configuration` class for settings
- Prefix all configuration keys with module name
- Validate all configuration inputs
- Provide admin configuration pages

## Override & Extension Rules

### Override System
- Place overrides in `override/{classes|controllers|modules}/`
- Override classes extend core class with "Core" suffix
- Delete `cache/class_index.php` after adding overrides
- Document override purpose and impact

**Example:**
```php
class Cart extends CartCore {
    // Override methods here
}
```

### Core Modification Rules
- Never modify core files directly
- Use the override system for extending core classes
- Use hooks for injecting custom functionality
- Maintain backward compatibility

## Hook Usage Guidelines

### Hook Registration
- Register hooks in module `install()` method
- Unregister in `uninstall()` method
- Document expected parameters for custom hooks

### Hook Implementation
- Keep hook handlers lightweight (< 50ms execution)
- Avoid database writes in display hooks
- Use action hooks for data modifications
- Cache hook output when appropriate

### Hook Naming
- Display hooks: `display{Location}` (e.g., `displayHeader`)
- Action hooks: `action{Event}` (e.g., `actionOrderStatusUpdate`)
- Use camelCase for hook names

## Code Standards

### Coding Style
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- One class per file; filename matches class name
- Maximum function length: 50 lines
- Maximum file length: 1000 lines

### Documentation
- Add PHPDoc blocks to all classes, methods, and properties
- Document parameters, return types, and exceptions
- Include `@since` tags for new features
- Maintain changelog for all modifications

### Code Quality
- No debug statements (`var_dump()`, `print_r()`, `die()`)
- No dead code or commented-out blocks
- Replace magic numbers with named constants
- Handle all error conditions appropriately

## Security Guidelines

### Input Validation
- Validate all user input at controller level
- Use `Validate::is*()` methods for type checking
- Sanitize input before database operations
- Implement CSRF protection for state-changing operations

### SQL Injection Prevention
- Use `pSQL()` for string values in SQL queries
- Cast all IDs to integer: `(int)$id`
- Prefer `ObjectModel` methods over raw queries
- Never concatenate user input into SQL

### XSS Prevention
- Escape output with `Tools::safeOutput()` or `htmlspecialchars()`
- Use Smarty's `|escape:'html'` modifier in templates
- Validate and sanitize rich text content

### Authentication & Authorization
- Check `$this->tabAccess` in admin controllers
- Verify permissions before sensitive operations
- Implement rate limiting for authentication
- Use secure session management

### Sensitive Data
- Never log passwords, tokens, or PII
- Use environment variables for secrets
- Comply with PCI DSS for payment data
- Encrypt sensitive data at rest

## Logging & Error Handling

### Logging
Use the platform logging system for all events:

```php
PrestaShopLogger::addLog(
    'Operation completed: #'.$id,
    1, // severity: 1=info, 2=warning, 3=error
    null,
    'EntityType',
    $entityId
);
```

### Logging Levels
- **ERROR**: System errors requiring immediate attention
- **WARNING**: Unexpected conditions
- **INFO**: Significant business events
- **DEBUG**: Detailed diagnostic information

### Audit Logging
- Log all financial transactions
- Log booking state changes
- Log administrative actions
- Include user attribution

### Error Handling
- Handle exceptions appropriately
- Provide meaningful error messages
- Never expose sensitive information in errors
- Log errors with sufficient context

## Performance Guidelines

### Query Optimization
- Avoid N+1 queries
- Use `JOIN` instead of multiple queries in loops
- Add indexes for frequently queried columns
- Use `LIMIT` clause for result sets
- Analyze queries with `EXPLAIN`

### Caching
- Cache expensive computations and queries
- Invalidate cache on data modification
- Support multiple cache backends (file, Redis)
- Respect cache TTL configurations

### Resource Management
- Limit memory consumption per request
- Implement pagination for large datasets
- Use generators for large result sets
- Avoid loading entire collections into memory

### Database Design
- Normalize schema to 3NF minimum
- Use appropriate data types
- Define foreign key constraints
- Create indexes on foreign keys and search columns

## Things to Avoid

### Never Modify
- Core system files without override mechanism
- Historical financial records
- Completed transaction totals
- Audit trail entries

### Never Introduce
- Hardcoded credentials or secrets
- Direct database access bypassing abstraction layer
- Unbounded loops or recursion
- Breaking changes to public APIs

### Never Bypass
- Input validation
- Authentication and authorization checks
- CSRF protection
- SQL injection prevention
- XSS prevention mechanisms

### Never Use
- `eval()` or dynamic code execution
- `mysql_*` functions (use PDO/mysqli)
- Direct `$_GET`, `$_POST`, `$_COOKIE` access (use `Tools::getValue()`)
- Deprecated PHP features
- Unescaped output to browser

### Critical Operations
- Never modify booking totals without recalculation
- Never alter pricing or tax logic without validation
- Never oversell room inventory
- Never modify payment processing without approval
- Always use row-level locking for inventory updates
- Always validate availability before booking confirmation
- Always maintain backward compatibility
