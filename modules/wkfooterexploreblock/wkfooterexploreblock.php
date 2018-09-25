<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class WkFooterExploreBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wkfooterexploreblock';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
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
            ||!$this->registerHook('footer')) {
            return false;
        }
        return true;
    }
}
