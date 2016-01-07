<?php
	class WkHotelTestimonialData extends ObjectModel
	{
		public $id;
		public $name;
		public $designation;
		public $testimonial_heading;
		public $testimonial_description;
		public $testimonial_content;
		public $testimonial_image;
		public $parent_data;
		public $date_add;
		public $date_upd;

		public static $definition = array(
			'table' => 'htl_testimonials_block_data',
			'primary' => 'id',
			'fields' => array(
				'name' =>					array('type' => self::TYPE_STRING),
				'designation'=> 			array('type' => self::TYPE_STRING),
				'testimonial_heading'=> 	array('type' => self::TYPE_STRING),
				'testimonial_description'=> array('type' => self::TYPE_STRING),
				'testimonial_content' =>	array('type' => self::TYPE_STRING),
				'testimonial_image' =>		array('type' => self::TYPE_STRING),
				'parent_data' =>			array('type' => self::TYPE_INT),
				'date_add' =>				array('type' => self::TYPE_DATE),
				'date_upd' =>				array('type' => self::TYPE_DATE),
		));

		public function getAllTestimonialsData()
		{
			$result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_testimonials_block_data` WHERE `parent_data`=0');
			if ($result)
				return $result;
			return false; 
		}

		public function getParentTestimonialsData()
		{
			$result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_testimonials_block_data` WHERE `parent_data`=1');
			if ($result)
				return $result;
			return false; 
		}
	}