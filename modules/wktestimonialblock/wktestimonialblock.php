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

require_once dirname(__FILE__).'/../wktestimonialblock/classes/WkHotelTestimonialData.php';
require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';

class WkTestimonialBlock extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'wktestimonialblock';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Hotel Testimonial');
        $this->description = $this->l('Show Hotel testimonials on home page using this module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookDisplayAddModuleSettingLink()
    {
        $hrefTestimonialsConf = $this->context->link->getAdminLink('AdminTestimonialsModuleSetting');
        $this->context->smarty->assign('testimonials_setting_link', $hrefTestimonialsConf);
        return $this->display(__FILE__, 'hotelTestimonialSettingLink.tpl');
    }

    public function hookDisplayHome()
    {
        // These files are already included in "wkabouthotelblock" module
        if (!(Module::isInstalled('wkabouthotelblock') && Module::isEnabled('wkabouthotelblock'))) {
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
        }
        /*---- Module Files ----*/
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/WkTestimonialBlockFront.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/WkTestimonialBlockFront.js');

        $HOTEL_TESIMONIAL_BLOCK_HEADING = Configuration::get(
            'HOTEL_TESIMONIAL_BLOCK_HEADING',
            $this->context->language->id
        );
        $HOTEL_TESIMONIAL_BLOCK_CONTENT = Configuration::get(
            'HOTEL_TESIMONIAL_BLOCK_CONTENT',
            $this->context->language->id
        );

        $objTestimonialData = new WkHotelTestimonialData();
        $testimonialsData = $objTestimonialData->getTestimonialData(1);
        $this->context->smarty->assign(
            array(
                'HOTEL_TESIMONIAL_BLOCK_HEADING' => $HOTEL_TESIMONIAL_BLOCK_HEADING,
                'HOTEL_TESIMONIAL_BLOCK_CONTENT' => $HOTEL_TESIMONIAL_BLOCK_CONTENT,
                'testimonials_data' => $testimonialsData,
                'ps_module_dir' => _PS_MODULE_DIR_,
            )
        );
        return $this->display(__FILE__, 'wktestimonialblock.tpl');
    }

    public function hookDisplayDefaultNavigationHook()
    {
        return $this->display(__FILE__, 'hotelTestimonialNaviagtionMenu.tpl');
    }

    public function hookDisplayFooterExploreSectionHook()
    {
        return $this->display(__FILE__, 'hotelTestimonialFooterExploreLink.tpl');
    }

    /**
     * If admin add any language then an entry will add in defined $lang_tables array's lang table same as prestashop
     * @param array $params
     */
    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($newIdLang = $params['object']->id) {
            $langTables = array('htl_testimonials_block_data');
            //If Admin update new language when we do entry in module all lang tables.
            HotelHelper::updateLangTables($newIdLang, $langTables);

            // update configuration keys
            $configKeys = array(
                'HOTEL_TESIMONIAL_BLOCK_HEADING',
                'HOTEL_TESIMONIAL_BLOCK_CONTENT',
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
        if (!parent::install()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
            || !WkHotelTestimonialData::insertModuleDemoData()
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
                'displayDefaultNavigationHook',
                'actionObjectLanguageAddAfter'
            )
        );
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those controllers...
        $this->installTab('AdminTestimonialsModuleSetting', 'Testimonial configuration');
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
        //Set position of the Hotel reservation System Tab to the position where we want...
        return $res;
    }

    public function deleteConfigKeys()
    {
        $configVars = array(
            'HOTEL_TESIMONIAL_BLOCK_HEADING',
            'HOTEL_TESIMONIAL_BLOCK_CONTENT'
        );
        foreach ($configVars as $key) {
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
            `'._DB_PREFIX_.'htl_testimonials_block_data`,
            `'._DB_PREFIX_.'htl_testimonials_block_data_lang`'
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

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->deleteTables()
            || !$this->uninstallTab()
            || !$this->deleteConfigKeys()
            || !$this->deleteTestimonialUserImage()
        ) {
            return false;
        }
        return true;
    }

    public function deleteTestimonialUserImage()
    {
        if ($uploadedImg = glob(_PS_MODULE_DIR_.$this->name.'/views/img/hotels_testimonials_img/*.jpg')) {
            foreach ($uploadedImg as $img) {
                unlink($img);
            }
        }
        return true;
    }
}
