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

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class blocksharefb extends Module
{
	public function __construct()
	{
		$this->name = 'blocksharefb';
		if(version_compare(_PS_VERSION_, '1.4.0.0') >= 0)
			$this->tab = 'front_office_features';
		else
			$this->tab = 'Blocks';
		$this->version = '1.3.1';
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->displayName = $this->l('Facebook Share Button');
		$this->description = $this->l('Allows customers to share products or content on Facebook.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
	}

	public function install()
	{
		return (parent::install() AND $this->registerHook('extraLeft'));
	}

	public function uninstall()
	{
		//Delete configuration
		return (parent::uninstall() AND $this->unregisterHook(Hook::getIdByName('extraLeft')));
	}

	public function hookExtraLeft($params)
	{
		global $smarty, $cookie, $link;

		$id_product = Tools::getValue('id_product');

		if (isset($id_product) && $id_product != '')
		{
			$product_infos = $this->context->controller->getProduct();
			$smarty->assign(array(
				'product_link' => urlencode($link->getProductLink($product_infos)),
				'product_title' => urlencode($product_infos->name),
			));

			return $this->display(__FILE__, 'blocksharefb.tpl');
		} else {
			return '';
		}
	}
}
