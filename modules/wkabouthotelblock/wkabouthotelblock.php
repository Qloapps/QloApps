<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/define.php';

class WkAboutHotelBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wkabouthotelblock';
        $this->tab = 'front_office_features';
        $this->version = '1.1.7';
        $this->author = 'Webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('About Hotel Block');
        $this->description = $this->l('Now show Block about your hotel using this module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookDisplayHome()
    {
        $this->context->controller->addCSS(_PS_JS_DIR_.'owl-carousel/assets/owl.carousel.min.css');
        $this->context->controller->addCSS(_PS_JS_DIR_.'owl-carousel/assets/owl.theme.default.min.css');
        $this->context->controller->addJS(_PS_JS_DIR_.'owl-carousel/owl.carousel.min.js');
        $this->context->controller->addCSS($this->_path.'/views/css/WkAboutHotelBlockFront.css');
        $this->context->controller->addJS($this->_path.'/views/js/WkAboutHotelBlockFront.js');

        $HOTEL_INTERIOR_HEADING = Configuration::get('HOTEL_INTERIOR_HEADING', $this->context->language->id);
        $HOTEL_INTERIOR_DESCRIPTION = Configuration::get('HOTEL_INTERIOR_DESCRIPTION', $this->context->language->id);

        $objHtlInteriorImg = new WkHotelInteriorImage();
        $InteriorImg = $objHtlInteriorImg->getHotelInteriorImg(1);

        $this->context->smarty->assign(
            array(
                'HOTEL_INTERIOR_HEADING' => $HOTEL_INTERIOR_HEADING,
                'HOTEL_INTERIOR_DESCRIPTION' => $HOTEL_INTERIOR_DESCRIPTION,
                'InteriorImg' => $InteriorImg,
            )
        );

        return $this->display(__FILE__, 'hotelInteriorBlock.tpl');
    }

    public function hookDisplayAddModuleSettingLink()
    {
        return $this->display(__FILE__, 'aboutHotelBlockModuleSetting.tpl');
    }

    /**
     * If admin add any language then an entry will add in defined $lang_tables array's lang table same as prestashop
     * @param array $params
     */
    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($newIdLang = $params['object']->id) {
            $configKeys = array(
                'HOTEL_INTERIOR_HEADING',
                'HOTEL_INTERIOR_DESCRIPTION',
            );
            HotelHelper::updateConfigurationLangKeys($newIdLang, $configKeys);
        }
    }

    public function install()
    {
        $objAboutHotelBlockDb = new WkAboutHotelBlockDb();
        $objHtlInteriorImg = new WkHotelInteriorImage();
        if (!parent::install()
            || !$objAboutHotelBlockDb->createTables()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
            || !$objHtlInteriorImg->insertModuleDemoData()
        ) {
            return false;
        }
        return true;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'displayHome',
                'displayFooterExploreSectionHook',
                'displayAddModuleSettingLink',
                'actionObjectLanguageAddAfter'
            )
        );
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those controllers...
        $this->installTab('AdminAboutHotelBlockSetting', 'Hotel Description Configuration');
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

    public function uninstall()
    {
        $objAboutHotelBlockDb = new WkAboutHotelBlockDb();
        if (!parent::uninstall()
            || !$this->deleteHotelInterierImg()
            || !$objAboutHotelBlockDb->dropTables()
            || !$this->deleteConfigKeys()
            || !$this->uninstallTab()
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

    public function deleteHotelInterierImg()
    {
        $objHtlInteriorImg = new WkHotelInteriorImage();
        $InteriorImgs = $objHtlInteriorImg->getHotelInteriorImg();
        foreach($InteriorImgs as $key => $interiorImg) {
            $objHtlInteriorImg = new WkHotelInteriorImage($interiorImg['id_interior_image']);
            if (Validate::isLoadedObject($objHtlInteriorImg)) {
                $objHtlInteriorImg->deleteImage(true);
            }
        }
        return true;
    }

    public function deleteConfigKeys()
    {
        $var = array('HOTEL_INTERIOR_HEADING', 'HOTEL_INTERIOR_DESCRIPTION');
        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }
        return true;
    }
}
