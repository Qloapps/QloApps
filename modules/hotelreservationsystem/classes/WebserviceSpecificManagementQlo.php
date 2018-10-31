<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WebserviceSpecificManagementQlo implements WebserviceSpecificManagementInterface
{
    protected $objOutput;
    protected $output;
    protected $wsObject;

    public $id_lang;
    public $id_customer;
    public $id_currency;
    public $mobikulGlobal;

    public function __construct()
    {
        ini_set('memory_limit', '256M');
        $psCurrencyDefault = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $this->context = Context::getContext();
        $this->id_lang = Configuration::get('PS_LANG_DEFAULT');
        $this->id_currency = $psCurrencyDefault;
        $this->currency = Currency::getCurrencyInstance($psCurrencyDefault);
    }

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

    // written in alphabatical order
    public function manage()
    {
        $this->wsQloCRUD();
        return $this->wsObject->getOutputEnabled();
    }

    protected $allowedMethods = array(
        'getRoomTypes' => array(),
        'getRoomRates' => array(),
        'updRoomRateAvail' => array(),
    );

    public function getContent()
    {
        if ($this->output != '') {
            $outputXML = $this->objOutput->getObjectRender()->overrideContent($this->output);
            if (isset($this->wsObject->urlFragments['outputformat']) && $this->wsObject->urlFragments['outputformat'] == 'json') {
                $outputXML = simplexml_load_string($outputXML, null, LIBXML_NOCDATA);
                $json = Tools::jsonEncode($outputXML);
                $content = preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", $json);
                header('Content-Type: application/json');
                die($content);
            } else {
                return $outputXML;
            }
        }
    }

    protected function wsQloCRUD()
    {
        $objQloRoomType = new QloRoomType($this->objOutput, $this->wsObject, $this->output);

        switch ($this->wsObject->method) {
            case 'GET':
                switch ($this->wsObject->urlSegment[1]) {
                    case 'getRoomTypes':
                        if (!isset($this->wsObject->urlFragments['id_hotel'])) {
                            throw new WebserviceException('You have to set \'id_hotel\' parameter to get a result', array(100, 400));
                        }
                        $this->output .= $objQloRoomType->getRoomType($this->wsObject->urlFragments['id_hotel']);
                        break;
                    case 'getRoomRates':
                        if (!isset($this->wsObject->urlFragments['id_hotel']) ) {
                            throw new WebserviceException('You have to set \'id_hotel\', \'date_from\' and \'date_to\'parameters to get a result', array(100, 400));
                        }
                        $kwargs = [
                            'idHotel' => $this->wsObject->urlFragments['id_hotel'],
                            'dateFrom' => $this->wsObject->urlFragments['date_from'],
                            'dateTo' => $this->wsObject->urlFragments['date_to'],
                        ];
                        $this->output .= $objQloRoomType->getRoomRates($kwargs);
                        break;

                    default:
                        $exception = new WebserviceException(sprintf('Method with name "%s" does not exist in Qlo Library,', $this->wsObject->urlSegment[1]), array(48, 400));
                        throw $exception->setDidYouMean($this->wsObject->urlSegment[1], array_keys($this->allowedMethods));
                }
                break;
            case 'POST':
                switch ($this->wsObject->urlSegment[1]) {
                    case 'updRoomRateAvail':
                        $fields = $this->getXMLFields('updRoomRateAvail');

                        if (!isset($fields)) {
                            throw new WebserviceException('Send XML formated data', array(100, 400));
                        }
                        $this->output .= $objQloRoomType->updRoomRateAvail($fields);
                        break;
                }
                break;
            default :
                throw new WebserviceException('This HTTP method is not allowed', array(67, 405));
        }
    }

    public function getXMLFields($head = false)
    {
        $putresource = fopen('php://input', 'r');
        $inputXML = '';
        while ($putData = fread($putresource, 1024)) {
            $inputXML .= $putData;
        }
        fclose($putresource);

        // If xml
        if (simplexml_load_string($inputXML)) {
            if (isset($inputXML) && strncmp($inputXML, 'xml=', 4) == 0) {
                $inputXML = Tools::substr($inputXML, 4);
            }
        } else {
            // If input type is json
            $array = Tools::jsonDecode($inputXML, true);
            if (isset($array['json']) && $array['json'] && isset($array[$head])) {
                return ($head ? $array[$head] : $array);
            } else {
                WebserviceRequest::getInstance()->setError(500, 'Invalid json.', 127);
                return;
            }
        }

        try {
            $xml = new SimpleXMLElement($inputXML);
        } catch (Exception $error) {
            WebserviceRequest::getInstance()->setError(500, 'XML error : '.$error->getMessage()."\n".'XML length : '.Tools::strlen($inputXML)."\n".'Original XML : '.$inputXML, 127);

            return;
        }

        $xmlEntities = $xml->children();
        // Convert multi-dimention xml into an array
        $array = Tools::jsonDecode(Tools::jsonEncode($xmlEntities), true);

        return ($head ? $array[$head] : $array);
    }
}
