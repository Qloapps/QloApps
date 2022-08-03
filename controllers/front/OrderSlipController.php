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

class OrderSlipControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'order-slip';
    public $authRedirection = 'order-slip';
    public $ssl = true;

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(array(_THEME_CSS_DIR_.'history.css', _THEME_CSS_DIR_.'addresses.css'));
        $this->addJqueryPlugin(array('scrollTo', 'footable', 'footable-sort'));
        $this->addJS(array(
            _THEME_JS_DIR_.'history.js',
            _THEME_JS_DIR_.'tools.js') // retro compat themes 1.5
        );
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign('ordersSlip', OrderSlip::getOrdersSlip((int)$this->context->cookie->id_customer));
        $this->setTemplate(_PS_THEME_DIR_.'order-slip.tpl');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('generateVoucher')) {
            $idOrderSlip = Tools::getValue('id_order_slip');
            $objOrderSlip = new OrderSlip($idOrderSlip);
            if (!Validate::isLoadedObject($objOrderSlip)) {
                $this->errors[] = Tools::displayError('The credit slip is invalid.');
            } elseif ($objOrderSlip->generated) {
                $this->errors[] = Tools::displayError('The voucher code for this credit slip has already been generated.');
            } elseif ($objOrderSlip->id_customer != $this->context->customer->id) {
                $this->errors[] = Tools::displayError('Fatal error.');
            }

            if (!count($this->errors)) {
                if ($idCartRule = $objOrderSlip->generateVoucher()) {
                    $objCartRule = new CartRule($idCartRule);
                    $objCustomer = new Customer($objCartRule->id_customer);

                    $creditSlipPrefix = Configuration::get('PS_CREDIT_SLIP_PREFIX', $this->context->language->id);
                    $creditSlipID = sprintf(('%1$s%2$06d'), $creditSlipPrefix, (int) $objOrderSlip->id);

                    $objCurrency = new Currency($objCartRule->reduction_currency, $this->context->language->id);
                    $mailVars['{firstname}'] = $objCustomer->firstname;
                    $mailVars['{lastname}'] = $objCustomer->lastname;
                    $mailVars['{credit_slip_id}'] = $creditSlipID;
                    $mailVars['{voucher_code}'] = $objCartRule->code;
                    $mailVars['{voucher_amount}'] = Tools::displayPrice($objCartRule->reduction_amount, $objCurrency, false);

                    Mail::Send(
                        $this->context->language->id,
                        'credit_slip_voucher',
                        sprintf(Mail::l('New voucher for your credit slip #%s', $this->context->language->id), $creditSlipID),
                        $mailVars,
                        $objCustomer->email,
                        $objCustomer->firstname.' '.$objCustomer->lastname,
                        null,
                        null,
                        null,
                        null,
                        _PS_MAIL_DIR_,
                        true
                    );

                    Tools::redirect($this->context->link->getPageLink(
                        $this->php_self,
                        $this->ssl,
                        $this->context->language->id,
                        'confirmation=1'
                    ));
                }

                $this->errors[] = Tools::displayError('The voucher code for this credit slip could not be generated.');
            }
        }

        parent::postProcess();
    }
}
