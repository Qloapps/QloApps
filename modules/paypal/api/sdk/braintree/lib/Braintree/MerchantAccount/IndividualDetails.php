<?php
namespace Braintree\MerchantAccount;

use Braintree\Base;

final class IndividualDetails extends Base
{
    protected function _initialize($individualAttribs)
    {
        $this->_attributes = $individualAttribs;
        if (isset($individualAttribs['address'])) {
            $this->_set('addressDetails', new AddressDetails($individualAttribs['address']));
        }
    }

    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }
}
class_alias('Braintree\MerchantAccount\IndividualDetails', 'Braintree_MerchantAccount_IndividualDetails');
