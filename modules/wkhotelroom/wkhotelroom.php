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

require_once dirname(__FILE__).'/define.php';

class WkHotelRoom extends Module
{
    public function __construct()
    {
        $this->name = 'wkhotelroom';
        $this->tab = 'front_office_features';
        $this->version = '1.2.0';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Display Hotel Rooms');
        $this->description = $this->l('Using this module you can display your hotel rooms in home page.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookDisplayHome()
    {
        $objRoomBlock = new WkHotelRoomDisplay();
        if ($hotelRoomDisplay = $objRoomBlock->getHotelRoomDisplayData()) {
            $idLang = $this->context->language->id;
            $dateFrom = date('Y-m-d');
            $dateTo = date('Y-m-d', strtotime($dateFrom) + 86400);
            $useTax = HotelBookingDetail::useTax();
            foreach ($hotelRoomDisplay as &$htlRoom) {
                $idProduct = $htlRoom['id_product'];
                $product = new Product($idProduct, false, $idLang);

                if ($coverImageId = Product::getCover($product->id)) {
                    $prodImg = $this->context->link->getImageLink(
                        $product->link_rewrite,
                        $product->id.'-'.$coverImageId['id_image'],
                        ImageType::getFormatedName('large')
                    );
                } else {
                    $prodImg = $this->context->link->getImageLink(
                        $product->link_rewrite,
                        $this->context->language->iso_code."-default",
                        ImageType::getFormatedName('large')
                    );
                }
                $productPriceWithoutReduction = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                    $idProduct,
                    $dateFrom,
                    $dateTo,
                    1,
                    0,
                    0,
                    0,
                    0,
                    1,
                    0
                );

                if ($useTax) {
                    $priceWithoutReduction = $productPriceWithoutReduction['total_price_tax_incl'];
                } else {
                    $priceWithoutReduction = $productPriceWithoutReduction['total_price_tax_excl'];
                }

                $product_price = Product::getPriceStatic($idProduct, $useTax);
                $htlRoom['image'] = $prodImg;
                $htlRoom['description'] = $product->description_short;
                $htlRoom['name'] = $product->name;
                $htlRoom['show_price'] = $product->show_price;
                $htlRoom['price'] = $product_price;
                $htlRoom['price_without_reduction'] = $priceWithoutReduction;
                $featurePrice = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                    $idProduct,
                    $dateFrom,
                    $dateTo,
                    $useTax
                );
                $htlRoom['feature_price'] = $featurePrice;
                $htlRoom['feature_price_diff'] = (float)($priceWithoutReduction - $featurePrice);
            }
        }
        $this->context->smarty->assign(
            array(
                'HOTEL_ROOM_DISPLAY_HEADING' => Configuration::get(
                    'HOTEL_ROOM_DISPLAY_HEADING',
                    $this->context->language->id
                ),
                'HOTEL_ROOM_DISPLAY_DESCRIPTION' => Configuration::get(
                    'HOTEL_ROOM_DISPLAY_DESCRIPTION',
                    $this->context->language->id
                ),
                'hotelRoomDisplay' => $hotelRoomDisplay
            )
        );

        $this->context->controller->addJs($this->_path.'/views/js/WkHotelRoomBlockFront.js');
        $this->context->controller->addCSS($this->_path.'/views/css/WkHotelRoomBlockFront.css');

        return $this->display(__FILE__, 'hotelRoomDisplayBlock.tpl');
    }

    public function hookActionProductSave($params)
    {
        if (isset($params['id_product']) && $params['id_product']) {
            if (Validate::isLoadedObject($objProduct = new Product($params['id_product']))) {
                if (!$objProduct->active) {
                    $objRoomBlock = new WkHotelRoomDisplay();
                    if ($roomBlockInfo = $objRoomBlock->gerRoomByIdProduct($params['id_product'])) {
                        $objRoomBlock = new WkHotelRoomDisplay($roomBlockInfo['id_room_block']);
                        $objRoomBlock->active = 0;
                        $objRoomBlock->save();
                    }
                }
            }
        }
    }

    public function hookActionProductDelete($params)
    {
        if (isset($params['id_product']) && $params['id_product']) {
            $objRoomBlock = new WkHotelRoomDisplay();
            $objRoomBlock->deleteRoomByIdProduct($params['id_product']);
        }
    }

    /**
     * If admin add any language then an entry will add in defined $lang_tables array's lang table same as prestashop
     * @param array $params
     */
    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($newIdLang = $params['object']->id) {
            $configKeys = array(
                'HOTEL_ROOM_DISPLAY_HEADING',
                'HOTEL_ROOM_DISPLAY_DESCRIPTION',
            );
            HotelHelper::updateConfigurationLangKeys($newIdLang, $configKeys);
        }
    }

    public function hookActionCleanData($params)
    {
        if ($params['method'] == 'catalog') {
            WkHotelRoomDb::truncateTables();
        }
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those controllers...
        $this->installTab('AdminHotelRoomModuleSetting', 'Manage Hotel Rooms Display');
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
        $objHotelRoomDb = new WkHotelRoomDb();
        if (!parent::install()
            || !$objHotelRoomDb->createTables()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
        ) {
            return false;
        }

        $objRoomBlock = new WkHotelRoomDisplay();
        // if module should be populated while installation
        if (isset($this->populateData) && $this->populateData) {
            if (!$objRoomBlock->insertModuleDemoData()) {
                return false;
            }
        }

        return true;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array (
                'displayHome',
                'actionProductDelete',
                'displayFooterExploreSectionHook',
                'actionProductSave',
                'actionObjectLanguageAddAfter',
                'actionCleanData',
            )
        );
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminHotelRoomModuleSetting'));
    }

    public function uninstall()
    {
        $objHotelRoomDb = new WkHotelRoomDb();
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$objHotelRoomDb->dropTables()
            || !$this->deleteConfigKeys()
        ) {
            return false;
        }
        return true;
    }

    public function deleteConfigKeys()
    {
        $configVars = array(
            'HOTEL_ROOM_DISPLAY_HEADING',
            'HOTEL_ROOM_DISPLAY_DESCRIPTION'
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
}