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
                            if ($hotelRoomData = $objBookingDetail->DataForFrontSearch(
                                $dateFrom,
                                $dateTo,
                                $idHotel,
                                $idProduct,
                                1,
                                0,
                                0,
                                -1,
                                0,
                                0,
                                $idCart,
                                $idGuest
                            )) {
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