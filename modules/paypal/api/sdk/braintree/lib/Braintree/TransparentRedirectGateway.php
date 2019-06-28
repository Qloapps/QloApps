<?php
namespace Braintree;

use InvalidArgumentException;
use DateTime;
use DateTimeZone;

/**
 * Braintree Transparent Redirect Gateway module
 * Static class providing methods to build Transparent Redirect urls
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class TransparentRedirectGateway
{
    private $_gateway;
    private $_config;

    public function __construct($gateway)
    {
        $this->_gateway = $gateway;
        $this->_config = $gateway->config;
        $this->_config->assertHasAccessTokenOrKeys();
    }

    /**
     *
     * @ignore
     */
    private static $_transparentRedirectKeys = 'redirectUrl';
    private static $_createCustomerSignature;
    private static $_updateCustomerSignature;
    private static $_transactionSignature;
    private static $_createCreditCardSignature;
    private static $_updateCreditCardSignature;

    /**
     * create signatures for different call types
     * @ignore
     */
    public static function init()
    {

        self::$_createCustomerSignature = [
            self::$_transparentRedirectKeys,
            ['customer' => CustomerGateway::createSignature()],
            ];
        self::$_updateCustomerSignature = [
            self::$_transparentRedirectKeys,
            'customerId',
            ['customer' => CustomerGateway::updateSignature()],
            ];
        self::$_transactionSignature = [
            self::$_transparentRedirectKeys,
            ['transaction' => TransactionGateway::createSignature()],
            ];
        self::$_createCreditCardSignature = [
            self::$_transparentRedirectKeys,
            ['creditCard' => CreditCardGateway::createSignature()],
            ];
        self::$_updateCreditCardSignature = [
            self::$_transparentRedirectKeys,
            'paymentMethodToken',
            ['creditCard' => CreditCardGateway::updateSignature()],
            ];
    }

    public function confirm($queryString)
    {
        $params = TransparentRedirect::parseAndValidateQueryString(
                $queryString
        );
        $confirmationKlasses = [
            TransparentRedirect::CREATE_TRANSACTION => 'Braintree\TransactionGateway',
            TransparentRedirect::CREATE_CUSTOMER => 'Braintree\CustomerGateway',
            TransparentRedirect::UPDATE_CUSTOMER => 'Braintree\CustomerGateway',
            TransparentRedirect::CREATE_PAYMENT_METHOD => 'Braintree\CreditCardGateway',
            TransparentRedirect::UPDATE_PAYMENT_METHOD => 'Braintree\CreditCardGateway',
        ];
        $confirmationGateway = new $confirmationKlasses[$params["kind"]]($this->_gateway);
        return $confirmationGateway->_doCreate('/transparent_redirect_requests/' . $params['id'] . '/confirm', []);
    }

    /**
     * returns the trData string for creating a credit card,
     * @param array $params
     * @return string
     */
    public function createCreditCardData($params)
    {
        Util::verifyKeys(
                self::$_createCreditCardSignature,
                $params
                );
        $params["kind"] = TransparentRedirect::CREATE_PAYMENT_METHOD;
        return $this->_data($params);
    }

    /**
     * returns the trData string for creating a customer.
     * @param array $params
     * @return string
     */
    public function createCustomerData($params)
    {
        Util::verifyKeys(
                self::$_createCustomerSignature,
                $params
                );
        $params["kind"] = TransparentRedirect::CREATE_CUSTOMER;
        return $this->_data($params);

    }

    public function url()
    {
        return $this->_config->baseUrl() . $this->_config->merchantPath() . '/transparent_redirect_requests';
    }

    /**
     * returns the trData string for creating a transaction
     * @param array $params
     * @return string
     */
    public function transactionData($params)
    {
        Util::verifyKeys(
                self::$_transactionSignature,
                $params
                );
        $params["kind"] = TransparentRedirect::CREATE_TRANSACTION;
        $transactionType = isset($params['transaction']['type']) ?
            $params['transaction']['type'] :
            null;
        if ($transactionType != Transaction::SALE && $transactionType != Transaction::CREDIT) {
           throw new InvalidArgumentException(
                   'expected transaction[type] of sale or credit, was: ' .
                   $transactionType
                   );
        }

        return $this->_data($params);
    }

    /**
     * Returns the trData string for updating a credit card.
     *
     *  The paymentMethodToken of the credit card to update is required.
     *
     * <code>
     * $trData = TransparentRedirect::updateCreditCardData(array(
     *     'redirectUrl' => 'http://example.com/redirect_here',
     *     'paymentMethodToken' => 'token123',
     *   ));
     * </code>
     *
     * @param array $params
     * @return string
     */
    public function updateCreditCardData($params)
    {
        Util::verifyKeys(
                self::$_updateCreditCardSignature,
                $params
                );
        if (!isset($params['paymentMethodToken'])) {
            throw new InvalidArgumentException(
                   'expected params to contain paymentMethodToken.'
                   );
        }
        $params["kind"] = TransparentRedirect::UPDATE_PAYMENT_METHOD;
        return $this->_data($params);
    }

    /**
     * Returns the trData string for updating a customer.
     *
     *  The customerId of the customer to update is required.
     *
     * <code>
     * $trData = TransparentRedirect::updateCustomerData(array(
     *     'redirectUrl' => 'http://example.com/redirect_here',
     *     'customerId' => 'customer123',
     *   ));
     * </code>
     *
     * @param array $params
     * @return string
     */
    public function updateCustomerData($params)
    {
        Util::verifyKeys(
                self::$_updateCustomerSignature,
                $params
                );
        if (!isset($params['customerId'])) {
            throw new InvalidArgumentException(
                   'expected params to contain customerId of customer to update'
                   );
        }
        $params["kind"] = TransparentRedirect::UPDATE_CUSTOMER;
        return $this->_data($params);
    }

    public function parseAndValidateQueryString($queryString)
    {
        // parse the params into an array
        parse_str($queryString, $params);
        // remove the hash
        $queryStringWithoutHash = null;
        if (preg_match('/^(.*)&hash=[a-f0-9]+$/', $queryString, $match)) {
            $queryStringWithoutHash = $match[1];
        }

        if($params['http_status'] != '200') {
            $message = null;
            if(array_key_exists('bt_message', $params)) {
                $message = $params['bt_message'];
            }
            Util::throwStatusCodeException(isset($params['http_status']) ? $params['http_status'] : null, $message);
        }

        // recreate the hash and compare it
        if ($this->_hash($queryStringWithoutHash) == $params['hash']) {
            return $params;
        } else {
            throw new Exception\ForgedQueryString();
        }
    }


    /**
     *
     * @ignore
     */
    private function _data($params)
    {
        if (!isset($params['redirectUrl'])) {
            throw new InvalidArgumentException(
                    'expected params to contain redirectUrl'
                    );
        }
        $params = $this->_underscoreKeys($params);
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $trDataParams = array_merge($params,
            [
                'api_version' => Configuration::API_VERSION,
                'public_key'  => $this->_config->publicKey(),
                'time'        => $now->format('YmdHis'),
            ]
        );
        ksort($trDataParams);
        $urlEncodedData = http_build_query($trDataParams, null, "&");
        $signatureService = new SignatureService(
            $this->_config->privateKey(),
            "Braintree\Digest::hexDigestSha1"
        );
        return $signatureService->sign($urlEncodedData);
    }

    private function _underscoreKeys($array)
    {
        foreach($array as $key=>$value)
        {
            $newKey = Util::camelCaseToDelimiter($key, '_');
            unset($array[$key]);
            if (is_array($value))
            {
                $array[$newKey] = $this->_underscoreKeys($value);
            }
            else
            {
                $array[$newKey] = $value;
            }
        }
        return $array;
    }

    /**
     * @ignore
     */
    private function _hash($string)
    {
        return Digest::hexDigestSha1($this->_config->privateKey(), $string);
    }
}
TransparentRedirectGateway::init();
class_alias('Braintree\TransparentRedirectGateway', 'Braintree_TransparentRedirectGateway');
