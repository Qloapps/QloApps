<?php
/**
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/* Debug only */
if (!defined('_PS_MODE_DEV_')) {
    define('_PS_MODE_DEV_', false);
}
/* Compatibility warning */
define('_PS_DISPLAY_COMPATIBILITY_WARNING_', false);
if (_PS_MODE_DEV_ === true) {
    @ini_set('display_errors', 'on');
    @error_reporting(E_ALL | E_STRICT);
    define('_PS_DEBUG_SQL_', true);
} else {
    @ini_set('display_errors', 'off');
    define('_PS_DEBUG_SQL_', false);
}

define('_PS_DEBUG_PROFILING_', false);
define('_PS_MODE_DEMO_', false);

$currentDir = dirname(__FILE__);

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

if (!defined('_PS_VERSION_') && (getenv('_PS_VERSION_') || getenv('REDIRECT__PS_VERSION_'))) {
    define('_PS_VERSION_', getenv('_PS_VERSION_') ? getenv('_PS_VERSION_') : getenv('REDIRECT__PS_VERSION_'));
}

if (!defined('_PS_HOST_MODE_') && (getenv('_PS_HOST_MODE_') || getenv('REDIRECT__PS_HOST_MODE_'))) {
    define('_PS_HOST_MODE_', getenv('_PS_HOST_MODE_') ? getenv('_PS_HOST_MODE_') : getenv('REDIRECT__PS_HOST_MODE_'));
}

if (!defined('_PS_ROOT_DIR_') && (getenv('_PS_ROOT_DIR_') || getenv('REDIRECT__PS_ROOT_DIR_'))) {
    define('_PS_ROOT_DIR_', getenv('_PS_ROOT_DIR_') ? getenv('_PS_ROOT_DIR_') : getenv('REDIRECT__PS_ROOT_DIR_'));
}

/* Directories */
if (!defined('_PS_ROOT_DIR_')) {
    define('_PS_ROOT_DIR_', realpath($currentDir.'/..'));
}

if (!defined('_PS_CORE_DIR_')) {
    define('_PS_CORE_DIR_', realpath($currentDir.'/..'));
}

define('_PS_ALL_THEMES_DIR_',        _PS_ROOT_DIR_.'/themes/');
/* BO THEMES */
if (defined('_PS_ADMIN_DIR_')) {
    define('_PS_BO_ALL_THEMES_DIR_', _PS_ADMIN_DIR_.'/themes/');
}
if (!defined('_PS_CACHE_DIR_')) {
    define('_PS_CACHE_DIR_',             _PS_ROOT_DIR_.'/cache/');
}
define('_PS_CONFIG_DIR_',             _PS_CORE_DIR_.'/config/');
define('_PS_CUSTOM_CONFIG_FILE_',     _PS_CONFIG_DIR_.'settings_custom.inc.php');
define('_PS_CLASS_DIR_',             _PS_CORE_DIR_.'/classes/');
if (!defined('_PS_DOWNLOAD_DIR_')) {
    define('_PS_DOWNLOAD_DIR_',          _PS_ROOT_DIR_.'/download/');
}
define('_PS_MAIL_DIR_',              _PS_CORE_DIR_.'/mails/');
if (!defined('_PS_MODULE_DIR_')) {
    define('_PS_MODULE_DIR_',        _PS_ROOT_DIR_.'/modules/');
}
if (!defined('_PS_OVERRIDE_DIR_')) {
    define('_PS_OVERRIDE_DIR_',          _PS_ROOT_DIR_.'/override/');
}
define('_PS_PDF_DIR_',               _PS_CORE_DIR_.'/pdf/');
define('_PS_TRANSLATIONS_DIR_',      _PS_ROOT_DIR_.'/translations/');
if (!defined('_PS_UPLOAD_DIR_')) {
    define('_PS_UPLOAD_DIR_',             _PS_ROOT_DIR_.'/upload/');
}
define('_PS_CONTROLLER_DIR_',        _PS_CORE_DIR_.'/controllers/');
define('_PS_ADMIN_CONTROLLER_DIR_',  _PS_CORE_DIR_.'/controllers/admin/');
define('_PS_FRONT_CONTROLLER_DIR_',  _PS_CORE_DIR_.'/controllers/front/');

define('_PS_TOOL_DIR_',              _PS_CORE_DIR_.'/tools/');
if (!defined('_PS_GEOIP_DIR_')) {
    define('_PS_GEOIP_DIR_',             _PS_TOOL_DIR_.'geoip/');
}
if (!defined('_PS_GEOIP_CITY_FILE_')) {
    define('_PS_GEOIP_CITY_FILE_',       'GeoLiteCity.dat');
}
define('_PS_SWIFT_DIR_',             _PS_TOOL_DIR_.'swift/');
define('_PS_TAASC_PATH_',            _PS_TOOL_DIR_.'taasc/');
define('_PS_TCPDF_PATH_',            _PS_TOOL_DIR_.'tcpdf/');

if (!defined('_PS_IMG_DIR_')) {
    define('_PS_IMG_DIR_',               _PS_ROOT_DIR_.'/img/');
}
if (!defined('_PS_HOST_MODE_')) {
    define('_PS_CORE_IMG_DIR_',      _PS_CORE_DIR_.'/img/');
} else {
    define('_PS_CORE_IMG_DIR_',      _PS_ROOT_DIR_.'/img/');
}

define('_PS_CAT_IMG_DIR_',           _PS_IMG_DIR_.'c/');
define('_PS_COL_IMG_DIR_',             _PS_IMG_DIR_.'co/');
define('_PS_EMPLOYEE_IMG_DIR_',      _PS_IMG_DIR_.'e/');
define('_PS_GENDERS_DIR_',           _PS_IMG_DIR_.'genders/');
define('_PS_LANG_IMG_DIR_',          _PS_IMG_DIR_.'l/');
define('_PS_MANU_IMG_DIR_',          _PS_IMG_DIR_.'m/');
define('_PS_ORDER_STATE_IMG_DIR_',   _PS_IMG_DIR_.'os/');
define('_PS_PROD_IMG_DIR_',          _PS_IMG_DIR_.'p/');
define('_PS_SCENE_IMG_DIR_',         _PS_IMG_DIR_.'scenes/');
define('_PS_SCENE_THUMB_IMG_DIR_',   _PS_IMG_DIR_.'scenes/thumbs/');
define('_PS_SHIP_IMG_DIR_',          _PS_IMG_DIR_.'s/');
define('_PS_STORE_IMG_DIR_',         _PS_IMG_DIR_.'st/');
define('_PS_SUPP_IMG_DIR_',          _PS_IMG_DIR_.'su/');
define('_PS_TMP_IMG_DIR_',           _PS_IMG_DIR_.'tmp/');

/* settings php */
define('_PS_TRANS_PATTERN_',            '(.*[^\\\\])');
define('_PS_MIN_TIME_GENERATE_PASSWD_', '360');
if (!defined('_PS_MAGIC_QUOTES_GPC_')) {
    define('_PS_MAGIC_QUOTES_GPC_', function_exists('get_magic_quotes_gpc')? @get_magic_quotes_gpc() : false);
}

define('_CAN_LOAD_FILES_', 1);

/* Order statuses
Order statuses have been moved into config.inc.php file for backward compatibility reasons */

/* Tax behavior */
define('PS_PRODUCT_TAX', 0);
define('PS_STATE_TAX', 1);
define('PS_BOTH_TAX', 2);

define('PS_TAX_EXC', 1);
define('PS_TAX_INC', 0);

define('PS_ORDER_PROCESS_STANDARD', 0);
define('PS_ORDER_PROCESS_OPC', 1);

define('PS_ROUND_UP', 0);
define('PS_ROUND_DOWN', 1);
define('PS_ROUND_HALF_UP', 2);
define('PS_ROUND_HALF_DOWN', 3);
define('PS_ROUND_HALF_EVEN', 4);
define('PS_ROUND_HALF_ODD', 5);

/* Backward compatibility */
define('PS_ROUND_HALF', PS_ROUND_HALF_UP);

/* Registration behavior */
define('PS_REGISTRATION_PROCESS_STANDARD', 0);
define('PS_REGISTRATION_PROCESS_AIO', 1);

/* Carrier::getCarriers() filter */
// these defines are DEPRECATED since 1.4.5 version
define('PS_CARRIERS_ONLY', 1);
define('CARRIERS_MODULE', 2);
define('CARRIERS_MODULE_NEED_RANGE', 3);
define('PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE', 4);
define('ALL_CARRIERS', 5);

/* SQL Replication management */
define('_PS_USE_SQL_SLAVE_', 0);

/* PS Technical configuration */
define('_PS_ADMIN_PROFILE_', 1);

/* Stock Movement */
define('_STOCK_MOVEMENT_ORDER_REASON_', 3);
define('_STOCK_MOVEMENT_MISSING_REASON_', 4);

/**
 * @deprecated 1.5.0.1
 * @see Configuration::get('PS_CUSTOMER_GROUP')
 */
define('_PS_DEFAULT_CUSTOMER_GROUP_', 3);

define('_PS_CACHEFS_DIRECTORY_', _PS_ROOT_DIR_.'/cache/cachefs/');

/* Geolocation */
define('_PS_GEOLOCATION_NO_CATALOG_', 0);
define('_PS_GEOLOCATION_NO_ORDER_', 1);

define('MIN_PASSWD_LENGTH', 8);

define('_PS_SMARTY_NO_COMPILE_', 0);
define('_PS_SMARTY_CHECK_COMPILE_', 1);
define('_PS_SMARTY_FORCE_COMPILE_', 2);

define('_PS_SMARTY_CONSOLE_CLOSE_', 0);
define('_PS_SMARTY_CONSOLE_OPEN_BY_URL_', 1);
define('_PS_SMARTY_CONSOLE_OPEN_', 2);

if (!defined('_PS_JQUERY_VERSION_')) {
    define('_PS_JQUERY_VERSION_', '1.11.0');
}

define('_PS_CACHE_CA_CERT_FILE_', _PS_CACHE_DIR_.'cacert.pem');
