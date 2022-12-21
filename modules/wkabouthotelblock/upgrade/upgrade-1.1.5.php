<?php
/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1_5($module)
{
    return updateTableData();
}

function updateTableData()
{
    $interiorImages = $htlBookings = Db::getInstance()->executes(
        'SELECT `id_interior_image` FROM `'._DB_PREFIX_.'htl_interior_image`'
    );

    foreach ($interiorImages as $img) {
        $objHtlInteriorImg = new WkHotelInteriorImage($img['id_interior_image']);
        if(explode('.', $objHtlInteriorImg->name)[0]) {
            $objHtlInteriorImg->name = explode('.', $objHtlInteriorImg->name)[0];
        }
        $objHtlInteriorImg->save();
    }
    return true;
}