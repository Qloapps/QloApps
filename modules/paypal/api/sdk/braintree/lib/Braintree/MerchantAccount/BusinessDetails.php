<?php
namespace Braintree\MerchantAccount;

use Braintree\Base;

final class BusinessDetails extends Base
{
    protected function _initialize($businessAttribs)
    {
        $this->_attributes = $businessAttribs;
        if (isset($businessAttribs['address'])) {
            $this->_set('addressDetails', new AddressDetails($businessAttribs['address']));
        }
    }

    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }
}
class_alias('Braintree\MerchantAccount\BusinessDetails', 'Braintree_MerchantAccount_BusinessDetails');
