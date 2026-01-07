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

require_once 'classes/QhoHotelOfferingDb.php';
require_once 'classes/QhoHotelOffering.php';

// @todo: add these in the cleaner module
// to delete the dummy_images
class QloHotelOfferings extends Module
{
    public function __construct()
    {
        $this->name = 'qlohotelofferings';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Webkul';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Display Offerings');
        $this->description = $this->l('This module allows you to display offerings both globally and on a hotel-wise basis.');

        $this->qloapps_versions_compliancy = array('min' => '1.7.0', 'max' => _QLOAPPS_VERSION_);
    }

    public function hookDisplayHome()
    {
        $this->context->controller->addCSS(_PS_JS_DIR_.'/owl-carousel/assets/owl.carousel.min.css');
        $this->context->controller->addCSS(_PS_JS_DIR_.'/owl-carousel/assets/owl.theme.default.min.css');
        $this->context->controller->addJS(_PS_JS_DIR_.'/owl-carousel/owl.carousel.min.js');

        $this->context->controller->addJS($this->_path.'/views/js/hotelOffering.js');

        $OFFERING_BLOCK_HEADING = Configuration::get(
            'OFFERING_BLOCK_HEADING',
            $this->context->language->id
        );
        $OFFERING_BLOCK_CONTENT = Configuration::get(
            'OFFERING_BLOCK_CONTENT',
            $this->context->language->id
        );

        $objHotelOffering = new QhoHotelOffering();
        $offeringData = $objHotelOffering->getOfferingsData(1);
        foreach($offeringData as $offeringKey => $offering) {
            $offeringData[$offeringKey]['img_url'] = $this->context->link->getMediaLink(
                $this->getPathUri().'/views/img/offering_img/'.$offering['id_offering'].'.jpg'
            );
        }

        $this->context->smarty->assign(
            array(
                'OFFERING_BLOCK_HEADING' => $OFFERING_BLOCK_HEADING,
                'OFFERING_BLOCK_CONTENT' => $OFFERING_BLOCK_CONTENT,
                'offeringData' => $offeringData,
            )
        );

        return $this->display(__FILE__, 'hotelOfferings.tpl');
    }

    /**
     * If admin add any language then an entry will add in defined $lang_tables array's lang table same as prestashop
     * @param array $params
     */
    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($newIdLang = $params['object']->id) {
            $langTables = array('qho_offering');
            //If Admin update new language when we do entry in module all lang tables.
            HotelHelper::updateLangTables($newIdLang, $langTables);

            // update configuration keys
            $configKeys = array(
                'OFFERING_BLOCK_HEADING',
                'OFFERING_BLOCK_CONTENT',
            );
            HotelHelper::updateConfigurationLangKeys($newIdLang, $configKeys);
        }
    }

    public function install()
    {
        $objHotelOfferingsDb = new QhoHotelOfferingDb();
        if (!parent::install()
            || !$objHotelOfferingsDb->createTables()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
        ) {
            return false;
        }

        // if module should create demo data during installation
        if (isset($this->populateData) && $this->populateData) {
            if (!$objHotelOfferingsDb->insertModuleDemoData()) {
                return false;
            }
        } else {
            Tools::deleteDirectory($this->getLocalPath().'views/img/dummy_img');
        }

        return true;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array (
                'displayHome',
                'displayFooterExploreSectionHook',
                'actionObjectLanguageAddAfter',
            )
        );
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminOfferingsManagement'));
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those controllers...
        $this->installTab('AdminOfferingsManagement', 'Manage Offerings');
        return true;
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $objTab = new Tab((int) Tab::getIdFromClassName($className));
        $objTab->active = 1;
        $objTab->class_name = $className;
        $objTab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $objTab->name[$lang['id_lang']] = $tabName;
        }

        if ($tabParentName) {
            $objTab->id_parent = (int)Tab::getIdFromClassName($tabParentName);
        } else {
            $objTab->id_parent = -1;
        }

        $objTab->module = $this->name;
        $res = $objTab->add();

        return $res;
    }

    public function deleteConfigKeys()
    {
        $configVars = array(
            'OFFERING_BLOCK_HEADING',
            'OFFERING_BLOCK_CONTENT'
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
        $objHotelOfferingsDb = new QhoHotelOfferingDb();
        if (!parent::uninstall()
            || !$this->deleteOfferingsImages()
            || !$objHotelOfferingsDb->dropTables()
            || !$this->uninstallTab()
            || !$this->deleteConfigKeys()
        ) {
            return false;
        }
        return true;
    }

    public function deleteOfferingsImages()
    {
        $objHotelOffering = new QhoHotelOffering();
        $testimonialsData = $objHotelOffering->getOfferingsData(1);
        foreach($testimonialsData as $testimonials) {
            $objHotelOffering = new QhoHotelOffering((int) $testimonials['id_offering']);
            if(Validate::isLoadedObject($objHotelOffering)) {
                $objHotelOffering->deleteImage(true);
            }
        }

        return true;
    }
}
