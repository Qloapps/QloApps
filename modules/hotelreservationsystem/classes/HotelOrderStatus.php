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

class HotelOrderStatus extends ObjectModel
{
    public $id;
    public $status;

    public static $definition = array(
        'table' => 'htl_order_status',
        'primary' => 'id',
        'fields' => array(
            'status' =>    array('type' => self::TYPE_STRING),
        ),
    );

    /**
     * [getAllHotelOrderStatus :: To get array all possible order statuses]
     * @return [array|boolean] [If data found then Returns array of the order statuses else returns false]
     */
    public static function getAllHotelOrderStatus()
    {
        $result = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."htl_order_status`");
        if ($result) {
            return $result;
        }
        return false;
    }
}
