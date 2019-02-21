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
 *  @version  Release: $Revision: 13573 $
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once(_PS_MODULE_DIR_.'paypal/paypal.php');

class PayPalPlusPatchModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();

        if (class_exists('Context')) {
            $this->context = Context::getContext();
        } else {
            global $smarty, $cookie;
            $this->context = new StdClass();
            $this->context->smarty = $smarty;
            $this->context->cookie = $cookie;
        }
        $this->ajax = true;
    }

    public function postProcess()
    {
        if (Tools::getValue('id_cart') == $this->context->cart->id) {
            if (Tools::getValue('id_cart') && Tools::getValue('id_payment')) {
                $cart = new Cart(Tools::getValue('id_cart'));
                $address_delivery = new Address($cart->id_address_delivery);
                $ppplus = new CallApiPaypalPlus();
                $result = $ppplus->patch(Tools::getValue('id_payment'), $address_delivery);
            }
        }
    }
}
