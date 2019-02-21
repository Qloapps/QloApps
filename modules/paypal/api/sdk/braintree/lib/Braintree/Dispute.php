<?php
namespace Braintree;

/**
 * Creates an instance of Dispute as returned from a transaction
 *
 *
 * @package    Braintree
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $amount
 * @property-read string $currencyIsoCode
 * @property-read date   $receivedDate
 * @property-read string $reason
 * @property-read string $status
 * @property-read string $disbursementDate
 * @property-read object $transactionDetails
 */
final class Dispute extends Base
{
    protected $_attributes = [];

    /* Dispute Status */
    const OPEN  = 'open';
    const WON  = 'won';
    const LOST = 'lost';

    /* deprecated; for backwards compatibilty */
    const Open  = 'open';

    /* Dispute Reason */
    const CANCELLED_RECURRING_TRANSACTION = "cancelled_recurring_transaction";
    const CREDIT_NOT_PROCESSED            = "credit_not_processed";
    const DUPLICATE                       = "duplicate";
    const FRAUD                           = "fraud";
    const GENERAL                         = "general";
    const INVALID_ACCOUNT                 = "invalid_account";
    const NOT_RECOGNIZED                  = "not_recognized";
    const PRODUCT_NOT_RECEIVED            = "product_not_received";
    const PRODUCT_UNSATISFACTORY          = "product_unsatisfactory";
    const TRANSACTION_AMOUNT_DIFFERS      = "transaction_amount_differs";
    const RETRIEVAL                       = "retrieval";

    /* Dispute Kind */
    const CHARGEBACK      = 'chargeback';
    const PRE_ARBITRATION = 'pre_arbitration';
    // RETRIEVAL for kind already defined under Dispute Reason

    protected function _initialize($disputeAttribs)
    {
        $this->_attributes = $disputeAttribs;

        if (isset($disputeAttribs['transaction'])) {
            $this->_set('transactionDetails',
                new Dispute\TransactionDetails($disputeAttribs['transaction'])
            );
        }
    }

    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    public function  __toString()
    {
        $display = [
            'amount', 'reason', 'status',
            'replyByDate', 'receivedDate', 'currencyIsoCode'
            ];

        $displayAttributes = [];
        foreach ($display AS $attrib) {
            $displayAttributes[$attrib] = $this->$attrib;
        }
        return __CLASS__ . '[' .
                Util::attributesToString($displayAttributes) .']';
    }
}
class_alias('Braintree\Dispute', 'Braintree_Dispute');
