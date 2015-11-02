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

class ProductToolTip extends Module
{
	public function __construct()
	{
		$this->name = 'producttooltip';
		$this->tab = 'front_office_features';
		$this->version = '1.3.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Product tooltips');
		$this->description = $this->l('Shows information on a product page: how many people are viewing it, the last time it was sold and the last time it was added to a cart.');
	}

	public function install()
	{
		if (!parent::install())
			return false;

		/* Default configuration values */
		Configuration::updateValue('PS_PTOOLTIP_PEOPLE', 1);
		Configuration::updateValue('PS_PTOOLTIP_DATE_CART', 1);
		Configuration::updateValue('PS_PTOOLTIP_DATE_ORDER', 1);
		Configuration::updateValue('PS_PTOOLTIP_DAYS', 3);
		Configuration::updateValue('PS_PTOOLTIP_LIFETIME', 30);

		return $this->registerHook('header') && $this->registerHook('productfooter');
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('PS_PTOOLTIP_PEOPLE')
			|| !Configuration::deleteByName('PS_PTOOLTIP_DATE_CART')
			|| !Configuration::deleteByName('PS_PTOOLTIP_DATE_ORDER')
			|| !Configuration::deleteByName('PS_PTOOLTIP_DAYS')
			|| !Configuration::deleteByName('PS_PTOOLTIP_LIFETIME')
			|| !parent::uninstall()
		)
			return false;

		return true;
	}

	public function getContent()
	{
		$html = '';
		/* Update values in DB */
		if (Tools::isSubmit('SubmitToolTip'))
		{
			Configuration::updateValue('PS_PTOOLTIP_PEOPLE', (int)Tools::getValue('PS_PTOOLTIP_PEOPLE'));
			Configuration::updateValue('PS_PTOOLTIP_DATE_CART', (int)Tools::getValue('PS_PTOOLTIP_DATE_CART'));
			Configuration::updateValue('PS_PTOOLTIP_DATE_ORDER', (int)Tools::getValue('PS_PTOOLTIP_DATE_ORDER'));
			Configuration::updateValue('PS_PTOOLTIP_DAYS', ((int)(Tools::getValue('PS_PTOOLTIP_DAYS') < 0 ? 0 : (int)Tools::getValue('PS_PTOOLTIP_DAYS'))));
			Configuration::updateValue('PS_PTOOLTIP_LIFETIME', ((int)(Tools::getValue('PS_PTOOLTIP_LIFETIME') < 0 ? 0 : (int)Tools::getValue('PS_PTOOLTIP_LIFETIME'))));

			$html .= $this->displayConfirmation($this->l('Settings updated'));
		}

		/* Configuration form */

		return $html.$this->renderForm();
	}

	public function hookHeader($params)
	{
		$this->context->controller->addJQueryPlugin('growl');
	}

	public function hookProductFooter($params)
	{
		$id_product = (int)$params['product']->id;

		/* First we try to display the number of people who are currently watching this product page */
		if (Configuration::get('PS_PTOOLTIP_PEOPLE'))
		{
			$date = strftime('%Y-%m-%d %H:%M:%S', time() - (int)(Configuration::get('PS_PTOOLTIP_LIFETIME') * 60));

			$nb_people = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT COUNT(DISTINCT(id_connections)) nb
			FROM '._DB_PREFIX_.'page p
			LEFT JOIN '._DB_PREFIX_.'connections_page cp ON (p.id_page = cp.id_page)
			WHERE p.id_page_type = 1 AND p.id_object = '.(int)$id_product.' AND cp.time_start > \''.pSQL($date).'\'');

			if (isset($nb_people['nb']) && $nb_people['nb'] > 0)
				$this->smarty->assign('nb_people', (int)$nb_people['nb']);
		}

		/* Then, we try to display last sale */
		if (Configuration::get('PS_PTOOLTIP_DATE_ORDER'))
		{
			$days = (int)Configuration::get('PS_PTOOLTIP_DAYS');
			$date = strftime('%Y-%m-%d', strtotime('-'.(int)$days.' day'));

			$order = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT o.date_add
			FROM '._DB_PREFIX_.'order_detail od
			LEFT JOIN '._DB_PREFIX_.'orders o ON (od.id_order = o.id_order)
			WHERE od.product_id = '.(int)$id_product.' AND o.date_add >= \''.pSQL($date).'\'
			ORDER BY o.date_add DESC');

			if (isset($order['date_add']) && Validate::isDateFormat($order['date_add']) && $order['date_add'] != '0000-00-00 00:00:00')
				$this->smarty->assign('date_last_order', $order['date_add']);
			else
			{
				/* No sale? display last cart add instead */
				if (Configuration::get('PS_PTOOLTIP_DATE_CART'))
				{
					$cart = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
					SELECT cp.date_add
					FROM '._DB_PREFIX_.'cart_product cp
					WHERE cp.id_product = '.(int)$id_product);

					if (isset($cart['date_add']) && Validate::isDateFormat($cart['date_add']) && $cart['date_add'] != '0000-00-00 00:00:00')
						$this->smarty->assign('date_last_cart', $cart['date_add']);
				}
			}
		}

		if ((isset($nb_people['nb']) && $nb_people['nb'] > 0) || isset($order['date_add']) || isset($cart['date_add']))
			return $this->display(__FILE__, 'producttooltip.tpl');
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
						'label' => $this->l('Number of visitors'),
						'desc' => $this->l('Display the number of visitors who are currently watching this product.').'<br>'.
							$this->l('If you activate the option above, you must activate the first option ("Save page views for each customer") of the "Data mining for statistics" (StatsData) module.'),
						'name' => 'PS_PTOOLTIP_PEOPLE',
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
						'label' => $this->l('Period length'),
						'desc' => $this->l('Set the reference period length.').'<br>'.
							$this->l('For instance, if set to 30 minutes, the module will display the number of visitors in the last 30 minutes.'),
						'name' => 'PS_PTOOLTIP_LIFETIME',
						'suffix' => $this->l('minutes'),
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
						'type' => 'switch',
						'label' => $this->l('Last order date'),
						'desc' => $this->l('Display the last time the product has been ordered.'),
						'name' => 'PS_PTOOLTIP_DATE_ORDER',
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
						'type' => 'switch',
						'label' => $this->l('Added to a cart'),
						'desc' => $this->l('If the product has not been ordered yet, display the last time it was added to a cart.'),
						'name' => 'PS_PTOOLTIP_DATE_CART',
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
						'label' => $this->l('Do not display events older than'),
						'name' => 'PS_PTOOLTIP_DAYS',
						'suffix' => $this->l('days')
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
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'SubmitToolTip';
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
			'PS_PTOOLTIP_PEOPLE' => Tools::getValue('PS_PTOOLTIP_PEOPLE', Configuration::get('PS_PTOOLTIP_PEOPLE')),
			'PS_PTOOLTIP_LIFETIME' => Tools::getValue('PS_PTOOLTIP_LIFETIME', Configuration::get('PS_PTOOLTIP_LIFETIME')),
			'PS_PTOOLTIP_DATE_ORDER' => Tools::getValue('PS_PTOOLTIP_DATE_ORDER', Configuration::get('PS_PTOOLTIP_DATE_ORDER')),
			'PS_PTOOLTIP_DATE_CART' => Tools::getValue('PS_PTOOLTIP_DATE_CART', Configuration::get('PS_PTOOLTIP_DATE_CART')),
			'PS_PTOOLTIP_DAYS' => Tools::getValue('PS_PTOOLTIP_DAYS', Configuration::get('PS_PTOOLTIP_DAYS')),
		);
	}
}
