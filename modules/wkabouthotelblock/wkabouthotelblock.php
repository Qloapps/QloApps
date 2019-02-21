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

require_once dirname(__FILE__).'/define.php';

class WkAboutHotelBlock extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'wkabouthotelblock';
        $this->tab = 'front_office_features';
        $this->version = '1.1.1';
        $this->author = 'webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('About Hotel Block');
        $this->description = $this->l('Now show Block about your hotel using this module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookDisplayDefaultNavigationHook()
    {
        if (Configuration::get('HOTEL_INTERIOR_BLOCK_NAV_LINK')) {
            return $this->display(__FILE__, 'hotelInteriorNaviagtionMenu.tpl');
        }
    }

    public function hookDisplayHome()
    {
        // owl.carousel Plug-in files
        $this->context->controller->addCSS(
            _PS_MODULE_DIR_.'hotelreservationsystem/libs/owl.carousel/assets/owl.carousel.min.css'
        );
        $this->context->controller->addCSS(
            _PS_MODULE_DIR_.'hotelreservationsystem/libs/owl.carousel/assets/owl.theme.default.min.css'
        );
        $this->context->controller->addJS(
            _PS_MODULE_DIR_.'hotelreservationsystem/libs/owl.carousel/owl.carousel.min.js'
        );
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/WkAboutHotelBlockFront.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/WkAboutHotelBlockFront.js');

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
        $objHtlInteriorImg = new WkHotelInteriorImage();
        if (!parent::install()
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
                'displayDefaultNavigationHook',
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
        if (!parent::uninstall()
            || !$this->deleteTables()
            || !$this->deleteConfigKeys()
            || !$this->deleteHotelInterierImg()
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
        $uploadedImg = glob(_PS_MODULE_DIR_.$this->name.'/views/img/hotel_interior/*.jpg');
        if ($uploadedImg) {
            foreach ($uploadedImg as $interiorImg) {
                unlink($interiorImg);
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

    public function deleteTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_interior_image`'
        );
    }
}
