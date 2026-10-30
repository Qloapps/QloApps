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

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}

include(_PS_ADMIN_DIR_.'/../../config/config.inc.php');

require_once _PS_MODULE_DIR_ . 'qlocmconnector/classes/QcmcChannelManagerApiService.php';

$logDir  = _PS_MODULE_DIR_ . 'qlocmconnector/logs';
$markerFile = $logDir . '/.last_cleanup';
$logFile = $logDir . '/' . date('Y-m-d') . '.log';

if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// simple logger
function qcmc_log($message)
{
    global $logFile;

    $timestamp = date('[Y-m-d H:i e] ');
    error_log($timestamp . $message . PHP_EOL, 3, $logFile);
}

$objCMConnector = Module::getInstanceByName('qlocmconnector');
$objChannelManagerApiService = new QcmcChannelManagerApiService();
$objAri = new QcmcAri();

if (!Tools::getValue('token') || (Tools::getValue('token') != $objCMConnector->secure_key)) {
    qcmc_log("Failed to get or refresh access token. Aborting cron.");
    die('Something went wrong.');
}


try {
    $clientId = Configuration::get('QCMC_CM_CLIENT_ID');
    $clientSecret = Configuration::get('QCMC_CM_CLIENT_SECRET');

    if($clientId && $clientSecret) {
        if(Configuration::get('QCMC_CM_ACCESS_TOKEN')) {
            if(!$objAri->processAriUpdateData()) {
                $result = $objChannelManagerApiService->getAccessToken($clientId, $clientSecret);
                if(isset($result['access_token'])) {
                    if($objChannelManagerApiService->updateAccessToken($result['access_token'])) {
                        $objAri->processAriUpdateData();
                    }
                } else {
                    qcmc_log("Error getting access token");
                }
            }
        } else {
            $result = $objChannelManagerApiService->getAccessToken($clientId, $clientSecret);
            if(isset($result['access_token'])) {
                if($objChannelManagerApiService->updateAccessToken($result['access_token'])) {
                    $objAri->processAriUpdateData();
                }
            } else {
                qcmc_log("Error getting access token");
            }
        }
    }
} catch (Exception $e) {
    qcmc_log("Fatal error in cron: " . $e->getMessage());
}

echo "Process Completed successsfully";

if (!file_exists($markerFile) || ((time() - (int)@file_get_contents($markerFile)) >= 86400)) {
    foreach (glob($logDir . "/*.log") as $file) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', basename($file, '.log'))) {
            $fileDate = strtotime(basename($file, '.log'));
            if ($fileDate !== false && $fileDate < strtotime('-60 days')) {
                @unlink($file);
            }
        }
    }
    @file_put_contents($markerFile, time());
}
die;
