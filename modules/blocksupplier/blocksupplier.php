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

class BlockSupplier extends Module
{
    function __construct()
    {
        $this->name = 'blocksupplier';
        $this->tab = 'front_office_features';
        $this->version = '1.2.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

        $this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Suppliers block');
        $this->description = $this->l('Adds a block displaying your product suppliers.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

	function install()
	{
		if (!parent::install())
			return false;
		if (!$this->registerHook('displayHeader') ||
				!$this->registerHook('actionObjectSupplierDeleteAfter') ||
				!$this->registerHook('actionObjectSupplierAddAfter') ||
				!$this->registerHook('actionObjectSupplierUpdateAfter')
			)
			return false;

		$theme = new Theme(Context::getContext()->shop->id_theme);
		if ((!$theme->default_right_column || !$this->registerHook('rightColumn'))
			&& (!$theme->default_left_column || !$this->registerHook('leftColumn'))
		)
		{
			parent::uninstall();

			return false;
		}
		Configuration::updateValue('SUPPLIER_DISPLAY_TEXT', true);
		Configuration::updateValue('SUPPLIER_DISPLAY_TEXT_NB', 5);
		Configuration::updateValue('SUPPLIER_DISPLAY_FORM', false);

		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;

		/* remove the configuration variable */
		$result = Configuration::deleteByName('SUPPLIER_DISPLAY_TEXT');
		$result &= Configuration::deleteByName('SUPPLIER_DISPLAY_TEXT_NB');
		$result &= Configuration::deleteByName('SUPPLIER_DISPLAY_FORM');

		return $result;
	}
	
	function hookDisplayLeftColumn($params)
	{
		$id_lang = (int)Context::getContext()->language->id;
		if (!$this->isCached('blocksupplier.tpl', $this->getCacheId()))
			$this->smarty->assign(array(
				'suppliers' => Supplier::getSuppliers(false, $id_lang),
				'link' => $this->context->link,
				'text_list' => Configuration::get('SUPPLIER_DISPLAY_TEXT'),
				'text_list_nb' => Configuration::get('SUPPLIER_DISPLAY_TEXT_NB'),
				'form_list' => Configuration::get('SUPPLIER_DISPLAY_FORM'),
				'display_link_supplier' => Configuration::get('PS_DISPLAY_SUPPLIERS')
			));
		return $this->display(__FILE__, 'blocksupplier.tpl', $this->getCacheId());
	}

	function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBlockSuppliers'))
		{
			$text_list = (int)(Tools::getValue('SUPPLIER_DISPLAY_TEXT'));
			$text_nb = (int)(Tools::getValue('SUPPLIER_DISPLAY_TEXT_NB'));
			$form_list = (int)(Tools::getValue('SUPPLIER_DISPLAY_FORM'));
			if ($text_list AND !Validate::isUnsignedInt($text_nb))
				$errors[] = $this->l('Invalid number of elements.');
			elseif (!$text_list AND !$form_list)
				$errors[] = $this->l('Please activate at least one type of list.');
			else
			{
				Configuration::updateValue('SUPPLIER_DISPLAY_TEXT', $text_list);
				Configuration::updateValue('SUPPLIER_DISPLAY_TEXT_NB', $text_nb);
				Configuration::updateValue('SUPPLIER_DISPLAY_FORM', $form_list);
				$this->_clearCache('blocksupplier.tpl');
			}
			if (isset($errors) AND sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated.'));
		}
		return $output.$this->renderForm();
	}

	public function hookDisplayRightColumn($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blocksupplier.css', 'all');
	}
	
	public function hookActionObjectSupplierUpdateAfter($params)
	{
		$this->_clearCache('blocksupplier.tpl');
	}

	public function hookActionObjectSupplierAddAfter($params)
	{
		$this->_clearCache('blocksupplier.tpl');
	}

	public function hookActionObjectSupplierDeleteAfter($params)
	{
		$this->_clearCache('blocksupplier.tpl');
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
						'type' => 'switch',
						'label' => $this->l('Use a plain-text list'),
						'name' => 'SUPPLIER_DISPLAY_TEXT',
						'desc' => $this->l('Display suppliers in a plain-text list.'),
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Enabled')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Disabled')
									)
								),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Number of elements to display'),
						'name' => 'SUPPLIER_DISPLAY_TEXT_NB',
						'class' => 'fixed-width-xs'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Use a drop-down list'),
						'name' => 'SUPPLIER_DISPLAY_FORM',
						'desc' => $this->l('Display suppliers in a drop-down list.'),
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Enabled')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Disabled')
									)
								),
					)
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
		$helper->submit_action = 'submitBlockSuppliers';
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
			'SUPPLIER_DISPLAY_TEXT' => Tools::getValue('SUPPLIER_DISPLAY_TEXT', Configuration::get('SUPPLIER_DISPLAY_TEXT')),
			'SUPPLIER_DISPLAY_TEXT_NB' => Tools::getValue('SUPPLIER_DISPLAY_TEXT_NB', Configuration::get('SUPPLIER_DISPLAY_TEXT_NB')),
			'SUPPLIER_DISPLAY_FORM' => Tools::getValue('SUPPLIER_DISPLAY_FORM', Configuration::get('SUPPLIER_DISPLAY_FORM')),
		);
	}
}
