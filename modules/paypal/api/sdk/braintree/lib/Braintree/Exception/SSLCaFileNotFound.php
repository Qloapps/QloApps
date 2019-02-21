<?php
namespace Braintree\Exception;

use Braintree\Exception;

/**
 * Raised when the SSL CaFile is not found.
 *
 * @package    Braintree
 * @subpackage Exception
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class SSLCaFileNotFound extends Exception
{

}
class_alias('Braintree\Exception\SSLCaFileNotFound', 'Braintree_Exception_SSLCaFileNotFound');
