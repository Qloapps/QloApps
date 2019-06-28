<?php
namespace Braintree\Subscription;

use Braintree\Instance;

/**
 * Status details from a subscription
 * Creates an instance of StatusDetails, as part of a subscription response
 *
 * @package    Braintree
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $price
 * @property-read string $balance
 * @property-read string $status
 * @property-read string $timestamp
 * @property-read string $subscriptionSource
 * @property-read string $user
 */
class StatusDetails extends Instance
{
}
class_alias('Braintree\Subscription\StatusDetails', 'Braintree_Subscription_StatusDetails');
