<?php
	class HotelBookingDetail extends ObjectModel
	{
		private $all_dates_arr;

		public $id;
		public $id_product;
		public $id_order;
		public $id_cart;
		public $id_room;
		public $id_hotel;
		public $id_customer;
		public $booking_type;
		public $id_status;
		public $comment;
		public $check_in;
		public $check_out;
		public $date_from;
		public $date_to;
		public $date_add;
		public $date_upd;

		public static $definition = array(
			'table' => 'htl_booking_detail',
			'primary' => 'id',
			'fields' => array(
				'id_product' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_order' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_cart' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_room' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_hotel' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_customer' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'booking_type' =>  	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_status' =>  	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'comment' =>  		array('type' => self::TYPE_STRING),
				'check_in' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'check_out' =>  	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_from' =>  	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_to' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_add' =>  		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
				'date_upd' =>  	 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		));

		// Adult and children both are used for front category page only in available rooms
		// $for_calendar is used both for calender and for getting stats of rooms
		public function getBookingData($date_from, $date_to, $hotel_id, $room_type, $adult = 0, $children = 0, $num_rooms = 1, $for_calendar=0, $search_available = 1, $search_partial = 1, $search_booked = 1, $search_unavai = 1, $id_cart = 0, $id_guest = 0, $search_cart_rms = 0)
		{
			$date_from = date('Y-m-d H:i:s', strtotime($date_from));
			$date_to = date('Y-m-d H:i:s', strtotime($date_to));

			$obj_room_info = new HotelRoomInformation();

			//For check-in and check-out time
			//
			// $obj_hotel_info = new HotelBranchInformation();
			// $hotel_info = $obj_hotel_info->hotelBranchInfoById($hotel_id);

			// $date_from = date('Y-m-d H:i:s', strtotime("$date_from +".date('H',strtotime($hotel_info['check_in']))." hours +".date('i', strtotime($hotel_info['check_in']))." minutes"));
			// $date_to = date('Y-m-d H:i:s', strtotime("$date_to +".date('H', strtotime($hotel_info['check_out']))." hours +".date('i', strtotime($hotel_info['check_out']))." minutes"));

			$obj_rm_type = new HotelRoomType();
			$room_types = $obj_rm_type->getIdProductByHotelId($hotel_id, $room_type);
			if ($room_types)
			{
				$total_rooms = 0;
				$num_booked = 0;
				$num_unavail = 0;
				$num_avail = 0;
				$num_part_avai = 0;
				$num_cart = 0;

				foreach ($room_types as $key => $room_type)
				{
					$total_rooms += $obj_room_info->getHotelRoomInfo($room_type['id_product'], $hotel_id, 1);

					$product_name = (new Product((int) $room_type['id_product']))->name[Configuration::get('PS_LANG_DEFAULT')];

					if ($search_cart_rms)
					{
						$sql = "SELECT cbd.id_product, cbd.id_room, cbd.id_hotel, cbd.booking_type, cbd.comment, rf.room_num, cbd.date_from, cbd.date_to 
							FROM `"._DB_PREFIX_."htl_cart_booking_data` AS cbd
							INNER JOIN `"._DB_PREFIX_."htl_room_information` AS rf ON (rf.id = cbd.id_room)
							WHERE cbd.id_hotel=".$hotel_id." AND cbd.id_product =".$room_type['id_product']." AND cbd.id_cart = ".$id_cart." AND cbd.id_guest =".$id_guest;
						$cart_rooms = Db::getInstance()->executeS($sql);
						$num_cart += count($cart_rooms);
					}

					if ($search_booked)
					{
						$sql = "SELECT bd.id_product, bd.id_room, bd.id_hotel, bd.id_customer, bd.booking_type, bd.id_status AS booking_status, bd.comment, rf.room_num, bd.date_from, bd.date_to 
							FROM `"._DB_PREFIX_."htl_booking_detail` AS bd
							INNER JOIN `"._DB_PREFIX_."htl_room_information` AS rf ON (rf.id = bd.id_room)
							WHERE bd.id_hotel=".$hotel_id." AND bd.id_product =".$room_type['id_product']." AND bd.date_from <= '$date_from' AND bd.date_to >='$date_to'";
						$booked_rooms = Db::getInstance()->executeS($sql);
						$num_booked += count($booked_rooms);
					}

					if ($search_unavai)
					{
						$sql = "SELECT `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE id_hotel=".$hotel_id." AND id_product =".$room_type['id_product']." AND id_status = 2";
						$unavail_rooms = Db::getInstance()->executeS($sql);
						$num_unavail += count($unavail_rooms);
					}

					if ($search_available)
					{
						$exclude_ids = "SELECT id_room 
								FROM "._DB_PREFIX_."htl_booking_detail 
								WHERE (date_from <= '$date_from' AND date_to > '$date_from' AND date_to <= '$date_to') OR (date_from > '$date_from' AND date_to < '$date_to') OR (date_from >= '$date_from' AND date_from < '$date_to' AND date_to >= '$date_to') OR (date_from < '$date_from' AND date_to > '$date_to')";

						
						if (!empty($id_cart) && !empty($id_guest)) 
						{
							$exclude_ids .= " UNION
								SELECT id_room 
								FROM "._DB_PREFIX_."htl_cart_booking_data 
								WHERE id_cart=".$id_cart." AND id_guest=".$id_guest." AND ((date_from <= '$date_from' AND date_to > '$date_from' AND date_to <= '$date_to') OR (date_from > '$date_from' AND date_to < '$date_to') OR (date_from >= '$date_from' AND date_from < '$date_to' AND date_to >= '$date_to') OR (date_from < '$date_from' AND date_to > '$date_to'))";
						}

						$sql = "SELECT ri.`id` AS `id_room`, ri.`id_product`, ri.`id_hotel`, ri.`room_num`, ri.`comment` AS `room_comment` 
							FROM `"._DB_PREFIX_."htl_room_information` AS ri ";
						if ($adult || $children) 
						{
							$sql .= "INNER JOIN "._DB_PREFIX_."htl_room_type AS rt ON (rt.id_product = ri.id_product AND rt.id_hotel = ri.id_hotel";
							if ($adult) 
								$sql .= " AND rt.adult >= ".$adult;
							if ($children) 
								$sql .= " AND rt.children >= ".$children;
							$sql .= ")";
						}
						
						$sql .= " WHERE ri.id_hotel=".$hotel_id." AND ri.id_product=".$room_type['id_product']." AND ri.id_status = 1 AND ri.id NOT IN (".$exclude_ids.")";

						$avai_rooms = Db::getInstance()->executeS($sql);
						$num_avail += count($avai_rooms);
					}

					if ($search_partial)
					{
						$sql = "SELECT bd.id_product, bd.id_room, bd.id_hotel, bd.id_customer, bd.booking_type, bd.id_status AS booking_status, bd.comment AS `room_comment`, rf.room_num, bd.date_from, bd.date_to
							FROM `"._DB_PREFIX_."htl_booking_detail` AS bd 
							INNER JOIN `"._DB_PREFIX_."htl_room_information` AS rf ON (rf.id = bd.id_room AND rf.id_status = 1)
							WHERE bd.id_hotel=".$hotel_id." AND bd.id_product=".$room_type['id_product']." AND ((bd.date_from <= '$date_from' AND bd.date_to > '$date_from' AND bd.date_to < '$date_to') OR (bd.date_from > '$date_from' AND bd.date_from < '$date_to' AND bd.date_to >= '$date_to') OR (bd.date_from > '$date_from' AND bd.date_from < '$date_to' AND bd.date_to < '$date_to'))";

						$partial_avai_rooms = Db::getInstance()->executeS($sql);
						$rm_part_avai = count($partial_avai_rooms);
						
						$this->all_dates_arr = $this->createDateRangeArray($date_from, $date_to, 1);

						if (!$for_calendar)
						{
							foreach ($partial_avai_rooms as $r_key => $r_val)
							{
								if (($r_val['date_from'] <= $date_from) && ($r_val['date_to'] > $date_from) && ($r_val['date_to'] < $date_to)) // from lower to middle range
								{
									$forRange = $this->createDateRangeArray($r_val['date_to'], $date_to);
									$available_dates = $this->getPartialRange($forRange);
								}
								elseif (($r_val['date_from'] > $date_from) && ($r_val['date_from'] < $date_to) && ($r_val['date_to'] >= $date_to)) // from middle to higher range
								{
									$forRange = $this->createDateRangeArray($date_from, $r_val['date_from']);
									$available_dates = $this->getPartialRange($forRange);
								}
								elseif (($r_val['date_from'] > $date_from) && ($r_val['date_from'] < $date_to) && ($r_val['date_to'] > $date_from) && ($r_val['date_to'] < $date_to))
								// between range
								{
									$forRange1 = $this->createDateRangeArray($date_from, $r_val['date_from']);
									$init_range = $this->getPartialRange($forRange1);

									$forRange2 = $this->createDateRangeArray($r_val['date_to'], $date_to);
									$last_range = $this->getPartialRange($forRange2);
									
									$available_dates = array_merge($init_range, $last_range);
								}

								$partial_avai_rooms[$r_key]['avai_dates'] = $available_dates;
							}

							if (!empty($this->all_dates_arr))
							{
								$num_unavail += $rm_part_avai;
								$rm_part_avai = 0;

								$unavail_rooms = array_merge($unavail_rooms, $partial_avai_rooms);
								$partial_avai_rooms = false;
							}
							$num_part_avai += $rm_part_avai;
						}
					}

					if (!$for_calendar)
					{
						$booking_data['rm_data'][$key]['name'] = $product_name;
						$booking_data['rm_data'][$key]['id_product'] = (new Product((int) $room_type['id_product']))->id;
	
						if ($search_available)
							$booking_data['rm_data'][$key]['data']['available'] = $avai_rooms;
	
						if ($search_unavai)
							$booking_data['rm_data'][$key]['data']['unavailable'] = $unavail_rooms;

						if ($search_booked)
							$booking_data['rm_data'][$key]['data']['booked'] = $booked_rooms;

						if ($search_partial)
							$booking_data['rm_data'][$key]['data']['partially_available'] = $partial_avai_rooms;

						if ($search_cart_rms)
							$booking_data['rm_data'][$key]['data']['cart_rooms'] = $cart_rooms;
					}
				}

				$booking_data['stats']['total_rooms'] = $total_rooms;
	
				if ($search_booked)
					$booking_data['stats']['num_booked'] = $num_booked;
	
				if ($search_unavai)
					$booking_data['stats']['num_unavail'] = $num_unavail;
	
				if ($search_available)
					$booking_data['stats']['num_avail'] = $num_avail;

				if ($search_partial)
					$booking_data['stats']['num_part_avai'] = $num_part_avai;

				if ($search_partial)
					$booking_data['stats']['num_cart'] = $num_cart;

				return $booking_data;
			}
		}

		public function createDateRangeArray($strDateFrom, $strDateTo, $for_check = 0)
		{
		    $aryRange=array();

		    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2), substr($strDateFrom,8,2),substr($strDateFrom,0,4));
		    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2), substr($strDateTo,8,2),substr($strDateTo,0,4));

		    if ($iDateTo >= $iDateFrom)
		    {
		    	$entryDate = date('Y-M-d',$iDateFrom);
		        array_push($aryRange, $entryDate); // first entry
		        $this->checkAllDatesCover($entryDate);

		        while ($iDateFrom < $iDateTo)
		        {
		            $iDateFrom += 86400; // add 24 hours
		            if ($iDateFrom != $iDateTo || !$for_check) // to stop last entry in check partial case
		            {
			            $entryDate = date('Y-M-d',$iDateFrom);
			            array_push($aryRange, $entryDate);

			            if ($iDateFrom != $iDateTo) 
			            {
			            	$this->checkAllDatesCover($entryDate);
			            }
		            }
		        }
		    }
		    return $aryRange;
		}

		public function checkAllDatesCover($dateCheck)
		{
			if (isset($this->all_dates_arr) && !empty($this->all_dates_arr)) 
			{
				if(($key = array_search($dateCheck, $this->all_dates_arr)) !== false) 
		        {
				    unset($this->all_dates_arr[$key]);
				}
			}

			return true;
		}

		public function getPartialRange($dateArr)
		{
			if (count($dateArr)>=2)
			{
			    for ($i=0; $i < count($dateArr)-1; $i++) 
				{ 
					$dateRange[] = $dateArr[$i]." To ".$dateArr[$i+1];
				}
			}
			else
				$dateRange = $dateArr;
		    return $dateRange;
		}

		public function getNumberOfDays($dateFrom, $dateTo)
		{
			$startTimeStamp = strtotime($dateFrom);
			$endTimeStamp = strtotime($dateTo);

			$timeDiff = abs($endTimeStamp - $startTimeStamp);

			$numberDays = $timeDiff/86400;  // 86400 seconds in one day

			// and you might want to convert to integer
			$numberDays = intval($numberDays);
		    return $numberDays;
		}

		public function getBookingDataByOrderId($order_id)
		{
			$result = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."htl_booking_detail` WHERE `id_order`=".$order_id);
			if ($result)
				return $result;
			return false;
		}

		public function updateBookingOrderStatusBYOrderId($order_id, $new_status, $id_room)
		{
			$table = 'htl_booking_detail';
			if ($new_status == 2)
				$data = array('id_status' => $new_status,'check_in'=>date('y-m-d h:i:s'));
			elseif ($new_status == 3)
				$data = array('id_status' => $new_status,'check_out'=>date('y-m-d h:i:s'));
			else
				$data = array('id_status' => $new_status); 

			$where = 'id_order = '.$order_id.' AND id_room = '.$id_room;
			$result = Db::getInstance()->update($table, $data, $where);
			if ($result)
				return $result;
			return false;
		}

		// $for_room_type is used for product page and category page for block cart
		public function DataForFrontSearch($date_from, $date_to, $id_hotel, $id_product = 0, $for_room_type = 0, $adult = 0, $children = 0, $ratting = -1, $amenities = 0, $price = 0, $id_cart = 0, $id_guest = 0)
		{
			require_once (_PS_MODULE_DIR_.'productcomments/ProductComment.php');

			$this->context = Context::getContext();

			$booking_data = $this->getBookingData($date_from, $date_to, $id_hotel, $id_product, $adult, $children, 0, 0, 1, 0, 0, 0, $id_cart, $id_guest);
			// ddd($booking_data);
			if (!$for_room_type) 
			{
				if (!empty($booking_data))
				{
					$obj_rm_type = new HotelRoomType();

					foreach ($booking_data['rm_data'] as $key => $value)
					{
						if (empty($value['data']['available'])) 
						{
							unset($booking_data['rm_data'][$key]);
						}
						else
						{
							$prod_ratting = ProductComment::getAverageGrade($value['id_product'])['grade'];
							if ($prod_ratting === NULL) 
								$prod_ratting = 0;

							if ($prod_ratting < $ratting && $ratting != -1) 
							{
								unset($booking_data['rm_data'][$key]);
							}
							else
							{
								$product = new Product($value['id_product'], false, $this->context->language->id);

								$product_feature = $product->getFrontFeaturesStatic($this->context->language->id, $value['id_product']);

								$prod_amen = array();
								if (!empty($amenities) && $amenities) 
								{
									$prod_amen = $amenities;
									foreach ($product_feature as $a_key => $a_val) 
									{
										if(($pa_key = array_search($a_val['id_feature'], $prod_amen)) !== false) 
										{
										    unset($prod_amen[$pa_key]);
										    if (empty($prod_amen)) 
										    	break;
										}
									}
									if (!empty($prod_amen))
										unset($booking_data['rm_data'][$key]);
								}

								if (empty($prod_amen)) 
								{
									$prod_price = Product::getPriceStatic($value['id_product']);
									if (empty($price) || ($price['from'] <= $prod_price && $price['to'] >= $prod_price))
									{
										$cover_image_arr = $product->getCover($value['id_product']);

										if(!empty($cover_image_arr))
											$cover_img = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_arr['id_image'], 'home_default');
										else
											$cover_img = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code."-default", 'home_default');

										$room_left = count($booking_data['rm_data'][$key]['data']['available']);

										$rm_dtl = $obj_rm_type->getRoomTypeInfoByIdProduct($value['id_product']);

										$booking_data['rm_data'][$key]['name'] = $product->name;
										$booking_data['rm_data'][$key]['image'] = $cover_img;
										$booking_data['rm_data'][$key]['description'] = $product->description_short;
										$booking_data['rm_data'][$key]['feature'] = $product_feature;
										$booking_data['rm_data'][$key]['price'] = $prod_price;

										if ($room_left <= (int)Configuration::get('WK_ROOM_LEFT_WARNING_NUMBER'))
											$booking_data['rm_data'][$key]['room_left'] = $room_left;

										$booking_data['rm_data'][$key]['adult'] = $rm_dtl['adult'];
										$booking_data['rm_data'][$key]['children'] = $rm_dtl['children'];

										$booking_data['rm_data'][$key]['ratting'] = $prod_ratting;
										$booking_data['rm_data'][$key]['num_review'] = ProductComment::getCommentNumber($value['id_product']);

										if (Configuration::get('PS_REWRITING_SETTINGS'))
											$booking_data['rm_data'][$key]['product_link'] = $this->context->link->getProductLink($product).'?date_from='.$date_from.'&date_to='.$date_to;
										else
											$booking_data['rm_data'][$key]['product_link'] = $this->context->link->getProductLink($product).'&date_from='.$date_from.'&date_to='.$date_to;
									}
									else
										unset($booking_data['rm_data'][$key]);
								}
							}
						}
					}
				}
			}
			return $booking_data;
		}

		public function getAvailableRoomsForSwaping($date_from, $date_to, $room_type, $hotel_id)
		{
			$sql = "SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_hotel`=".$hotel_id." AND `id_product`=".$room_type." AND id_status = 1 AND `id` NOT IN ("."SELECT `id_room` FROM `"._DB_PREFIX_."htl_booking_detail` WHERE date_from < '$date_to' AND date_to > '$date_from')";
						
			$avail_rooms = Db::getInstance()->executeS($sql);

			if ($avail_rooms)
				return $avail_rooms;
			return false;
		}

		public function reallocateRoomWithAvailableSameRoomType($current_room_id, $date_from, $date_to, $swapped_room_id)
		{
			$date_from = date('Y-m-d H:i:s', strtotime($date_from));
			$date_to = date('Y-m-d H:i:s', strtotime($date_to));
			$table = 'htl_booking_detail';
			$table2 = 'htl_cart_booking_data';
			$data = array('id_room'=>$swapped_room_id);
			$where = "date_from='$date_from' AND date_to='$date_to' AND id_room=".$current_room_id;
			$result = Db::getInstance()->update($table, $data, $where);
			$result2 = Db::getInstance()->update($table2, $data, $where);
			if ($result)
			{
				$result2 = Db::getInstance()->update($table2, $data, $where);
				if ($result2)
					return true;
				return false;
			}
			return false;
		}
	}