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

class ProductsCategory extends Module
{
	protected $html;

	public function __construct()
	{
		$this->name = 'productscategory';
		$this->version = '1.8.1';
		$this->author = 'PrestaShop';
		$this->tab = 'front_office_features';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Products in the same category');
		$this->description = $this->l('Adds a block on the product page that displays products from the same category.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
	}

	public function install()
	{
		Configuration::updateValue('PRODUCTSCATEGORY_DISPLAY_PRICE', 0);
		$this->_clearCache('productscategory.tpl');

		return (parent::install()
			&& $this->registerHook('productfooter')
			&& $this->registerHook('header')
			&& $this->registerHook('addproduct')
			&& $this->registerHook('updateproduct')
			&& $this->registerHook('deleteproduct')
		);
	}

	public function uninstall()
	{
		Configuration::deleteByName('PRODUCTSCATEGORY_DISPLAY_PRICE');
		$this->_clearCache('productscategory.tpl');

		return parent::uninstall();
	}

	public function getContent()
	{
		$this->html = '';
		if (Tools::isSubmit('submitCross') &&
			Tools::getValue('PRODUCTSCATEGORY_DISPLAY_PRICE') != 0 &&
			Tools::getValue('PRODUCTSCATEGORY_DISPLAY_PRICE') != 1
		)
			$this->html .= $this->displayError('Invalid displayPrice.');
		elseif (Tools::isSubmit('submitCross'))
		{
			Configuration::updateValue(
				'PRODUCTSCATEGORY_DISPLAY_PRICE',
				Tools::getValue('PRODUCTSCATEGORY_DISPLAY_PRICE')
			);
			$this->_clearCache('productscategory.tpl');
			$this->html .= $this->displayConfirmation($this->l('Settings updated successfully.'));
		}
		$this->html .= $this->renderForm();

		return $this->html;
	}

	protected function getCurrentProduct($products, $id_current)
	{
		if ($products)
		{
			foreach ($products as $key => $product)
			{
				if ($product['id_product'] == $id_current)
					return $key;
			}
		}

		return false;
	}

	public function hookProductFooter($params)
	{
		$id_product = (int)$params['product']->id;
		$product = $params['product'];

		$cache_id = 'productscategory|'.$id_product.'|'.(isset($params['category']->id_category) ? (int)$params['category']->id_category : (int)$product->id_category_default);

		if (!$this->isCached('productscategory.tpl', $this->getCacheId($cache_id)))
		{

			$category = false;
			if (isset($params['category']->id_category))
				$category = $params['category'];
			else
			{
				if (isset($product->id_category_default) && $product->id_category_default > 1)
					$category = new Category((int)$product->id_category_default);
			}

			if (!Validate::isLoadedObject($category) || !$category->active)
				return false;

			// Get infos
			$category_products = $category->getProducts($this->context->language->id, 1, 100); /* 100 products max. */
			$nb_category_products = (int)count($category_products);
			$middle_position = 0;

			// Remove current product from the list
			if (is_array($category_products) && count($category_products))
			{
				foreach ($category_products as $key => $category_product)
				{
					if ($category_product['id_product'] == $id_product)
					{
						unset($category_products[$key]);
						break;
					}
				}

				$taxes = Product::getTaxCalculationMethod();
				if (Configuration::get('PRODUCTSCATEGORY_DISPLAY_PRICE'))
				{
					foreach ($category_products as $key => $category_product)
					{
						if ($category_product['id_product'] != $id_product)
						{
							if ($taxes == 0 || $taxes == 2)
							{
								$category_products[$key]['displayed_price'] = Product::getPriceStatic(
									(int)$category_product['id_product'],
									true,
									null,
									2
								);
							} elseif ($taxes == 1)
							{
								$category_products[$key]['displayed_price'] = Product::getPriceStatic(
									(int)$category_product['id_product'],
									false,
									null,
									2
								);
							}
						}
					}
				}

				// Get positions
				$middle_position = (int)round($nb_category_products / 2, 0);
				$product_position = $this->getCurrentProduct($category_products, (int)$id_product);

				// Flip middle product with current product
				if ($product_position)
				{
					$tmp = $category_products[$middle_position - 1];
					$category_products[$middle_position - 1] = $category_products[$product_position];
					$category_products[$product_position] = $tmp;
				}

				// If products tab higher than 30, slice it
				if ($nb_category_products > 30)
				{
					$category_products = array_slice($category_products, $middle_position - 15, 30, true);
					$middle_position = 15;
				}
			}

			// Display tpl
			$this->smarty->assign(
				array(
					'categoryProducts' => $category_products,
					'middlePosition' => (int)$middle_position,
					'ProdDisplayPrice' => Configuration::get('PRODUCTSCATEGORY_DISPLAY_PRICE')
				)
			);
		}

		return $this->display(__FILE__, 'productscategory.tpl', $this->getCacheId($cache_id));
	}

	public function hookHeader($params)
	{
		if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'product')
			return;
		$this->context->controller->addCSS($this->_path.'css/productscategory.css', 'all');
		$this->context->controller->addJS($this->_path.'js/productscategory.js');
		$this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll', 'bxslider'));
	}

	public function hookAddProduct($params)
	{
		if (!isset($params['product']))
			return;
		$id_product = (int)$params['product']->id;
		$product = $params['product'];

		$cache_id = 'productscategory|'.$id_product.'|'.(isset($params['category']->id_category) ? (int)$params['category']->id_category : (int)$product->id_category_default);
		$this->_clearCache('productscategory.tpl', $this->getCacheId($cache_id));
	}

	public function hookUpdateProduct($params)
	{
		if (!isset($params['product']))
			return;
		$id_product = (int)$params['product']->id;
		$product = $params['product'];

		$cache_id = 'productscategory|'.$id_product.'|'.(isset($params['category']->id_category) ? (int)$params['category']->id_category : (int)$product->id_category_default);
		$this->_clearCache('productscategory.tpl', $this->getCacheId($cache_id));
	}

	public function hookDeleteProduct($params)
	{
		if (!isset($params['product']))
			return;
		$id_product = (int)$params['product']->id;
		$product = $params['product'];

		$cache_id = 'productscategory|'.$id_product.'|'.(isset($params['category']->id_category) ? (int)$params['category']->id_category : (int)$product->id_category_default);
		$this->_clearCache('productscategory.tpl', $this->getCacheId($cache_id));
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
						'label' => $this->l('Display products\' prices'),
						'desc' => $this->l('Show the prices of the products displayed in the block.'),
						'name' => 'PRODUCTSCATEGORY_DISPLAY_PRICE',
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
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get(
			'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
		) : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitCross';
		$helper->currentIndex = $this->context->link->getAdminLink(
				'AdminModules',
				false
			).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
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
			'PRODUCTSCATEGORY_DISPLAY_PRICE' => Tools::getValue(
					'PRODUCTSCATEGORY_DISPLAY_PRICE',
					Configuration::get('PRODUCTSCATEGORY_DISPLAY_PRICE')
				),
		);
	}

}
