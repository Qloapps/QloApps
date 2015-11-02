<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class TrackingFront extends Module
{
	public function __construct()
	{
		$this->name = 'trackingfront';
		$this->tab = 'shipping_logistics';
		$this->version = '1.8.3';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Tracking - Front office');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->description = $this->l('Enables your affiliates to access their own statistics. See Stats/Referrers.');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('ajaxProductFilter'))
		{
			$fake_employee = new Employee();
			$fake_employee->id = 424242;
			$fake_employee->stats_date_from = $this->context->cookie->stats_date_from;
			$fake_employee->stats_date_to = $this->context->cookie->stats_date_to;
			
			if (empty($fake_employee->stats_date_from) || empty($fake_employee->stats_date_to) || $fake_employee->stats_date_from == '0000-00-00' || $fake_employee->stats_date_to == '0000-00-00')
			{
				if (empty($fake_employee->stats_date_from) || $fake_employee->stats_date_from == '0000-00-00')
					$fake_employee->stats_date_from = date('Y').'-01-01';
				if (empty($fake_employee->stats_date_to) || $fake_employee->stats_date_to == '0000-00-00')
					$fake_employee->stats_date_to = date('Y').'-12-31';
			}

			$result = Db::getInstance()->getRow('
			SELECT `id_referrer`
			FROM `'._DB_PREFIX_.'referrer`
			WHERE `id_referrer` = '.(int)Tools::getValue('id_referrer').' AND `passwd` = \''.pSQL(Tools::getValue('token')).'\'');

			if (isset($result['id_referrer']) && (int)$result['id_referrer'] > 0)
				Referrer::getAjaxProduct((int)$result['id_referrer'], (int)Tools::getValue('id_product'), $fake_employee);
		}
		elseif (Tools::isSubmit('logout_tracking'))
		{
			unset($this->context->cookie->tracking_id);
			unset($this->context->cookie->tracking_passwd);
			Tools::redirect(Tools::getShopDomain(true, false).__PS_BASE_URI__.'modules/trackingfront/stats.php');
		}
		elseif (Tools::isSubmit('submitLoginTracking'))
		{
			$errors = array();
			$login = trim(Tools::getValue('login'));
			$passwd = trim(Tools::getValue('passwd'));
			if (empty($login))
				$errors[] = $this->l('login is required');
			elseif (!Validate::isGenericName($login))
				$errors[] = $this->l('invalid login');
			elseif (empty($passwd))
				$errors[] = $this->l('password is required');
			elseif (!Validate::isPasswd($passwd, 1))
				$errors[] = $this->l('invalid password');
			else
			{
				$passwd = Tools::encrypt($passwd);
				$result = Db::getInstance()->getRow('
				SELECT `id_referrer`
				FROM `'._DB_PREFIX_.'referrer`
				WHERE `name` = \''.pSQL($login).'\' AND `passwd` = \''.pSQL($passwd).'\'');
				if (!isset($result['id_referrer']) || !($tracking_id = (int)$result['id_referrer']))
					$errors[] = $this->l('authentication failed');
				else
				{
					$this->context->cookie->tracking_id = $tracking_id;
					$this->context->cookie->tracking_passwd = $passwd;
					Tools::redirect(Tools::getShopDomain(true, false).__PS_BASE_URI__.'modules/trackingfront/stats.php');
				}
			}
			$this->smarty->assign('errors', $errors);
		}

		$from = date('Y-m-d');
		$to = date('Y-m-d');

		if (Tools::isSubmit('submitDatePicker'))
		{
			$from = Tools::getValue('datepickerFrom');
			$to = Tools::getValue('datepickerTo');
		}
		if (Tools::isSubmit('submitDateDay'))
		{
			$from = date('Y-m-d');
			$to = date('Y-m-d');
		}
		if (Tools::isSubmit('submitDateDayPrev'))
		{
			$yesterday = time() - 60 * 60 * 24;
			$from = date('Y-m-d', $yesterday);
			$to = date('Y-m-d', $yesterday);
		}
		if (Tools::isSubmit('submitDateMonth'))
		{
			$from = date('Y-m-01');
			$to = date('Y-m-t');
		}
		if (Tools::isSubmit('submitDateMonthPrev'))
		{
			$m = (date('m') == 1 ? 12 : date('m') - 1);
			$y = ($m == 12 ? date('Y') - 1 : date('Y'));
			$from = $y.'-'.$m.'-01';
			$to = $y.'-'.$m.date('-t', mktime(12, 0, 0, $m, 15, $y));
		}
		if (Tools::isSubmit('submitDateYear'))
		{
			$from = date('Y-01-01');
			$to = date('Y-12-31');
		}
		if (Tools::isSubmit('submitDateYearPrev'))
		{
			$from = (date('Y') - 1).date('-01-01');
			$to = (date('Y') - 1).date('-12-31');
		}
		$this->context->cookie->stats_date_from = $from;
		$this->context->cookie->stats_date_to = $to;
	}

	public function isLogged()
	{
		if (!$this->context->cookie->tracking_id || !$this->context->cookie->tracking_passwd)
			return false;
		$result = Db::getInstance()->getRow('
		SELECT `id_referrer`
		FROM `'._DB_PREFIX_.'referrer`
		WHERE `id_referrer` = '.(int)$this->context->cookie->tracking_id.' AND `passwd` = \''.pSQL($this->context->cookie->tracking_passwd).'\'');

		return isset($result['id_referrer']) ? $result['id_referrer'] : false;
	}

	public function displayLogin()
	{
		return $this->display(__FILE__, 'login.tpl');
	}

	public function displayAccount()
	{
		if (!isset($this->context->cookie->stats_date_from))
			$this->context->cookie->stats_date_from = date('Y-m-01');
		if (!isset($this->context->cookie->stats_date_to))
			$this->context->cookie->stats_date_to = date('Y-m-t');
		Referrer::refreshCache(array(array('id_referrer' => (int)$this->context->cookie->tracking_id)));

		$referrer = new Referrer((int)$this->context->cookie->tracking_id);
		$this->smarty->assign('referrer', $referrer);
		$this->smarty->assign('datepickerFrom', $this->context->cookie->stats_date_from);
		$this->smarty->assign('datepickerTo', $this->context->cookie->stats_date_to);

		$display_tab = array(
			'uniqs' => $this->l('Unique visitors'),
			'visitors' => $this->l('Visitors'),
			'visits' => $this->l('Visits'),
			'pages' => $this->l('Pages viewed'),
			'registrations' => $this->l('Registrations'),
			'orders' => $this->l('Orders'),
			'base_fee' => $this->l('Base fee'),
			'percent_fee' => $this->l('Percent fee'),
			'click_fee' => $this->l('Click fee'),
			'sales' => $this->l('Sales'),
			'cart' => $this->l('Average cart'),
			'reg_rate' => $this->l('Registration rate'),
			'order_rate' => $this->l('Order rate')
		);
		$this->smarty->assign('displayTab', $display_tab);

		$products = Product::getSimpleProducts($this->context->language->id);
		$products_array = array();
		foreach ($products as $product)
			$products_array[] = $product['id_product'];

		$js_files = array();

		$jquery_files = Media::getJqueryPath();
		if (is_array($jquery_files))
			$js_files = array_merge($js_files, $jquery_files);
		else
			$js_files[] = $jquery_files;

		$jquery_ui_files = Media::getJqueryUIPath('ui.datepicker', 'base', true);

		$js_files = array_merge($js_files, $jquery_ui_files['js']);
		$css_files = $jquery_ui_files['css'];

		$js_files[] = $this->_path.'js/trackingfront.js';

		$js_tpl_var = array(
			'product_ids' => implode(', ', $products_array),
			'referrer_id' => $referrer->id,
			'token' => $this->context->cookie->tracking_passwd,
			'display_tab' => implode('", "', array_keys($display_tab))
		);

		$this->smarty->assign(array(
			'js' => $js_files,
			'css' => $css_files,
			'js_tpl_var' => $js_tpl_var
		));

		return $this->display(__FILE__, 'views/templates/front/account.tpl');
	}
}
