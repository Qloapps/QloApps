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
class QdpDuitkuPaymentDb
{
    public function createTable()
    {
        $sqls = array(
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "qdp_duitku_payment_transaction` (
                `id_duitku_payment_transaction` int(10) unsigned NOT NULL auto_increment,
                `id_transaction` varchar(255) NOT NULL,
                `id_cart` int(11) unsigned NOT NULL,
                `id_order` varchar(255) NOT NULL,
                `cart_total` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `status` int(10) unsigned NOT NULL DEFAULT '0',
                `environment` int(10) unsigned NOT NULL DEFAULT '0',
                `id_currency` int(10) unsigned NOT NULL DEFAULT '0',
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_duitku_payment_transaction`)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "qdp_duitku_order_lock` (
                `id_duitku_order_lock` int(10) unsigned NOT NULL auto_increment,
                `id_cart` int(10) unsigned NOT NULL,
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_duitku_order_lock`)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8",
        );

        foreach ($sqls as $sql) {
            if (!Db::getInstance()->execute(trim($sql))) {
                return false;
            }
        }

        return true;
    }

    public function deleteTable()
    {
        $sql = 'DROP TABLE IF EXISTS '
            . _DB_PREFIX_ . 'qdp_duitku_payment_transaction, '
            . _DB_PREFIX_ . 'qdp_duitku_order_lock';

        return Db::getInstance()->execute($sql);
    }
}
