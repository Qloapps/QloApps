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

	/**
	 * [getAllImagesByHotelId :: To get all images data of a hotel by hotel id]
	 * @param  [int] $htl_id [Id of the hotel which images data you want]
	 * @return [array|boolean] [if data found returns array containing information of the images of the hotel which id is passed]
	 */
	public function getAllImagesByHotelId($htl_id)
	{
		$sql = "SELECT * FROM `"._DB_PREFIX_."htl_image` WHERE `id_hotel` = ".(int)$htl_id;

		$htl_images = Db::getInstance()->executeS($sql);

		if ($htl_images)
			return $htl_images;
		else
			return false;
	}

	/**
	 * [deleteByHotelId :: To delete hotel's images data of a hotel by its hotel Id]
	 * @param  [int] $htl_id [Id of the hotel which images data you want to delete]
	 * @return [boolean]         [Returns true if deleted successfully else returns false]
	 */
	public function deleteByHotelId($htl_id)
	{
		$delete = Db::getInstance()->delete('htl_image', '`id_hotel`='.(int)$htl_id);
		return $delete;
	}

	/**
	 * [validAddHotelMainImage :: To validate the image of the hotel before saving it]
	 * @param  [array] $image [variable having image information of the hotel]
	 * @return [boolean]        [returns true if image is valid]
	 */
	public static function validateImage($image)
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

	/**
	 * [validAddHotelOtherImage :: To validate the array of images of the hotel before saving it]
	 * @param  [array] $image [array containing images of the hotel]
	 * @return [boolean]        [returns true if images are valid]
	 */
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

	/**
	 * [uploadOtherImages :: To upload the images of the hotel]
	 * @param  [array] $image          [array containing images of the hotel]
	 * @param  [int] $hotel_id       [Id of the hotel to which the images belong]
	 * @param  [string] $hotel_img_path [Path where the images to be stored]
	 * @return [boolean]                 [true]
	 */
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
		return true;
	}

	/**
	 * [uploadMainImage :: To upload the main image of the hotel]
	 * @param  [type]  $image          [variable having image information of the hotel]
	 * @param  [type]  $hotel_id       [Id of the hotel to which the image belongs]
	 * @param  [type]  $hotel_img_path [Path where the image to be stored]
	 * @param  boolean $active         []
	 * @return [boolean]                 [true]
	 */
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
		return true;
	}

	/**
	 * [randomImageName :: TO get a random string of 6 characters]
	 * @return [string] [Returns randomly generated string of 6 digits]
	 */
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
