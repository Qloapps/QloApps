<?php
namespace Braintree;

final class PaymentInstrumentType
{
    const PAYPAL_ACCOUNT      = 'paypal_account';
    const COINBASE_ACCOUNT    = 'coinbase_account';
    const EUROPE_BANK_ACCOUNT = 'europe_bank_account';
    const CREDIT_CARD         = 'credit_card';
    const APPLE_PAY_CARD      = 'apple_pay_card';
    const ANDROID_PAY_CARD    = 'android_pay_card';
}
class_alias('Braintree\PaymentInstrumentType', 'Braintree_PaymentInstrumentType');
