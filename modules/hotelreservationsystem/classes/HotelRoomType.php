<?php
class HotelRoomType extends ObjectModel
{
	public $id;
	public $id_product;
	public $id_hotel;
	public $adult;
	public $children;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'htl_room_type',
		'primary' => 'id',
		'fields' => array(
			'id_product' =>	array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'id_hotel' =>	array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'adult' =>		array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'children' =>	array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'date_add' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		),
	);

	public function deleteByProductId($id_product)
	{
		$delete = Db::getInstance()->delete('htl_room_type', '`id_product`='.(int)$id_product);
		return $delete;
	}

	public function getRoomTypeInfoByIdProduct($id_product)
	{
		$sql = "SELECT `id`,`id_hotel`, `adult`, `children` FROM `"._DB_PREFIX_."htl_room_type` WHERE `id_product` = ".$id_product;
		$rm_info = Db::getInstance()->getRow($sql);

		if ($rm_info) 
			return $rm_info;
		else
			return false;
	}

	public function getRoomTypeByHotelId($hotel_id)
	{
		$sql = "SELECT pl.name AS room_type, pl.id_product AS id_product
			FROM `"._DB_PREFIX_."htl_room_type` AS rt 
			INNER JOIN `"._DB_PREFIX_."product_lang` AS pl ON (rt.id_product = pl.id_product)
			WHERE rt.id_hotel =".$hotel_id;

		$rm_type = Db::getInstance()->executeS($sql);

		if ($rm_type) 
			return $rm_type;
		else
			return false;
	}

	public function getIdProductByHotelId($hotel_id, $room_type = 0)
	{
		if ($room_type) 
			$sql = 'SELECT DISTINCT `id_product`, `adult`, `children` FROM `'._DB_PREFIX_.'htl_room_type` WHERE `id_hotel`='.$hotel_id.' AND `id_product` ='.$room_type;
		else
			$sql = 'SELECT DISTINCT `id_product`, `adult`, `children` FROM `'._DB_PREFIX_.'htl_room_type` WHERE `id_hotel`='.$hotel_id;

		$rm_type = Db::getInstance()->executeS($sql);

		if ($rm_type) 
			return $rm_type;
		else
			return false;
	}

	public static function getMaxAdults($id_hotel)
	{
		$sql = 'SELECT MAX(adult) AS max_adult FROM '._DB_PREFIX_.'htl_room_type WHERE id_hotel='.$id_hotel;

		$max_adult = Db::getInstance()->getValue($sql);

		if ($max_adult) 
			return $max_adult;
		else
			return false;
	}

	public static function getMaxChild($id_hotel)
	{
		$sql = 'SELECT MAX(children) AS max_child FROM '._DB_PREFIX_.'htl_room_type WHERE id_hotel='.$id_hotel;

		$max_child = Db::getInstance()->getValue($sql);

		if ($max_child) 
			return $max_child;
		else
			return false;
	}
}