<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
*/

class QloPaypalCommerceWebhookModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_UPPER);

        $json = Tools::file_get_contents('php://input');

        $payload = array();

        if ($headers) {
            $payload['transmission_id'] = $headers['PAYPAL-TRANSMISSION-ID'];
            $payload['cert_url'] = $headers['PAYPAL-CERT-URL'];
            $payload['transmission_time'] = $headers['PAYPAL-TRANSMISSION-TIME'];
            $payload['auth_algo'] = $headers['PAYPAL-AUTH-ALGO'];
            $payload['transmission_sig'] = $headers['PAYPAL-TRANSMISSION-SIG'];
            $payload['webhook_id'] = Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_WEBHOOK_ID');
        }

        if ($json) {
            $payload['webhook_event'] = Tools::jsonDecode($json, true);
        }

        if ($payload) {
            WkPaypalCommerceHelper::logMsg('webhook', 'Webhook initiated...', true);
            WkPaypalCommerceHelper::logMsg('webhook', 'Environment: '. Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE'));
            WkPaypalCommerceHelper::logMsg('webhook', 'Webhook payload data: ');
            WkPaypalCommerceHelper::logMsg('webhook', Tools::jsonEncode($payload));
            WkPaypalCommerceHelper::logMsg('webhook', 'Validating webhook signature...');

            $validateSig = WkPaypalCommerceHelper::validateWebhookSig($payload);

            WkPaypalCommerceHelper::logMsg('webhook', 'Webhook respose data: ');
            WkPaypalCommerceHelper::logMsg('webhook', Tools::jsonEncode($validateSig));

            if (isset($validateSig['verification_status'])
                && $validateSig['verification_status'] == 'SUCCESS'
            ) {
                $eventData = Tools::jsonDecode($json, true);
                $objWebhook = new WkPaypalCommerceWebhook();
                switch ($eventData['event_type']) {
                    case 'CHECKOUT.ORDER.APPROVED':
                        $objWebhook->orderCompleted($eventData);
                        break;
                    case 'CHECKOUT.ORDER.COMPLETED':
                        $objWebhook->orderCompleted($eventData);
                        break;

                    case 'PAYMENT.CAPTURE.COMPLETED':
                        $objWebhook->captureCompleted($eventData);
                        break;

                    case 'PAYMENT.CAPTURE.DENIED':
                        $objWebhook->captureDenied($eventData);
                        break;

                    case 'PAYMENT.CAPTURE.PENDING':
                        $objWebhook->capturePending($eventData);
                        break;

                    case 'PAYMENT.CAPTURE.REFUNDED':
                        $objWebhook->captureRefunded($eventData);
                        break;

                    case 'PAYMENT.CAPTURE.REVERSED':
                        $objWebhook->captureRefunded($eventData);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }

        header("HTTP/1.1 200 OK");
        die;
    }
}
