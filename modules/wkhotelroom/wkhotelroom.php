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

class WkHotelRoom extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct()
    {
        $this->name = 'wkhotelroom';
        $this->tab = 'front_office_features';
        $this->version = '1.1.6';
        $this->author = 'webkul';
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
                $productPriceWithoutReduction = $product->getPriceWithoutReduct(!$useTax);
                $product_price = Product::getPriceStatic($idProduct, $useTax);
                $htlRoom['image'] = $prodImg;
                $htlRoom['description'] = $product->description_short;
                $htlRoom['name'] = $product->name;
                $htlRoom['show_price'] = $product->show_price;
                $htlRoom['price'] = $product_price;
                $htlRoom['price_without_reduction'] = $productPriceWithoutReduction;
                $featurePrice = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                    $idProduct,
                    $dateFrom,
                    $dateTo,
                    $useTax
                );
                $htlRoom['feature_price'] = $featurePrice;
                $htlRoom['feature_price_diff'] = (float)($productPriceWithoutReduction - $featurePrice);
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

    public function hookDisplayAddModuleSettingLink()
    {
        $htlRoomBlockConfigLink = $this->context->link->getAdminLink('AdminHotelRoomModuleSetting');
        $this->context->smarty->assign('htlRoomBlockConfigLink', $htlRoomBlockConfigLink);

        return $this->display(__FILE__, 'hotelRoomSettingLink.tpl');
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
                'displayAddModuleSettingLink',
                'displayFooterExploreSectionHook',
                'actionProductSave',
                'actionObjectLanguageAddAfter'
            )
        );
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$this->deleteTables()
            || !$this->deleteConfigKeys()
        ) {
            return false;
        }
        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_room_block_data`'
        );
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