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
		public $date_add;
		public $date_upd;

		public static $definition = array(
			'table' => 'htl_branch_info',
			'primary' => 'id',
			'fields' => array(
				'id_category' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'hotel_name' =>  		array('type' => self::TYPE_STRING),
				'description' => 		array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
				'short_description' => 	array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
				'phone' =>  	 		array('type' => self::TYPE_STRING,'validate' => 'isPhoneNumber', 'size' => 32),
				'email' =>  	 		array('type' => self::TYPE_STRING,'validate' => 'isEmail', 'size' => 255),
				'rating' => 	 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'check_in' => 	 		array('type' => self::TYPE_STRING),
				'check_out' =>   		array('type' => self::TYPE_STRING),
				'address' =>     		array('type' => self::TYPE_STRING),
				'policies' =>    		array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
				'city' => 		 		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
				'state_id' => 	 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'country_id' =>  		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'zipcode'=>      		array('type' => self::TYPE_STRING),
				'active' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
				'date_add' =>  	 		array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
				'date_upd' =>  	 		array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
		));
		
		/**
		 * [hotelBranchesInfo : To get all the hotels information created by the admin]
		 * @return [array | false] [If no hotel is created then returns false otherwise returns all hotels information array]
		 */
		public function hotelBranchesInfo()
		{
			$result = Db::getInstance()->executeS('SELECT `id` , `hotel_name`, `id_category` FROM `'._DB_PREFIX_.'htl_branch_info`');
			if ($result)
				return $result;
			return false;
		}

		/**
		 * [getActiveHotelBranchesInfo : To get all the activated hotels information created by the admin]
		 * @return [array | false] [If no hotel is created or activated then returns false otherwise returns all activated hotels information array]
		 */
		public function getActiveHotelBranchesInfo()
		{
			$result = Db::getInstance()->executeS('SELECT `id` , `hotel_name`, `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE active=1');
			if ($result)
				return $result;
			return false;
		}

		/**
		 * Deprecated
		 * [hotelBranchInfoById : Hotel's information By its id]
		 * @param  [int] $id [id of the hotel which information is wanted]
		 * @return [array | false]     [If no hotel found with id sent then returns false otherwise returns all hotel's information array]
		 */
		public function hotelBranchInfoById($id)
		{
			$result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id='.$id);
			if ($result)
				return $result;
			return false;
		}

		/**
		 * Deprecated
		 * [hotelsNameAndId : To get array of All created Hotels name and id]
		 * @return [array | false] [If no hotel found then returns false otherwise returns array of all hotels name and id]
		 */
		public function hotelsNameAndId()
		{
			$result = Db::getInstance()->executeS('SELECT id , hotel_name FROM `'._DB_PREFIX_.'htl_branch_info`');
			if ($result)
				return $result;
			return false;
		}

		/**
		 * [getUnassignedFeaturesHotelIds : To get array of hotels id and name To which no hotel features are assigned]
		 * @return [array | false] [If no hotel found then returns false otherwise returns array of all hotels name and id to which no features are assigned]
		 */
		public function getUnassignedFeaturesHotelIds()
		{
			$result = Db::getInstance()->executeS('SELECT id , hotel_name FROM `'._DB_PREFIX_.'htl_branch_info` WHERE `id` NOT IN (SELECT DISTINCT id_hotel FROM `'._DB_PREFIX_.'htl_branch_features`)');
			if ($result)
				return $result;
			return false;
		}

		/**
		 * [getFeaturesOfHotelByHotelId : To get assigned Features of a hotel by its id]
		 * @param  [type] $id_hotel [id of the hotel]
		 * @return [array | false] [If no feature found then returns false otherwise returns array of all features assigned to the hotel]
		 */
		public function getFeaturesOfHotelByHotelId($id_hotel)
		{
			$result = Db::getInstance()->executeS('SELECT feature_id FROM `'._DB_PREFIX_.'htl_branch_features` WHERE id_hotel='.$id_hotel);
			if ($result)
				return $result;
			return false;
		}

		/**
		 * [getFeaturesOfHotelByHotelId : To get Hotel's id by its category id]
		 * @param  [int] $id_category [id_category of the hotel]
		 * @return [array | int] [If no hotel found then returns false otherwise returns id of the hotel]
		 */
		public static function getHotelIdByIdCategory($id_category)
		{
			$result = Db::getInstance()->getValue('SELECT id FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id_category='.$id_category);
			if ($result)
				return $result;
			return false;
		}

		/**
		 * [getFeaturesOfHotelByHotelId : To get Category Information by its id_category]
		 * @param  [int] $id_category [id of the category , Which innformation is wanted]
		 * @return [array | false] [If no Category found then returns false otherwise returns array of information of that category]
		 */
		public function getCategoryDataByIdCategory($id_category)
		{
			$result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'category_lang` WHERE id_category='.$id_category);
			if ($result)
				return $result;
			
			return false;
		}

		public function hotelBranchInfoByCategoryId($cat_id)
		{
			$result = Db::getInstance()->executeS("SELECT `id`, `hotel_name`, `id_category` FROM `"._DB_PREFIX_.'htl_branch_info` WHERE `id_category` ='.$cat_id.' AND `active`=1');

			if ($result)
				return $result;
			return false;
		}

	}