<?php
/**
* 2007-2015 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class SocialSharing extends Module
{
	protected static $networks = array('Facebook', 'Twitter', 'Google', 'Pinterest');
	protected $html = '';

	public function __construct()
	{
		$this->name = 'socialsharing';
		$this->author = 'PrestaShop';
		$this->tab = 'advertising_marketing';
		$this->need_instance = 0;
		$this->version = '1.3.1';
		$this->bootstrap = true;
		$this->_directory = dirname(__FILE__);

		parent::__construct();

		$this->displayName = $this->l('Social sharing');
		$this->description = $this->l('Displays social sharing buttons (Twitter, Facebook, Google+ and Pinterest) on every product page.');
	}

	public function install()
	{
		if (!parent::install())
			return false;

		// Activate every option by default
		Configuration::updateValue('PS_SC_TWITTER', 1);
		Configuration::updateValue('PS_SC_FACEBOOK', 1);
		Configuration::updateValue('PS_SC_GOOGLE', 1);
		Configuration::updateValue('PS_SC_PINTEREST', 1);

		// The module will add a meta in the product page header and add a javascript file
		$this->registerHook('header');

		// This hook could have been called only from the product page, but it's better to add the JS in all the pages with CCC
		/*
			$id_hook_header = Hook::getIdByName('header');
			$pages = array();
			foreach (Meta::getPages() as $page)
				if ($page != 'product')
					$pages[] = $page;
			$this->registerExceptions($id_hook_header, $pages);
		*/

		// The module need to clear the product page cache after update/delete
		$this->registerHook('actionObjectProductUpdateAfter');
		$this->registerHook('actionObjectProductDeleteAfter');

		// The module will then be hooked on the product and comparison pages
		$this->registerHook('displayRightColumnProduct');
		$this->registerHook('displayCompareExtraInformation');
		
		// The module will then be hooked and accessible with Smarty function
		$this->registerHook('displaySocialSharing');

		return true;
	}

	public function getConfigFieldsValues()
	{
		$values = array();
		foreach (self::$networks as $network)
			$values['PS_SC_'.Tools::strtoupper($network)] = (int)Tools::getValue('PS_SC_'.Tools::strtoupper($network), Configuration::get('PS_SC_'.Tools::strtoupper($network)));

		return $values;
	}

	public function getContent()
	{
		if (Tools::isSubmit('submitSocialSharing'))
		{
			foreach (self::$networks as $network)
				Configuration::updateValue('PS_SC_'.Tools::strtoupper($network), (int)Tools::getValue('PS_SC_'.Tools::strtoupper($network)));
			$this->html .= $this->displayConfirmation($this->l('Settings updated'));
			Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('socialsharing.tpl'));
			Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('socialsharing_compare.tpl'));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=6&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);

		}

		$helper = new HelperForm();
		$helper->submit_action = 'submitSocialSharing';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array('fields_value' => $this->getConfigFieldsValues());

		$fields = array();
		foreach (self::$networks as $network)
			$fields[] = array(
				'type' => 'switch',
				'label' => $network,
				'name' => 'PS_SC_'.Tools::strtoupper($network),
				'values' => array(
					array(
						'id' => Tools::strtolower($network).'_active_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => Tools::strtolower($network).'_active_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				)
			);

		return $this->html.$helper->generateForm(array(
			array(
				'form' => array(
					'legend' => array(
						'title' => $this->displayName,
						'icon' => 'icon-share'
					),
					'input' => $fields,
					'submit' => array(
						'title' => $this->l('Save')
					)
				)
			)
		));
	}

	public function hookDisplayHeader($params)
	{
		if (!isset($this->context->controller->php_self) || !in_array($this->context->controller->php_self, array('product', 'products-comparison')))
			return;

		$this->context->controller->addCss($this->_path.'css/socialsharing.css');
		$this->context->controller->addJS($this->_path.'js/socialsharing.js');

		// Exception are managed with Module::registerExceptions() but this is needed in case the merchant added new controllers afterwards
		if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'product')
			return;

		$product = $this->context->controller->getProduct();
		if (!$this->isCached('socialsharing_header.tpl', $this->getCacheId('socialsharing_header|'.(isset($product->id) && $product->id ? (int)$product->id : ''))))
		{
			$this->context->smarty->assign(array(
				'price' => Tools::ps_round($product->getPrice(!Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer), null), _PS_PRICE_COMPUTE_PRECISION_),
				'pretax_price' => Tools::ps_round($product->getPrice(false, null), _PS_PRICE_COMPUTE_PRECISION_),
				'weight' => $product->weight,
				'weight_unit' => Configuration::get('PS_WEIGHT_UNIT'),
				'cover' => isset($product->id) ? Product::getCover((int)$product->id) : '',
				'link_rewrite' => isset($product->link_rewrite) && $product->link_rewrite ? $product->link_rewrite : '',
			));
		}

		return $this->display(__FILE__, 'socialsharing_header.tpl', $this->getCacheId('socialsharing_header|'.(isset($product->id) && $product->id ? (int)$product->id : '')));
	}

	public function hookDisplaySocialSharing()
	{
		$product = $this->context->controller->getProduct();
		if (isset($product) && Validate::isLoadedObject($product))
		{
			$image_cover_id = $product->getCover($product->id);
			if (is_array($image_cover_id) && isset($image_cover_id['id_image']))
				$image_cover_id = (int)$image_cover_id['id_image'];
			else
				$image_cover_id = 0;

			Media::addJsDef(array(
				'sharing_name' => addcslashes($product->name, "'"),
				'sharing_url' => addcslashes($this->context->link->getProductLink($product), "'"),
				'sharing_img' => addcslashes($this->context->link->getImageLink($product->link_rewrite, $image_cover_id), "'")
			));
		}

		if (!$this->isCached('socialsharing.tpl', $this->getCacheId('socialsharing|'.(isset($product->id) && $product->id ? (int)$product->id : ''))))
		{
			$this->context->smarty->assign(array(
				'product' => isset($product) ? $product : '',
				'PS_SC_TWITTER' => Configuration::get('PS_SC_TWITTER'),
				'PS_SC_GOOGLE' => Configuration::get('PS_SC_GOOGLE'),
				'PS_SC_FACEBOOK' => Configuration::get('PS_SC_FACEBOOK'),
				'PS_SC_PINTEREST' => Configuration::get('PS_SC_PINTEREST')
			));
		}

		return $this->display(__FILE__, 'socialsharing.tpl', $this->getCacheId('socialsharing|'.(isset($product->id) && $product->id ? (int)$product->id : '')));
	}

	protected function clearProductHeaderCache($id_product)
	{
		return $this->_clearCache('socialsharing_header.tpl', 'socialsharing_header|'.(int)$id_product);
	}

	public function hookDisplayCompareExtraInformation($params)
	{
		Media::addJsDef(array(
			'sharing_name' => addcslashes($this->l('Product comparison'), "'"),
			'sharing_url' => addcslashes($this->context->link->getPageLink('products-comparison', null, $this->context->language->id,
			array('compare_product_list' => Tools::getValue('compare_product_list'))), "'"),
			'sharing_img' => addcslashes(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL', null, null, $this->context->shop->id), "'"
			)
		));

		if (!$this->isCached('socialsharing_compare.tpl', $this->getCacheId('socialsharing_compare')))
		{
			$this->context->smarty->assign(array(
				'PS_SC_TWITTER' => Configuration::get('PS_SC_TWITTER'),
				'PS_SC_GOOGLE' => Configuration::get('PS_SC_GOOGLE'),
				'PS_SC_FACEBOOK' => Configuration::get('PS_SC_FACEBOOK'),
				'PS_SC_PINTEREST' => Configuration::get('PS_SC_PINTEREST')
			));
		}

		return $this->display(__FILE__, 'socialsharing_compare.tpl', $this->getCacheId('socialsharing_compare'));
	}

	public function hookDisplayRightColumnProduct($params)
	{
		return $this->hookDisplaySocialSharing();
	}

	public function hookExtraleft($params)
	{
		return $this->hookDisplaySocialSharing();
	}

	public function hookProductActions($params)
	{
		return $this->hookDisplaySocialSharing();
	}

	public function hookProductFooter($params)
	{
		return $this->hookDisplaySocialSharing();
	}

	public function hookActionObjectProductUpdateAfter($params)
	{
		return $this->clearProductHeaderCache($params['object']->id);
	}

	public function hookActionObjectProductDeleteAfter($params)
	{
		return $this->clearProductHeaderCache($params['object']->id);
	}
}
