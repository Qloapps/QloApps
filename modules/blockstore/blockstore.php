<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockStore extends Module
{
	public function __construct()
	{
		$this->name = 'blockstore';
		$this->tab = 'front_office_features';
		$this->version = '1.3.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Store locator block');
		$this->description = $this->l('Displays an image link to PrestaShop\'s store locator feature.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
	}

	public function install()
	{
		if (!parent::install())
			return false;

		// Hook the module either on the left or right column
		$theme = new Theme(Context::getContext()->shop->id_theme);
		if ((!$theme->default_right_column || !$this->registerHook('rightColumn'))
			&& (!$theme->default_left_column || !$this->registerHook('leftColumn')))
		{
			// If there are no colums implemented by the template, throw an error and uninstall the module
			$this->_errors[] = $this->l('This module needs to be hooked in a column, but your theme does not implement one.');
			parent::uninstall();
			return false;
		}

		if (file_exists(dirname(__FILE__).'/store.jpg'))
			Configuration::updateValue('BLOCKSTORE_IMG', 'store.jpg');

		// Hook the module at the end on the header, only if it has been hooked 
		return $this->registerHook('header');
	}

	public function uninstall()
	{
		Configuration::deleteByName('BLOCKSTORE_IMG');
		return parent::uninstall();
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookRightColumn($params)
	{
		if (!$this->isCached('blockstore.tpl', $this->getCacheId()))
		{
			$id_lang = $this->context->cart->id_lang;
			$this->smarty->assign(array(
					'store_img' => Configuration::get('BLOCKSTORE_IMG'),
					'store_text' => Configuration::get('BLOCKSTORE_TEXT', $id_lang),
				));
			$sql = 'SELECT COUNT(*)
					FROM '._DB_PREFIX_.'store s'
					.Shop::addSqlAssociation('store', 's');
			$total = Db::getInstance()->getValue($sql);
			
			if ($total <= 0)
				return;
		}
		return $this->display(__FILE__, 'blockstore.tpl', $this->getCacheId());

	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'blockstore.css', 'all');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitStoreConf'))
		{
			if (!($languages = Language::getLanguages(true)))
				return false;

			$text = array();
			foreach ($languages as $lang)
				$text[$lang['id_lang']] = Tools::getValue('BLOCKSTORE_TEXT_'.$lang['id_lang']);

			Configuration::updateValue('BLOCKSTORE_TEXT', $text);

			if (isset($_FILES['BLOCKSTORE_IMG']) && isset($_FILES['BLOCKSTORE_IMG']['tmp_name']) && !empty($_FILES['BLOCKSTORE_IMG']['tmp_name']))
			{
				if ($error = ImageManager::validateUpload($_FILES['BLOCKSTORE_IMG'], 4000000))
					return $this->displayError($this->l('Invalid image.'));
				else
				{
					$ext = substr($_FILES['BLOCKSTORE_IMG']['name'], strrpos($_FILES['BLOCKSTORE_IMG']['name'], '.') + 1);
					$file_name = md5($_FILES['BLOCKSTORE_IMG']['name']).'.'.$ext;
					if (!move_uploaded_file($_FILES['BLOCKSTORE_IMG']['tmp_name'], dirname(__FILE__).'/'.$file_name))
						return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
					else
					{
						if (Configuration::hasContext('BLOCKSTORE_IMG', null, Shop::getContext()) && Configuration::get('BLOCKSTORE_IMG') != $file_name)
							@unlink(dirname(__FILE__).'/'.Configuration::get('BLOCKSTORE_IMG'));
						Configuration::updateValue('BLOCKSTORE_IMG', $file_name);
						$this->_clearCache('blockstore.tpl');
						return $this->displayConfirmation($this->l('The settings have been updated.'));
					}
				}
			}
			$this->_clearCache('blockstore.tpl');
		}
		return '';
	}

	public function getContent()
	{
		return $this->postProcess().$this->renderForm();
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
						'type' => 'file',
						'label' => $this->l('Image for the Store Locator block'),
						'name' => 'BLOCKSTORE_IMG',
						'thumb' => '../modules/'.$this->name.'/'.Configuration::get('BLOCKSTORE_IMG'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Text for the Store Locator block'),
						'name' => 'BLOCKSTORE_TEXT',
						'lang' => true,
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
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitStoreConf';
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
		if (!($languages = Language::getLanguages(true)))
			return false;

		$data = array(
			'BLOCKSTORE_IMG' => Tools::getValue('BLOCKSTORE_IMG', Configuration::get('BLOCKSTORE_IMG')),
		);

		foreach ($languages as $lang)
			$data['BLOCKSTORE_TEXT'][$lang['id_lang']] = Configuration::get('BLOCKSTORE_TEXT', $lang['id_lang']);

		return $data;
	}
}
