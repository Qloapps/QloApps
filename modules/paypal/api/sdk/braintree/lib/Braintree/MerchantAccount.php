<?php
namespace Braintree;

final class MerchantAccount extends Base
{
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';
    const STATUS_SUSPENDED = 'suspended';

    const FUNDING_DESTINATION_BANK = 'bank';
    const FUNDING_DESTINATION_EMAIL = 'email';
    const FUNDING_DESTINATION_MOBILE_PHONE = 'mobile_phone';

    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    protected function _initialize($merchantAccountAttribs)
    {
        $this->_attributes = $merchantAccountAttribs;

        if (isset($merchantAccountAttribs['individual'])) {
            $individual = $merchantAccountAttribs['individual'];
            $this->_set('individualDetails', MerchantAccount\IndividualDetails::Factory($individual));
        }

        if (isset($merchantAccountAttribs['business'])) {
            $business = $merchantAccountAttribs['business'];
            $this->_set('businessDetails', MerchantAccount\BusinessDetails::Factory($business));
        }

        if (isset($merchantAccountAttribs['funding'])) {
            $funding = $merchantAccountAttribs['funding'];
            $this->_set('fundingDetails', new MerchantAccount\FundingDetails($funding));
        }

        if (isset($merchantAccountAttribs['masterMerchantAccount'])) {
            $masterMerchantAccount = $merchantAccountAttribs['masterMerchantAccount'];
            $this->_set('masterMerchantAccount', self::Factory($masterMerchantAccount));
        }
    }


    // static methods redirecting to gateway

    public static function create($attribs)
    {
        return Configuration::gateway()->merchantAccount()->create($attribs);
    }

    public static function find($merchant_account_id)
    {
        return Configuration::gateway()->merchantAccount()->find($merchant_account_id);
    }

    public static function update($merchant_account_id, $attributes)
    {
        return Configuration::gateway()->merchantAccount()->update($merchant_account_id, $attributes);
    }
}
class_alias('Braintree\MerchantAccount', 'Braintree_MerchantAccount');
