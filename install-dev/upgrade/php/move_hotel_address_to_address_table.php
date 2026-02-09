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

function move_hotel_address_to_address_table()
{
    $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
    $sql = 'SELECT hbi.`id` as `id_hotel`, hbi.`country_id` as `id_country`, hbi.`state_id` as `id_state`,
        hbi.`city`, hbi.`zipcode` as `postcode`, hbil.`hotel_name` as `alias`, hbil.`hotel_name` as `lastname`,
        hbil.`hotel_name` as `firstname`, hbi.`address` as `address1`, hbi.`phone`, NOW() as `date_add`,
        NOW() as `date_upd`
        FROM `'._DB_PREFIX_.'htl_branch_info` hbi
        INNER JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
        ON (hbil.`id` = hbi.`id` AND hbil.`id_lang` = '.(int)$defaultLangId.')';
    if ($addresses = Db::getInstance()->executeS($sql)) {
        foreach($addresses as &$address) {
            $address['firstname'] = preg_replace('/[0-9]+/', '', $address['firstname']);
            $address['lastname'] = preg_replace('/[0-9]+/', '', $address['lastname']);
        }
        return Db::getInstance()->insert('address', $addresses);
    }
}