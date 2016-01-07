<?php
	class HotelCartBookingData extends ObjectModel
	{
		public $id;
		public $id_cart;
		public $id_guest;
		public $id_order;
		public $id_customer;
		public $id_currency;
		public $id_product;
		public $id_room;
		public $id_hotel;
		public $quantity;
		public $booking_type;
		public $comment;
		public $is_refunded;
		public $is_back_order;
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
				'id_currency' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_product' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_room' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_hotel' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'quantity' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'booking_type' =>  	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'comment' =>  		array('type' => self::TYPE_STRING),
				'is_refunded' =>  	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'is_back_order' =>  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'date_from' =>  	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_to' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_add' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_upd' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			),
		);
	
		/**
		 * [getCountRoomsInCart :: To get How much number of the rooms available in the cart of a customer or guest]
		 * @param  [int] $id_cart [Id of the cart]
		 * @param  [int] $guest   [Guest Id of the customer]
		 * @return [int|boolean]          [Returns number of the rooms available in the cart of a customer or guest if no rooms available returns 0]
		 */
		public function getCountRoomsInCart($id_cart, $guest)
		{
			$sql = "SELECT Count(`id`) FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_cart` = ".$id_cart." AND `id_guest` = ".$guest." AND `id_order` = 0";
			$count_rooms = Db::getInstance()->getValue($sql);

			if ($count_rooms)
				return $count_rooms;
			else
				return 0;
		}

		/**
		 * [getCartBookingDetailsByIdCartIdGuest :: To get Booking data from table by id cart and id guest of the customer]
		 * @param  [int] $id_cart  [Id of the cart which booking information you want to get]
		 * @param  [int] $id_guest [Id guest of the customer]
		 * @return [array|false]           [If required information found then returns the array of the information else returns false]
		 */
		public function getCartBookingDetailsByIdCartIdGuest($id_cart, $id_guest, $id_lang)
		{
			$sql = "SELECT cbd.id AS id_cart_book_data, cbd.id_cart, cbd.id_guest, cbd.id_product, cbd.id_room, cbd.id_hotel, cbd.quantity, cbd.date_from, cbd.date_to, ri.room_num, pl.name AS room_type 
					FROM `"._DB_PREFIX_."htl_cart_booking_data` AS cbd
					INNER JOIN `"._DB_PREFIX_."htl_room_information` AS ri ON (cbd.id_room = ri.id)
					INNER JOIN `"._DB_PREFIX_."product_lang` AS pl ON (cbd.id_product = pl.id_product AND pl.`id_lang`=".$id_lang.")
					WHERE cbd.id_cart = ".$id_cart." AND cbd.id_guest = ".$id_guest;
			$cart_book_data = Db::getInstance()->executeS($sql);

			if ($cart_book_data)
			{
				foreach ($cart_book_data as $key => $value)
				{
					$unit_price = Product::getPriceStatic($value['id_product']);
					$cart_book_data[$key]['amt_with_qty'] = $value['quantity'] * $unit_price;
				}
				return $cart_book_data;
			}
			else
				return false;
		}

		/**
		 * [getOnlyCartBookingData description]
		 * @param  [type]  $id_cart     [description]
		 * @param  [type]  $id_guest    [description]
		 * @param  [type]  $id_product  [description]
		 * @param  integer $id_customer [description]
		 * @return [type]               [description]
		 */
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

		/**
		 * [getCountRoomsByIdCartIdProduct :: To get Number of rooms for a date range(From $date_from TO $date_to) in a cart(which cat cart id is $id_cart) belongs to the same room type(product)]
		 * @param  [int] $id_cart    [Id of the cart]
		 * @param  [int] $id_product [Id of the product]
		 * @param  [date] $date_from  [Start date of the booking]
		 * @param  [date] $date_to    [End date of the booking]
		 * @return [int|false]  [If data found Returns the number for a date range(From $date_from TO $date_to) in a cart(which cat 			cart id is $id_cart) belongs to a room type(product) else returns false]
		 */
		public function getCountRoomsByIdCartIdProduct($id_cart, $id_product, $date_from, $date_to)
		{
			$sql = "SELECT Count(`id`) FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_cart` = ".$id_cart." AND `id_product` = ".$id_product." AND `date_from` <= '$date_from' AND `date_to` >= '$date_to'";

			$count_rooms = Db::getInstance()->getValue($sql);

			if ($count_rooms)
				return $count_rooms;
			else
				return false;
		}

		/**
		 * Deprecated
		 * [deleteRowById :: To delete Row from the table by its id(primary key)]
		 * @param  [int] $id [Id(primary key) of the table which row has to be deleted]
		 * @return [type]     [Returns true if deleted successfully else returns false]
		 */
		public function deleteRowById($id)
		{
			$delete = Db::getInstance()->delete('htl_cart_booking_data', '`id`='.(int)$id);
			
			return $delete;
		}

		/**
		 * [getCartCurrentDataByCartId :: To get booking information of the cart by cart id]
		 * @param  [int] $cart_id [Id of the cart]
		 * @return [array|false]  [If data found Returns the array containing the information of the cart of the passed cart id else returns false]
		 */
		public function getCartCurrentDataByCartId($cart_id)
		{
			$result = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_cart`=".$cart_id);
			if ($result)
				return $result;
			else
				return false;
		}

		/*public function deleteRowHotelCustomerCartDetail($id)
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
		}*/

		/**
		 * [changeProductDataByRoomId :: To delete the room from the cart when added from the admin side to the cart and update the 								quantity of the product in prestashop cart table]
		 * @param  [int] $roomid     [Id of the room]
		 * @param  [int] $id_product [Id of the room type(product)]
		 * @param  [int] $days_diff  [Number of days between start date and end date of the booking]
		 * @param  [int] $cart_id    [Id of the cart which products information has to be changed]
		 * @return [boolean]         [If cart updated with quantity successfully returns true else returns false]
		 */
		public function changeProductDataByRoomId($roomid, $id_product, $days_diff, $cart_id)
		{
			$deleted = Db::getInstance()->delete('htl_cart_booking_data', '`id_room` = '.(int)$roomid.' AND `id_cart` = '.(int)$cart_id);
			
			$cart_product_quantity = Db::getInstance()->getValue('SELECT `quantity` FROM `'._DB_PREFIX_.'cart_product` WHERE `id_cart`='.$cart_id.' AND `id_product`='.$id_product);
			
			$new_quantity = $cart_product_quantity-$days_diff;
			
			if ($new_quantity > 0)
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

		/**
		 * [deleteCartBookingDataOnRemoveFromBlockCart :: To delete all rooms from the cart(which cart id is passed) which belong to 												the same room type(product)(which product id is passed)]
		 * @param  [int] $cart_id    [Id of the cart]
		 * @param  [int] $id_product [Id of the product(room type)]
		 * @return [boolean]   [Returns true if deleted successfully else returns false]
		 */
		public function deleteCartBookingDataOnRemoveFromBlockCart($cart_id, $id_product)
		{
			$delete_rooms = Db::getInstance()->delete('htl_cart_booking_data', "`id_cart`=".$cart_id." AND `id_product`=".$id_product);

			if ($delete_rooms)
				return true;
			return false;
		}

		/**
		 * [checkExistanceOfRoomInCurrentCart :: To check Whether a room for a date range(which start date is $date_from and End date 									is $date_to) in current cart is already exists for a customer whose guest id is $id_guest]
		 * @param  [int] $id_room   [Id of the room]
		 * @param  [date] $date_from [Start date of the booking]
		 * @param  [date] $date_to   [End date of the booking]
		 * @param  [int] $id_cart   [Id of the cart]
		 * @param  [int] $id_guest  [Customer's guest Id]
		 * @return [int|false]            [If room already exists in the cart then returns id of the row in the table where entry for this *								room is located else returns false]
		 */
		public function checkExistanceOfRoomInCurrentCart($id_room, $date_from, $date_to, $id_cart, $id_guest)
		{
			$result = Db::getInstance()->getValue("SELECT id FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_room`=".$id_room." AND `date_from`='$date_from' AND `date_to`='$date_to' AND `id_cart`=".$id_cart." AND `id_guest`=".$id_guest);
			
			if ($result)
				return $result;
			return false;
		}

		/**
		 * [deleteCartDataByIdProductIdCart ::  To delete room from the cart(which cart id is passed) which belong to the room type(									product)(which product id is passed) and booked for the date range(hich start date is 										$date_from and end date is $date_to)]
		 * @param  [int] $id_cart    [Id of the cart]
		 * @param  [int] $id_product [Id of the product]
		 * @param  [date] $date_from  [Start date of the booking]
		 * @param  [date] $date_to    [End date og=f the booking]
		 * @return [boolean]             [Returns true if deleted successfully else returns false]
		 */
		public function deleteCartDataByIdProductIdCart($id_cart, $id_product, $date_from, $date_to)
		{
			$result = Db::getInstance()->delete('htl_cart_booking_data',"`id_cart`=".$id_cart." AND `id_product`=".$id_product." AND `date_from` = '$date_from' AND `date_to` = '$date_to'");
			if ($result)
				return true;
			return false;
		}

		/**
		 * [deleteRoomDataFromOrderLine :: To delete room from the order line when customer deletes the room from the order line for a 										date range (which start date is $date_from and End date is $date_to) in current cart]
		 * @param  [int] $id_cart    [Id of the cart]
		 * @param  [int] $id_guest   [Customer's guest Id]
		 * @param  [int] $id_product [Id of the product]
		 * @param  [date] $date_from  [Start date of the booking]
		 * @param  [date] $date_to    [End date of the booking]
		 * @return [boolean]          [Returns true if deleted successfully else returns false]
		 */
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

		/**
		 * [deleteBookingCartDataNotOrderedByProductId :: To delete the data of the rooms booking from the table Which rooms were 														added to the cart but Not Ordered By there room type(product id)]
		 * @param  [int] $id_product 	[Id of the product(room type)]
		 * @return [boolean]            [If deleted successfully returns true else returns false]
		 */
		public function deleteBookingCartDataNotOrderedByProductId($id_product)
		{
			$delete = Db::getInstance()->delete('htl_cart_booking_data', '`id_product`='.(int)$id_product.' AND `id_order`=0');
			return $delete;
		}

		/**
		 * [updateOrderRefundStatus :: To update the refund status of a room booked in the Cart(htl_cart_booking_data) if amount 									refunded by the admin]
		 * @param  [int] $id_cart  [Id of the Cart]
		 * @param  [date] $date_from [start date of the bookin of the room]
		 * @param  [date] $date_to   [end date of the bookin of the room]
		 * @param  [int] $id_room   [id of the room for which refund is done]
		 * @return [boolean]            [true if updated otherwise false]
		 */
		public function updateOrderRefundStatus($id_cart, $date_from, $date_to, $id_rooms)
		{
			$table = 'htl_cart_booking_data';
			$data = array('is_refunded' => 1);

			foreach ($id_rooms as $key_rm => $val_rm)
			{
				$where = "`id_cart` = ".$id_cart." AND `id_room` = ".$val_rm['id_room']." AND `date_from` = '$date_from' AND `date_to` = '$date_to'";
				$result = Db::getInstance()->update($table, $data, $where);
			}

			if ($result)
				return $result;
			return false;
		}

		/**
		 * [getCustomerIdRoomsByIdCartIdProduct :: To get array of rooms ids in the cart booked by a customer for a date range]
		 * @param  [int] $id_cart    [Id of the cart]
		 * @param  [int] $id_product [Id of the product]
		 * @param  [date] $date_from [Start date of the booking]
		 * @param  [date] $date_to   [End date of the booking]
		 * @return [array|false]     [If rooms found returns array containing rooms ids else returns false]
		 */
		public function getCustomerIdRoomsByIdCartIdProduct($id_cart, $id_product, $date_from, $date_to)
		{
			$rooms_ids = Db::getInstance()->executeS("SELECT `id_room` FROM `"._DB_PREFIX_."htl_cart_booking_data` WHERE `id_cart`=".$id_cart." AND `id_product`=".$id_product." AND `date_from`='$date_from' AND `date_to`='$date_to'");
			if ($rooms_ids)
				return $rooms_ids;
			return false;
		}	

		/**
		 * [deleteRowByCartBookingData : To delete data from the table by given conditions(conditions array in argument)]
		 * @param  [Array] $cartData [array of the conditions on which row is to be deleted]
		 * @return [boolean]        [Returns true if successfully updated else returns false]
		 */
		public function deleteRowByCartBookingData($cartData)
		{
			if (!is_array($cartData)) 
				return false;

			$dltdata = false;

			$stringArr = array('date_from', 'date_to');
			
			foreach ($cartData as $c_key => $c_val) 
			{
				if ($dltdata) 
					$dltdata .= " AND ";

				if (in_array($c_key, $stringArr)) 
					$dltdata .= $c_key."= '$c_val'";
				else
					$dltdata .= $c_key." = ".$c_val;
			}
			$delete = Db::getInstance()->delete('htl_cart_booking_data', $dltdata);
			
			return $delete;
		}

		/**
		 * [updateCartBookingData : To update data in the table with given data and conditions]
		 * @param  [Array] $updData [array of the data to be updated]
		 * @param  [Array] $updBy   [Conditions array will be used in where condition]
		 * @return [boolean]        [Returns true if successfully updated else returns false]
		 */
		public function updateCartBookingData($updData, $updBy)
		{
			if (!is_array($updData) && !is_array($updBy)) 
				return false;

			$where = false;
			$stringArr = array('date_from', 'date_to');
			foreach ($updBy as $u_key => $u_val) 
			{
				if ($where) 
					$where .= " AND ";

				if (in_array($u_key, $stringArr)) 
					$where .= $u_key."= '$u_val'";
				else
					$where .= $u_key." = ".$u_val;
			}
			$update = Db::getInstance()->update('htl_cart_booking_data', $updData, $where);

			return $update;
		}

		/**
		 * [getCartFormatedBookinInfoByIdCart : To get cart booking information with some additional information in a custom famated way]
		 * @param  [Int] $id_cart [Id of the cart]
		 * @return [Array|false]          [If data found returns cart booking information with some additional information else returns false]
		 */
		public function getCartFormatedBookinInfoByIdCart($id_cart)
		{
			$context = Context::getContext();
			$cart_detail_data = $this->getCartCurrentDataByCartId((int) $id_cart);
			if ($cart_detail_data)
			{
				foreach ($cart_detail_data as $key => $value)
				{
					$product_image_id = Product::getCover($value['id_product']);
					$link_rewrite = ((new Product((int) $value['id_product'], Configuration::get('PS_LANG_DEFAULT')))->link_rewrite[Configuration::get('PS_LANG_DEFAULT')]);

					if ($product_image_id)
						$cart_detail_data[$key]['image_link'] = $context->link->getImageLink($link_rewrite, $product_image_id['id_image'], 'small_default');
					else
						$cart_detail_data[$key]['image_link'] = $context->link->getImageLink($link_rewrite, $context->language->iso_code."-default", 'small_default');

					$cart_detail_data[$key]['room_type'] = (new Product((int) $value['id_product']))->name[Configuration::get('PS_LANG_DEFAULT')];
					$cart_detail_data[$key]['room_num'] = (new HotelRoomInformation((int) $value['id_room']))->room_num;
					$cart_detail_data[$key]['date_from'] = $value['date_from'];
					$cart_detail_data[$key]['date_to'] = $value['date_to'];

					$unit_price = Product::getPriceStatic($value['id_product']);
					$cart_detail_data[$key]['amt_with_qty'] = $value['quantity'] * $unit_price;
				}
			}
			if ($cart_detail_data)
				return $cart_detail_data;
			return false;
		}

		/**
		 * [updateIdCurrencyByIdCart : To update id_currency in the table By id_cart]
		 * @param  [Int] $id_cart     [Id of the cart]
		 * @param  [Int] $id_currency [Id of the currency]
		 * @return [Boolean]              [Returns true if successfully updated else returns false]
		 */
		public function updateIdCurrencyByIdCart($id_cart, $id_currency)
		{
			$update_currency = Db::getInstance()->update('htl_cart_booking_data', array('id_currency' => $id_currency), '`id_cart`='.$id_cart);
			if ($update_currency)
				return true;
			return false;
		}
	}