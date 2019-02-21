<?php
namespace Braintree\Test;

/**
 * Nonces used for testing purposes
 *
 * @package    Braintree
 * @subpackage Test
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */

/**
 * Nonces used for testing purposes
 *
 * The constants in this class can be used to perform nonce operations
 * with the desired status in the sandbox environment.
 *
 * @package    Braintree
 * @subpackage Test
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 */
class Nonces
{
   public static $transactable = "fake-valid-nonce";
   public static $consumed = "fake-consumed-nonce";
   public static $paypalOneTimePayment = "fake-paypal-one-time-nonce";
   public static $paypalFuturePayment = "fake-paypal-future-nonce";
   public static $paypalBillingAgreement = "fake-paypal-billing-agreement-nonce";
   public static $applePayVisa = "fake-apple-pay-visa-nonce";
   public static $applePayMasterCard = "fake-apple-pay-visa-nonce";
   public static $applePayAmEx = "fake-apple-pay-amex-nonce";
   public static $androidPay = "fake-android-pay-nonce";
   public static $androidPayDiscover = "fake-android-pay-discover-nonce";
   public static $androidPayVisa = "fake-android-pay-visa-nonce";
   public static $androidPayMasterCard = "fake-android-pay-mastercard-nonce";
   public static $androidPayAmEx = "fake-android-pay-amex-nonce";
   public static $amexExpressCheckout = "fake-amex-express-checkout-nonce";
   public static $abstractTransactable = "fake-abstract-transactable-nonce";
   public static $europe = "fake-europe-bank-account-nonce";
   public static $coinbase = "fake-coinbase-nonce";
   public static $transactableVisa = "fake-valid-visa-nonce";
   public static $transactableAmEx = "fake-valid-amex-nonce";
   public static $transactableMasterCard = "fake-valid-mastercard-nonce";
   public static $transactableDiscover = "fake-valid-discover-nonce";
   public static $transactableJCB = "fake-valid-jcb-nonce";
   public static $transactableMaestro = "fake-valid-maestro-nonce";
   public static $transactableDinersClub = "fake-valid-dinersclub-nonce";
   public static $transactablePrepaid = "fake-valid-prepaid-nonce";
   public static $transactableCommercial = "fake-valid-commercial-nonce";
   public static $transactableDurbinRegulated = "fake-valid-durbin-regulated-nonce";
   public static $transactableHealthcare = "fake-valid-healthcare-nonce";
   public static $transactableDebit = "fake-valid-debit-nonce";
   public static $transactablePayroll = "fake-valid-payroll-nonce";
   public static $transactableNoIndicators = "fake-valid-no-indicators-nonce";
   public static $transactableUnknownIndicators = "fake-valid-unknown-indicators-nonce";
   public static $transactableCountryOfIssuanceUSA = "fake-valid-country-of-issuance-usa-nonce";
   public static $transactableCountryOfIssuanceCAD = "fake-valid-country-of-issuance-cad-nonce";
   public static $transactableIssuingBankNetworkOnly = "fake-valid-issuing-bank-network-only-nonce";
   public static $processorDeclinedVisa = "fake-processor-declined-visa-nonce";
   public static $processorDeclinedMasterCard = "fake-processor-declined-mastercard-nonce";
   public static $processorDeclinedAmEx = "fake-processor-declined-amex-nonce";
   public static $processorDeclinedDiscover = "fake-processor-declined-discover-nonce";
   public static $processorFailureJCB = "fake-processor-failure-jcb-nonce";
   public static $luhnInvalid = "fake-luhn-invalid-nonce";
   public static $paypalFuturePaymentRefreshToken = "fake-paypal-future-refresh-token-nonce";
   public static $sepa = "fake-sepa-bank-account-nonce";
   public static $gatewayRejectedFraud = "fake-gateway-rejected-fraud-nonce";
   public static $venmoAccount = "fake-venmo-account-nonce";
}
class_alias('Braintree\Test\Nonces', 'Braintree_Test_Nonces');
