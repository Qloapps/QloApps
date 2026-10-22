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

// Bootstrap PrestaShop (module is 2 levels below shop root: /modules/qlogooglehotelconnector/)
$configPath = dirname(dirname(dirname(__FILE__))) . '/config/config.inc.php';
if (!file_exists($configPath)) {
    header('HTTP/1.1 500 Internal Server Error');
    die('PrestaShop configuration not found.');
}
require_once $configPath;

// Token validation — must match the module's secure_key
$token = Tools::getValue('token', '');
if ($token !== Tools::encrypt('qlogooglehotelconnector')) {
    header('HTTP/1.1 403 Forbidden');
    die('Invalid token.');
}

// Delete qghc_api_log rows and daily file logs older than 2 months
$result = Module::getInstanceByName('qlogooglehotelconnector')->cleanupApiLogs();

if ($result !== false) {
    echo 'OK — ' . (int)$result['count'] . ' log(s) deleted (older than ' . $result['cutoff'] . ').';
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo 'ERROR — failed to delete old logs.';
}
