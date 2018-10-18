<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

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

        //unset($_COOKIE['wk_id_guest']);
        //unset($_COOKIE['wk_id_cart']);
        $this->setPhpCookieData();
    }

    public function setPhpCookieData()
    {
        if (!isset($_COOKIE['wk_id_guest']) || !$_COOKIE['wk_id_guest']) {
            if (!isset($this->context->cookie->id_guest)) {
                Guest::setNewGuest($this->context->cookie);
            }

            setcookie('wk_id_guest', $this->context->cookie->id_guest, time() + 86400, "/");
        } else {
            $this->context->cookie->id_guest = $_COOKIE['wk_id_guest'];
            setcookie('wk_id_guest', $this->context->cookie->id_guest, time() + 86400, "/");
        }
        $guest = new Guest($this->context->cookie->id_guest);

        if (!isset($_COOKIE['wk_id_cart']) && !isset($this->context->cart->id)) {
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

            setcookie('wk_id_cart', $cart->id, time() + 86400, "/");
        } else {
            $cart = new Cart((int)$_COOKIE['wk_id_cart']);

            $this->context->cart = $cart;
            $this->context->cookie->id_cart = $cart->id;
            setcookie('wk_id_cart', $cart->id, time() + 86400, "/");
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

        return true;
    }

    public function initContent()
    {
        $this->show_toolbar = false;
        $this->display = 'view';
        parent::initContent();
    }

    public function renderView()
    {
        $obj_rm_type = new HotelRoomType();
        $obj_booking_dtl = new HotelBookingDetail();
        $obj_htl_info = new HotelBranchInformation();
        $obj_cart_book_data = new HotelCartBookingData();

        $check_calender_var = 0;
        $hotel_name = '';
        $all_room_type = array();
        $rms_in_cart = 0;
        $room_type = 0;

        $id_cart = $this->context->cart->id;
        $id_guest = $this->context->cookie->id_guest;

        $this->context->smarty->assign(
            array(
                'id_cart' => $id_cart,
                'id_guest' => $id_guest
            )
        );

        $cart_bdata = $obj_cart_book_data->getCartBookingDetailsByIdCartIdGuest(
            $id_cart,
            $id_guest,
            Configuration::get('PS_LANG_DEFAULT')
        );
        if ($cart_bdata) {
            $cart_tamount = $this->context->cart->getOrderTotal();
            $this->context->smarty->assign(
                array(
                    'cart_bdata' => $cart_bdata,
                    'cart_tamount' => $cart_tamount
                )
            );
        }

        // No use of adult, child, num_rooms
        $adult = 0;
        $children = 0;
        $num_rooms = 1;
        $check_css_condition_var = '';

        if (Tools::isSubmit('search_hotel_list')) {
            $date_from = Tools::getValue('from_date');
            $date_to = Tools::getValue('to_date');

            $date_from = date("Y-m-d", strtotime($date_from));
            $date_to = date("Y-m-d", strtotime($date_to));

            $hotel_id = Tools::getValue('hotel_id');
            $room_type = Tools::getValue('room_type');

            if ($date_from == '') {
                $this->errors[] = $this->l('Date From is required field.');
            }
            if ($date_to == '') {
                $this->errors[] = $this->l('Date To is required field.');
            }
            if ($date_to <= $date_from) {
                $this->errors[] = $this->l('Date To should be greater than Date From.');
            }
            if ($hotel_id == '') {
                $this->errors[] = $this->l('Hotel name is required field.');
            }

            $booking_calendar_data = array();
            if (!count($this->errors)) {
                $booking_data = $this->getAllBookingDataInfo(
                    $date_from,
                    $date_to,
                    $hotel_id,
                    $room_type,
                    $adult,
                    $children,
                    $num_rooms,
                    $id_cart,
                    $id_guest
                );

                /*$booking_data = $obj_booking_dtl->getBookingData($date_from, $date_to, $hotel_id, $room_type, $adult, $children, $num_rooms, 0, 1, 1, 1, 1, $id_cart, $id_guest, 1);*/

                //To show info of every date
                $start_date = $date_from; // hard-coded '01' for first day
                $last_day_this_month  = $date_to;

                while ($start_date <= $last_day_this_month) {
                    $cal_date_from = $start_date;
                    $cal_date_to = date('Y-m-d', strtotime('+1 day', strtotime($cal_date_from)));
                    $booking_calendar_data[$cal_date_from] = $obj_booking_dtl->getBookingData(
                        $cal_date_from,
                        $cal_date_to,
                        $hotel_id,
                        $room_type,
                        $adult,
                        $children,
                        $num_rooms,
                        1,
                        1,
                        1,
                        1,
                        1,
                        $id_cart,
                        $id_guest,
                        1
                    );
                    $start_date = date('Y-m-d', strtotime('+1 day', strtotime($start_date)));
                }

                if (isset($booking_data)) {
                    if ($num_rooms <= $booking_data['stats']['num_avail']) {
                        $check_css_condition_var = 'available';
                    } elseif ($num_rooms <= $booking_data['stats']['num_part_avai']) {
                        $check_css_condition_var = 'part_available';
                    } else {
                        $check_css_condition_var = 'unavailable';
                    }
                } else {
                    $booking_data = false;
                }
            }
        } else {
            $check_calender_var = 1;
            $date_from = date('Y-m-d');
            $date_to = date('Y-m-t');
            // if date from is the last date of the month
            if (strtotime($date_from) == strtotime($date_to)) {
                $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            }
            $obj_htl_info = new HotelBranchInformation();
            $htl_info = $obj_htl_info->hotelBranchesInfo(false, 1);
            $hotel_id = $htl_info[0]['id'];
            if ($hotel_id) {
                $booking_data = $this->getAllBookingDataInfo(
                    $date_from,
                    $date_to,
                    $hotel_id,
                    $room_type,
                    $adult,
                    $children,
                    $num_rooms,
                    $id_cart,
                    $id_guest
                );
                /*$booking_data = $obj_booking_dtl->getBookingData($date_from, $date_to, $hotel_id, $room_type, $adult, $children, $num_rooms, 0, 1, 1, 1, 1, $id_cart, $id_guest, 1);*/

                // show info of every date

                $start_date = date('Y-m-d'); // hard-coded '01' for first day of the month
                $last_day_this_month  = date('Y-m-t');

                while ($start_date <= $last_day_this_month) {
                    $cal_date_from = $start_date;
                    $cal_date_to = date('Y-m-d', strtotime('+1 day', strtotime($cal_date_from)));

                    $booking_calendar_data[$cal_date_from] = $obj_booking_dtl->getBookingData(
                        $cal_date_from,
                        $cal_date_to,
                        $hotel_id,
                        $room_type,
                        $adult,
                        $children,
                        $num_rooms,
                        1,
                        1,
                        1,
                        1,
                        1,
                        $id_cart,
                        $id_guest,
                        1
                    );
                    $start_date = date('Y-m-d', strtotime('+1 day', strtotime($start_date)));
                }
                if (isset($booking_data)) {
                    if ($num_rooms <= $booking_data['stats']['num_avail']) {
                        $check_css_condition_var = 'default_available';
                    } elseif ($num_rooms <= $booking_data['stats']['num_part_avai']) {
                        $check_css_condition_var = 'default_part_available';
                    } else {
                        $check_css_condition_var = 'default_unavailable';
                    }
                } else {
                    $booking_data = false;
                }
            } else {
                $booking_data = false;
                $booking_calendar_data = false;
            }
        }
        $hotel_name = $obj_htl_info->hotelBranchesInfo(false);
        $all_room_type = $obj_rm_type->getRoomTypeByHotelId($hotel_id, Configuration::get('PS_LANG_DEFAULT'));

        $rms_in_cart = $obj_cart_book_data->getCountRoomsInCart($id_cart, $id_guest);
        $date_from = date("d-m-Y", strtotime($date_from));
        $date_to = date("d-m-Y", strtotime($date_to));
        $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));

        $this->tpl_view_vars = array(
            'check_calender_var' => $check_calender_var,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'hotel_id' => $hotel_id,
            'room_type' => $room_type,
            'adult' => $adult,
            'children' => $children,
            'num_rooms' => $num_rooms,
            'booking_data' => $booking_data,
            'booking_calendar_data' => $booking_calendar_data,
            'check_css_condition_var' => $check_css_condition_var,
            'hotel_name' => $hotel_name,
            'all_room_type' => $all_room_type,
            'currency' => $currency,
            'rms_in_cart' => $rms_in_cart,
        );
        return parent::renderView();
    }

    public function ajaxProcessGetRoomType()
    {
        $hotel_id  = Tools::getValue('hotel_id');

        $obj_room_type = new HotelRoomType();
        $room_type_info = $obj_room_type->getRoomTypeByHotelId($hotel_id, Configuration::get('PS_LANG_DEFAULT'));

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

        $date_from = date("Y-m-d", strtotime($date_from));
        $date_to = date("Y-m-d", strtotime($date_to));

        $search_id_prod = Tools::getValue('search_id_prod');
        $search_date_from = Tools::getValue('search_date_from');
        $search_date_to = Tools::getValue('search_date_to');

        // for delete quantity
        $id_cart = Tools::getValue('id_cart');
        $id_cart_book_data = Tools::getValue('id_cart_book_data');
        $ajax_delete = Tools::getValue('ajax_delete'); // If delete from cart(not for room list delete(pagebottom tabs))

        $opt = Tools::getValue('opt'); // if 1 then add quantity or if 0 means delete quantity

        $obj_booking_dtl = new HotelBookingDetail();
        $num_day = $obj_booking_dtl->getNumberOfDays($date_from, $date_to); //quantity of product
        $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'));

        if ($opt) {
            $unit_price = Product::getPriceStatic(
                $id_product,
                HotelBookingDetail::useTax(),
                null,
                6,
                null,
                false,
                true,
                $num_day
            );
        }

        if ($opt) {
            $direction = 'up';
        } else {
            $direction = 'down';
        }

        $this->context->cart->updateQty($num_day, $id_product, null, false, $direction);


        $id_cart = $this->context->cart->id;
        $id_guest = $this->context->cookie->id_guest;

        $obj_cart_book_data = new HotelCartBookingData();
        $total_amount = $this->context->cart->getOrderTotal();
        if ($opt) {
            $obj_cart_book_data->id_cart = $id_cart;
            $obj_cart_book_data->id_guest = $id_guest;
            $obj_cart_book_data->id_product = $id_product;
            $obj_cart_book_data->id_room = $id_room;
            $obj_cart_book_data->id_hotel = $id_hotel;
            $obj_cart_book_data->quantity = $num_day;
            $obj_cart_book_data->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
            $obj_cart_book_data->booking_type = $booking_type;
            $obj_cart_book_data->comment = $comment;
            $obj_cart_book_data->date_from = $date_from;
            $obj_cart_book_data->date_to = $date_to;
            $obj_cart_book_data->save();

            $obj_rm_info = new HotelRoomInformation($id_room);
            $total_amount = $this->context->cart->getOrderTotal();
            $rms_in_cart = $obj_cart_book_data->getCountRoomsInCart($id_cart, $id_guest);

            $booking_stats = $obj_booking_dtl->getBookingData(
                $search_date_from,
                $search_date_to,
                $id_hotel,
                $search_id_prod,
                0,
                0,
                1,
                1,
                1,
                1,
                0,
                0,
                $id_cart,
                $id_guest,
                1
            );
            //$rm_amount = $unit_price * (int)$num_day;
            //$rm_amount = Tools::ps_round($rm_amount, 2);
            //
            //// By webkul New way to calculate product prices with feature Prices
            $roomTypeDateRangePrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                $id_product,
                $date_from,
                $date_to
            );
            $rm_amount = $roomTypeDateRangePrice['total_price_tax_incl'];

            $cart_data = array('room_num' => $obj_rm_info->room_num,
                                'room_type' => Product::getProductName((int)$id_product),
                                'date_from' => date('Y-M-d', strtotime($date_from)),
                                'date_to' => date('Y-M-d', strtotime($date_to)),
                                'amount' => $rm_amount,
                                'qty' => $num_day,
                                'rms_in_cart' => $rms_in_cart,
                                'total_amount' => $total_amount,
                                'booking_stats' => $booking_stats,
                                'id_cart_book_data' => $obj_cart_book_data->id);

            if ($obj_cart_book_data->id) {
                die(Tools::jsonEncode($cart_data));
            } else {
                die(0);
            }
        } else {
            $total_amount = $this->context->cart->getOrderTotal();
            $data_dlt = $obj_cart_book_data->deleteRowById($id_cart_book_data);
            if ($data_dlt) {
                $rms_in_cart = $obj_cart_book_data->getCountRoomsInCart($id_cart, $id_guest);

                if (!$ajax_delete) {
                    $booking_stats = $obj_booking_dtl->getBookingData(
                        $search_date_from,
                        $search_date_to,
                        $id_hotel,
                        $search_id_prod,
                        0,
                        0,
                        1,
                        1,
                        1,
                        1,
                        0,
                        0,
                        $id_cart,
                        $id_guest,
                        1
                    );
                    $cart_data = array(
                        'total_amount' => $total_amount,
                        'rms_in_cart' => $rms_in_cart,
                        'booking_stats' => $booking_stats
                    );
                }

                if ($ajax_delete) {
                    $obj_htl_info = new HotelBranchInformation();
                    $obj_rm_type = new HotelRoomType();

                    $this->context->smarty->assign(
                        array(
                            'id_cart' => $id_cart,
                            'id_guest' => $id_guest
                        )
                    );

                    // No use of adult, child, num_rooms
                    $adult = 0;
                    $children = 0;
                    $num_rooms = 1;

                    $booking_data = array();

                    $booking_data = $this->getAllBookingDataInfo(
                        $search_date_from,
                        $search_date_to,
                        $id_hotel,
                        $search_id_prod,
                        $adult,
                        $children,
                        $num_rooms,
                        $id_cart,
                        $id_guest
                    );
                    $this->context->smarty->assign(
                        array(
                            'date_from' => $search_date_from,
                            'date_to'=>$search_date_to,
                            'booking_data'=>$booking_data,
                            'ajax_delete'=>$ajax_delete,
                        )
                    );
                    $tpl_path = 'hotelreservationsystem/views/templates/admin/hotel_rooms_booking/helpers/view/view.tpl';
                    $room_tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_.$tpl_path);

                    $cart_data = array(
                        'total_amount' => $total_amount,
                        'room_tpl' => $room_tpl,
                        'rms_in_cart' => $rms_in_cart,
                        'booking_data' => $booking_data,
                    );
                }
                die(Tools::jsonEncode($cart_data));
            } else {
                die(0);
            }
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

        while ($start_date <= $last_day_this_month) {
            $cal_date_from = $start_date;
            $cal_date_to = date('Y-m-d', strtotime('+1 day', strtotime($cal_date_from)));

            $booking_calendar_data[$cal_date_from] = $obj_booking_dtl->getBookingData(
                $cal_date_from,
                $cal_date_to,
                $hotel_id,
                $room_type,
                $adult,
                $children,
                $num_rooms
            );
            $start_date = date('Y-m-d', strtotime('+1 day', strtotime($start_date)));
        }
        if ($booking_calendar_data) {
            die(Tools::jsonEncode($booking_calendar_data));
        } else {
            die(0);
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('realloc_allocated_rooms')) {
            $current_room_id = Tools::getValue('modal_id_room');
            $current_room = Tools::getValue('modal_curr_room_num');
            $date_from = Tools::getValue('modal_date_from');
            $date_to = Tools::getValue('modal_date_to');
            $realloc_room_id = Tools::getValue('realloc_avail_rooms');

            if ($realloc_room_id == 0) {
                $this->errors[] = $this->l('Please select a room to swap with this room.');
            }
            if ($current_room_id == 0) {
                $this->errors[] = $this->l('Cuurent room is missing.');
            }
            if ($date_from == 0) {
                $this->errors[] = $this->l('Check In date is missing.');
            }
            if ($date_to == 0) {
                $this->errors[] = $this->l('Check Out date is missing.');
            }

            if (!count($this->errors)) {
                $obj_booking_dtl = new HotelBookingDetail();
                $room_swapped = $obj_booking_dtl->reallocateRoomWithAvailableSameRoomType(
                    $current_room_id,
                    $date_from,
                    $date_to,
                    $realloc_room_id
                );
                if (!$room_swapped) {
                    $this->errors[] = $this->l('Some error occured. Please try again.');
                }
            }
        }
        if (Tools::isSubmit('swap_allocated_rooms')) {
            $current_room_id = Tools::getValue('modal_id_room');
            $current_room = Tools::getValue('modal_curr_room_num');
            $date_from = Tools::getValue('modal_date_from');
            $date_to = Tools::getValue('modal_date_to');
            $swapped_room_id = Tools::getValue('swap_avail_rooms');

            if ($swapped_room_id == 0) {
                $this->errors[] = $this->l('Please select a room to swap with this room.');
            }
            if ($current_room_id == 0) {
                $this->errors[] = $this->l('Cuurent room is missing.');
            }
            if ($date_from == 0) {
                $this->errors[] = $this->l('Check In date is missing.');
            }
            if ($date_to == 0) {
                $this->errors[] = $this->l('Check Out date is missing.');
            }

            if (!count($this->errors)) {
                $obj_booking_dtl = new HotelBookingDetail();
                $room_swapped = $obj_booking_dtl->swapRoomWithAvailableSameRoomType(
                    $current_room_id,
                    $date_from,
                    $date_to,
                    $swapped_room_id
                );
                if (!$room_swapped) {
                    $this->errors[] = $this->l('Some error occured. Please try again.');
                }
            }
        }
        parent::postProcess();
    }

    public function getAllBookingDataInfo(
        $date_from,
        $date_to,
        $hotel_id,
        $room_type,
        $adult,
        $children,
        $num_rooms,
        $id_cart,
        $id_guest
    ) {
        $obj_booking_dtl = new HotelBookingDetail();
        $obj_cart_book_data = new HotelCartBookingData();
        $booking_data = $obj_booking_dtl->getBookingData(
            $date_from,
            $date_to,
            $hotel_id,
            $room_type,
            $adult,
            $children,
            $num_rooms,
            0,
            1,
            1,
            1,
            1,
            $id_cart,
            $id_guest,
            1
        );
        if ($booking_data) {
            foreach ($booking_data['rm_data'] as $key_bk_data => $value_bk_data) {
                if (isset($value_bk_data['data']['booked']) && $value_bk_data['data']['booked']) {
                    foreach ($value_bk_data['data']['booked'] as $booked_k1 => $booked_v1) {
                        $cust_obj = new Customer($booked_v1['detail'][0]['id_customer']);
                        if ($cust_obj->firstname) {
                            $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][0]['alloted_cust_name'] = $cust_obj->firstname.' '.$cust_obj->lastname;
                        } else {
                            $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][0]['alloted_cust_name'] = "No customer name found";
                        }
                        if ($cust_obj->email) {
                            $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][0]['alloted_cust_email'] = $cust_obj->email;
                        } else {
                            $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][0]['alloted_cust_email'] = "No customer email found";
                        }
                        $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][0]['avail_rooms_to_realloc'] = $obj_booking_dtl->getAvailableRoomsForReallocation($booked_v1['detail'][0]['date_from'], $booked_v1['detail'][0]['date_to'], $booked_v1['id_product'], $booked_v1['id_hotel']);
                        $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][0]['avail_rooms_to_swap'] = $obj_booking_dtl->getAvailableRoomsForSwapping($booked_v1['detail'][0]['date_from'], $booked_v1['detail'][0]['date_to'], $booked_v1['id_product'], $booked_v1['id_hotel'], $booked_v1['id_room']);
                    }
                }
            }
        }
        return $booking_data;
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS(array(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css'));
        $this->addJs(_PS_MODULE_DIR_.$this->module->name.'/views/js/HotelReservationAdmin.js');
    }
}
