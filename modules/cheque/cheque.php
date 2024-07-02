<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Cheque extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();

	public $chequeName;
	public $address;
	public $extra_mail_vars;

	public function __construct()
	{
		$this->name = 'cheque';
		$this->tab = 'payments_gateways';
		$this->version = '2.6.6';
		$this->author = 'PrestaShop';
		$this->controllers = array('payment', 'validation');
		$this->is_eu_compatible = 1;

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('CHEQUE_NAME', 'CHEQUE_ADDRESS'));
		if (isset($config['CHEQUE_NAME']))
			$this->chequeName = $config['CHEQUE_NAME'];
		if (isset($config['CHEQUE_ADDRESS']))
			$this->address = $config['CHEQUE_ADDRESS'];

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Payments by check');
		$this->description = $this->l('This module allows you to accept payments for bookings by check.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete these details?');

		if ((!isset($this->chequeName) || !isset($this->address) || empty($this->chequeName) || empty($this->address)))
			$this->warning = $this->l('The "Pay to the order of" and "Address" fields must be configured before using this module.');

		$currencies = Currency::checkPaymentCurrencies($this->id);
		if (!$currencies || !count($currencies)) {
			$this->warning = $this->l('No currency has been set for this module.');
		}

		$this->payment_type = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT;
	}

	public function getExtraMailContent($id_order_state, $order)
	{
        if (Configuration::get('PS_OS_AWAITING_PAYMENT') == $id_order_state) {
			$this->context->smarty->assign(array(
				'cheque_name' => Configuration::get('CHEQUE_NAME'),
				'cheque_address' => Configuration::get('CHEQUE_ADDRESS'),
				'cheque_address_html' => str_replace("\n", '<br />', Configuration::get('CHEQUE_ADDRESS')),
                'lang' => new Language($order->id_lang)
			));

			return array(
                '{extra_mail_content_html}' => $this->context->smarty->fetch(
                    $this->local_path.'views/templates/mail/mail_template_html.tpl'
				),
                '{extra_mail_content_txt}' => $this->context->smarty->fetch(
                    $this->local_path.'views/templates/mail/mail_template_text.tpl'
				)
            );
		}
		return array();
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('payment') || ! $this->registerHook('displayPaymentEU') || !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('CHEQUE_NAME') || !Configuration::deleteByName('CHEQUE_ADDRESS') || !parent::uninstall())
			return false;
		return true;
	}

	private function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			if (!Tools::getValue('CHEQUE_NAME'))
				$this->_postErrors[] = $this->l('The "Pay to the order of" field is required.');
			elseif (!Tools::getValue('CHEQUE_ADDRESS'))
				$this->_postErrors[] = $this->l('The "Address" field is required.');
		}
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			Configuration::updateValue('CHEQUE_NAME', Tools::getValue('CHEQUE_NAME'));
			Configuration::updateValue('CHEQUE_ADDRESS', Tools::getValue('CHEQUE_ADDRESS'));
		}
		$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}

	private function _displayCheque()
	{
		return $this->display(__FILE__, 'infos.tpl');
	}

	public function getContent()
	{
		$this->_html = '';

		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= $this->displayError($err);
		}

		$this->_html .= $this->_displayCheque();
		$this->_html .= $this->renderForm();

		return $this->_html;
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;

		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_cheque' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookDisplayPaymentEU($params)
	{
		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;

		$payment_options = array(
			'cta_text' => $this->l('Pay by Check'),
			'logo' => Media::getMediaPath(dirname(__FILE__).'/cheque.jpg'),
			'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true)
		);

		return $payment_options;
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active) {
            return;
		}
		$objOrder = $params['objOrder'];
		$idOrderState = $objOrder->getCurrentState();
        $objOrderState = new OrderState($idOrderState);
        $history = $objOrder->getHistory($this->context->language->id);
        $initialStatus = array_pop($history);
        if ($idOrderState == Configuration::get('PS_OS_AWAITING_PAYMENT')
            || ($objOrderState->logable
                && $initialStatus['id_order_state'] == Configuration::get('PS_OS_AWAITING_PAYMENT')
            )
        ) {
			$objCart = new Cart($objOrder->id_cart);
            if ($objCart->is_advance_payment) {
                $cartTotal = $objOrder->getOrdersTotalPaid(1);
            } else {
                $cartTotal = $objOrder->getOrdersTotalPaid();
			}

			$smartyVars['total_to_pay'] = Tools::displayPrice($cartTotal, $params['currencyObj'], false);
            $smartyVars['chequeName'] = $this->chequeName;
            $smartyVars['chequeAddress'] = Tools::nl2br($this->address);
            $smartyVars['status'] = 'ok';
            $smartyVars['id_order'] = $objOrder->id;
            $smartyVars['reference'] = $objOrder->reference;
		} else {
			$smartyVars['status'] = 'failed';
		}

		$this->smarty->assign($smartyVars);
		return $this->display(__FILE__, 'payment_return.tpl');
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency((int)($cart->id_currency));
		$currencies_module = $this->getCurrency((int)$cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Contact details'),
					'icon' => 'icon-envelope'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Pay to the order of (name)'),
						'name' => 'CHEQUE_NAME',
						'required' => true
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Address'),
						'desc' => $this->l('Address where the check should be sent to.'),
						'name' => 'CHEQUE_ADDRESS',
						'required' => true
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'CHEQUE_NAME' => Tools::getValue('CHEQUE_NAME', Configuration::get('CHEQUE_NAME')),
			'CHEQUE_ADDRESS' => Tools::getValue('CHEQUE_ADDRESS', Configuration::get('CHEQUE_ADDRESS')),
		);
	}
}
