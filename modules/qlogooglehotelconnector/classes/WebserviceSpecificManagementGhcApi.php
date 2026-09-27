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

if (!class_exists('QghcProperty')) {
    require_once dirname(__FILE__) . '/QghcRequiredClasses.php';
}

class WebserviceSpecificManagementGhcApi implements WebserviceSpecificManagementInterface
{
    protected $objOutput;
    protected $output;
    protected $wsObject;

    private $logApiType = QghcApiLog::TYPE_UNKNOWN;
    private $logIdHotel = 0;
    private $logStatus  = true;
    private $logMessage = '';

    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;
        return $this;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;
        return $this;
    }

    public function getWsObject()
    {
        return $this->wsObject;
    }

    public function manage()
    {
        $method   = $this->wsObject->method;
        $endpoint = isset($this->wsObject->urlSegment[1]) ? $this->wsObject->urlSegment[1] : '';

        if (in_array($method, array('POST', 'PUT', 'PATCH'), true)) {
            $rawBody = file_get_contents('php://input');
        } else {
            // GET requests (e.g. the CM fetching properties/ARI to verify the connection)
            // carry their data in the query string, not a body — log that instead, or the
            // Request column is always empty for every GET call. ws_key is PS's webservice
            // auth fallback (see webservice/dispatcher.php) — never log it.
            $queryParams = $_GET;
            unset($queryParams['ws_key']);
            $rawBody = $queryParams ? json_encode($queryParams, JSON_UNESCAPED_SLASHES) : '';
        }

        try {
            if ($method === 'GET' && $endpoint === 'google-ari') {
                $this->logApiType = (int)Tools::getValue('only_change', 0) === 1 ? QghcApiLog::TYPE_ARI_CHANGE : QghcApiLog::TYPE_ARI_FULL;
                $filter = Tools::getValue('filter', array());
                
                $allowedFilters = array(
                    'id_property' => 'int',
                    'id_room_type' => 'string',
                    'date_from' => 'string',
                    'date_to' => 'string',
                );

                $invalidFilterKey = null;
                foreach ($filter as $filterKey => $filterValue) {
                    if (isset($allowedFilters[$filterKey]) && is_array($filterValue)) {
                        $invalidFilterKey = $filterKey;
                        break;
                    }
                }

                if ($invalidFilterKey !== null) {
                    $this->output = json_encode(array('success' => false, 'error' => 'Invalid filter: ' . $invalidFilterKey), JSON_UNESCAPED_SLASHES);
                    $this->logStatus = false;
                    $this->logMessage = 'Invalid filter: ' . $invalidFilterKey;
                } elseif (!isset($filter['id_property']) || !filter_var($filter['id_property'], FILTER_VALIDATE_INT)) {
                    $this->output = json_encode(array('success' => false, 'error' => 'id_property filter is required'), JSON_UNESCAPED_SLASHES);
                    $this->logStatus = false;
                    $this->logMessage = 'id_property filter is required';
                } else {
                    $this->logIdHotel = (int)$filter['id_property'];
                    $objAri = new QghcAri();
                    $this->output = $this->logApiType === QghcApiLog::TYPE_ARI_CHANGE ? $objAri->getAriChange($filter) : $objAri->getAriByIdProperty($filter);
                }
            } elseif ($method === 'GET' && $endpoint === 'google-hotel-properties') {
                $this->logApiType = QghcApiLog::TYPE_HOTEL_LIST_FEED;
                $this->output = QghcPropertyService::getPropertiesFeed();
            } elseif ($method === 'POST' && $endpoint === 'google_status') {
                $this->logApiType = QghcApiLog::TYPE_GOOGLE_STATUS;

                $input  = $this->getPostRequest();
                $values = isset($input['google_hotel_values']) && is_array($input['google_hotel_values']) ? $input['google_hotel_values'] : array();
                $values['send_mail'] = !empty($input['send_mail']);

                if (empty($values['properties']) || !is_array($values['properties'])) {
                    $this->output = json_encode(array('success' => false, 'error' => 'properties map is required in google_hotel_values'), JSON_UNESCAPED_SLASHES);
                    $this->logStatus = false;
                    $this->logMessage = 'properties map is required in google_hotel_values';
                } else {
                    $result = QghcPropertyService::applyGoogleStatusUpdate($values);

                    if (empty($result['updated_properties'])) {
                        $this->output = json_encode(array('success' => false, 'error' => 'No matching properties found to update'), JSON_UNESCAPED_SLASHES);
                        $this->logStatus = false;
                        $this->logMessage = 'No matching properties found to update';
                    } else {
                        $this->logIdHotel = $result['updated_properties'][0];
                        $this->output = json_encode(array(
                            'success' => true,
                            'google_status' => $result['google_status'],
                            'updated_properties' => $result['updated_properties'],
                            'updated_room_types' => $result['updated_room_types'],
                        ));
                    }
                }
            } elseif (!in_array($method, array('GET', 'POST'), true)) {
                // A raw header() call here is overwritten later by dispatcher.php's own
                // header loop (WebserviceOutputBuilder defaults to 200 OK) — setStatus()
                // is the only way to actually change the response's HTTP status code.
                $this->objOutput->setStatus(405);
                $this->output = json_encode(array('success' => false, 'error' => 'HTTP method not allowed'), JSON_UNESCAPED_SLASHES);
                $this->logStatus = false;
                $this->logMessage = 'HTTP method not allowed';
            } else {
                $this->output = json_encode(array('success' => false, 'error' => 'Unknown endpoint'), JSON_UNESCAPED_SLASHES);
                $this->logStatus = false;
                $this->logMessage = 'Unknown endpoint';
            }
        } catch (\Exception $e) {
            $this->output = json_encode(array('success' => false, 'error' => 'Internal error, please contact support.'), JSON_UNESCAPED_SLASHES);
            $this->logStatus = false;
            $this->logMessage = $e->getMessage();
        }

        $response = (string) $this->output;

        // DB Log started
        QghcApiLog::insert(
            $this->logIdHotel,
            $this->logApiType,
            $rawBody,
            $response,
            $this->logStatus ? QghcApiLog::STATUS_SUCCESS : QghcApiLog::STATUS_ERROR,
            $this->logMessage
        );
        // DB Log END

        // File Log started
        $logDir = _PS_MODULE_DIR_ . 'qlogooglehotelconnector/logs/';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $data  = date('[Y-m-d H:i e] ') . PHP_EOL;
        $data .= 'Url     : ghc_api/' . $endpoint . PHP_EOL;
        $data .= 'Method  : ' . $method . PHP_EOL;
        $data .= 'Request : ' . $rawBody . PHP_EOL;
        $data .= 'Response: ' . $response . PHP_EOL;
        $data .= '___________________________________________';
        $data .= PHP_EOL . PHP_EOL;
        error_log($data, 3, $logDir . date('Y-m-d') . '.log');
        // File Log END

        return $this->wsObject->getOutputEnabled();
    }

    public function getContent()
    {
        $content = $this->output ? $this->output : json_encode(array());
        $this->objOutput->setHeaderParams('Content-Type', 'application/json');

        $content = preg_replace_callback(
            '/\\\\u([a-f0-9]{4})/',
            function ($matches) {
                return iconv(
                    'UCS-4LE',
                    'UTF-8',
                    pack('V', hexdec($matches[1]))
                );
            },
            $content
        );

        return $content;
    }

    private function getPostRequest()
    {
        $rawBody = file_get_contents('php://input');
        $data    = json_decode($rawBody, true);
        return is_array($data) ? $data : array();
    }
}
