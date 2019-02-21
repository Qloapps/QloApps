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

function upgrade_module_3_11($object, $install = false)
{
    $paypal_version = Configuration::get('PAYPAL_VERSION');

    if ((!$paypal_version) || (empty($paypal_version)) || ($paypal_version < $object->version)) {
        if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_braintree` (
                `id_paypal_braintree` int(11) NOT NULL AUTO_INCREMENT,
                `id_cart` int(11) NOT NULL,
                `nonce_payment_token` varchar(255) NOT NULL,
                `client_token` text NOT NULL,
                `transaction` varchar(255) NULL,
                `datas` varchar(255) NULL,
                `id_order` int(11) NULL,
			    PRIMARY KEY (`id_paypal_braintree`)
				) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;')) {
            return false;
        }
        if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_plus_pui` (
                `id_paypal_plus_pui` int(11) NOT NULL AUTO_INCREMENT,
                `id_order` int(11) NOT NULL,
                `pui_informations` text NOT NULL,
                PRIMARY KEY (`id_paypal_plus_pui`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;')) {
            return false;
        }

        if (_PS_VERSION_ >= '1.5') {
            $object->registerHook('actionOrderStatusPostUpdate');
            $object->registerHook('displayOrderConfirmation');
        }

        Configuration::updateValue('PAYPAL_VERSION', '3.11.0');

    }
    return true;
}
