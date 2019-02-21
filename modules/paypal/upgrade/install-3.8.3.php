<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_8_3($object, $install = false)
{
    $paypal_version = Configuration::get('PAYPAL_VERSION');

    if ((!$paypal_version) || (empty($paypal_version)) || ($paypal_version < $object->version)) {
        if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'paypal_order` LIKE \'id_invoice\'') == false) {
            Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'paypal_order` ADD `id_invoice` varchar(255) DEFAULT NULL');
        }

        if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'paypal_order` LIKE \'currency\'') == false) {
            Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'paypal_order` ADD `currency` varchar(10) DEFAULT NULL');
        }

        if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'paypal_order` LIKE \'total_paid\'') == false) {
            Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'paypal_order` ADD `total_paid` varchar(50) DEFAULT NULL');
        }

        if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'paypal_order` LIKE \'shipping\'') == false) {
            Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'paypal_order` ADD `shipping` varchar(50) DEFAULT NULL');
        }

        if (Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'paypal_order` LIKE \'payment_date\'') == false) {
            Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'paypal_order` ADD `payment_date` varchar(50) DEFAULT NULL');
        }

        Configuration::updateValue('PAYPAL_VERSION', '3.8.3');
    }

    return true;
}
