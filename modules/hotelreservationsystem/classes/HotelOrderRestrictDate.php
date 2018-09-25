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

        $max_order_date_restr = $order_restrict_data['max_order_date'];

        if (isset($max_order_date_restr) && $max_order_date_restr) {
            $max_order_date = $max_order_date_restr;
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
                $objCartBookingData = new HotelCartBookingData();
                if ($cart_bk_data = $objCartBookingData->getOnlyCartBookingData(
                    $context->cart->id,
                    $context->cart->id_guest,
                    $product['id_product']
                )) {
                    foreach ($cart_bk_data as $data) {
                        $objCartBookingData = new HotelCartBookingData($data['id']);
                        if ($maxOrderDate = HotelOrderRestrictDate::getMaxOrderDate($objCartBookingData->id_hotel)) {
                            if (strtotime($maxOrderDate) < strtotime($objCartBookingData->date_from)
                                || strtotime($maxOrderDate) < strtotime($objCartBookingData->date_to)
                            ) {
                                $objBranchInfo = new HotelBranchInformation(
                                    $objCartBookingData->id_hotel,
                                    $context->language->id
                                );
                                $controller->errors[] = $moduleInstance->l('You can\'t Book room after date').' \''.
                                date('d-m-Y', strtotime($maxOrderDate)).'\' '.$moduleInstance->l('For').'\''.
                                $objBranchInfo->hotel_name.'\'. '.$moduleInstance->l('Please remove rooms from cart from').
                                ' \''.$objBranchInfo->hotel_name.'\' '.$moduleInstance->l('after date').' \''.
                                date('d-m-Y', strtotime($maxOrderDate)).'\' '.$moduleInstance->l('to proceed.');
                                $error = true;
                            }
                        }
                    }
                }
            }
        }
        return $error;
    }
}
