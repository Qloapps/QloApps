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

class BlockUserInfo extends Module
{
	public function __construct()
	{
		$this->name = 'blockuserinfo';
		$this->tab = 'front_office_features';
		$this->version = '0.4.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('User info block');
		$this->description = $this->l('Adds a block that displays information about the customer.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		return (parent::install() && 
			$this->registerHook('displayTop') && 
			$this->registerHook('displayExternalNavigationHook') && 
			$this->registerHook('displayHeader'));
	}

	// for ajax form login in order-opc controller before this hookDisplayTop function in used
	public function displayUserInfo($params)
	{
		$this->context->smarty->assign('ajaxCustomerLogin',1);

		return $this->display(__FILE__, 'nav.tpl');
	}


	/**
	* Returns module content for header
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookDisplayTop($params)
	{
		return $this->hookdisplayNav($params);
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockuserinfo.css', 'all');
	}

	public function hookdisplayNav($params)
	{
		return $this->display(__FILE__, 'nav.tpl');
	}

	public function hookDisplayTopSubSecondaryBlock($params)
	{
		return $this->hookdisplayNav($params);
	}

	public function hookDisplayExternalNavigationHook($params)
	{
		return $this->display(__FILE__, 'nav-xs.tpl');
	}
}