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

include_once(dirname(__FILE__) . '/HttpHelper.php');

class PayPalHelper {

    protected $_http = null;
    protected $_apiUrl = null;
    protected $_token = null;
    protected $_apiVersion = null;

    const WK_PAYPAL_SANDBOX_URL = 'https://api.sandbox.paypal.com';
    const WK_PAYPAL_LIVE_URL = 'https://api.paypal.com';
    const WK_PAYPAL_ACCESS_TOKEN_URI = 'v1/oauth2/token';
    const WK_PAYPAL_COMMERCE_ATTRIBUTION_ID = 'QloApp_SI';

	public function __construct() {
		$this->_http = new HttpHelper;
        $this->_apiUrl = self::isSanboxEnvironment() ? self::WK_PAYPAL_SANDBOX_URL : self::WK_PAYPAL_LIVE_URL;
		$this->_setAPIVersion('1');
	}

	protected function _setDefaultHeaders() {
        $this->_http->addHeader("PayPal-Partner-Attribution-Id: " . self::WK_PAYPAL_COMMERCE_ATTRIBUTION_ID);
        $this->_http->addHeader("Content-Type: application/json");
        if($this->_token !== null) {
            $this->_http->addHeader("Authorization: Bearer " . $this->_token);
        }
	}

    protected function _setAPIVersion($version) {
        if((int)$version === 2) {
            $this->_apiVersion = 'v2';
        }
        else {
            $this->_apiVersion = 'v1';
        }
    }

    protected function _createApiUrl($resource) {
		return $this->_apiUrl . "/" . $this->_apiVersion . "/" . $resource;
	}

    protected function _getToken() {
		$this->_http->resetHelper();
		$this->_setDefaultHeaders();
        $this->_setAPIVersion('1');
		$this->_http->setUrl($this->_createApiUrl("oauth2/token"));
        $this->_http->setAuthentication(Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID') . ":" . Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_SECRET'));
        $this->_http->setBody("grant_type=client_credentials");
        $returnData = $this->_http->sendRequest();
        $this->_token = $returnData['access_token'];
    }

    protected function _checkToken() {
        if($this->_token === null) {
            $this->_getToken();
        }
    }

    protected function _respond($data) {
        return array(
            "ack" => true,
            "data" => $data
        );
    }

    public static function isSanboxEnvironment()
    {
        if (Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE') == 'sandbox') {
            return true;
        }
        return false;
    }

    public function getApiUrl($uri)
    {
        if (Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE') == 'sandbox') {
            return self::WK_PAYPAL_SANDBOX_URL.$uri;
        }

        return self::WK_PAYPAL_LIVE_URL.$uri;
    }

    protected function getUUID()
    {
        // random data of 16 bytes (128 bits)
        $bytes = openssl_random_pseudo_bytes(16);

        // Set version to 0100
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);

        // Set bits 6-7 to 10
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);

        // returns UUID of 36 characters
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
