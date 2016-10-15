<?php

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
        $result = Db::getInstance()->executeS('SELECT `id` , `hotel_name` FROM `'._DB_PREFIX_.'htl_branch_info` WHERE `id` NOT IN (SELECT `id_hotel` FROM `'._DB_PREFIX_.'htl_order_restrict_date`)');
        if ($result) {
            return $result;
        }

        return false;
    }

    public static function validateOrderRestrictDateOnPayment(&$controller)
    {
        $error = false;
        $context = Context::getContext();
        $cart_products = $context->cart->getProducts();
        foreach ($cart_products as $product) {
            $obj_cart_bk_data = new HotelCartBookingData();
            $cart_bk_data = $obj_cart_bk_data->getOnlyCartBookingData($context->cart->id, $context->cart->id_guest, $product['id_product']);
            if ($cart_bk_data) {
                foreach ($cart_bk_data as $data) {
                    $obj_cart_bk_data = new HotelCartBookingData($data['id']);
                    $max_order_date = HotelOrderRestrictDate::getMaxOrderDate($obj_cart_bk_data->id_hotel);
                    if ($max_order_date) {
                        if (strtotime($max_order_date) < strtotime($obj_cart_bk_data->date_from) || strtotime($max_order_date) < strtotime($obj_cart_bk_data->date_to)) {
                            $htl_branch_info = new HotelBranchInformation($obj_cart_bk_data->id_hotel);
                            $controller->errors[] = Tools::displayError('You can\'t Book room after date').' \''.date('d-m-Y', strtotime($max_order_date)).'\' '.Tools::displayError('For').' \''.$htl_branch_info->hotel_name.'\'. '.Tools::displayError('Please remove rooms from cart from').' \''.$htl_branch_info->hotel_name.'\' '.Tools::displayError('after date').' \''.date('d-m-Y', strtotime($max_order_date)).'\' '.Tools::displayError('to proceed.');
                            $error = true;
                        }
                    }
                }
            }
        }
        return $error;
    }
}
