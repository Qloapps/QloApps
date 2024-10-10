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

require_once dirname(__FILE__).'/../wktestimonialblock/classes/WkTestimonialBlockDb.php';
require_once dirname(__FILE__).'/../wktestimonialblock/classes/WkHotelTestimonialData.php';
require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';

class WkTestimonialBlock extends Module
{
    public function __construct()
    {
        $this->name = 'wktestimonialblock';
        $this->tab = 'front_office_features';
        $this->version = '1.1.6';
        $this->author = 'Webkul';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Hotel Testimonial');
        $this->description = $this->l('Show Hotel testimonials on home page using this module.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookDisplayHome()
    {
        $this->context->controller->addCSS(_PS_JS_DIR_.'/owl-carousel/assets/owl.carousel.min.css');
        $this->context->controller->addCSS(_PS_JS_DIR_.'/owl-carousel/assets/owl.theme.default.min.css');
        $this->context->controller->addJS(_PS_JS_DIR_.'/owl-carousel/owl.carousel.min.js');

        $this->context->controller->addCSS($this->_path.'/views/css/WkTestimonialBlockFront.css');
        $this->context->controller->addJS($this->_path.'/views/js/WkTestimonialBlockFront.js');

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
        foreach($testimonialsData as &$testimonials) {
            $imgUrl = $this->context->link->getMediaLink(_MODULE_DIR_.$this->name.'/views/img/hotels_testimonials_img/'.$testimonials['id_testimonial_block'].'.jpg');
            if ((bool)Tools::file_get_contents($imgUrl)) {
                $testimonials['img_url'] = $imgUrl;
            } else {
                $testimonials['img_url'] = $this->context->link->getMediaLink(_MODULE_DIR_.$this->name.'/views/img/default-user.jpg');
            }
        }

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
        $objTestimonialBlockDb = new WkTestimonialBlockDb();
        if (!parent::install()
            || !$objTestimonialBlockDb->createTables()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
        ) {
            return false;
        }

        // if module should create demo data during installation
        if (isset($this->populateData) && $this->populateData) {
            $objTestimonialData = new WkHotelTestimonialData();
            if (!$objTestimonialData->insertModuleDemoData()) {
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
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminTestimonialsModuleSetting'));
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
        $objTestimonialBlockDb = new WkTestimonialBlockDb();
        if (!parent::uninstall()
            || !$this->deleteTestimonialUserImage()
            || !$objTestimonialBlockDb->dropTables()
            || !$this->uninstallTab()
            || !$this->deleteConfigKeys()
        ) {
            return false;
        }
        return true;
    }

    public function deleteTestimonialUserImage()
    {
        $objTestimonialData = new WkHotelTestimonialData();
        $testimonialsData = $objTestimonialData->getTestimonialData(1);
        foreach($testimonialsData as &$testimonials) {
            $objTestimonialData = new WkHotelTestimonialData($testimonials['id_testimonial_block']);
            if(Validate::isLoadedObject($objTestimonialData)) {
                $objTestimonialData->deleteImage(true);
            }
        }
        return true;
    }
}
