<?php
namespace Braintree\Exception;

use Braintree\Exception;

/**
 * Raised when authentication fails.
 * This may be caused by an incorrect Configuration
 *
 * @package    Braintree
 * @subpackage Exception
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class Authentication extends Exception
{

}
class_alias('Braintree\Exception\Authentication', 'Braintree_Exception_Authentication');
