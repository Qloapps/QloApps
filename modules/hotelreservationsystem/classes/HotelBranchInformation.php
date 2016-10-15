<?php
    class HotelBranchInformation extends ObjectModel
    {
        public $id;
        public $id_category;
        public $hotel_name;
        public $phone;
        public $email;
        public $check_in;
        public $check_out;
        public $description;
        public $short_description;
        public $rating;
        public $city;
        public $state_id;
        public $country_id;
        public $zipcode;
        public $address;
        public $policies;
        public $active;
        public $latitude;
        public $longitude;
        public $map_formated_address;
        public $map_input_text;
        public $date_add;
        public $date_upd;

        public static $definition = array(
            'table' => 'htl_branch_info',
            'primary' => 'id',
            'fields' => array(
                'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'hotel_name' => array('type' => self::TYPE_STRING),
                'description' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
                'short_description' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
                'phone' => array('type' => self::TYPE_STRING,'validate' => 'isPhoneNumber', 'size' => 32),
                'email' => array('type' => self::TYPE_STRING,'validate' => 'isEmail', 'size' => 255),
                'rating' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'check_in' => array('type' => self::TYPE_STRING),
                'check_out' => array('type' => self::TYPE_STRING),
                'address' => array('type' => self::TYPE_STRING),
                'policies' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
                'city' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
                'state_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'country_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'zipcode' => array('type' => self::TYPE_STRING),
                'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
                'latitude' => array('type' => self::TYPE_FLOAT),
                'longitude' => array('type' => self::TYPE_FLOAT),
                'map_formated_address' => array('type' => self::TYPE_HTML, 'size' => 128),
                'map_input_text' => array('type' => self::TYPE_STRING, 'size' => 128),
                'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
                'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ), );

        /**
         * [hotelBranchesInfo : To get all the hotels information created by the admin].
         *
         * @return [array | false] [If no hotel is created then returns false otherwise returns all hotels information array]
         */
        public function hotelBranchesInfo()
        {
            $result = Db::getInstance()->executeS('SELECT `id` , `hotel_name`, `id_category`, `country_id` FROM `'._DB_PREFIX_.'htl_branch_info`');
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [getActiveHotelBranchesInfo : To get all the activated hotels information created by the admin].
         *
         * @return [array | false] [If no hotel is created or activated then returns false otherwise returns all activated hotels information array]
         */
        public function getActiveHotelBranchesInfo()
        {
            $result = Db::getInstance()->executeS('SELECT `id` , `hotel_name`, `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE active=1');
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * Deprecated
         * [hotelBranchInfoById : Hotel's information By its id].
         *
         * @param [int] $id [id of the hotel which information is wanted]
         *
         * @return [array | false]     [If no hotel found with id sent then returns false otherwise returns all hotel's information array]
         */
        public function hotelBranchInfoById($id)
        {
            $result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id='.$id);
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * Deprecated
         * [hotelsNameAndId : To get array of All created Hotels name and id].
         *
         * @return [array | false] [If no hotel found then returns false otherwise returns array of all hotels name and id]
         */
        public function hotelsNameAndId()
        {
            $result = Db::getInstance()->executeS('SELECT id , hotel_name FROM `'._DB_PREFIX_.'htl_branch_info`');
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [getUnassignedFeaturesHotelIds : To get array of hotels id and name To which no hotel features are assigned].
         *
         * @return [array | false] [If no hotel found then returns false otherwise returns array of all hotels name and id to which no features are assigned]
         */
        public function getUnassignedFeaturesHotelIds()
        {
            $result = Db::getInstance()->executeS('SELECT id , hotel_name FROM `'._DB_PREFIX_.'htl_branch_info` WHERE `id` NOT IN (SELECT DISTINCT id_hotel FROM `'._DB_PREFIX_.'htl_branch_features`)');
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [getFeaturesOfHotelByHotelId : To get assigned Features of a hotel by its id].
         *
         * @param [type] $id_hotel [id of the hotel]
         *
         * @return [array | false] [If no feature found then returns false otherwise returns array of all features assigned to the hotel]
         */
        public function getFeaturesOfHotelByHotelId($id_hotel)
        {
            $result = Db::getInstance()->executeS('SELECT feature_id FROM `'._DB_PREFIX_.'htl_branch_features` WHERE id_hotel='.$id_hotel);
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [getFeaturesOfHotelByHotelId : To get Hotel's id by its category id].
         *
         * @param [int] $id_category [id_category of the hotel]
         *
         * @return [array | int] [If no hotel found then returns false otherwise returns id of the hotel]
         */
        public static function getHotelIdByIdCategory($id_category)
        {
            $result = Db::getInstance()->getValue('SELECT id FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id_category='.$id_category);
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [getFeaturesOfHotelByHotelId : To get Category Information by its id_category].
         *
         * @param [int] $id_category [id of the category , Which innformation is wanted]
         *
         * @return [array | false] [If no Category found then returns false otherwise returns array of information of that category]
         */
        public function getCategoryDataByIdCategory($id_category)
        {
            $result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'category_lang` WHERE id_category='.$id_category);
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [hotelBranchInfoByCategoryId : To get active Hotels which belongs to the passed category Id]
         * @param  [int] $cat_id [id of the category]
         * @return [array | false]         [retuns array of the information of hotels belongs to the category if found otherwise false]
         */
        public function hotelBranchInfoByCategoryId($cat_id)
        {
            $result = Db::getInstance()->executeS('SELECT `id`, `hotel_name`, `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE `id_category` ='.$cat_id.' AND `active`=1');

            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [getHotelCategoryTree : Select category info by name of the category]
         * @param  [string] $search_data [description]
         * @return [type]              [description]
         */
        public function getHotelCategoryTree($search_data)
        {
        	$context = Context::getContext();
            $sql = 'SELECT cl.`id_category` , cl.`name` 
					FROM `'._DB_PREFIX_.'category_lang` AS cl
					INNER JOIN `'._DB_PREFIX_."category` AS c ON (cl.id_category = c.id_category)
					WHERE cl.name LIKE '%$search_data%' AND c.level_depth NOT IN (0, 1, 5) and id_lang=".$context->language->id.' GROUP BY cl.`name`';

            $result = Db::getInstance()->executeS($sql);

            return $result;
        }

        /**
         * [getMapFormatHotelsInfo To get address ofthe hotel in the format to get the latitude and longitude of the place by using address]
         * @return [array] [description]
         */
        public function getMapFormatHotelsInfo($active = false, $id_lang = false)
        {
            if (!$id_lang) {
                $context = Context::getContext();
                $id_lang = $context->language->id;
            }

            $sql = "SELECT hbi.`hotel_name`, hbi.`phone`, hbi.`email`, hbi.`city`, cl.`name` AS country, hbi.`zipcode`, hbi.`address`, hbi.`latitude`, hbi.`longitude`, hbi.`map_formated_address`, hbi.`map_input_text`
                FROM `"._DB_PREFIX_."htl_branch_info` AS hbi
                INNER JOIN `"._DB_PREFIX_."country_lang` AS cl ON (cl.id_country = hbi.country_id AND cl.id_lang = ".$id_lang.")
                WHERE hbi.`latitude` != 0 AND hbi.`longitude` != 0";

            if ($active !== false) {
                if ($active) {
                    $sql .= " AND hbi.`active` = ".(int)$active;
                }
                return $hotel_map_array;
            } else {
                return false;
            }

            $result = Db::getInstance()->executeS($sql);
            if ($result) {
                return $result;
            }
            return false;

            // $htls_array = Db::getInstance()->executeS('SELECT `hotel_name`, `city`, `country_id`, `zipcode` FROM `'._DB_PREFIX_.'htl_branch_info`');
            // if ($htls_array) {
            //     $hotel_map_array = array();
            //     foreach ($htls_array as $hotel) {
            //         $country = new Country($hotel['country_id']);
            //         $hotel_map_array[] = $hotel['hotel_name'].', '.$hotel['city'].', '.$country->iso_code.' '.$hotel['zipcode'].', '.$country->name[1];
            //     }
            //     return $hotel_map_array;
            // } else {
            //     return false;
            // }
        }
    }
