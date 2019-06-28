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

class BlockSpecials extends Module
{
	protected $_html = '';
	protected $_postErrors = array();

	protected static $cache_specials;

	public function __construct()
	{
		$this->name = 'blockspecials';
		$this->tab = 'pricing_promotion';
		$this->version = '1.3.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Specials block');
		$this->description = $this->l('Adds a block displaying your current discounted products.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
	}

	public function install()
	{
		if (!Configuration::get('BLOCKSPECIALS_NB_CACHES'))
			Configuration::updateValue('BLOCKSPECIALS_NB_CACHES', 20);

		if (!Configuration::get('BLOCKSPECIALS_SPECIALS_NBR'))
			Configuration::updateValue('BLOCKSPECIALS_SPECIALS_NBR', 5);

		$this->_clearCache('*');

		$success = parent::install()
			&& $this->registerHook('header')
			&& $this->registerHook('leftColumn')
			&& $this->registerHook('addproduct')
			&& $this->registerHook('updateproduct')
			&& $this->registerHook('deleteproduct')
			&& $this->registerHook('displayHomeTab')
			&& $this->registerHook('displayHomeTabContent');

		return $success;
	}

	public function uninstall()
	{
		$this->_clearCache('*');
		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitSpecials'))
		{
			Configuration::updateValue('PS_BLOCK_SPECIALS_DISPLAY', (int)Tools::getValue('PS_BLOCK_SPECIALS_DISPLAY'));
			Configuration::updateValue('BLOCKSPECIALS_NB_CACHES', (int)Tools::getValue('BLOCKSPECIALS_NB_CACHES'));
			Configuration::updateValue('BLOCKSPECIALS_SPECIALS_NBR', (int)Tools::getValue('BLOCKSPECIALS_SPECIALS_NBR'));
			$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->renderForm();
	}

	public function hookRightColumn($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;

		// We need to create multiple caches because the products are sorted randomly
		$random = date('Ymd').'|'.round(rand(1, max(Configuration::get('BLOCKSPECIALS_NB_CACHES'), 1)));

		if (!Configuration::get('BLOCKSPECIALS_NB_CACHES') || !$this->isCached('blockspecials.tpl', $this->getCacheId('blockspecials|'.$random)))
		{
			if (!($special = Product::getRandomSpecial((int)$params['cookie']->id_lang)) && !Configuration::get('PS_BLOCK_SPECIALS_DISPLAY'))
				return;

			$this->smarty->assign(array(
				'special' => $special,
				'priceWithoutReduction_tax_excl' => Tools::ps_round($special['price_without_reduction'], 2),
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			));
		}

		return $this->display(__FILE__, 'blockspecials.tpl', (Configuration::get('BLOCKSPECIALS_NB_CACHES') ? $this->getCacheId('blockspecials|'.$random) : null));
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		$this->context->controller->addCSS(($this->_path).'blockspecials.css', 'all');
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookDisplayHomeTab($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;

		if (!$this->isCached('tab.tpl', $this->getCacheId('blockspecials-tab')))
			BlockSpecials::$cache_specials = Product::getPricesDrop((int)$params['cookie']->id_lang, 0, Configuration::get('BLOCKSPECIALS_SPECIALS_NBR'));

		if (BlockSpecials::$cache_specials === false)
			return false;

		return $this->display(__FILE__, 'tab.tpl', $this->getCacheId('blockspecials-tab'));
	}

	public function hookDisplayHomeTabContent($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;

		if (!$this->isCached('blockspecials-home.tpl', $this->getCacheId('blockspecials-home')))
		{
			$this->smarty->assign(array(
				'specials' => BlockSpecials::$cache_specials,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
			));
		}

		if (BlockSpecials::$cache_specials === false)
			return false;

		return $this->display(__FILE__, 'blockspecials-home.tpl', $this->getCacheId('blockspecials-home'));
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
						'label' => $this->l('Always display this block'),
						'name' => 'PS_BLOCK_SPECIALS_DISPLAY',
						'desc' => $this->l('Show the block even if no products are available.'),
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
						'label' => $this->l('Number of cached files'),
						'name' => 'BLOCKSPECIALS_NB_CACHES',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Specials are displayed randomly on the front-end, but since it takes a lot of ressources, it is better to cache the results. The cache is reset daily. 0 will disable the cache.'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Products to display'),
						'name' => 'BLOCKSPECIALS_SPECIALS_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Define the number of products to be displayed in this block on home page.')
					)
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
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitSpecials';
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
			'PS_BLOCK_SPECIALS_DISPLAY' => Tools::getValue('PS_BLOCK_SPECIALS_DISPLAY', Configuration::get('PS_BLOCK_SPECIALS_DISPLAY')),
			'BLOCKSPECIALS_NB_CACHES' => Tools::getValue('BLOCKSPECIALS_NB_CACHES', Configuration::get('BLOCKSPECIALS_NB_CACHES')),
			'BLOCKSPECIALS_SPECIALS_NBR' => Tools::getValue('BLOCKSPECIALS_SPECIALS_NBR', Configuration::get('BLOCKSPECIALS_SPECIALS_NBR'))
		);
	}

	protected function getCacheId($name = null)
	{
		if ($name === null)
		$name = 'blockspecials';
		return parent::getCacheId($name.'|'.date('Ymd'));
	}

	public function _clearCache($template, $cache_id = null, $compile_id = null)
	{
		parent::_clearCache('blockspecials.tpl');
		parent::_clearCache('blockspecials-home.tpl', 'blockspecials-home');
		parent::_clearCache('tab.tpl', 'blockspecials-tab');
	}
}
