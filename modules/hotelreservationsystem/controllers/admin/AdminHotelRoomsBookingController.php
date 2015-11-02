<?php
class AdminHotelRoomsBookingController extends ModuleAdminController 
{
	public function __construct() 
	{
		$this->table = 'htl_booking_detail';
		$this->className = 'HotelBookingDetail';
		$this->lang = false;
		$this->bootstrap = true;
		$this->context = Context::getContext();

		parent::__construct();

		$this->setPhpCookieData();
	}

	public function setPhpCookieData()
	{
		if (!isset($_COOKIE['id_guest'])) 
		{
			if (!isset($this->context->cookie->id_guest)) 
				Guest::setNewGuest($this->context->cookie);
			
			setcookie('id_guest', $this->context->cookie->id_guest , time() + 86400, "/");
		}
		else
		{
			$this->context->cookie->id_guest = $_COOKIE['id_guest'];
			setcookie('id_guest', $this->context->cookie->id_guest , time() + 86400, "/");
		}
		$guest = new Guest($this->context->cookie->id_guest);
		
		if (!isset($_COOKIE['id_cart']) && !isset($this->context->cart->id)) 
		{
			$cart = new Cart();
			
			$cart->recyclable = 0;
			$cart->gift = 0;
			$cart->id_shop = (int)$this->context->shop->id;
			$cart->id_lang = (($id_lang = (int)Tools::getValue('id_lang')) ? $id_lang : Configuration::get('PS_LANG_DEFAULT'));
			$cart->id_currency = (($id_currency = (int)Tools::getValue('id_currency')) ? $id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
			$cart->id_address_delivery = 0;
			$cart->id_address_invoice = 0;
			$cart->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
			$cart->id_guest = (int)$this->context->cookie->id_guest;
			$cart->setNoMultishipping();
			$cart->save();

			$this->context->cart = $cart;
			$this->context->cookie->id_cart = $cart->id;
			
			setcookie('id_cart', $cart->id , time() + 86400, "/");
		}
		else
		{
			$cart = new Cart((int)$_COOKIE['id_cart']);

			$this->context->cart = $cart;
			$this->context->cookie->id_cart = $cart->id;
			setcookie('id_cart', $cart->id , time() + 86400, "/");
		}

		$customer = new Customer();
		$customer->id_gender = 0;
		$customer->id_default_group = 1;
		$customer->outstanding_allow_amount = 0;
		$customer->show_public_prices = 0;
		$customer->max_payment_days = 0;
		$customer->active = 1;
		$customer->is_guest = 0;
		$customer->deleted = 0;
		$customer->logged = 0;
		$customer->id_guest = $this->context->cookie->id_guest;
		
		$this->context->customer = $customer;
	}

	public function initContent()
	{
		$this->show_toolbar = false;
		$this->display = 'view';
		parent::initContent();
	}

	public function renderView()
	{
		$check_calender_var = 0;
		$obj_booking_dtl = new HotelBookingDetail();
		$obj_htl_info = new HotelBranchInformation();
		$obj_rm_type = new HotelRoomType();
		$obj_cart_book_data = new HotelCartBookingData();

		$id_cart = $this->context->cart->id;
		$id_guest = $this->context->cookie->id_guest;
		
		$this->context->smarty->assign(array('id_cart' => $id_cart, 
											'id_guest' => $id_guest));

		$cart_bdata = $obj_cart_book_data->getCartBookingDetailsByIdCartIdGuest($id_cart, $id_guest);
		if ($cart_bdata) 
		{
			$cart_tamount = $this->context->cart->getOrderTotal();
			$this->context->smarty->assign(array('cart_bdata' => $cart_bdata, 
												'cart_tamount' => $cart_tamount));
		}

		// No use of adult, child, num_rooms
		$adult = 0;
		$children = 0;
		$num_rooms = 1;

		if (Tools::isSubmit('search_hotel_list'))
		{
			$date_from = Tools::getValue('from_date');
			$date_to = Tools::getValue('to_date');
			$hotel_id = Tools::getValue('hotel_id');
			$room_type = Tools::getValue('room_type');

			if ($date_from == '')
				$this->errors[] = Tools::displayError('Date From is required field.');
			if ($date_to == '')
				$this->errors[] = Tools::displayError('Date To is required field.');
			if ($date_to <= $date_from)
				$this->errors[] = Tools::displayError('Date To should be greater than Date From.');
			if ($hotel_id == '')
				$this->errors[] = Tools::displayError('Hotel name is required field.');

			$booking_data = array();
			$booking_calendar_data = array();
			$check_css_condition_var = '';
			if (!count($this->errors))
			{
				$booking_data = $obj_booking_dtl->getBookingData($date_from, $date_to, $hotel_id, $room_type, $adult, $children, $num_rooms, 0, 1, 1, 1, 1, $id_cart, $id_guest, 1);

				if ($booking_data)
				{
					foreach ($booking_data['rm_data'] as $key => $value)
					{
						if ($value['data']['partially_available'])
						{
							foreach ($value['data']['partially_available'] as $key1 => $value1)
							{
								if ($value1['avai_dates'])
								{
									foreach ($value1['avai_dates'] as $key2 => $value2)
									{
										$explode_date = explode(' ', $value2);
										$date_start = date('Y-m-d', strtotime($explode_date[0]));
										$date_end = date('Y-m-d', strtotime($explode_date[2]));
										$result = $obj_cart_book_data->checkExistanceOfRoomInCurrentCart($value1['id_room'], $date_start, $date_end, $id_cart, $id_guest);
										if ($result)
											$room_in_cart = true;
										else
											$room_in_cart = false;
										$booking_data['rm_data'][$key]['data']['partially_available'][$key1]['check_cart'][$key2]['in_current_cart'] = $room_in_cart;
										$booking_data['rm_data'][$key]['data']['partially_available'][$key1]['check_cart'][$key2]['id_cart'] = $this->context->cart->id;
										$booking_data['rm_data'][$key]['data']['partially_available'][$key1]['check_cart'][$key2]['cart_booking_data_id'] = $result;
									}
								}
							}
						}
					}

					foreach ($booking_data['rm_data'] as $avil_k => $avail_v)
					{
						if ($avail_v['data']['available'])
						{
							foreach ($avail_v['data']['available'] as $avil_k1 => $avail_v1)
							{
								$result = $obj_cart_book_data->checkExistanceOfRoomInCurrentCart($avail_v1['id_room'], $date_from, $date_to, $id_cart, $id_guest);
								if ($result)
									$room_in_cart = true;
								else
									$room_in_cart = false;

								$booking_data['rm_data'][$avil_k]['data']['available'][$avil_k1]['in_current_cart'] = $room_in_cart;
								$booking_data['rm_data'][$avil_k]['data']['available'][$avil_k1]['id_cart'] = $this->context->cart->id;
								$booking_data['rm_data'][$avil_k]['data']['available'][$avil_k1]['cart_booking_data_id'] = $result;
							}
						}
					}

					foreach ($booking_data['rm_data'] as $booked_k => $booked_v)
					{
						if ($booked_v['data']['booked'])
						{
							foreach ($booked_v['data']['booked'] as $booked_k1 => $booked_v1)
							{
								$cust_obj = new Customer($booked_v1['id_customer']);

								$booking_data['rm_data'][$booked_k]['data']['booked'][$booked_k1]['alloted_cust_name'] = $cust_obj->firstname.' '.$cust_obj->lastname;
								$booking_data['rm_data'][$booked_k]['data']['booked'][$booked_k1]['alloted_cust_email'] = $cust_obj->email;
								$booking_data['rm_data'][$booked_k]['data']['booked'][$booked_k1]['avail_rooms_to_swap'] = $obj_booking_dtl->getAvailableRoomsForSwaping($booked_v1['date_from'], $booked_v1['date_to'], $booked_v1['id_product'], $booked_v1['id_hotel']);
							}
						}
					}
				}
				//sumit to show info of every date
				$start_date = $date_from; // hard-coded '01' for first day
				$last_day_this_month  = $date_to;

				while ($start_date <= $last_day_this_month)
				{
					$cal_date_from = $start_date;
					$cal_date_to = date('Y-m-d', strtotime($cal_date_from)+ 86400);

					$booking_calendar_data[$cal_date_from] = $obj_booking_dtl->getBookingData($cal_date_from, $cal_date_to, $hotel_id, $room_type, $adult, $children, $num_rooms, 1, 1, 1, 1, 1, $id_cart, $id_guest, 1);
					$start_date = date('Y-m-d', strtotime($start_date)+ 86400);
				}

				if ($num_rooms <= $booking_data['stats']['num_avail'])
					$check_css_condition_var = 'available';
				else if($num_rooms <= $booking_data['stats']['num_part_avai'])
					$check_css_condition_var = 'part_available';
				else
					$check_css_condition_var = 'unavailable';
			}
		}
		else
		{
			$check_calender_var = 1;
			$date_from = date('Y-m-d');
			$date_to = date('Y-m-d', strtotime($date_from)+ 86400);
			$hotel_id = 1;
			$room_type = 0;

			$booking_data = $obj_booking_dtl->getBookingData($date_from, $date_to, $hotel_id, $room_type, $adult, $children, $num_rooms, 0, 1, 1, 1, 1, $id_cart, $id_guest, 1);

			// ddd($booking_data);

			if ($booking_data)
			{
				foreach ($booking_data['rm_data'] as $key => $value)
				{
					if ($value['data']['partially_available'])
					{
						foreach ($value['data']['partially_available'] as $key1 => $value1)
						{
							if ($value1['avai_dates'])
							{
								foreach ($value1['avai_dates'] as $key2 => $value2)
								{
									$explode_date = explode(' ', $value2);
									$date_start = date('Y-m-d', strtotime($explode_date[0]));
									$date_end = date('Y-m-d', strtotime($explode_date[2]));
									$result = $obj_cart_book_data->checkExistanceOfRoomInCurrentCart($value1['id_room'], $date_start, $date_end, $id_cart, $id_guest);
									if ($result)
										$room_in_cart = true;
									else
										$room_in_cart = false;
									$booking_data['rm_data'][$key]['data']['partially_available'][$key1]['check_cart'][$key2]['in_current_cart'] = $room_in_cart;
									$booking_data['rm_data'][$key]['data']['partially_available'][$key1]['check_cart'][$key2]['id_cart'] = $this->context->cart->id;
									$booking_data['rm_data'][$key]['data']['partially_available'][$key1]['check_cart'][$key2]['cart_booking_data_id'] = $result;
								}
							}
						}
					}
				}
				foreach ($booking_data['rm_data'] as $avil_k => $avail_v)
				{
					if ($avail_v['data']['available'])
					{
						foreach ($avail_v['data']['available'] as $avil_k1 => $avail_v1)
						{
							$result = $obj_cart_book_data->checkExistanceOfRoomInCurrentCart($avail_v1['id_room'], $date_from, $date_to, $id_cart, $id_guest);
							if ($result)
								$room_in_cart = true;
							else
								$room_in_cart = false;

							$booking_data['rm_data'][$avil_k]['data']['available'][$avil_k1]['in_current_cart'] = $room_in_cart;
							$booking_data['rm_data'][$avil_k]['data']['available'][$avil_k1]['id_cart'] = $this->context->cart->id;
							$booking_data['rm_data'][$avil_k]['data']['available'][$avil_k1]['cart_booking_data_id'] = $result;
						}
					}
				}
				foreach ($booking_data['rm_data'] as $booked_k => $booked_v)
				{
					if ($booked_v['data']['booked'])
					{
						foreach ($booked_v['data']['booked'] as $booked_k1 => $booked_v1)
						{
							$cust_obj = new Customer($booked_v1['id_customer']);

							$booking_data['rm_data'][$booked_k]['data']['booked'][$booked_k1]['alloted_cust_name'] = $cust_obj->firstname.' '.$cust_obj->lastname;
							$booking_data['rm_data'][$booked_k]['data']['booked'][$booked_k1]['alloted_cust_email'] = $cust_obj->email;
							$booking_data['rm_data'][$booked_k]['data']['booked'][$booked_k1]['avail_rooms_to_swap'] = $obj_booking_dtl->getAvailableRoomsForSwaping($booked_v1['date_from'], $booked_v1['date_to'], $booked_v1['id_product'], $booked_v1['id_hotel']);
						}
					}
				}
			}
			//sumit to show info of every date

			$start_date = date('Y-m-01'); // hard-coded '01' for first day
			$last_day_this_month  = date('Y-m-t');

			while ($start_date <= $last_day_this_month)
			{
				$cal_date_from = $start_date;
				$cal_date_to = date('Y-m-d', strtotime($cal_date_from)+ 86400);

				$booking_calendar_data[$cal_date_from] = $obj_booking_dtl->getBookingData($cal_date_from, $cal_date_to, $hotel_id, $room_type, $adult, $children, $num_rooms, 1, 1, 1, 1, 1, $id_cart, $id_guest, 1);
				$start_date = date('Y-m-d', strtotime($start_date)+ 86400);
			}
			if ($num_rooms <= $booking_data['stats']['num_avail'])
				$check_css_condition_var = 'default_available';
			else if($num_rooms <= $booking_data['stats']['num_part_avai'])
				$check_css_condition_var = 'default_part_available';
			else
				$check_css_condition_var = 'default_unavailable';
		}


		$currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));

		$hotel_name = $obj_htl_info->hotelsNameAndId();
		$all_room_type = $obj_rm_type->getRoomTypeByHotelId($hotel_id);

		$rms_in_cart = $obj_cart_book_data->getCountRoomsInCart($id_cart, $id_guest);

		$this->tpl_view_vars = array(
			'check_calender_var'=>$check_calender_var,
			'date_from' => $date_from,
			'date_to'=>$date_to,
			'hotel_id'=>$hotel_id,
			'room_type'=>$room_type,
			'adult'=>$adult,
			'children'=>$children,
			'num_rooms'=>$num_rooms,
			'booking_data'=>$booking_data,
			'booking_calendar_data'=>$booking_calendar_data,
			'check_css_condition_var'=>$check_css_condition_var,
			'hotel_name'=>$hotel_name,
			'all_room_type'=>$all_room_type,
			'currency'=>$currency,
			'rms_in_cart'=>$rms_in_cart,
			);

		return parent::renderView();
	}

	public function ajaxProcessGetRoomType()
	{
		$hotel_id  = Tools::getValue('hotel_id');

		$obj_room_type = new HotelRoomType();
		$room_type_info = $obj_room_type->getRoomTypeByHotelId($hotel_id);

		die(Tools::jsonEncode($room_type_info));
	}

	public function ajaxProcessAddDataToCart()
	{
		// for Add Quantity
		$id_room = Tools::getValue('id_room');
		$booking_type = Tools::getValue('booking_type');
		$comment = Tools::getValue('comment');

		// for both (add , delete)
		$id_hotel = Tools::getValue('id_hotel');
		$id_product = Tools::getValue('id_prod');
		$date_from = Tools::getValue('date_from');
		$date_to = Tools::getValue('date_to');
		$search_id_prod = Tools::getValue('search_id_prod');
		$search_date_from = Tools::getValue('search_date_from');
		$search_date_to = Tools::getValue('search_date_to');

		// for delete quantity
		$id_cart = Tools::getValue('id_cart');
		$id_cart_book_data = Tools::getValue('id_cart_book_data');
		$ajax_delete = Tools::getValue('ajax_delete'); // If delete from cart(not for room list delete(pagebottom tabs))

		$opt = Tools::getValue('opt'); // if 1 then add quantity or if 0 means delete quantity

		// discuss later
		// 
		// $obj_hotel_info = new HotelBranchInformation();
		// $hotel_info = $obj_hotel_info->hotelBranchInfoById($id_hotel);

		// $date_from = date('Y-m-d H:i:s', strtotime("$date_from +".date('H', strtotime($hotel_info['check_in']))." hours +".date('i', strtotime($hotel_info['check_in']))." minutes"));
		// $date_to = date('Y-m-d H:i:s', strtotime("$date_to +".date('H', strtotime($hotel_info['check_out']))." hours +".date('i', strtotime($hotel_info['check_out']))." minutes"));
		
		$obj_booking_dtl = new HotelBookingDetail();
		$num_day = $obj_booking_dtl->getNumberOfDays($date_from, $date_to); //quantity of product

		$product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'));
		// $id_product_attribute = $product->getDefaultIdProductAttribute();

		if ($opt) 
		{
			// $cart_rules = $this->context->cart->getCartRules();
			$amount = Product::getPriceStatic($id_product, true, null, 6, null,	false, true, $num_day);
		}

		if ($opt) 
			$direction = 'up';
		else
			$direction = 'down';

		$this->context->cart->updateQty($num_day, $id_product, null, false, $direction);

		$total_amount = $this->context->cart->getOrderTotal();

		$id_cart = $this->context->cart->id;
		$id_guest = $this->context->cookie->id_guest;

		$obj_cart_book_data = new HotelCartBookingData();
		if ($opt) 
		{
			$obj_cart_book_data->id_cart = 		$id_cart;
			$obj_cart_book_data->id_guest = 	$id_guest;
			$obj_cart_book_data->id_product = 	$id_product;
			$obj_cart_book_data->id_room = 		$id_room;
			$obj_cart_book_data->id_hotel = 	$id_hotel;
			$obj_cart_book_data->amount = 		$amount;
			$obj_cart_book_data->booking_type = $booking_type;
			$obj_cart_book_data->comment = 		$comment;
			$obj_cart_book_data->date_from = 	$date_from;
			$obj_cart_book_data->date_to = 		$date_to;
			$obj_cart_book_data->save();

			$obj_rm_info = new HotelRoomInformation($id_room); 

			$rms_in_cart = $obj_cart_book_data->getCountRoomsInCart($id_cart, $id_guest);
			
			$booking_stats = $obj_booking_dtl->getBookingData($date_from, $date_to, $id_hotel, $search_id_prod, 0, 0, 1, 1, 1, 1, 0, 0, $id_cart, $id_guest, 1);

			$cart_data = array('room_num' => $obj_rm_info->room_num,
								'room_type' => Product::getProductName((int)$id_product),
								'date_from' => date('Y-M-d', strtotime($date_from)),
								'date_to' => date('Y-M-d', strtotime($date_to)),
								'amount' => $amount,
								'qty' => $num_day,
								'rms_in_cart' => $rms_in_cart,
								'total_amount' => $total_amount,
								'booking_stats' => $booking_stats,
								'id_cart_book_data' => $obj_cart_book_data->id);

			if ($obj_cart_book_data->id) 
				die(Tools::jsonEncode($cart_data));
			else
				die(0);
		}
		else
		{
			$data_dlt = $obj_cart_book_data->deleteRowById($id_cart_book_data);
			if ($data_dlt) 
			{
				$rms_in_cart = $obj_cart_book_data->getCountRoomsInCart($id_cart, $id_guest);

				if (!$ajax_delete) 
				{
					$booking_stats = $obj_booking_dtl->getBookingData($date_from, $date_to, $id_hotel, $search_id_prod, 0, 0, 1, 1, 1, 1, 0, 0, $id_cart, $id_guest, 1);
					$cart_data = array('total_amount' => $total_amount,
									   'rms_in_cart' => $rms_in_cart,
									   'booking_stats' => $booking_stats);
				}

				if ($ajax_delete) 
				{
					$obj_htl_info = new HotelBranchInformation();
					$obj_rm_type = new HotelRoomType();

					$this->context->smarty->assign(array('id_cart' => $id_cart, 
												'id_guest' => $id_guest));

					// No use of adult, child, num_rooms
					$adult = 0;
					$children = 0;
					$num_rooms = 1;

					$booking_data = array();
					$booking_data = $obj_booking_dtl->getBookingData($search_date_from, $search_date_to, $id_hotel, $search_id_prod, $adult, $children, $num_rooms, 0, 1, 1, 1, 1, $id_cart, $id_guest, 1);

					$this->context->smarty->assign(array(
						'date_from' => $date_from,
						'date_to'=>$date_to,
						'booking_data'=>$booking_data,
						'ajax_delete'=>$ajax_delete,
					));
					
					$tpl_path = 'hotelreservationsystem/views/templates/admin/hotel_rooms_booking/helpers/view/view.tpl';
					$room_tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_.$tpl_path);
				
					$cart_data = array('total_amount' => $total_amount,
									   'room_tpl' => $room_tpl,
									   'rms_in_cart' => $rms_in_cart,
									   'booking_data' => $booking_data,
								);
				}
				die(Tools::jsonEncode($cart_data));
			}
			else
				die(0);
		}
	}

	public function ajaxProcessGetDataOnMonthChange()
	{
		$month = Tools::getValue('month');
		$year = Tools::getValue('year');
		$query_date = $year.'-'.$month.'-04';
		$start_date = date('Y-m-01', strtotime($query_date)); // hard-coded '01' for first day
		$last_day_this_month  = date('Y-m-t', strtotime($query_date));
		$hotel_id = 1;
		$room_type = 0;
		$adult = 1;
		$children = 0;
		$num_rooms = 1;

		$obj_booking_dtl = new HotelBookingDetail();

		while ($start_date <= $last_day_this_month)
		{
			$cal_date_from = $start_date;
			$cal_date_to = date('Y-m-d', strtotime($cal_date_from)+ 86400);

			$booking_calendar_data[$cal_date_from] = $obj_booking_dtl->getBookingData($cal_date_from, $cal_date_to, $hotel_id, $room_type, $adult, $children, $num_rooms);
			$start_date = date('Y-m-d', strtotime($start_date)+ 86400);
		}
		if ($booking_calendar_data)
			die(Tools::jsonEncode($booking_calendar_data));
		else
			die(0);
	}

	public function postProcess()
	{
		if (Tools::isSubmit('swap_allocated_rooms'))
		{
			$current_room_id = Tools::getValue('id_room');
			$current_room = Tools::getValue('curr_room_num');
			$date_from = Tools::getValue('date_from');
			$date_to = Tools::getValue('date_to');
			$swapped_room_id = Tools::getValue('swapped_avail_rooms');

			$obj_booking_dtl = new HotelBookingDetail();
			$room_swapped = $obj_booking_dtl->reallocateRoomWithAvailableSameRoomType($current_room_id, $date_from, $date_to, $swapped_room_id);
			if (!$room_swapped)
				$this->errors[] = Tools::displayError('Some error occured. Please try again.');
		}
		parent::postProcess();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(array(
				_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css',
			));

		$this->addJs(_PS_MODULE_DIR_.$this->module->name.'/views/js/HotelReservationAdmin.js');
	}
}
