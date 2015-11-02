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

class sendToAFriend extends Module
{
	private $_html = '';
	private $_postErrors = array();
	public $context;

	function __construct($dontTranslate = false)
 	{
 	 	$this->name = 'sendtoafriend';
		$this->version = '1.8.0';
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
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
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
}