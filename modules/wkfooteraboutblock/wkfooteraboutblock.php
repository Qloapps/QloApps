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

class WkFooterAboutBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wkfooteraboutblock';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
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
        $this->context->smarty->assign(
            array('WK_HTL_SHORT_DESC' => Configuration::get('WK_HTL_SHORT_DESC', $this->context->language->id))
        );

        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkFooterAboutBlockFront.css');
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
        ) {
            return false;
        }
        return true;
    }
}
