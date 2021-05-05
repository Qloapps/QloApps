<?php
    include_once '../../config/config.inc.php';
    include_once(_PS_MODULE_DIR_.'hotelreservationsystem/define.php');

    $add = Tools::getValue('add');
    $delete = Tools::getValue('delete');
    $delete_room_form_cart = Tools::getValue('delete_room_form_cart');
    $date_from = Tools::getValue('date_from');
    $date_to = Tools::getValue('date_to');
    $id_cart = Tools::getValue('id_cart');
    $id_customer = Tools::getValue('id_customer');
    $id_guest = Tools::getValue('id_guest');
    $id_product = Tools::getValue('id_product');


    if (isset($add) && $add) {
        $quantity = Tools::getValue('qty');

        $obj_room_type = new HotelRoomType();
        $room_info_by_id_product = $obj_room_type->getRoomTypeInfoByIdProduct($id_product);

        if ($room_info_by_id_product) {
            $id_hotel = $room_info_by_id_product['id_hotel'];

            if ($id_hotel) {
                $obj_booking_dtl = new HotelBookingDetail();
                $hotel_room_data = $obj_booking_dtl->DataForFrontSearch($date_from, $date_to, $id_hotel, $id_product, 1, 0, 0, -1, 0, 0, $id_cart, $id_guest);
                if ($hotel_room_data) {
                    $obj_htl_cart_booking_data = new HotelCartBookingData();
                    $num_cart_rooms = $obj_htl_cart_booking_data->getCountRoomsByIdCartIdProduct($id_cart, $id_product, $date_from, $date_to);
                    $total_available_rooms = $hotel_room_data['stats']['num_avail'];
                    if (!$num_cart_rooms) {
                        $num_cart_rooms = 0;
                    }

                    $total_available_rooms = $total_available_rooms;

                    if ($total_available_rooms >= $quantity) {
                        $hotel_room_info_arr = $hotel_room_data['rm_data'][0]['data']['available'];
                        if ($hotel_room_info_arr) {
                            $i = 0;
                            foreach ($hotel_room_info_arr as $key => $value) {
                                if ($i<$quantity) {
                                    $obj_htl_cart_booking_data = new HotelCartBookingData();
                                    $obj_htl_cart_booking_data->id_cart = $id_cart;
                                    $obj_htl_cart_booking_data->id_guest = $id_guest;
                                    $obj_htl_cart_booking_data->id_customer = $id_customer;
                                    $obj_htl_cart_booking_data->id_product = $value['id_product'];
                                    $obj_htl_cart_booking_data->id_room = $value['id_room'];
                                    $obj_htl_cart_booking_data->id_hotel = $value['id_hotel'];
                                    $obj_htl_cart_booking_data->booking_type = 1;
                                    $obj_htl_cart_booking_data->date_from = $date_from;
                                    $obj_htl_cart_booking_data->date_to = $date_to;
                                    $obj_htl_cart_booking_data->save();
                                    $i++;
                                } else {
                                    break;
                                }
                            }
                            die(json_encode(array('status'=>'success', 'avail_rooms'=>$total_available_rooms)));
                        } else {
                            die(json_encode(array('status'=>'failed1')));
                        }
                    } else {
                        die(json_encode(array('status'=>'unavailable_quantity', 'avail_rooms'=>$total_available_rooms)));
                    }
                } else {
                    die(json_encode(array('status'=>'failed2')));
                }
            } else {
                die(json_encode(array('status'=>'failed3')));
            }
        } else {
            die(json_encode(array('status'=>'failed4')));
        }
    }

    if (isset($delete) && $delete) {
        $obj_room_type = new HotelRoomType();
        $room_info_by_id_product = $obj_room_type->getRoomTypeInfoByIdProduct($id_product);

        if ($room_info_by_id_product) {
            $id_hotel = $room_info_by_id_product['id_hotel'];

            if ($id_hotel) {
                $obj_booking_dtl = new HotelBookingDetail();
                $hotel_room_data = $obj_booking_dtl->DataForFrontSearch($date_from, $date_to, $id_hotel, $id_product, 1);
                if ($hotel_room_data) {
                    $total_available_rooms = $hotel_room_data['stats']['num_avail'];
                }
            }
        }
        $obj_htl_cart_booking_data = new HotelCartBookingData();
        $result = $obj_htl_cart_booking_data->deleteCartBookingData($id_cart, $id_product);

        if ($result) {
            die(json_encode(array('status'=>'success', 'avail_rooms'=>$total_available_rooms)));
        } else {
            die(json_encode(array('status'=>'failed')));
        }
    }

    if (isset($delete_room_form_cart) && $delete_room_form_cart) {
        $num_rooms_to_decr_from_cart = Tools::getValue('num_rooms');
        $obj_htl_cart_booking_data = new HotelCartBookingData();
        $delete_room_from_cart_booking_data = $obj_htl_cart_booking_data->deleteCartDataByIdProductIdCart($id_cart, $id_product, $date_from, $date_to);

        if ($delete_room_from_cart_booking_data) {
            $cart_obj = new Cart($id_cart);
            $update_cart_product = $cart_obj->updateQty($num_rooms_to_decr_from_cart, $id_product, null, false, $operator = 'down', 0, null, true);
            if ($update_cart_product) {
                die(json_encode(array('status'=>'success', 'msg'=>'successfully cart product updated.')));
            } else {
                die(json_encode(array('status'=>'failed', 'msg'=>'error while updating cart product.')));
            }
        }
        die(json_encode(array('status'=>'failed', 'msg'=>'error while deleting room from cart booking table.')));
    }
