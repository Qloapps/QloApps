<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if ((bool)Configuration::get('PS_MOBILE_DEVICE'))
	require_once(_PS_MODULE_DIR_ . '/mobile_theme/Mobile_Detect.php');

// Retro 1.3, 'class_exists' cause problem with autoload...
if (version_compare(_PS_VERSION_, '1.4', '<'))
{
	// Not exist for 1.3
	class Shop extends ObjectModel
	{
		public $id = 1;
		public $id_shop_group = 1;
		
		public function __construct()
		{
		}


		public static function getShops()
		{
			return array(
				array('id_shop' => 1, 'name' => 'Default shop')
			);
		}

		public static function getCurrentShop()
		{
				return 1;
		}
	}

	class Logger
	{
		public static function AddLog($message, $severity = 2)
		{
			$fp = fopen(dirname(__FILE__).'/../logs.txt', 'a+');
			fwrite($fp, '['.(int)$severity.'] '.Tools::safeOutput($message));
			fclose($fp);
		}
	}

}

// Not exist for 1.3 and 1.4
class Context
{
	/**
	 * @var Context
	 */
	protected static $instance;

	/**
	 * @var Cart
	 */
	public $cart;

	/**
	 * @var Customer
	 */
	public $customer;

	/**
	 * @var Cookie
	 */
	public $cookie;

	/**
	 * @var Link
	 */
	public $link;

	/**
	 * @var Country
	 */
	public $country;

	/**
	 * @var Employee
	 */
	public $employee;

	/**
	 * @var Controller
	 */
	public $controller;

	/**
	 * @var Language
	 */
	public $language;

	/**
	 * @var Currency
	 */
	public $currency;

	/**
	 * @var AdminTab
	 */
	public $tab;

	/**
	 * @var Shop
	 */
	public $shop;

	/**
	 * @var Smarty
	 */
	public $smarty;

	/**
	 * @ var Mobile Detect
	 */
	public $mobile_detect;

	/**
	 * @var boolean|string mobile device of the customer
	 */
	protected $mobile_device;

	public function __construct()
	{
		global $cookie, $cart, $smarty, $link;

		$this->tab = null;

		$this->cookie = $cookie;
		$this->cart = $cart;
		$this->smarty = $smarty;
		$this->link = $link;

		$this->controller = new ControllerBackwardModule();
		if (is_object($cookie))
		{
			$this->currency = new Currency((int)$cookie->id_currency);
			$this->language = new Language((int)$cookie->id_lang);
			$this->country = new Country((int)$cookie->id_country);
			$this->customer = new CustomerBackwardModule((int)$cookie->id_customer);
			$this->employee = new Employee((int)$cookie->id_employee);
		}
		else
		{
			$this->currency = null;
			$this->language = null;
			$this->country = null;
			$this->customer = null;
			$this->employee = null;
		}

		$this->shop = new ShopBackwardModule();

		if ((bool)Configuration::get('PS_MOBILE_DEVICE'))
			$this->mobile_detect = new Mobile_Detect();
	}

	public function getMobileDevice()
	{
		if (is_null($this->mobile_device))
		{
			$this->mobile_device = false;
			if ($this->checkMobileContext())
			{
				switch ((int)Configuration::get('PS_MOBILE_DEVICE'))
				{
					case 0: // Only for mobile device
						if ($this->mobile_detect->isMobile() && !$this->mobile_detect->isTablet())
							$this->mobile_device = true;
						break;
					case 1: // Only for touchpads
						if ($this->mobile_detect->isTablet() && !$this->mobile_detect->isMobile())
							$this->mobile_device = true;
						break;
					case 2: // For touchpad or mobile devices
						if ($this->mobile_detect->isMobile() || $this->mobile_detect->isTablet())
							$this->mobile_device = true;
						break;
				}
			}
		}

		return $this->mobile_device;
	}

	protected function checkMobileContext()
	{
		return isset($_SERVER['HTTP_USER_AGENT'])
			&& (bool)Configuration::get('PS_MOBILE_DEVICE')
			&& !Context::getContext()->cookie->no_mobile;
	}

	/**
	 * Get a singleton context
	 *
	 * @return Context
	 */
	public static function getContext()
	{
		if (!isset(self::$instance))
			self::$instance = new Context();
		return self::$instance;
	}

	/**
	 * Clone current context
	 *
	 * @return Context
	 */
	public function cloneContext()
	{
		return clone($this);
	}

	/**
	 * @return int Shop context type (Shop::CONTEXT_ALL, etc.)
	 */
	public static function shop()
	{
		if (!self::$instance->shop->getContextType())
			return ShopBackwardModule::CONTEXT_ALL;
		return self::$instance->shop->getContextType();
	}
}

/**
 * Class Shop for Backward compatibility
 */
class ShopBackwardModule extends Shop
{
	const CONTEXT_ALL = 1;

	public $id = 1;
	public $id_shop_group = 1;
	
	
	public function getContextType()
	{
		return ShopBackwardModule::CONTEXT_ALL;
	}

	// Simulate shop for 1.3 / 1.4
	public function getID()
	{
		return 1;
	}
	
	/**
	 * Get shop theme name
	 *
	 * @return string
	 */
	public function getTheme()
	{
		return _THEME_NAME_;
	}

	public function isFeatureActive()
	{
		return false;
	}
}

/**
 * Class Controller for a Backward compatibility
 * Allow to use method declared in 1.5
 */
class ControllerBackwardModule
{
	/**
	 * @param $js_uri
	 * @return void
	 */
	public function addJS($js_uri)
	{
		Tools::addJS($js_uri);
	}

	/**
	 * @param $css_uri
	 * @param string $css_media_type
	 * @return void
	 */
	public function addCSS($css_uri, $css_media_type = 'all')
	{
		Tools::addCSS($css_uri, $css_media_type);
	}

	public function addJquery()
	{
		if (_PS_VERSION_ < '1.5')
			$this->addJS(_PS_JS_DIR_.'jquery/jquery-1.4.4.min.js');
		elseif (_PS_VERSION_ >= '1.5')
			$this->addJS(_PS_JS_DIR_.'jquery/jquery-1.7.2.min.js');
	}

}

/**
 * Class Customer for a Backward compatibility
 * Allow to use method declared in 1.5
 */
class CustomerBackwardModule extends Customer
{
	public $logged = false; 
	/**
	 * Check customer informations and return customer validity
	 *
	 * @since 1.5.0
	 * @param boolean $with_guest
	 * @return boolean customer validity
	 */
	public function isLogged($with_guest = false)
	{
		if (!$with_guest && $this->is_guest == 1)
			return false;

		/* Customer is valid only if it can be load and if object password is the same as database one */
		if ($this->logged == 1 && $this->id && Validate::isUnsignedId($this->id) && Customer::checkPassword($this->id, $this->passwd))
			return true;
		return false;
	}
}
