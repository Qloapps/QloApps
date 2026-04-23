<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
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
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

class QloDuitkuPaymentValidateModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;

        if (Tools::getValue('ajax')) {
            if (Tools::getValue('action') == 'getDuitkuTransactionToken') {
                $objDuitkuPayment = Module::getInstanceByName('qloduitkupayment');
                $response = $objDuitkuPayment->getDuitkuTransactionTokenResponse();

                if (isset($response['status']) && $response['status'] === true) {
                    $response['status'] = true;
                } else if (isset($response['response']['statusMessage'])) {
                    $response['message'] = $response['response']['statusMessage'];
                } elseif (isset($response['response']['title'])) {
                    $response['message'] = $response['response']['title'];
                } else {
                    $response['message'] = $objDuitkuPayment->l('Unknown error');
                }

                die(json_encode($response));
            }
        }
    }
}
