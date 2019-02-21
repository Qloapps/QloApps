<?php
namespace Braintree;

/**
 * Braintree EuropeBankAccount module
 * Creates and manages Braintree Europe Bank Accounts
 *
 * <b>== More information ==</b>
 *
 * See {@link https://developers.braintreepayments.com/javascript+php}<br />
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $account-holder-name
 * @property-read string $bic
 * @property-read string $customerId
 * @property-read string $default
 * @property-read string $image-url
 * @property-read string $mandate-reference-number
 * @property-read string $masked-iban
 * @property-read string $token
 */
class EuropeBankAccount extends Base
{

    /* instance methods */
    /**
     * returns false if default is null or false
     *
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     *  factory method: returns an instance of EuropeBankAccount
     *  to the requesting method, with populated properties
     *
     * @ignore
     * @return EuropeBankAccount
     */
    public static function factory($attributes)
    {
        $defaultAttributes = [
        ];

        $instance = new self();
        $instance->_initialize(array_merge($defaultAttributes, $attributes));
        return $instance;
    }

    /**
     * sets instance properties from an array of values
     *
     * @access protected
     * @param array $europeBankAccountAttribs array of EuropeBankAccount properties
     * @return void
     */
    protected function _initialize($europeBankAccountAttribs)
    {
        $this->_attributes = $europeBankAccountAttribs;
    }
}
class_alias('Braintree\EuropeBankAccount', 'Braintree_EuropeBankAccount');
