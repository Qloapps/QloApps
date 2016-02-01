<?php
if (!defined('_PS_VERSION_'))
    exit;

require_once ('define.php');

class HotelReservationSystem extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    public function __construct()
    {
        $this->name = 'hotelreservationsystem';
        $this->version = '0.0.2';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Hotel Booking and Reservation System');
        $this->description = $this->l('Now you can be able to build your website for your hotels for their bookings and reservations by using this module.');
        $this->confirmUninstall = $this->l('Are you sure? All module data will be lost after uninstalling the module');
    }

    public function hookFooter($params)
    {
        /*id_guest is set to the context->cookie object because data mining for prestashop module is disabled in which id_guest was set before this*/
        if (!isset($this->context->cookie->id_guest)) 
            Guest::setNewGuest($this->context->cookie);

        // ddd($this->_path);
        // $this->context->controller->addCSS(($this->_path).'footertop.css', 'all');
        $this->context->smarty->assign(array(
            'HOOK_FOOTER_TOP'        => Hook::exec('displayFooterTop'),          // Hook created By Webkul
        ));

        $global_email = Configuration::get('WK_HOTEL_GLOBAL_CONTACT_EMAIL');
        if (!$global_email)
            $global_email = Configuration::get('PS_SHOP_EMAIL');

        $global_contact_num = Configuration::get('WK_HOTEL_GLOBAL_CONTACT_NUMBER');
        if (!$global_contact_num)
            $global_contact_num = 9999999999;

        $this->context->smarty->assign(array(
            'hotel_global_email'        => $global_email,
            'hotel_global_contact_num'  => $global_contact_num,
        ));
        
        return $this->display(__FILE__, 'footertop.tpl');
    }

    public function hookActionProductDelete($params)
    {
        if ($params['id_product'])
        {
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
        if (isset($isToggling))
        {
            $obj_htl_rm_info = new HotelRoomType();
            $htl_rm_info = $obj_htl_rm_info->getRoomTypeInfoByIdProduct($params['id_product']);
            if (isset($htl_rm_info) && $htl_rm_info)
            {
                $prod_htl_id = $htl_rm_info['id_hotel'];
                if (isset($prod_htl_id) && $prod_htl_id)
                {
                    $obj_hotel = new HotelBranchInformation($prod_htl_id);
                    if (!$obj_hotel->active)
                    {
                        $obj_hotel->toggleStatus();
                    }
                }
            }

        }
        else
        {
            if ($params['id_product'])
            {
                $obj_htl_rm_info = new HotelRoomType();
                $htl_rm_info = $obj_htl_rm_info->getRoomTypeInfoByIdProduct($params['id_product']);
                if (isset($htl_rm_info) && $htl_rm_info)
                {
                    $prod_htl_id = $htl_rm_info['id_hotel'];
                    if (isset($prod_htl_id) && $prod_htl_id)
                    {
                        $obj_hotel = new HotelBranchInformation($prod_htl_id);
                        if (!$obj_hotel->active)
                        {
                            $obj_product = new Product($params['id_product']);
                            if ($obj_product->active == 1)
                                $obj_product->toggleStatus();
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
        
        $cart_products = $cart->getProducts();

        // Deprecated in future
        // For Cart Lock
        // $this->is_back_order = 0;
        // foreach ($cart_products as $t_key => $t_value) 
        // {
        //     $rm_dtl = $obj_rm_type->getRoomTypeInfoByIdProduct($t_value['id_product']);
        //     $cart_bk_data = $obj_cart_bk_data->getOnlyCartBookingData($cart->id, $cart->id_guest, $t_value['id_product']);

        //     $cart_data = array();
        //     foreach ($cart_bk_data as $cd_key => $cd_val) 
        //     {
        //         $date_join = strtotime($cd_val['date_from']).strtotime($cd_val['date_to']);

        //         $cart_data[$date_join]['date_from'] = $cd_val['date_from'];
        //         $cart_data[$date_join]['date_to'] = $cd_val['date_to'];
        //         $cart_data[$date_join]['id_rms'][] = $cd_val['id_room'];
        //     }

        //     foreach ($cart_data as $cl_key => $cl_val) 
        //     {
        //         $avai_rm = $obj_htl_bk_dtl->DataForFrontSearch($cl_val['date_from'], $cl_val['date_to'], $rm_dtl['id_hotel'], $t_value['id_product'], 1);

        //         if (count($avai_rm['rm_data'][0]['data']['available']) < count($cl_val['id_rms']))
        //         {
        //             foreach ($cl_val['id_rms'] as $cr_key => $cr_val) 
        //             {
        //                 $isRmBooked = $obj_htl_bk_dtl->chechRoomBooked($cr_val, $cl_val['date_from'], $cl_val['date_to']);
        //                 if ($isRmBooked) 
        //                 {
        //                     $this->is_back_order = 1;         // Use for change in order status

        //                     $updData = array('is_back_order' => 1);
        //                     $updBy = array('id_cart' => $cart->id,
        //                                     'id_room' => $cr_val,
        //                                     'date_from' => $cl_val['date_from'],
        //                                     'date_to' => $cl_val['date_to']);

        //                     $obj_cart_bk_data->updateCartBookingData($updData, $updBy);
        //                 }
        //             }
        //         }
        //         else
        //         {
        //             foreach ($cl_val['id_rms'] as $cr_key => $cr_val) 
        //             {
        //                 $isRmBooked = $obj_htl_bk_dtl->chechRoomBooked($cr_val, $cl_val['date_from'], $cl_val['date_to']);
        //                 if ($isRmBooked) 
        //                 {
        //                     foreach ($avai_rm['rm_data'][0]['data']['available'] as $av_key => $av_val) 
        //                     {
        //                         if (!in_array($av_val['id_room'], $cl_val['id_rms'])) 
        //                         {
        //                             $cl_val['id_rms'][$cr_key] = $av_val['id_room'];

        //                             $updData = array('id_room' => $av_val['id_room']);
        //                             $updBy = array('id_cart' => $cart->id,
        //                                             'id_room' => $cr_val,
        //                                             'date_from' => $cl_val['date_from'],
        //                                             'date_to' => $cl_val['date_to']);

        //                             $obj_cart_bk_data->updateCartBookingData($updData, $updBy);
        //                             unset($avai_rm['rm_data'][0]['data']['available'][$av_key]);
        //                             break;
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        foreach ($cart_products as $k => $v) 
        {
            $obj_cart_bk_data = new HotelCartBookingData();
            $cart_bk_data = $obj_cart_bk_data->getOnlyCartBookingData($cart->id, $cart->id_guest, $v['id_product']);
            if ($cart_bk_data) 
            {
                foreach ($cart_bk_data as $cb_k => $cb_v) 
                {
                    $obj_cart_bk_data = new HotelCartBookingData($cb_v['id']);
                    $obj_cart_bk_data->id_order = $order->id;
                    $obj_cart_bk_data->id_customer = $customer->id;
                    $obj_cart_bk_data->save();

                    $obj_htl_bk_dtl = new HotelBookingDetail();
                    $obj_htl_bk_dtl->id_product =   $v['id_product'];
                    $obj_htl_bk_dtl->id_order =     $order->id;
                    $obj_htl_bk_dtl->id_cart =      $cart->id;
                    $obj_htl_bk_dtl->id_room =      $obj_cart_bk_data->id_room;
                    $obj_htl_bk_dtl->id_hotel =     $obj_cart_bk_data->id_hotel;
                    $obj_htl_bk_dtl->id_customer =  $customer->id;
                    $obj_htl_bk_dtl->booking_type = $obj_cart_bk_data->booking_type;
                    $obj_htl_bk_dtl->id_status =    1;
                    $obj_htl_bk_dtl->comment =      $obj_cart_bk_data->comment;

                    // For Back Order(Because of cart lock)
                    if ($obj_cart_bk_data->is_back_order) 
                        $obj_htl_bk_dtl->is_back_order = 1;

                    $obj_htl_bk_dtl->date_from =    $obj_cart_bk_data->date_from;
                    $obj_htl_bk_dtl->date_to =      $obj_cart_bk_data->date_to;
                    $obj_htl_bk_dtl->save();
                }
            }
        }

        // For Advanced Payment
        if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT'))
        {
            $obj_customer_adv = new HotelCustomerAdvancedPayment();
            $customer_adv_dtl = $obj_customer_adv->getClientAdvPaymentDtl($cart->id, $cart->id_guest);
            if ($customer_adv_dtl) 
            {
                $obj_customer_adv = new HotelCustomerAdvancedPayment($customer_adv_dtl['id']);

                $obj_customer_adv->id_customer = $customer->id;
                $obj_customer_adv->id_order = $order->id;
                
                //if currency is changed before order
                if ($cart->id_currency != $obj_customer_adv->id_currency)
                {
                    $obj_customer_adv->total_paid_amount = Tools::convertPriceFull($obj_customer_adv->total_paid_amount, new Currency($obj_customer_adv->id_currency), new Currency($cart->id_currency));

                    $order_amt = $order->total_paid;
                    $obj_customer_adv->id_currency = $cart->id_currency;
                } 
                else
                    $order_amt = $order->total_paid;

                $obj_customer_adv->total_order_amount = $order_amt;
                $obj_customer_adv->save();
            }
        }
    }

    // Deprecated in future
    // change order status in case of cart lock
    // public function hookActionOrderHistoryAddAfter($data)
    // {
    //     $order_history = $data['order_history'];
    //     $order = new Order((int)$order_history->id_order);
        
    //     if ($order->getCurrentState() != 9) 
    //     {
    //         if ($this->is_back_order) 
    //         {
    //             if ($order->payment == 'Bank wire')
    //             {
    //                 $mailVars = array(
    //                     '{bankwire_owner}' => Configuration::get('BANK_WIRE_OWNER'),
    //                     '{bankwire_details}' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
    //                     '{bankwire_address}' => nl2br(Configuration::get('BANK_WIRE_ADDRESS'))
    //                 );
    //             }
    //             elseif ($order->payment == 'Payment by check') 
    //             {
    //                 $mailVars = array(
    //                     '{cheque_name}' => Configuration::get('CHEQUE_NAME'),
    //                     '{cheque_address}' => Configuration::get('CHEQUE_ADDRESS'),
    //                     '{cheque_address_html}' => str_replace("\n", '<br />', Configuration::get('CHEQUE_ADDRESS')));
    //             }

    //             $extra_vars = $mailVars;
    //             $new_history = new OrderHistory();
    //             $new_history->id_order = (int)$order_history->id_order;
    //             $new_history->changeIdOrderState(9, $order, true);
    //             $new_history->addWithemail(true, $extra_vars);
    //         }
    //     }
    // }

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
        return true;
    }

    public function insertDefaultHotelEntries()
    {
        //from setting tab
        $home_banner_default_title = $this->l('Four Lessons Hotel Greshon Palace');
        $home_banner_default_content = $this->l('Tofu helvetica leggings tattooed. Skateboard blue bottle green juice, brooklyn cardigan kitsch fap narwhal organic flexitarian.');

        Configuration::updateValue('WK_HOTEL_LOCATION_ENABLE', 1);
        Configuration::updateValue('WK_ROOM_LEFT_WARNING_NUMBER', 10);
        Configuration::updateValue('WK_HOTEL_GLOBAL_CONTACT_EMAIL', 'globalhotelemail@hotels.com');
        Configuration::updateValue('WK_HOTEL_GLOBAL_CONTACT_NUMBER', 9999999999);
        Configuration::updateValue('WK_TITLE_HEADER_BLOCK', $home_banner_default_title);
        Configuration::updateValue('WK_CONTENT_HEADER_BLOCK', $home_banner_default_content);

        Configuration::updateValue('WK_ALLOW_ADVANCED_PAYMENT', 1);
        Configuration::updateValue('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT', 10);
        Configuration::updateValue('WK_ADVANCED_PAYMENT_INC_TAX', 1);

        return true;
    }
    
    public function insertHotelCommonFeatures()
    {
        $parent_features_arr = array(
            'Business Services'=>array('Business Center','Audio-Visual Equipment','Board room','Conference Facilities','Secretaial Services','Fax Machine','Internet Access'),

            'Complementry'=>array('Internet Access Free','Transfer Available','NewsPaper In Lobby','Shopping Drop Facility','Welcome Drinks'),

            'Entertainment'=>array('DiscoTheatre','Casino',' Amphitheatre','Dance Performances(On Demand)','Karoke','Mini Theatre','Night Club'),

            'Facilities'=>array('Laundary Service','Power BackUp','ATM/Banking','Currency Exchange','Dry Cleaning','Library','Doctor On Call','Party Hall','Yoga Hall','Pets Allowed','Kids Play Zone','Wedding Services Facilities','Fire Place Available'),

            'General Services'=>array('Room Service','Cook Service','Car Rental','Door Man','Grocery','Medical Assistance','Postal Services','Spa Services','Multilingual Staff'),

            'Indoors'=>array('Parking','Solarium','Veranda'),

            'Internet'=>array('Internet Access-Surcharge','Internet / Fax (Reception area only)'),

            'Outdoors'=>array('Gardens', 'Outdoor Parking - Secured', 'Barbecue AreaCampfire / Bon Fire', 'Childrens Park','Fishing', 'Golf Course', 'Outdoor Parking - Non Secured','Private Beach','Rooftop Garden'),

            'Parking'=>array('Parking (Surcharge)', 'Parking Facilities Available', 'Valet service'),

            'Sports And Recreation'=>array('Health Club / Gym Facility Available', 'Bike on Rent', 'Badminttion Court', 'Basketball Court', 'Billiards' ,'Boating' ,'Bowling', 'Camel Ride','Clubhouse' ,'Fitness Equipment','Fun Floats','Games Zone', 'Horse Ride ( Chargeable )', 'Marina On Site', 'Nature Walk', 'Pool Table','Safari', 'Skiing Facility', 'Available Spa Services', 'NearbySquash court','Table Tennis', 'Tennis Court','Virtual Golf'),

            'Water Amenites'=>array('Swimming Pool', 'Jacuzzi', 'Private / Plunge Pool', 'Sauna','Whirlpool Bath / Shower Cubicle'),

            'Wine And Dine'=>array('Bar / Lounge', 'Multi Cuisine Restaurant', 'Catering', 'Coffee Shop / Cafe', 'Food Facility', 'Hookah Lounge','Kitchen available (home cook food on request)', 'Open Air Restaurant / Dining' ,'Pool Cafe', 'Poolside Bar', 'Restaurant Veg / Non Veg Kitchens Separate', 'Vegetarian Food / Jain Food Available' )
        );
        $i=1;
        foreach ($parent_features_arr as $key => $value)
        {
            $obj_feature = new HotelFeatures();
            $obj_feature->name = $this->l($key);
            $obj_feature->active = 1;
            $obj_feature->position = $i;
            $obj_feature->parent_feature_id = 0;
            $obj_feature->save();
            $parent_feature_id = $obj_feature->id;
            foreach ($value as $val)
            {
                $obj_feature = new HotelFeatures();
                $obj_feature->name = $this->l($val);
                $obj_feature->active = 1;
                $obj_feature->parent_feature_id = $parent_feature_id;
                $obj_feature->save();
            }
            $i++;
        }
        return true;
    }

    public function insertHotelRoomsStatus()
    {
    	$room_status_arr = array('Available','Unavailable','Hold For Maintenance');
    	foreach ($room_status_arr as $key=>$value)
    	{
	    	$obj_room_status = new HotelRoomStatus();
	        $obj_room_status->status = $value;
	        $obj_room_status->save();
	    }
        return true;
    }

    public function insertHotelOrderStatus()
    {
        $order_status_arr = array('Alloted','Checked In','Checked Out');
        foreach ($order_status_arr as $key=>$value)
        {
            $obj_order_status = new HotelOrderStatus();
            $obj_order_status->status = $value;
            $obj_order_status->save();
        }
        return true;
    }

    public function insertHotelRoomAllotmentType()
    {
        $altment_type_arr = array('Random Allotment','Manual Allotment');
        foreach ($altment_type_arr as $key=>$value)
        {
            $obj_allotmanet_type = new HotelRoomAllotmentType();
            $obj_allotmanet_type->type = $value;
            $obj_allotmanet_type->save();
        }
        return true;
    }

    public function deletePrestashopDefaultCategories()
    {
        $all_root_childrean_categories = Category::getAllCategoriesName();
        foreach ($all_root_childrean_categories as $cat_key => $cat_value)
        {
            if ($cat_value['id_category'] > 2)
            {
                $obj_category = new Category($cat_value['id_category']);
                $obj_category->delete();
            }
        }
        return true;
    }

    public function deletePrestashopDefaultFeatures()
    {
        $all_features = Feature::getFeatures($this->context->language->id);
        foreach($all_features as $ftr_k => $ftr_v)
        {
            $obj_feature = new Feature($ftr_v['id_feature']);
            $obj_feature->delete();
        }
        return true;
    }

    public function createHotelRoomDefaultFeatures()
    {
        $htl_room_ftrs = array('Wi-Fi', 'News Paper', 'Power BackUp', 'Refrigerator','Restaurant', 'Room Service', 'Gym');
        $pos = 0;
        foreach ($htl_room_ftrs as $room_ftr_k => $room_ftr_v)
        {
            $obj_feature = new Feature();
            foreach (Language::getLanguages(true) as $lang)
                $obj_feature->name[$lang['id_lang']] = $room_ftr_v;
            $obj_feature->position = $pos;
            $obj_feature->save();
            if ($obj_feature->id)
            {
                $obj_feature_value = new FeatureValue();
                $obj_feature_value->id_feature = $obj_feature->id;
                
                foreach (Language::getLanguages(true) as $lang)
                    $obj_feature_value->value[$lang['id_lang']] = $obj_feature->id.'.png';
                
                $obj_feature_value->save();
                if ($obj_feature_value->id)
                {
                    if (file_exists(_PS_IMG_DIR_.'rf/'.$pos.'.png'))
                        rename(_PS_IMG_DIR_.'rf/'.$pos.'.png', _PS_IMG_DIR_.'rf/'.$obj_feature->id.'.png');
                }
            }
            $pos++;
        }
        return true;
    }

    public function createDummyDataForProject()
    {
        //delete privious products of prestashop
        $all_products = Product::getSimpleProducts(Configuration::get('PS_LANG_DEFAULT'));
        foreach ($all_products as $key_pro => $value_pro)
        {
            $obj_product = new Product($value_pro['id_product']);
            $obj_product->delete();
        }
        // first add a hotel.................
        $def_cont_id = Country::getDefaultCountryId();
        $obj_hotel_info = new HotelBranchInformation();
        $obj_hotel_info->active = 1;
        $obj_hotel_info->hotel_name = "The Hotel Prime";
        $obj_hotel_info->phone = 0123456789;
        $obj_hotel_info->email = "prime@htl.com";
        $obj_hotel_info->check_in = '12:00';
        $obj_hotel_info->check_out = '12:00';
        $obj_hotel_info->short_description = $this->l('Nice place to stay');
        $obj_hotel_info->description = $this->l('Nice place to stay');
        $obj_hotel_info->rating = 3;
        $obj_hotel_info->city = 'DefCity';
        $states = State::getStatesByIdCountry($def_cont_id);
        $state_id = $states[0]['id_state'];
        $obj_hotel_info->state_id = $state_id;
        $obj_hotel_info->country_id = $def_cont_id;
        $obj_hotel_info->zipcode = 263001;
        $obj_hotel_info->policies = $this->l('1. intelligentsia tattooed pop-up salvia asymmetrical mixtape meggings tousled ramps VHS cred. 2. intelligentsia tattooed pop-up salvia asymmetrical mixtape meggings tousled ramps VHS cred. 3. intelligentsia tattooed pop-up salvia asymmetrical mixtape meggings tousled ramps VHS cred. 4. intelligentsia tattooed pop-up salvia asymmetrical mixtape meggings tousled ramps VHS cred.');
        $obj_hotel_info->address = 'Near post office, Mallital, Nainital';
        $obj_hotel_info->save();

        $htl_id = $obj_hotel_info->id;

        $grp_ids = array();
        $obj_grp = new Group();
        $data_grp_ids = $obj_grp->getGroups(1, $id_shop = false);

        foreach ($data_grp_ids as $key => $value)
        {
            $grp_ids[] = $value['id_group'];
        }
        $country_name = (new Country())->getNameById(Configuration::get('PS_LANG_DEFAULT'),$def_cont_id);
        $cat_country = $this->addCategory($country_name, false, $grp_ids);

        if ($cat_country)
        {
            $states = State::getStatesByIdCountry($def_cont_id);
            if (count($states) > 0)
            {
                $state_name = $states[0]['name'];
                $cat_state = $this->addCategory($state_name, $cat_country, $grp_ids);
            }
        }
        if (count($states) > 0)
        {
            if ($cat_state)
                $cat_city = $this->addCategory('DefCity', $cat_state, $grp_ids);
        }
        else
            $cat_city = $this->addCategory('DefCity', $cat_country, $grp_ids);
        if ($cat_city)
            $cat_hotel = $this->addCategory('The Hotel Prime', $cat_city, $grp_ids, 1, $htl_id);
        if ($cat_hotel)
        {
            $obj_hotel_info = new HotelBranchInformation($htl_id);
            $obj_hotel_info->id_category = $cat_hotel;
            $obj_hotel_info->save();
        }

        $branch_ftr_ids = array(1, 2, 4, 7, 8, 9, 11, 12, 14, 16, 17, 18, 21);
        foreach ($branch_ftr_ids as $key_ftr => $value_ftr)
        {
            $htl_ftr_obj = new HotelBranchFeatures();
            $htl_ftr_obj->id_hotel = $htl_id;
            $htl_ftr_obj->feature_id = $value_ftr;
            $htl_ftr_obj->save();
        }

        $prod_arr = array('Delux Rooms', 'Executive Rooms', 'luxury Rooms');
        $img_num = 1;
        foreach ($prod_arr as $key_prod => $value_prod)
        {
            // Add Product
            $product = new Product();
            $product->name = array();
            $product->description = array();
            $product->description_short = array();
            $product->link_rewrite = array();
            foreach (Language::getLanguages(true) as $lang)
            {
                $product->name[$lang['id_lang']] = $value_prod;
                $product->description[$lang['id_lang']] = $this->l('Fashion axe kogi yuccie, ramps shabby chic direct trade before they sold out distillery bicycle rights. Slow-carb +1 quinoa VHS. +1 brunch trust fund, meggings chartreuse sustainable everyday carry tumblr hoodie tacos tilde ramps post-ironic fixie.');
                $product->description_short[$lang['id_lang']] = $this->l('Fashion axe kogi yuccie, ramps shabby chic direct trade before they sold out distillery bicycle rights. Slow-carb +1 quinoa VHS. +1 brunch trust fund, meggings chartreuse sustainable everyday carry tumblr hoodie tacos tilde ramps post-ironic fixie.');
                $product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite('Super Delux Rooms');
            }
            $product->id_shop_default = Context::getContext()->shop->id;
            $product->id_category_default = 2;
            $product->price = 1000;
            $product->active = 1;
            $product->quantity = 99999999;
            $product->is_virtual = 1;
            $product->indexed = 1;
            $product->save();
            $product_id = $product->id;

            Search::indexation(Tools::link_rewrite($value_prod),$product_id);
            
            $product->addToCategories(2);
            
            StockAvailable::updateQuantity($product_id, null, 99999999);

            //image upload for products
            $count = 0;
            $have_cover = false;
            $old_path = _PS_MODULE_DIR_.$this->name.'/views/img/prod_imgs/'.$img_num.'.png';
            $image_obj = new Image();
            $image_obj->id_product = $product_id;
            $image_obj->position = Image::getHighestPosition($product_id) + 1;

            if ($count == 0)
            {
                if (!$have_cover)
                    $image_obj->cover = 1;
            }
            else
                $image_obj->cover = 0;

            $image_obj->add();
            $new_path = $image_obj->getPathForCreation();
            $imagesTypes = ImageType::getImagesTypes('products');
            
            foreach ($imagesTypes as $image_type)
                ImageManager::resize($old_path, $new_path.'-'.$image_type['name'].'.jpg', $image_type['width'],$image_type['height']);
            
            ImageManager::resize($old_path,$new_path.'.jpg');

            for ($k=1; $k<=5; $k++)
            {
                $htl_room_info_obj = new HotelRoomInformation();
                $htl_room_info_obj->id_product = $product_id;
                $htl_room_info_obj->id_hotel = $htl_id;
                $htl_room_info_obj->room_num = 'A'.$i.'-10'.$k;
                $htl_room_info_obj->id_status = 1;
                $htl_room_info_obj->floor = 'first';
                $htl_room_info_obj->save();
            }

            $htl_rm_type = new HotelRoomType();
            $htl_rm_type->id_product = $product_id;
            $htl_rm_type->id_hotel = $htl_id;
            $htl_rm_type->adult = 2;
            $htl_rm_type->children = 2;
            $htl_rm_type->save();
            $img_num++;

            // Add features to the product
            $ftr_arr = array(0=>8, 1=>9, 2=>10, 3=>11);
            $ftr_val_arr = array(0=>34, 1=>35, 2=>36, 3=>37);
            foreach ($ftr_arr as $key_htl_ftr => $val_htl_ftr)
            {
                $product->addFeaturesToDB($val_htl_ftr, $ftr_val_arr[$key_htl_ftr]);
            }
        }
        return true;
    }

    public function addCategory($name, $parent_cat=false, $group_ids, $ishotel=false, $hotel_id=false)
    {
        if (!$parent_cat)
            $parent_cat = Category::getRootCategory()->id;

        if ($ishotel && $hotel_id)
        {
            $cat_id_hotel = Db::getInstance()->getValue('SELECT `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id='.$hotel_id);
            if ($cat_id_hotel)
            {
                $obj_cat = new Category($cat_id_hotel);
                $obj_cat->name = array();
                $obj_cat->description = array();
                $obj_cat->link_rewrite = array();

                foreach (Language::getLanguages(true) as $lang)
                {
                    $obj_cat->name[$lang['id_lang']] = $name;
                    $obj_cat->description[$lang['id_lang']] = $this->l('this category are for hotels only');
                    $obj_cat->link_rewrite[$lang['id_lang']] = $this->l(Tools::link_rewrite($name));
                }
                $obj_cat->id_parent = $parent_cat;
                $obj_cat->groupBox = $group_ids;
                $obj_cat->save();
                $cat_id = $obj_cat->id;
                return $cat_id;
            }
        }
        $check_category_exists = Category::searchByNameAndParentCategoryId($this->context->language->id, $name, $parent_cat);

        if ($check_category_exists)
            return $check_category_exists['id_category'];
        else
        {
            $obj = new Category();
            $obj->name = array();
            $obj->description = array();
            $obj->link_rewrite = array();

            foreach (Language::getLanguages(true) as $lang)
            {
                $obj->name[$lang['id_lang']] = $name;
                $obj->description[$lang['id_lang']] = $this->l('this category are for hotels only');
                $obj->link_rewrite[$lang['id_lang']] = $this->l(Tools::link_rewrite($name));
            }
            $obj->id_parent = $parent_cat;
            $obj->groupBox = $group_ids;
            $obj->add();
            $cat_id = $obj->id;
            return $cat_id;
        }
    }

    public function installTab($class_name,$tab_name,$tab_parent_name=false) 
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $tab_name;

        if($tab_parent_name)
            $tab->id_parent = (int)Tab::getIdFromClassName($tab_parent_name);
        else
            $tab->id_parent = 0;
        
        $tab->module = $this->name;
        $res = $tab->add();
        if ($tab_name == 'Hotel Reservation System')
        {
            $objTab = new Tab($tab->id);
            $objTab->updatePosition(0, 5);
        }
        return $res;
    }
    
    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;

        $sql = str_replace(array('PREFIX_',  'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query)
            if ($query)
                if (!Db::getInstance()->execute(trim($query)))
                    return false;

        if (!parent::install()
            || !$this->callInstallTab()
            || !$this->insertDefaultHotelEntries()
            || !$this->deletePrestashopDefaultCategories()
            || !$this->deletePrestashopDefaultFeatures()
            || !$this->createHotelRoomDefaultFeatures()
            || !$this->insertHotelCommonFeatures()
            || !$this->insertHotelRoomsStatus()
            || !$this->insertHotelOrderStatus()
            || !$this->insertHotelRoomAllotmentType()
            || !$this->createDummyDataForProject()
            || !$this->registerHook('footer')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('actionOrderHistoryAddAfter')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('actionProductDelete')
        )
            return false;
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
            `'._DB_PREFIX_.'htl_room_type`');
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
        return true;
    }
        
    public function uninstallTab($class_name)
    {
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        if ($id_tab)
        {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        else
            return false;
    }

    public function reset()
    {
        if (!$this->uninstall(false))
            return false;
        if (!$this->install(false))
            return false;
        return true;
    }

    public function deleteConfigVars()
    {
        $var = array('WK_HOTEL_LOCATION_ENABLE', 
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
                     );

        foreach ($var as $key)
            if (!Configuration::deleteByName($key))
                return false;
        
        return true;
    }

    public function uninstall($keep = true)
    {
        if(!parent::uninstall() 
            || !$this->deleteConfigVars()
            || !$this->deleteTables()
            || !$this->callUninstallTab())
            return false;

        return true;
    }
}
?>