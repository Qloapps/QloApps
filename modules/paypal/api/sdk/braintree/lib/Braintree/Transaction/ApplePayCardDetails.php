<?php
namespace Braintree\Transaction;

use Braintree\Instance;

/**
 * Apple Pay card details from a transaction
 *
 * @package    Braintree
 * @subpackage Transaction
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * creates an instance of ApplePayCardDetails
 *
 *
 * @package    Braintree
 * @subpackage Transaction
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $cardType
 * @property-read string $paymentInstrumentName
 * @property-read string $expirationMonth
 * @property-read string $expirationYear
 * @property-read string $cardholderName
 * @property-read string $sourceDescription
 */
class ApplePayCardDetails extends Instance
{
    protected $_attributes = [];

    /**
     * @ignore
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);
    }
}
class_alias('Braintree\Transaction\ApplePayCardDetails', 'Braintree_Transaction_ApplePayCardDetails');
