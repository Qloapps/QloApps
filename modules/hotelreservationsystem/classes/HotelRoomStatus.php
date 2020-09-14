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

class HotelRoomStatus extends ObjectModel
{
    public $id;
    public $status;

    public static $definition = array(
        'table' => 'htl_room_status',
        'primary' => 'id',
        'fields' => array(
            'status' =>    array('type' => self::TYPE_STRING),
        ),
    );

    /**
     * [getAllRoomStatus :: To get all possible Room statuses]
     * @return [array|boolean] [if data found Returns array containing all possible statuses of the rooms else returns false]
     */
    public function getAllRoomStatus()
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."htl_room_status`";
        $rm_status = Db::getInstance()->executeS($sql);

        if ($rm_status) {
            return $rm_status;
        } else {
            return false;
        }
    }
}
