<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class QloPaypalCommerceCallbackModuleFrontController extends ModuleFrontController
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
            $payload['webhook_id'] = Configuration::get('WK_PAYPAL_COMMERCE_LIVE_WEBHOOK_ID');
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
