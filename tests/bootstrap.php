<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

define('_PS_IN_TEST_', true);
define('_PS_ROOT_DIR_', dirname(__DIR__));

// Load all QloApps path constants (_PS_CLASS_DIR_, _PS_CONFIG_DIR_, etc.)
require_once dirname(__DIR__) . '/config/defines.inc.php';

// DB constants not in defines.inc.php (normally set by settings.inc.php)
if (!defined('_DB_PREFIX_'))        define('_DB_PREFIX_', 'ps_');
if (!defined('_PS_USE_SQL_SLAVE_')) define('_PS_USE_SQL_SLAVE_', 1);

// Composer autoloader (PHPUnit and its dependencies)
require dirname(__DIR__) . '/vendor/autoload.php';

// Stubs for classes that require a live DB, config secrets, or heavy dependencies.
// Registered BEFORE the QloApps autoloader so stubs shadow the real classes.
require_once __DIR__ . '/Unit/stubs/CoreStubs.php';

// QloApps class autoloader — loads real classes not covered by stubs above.
// pSQL/bqSQL are defined here via config/alias.php.
require_once dirname(__DIR__) . '/config/autoload.php';
