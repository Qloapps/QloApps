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

class HotelRoomType extends ObjectModel
{
    public $id;
    public $id_product;
    public $id_hotel;
    public $adults;
    public $children;
    public $max_adults;
	public $max_children;
    public $max_guests;
    public $min_los;
    public $max_los;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_room_type',
        'primary' => 'id',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'adults' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'children' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'max_adults' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'default' => 2),
            'max_children' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'max_guests' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'default' => 2),
            'min_los' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'default' => 1),
            'max_los' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'default' => 0),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'hotel_room_types',
        'objectNodeName' => 'hotel_room_type',
        'fields' => array(
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'room_types',
                )
            ),
            'id_hotel' => array(
                'xlink_resource' => array(
                    'resourceName' => 'hotels',
                )
            ),
        ),
        'associations' => array(
            'hotel_rooms' => array(
                'resource' => 'hotel_room',
                'fields' => array('id' => array('required' => true))
            ),
        ),
    );

    /**
     * [duplicateRoomType :: duplicates room type].
     *
     * @param [int] $idHotelRoomTypeOld [Id of room type from which information will be used]
     *
     * @param [int] $idHotelRoomTypeNew [Id of room type to which information will be assigned]
     *
     * @param [int] $idHotelNew [Id of hotel to which room type will be assigned]
     *
     * @param [bool] $returnId [Decides whether to return new id or not]
     *
     * @return [bool] [Returns true if successful, false otherwise]
     */
    public static function duplicateRoomType($idProductOld, $idProductNew, $idHotelNew = null, $returnId = true)
    {
        $roomType = Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type` hrt
            WHERE hrt.`id_product` = '.(int)$idProductOld
        );

        if (!Db::getInstance()->NumRows()) {
            return true;
        }

        if (!$idHotelNew) {
            $idHotelNew = $roomType['id_hotel'];
        }

        $objHotelRoomType = new HotelRoomType();
        $objHotelRoomType->id_product = $idProductNew;
        $objHotelRoomType->id_hotel = $idHotelNew;
        $objHotelRoomType->adults = $roomType['adults'];
        $objHotelRoomType->children = $roomType['children'];
        if ($objHotelRoomType->save()) {
            $objHotelRoomType->updateCategories();
            return $returnId ? $objHotelRoomType->id : true;
        }
        return false;
    }

    /**
     * [duplicateRooms :: duplicates rooms].
     *
     * @param [int] $idProductOld [Id product of room type from which rooms will be used]
     *
     * @param [int] $idHotelRoomTypeNew [Id of room type to which rooms will be assigned]
     *
     * @param [int] $idProductNew [id_product of the new room type]
     *
     * @param [int] $idHotelNew [Id of hotel to which rooms will be assigned]
     *
     * @return [bool] [Returns true if successful, false otherwise]
     */
    public static function duplicateRooms($idProductOld, $idHotelRoomTypeNew, $idProductNew, $idHotelNew = null)
    {
        $rooms = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_information` hri
            WHERE hri.`id_product` = '.(int)$idProductOld
        );

        if (!Db::getInstance()->NumRows()) {
            return true;
        }

        if (!$idHotelNew) {
            $idHotelNew = $rooms[0]['id_hotel'];
        }
        foreach ($rooms as $room) {
            $objHRInformation = new HotelRoomInformation();
            $objHRInformation->id_product = $idProductNew;
            $objHRInformation->id_hotel = $idHotelNew;
            $objHRInformation->room_num = $room['room_num'];
            $objHRInformation->id_status = $room['id_status'];
            $objHRInformation->floor = $room['floor'];
            $objHRInformation->comment = $room['comment'];
            if ($objHRInformation->save()) {
                $idRoom = $objHRInformation->id;
                if ((int)$room['id_status'] === (int)HotelRoomInformation::STATUS_TEMPORARY_INACTIVE) {
                    $disableDates = Db::getInstance()->executeS(
                        'SELECT * FROM `'._DB_PREFIX_.'htl_room_disable_dates` hrdd
                        WHERE hrdd.`id_room` = '.(int)$room['id']
                    );

                    if (is_array($disableDates) && count($disableDates)) {
                        foreach ($disableDates as $disableDate) {
                            $objHRDisableDates = new HotelRoomDisableDates();
                            $objHRDisableDates->id_room_type = $idHotelRoomTypeNew;
                            $objHRDisableDates->id_room = $idRoom;
                            $objHRDisableDates->date_from = $disableDate['date_from'];
                            $objHRDisableDates->date_to = $disableDate['date_to'];
                            $objHRDisableDates->reason = $disableDate['reason'];
                            if (!$objHRDisableDates->save()) {
                                return false;
                            }
                        }
                    }
                }
            } else {
                return false;
            }
        }
        return true;
    }

    public function updateCategories()
    {
        $objProduct = new Product($this->id_product);
        if (!Validate::isLoadedObject($objProduct)) {
            return false;
        }

        $objHBInformation = new HotelBranchInformation($this->id_hotel);
        if (!Validate::isLoadedObject($objHBInformation)) {
            return false;
        }

        $objCategory = new Category($objHBInformation->id_category);
        if (!Validate::isLoadedObject($objCategory)) {
            return false;
        }

        $categories = $objCategory->getParentsCategories();
        $categories = array_column($categories, 'id_category');
        return $objProduct->updateCategories($categories);
    }

    /**
     * [deleteByProductId :: delete rows from the table where passed product Id matched].
     *
     * @param [int] $id_product [Id of the product]
     *
     * @return [boolean] [Returns true if successfully deleted else return false]
     */
    public function deleteByProductId($id_product)
    {
        $delete = Db::getInstance()->delete('htl_room_type', '`id_product`='.(int) $id_product);

        return $delete;
    }

    /**
     * [getRoomTypeInfoByIdProduct :: To get Information about the room type(product) by product id].
     *
     * @param [int] $id_product [Id of the product]
     *
     * @return [array|false] [If data found returns array containing information of the room type else returns false]
     */
    public function getRoomTypeInfoByIdProduct($id_product, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT hrt.*, hbl.`hotel_name`
                FROM `'._DB_PREFIX_.'htl_room_type` AS hrt
                INNER JOIN `'._DB_PREFIX_.'htl_branch_info_lang` AS hbl
                ON (hbl.`id` = hrt.`id_hotel` AND hbl.`id_lang` = '.(int)$idLang.')
                WHERE hrt.`id_product` = '.(int)$id_product;

        return Db::getInstance()->getRow($sql);
    }

    /**
     * [getHotelIdAddressByIdProduct: get room type location by id_product]
     *
     * @param [int] $id_product
     * @return int
     */
    public static function getHotelIdAddressByIdProduct($id_product)
    {
        return Db::getInstance()->getValue(
            'SELECT `id_address` from `'._DB_PREFIX_.'address` a
            INNER JOIN `'._DB_PREFIX_.'htl_room_type` hrt
            ON (hrt.`id_hotel` = a.`id_hotel`)
            WHERE hrt.`id_product` = '.(int)$id_product.' AND a.`deleted` = 0
        ');
    }

    /**
     * [getRoomTypeByHotelId :: To get All room types informations which belong to the hotel having id passed as $hotel_id].
     *
     * @param [int] $hotel_id [Id of the hotel Which room types information you want]
     *
     * @return [array|boolean] [If data found returns array containing all rooms types information belongs to the passed hotel_id type else returns false ]
     */
    public function getRoomTypeByHotelId($hotel_id, $id_lang, $active = 2)
    {
        $sql = 'SELECT rt.`id` as id_room_type, pl.`name` AS room_type, pl.`id_product` AS id_product, p.`active`
			FROM `'._DB_PREFIX_.'htl_room_type` AS rt';
        if ($active != 2) {
            $sql .= ' INNER JOIN `'._DB_PREFIX_.'product` AS pp ON (rt.id_product = pp.id_product AND pp.active = 1)';
        }
        $sql .= ' INNER JOIN `'._DB_PREFIX_.'product_lang` AS pl
            ON (rt.`id_product` = pl.`id_product` AND pl.`id_lang`='.(int)$id_lang.')
            INNER JOIN `'._DB_PREFIX_.'product` AS p ON (rt.`id_product` = p.`id_product`)
			WHERE rt.id_hotel ='.(int)$hotel_id;

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @param [int] $roomTypesList: string of idRoomTypes seperated by ","
     */
    public function getRoomTypeDetailByRoomTypeIds($roomTypesList)
    {
        $sql = 'SELECT COUNT(hri.`id`) AS `numberOfRooms`, hrt.`id_product`, `adults`, `children`, `max_adults`, `max_children`, `max_guests`
                FROM `'._DB_PREFIX_.'htl_room_type` AS `hrt`
                INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS `hri` ON (hri.`id_product` = hrt.`id_product`)
                WHERE hrt.`id_product` IN ('.$roomTypesList.')
                GROUP BY hrt.`id_product`';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * [getIdProductByHotelId ::   	if (0)
     *                          	{
     *                          		returns array containing all rooms types information belongs to the hotel which id is passed 										as $idHotel
     *                          	}
     *                          	else
     *                          	{
     *                          		returns array containing rooms type information which produt_id=$idRoomType belongs to the 											hotel which id is passed as $idHotel
     *                          	}].
     *
     * @param [type] $idHotel        [Id of the hotel ]
     * @param [int]  $idRoomType       [
     *                                Id of the product
     *                                if (0)
     *                                {
     *                                returns array containing all rooms types information belongs to the hotel which id is passed 										as $idHotel
     *                                }
     *                                else
     *                                {
     *                                returns array containing rooms type information which produt_id=$idRoomType belongs to the 											hotel which id is passed as $idHotel
     *                                }
     *                                ]
     * @param [1|0]  $onlyActiveProd  [1 for only active products data and 0 for all products data]
     * @param [1|0]  $onlyActiveHotel [1 for only active Hotel results and 0 for all hotel data]
     *
     * @return [array|false] [If data found returns array containing information of the room types else returns false ]
     */
    public function getIdProductByHotelId($idHotel, $idRoomType = 0, $onlyActiveProd = 0, $onlyActiveHotel = 0)
    {
        $sql = 'SELECT DISTINCT hrt.`id_product`, hrt.`adults`, hrt.`children`, hrt.`id`
                FROM `'._DB_PREFIX_.'htl_room_type` AS hrt ';

        if ($onlyActiveHotel) {
            $sql .= 'INNER JOIN `'._DB_PREFIX_.'htl_branch_info` AS hti ON (hti.id = hrt.id_hotel AND hti.active = 1)';
        }
        if ($onlyActiveProd) {
            $sql .= 'INNER JOIN `'._DB_PREFIX_.'product` AS pp ON (hrt.id_product = pp.id_product AND pp.active = 1)';
        }
        $sql .= 'WHERE hrt.`id_hotel`='. (int)$idHotel;

        if ($idRoomType) {
            $sql .= ' AND hrt.`id_product` = '. (int)$idRoomType;
        }
        return Db::getInstance()->executeS($sql);
    }


    /**
     * [getMaxAdults :: To get Maximum number of adults can be in a room type for a hotel].
     *
     * @param [int] $id_hotel [Id of the hotel for Maximum number of adults data you want]
     *
     * @return [int|false] [If data found returns number of maximum adults can be in a room type for hotel else returns false ]
     */
    public static function getMaxAdults($id_hotel)
    {
        $sql = 'SELECT MAX(adults) AS max_adult FROM '._DB_PREFIX_.'htl_room_type WHERE id_hotel='.$id_hotel;

        $max_adult = Db::getInstance()->getValue($sql);

        if ($max_adult) {
            return $max_adult;
        } else {
            return false;
        }
    }

    /**
     * [getMaxChild :: To get Maximum number of children can be in a room type for a hotel].
     *
     * @param [int] $id_hotel [Id of the hotel for Maximum number of children data you want]
     *
     * @return [int|false] [If data found returns number of maximum children can be in a room type for hotel else returns false ]
     */
    public static function getMaxChild($id_hotel)
    {
        $sql = 'SELECT MAX(children) AS max_child FROM '._DB_PREFIX_.'htl_room_type WHERE id_hotel='.$id_hotel;

        $max_child = Db::getInstance()->getValue($sql);

        if ($max_child) {
            return $max_child;
        } else {
            return false;
        }
    }

    public function getAllRoomTypes()
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_type`');
    }

    public static function getRoomTypeTaxRate($idproduct)
    {
        $priceTI = Product::getPriceStatic($idproduct, true, null, 6, null, false, true);
        $priceTE = Product::getPriceStatic($idproduct, false, null, 6, null, false, true);
        if ($priceTE) {
            $taxRate = (($priceTI - $priceTE) / $priceTE) * 100;
        } else {
            $taxRate = 0;
        }
        return $taxRate;
    }

    // Webservice funcions
    public function getWsHotelRooms()
    {
        return Db::getInstance()->executeS(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_product` = '.(int)$this->id_product.' ORDER BY `id` ASC'
        );
    }
}
