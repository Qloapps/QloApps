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

class WkFooterPaymentInfoBlockContainer extends Module
{
    public function __construct()
    {
        $this->name = 'wkfooterpaymentinfoblockcontainer';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'Webkul';
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
            ||!$this->registerHook('footer')
        ) {
            return false;
        }
        return true;
    }
}
