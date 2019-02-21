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

function upgrade_module_3_9($object, $install = false)
{
    $paypal_version = Configuration::get('PAYPAL_VERSION');

    if ((!$paypal_version) || (empty($paypal_version)) || ($paypal_version < $object->version)) {
        if (!Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_capture` (
			  `id_paypal_capture` int(11) NOT NULL AUTO_INCREMENT,
			  `id_order` int(11) NOT NULL,
			  `capture_amount` float NOT NULL,
			  `result` text NOT NULL,
			  `date_add` datetime NOT NULL,
			  `date_upd` datetime NOT NULL,
			  PRIMARY KEY (`id_paypal_capture`)
			) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;')) {
            return false;
        }

        Configuration::updateValue('PAYPAL_VERSION', '3.9.0');
    }

    return true;
}
