<?php
namespace Braintree;

/**
 * Braintree PaymentMethodNonce module
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * Creates and manages Braintree PaymentMethodNonces
 *
 * <b>== More information ==</b>
 *
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 */
class PaymentMethodNonce extends Base
{
    // static methods redirecting to gateway

    public static function create($token)
    {
        return Configuration::gateway()->paymentMethodNonce()->create($token);
    }

    public static function find($nonce)
    {
        return Configuration::gateway()->paymentMethodNonce()->find($nonce);
    }

    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    protected function _initialize($nonceAttributes)
    {
        $this->_attributes = $nonceAttributes;
        $this->_set('nonce', $nonceAttributes['nonce']);
        $this->_set('type', $nonceAttributes['type']);

        if(isset($nonceAttributes['threeDSecureInfo'])) {
            $this->_set('threeDSecureInfo', ThreeDSecureInfo::factory($nonceAttributes['threeDSecureInfo']));
        }
    }
}
class_alias('Braintree\PaymentMethodNonce', 'Braintree_PaymentMethodNonce');
