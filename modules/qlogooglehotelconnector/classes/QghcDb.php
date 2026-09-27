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

class QghcDb
{
    public function createTables()
    {
        $sqls = array(

            // Google Hotel enrollment state lives directly on the core hotel/room-type
            "ALTER TABLE `" . _DB_PREFIX_ . "htl_branch_info`
                ADD `is_google_hotel_enabled` tinyint(1) NOT NULL DEFAULT 0,
                ADD `id_google_hotel` varchar(255) DEFAULT NULL,
                ADD `google_status` tinyint(1) NOT NULL DEFAULT 0,
                ADD `failure_reason` text",

            "ALTER TABLE `" . _DB_PREFIX_ . "htl_room_type`
                ADD `is_google_hotel_enabled` tinyint(1) NOT NULL DEFAULT 0,
                ADD `id_google_room_type` varchar(255) DEFAULT NULL",

            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "qghc_api_log` (
                `id_qghc_api_log` int(11) NOT NULL AUTO_INCREMENT,
                `id_hotel` int(11) NOT NULL,
                `api_type` varchar(50) NOT NULL,
                `request` longtext,
                `response` longtext,
                `status` tinyint(1) NOT NULL DEFAULT 0,
                `message` text,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_qghc_api_log`),
                KEY `id_hotel` (`id_hotel`),
                KEY `api_type` (`api_type`),
                KEY `date_add` (`date_add`)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4",

            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "qghc_ari_updates` (
                `id_qghc_ari_updates` int(11) NOT NULL AUTO_INCREMENT,
                `id_hotel` int(11) NOT NULL,
                `id_room_type` int(11) NOT NULL,
                `ari_date` date NOT NULL,
                `ari_data` longtext NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_qghc_ari_updates`),
                UNIQUE KEY `hotel_room_date` (`id_hotel`, `id_room_type`, `ari_date`),
                KEY `id_hotel` (`id_hotel`)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8mb4",

        );

        foreach ($sqls as $sql) {
            if (!Db::getInstance()->execute(trim($sql))) {
                return false;
            }
        }

        return true;
    }

    public function dropTables()
    {
        $sqls = array(
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "qghc_ari_updates`",
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "qghc_api_log`",
            "ALTER TABLE `" . _DB_PREFIX_ . "htl_room_type`
                DROP COLUMN `is_google_hotel_enabled`,
                DROP COLUMN `id_google_room_type`",
            "ALTER TABLE `" . _DB_PREFIX_ . "htl_branch_info`
                DROP COLUMN `is_google_hotel_enabled`,
                DROP COLUMN `id_google_hotel`,
                DROP COLUMN `google_status`,
                DROP COLUMN `failure_reason`",
        );

        foreach ($sqls as $sql) {
            if (!Db::getInstance()->execute(trim($sql))) {
                return false;
            }
        }

        return true;
    }
}
