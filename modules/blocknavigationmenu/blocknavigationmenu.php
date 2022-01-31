<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/../blocknavigationmenu/classes/WkNavigationRequiredClasses.php';
class blocknavigationmenu extends Module
{
    public function __construct()
    {
        $this->name = 'blocknavigationmenu';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Navigation block');
        $this->description = $this->l('Adds a navigation block at top and footer section.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookTop($params)
    {
        return $this->hookDisplayTopSubSecondaryBlock($params);
    }

    public function hookDisplayTopSubSecondaryBlock($params)
    {
        Media::addJsDef(
            array(
                'currentPage' => Tools::getValue('controller')
            )
        );
        $this->context->controller->addJS($this->_path.'/views/js/htlnevigationmenu.js');
        $this->context->controller->addCSS($this->_path.'/views/css/blocknavigation.css');

        $objCustomNavigationLink = new WkCustomNavigationLink();
        if ($navigationLinks = $objCustomNavigationLink->getCustomNavigationLinks(1, false, 1)) {
            foreach ($navigationLinks as &$link) {
                if (!$link['id_cms'] && !$link['is_custom_link']) {
                    $link['link'] = $this->context->link->getPageLink($link['link']);
                }
            }
            $this->context->smarty->assign('navigation_links', $navigationLinks);
        }
        return $this->display(__FILE__, 'navigationMenuBlock.tpl');
    }

    public function hookFooter()
    {
        if (Configuration::get('WK_SHOW_FOOTER_NAVIGATION_BLOCK')) {
            $objCustomNavigationLink = new WkCustomNavigationLink();
            if ($navigationLinks = $objCustomNavigationLink->getCustomNavigationLinks(1, false, 2, 1)) {
                foreach ($navigationLinks as &$link) {
                    if (!$link['id_cms'] && !$link['is_custom_link']) {
                        $link['link'] = $this->context->link->getPageLink($link['link']);
                    }
                }
                $this->context->controller->addCSS($this->_path.'/views/css/wkFooterNavigationBlock.css');
                $this->context->smarty->assign('navigation_links', $navigationLinks);
                return $this->display(__FILE__, 'wkFooterNavigationBlock.tpl');
            }
        }
    }

    /**
     * If admin add any language then an entry will add in defined $lang_tables array's lang table same as prestashop
     * @param array $params
     */
    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($newIdLang = $params['object']->id) {
            $langTables = array('htl_custom_navigation_link');
            //If Admin update new language when we do entry in module all lang tables.
            HotelHelper::updateLangTables($newIdLang, $langTables);
        }
    }

    public function hookDisplayAddModuleSettingLink()
    {
        $this->context->smarty->assign(
            'custom_navigation_link_setting_url',
            $this->context->link->getAdminLink('AdminCustomNavigationLinkSetting')
        );
        return $this->display(__FILE__, 'customNavigationLinkSetting.tpl');
    }

    public function install()
    {
        $objModuleDb = new WkBlockNavigationMenuDb();
        $objCustomNavigationLink = new WkCustomNavigationLink();
        if (!parent::install()
            || !$objModuleDb->createTables()
            || !$this->callInstallTab()
            || !$this->registerModuleHooks()
            || !Configuration::updateValue('WK_SHOW_FOOTER_NAVIGATION_BLOCK', 1)
            || !$objCustomNavigationLink->insertDemoData()
        ) {
            return false;
        }
        return true;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array (
                'footer',
                'displayAddModuleSettingLink',
                'actionObjectLanguageAddAfter',
                'displayDefaultNavigationHook',
                'displayNavigationHook',
                'top',
            )
        );
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those Controllers...
        $this->installTab('AdminCustomNavigationLinkSetting', 'Manage Custom Navigation Links');
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
        return $res;
    }

    public function uninstall()
    {
        $objModuleDb = new WkBlockNavigationMenuDb();
        if (!parent::uninstall()
            || !$objModuleDb->dropTables()
            || !$this->uninstallTab()
            || !$this->deleteConfigKeys()
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

    public function deleteConfigKeys()
    {
        return Configuration::deleteByName('WK_SHOW_FOOTER_NAVIGATION_BLOCK');
    }
}
