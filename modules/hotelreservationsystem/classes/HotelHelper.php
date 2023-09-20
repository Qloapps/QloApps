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

class HotelHelper
{
    public static function assignDataTableVariables()
    {
        $objModule = new HotelreservationSystem();
        $jsVars = array(
                'display_name' => $objModule->l('Display', 'HotelHelper', false, true),
                'records_name' => $objModule->l('records per page', 'HotelHelper', false, true),
                'no_product' => $objModule->l('No records found', 'HotelHelper', false, true),
                'show_page' => $objModule->l('Showing page', 'HotelHelper', false, true),
                'show_of' => $objModule->l('of', 'HotelHelper', false, true),
                'no_record' => $objModule->l('No records available', 'HotelHelper', false, true),
                'filter_from' => $objModule->l('filtered from', 'HotelHelper', false, true),
                't_record' => $objModule->l('total records', 'HotelHelper', false, true),
                'search_item' => $objModule->l('Search', 'HotelHelper', false, true),
                'p_page' => $objModule->l('Previous', 'HotelHelper', false, true),
                'n_page' => $objModule->l('Next', 'HotelHelper', false, true),
            );

        Media::addJsDef($jsVars);
    }

    public function insertHotelCommonFeatures()
    {
        $parent_features_arr = array(
            'Business Services' => array(
                'Business Center',
                'Audio-Visual Equipment',
                'Board room',
                'Conference Facilities',
                'Secretaial Services',
                'Fax Machine',
                'Internet Access'
            ),
            'Complementry' => array(
                'Internet Access Free',
                'Transfer Available',
                'NewsPaper In Lobby',
                'Shopping Drop Facility',
                'Welcome Drinks'
            ),
            'Entertainment' => array(
                'DiscoTheatre',
                'Casino',
                ' Amphitheatre',
                'Dance Performances(On Demand)',
                'Karoke',
                'Mini Theatre',
                'Night Club'
            ),
            'Facilities' => array(
                'Laundary Service',
                'Power BackUp',
                'ATM/Banking',
                'Currency Exchange',
                'Dry Cleaning',
                'Library',
                'Doctor On Call',
                'Party Hall',
                'Yoga Hall',
                'Pets Allowed',
                'Kids Play Zone',
                'Wedding Services Facilities',
                'Fire Place Available'
            ),
            'General Services' => array(
                'Room Service',
                'Cook Service',
                'Car Rental',
                'Door Man',
                'Grocery',
                'Medical Assistance',
                'Postal Services',
                'Spa Services',
                'Multilingual Staff'
            ),
            'Indoors' => array(
                'Parking',
                'Solarium',
                'Veranda'
            ),
            'Internet' => array(
                'Internet Access-Surcharge',
                'Internet / Fax (Reception area only)'
            ),
            'Outdoors' => array(
                'Gardens',
                'Outdoor Parking - Secured',
                'Barbecue AreaCampfire / Bon Fire',
                'Childrens Park',
                'Fishing',
                'Golf Course',
                'Outdoor Parking - Non Secured',
                'Private Beach',
                'Rooftop Garden'
            ),
            'Parking' => array(
                'Parking (Surcharge)',
                'Parking Facilities Available',
                'Valet service'
            ),
            'Sports And Recreation' => array(
                'Health Club / Gym Facility Available',
                'Bike on Rent',
                'Badminttion Court',
                'Basketball Court',
                'Billiards' ,
                'Boating' ,
                'Bowling',
                'Camel Ride',
                'Clubhouse' ,
                'Fitness Equipment',
                'Fun Floats',
                'Games Zone',
                'Horse Ride ( Chargeable )',
                'Marina On Site',
                'Nature Walk',
                'Pool Table',
                'Safari',
                'Skiing Facility',
                'Available Spa Services',
                'NearbySquash court',
                'Table Tennis',
                'Tennis Court',
                'Virtual Golf'
            ),
            'Water Amenites' => array(
                'Swimming Pool',
                'Jacuzzi',
                'Private / Plunge Pool',
                'Sauna','Whirlpool Bath / Shower Cubicle'
            ),
            'Wine And Dine' => array(
                'Bar / Lounge',
                'Multi Cuisine Restaurant',
                'Catering',
                'Coffee Shop / Cafe',
                'Food Facility',
                'Hookah Lounge','Kitchen available (home cook food on request)',
                'Open Air Restaurant / Dining' ,'Pool Cafe',
                'Poolside Bar',
                'Restaurant Veg / Non Veg Kitchens Separate',
                'Vegetarian Food / Jain Food Available'
            ),
        );
        // lang fields
        $languages = Language::getLanguages(false);
        $i = 1;
        foreach ($parent_features_arr as $key => $value) {
            $obj_feature = new HotelFeatures();
            foreach ($languages as $lang) {
                $obj_feature->name[$lang['id_lang']] = $key;
            }
            $obj_feature->active = 1;
            $obj_feature->position = $i;
            $obj_feature->parent_feature_id = 0;
            $obj_feature->save();
            $parent_feature_id = $obj_feature->id;
            foreach ($value as $val) {
                $obj_feature = new HotelFeatures();
                foreach ($languages as $lang) {
                    $obj_feature->name[$lang['id_lang']] = $val;
                }
                $obj_feature->active = 1;
                $obj_feature->parent_feature_id = $parent_feature_id;
                $obj_feature->save();
            }
            ++$i;
        }

        return true;
    }

    public function insertDefaultHotelEntries()
    {
        //from setting tab
        $home_banner_default_title = 'Four Lessons Hotel Greshon Palace';
        $home_banner_default_content = 'Tofu helvetica leggings tattooed. Skateboard blue bottle green juice, brooklyn cardigan kitsch fap narwhal organic flexitarian.';

        Configuration::updateValue('WK_HOTEL_LOCATION_ENABLE', 1);
        Configuration::updateValue('WK_HOTEL_NAME_ENABLE', 1);
        Configuration::updateValue('WK_ROOM_LEFT_WARNING_NUMBER', 10);
        Configuration::updateValue('WK_HTL_ESTABLISHMENT_YEAR', 2010);

        Configuration::updateValue(
            'WK_HOTEL_GLOBAL_ADDRESS',
            'The Hotel Prime, Monticello Dr, Montgomery, AL 36117, USA'
        );
        Configuration::updateValue('WK_HOTEL_GLOBAL_CONTACT_NUMBER', '0987654321');
        Configuration::updateValue('WK_HOTEL_GLOBAL_CONTACT_EMAIL', 'hotelprime@htl.com');

        Configuration::updateValue('WK_TITLE_HEADER_BLOCK', $home_banner_default_title);
        Configuration::updateValue('WK_CONTENT_HEADER_BLOCK', $home_banner_default_content);
        Configuration::updateValue('WK_HOTEL_HEADER_IMAGE', 'hotel_header_image.jpg');
        Configuration::updateValue('WK_ALLOW_ADVANCED_PAYMENT', 1);
        Configuration::updateValue('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT', 10);
        Configuration::updateValue('WK_ADVANCED_PAYMENT_INC_TAX', 1);

        Configuration::updateValue('WK_GLOBAL_CHILD_MAX_AGE', 15);
        Configuration::updateValue('WK_GLOBAL_MAX_CHILD_IN_ROOM', 3);

        Configuration::updateValue(
            'MAX_GLOBAL_BOOKING_DATE',
            date('d-m-Y', strtotime(date('Y-m-d', time()).' + 1 year'))
        );

        Configuration::updateValue('GLOBAL_PREPARATION_TIME', 0);

        Configuration::updateValue('HTL_FEATURE_PRICING_PRIORITY', 'specific_date;special_day;date_range');
        Configuration::updateValue('WK_GOOGLE_ACTIVE_MAP', 0);
        Configuration::updateValue('WK_MAP_HOTEL_ACTIVE_ONLY', 1);

        // Prestashop logo's
        Configuration::updateValue('PS_LOGO', 'logo.jpg');
        Configuration::updateValue('PS_STORES_ICON', 'logo_stores.gif');
        Configuration::updateValue('PS_LOGO_MAIL', 'logo_mail.jpg');
        Configuration::updateValue('PS_LOGO_INVOICE', 'logo_invoice.jpg');

        // lang fields
        $languages = Language::getLanguages(false);
        $WK_HTL_CHAIN_NAME = array();
        $WK_HTL_TAG_LINE = array();
        $WK_HTL_SHORT_DESC = array();
        foreach ($languages as $lang) {
            $WK_HTL_CHAIN_NAME[$lang['id_lang']] = 'Hotel Dominic Parks';
            $WK_HTL_TAG_LINE[$lang['id_lang']] = 'Tofu helvetica leggings tattooed. Skateboard blue bottle green juice, brooklyn cardigan kitsch fap narwhal organic flexitarian.';
            $WK_HTL_SHORT_DESC[$lang['id_lang']] = 'Tofu helvetica leggings tattooed. Skateboard blue bottle green juice, brooklyn cardigan kitsch fap narwhal organic flexitarian.';
        }
        Configuration::updateValue('WK_HTL_CHAIN_NAME', $WK_HTL_CHAIN_NAME);
        Configuration::updateValue('WK_HTL_TAG_LINE', $WK_HTL_TAG_LINE);
        Configuration::updateValue('WK_HTL_SHORT_DESC', $WK_HTL_SHORT_DESC);

        // Search Fields
        Configuration::updateValue('PS_FRONT_SEARCH_TYPE', HotelBookingDetail::SEARCH_TYPE_OWS);
        Configuration::updateValue('PS_FRONT_OWS_SEARCH_ALGO_TYPE', HotelBookingDetail::SEARCH_EXACT_ROOM_TYPE_ALGO);
        Configuration::updateValue('PS_FRONT_ROOM_UNIT_SELECTION_TYPE', HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY);
        Configuration::updateValue('PS_BACKOFFICE_SEARCH_TYPE', HotelBookingDetail::SEARCH_TYPE_OWS);
        Configuration::updateValue('PS_BACKOFFICE_OWS_SEARCH_ALGO_TYPE', HotelBookingDetail::SEARCH_ALL_ROOM_TYPE_ALGO);
        Configuration::updateValue('PS_BACKOFFICE_ROOM_BOOKING_TYPE', HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY);

        return true;
    }

    public function createHotelRoomDefaultFeatures()
    {
        $htl_room_ftrs = array(
            'Wi-Fi',
            'News Paper',
            'Power BackUp',
            'Refrigerator',
            'Restaurant',
            'Room Service',
            'Gym'
        );

        // image value in rf/ folder
        $pos = 1;

        foreach ($htl_room_ftrs as $room_ftr_k => $room_ftr_v) {
            $obj_feature = new Feature();
            foreach (Language::getLanguages(true) as $lang) {
                $obj_feature->name[$lang['id_lang']] = $room_ftr_v;
            }
            $obj_feature->position = $pos-1;
            $obj_feature->save();
            if ($obj_feature->id) {
                $obj_feature_value = new FeatureValue();
                $obj_feature_value->id_feature = $obj_feature->id;

                foreach (Language::getLanguages(true) as $lang) {
                    $obj_feature_value->value[$lang['id_lang']] = $obj_feature->id.'.jpg';
                }

                $obj_feature_value->save();
                if ($obj_feature_value->id) {
                    if (file_exists(_PS_IMG_DIR_.'rf/'.$pos.'.jpg')) {
                        rename(_PS_IMG_DIR_.'rf/'.$pos.'.jpg', _PS_IMG_DIR_.'rf/'.$obj_feature->id.'.jpg');
                    }
                }
            }

            $pos++;
        }

        return true;
    }

    public static function getPsProducts($id_lang, $start = 0, $limit = 0)
    {
        $sql = 'SELECT p.`id_product`, pl.`name`
            FROM `'._DB_PREFIX_.'product` p
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.
            Shop::addSqlRestrictionOnLang('pl').')
            WHERE pl.`id_lang` = '.(int)$id_lang.
            ' ORDER BY pl.`name`'.
            ($limit > 0 ? ' LIMIT '.(int)$start.','.(int)$limit : '');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function saveDummyHotelBranchInfo()
    {
        $obj_hotel_info = new HotelBranchInformation();
        $obj_hotel_info->active = 1;
        $obj_hotel_info->email = 'hotelprime@htl.com';
        $obj_hotel_info->check_in = '12:00';
        $obj_hotel_info->check_out = '11:00';

        // lang fields
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $obj_hotel_info->hotel_name[$lang['id_lang']] = 'The Hotel Prime';
            $obj_hotel_info->short_description[$lang['id_lang']] = 'Nice place to stay';
            $obj_hotel_info->description[$lang['id_lang']] = 'Nice place to stay';
            $obj_hotel_info->policies[$lang['id_lang']] = '1. intelligentsia tattooed pop-up salvia asymmetrical mixtape
            meggings tousled ramps VHS cred. 2. intelligentsia tattooed pop-up salvia asymmetrical mixtape meggings tousled
            ramps VHS cred. 3. intelligentsia tattooed pop-up salvia asymmetrical mixtape meggings tousled ramps VHS cred.
            4. intelligentsia tattooed pop-up salvia asymmetrical mixtape meggings tousled ramps VHS cred.';
        }

        $obj_hotel_info->rating = 3;

        $obj_hotel_info->save();
        $htl_id = $obj_hotel_info->id;

        // add hotel address info
        $def_cont_id = Configuration::get('PS_COUNTRY_DEFAULT');

        if ($states = State::getStatesByIdCountry($def_cont_id)) {
            $state_id = $states[0]['id_state'];
        } else {
            $state_id = 0;
        }
        $objAddress = new Address();
        $objAddress->id_hotel = $htl_id;
        $objAddress->phone = '0987654321';
        $objAddress->city = 'Demo City';
        $objAddress->id_state = $state_id;
        $objAddress->id_country = $def_cont_id;
        $objAddress->postcode = self::getRandomZipcodeByForCountry($def_cont_id);
        $objAddress->address1 = 'Monticello Dr, Montgomery, AL 36117, USA';
        $objAddress->alias = 'The Hotel Prime';
        $objAddress->lastname = 'The Hotel Prime';
        $objAddress->firstname = 'The Hotel Prime';

        $objAddress->save();

        $grp_ids = array();
        $obj_grp = new Group();
        $data_grp_ids = $obj_grp->getGroups(1, $id_shop = false);

        foreach ($data_grp_ids as $key => $value) {
            $grp_ids[] = $value['id_group'];
        }
        $obj_country = new Country();
        $country_name = $obj_country->getNameById(Configuration::get('PS_LANG_DEFAULT'), $def_cont_id);
        $cat_country = $this->addCategory($country_name, false, $grp_ids);

        if ($cat_country) {
            $states = State::getStatesByIdCountry($def_cont_id);
            if (count($states) > 0) {
                $state_name = $states[0]['name'];
                $cat_state = $this->addCategory($state_name, $cat_country, $grp_ids);
            }
        }
        if (count($states) > 0) {
            if ($cat_state) {
                $cat_city = $this->addCategory('Demo City', $cat_state, $grp_ids);
            }
        } else {
            $cat_city = $this->addCategory('Demo City', $cat_country, $grp_ids);
        }
        if ($cat_city) {
            $cat_hotel = $this->addCategory('The Hotel Prime', $cat_city, $grp_ids, 1, $htl_id);
        }
        if ($cat_hotel) {
            $obj_hotel_info = new HotelBranchInformation($htl_id);
            $obj_hotel_info->id_category = $cat_hotel;
            $obj_hotel_info->save();
        }
        // save dummy hotel as primary hotel
        Configuration::updateValue('WK_PRIMARY_HOTEL', $htl_id);

        return $htl_id;
    }

    public function saveDummyHotelFeatures($id_hotel)
    {
        $branch_ftr_ids = array(1, 2, 4, 7, 8, 9, 11, 12, 14, 16, 17, 18, 21);
        foreach ($branch_ftr_ids as $value_ftr) {
            $htl_ftr_obj = new HotelBranchFeatures();
            $htl_ftr_obj->id_hotel = $id_hotel;
            $htl_ftr_obj->feature_id = $value_ftr;
            $htl_ftr_obj->save();
        }
    }

    public function saveDummyProductsAndRelatedInfo($id_hotel)
    {
        $prod_arr = array('General Rooms', 'Delux Rooms', 'Executive Rooms', 'Luxury Rooms');
        $prod_price_arr = array(1000, 1500, 2000, 2500);
        foreach ($prod_arr as $key => $value_prod) {
            // Add Product
            $product = new Product();
            $product->name = array();
            $product->description = array();
            $product->description_short = array();
            $product->link_rewrite = array();
            foreach (Language::getLanguages(true) as $lang) {
                $product->name[$lang['id_lang']] = $value_prod;
                $product->description[$lang['id_lang']] = 'Fashion axe kogi yuccie, ramps shabby chic direct trade
                before they sold out distillery bicycle rights. Slow-carb +1 quinoa VHS. +1 brunch trust fund, meggings
                chartreuse sustainable everyday carry tumblr hoodie tacos tilde ramps post-ironic fixie.';
                $product->description_short[$lang['id_lang']] = 'Fashion axe kogi yuccie, ramps shabby chic direct
                trade before they sold out distillery bicycle rights. Slow-carb +1 quinoa VHS. +1 brunch trust fund,
                meggings chartreuse sustainable everyday carry tumblr hoodie tacos tilde ramps post-ironic fixie.';
                $product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite('Super Delux Rooms');
            }
            $product->id_shop_default = Context::getContext()->shop->id;
            $product->id_category_default = 2;
            $product->price = $prod_price_arr[$key];
            $product->active = 1;
            $product->quantity = 999999999;
            $product->booking_product = true;
            $product->show_at_front = 1;
            $product->is_virtual = 1;
            $product->indexed = 1;
            $product->save();
            $product_id = $product->id;

            Search::indexation(Tools::link_rewrite($value_prod), $product_id);

            // assign all the categories of hotel and its parent to the product
            if (Validate::isLoadedObject($objHotel = new HotelBranchInformation($id_hotel))) {
                $hotelIdCategory = $objHotel->id_category;
                if (Validate::isLoadedObject($objCategory = new Category($hotelIdCategory))) {
                    if ($hotelCategories = $objCategory->getParentsCategories()) {
                        $categoryIds = array();
                        foreach ($hotelCategories as $rowCateg) {
                            $categoryIds[] = $rowCateg['id_category'];
                        }
                        $product->addToCategories($categoryIds);
                        // set the default category to the hotel category
                        $product->id_category_default = $hotelIdCategory;
                        $product->save();
                    }
                }
            }

            StockAvailable::updateQuantity($product_id, null, 999999999);

            //image upload for products
            $image_dir_path = _PS_MODULE_DIR_.'hotelreservationsystem/views/img/prod_imgs/'.($key+1).'/';
            $imagesTypes = ImageType::getImagesTypes('products');
            $count = 0;
            $have_cover = false;
            if (is_dir($image_dir_path)) {
                if ($opendir = opendir($image_dir_path)) {
                    while (($image = readdir($opendir)) !== false) {
                        $old_path = $image_dir_path.$image;

                        if (ImageManager::isRealImage($old_path)
                            && ImageManager::isCorrectImageFileExt($old_path)
                        ) {
                            $image_obj = new Image();
                            $image_obj->id_product = $product_id;
                            $image_obj->position = Image::getHighestPosition($product_id) + 1;

                            if ($count == 0) {
                                if (!$have_cover) {
                                    $image_obj->cover = 1;
                                    $have_cover = true;
                                }
                                $count++;
                            } else {
                                $image_obj->cover = 0;
                            }
                            $image_obj->add();
                            $new_path = $image_obj->getPathForCreation();
                            foreach ($imagesTypes as $image_type) {
                                ImageManager::resize(
                                    $old_path,
                                    $new_path.'-'.$image_type['name'].'.jpg',
                                    $image_type['width'],
                                    $image_type['height']
                                );
                            }
                            ImageManager::resize($old_path, $new_path.'.jpg');
                        }
                    }
                    closedir($opendir);
                }
            }

            for ($k = 1; $k <= 5; ++$k) {
                $htl_room_info_obj = new HotelRoomInformation();
                $htl_room_info_obj->id_product = $product_id;
                $htl_room_info_obj->id_hotel = $id_hotel;
                $htl_room_info_obj->room_num = 'A-10'.$k;
                $htl_room_info_obj->id_status = 1;
                $htl_room_info_obj->floor = 'First';
                $htl_room_info_obj->save();
            }

            $htl_rm_type = new HotelRoomType();
            $htl_rm_type->id_product = $product_id;
            $htl_rm_type->id_hotel = $id_hotel;
            $htl_rm_type->adults = 2;
            $htl_rm_type->children = 2;
            $htl_rm_type->max_adults = 2;
            $htl_rm_type->max_children = 2;
            $htl_rm_type->max_guests = 4;

            $htl_rm_type->save();

            // Add features to the product
            $ftr_arr = array(0 => 1, 1 => 2, 2 => 3, 3 => 4);
            $ftr_val_arr = array(0 => 1, 1 => 2, 2 => 3, 3 => 4);
            foreach ($ftr_arr as $key_htl_ftr => $val_htl_ftr) {
                $product->addFeaturesToDB($val_htl_ftr, $ftr_val_arr[$key_htl_ftr]);
            }

            // save advance payment information
            $this->saveAdvancedPaymentInfo($product_id);
        }
    }

    public function saveAdvancedPaymentInfo($id_product)
    {
        $obj_adv_pmt = new HotelAdvancedPayment();
        $obj_adv_pmt->active = 0;
        $obj_adv_pmt->id_product = $id_product;
        $obj_adv_pmt->payment_type = '';
        $obj_adv_pmt->value = '';
        $obj_adv_pmt->id_currency = '';
        $obj_adv_pmt->tax_include = '';
        $obj_adv_pmt->calculate_from = 0;
        return $obj_adv_pmt->save();
    }

    public function saveDummyHotelImages($idHotel)
    {
        if ($idHotel) {
            $objHotelImage = new HotelImage();
            if (is_dir(_PS_HOTEL_IMG_DIR_)) {
                foreach(scandir(_PS_HOTEL_IMG_DIR_) as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    if (preg_match('/[^\\s]+\.jpg$/', $file)) {
                        $imageDetail = $objHotelImage->uploadHotelImages(
                            array('tmp_name' => _PS_HOTEL_IMG_DIR_.$file),
                            $idHotel
                        );
                        unlink(_PS_HOTEL_IMG_DIR_.$file);
                    }
                }
            }
        }
    }

    public function createDummyDataForProject()
    {
        $htl_id = $this->saveDummyHotelBranchInfo();
        $this->saveDummyHotelImages($htl_id);
        $this->saveDummyHotelFeatures($htl_id);
        $this->saveDummyProductsAndRelatedInfo($htl_id);

        return true;
    }

    public function addCategory($name, $parent_cat = false, $group_ids, $ishotel = false, $hotel_id = false)
    {
        if (!$parent_cat) {
            $parent_cat = Configuration::get('PS_LOCATIONS_CATEGORY');
        }

        if ($ishotel && $hotel_id) {
            $cat_id_hotel = Db::getInstance()->getValue(
                'SELECT `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id='.$hotel_id
            );
            if ($cat_id_hotel) {
                $obj_cat = new Category($cat_id_hotel);
                $obj_cat->name = array();
                $obj_cat->description = array();
                $obj_cat->link_rewrite = array();

                foreach (Language::getLanguages(true) as $lang) {
                    $obj_cat->name[$lang['id_lang']] = $name;
                    $obj_cat->description[$lang['id_lang']] = 'This category are for hotels only';
                    $obj_cat->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($name);
                }
                $obj_cat->id_parent = $parent_cat;
                $obj_cat->groupBox = $group_ids;
                $obj_cat->save();
                $cat_id = $obj_cat->id;

                return $cat_id;
            }
        }
        $context = Context::getContext();
        $check_category_exists = Category::searchByNameAndParentCategoryId($context->language->id, $name, $parent_cat);

        if ($check_category_exists) {
            return $check_category_exists['id_category'];
        } else {
            $obj = new Category();
            $obj->name = array();
            $obj->description = array();
            $obj->link_rewrite = array();

            foreach (Language::getLanguages(true) as $lang) {
                $obj->name[$lang['id_lang']] = $name;
                $obj->description[$lang['id_lang']] = 'This category are for hotels only';
                $obj->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($name);
            }
            $obj->id_parent = $parent_cat;
            $obj->groupBox = $group_ids;
            $obj->add();
            $cat_id = $obj->id;

            return $cat_id;
        }
    }

    public static function generateRandomCode($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $rand = '';
        for ($i = 0; $i < $length; ++$i) {
            $rand = $rand.$characters[mt_rand(0, Tools::strlen($characters) - 1)];
        }

        return $rand;
    }

    public static function getBaseDirUrl()
    {
        $forceSsl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $baseDirSsl = $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__;
        $baseDir = _PS_BASE_URL_.__PS_BASE_URI__;

        $startUrl = $forceSsl ? $baseDirSsl : $baseDir;
        return $startUrl;
    }

    // update lang values of Configuration lang type keys when importing new language from localization
    public static function updateConfigurationLangKeys($idNewLang, $langKeys)
    {
        if ($langKeys && $idNewLang) {
            if (!is_array($langKeys)) {
                $langKeys = array($langKeys);
            }
            $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
            foreach ($langKeys as $configKey) {
                Configuration::updateValue(
                    $configKey,
                    array($idNewLang => Configuration::get($configKey, $defaultLangId))
                );
            }
        }
        return true;
    }

    // update lang values of lang tables when importing new language from localization
    public static function updateLangTables($idNewLang, $langTables)
    {
        if ($langTables && $idNewLang) {
            if (!is_array($langTables)) {
                $langTables = array($langTables);
            }
            $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
            foreach ($langTables as $table) {
                if ($tableLangsVals = Db::getInstance()->executeS(
                    'SELECT * FROM `'._DB_PREFIX_.$table.'_lang` WHERE `id_lang` = '.(int) $defaultLangId
                )) {
                    foreach ($tableLangsVals as $defaultLangRow) {
                        $defaultLangRow['id_lang'] = $idNewLang;
                        $tableValue = '';
                        $flag = 0;
                        foreach ($defaultLangRow as $value) {
                            $content = str_replace("'", "\'", $value);
                            $tableValue .= ($flag != 0 ? ', ' : '')."'".$content."'";
                            $flag = 1;
                        }
                        Db::getInstance()->execute(
                            'INSERT INTO `'._DB_PREFIX_.$table.'_lang` VALUES ('.$tableValue.')'
                        );
                    }
                }
            }
        }
        return true;
    }

    public static function getRandomZipcodeByForCountry($idCountry)
    {
        $randZipCode = '';
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (Validate::isLoadedObject($objCountry = new Country($idCountry))) {
            if ($objCountry->need_zip_code) {
                if ($randZipCode = $objCountry->zip_code_format) {
                    $randZipCode = str_replace('N', mt_rand(0, 9), $randZipCode);
                    $randZipCode = str_replace('L', $alphabet[mt_rand(0, Tools::strlen($alphabet) - 1)], $randZipCode);
                    $randZipCode = str_replace('C', $objCountry->iso_code, $randZipCode);
                } else {
                    for ($i = 0; $i < 5; ++$i) {
                        $randZipCode .= mt_rand(0, 9);
                    }
                }
            }
        }
        return $randZipCode;
    }

    /**
     * Get Super Admin Of Prestashop
     * @return int Super Admin Employee ID
     */
    public static function getSupperAdmin()
    {
        if ($data = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'employee` ORDER BY `id_employee`')) {
            foreach ($data as $emp) {
                $employee = new Employee($emp['id_employee']);
                if ($employee->isSuperAdmin()) {
                    return $emp['id_employee'];
                }
            }
        }

        return false;
    }

    public static function getNumberOfDays($dateFrom, $dateTo)
    {
        $startDate = new DateTime($dateFrom);
        $endDate = new DateTime($dateTo);
        $daysDifference = $startDate->diff($endDate)->days;

        return $daysDifference;
    }
}
