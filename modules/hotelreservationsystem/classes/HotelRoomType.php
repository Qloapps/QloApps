<?php

class HotelRoomType extends ObjectModel
{
    public $id;
    public $id_product;
    public $id_hotel;
    public $adult;
    public $children;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_room_type',
        'primary' => 'id',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'adult' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'children' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

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
    public function getRoomTypeInfoByIdProduct($id_product)
    {
        $sql = 'SELECT `id`,`id_hotel`, `adult`, `children` FROM `'._DB_PREFIX_.'htl_room_type` WHERE `id_product` = '.$id_product;
        $rm_info = Db::getInstance()->getRow($sql);

        if ($rm_info) {
            return $rm_info;
        } else {
            return false;
        }
    }

    /**
     * [getRoomTypeByHotelId :: To get All room types informations which belong to the hotel having id passed as $hotel_id].
     *
     * @param [int] $hotel_id [Id of the hotel Which room types information you want]
     *
     * @return [array|boolean] [If data found returns array containing all rooms types information belongs to the passed hotel_id type else returns false ]
     */
    public function getRoomTypeByHotelId($hotel_id, $id_lang)
    {
        $sql = 'SELECT pl.name AS room_type, pl.id_product AS id_product, p.active
			FROM `'._DB_PREFIX_.'htl_room_type` AS rt 
            INNER JOIN `'._DB_PREFIX_.'product_lang` AS pl ON (rt.id_product = pl.id_product AND pl.id_lang='.$id_lang.')
            INNER JOIN `'._DB_PREFIX_.'product` AS p ON (rt.id_product = p.id_product)
			WHERE rt.id_hotel ='.$hotel_id;

        $rm_type = Db::getInstance()->executeS($sql);

        if ($rm_type) {
            return $rm_type;
        } else {
            return false;
        }
    }

    /**
     * [getIdProductByHotelId ::   	if (0)
     *                          	{
     *                          		returns array containing all rooms types information belongs to the hotel which id is passed 										as $hotel_id
     *                          	}
     *                          	else
     *                          	{
     *                          		returns array containing rooms type information which produt_id=$room_type belongs to the 											hotel which id is passed as $hotel_id
     *                          	}].
     *
     * @param [type] $hotel_id        [Id of the hotel ]
     * @param [int]  $room_type       [
     *                                Id of the product
     *                                if (0)
     *                                {
     *                                returns array containing all rooms types information belongs to the hotel which id is passed 										as $hotel_id
     *                                }
     *                                else
     *                                {
     *                                returns array containing rooms type information which produt_id=$room_type belongs to the 											hotel which id is passed as $hotel_id
     *                                }
     *                                ]
     * @param [1|0]  $onlyActiveProd  [1 for only active products data and 0 for all products data]
     * @param [1|0]  $onlyActiveHotel [1 for only active Hotel results and 0 for all hotel data]
     *
     * @return [array|false] [If data found returns array containing information of the room types else returns false ]
     */
    public function getIdProductByHotelId($hotel_id, $room_type = 0, $onlyActiveProd = 0, $onlyActiveHotel = 0)
    {
        $sql = 'SELECT DISTINCT hrt.`id_product`, hrt.`adult`, hrt.`children` 
				FROM `'._DB_PREFIX_.'htl_room_type` AS hrt ';

        if ($onlyActiveHotel) {
            $sql .= 'INNER JOIN `'._DB_PREFIX_.'htl_branch_info` AS hti ON (hti.id = hrt.id_hotel AND hti.active = 1)';
        }

        if ($onlyActiveProd) {
            $sql .= 'INNER JOIN `'._DB_PREFIX_.'product` AS pp ON (hrt.id_product = pp.id_product AND pp.active = 1)';
        }

        $sql .= 'WHERE hrt.`id_hotel`='.$hotel_id;

        if ($room_type) {
            $sql .= ' AND hrt.`id_product` ='.$room_type;
        }
        $rm_type = Db::getInstance()->executeS($sql);
        if ($rm_type) {
            return $rm_type;
        } else {
            return false;
        }
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
        $sql = 'SELECT MAX(adult) AS max_adult FROM '._DB_PREFIX_.'htl_room_type WHERE id_hotel='.$id_hotel;

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
}
