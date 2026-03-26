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

class QcmcCMConnectorDb
{
    /**
     * Prepare the SQL statement and create the Universal payment table.
     *
     * @return bool
     */
    public function createTables()
    {
        $sqls = array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."ari_updates` (
                `id_ari_updates` INT AUTO_INCREMENT,
                `id_hotel` INT NOT NULL,
                `id_room_type` INT NOT NULL,
                `date` DATETIME NOT NULL,
                `ari_data` LONGTEXT NULL,
                `status` TINYINT(1) NOT NULL DEFAULT 0,
                `date_add` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_upd` DATETIME NULL,
                PRIMARY KEY (`id_ari_updates`)
            )ENGINE=" . _MYSQL_ENGINE_ ." DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qcmc_channel_manager_booking` (
                `id_channel_manager_booking` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_order` INT(11) UNSIGNED NOT NULL,
                `date_add` DATETIME NOT NULL,
                PRIMARY KEY (`id_channel_manager_booking`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8"
        );

        foreach ($sqls as $sql) {
            if (!Db::getInstance()->execute(trim($sql))) {
                return false;
            }
        }

        if (!$this->idChannelManagerBookingColumnExists()) {
            if (!Db::getInstance()->execute(trim("ALTER TABLE `"._DB_PREFIX_."orders` ADD COLUMN `id_channel_manager_booking` VARCHAR(255) NULL"))) {
                return false;
            }
        }

        return true;
    }

    public function dropTables()
    {
        $sqls = array(
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'ari_updates`',
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'qcmc_channel_manager_booking`'
        );

        foreach ($sqls as $sql) {
            if (!Db::getInstance()->execute(trim($sql))) {
                return false;
            }
        }

        if ($this->idChannelManagerBookingColumnExists()) {
            if (!Db::getInstance()->execute(trim("ALTER TABLE `"._DB_PREFIX_."orders` DROP COLUMN `id_channel_manager_booking`"))) {
                return false;
            }
        }

        return true;
    }

    protected function idChannelManagerBookingColumnExists()
    {
        $sql = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS`
            WHERE `TABLE_SCHEMA` = DATABASE()
            AND `TABLE_NAME` = '" . pSQL(_DB_PREFIX_ . "orders") . "'
            AND `COLUMN_NAME` = 'id_channel_manager_booking'";

        return (bool) Db::getInstance()->getValue($sql);
    }
}
