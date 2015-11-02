<?php
	class HotelImage extends ObjectModel
	{
		public $id;	
		public $hotel_id;
		public $hotel_image_id;
		public $active;

		public static $definition = array(
			'table' => 'htl_image',
			'primary' => 'id',
			'fields' => array(
				'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
				'hotel_image_id' => array('type' => self::TYPE_STRING),
				'active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool')
			),
		);

	public function getAllImagesByHotelId($htl_id)
	{
		$sql = "SELECT * FROM `"._DB_PREFIX_."htl_image` WHERE `id_hotel` = ".(int)$htl_id;

		$htl_images = Db::getInstance()->executeS($sql);

		if ($htl_images)
			return $htl_images;
		else
			return false;
	}

	public function deleteByHotelId($htl_id)
	{
		$delete = Db::getInstance()->delete('htl_image', '`id_hotel`='.(int)$htl_id);
		return $delete;
	}

	public function validAddHotelMainImage($image)
	{
		if ($image['size'] > 0)
		{			
			if ($image['tmp_name'] != "")
			{
				if(!ImageManager::isCorrectImageFileExt($image['name']))
				  	return true;
			}
		}
		else
			return true;
	}

	public function validAddHotelOtherImage($image)
	{
		if (empty($image['name']))
			return;
		//if any one is invalid extension redirect
		foreach ($image['name'] as $img_name)
		{
			if ($img_name != "")
			{
				if(!ImageManager::isCorrectImageFileExt($img_name))
					return true;
			}
		}
	}

	public function uploadOtherImages($image, $hotel_id, $hotel_img_path)
	{
		//Upload More Images
		if(isset($image))
		{
			$other_images  = $image['tmp_name'];
			$count = count($other_images);
		}
		else
			$count = 0;	
		
		for ($i = 0; $i < $count; $i++)
		{
			$rand_name = $this->randomImageName();
			Db::getInstance()->insert('htl_image', array(
										'id_hotel' => (int) $hotel_id,
										'hotel_image_id' => pSQL($rand_name)
									));
			$image_name = $rand_name.'.jpg';
			ImageManager::resize($other_images[$i], $hotel_img_path.$image_name);
		}
	}

	public function uploadMainImage($image, $hotel_id, $hotel_img_path, $active = false)
	{
		if (isset($image))
		{
			if ($image['size'] > 0)
			{
				$rand_name = $this->randomImageName();
				Db::getInstance()->insert('htl_image',
								array('id_hotel' => (int) $hotel_id,
									'hotel_image_id' => pSQL($rand_name),
							));
				$image_name = $rand_name.'.jpg';
				ImageManager::resize($image['tmp_name'], $hotel_img_path.$image_name);
			}
		}
	}

	public function randomImageName()
	{
		$length = 6;
		$characters= "0123456789abcdefghijklmnopqrstuvwxyz";
		$rand = '';
		
		for ($i = 0; $i < $length; $i++)
			$rand = $rand.$characters[mt_rand(0, Tools::strlen($characters)-1)];
		return $rand;
	}
	}
