<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
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

    public function postProcess()
    {
        if (Tools::getValue('date_from')) {
            $date_from = Tools::getValue('date_from');
        } else {
            $date_from = date('Y-m-d');
        }
        if (Tools::getValue('date_to')) {
            $date_to = Tools::getValue('date_to');
        } else {
            $date_to = date('Y-m-d');
            if (strtotime($date_from) >= strtotime($date_to)) {
                $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
            }
        }

        if (Tools::getValue('id_hotel')) {
            $id_hotel = Tools::getValue('id_hotel');
        } else {
            $obj_htl_info = new HotelBranchInformation();
            if ($htl_info = $obj_htl_info->hotelBranchesInfo(false, 1)) {
                // filter hotels as per accessed hotels
                $htl_info = HotelBranchInformation::filterDataByHotelAccess(
                    $htl_info,
                    $this->context->employee->id_profile,
                    1
                );
                $id_hotel = reset($htl_info)['id'];
            }
        }

        if (Tools::getValue('id_room_type')) {
            $id_room_type = Tools::getValue('id_room_type');
        } else {
            $id_room_type = 0;
        }

        if (Tools::getValue('occupancy')) {
            $occupancy = Tools::getValue('occupancy');
        } else {
            $occupancy = array();
        }

        if (Tools::isSubmit('search_hotel_list')) {
            $urlData = array (
                'date_from' => $date_from,
                'date_to' => $date_to,
                'id_hotel' => $id_hotel,
                'id_room_type' => $id_room_type,
                'occupancy' => $occupancy
            );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHotelRoomsBooking').'&'.http_build_query($urlData));
        }

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
                $this->errors[] = $this->l('Please select aroom to swap with thisroom.');
            }
            if ($current_room_id == 0) {
                $this->errors[] = $this->l('Cuurentroom is missing.');
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

        $this->id_cart = $this->context->cart->id;
        $this->id_guest = $this->context->cookie->id_guest;
        $this->id_hotel = $id_hotel;
        $this->id_room_type = $id_room_type;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->occupancy = $occupancy;

        parent::postprocess();
    }

    public function initContent()
    {
        // $this->show_toolbar = false;
        $this->toolbar_title = $this->l('Book Now');
        $this->display = 'view';

        parent::initContent();
        // $this->content = $this->renderView();
        // $this->context->smarty->assign('content', $this->content);
    }

    public function initSearchFormData()
    {
        $obj_htl_info = new HotelBranchInformation();
        $obj_rm_type = new HotelRoomType();
        $objHotelCartBookingData = new HotelCartBookingData();


        $hotel_list = $obj_htl_info->hotelBranchesInfo(false, 1);
        // filter hotels as per accessed hotels
        $hotel_list = HotelBranchInformation::filterDataByHotelAccess(
            $hotel_list,
            $this->context->employee->id_profile,
            1
        );
        $all_room_type = $obj_rm_type->getRoomTypeByHotelId($this->id_hotel, Configuration::get('PS_LANG_DEFAULT'), 1);

        $this->tpl_view_vars = array_merge($this->tpl_view_vars, array(
            'hotel_list' => $hotel_list,
            'all_room_type' => $all_room_type,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'id_hotel' => $this->id_hotel,
            'occupancy' => $this->occupancy,
            'id_room_type' => $this->id_room_type,
        ));
    }

    public function renderView()
    {
        $this->tpl_view_vars = array(
            'id_cart' => $this->context->cart->id,
            'id_guest' => $this->context->cookie->id_guest,
        );
        $this->initSearchFormData();
        $this->assignRoomBookingForm();
        $this->initCartData();

        return parent::renderView();
    }

    public function assignRoomBookingForm()
    {
        $objHotelBookingDetail = new HotelBookingDetail();

        $adult = 0;
        $children = 0;
        $num_rooms = 1;
        $booking_data = $this->getAllBookingDataInfo(
            $this->date_from,
            $this->date_to,
            $this->id_hotel,
            $this->id_room_type,
            $this->occupancy,
            $num_rooms,
            $this->context->cart->id,
            $this->context->cookie->id_guest
        );

        $allotmentTypes = HotelBookingDetail::getAllAllotmentTypes();

        $this->context->smarty->assign(array(
            'adult' => $adult,
            'children' => $children,
            'num_rooms' => $num_rooms,
            'booking_data' => $booking_data,
            'allotment_types' => $allotmentTypes,
            'occupancy' => $this->occupancy,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'max_child_age' => Configuration::get('WK_GLOBAL_CHILD_MAX_AGE'),
            'max_child_in_room' => Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM'),
        ));

        $this->context->smarty->assign('occupancy_required_for_booking', Configuration::get('PS_BACKOFFICE_OCCUPANCY_REQUIRED_FOR_BOOKING'));

        if (Configuration::get('PS_BACKOFFICE_SEARCH_TYPE') == HotelBookingDetail::SEARCH_TYPE_OWS ) {
            $this->context->smarty->assign(array(
                'occupancy_adults' => array_sum(array_column($occupancy, 'adult')),
                'occupancy_children' => array_sum(array_column($occupancy, 'children')),
                'occupancy_child_ages' => array_sum(array_column($occupancy, 'child_ages')),
                'occupancy_wise_search' => true,
            ));
        } else {
            $this->context->smarty->assign('occupancy_wise_search', false);
        }
    }

    public function initCartData()
    {
        $objHotelCartBookingData = new HotelCartBookingData();

        $cart_bdata = $objHotelCartBookingData->getCartBookingDetailsByIdCartIdGuest(
            $this->context->cart->id,
            $this->context->cookie->id_guest,
            $this->context->employee->id_lang
        );
        $this->context->smarty->assign(array(
            'id_cart' => $this->context->cart->id,
            'id_guest' => $this->context->cookie->id_guest,
            'rms_in_cart' => $objHotelCartBookingData->getCountRoomsInCart($this->id_cart, $this->id_guest)
        ));
        if ($cart_bdata) {
            $cart_tamount = $this->context->cart->getOrderTotal();
            $this->context->smarty->assign(
                array(
                    'cart_bdata' => $cart_bdata,
                    'cart_tamount' => $cart_tamount
                )
            );
        }
    }


    public function ajaxProcessUpdateCartData()
    {
        $response = array(
            'rms_in_cart' => 0
        );

        $tplVars = $this->initCartData();
        $tplVars['link'] = $this->context->link;
        $this->context->smarty->assign($tplVars);
        if ($room_tpl = $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/hotel_rooms_booking/helpers/view/_partials/booking-cart.tpl'
        )) {
            $response['cart_content'] = $room_tpl;
        }
        $objHotelCartBookingData = new HotelCartBookingData();
        $response['rms_in_cart'] = $objHotelCartBookingData->getCountRoomsInCart($this->id_cart, $this->id_guest);

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessGetBookingStats()
    {
        $response = array(
            'success' => false,
            'data' => array()
        );

        $searchIdHotel = Tools::getValue('search_id_hotel');
        $searchIdRoomType = Tools::getValue('search_id_room_type');
        $searchDateFrom = Tools::getValue('search_date_from');
        $searchDateTo = Tools::getValue('search_date_to');

        // No use of adult, child, num_rooms
        $adult = 0;
        $children = 0;
        $num_rooms = 1;

        $bookingParams = array();
        $bookingParams['date_from'] = $searchDateFrom;
        $bookingParams['date_to'] = $searchDateTo;
        $bookingParams['hotel_id'] = $searchIdHotel;
        $bookingParams['room_type'] = $searchIdRoomType;
        $bookingParams['adult'] = $adult;
        $bookingParams['children'] = $children;
        $bookingParams['num_rooms'] = $num_rooms;
        $bookingParams['for_calendar'] = 1;
        $bookingParams['search_available'] = 1;
        $bookingParams['search_partial'] = 1;
        $bookingParams['search_booked'] = 1;
        $bookingParams['search_unavai'] = 1;
        $bookingParams['id_cart'] = $this->id_cart;
        $bookingParams['id_guest'] = $this->id_guest;
        $bookingParams['search_cart_rms'] = 1;

        $objBookingDetail = new HotelBookingDetail();
        if ($bookingData = $objBookingDetail->getBookingData($bookingParams)) {
            $this->context->smarty->assign(array('booking_data' => $bookingData));
            $tpl_path = 'hotelreservationsystem/views/templates/admin/hotel_rooms_booking/helpers/view/_partials/search-stats.tpl';
            $searchStats = $this->context->smarty->fetch(_PS_MODULE_DIR_.$tpl_path);
            $response['success'] = true;
            $response['data']['stats_panel'] = $searchStats;
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessGetCalenderData()
    {
        $events = array();
        // No use of adult, child, num_rooms
        $adult = 0;
        $children = 0;
        $num_rooms = 1;

        $start_date = date('Y-m-d', strtotime(Tools::getValue('start')));
        $last_day_this_month  = date('Y-m-d', strtotime(Tools::getValue('end')));
        $searchIdHotel = Tools::getValue('search_id_hotel');
        $searchIdRoomType = Tools::getValue('search_id_room_type');
        $searchDateFrom = Tools::getValue('search_date_from');
        $searchDateTo = Tools::getValue('search_date_to');

        $bookingParams = array();
        $bookingParams['hotel_id'] = $searchIdHotel;
        $bookingParams['room_type'] = $searchIdRoomType;
        $bookingParams['adult'] = $adult;
        $bookingParams['children'] = $children;
        $bookingParams['num_rooms'] = $num_rooms;
        $bookingParams['for_calendar'] = 1;
        $bookingParams['search_available'] = 1;
        $bookingParams['search_partial'] = 1;
        $bookingParams['search_booked'] = 1;
        $bookingParams['search_unavai'] = 1;
        $bookingParams['id_cart'] = $this->id_cart;
        $bookingParams['id_guest'] = $this->id_guest;
        $bookingParams['search_cart_rms'] = 1;

        $objBookingDetail = new HotelBookingDetail();

        while ($start_date <= $last_day_this_month) {
            $cal_date_from = $start_date;
            $cal_date_to = date('Y-m-d', strtotime('+1 day', strtotime($cal_date_from)));
            $bookingParams['date_from'] = $cal_date_from;
            $bookingParams['date_to'] = $cal_date_to;
            $eventData = $objBookingDetail->getBookingData($bookingParams);
            $eventData['date_format'] = Tools::displayDate($cal_date_from);
            $events[strtotime($bookingParams['date_from'])] = array(
                'is_notification' => 1,
                'title' => $this->l('icon'),
                'start' => date('Y-m-d H:i:s', strtotime($bookingParams['date_from'])),
                'data' => $eventData
            );

            $start_date = date('Y-m-d', strtotime('+1 day', strtotime($start_date)));
        }
        $bookingParams['date_from'] = $searchDateFrom;
        $bookingParams['date_to'] = $searchDateTo;
        if ($bookingData = $objBookingDetail->getBookingData($bookingParams)) {
            if ($bookingData['stats']['num_avail']) {
                $eventColor = '#7EC77B';
                $title = sprintf($this->l('Available Rooms : %s'), $bookingData['stats']['num_avail']);
            } elseif ($bookingData['stats']['num_part_avai']) {
                $eventColor = '#FFC224';
                $title = sprintf($this->l('Partially Available Rooms : %s'), $bookingData['stats']['num_part_avai']);
            } else {
                $eventColor = '#FF3838';
                $title = sprintf($this->l('Available Rooms : %s'), $bookingData['stats']['num_avail']);
            }
            $bookingData['date_from_format'] = Tools::displayDate($searchDateFrom);
            $bookingData['date_to_format'] = Tools::displayDate($searchDateTo);
            $events[] = array(
                'title' => $title,
                'start' => date('Y-m-d', strtotime($searchDateFrom)),
                'end' => date('Y-m-d', strtotime($searchDateTo)),
                'backgroundColor' => $eventColor,
                'color' => $eventColor,
                'textColor' => '#FFF',
                'data' => $bookingData
            );
        }


        $events = array_values($events);
        $this->ajaxDie(Tools::jsonEncode($events));
    }

    public function ajaxProcessGetRoomType()
    {
        $hotel_id  = Tools::getValue('hotel_id');
        $obj_room_type = new HotelRoomType();
        $room_type_info = $obj_room_type->getRoomTypeByHotelId($hotel_id, Configuration::get('PS_LANG_DEFAULT'), 1);
        die(json_encode($room_type_info));
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
        $occupancy = Tools::getValue('occupancy');

        $date_from = date("Y-m-d", strtotime($date_from));
        $date_to = date("Y-m-d", strtotime($date_to));

        $search_id_room_type = Tools::getValue('search_id_room_type');
        $search_id_hotel = Tools::getValue('search_id_hotel');
        $search_date_from = Tools::getValue('search_date_from');
        $search_date_to = Tools::getValue('search_date_to');

        // for delete quantity
        $id_cart = Tools::getValue('id_cart');
        $id_cart_book_data = Tools::getValue('id_cart_book_data');
        $ajax_delete = Tools::getValue('ajax_delete'); // If delete from cart(not for room list delete(pagebottom tabs))

        $opt = Tools::getValue('opt'); // if 1 then add quantity or if 0 means delete quantity

        $obj_booking_dtl = new HotelBookingDetail();
        $num_day = $obj_booking_dtl->getNumberOfDays($date_from, $date_to); //quantity of product

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

        $response = array(
            'success' => false,
            'data' => array()
        );
        if ($opt) {
            // add room in cart

            $obj_cart_book_data = new HotelCartBookingData();
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
            if (Configuration::get('PS_BACKOFFICE_OCCUPANCY_REQUIRED_FOR_BOOKING')) {
                $room_occupancy = array_shift($occupancy);
                $obj_cart_book_data->adult = $room_occupancy['adult'];
                $obj_cart_book_data->children = $room_occupancy['children'];
                $obj_cart_book_data->child_ages = $room_occupancy['children'] ? json_encode($room_occupancy['child_ages']) : json_encode(array());
            } else {
                $obj_cart_book_data->adult = $roomTypeInfo['adult'];
                $obj_cart_book_data->children = $roomTypeInfo['children'];
                $obj_cart_book_data->child_ages = json_encode(array());
            }
            if ($obj_cart_book_data->save()) {
                $response['success'] = true;
                $response['data']['id_cart_book_data'] = $obj_cart_book_data->id;
            }
        } else {
            // remove room from cart
            $obj_cart_book_data = new HotelCartBookingData();
            $data_dlt = $obj_cart_book_data->deleteRowById($id_cart_book_data);
            if ($data_dlt) {
                $response['success'] = true;
                if ($ajax_delete) {
                    // deleting from cart modal
                    $this->date_from = $search_date_from;
                    $this->date_to = $search_date_to;
                    $this->id_hotel = $search_id_hotel;
                    $this->id_room_type = $search_id_room_type;

                    $this->assignRoomBookingForm();
                    $tpl_path = 'hotelreservationsystem/views/templates/admin/hotel_rooms_booking/helpers/view/_partials/booking-rooms.tpl';
                    $room_tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_.$tpl_path);

                    $response['data'] = array(
                        'room_tpl' => $room_tpl,
                    );
                }
            }
        }
        $this->ajaxDie(json_encode($response));
    }

    public function getAllBookingDataInfo(
        $date_from,
        $date_to,
        $hotel_id,
        $id_room_type,
        $occupancy,
        $num_rooms,
        $id_cart,
        $id_guest
    ) {
        $obj_booking_dtl = new HotelBookingDetail();
        $obj_cart_book_data = new HotelCartBookingData();

        $bookingParams = array();
        $bookingParams['date_from'] = $date_from;
        $bookingParams['date_to'] = $date_to;
        $bookingParams['hotel_id'] = $hotel_id;
        $bookingParams['id_room_type'] = $id_room_type;
        $bookingParams['occupancy'] = $occupancy;
        $bookingParams['num_rooms'] = $num_rooms;
        $bookingParams['search_available'] = 1;
        $bookingParams['search_partial'] = 1;
        $bookingParams['search_booked'] = 1;
        $bookingParams['search_unavai'] = 1;
        $bookingParams['id_cart'] = $id_cart;
        $bookingParams['id_guest'] = $id_guest;
        $bookingParams['search_cart_rms'] = 1;

        $booking_data = $obj_booking_dtl->getBookingData($bookingParams);

        if ($booking_data) {
            $objHotelRoomType = new HotelRoomType();
            foreach ($booking_data['rm_data'] as $key_bk_data => $value_bk_data) {
                $booking_data['rm_data'][$key_bk_data]['room_type_info'] = $objHotelRoomType->getRoomTypeInfoByIdProduct($value_bk_data['id_product']);
                if (isset($value_bk_data['data']['booked']) && $value_bk_data['data']['booked']) {
                    foreach ($value_bk_data['data']['booked'] as $booked_k1 => $booked_v1) {
                        if (isset($booked_v1['detail']) && $booked_v1['detail']) {
                            foreach ($booked_v1['detail'] as $kDtl => $bookedDtls) {
                                $cust_obj = new Customer($booked_v1['detail'][$kDtl]['id_customer']);
                                if ($cust_obj->firstname) {
                                    $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][$kDtl]['alloted_cust_name'] = $cust_obj->firstname.' '.$cust_obj->lastname;
                                } else {
                                    $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][$kDtl]['alloted_cust_name'] = "No customer name found";
                                }
                                if ($cust_obj->email) {
                                    $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][$kDtl]['alloted_cust_email'] = $cust_obj->email;
                                } else {
                                    $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][$kDtl]['alloted_cust_email'] = "No customer email found";
                                }
                                $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][$kDtl]['avail_rooms_to_realloc'] = $obj_booking_dtl->getAvailableRoomsForReallocation($booked_v1['detail'][$kDtl]['date_from'], $booked_v1['detail'][$kDtl]['date_to'], $booked_v1['id_product'], $booked_v1['id_hotel']);
                                $booking_data['rm_data'][$key_bk_data]['data']['booked'][$booked_k1]['detail'][$kDtl]['avail_rooms_to_swap'] = $obj_booking_dtl->getAvailableRoomsForSwapping($booked_v1['detail'][$kDtl]['date_from'], $booked_v1['detail'][$kDtl]['date_to'], $booked_v1['id_product'], $booked_v1['id_hotel'], $booked_v1['id_room']);
                            }
                        }
                    }
                }
            }
        }
        return $booking_data;
    }

    public function setMedia()
    {
        parent::setMedia();
        $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        $jsVars = array(
            'currency_prefix' => $currency->prefix,
            'currency_suffix' => $currency->suffix,
            'currency_sign' => $currency->sign,
            'currency_format' => $currency->format,
            'currency_blank' => $currency->blank,
            'ALLOTMENT_AUTO' => HotelBookingDetail::ALLOTMENT_AUTO,
            'ALLOTMENT_MANUAL' => HotelBookingDetail::ALLOTMENT_MANUAL,
            'max_child_age' => $max_child_age,
            'max_child_in_room' => $max_child_in_room,
            'occupancy_wise_search' => $occupancy_wise_search,
            'occupancy_required_for_booking' => $occupancy_required_for_booking,
            'rooms_booking_url' => $this->context->link->getAdminLink('AdminHotelRoomsBooking'),
            'opt_select_all' => $this->l('All Types'),
            'slt_another_htl' => $this->l('Select Another Hotel'),
            'product_type_cond' => $this->l('Product type is required'),
            'from_date_cond' => $this->l('From date is required'),
            'to_date_cond' => $this->l('To date is required'),
            'hotel_name_cond' => $this->l('Hotel Name is required'),
            'num_rooms_cond' => $this->l('Number of Rooms is required'),
            'add_to_cart' => $this->l('Add To Cart'),
            'remove' => $this->l('Remove'),
            'noRoomTypeAvlTxt' => $this->l('No room type available.'),
            'no_rm_avail_txt' => $this->l('No rooms available.'),
            'slct_rm_err' => $this->l('Please select a room first.'),
            'product_added_cart_txt' => $this->l('Product added in cart'),
            'info_icon_path' => _MODULE_DIR_.$this->module->name.'/views/img/Slices/info-icon.svg',
            'select_age_txt' => 'Select age',
            'under_1_age' => 'Under 1',
            'room_txt' => 'Room',
            'rooms_txt' => 'Rooms',
            'remove_txt' => 'Remove',
            'adult_txt' => 'Adult',
            'adults_txt' => 'Adults',
            'child_txt' => 'Child',
            'children_txt' => 'Children',
            'below_txt' => 'Below',
            'years_txt' => 'years',
            'all_children_txt' => 'years',
            'invalid_occupancy_txt' => 'Invalid occupancy(adults/children) found.',
            // 'check_calender_var' => $check_calender_var,
        );
        MediaCore::addJsDef($jsVars);

        // add fullcalender plugin
        $this->addJqueryUI('ui.tooltip', 'base', true);
        $this->removeJS(Media::getJqueryUIPath('effects.core', 'base', false), false);

        $this->addCSS(_PS_JS_DIR_.'fullcalendar/main.css');
        $this->addJs(_PS_JS_DIR_.'fullcalendar/main.js');

        $this->addCSS(array(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css'));
        $this->addJs(_MODULE_DIR_.$this->module->name.'/views/js/HotelReservationAdmin.js');
        $this->addJs(_MODULE_DIR_.$this->module->name.'/views/js/admin/hotel_rooms_booking.js');
    }
}
