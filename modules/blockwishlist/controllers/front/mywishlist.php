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

/**
 * @since 1.5.0
 */
class BlockWishListMyWishListModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
		include_once($this->module->getLocalPath().'WishList.php');
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
		$action = Tools::getValue('action');

		if (!Tools::isSubmit('myajax'))
			$this->assign();
		elseif (!empty($action) && method_exists($this, 'ajaxProcess'.Tools::toCamelCase($action)))
			$this->{'ajaxProcess'.Tools::toCamelCase($action)}();
		else
			die(json_encode(array('error' => 'method doesn\'t exist')));
	}

	/**
	 * Assign wishlist template
	 */
	public function assign()
	{
		$errors = array();

		if ($this->context->customer->isLogged())
		{
			$add = Tools::getIsset('add');
			$add = (empty($add) === false ? 1 : 0);
			$delete = Tools::getIsset('deleted');
			$delete = (empty($delete) === false ? 1 : 0);
			$default = Tools::getIsset('default');
			$default = (empty($default) === false ? 1 : 0);
			$id_wishlist = Tools::getValue('id_wishlist');
			if (Tools::isSubmit('submitWishlist'))
			{
				if (Configuration::get('PS_TOKEN_ACTIVATED') == 1 && strcmp(Tools::getToken(), Tools::getValue('token')))
					$errors[] = $this->module->l('Invalid token', 'mywishlist');
				if (!count($errors))
				{
					$name = Tools::getValue('name');
					if (empty($name))
						$errors[] = $this->module->l('You must specify a name.', 'mywishlist');
					if (WishList::isExistsByNameForUser($name))
						$errors[] = $this->module->l('This name is already used by another list.', 'mywishlist');

					if (!count($errors))
					{
						$wishlist = new WishList();
						$wishlist->id_shop = $this->context->shop->id;
						$wishlist->id_shop_group = $this->context->shop->id_shop_group;
						$wishlist->name = $name;
						$wishlist->id_customer = (int)$this->context->customer->id;
						!$wishlist->isDefault($wishlist->id_customer) ? $wishlist->default = 1 : '';
						list($us, $s) = explode(' ', microtime());
						srand($s * $us);
						$wishlist->token = strtoupper(substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$this->context->customer->id), 0, 16));
						$wishlist->add();
						Mail::Send(
							$this->context->language->id,
							'wishlink',
							Mail::l('Your wishlist\'s link', $this->context->language->id),
							array(
							'{wishlist}' => $wishlist->name,
							'{message}' => $this->context->link->getModuleLink('blockwishlist', 'view', array('token' => $wishlist->token))
							),
							$this->context->customer->email,
							$this->context->customer->firstname.' '.$this->context->customer->lastname,
							null,
							strval(Configuration::get('PS_SHOP_NAME')),
							null,
							null,
							$this->module->getLocalPath().'mails/');

						Tools::redirect($this->context->link->getModuleLink('blockwishlist', 'mywishlist'));
					}
				}
			}
			else if ($add)
				WishList::addCardToWishlist($this->context->customer->id, Tools::getValue('id_wishlist'), $this->context->language->id);
			elseif ($delete && empty($id_wishlist) === false)
			{
				$wishlist = new WishList((int)$id_wishlist);
				if ($this->context->customer->isLogged() && $this->context->customer->id == $wishlist->id_customer && Validate::isLoadedObject($wishlist))
					$wishlist->delete();
				else
					$errors[] = $this->module->l('Cannot delete this wishlist', 'mywishlist');
			}
			elseif ($default)
			{
				$wishlist = new WishList((int)$id_wishlist);
				if ($this->context->customer->isLogged() && $this->context->customer->id == $wishlist->id_customer && Validate::isLoadedObject($wishlist))
					$wishlist->setDefault();
				else
					$errors[] = $this->module->l('Cannot delete this wishlist', 'mywishlist');
			}
			$this->context->smarty->assign('wishlists', WishList::getByIdCustomer($this->context->customer->id));
			$this->context->smarty->assign('nbProducts', WishList::getInfosByIdCustomer($this->context->customer->id));
		}
		else
			Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('blockwishlist', 'mywishlist')));

		$this->context->smarty->assign(array(
			'id_customer' => (int)$this->context->customer->id,
			'errors' => $errors,
			'form_link' => $errors,
		));

		$this->setTemplate('mywishlist.tpl');
	}

	public function ajaxProcessDeleteList()
	{
		if (!$this->context->customer->isLogged())
			die(json_encode(array('success' => false,
				'error' => $this->module->l('You aren\'t logged in', 'mywishlist'))));

		$default = Tools::getIsset('default');
		$default = (empty($default) === false ? 1 : 0);
		$id_wishlist = Tools::getValue('id_wishlist');

		$wishlist = new WishList((int)$id_wishlist);
		if (Validate::isLoadedObject($wishlist) && $wishlist->id_customer == $this->context->customer->id)
		{
			$default_change = $wishlist->default ? true : false;
			$id_customer = $wishlist->id_customer;
			$wishlist->delete();
		}
		else
			die(json_encode(array('success' => false,
				'error' => $this->module->l('Cannot delete this wishlist', 'mywishlist'))));

		if ($default_change)
		{
			$array = WishList::getDefault($id_customer);

			if (count($array))
				die(json_encode(array(
					'success' => true,
					'id_default' => $array[0]['id_wishlist']
					)));
		}

		die(json_encode(array('success' => true)));
	}

	public function ajaxProcessSetDefault()
	{
		if (!$this->context->customer->isLogged())
			die(json_encode(array('success' => false,
				'error' => $this->module->l('You aren\'t logged in', 'mywishlist'))));

		$default = Tools::getIsset('default');
		$default = (empty($default) === false ? 1 : 0);
		$id_wishlist = Tools::getValue('id_wishlist');

		if ($default)
		{
			$wishlist = new WishList((int)$id_wishlist);
			if (Validate::isLoadedObject($wishlist) && $wishlist->id_customer == $this->context->customer->id && $wishlist->setDefault())
				die(json_encode(array('success' => true)));
		}

		die(json_encode(array('error' => true)));
	}

	public function ajaxProcessProductChangeWishlist()
	{
		if (!$this->context->customer->isLogged())
			die(json_encode(array('success' => false,
				'error' => $this->module->l('You aren\'t logged in', 'mywishlist'))));

		$id_product = (int)Tools::getValue('id_product');
		$id_product_attribute = (int)Tools::getValue('id_product_attribute');
		$quantity = (int)Tools::getValue('quantity');
		$priority = (int)Tools::getValue('priority');
		$id_old_wishlist = (int)Tools::getValue('id_old_wishlist');
		$id_new_wishlist = (int)Tools::getValue('id_new_wishlist');
		$new_wishlist = new WishList((int)$id_new_wishlist);
		$old_wishlist = new WishList((int)$id_old_wishlist);

		//check the data is ok
		if (!$id_product || !is_int($id_product_attribute) || !$quantity ||
			!is_int($priority) || ($priority < 0 && $priority > 2) || !$id_old_wishlist || !$id_new_wishlist ||
			(Validate::isLoadedObject($new_wishlist) && $new_wishlist->id_customer != $this->context->customer->id) ||
			(Validate::isLoadedObject($old_wishlist) && $old_wishlist->id_customer != $this->context->customer->id))
			die(json_encode(array('success' => false, 'error' => $this->module->l('Error while moving product to another list', 'mywishlist'))));

		$res = true;
		$check = (int)Db::getInstance()->getValue('SELECT quantity FROM '._DB_PREFIX_.'wishlist_product
			WHERE `id_product` = '.$id_product.' AND `id_product_attribute` = '.$id_product_attribute.' AND `id_wishlist` = '.$id_new_wishlist);

		if ($check)
		{
			$res &= $old_wishlist->removeProduct($id_old_wishlist, $this->context->customer->id, $id_product, $id_product_attribute);
			$res &= $new_wishlist->updateProduct($id_new_wishlist, $id_product, $id_product_attribute, $priority, $quantity + $check);
		}
		else
		{
			$res &= $old_wishlist->removeProduct($id_old_wishlist, $this->context->customer->id, $id_product, $id_product_attribute);
			$res &= $new_wishlist->addProduct($id_new_wishlist, $this->context->customer->id, $id_product, $id_product_attribute, $quantity);
		}

		if (!$res)
			die(json_encode(array('success' => false, 'error' => $this->module->l('Error while moving product to another list', 'mywishlist'))));
		die(json_encode(array('success' => true, 'msg' => $this->module->l('The product has been correctly moved', 'mywishlist'))));
	}
}
