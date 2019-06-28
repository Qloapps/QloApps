<?php
namespace Braintree;

class TransactionSearch
{
    public static function amount()                     { return new RangeNode("amount"); }
    public static function authorizationExpiredAt()     { return new RangeNode("authorizationExpiredAt"); }
    public static function authorizedAt()               { return new RangeNode("authorizedAt"); }
    public static function billingCompany()             { return new TextNode('billing_company'); }
    public static function billingCountryName()         { return new TextNode('billing_country_name'); }
    public static function billingExtendedAddress()     { return new TextNode('billing_extended_address'); }
    public static function billingFirstName()           { return new TextNode('billing_first_name'); }
    public static function billingLastName()            { return new TextNode('billing_last_name'); }
    public static function billingLocality()            { return new TextNode('billing_locality'); }
    public static function billingPostalCode()          { return new TextNode('billing_postal_code'); }
    public static function billingRegion()              { return new TextNode('billing_region'); }
    public static function billingStreetAddress()       { return new TextNode('billing_street_address'); }
    public static function createdAt()                  { return new RangeNode("createdAt"); }
    public static function creditCardCardholderName()   { return new TextNode('credit_card_cardholderName'); }
    public static function creditCardExpirationDate()   { return new EqualityNode('credit_card_expiration_date'); }
    public static function creditCardNumber()           { return new PartialMatchNode('credit_card_number'); }
    public static function creditCardUniqueIdentifier() { return new TextNode('credit_card_unique_identifier'); }
    public static function customerCompany()            { return new TextNode('customer_company'); }
    public static function customerEmail()              { return new TextNode('customer_email'); }
    public static function customerFax()                { return new TextNode('customer_fax'); }
    public static function customerFirstName()          { return new TextNode('customer_first_name'); }
    public static function customerId()                 { return new TextNode('customer_id'); }
    public static function customerLastName()           { return new TextNode('customer_last_name'); }
    public static function customerPhone()              { return new TextNode('customer_phone'); }
    public static function customerWebsite()            { return new TextNode('customer_website'); }
    public static function disbursementDate()           { return new RangeNode("disbursementDate"); }
    public static function disputeDate()                { return new RangeNode("disputeDate"); }
    public static function europeBankAccountIban()      { return new TextNode("europeBankAccountIban"); }
    public static function failedAt()                   { return new RangeNode("failedAt"); }
    public static function gatewayRejectedAt()          { return new RangeNode("gatewayRejectedAt"); }
    public static function id()                         { return new TextNode('id'); }
    public static function ids()                        { return new MultipleValueNode('ids'); }
    public static function merchantAccountId()          { return new MultipleValueNode("merchant_account_id"); }
    public static function orderId()                    { return new TextNode('order_id'); }
    public static function paymentInstrumentType()      { return new MultipleValueNode('paymentInstrumentType'); }
    public static function paymentMethodToken()         { return new TextNode('payment_method_token'); }
    public static function paypalAuthorizationId()      { return new TextNode('paypal_authorization_id'); }
    public static function paypalPayerEmail()           { return new TextNode('paypal_payer_email'); }
    public static function paypalPaymentId()            { return new TextNode('paypal_payment_id'); }
    public static function processorAuthorizationCode() { return new TextNode('processor_authorization_code'); }
    public static function processorDeclinedAt()        { return new RangeNode("processorDeclinedAt"); }
    public static function refund()                     { return new KeyValueNode("refund"); }
    public static function settledAt()                  { return new RangeNode("settledAt"); }
    public static function settlementBatchId()          { return new TextNode('settlement_batch_id'); }
    public static function shippingCompany()            { return new TextNode('shipping_company'); }
    public static function shippingCountryName()        { return new TextNode('shipping_country_name'); }
    public static function shippingExtendedAddress()    { return new TextNode('shipping_extended_address'); }
    public static function shippingFirstName()          { return new TextNode('shipping_first_name'); }
    public static function shippingLastName()           { return new TextNode('shipping_last_name'); }
    public static function shippingLocality()           { return new TextNode('shipping_locality'); }
    public static function shippingPostalCode()         { return new TextNode('shipping_postal_code'); }
    public static function shippingRegion()             { return new TextNode('shipping_region'); }
    public static function shippingStreetAddress()      { return new TextNode('shipping_street_address'); }
    public static function submittedForSettlementAt()   { return new RangeNode("submittedForSettlementAt"); }
    public static function user()                       { return new MultipleValueNode('user'); }
    public static function voidedAt()                   { return new RangeNode("voidedAt"); }

    public static function createdUsing()
    {
        return new MultipleValueNode('created_using', [
            Transaction::FULL_INFORMATION,
            Transaction::TOKEN
        ]);
    }

    public static function creditCardCardType()
    {
        return new MultipleValueNode('credit_card_card_type', [
            CreditCard::AMEX,
            CreditCard::CARTE_BLANCHE,
            CreditCard::CHINA_UNION_PAY,
            CreditCard::DINERS_CLUB_INTERNATIONAL,
            CreditCard::DISCOVER,
            CreditCard::JCB,
            CreditCard::LASER,
            CreditCard::MAESTRO,
            CreditCard::MASTER_CARD,
            CreditCard::SOLO,
            CreditCard::SWITCH_TYPE,
            CreditCard::VISA,
            CreditCard::UNKNOWN
        ]);
    }

    public static function creditCardCustomerLocation()
    {
        return new MultipleValueNode('credit_card_customer_location', [
            CreditCard::INTERNATIONAL,
            CreditCard::US
        ]);
    }

    public static function source()
    {
        return new MultipleValueNode('source', []);
    }

    public static function status()
    {
        return new MultipleValueNode('status', [
            Transaction::AUTHORIZATION_EXPIRED,
            Transaction::AUTHORIZING,
            Transaction::AUTHORIZED,
            Transaction::GATEWAY_REJECTED,
            Transaction::FAILED,
            Transaction::PROCESSOR_DECLINED,
            Transaction::SETTLED,
            Transaction::SETTLING,
            Transaction::SUBMITTED_FOR_SETTLEMENT,
            Transaction::VOIDED,
            Transaction::SETTLEMENT_DECLINED,
            Transaction::SETTLEMENT_PENDING
        ]);
    }

    public static function type()
    {
        return new MultipleValueNode('type', [
            Transaction::SALE,
            Transaction::CREDIT
        ]);
    }
}
class_alias('Braintree\TransactionSearch', 'Braintree_TransactionSearch');
