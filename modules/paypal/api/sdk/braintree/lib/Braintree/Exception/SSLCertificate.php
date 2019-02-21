<?php
namespace Braintree\Exception;

use Braintree\Exception;

/**
 * Raised when the SSL certificate fails verification.
 *
 * @package    Braintree
 * @subpackage Exception
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class SSLCertificate extends Exception
{

}
class_alias('Braintree\Exception\SSLCertificate', 'Braintree_Exception_SSLCertificate');
