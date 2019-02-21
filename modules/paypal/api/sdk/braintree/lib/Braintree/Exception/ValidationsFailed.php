<?php
namespace Braintree\Exception;

use Braintree\Exception;

/**
 * Raised from non-validating methods when gateway validations fail.
 *
 * @package    Braintree
 * @subpackage Exception
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class ValidationsFailed extends Exception
{

}
class_alias('Braintree\Exception\ValidationsFailed', 'Braintree_Exception_ValidationsFailed');
