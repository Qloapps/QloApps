<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
	
class Blockcustomerprivacy extends Module
{
	public function __construct()
	{
		$this->name = 'blockcustomerprivacy';
		if (version_compare(_PS_VERSION_, '1.4.0.0') >= 0)
			$this->tab = 'front_office_features';
		else
			$this->tab = 'Blocks';
		$this->version = '1.1.2';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
			
		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Customer data privacy block');
		$this->description = $this->l('Adds a block displaying a message about a customer\'s privacy data.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}
	
	public function install()
	{	
		$return = (parent::install() && $this->registerHook('createAccountForm') && $this->registerHook('header') && $this->registerHook('actionBeforeSubmitAccount'));
		Configuration::updateValue('CUSTPRIV_MESSAGE', array($this->context->language->id => 
			$this->l('The personal data you provide is used to answer queries, process orders or allow access to specific information.').' '.
			$this->l('You have the right to modify and delete all the personal information found in the "My Account" page.')
		));
		return $return;
	}
	
	public function getContent()
	{
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		$languages = Language::getLanguages(false);
		$iso = $this->context->language->iso_code;

		$output = '';
		if (Tools::isSubmit('submitCustPrivMess'))
		{
			$message_trads = array();
			foreach ($_POST as $key => $value)
				if (preg_match('/custpriv_message_/i', $key))
				{
					$id_lang = preg_split('/custpriv_message_/i', $key);
					$message_trads[(int)$id_lang[1]] = $value;
				}
			Configuration::updateValue('CUSTPRIV_MESSAGE', $message_trads, true);
			$this->_clearCache('blockcustomerprivacy.tpl');
			$output .= $this->displayConfirmation($this->l('Configuration updated'));
		}		
		
		return $output.$this->renderForm();
	}
	
	public function checkConfig()
	{
		if (!$this->active)
			return false;
		
		$message = Configuration::get('CUSTPRIV_MESSAGE', $this->context->language->id);
		if (empty($message))
			return false;
		
		return true;
	}
	
	public function hookHeader($params)
	{
		if (!$this->checkConfig())
			return;
		$this->context->controller->addJS($this->_path.'blockcustomerprivacy.js');
	}
	
	public function hookActionBeforeSubmitAccount($params)
	{
		if (!$this->checkConfig())
			return;
		
		if (!Tools::getValue('customer_privacy'))
			$this->context->controller->errors[] = $this->l('If you agree to the terms in the Customer Data Privacy message, please click the check box below.');
	}
	
	public function hookCreateAccountForm($params)
	{
		if (!$this->checkConfig())
			return;
		if (!$this->isCached('blockcustomerprivacy.tpl', $this->getCacheId()))
			$this->smarty->assign('privacy_message', Configuration::get('CUSTPRIV_MESSAGE', $this->context->language->id));
		
		return $this->display(__FILE__, 'blockcustomerprivacy.tpl', $this->getCacheId());
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'textarea',
						'lang' => true,
						'autoload_rte' => true,
						'label' => $this->l('Customer data privacy message:'),
						'name' => 'custpriv_message',
						'desc' => $this->l('The customer data privacy message will be displayed in the account creation form.').'<br>'.$this->l('Tip: If the customer privacy message is too long to be written directly in the form, you can add a link to one of your pages. This can easily be created via the "CMS" page under the "Preferences" menu.')
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitCustPrivMess';
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
		$return = array();
		foreach (Language::getLanguages(false) as $lang)
			$return['custpriv_message'][(int)$lang['id_lang']] = Tools::getValue('custpriv_message_'.(int)$lang['id_lang'], Configuration::get('CUSTPRIV_MESSAGE', (int)$lang['id_lang']));

		return $return;
	}
}
