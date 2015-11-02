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
        $this->version = '1.6.1';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Hotel Booking and Reservation System');
        $this->description = $this->l('Now you can be able to build your website for your hotels for their bookings and reservations by using this module.');
        $this->confirmUninstall = $this->l('Are you sure? All module data will be lost after uninstalling the module');
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

        $cart_products = $cart->getProducts();
        
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
        			$obj_htl_bk_dtl->id_product = 	$v['id_product'];
					$obj_htl_bk_dtl->id_order = 	$order->id;
					$obj_htl_bk_dtl->id_cart = 		$cart->id;
					$obj_htl_bk_dtl->id_room = 		$obj_cart_bk_data->id_room;
					$obj_htl_bk_dtl->id_hotel = 	$obj_cart_bk_data->id_hotel;
					$obj_htl_bk_dtl->id_customer = 	$customer->id;
					$obj_htl_bk_dtl->booking_type = $obj_cart_bk_data->booking_type;
					$obj_htl_bk_dtl->id_status = 	1;
					$obj_htl_bk_dtl->comment = 		$obj_cart_bk_data->comment;
					$obj_htl_bk_dtl->date_from = 	$obj_cart_bk_data->date_from;
					$obj_htl_bk_dtl->date_to = 		$obj_cart_bk_data->date_to;
					$obj_htl_bk_dtl->save();
        		}
        	}
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
        $this->installTab('AdminAddHotel', 'Add Hotel', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminHotelFeatures', 'Manage Hotel Features', 'AdminHotelReservationSystemManagement');
        $this->installTab('AdminOrderRoomStatus', 'Manage Ordered Room Status', 'AdminHotelReservationSystemManagement');
        return true;
    }

    public function insertDefaultHotelEntries()
    {
        //from setting tab
        $home_banner_default_title = $this->l('Four Lessons Hotel Greshon Palace');
        $home_banner_default_content = $this->l('what is your hotel\'s best dish? describe in detail. An prepare one we want to taste it once. what is your hotel\'s best dish? ');

        Configuration::updateValue('WK_HOTEL_LOCATION_ENABLE', 1);
        Configuration::updateValue('WK_ROOM_LEFT_WARNING_NUMBER', 10);
        Configuration::updateValue('WK_HOTEL_GLOBAL_CONTACT_EMAIL', 'globalhotelemail@hotels.com');
        Configuration::updateValue('WK_HOTEL_GLOBAL_CONTACT_NUMBER', 9999999999);
        Configuration::updateValue('WK_TITLE_HEADER_BLOCK', $home_banner_default_title);
        Configuration::updateValue('WK_CONTENT_HEADER_BLOCK', $home_banner_default_content);

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
        /*$all_root_childrean_categories = Category::getAllCategoriesName();
        foreach ($all_root_childrean_categories as $cat_key => $cat_value)
        {
            if ($cat_value['id_category'] > 2)
            {
                $obj_category = new Category($cat_value['id_category']);
                $obj_category->delete();
            }
        }*/
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
        $htl_room_ftrs = array('Wi-Fi','Restaurant', 'Room Service', 'Power BackUp', 'Refrigerator', 'News Paper');
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
                    $obj_feature_value->value[$lang['id_lang']] = $obj_feature->id.'.jpg';
                
                $obj_feature_value->save();
                if ($obj_feature_value->id)
                {
                    if (file_exists(_PS_IMG_DIR_.'rf/'.$pos.'.jpg'))
                        rename(_PS_IMG_DIR_.'rf/'.$pos.'.jpg', _PS_IMG_DIR_.'rf/'.$obj_feature->id.'.jpg');
                }
            }
            $pos++;
        }
        return true;
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
        return $tab->add();
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
            || !$this->registerHook('actionValidateOrder')
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
            `'._DB_PREFIX_.'htl_room_type`');
    }
        
    public function callUninstallTab()
    {
        $this->uninstallTab('AdminHotelRoomsBooking');
        $this->uninstallTab('AdminHotelConfigurationSetting');
        $this->uninstallTab('AdminAddHotel');
        $this->uninstallTab('AdminHotelFeatures');
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
        $var = array('WK_HOTEL_LOCATION_ENABLE', 'WK_ROOM_LEFT_WARNING_NUMBER', 'WK_HOTEL_GLOBAL_CONTACT_EMAIL', 'WK_HOTEL_GLOBAL_CONTACT_NUMBER');

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