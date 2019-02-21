<?php
namespace Braintree\Result;

use Braintree\RiskData;
use Braintree\Util;

/**
 * Braintree Credit Card Verification Result
 *
 * This object is returned as part of an Error Result; it provides
 * access to the credit card verification data from the gateway
 *
 *
 * @package    Braintree
 * @subpackage Result
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read string $avsErrorResponseCode
 * @property-read string $avsPostalCodeResponseCode
 * @property-read string $avsStreetAddressResponseCode
 * @property-read string $cvvResponseCode
 * @property-read string $status
 *
 */
class CreditCardVerification
{
    // Status
    const FAILED                   = 'failed';
    const GATEWAY_REJECTED         = 'gateway_rejected';
    const PROCESSOR_DECLINED       = 'processor_declined';
    const VERIFIED                 = 'verified';

    private $_attributes;
    private $_avsErrorResponseCode;
    private $_avsPostalCodeResponseCode;
    private $_avsStreetAddressResponseCode;
    private $_cvvResponseCode;
    private $_gatewayRejectionReason;
    private $_status;

    /**
     * @ignore
     */
    public function  __construct($attributes)
    {
        $this->_initializeFromArray($attributes);
    }

    /**
     * initializes instance properties from the keys/values of an array
     * @ignore
     * @access protected
     * @param <type> $aAttribs array of properties to set - single level
     * @return void
     */
    private function _initializeFromArray($attributes)
    {
        if(isset($attributes['riskData']))
        {
            $attributes['riskData'] = RiskData::factory($attributes['riskData']);
        }

        $this->_attributes = $attributes;
        foreach($attributes AS $name => $value) {
            $varName = "_$name";
            $this->$varName = $value;
        }
    }

    /**
     * @ignore
     */
    public function  __get($name)
    {
        $varName = "_$name";
        return isset($this->$varName) ? $this->$varName : null;
    }

    /**
     * returns a string representation of the customer
     * @return string
     */
    public function  __toString()
    {
        return __CLASS__ . '[' .
                Util::attributesToString($this->_attributes) . ']';
    }

    public static function allStatuses()
    {
        return [
            CreditCardVerification::FAILED,
            CreditCardVerification::GATEWAY_REJECTED,
            CreditCardVerification::PROCESSOR_DECLINED,
            CreditCardVerification::VERIFIED
        ];
    }
}
class_alias('Braintree\Result\CreditCardVerification', 'Braintree_Result_CreditCardVerification');
