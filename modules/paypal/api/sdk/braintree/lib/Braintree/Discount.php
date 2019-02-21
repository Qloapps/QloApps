<?php
namespace Braintree;

class Discount extends Modification
{
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }


    // static methods redirecting to gateway

    public static function all()
    {
        return Configuration::gateway()->discount()->all();
    }
}
class_alias('Braintree\Discount', 'Braintree_Discount');
