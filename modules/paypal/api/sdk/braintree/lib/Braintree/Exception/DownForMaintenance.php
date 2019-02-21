<?php
namespace Braintree\Exception;

use Braintree\Exception;

/**
 * Raised when the gateway is down for maintenance.
 *
 * @package    Braintree
 * @subpackage Exception
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class DownForMaintenance extends Exception
{

}
class_alias('Braintree\Exception\DownForMaintenance', 'Braintree_Exception_DownForMaintenance');
