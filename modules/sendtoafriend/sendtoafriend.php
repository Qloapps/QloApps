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

class sendToAFriend extends Module
{
	private $_html = '';
	private $_postErrors = array();
	public $context;

	function __construct($dontTranslate = false)
 	{
 	 	$this->name = 'sendtoafriend';
		$this->version = '1.9.1';
		$this->author = 'PrestaShop';
 	 	$this->tab = 'front_office_features';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);

		parent::__construct();

		if (!$dontTranslate)
		{
			$this->displayName = $this->l('Send to a Friend module');
			$this->description = $this->l('Allows customers to send a product link to a friend.');
 		}
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
	}

	public function install()
	{
	 	return (parent::install() && $this->registerHook('extraLeft') && $this->registerHook('header'));
	}

	public function uninstall()
	{
		return (parent::uninstall() && $this->unregisterHook('header') && $this->unregisterHook('extraLeft'));
	}

	public function hookExtraLeft($params)
	{
		/* Product informations */
		$product = new Product((int)Tools::getValue('id_product'), false, $this->context->language->id);
		$image = Product::getCover((int)$product->id);


		$this->context->smarty->assign(array(
			'stf_product' => $product,
			'stf_product_cover' => (int)$product->id.'-'.(int)$image['id_image'],
			'stf_secure_key' => $this->secure_key
		));

		return $this->display(__FILE__, 'sendtoafriend-extra.tpl');
	}

	public function hookHeader($params)
	{
		$this->page_name = Dispatcher::getInstance()->getController();
		if ($this->page_name == 'product')
		{
			$this->context->controller->addCSS($this->_path.'sendtoafriend.css', 'all');
			$this->context->controller->addJS($this->_path.'sendtoafriend.js');
		}
	}

	public function isValidName($name)
	{
		$isName          = Validate::isName($name);
		$isShortName     = $this->isShortName($name);
		$isNameLikeAnUrl = $this->isNameLikeAnUrl($name);
		$isValidName     = $isName && $isShortName && !$isNameLikeAnUrl;

		return $isValidName;
	}

	public function isShortName($name)
	{
		$isShortName = (strlen($name) <= 50);

		return $isShortName;
	}

	public function isNameLikeAnUrl($name)
	{
		// THIS REGEX IS NOT MEANT TO FIND A VALID URL
		// the goal is to see if the given string for a Person Name is containing something similar to an url
		//
		// See all strings that i tested the regex against in https://regex101.com/r/yL7lU0/3
		//
		// Please fork the regex if you can improve it and make a Pull Request
		$regex           = "/(https?:[\/]*.*)|([\.]*[[[:alnum:]]+\.[^ ]]*.*)/m";
		$isNameLikeAnUrl = (bool) preg_match_all($regex, $name);

		return $isNameLikeAnUrl;
	}
}
