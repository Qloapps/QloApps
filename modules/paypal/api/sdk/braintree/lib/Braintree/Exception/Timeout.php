<?php
namespace Braintree\Exception;

use Braintree\Exception;

/**
 * Raised when a Timeout occurs
 *
 * @package    Braintree
 * @subpackage Exception
 * @copyright  2016 Braintree, a division of PayPal, Inc.
 */
class Timeout extends Exception
{

}
class_alias('Braintree\Exception\Timeout', 'Braintree_Exception_Timeout');
