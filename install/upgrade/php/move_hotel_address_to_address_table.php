<?php
/**
* 2010-2023 Webkul.
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
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
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