<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
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
