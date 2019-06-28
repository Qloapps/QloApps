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

function upgrade_module_3_0($object, $install = false)
{
    $paypal_version = Configuration::get('PAYPAL_VERSION');

    if ((!$paypal_version) || (empty($paypal_version)) || ($paypal_version < $object->version)) {
        /* Update hooks */
        $object->registerHook('payment');
        $object->registerHook('paymentReturn');
        $object->registerHook('shoppingCartExtra');
        $object->registerHook('backBeforePayment');
        $object->registerHook('cancelProduct');
        $object->registerHook('productFooter');
        $object->registerHook('header');
        $object->registerHook('adminOrder');
        $object->registerHook('backOfficeHeader');

        Configuration::updateValue('PAYPAL_VERSION', $object->version);

        $payment_method = (int) Configuration::get('PAYPAL_PAYMENT_METHOD');
        $payment_methods = array(0 => WPS, 2 => HSS, 1 => ECS);

        Configuration::updateValue('PAYPAL_PAYMENT_METHOD', (int) $payment_methods[$payment_method]);
        Configuration::updateValue('PAYPAL_BUSINESS_ACCOUNT', Configuration::get('PAYPAL_BUSINESS'));
        Configuration::updateValue('PAYPAL_BUSINESS', 0);
    }

    if (count(Db::getInstance()->ExecuteS('SHOW TABLES FROM `'._DB_NAME_.'` LIKE \''._DB_PREFIX_.'paypal_order\'')) > 0) {
        $columns = array(array('name' => 'id_invoice', 'type' => 'varchar(255) DEFAULT NULL'),
            array('name' => 'currency', 'type' => 'varchar(10) NOT NULL'),
            array('name' => 'total_paid', 'type' => 'varchar(50) NOT NULL'),
            array('name' => 'shipping', 'type' => 'varchar(50) NOT NULL'),
            array('name' => 'payment_date', 'type' => 'varchar(50) NOT NULL'),
            array('name' => 'capture', 'type' => 'int(2) NOT NULL'));

        foreach ($columns as $column) {
            if (!Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'paypal_order` LIKE \''.pSQL($column['name']).'\'')) {
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'paypal_order` ADD `'.pSQL($column['name']).'` '.pSQL($column['type']));
            }
        }

    }

    if (count(Db::getInstance()->ExecuteS('SHOW TABLES FROM `'._DB_NAME_.'` LIKE \''._DB_PREFIX_.'paypal_customer\'')) <= 0) {
        Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_customer` (
				`id_paypal_customer` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_customer` int(10) unsigned NOT NULL,
				`paypal_email` varchar(255) NOT NULL,
				PRIMARY KEY (`id_paypal_customer`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
    }
    return true;
}
