<?php
class BlockcartCheckRoomAvailabilityAjaxProcessModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;

        $this->context = Context::getContext();
        
        $delete_room_form_cart = Tools::getValue('delete_room_form_cart');
        $date_from = Tools::getValue('date_from');
        $date_to = Tools::getValue('date_to');
        $id_product = Tools::getValue('id_product');
        
        $id_cart = $this->context->cart->id;
        $id_guest = $this->context->cart->id_guest;

        if (Module::isInstalled('hotelreservationsystem')) 
        {
            require_once (_PS_MODULE_DIR_.'hotelreservationsystem/define.php');
            if (isset($delete_room_form_cart) && $delete_room_form_cart)
            {
                $obj_booking_dtl = new HotelBookingDetail();
                $num_days = $obj_booking_dtl->getNumberOfDays($date_from, $date_to);
                $num_rooms = Tools::getValue('num_rooms');
                $obj_htl_cart_booking_data = new HotelCartBookingData();

                $obj_room_type = new HotelRoomType();
                $room_info_by_id_product = $obj_room_type->getRoomTypeInfoByIdProduct($id_product);

                if ($room_info_by_id_product)
                {
                    $id_hotel = $room_info_by_id_product['id_hotel'];

                    if ($id_hotel)
                    {
                        $hotel_room_data = $obj_booking_dtl->DataForFrontSearch($date_from, $date_to, $id_hotel, $id_product, 1, 0, 0, -1, 0, 0, $id_cart, $id_guest);

                        $total_available_rooms = $hotel_room_data['stats']['num_avail'];
                    }
                }

                $total_available_rooms += $num_rooms;
                $delete_room_from_cart_booking_data = $obj_htl_cart_booking_data->deleteCartDataByIdProductIdCart($id_cart, $id_product, $date_from, $date_to);
                if ($delete_room_from_cart_booking_data)
                {
                    $num_rooms_to_decr_from_cart = $num_rooms * $num_days;
                    $update_cart_product = $this->context->cart->updateQty($num_rooms_to_decr_from_cart, $id_product, null, false, $operator = 'down', 0, null, true);
                    if ($update_cart_product)
                        die(Tools::jsonEncode(array('status'=>'success', 'msg'=>'successfully cart product updated.','avail_rooms'=>$total_available_rooms)));
                    else
                        die(Tools::jsonEncode(array('status'=>'failed', 'msg'=>'error while updating cart product.')));
                }
                die(Tools::jsonEncode(array('status'=>'failed', 'msg'=>'error while deleting room from cart booking table.')));
            }
        }
    }
}
?>