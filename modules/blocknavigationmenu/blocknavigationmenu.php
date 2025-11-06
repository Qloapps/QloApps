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

require_once dirname(__FILE__).'/../blocknavigationmenu/classes/WkNavigationRequiredClasses.php';
class blocknavigationmenu extends Module
{
    public function __construct()
    {
        $this->name = 'blocknavigationmenu';
        $this->tab = 'front_office_features';
        $this->version = '1.1.4';
        $this->author = 'Webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Navigation block');
        $this->description = $this->l('Adds a navigation block at top and footer section.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminCustomNavigationLinkSetting'));
    }

    public function hookTop($params)
    {
        return $this->hookDisplayTopSubSecondaryBlock($params);
    }

    public function hookDisplayTopSubSecondaryBlock($params)
    {
        Media::addJsDef(array('currentPage' => $this->context->controller->php_self));
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

    public function hookActionObjectCMSUpdateAfter($params)
    {
        $idCMS = $params['object']->id;
        if (!$params['object']->active) {
            $objCustomNavigationLink = new WkCustomNavigationLink();
            // Disabling depended Navigation link
            $objCustomNavigationLink->disableNavigationLinksByIdCMS($idCMS);
        }
    }

    public function hookActionObjectCMSDeleteAfter($params)
    {
        $idCMS = $params['object']->id;
        $objCustomNavigationLink = new WkCustomNavigationLink();
        // Deleting depended Navigation link
        $objCustomNavigationLink->deleteNavigationLinksByIdCMS($idCMS);
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
            || !$objCustomNavigationLink->insertDemoData(isset($this->populateData) ? $this->populateData : null)
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
                'actionObjectLanguageAddAfter',
                'actionObjectCMSUpdateAfter',
                'actionObjectCMSDeleteAfter',
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
