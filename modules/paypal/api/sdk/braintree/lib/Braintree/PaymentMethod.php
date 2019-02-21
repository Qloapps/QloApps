<?php
namespace Braintree;

/**
 * Braintree PaymentMethod module
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
class PaymentMethod extends Base
{
    // static methods redirecting to gateway

    public static function create($attribs)
    {
        return Configuration::gateway()->paymentMethod()->create($attribs);
    }

    public static function find($token)
    {
        return Configuration::gateway()->paymentMethod()->find($token);
    }

    public static function update($token, $attribs)
    {
        return Configuration::gateway()->paymentMethod()->update($token, $attribs);
    }

    public static function delete($token)
    {
        return Configuration::gateway()->paymentMethod()->delete($token);
    }
}
class_alias('Braintree\PaymentMethod', 'Braintree_PaymentMethod');
