<?php
namespace Braintree;

use InvalidArgumentException;

/**
 * Braintree PayPalAccountGateway module
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * Manages Braintree PayPalAccounts
 *
 * <b>== More information ==</b>
 *
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class PayPalAccountGateway
{
    private $_gateway;
    private $_config;
    private $_http;

    public function __construct($gateway)
    {
        $this->_gateway = $gateway;
        $this->_config = $gateway->config;
        $this->_config->assertHasAccessTokenOrKeys();
        $this->_http = new Http($gateway->config);
    }


    /**
     * find a paypalAccount by token
     *
     * @access public
     * @param string $token paypal accountunique id
     * @return PayPalAccount
     * @throws Exception\NotFound
     */
    public function find($token)
    {
        $this->_validateId($token);
        try {
            $path = $this->_config->merchantPath() . '/payment_methods/paypal_account/' . $token;
            $response = $this->_http->get($path);
            return PayPalAccount::factory($response['paypalAccount']);
        } catch (Exception\NotFound $e) {
            throw new Exception\NotFound(
                'paypal account with token ' . $token . ' not found'
            );
        }

    }

    /**
     * updates the paypalAccount record
     *
     * if calling this method in context, $token
     * is the 2nd attribute. $token is not sent in object context.
     *
     * @access public
     * @param array $attributes
     * @param string $token (optional)
     * @return Result\Successful or Result\Error
     */
    public function update($token, $attributes)
    {
        Util::verifyKeys(self::updateSignature(), $attributes);
        $this->_validateId($token);
        return $this->_doUpdate('put', '/payment_methods/paypal_account/' . $token, ['paypalAccount' => $attributes]);
    }

    public function delete($token)
    {
        $this->_validateId($token);
        $path = $this->_config->merchantPath() . '/payment_methods/paypal_account/' . $token;
        $this->_http->delete($path);
        return new Result\Successful();
    }

    /**
     * create a new sale for the current PayPal account
     *
     * @param string $token
     * @param array $transactionAttribs
     * @return Result\Successful|Result\Error
     * @see Transaction::sale()
     */
    public function sale($token, $transactionAttribs)
    {
        $this->_validateId($token);
        return Transaction::sale(
            array_merge(
                $transactionAttribs,
                ['paymentMethodToken' => $token]
            )
        );
    }

    public static function updateSignature()
    {
        return [
            'token',
            ['options' => ['makeDefault']]
        ];
    }

    /**
     * sends the update request to the gateway
     *
     * @ignore
     * @param string $subPath
     * @param array $params
     * @return mixed
     */
    private function _doUpdate($httpVerb, $subPath, $params)
    {
        $fullPath = $this->_config->merchantPath() . $subPath;
        $response = $this->_http->$httpVerb($fullPath, $params);
        return $this->_verifyGatewayResponse($response);
    }

    /**
     * generic method for validating incoming gateway responses
     *
     * creates a new PayPalAccount object and encapsulates
     * it inside a Result\Successful object, or
     * encapsulates a Errors object inside a Result\Error
     * alternatively, throws an Unexpected exception if the response is invalid.
     *
     * @ignore
     * @param array $response gateway response values
     * @return Result\Successful|Result\Error
     * @throws Exception\Unexpected
     */
    private function _verifyGatewayResponse($response)
    {
        if (isset($response['paypalAccount'])) {
            // return a populated instance of PayPalAccount
            return new Result\Successful(
                    PayPalAccount::factory($response['paypalAccount'])
            );
        } else if (isset($response['apiErrorResponse'])) {
            return new Result\Error($response['apiErrorResponse']);
        } else {
            throw new Exception\Unexpected(
            'Expected paypal account or apiErrorResponse'
            );
        }
    }

    /**
     * verifies that a valid paypal account identifier is being used
     * @ignore
     * @param string $identifier
     * @param Optional $string $identifierType type of identifier supplied, default 'token'
     * @throws InvalidArgumentException
     */
    private function _validateId($identifier = null, $identifierType = 'token')
    {
        if (empty($identifier)) {
           throw new InvalidArgumentException(
                   'expected paypal account id to be set'
                   );
        }
        if (!preg_match('/^[0-9A-Za-z_-]+$/', $identifier)) {
            throw new InvalidArgumentException(
                    $identifier . ' is an invalid paypal account ' . $identifierType . '.'
                    );
        }
    }
}
class_alias('Braintree\PayPalAccountGateway', 'Braintree_PayPalAccountGateway');
