<?php
if (!defined('_PS_VERSION_'))
	exit;

class WkFooterPaymentInfoBlockContainer extends Module
{
	public function __construct()
	{
		$this->name = 'wkfooterpaymentinfoblockcontainer';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Display about hotel block and payment block in footer');
		$this->description = $this->l('Contain short information about hotel and payment gateway accepted by site.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function hookFooter()
	{
		return $this->display(__FILE__, 'footerPaymentInfoBlock.tpl');
	}

	public function install()
	{
		if (!parent::install()
			||!$this->registerHook('footer'))
			return false;
		return true;
	}
}