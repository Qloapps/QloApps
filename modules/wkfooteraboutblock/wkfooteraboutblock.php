<?php
if (!defined('_PS_VERSION_'))
	exit;

class WkFooterAboutBlock extends Module
{
	public function __construct()
	{
		$this->name = 'wkfooteraboutblock';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Footer About Hotel Block');
		$this->description = $this->l('Show About Hotel block in footer.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function hookFooter($params)
	{
    	$this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkFooterAboutBlockFront.css');
		$this->context->smarty->assign(array('WK_HTL_SHORT_DESC' => Configuration::get('WK_HTL_SHORT_DESC'),
                                            ));
		return $this->display(__FILE__, 'wkFooterAboutBlock.tpl');
	}

	public function hookDisplayFooterMostLeftBlock($params)
	{
		$languages = Language::getLanguages(true, $this->context->shop->id);
		$currencies = Currency::getCurrencies(false, true);
		if ((count($languages) <= 1) && (count($currencies) <= 1)) {
			return $this->hookFooter($params);
		}
	}

	public function hookdisplayFooterPaymentInfo($params)
	{
		$languages = Language::getLanguages(true, $this->context->shop->id);
		$currencies = Currency::getCurrencies(false, true);
		if ((count($languages) > 1) || (count($currencies) > 1)) {
			return $this->hookFooter($params);
		}
	}

	public function install()
	{
		if (!parent::install()
			||!$this->registerHook('displayFooterMostLeftBlock')
			||!$this->registerHook('displayFooterPaymentInfo')
			)
			return false;
		return true;
	}
}