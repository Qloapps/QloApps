<?php
namespace Braintree;

/**
 * Braintree Transparent Redirect module
 * Static class providing methods to build Transparent Redirect urls.
 *
 * The TransparentRedirect module provides methods to build the tr_data param
 * that must be submitted when using the transparent redirect API.
 * For more information
 * about transparent redirect, see (TODO).
 *
 * You must provide a redirectUrl to which the gateway will redirect the
 * user the action is complete.
 *
 * <code>
 *   $trData = TransparentRedirect::createCustomerData(array(
 *     'redirectUrl => 'http://example.com/redirect_back_to_merchant_site',
 *      ));
 * </code>
 *
 * In addition to the redirectUrl, any data that needs to be protected
 * from user tampering should be included in the trData.
 * For example, to prevent the user from tampering with the transaction
 * amount, include the amount in the trData.
 *
 * <code>
 *   $trData = TransparentRedirect::transactionData(array(
 *     'redirectUrl' => 'http://example.com/complete_transaction',
 *     'transaction' => array('amount' => '100.00'),
 *   ));
 *
 *  </code>
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class TransparentRedirect
{
    // Request Kinds
    const CREATE_TRANSACTION = 'create_transaction';
    const CREATE_PAYMENT_METHOD = 'create_payment_method';
    const UPDATE_PAYMENT_METHOD = 'update_payment_method';
    const CREATE_CUSTOMER = 'create_customer';
    const UPDATE_CUSTOMER = 'update_customer';

    /**
     * @ignore
     * don't permit an explicit call of the constructor!
     * (like $t = new TransparentRedirect())
     */
    protected function __construct()
    {

    }


    // static methods redirecting to gateway

    public static function confirm($queryString)
    {
        return Configuration::gateway()->transparentRedirect()->confirm($queryString);
    }

    public static function createCreditCardData($params)
    {
        return Configuration::gateway()->transparentRedirect()->createCreditCardData($params);
    }

    public static function createCustomerData($params)
    {
        return Configuration::gateway()->transparentRedirect()->createCustomerData($params);
    }

    public static function url()
    {
        return Configuration::gateway()->transparentRedirect()->url();
    }

    public static function transactionData($params)
    {
        return Configuration::gateway()->transparentRedirect()->transactionData($params);
    }

    public static function updateCreditCardData($params)
    {
        return Configuration::gateway()->transparentRedirect()->updateCreditCardData($params);
    }

    public static function updateCustomerData($params)
    {
        return Configuration::gateway()->transparentRedirect()->updateCustomerData($params);
    }

    public static function parseAndValidateQueryString($queryString)
    {
        return Configuration::gateway()->transparentRedirect()->parseAndValidateQueryString($queryString);
    }
}
class_alias('Braintree\TransparentRedirect', 'Braintree_TransparentRedirect');
