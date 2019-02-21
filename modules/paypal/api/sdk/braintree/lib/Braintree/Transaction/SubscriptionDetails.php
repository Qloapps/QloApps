<?php
namespace Braintree\Transaction;

use Braintree\Instance;

/**
 * Customer details from a transaction
 * Creates an instance of customer details as returned from a transaction
 *
 * @package    Braintree
 * @subpackage Transaction
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $billing_period_start_date
 * @property-read string $billing_period_end_date
 */
class SubscriptionDetails extends Instance
{
}
class_alias('Braintree\Transaction\SubscriptionDetails', 'Braintree_Transaction_SubscriptionDetails');
