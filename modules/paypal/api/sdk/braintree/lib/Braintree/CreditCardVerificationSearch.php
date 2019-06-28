<?php
namespace Braintree;

class CreditCardVerificationSearch
{
    public static function id() {
	    return new TextNode('id');
    }

    public static function creditCardCardholderName() {
	    return new TextNode('credit_card_cardholder_name');
    }

    public static function billingAddressDetailsPostalCode() {
        return new TextNode('billing_address_details_postal_code');
    }

    public static function customerEmail() {
        return new TextNode('customer_email');
    }

    public static function customerId() {
        return new TextNode('customer_id');
    }

    public static function paymentMethodToken(){
        return new TextNode('payment_method_token');
    }

    public static function creditCardExpirationDate() {
	    return new EqualityNode('credit_card_expiration_date');
    }

    public static function creditCardNumber() {
	    return new PartialMatchNode('credit_card_number');
    }

    public static function ids() {
        return new MultipleValueNode('ids');
    }

    public static function createdAt() {
	    return new RangeNode("created_at");
    }

    public static function creditCardCardType()
    {
        return new MultipleValueNode("credit_card_card_type", CreditCard::allCardTypes());
    }

    public static function status()
    {
        return new MultipleValueNode("status", Result\CreditCardVerification::allStatuses());
    }
}
class_alias('Braintree\CreditCardVerificationSearch', 'Braintree_CreditCardVerificationSearch');
