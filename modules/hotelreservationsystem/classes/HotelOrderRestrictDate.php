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
    public $max_order_date;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_order_restrict_date',
        'primary' => 'id',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'max_order_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function getDataByHotelId($id_hotel)
    {
        $result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_order_restrict_date` WHERE `id_hotel`='.$id_hotel);
        if ($result) {
            return $result;
        }

        return false;
    }

    /*Max date of ordering for order restrict*/
    public static function getMaxOrderDate($id_hotel)
    {
        $global_max_order_date = Configuration::get('MAX_GLOBAL_BOOKING_DATE');
        $obj_ord_rest = new self();
        $order_restrict_data = $obj_ord_rest->getDataByHotelId($id_hotel);

        if (isset($order_restrict_data['max_order_date']) && $order_restrict_data['max_order_date']) {
            $max_order_date = $order_restrict_data['max_order_date'];
        } elseif (isset($global_max_order_date) && $global_max_order_date) {
            $max_order_date = $global_max_order_date;
        } else {
            $max_order_date = 0;
        }

        return $max_order_date;
    }

    public function getUnsavedHotelsForOrderRestrict()
    {
        $idLang = Context::getContext()->language->id;
        $sql = 'SELECT hbi.`id`, hbl.`hotel_name` FROM `'._DB_PREFIX_.'htl_branch_info` hbi
            LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbl
            ON (hbl.`id` = hbi.`id` AND hbl.`id_lang` = '.(int)$idLang.')
            WHERE hbi.`id` NOT IN (SELECT DISTINCT id_hotel FROM `'._DB_PREFIX_.'htl_order_restrict_date`)';

        return Db::getInstance()->executeS($sql);
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
                                $controller->errors[] = $moduleInstance->l('You can not Book room after date', 'HotelOrderRestrictDate').' \''.date('d-m-Y', strtotime($maxOrderDate)).'\' '.$moduleInstance->l('For hotel', 'HotelOrderRestrictDate').' \''.$objBranchInfo->hotel_name.'\'. '.$moduleInstance->l('Please remove rooms from cart from', 'HotelOrderRestrictDate').' \''.$objBranchInfo->hotel_name.'\' '.$moduleInstance->l('after date', 'HotelOrderRestrictDate').' \''.date('d-m-Y', strtotime($maxOrderDate)).'\' '.$moduleInstance->l('to proceed.', 'HotelOrderRestrictDate');
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
