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

class PayPalInstall
{
    /**
     * Create PayPal tables
     */
    public function createTables()
    {
        /* Set database */
        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_order` (
            `id_order` int(10) unsigned NOT NULL,
            `id_transaction` varchar(255) NOT NULL,
            `id_invoice` varchar(255) DEFAULT NULL,
            `currency` varchar(10) NOT NULL,
            `total_paid` varchar(50) NOT NULL,
            `shipping` varchar(50) NOT NULL,
            `capture` int(2) NOT NULL,
            `payment_date` varchar(50) NOT NULL,
            `payment_method` int(2) unsigned NOT NULL,
            `payment_status` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id_order`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')) {
            return false;
        }

        /* Set database */
        if (!Db::getInstance()->Execute('
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_customer` (
            `id_paypal_customer` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_customer` int(10) unsigned NOT NULL,
            `paypal_email` varchar(255) NOT NULL,
            PRIMARY KEY (`id_paypal_customer`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_login_user`  (
                `id_paypal_login_user` INT(11) AUTO_INCREMENT,
                `id_customer` INT(11) NOT NULL,
                `token_type` VARCHAR(255) NOT NULL,
                `expires_in` VARCHAR(255) NOT NULL,
                `refresh_token` VARCHAR(255) NOT NULL,
                `id_token` VARCHAR(255) NOT NULL,
                `access_token` VARCHAR(255) NOT NULL,
                `account_type` VARCHAR(255) NOT NULL,
                `user_id` VARCHAR(255) NOT NULL,
                `verified_account` VARCHAR(255) NOT NULL,
                `zoneinfo` VARCHAR(255) NOT NULL,
                `age_range` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`id_paypal_login_user`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8')) {
            return false;
        }

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
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_plus_pui` (
                `id_paypal_plus_pui` int(11) NOT NULL AUTO_INCREMENT,
                `id_order` int(11) NOT NULL,
                `pui_informations` text NOT NULL,
                PRIMARY KEY (`id_paypal_plus_pui`)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
        ')) {
            return false;
        }
    }

    /**
     * Set configuration table
     */
    public function updateConfiguration($paypal_version)
    {
        Configuration::updateValue('PAYPAL_SANDBOX', 0);
        Configuration::updateValue('PAYPAL_HEADER', '');
        Configuration::updateValue('PAYPAL_BUSINESS', 0);
        Configuration::updateValue('PAYPAL_BUSINESS_ACCOUNT', 'paypal@prestashop.com');
        Configuration::updateValue('PAYPAL_API_USER', '');
        Configuration::updateValue('PAYPAL_API_PASSWORD', '');
        Configuration::updateValue('PAYPAL_API_SIGNATURE', '');
        Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT', 0);
        Configuration::updateValue('PAYPAL_CAPTURE', 0);
        Configuration::updateValue('PAYPAL_PAYMENT_METHOD', WPS);
        Configuration::updateValue('PAYPAL_NEW', 1);
        Configuration::updateValue('PAYPAL_DEBUG_MODE', 0);
        Configuration::updateValue('PAYPAL_SHIPPING_COST', 20.00);
        Configuration::updateValue('PAYPAL_VERSION', $paypal_version);
        Configuration::updateValue('PAYPAL_COUNTRY_DEFAULT', (int) Configuration::get('PS_COUNTRY_DEFAULT'));
        Configuration::updateValue('PAYPAL_USE_3D_SECURE', 0);
        // PayPal v3 configuration
        Configuration::updateValue('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT', 0);
        //$paypal = new Paypal();
        //$ssl_verif = new TLSVerificator(true, $paypal);
        //Configuration::updateValue('PAYPAL_VERSION_TLS_CHECKED', $ssl_verif->getVersion());

        Configuration::updateValue('VZERO_ENABLED', 0);
        Configuration::updateValue('PAYPAL_BRAINTREE_ENABLED', 0);
    }

    /**
     * Delete PayPal configuration
     */
    public function deleteConfiguration()
    {
        Configuration::deleteByName('PAYPAL_SANDBOX');
        Configuration::deleteByName('PAYPAL_HEADER');
        Configuration::deleteByName('PAYPAL_BUSINESS');
        Configuration::deleteByName('PAYPAL_API_USER');
        Configuration::deleteByName('PAYPAL_API_PASSWORD');
        Configuration::deleteByName('PAYPAL_API_SIGNATURE');
        Configuration::deleteByName('PAYPAL_BUSINESS_ACCOUNT');
        Configuration::deleteByName('PAYPAL_EXPRESS_CHECKOUT');
        Configuration::deleteByName('PAYPAL_PAYMENT_METHOD');
        Configuration::deleteByName('PAYPAL_TEMPLATE');
        Configuration::deleteByName('PAYPAL_CAPTURE');
        Configuration::deleteByName('PAYPAL_DEBUG_MODE');
        Configuration::deleteByName('PAYPAL_COUNTRY_DEFAULT');
        Configuration::deleteByName('PAYPAL_VERSION');

        /* USE PAYPAL LOGIN */
        Configuration::deleteByName('PAYPAL_LOGIN');
        Configuration::deleteByName('PAYPAL_LOGIN_CLIENT_ID');
        Configuration::deleteByName('PAYPAL_LOGIN_SECRET');
        Configuration::deleteByName('PAYPAL_LOGIN_TPL');
        /* /USE PAYPAL LOGIN */

        // PayPal v3 configuration
        Configuration::deleteByName('PAYPAL_EXPRESS_CHECKOUT_SHORTCUT');

        Configuration::deleteByName('PAYPAL_BRAINTREE_ENABLED');

        // Braintree access credentials
        Configuration::deleteByName('PAYPAL_BRAINTREE_ACCESS_TOKEN');
        Configuration::deleteByName('PAYPAL_BRAINTREE_EXPIRE_AT');
        Configuration::deleteByName('PAYPAL_BRAINTREE_REFRESH_TOKEN');
        Configuration::deleteByName('PAYPAL_BRAINTREE_MERCHANT_ID');
    }

    /**
     * Create a new order state
     */
    public function createOrderState()
    {
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
                $source = dirname(__FILE__).'/../../img/os/'.Configuration::get('PS_OS_PAYPAL').'.gif';
                $destination = dirname(__FILE__).'/../../img/os/'.(int) $order_state->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('PAYPAL_OS_AUTHORIZATION', (int) $order_state->id);
        }

        // OS BRAINTREE

        if (!Configuration::get('PAYPAL_BT_OS_AUTHORIZATION', false)) {
            $order_state_auth = new OrderState();
            $order_state_auth->name = array();


            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state_auth->name[$language['id_lang']] = 'Autorisation acceptée par Braintree';
                } else {
                    $order_state_auth->name[$language['id_lang']] = 'Authorization accepted from Braintree';
                }

            }
            $order_state_auth->send_email = false;
            $order_state_auth->color = '#4169E1';
            $order_state_auth->hidden = false;
            $order_state_auth->delivery = false;
            $order_state_auth->logable = true;
            $order_state_auth->invoice = true;
            if ($order_state_auth->add()) {
                $source = _PS_MODULE_DIR_.'paypal/views/img/logos/os_braintree.png';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state_auth->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('PAYPAL_BT_OS_AUTHORIZATION', (int) $order_state_auth->id);
        }


        if (!Configuration::get('PAYPAL_BRAINTREE_OS_AWAITING', false)) {
            $order_state_wait = new OrderState();
            $order_state_wait->name = array();


            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state_wait->name[$language['id_lang']] = 'En attente de paiement Braintree';
                } else {
                    $order_state_wait->name[$language['id_lang']] = 'Awaiting for Braintree payment';
                }

            }
            $order_state_wait->send_email = false;
            $order_state_wait->color = '#4169E1';
            $order_state_wait->hidden = false;
            $order_state_wait->delivery = false;
            $order_state_wait->logable = true;
            $order_state_wait->invoice = false;
            if ($order_state_wait->add()) {
                $source = _PS_MODULE_DIR_.'paypal/views/img/logos/os_braintree.png';
                $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state_wait->id.'.gif';
                copy($source, $destination);

            }
            Configuration::updateValue('PAYPAL_BRAINTREE_OS_AWAITING', (int) $order_state_wait->id);
        }

    }
}
