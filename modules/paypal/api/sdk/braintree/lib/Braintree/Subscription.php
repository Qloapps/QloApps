<?php
namespace Braintree;

/**
 * Braintree Subscription module
 *
 * <b>== More information ==</b>
 *
 * For more detailed information on Subscriptions, see {@link http://www.braintreepayments.com/gateway/subscription-api http://www.braintreepaymentsolutions.com/gateway/subscription-api}
 *
 * PHP Version 5
 *
 * @package   Braintree
 * @copyright 2015 Braintree, a division of PayPal, Inc.
 */
class Subscription extends Base
{
    const ACTIVE = 'Active';
    const CANCELED = 'Canceled';
    const EXPIRED = 'Expired';
    const PAST_DUE = 'Past Due';
    const PENDING = 'Pending';

    // Subscription Sources
    const API           = 'api';
    const CONTROL_PANEL = 'control_panel';
    const RECURRING     = 'recurring';

    /**
     * @ignore
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);

        return $instance;
    }

    /**
     * @ignore
     */
    protected function _initialize($attributes)
    {
        $this->_attributes = $attributes;

        $addOnArray = [];
        if (isset($attributes['addOns'])) {
            foreach ($attributes['addOns'] AS $addOn) {
                $addOnArray[] = AddOn::factory($addOn);
            }
        }
        $this->_attributes['addOns'] = $addOnArray;

        $discountArray = [];
        if (isset($attributes['discounts'])) {
            foreach ($attributes['discounts'] AS $discount) {
                $discountArray[] = Discount::factory($discount);
            }
        }
        $this->_attributes['discounts'] = $discountArray;

        if (isset($attributes['descriptor'])) {
            $this->_set('descriptor', new Descriptor($attributes['descriptor']));
        }

        $statusHistory = [];
        if (isset($attributes['statusHistory'])) {
            foreach ($attributes['statusHistory'] AS $history) {
                $statusHistory[] = new Subscription\StatusDetails($history);
            }
        }
        $this->_attributes['statusHistory'] = $statusHistory;

        $transactionArray = [];
        if (isset($attributes['transactions'])) {
            foreach ($attributes['transactions'] AS $transaction) {
                $transactionArray[] = Transaction::factory($transaction);
            }
        }
        $this->_attributes['transactions'] = $transactionArray;
    }

    /**
     * returns a string representation of the customer
     * @return string
     */
    public function  __toString()
    {
        $excludedAttributes = ['statusHistory'];

        $displayAttributes = [];
        foreach($this->_attributes as $key => $val) {
            if (!in_array($key, $excludedAttributes)) {
                $displayAttributes[$key] = $val;
            }
        }

        return __CLASS__ . '[' .
                Util::attributesToString($displayAttributes) .']';
    }


    // static methods redirecting to gateway

    public static function create($attributes)
    {
        return Configuration::gateway()->subscription()->create($attributes);
    }

    public static function find($id)
    {
        return Configuration::gateway()->subscription()->find($id);
    }

    public static function search($query)
    {
        return Configuration::gateway()->subscription()->search($query);
    }

    public static function fetch($query, $ids)
    {
        return Configuration::gateway()->subscription()->fetch($query, $ids);
    }

    public static function update($subscriptionId, $attributes)
    {
        return Configuration::gateway()->subscription()->update($subscriptionId, $attributes);
    }

    public static function retryCharge($subscriptionId, $amount = null)
    {
        return Configuration::gateway()->subscription()->retryCharge($subscriptionId, $amount);
    }

    public static function cancel($subscriptionId)
    {
        return Configuration::gateway()->subscription()->cancel($subscriptionId);
    }
}
class_alias('Braintree\Subscription', 'Braintree_Subscription');
