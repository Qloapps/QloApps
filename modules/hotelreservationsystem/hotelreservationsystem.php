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
include_once 'define.php';

class hotelreservationsystem extends Module
{
    public function __construct()
    {
        $this->name = 'hotelreservationsystem';
        $this->version = '1.4.3';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Hotel Booking and Reservation System');
        $this->description = $this->l('Now you can be able to build your website for your hotels for their bookings and reservations by using this module.');
        $this->confirmUninstall = $this->l('Are you sure? All module data will be lost after uninstalling the module');
    }

    public function hookAddWebserviceResources()
    {
        $resources = array(
            'hotels' => array('description' => 'Hotel Branch Information','class' => 'HotelBranchInformation'),
            'hotel_room_types' => array('description' => 'Hotel room types','class' => 'HotelRoomType'),
            'hotel_images' => array('description' => 'Hotel images', 'specific_management' => true),
            'hotel_features' => array('description' => 'The hotel features','class' => 'HotelFeatures'),
            'hotel_refund_rules' => array('description' => 'The hotel refund rules','class' => 'HotelOrderRefundRules'),
            'hotel_rooms' => array('description' => 'The hotel rooms','class' => 'HotelRoomInformation'),
            'feature_prices' => array('description' => 'Feature prices', 'class' => 'HotelRoomTypeFeaturePricing'),
            'advance_payments' => array('description' => 'Room type advance payment', 'class' => 'HotelAdvancedPayment'),
            'cart_bookings' => array('description' => 'Cart bookings', 'class' => 'HotelCartBookingData'),
            'bookings' => array('description' => 'Order bookings', 'class' => 'HotelBookingDetail'),
            'booking_extra_demands' => array('description' => 'Booking extra demands', 'class' => 'HotelBookingDemands'),
            'extra_demands' => array('description' => 'Extra demands', 'class' => 'HotelRoomTypeGlobalDemand'),
            'demand_advance_options' => array('description' => 'Extra demand advance options', 'class' => 'HotelRoomTypeGlobalDemandAdvanceOption'),

            'qlo' => array('description' => 'qlo API', 'specific_management' => true),
        );

        return $resources;
    }

    public function hookDisplayHeader()
    {
        // check max global order_restriction date is set
        if (!Configuration::get('MAX_GLOBAL_BOOKING_DATE')
            || (strtotime(date('Y-m-d')) > strtotime(Configuration::get('MAX_GLOBAL_BOOKING_DATE')))
        ) {
            Configuration::updateValue(
                'MAX_GLOBAL_BOOKING_DATE',
                date('d-m-Y', strtotime(date('Y-m-d', time()).' + 1 year'))
            );
        }
        /*To remove room from cart before todays date*/
        if (isset($this->context->cart->id) && $this->context->cart->id) {
            $htlCart = new HotelCartBookingData();
            if ($cartBookingData = $htlCart->getCartBookingDetailsByIdCartIdGuest(
                $this->context->cart->id,
                $this->context->cart->id_guest,
                $this->context->language->id
            )) {
                foreach ($cartBookingData as $cartRoom) {
                    if (strtotime($cartRoom['date_from']) < strtotime(date('Y-m-d'))) {
                        $htlCart->deleteRoomDataFromOrderLine(
                            $cartRoom['id_cart'],
                            $cartRoom['id_guest'],
                            $cartRoom['id_product'],
                            $cartRoom['date_from'],
                            $cartRoom['date_to']
                        );
                    }
                }
            }
        }
        //End
        $this->context->controller->addCSS($this->_path.'/views/css/HotelReservationFront.css');
        $this->context->controller->addJS($this->_path.'/views/js/HotelReservationFront.js');
    }

    public function hookDisplayAfterHookTop()
    {
        if (Tools::getValue('controller') == 'index') {
            $this->context->smarty->assign(
                array(
                    'WK_HTL_CHAIN_NAME' => Configuration::get('WK_HTL_CHAIN_NAME', $this->context->language->id),
                    'WK_HTL_TAG_LINE' => Configuration::get('WK_HTL_TAG_LINE', $this->context->language->id),
                )
            );
            return $this->display(__FILE__, 'headerHotelDescBlock.tpl');
        }
    }

    public function hookDisplayFooter($params)
    {
        /*NOTE : NEVER REMOVE THIS CODE BEFORE DISCUSSION*/
        /*id_guest is set to the context->cookie object because data mining for prestashop module is disabled
        in which id_guest was set before this*/
        if (!isset($this->context->cookie->id_guest)) {
            Guest::setNewGuest($this->context->cookie);

            if (Configuration::get('PS_STATSDATA_PLUGINS')) {
                $this->context->controller->addJS($this->_path.'views/js/plugindetect.js');

                $token = sha1($params['cookie']->id_guest._COOKIE_KEY_);

                return '<script type="text/javascript">
                    $(document).ready(function() {
                        plugins = new Object;
                        plugins.adobe_director = (PluginDetect.getVersion("Shockwave") != null) ? 1 : 0;
                        plugins.adobe_flash = (PluginDetect.getVersion("Flash") != null) ? 1 : 0;
                        plugins.apple_quicktime = (PluginDetect.getVersion("QuickTime") != null) ? 1 : 0;
                        plugins.windows_media = (PluginDetect.getVersion("WindowsMediaPlayer") != null) ? 1 : 0;
                        plugins.sun_java = (PluginDetect.getVersion("java") != null) ? 1 : 0;
                        plugins.real_player = (PluginDetect.getVersion("RealPlayer") != null) ? 1 : 0;

                        navinfo = { screen_resolution_x: screen.width, screen_resolution_y: screen.height, screen_color:screen.colorDepth};
                        for (var i in plugins)
                            navinfo[i] = plugins[i];
                        navinfo.type = "navinfo";
                        navinfo.id_guest = "'.(int)$params['cookie']->id_guest.'";
                        navinfo.token = "'.$token.'";
                        $.post("'.Context::getContext()->link->getPageLink('statistics', (bool)(Tools::getShopProtocol() == 'https://')).'", navinfo);
                    });
                </script>';
            }
        }

    }
    public function hookDisplayAfterDefautlFooterHook($params)
    {
        $this->context->smarty->assign(
            array(
                'WK_HTL_ESTABLISHMENT_YEAR' => Configuration::get('WK_HTL_ESTABLISHMENT_YEAR'),
                'WK_HTL_CHAIN_NAME' => Configuration::get('WK_HTL_CHAIN_NAME', $this->context->language->id),
            )
        );
        return $this->display(__FILE__, 'copyRight.tpl');
    }

    public function hookActionObjectProductDeleteBefore($params)
    {
        if (isset($params['object']->id)) {
            $idProduct = $params['object']->id;

            // delete the hotel room information of this product
            $objRoomInfo = new HotelRoomInformation();
            $objRoomInfo->deleteByProductId($idProduct);

            // delete the hotel room type info of this product
            $objRoomType = new HotelRoomType();
            $objRoomType->deleteByProductId($idProduct);

            // delete the advance payment configuration for this room type
            $objHotelAdvancedPayment = new HotelAdvancedPayment();
            if ($advPaymentDetail = $objHotelAdvancedPayment->getIdAdvPaymentByIdProduct($idProduct)) {
                $objHotelAdvancedPayment = new HotelAdvancedPayment($advPaymentDetail['id']);
                $objHotelAdvancedPayment->delete();
            }

            // delete the feature prices of the room type
            $objRoomTypeFeaturePricing = new HotelRoomTypeFeaturePricing();
            $objRoomTypeFeaturePricing->deleteFeaturePriceByIdProduct($idProduct);

            // delete the disable dates (temporary inactive status) of the room type
            $objRoomDisableDates = new HotelRoomDisableDates();
            $objRoomDisableDates->deleteRoomDisableDatesByIdRoomType($idProduct);

            // delete all the additional demand prices and demands of this room type
            $objRoomTypeDemandPrice = new HotelRoomTypeDemandPrice();
            $objRoomTypeDemandPrice->deleteRoomTypeDemandPrices($idProduct); // delete prices for room type
            $objRoomTypeDemand = new HotelRoomTypeDemand();
            $objRoomTypeDemand->deleteRoomTypeDemands($idProduct); // delete additional demands for room type
        }
    }

    // Add profile hotel access while profile is added
    public function hookActionObjectProfileAddAfter($params)
    {
        if (isset($params['object']->id)) {
            $idProfile = $params['object']->id;
            $objHotelBranch = new HotelBranchInformation();
            if (!$objHotelBranch->addHotelsAccessToProfile($idProfile)) {
                $this->context->controller->errors[] = $this->l('Some error occurred while adding hotel accesses to this profile');
            }
        }
    }

    // Delete profile hotel access while profile is deleted
    public function hookActionObjectProfileDeleteBefore($params)
    {
        if (isset($params['object']->id)) {
            $idProfile = $params['object']->id;
            $objHotelBranch = new HotelBranchInformation();
            if (!$objHotelBranch->deleteProfileHotelsAccess($idProfile)) {
                $this->context->controller->errors[] = $this->l('Some error occurred while deleting hotel accesses of the profile');
            }
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if ($idProduct = Tools::getValue('id_product')) {
            $objGlobalDemand = new HotelRoomTypeGlobalDemand();
            $allDemands = $objGlobalDemand->getAllDemands();
            $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
            // get room type additional services
            $objRoomDemand = new HotelRoomTypeDemand();
            $roomDemandPrices = $objRoomDemand->getRoomTypeDemands($idProduct, 0, 0);
            $this->context->smarty->assign(
                array(
                    'idProduct' => $idProduct,
                    'roomDemandPrices' => $roomDemandPrices,
                    'allDemands' => $allDemands,
                    'defaultcurrencySign' => $objCurrency->sign,
                )
            );
        }
        return $this->display(__FILE__, 'roomTypeDemands.tpl');
    }

    public function moduleProductsExtraTabName()
    {
        return $this->l('Additional Facilities');
    }

    public function hookActionProductUpdate($params)
    {
        if ($idProduct = $params['id_product']) {
            $objRoomTypeDemand = new HotelRoomTypeDemand();
            $objRoomTypeDemandPrice = new HotelRoomTypeDemandPrice();
            // first delete all the previously saved prices and demands of this room type
            $objRoomTypeDemand->deleteRoomTypeDemands($idProduct);
            $objRoomTypeDemandPrice->deleteRoomTypeDemandPrices($idProduct);
            if ($selectedDemands = Tools::getValue('selected_demand')) {
                $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption();
                foreach ($selectedDemands as $idGlobalDemand) {
                    if (Validate::isLoadedObject($objGlobalDemand = new HotelRoomTypeGlobalDemand($idGlobalDemand))) {
                        // save selected demands for this room type
                        $objRoomTypeDemand = new HotelRoomTypeDemand();
                        $objRoomTypeDemand->id_product = $idProduct;
                        $objRoomTypeDemand->id_global_demand = $idGlobalDemand;
                        $objRoomTypeDemand->save();

                        // save selected demands prices for this room type
                        $demandPrice = Tools::getValue('demand_price_'.$idGlobalDemand);
                        if (Validate::isPrice($demandPrice)) {
                            if ($objGlobalDemand->price != $demandPrice) {
                                $objRoomTypeDemandPrice = new HotelRoomTypeDemandPrice();
                                $objRoomTypeDemandPrice->id_product = $idProduct;
                                $objRoomTypeDemandPrice->id_global_demand = $idGlobalDemand;
                                $objRoomTypeDemandPrice->id_option = 0;
                                $objRoomTypeDemandPrice->price = $demandPrice;
                                $objRoomTypeDemandPrice->save();
                            }
                        } else {
                            $this->context->controller->errors[] = $this->l('Invalid demand price of facility').
                            ' : '.$objGlobalDemand->name[$this->context->language->id];
                        }
                        if ($advOptions = $objAdvOption->getGlobalDemandAdvanceOptions($idGlobalDemand)) {
                            foreach ($advOptions as $option) {
                                if (Validate::isLoadedObject($objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption($option['id']))) {
                                    $optionPrice = Tools::getValue('option_price_'.$option['id']);
                                    if (Validate::isPrice($optionPrice)) {
                                        if ($optionPrice != $objAdvOption->price) {
                                            $objRoomTypeDemandPrice = new HotelRoomTypeDemandPrice();
                                            $objRoomTypeDemandPrice->id_product = $idProduct;
                                            $objRoomTypeDemandPrice->id_global_demand = $idGlobalDemand;
                                            $objRoomTypeDemandPrice->id_option = $option['id'];
                                            $objRoomTypeDemandPrice->price = $optionPrice;
                                            $objRoomTypeDemandPrice->save();
                                        }
                                    } else {
                                        $this->context->controller->errors[] = $this->l('Invalid price of advance option').
                                        ' : '.$objAdvOption->name[$this->context->language->id];
                                    }
                                }
                            }
                        }
                    }
                }
                if (count($this->context->controller->errors)) {
                    $this->context->controller->warnings[] = $this->l('Invalid price values are not saved. Please correct them and save again');
                }

                $objCartBookingData = new HotelCartBookingData();
                if ($cartExtraDemands = $objCartBookingData->getCartExtraDemands(0, $idProduct)) {
                    // delete the demands from cart if not available in cart
                    $objRoomDemand = new HotelRoomTypeDemand();
                    $roomTypeDemandIds = array();
                    if ($roomTypeDemands = $objRoomDemand->getRoomTypeDemands($idProduct)) {
                        $roomTypeDemandIds = array_keys($roomTypeDemands);
                    }
                    foreach ($cartExtraDemands as &$demandInfo) {
                        if (isset($demandInfo['extra_demands']) && $demandInfo['extra_demands']) {
                            $cartChanged = 0;
                            foreach ($demandInfo['extra_demands'] as $key => $demand) {
                                if (!in_array($demand['id_global_demand'], $roomTypeDemandIds)) {
                                    $cartChanged = 1;
                                    unset($demandInfo['extra_demands'][$key]);
                                }
                            }
                            if ($cartChanged) {
                                if (Validate::isLoadedObject(
                                    $objCartBooking = new HotelCartBookingData($demandInfo['id'])
                                )) {
                                    $objCartBooking->extra_demands = json_encode($demandInfo['extra_demands']);
                                    $objCartBooking->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function hookActionProductSave($params)
    {
        $isToggling = Tools::getValue('statusproduct');
        if (isset($isToggling) && $isToggling) {
            $obj_htl_rm_info = new HotelRoomType();
            if ($htl_rm_info = $obj_htl_rm_info->getRoomTypeInfoByIdProduct($params['id_product'])) {
                $prod_htl_id = $htl_rm_info['id_hotel'];
                if (isset($prod_htl_id) && $prod_htl_id) {
                    $obj_hotel = new HotelBranchInformation($prod_htl_id);
                    if (!$obj_hotel->active) {
                        $obj_hotel->toggleStatus();
                    }
                }
            }
        } else {
            if (!$params['product']->quantity) {
                StockAvailable::setQuantity($params['id_product'], 0, 999999999);
            }
            if ($params['id_product']) {
                $obj_htl_rm_info = new HotelRoomType();
                if ($htl_rm_info = $obj_htl_rm_info->getRoomTypeInfoByIdProduct($params['id_product'])) {
                    $prod_htl_id = $htl_rm_info['id_hotel'];
                    if (isset($prod_htl_id) && $prod_htl_id) {
                        $obj_hotel = new HotelBranchInformation($prod_htl_id);
                        if (!$obj_hotel->active) {
                            $obj_product = new Product($params['id_product']);
                            if ($obj_product->active == 1) {
                                $this->context->controller->errors[] = $this->l('Room type can not be active as long as hotel is disabled.');
                                $obj_product->toggleStatus();
                            }
                        }
                    }
                }
            }
        }
    }

    public function hookActionOrderStatusUpdate($params)
    {
        $objHotelBookingDetail = new HotelBookingDetail();
        $statusesAvailable = $objHotelBookingDetail->getOrderStatusToFreeBookedRoom();

        if (in_array($params['newOrderStatus']->id, $statusesAvailable)) {
            // make rooms available for booking if order status is changing to cancelled, refunded or error
            if (!$objHotelBookingDetail->updateOrderRefundStatus(
                $params['id_order'],
                false,
                false,
                array(),
                1
            )) {
                $this->context->controller->errors[] = $this->l('Error while making rooms available attached with this order. Please try again.');
            }
        } else {
            $objOrder = new Order($params['id_order']);
            if (!Validate::isLoadedObject($objOrder)) {
                return;
            }

            $objOrderStatusCurrent = $objOrder->getCurrentOrderState();
            if (in_array($objOrderStatusCurrent->id, $statusesAvailable)) {
                // make rooms unavailable for booking if previous order status was any of cancelled, refunded or error
                if (!$objHotelBookingDetail->updateOrderRefundStatus(
                    $params['id_order'],
                    false,
                    false,
                    array(),
                    0
                )) {
                    $this->context->controller->errors[] = $this->l('Error while making rooms unavailable attached with this order. Please try again.');
                }
            }
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        if ('AdminProducts' == Tools::getValue('controller')) {
            $this->context->controller->addJs($this->_path.'views/js/roomTypeDemand.js');
            $this->context->controller->addCSS($this->_path.'views/css/roomTypeDemand.css');
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/admin/css/hotel_admin_tab_logo.css');
    }

    /**
     * If admin add any language then an entry will add in defined $lang_tables array's lang table same as prestashop
     * @param array $params
     */
    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($newIdLang = $params['object']->id) {
            $langTables = array(
                'htl_room_type_feature_pricing',
                'htl_branch_info',
                'htl_features',
                'htl_room_type_global_demand',
                'htl_room_type_global_demand_advance_option',
                'htl_order_refund_rules',
            );
            //If Admin update new language when we do entry in module all lang tables.
            HotelHelper::updateLangTables($newIdLang, $langTables);

            // update configuration keys
            $configKeys = array(
                'WK_HTL_CHAIN_NAME',
                'WK_HTL_TAG_LINE',
                'WK_HTL_SHORT_DESC',
            );
            HotelHelper::updateConfigurationLangKeys($newIdLang, $configKeys);
        }
    }

    public function hookActionObjectGroupDeleteBefore($params)
    {
        if (isset($params['object']->id)) {
            // delete the group entries in the feature price groups before deleting the group
            $objFeaturePrice = new HotelRoomTypeFeaturePricing();
            $objFeaturePrice->cleanGroups($params['object']->id);
        }
    }

    public function callInstallTab()
    {
        $this->installTab('AdminHotelReservationSystemManagement', 'Hotel Reservation System');
        $this->installTab('AdminHotelRoomsBooking', 'Book Now', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminHotelConfigurationSetting', 'Settings', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminAddHotel', 'Manage Hotel', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminHotelFeatures', 'Manage Hotel Features', 'AdminHotelReservationSystemManagement');
        $this->installTab(
            'AdminOrderRefundRules',
            'Manage Order Refund Rules',
            'AdminHotelReservationSystemManagement'
        );
        $this->installTab(
            'AdminOrderRefundRequests',
            'Manage Order Refund Requests',
            'AdminHotelReservationSystemManagement'
        );

        //Controllers which are to be used in this modules but we have not to create tab for those ontrollers...
        $this->installTab('AdminOrderRestrictSettings', 'Order Restrict Configuration', false, false);
        $this->installTab('AdminHotelGeneralSettings', 'Hotel General configuration', false, false);
        $this->installTab('AdminHotelFeaturePricesSettings', 'Feature Pricing Configuration', false, false);
        $this->installTab('AdminRoomTypeGlobalDemand', 'Additional Demand Configuration', false, false);
        $this->installTab('AdminAssignHotelFeatures', 'Assign Hotel Features', false, false);

        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false, $need_tab = true)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } elseif (!$need_tab) {
            $tab->id_parent = -1;
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;
        $res = $tab->add();
        //Set position of the Hotel reservation System Tab to the position wherewe want...
        if ($tab_name == 'Hotel Reservation System') {
            $objTab = new Tab($tab->id);
            $objTab->updatePosition(0, 5);
        }

        return $res;
    }

    public function install()
    {
        $objModuleDb = new HotelReservationSystemDb();
        $objHtlHelper = new HotelHelper();
        if (!parent::install()
            || !$objModuleDb->createTables()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
            || !$objHtlHelper->insertDefaultHotelEntries()
            || !$objHtlHelper->insertHotelRoomAllotmentType()
        ) {
            return false;
        }

        // if module should be populated while installation
        if (isset($this->populateData) && $this->populateData) {
            if (!$objHtlHelper->createHotelRoomDefaultFeatures()
                || !$objHtlHelper->insertHotelCommonFeatures()
                || !$objHtlHelper->createDummyDataForProject()
            ) {
                return false;
            }
        }

        return true;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'displayHeader',
                'displayTop',
                'displayAfterHookTop',
                'actionOrderHistoryAddAfter',
                'displayBackOfficeHeader',
                'actionObjectProductDeleteBefore',
                'displayFooter',
                'displayAfterDefautlFooterHook',
                'actionProductSave',
                'addWebserviceResources',
                'actionObjectLanguageAddAfter',
                'actionAdminControllerSetMedia',
                'displayAdminProductsExtra',
                'actionProductUpdate',
                'actionObjectProfileAddAfter',
                'actionObjectProfileDeleteBefore',
                'actionObjectGroupDeleteBefore',
                'actionOrderStatusUpdate',
            )
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

    public function deleteConfigVars()
    {
        $configKeys = array(
            'WK_HOTEL_LOCATION_ENABLE',
            'WK_ROOM_LEFT_WARNING_NUMBER',
            'WK_HOTEL_GLOBAL_ADDRESS',
            'WK_HOTEL_GLOBAL_CONTACT_EMAIL',
            'WK_HOTEL_GLOBAL_CONTACT_NUMBER',
            'WK_HTL_ESTABLISHMENT_YEAR',
            'WK_HTL_CHAIN_NAME',
            'WK_TITLE_HEADER_BLOCK',
            'WK_CONTENT_HEADER_BLOCK',
            'WK_HTL_HEADER_IMAGE',
            'WK_ALLOW_ADVANCED_PAYMENT',
            'WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT',
            'WK_ADVANCED_PAYMENT_INC_TAX',
            'WK_GOOGLE_ACTIVE_MAP',
            'WK_MAP_HOTEL_ACTIVE_ONLY',
            'WK_HOTEL_NAME_ENABLE'
        );
        foreach ($configKeys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }
        return true;
    }

    public function uninstall()
    {
        $objModuleDb = new HotelReservationSystemDb();
        if (!parent::uninstall()
            || !$this->deleteConfigVars()
            || !$objModuleDb->dropTables()
            || !$this->uninstallTab()
        ) {
            return false;
        }
        return true;
    }
}
