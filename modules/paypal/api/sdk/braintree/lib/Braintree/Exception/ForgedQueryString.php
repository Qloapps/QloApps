<?php
namespace Braintree\Exception;

use Braintree\Exception;

/**
 * Raised when a suspected forged query string is present
 * Raised from methods that confirm transparent redirect requests
 * when the given query string cannot be verified. This may indicate
 * an attempted hack on the merchant's transparent redirect
 * confirmation URL.
 *
 * @package    Braintree
 * @subpackage Exception
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class ForgedQueryString extends Exception
{

}
class_alias('Braintree\Exception\ForgedQueryString', 'Braintree_Exception_ForgedQueryString');
