<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PdfOrderReturnControllerCore extends FrontController
{
    public $php_self = 'pdf-order-return';
    protected $display_header = false;
    protected $display_footer = false;

    public function postProcess()
    {
        $from_admin = (Tools::getValue('adtoken') == Tools::getAdminToken('AdminReturn'.(int)Tab::getIdFromClassName('AdminReturn').(int)Tools::getValue('id_employee')));

        if (!$from_admin && !$this->context->customer->isLogged()) {
            Tools::redirect('index.php?controller=authentication&back=order-follow');
        }

        if (Tools::getValue('id_order_return') && Validate::isUnsignedId(Tools::getValue('id_order_return'))) {
            $this->orderReturn = new OrderReturn(Tools::getValue('id_order_return'));
        }

        if (!isset($this->orderReturn) || !Validate::isLoadedObject($this->orderReturn)) {
            die(Tools::displayError('Order return not found.'));
        } elseif (!$from_admin && $this->orderReturn->id_customer != $this->context->customer->id) {
            die(Tools::displayError('Order return not found.'));
        } elseif ($this->orderReturn->state < 2) {
            die(Tools::displayError('Order return not confirmed.'));
        }
    }

    public function display()
    {
        $pdf = new PDF($this->orderReturn, PDF::TEMPLATE_ORDER_RETURN, $this->context->smarty);
        $pdf->render();
    }
}
