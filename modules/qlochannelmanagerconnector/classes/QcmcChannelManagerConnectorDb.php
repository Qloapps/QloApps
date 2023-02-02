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
