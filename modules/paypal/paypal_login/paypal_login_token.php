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

header('Content-Type: text/html; charset=utf-8');
include_once dirname(__FILE__).'/../../../config/config.inc.php';
include_once _PS_ROOT_DIR_.'/init.php';


include_once _PS_MODULE_DIR_.'paypal/paypal.php';
include_once _PS_MODULE_DIR_.'paypal/paypal_login/paypal_login.php';
include_once _PS_MODULE_DIR_.'paypal/paypal_login/PayPalLoginUser.php';

$login = new PayPalLogin();

$obj = $login->getAuthorizationCode();
if ($obj) {
    $context = Context::getContext();
    $customer = new Customer((int) $obj->id_customer);
    $context->cookie->id_customer = (int) ($customer->id);
    $context->cookie->customer_lastname = $customer->lastname;
    $context->cookie->customer_firstname = $customer->firstname;
    $context->cookie->logged = 1;
    $customer->logged = 1;
    $context->cookie->is_guest = $customer->isGuest();
    $context->cookie->passwd = $customer->passwd;
    $context->cookie->email = $customer->email;
    $context->customer = $customer;
    $context->cookie->write();
}

?>

<script type="text/javascript">
	window.opener.location.reload(false);
	window.close();
</script>
