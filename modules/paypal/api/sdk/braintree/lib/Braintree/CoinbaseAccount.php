<?php
namespace Braintree;

/**
 * Braintree CoinbaseAccount module
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * Manages Braintree CoinbaseAccounts
 *
 * <b>== More information ==</b>
 *
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $customerId
 * @property-read string $token
 * @property-read string $userId
 * @property-read string $userName
 * @property-read string $userEmail
 */
class CoinbaseAccount extends Base
{
    /**
     *  factory method: returns an instance of CoinbaseAccount
     *  to the requesting method, with populated properties
     *
     * @ignore
     * @return CoinbaseAccount
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

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
     * sets instance properties from an array of values
     *
     * @access protected
     * @param array $coinbaseAccountAttribs array of coinbaseAccount data
     * @return void
     */
    protected function _initialize($coinbaseAccountAttribs)
    {
        // set the attributes
        $this->_attributes = $coinbaseAccountAttribs;

        $subscriptionArray = [];
        if (isset($coinbaseAccountAttribs['subscriptions'])) {
            foreach ($coinbaseAccountAttribs['subscriptions'] AS $subscription) {
                $subscriptionArray[] = Subscription::factory($subscription);
            }
        }

        $this->_set('subscriptions', $subscriptionArray);
    }

    /**
     * create a printable representation of the object as:
     * ClassName[property=value, property=value]
     * @return string
     */
    public function  __toString()
    {
        return __CLASS__ . '[' .
                Util::attributesToString($this->_attributes) .']';
    }


    // static methods redirecting to gateway

    public static function find($token)
    {
        return Configuration::gateway()->coinbaseAccount()->find($token);
    }

    public static function update($token, $attributes)
    {
        return Configuration::gateway()->coinbaseAccount()->update($token, $attributes);
    }

    public static function delete($token)
    {
        return Configuration::gateway()->coinbaseAccount()->delete($token);
    }

    public static function sale($token, $transactionAttribs)
    {
        return Configuration::gateway()->coinbaseAccount()->sale($token, $transactionAttribs);
    }
}
class_alias('Braintree\CoinbaseAccount', 'Braintree_CoinbaseAccount');
