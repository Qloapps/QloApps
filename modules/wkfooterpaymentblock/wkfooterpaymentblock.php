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

require_once dirname(__FILE__).'/classes/WkFooterPaymentBlockInfo.php';

class WkFooterPaymentBlock extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct()
    {
        $this->name = 'wkfooterpaymentblock';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Footer Payment Accepted Block');
        $this->description = $this->l('Show payment gateways icons in footer.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookFooter($params)
    {
        $objPaymentBlockInfo = new WkFooterPaymentBlockInfo();
        if ($allPaymentBlocks = $objPaymentBlockInfo->getAllPaymentBlocks(1, 'position')) {
            $this->context->smarty->assign('allPaymentBlocks', $allPaymentBlocks);
            $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/wkFooterPaymentBlockFront.css');
            return $this->display(__FILE__, 'wkFooterPaymentBlock.tpl');
        }
    }

    public function hookdisplayFooterPaymentInfo($params)
    {
        return $this->hookFooter($params);
    }

    public function hookDisplayAddModuleSettingLink()
    {
        $footerPaymentBlockLink = $this->context->link->getAdminLink('AdminFooterPaymentBlockSetting');
        $this->context->smarty->assign('footerPaymentBlockLink', $footerPaymentBlockLink);

        return $this->display(__FILE__, 'footerPaymentSettingLink.tpl');
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
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) {
            return false;
        }

        $sql = str_replace(array('PREFIX_',  'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            if ($query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }
        if (!parent::install()
            ||!$this->registerHook('displayFooterPaymentInfo')
            ||!$this->registerHook('displayAddModuleSettingLink')
            || !$this->callInstallTab()
            || !$this->insertDefaultModuleData()
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$this->dropTables()
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

    private function dropTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS `'._DB_PREFIX_.'htl_footer_payment_block_info`'
        );
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
