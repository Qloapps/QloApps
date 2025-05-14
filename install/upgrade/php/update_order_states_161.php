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

function update_order_states_161()
{
    $data = array(
        'invoice' => 0,
        'send_email' => 0,
        'color' => '#FF69B4',
        'unremovable' => 1,
        'hidden' => 0,
        'logable' => 0,
        'delivery' => 0,
        'shipped' => 0,
        'paid' => 0,
        'pdf_delivery' => 0,
        'pdf_invoice' => 0,
        'deleted' => 0
    );
    Db::getInstance()->insert('order_state', $data);
    if ($last_id = Db::getInstance()->Insert_ID()) {
        if ($languages = Db::getInstance()->executeS('SELECT id_lang, iso_code FROM `'._DB_PREFIX_.'lang`')) {
            $row = array();
            foreach ($languages as $lang) {
                $row[] = array(
                    'id_order_state' => (int)$last_id,
                    'id_lang' => (int)$lang['id_lang'],
                    'name' => 'Overbooking (Partial payment)',
                    'template' => '',
                );
            }
            Db::getInstance()->insert(
                'order_state_lang',
                $row
            );
        }
        if (file_exists(dirname(__FILE__).'/../../data/img/os/Overbooking_partial_paid.gif')) {
            copy(dirname(__FILE__).'/../../data/img/os/Overbooking_partial_paid.gif', _PS_ROOT_DIR_.'/img/os/'.$last_id.'.gif');
        }
        Db::getInstance()->insert('configuration',[
            'name' => 'PS_OS_OVERBOOKING_PARTIAL_PAID',
            'value' => $last_id,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s')
        ]);
    }
}