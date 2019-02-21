<?php
namespace Braintree;

use InvalidArgumentException;

/**
 * Braintree PaymentMethodGateway module
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * Creates and manages Braintree PaymentMethods
 *
 * <b>== More information ==</b>
 *
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 */
class PaymentMethodGateway
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


    public function create($attribs)
    {
        Util::verifyKeys(self::createSignature(), $attribs);
        return $this->_doCreate('/payment_methods', ['payment_method' => $attribs]);
    }

    /**
     * find a PaymentMethod by token
     *
     * @param string $token payment method unique id
     * @return CreditCard|PayPalAccount
     * @throws Exception\NotFound
     */
    public function find($token)
    {
        $this->_validateId($token);
        try {
            $path = $this->_config->merchantPath() . '/payment_methods/any/' . $token;
            $response = $this->_http->get($path);
            if (isset($response['creditCard'])) {
                return CreditCard::factory($response['creditCard']);
            } else if (isset($response['paypalAccount'])) {
                return PayPalAccount::factory($response['paypalAccount']);
            } else if (isset($response['coinbaseAccount'])) {
                return CoinbaseAccount::factory($response['coinbaseAccount']);
            } else if (isset($response['applePayCard'])) {
                return ApplePayCard::factory($response['applePayCard']);
            } else if (isset($response['androidPayCard'])) {
                return AndroidPayCard::factory($response['androidPayCard']);
            } else if (isset($response['amexExpressCheckoutCard'])) {
                return AmexExpressCheckoutCard::factory($response['amexExpressCheckoutCard']);
            } else if (isset($response['europeBankAccount'])) {
                return EuropeBankAccount::factory($response['europeBankAccount']);
            } else if (isset($response['venmoAccount'])) {
                return VenmoAccount::factory($response['venmoAccount']);
            } else if (is_array($response)) {
                return UnknownPaymentMethod::factory($response);
            }
        } catch (Exception\NotFound $e) {
            throw new Exception\NotFound(
                'payment method with token ' . $token . ' not found'
            );
        }
    }

    public function update($token, $attribs)
    {
        Util::verifyKeys(self::updateSignature(), $attribs);
        return $this->_doUpdate('/payment_methods/any/' . $token, ['payment_method' => $attribs]);
    }

    public function delete($token)
    {
        $this->_validateId($token);
        $path = $this->_config->merchantPath() . '/payment_methods/any/' . $token;
        $this->_http->delete($path);
        return new Result\Successful();
    }

    public function grant($sharedPaymentMethodToken, $allowVaulting)
    {
        return $this->_doCreate(
            '/payment_methods/grant',
            [
                'payment_method' => [
                    'shared_payment_method_token' => $sharedPaymentMethodToken,
                    'allow_vaulting' => $allowVaulting
                ]
            ]
        );
    }

    public function revoke($sharedPaymentMethodToken)
    {
        return $this->_doCreate(
            '/payment_methods/revoke',
            [
                'payment_method' => [
                    'shared_payment_method_token' => $sharedPaymentMethodToken
                ]
            ]
        );
    }

    private static function baseSignature()
    {
        $billingAddressSignature = AddressGateway::createSignature();
        $optionsSignature = [
            'failOnDuplicatePaymentMethod',
            'makeDefault',
            'verificationMerchantAccountId',
            'verifyCard',
            'verificationAmount'
        ];
        return [
            'billingAddressId',
            'cardholderName',
            'cvv',
            'deviceData',
            'expirationDate',
            'expirationMonth',
            'expirationYear',
            'number',
            'paymentMethodNonce',
            'token',
            ['options' => $optionsSignature],
            ['billingAddress' => $billingAddressSignature]
        ];
    }

    public static function createSignature()
    {
        $signature = array_merge(self::baseSignature(), ['customerId']);
        return $signature;
    }

    public static function updateSignature()
    {
        $billingAddressSignature = AddressGateway::updateSignature();
        array_push($billingAddressSignature, [
            'options' => [
                'updateExisting'
            ]
        ]);
        $signature = array_merge(self::baseSignature(), [
            'deviceSessionId',
            'venmoSdkPaymentMethodCode',
            'fraudMerchantId',
            ['billingAddress' => $billingAddressSignature]
        ]);
        return $signature;
    }

    /**
     * sends the create request to the gateway
     *
     * @ignore
     * @param string $subPath
     * @param array $params
     * @return mixed
     */
    public function _doCreate($subPath, $params)
    {
        $fullPath = $this->_config->merchantPath() . $subPath;
        $response = $this->_http->post($fullPath, $params);

        return $this->_verifyGatewayResponse($response);
    }

    /**
     * sends the update request to the gateway
     *
     * @ignore
     * @param string $subPath
     * @param array $params
     * @return mixed
     */
    public function _doUpdate($subPath, $params)
    {
        $fullPath = $this->_config->merchantPath() . $subPath;
        $response = $this->_http->put($fullPath, $params);

        return $this->_verifyGatewayResponse($response);
    }

    /**
     * generic method for validating incoming gateway responses
     *
     * creates a new CreditCard or PayPalAccount object
     * and encapsulates it inside a Result\Successful object, or
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
        if (isset($response['creditCard'])) {
            return new Result\Successful(
                CreditCard::factory($response['creditCard']),
                'paymentMethod'
            );
        } else if (isset($response['paypalAccount'])) {
            return new Result\Successful(
                PayPalAccount::factory($response['paypalAccount']),
                "paymentMethod"
            );
        } else if (isset($response['coinbaseAccount'])) {
            return new Result\Successful(
                CoinbaseAccount::factory($response['coinbaseAccount']),
                "paymentMethod"
            );
        } else if (isset($response['applePayCard'])) {
            return new Result\Successful(
                ApplePayCard::factory($response['applePayCard']),
                "paymentMethod"
            );
        } else if (isset($response['androidPayCard'])) {
            return new Result\Successful(
                AndroidPayCard::factory($response['androidPayCard']),
                "paymentMethod"
            );
        } else if (isset($response['amexExpressCheckoutCard'])) {
            return new Result\Successful(
                AmexExpressCheckoutCard::factory($response['amexExpressCheckoutCard']),
                "paymentMethod"
            );
        } else if (isset($response['europeBankAccount'])) {
            return new Result\Successful(
                EuropeBankAccount::factory($response['europeBankAccount']),
                "paymentMethod"
            );
        } else if (isset($response['venmoAccount'])) {
            return new Result\Successful(
                VenmoAccount::factory($response['venmoAccount']),
                "paymentMethod"
            );
        } else if (isset($response['paymentMethodNonce'])) {
            return new Result\Successful(
                PaymentMethodNonce::factory($response['paymentMethodNonce']),
                "paymentMethodNonce"
            );
        } else if (isset($response['apiErrorResponse'])) {
            return new Result\Error($response['apiErrorResponse']);
        } else if (is_array($response)) {
            return new Result\Successful(
                UnknownPaymentMethod::factory($response),
                "paymentMethod"
            );
        } else {
            throw new Exception\Unexpected(
            'Expected payment method or apiErrorResponse'
            );
        }
    }

    /**
     * verifies that a valid payment method identifier is being used
     * @ignore
     * @param string $identifier
     * @param Optional $string $identifierType type of identifier supplied, default 'token'
     * @throws InvalidArgumentException
     */
    private function _validateId($identifier = null, $identifierType = 'token')
    {
        if (empty($identifier)) {
           throw new InvalidArgumentException(
                   'expected payment method id to be set'
                   );
        }
        if (!preg_match('/^[0-9A-Za-z_-]+$/', $identifier)) {
            throw new InvalidArgumentException(
                    $identifier . ' is an invalid payment method ' . $identifierType . '.'
                    );
        }
    }
}
class_alias('Braintree\PaymentMethodGateway', 'Braintree_PaymentMethodGateway');
