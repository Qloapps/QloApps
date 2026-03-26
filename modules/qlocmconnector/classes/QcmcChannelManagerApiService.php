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

class QcmcChannelManagerApiService
{
    private $logFile;
    private $logDir;
    private $baseUrl;
    private $curlConfig = [];

    public function __construct()
    {
        $moduleDir = _PS_MODULE_DIR_ . 'qlocmconnector/';

        $this->logDir = $moduleDir . 'logs';
        $this->logFile = $this->logDir . '/' . date('Y-m-d') . '.log';
        $this->baseUrl = 'https://channels.qloapps.com/';
    }

    private function setMethod($method = 'POST')
    {
        $this->curlConfig['method'] = strtoupper($method);
    }

    private function setEndpoint($url)
    {
        $this->curlConfig['url'] = $this->baseUrl.$url;
    }

    private function addHeaders(array $headers)
    {
        $this->curlConfig['headers'] = array_merge(
            $this->curlConfig['headers'] ?? [], 
            $headers
        );
    }

    private function setBody($body)
    {
        $this->curlConfig['body'] = $body;
    }

    private function getResult()
    {
        $ch = curl_init($this->curlConfig['url']);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->curlConfig['method']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if (!empty($this->curlConfig['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->curlConfig['body']);
        }

        if (!empty($this->curlConfig['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->curlConfig['headers']);
        }

        if ($result = curl_exec($ch)) {
            $result = json_decode($result, true);
        } else {
            $result = [];
        }

        $result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0755, true);
        }

        $data = date('[Y-m-d H:i e] ') . PHP_EOL;
        $data .= 'Channel Manager API called: ' . $this->curlConfig['url'] . PHP_EOL;
        if (isset($this->curlConfig['body']) && $this->curlConfig['body']) {
            $data .= 'Request : ' . $this->curlConfig['body'] . PHP_EOL;
        }
        $data .= 'Response : ' . json_encode($result) . PHP_EOL;
        $data .= '___________________________________________';
        $data .= PHP_EOL . PHP_EOL;
        error_log($data, 3, $this->logFile);

        $this->curlConfig = [];

        return $result;
    }

    public function pushARI($payload)
    {
        $this->setEndpoint('api/v1/push_ari');
        $this->setMethod('POST');

        $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES);

        if ($jsonBody === false) {
            return false;
        }

        $accessToken = Configuration::get('QCMC_CM_ACCESS_TOKEN');

        $this->addHeaders([
            'Content-Type: application/json',
            'X-Requested-With: XMLHttpRequest',
            'X-PMS-REQUEST: true',
            'Authorization: Bearer ' . $accessToken,
        ]);

        $this->setBody($jsonBody);

        $result = $this->getResult();

        return $result;
    }

    public function getAccessToken($clientId, $clientSecret)
    {
        $this->setEndpoint('oauth/token');
        $this->setMethod('POST');

        $this->addHeaders([
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $postFields = http_build_query([
            'grant_type'    => 'client_credentials',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
        ]);

        $this->setBody($postFields);

        $response = $this->getResult();

        return $response;
    }

    public function updateAccessToken($accessToken)
    {
        if(Configuration::updateValue('QCMC_CM_ACCESS_TOKEN', $accessToken)) {    
            return true;
        }
        return false;
    }
}