<?php
if (!defined('_PS_VERSION_'))
	exit;

class WkFooterPaymentBlock extends Module
{
	public function __construct()
	{
		$this->name = 'wkfooterpaymentblock';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Footer Payment Accepted Block');
		$this->description = $this->l('Show payment gateways icons in footer.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function hookFooter($params)
	{
    	$this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkFooterPaymentBlockFront.css');
		return $this->display(__FILE__, 'wkFooterPaymentBlock.tpl');
	}

	public function hookdisplayFooterPaymentInfo($params)
	{
		return $this->hookFooter($params);
	}

	public function install()
	{
		if (!parent::install()
			||!$this->registerHook('displayFooterPaymentInfo')
			)
			return false;
		return true;
	}
}