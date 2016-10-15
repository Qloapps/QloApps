<?php
if (!defined('_PS_VERSION_'))
	exit;

class WkFooterLangCurrencyBlock extends Module
{
	public function __construct()
	{
		$this->name = 'wkfooterlangcurrencyblock';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Display Language and Currency Block');
		$this->description = $this->l('Contains language and currency block in footer.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function hookFooter()
	{
		return $this->display(__FILE__, 'footerMostLeftBlock.tpl');
	}

	public function install()
	{
		if (!parent::install()
			||!$this->registerHook('footer'))
			return false;
		return true;
	}
}