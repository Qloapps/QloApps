<?php
namespace Braintree;

/**
 *
 * Configuration registry
 *
 * @package    Braintree
 * @subpackage Utility
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

class Configuration
{
    public static $global;

    private $_environment = null;
    private $_merchantId = null;
    private $_publicKey = null;
    private $_privateKey = null;
    private $_clientId = null;
    private $_clientSecret = null;
    private $_accessToken = null;
    private $_proxyHost = null;
    private $_proxyPort = null;
    private $_proxyType = null;
    private $_timeout = 60;

    /**
     * Braintree API version to use
     * @access public
     */
     const API_VERSION =  4;

    public function __construct($attribs = [])
    {
        foreach ($attribs as $kind => $value) {
            if ($kind == 'environment') {
                CredentialsParser::assertValidEnvironment($value);
                $this->_environment = $value;
            }
            if ($kind == 'merchantId') {
                $this->_merchantId = $value;
            }
            if ($kind == 'publicKey') {
                $this->_publicKey = $value;
            }
            if ($kind == 'privateKey') {
                $this->_privateKey = $value;
            }
        }

        if (isset($attribs['clientId']) || isset($attribs['accessToken'])) {
            if (isset($attribs['environment']) || isset($attribs['merchantId']) || isset($attribs['publicKey']) || isset($attribs['privateKey'])) {
                throw new Exception\Configuration('Cannot mix OAuth credentials (clientId, clientSecret, accessToken) with key credentials (publicKey, privateKey, environment, merchantId).');
            }
            $parsedCredentials = new CredentialsParser($attribs);

            $this->_environment = $parsedCredentials->getEnvironment();
            $this->_merchantId = $parsedCredentials->getMerchantId();
            $this->_clientId = $parsedCredentials->getClientId();
            $this->_clientSecret = $parsedCredentials->getClientSecret();
            $this->_accessToken = $parsedCredentials->getAccessToken();
        }
    }

    /**
     * resets configuration to default
     * @access public
     */
    public static function reset()
    {
        self::$global = new Configuration();
    }

    public static function gateway()
    {
        return new Gateway(self::$global);
    }

    public static function environment($value=null)
    {
        if (empty($value)) {
            return self::$global->getEnvironment();
        }
        CredentialsParser::assertValidEnvironment($value);
        self::$global->setEnvironment($value);
    }

    public static function merchantId($value=null)
    {
        if (empty($value)) {
            return self::$global->getMerchantId();
        }
        self::$global->setMerchantId($value);
    }

    public static function publicKey($value=null)
    {
        if (empty($value)) {
            return self::$global->getPublicKey();
        }
        self::$global->setPublicKey($value);
    }

    public static function privateKey($value=null)
    {
        if (empty($value)) {
            return self::$global->getPrivateKey();
        }
        self::$global->setPrivateKey($value);
    }

    /**
     * Sets or gets the read timeout to use for making requests.
     *
     * @param integer $value If provided, sets the read timeout
     * @return integer The read timeout used for connecting to Braintree
     */
    public static function timeout($value=null)
    {
        if (empty($value)) {
            return self::$global->getTimeout();
        }
        self::$global->setTimeout($value);
    }

    /**
     * Sets or gets the proxy host to use for connecting to Braintree
     *
     * @param string $value If provided, sets the proxy host
     * @return string The proxy host used for connecting to Braintree
     */
    public static function proxyHost($value = null)
    {
        if (empty($value)) {
            return self::$global->getProxyHost();
        }
        self::$global->setProxyHost($value);
    }

    /**
     * Sets or gets the port of the proxy to use for connecting to Braintree
     *
     * @param string $value If provided, sets the port of the proxy
     * @return string The port of the proxy used for connecting to Braintree
     */
    public static function proxyPort($value = null)
    {
        if (empty($value)) {
            return self::$global->getProxyPort();
        }
        self::$global->setProxyPort($value);
    }

    /**
     * Sets or gets the proxy type to use for connecting to Braintree. This value
     * can be any of the CURLOPT_PROXYTYPE options in PHP cURL.
     *
     * @param string $value If provided, sets the proxy type
     * @return string The proxy type used for connecting to Braintree
     */
    public static function proxyType($value = null)
    {
        if (empty($value)) {
            return self::$global->getProxyType();
        }
        self::$global->setProxyType($value);
    }

    /**
     * Specifies whether or not a proxy is properly configured
     *
     * @return bool true if a proxy is configured properly, false if not
     */
    public static function isUsingProxy()
    {
        $proxyHost = self::$global->getProxyHost();
        $proxyPort = self::$global->getProxyPort();
        return !empty($proxyHost) && !empty($proxyPort);
    }

    public static function assertGlobalHasAccessTokenOrKeys()
    {
        self::$global->assertHasAccessTokenOrKeys();
    }

    public function assertHasAccessTokenOrKeys()
    {
        if (empty($this->_accessToken)) {
            if (empty($this->_merchantId)) {
                throw new Exception\Configuration('Braintree\\Configuration::merchantId needs to be set (or accessToken needs to be passed to Braintree\\Gateway).');
            } else if (empty($this->_environment)) {
                throw new Exception\Configuration('Braintree\\Configuration::environment needs to be set.');
            } else if (empty($this->_publicKey)) {
                throw new Exception\Configuration('Braintree\\Configuration::publicKey needs to be set.');
            } else if (empty($this->_privateKey)) {
                throw new Exception\Configuration('Braintree\\Configuration::privateKey needs to be set.');
            }
        }
    }

    public function assertHasClientCredentials()
    {
        $this->assertHasClientId();
        $this->assertHasClientSecret();
    }

    public function assertHasClientId()
    {
        if (empty($this->_clientId)) {
            throw new Exception\Configuration('clientId needs to be passed to Braintree\\Gateway.');
        }
    }

    public function assertHasClientSecret()
    {
        if (empty($this->_clientSecret)) {
            throw new Exception\Configuration('clientSecret needs to be passed to Braintree\\Gateway.');
        }
    }

    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Do not use this method directly. Pass in the environment to the constructor.
     */
    public function setEnvironment($value)
    {
        $this->_environment = $value;
    }

    public function getMerchantId()
    {
        return $this->_merchantId;
    }

    /**
     * Do not use this method directly. Pass in the merchantId to the constructor.
     */
    public function setMerchantId($value)
    {
        $this->_merchantId = $value;
    }

    public function getPublicKey()
    {
        return $this->_publicKey;
    }

    public function getClientId()
    {
        return $this->_clientId;
    }

    /**
     * Do not use this method directly. Pass in the publicKey to the constructor.
     */
    public function setPublicKey($value)
    {
        $this->_publicKey = $value;
    }

    public function getPrivateKey()
    {
        return $this->_privateKey;
    }

    public function getClientSecret()
    {
        return $this->_clientSecret;
    }

    /**
     * Do not use this method directly. Pass in the privateKey to the constructor.
     */
    public function setPrivateKey($value)
    {
        $this->_privateKey = $value;
    }

    private function setProxyHost($value)
    {
        $this->_proxyHost = $value;
    }

    public function getProxyHost()
    {
        return $this->_proxyHost;
    }

    private function setProxyPort($value)
    {
        $this->_proxyPort = $value;
    }

    public function getProxyPort()
    {
        return $this->_proxyPort;
    }

    private function setProxyType($value)
    {
        $this->_proxyType = $value;
    }

    public function getProxyType()
    {
        return $this->_proxyType;
    }

    private function setTimeout($value)
    {
        $this->_timeout = $value;
    }

    public function getTimeout()
    {
        return $this->_timeout;
    }

    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    public function isAccessToken()
    {
        return !empty($this->_accessToken);
    }

    public function isClientCredentials()
    {
        return !empty($this->_clientId);
    }
    /**
     * returns the base braintree gateway URL based on config values
     *
     * @access public
     * @param none
     * @return string braintree gateway URL
     */
    public function baseUrl()
    {
        return sprintf('%s://%s:%d', $this->protocol(), $this->serverName(), $this->portNumber());
    }

    /**
     * sets the merchant path based on merchant ID
     *
     * @access protected
     * @param none
     * @return string merchant path uri
     */
    public function merchantPath()
    {
        return '/merchants/' . $this->_merchantId;
    }

    /**
     * sets the physical path for the location of the CA certs
     *
     * @access public
     * @param none
     * @return string filepath
     */
    public function caFile($sslPath = NULL)
    {
        $sslPath = $sslPath ? $sslPath : DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                   'ssl' . DIRECTORY_SEPARATOR;
        $caPath = __DIR__ . $sslPath . 'api_braintreegateway_com.ca.crt';

        if (!file_exists($caPath))
        {
            throw new Exception\SSLCaFileNotFound();
        }

        return $caPath;
    }

    /**
     * returns the port number depending on environment
     *
     * @access public
     * @param none
     * @return int portnumber
     */
    public function portNumber()
    {
        if ($this->sslOn()) {
            return 443;
        }
        return getenv("GATEWAY_PORT") ? getenv("GATEWAY_PORT") : 3000;
    }

    /**
     * returns http protocol depending on environment
     *
     * @access public
     * @param none
     * @return string http || https
     */
    public function protocol()
    {
        return $this->sslOn() ? 'https' : 'http';
    }

    /**
     * returns gateway server name depending on environment
     *
     * @access public
     * @param none
     * @return string server domain name
     */
    public function serverName()
    {
        switch($this->_environment) {
         case 'production':
             $serverName = 'api.braintreegateway.com';
             break;
         case 'qa':
             $serverName = 'gateway.qa.braintreepayments.com';
             break;
         case 'sandbox':
             $serverName = 'api.sandbox.braintreegateway.com';
             break;
         case 'development':
         case 'integration':
         default:
             $serverName = 'localhost';
             break;
        }

        return $serverName;
    }

    public function authUrl()
    {
        switch($this->_environment) {
         case 'production':
             $serverName = 'https://auth.venmo.com';
             break;
         case 'qa':
             $serverName = 'https://auth.qa.venmo.com';
             break;
         case 'sandbox':
             $serverName = 'https://auth.sandbox.venmo.com';
             break;
         case 'development':
         case 'integration':
         default:
             $serverName = 'http://auth.venmo.dev:9292';
             break;
        }

        return $serverName;
    }

    /**
     * returns boolean indicating SSL is on or off for this session,
     * depending on environment
     *
     * @access public
     * @param none
     * @return boolean
     */
    public function sslOn()
    {
        switch($this->_environment) {
         case 'integration':
         case 'development':
             $ssl = false;
             break;
         case 'production':
         case 'qa':
         case 'sandbox':
         default:
             $ssl = true;
             break;
        }

       return $ssl;
    }

    /**
     * log message to default logger
     *
     * @param string $message
     *
     */
    public function logMessage($message)
    {
        error_log('[Braintree] ' . $message);
    }
}
Configuration::reset();
class_alias('Braintree\Configuration', 'Braintree_Configuration');
