<?php
namespace Braintree;

class CreditCardVerification extends Result\CreditCardVerification
{
    public static function factory($attributes)
    {
        $instance = new self($attributes);
        return $instance;
    }

    // static methods redirecting to gateway
    //
    public static function create($attributes)
    {
        Util::verifyKeys(self::createSignature(), $attributes);
        return Configuration::gateway()->creditCardVerification()->create($attributes);
    }

    public static function fetch($query, $ids)
    {
        return Configuration::gateway()->creditCardVerification()->fetch($query, $ids);
    }

    public static function search($query)
    {
        return Configuration::gateway()->creditCardVerification()->search($query);
    }

    public static function createSignature()
    {
        return [
                ['options' => ['amount', 'merchantAccountId']],
                ['creditCard' =>
                    [
                        'cardholderName', 'cvv', 'number',
                        'expirationDate', 'expirationMonth', 'expirationYear',
                        ['billingAddress' => CreditCardGateway::billingAddressSignature()]
                    ]
                ]];
    }
}
class_alias('Braintree\CreditCardVerification', 'Braintree_CreditCardVerification');
