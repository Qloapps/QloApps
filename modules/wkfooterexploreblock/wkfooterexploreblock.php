<?php
if (!defined('_PS_VERSION_'))
	exit;

class WkFooterExploreBlock extends Module
{
	public function __construct()
	{
		$this->name = 'wkfooterexploreblock';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Footer Explore Block');
		$this->description = $this->l('Show Explore block in footer.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function hookFooter()
	{
    	$this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkFooterExploreBlockFront.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/wkFooterExploreBlockFront.js');
		
		return $this->display(__FILE__, 'wkFooterExploreBlock.tpl');
	}

	public function install()
	{
		if (!parent::install()
			||!$this->registerHook('footer'))
			return false;
		return true;
	}
}