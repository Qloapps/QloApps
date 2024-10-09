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

function add_new_order_refund_states_160()
{
     // add new order states
    $orderStates = array(
        'Awaiting_payment' => array(
            'invoice' => '0',
            'send_email' => '1',
            'color' => '#4169E1',
            'unremovable' => '1',
            'hidden' => '0',
            'logable' => '0',
            'delivery' => '0',
            'shipped' => '0',
            'paid' => '0',
            'pdf_delivery' => '0',
            'pdf_invoice' => '0',
            'lang' => array(
                'name' => 'Awaiting payment',
                'template' => 'awaiting_payment',
            ),
            'key' => 'PS_OS_AWAITING_PAYMENT'
        ),
        'Payment_accepted' => array(
            'id' => Configuration::get('PS_OS_PAYMENT'),
            'lang' => array(
                'template' => 'payment_accepted',
            ),
            'key' => 'PS_OS_PAYMENT_ACCEPTED'
        ),
        'Processing_in_progress' => array(
            'id' => Configuration::get('PS_OS_PREPARATION'),
            'lang' => array(
                'template' => 'processing',
            ),
            'key' => 'PS_OS_PROCESSING'
        ),
        'Payment_remotely_accepted' => array(
            'id' => Configuration::get('PS_OS_WS_PAYMENT'),
            'paid' => '0',
            'send_email' => '0',
            'lang' => array(
                'template' => 'payment_accepted',
            ),
            'key' => 'PS_OS_REMOTE_PAYMENT_ACCEPTED'
        ),
        'Overbooking_paid' => array(
            'id' => Configuration::get('PS_OS_OUTOFSTOCK_PAID'),
            'lang' => array(
                'name' => 'Overbooking (paid)',
                'template' => '',
            ),
            'key' => 'PS_OS_OVERBOOKING_PAID'
        ),
        'Overbooking_unpaid' => array(
            'id' => Configuration::get('PS_OS_OUTOFSTOCK_UNPAID'),
            'lang' => array(
                'name' => 'Overbooking (not paid)',
                'template' => '',
            ),
            'key' => 'PS_OS_OVERBOOKING_UNPAID'
        ),
        'Partial_payment_accepted' => array(
            'id' => Configuration::get('PS_OS_PARTIAL_PAYMENT'),
            'color' => '#F0B656',
            'logable' => 1,
            'send_email' => 1,
            'lang' => array(
                'name' => 'Partial payment accepted',
                'template' => 'payment_accepted',
            ),
            'key' => 'PS_OS_PARTIAL_PAYMENT_ACCEPTED'
        ),
    );
    foreach ($orderStates as $stateName => $state) {
        if (isset($state['id']) && $state['id']) {
            $objState = new OrderState($state['id']);
        } else {
            $objState = new OrderState();
        }
        foreach ($state as $key => $stateValue) {
            if ($key == 'lang') {
                foreach ($stateValue as $key => $value) {
                    foreach (Language::getLanguages(true) as $lang) {
                        $objState->{$key}[$lang['id_lang']] = $value;
                    }
                }
            } else {
                $objState->{$key} = $stateValue;
            }
        }
        if ($objState->save()) {
            // update order status image
            if (file_exists(dirname(__FILE__).'/../../data/img/os/'.$stateName.'.gif')) {
                copy(dirname(__FILE__).'/../../data/img/os/'.$stateName.'.gif', _PS_ROOT_DIR_.'/img/os/'.$objState->id.'.gif');
            }

            // update order status id in configuration table
            if (isset($state['key']) && $state['key']) {
                Configuration::updateValue($state['key'], $objState->id);
            }
        }
    }

    $refundStatesDelete = array(
        'recieved' => array(
            'id' => 2,
            'update_id' => 1
        )
    );

    foreach ($refundStatesDelete as $state) {
        Db::getInstance()->delete('order_return_state', '`id_order_return_state` = '.$state['id']);
        Db::getInstance()->delete('order_return_state_lang', '`id_order_return_state` = '.$state['id']);
        Db::getInstance()->update('order_return', array('state' => $state['update_id']), '`state` = '.$state['id']);
    }

    $refundStatusUpdate = array(
        'waiting' => array(
            'id' => 1,
            'fields_lang' => array(
                'name' => 'Pending',
                'customer_template' => 'order_refund_pending_customer',
                'admin_template' => 'order_refund_pending_admin'
            ),
        )
    );
    foreach ($refundStatusUpdate as $state) {
        Db::getInstance()->update('order_return_state_lang', $state['fields_lang'], '`id_order_return_state` = '.$state['id']);
    }

    return true;
}