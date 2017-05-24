<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once 'define.php';

class hotelreservationsystem extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'hotelreservationsystem';
        $this->version = '1.1.0';
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
            'qlo' => array('description' => 'qlo API', 'specific_management' => true),
        );

        return $resources;
    }

    public function hookDisplayHeader()
    {
        // check max global order_restriction date is set
        if (!Configuration::get('MAX_GLOBAL_BOOKING_DATE') || (strtotime(date('Y-m-d')) > strtotime(Configuration::get('MAX_GLOBAL_BOOKING_DATE')))) {
            Configuration::updateValue('MAX_GLOBAL_BOOKING_DATE', date('d-m-Y', strtotime(date('Y-m-d', time()).' + 1 year')));
        }
        /*To remove room from cart before todays date*/
        if (isset($this->context->cart->id) && $this->context->cart->id) {
            $htlCart = new HotelCartBookingData();
            $cartBookingData = $htlCart->getCartBookingDetailsByIdCartIdGuest($this->context->cart->id, $this->context->cart->id_guest, $this->context->language->id);
            if ($cartBookingData) {
                foreach ($cartBookingData as $cartRoom) {
                    if (strtotime($cartRoom['date_from']) < strtotime(date('Y-m-d'))) {
                        $htlCart->deleteRoomDataFromOrderLine($cartRoom['id_cart'], $cartRoom['id_guest'], $cartRoom['id_product'], $cartRoom['date_from'], $cartRoom['date_to']);
                    }
                }
            }
        }
        //End
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->name.'/views/css/HotelReservationFront.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->name.'/views/js/HotelReservationFront.js');
    }

    public function hookDisplayAfterHookTop()
    {
        if (Tools::getValue('controller') == 'index') {
            $this->context->smarty->assign(array('WK_HTL_CHAIN_NAME' => Configuration::get('WK_HTL_CHAIN_NAME'),
                                                 'WK_HTL_TAG_LINE' => Configuration::get('WK_HTL_TAG_LINE'),
                                                ));

            return $this->display(__FILE__, 'headerHotelDescBlock.tpl');
        }
    }

    public function hookFooter($params)
    {
        /*NOTE : NEVER REMOVE THIS CODE BEFORE DISCUSSION*/
        /*id_guest is set to the context->cookie object because data mining for prestashop module is disabled in which id_guest was set before this*/
        if (!isset($this->context->cookie->id_guest)) {
            Guest::setNewGuest($this->context->cookie);
        }

        // return $this->display(__FILE__, 'hotelGlobalVariables.tpl');
    }
    public function hookDisplayAfterDefautlFooterHook($params)
    {
        $this->context->smarty->assign(array(
            'WK_HTL_ESTABLISHMENT_YEAR' => Configuration::get('WK_HTL_ESTABLISHMENT_YEAR'),
            'WK_HTL_CHAIN_NAME' => Configuration::get('WK_HTL_CHAIN_NAME'),
            ));
        
        return $this->display(__FILE__, 'copyRight.tpl');
    }

    public function hookActionProductDelete($params)
    {
        if ($params['id_product']) {
            $obj_htl_rm_type = new HotelRoomType();
            $obj_htl_rm_info = new HotelRoomInformation();
            $obj_htl_cart_data = new HotelCartBookingData();

            $delete_cart_data = $obj_htl_cart_data->deleteBookingCartDataNotOrderedByProductId($params['id_product']);
            $delete_room_info = $obj_htl_rm_info->deleteByProductId($params['id_product']);
            $delete_room_type = $obj_htl_rm_type->deleteByProductId($params['id_product']);
        }
    }

    public function hookActionProductSave($params)
    {
        $isToggling = Tools::getValue('statusproduct');
        if (isset($isToggling) && $isToggling) {
            $obj_htl_rm_info = new HotelRoomType();
            $htl_rm_info = $obj_htl_rm_info->getRoomTypeInfoByIdProduct($params['id_product']);
            if (isset($htl_rm_info) && $htl_rm_info) {
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
                $htl_rm_info = $obj_htl_rm_info->getRoomTypeInfoByIdProduct($params['id_product']);
                if (isset($htl_rm_info) && $htl_rm_info) {
                    $prod_htl_id = $htl_rm_info['id_hotel'];
                    if (isset($prod_htl_id) && $prod_htl_id) {
                        $obj_hotel = new HotelBranchInformation($prod_htl_id);
                        if (!$obj_hotel->active) {
                            $obj_product = new Product($params['id_product']);
                            if ($obj_product->active == 1) {
                                $obj_product->toggleStatus();
                            }
                        }
                    }
                }
            }
        }
    }

    public function hookActionValidateOrder($data)
    {
        $cart = $data['cart'];
        $order = $data['order'];
        $customer = $data['customer'];

        $obj_cart_bk_data = new HotelCartBookingData();
        $obj_htl_bk_dtl = new HotelBookingDetail();
        $obj_rm_type = new HotelRoomType();
        $obj_adv_payment = new HotelAdvancedPayment();

        $cart_products = $cart->getProducts();
        foreach ($cart_products as $product) {
            $obj_cart_bk_data = new HotelCartBookingData();
            $cart_bk_data = $obj_cart_bk_data->getOnlyCartBookingData($cart->id, $cart->id_guest, $product['id_product']);
            if ($cart_bk_data) {
                foreach ($cart_bk_data as $cb_k => $cb_v) {
                    $obj_cart_bk_data = new HotelCartBookingData($cb_v['id']);
                    $obj_cart_bk_data->id_order = $order->id;
                    $obj_cart_bk_data->id_customer = $customer->id;
                    $obj_cart_bk_data->save();

                    $obj_htl_bk_dtl = new HotelBookingDetail();
                    $id_order_detail = $obj_htl_bk_dtl->getPsOrderDetailIdByIdProduct($product['id_product'], $order->id);
                    $obj_htl_bk_dtl->id_product = $product['id_product'];
                    $obj_htl_bk_dtl->id_order = $order->id;
                    $obj_htl_bk_dtl->id_order_detail = $id_order_detail;
                    $obj_htl_bk_dtl->id_cart = $cart->id;
                    $obj_htl_bk_dtl->id_room = $obj_cart_bk_data->id_room;
                    $obj_htl_bk_dtl->id_hotel = $obj_cart_bk_data->id_hotel;
                    $obj_htl_bk_dtl->id_customer = $customer->id;
                    $obj_htl_bk_dtl->booking_type = $obj_cart_bk_data->booking_type;
                    $obj_htl_bk_dtl->id_status = 1;
                    $obj_htl_bk_dtl->comment = $obj_cart_bk_data->comment;

                    // For Back Order(Because of cart lock)
                    if ($obj_cart_bk_data->is_back_order) {
                        $obj_htl_bk_dtl->is_back_order = 1;
                    }

                    $total_price = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($product['id_product'], $obj_cart_bk_data->date_from, $obj_cart_bk_data->date_to);
                    $obj_htl_bk_dtl->date_from = $obj_cart_bk_data->date_from;
                    $obj_htl_bk_dtl->date_to = $obj_cart_bk_data->date_to;
                    $obj_htl_bk_dtl->total_price_tax_excl = Tools::ps_round($total_price['total_price_tax_excl'], 5);
                    $obj_htl_bk_dtl->total_price_tax_incl = Tools::ps_round($total_price['total_price_tax_incl'], 5);
                    $obj_htl_bk_dtl->save();

                    /*for saving details of the advance payment product wise*/
                    if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT')) {
                        $obj_customer_adv = new HotelCustomerAdvancedPayment();
                        $cust_adv_payment_dtls = $obj_customer_adv->getClientAdvPaymentDtl($cart->id, $cart->id_guest);
                        if ($cust_adv_payment_dtls) {
                            $prod_adv_payment = $obj_adv_payment->getIdAdvPaymentByIdProduct($product['id_product']);

                            if (!$prod_adv_payment || (isset($prod_adv_payment['payment_type']) && $prod_adv_payment['payment_type'])) {
                                $room_adv_amount = $obj_adv_payment->getRoomMinAdvPaymentAmount($product['id_product'], $obj_cart_bk_data->date_from, $obj_cart_bk_data->date_to);
                                $obj_customer_adv_product = new HotelCustomerAdvancedProductPayment();
                                $obj_customer_adv_product->id_cart = $cart->id;
                                $obj_customer_adv_product->id_room = $obj_cart_bk_data->id_room;
                                $obj_customer_adv_product->id_hotel = $obj_cart_bk_data->id_hotel;
                                $obj_customer_adv_product->id_hotel = $obj_cart_bk_data->quantity;
                                $obj_customer_adv_product->id_product = $product['id_product'];
                                $obj_customer_adv_product->id_order = $order->id;
                                $obj_customer_adv_product->id_guest = $cart->id_guest;
                                $obj_customer_adv_product->id_customer = $customer->id;
                                $obj_customer_adv_product->id_currency = $cart->id_currency;
                                $obj_customer_adv_product->product_price_tax_incl = Product::getPriceStatic($product['id_product'], true);
                                $obj_customer_adv_product->product_price_tax_excl = Product::getPriceStatic($product['id_product'], false);
                                $obj_customer_adv_product->advance_payment_amount = $room_adv_amount;
                                $obj_customer_adv_product->date_from = $obj_cart_bk_data->date_from;
                                $obj_customer_adv_product->date_to = $obj_cart_bk_data->date_to;
                                $obj_customer_adv_product->save();
                            }
                        }
                    }
                    /*End*/
                }
            }
        }

        // For Advanced Payment
        if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT')) {
            $obj_customer_adv = new HotelCustomerAdvancedPayment();
            $customer_adv_dtl = $obj_customer_adv->getClientAdvPaymentDtl($cart->id, $cart->id_guest);
            if ($customer_adv_dtl) {
                $obj_customer_adv = new HotelCustomerAdvancedPayment($customer_adv_dtl['id']);

                $obj_customer_adv->id_customer = $customer->id;
                $obj_customer_adv->id_order = $order->id;

                //if currency is changed before order
                if ($cart->id_currency != $obj_customer_adv->id_currency) {
                    $obj_customer_adv->total_paid_amount = Tools::convertPriceFull($obj_customer_adv->total_paid_amount, new Currency($obj_customer_adv->id_currency), new Currency($cart->id_currency));

                    $order_amt = $order->total_products_wt;
                    $obj_customer_adv->id_currency = $cart->id_currency;
                } else {
                    $order_amt = $order->total_products_wt;
                }

                $obj_customer_adv->total_order_amount = $order_amt;
                $obj_customer_adv->save();
            }
        }

        if (isset($_COOKIE['wk_id_cart'])) {
            setcookie('wk_id_cart', ' ', time() - 86400, '/');
            setcookie('wk_id_guest', ' ', time() - 86400, '/');

            unset($_COOKIE['wk_id_cart']);
            unset($_COOKIE['wk_id_guest']);
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/admin/css/hotel_admin_tab_logo.css');
    }

    public function callInstallTab()
    {
        $this->installTab('AdminHotelReservationSystemManagement', 'Hotel Reservation System');
        $this->installTab('AdminHotelRoomsBooking', 'Book Now', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminHotelConfigurationSetting', 'Settings', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminAddHotel', 'Manage Hotel', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminHotelFeatures', 'Manage Hotel Features', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminOrderRefundRules', 'Manage Order Refund Rules', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminOrderRefundRequests', 'Manage Order Refund Requests', 'AdminHotelReservationSystemManagement');

        //Controllers which are to be used in this modules but we have not to create tab for those ontrollers...
        $this->installTab('AdminOrderRestrictSettings', 'order restrict configuration', false, false);
        $this->installTab('AdminHotelGeneralSettings', 'Hotel General configuration', false, false);
        $this->installTab('AdminOtherHotelModulesSetting', 'other hotel configuration', false, false);
        $this->installTab('AdminPaymentsSetting', 'payments configuration', false, false);
        $this->installTab('AdminHotelFeaturePricesSettings', 'feature pricing configuration', false, false);

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
        $htl_helper = new HotelHelper();
        if (!parent::install()
            || !$this->callInstallTab()
            || !$htl_helper->insertDefaultHotelEntries()
            || !$htl_helper->deletePrestashopDefaultCategories()
            || !$htl_helper->deletePrestashopDefaultFeatures()
            || !$htl_helper->createHotelRoomDefaultFeatures()
            || !$htl_helper->insertHotelCommonFeatures()
            || !$htl_helper->insertHotelRoomsStatus()
            || !$htl_helper->insertHotelOrderStatus()
            || !$htl_helper->insertHotelRoomAllotmentType()
            || !$htl_helper->createDummyDataForProject()
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayTop')
            || !$this->registerHook('displayAfterHookTop')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('actionOrderHistoryAddAfter')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('actionProductDelete')
            || !$this->registerHook('footer')
            || !$this->registerHook('displayAfterDefautlFooterHook')
            || !$this->registerHook('actionProductSave')
            || !$this->registerHook('addWebserviceResources')
        ) {
            return false;
        }

        // Set Default Values
        Configuration::updateValue('HTL_FEATURE_PRICING_PRIORITY', 'specific_date;special_day;date_range');
        Configuration::updateValue('WK_GOOGLE_ACTIVE_MAP', 0);
        Configuration::updateValue('WK_MAP_HOTEL_ACTIVE_ONLY', 1);

        // Prestashop logo's
        Configuration::updateValue('PS_LOGO', 'logo.jpg');
        Configuration::updateValue('PS_STORES_ICON', 'logo_stores.gif');
        Configuration::updateValue('PS_LOGO_MAIL', 'logo_mail.jpg');
        Configuration::updateValue('PS_LOGO_INVOICE', 'logo_invoice.jpg');

        return true;
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_room_information`,
            `'._DB_PREFIX_.'htl_image`,
            `'._DB_PREFIX_.'htl_features`,
            `'._DB_PREFIX_.'htl_branch_features`,
            `'._DB_PREFIX_.'htl_branch_info`,
            `'._DB_PREFIX_.'htl_booking_detail`,
            `'._DB_PREFIX_.'htl_room_status`,
            `'._DB_PREFIX_.'htl_order_status`,
            `'._DB_PREFIX_.'htl_room_allotment_type`,
            `'._DB_PREFIX_.'htl_cart_booking_data`,
            `'._DB_PREFIX_.'htl_order_refund_stages`,
            `'._DB_PREFIX_.'htl_order_refund_info`,
            `'._DB_PREFIX_.'htl_order_refund_rules`,
            `'._DB_PREFIX_.'htl_customer_adv_payment`,
            `'._DB_PREFIX_.'htl_advance_payment`,
            `'._DB_PREFIX_.'htl_room_type`,
            `'._DB_PREFIX_.'htl_room_type_feature_pricing`,
            `'._DB_PREFIX_.'htl_order_restrict_date`');
    }

    public function callUninstallTab()
    {
        $this->uninstallTab('AdminHotelRoomsBooking');
        $this->uninstallTab('AdminHotelConfigurationSetting');
        $this->uninstallTab('AdminAddHotel');
        $this->uninstallTab('AdminHotelFeatures');
        $this->uninstallTab('AdminHotelReservationSystemManagement');
        $this->uninstallTab('AdminOrderRefundRequests');
        $this->uninstallTab('AdminHotelReservationSystemManagement');

        //removing tabs having no tabs displaying but entry in the ps_tab table
        $this->uninstallTab('AdminHotelGeneralSettings');
        $this->uninstallTab('AdminOrderRestrictSettings');
        $this->uninstallTab('AdminPaymentsSetting');
        $this->uninstallTab('AdminOtherHotelModulesSetting');
        $this->uninstallTab('AdminHotelFeaturePricesSettings');
        //End
        return true;
    }

    public function uninstallTab($class_name)
    {
        $id_tab = (int) Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
        } else {
            return false;
        }
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

    public function deleteConfigVars()
    {
        $configKeys = array('WK_HOTEL_LOCATION_ENABLE',
                     'WK_ROOM_LEFT_WARNING_NUMBER',
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
                     );

        foreach ($configKeys as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()
            || !$this->deleteConfigVars()
            || !$this->deleteTables()
            || !$this->callUninstallTab()) {
            return false;
        }

        return true;
    }
}
