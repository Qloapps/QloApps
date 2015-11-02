<?php
class HotelRoomInformation extends ObjectModel
{
	public $id;
	public $id_product;
	public $id_hotel;
	public $room_num;
	public $id_status;
	public $floor;
	public $comment;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'htl_room_information',
		'primary' => 'id',
		'fields' => array(
			'id_product' =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_hotel' =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'room_num' =>	array('type' => self::TYPE_STRING),
			'id_status' =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'floor' =>		array('type' => self::TYPE_STRING),
			'comment' =>	array('type' => self::TYPE_STRING),
			'date_add' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		),
	);

	public function deleteByProductId($id_product)
	{
		$delete = Db::getInstance()->delete('htl_room_information', '`id_product`='.(int)$id_product);
		return $delete;
	}

	public function getHotelRoomInfoByProductId($id_product)
	{
		$result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_product`='.$id_product);
		if ($result)
			return $result;
		return false;
	}

	public function getHotelRoomInfo($id_product, $id_hotel, $is_getNum = 0)
	{
		$sql = "SELECT `id`, `id_hotel`, `room_num`, `id_status`, `floor`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_product` = ".$id_product." AND `id_hotel` = ".$id_hotel;

		$rm_info = Db::getInstance()->executeS($sql);

		if ($is_getNum) 
		{
			$no_row = Db::getInstance()->NumRows();
			return $no_row;
		}
		else
		{
			if ($rm_info)
				return $rm_info;
			else
				return false;
		}

	}

	public function getHotelRoomInfoById($id)
	{
		$sql = "SELECT `room_num` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id` = ".$id;
		$rm_num = Db::getInstance()->getValue($sql);

		if ($rm_num)
			return $rm_num;
		else
			return false;
	}

	public function deleteHotelRoomInfoById($id_room_info)
	{
		$result = Db::getInstance()->delete('htl_room_information', '`id` = '.(int)$id_room_info, 1);

		return $result;
	}

	Public function getHotelDataByBookingInfo($date_from,$date_to,$room_type=false,$hotel_id,$num_rooms)
	{
		if ($room_type)
		{
			$product_name = (new Product((int) $room_type))->name[Configuration::get('PS_LANG_DEFAULT')];
			$room_info_avail = Db::getInstance()->executeS("SELECT `id`, `id_product`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type." AND `id` NOT IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to')");

			$room_info_unavail = Db::getInstance()->executeS("SELECT `id`, `id_product`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type." AND `id` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `id_hotel`=".$hotel_id." AND `date_from`<='$date_from' AND `date_to`>='$date_to')");
	
			$rooms_info_partially_avail = Db::getInstance()->executeS("SELECT `id`, `id_product`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type." AND `id` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `id_hotel`=".$hotel_id." AND `date_from`<='$date_from' AND `date_to`<='$date_to' AND `id_room` NOT IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to'))");

			$rooms_info[0]['name'] = $product_name;
			$rooms_info[0]['id_product'] = (new Product((int) $room_type))->id;
			$rooms_info[0]['info']['Available'] = $room_info_avail;
			$rooms_info[0]['info']['Unavailable'] = $room_info_unavail;
			$rooms_info[0]['info']['Partially_Available'] = $rooms_info_partially_avail;
		}
		else
		{
			$i = 0;
			$room_types = Db::getInstance()->executeS('SELECT DISTINCT `id_product` FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_hotel`='.$hotel_id);
			foreach ($room_types as $room_type)
			{
				$product_name = (new Product((int) $room_type['id_product']))->name[Configuration::get('PS_LANG_DEFAULT')];

				$room_info_avail = Db::getInstance()->executeS("SELECT `id`, `id_product`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type['id_product']." AND `id` NOT IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to')");

				$room_info_unavail = Db::getInstance()->executeS("SELECT `id`, `id_product`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type['id_product']." AND `id` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `id_hotel`=".$hotel_id." AND `date_from`<='$date_from' AND `date_to`>='$date_to')");
		
				$rooms_info_partially_avail = Db::getInstance()->executeS("SELECT `id`, `id_product`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type['id_product']." AND `id` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `id_hotel`=".$hotel_id." AND `date_from`<='$date_from' AND `date_to`<='$date_to' AND `id_room` NOT IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to'))");

				$rooms_info[$i]['name'] = $product_name;
				$rooms_info[$i]['id_product'] = (new Product((int) $room_type['id_product']))->id;
				$rooms_info[$i]['info']['Available'] = $room_info_avail;
				$rooms_info[$i]['info']['Unavailable'] = $room_info_unavail;
				$rooms_info[$i]['info']['Partially_Available'] = $rooms_info_partially_avail;
				$i++;
			}
		}

		if ($rooms_info)
			return $rooms_info;
		else
			return false;
	}

	Public function getCalenderInformationByBookingInfo($date_from,$date_to,$room_type=false,$hotel_id,$num_rooms)
	{
		if ($room_type)
		{
			$product_name = (new Product((int) $room_type))->name[Configuration::get('PS_LANG_DEFAULT')];
			$unavailable_rooms = Db::getInstance()->executeS("SELECT `id`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type." AND `id` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `id_hotel`=".$hotel_id." AND `date_from`<='$date_from' AND `date_to`>='$date_to')");

			$available_rooms = Db::getInstance()->executeS("SELECT `id`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type." AND `id` NOT IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to')");
			
			$partially_avail_rooms['room_info'] = Db::getInstance()->executeS("SELECT `id`,`comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type." AND `id` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`>='$date_from' AND `date_from`<='$date_to' AND `date_to`<='$date_to' AND `date_to`>='$date_from' AND id_room NOT IN("."SELECT id_room FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to'))");

			$partially_avail_rooms['booking_info'] = Db::getInstance()->executeS("SELECT `id_room`,`id_status`, `message`, `booking_type`, `date_from`, `date_to` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type." AND `id_room` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`>='$date_from' AND `date_from`<='$date_to' AND `date_to`<='$date_to' AND `date_to`>='$date_from' AND id_room NOT IN("."SELECT id_room FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to'))");

				$rooms_info['name'] = $product_name;
				$rooms_info['id_product'] = (new Product((int) $room_type))->id;
				$rooms_info['info']['available'] = $available_rooms;
				$rooms_info['info']['unavailable'] = $unavailable_rooms;
				$rooms_info['info']['partially_available'] = $partially_avail_rooms;
		}
		else
		{
			$i = 0;
			$room_types = Db::getInstance()->executeS('SELECT DISTINCT `id_product` FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_hotel`='.$hotel_id);
			foreach ($room_types as $room_type)
			{
				$product_name = (new Product((int) $room_type['id_product']))->name[Configuration::get('PS_LANG_DEFAULT')];

				$unavailable_rooms = Db::getInstance()->executeS("SELECT `id`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type['id_product']." AND `id` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `id_hotel`=".$hotel_id." AND `date_from`<='$date_from' AND `date_to`>='$date_to')");

				$available_rooms = Db::getInstance()->executeS("SELECT `id`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type['id_product']." AND `id` NOT IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to')");
				
				$partially_avail_rooms['room_info'] = Db::getInstance()->executeS("SELECT `id`, `comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type['id_product']." AND `id` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`>='$date_from' AND `date_from`<='$date_to' AND `date_to`<='$date_to' AND `date_to`>='$date_from' AND id_room NOT IN("."SELECT id_room FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to'))");

				$partially_avail_rooms['booking_info'] = Db::getInstance()->executeS("SELECT `id_status`, `message`, `booking_type`, `date_from`, `date_to` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type['id_product']." AND `id` IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`>='$date_from' AND `date_from`<='$date_to' AND `date_to`<='$date_to' AND `date_to`>='$date_from' AND id_room NOT IN("."SELECT id_room FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `date_from`<='$date_from' AND `date_to`>='$date_to'))");


				$rooms_info[$i]['name'] = $product_name;
				$rooms_info[$i]['id_product'] = (new Product((int) $room_type['id_product']))->id;
				$rooms_info[$i]['info']['Available'] = $available_rooms;
				$rooms_info[$i]['info']['Unavailable'] = $unavailable_rooms;
				$rooms_info[$i]['info']['Partially_Available'] = $partially_avail_rooms;
				$i++;
			}
		}

		if (isset($rooms_info) && $rooms_info)
			return $rooms_info;
		else
			return false;
	}
}