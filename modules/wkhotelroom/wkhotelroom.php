<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/define.php';

class wkhotelroom extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct()
    {
        $this->name = 'wkhotelroom';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'webkul';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Display Hotel Rooms');
        $this->description = $this->l('Using this module you can display your hotel rooms in home page.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function hookDisplayHome()
    {
        $this->registerHook('displayFooterExploreSectionHook');
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/WkHotelRoomBlockFront.css');
        $id_lang = $this->context->language->id;
        $useTax = HotelBookingDetail::useTax();

        $obj_room_block = new WkHotelRoomDisplay();
        $hotelRoomDisplay = $obj_room_block->getHotelRoomDisplayData();

        $date_from = date('Y-m-d');
        $date_to = date('Y-m-d', strtotime($date_from) + 86400);
        if ($hotelRoomDisplay) {
            foreach ($hotelRoomDisplay as &$htlRoom) {
                $id_product = $htlRoom['id_product'];
                $product = new Product($id_product, false, $id_lang);
                $cover_image_id = Product::getCover($product->id);
                if ($cover_image_id) {
                    $prod_img = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_id['id_image'], 'large_default');
                } else {
                    $prod_img = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code."-default", 'large_default');
                }
                $productPriceWithoutReduction = $product->getPriceWithoutReduct(!$useTax);
                $product_price = Product::getPriceStatic($id_product, $useTax);
                $htlRoom['image'] = $prod_img;
                $htlRoom['description'] = $product->description_short;
                $htlRoom['name'] = $product->name;
                $htlRoom['price'] = $product_price;
                $htlRoom['price_without_reduction'] = $productPriceWithoutReduction;

                $feature_price = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay($id_product, $date_from, $date_to, $useTax);
                $htlRoom['feature_price'] = $feature_price;
                $htlRoom['feature_price_diff'] = (float)($productPriceWithoutReduction - $feature_price);
            }
        }
        $HOTEL_ROOM_DISPLAY_HEADING = Configuration::get('HOTEL_ROOM_DISPLAY_HEADING');
        $HOTEL_ROOM_DISPLAY_DESCRIPTION = Configuration::get('HOTEL_ROOM_DISPLAY_DESCRIPTION');

        $this->context->smarty->assign(array('HOTEL_ROOM_DISPLAY_HEADING' => $HOTEL_ROOM_DISPLAY_HEADING,
                                             'HOTEL_ROOM_DISPLAY_DESCRIPTION' => $HOTEL_ROOM_DISPLAY_DESCRIPTION,
                                             'hotelRoomDisplay' => $hotelRoomDisplay,
                                            ));

        return $this->display(__FILE__, 'hotelRoomDisplayBlock.tpl');
    }


    public function hookActionProductDelete($params)
    {
        if ($params['id_product']) {
            WkHotelRoomDisplay::deleteRoomByIdProduct($params['id_product']);
        }
    }

    public function hookDisplayAddModuleSettingLink()
    {
        $htlRoomBlockConfigLink = $this->context->link->getAdminLink('AdminHotelRoomModuleSetting');
        $this->context->smarty->assign('htlRoomBlockConfigLink', $htlRoomBlockConfigLink);
        
        return $this->display(__FILE__, 'hotelRoomSettingLink.tpl');
    }

    public function hookDisplayDefaultNavigationHook()
    {
        return $this->display(__FILE__, 'hotelRoomNaviagtionMenu.tpl');
    }

    public function hookDisplayFooterExploreSectionHook()
    {
        return $this->display(__FILE__, 'hotelRoomFooterExploreLink.tpl');
    }

    public function insertDefaultHotelFeaturesEntries()
    {
        $HOTEL_ROOM_DISPLAY_HEADING = $this->l('Our Rooms');
        $HOTEL_ROOM_DISPLAY_DESCRIPTION = $this->l('Families travelling with kids will find Amboseli national park a safari destination matched to no other, with less tourist traffic, breathtaking open space.');

        Configuration::updateValue('HOTEL_ROOM_DISPLAY_HEADING', $HOTEL_ROOM_DISPLAY_HEADING);
        Configuration::updateValue('HOTEL_ROOM_DISPLAY_DESCRIPTION', $HOTEL_ROOM_DISPLAY_DESCRIPTION);

        $ps_product = Product::getProducts(Configuration::get('PS_LANG_DEFAULT'), 0, 5, 'id_product', 'ASC');
        foreach ($ps_product as $product) {
            $obj_room_block = new WkHotelRoomDisplay();
            $obj_room_block->id_product = $product['id_product'];
            $obj_room_block->active = 1;
            $obj_room_block->save();
        }

        return true;
    }

    public function callInstallTab()
    {
        //Controllers which are to be used in this modules but we have not to create tab for those controllers...
        $this->installTab('AdminHotelRoomModuleSetting', 'Manage Hotel Rooms Display');
        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name=false)
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
            || !$this->registerHook('displayHome')
            || !$this->registerHook('actionProductDelete')
            || !$this->registerHook('displayAddModuleSettingLink')
            || !$this->registerHook('displayDefaultNavigationHook')
            || !$this->registerHook('displayFooterExploreSectionHook')
            || !$this->callInstallTab()
            || !$this->insertDefaultHotelFeaturesEntries()
            ) {
            return false;
        }
        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }
        return true;
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()
            || !$this->callUninstallTab()
            || ($keep && !$this->deleteTables())
            || ($keep && !$this->deleteConfigKeys())
            ) {
            return false;
        }

        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_room_block_data`');
    }

    public function deleteConfigKeys()
    {
        $var = array('HOTEL_ROOM_DISPLAY_HEADING',
                     'HOTEL_ROOM_DISPLAY_DESCRIPTION');

        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }
        
        return true;
    }

    public function callUninstallTab()
    {
        $this->uninstallTab('AdminHotelRoomModuleSetting');
        return true;
    }
        
    public function uninstallTab($class_name)
    {
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        } else {
            return false;
        }
    }
}
