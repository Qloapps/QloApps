<?php
namespace Braintree\Transaction;

use Braintree\Instance;
/**
 * Venmo account details from a transaction
 *
 * @package    Braintree
 * @subpackage Transaction
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * creates an instance of VenmoAccountDetails
 *
 *
 * @package    Braintree
 * @subpackage Transaction
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $sourceDescription
 * @property-read string $token
 * @property-read string $imageUrl
 * @property-read string $username
 * @property-read string $venmo_user_id
 * @uses Instance inherits methods
 */
class VenmoAccountDetails extends Instance
{
    protected $_attributes = array();

    /**
     * @ignore
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);
    }
}
class_alias('Braintree\Transaction\VenmoAccountDetails', 'Braintree_Transaction_VenmoAccountDetails');
