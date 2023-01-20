<?php

class BlockcartCheckRoomAvailabilityAjaxProcessModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;

        $this->context = Context::getContext();
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');
        $idProduct = Tools::getValue('id_product');
        $idCart = $this->context->cart->id;
        $idGuest = $this->context->cart->id_guest;

        if (Module::isInstalled('hotelreservationsystem')) {
            require_once (_PS_MODULE_DIR_.'hotelreservationsystem/define.php');
            if (Tools::getValue('delete_room_form_cart')) {
                $objBookingDetail = new HotelBookingDetail();
                $numDays = $objBookingDetail->getNumberOfDays($dateFrom, $dateTo);
                $objCartBooking = new HotelCartBookingData();
                $objRoomType = new HotelRoomType();
                $totalAvailRooms = 0;
                if ($objCartBooking->deleteCartBookingData(
                    $idCart,
                    $idProduct,
                    0,
                    $dateFrom,
                    $dateTo
                )) {
                    if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($idProduct)) {
                        if ($idHotel = $roomTypeInfo['id_hotel']) {
                            $bookingParams = array(
                                'date_from' => $dateFrom,
                                'date_to' => $dateTo,
                                'hotel_id' => $idHotel,
                                'id_room_type' => $idProduct,
                                'only_search_data' => 1,
                                'id_cart' => $idCart,
                                'id_guest' => $idGuest,
                            );
                            if ($hotelRoomData = $objBookingDetail->dataForFrontSearch($bookingParams)) {
                                $totalAvailRooms = $hotelRoomData['stats']['num_avail'];
                            }
                        }
                    }
                    die(
                        json_encode(
                            array(
                                'status'=>'success',
                                'msg'=>'successfully cart product updated.',
                                'avail_rooms'=>$totalAvailRooms
                            )
                        )
                    );
                }
                die(
                    json_encode(
                        array('status'=>'failed', 'msg'=>'error while deleting room from cart booking table.')
                    )
                );
            }
        }
    }
}