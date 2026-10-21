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

if (!defined('QGHC_CM_BASE_URL')) {
    define('QGHC_CM_BASE_URL', 'https://channels.qloapps.com/');
}

class QghcApiService
{
    private $baseUrl = QGHC_CM_BASE_URL;
    private $curlConfig = array();
    private $apiType = QghcApiLog::TYPE_UNKNOWN;

    protected function setApiType($apiType)
    {
        $this->apiType = $apiType;
    }

    protected function setMethod($method = 'POST')
    {
        $this->curlConfig['method'] = strtoupper($method);
    }

    protected function setEndpoint($endpoint)
    {
        $this->curlConfig['url'] = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }

    protected function addHeaders(array $headers)
    {
        $this->curlConfig['headers'] = array_merge(
            isset($this->curlConfig['headers']) ? $this->curlConfig['headers'] : array(),
            $headers
        );
    }

    protected function setBody($body)
    {
        $this->curlConfig['body'] = $body;
    }

    protected function getResult()
    {
        $ch = curl_init($this->curlConfig['url']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->curlConfig['method']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if (!empty($this->curlConfig['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->curlConfig['body']);
        }
        if (!empty($this->curlConfig['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->curlConfig['headers']);
        }

        $rawResult = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($rawResult) {
            $result = json_decode($rawResult, true);
            if (!is_array($result)) {
                $result = array('raw' => $rawResult);
            }
        } else {
            $result = array('curl_error' => $curlError);
        }
        $result['http_code'] = $httpCode;

        $this->saveApiLog($result);
        $this->curlConfig = array();
        $this->apiType    = QghcApiLog::TYPE_UNKNOWN;

        return $result;
    }

    protected function saveApiLog($result)
    {
        $request  = isset($this->curlConfig['body']) ? $this->curlConfig['body'] : '';
        $response = json_encode($result, JSON_UNESCAPED_SLASHES);
        $httpCode = isset($result['http_code']) ? (int)$result['http_code'] : 0;
        $success  = $httpCode >= 200 && $httpCode < 300;

        // File Log started
        $logDir = _PS_MODULE_DIR_ . 'qlogooglehotelconnector/logs/';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $requestDecoded = json_decode($request, true);
        if (is_array($requestDecoded) && isset($requestDecoded['api_key'])) {
            $requestDecoded['api_key'] = '[REDACTED]';
        }
        $requestDisplay = is_array($requestDecoded)
            ? json_encode($requestDecoded, JSON_UNESCAPED_SLASHES)
            : $request;

        $headersDisplay = array();
        foreach ((array)(isset($this->curlConfig['headers']) ? $this->curlConfig['headers'] : array()) as $h) {
            $name = strtolower(strtok($h, ':'));
            $headersDisplay[] = in_array($name, array('api-key', 'authorization'))
                ? strtok($h, ':') . ': [REDACTED]'
                : $h;
        }

        $url    = isset($this->curlConfig['url'])    ? $this->curlConfig['url']    : '';
        $method = isset($this->curlConfig['method']) ? $this->curlConfig['method'] : '';

        $data  = date('[Y-m-d H:i e] ') . PHP_EOL;
        $data .= 'Url     : ' . $url . PHP_EOL;
        $data .= 'Method  : ' . $method . PHP_EOL;
        $data .= 'Headers : ' . json_encode($headersDisplay, JSON_UNESCAPED_SLASHES) . PHP_EOL;
        $data .= 'Request : ' . $requestDisplay . PHP_EOL;
        $data .= 'Response: ' . $response . PHP_EOL;
        if (!empty($result['curl_error'])) {
            $data .= 'CurlErr : ' . $result['curl_error'] . PHP_EOL;
        }
        $data .= '___________________________________________';
        $data .= PHP_EOL . PHP_EOL;
        error_log($data, 3, $logDir . date('Y-m-d') . '.log');
        // File Log END

        // DB Log started
        $message = $success ? '' : (
            $httpCode > 0
                ? 'HTTP ' . $httpCode
                : (!empty($result['curl_error']) ? $result['curl_error'] : 'No response')
        );

        QghcApiLog::insert(
            0,
            $this->apiType,
            $requestDisplay,
            $response,
            $success ? QghcApiLog::STATUS_SUCCESS : QghcApiLog::STATUS_ERROR,
            $message
        );
        // DB Log END
    }

    /**
     * Registers this PMS with the channel manager.
     * Sends our shop URL (pms_url) and our PrestaShop webservice key (api_key)
     * so the CM can authenticate future calls to our /api/ghc_api/* endpoints.
     */
    public function createConnection($pmsUrl, $apiKey)
    {
        $this->setApiType(QghcApiLog::TYPE_CONNECTION);
        $this->setEndpoint('/api/google-hotel-connection');
        $this->setMethod('POST');
        $this->addHeaders(array(
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        $this->setBody(json_encode(array(
            'pms_url' => $pmsUrl,
            'api_key' => $apiKey,
        )));

        return $this->getResult();
    }

    /**
     * Deregisters this PMS from the channel manager.
     * The CM removes all associated data for this pms_url + api_key pair.
     * Local connection state is only cleared after the CM confirms success.
     */
    public function deleteConnection($pmsUrl, $apiKey)
    {
        $this->setApiType(QghcApiLog::TYPE_DELETE_CONNECTION);
        $this->setEndpoint('/api/google-hotel-connection');
        $this->setMethod('DELETE');
        $this->addHeaders(array(
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        $this->setBody(json_encode(array(
            'pms_url' => $pmsUrl,
            'api_key' => $apiKey,
        )));

        return $this->getResult();
    }
}
