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

function upgrade_module_3_11_2($object, $install = false)
{
    $paypal_version = Configuration::get('PAYPAL_VERSION');

    if ((!$paypal_version) || (empty($paypal_version)) || ($paypal_version < $object->version)) {
        // OS BRAINTREE

        $order_state_auth = new OrderState();
        $order_state_auth->name = array();

        $order_state_wait = new OrderState();
        $order_state_wait->name = array();


        foreach (Language::getLanguages() as $language) {
            if (Tools::strtolower($language['iso_code']) == 'fr') {
                $order_state_auth->name[$language['id_lang']] = 'Autorisation acceptée par Braintree';
                $order_state_wait->name[$language['id_lang']] = 'En attente de paiement Braintree';
            } else {
                $order_state_auth->name[$language['id_lang']] = 'Authorization accepted from Braintree';
                $order_state_wait->name[$language['id_lang']] = 'Awaiting for Braintree payment';
            }

        }

        $order_state_auth->send_email = false;
        $order_state_auth->color = '#DDEEFF';
        $order_state_auth->hidden = false;
        $order_state_auth->delivery = false;
        $order_state_auth->logable = true;
        $order_state_auth->invoice = true;

        $order_state_wait->send_email = false;
        $order_state_wait->color = '#4169E1';
        $order_state_wait->hidden = false;
        $order_state_wait->delivery = false;
        $order_state_wait->logable = true;
        $order_state_wait->invoice = false;

        if ($order_state_auth->add()) {
            $source = _PS_MODULE_DIR_.'paypal/views/img/logos/os_braintree.png';
            $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state_auth->id.'.gif';
            copy($source, $destination);
        }
        Configuration::updateValue('PAYPAL_BT_OS_AUTHORIZATION', (int) $order_state_auth->id);

        if ($order_state_wait->add()) {

            $source = _PS_MODULE_DIR_.'paypal/views/img/logos/os_braintree.png';
            $destination = _PS_ROOT_DIR_.'/img/os/'.(int) $order_state_wait->id.'.gif';
            copy($source, $destination);
        }
        Configuration::updateValue('PAYPAL_BRAINTREE_OS_AWAITING', (int) $order_state_wait->id);

    }
    return true;
}
