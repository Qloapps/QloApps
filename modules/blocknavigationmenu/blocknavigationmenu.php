<?php
if (!defined('_PS_VERSION_'))
	exit;

class BlockNavigationMenu extends Module
{
	public function __construct()
	{
		$this->name = 'blocknavigationmenu';
		$this->tab = 'front_office_features';
		$this->version = '1.6.0';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Navigatoion block');
		$this->description = $this->l('Adds a Navigation block.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function hookTop($params)
	{
		$this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/htlnevigationmenu.js');
		$this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/blocknavigation.css');
		return $this->display(__FILE__, 'navigationmenublock.tpl');
	}

	public function install()
	{
		if (!parent::install()
			|| !$this->registerHook('top'))
			return false;
		return true;
	}
}
