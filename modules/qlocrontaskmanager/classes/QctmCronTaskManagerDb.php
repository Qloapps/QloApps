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

if (!defined('_PS_VERSION_')) {
    exit;
}

class QctmCronTaskManagerDb
{
    public function createTables()
    {

        $sql = array(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'qctm_cron_task` (
                `id_cron_task` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_module` INT(11) UNSIGNED NOT NULL,
                `task_name` VARCHAR(128) NOT NULL,
                `description` VARCHAR(255) NOT NULL,
                `cron_expression` VARCHAR(64) NOT NULL,
                `callback` VARCHAR(128) NOT NULL,
                `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
                `is_system` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_cron_task`),
                UNIQUE KEY `module_task` (`id_module`, `task_name`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;',
        
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'qctm_cron_task_log` (
                `id_cron_task_log` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_cron_task` INT(11) UNSIGNED NOT NULL,
                `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
                `error_message` TEXT DEFAULT NULL,
                `execution_time` FLOAT NOT NULL DEFAULT 0,
                `date_add` DATETIME NOT NULL,
                PRIMARY KEY (`id_cron_task_log`),
                KEY `idx_cron_task` (`id_cron_task`),
                KEY `idx_date_add` (`date_add`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;'
        );

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    public function dropTables()
    {
        $sql = array(
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'qctm_cron_task_log`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'qctm_cron_task`'
        );

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    public function installDefaultData()
    {
        Configuration::updateValue('QCTM_CRON_TASK_MANAGER_TOKEN', Tools::passwdGen(32));
        Configuration::updateValue('QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS', 30);

        return true;
    }

    public function deleteConfigurations()
    {
        Configuration::deleteByName('QCTM_CRON_TASK_MANAGER_TOKEN');
        Configuration::deleteByName('QCTM_CRON_TASK_MANAGER_LOG_RETENTION_DAYS');

        return true;
    }
}
