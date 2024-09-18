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

class WkHotelRoomDb
{
    public function createTables()
    {
        if ($sqls = $this->getModuleSqls()) {
            foreach ($sqls as $query) {
                if ($query) {
                    if (!DB::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function getModuleSqls()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_block_data` (
                `id_room_block` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `active` tinyint(4) NOT NULL,
                `position` int(11) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_room_block`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;"
        );
    }

    public function dropTables()
    {
        return DB::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_room_block_data`;
        ');
    }

    public static function truncateTables()
    {
        return DB::getInstance()->execute(
            'TRUNCATE TABLE `'._DB_PREFIX_.'htl_room_block_data`;
        ');
    }
}
