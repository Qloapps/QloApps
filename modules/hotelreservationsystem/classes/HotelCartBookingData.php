<?php
	class HotelCartBookingData extends ObjectModel
	{
		public $id;
		public $id_cart;
		public $id_guest;
		public $id_order;
		public $id_customer;
		public $id_product;
		public $id_room;
		public $id_hotel;
		public $amount;
		public $booking_type;
		public $comment;
		public $date_from;
		public $date_to;
		public $date_add;
		public $date_upd;

		public static $definition = array(
			'table' => 'htl_cart_booking_data',
			'primary' => 'id',
			'fields' => array(
				'id_cart' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_guest' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_order' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_customer' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_product' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_room' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_hotel' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'amount' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
				'booking_type' =>  	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'comment' =>  		array('type' => self::TYPE_STRING),
				'date_from' =>  	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_to' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_add' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_upd' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			),
		);

		public function getCountRoomsInCart($id_cart, $guest)
		{
			$sql = "SELECT Count(`id`) FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_cart` = ".$id_cart." AND `id_guest` = ".$guest." AND `id_order` = 0";
			$count_rooms = Db::getInstance()->getValue($sql);

			if ($count_rooms)
				return $count_rooms;
			else
				return 0;
		}

		public function getCartBookingDetailsByIdCartIdGuest($id_cart, $id_guest)
		{
			$sql = "SELECT cbd.id AS id_cart_book_data, cbd.id_cart, cbd.id_guest, cbd.id_product, cbd.id_room, cbd.id_hotel, cbd.amount, cbd.date_from, cbd.date_to, ri.room_num, pl.name AS room_type 
					FROM `"._DB_PREFIX_."htl_cart_booking_data` AS cbd
					INNER JOIN `"._DB_PREFIX_."htl_room_information` AS ri ON (cbd.id_room = ri.id)
					INNER JOIN `"._DB_PREFIX_."product_lang` AS pl ON (cbd.id_product = pl.id_product)
					WHERE cbd.id_cart = ".$id_cart." AND cbd.id_guest = ".$id_guest;
			$cart_book_data = Db::getInstance()->executeS($sql);

			if ($cart_book_data)
			{
				foreach ($cart_book_data as $key => $value)
				{
					$obj_booking_dtl = new HotelBookingDetail();
					$num_days = $obj_booking_dtl->getNumberOfDays($value['date_from'], $value['date_to']); //quantity of product
					$cart_book_data[$key]['amt_with_qty'] = $value['amount'] * $num_days;
				}
				return $cart_book_data;
			}
			else
				return false;
		}

		public function getOnlyCartBookingData($id_cart, $id_guest, $id_product, $id_customer = 0)
		{
			$sql = "SELECT * FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_cart` = ".$id_cart." AND `id_product` = ".$id_product;

			if ($id_customer) 
				$sql .=  " AND `id_customer` = ".$id_customer;

			$cart_book_data = Db::getInstance()->executeS($sql);

			if ($cart_book_data)
				return $cart_book_data;
			else
				return false;
		}

		public function getCountRoomsByIdCartIdProduct($id_cart, $id_product, $date_from, $date_to)
		{
			$sql = "SELECT Count(`id`) FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_cart` = ".$id_cart." AND `id_product` = ".$id_product." AND `date_from` = '$date_from' AND `date_to` = '$date_to'";
			$count_rooms = Db::getInstance()->getValue($sql);

			if ($count_rooms)
				return $count_rooms;
			else
				return false;
		}

		public function deleteRowById($id)
		{
			$delete = Db::getInstance()->delete('htl_cart_booking_data', '`id`='.(int)$id);
			
			return $delete;
		}
		public function getCartCurrentDataByCartId($cart_id)
		{
			$result = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_cart`=".$cart_id);
			if ($result)
				return $result;
			else
				return false;
		}

		public function deleteRowHotelCustomerCartDetail($id)
		{
			$deleted = Db::getInstance()->delete('htl_cart_booking_data','id='.$id);
			if ($deleted)
				return true;
			return false;
		}

		public function deleteCartDataById($id)
		{
			$deleted = Db::getInstance()->delete('htl_cart_booking_data','id='.$id);
			if ($deleted)
				return true;
			return false;
		}

		public function changeProductDataByRoomId($roomid, $id_product, $days_diff, $cart_id)
		{
			$deleted = Db::getInstance()->delete('htl_cart_booking_data','id_room='.$roomid);
			
			$cart_product_quantity = Db::getInstance()->getValue('SELECT `quantity` FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart`='.$cart_id.' AND `id_product`='.$id_product);
			
			$new_quantity = $cart_product_quantity-$days_diff;
			
			if ($new_quantity>0)
			{
				$update_quantity = Db::getInstance()->update('cart_product', array('quantity' => $new_quantity), '`id_cart`='.$cart_id.' AND `id_product`='.$id_product);
				if ($update_quantity)
					return true;
				return false;
			}
			else
			{
				$delete_product = Db::getInstance()->delete('cart_product', '`id_cart`='.$cart_id.' AND `id_product`='.$id_product);
				if ($delete_product)
					return true;
				return false;
			}
		}

		public function deleteCartBookingDataOnRemoveFromBlockCart($cart_id, $id_product)
		{
			$delete_rooms = Db::getInstance()->delete('htl_cart_booking_data', "`id_cart`=".$cart_id." AND `id_product`=".$id_product);

			if ($delete_rooms)
				return true;
			return false;
		}

		public function checkExistanceOfRoomInCurrentCart($id_room, $date_from, $date_to, $id_cart, $id_guest)
		{
			$result = Db::getInstance()->getValue("SELECT id FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_room`=".$id_room." AND `date_from`='$date_from' AND `date_to`='$date_to' AND `id_cart`=".$id_cart." AND `id_guest`=".$id_guest);
			
			if ($result)
				return $result;
			return false;
		}

		public function deleteCartDataByIdProductIdCart($id_cart, $id_product, $date_from, $date_to)
		{
			$result = Db::getInstance()->delete('htl_cart_booking_data',"`id_cart`=".$id_cart." AND `id_product`=".$id_product." AND `date_from` = '$date_from' AND `date_to` = '$date_to'");
			if ($result)
				return true;
			return false;
		}

		public function deleteRoomDataFromOrderLine($id_cart, $id_guest, $id_product, $date_from, $date_to)
		{
			$result = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_cart`=".$id_cart." AND `id_guest`=".$id_guest." AND `id_product`=".$id_product." AND `date_from`= '$date_from' AND `date_to`= '$date_to'");

			$num_rm = Db::getInstance()->NumRows();

			$obj_htl_bk_dtl = new HotelBookingDetail();
			$num_days = $obj_htl_bk_dtl->getNumberOfDays($date_from, $date_to);

			$qty = (int)$num_rm * (int)$num_days;
			if ($qty) 
			{
				$this->context = Context::getContext();
				$update_quantity = $this->context->cart->updateQty($qty, $id_product, null, false, 'down');

				$delete_rooms = Db::getInstance()->delete('htl_cart_booking_data', "`id_cart`=".$id_cart." AND `id_guest`=".$id_guest." AND `id_product`=".$id_product." AND `date_from`= '$date_from' AND `date_to`= '$date_to'");
				if ($delete_rooms)
					return true;
				
				return false;
			}
			else
				return false;
		}

		public function deleteBookingCartDataNotOrderedByProductId($id_product)
		{
			$delete = Db::getInstance()->delete('htl_cart_booking_data', '`id_product`='.(int)$id_product.' AND `id_order`=0');
			return $delete;
		}
	}