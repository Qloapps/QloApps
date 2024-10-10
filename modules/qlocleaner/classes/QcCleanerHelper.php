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

class QcCleanerHelper extends ObjectModel
{
    public static function deleteFolderImages($folderPath)
    {
        if ($folderPath) {
            foreach (scandir($folderPath) as $dir) {
                if (preg_match('/^([0-9]*[a-z]*[A-Z]*)+(\-(.*))?\.(jpg|png|jpeg|gif)+$/', $dir)) {
                    unlink($folderPath.$dir);
                }
            }
        }
    }

    public static function deleteModulesConfigurations()
    {
        // modules configuration keys
        $moduleConfKeys = array(
            "'HOTEL_INTERIOR_HEADING'",
            "'HOTEL_INTERIOR_DESCRIPTION'",
            "'HOTEL_AMENITIES_HEADING'",
            "'HOTEL_AMENITIES_DESCRIPTION'",
            "'HOTEL_ROOM_DISPLAY_HEADING'",
            "'HOTEL_ROOM_DISPLAY_DESCRIPTION'",
            "'HOTEL_TESIMONIAL_BLOCK_HEADING'",
            "'HOTEL_TESIMONIAL_BLOCK_CONTENT'",
        );

        $moduleConfKeys = implode(",", $moduleConfKeys);
        $dbInst = Db::getInstance();
        if ($result = $dbInst->ExecuteS(
            'SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` IN ('.$moduleConfKeys.')'
        )) {
            $moduleConfIds = array();
            foreach ($result as $confRow) {
                $moduleConfIds[] = $confRow['id_configuration'];
            }
            $moduleConfIds = implode(",", $moduleConfIds);
            // delete from canfiguration and configuration_lang bothe tables
            $dltConfigLang = $dbInst->delete(
                'configuration_lang',
                'id_configuration IN ('.$moduleConfIds.')'
            );
            $dltConfig = $dbInst->delete(
                'configuration',
                'id_configuration IN ('.$moduleConfIds.')'
            );

            return ($dltConfigLang && $dltConfig);
        }
        return true;
    }
}
