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

require_once dirname(__FILE__).'/classes/WkFooterPaymentBlockDb.php';
require_once dirname(__FILE__).'/classes/WkFooterPaymentBlockInfo.php';

class WkFooterPaymentBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wkfooterpaymentblock';
        $this->tab = 'front_office_features';
        $this->version = '1.1.6';
        $this->author = 'Webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Footer Payment Accepted Block');
        $this->description = $this->l('Show payment gateways icons in footer.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminFooterPaymentBlockSetting'));
    }

    public function hookFooter($params)
    {
        $objPaymentBlockInfo = new WkFooterPaymentBlockInfo();
        if ($allPaymentBlocks = $objPaymentBlockInfo->getAllPaymentBlocks(1, 'position')) {
            $this->context->smarty->assign('allPaymentBlocks', $allPaymentBlocks);
            $this->context->controller->addCSS($this->_path.'/views/css/wkFooterPaymentBlockFront.css');
            return $this->display(__FILE__, 'wkFooterPaymentBlock.tpl');
        }
    }

    public function hookdisplayFooterPaymentInfo($params)
    {
        return $this->hookFooter($params);
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those controllers...
        $this->installTab('AdminFooterPaymentBlockSetting', 'Manage Footer Payment Block');
        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int)Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = -1;
        }

        $tab->module = $this->name;
        $res = $tab->add();
        //Set position of the Hotel reservation System Tab to the position wherewe want...
        return $res;
    }

    public function install()
    {
        $objFooterPaymentBlockDb = new WkFooterPaymentBlockDb();
        if (!parent::install()
            || !$objFooterPaymentBlockDb->createTables()
            ||!$this->registerHook('displayFooterPaymentInfo')
            || !$this->callInstallTab()
            || !$this->insertDefaultModuleData()
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        $objFooterPaymentBlockDb = new WkFooterPaymentBlockDb();
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$objFooterPaymentBlockDb->dropTables()
        ) {
            return false;
        }
        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }

    public function insertDefaultModuleData()
    {
        $paymentNames = array(
            '1' => $this->l('Visa'),
            '2' => $this->l('American Express'),
            '3' => $this->l('MasterCard'),
            '4' => $this->l('Paypal'),
        );
        foreach ($paymentNames as $key => $paymentName) {
            $objPaymentBlockInfo = new WkFooterPaymentBlockInfo();
            $objPaymentBlockInfo->name = $paymentName;
            $objPaymentBlockInfo->active = 1;
            $objPaymentBlockInfo->position = WkFooterPaymentBlockInfo::getHigherPosition();
            if ($objPaymentBlockInfo->save()) {
                $imgName = $objPaymentBlockInfo->id.'.jpg';
                $imgPath = _PS_MODULE_DIR_.$this->name.'/views/img/payment_img/'.$imgName;
                if (file_exists($imgPath)) {
                    unlink($imgPath);
                }
                ImageManager::resize(
                    _PS_MODULE_DIR_.$this->name.'/views/img/dummy_img/'.$key.'.png',
                    $imgPath
                );
            }
        }
        return true;
    }
}
