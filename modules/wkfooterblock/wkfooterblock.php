<?php
if (!defined('_PS_VERSION_'))
	exit;

class WkFooterBlock extends Module
{
	public function __construct()
	{
		$this->name = 'wkfooterblock';
		$this->tab = 'front_office_features';
		$this->version = '1.6.1';
		$this->author = 'webkul';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Pages Footer block');
		$this->description = $this->l('Shows footer content.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function hookFooter($params)
	{
    	$redirect_link_terms = $this->context->link->getCMSLink(new CMS(3, $this->context->language->id), null, $this->context->language->id);

		$redirect_link_about = $this->context->link->getCMSLink(new CMS(4, $this->context->language->id), null, $this->context->language->id);

		$this->context->smarty->assign('redirect_link_terms', $redirect_link_terms);
		$this->context->smarty->assign('redirect_link_about', $redirect_link_about);
		$this->context->smarty->assign('hotel_establish_year', Configuration::get('WK_HTL_ESTABLISHMENT_YEAR'));
		$this->context->smarty->assign('hotel_chain_name', Configuration::get('WK_HTL_CHAIN_NAME'));
		$this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/wkfooterblock.js');
		$this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/hotel-footer.css');
		return $this->display(__FILE__, 'wkfooterblock.tpl');
	}

	public function install()
	{
		if (!parent::install()
			||!$this->registerHook('footer'))
			return false;
		return true;
	}
}
