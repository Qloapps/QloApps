<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

ob_start();

// Check PHP version
if (version_compare(preg_replace('/[^0-9.]/', '', PHP_VERSION), '5.2', '<')) {
    die('You need at least PHP 5.2 to run PrestaShop. Your current PHP version is '.PHP_VERSION);
}

// we check if theses constants are defined
// in order to use init.php in upgrade.php script
if (!defined('__PS_BASE_URI__')) {
    define('__PS_BASE_URI__', substr($_SERVER['REQUEST_URI'], 0, -1 * (strlen($_SERVER['REQUEST_URI']) - strrpos($_SERVER['REQUEST_URI'], '/')) - strlen(substr(dirname($_SERVER['REQUEST_URI']), strrpos(dirname($_SERVER['REQUEST_URI']), '/') + 1))));
}

if (!defined('_PS_CORE_DIR_')) {
    define('_PS_CORE_DIR_', realpath(dirname(__FILE__).'/..'));
}

if (!defined('_THEME_NAME_')) {
    define('_THEME_NAME_', 'hotel-theme');
}


require_once(_PS_CORE_DIR_.'/config/defines.inc.php');
require_once(_PS_CORE_DIR_.'/config/autoload.php');
require_once(_PS_CORE_DIR_.'/config/bootstrap.php');
require_once(_PS_CORE_DIR_.'/config/defines_uri.inc.php');

// Generate common constants
define('PS_INSTALLATION_IN_PROGRESS', true);
define('_PS_INSTALL_PATH_', dirname(__FILE__).'/');
define('_PS_INSTALL_DATA_PATH_', _PS_INSTALL_PATH_.'data/');
define('_PS_INSTALL_CONTROLLERS_PATH_', _PS_INSTALL_PATH_.'controllers/');
define('_PS_INSTALL_MODELS_PATH_', _PS_INSTALL_PATH_.'models/');
define('_PS_INSTALL_LANGS_PATH_', _PS_INSTALL_PATH_.'langs/');
define('_PS_INSTALL_FIXTURES_PATH_', _PS_INSTALL_PATH_.'fixtures/');

require_once(_PS_INSTALL_PATH_.'install_version.php');

// PrestaShop autoload is used to load some helpfull classes like Tools.
// Add classes used by installer bellow.

require_once(_PS_CORE_DIR_.'/config/alias.php');
require_once(_PS_INSTALL_PATH_.'classes/exception.php');
require_once(_PS_INSTALL_PATH_.'classes/languages.php');
require_once(_PS_INSTALL_PATH_.'classes/language.php');
require_once(_PS_INSTALL_PATH_.'classes/model.php');
require_once(_PS_INSTALL_PATH_.'classes/session.php');
require_once(_PS_INSTALL_PATH_.'classes/sqlLoader.php');
require_once(_PS_INSTALL_PATH_.'classes/xmlLoader.php');
require_once(_PS_INSTALL_PATH_.'classes/simplexml.php');

@set_time_limit(0);
if (!@ini_get('date.timezone')) {
    @date_default_timezone_set('UTC');
}

// Some hosting still have magic_quotes_runtime configured
ini_set('magic_quotes_runtime', 0);

// Try to improve memory limit if it's under 64M
$current_memory_limit = psinstall_get_memory_limit();
if ($current_memory_limit > 0 && $current_memory_limit < psinstall_get_octets('64M')) {
    ini_set('memory_limit', '64M');
}

function psinstall_get_octets($option)
{
    if (preg_match('/[0-9]+k/i', $option)) {
        return 1024 * (int)$option;
    }

    if (preg_match('/[0-9]+m/i', $option)) {
        return 1024 * 1024 * (int)$option;
    }

    if (preg_match('/[0-9]+g/i', $option)) {
        return 1024 * 1024 * 1024 * (int)$option;
    }

    return $option;
}

function psinstall_get_memory_limit()
{
    $memory_limit = @ini_get('memory_limit');

    if (preg_match('/[0-9]+k/i', $memory_limit)) {
        return 1024 * (int)$memory_limit;
    }

    if (preg_match('/[0-9]+m/i', $memory_limit)) {
        return 1024 * 1024 * (int)$memory_limit;
    }

    if (preg_match('/[0-9]+g/i', $memory_limit)) {
        return 1024 * 1024 * 1024 * (int)$memory_limit;
    }

    return $memory_limit;
}
