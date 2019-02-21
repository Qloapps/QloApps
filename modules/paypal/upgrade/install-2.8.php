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

function upgrade_module_2_8($object, $install = false)
{
    if (!Configuration::get('PAYPAL_NEW') && ($object->active || $install)) {
        $result = true;

        /* Check PayPal API */
        if (file_exists(_PS_MODULE_DIR_.'paypalapi/paypalapi.php')) {
            $confs = Configuration::getMultiple(array('PAYPAL_HEADER', 'PAYPAL_SANDBOX', 'PAYPAL_API_USER', 'PAYPAL_API_PASSWORD',
                'PAYPAL_API_SIGNATURE', 'PAYPAL_EXPRESS_CHECKOUT'));

            include_once _PS_MODULE_DIR_.'paypalapi/paypalapi.php';
            $paypalapi = new PayPalAPI();

            if ($paypalapi->active) {
                if (Configuration::get('PAYPAL_INTEGRAL') == 1) {
                    Configuration::updateValue('PAYPAL_PAYMENT_METHOD', WPS);
                } elseif (Configuration::get('PAYPAL_INTEGRAL') == 0) {
                    Configuration::updateValue('PAYPAL_PAYMENT_METHOD', ECS);
                }

                $paypalapi->uninstall();
                Configuration::loadConfiguration();

                foreach ($confs as $key => $value) {
                    Configuration::updateValue($key, $value);
                }

            }
        }

        /* Create Table */
        if (!Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_order` (
		`id_order` int(10) unsigned NOT null auto_increment,
		`id_transaction` varchar(255) NOT null,
		PRIMARY KEY (`id_order`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')) {
            $result = false;
        }

        if (!Db::getInstance()->Execute('
		ALTER TABLE `'._DB_PREFIX_.'paypal_order` ADD `payment_method` INT NOT null,
		ADD `payment_status` VARCHAR(255) NOT null,
		ADD `capture` INT NOT null')) {
            $result = false;
        }

        /* Hook */
        $object->registerHook('cancelProduct');
        $object->registerHook('adminOrder');

        /* Create OrderState */
        if (!Configuration::get('PAYPAL_OS_AUTHORIZATION')) {
            $order_state = new OrderState();
            $order_state->name = array();

            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state->name[$language['id_lang']] = 'Autorisation acceptée par PayPal';
                } else {
                    $order_state->name[$language['id_lang']] = 'Authorization accepted from PayPal';
                }

            }

            $order_state->send_email = false;
            $order_state->color = '#DDEEFF';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = true;
            $order_state->invoice = true;

            if ($order_state->add()) {
                copy(_PS_ROOT_DIR_.'/img/os/'.Configuration::get('PS_OS_PAYPAL').'.gif', _PS_ROOT_DIR_.'/img/os/'.(int) $order_state->id.'.gif');
            }

            Configuration::updateValue('PAYPAL_OS_AUTHORIZATION', (int) $order_state->id);
        }
        /* Delete unseless configuration */
        Configuration::deleteByName('PAYPAL_INTEGRAL');

        /* Add new Configurations */
        if (!Configuration::get('PAYPAL_PAYMENT_METHOD')) {
            Configuration::updateValue('PAYPAL_PAYMENT_METHOD', WPS);
        }

        Configuration::updateValue('PAYPAL_CAPTURE', 0);
        Configuration::updateValue('PAYPAL_TEMPLATE', 'A');

        if ($result) {
            Configuration::updateValue('PAYPAL_NEW', 1);
        }

        return $result;
    }

    $object->upgrade_detail['2.8'][] = 'PayPalAPI upgrade error !';
    return false;
}
