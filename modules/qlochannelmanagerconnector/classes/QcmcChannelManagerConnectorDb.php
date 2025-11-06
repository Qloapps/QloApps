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

class QcmcChannelManagerConnectorDb
{
    public function createTables()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'qcmc_channel_manager_booking` (
            `id_channel_manager_booking` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_order` INT(11) UNSIGNED NOT NULL,
            `date_add` DATETIME NOT NULL,
            PRIMARY KEY (`id_channel_manager_booking`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        if (!Db::getInstance()->execute(trim($sql))) {
            return false;
        }

        return true;
    }

    public function dropTables()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'qcmc_channel_manager_booking');
    }
}
