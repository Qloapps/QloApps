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

/**
 * @since 1.5.0
 */

class PayPalConfirmModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;

    public function initContent()
    {
        if (!$this->context->customer->isLogged(true) || empty($this->context->cart)) {
            Tools::redirect('index.php');
        }

        parent::initContent();

        $this->paypal = new PayPal();
        $this->context = Context::getContext();
        $this->id_module = (int) Tools::getValue('id_module');

        //$currency = new Currency((int) $this->context->cart->id_currency);

        $this->module->assignCartSummary();

        $this->context->smarty->assign(array(
            'form_action' => PayPal::getShopDomainSsl(true, true)._MODULE_DIR_.$this->paypal->name.'/express_checkout/payment.php',
        ));

        $this->setTemplate('order-summary.tpl');
    }
}
