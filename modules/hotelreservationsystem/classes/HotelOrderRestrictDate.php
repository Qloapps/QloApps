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

class HotelOrderRestrictDate extends ObjectModel
{
    public $id;
    public $id_hotel;
    public $use_global_max_order_date;
    public $max_order_date;
    public $use_global_preparation_time;
    public $preparation_time;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_order_restrict_date',
        'primary' => 'id',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'use_global_max_order_date' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'max_order_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'use_global_preparation_time' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'preparation_time' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function getDataByHotelId($idHotel)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'htl_order_restrict_date` ord WHERE ord.`id_hotel` = '.(int) $idHotel
        );
    }

    /*Max date of ordering for order restrict*/
    public static function getMaxOrderDate($idHotel)
    {
        $result = self::getDataByHotelId($idHotel);
        if (is_array($result) && count($result) && !$result['use_global_max_order_date']) {
            return $result['max_order_date'];
        }

        if ($globalBookingDate = Configuration::get('MAX_GLOBAL_BOOKING_DATE')) {
            return $globalBookingDate;
        }

        return 0;
    }

    /**
     * @param int $id_hotel
     * @return int|false
     */
    public static function getPreparationTime($idHotel)
    {
        $result = self::getDataByHotelId($idHotel);
        if (is_array($result) && count($result) && !$result['use_global_preparation_time']) {
            return (int) $result['preparation_time'];
        }

        $globalPreparationTime = Configuration::get('GLOBAL_PREPARATION_TIME');
        if ($globalPreparationTime != '0') {
            return (int) $globalPreparationTime;
        }

        return false;
    }

    public static function validateOrderRestrictDateOnPayment(&$controller)
    {
        $error = false;
        $moduleInstance = Module::getInstanceByName('hotelreservationsystem');
        $context = Context::getContext();
        if ($cartProducts = $context->cart->getProducts()) {
            foreach ($cartProducts as $product) {
                if ($product['active']) {
                    $objCartBookingData = new HotelCartBookingData();
                    $obj_htl_bk_dtl = new HotelBookingDetail();
                    $obj_rm_type = new HotelRoomType();

                    if ($cart_bk_data = $objCartBookingData->getOnlyCartBookingData(
                        $context->cart->id,
                        $context->cart->id_guest,
                        $product['id_product']
                    )) {
                        $cart_data = array();
                        foreach ($cart_bk_data as $data) {
                            $objCartBookingData = new HotelCartBookingData($data['id']);
                            if ($maxOrderDate = HotelOrderRestrictDate::getMaxOrderDate($objCartBookingData->id_hotel)) {
                                if (strtotime('-1 day', strtotime($maxOrderDate)) < strtotime($objCartBookingData->date_from)
                                    || strtotime($maxOrderDate) < strtotime($objCartBookingData->date_to)
                                ) {
                                    $objBranchInfo = new HotelBranchInformation(
                                        $objCartBookingData->id_hotel,
                                        $context->language->id
                                    );
                                    $controller->errors[] = sprintf(
                                        'You can not book rooms for hotel \'%s\' after date %s. Please remove such rooms to proceed.',
                                        $objBranchInfo->hotel_name,
                                        Tools::displayDate($maxOrderDate)
                                    );
                                    $error = true;
                                }
                            }
                            $preparationTime = HotelOrderRestrictDate::getPreparationTime($objCartBookingData->id_hotel);
                            if ($preparationTime !== false) {
                                $minOrderDate = date('Y-m-d', strtotime('+'. ($preparationTime) .' days'));
                                if (strtotime($minOrderDate) > strtotime($objCartBookingData->date_from)
                                    || strtotime($minOrderDate . ' +1 day')> strtotime($objCartBookingData->date_to)
                                ) {
                                    $objBranchInfo = new HotelBranchInformation(
                                        $objCartBookingData->id_hotel,
                                        $context->language->id
                                    );
                                    $controller->errors[] = sprintf(
                                        'You can not book rooms for hotel \'%s\' before date %s. Please remove such rooms to proceed.',
                                        $objBranchInfo->hotel_name,
                                        Tools::displayDate($minOrderDate)
                                    );
                                    $error = true;
                                }
                            }
                            $date_join = strtotime($data['date_from']).strtotime($data['date_to']);
                            $cart_data[$date_join]['date_from'] = $data['date_from'];
                            $cart_data[$date_join]['date_to'] = $data['date_to'];
                            $cart_data[$date_join]['id_hotel'] = $data['id_hotel'];
                            $cart_data[$date_join]['id_rms'][] = $data['id_room'];
                        }
                        foreach ($cart_data as $cl_key => $cl_val) {
                            $avai_rm = $obj_htl_bk_dtl->DataForFrontSearch($cl_val['date_from'], $cl_val['date_to'], $cl_val['id_hotel'], $product['id_product'], 1);
                            $isRmBooked = 0;
                            if (count($avai_rm['rm_data'][0]['data']['available']) < count($cl_val['id_rms'])) {
                                foreach ($cl_val['id_rms'] as $cr_key => $cr_val) {
                                    if($isRmBooked = $obj_htl_bk_dtl->chechRoomBooked($cr_val, $cl_val['date_from'], $cl_val['date_to'])){
                                        break;
                                    }
                                }
                                if ($isRmBooked) {
                                    $controller->errors[] = sprintf($moduleInstance->l('The Room \'%s\' has been booked by another customer from \'%s\' to \'%s\' Please remove rooms from cart to proceed', 'HotelOrderRestrictDate'), $product['name'], date('d-m-Y', strtotime($cl_val['date_from'])), date('d-m-Y', strtotime($cl_val['date_to'])));
                                    $error = true;
                                } else {
                                    $controller->errors[] = sprintf($moduleInstance->l('The Room \'%s\' is no longer avalable from \'%s\' to \'%s\' Please remove rooms from cart to proceed', 'HotelOrderRestrictDate'), $product['name'], date('d-m-Y', strtotime($cl_val['date_from'])), date('d-m-Y', strtotime($cl_val['date_to'])));
                                    $error = true;
                                }
                            }
                        }
                    }
                } else {
                    $error = true;
                    $controller->errors[] = $moduleInstance->l('You can not book rooms from "', 'HotelOrderRestrictDate'). $product['name'] .$moduleInstance->l('". Please remove rooms from "', 'HotelOrderRestrictDate'). $product['name'] . $moduleInstance->l('" from cart to proceed.', 'HotelOrderRestrictDate');
                }
            }
        }
        return $error;
    }
}
