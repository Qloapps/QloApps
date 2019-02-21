<?php
namespace Braintree\Transaction;

use Braintree\Instance;

/**
 * CreditCard details from a transaction
 * creates an instance of CreditCardDetails
 *
 * @package    Braintree
 * @subpackage Transaction
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $bin
 * @property-read string $cardType
 * @property-read string $expirationDate
 * @property-read string $expirationMonth
 * @property-read string $expirationYear
 * @property-read string $issuerLocation
 * @property-read string $last4
 * @property-read string $maskedNumber
 * @property-read string $token
 */
class CreditCardDetails extends Instance
{
    protected $_attributes = [];

    /**
     * @ignore
     */
    public function __construct($attributes)
    {
        parent::__construct($attributes);
        $this->_attributes['expirationDate'] = $this->expirationMonth . '/' . $this->expirationYear;
        $this->_attributes['maskedNumber'] = $this->bin . '******' . $this->last4;

    }
}
class_alias('Braintree\Transaction\CreditCardDetails', 'Braintree_Transaction_CreditCardDetails');
