<?php
namespace Braintree;

class ClientToken
{
    const DEFAULT_VERSION = 2;


    // static methods redirecting to gateway

    /**
     *
     * @param array $params
     * @return array
     */
    public static function generate($params=[])
    {
        return Configuration::gateway()->clientToken()->generate($params);
    }

    /**
     *
     * @param type $params
     * @throws InvalidArgumentException
     */
    public static function conditionallyVerifyKeys($params)
    {
        return Configuration::gateway()->clientToken()->conditionallyVerifyKeys($params);
    }

    /**
     *
     * @return string client token retrieved from server
     */
    public static function generateWithCustomerIdSignature()
    {
        return Configuration::gateway()->clientToken()->generateWithCustomerIdSignature();
    }

    /**
     *
     * @return string client token retrieved from server
     */
    public static function generateWithoutCustomerIdSignature()
    {
        return Configuration::gateway()->clientToken()->generateWithoutCustomerIdSignature();
    }
}
class_alias('Braintree\ClientToken', 'Braintree_ClientToken');
