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

include_once (dirname(__FILE__).'/classes/BlockWishlistDb.php');
include_once (dirname(__FILE__).'/WishList.php');

class BlockWishList extends Module
{
	private $html = '';

	public function __construct()
	{
		$this->name = 'blockwishlist';
		$this->tab = 'front_office_features';
		$this->version = '1.3.3';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->controllers = array('mywishlist', 'view');

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Wishlist block');
		$this->description = $this->l('Adds a block containing the customer\'s wishlists.');
		$this->default_wishlist_name = $this->l('My wishlist');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->html = '';
	}

	public function install($delete_params = true)
	{
		if ($delete_params) {
			$objBlockWishListDb = new BlockWishlistDb();
			if (!$objBlockWishListDb->createTables()) {
				return false;
			}
		}

		if (!parent::install() ||
			!$this->registerHook('rightColumn') ||
			!$this->registerHook('productActions') ||
			!$this->registerHook('cart') ||
			!$this->registerHook('customerAccount') ||
			!$this->registerHook('header') ||
			!$this->registerHook('adminCustomers') ||
			!$this->registerHook('displayProductListFunctionalButtons') ||
			!$this->registerHook('top'))
			return false;
		/* This hook is optional */
		$this->registerHook('displayMyAccountBlock');

		return true;
	}

	public function uninstall($delete_params = true)
	{
		$objBlockWishListDb = new BlockWishlistDb();
		if (($delete_params && !$objBlockWishListDb->dropTables()) || !parent::uninstall()) {
			return false;
		}

		return true;
	}

	private function deleteTables()
	{
		return Db::getInstance()->execute(
			'DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'wishlist`,
			`'._DB_PREFIX_.'wishlist_email`,
			`'._DB_PREFIX_.'wishlist_product`,
			`'._DB_PREFIX_.'wishlist_product_cart`'
		);
	}

	public function reset()
	{
		if (!$this->uninstall(false))
			return false;
		if (!$this->install(false))
			return false;

		return true;
	}

	public function getContent()
	{
		//$this->context->link->getProductLink($val['id_product']);

		if (Tools::isSubmit('viewblockwishlist') && $id = Tools::getValue('id_product'))
			Tools::redirect($this->context->link->getProductLink($id));
		elseif (Tools::isSubmit('submitSettings'))
		{
			$activated = Tools::getValue('activated');
			if ($activated != 0 && $activated != 1)
				$this->html .= '<div class="alert error alert-danger">'.$this->l('Activate module : Invalid choice.').'</div>';
			$this->html .= '<div class="conf confirm alert alert-success">'.$this->l('Settings updated').'</div>';
		}

		$this->html .= $this->renderJS();
		$this->html .= $this->renderForm();
		if (Tools::getValue('id_customer') && Tools::getValue('id_wishlist'))
			$this->html .= $this->renderList((int)Tools::getValue('id_wishlist'));

		return $this->html;
	}

	public function hookDisplayProductListFunctionalButtons($params)
	{
		//TODO : Add cache
		if ($this->context->customer->isLogged())
			$this->smarty->assign('wishlists', Wishlist::getByIdCustomer($this->context->customer->id));

		$this->smarty->assign('product', $params['product']);
		return $this->display(__FILE__, 'blockwishlist_button.tpl');
	}

	public function hookTop($params)
	{
		if ($this->context->customer->isLogged())
		{
			$wishlists = Wishlist::getByIdCustomer($this->context->customer->id);
			if (empty($this->context->cookie->id_wishlist) === true ||
				WishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false)
			{
				if (!count($wishlists))
					$id_wishlist = false;
				else
				{
					$id_wishlist = (int)$wishlists[0]['id_wishlist'];
					$this->context->cookie->id_wishlist = (int)$id_wishlist;
				}
			}
			else
				$id_wishlist = $this->context->cookie->id_wishlist;

			$this->smarty->assign(
				array(
					'id_wishlist' => $id_wishlist,
					'isLogged' => true,
					'wishlist_products' => ($id_wishlist == false ? false : WishList::getProductByIdCustomer($id_wishlist,
						$this->context->customer->id, $this->context->language->id, null, true)),
					'wishlists' => $wishlists,
					'ptoken' => Tools::getToken(false)
				)
			);
		}
		else
			$this->smarty->assign(array('wishlist_products' => false, 'wishlists' => false));

		return $this->display(__FILE__, 'blockwishlist_top.tpl');
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockwishlist.css', 'all');
		$this->context->controller->addJS(($this->_path).'js/ajax-wishlist.js');

		$this->smarty->assign(array('wishlist_link' => $this->context->link->getModuleLink('blockwishlist', 'mywishlist')));
	}

	public function hookRightColumn($params)
	{
		if ($this->context->customer->isLogged())
		{
			$wishlists = Wishlist::getByIdCustomer($this->context->customer->id);
			if (empty($this->context->cookie->id_wishlist) === true ||
				WishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false)
			{
				if (!count($wishlists))
					$id_wishlist = false;
				else
				{
					$id_wishlist = (int)$wishlists[0]['id_wishlist'];
					$this->context->cookie->id_wishlist = (int)$id_wishlist;
				}
			}
			else
				$id_wishlist = $this->context->cookie->id_wishlist;
			$this->smarty->assign(
				array(
					'id_wishlist' => $id_wishlist,
					'isLogged' => true,
					'wishlist_products' => ($id_wishlist == false ? false : WishList::getProductByIdCustomer($id_wishlist,
						$this->context->customer->id, $this->context->language->id, null, true)),
					'wishlists' => $wishlists,
					'ptoken' => Tools::getToken(false)
				)
			);
		}
		else
			$this->smarty->assign(array('wishlist_products' => false, 'wishlists' => false));

		return ($this->display(__FILE__, 'blockwishlist.tpl'));
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookProductActions($params)
	{
		$cookie = $params['cookie'];

		$this->smarty->assign(array(
			'id_product' => (int)Tools::getValue('id_product'),
		));

		if (isset($cookie->id_customer))
			$this->smarty->assign(array(
				'wishlists' => WishList::getByIdCustomer($cookie->id_customer),
			));

		return ($this->display(__FILE__, 'blockwishlist-extra.tpl'));
	}

	public function hookCustomerAccount($params)
	{
		return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayMyAccountBlock($params)
	{
		return $this->hookCustomerAccount($params);
	}

	private function _displayProducts($id_wishlist)
	{
		include_once(dirname(__FILE__).'/WishList.php');

		$wishlist = new WishList($id_wishlist);
		$products = WishList::getProductByIdCustomer($id_wishlist, $wishlist->id_customer, $this->context->language->id);
		$nb_products = count($products);
		for ($i = 0; $i < $nb_products; ++$i)
		{
			$obj = new Product((int)$products[$i]['id_product'], false, $this->context->language->id);
			if (!Validate::isLoadedObject($obj))
				continue;
			else
			{
				$images = $obj->getImages($this->context->language->id);
				foreach ($images as $image)
				{
					if ($image['cover'])
					{
						$products[$i]['cover'] = $obj->id.'-'.$image['id_image'];
						break;
					}
				}
				if (!isset($products[$i]['cover']))
					$products[$i]['cover'] = $this->context->language->iso_code.'-default';
			}
		}
		$this->html .= '
		<table class="table">
			<thead>
				<tr>
					<th class="first_item" style="width:600px;">'.$this->l('Product').'</th>
					<th class="item" style="text-align:center;width:150px;">'.$this->l('Quantity').'</th>
					<th class="item" style="text-align:center;width:150px;">'.$this->l('Priority').'</th>
				</tr>
			</thead>
			<tbody>';
		$priority = array($this->l('High'), $this->l('Medium'), $this->l('Low'));
		foreach ($products as $product)
		{
			$this->html .= '
				<tr>
					<td class="first_item">
						<img src="'.$this->context->link->getImageLink($product['link_rewrite'], $product['cover'],
							ImageType::getFormatedName('small')).'" alt="'.htmlentities($product['name'], ENT_COMPAT, 'UTF-8').'" style="float:left;" />
						'.$product['name'];
			if (isset($product['attributes_small']))
				$this->html .= '<br /><i>'.htmlentities($product['attributes_small'], ENT_COMPAT, 'UTF-8').'</i>';
			$this->html .= '
					</td>
					<td class="item" style="text-align:center;">'.(int)$product['quantity'].'</td>
					<td class="item" style="text-align:center;">'.$priority[(int)$product['priority'] % 3].'</td>
				</tr>';
		}
		$this->html .= '</tbody></table>';
	}

	public function hookAdminCustomers($params)
	{
		$customer = new Customer((int)$params['id_customer']);
		if (!Validate::isLoadedObject($customer))
			die (Tools::displayError());

		$this->html = '<h2>'.$this->l('Wishlists').'</h2>';

		$wishlists = WishList::getByIdCustomer((int)$customer->id);
		if (!count($wishlists))
			$this->html .= $customer->lastname.' '.$customer->firstname.' '.$this->l('No wishlist.');
		else
		{
			$this->html .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" id="listing">';

			$id_wishlist = (int)Tools::getValue('id_wishlist');
			if (!$id_wishlist)
				$id_wishlist = $wishlists[0]['id_wishlist'];

			$this->html .= '<span>'.$this->l('Wishlist').': </span> <select name="id_wishlist" onchange="$(\'#listing\').submit();">';

			if (is_array($wishlists))
				foreach ($wishlists as $wishlist)
				{
					$this->html .= '<option value="'.(int)$wishlist['id_wishlist'].'"';
					if ($wishlist['id_wishlist'] == $id_wishlist)
					{
						$this->html .= ' selected="selected"';
						$counter = $wishlist['counter'];
					}
					$this->html .= '>'.htmlentities($wishlist['name'], ENT_COMPAT, 'UTF-8').'</option>';
				}
			$this->html .= '</select>';

			$this->_displayProducts((int)$id_wishlist);

			$this->html .= '</form><br />';

			return $this->html;
		}

	}

	/*
	* Display Error from controler
	*/
	public function errorLogged()
	{
		return $this->l('You must be logged in to manage your wishlists.');
	}

	public function renderJS()
	{
		return "<script>
			$(document).ready(function () { $('#id_customer, #id_wishlist').change( function () { $('#module_form').submit();}); });
		</script>";
	}

	public function renderForm()
	{
		$_customers = WishList::getCustomers();

        	foreach ($_customers as $c)
        	{
            		$_customers[$c['id_customer']]['id_customer'] = $c['id_customer'];
            		$_customers[$c['id_customer']]['name'] = $c['firstname'].' '.$c['lastname'];
        	}

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Listing'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Customers :'),
						'name' => 'id_customer',
						'options' => array(
							'default' => array('value' => 0, 'label' => $this->l('Choose customer')),
							'query' => $_customers,
							'id' => 'id_customer',
							'name' => 'name'
						),
					)
				),
			),
		);

		if ($id_customer = Tools::getValue('id_customer'))
		{
			$wishlists = WishList::getByIdCustomer($id_customer);
			$fields_form['form']['input'][] = array(
				'type' => 'select',
				'label' => $this->l('Wishlist :'),
				'name' => 'id_wishlist',
				'options' => array(
					'default' => array('value' => 0, 'label' => $this->l('Choose wishlist')),
					'query' => $wishlists,
					'id' => 'id_wishlist',
					'name' => 'name'
				),
			);
		}

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name
			.'&tab_module='.$this->tab.'&module_name='.$this->name;
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
			'id_customer' => Tools::getValue('id_customer'),
			'id_wishlist' => Tools::getValue('id_wishlist'),
		);
	}

	public function renderList($id_wishlist)
	{
		$wishlist = new WishList($id_wishlist);
		$products = WishList::getProductByIdCustomer($id_wishlist, $wishlist->id_customer, $this->context->language->id);

		foreach ($products as $key => $val)
		{
			$image = Image::getCover($val['id_product']);
			$products[$key]['image'] = $this->context->link->getImageLink($val['link_rewrite'], $image['id_image'], ImageType::getFormatedName('small'));
		}

		$fields_list = array(
			'image' => array(
				'title' => $this->l('Image'),
				'type' => 'image',
			),
			'name' => array(
				'title' => $this->l('Product'),
				'type' => 'text',
			),
			'attributes_small' => array(
				'title' => $this->l('Combination'),
				'type' => 'text',
			),
			'quantity' => array(
				'title' => $this->l('Quantity'),
				'type' => 'text',
			),
			'priority' => array(
				'title' => $this->l('Priority'),
				'type' => 'priority',
				'values' => array($this->l('High'), $this->l('Medium'), $this->l('Low')),
			),
		);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->no_link = true;
		$helper->actions = array('view');
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->identifier = 'id_product';
		$helper->title = $this->l('Product list');
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->tpl_vars = array('priority' => array($this->l('High'), $this->l('Medium'), $this->l('Low')));

		return $helper->generateList($products, $fields_list);
	}
}
