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

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class Blockcontactinfos extends Module
{
	protected static $contact_fields = array(
		'BLOCKCONTACTINFOS_COMPANY',
		'BLOCKCONTACTINFOS_ADDRESS',
		'BLOCKCONTACTINFOS_PHONE',
		'BLOCKCONTACTINFOS_EMAIL',
	);

	public function __construct()
	{
		$this->name = 'blockcontactinfos';
		$this->author = 'PrestaShop';
		$this->tab = 'front_office_features';
		$this->version = '1.2.0';

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Contact information block');
		$this->description = $this->l('This module will allow you to display your e-store\'s contact information in a customizable block.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		Configuration::updateValue('BLOCKCONTACTINFOS_COMPANY', Configuration::get('PS_SHOP_NAME'));
		Configuration::updateValue('BLOCKCONTACTINFOS_ADDRESS', trim(preg_replace('/ +/', ' ', Configuration::get('PS_SHOP_ADDR1').' '.Configuration::get('PS_SHOP_ADDR2')."\n".Configuration::get('PS_SHOP_CODE').' '.Configuration::get('PS_SHOP_CITY')."\n".Country::getNameById(Configuration::get('PS_LANG_DEFAULT'), Configuration::get('PS_SHOP_COUNTRY_ID')))));
		Configuration::updateValue('BLOCKCONTACTINFOS_PHONE', Configuration::get('PS_SHOP_PHONE'));
		Configuration::updateValue('BLOCKCONTACTINFOS_EMAIL', Configuration::get('PS_SHOP_EMAIL'));
		$this->_clearCache('blockcontactinfos.tpl');
		return (parent::install() && $this->registerHook('header') && $this->registerHook('footer'));
	}

	public function uninstall()
	{
		foreach (Blockcontactinfos::$contact_fields as $field)
			Configuration::deleteByName($field);
		return (parent::uninstall());
	}

	public function getContent()
	{
		$html = '';
		if (Tools::isSubmit('submitModule'))
		{	
			foreach (Blockcontactinfos::$contact_fields as $field)
				Configuration::updateValue($field, Tools::getValue($field));
			$this->_clearCache('blockcontactinfos.tpl');
			$html = $this->displayConfirmation($this->l('Configuration updated'));
		}

		return $html.$this->renderForm();
	}

	public function hookHeader()
	{
		$this->context->controller->addCSS(($this->_path).'blockcontactinfos.css', 'all');
	}
	
	public function hookFooter($params)
	{	
		if (!$this->isCached('blockcontactinfos.tpl', $this->getCacheId()))
			foreach (Blockcontactinfos::$contact_fields as $field)
				$this->smarty->assign(strtolower($field), Configuration::get($field));
		return $this->display(__FILE__, 'blockcontactinfos.tpl', $this->getCacheId());
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
						'type' => 'text',
						'label' => $this->l('Company name'),
						'name' => 'BLOCKCONTACTINFOS_COMPANY',
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Address'),
						'name' => 'BLOCKCONTACTINFOS_ADDRESS',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Phone number'),
						'name' => 'BLOCKCONTACTINFOS_PHONE',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Email'),
						'name' => 'BLOCKCONTACTINFOS_EMAIL',
					),
				),
				'submit' => array(
					'title' => $this->l('Save')
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
		$helper->submit_action = 'submitModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => array(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		foreach (Blockcontactinfos::$contact_fields as $field)
			$helper->tpl_vars['fields_value'][$field] = Tools::getValue($field, Configuration::get($field));
		return $helper->generateForm(array($fields_form));
	}
}