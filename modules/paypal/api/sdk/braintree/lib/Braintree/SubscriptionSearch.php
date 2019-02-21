<?php
namespace Braintree;

class SubscriptionSearch
{
    public static function billingCyclesRemaining()
    {
        return new RangeNode('billing_cycles_remaining');
    }

    public static function daysPastDue()
    {
        return new RangeNode('days_past_due');
    }

    public static function id()
    {
        return new TextNode('id');
    }

    public static function inTrialPeriod()
    {
        return new MultipleValueNode('in_trial_period', [true, false]);
    }

    public static function merchantAccountId()
    {
        return new MultipleValueNode('merchant_account_id');
    }

    public static function nextBillingDate()
    {
        return new RangeNode('next_billing_date');
    }

    public static function planId()
    {
        return new MultipleValueOrTextNode('plan_id');
    }

    public static function price()
    {
        return new RangeNode('price');
    }

    public static function status()
    {
        return new MultipleValueNode('status', [
            Subscription::ACTIVE,
            Subscription::CANCELED,
            Subscription::EXPIRED,
            Subscription::PAST_DUE,
            Subscription::PENDING,
        ]);
    }

    public static function transactionId()
    {
        return new TextNode('transaction_id');
    }

    public static function ids()
    {
        return new MultipleValueNode('ids');
    }
}
class_alias('Braintree\SubscriptionSearch', 'Braintree_SubscriptionSearch');
