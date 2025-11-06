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

require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
require_once dirname(__FILE__).'/../wkhotelfeaturesblock/classes/WkHotelFeaturesBlockDb.php';
require_once dirname(__FILE__).'/../wkhotelfeaturesblock/classes/WkHotelFeaturesData.php';

class WkHotelFeaturesBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wkhotelfeaturesblock';
        $this->tab = 'front_office_features';
        $this->version = '2.0.8';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Hotel Features');
        $this->description = $this->l('Show Hotel Amenities on the home page using this module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookDisplayHome()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/wkHotelFeaturesBlockFront.css');
        $this->context->controller->addJS($this->_path.'/views/js/wkHotelFeaturesBlockFront.js');

        $objFeaturesData = new WkHotelFeaturesData();
        $hotelAmenities = $objFeaturesData->getHotelAmenities(1);

        $HOTEL_AMENITIES_HEADING = Configuration::get('HOTEL_AMENITIES_HEADING', $this->context->language->id);
        $HOTEL_AMENITIES_DESCRIPTION = Configuration::get('HOTEL_AMENITIES_DESCRIPTION', $this->context->language->id);

        $this->context->smarty->assign(
            array(
                'HOTEL_AMENITIES_HEADING' => $HOTEL_AMENITIES_HEADING,
                'HOTEL_AMENITIES_DESCRIPTION' => $HOTEL_AMENITIES_DESCRIPTION,
                'hotelAmenities' => $hotelAmenities,
            )
        );
        return $this->display(__FILE__, 'hotelfeaturescontent.tpl');
    }

    /**
     * If admin add any language then an entry will add in defined $lang_tables array's lang table same as prestashop
     * @param array $params
     */
    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($newIdLang = $params['object']->id) {
            $langTables = array('htl_features_block_data');
            //If Admin update new language when we do entry in module all lang tables.
            HotelHelper::updateLangTables($newIdLang, $langTables);

            // update configuration keys
            $configKeys = array(
                'HOTEL_AMENITIES_HEADING',
                'HOTEL_AMENITIES_DESCRIPTION',
            );
            HotelHelper::updateConfigurationLangKeys($newIdLang, $configKeys);
        }
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those Controllers...
        $this->installTab('AdminFeaturesModuleSetting', 'Hotel Amenities Configurations');
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
        $objHotelFeaturesBlockDb = new WkHotelFeaturesBlockDb();
        if (!parent::install()
            || !$objHotelFeaturesBlockDb->createTables()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
        ) {
            return false;
        }

        // if module should create demo data during installation
        if (isset($this->populateData) && $this->populateData) {
            $objHotelFeaturesData = new WkHotelFeaturesData();
            if (!$objHotelFeaturesData->insertModuleDemoData()) {
                return false;
            }
        } else {
            Tools::deleteDirectory($this->local_path.'views/img/dummy_img');
        }

        return true;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array (
                'displayHome',
                'displayFooterExploreSectionHook',
                'actionObjectLanguageAddAfter'
            )
        );
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminFeaturesModuleSetting'));
    }

    public function uninstall()
    {
        $objHotelFeaturesBlockDb = new WkHotelFeaturesBlockDb();
        if (!parent::uninstall()
            || !$this->deleteHotelAmenityImg()
            || !$this->uninstallTab()
            || !$objHotelFeaturesBlockDb->dropTables()
            || !$this->deleteConfigKeys()
        ) {
            return false;
        }

        return true;
    }

    public function deleteHotelAmenityImg()
    {
        $objFeaturesData = new WkHotelFeaturesData();
        $hotelAmenities = $objFeaturesData->getHotelAmenities();
        foreach ($hotelAmenities as $amenity) {
            $objFeaturesData = new WkHotelFeaturesData($amenity['id_features_block']);
            if (Validate::isLoadedObject($objFeaturesData)) {
                $objFeaturesData->deleteImage(true);
            }
        }
        return true;
    }

    public function deleteConfigKeys()
    {
        $configKeys = array(
            'HOTEL_AMENITIES_HEADING',
            'HOTEL_AMENITIES_DESCRIPTION'
        );
        foreach ($configKeys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
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
}
