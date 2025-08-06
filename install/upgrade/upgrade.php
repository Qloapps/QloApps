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
* /ersions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$filePrefix = 'PREFIX_';
$engineType = 'ENGINE_TYPE';

// Set execution time and time_limit to infinite if available
@set_time_limit(0);
@ini_set('max_execution_time', '0');

// setting the memory limit to 128M only if current is lower
$memory_limit = ini_get('memory_limit');
if (substr($memory_limit, -1) != 'G'
    and ((substr($memory_limit, -1) == 'M' and substr($memory_limit, 0, -1) < 128)
    or is_numeric($memory_limit) and (intval($memory_limit) < 131072) and $memory_limit > 0)
) {
    @ini_set('memory_limit', '128M');
}

// redefine REQUEST_URI if empty (on some webservers...)
if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '') {
    if (!isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['SCRIPT_FILENAME'])) {
        $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_FILENAME'];
    } else {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    }
}

if ($tmp = strpos($_SERVER['REQUEST_URI'], '?')) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $tmp);
}
$_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);

// retrocompatibility (is present in some upgrade scripts)
define('INSTALL_PATH', dirname(dirname(__FILE__)).'/');

require_once(INSTALL_PATH . 'install_version.php');

define('SETTINGS_FILE', INSTALL_PATH . '/../config/settings.inc.php');

if (file_exists(SETTINGS_FILE)) {
    include_once(SETTINGS_FILE);
} else {
    die('settings.inc.php is missing (error code 30).');
}

// need for upgrade before 1.5
if (!defined('__PS_BASE_URI__')) {
    define('__PS_BASE_URI__', str_replace('//', '/', '/'.trim(preg_replace('#/(install(-dev)?/upgrade)$#', '/', str_replace('\\', '/', dirname($_SERVER['REQUEST_URI']))), '/').'/'));
}

// need for upgrade before 1.5
if (!defined('_THEME_NAME_')) {
    define('_THEME_NAME_', 'default');
}

require_once(dirname(__FILE__).'/../init.php');

// set logger
require_once(_PS_INSTALL_PATH_.'upgrade/classes/AbstractLogger.php');
eval('abstract class AbstractLogger extends AbstractLoggerCore{}');
require_once(_PS_INSTALL_PATH_.'upgrade/classes/FileLogger.php');
eval('class FileLogger extends FileLoggerCore{}');

$logger = new FileLogger();
$logger->setFilename(_PS_ROOT_DIR_.'/log/'.@date('Ymd').'_upgrade.log');

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Europe/Paris');
}

// if _PS_ROOT_DIR_ is defined, use it instead of "guessing" the module dir.
if (defined('_PS_ROOT_DIR_') and !defined('_PS_MODULE_DIR_')) {
    define('_PS_MODULE_DIR_', _PS_ROOT_DIR_.'/modules/');
} elseif (!defined('_PS_MODULE_DIR_')) {
    define('_PS_MODULE_DIR_', _PS_INSTALL_PATH_.'/../modules/');
}

if (!defined('_PS_INSTALLER_PHP_UPGRADE_DIR_')) {
    define('_PS_INSTALLER_PHP_UPGRADE_DIR_', _PS_INSTALL_PATH_.'upgrade/php/');
}

if (!defined('_PS_INSTALLER_SQL_UPGRADE_DIR_')) {
    define('_PS_INSTALLER_SQL_UPGRADE_DIR_', _PS_INSTALL_PATH_.'upgrade/sql/');
}


//old version detection
global $oldversion, $logger;
$oldversion = false;

//sql file execution
global $requests, $warningExist;
$requests = '';
$fail_result = '';
$warningExist = false;


if (!defined('_THEMES_DIR_')) {
    define('_THEMES_DIR_', __PS_BASE_URI__.'themes/');
}
if (!defined('_PS_IMG_')) {
    define('_PS_IMG_', __PS_BASE_URI__.'img/');
}
if (!defined('_PS_JS_DIR_')) {
    define('_PS_JS_DIR_', __PS_BASE_URI__.'js/');
}
if (!defined('_PS_CSS_DIR_')) {
    define('_PS_CSS_DIR_', __PS_BASE_URI__.'css/');
}

$oldversion = _PS_VERSION_;

$versionCompare =  version_compare(_PS_INSTALL_VERSION_, $oldversion);

if ($versionCompare == '-1') {
    $logger->logError('This installer is too old.');
    $requests .= '<action result="fail" error="27" />'."\n";
} elseif ($versionCompare == 0) {
    $logger->logError(sprintf('You already have the %s version.', _PS_INSTALL_VERSION_));
    $fail_result .= '<action result="fail" error="28" />'."\n";
} elseif ($versionCompare === false) {
    $logger->logError('There is no older version. Did you delete or rename the config/settings.inc.php file?');
    $fail_result .= '<action result="fail" error="29" />'."\n";
}

if ((defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_)) {
    $logger->logError('The cache is activated. Please deactivate it first before lauching this script.');
    $fail_result .= '<action result="fail" error="35" />'."\n";
}


//check DB access
// include_once(_PS_INSTALL_PATH_.'/classes/ToolsInstall.php');
// $resultDB = ToolsInstall::checkDB(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_, false);
/*
if ($resultDB !== true)
{
    $logger->logError('Invalid database configuration.');
    die("<action result='fail' error='".$resultDB."'/>\n");
}
*/

//
//custom sql file creation
$upgradeFiles = array();
if (empty($fail_result)) {
    if ($handle = opendir(_PS_INSTALLER_SQL_UPGRADE_DIR_)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' and $file != '..') {
                $upgradeFiles[] = str_replace(".sql", "", $file);
            }
        }
        closedir($handle);
    }
    if (empty($upgradeFiles)) {
        $logger->logError('Can\'t find the sql upgrade files. Please verify that the /install/sql/upgrade folder is not empty)');
        $fail_result .=  '<action result="fail" error="31" />'."\n";
    }
    natcasesort($upgradeFiles);
}

// fix : complete version number if there is not all 4 numbers
// for example replace 1.4.3 by 1.4.3.0
// consequences : file 1.4.3.0.sql will be skipped if oldversion = 1.4.3
// @since 1.4.4.0
$arrayVersion = preg_split('#\.#', $oldversion);
$versionNumbers = sizeof($arrayVersion);

if ($versionNumbers != 4) {
    $arrayVersion = array_pad($arrayVersion, 4, '0');
}

$oldversion = implode('.', $arrayVersion);
// end of fix

$neededUpgradeFiles = array();
foreach ($upgradeFiles as $version) {
    if (version_compare($version, $oldversion) == 1 and version_compare(_PS_INSTALL_VERSION_, $version) != -1) {
        $neededUpgradeFiles[] = $version;
    }
}

if (empty($fail_result) && empty($neededUpgradeFiles)) {
    $logger->logError('No upgrade is possible.');
    $fail_result .= '<action result="fail" error="32" />'."\n";
}


// refresh conf file
require_once(_PS_INSTALL_PATH_.'upgrade/classes/AddConfToFile.php');

$oldLevel = error_reporting(E_ALL);
$mysqlEngine = (defined('_MYSQL_ENGINE_') ? _MYSQL_ENGINE_ : 'MyISAM');

if (defined('_PS_CACHING_SYSTEM_') and _PS_CACHING_SYSTEM_ == 'CacheFS') {
    $cache_engine = 'CacheFs';
} elseif (defined('_PS_CACHING_SYSTEM_') and _PS_CACHING_SYSTEM_ != 'CacheMemcache' and _PS_CACHING_SYSTEM_ != 'CacheMemcached') {
    $cache_engine = _PS_CACHING_SYSTEM_;
} else {
    $cache_engine = 'CacheMemcache';
}
$datas = array(
    array('_DB_SERVER_', _DB_SERVER_),
    array('_DB_NAME_', _DB_NAME_),
    array('_DB_USER_', _DB_USER_),
    array('_DB_PASSWD_', _DB_PASSWD_),
    array('_DB_PREFIX_', _DB_PREFIX_),
    array('_MYSQL_ENGINE_', $mysqlEngine),
    array('_PS_CACHING_SYSTEM_', $cache_engine),
    array('_PS_CACHE_ENABLED_', defined('_PS_CACHE_ENABLED_') ? _PS_CACHE_ENABLED_ : '0'),
    // 1.4 only
    // array('__PS_BASE_URI__', __PS_BASE_URI__),
    // 1.4 only
    // array('_THEME_NAME_', _THEME_NAME_),
    array('_PS_DIRECTORY_', __PS_BASE_URI__),
    array('_COOKIE_KEY_', _COOKIE_KEY_),
    array('_COOKIE_IV_', _COOKIE_IV_),
    array('_PS_CREATION_DATE_', defined("_PS_CREATION_DATE_") ? _PS_CREATION_DATE_ : date('Y-m-d')),
    array('_PS_VERSION_', _PS_INSTALL_VERSION_)
);

if (version_compare(_PS_INSTALL_VERSION_, '1.6.0.11', '<')) {
    $datas[] = array('_MEDIA_SERVER_1_', defined('_MEDIA_SERVER_1_') ? _MEDIA_SERVER_1_ : '');
    $datas[] = array('_MEDIA_SERVER_2_', defined('_MEDIA_SERVER_2_') ? _MEDIA_SERVER_2_ : '');
    $datas[] = array('_MEDIA_SERVER_3_', defined('_MEDIA_SERVER_3_') ? _MEDIA_SERVER_3_ : '');
}

if (defined('_RIJNDAEL_KEY_')) {
    $datas[] = array('_RIJNDAEL_KEY_', _RIJNDAEL_KEY_);
}
if (defined('_RIJNDAEL_IV_')) {
    $datas[] = array('_RIJNDAEL_IV_', _RIJNDAEL_IV_);
}
if (!defined('_PS_CACHE_ENABLED_')) {
    define('_PS_CACHE_ENABLED_', '0');
}
if (!defined('_MYSQL_ENGINE_')) {
    define('_MYSQL_ENGINE_', 'MyISAM');
}

global $smarty;
// Clean all cache values
Cache::clean('*');

Context::getContext()->shop = new Shop(1);
Shop::setContext(Shop::CONTEXT_SHOP, 1);

if (!isset(Context::getContext()->language) || !Validate::isLoadedObject(Context::getContext()->language)) {
    if ($id_lang = (int)getConfValue('PS_LANG_DEFAULT')) {
        Context::getContext()->language = new Language($id_lang);
    }
}
if (!isset(Context::getContext()->country) || !Validate::isLoadedObject(Context::getContext()->country)) {
    if ($id_country = (int)getConfValue('PS_COUNTRY_DEFAULT')) {
        Context::getContext()->country = new Country((int)$id_country);
    }
}

Context::getContext()->cart = new Cart();
Context::getContext()->employee = new Employee(1);
if (!defined('_PS_SMARTY_FAST_LOAD_')) {
    define('_PS_SMARTY_FAST_LOAD_', true);
}
require_once _PS_ROOT_DIR_.'/config/smarty.config.inc.php';

Context::getContext()->smarty = $smarty;

if (isset($_GET['customModule']) and $_GET['customModule'] == 'desactivate') {
    require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'deactivate_custom_modules.php');
    deactivate_custom_modules();
}
$sqlContentVersion = array();
if (empty($fail_result)) {
    foreach ($neededUpgradeFiles as $version) {
        $file = _PS_INSTALLER_SQL_UPGRADE_DIR_.$version.'.sql';
        if (!file_exists($file)) {
            $logger->logError('Error while loading sql upgrade file.');
            $fail_result .= '<action result="fail" error="33" sqlfile="'.$version.'" />'."\n";
        }
        if (!$sqlContent = file_get_contents($file)) {
            $logger->logError(sprintf('Error while loading sql upgrade file %s.', $version));
            $fail_result .= '<action result="fail" error="33" />'."\n";
        }
        $sqlContent .= "\n";
        $sqlContent = str_replace(array($filePrefix, $engineType), array(_DB_PREFIX_, $mysqlEngine), $sqlContent);
        $sqlContent = preg_split("/;\s*[\r\n]+/", $sqlContent);

        $sqlContentVersion[$version] = $sqlContent;
    }
}


if (empty($fail_result)) {
    error_reporting($oldLevel);
    $confFile = new AddConfToFile(SETTINGS_FILE, 'w');
    if ($confFile->error) {
        $logger->logError($confFile->error);
        $fail_result .= '<action result="fail" error="'.$confFile->error.'" />'."\n";
    } else {
        foreach ($datas as $data) {
            $confFile->writeInFile($data[0], $data[1]);
        }
    }
    if ($confFile->error != false) {
        $logger->logError($confFile->error);
        $fail_result .= '<action result="fail" error="'.$confFile->error.'" />'."\n";
    }
}

if (empty($fail_result)) {
    foreach ($sqlContentVersion as $version => $sqlContent) {
        foreach ($sqlContent as $query) {
            $query = trim($query);
            if (!empty($query)) {
                /* If php code have to be executed */
                if (strpos($query, '/* PHP:') !== false) {
                    /* Parsing php code */
                    $pos = strpos($query, '/* PHP:') + strlen('/* PHP:');
                    $phpString = substr($query, $pos, strlen($query) - $pos - strlen(' */;'));
                    $php = explode('::', $phpString);
                    preg_match('/\((.*)\)/', $phpString, $pattern);
                    $paramsString = trim($pattern[0], '()');
                    preg_match_all('/([^,]+),? ?/', $paramsString, $parameters);
                    if (isset($parameters[1])) {
                        $parameters = $parameters[1];
                    } else {
                        $parameters = array();
                    }
                    if (is_array($parameters)) {
                        foreach ($parameters as &$parameter) {
                            $parameter = str_replace('\'', '', $parameter);
                        }
                    }

                    /* Call a simple function */
                    if (strpos($phpString, '::') === false) {
                        $func_name = str_replace($pattern[0], '', $php[0]);
                        require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.Tools::strtolower($func_name).'.php');
                        $phpRes = call_user_func_array($func_name, $parameters);
                    } else {
                        /* Or an object method */
                        $func_name = array($php[0], str_replace($pattern[0], '', $php[1]));
                        $phpRes = call_user_func_array($func_name, $parameters);
                    }
                    if ((is_array($phpRes) and !empty($phpRes['error'])) or $phpRes === false) {
                        $warningExist = true;
                        $logger->logError('PHP error: '.$query."\r\n".(empty($phpRes['msg'])?'':' - '.$phpRes['msg']));
                        $logger->logError(empty($phpRes['error'])?'':$phpRes['error']);
                        $requests .= '
			<request result="fail" sqlfile="'.$version.'">
				<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
				<phpMsgError><![CDATA['.(empty($phpRes['msg'])?'':$phpRes['msg']).']]></phpMsgError>
				<phpNumberError><![CDATA['.(empty($phpRes['error'])?'':$phpRes['error']).']]></phpNumberError>
			</request>'."\n";
                    } else {
                        $requests .=
        '	<request result="ok" sqlfile="'.$version.'">
				<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
			</request>'."\n";
                    }
                } elseif (!Db::getInstance()->execute($query)) {
                    $logger->logError('SQL query: '."\r\n".$query);
                    $logger->logError('SQL error: '."\r\n".Db::getInstance()->getMsgError());
                    $warningExist = true;
                    $requests .= '
	<request result="fail" sqlfile="'.$version.'" >
		<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
		<sqlMsgError><![CDATA['.htmlentities(Db::getInstance()->getMsgError()).']]></sqlMsgError>
			<sqlNumberError><![CDATA['.htmlentities(Db::getInstance()->getNumberError()).']]></sqlNumberError>
		</request>'."\n";
                } else {
                    $requests .='
	<request result="ok" sqlfile="'.$version.'">
			<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
		</request>'."\n";
                }
            }
        }
    }
    Configuration::loadConfiguration();


    // Settings updated, compile and cache directories must be emptied
    $tools_dir = rtrim(_PS_INSTALL_PATH_, '\\/').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tools'.DIRECTORY_SEPARATOR;
    $arrayToClean = array(
        $tools_dir.'smarty'.DIRECTORY_SEPARATOR.'cache',
        $tools_dir.'smarty'.DIRECTORY_SEPARATOR.'compile',
        $tools_dir.'smarty_v2'.DIRECTORY_SEPARATOR.'cache',
        $tools_dir.'smarty_v2'.DIRECTORY_SEPARATOR.'compile'
    );
    foreach ($arrayToClean as $dir) {
        if (file_exists($dir)) {
            foreach (scandir($dir) as $file) {
                if ($file[0] != '.' and $file != 'index.php' and $file != '.htaccess') {
                    unlink($dir.DIRECTORY_SEPARATOR.$file);
                }
            }
        }
    }

    // delete cache filesystem if activated
    $depth = getConfValue('PS_CACHEFS_DIRECTORY_DEPTH');
    if (defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_  && $depth) {
        CacheFs::deleteCacheDirectory();
        CacheFs::createCacheDirectories((int)$depth);
    }
}
$result = '<?xml version="1.0" encoding="UTF-8"?>';
if (empty($fail_result)) {
    Configuration::updateValue('PS_HIDE_OPTIMIZATION_TIPS', 0);
    Configuration::updateValue('PS_NEED_REBUILD_INDEX', 1);
    Configuration::updateValue('PS_VERSION_DB', _PS_INSTALL_VERSION_);
    $result .= $warningExist ? '<action result="fail" error="34">'."\n" : '<action result="ok" error="">'."\n";
    $result .= $requests;
    $result .= '</action>'."\n";
} else {
    $result = $fail_result;
}

if (!isset($return_type)) {
    $return_type = 'xml';
}

// format available
// 1) output on screen
// - xml (default)
// - json
// 2) return value in php
// - include : variable $result available after inclusion
// 3) file_get_contents()
// - eval : $res = eval(file_get_contents());
if (empty($return_type) || $return_type == 'xml') {
    header('Content-Type: text/xml');
    echo $result;
} else {
    // result in xml to array
    $result = @simplexml_load_string($result);
    if (!class_exists('ToolsInstall', false)) {
        if (file_exists(_PS_INSTALL_PATH_.'/upgrade/classes/ToolsInstall.php')) {
            include_once(_PS_INSTALL_PATH_.'/upgrade/classes/ToolsInstall.php');
        }
    }

    if ($result && class_exists('ToolsInstall', false)) {
        $result = ToolsInstall::simpleXMLToArray($result);
        switch ($return_type) {
            case 'json':
                header('Content-Type: application/json');
                if (function_exists('json_encode')) {
                    $result = json_encode($result);
                } else {
                    include_once(INSTALL_PATH.'/../tools/json/json.php');
                    $pearJson = new Services_JSON();
                    $result = $pearJson->encode($result);
                }
                echo $result;
                break;
            case 'eval':
                return $result;
            case 'include':
                break;
        }
    }
}
function getConfValue($name)
{
    $full = version_compare('1.5.0.10', _PS_VERSION_) < 0;

    $sql = 'SELECT IF(cl.`id_lang` IS NULL, c.`value`, cl.`value`) AS value
			FROM `'._DB_PREFIX_.'configuration` c
			LEFT JOIN `'._DB_PREFIX_.'configuration_lang` cl ON (c.`id_configuration` = cl.`id_configuration`)
			WHERE c.`name`=\''.pSQL($name).'\'';

    if ($full) {
        $id_shop = Shop::getContextShopID(true);
        $id_shop_group = Shop::getContextShopGroupID(true);
        if ($id_shop) {
            $sql .= ' AND c.`id_shop` = '.(int)$id_shop;
        }
        if ($id_shop_group) {
            $sql .= ' AND c.`id_shop_group` = '.(int)$id_shop_group;
        }
    }
    return Db::getInstance()->getValue($sql);
}
