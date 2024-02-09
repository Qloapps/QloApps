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

class HotelReservationSystem extends Module
{
    public function __construct()
    {
        $this->name = 'hotelreservationsystem';
        $this->tab = 'administration';
        $this->version = '1.5.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Hotel Booking and Reservation System');
        $this->description = $this->l('This module is the backbone of QloApps and handles all booking processes on your website.');
        $this->confirmUninstall = $this->l('This module should not be uninstalled under any circumstances. Doing so may cause undesired results.');
    }

    public function hookAddWebserviceResources()
    {
        $resources = array(
            'hotels' => array('description' => 'Hotel Branch Information','class' => 'HotelBranchInformation'),
            'hotel_room_types' => array('description' => 'Hotel room types','class' => 'HotelRoomType'),
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
            'hotel_ari' => array('description' => 'Search availability, rates and inventory', 'specific_management' => true),
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
        if (!Configuration::get('PS_CATALOG_MODE')) {
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
        }
        //End
        $this->context->controller->addCSS($this->_path.'/views/css/HotelReservationFront.css');
        $this->context->controller->addJS($this->_path.'/views/js/HotelReservationFront.js');
    }

    public function hookActionFrontControllerSetMedia()
    {
        if (Configuration::get('WK_CUSTOMER_SUPPORT_PHONE_NUMBER') != ''
            || Configuration::get('WK_CUSTOMER_SUPPORT_EMAIL') != ''
        ) {
            $this->context->controller->addCSS($this->getPathUri().'views/css/hook/display-nav.css');
        }

    }

    public function hookDisplayNav()
    {
        $this->smarty->assign(array(
            'phone' => Configuration::get('WK_CUSTOMER_SUPPORT_PHONE_NUMBER'),
            'email' => Configuration::get('WK_CUSTOMER_SUPPORT_EMAIL'),
        ));

        return $this->display(__FILE__, 'display-nav.tpl');
    }

    public function hookDisplayExternalNavigationHook()
    {
        $this->smarty->assign(array(
            'phone' => Configuration::get('WK_CUSTOMER_SUPPORT_PHONE_NUMBER'),
            'email' => Configuration::get('WK_CUSTOMER_SUPPORT_EMAIL'),
        ));

        return $this->display(__FILE__, 'external-navigation-hook.tpl');
    }

    public function cartBookingDataForMail($order)
    {
        $result = array();
        $customer = new Customer($order->id_customer);
        // To show order details properly on order history page
        $products = $order->getProducts();
        if (Module::isInstalled('hotelreservationsystem')) {
            require_once(_PS_MODULE_DIR_.'hotelreservationsystem/define.php');
            $obj_cart_bk_data = new HotelCartBookingData();
            $obj_htl_bk_dtl = new HotelBookingDetail();
            $obj_rm_type = new HotelRoomType();
            $objBookingDemand = new HotelBookingDemands();
            $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
            $result['total_extra_demands_te'] = 0;
            $result['total_extra_demands_ti'] = 0;
            $cart_htl_data = array();
            if (!empty($products)) {
                foreach ($products as $type_key => $type_value) {
                    $product = new Product($type_value['product_id'], false, $this->context->language->id);
                    $cover_image_arr = $product->getCover($type_value['product_id']);

                    if (!empty($cover_image_arr)) {
                        $cover_img = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_arr['id_image'], 'small_default');
                    } else {
                        $cover_img = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code."-default", 'small_default');
                    }

                    $unit_price = Product::getPriceStatic($type_value['product_id'], true, null, 6, null, false, true, 1);

                    if (isset($customer->id)) {
                        $cart_obj = new Cart($order->id_cart);
                        $cart_bk_data = $obj_cart_bk_data->getOnlyCartBookingData($order->id_cart, $cart_obj->id_guest, $type_value['product_id'], $customer->id);
                    } else {
                        $cart_bk_data = $obj_cart_bk_data->getOnlyCartBookingData($order->id_cart, $customer->id_guest, $type_value['product_id']);
                    }
                    if ($cart_bk_data) {
                        $rm_dtl = $obj_rm_type->getRoomTypeInfoByIdProduct($type_value['product_id']);

                        $cart_htl_data[$type_key]['id_product'] = $type_value['product_id'];
                        $cart_htl_data[$type_key]['cover_img']    = $cover_img;
                        $cart_htl_data[$type_key]['name']        = $product->name;
                        $cart_htl_data[$type_key]['unit_price'] = $unit_price;
                        $cart_htl_data[$type_key]['hotel_name'] = $rm_dtl['hotel_name'];
                        $cart_htl_data[$type_key]['adults']        = $rm_dtl['adults'];
                        $cart_htl_data[$type_key]['children']    = $rm_dtl['children'];

                        foreach ($cart_bk_data as $data_k => $data_v) {
                            $date_join = strtotime($data_v['date_from']).strtotime($data_v['date_to']);

                            if (isset($cart_htl_data[$type_key]['date_diff'][$date_join])) {
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] += 1;

                                $num_days = $cart_htl_data[$type_key]['date_diff'][$date_join]['num_days'];
                                $vart_quant = (int)$cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'];

                                //$amount = Product::getPriceStatic($type_value['product_id'], true, null, 6, null,	false, true, 1);
                                //$amount *= $vart_quant;


                                $roomTypeDateRangePrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($type_value['id_product'], $data_v['date_from'], $data_v['date_to']);


                                $cart_htl_data[$type_key]['date_diff'][$date_join]['amount'] = $roomTypeDateRangePrice['total_price_tax_incl']*$vart_quant;
                                // extra demands prices
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to']
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_te'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    0,
                                    1,
                                    0
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    0,
                                    1,
                                    1
                                );
                            } else {
                                $num_days = $obj_htl_bk_dtl->getNumberOfDays($data_v['date_from'], $data_v['date_to']);

                                $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] = 1;
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['data_form'] = $data_v['date_from'];
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['data_to'] = $data_v['date_to'];
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['num_days'] = $num_days;
                                /*$amount = Product::getPriceStatic($type_value['product_id'], true, null, 6, null, false, true, 1);
                                $amount *= $num_days;*/

                                $roomTypeDateRangePrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($type_value['id_product'], $data_v['date_from'], $data_v['date_to']);

                                $cart_htl_data[$type_key]['date_diff'][$date_join]['amount'] = $roomTypeDateRangePrice['total_price_tax_incl'];
                                // extra demands prices
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to']
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_te'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    0,
                                    1,
                                    0
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    0,
                                    1,
                                    1
                                );

                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
                                    0,
                                    0,
                                    $type_value['product_id'],
                                    $data_v['date_from'],
                                    $data_v['date_to']
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_ti'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
                                    0,
                                    0,
                                    $type_value['product_id'],
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    $data_v['id_room'],
                                    1,
                                    1
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_te'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
                                    0,
                                    0,
                                    $type_value['product_id'],
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    $data_v['id_room'],
                                    1,
                                    0
                                );
                                $result['total_extra_demands_te'] += $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_te'];
                                $result['total_extra_demands_ti'] += $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'];
                            }
                        }
                    }
                }
            }
            $result['cart_htl_data'] = $cart_htl_data;
        }
        return $result;
    }

    public function hookDisplayLeftColumn()
    {
        if (Tools::getValue('controller') == 'category') {
            if ($apiKey = Configuration::get('PS_API_KEY')) {
                $idCategory = Tools::getValue('id_category');
                $idHotel = HotelBranchInformation::getHotelIdByIdCategory($idCategory);
                $objHotel = new HotelBranchInformation($idHotel, $this->context->language->id);

                if (floatval($objHotel->latitude) != 0
                    && floatval($objHotel->longitude) != 0
                ) {
                    Media::addJsDef(array(
                        'hotel_location' => array(
                            'latitude' => $objHotel->latitude,
                            'longitude' => $objHotel->longitude,
                            'map_input_text' => $objHotel->map_input_text,
                        ),
                        'hotel_name' => $objHotel->hotel_name,
                    ));

                    $this->context->controller->addJS(
                        'https://maps.googleapis.com/maps/api/js?key='.$apiKey.'&libraries=places&language='.
                        $this->context->language->iso_code.'&region='.$this->context->country->iso_code
                    );
                    $this->context->controller->addJS($this->getPathUri().'views/js/searchResultsMap.js');
                    $this->context->controller->addCSS($this->getPathUri().'views/css/searchResultsMap.css');

                    $this->context->smarty->assign('hotel', $objHotel);
                    return $this->display(__FILE__, 'searchResultsMap.tpl');
                }
            }
        }
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

    public function hookActionProductSave($params)
    {
        $obj_htl_rm_info = new HotelRoomType();
        if ($htl_rm_info = $obj_htl_rm_info->getRoomTypeInfoByIdProduct($params['id_product'])) {
            $isToggling = Tools::getValue('statusproduct');
            if (isset($isToggling) && $isToggling) {
                $prod_htl_id = $htl_rm_info['id_hotel'];
                if (isset($prod_htl_id) && $prod_htl_id) {
                    $obj_hotel = new HotelBranchInformation($prod_htl_id);
                    if (!$obj_hotel->active) {
                        $obj_hotel->toggleStatus();
                    }
                }
            } else {
                if (!$params['product']->quantity) {
                    StockAvailable::setQuantity($params['id_product'], 0, 999999999);
                }
                if ($params['id_product']) {
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

    public function hookActionOrderStatusPostUpdate($params)
    {
        $objHtlBkDtl = new HotelBookingDetail();

        // Make rooms available for booking if order status is cancelled, refunded or error
        if (in_array($params['newOrderStatus']->id, $objHtlBkDtl->getOrderStatusToFreeBookedRoom())) {
            if (!$objHtlBkDtl->updateOrderRefundStatus($params['id_order'])) {
                $this->context->controller->errors[] = $this->l('Error while making booked rooms available, attached with this order. Please try again !!');
            }
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

    public function HookActionCartSummary($params)
    {
        // $objCartBookingData = new HotelCartBookingData();
        // $totalFacilityCostTI = $objCartBookingData->getCartExtraDemands($params['cart']->id, 0, 0, 0, 0, 1, 0, 1);
        // $totalFacilityCostTE = $objCartBookingData->getCartExtraDemands($params['cart']->id, 0, 0, 0, 0, 1, 0, 0);
        // return array(
        //     'additional_facilities_tax' => ($totalFacilityCostTI - $totalFacilityCostTE),
        //     'totalFacilityCostTE' => $totalFacilityCostTE,
        //     'totalFacilityCostTI' => $totalFacilityCostTI,
        // );
        return array();
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

        // Controllers without tabs
        $this->installTab('AdminHotelGeneralSettings', 'Hotel General configuration', false, false);
        $this->installTab('AdminHotelFeaturePricesSettings', 'Advanced Price Rules', false, false);
        $this->installTab('AdminRoomTypeGlobalDemand', 'Additional Demand Configuration', false, false);
        $this->installTab('AdminAssignHotelFeatures', 'Assign Hotel Features', false, false);
        $this->installTab('AdminBookingDocument', 'Booking Documents', false, false);

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
            || !$objHtlHelper->createHotelRoomDefaultFeatures()
            || !$objHtlHelper->insertHotelCommonFeatures()
        ) {
            return false;
        }

        // if module should be populated while installation
        if (isset($this->populateData) && $this->populateData) {
            if (!$objHtlHelper->createDummyDataForProject()) {
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
                'actionObjectProfileAddAfter',
                'actionObjectProfileDeleteBefore',
                'actionObjectGroupDeleteBefore',
                'actionOrderStatusPostUpdate',
                'displayLeftColumn',
                'actionCartSummary',
                'actionFrontControllerSetMedia',
                'displayNav',
                'displayExternalNavigationHook',
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
            'WK_HOTEL_NAME_ENABLE',
            'WK_CUSTOMER_SUPPORT_PHONE_NUMBER',
            'WK_CUSTOMER_SUPPORT_EMAIL',
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
