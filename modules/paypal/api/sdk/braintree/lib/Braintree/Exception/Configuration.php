<?php
namespace Braintree\Exception;

use Braintree\Exception;

/**
 * Raised when the Braintree library is not completely configured.
 *
 * @package    Braintree
 * @subpackage Exception
 * @see        Configuration
 */
class Configuration extends Exception
{

}
class_alias('Braintree\Exception\Configuration', 'Braintree_Exception_Configuration');
