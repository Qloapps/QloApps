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

function add_new_tabs_180()
{
    $tabs = array(
        array(
            'className' => 'AdminRoomTypeSellingObjects',
            'name' => 'Room Selling Objects',
            'id_parent' => (int) Tab::getIdFromClassName('AdminCatalog'),
        ),
        array(
            'className' => 'AdminPaymentReceipts',
            'name' => 'Payment Receipts',
            'id_parent' => (int) Tab::getIdFromClassName('AdminParentOrders'),
        ),
        array(
            'className' => 'AdminSources',
            'name' => 'Business Sources',
            'id_parent' => (int) Tab::getIdFromClassName('AdminParentOrders'),
        ),
        array(
            'className' => 'AdminGuestRegistration',
            'name' => 'Guest Registration Card',
            'id_parent' => (int) Tab::getIdFromClassName('AdminParentOrders'),
        ),
    );

    foreach ($tabs as $tab) {
        if (Tab::getIdFromClassName($tab['className'])) {
            continue;
        }

        $objTab = new Tab();
        $objTab->active = 1;
        $objTab->class_name = $tab['className'];
        $objTab->id_parent = $tab['id_parent'];
        $objTab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $objTab->name[$lang['id_lang']] = $tab['name'];
        }

        $objTab->add();
    }

    foreach (array('AdminHotelBedTypes', 'AdminHotelFeatures') as $removedClassName) {
        $id_tab = Tab::getIdFromClassName($removedClassName);
        if ($id_tab) {
            $objTab = new Tab($id_tab);
            $objTab->delete();
        }
    }

    return true;
}
