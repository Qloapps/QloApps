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

class HotelRoomInformation extends ObjectModel
{
    public $id;
    public $id_product;
    public $id_hotel;
    public $room_num;
    public $id_status;
    public $floor;
    public $comment;
    public $date_add;
    public $date_upd;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_TEMPORARY_INACTIVE = 3;

    public static $definition = array(
        'table' => 'htl_room_information',
        'primary' => 'id',
        'fields' => array(
            'id_product' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_hotel' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'room_num' =>    array('type' => self::TYPE_STRING),
            'id_status' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'floor' =>        array('type' => self::TYPE_STRING),
            'comment' =>    array('type' => self::TYPE_STRING),
            'date_add' =>    array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>    array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        ),
    );

    protected $webserviceParameters = array(
        'objectMethods' => array(
            'add' => 'addWs',
            'update' => 'updateWs',
            'delete' => 'deleteWs',
        ),
        'objectsNodeName' => 'rooms',
        'objectNodeName' => 'room',
        'fields' => array(
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'products',
                )
            ),
            'id_hotel' => array(
                'setter' => false,
                'xlink_resource' => array(
                    'resourceName' => 'hotels',
                )
            ),
        ),
    );

    public function update($null_values = false)
    {
        if ($idRoom = $this->id) {
            // delete rooms from cart which are set inactive
            if ($this->id_status == self::STATUS_INACTIVE) {
                $objCartBookingData = new HotelCartBookingData();
                if (!$objCartBookingData->deleteCartBookingData(0, 0, $idRoom)) {
                    return false;
                }
            }
        }
        return parent::update();
    }

    //Overrided ObjectModel::delete()
    public function delete()
    {
        if ($idRoom = $this->id) {
            $objCartBookingData = new HotelCartBookingData();
            if (!$this->deleteRoomDisableDates()
                || !$objCartBookingData->deleteCartBookingData(0, 0, $idRoom)
                || !parent::delete()
            ) {
                return false;
            }
        }
        return true;
    }

    public function deleteRoomDisableDates($idRoom = false)
    {
        if (!$idRoom) {
            $idRoom = $this->id;
            if (!$idRoom) {
                return false;
            }
        }

        $objRoomDisableDates = new HotelRoomDisableDates();
        $objRoomDisableDates->deleteRoomDisableDates((int)$idRoom);

        return true;
    }

    /**
     * [deleteByProductId :: To delete all rooms information which belongs to a room type(which is a product in real) By product id]
     * @param  [int] $id_product [Id of the product form which all rooms information to be deleted]
     * @return [Boolean]         [Returns true if deleted successfully else returns false]
     */
    public function deleteByProductId($idProduct)
    {
        if ($rooms = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_product`='.(int) $idProduct
        )) {
            foreach ($rooms as $room) {
                $objRoomInfo = new HotelRoomInformation($room['id']);
                if (!$objRoomInfo->delete()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getAllRoomStatus()
    {
        $moduleInstance = Module::getInstanceByName('hotelreservationsystem');

        $status = array(
            'STATUS_ACTIVE' => array(
                'id' => self::STATUS_ACTIVE,
                'status' => $moduleInstance->l('Active', 'hotelreservationsystem')
            ),
            'STATUS_INACTIVE' => array(
                'id' => self::STATUS_INACTIVE,
                'status' => $moduleInstance->l('Inactive', 'hotelreservationsystem')
            ),
            'STATUS_TEMPORARY_INACTIVE' => array(
                'id' => self::STATUS_TEMPORARY_INACTIVE,
                'status' => $moduleInstance->l('Temporarily Inactive', 'hotelreservationsystem')
            ),
        );
        return $status;
    }

    /**
     * [getHotelRoomInfoByProductId :: To get all rooms information belong to a room type(product) by product id]
     * @param  [int] $id_product [Id of the product]
     * @return [array|false]     [If data found returns array containing all rooms information belongs to a room type(product) which product id is passed else returns false]
     */
    public function getHotelRoomInfoByProductId($id_product)
    {
        $result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_product`='.$id_product);
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * [getHotelRoomInfo :: To get all rooms information belong to a room type(product) and belongss to a hotel wwhich hotel id is 								passed]
     * @param  [type]  $id_product [Id of the room type(product)]
     * @param  [type]  $id_hotel   [Id of the hotel]
     * @param  [int] $is_getNum  [
     *                           If $is_getNum is passed
     *                           	 then returns number of rooms belong to a room type(product) and belongss to a hotel wwhich hotel 									id is passed
     *                           else
     *                             	Returns array containing all rooms information belong to a room type(product) and belongss to 										a hotel wwhich hotel id is passed
     *                           	  	 ]
     * @return [array|int|boolean] [If $is_getNum is passed
     *                           	 then returns number of rooms belong to a room type(product) and belongss to a hotel wwhich hotel 									id is passed
     *                           else
     *                             	if data found
     *                             		Returns array containing all rooms information belong to a room type(product) and belongss to 										a hotel wwhich hotel id is passed
     *                             	else
     *                             	returns false; ]
     */
    public function getHotelRoomInfo($id_product, $id_hotel, $is_getNum = 0)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_product` = ".$id_product." AND `id_hotel` = ".$id_hotel;

        $rm_info = Db::getInstance()->executeS($sql);

        if ($is_getNum) {
            $no_row = Db::getInstance()->NumRows();
            return $no_row;
        } else {
            if ($rm_info) {
                return $rm_info;
            } else {
                return false;
            }
        }
    }

    /**
     * [getHotelRoomInfoById :: To get Information of a room by its id(primary key)]
     * @param  [int] $id         [id of the room in the table(primary key)]
     * @return [array|false]     [If data found returns array containing information of the room which id is passed else returns false]
     */
    public function getHotelRoomInfoById($id)
    {
        $sql = "SELECT `room_num` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id` = ".$id;
        $rm_num = Db::getInstance()->getValue($sql);

        if ($rm_num) {
            return $rm_num;
        } else {
            return false;
        }
    }

    public static function getHotelRoomsInfo($idHotel = null, $idProduct = null, $idLang = null)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $sql = 'SELECT hri.*, hri.`id_product`, hri.`id_hotel`, hrt.`adults`, hrt.`children`, hrt.`max_adults`,
        hrt.`max_children`, hrt.`max_guests`, hrt.`min_los`, hrt.`max_los`, pl.`name` AS room_type_name, hbil.`hotel_name` AS hotel_name
        FROM `'._DB_PREFIX_.'htl_room_information` hri
        INNER JOIN `'._DB_PREFIX_.'htl_room_type` hrt ON (hrt.`id_product` = hri.`id_product`)
        INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON (hbi.`id` = hri.`id_hotel`)
        INNER JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil ON (hbil.`id` = hri.`id_hotel` AND hbil.`id_lang` = '.(int) $idLang.')
        INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
        INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int) $idLang.')
        WHERE 1 '.($idHotel ? ' AND hri.`id_hotel` = '.(int) $idHotel : '').
        ($idProduct ? ' AND hri.`id_product` = '.(int) $idProduct : '').'
        ORDER BY hri.`id_product`, hri.`id`';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Deprecated
     * [deleteHotelRoomInfoById :: To delete room information which id is passed]
     * @param  [int] $id_room_info [Id of the room which information(row in the table) to be deleted]
     * @return [Boolean]         [Returns true if deleted successfully else returns false]
     */
    public function deleteHotelRoomInfoById($id_room_info)
    {
        $result = Db::getInstance()->delete('htl_room_information', '`id` = '.(int)$id_room_info, 1);

        return $result;
    }

    public function getRoomTypeAvailableRoomsForDateRange($id_hotel, $id_product, $date_from, $date_to)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_information` where `id_hotel`='.$id_hotel.' AND `id_product`='.$id_product.' AND `id` NOT IN (SELECT `id_room` from `'._DB_PREFIX_.'htl_booking_detail` where `date_from`< \''.pSQL($date_to).'\' AND `date_to`>\''.$date_from.'\' AND `id_product`='.(int) $id_product.' AND `id_hotel`='.(int) $id_hotel.')');
    }

    public function getRoomTypeDisabledRoomsForDateRange($id_hotel, $id_product, $date_from, $date_to)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_information` where `id_hotel`='.$id_hotel.' AND `id_product`='.$id_product.' AND `id_status`=3 AND `id` NOT IN (SELECT `id_room` from `'._DB_PREFIX_.'htl_booking_detail` where `date_from`< \''.pSQL($date_to).'\' AND `date_to`>\''.$date_from.'\' AND `id_product`='.(int) $id_product.' AND `id_hotel`='.(int) $id_hotel.')');
    }

    public function getRoomTypeBookedRoomsForDateRange($id_hotel, $id_product, $date_from, $date_to)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` where `date_from`< \''.pSQL($date_to).'\' AND `date_to`>\''.pSQL($date_from).'\' AND `id_product`='.(int) $id_product.' AND `id_hotel`='.(int) $id_hotel);
    }

    public function getFutureBookings($idRoom)
    {
        return Db::getInstance()->executeS('SELECT `id`, `id_order`, `date_from`, `date_to` FROM `'._DB_PREFIX_.'htl_booking_detail` where `date_to` > \''.pSQL(date('Y-m-d')).'\' AND `is_refunded` = 0 AND `id_room`='.(int) $idRoom);
    }

    // Webservice :: webservice add function
    public function addWs($autodate = true, $null_values = false)
    {
        $objRoomType = new HotelRoomType();
        if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($this->id_product)) {
            $this->id_hotel = $roomTypeInfo['id_hotel'];
            return $this->add($autodate, $null_values);
        }
        return false;
    }

    // Webservice :: webservice update function
    public function updateWs($null_values = false)
    {
        $objRoomType = new HotelRoomType();
        if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($this->id_product)) {
            $this->id_hotel = $roomTypeInfo['id_hotel'];
            return $this->update($null_values);
        }
        return false;
    }

    // Webservice :: webservice delete function
    public function deleteWs()
    {
        return $this->delete();
    }
}
