<?php
	class WkHotelFeaturesData extends ObjectModel
	{
		public $id;
		public $blog_heading;
		public $blog_description;
		public $feature_image;
		public $feature_title;
		public $feature_description;
		public $is_blog;
		public $date_add;
		public $date_upd;

		public static $definition = array(
			'table' => 'htl_features_block_data',
			'primary' => 'id',
			'fields' => array(
				'blog_heading' =>			array('type' => self::TYPE_STRING),
				'blog_description' =>		array('type' => self::TYPE_STRING),
				'feature_image' =>			array('type' => self::TYPE_STRING),
				'feature_title' =>			array('type' => self::TYPE_STRING),
				'feature_description' =>	array('type' => self::TYPE_STRING),
				'is_blog' =>				array('type' => self::TYPE_INT),
				'date_add' =>				array('type' => self::TYPE_DATE),
				'date_upd' =>				array('type' => self::TYPE_DATE),
		));

		public function getAllFeaturesData()
		{
			$result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'htl_features_block_data WHERE is_blog=0');
			if ($result)
				return $result;
			return false; 
		}

		public function getMainBlogData()
		{
			$result = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'htl_features_block_data WHERE is_blog=1');
			if ($result)
				return $result;
			return false; 
		}
	}