<?php

/*
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2014 PrestaShop SA
 *
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_'))
	exit;

class PayPalUSA extends PaymentModule
{

	private $_error = array();
	private $_validation = array();
	private $_shop_country = array();

	public function __construct()
	{
		$this->name = 'paypalusa';
		$this->version = '1.3.9';
		$this->author = 'PrestaShop';
		$this->className = 'Paypalusa';
		$this->tab = 'payments_gateways';

		parent::__construct();

		$this->_shop_country = new Country((int)Configuration::get('PS_SHOP_COUNTRY_ID'));
		$this->displayName = $this->l((Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code == 'MX') ? 'PayPal Mexico' : 'PayPal USA, Canada');
		$this->description = $this->l((Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code == 'MX') ? 'Accept payments using PayPal\'s Express Checkout, PayPal Payments Standard.' : 'Accept payments using PayPal\'s Express Checkout, PayPal Payments Standard, Advanced, Pro, or Payflow.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');

		/* Backward compatibility */
		require(_PS_MODULE_DIR_.'paypalusa/backward_compatibility/backward.php');
		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);
	}

	/**
	 * PayPal USA installation process:
	 *
	 * Step 1 - Requirements checks (Shop country is USA, Canada or Mexico, cURL extension available)
	 * Step 2 - Pre-set Configuration option values
	 * Step 3 - Install the Addon and create a database table to store transaction details
	 *
	 * @return boolean Installation result
	 */
	public function install()
	{
		/* This Addon is only intended to work in the USA, Canada and Mexico */
		if (Validate::isLoadedObject($this->_shop_country) && !in_array($this->_shop_country->iso_code, array('US', 'MX', 'CA')))
		{
			$this->_errors[] = $this->l('Sorry, this module has been designed for stores based in USA, Canada and Mexico only. Please use the classic PayPal Addon instead.');
			return false;
		}

		/* The cURL PHP extension must be enabled to use this module */
		if (!function_exists('curl_version'))
		{
			$this->_errors[] = $this->l('Sorry, this module requires the cURL PHP Extension (http://www.php.net/curl), which is not enabled on your server. Please ask your hosting provider for assistance.');
			return false;
		}

		/* General Configuration options */
		Configuration::updateValue('PAYPAL_USA_SANDBOX', false);
		Configuration::updateValue('PAYPAL_USA_SANDBOX_ADVANCED', false);

		/* Configuration of PayPal Express Checkout */
		Configuration::updateValue('PAYPAL_USA_EXP_CHK_PRODUCT', true);
		Configuration::updateValue('PAYPAL_USA_EXP_CHK_SHOPPING_CART', true);
		Configuration::updateValue('PAYPAL_USA_MANAGER_PARTNER', 'PayPal');

		/* Configuration of the Payment options */
		Configuration::updateValue('PAYPAL_USA_PAYMENT_STANDARD', true);
		Configuration::updateValue('PAYPAL_USA_PAYMENT_ADVANCED', false);
		Configuration::updateValue('PAYPAL_USA_EXPRESS_CHECKOUT', false);
		Configuration::updateValue('PAYPAL_USA_PAYFLOW_LINK', false);
		// 2013-11-8 if there is no country specified, choose US, otherwise module will fail
		if(	version_compare(_PS_VERSION_, '1.5', '<')){
			Configuration::updateValue('PS_SHOP_COUNTRY','US');
		}else{
			Configuration::updateValue('PS_SHOP_COUNTRY_ID',21);
		}

		return parent::install() && $this->registerHook('payment') && $this->registerHook('adminOrder') &&
				$this->registerHook('header') && $this->registerHook('orderConfirmation') && $this->registerHook('shoppingCartExtra') &&
				$this->registerHook('productFooter') && $this->registerHook('BackOfficeHeader') && $this->_installDb();
	}

	/**
	 * PayPal USA database table installation (to store the transaction details)
	 *
	 * @return boolean Database table installation result
	 */
	private function _installDb()
	{
		return Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'paypal_usa_transaction` (
			`id_paypal_usa_transaction` int(11) NOT NULL AUTO_INCREMENT,
			`type` enum(\'payment\',\'refund\') NOT NULL,
			`source` enum(\'standard\',\'express\',\'advanced\',\'payflow_pro\',\'payflow_link\') NOT NULL,
			`id_shop` int(11) unsigned NOT NULL DEFAULT \'0\',
			`id_customer` int(11) unsigned NOT NULL,
			`id_cart` int(11) unsigned NOT NULL,
			`id_order` int(11) unsigned NOT NULL,
			`id_transaction` varchar(32) NOT NULL,
			`amount` decimal(10,2) NOT NULL,
			`currency` varchar(3) NOT NULL,
			`cc_type` varchar(16) NOT NULL,
			`cc_exp` varchar(8) NOT NULL,
			`cc_last_digits` int(11) NOT NULL,
			`cvc_check` tinyint(1) NOT NULL DEFAULT \'0\',
			`fee` decimal(10,2) NOT NULL,
			`mode` enum(\'live\',\'test\') NOT NULL,
			`date_add` datetime NOT NULL,
		PRIMARY KEY (`id_paypal_usa_transaction`), KEY `idx_transaction` (`type`,`id_order`))
		ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
	}

	/**
	 * PayPal USA uninstallation process:
	 *
	 * Step 1 - Remove Configuration option values from database
	 * Step 2 - Remove the database containing the transaction details (optional, must be done manually)
	 * Step 3 - Uninstallation of the Addon itself
	 *
	 * @return boolean Uninstallation result
	 */
	public function uninstall()
	{
		$keys_to_uninstall = array('PAYPAL_USA_ACCOUNT', 'PAYPAL_USA_SANDBOX', 'PAYPAL_USA_API_USERNAME', 'PAYPAL_USA_API_PASSWORD',
			'PAYPAL_USA_API_SIGNATURE', 'PAYPAL_USA_EXP_CHK_PRODUCT', 'PAYPAL_USA_EXP_CHK_SHOPPING_CART', 'PAYPAL_USA_EXP_CHK_BORDER_COLOR',
			'PAYPAL_USA_MANAGER_USER', 'PAYPAL_USA_MANAGER_LOGIN', 'PAYPAL_USA_MANAGER_PASSWORD', 'PAYPAL_USA_MANAGER_PARTNER',
			'PAYPAL_USA_PAYMENT_STANDARD', 'PAYPAL_USA_PAYMENT_ADVANCED', 'PAYPAL_USA_EXPRESS_CHECKOUT', 'PAYPAL_USA_PAYFLOW_LINK',
			'PAYPAL_USA_SANDBOX_ADVANCED');

		$result = true;
		foreach ($keys_to_uninstall as $key_to_uninstall)
			$result &= Configuration::deleteByName($key_to_uninstall);

		/* Uncomment this line if you would like to also delete the Transaction details table */
		/* $result &= Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'paypal_usa_transaction`'); */

		return $result && parent::uninstall();
	}

	/* PayPal USA configuration section
	 *
	 * @return HTML page (template) to configure the Addon
	 */

	public function getContent()
	{
		/* Loading CSS and JS files */
		// 2013-11-8 add 1.4 support
		if(isset($this->context->controller)){
			$this->context->controller->addCSS(array($this->_path.'css/paypal-usa.css', $this->_path.'css/colorpicker.css'));
			$this->context->controller->addJS(array(_PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js', $this->_path.'js/colorpicker.js', $this->_path.'js/jquery.lightbox_me.js', $this->_path.'js/paypalusa.js'));
		}

		/* Update the Configuration option values depending on which form has been submitted */
		if ((Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code == 'MX') && Tools::isSubmit('SubmitBasicSettings'))
		{
			$this->_saveSettingsProducts();
			$this->_saveSettingsBasic();
			unset($this->_validation[count($this->_validation) - 1]);
		}
		elseif (Tools::isSubmit('SubmitPayPalProducts'))
			$this->_saveSettingsProducts();
		elseif (Tools::isSubmit('SubmitBasicSettings'))
			$this->_saveSettingsBasic();
		elseif (Tools::isSubmit('SubmitAdvancedSettings'))
			$this->_saveSettingsAdvanced();

		/* If PayPal Payments Advanced has been enabled, the Shop country has to be USA */
		if (Configuration::get('PAYPAL_USA_PAYMENT_ADVANCED') && Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code != 'US')
			$this->context->smarty->assign('paypal_usa_advanced_only_us', true);

		/* If PayPal Payments Advanced has been enabled, PayPal's Manager credentials must be filled */
		if ((Configuration::get('PAYPAL_USA_PAYMENT_ADVANCED') || Configuration::get('PAYPAL_USA_PAYFLOW_LINK'))&& (Configuration::get('PAYPAL_USA_MANAGER_USER') == '' ||
				Configuration::get('PAYPAL_USA_MANAGER_PASSWORD') == '' || Configuration::get('PAYPAL_USA_MANAGER_PARTNER') == ''))
			$this->_error[] = $this->l('In order to use PayPal Payments Advanced, please provide your PayPal Manager credentials.');

		/* If PayPal Express Checkout has been enabled, PayPal's API credentials must be filled */
		if (Configuration::get('PAYPAL_USA_EXPRESS_CHECKOUT') && (Configuration::get('PAYPAL_USA_API_USERNAME') == '' ||
				Configuration::get('PAYPAL_USA_API_PASSWORD') == '' || Configuration::get('PAYPAL_USA_API_SIGNATURE') == ''))
			$this->_error[] = $this->l('In order to use PayPal ExpessCheckOut, please provide your PayPal API credentials.');

		/* If PayPal Standard has been enabled, PayPal's API credentials should be filled to use the refund feature */
		if (Configuration::get('PAYPAL_USA_PAYMENT_STANDARD') && (Configuration::get('PAYPAL_USA_API_USERNAME') == '' ||
				Configuration::get('PAYPAL_USA_API_PASSWORD') == '' || Configuration::get('PAYPAL_USA_API_SIGNATURE') == ''))
			$this->_warning[] = $this->l('In order to use PayPal refund feature with PayPal Standard, please provide your PayPal API credentials.');

		// 2013-11-8 add 1.4 token support
		if(method_exists('Tools','getAdminTokenLite')){
			$token = Tools::getAdminTokenLite('AdminModules');
		}else{
			$tabid = (int)Tab::getCurrentTabId();
			$employee_id = (int)$this->context->cookie->id_employee;
			$token = 'AdminModules'.$tabid.$employee_id;
			$token = Tools::getAdminToken($token);
		}
		$this->context->smarty->assign(array(
			'paypal_usa_tracking' => 'http://www.prestashop.com/modules/paypalusa.png?url_site='.Tools::safeOutput($_SERVER['SERVER_NAME']).'&id_lang='.(int)$this->context->cookie->id_lang,
			'paypal_usa_form_link' => './index.php?tab=AdminModules&configure=paypalusa&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module='.$this->tab.'&module_name=paypalusa',
			'paypal_usa_ssl' => Configuration::get('PS_SSL_ENABLED'),
			'paypal_usa_validation' => (empty($this->_validation) ? false : $this->_validation),
			'paypal_usa_error' => (empty($this->_error) ? false : $this->_error),
			'paypal_usa_warning' => (empty($this->_warning) ? false : $this->_warning),
			'paypal_usa_configuration' => Configuration::getMultiple(array('PAYPAL_USA_SANDBOX', 'PAYPAL_USA_PAYMENT_STANDARD', 'PAYPAL_USA_PAYMENT_ADVANCED',
				'PAYPAL_USA_EXPRESS_CHECKOUT', 'PAYPAL_USA_PAYFLOW_LINK', 'PAYPAL_USA_ACCOUNT', 'PAYPAL_USA_API_USERNAME',
				'PAYPAL_USA_API_PASSWORD', 'PAYPAL_USA_API_SIGNATURE', 'PAYPAL_USA_EXP_CHK_PRODUCT', 'PAYPAL_USA_EXP_CHK_SHOPPING_CART',
				'PAYPAL_USA_EXP_CHK_BORDER_COLOR', 'PAYPAL_USA_MANAGER_USER', 'PAYPAL_USA_MANAGER_LOGIN', 'PAYPAL_USA_MANAGER_PASSWORD',
				'PAYPAL_USA_MANAGER_PARTNER', 'PAYPAL_USA_SANDBOX_ADVANCED')),
			'paypal_usa_merchant_country_is_usa' => (Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code == 'US'),
			'paypal_usa_merchant_country_is_mx' => (Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code == 'MX'),
			'paypal_usa_ps_14' => (version_compare(_PS_VERSION_, '1.5', '<') ? 1 : 0),
			'paypal_usa_b1width' => (version_compare(_PS_VERSION_, '1.5', '>') ? '350' : '300'),
			'paypal_usa_js_files' => stripcslashes('"'._PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js","'.$this->_path.'js/colorpicker.js","'.$this->_path.'js/jquery.lightbox_me.js","'.$this->_path.'js/paypalusa.js'.'"')
		));

		return $this->display(__FILE__, 'views/templates/admin/configuration'.((Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code == 'MX') ? '-mx' : '').'.tpl');
	}

	/*
	 * PayPal USA configuration section - PayPal's product selection
	 */

	private function _saveSettingsProducts()
	{
		if (!isset($_POST['paypal_usa_products']) && !isset($_POST['paypal_usa_express_checkout']))
			$this->_error[] = $this->l('You must choose at least one PayPal product to enable.');
		else
		{
			foreach (array(1 => 'PAYPAL_USA_PAYMENT_STANDARD', 2 => 'PAYPAL_USA_PAYMENT_ADVANCED', 3 => 'PAYPAL_USA_PAYFLOW_LINK') as $paypal_usa_product_id => $paypal_usa_product)
				Configuration::updateValue($paypal_usa_product, (isset($_POST['paypal_usa_products']) && $_POST['paypal_usa_products'] == $paypal_usa_product_id) ? 1 : null);

			Configuration::updateValue('PAYPAL_USA_EXPRESS_CHECKOUT', isset($_POST['paypal_usa_express_checkout']));

			if (Configuration::get('PAYPAL_USA_EXPRESS_CHECKOUT') && !Configuration::get('PAYPAL_USA_EXP_CHK_PRODUCT') && !Configuration::get('PAYPAL_USA_EXP_CHK_SHOPPING_CART'))
				Configuration::updateValue('PAYPAL_USA_EXP_CHK_PRODUCT', 1);

			$this->_validation[] = $this->l('Congratulations, your configuration was updated successfully');
		}
	}

	/*
	 * PayPal USA configuration section - Basic settings (PayPal Business Account, API credentials, Express Checkout options)
	 */

	private function _saveSettingsBasic()
	{
		if (!isset($_POST['paypal_usa_account']) || !$_POST['paypal_usa_account'])
			$this->_error[] = $this->l('Your Paypal Business Account is required.');

		Configuration::updateValue('PAYPAL_USA_ACCOUNT', pSQL(Tools::getValue('paypal_usa_account')));
		if (Configuration::get('PAYPAL_USA_EXPRESS_CHECKOUT'))
		{
			if (!isset($_POST['paypal_usa_api_username']) || !$_POST['paypal_usa_api_username'])
				$this->_error[] = $this->l('Your Paypal API Username is required.');
			if (!isset($_POST['paypal_usa_api_password']) || !$_POST['paypal_usa_api_password'])
				$this->_error[] = $this->l('Your Paypal API Password is required.');
			if (!isset($_POST['paypal_usa_api_signature']) || !$_POST['paypal_usa_api_signature'])
				$this->_error[] = $this->l('Your Paypal API Signature is required.');
		}
			Configuration::updateValue('PAYPAL_USA_API_USERNAME', pSQL(Tools::getValue('paypal_usa_api_username')));
			Configuration::updateValue('PAYPAL_USA_API_PASSWORD', pSQL(Tools::getValue('paypal_usa_api_password')));
			Configuration::updateValue('PAYPAL_USA_API_SIGNATURE', pSQL(Tools::getValue('paypal_usa_api_signature')));
			Configuration::updateValue('PAYPAL_USA_SANDBOX', (bool)Tools::getValue('paypal_usa_sandbox'));

		/* PayPal Express Checkout options */
		if (Configuration::get('PAYPAL_USA_EXPRESS_CHECKOUT') && !isset($_POST['paypal_usa_checkbox_shopping_cart']) && !isset($_POST['paypal_usa_checkbox_product']))
			$this->_error[] = $this->l('As PayPal Express Checkout is enabled, please select where it should be displayed.');
		else
		{
			Configuration::updateValue('PAYPAL_USA_EXP_CHK_PRODUCT', isset($_POST['paypal_usa_checkbox_product']));
			Configuration::updateValue('PAYPAL_USA_EXP_CHK_SHOPPING_CART', isset($_POST['paypal_usa_checkbox_shopping_cart']));
			Configuration::updateValue('PAYPAL_USA_EXP_CHK_BORDER_COLOR', pSQL(Tools::getValue('paypal_usa_checkbox_border_color')));
		}

		/* Automated check to verify the API credentials configured by the merchant */
		if (Configuration::get('PAYPAL_USA_API_USERNAME') && Configuration::get('PAYPAL_USA_API_PASSWORD') && Configuration::get('PAYPAL_USA_API_SIGNATURE'))
		{
			$result = $this->postToPayPal('GetBalance', '');
			if (Tools::strtoupper($result['ACK']) != 'SUCCESS' && Tools::strtoupper($result['ACK']) != 'SUCCESSWITHWARNING')
				$this->_error[] = $this->l('Your Paypal API crendentials are not valid, please double-check their values or contact PayPal.');
			else
				Configuration::updateValue('PAYPALUSA_CONFIGURATION_OK', true);
		}

		if (!count($this->_error))
			$this->_validation[] = $this->l('Congratulations, your configuration was updated successfully');
	}

	/*
	 * PayPal USA configuration section - Advanced settings (PayPal Manager and Payment Gateway)
	 */

	private function _saveSettingsAdvanced()
	{
		if (!isset($_POST['paypal_usa_manager_user']) || !$_POST['paypal_usa_manager_user'])
			$this->_error[] = $this->l('Your Paypal Manager User is required.');
		if (!isset($_POST['paypal_usa_manager_login']) || !$_POST['paypal_usa_manager_login'])
			$this->_error[] = $this->l('Your Paypal Manager Login is required.');
		if (!isset($_POST['paypal_usa_manager_password']) || !$_POST['paypal_usa_manager_password'])
			$this->_error[] = $this->l('Your Paypal Manager Password is required.');
		if (!isset($_POST['paypal_usa_manager_partner']) || !$_POST['paypal_usa_manager_partner'])
			$this->_error[] = $this->l('Your Paypal Manager Partner is required (Default value is "PayPal").');

		Configuration::updateValue('PAYPAL_USA_SANDBOX_ADVANCED', (bool)Tools::getValue('paypal_usa_sandbox_advanced'));
		Configuration::updateValue('PAYPAL_USA_MANAGER_USER', Tools::getValue('paypal_usa_manager_user'));
		Configuration::updateValue('PAYPAL_USA_MANAGER_LOGIN', Tools::getValue('paypal_usa_manager_login'));
		Configuration::updateValue('PAYPAL_USA_MANAGER_PASSWORD', Tools::getValue('paypal_usa_manager_password'));
		Configuration::updateValue('PAYPAL_USA_MANAGER_PARTNER', Tools::getValue('paypal_usa_manager_partner'));

		/* Automated check to verify the PayPal Manager credentials configured by the merchant */
		if (Configuration::get('PAYPAL_USA_MANAGER_USER') && Configuration::get('PAYPAL_USA_MANAGER_PASSWORD') && Configuration::get('PAYPAL_USA_MANAGER_PARTNER'))
		{
			$params = 'PARTNER='.urlencode(Configuration::get('PAYPAL_USA_MANAGER_PARTNER')).'&VENDOR='.urlencode(Configuration::get('PAYPAL_USA_MANAGER_LOGIN')).
					'&USER='.urlencode(Configuration::get('PAYPAL_USA_MANAGER_USER')).
					'&PWD='.urlencode(Configuration::get('PAYPAL_USA_MANAGER_PASSWORD')).
					'&TRXTYPE=S&AMT=0&CREATESECURETOKEN=Y&SECURETOKENID='.urlencode(Tools::passwdGen(36));
			$result = $this->postToPayFlow($params, Configuration::get('PAYPAL_USA_PAYFLOW_LINK') ? 'link' : 'pro');
			if ($result['RESULT'] != 0 && $result['RESPMSG'] != 'Approved')
				$this->_error[] = $this->l('Your PayPal Manager Configuration crendentials are not valid, please double-check their values or contact PayPal.');
		}

		if (!count($this->_error))
			$this->_validation[] = $this->l('Congratulations, your configuration was updated successfully');
	}

	/* PayPal USA payment hook
	 *
	 * @param $params Array Default PrestaShop parameters sent to the hookPayment() method (Order details, etc.)
	 *
	 * @return HTML content (Template) displaying the enable PayPal payment methods (PayPal Payments Standard, PayPal Payments Advanced or PayPal Express Checkout)
	 */

	public function hookPayment($params)
	{
		$html = '';
		$paypal_usa_express_checkout_no_token = (!isset($this->context->cookie->paypal_express_checkout_token) || empty($this->context->cookie->paypal_express_checkout_token));

		/* 2013-11-8 modify to adapt 1.4, PayPal Express Checkout */
		if (Configuration::get('PAYPAL_USA_EXPRESS_CHECKOUT'))
		{
			/* Display the PayPal Express Checkout button to confirm the payment */
			$this->context->smarty->assign(array('paypal_usa_express_checkout_hook_payment' => true,
				'paypal_usa_merchant_country_is_mx' => (Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code == 'MX'),
				'paypal_usa_express_checkout_no_token' => $paypal_usa_express_checkout_no_token,
				'paypal_usa_action_payment' => $this->getModuleLink('paypalusa', 'expresscheckout', array('pp_exp_payment' => 1), Configuration::get('PS_SSL_ENABLED')),
				'paypal_usa_action' => $this->getModuleLink('paypalusa', 'expresscheckout', array('pp_exp_initial' => 1), Configuration::get('PS_SSL_ENABLED'))));
			$html .= $this->display(__FILE__, 'views/templates/hook/express-checkout.tpl');
		}
		else
		{
			/*If paypal express checkout not active clean up previous */
			unset($this->context->cookie->paypal_express_checkout_token,$this->context->cookie->paypal_express_checkout_payer_id);
			$paypal_usa_express_checkout_no_token = true;
		}

		/* PayPal Payments Standard */

		if (Configuration::get('PAYPAL_USA_PAYMENT_STANDARD') && ($this->_shop_country instanceof Country && $this->_shop_country->iso_code != 'MX') && $paypal_usa_express_checkout_no_token)
		{
			/* Display a form/button that will be sent to PayPal with the customer details */
			$billing_address = new Address((int)$this->context->cart->id_address_invoice);
			$billing_address->country = new Country((int)$billing_address->id_country);
			$billing_address->state = new State((int)$billing_address->id_state);

			$this->context->smarty->assign(array(
				'paypal_usa_action' => 'https://www'.(Configuration::get('PAYPAL_USA_SANDBOX') ? '.sandbox' : '').'.paypal.com/cgi-bin/webscr',
				'paypal_usa_customer' => $this->context->customer,
				'paypal_usa_business_account' => Configuration::get('PAYPAL_USA_ACCOUNT'),
				'paypal_usa_billing_address' => $billing_address,
				'paypal_usa_total_tax' => (float)$this->context->cart->getOrderTotal(true) - (float)$this->context->cart->getOrderTotal(false),
				'paypal_usa_cancel_url' => $this->context->link->getPageLink('order.php',''),
				'paypal_usa_notify_url' => $this->getModuleLink('paypalusa', 'validation', array('pps' => 1), Configuration::get('PS_SSL_ENABLED')),
				'paypal_usa_return_url' => /*26/12/2013 fix for Backward compatibilies on confirmation page*/
					version_compare(_PS_VERSION_, '1.5', '<') ?
					(Configuration::get('PS_SSL_ENABLED') ? Tools::getShopDomainSsl(true) : Tools::getShopDomain(true)).
					__PS_BASE_URI__.'order-confirmation.php?id_cart='.(int)$this->context->cart->id.'&id_module='.(int)$this->id.'&key='.$this->context->customer->secure_key :
					$this->context->link->getPageLink('order-confirmation.php', null, null, array('id_cart' => (int)$this->context->cart->id, 'key' => $this->context->customer->secure_key, 'id_module' => $this->id)),

				));

			$html .= $this->display(__FILE__, 'views/templates/hook/standard.tpl');
		}

		/* PayPal Payments Advanced or PayPal Payflow link */
		if ((Configuration::get('PAYPAL_USA_PAYFLOW_LINK') || Configuration::get('PAYPAL_USA_PAYMENT_ADVANCED')) && (Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code != 'MX') && $paypal_usa_express_checkout_no_token)
		{
			/* Create a unique token and a PayPal payment request to display an <iframe> loading the marchant Hosted Checkout page (see PayPal Manager website) */
			$token = Tools::passwdGen(36);

			$amount = $this->context->cart->getOrderTotal(true);
			$taxes = $amount - $this->context->cart->getOrderTotal(false);
			$i = 0;
			$nvp_request = '';
			if ($this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS) == 0)
			{
				foreach ($this->context->cart->getProducts() as $product)
				{
					$nvp_request .= '&L_NAME'.$i.'['.strlen(urlencode($product['name'])).']='.urlencode($product['name']).
							'&L_SKU'.$i.'['.strlen(urlencode((int)$product['id_product'])).']='.urlencode((int)$product['id_product']).
							'&L_DESC'.$i.'['.strlen(urlencode(strip_tags(Tools::truncate($product['description_short'], 80)))).']='.urlencode(strip_tags(Tools::truncate($product['description_short'], 80))).
							'&L_COST'.$i.'['.strlen(urlencode((float)$product['price'])).']='.urlencode((float)$product['price']).
							'&L_QTY'.$i.'['.strlen(urlencode((int)$product['cart_quantity'])).']='.urlencode((int)$product['cart_quantity']);
					$i++;
				}
				$nvp_request .= '&FREIGHTAMT['.strlen(urlencode((float)$this->context->cart->getTotalShippingCost())).']='.urlencode((float)$this->context->cart->getTotalShippingCost()).
						'&TAXAMT['.strlen(urlencode((float)$taxes)).']='.urlencode((float)$taxes);
			}

			$currency = new Currency((int)$this->context->cart->id_currency);
			$result = $this->postToPayFlow('&TRXTYPE[1]=S&AMT['.strlen($amount).']='.$amount.$nvp_request.'&CREATESECURETOKEN[1]=Y&DISABLERECEIPT=TRUE&SECURETOKENID[36]='.$token.
					'&CURRENCY['.strlen(urlencode($currency->iso_code)).']='.urlencode($currency->iso_code).'&TEMPLATE[9]=MINLAYOUT&ERRORURL['.strlen($this->getModuleLink('paypalusa', 'validation', array(), Configuration::get('PS_SSL_ENABLED'))).']='.$this->getModuleLink('paypalusa', 'validation', array(), Configuration::get('PS_SSL_ENABLED')).
					'&CANCELURL='.$this->context->link->getPageLink('order.php','').
					'&RETURNURL['.strlen($this->getModuleLink('paypalusa', 'validation', array(), Configuration::get('PS_SSL_ENABLED'))).']='.$this->getModuleLink('paypalusa', 'validation', array(), Configuration::get('PS_SSL_ENABLED')), Configuration::get('PAYPAL_USA_PAYFLOW_LINK') ? 'link' : 'pro');
			if ((isset($result['RESULT']) && $result['RESULT']== 0) && !empty($result['SECURETOKEN']) && $result['SECURETOKENID'] == $token && Tools::strtoupper($result['RESPMSG']) == 'APPROVED')
			{
				/* Store the PayPal response token in the customer cookie for later use (payment confirmation) */
				Context::getContext()->cookie->paypal_advanced_token = $result['SECURETOKEN'];

				$this->context->smarty->assign('paypal_usa_advanced_iframe_url', 'https://'.(Configuration::get('PAYPAL_USA_SANDBOX_ADVANCED') ? 'pilot-' : '').'payflowlink.paypal.com/payflowlink.do?SECURETOKEN='.$result['SECURETOKEN'].'&SECURETOKENID='.$result['SECURETOKENID'].(Configuration::get('PAYPAL_USA_SANDBOX_ADVANCED') ? '&MODE=TEST' : ''));

				$html .= $this->display(__FILE__, 'views/templates/hook/payment-advanced.tpl');
			}
		}
		return $html;
	}

	/* PayPal USA Back-office header hook
	 * Only called in case of a refund performed by the merchant on the Order details page
	 *
	 * @return Error or confirmation message
	 */

	public function hookBackOfficeHeader()
	{
		// 2013-11-8 Add 1.4 js and css support
		if(	version_compare(_PS_VERSION_, '1.5', '<')){
			$css_files = array($this->_path.'css/paypal-usa.css', $this->_path.'css/colorpicker.css');

			foreach($css_files as $cssfile){
				echo 	'<link type="text/css" rel="stylesheet" href="'.$cssfile.'" />';
			}

		}
		/* Continue only if we are on the order's details page (Back-office) */
		if (!isset($_GET['vieworder']) || !isset($_GET['id_order']))
			return;

		/* If the "Refund" button has been clicked, check if we can perform a partial or full refund on this order */
		if (Tools::isSubmit('process_refund') && isset($_POST['refund_amount']) && isset($_POST['id_transaction']))
		{
			/* Get transaction details and make sure the token is valid */
			$paypal_usa_transaction_details = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'paypal_usa_transaction WHERE id_order = '.(int)$_GET['id_order'].' AND type = \'payment\' AND id_shop = '.(int)$this->context->shop->id);
			if (isset($paypal_usa_transaction_details['id_transaction']) && $paypal_usa_transaction_details['id_transaction'] == Tools::getValue('id_transaction'))
			{
				/* Check how much has been refunded already on this order */
				$paypal_usa_refunded = Db::getInstance()->getValue('SELECT SUM(amount) FROM '._DB_PREFIX_.'paypal_usa_transaction WHERE id_order = '.(int)$_GET['id_order'].' AND type = \'refund\' AND id_shop = '.(int)$this->context->shop->id);
				if ($_POST['refund_amount'] <= number_format($paypal_usa_transaction_details['amount'] - $paypal_usa_refunded, 2, '.', ''))
					$this->_processRefund(Tools::getValue('id_transaction'), (float)Tools::getValue('refund_amount'), $paypal_usa_transaction_details);
				else
				{
					$this->context->smarty->assign('paypal_usa_refund', 0);
					$this->context->smarty->assign('paypal_usa_refund_error', $this->l('You cannot refund more than').' '.Tools::displayPrice($paypal_usa_transaction_details['amount'] - $paypal_usa_refunded).' '.$this->l('on this order'));
				}
			}
			else
			{
				$this->context->smarty->assign('paypal_usa_refund', 0);
				$this->context->smarty->assign('paypal_usa_refund_error', $this->l('Invalid transaction ID, refund cannot be performed'));
			}
		}
	}

	/* PayPal USA Order refund process
	 * Only called in case of a refund performed by the merchant on the Order details page
	 *
	 * @param $id_transaction integer PayPal's Transaction ID
	 * @param $amount float Amount to be refunded (can be either a full or partial amount)
	 * @param $original_transaction array Original transaction details (Source, currency, amount)
	 *
	 * @return Error or confirmation message
	 */

	private function _processRefund($id_transaction, $amount, $original_transaction)
	{
		$refund_type = ($amount == $original_transaction['amount']) ? 'Full' : 'Partial';

		/* For PayPal Payments Standard and PayPal Express Checkout */
		if ($original_transaction['source'] == 'express' || ($original_transaction['source'] == 'standard' && Configuration::get('PAYPAL_USA_ACCOUNT') && Configuration::get('PAYPAL_USA_ACCOUNT') && Configuration::get('PAYPAL_USA_ACCOUNT')))
		{
			/* Send the refund request to PayPal */
			$result = $this->postToPayPal('RefundTransaction', '&'.http_build_query(array('TRANSACTIONID' => $id_transaction, 'REFUNDTYPE' => $refund_type, 'AMT' => $amount, 'CURRENCYCODE' => $original_transaction['currency'])));

			/* Check the response from PayPal and store the refund transaction details */
			if (Tools::strtoupper($result['ACK']) == 'SUCCESS' || Tools::strtoupper($result['ACK']) == 'SUCCESSWITHWARNING')
			{
				$refund_transaction = $original_transaction;
				$refund_transaction['amount'] = $result['GROSSREFUNDAMT'];
				$refund_transaction['id_transaction'] = $result['REFUNDTRANSACTIONID'];
				$refund_transaction['fee'] = $result['FEEREFUNDAMT'];
				$refund_transaction['date_add'] = date('Y-m-d H:i:s');
				$refund_transaction['source'] = 'RefundTransaction';
				$refund_transaction['currency'] = $result['CURRENCYCODE'];
				$refund_transaction['id_shop'] = (int)$this->context->shop->id;
				$this->addTransaction('refund', $refund_transaction);
				$this->context->smarty->assign('paypal_usa_refund', 1);
			}
			else
			{
				$this->context->smarty->assign('paypal_usa_refund', 0);
				if (isset($result['L_SHORTMESSAGE0']))
					$this->context->smarty->assign('paypal_usa_refund_error', $result['L_SHORTMESSAGE0'].' ('.$result['L_LONGMESSAGE0'].' - Err. code: '.$result['L_ERRORCODE0'].')');
			}
		}

		/* For PayPal Payments Advanced */
		elseif ($original_transaction['source'] == 'advanced')
		{
			/* Send the refund request to PayPal */
			$result = $this->postToPayFlow('TRXTYPE=C&TENDER=C&ORIGID='.$id_transaction.'&AMT=.'.$amount, Configuration::get('PAYPAL_USA_PAYFLOW_LINK') ? 'link' : 'pro');

			/* Check the response from PayPal and store the refund transaction details */
			if ($result['RESULT'] == 0 && !empty($result['SECURETOKEN']) && $result['SECURETOKENID'] == $token && Tools::strtoupper($result['RESPMSG']) == 'APPROVED')
			{
				$refund_transaction = $original_transaction;
				$refund_transaction['amount'] = $result['AMT'];
				$refund_transaction['id_transaction'] = $result['PNREF'];
				$refund_transaction['id_shop'] = (int)$this->context->shop->id;
				$this->addTransaction('refund', $refund_transaction);
				$this->context->smarty->assign('paypal_usa_refund', 1);
			}
			else
			{
				$this->context->smarty->assign('paypal_usa_refund', 0);
				if (isset($result['RESPMSG']))
					$this->context->smarty->assign('paypal_usa_refund_error', $result['RESPMSG']);
			}
		}
	}

	/* PayPal USA Admin order detail hook
	 *
	 * @param $params Array Default PrestaShop parameters sent to the hookAdminOrder() method
	 *
	 * @return HTML content (Template) displaying the Transaction details and Refund form
	 */

	public function hookAdminOrder($params)
	{
		/* Check if the order was paid with this Addon and display the Transaction details */
		if (Db::getInstance()->getValue('SELECT module FROM '._DB_PREFIX_.'orders WHERE id_order = '.(int)$_GET['id_order']) == $this->name)
		{
			/* Do not display the refund block unless the API crendetials are set */
			if (Configuration::get('PAYPAL_USA_API_USERNAME') == '' || Configuration::get('PAYPAL_USA_API_PASSWORD') == '' || Configuration::get('PAYPAL_USA_API_SIGNATURE') == '')
				return;

			/* Retrieve the transaction details */
			$paypal_usa_transaction_details = Db::getInstance()->getRow('
			SELECT *
			FROM '._DB_PREFIX_.'paypal_usa_transaction
			WHERE id_order = '.(int)$_GET['id_order'].' AND type = \'payment\' AND id_shop = '.(int)$this->context->shop->id);

			/* Get all the refunds previously made (to build a list and determine if another refund is still possible) */
			$paypal_usa_refund_details = Db::getInstance()->ExecuteS('
			SELECT amount, date_add, currency
			FROM '._DB_PREFIX_.'paypal_usa_transaction
			WHERE id_order = '.(int)$_GET['id_order'].' AND type = \'refund\' AND id_shop = '.(int)$this->context->shop->id.' ORDER BY date_add DESC');

			$paypal_products = array('express' => 'PayPal Express Checkout', 'standard' => 'PayPal Standard', 'advanced' => 'PayPal Payments Advanced', 'payflow_pro' => 'PayPal PayFlow Pro');
			$paypal_usa_transaction_details['source'] = $paypal_products[$paypal_usa_transaction_details['source']];

			$this->context->smarty->assign(array(
				'paypal_usa_more60d' => ((time() - strtotime($paypal_usa_transaction_details['date_add'])) > (60 * 86400)), /* Do not allow refund if the order has been placed more than 60 days ago */
				'paypal_usa_transaction_details' => $paypal_usa_transaction_details,
				'paypal_usa_refund_details' => $paypal_usa_refund_details));

			return $this->display(__FILE__, 'views/templates/admin/admin-order.tpl');
		}
	}

	/* PayPal USA Order confirmation hook
	 *
	 * @param $params Array Default PrestaShop parameters sent to the hookOrderConfirmation() method
	 *
	 * @return HTML content (Template) displaying a confirmation or error message upon order creation
	 */

	public function hookOrderConfirmation($params)
	{
		if (!isset($params['objOrder']) || ($params['objOrder']->module != $this->name))
			return false;
		if (isset($params['objOrder']) && Validate::isLoadedObject($params['objOrder']) && isset($params['objOrder']->valid) &&
				version_compare(_PS_VERSION_, '1.5', '>=') && isset($params['objOrder']->reference))
		{
			$this->smarty->assign('paypal_usa_order', array('id' => $params['objOrder']->id, 'reference' => $params['objOrder']->reference, 'valid' => $params['objOrder']->valid));
			return $this->display(__FILE__, 'views/templates/hook/order-confirmation.tpl');
		}



		// 2013-11-8 add 1.4 support
		if (isset($params['objOrder']) && Validate::isLoadedObject($params['objOrder']) && isset($params['objOrder']->valid) &&
				version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$this->smarty->assign('paypal_usa_order', array('id' => $params['objOrder']->id,  'valid' => $params['objOrder']->valid));

			return $this->display(__FILE__, 'views/templates/hook/order-confirmation.tpl');
		}
	}

	/* PayPal USA Shopping Cart content page hook
	 *
	 * @param $params Array Default PrestaShop parameters sent to the hookShoppingCartExtra() method
	 *
	 * @return HTML content (Template) displaying a PayPal Express Checkout button (if the option has been enabled by the merchant)
	 */

	public function hookShoppingCartExtra($params)
	{
		if (Configuration::get('PAYPAL_USA_EXPRESS_CHECKOUT') == 1 && Configuration::get('PAYPAL_USA_EXP_CHK_SHOPPING_CART'))
		{
			$this->smarty->assign('paypal_usa_action', $this->getModuleLink('paypalusa', 'expresscheckout', array('pp_exp_initial' => 1), Configuration::get('PS_SSL_ENABLED')));
			$this->smarty->assign('paypal_usa_merchant_country_is_mx', (Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code == 'MX'));

			return $this->display(__FILE__, 'views/templates/hook/express-checkout.tpl');
		}
	}

	/* PayPal USA Product page hook
	 *
	 * @param $params Array Default PrestaShop parameters sent to the hookProductFooter() method
	 *
	 * @return HTML content (Template) displaying a PayPal Express Checkout button (if the option has been enabled by the merchant)
	 */

	public function hookProductFooter($params)
	{
		$product_quantity = Product::getQuantity((int)Tools::getValue('id_product'));
		if ($product_quantity == 0)
			return;
		if (Configuration::get('PAYPAL_USA_EXPRESS_CHECKOUT') == 1 && Configuration::get('PAYPAL_USA_EXP_CHK_PRODUCT'))
		{
			$this->smarty->assign('paypal_usa_action', $this->getModuleLink('paypalusa', 'expresscheckout', array('pp_exp_initial' => 1), Configuration::get('PS_SSL_ENABLED')));
			$this->smarty->assign('paypal_usa_merchant_country_is_mx', (Validate::isLoadedObject($this->_shop_country) && $this->_shop_country->iso_code == 'MX'));

			return $this->display(__FILE__, 'views/templates/hook/express-checkout.tpl');
		}
	}

	/* PayPal USA Order Transaction ID update
	 * Attach a PayPal Transaction ID to an existing order (it will be displayed in the Order details section of the Back-office)
	 *
	 * @param $id_order integer Order ID
	 * @param $id_transaction string PayPal Transaction ID
	 */

	public function addTransactionId($id_order, $id_transaction)
	{
		if (version_compare(_PS_VERSION_, '1.5', '>='))
		{
			$new_order = new Order((int)$id_order);
			if (Validate::isLoadedObject($new_order))
			{
				$payment = $new_order->getOrderPaymentCollection();
				if (isset($payment[0]))
				{
					$payment[0]->transaction_id = pSQL($id_transaction);
					$payment[0]->save();
				}
			}
		}
	}

	/* PayPal USA Transaction details update
	 * Attach transactions details to an existing order (it will be displayed in the Order details section of the Back-office)
	 *
	 * @param $type Can be either 'payment' or 'refund' depending on the desired operation
	 * @param $details Array Transaction details
	 *
	 * @return boolean Operation result
	 */

	public function addTransaction($type = 'payment', $details)
	{
		$sandbox_value = $details['source'] == 'advanced' ? Configuration::get('PAYPAL_USA_SANDBOX_ADVANCED') : Configuration::get('PAYPAL_USA_SANDBOX');

		return Db::getInstance()->Execute('
		INSERT INTO '._DB_PREFIX_.'paypal_usa_transaction (type, source, id_shop, id_customer, id_cart, id_order,
		id_transaction, amount, currency, cc_type, cc_exp, cc_last_digits, cvc_check, fee, mode, date_add)
		VALUES (\''.pSQL($type).'\', \''.pSQL($details['source']).'\', '.(int)$details['id_shop'].', '.(int)$details['id_customer'].', '.(int)$details['id_cart'].', '.(int)$details['id_order'].',
		\''.pSQL($details['id_transaction']).'\', \''.(float)$details['amount'].'\', \''.pSQL($details['currency']).'\',
		\''.pSQL($details['cc_type']).'\', \''.pSQL($details['cc_exp']).'\', \''.pSQL($details['cc_last_digits']).'\',
		\''.pSQL($details['cvc_check']).'\', \''.pSQL($details['fee']).'\', \''.($sandbox_value ? 'test' : 'live').'\', NOW())');
	}

	/* PayPal USA PayFlow Link and PayFlow Pro API communication method
	 *
	 * @param $params string Parameters and method to be sent to the PayPal Payment gateway
	 * @param $type Payment gateway type, can be either 'link' or 'pro' (e.g. PayFlow Link or PayFlow Pro)
	 *
	 * @return Array PayPal reponse formatted as an array (Key/value)
	 */

	public function postToPayFlow($params, $type = 'link')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://'.(Configuration::get('PAYPAL_USA_SANDBOX_ADVANCED') ? 'pilot-' : '').($type == 'link' ? 'payflowlink' : 'payflowpro').'.paypal.com');
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'PARTNER='.urlencode(Configuration::get('PAYPAL_USA_MANAGER_PARTNER')).'&VENDOR='.urlencode(Configuration::get('PAYPAL_USA_MANAGER_LOGIN')).
				'&USER='.urlencode(Configuration::get('PAYPAL_USA_MANAGER_USER')).'&PWD='.urlencode(Configuration::get('PAYPAL_USA_MANAGER_PASSWORD')).$params.'&BUTTONSOURCE=PrestashopUS_Cart');
		$response = curl_exec($ch);
		curl_close($ch);
		return $this->_readNvp($response);
	}

	/* PayPal USA PayPal API communication method
	 *
	 * @param $method_name PayPal API method name (e.g. GetBalance, DoExpressCheckoutPayment, etc.)
	 * @param $params string Parameters and method to be sent to the PayPal API
	 *
	 * @return Array PayPal reponse formatted as an array (Key/value) - Can also be a string in case the response only consists in one word
	 */

	public function postToPayPal($method_name, $params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api-3t.'.(Configuration::get('PAYPAL_USA_SANDBOX') ? 'sandbox.' : '').'paypal.com/nvp');
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'METHOD='.urlencode($method_name).'&VERSION=98&PWD='.urlencode(Configuration::get('PAYPAL_USA_API_PASSWORD')).'&USER='.urlencode(Configuration::get('PAYPAL_USA_API_USERNAME')).'&SIGNATURE='.urlencode(Configuration::get('PAYPAL_USA_API_SIGNATURE')).$params.'&BUTTONSOURCE=PrestashopUS_Cart');
		$response = curl_exec($ch);
		curl_close($ch);

		return strpos($response, '=') ? $this->_readNvp($response) : $response;
	}

	/* PayPal USA PayPal API response decoding
	 *
	 * @param $string string PayPal API response
	 *
	 * @return Array PayPal reponse formatted as an array (Key/value)
	 */

	private function _readNvp($string)
	{
		while (Tools::strlen($string))
		{
			$keypos = strpos($string, '=');
			$valuepos = strpos($string, '&') ? strpos($string, '&') : Tools::strlen($string);
			$nvp_array[urldecode(Tools::substr($string, 0, $keypos))] = urldecode(Tools::substr($string, $keypos + 1, $valuepos - $keypos - 1));
			$string = Tools::substr($string, $valuepos + 1, Tools::strlen($string));
		}

		return $nvp_array;
	}

	public function getModuleLink($module, $controller = 'default', array $params = array(), $ssl = null)
	{
		if (version_compare(_PS_VERSION_, '1.5', '<'))
			$link = Tools::getShopDomainSsl(true)._MODULE_DIR_.$module.'/'.$controller.'?'.http_build_query($params);
		else
			$link = $this->context->link->getModuleLink($module, $controller, $params, $ssl);

		return $link;
	}

}
