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

require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
require_once dirname(__FILE__).'/../wkhotelfeaturesblock/classes/WkHotelFeaturesData.php';

class WkHotelFeaturesBlock extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'wkhotelfeaturesblock';
        $this->tab = 'front_office_features';
        $this->version = '2.0.4';
        $this->author = 'webkul';
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

    public function hookDisplayAddModuleSettingLink()
    {
        $hrefFeaturesConf = $this->context->link->getAdminLink('AdminFeaturesModuleSetting');
        $this->context->smarty->assign('features_setting_link', $hrefFeaturesConf);
        return $this->display(__FILE__, 'hotelFeatureSettingLink.tpl');
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
        $objFeaturesData = new WkHotelFeaturesData();
        if (!parent::install()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
            || !$objFeaturesData->insertModuleDemoData()
        ) {
            return false;
        }
        return true;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array (
                'displayHome',
                'displayFooterExploreSectionHook',
                'displayAddModuleSettingLink',
                'actionObjectLanguageAddAfter'
            )
        );
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->deleteHotelAmenityImg()
            || !$this->uninstallTab()
            || !$this->deleteTables()
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

    public function deleteTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_features_block_data`,
            `'._DB_PREFIX_.'htl_features_block_data_lang`'
        );
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
