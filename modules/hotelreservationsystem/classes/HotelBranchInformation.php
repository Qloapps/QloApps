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
		
		public function hotelBranchesInfo()
		{
			$result = Db::getInstance()->executeS('SELECT `id` , `hotel_name`, `id_category` FROM `'._DB_PREFIX_.'htl_branch_info`');
			if ($result)
				return $result;
			return false;
		}

		public function getActiveHotelBranchesInfo()
		{
			$result = Db::getInstance()->executeS('SELECT `id` , `hotel_name`, `id_category` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE active=1');
			if ($result)
				return $result;
			return false;
		}

		public function hotelBranchInfoById($id)
		{
			$result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id='.$id);
			if ($result)
				return $result;
			return false;
		}

		public function hotelsNameAndId()
		{
			$result = Db::getInstance()->executeS('SELECT id , hotel_name FROM `'._DB_PREFIX_.'htl_branch_info`');
			if ($result)
				return $result;
			return false;
		}

		public function getUnassignedFeaturesHotelIds()
		{
			$result = Db::getInstance()->executeS('SELECT id , hotel_name FROM `'._DB_PREFIX_.'htl_branch_info` WHERE `id` NOT IN (SELECT DISTINCT id_hotel FROM `'._DB_PREFIX_.'htl_branch_features`)');
			if ($result)
				return $result;
			return false;
		}

		public function getFeaturesOfHotelByHotelId($id_hotel)
		{
			$result = Db::getInstance()->executeS('SELECT feature_id FROM `'._DB_PREFIX_.'htl_branch_features` WHERE id_hotel='.$id_hotel);
			if ($result)
				return $result;
			return false;
		}

		public static function getHotelIdByIdCategory($id_category)
		{
			$result = Db::getInstance()->getValue('SELECT id FROM `'._DB_PREFIX_.'htl_branch_info` WHERE id_category='.$id_category);
			if ($result)
				return $result;
			return false;
		}

		public function getCategoryDataByIdCategory($id_category)
		{
			$result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'category_lang` WHERE id_category='.$id_category);
			if ($result)
				return $result;
			
			return false;
		}
	}