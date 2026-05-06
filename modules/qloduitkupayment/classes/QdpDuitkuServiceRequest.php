<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
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
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

class QdpDuitkuServiceRequest
{

    public static function makeRequest($endpoint, $method, $data = [])
    {
        $url = self::getApiUrl($endpoint);

        $contentArray = array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data))
        );

        $headers = array_merge($contentArray,self::createHeader());

        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $headers,
        );

        if ($method == QdpDuitkuTransaction::QDP_CURL_REQUEST_TYPE_POST) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
        } else if ($method == QdpDuitkuTransaction::QDP_CURL_REQUEST_TYPE_GET) {
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        }

        curl_setopt_array($curl, $options);
        $request = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        self::logResponse($request);
        $response = array();

        if ($httpCode == 200) {
            $response['status'] = true;
            $response['response'] = json_decode($request, true);

        } else {
            $response['status'] = false;
            $response['response'] = json_decode($request, true);
        }

        return $response;
    }


    private static function createHeader()
    {
        date_default_timezone_set('Asia/Jakarta');

        $timestamp = round(microtime(true) * 1000);
        $merchantCode = Configuration::get('QDP_DUITKU_PAYMENT_MERCHANT_CODE');
        $apiKey = Configuration::get('QDP_DUITKU_PAYMENT_API_KEY');
        $signature = hash('sha256', $merchantCode . $timestamp . $apiKey);

        return array(
            "x-duitku-signature: $signature",
            "x-duitku-timestamp: $timestamp",
            "x-duitku-merchantcode: $merchantCode"
        );
    }


    private static function getApiUrl($endpoint)
    {
        $environment = (int) Configuration::get('QDP_DUITKU_PAYMENT_ENVIRONMENT');

        if ($environment === QdpDuitkuTransaction::QDP_DUITKU_ENVIRONMENT_SANDBOX) {
            return QdpDuitkuTransaction::QDP_DUITKU_HOST_SANDBOX_URL . $endpoint;
        }

        if ($environment === QdpDuitkuTransaction::QDP_DUITKU_ENVIRONMENT_PRODUCTION) {
            return QdpDuitkuTransaction::QDP_DUITKU_ENVIRONMENT_PRODUCTION . $endpoint;
        }

        return QdpDuitkuTransaction::QDP_DUITKU_HOST_SANDBOX_URL . $endpoint;
    }


    private static function logResponse($response)
    {
        $module = Module::getInstanceByName('qloduitkupayment');
        $module->logger->log('Duitku Payment response:', FileLogger::DEBUG);
        $module->logger->log($response, FileLogger::DEBUG);
    }
}
