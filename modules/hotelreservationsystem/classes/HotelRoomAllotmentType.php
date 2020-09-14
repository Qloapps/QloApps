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

class HotelRoomAllotmentType extends ObjectModel
{
    public $id;
    public $type;

    public static $definition = array(
        'table' => 'htl_room_allotment_type',
        'primary' => 'id',
        'fields' => array(
            'type' =>    array('type' => self::TYPE_STRING),
        ),
    );
}
