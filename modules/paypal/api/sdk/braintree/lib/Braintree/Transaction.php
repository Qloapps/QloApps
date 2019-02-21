<?php
namespace Braintree;

/**
 * Braintree Transaction processor
 * Creates and manages transactions
 *
 * At minimum, an amount, credit card number, and
 * credit card expiration date are required.
 *
 * <b>Minimalistic example:</b>
 * <code>
 * Transaction::saleNoValidate(array(
 *   'amount' => '100.00',
 *   'creditCard' => array(
 *       'number' => '5105105105105100',
 *       'expirationDate' => '05/12',
 *       ),
 *   ));
 * </code>
 *
 * <b>Full example:</b>
 * <code>
 * Transaction::saleNoValidate(array(
 *    'amount'      => '100.00',
 *    'orderId'    => '123',
 *    'channel'    => 'MyShoppingCardProvider',
 *    'creditCard' => array(
 *         // if token is omitted, the gateway will generate a token
 *         'token' => 'credit_card_123',
 *         'number' => '5105105105105100',
 *         'expirationDate' => '05/2011',
 *         'cvv' => '123',
 *    ),
 *    'customer' => array(
 *     // if id is omitted, the gateway will generate an id
 *     'id'    => 'customer_123',
 *     'firstName' => 'Dan',
 *     'lastName' => 'Smith',
 *     'company' => 'Braintree',
 *     'email' => 'dan@example.com',
 *     'phone' => '419-555-1234',
 *     'fax' => '419-555-1235',
 *     'website' => 'http://braintreepayments.com'
 *    ),
 *    'billing'    => array(
 *      'firstName' => 'Carl',
 *      'lastName'  => 'Jones',
 *      'company'    => 'Braintree',
 *      'streetAddress' => '123 E Main St',
 *      'extendedAddress' => 'Suite 403',
 *      'locality' => 'Chicago',
 *      'region' => 'IL',
 *      'postalCode' => '60622',
 *      'countryName' => 'United States of America'
 *    ),
 *    'shipping' => array(
 *      'firstName'    => 'Andrew',
 *      'lastName'    => 'Mason',
 *      'company'    => 'Braintree',
 *      'streetAddress'    => '456 W Main St',
 *      'extendedAddress'    => 'Apt 2F',
 *      'locality'    => 'Bartlett',
 *      'region'    => 'IL',
 *      'postalCode'    => '60103',
 *      'countryName'    => 'United States of America'
 *    ),
 *    'customFields'    => array(
 *      'birthdate'    => '11/13/1954'
 *    )
 *  )
 * </code>
 *
 * <b>== Storing in the Vault ==</b>
 *
 * The customer and credit card information used for
 * a transaction can be stored in the vault by setting
 * <i>transaction[options][storeInVault]</i> to true.
 * <code>
 *   $transaction = Transaction::saleNoValidate(array(
 *     'customer' => array(
 *       'firstName'    => 'Adam',
 *       'lastName'    => 'Williams'
 *     ),
 *     'creditCard'    => array(
 *       'number'    => '5105105105105100',
 *       'expirationDate'    => '05/2012'
 *     ),
 *     'options'    => array(
 *       'storeInVault'    => true
 *     )
 *   ));
 *
 *  echo $transaction->customerDetails->id
 *  // '865534'
 *  echo $transaction->creditCardDetails->token
 *  // '6b6m'
 * </code>
 *
 * To also store the billing address in the vault, pass the
 * <b>addBillingAddressToPaymentMethod</b> option.
 * <code>
 *   Transaction.saleNoValidate(array(
 *    ...
 *     'options' => array(
 *       'storeInVault' => true
 *       'addBillingAddressToPaymentMethod' => true
 *     )
 *   ));
 * </code>
 *
 * <b>== Submitting for Settlement==</b>
 *
 * This can only be done when the transction's
 * status is <b>authorized</b>. If <b>amount</b> is not specified,
 * the full authorized amount will be settled. If you would like to settle
 * less than the full authorized amount, pass the desired amount.
 * You cannot settle more than the authorized amount.
 *
 * A transaction can be submitted for settlement when created by setting
 * $transaction[options][submitForSettlement] to true.
 *
 * <code>
 *   $transaction = Transaction::saleNoValidate(array(
 *     'amount'    => '100.00',
 *     'creditCard'    => array(
 *       'number'    => '5105105105105100',
 *       'expirationDate'    => '05/2012'
 *     ),
 *     'options'    => array(
 *       'submitForSettlement'    => true
 *     )
 *   ));
 * </code>
 *
 * <b>== More information ==</b>
 *
 * For more detailed information on Transactions, see {@link http://www.braintreepayments.com/gateway/transaction-api http://www.braintreepaymentsolutions.com/gateway/transaction-api}
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 *
 * @property-read string $avsErrorResponseCode
 * @property-read string $avsPostalCodeResponseCode
 * @property-read string $avsStreetAddressResponseCode
 * @property-read string $cvvResponseCode
 * @property-read string $id transaction id
 * @property-read string $amount transaction amount
 * @property-read Braintree\Transaction\AddressDetails $billingDetails transaction billing address
 * @property-read string $createdAt transaction created timestamp
 * @property-read Braintree\ApplePayCardDetails $applePayCardDetails transaction Apple Pay card info
 * @property-read Braintree\AndroidPayCardDetails $androidPayCardDetails transaction Android Pay card info
 * @property-read Braintree\AmexExpressCheckoutCardDetails $amexExpressCheckoutCardDetails transaction Amex Express Checkout card info
 * @property-read Braintree\CreditCardDetails $creditCardDetails transaction credit card info
 * @property-read Braintree\CoinbaseAccountDetails $coinbaseDetails transaction Coinbase account info
 * @property-read Braintree\PayPalAccountDetails $paypalDetails transaction paypal account info
 * @property-read Braintree\Customer $customerDetails transaction customer info
 * @property-read Braintree\VenmoAccount $venmoAccountDetails transaction Venmo Account info
 * @property-read array  $customFields custom fields passed with the request
 * @property-read string $processorResponseCode gateway response code
 * @property-read string $additionalProcessorResponse raw response from processor
 * @property-read Braintree\Transaction\AddressDetails $shippingDetails transaction shipping address
 * @property-read string $status transaction status
 * @property-read array  $statusHistory array of StatusDetails objects
 * @property-read string $type transaction type
 * @property-read string $updatedAt transaction updated timestamp
 * @property-read Braintree\Disbursement $disbursementDetails populated when transaction is disbursed
 * @property-read Braintree\Dispute $disputes populated when transaction is disputed
 *
 */

final class Transaction extends Base
{
    // Transaction Status
    const AUTHORIZATION_EXPIRED    = 'authorization_expired';
    const AUTHORIZING              = 'authorizing';
    const AUTHORIZED               = 'authorized';
    const GATEWAY_REJECTED         = 'gateway_rejected';
    const FAILED                   = 'failed';
    const PROCESSOR_DECLINED       = 'processor_declined';
    const SETTLED                  = 'settled';
    const SETTLING                 = 'settling';
    const SUBMITTED_FOR_SETTLEMENT = 'submitted_for_settlement';
    const VOIDED                   = 'voided';
    const UNRECOGNIZED             = 'unrecognized';
    const SETTLEMENT_DECLINED      = 'settlement_declined';
    const SETTLEMENT_PENDING       = 'settlement_pending';
    const SETTLEMENT_CONFIRMED     = 'settlement_confirmed';

    // Transaction Escrow Status
    const ESCROW_HOLD_PENDING    = 'hold_pending';
    const ESCROW_HELD            = 'held';
    const ESCROW_RELEASE_PENDING = 'release_pending';
    const ESCROW_RELEASED        = 'released';
    const ESCROW_REFUNDED        = 'refunded';

    // Transaction Types
    const SALE   = 'sale';
    const CREDIT = 'credit';

    // Transaction Created Using
    const FULL_INFORMATION = 'full_information';
    const TOKEN            = 'token';

    // Transaction Sources
    const API           = 'api';
    const CONTROL_PANEL = 'control_panel';
    const RECURRING     = 'recurring';

    // Gateway Rejection Reason
    const AVS            = 'avs';
    const AVS_AND_CVV    = 'avs_and_cvv';
    const CVV            = 'cvv';
    const DUPLICATE      = 'duplicate';
    const FRAUD          = 'fraud';
    const THREE_D_SECURE = 'three_d_secure';
    const APPLICATION_INCOMPLETE = 'application_incomplete';

    // Industry Types
    const LODGING_INDUSTRY           = 'lodging';
    const TRAVEL_AND_CRUISE_INDUSTRY = 'travel_cruise';

    /**
     * sets instance properties from an array of values
     *
     * @ignore
     * @access protected
     * @param array $transactionAttribs array of transaction data
     * @return void
     */
    protected function _initialize($transactionAttribs)
    {
        $this->_attributes = $transactionAttribs;

        if (isset($transactionAttribs['applePay'])) {
            $this->_set('applePayCardDetails',
                new Transaction\ApplePayCardDetails(
                    $transactionAttribs['applePay']
                )
            );
        }

        if (isset($transactionAttribs['androidPayCard'])) {
            $this->_set('androidPayCardDetails',
                new Transaction\AndroidPayCardDetails(
                    $transactionAttribs['androidPayCard']
                )
            );
        }

        if (isset($transactionAttribs['amexExpressCheckoutCard'])) {
            $this->_set('amexExpressCheckoutCardDetails',
                new Transaction\AmexExpressCheckoutCardDetails(
                    $transactionAttribs['amexExpressCheckoutCard']
                )
            );
        }

        if (isset($transactionAttribs['venmoAccount'])) {
            $this->_set('venmoAccountDetails',
                new Transaction\VenmoAccountDetails(
                    $transactionAttribs['venmoAccount']
                )
            );
        }

        if (isset($transactionAttribs['creditCard'])) {
            $this->_set('creditCardDetails',
                new Transaction\CreditCardDetails(
                    $transactionAttribs['creditCard']
                )
            );
        }

        if (isset($transactionAttribs['coinbaseAccount'])) {
            $this->_set('coinbaseDetails',
                new Transaction\CoinbaseDetails(
                    $transactionAttribs['coinbaseAccount']
                )
            );
        }

        if (isset($transactionAttribs['europeBankAccount'])) {
            $this->_set('europeBankAccount',
                new Transaction\EuropeBankAccountDetails(
                    $transactionAttribs['europeBankAccount']
                )
            );
        }

        if (isset($transactionAttribs['paypal'])) {
            $this->_set('paypalDetails',
                new Transaction\PayPalDetails(
                    $transactionAttribs['paypal']
                )
            );
        }

        if (isset($transactionAttribs['customer'])) {
            $this->_set('customerDetails',
                new Transaction\CustomerDetails(
                    $transactionAttribs['customer']
                )
            );
        }

        if (isset($transactionAttribs['billing'])) {
            $this->_set('billingDetails',
                new Transaction\AddressDetails(
                    $transactionAttribs['billing']
                )
            );
        }

        if (isset($transactionAttribs['shipping'])) {
            $this->_set('shippingDetails',
                new Transaction\AddressDetails(
                    $transactionAttribs['shipping']
                )
            );
        }

        if (isset($transactionAttribs['subscription'])) {
            $this->_set('subscriptionDetails',
                new Transaction\SubscriptionDetails(
                    $transactionAttribs['subscription']
                )
            );
        }

        if (isset($transactionAttribs['descriptor'])) {
            $this->_set('descriptor',
                new Descriptor(
                    $transactionAttribs['descriptor']
                )
            );
        }

        if (isset($transactionAttribs['disbursementDetails'])) {
            $this->_set('disbursementDetails',
                new DisbursementDetails($transactionAttribs['disbursementDetails'])
            );
        }

        $disputes = [];
        if (isset($transactionAttribs['disputes'])) {
            foreach ($transactionAttribs['disputes'] AS $dispute) {
                $disputes[] = Dispute::factory($dispute);
            }
        }

        $this->_set('disputes', $disputes);

        $statusHistory = [];
        if (isset($transactionAttribs['statusHistory'])) {
            foreach ($transactionAttribs['statusHistory'] AS $history) {
                $statusHistory[] = new Transaction\StatusDetails($history);
            }
        }

        $this->_set('statusHistory', $statusHistory);

        $addOnArray = [];
        if (isset($transactionAttribs['addOns'])) {
            foreach ($transactionAttribs['addOns'] AS $addOn) {
                $addOnArray[] = AddOn::factory($addOn);
            }
        }
        $this->_set('addOns', $addOnArray);

        $discountArray = [];
        if (isset($transactionAttribs['discounts'])) {
            foreach ($transactionAttribs['discounts'] AS $discount) {
                $discountArray[] = Discount::factory($discount);
            }
        }
        $this->_set('discounts', $discountArray);

        if(isset($transactionAttribs['riskData'])) {
            $this->_set('riskData', RiskData::factory($transactionAttribs['riskData']));
        }
        if(isset($transactionAttribs['threeDSecureInfo'])) {
            $this->_set('threeDSecureInfo', ThreeDSecureInfo::factory($transactionAttribs['threeDSecureInfo']));
        }
        if(isset($transactionAttribs['facilitatorDetails'])) {
            $this->_set('facilitatorDetails', FacilitatorDetails::factory($transactionAttribs['facilitatorDetails']));
        }
    }

    /**
     * returns a string representation of the transaction
     * @return string
     */
    public function  __toString()
    {
        // array of attributes to print
        $display = [
            'id', 'type', 'amount', 'status',
            'createdAt', 'creditCardDetails', 'customerDetails'
            ];

        $displayAttributes = [];
        foreach ($display AS $attrib) {
            $displayAttributes[$attrib] = $this->$attrib;
        }
        return __CLASS__ . '[' .
                Util::attributesToString($displayAttributes) .']';
    }

    public function isEqual($otherTx)
    {
        return $this->id === $otherTx->id;
    }

    public function vaultCreditCard()
    {
        $token = $this->creditCardDetails->token;
        if (empty($token)) {
            return null;
        }
        else {
            return CreditCard::find($token);
        }
    }

    /** @return void|Braintree\Customer */
    public function vaultCustomer()
    {
        $customerId = $this->customerDetails->id;
        if (empty($customerId)) {
            return null;
        }
        else {
            return Customer::find($customerId);
        }
    }

    /** @return bool */
    public function isDisbursed() {
        return $this->disbursementDetails->isValid();
    }

    /**
     *  factory method: returns an instance of Transaction
     *  to the requesting method, with populated properties
     *
     * @ignore
     * @return Transaction
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }


    // static methods redirecting to gateway

    public static function cloneTransaction($transactionId, $attribs)
    {
        return Configuration::gateway()->transaction()->cloneTransaction($transactionId, $attribs);
    }

    public static function createFromTransparentRedirect($queryString)
    {
        return Configuration::gateway()->transaction()->createFromTransparentRedirect($queryString);
    }

    public static function createTransactionUrl()
    {
        return Configuration::gateway()->transaction()->createTransactionUrl();
    }

    public static function credit($attribs)
    {
        return Configuration::gateway()->transaction()->credit($attribs);
    }

    public static function creditNoValidate($attribs)
    {
        return Configuration::gateway()->transaction()->creditNoValidate($attribs);
    }

    public static function find($id)
    {
        return Configuration::gateway()->transaction()->find($id);
    }

    public static function sale($attribs)
    {
        return Configuration::gateway()->transaction()->sale($attribs);
    }

    public static function saleNoValidate($attribs)
    {
        return Configuration::gateway()->transaction()->saleNoValidate($attribs);
    }

    public static function search($query)
    {
        return Configuration::gateway()->transaction()->search($query);
    }

    public static function fetch($query, $ids)
    {
        return Configuration::gateway()->transaction()->fetch($query, $ids);
    }

    public static function void($transactionId)
    {
        return Configuration::gateway()->transaction()->void($transactionId);
    }

    public static function voidNoValidate($transactionId)
    {
        return Configuration::gateway()->transaction()->voidNoValidate($transactionId);
    }

    public static function submitForSettlement($transactionId, $amount = null, $attribs = [])
    {
        return Configuration::gateway()->transaction()->submitForSettlement($transactionId, $amount, $attribs);
    }

    public static function submitForSettlementNoValidate($transactionId, $amount = null, $attribs = [])
    {
        return Configuration::gateway()->transaction()->submitForSettlementNoValidate($transactionId, $amount, $attribs);
    }

    public static function submitForPartialSettlement($transactionId, $amount, $attribs = [])
    {
        return Configuration::gateway()->transaction()->submitForPartialSettlement($transactionId, $amount, $attribs);
    }

    public static function holdInEscrow($transactionId)
    {
        return Configuration::gateway()->transaction()->holdInEscrow($transactionId);
    }

    public static function releaseFromEscrow($transactionId)
    {
        return Configuration::gateway()->transaction()->releaseFromEscrow($transactionId);
    }

    public static function cancelRelease($transactionId)
    {
        return Configuration::gateway()->transaction()->cancelRelease($transactionId);
    }

    public static function refund($transactionId, $amount = null)
    {
        return Configuration::gateway()->transaction()->refund($transactionId, $amount);
    }
}
class_alias('Braintree\Transaction', 'Braintree_Transaction');
