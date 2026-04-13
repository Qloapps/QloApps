<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/osl-3-0-php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@qloapps.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to https://store.webkul.com/customisation-guidelines for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
 */

class HotelConnectedRoom extends ObjectModel
{
    /** @var bool|null */
    protected static $connectedRoomTableAvailable = null;

    public $id_connected_room;
    public $id_room;
    public $id_room_connected;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_connected_room',
        'primary' => 'id_connected_room',
        'fields' => array(
            'id_room' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_room_connected' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE),
            'date_upd' => array('type' => self::TYPE_DATE),
        ),
    );

    public static function getConnectedRooms($idLang, $roomId = null, $isConnected = true ,$count = false)
    {
        $idLang = (int) $idLang;
        $roomId = (int) $roomId;
        $objHotelRoomInformation = new HotelRoomInformation($roomId);
        $hotelId = $objHotelRoomInformation->id_hotel;

        if ($isConnected) {
            $sql = 'SELECT 
            hcr.id_connected_room, hcr.id_room AS id_room_information, hcr.id_room_connected AS id_room, main_room.id_hotel, main_room.room_num AS main_room_num, connected_room.room_num AS connected_room_num,
            main_pl.name AS main_room_type, conn_pl.name AS connected_room_type
            FROM `' . _DB_PREFIX_ . 'htl_connected_room` hcr
            INNER JOIN `' . _DB_PREFIX_ . 'htl_room_information` main_room ON main_room.id = hcr.id_room
            INNER JOIN `' . _DB_PREFIX_ . 'htl_room_information` connected_room ON connected_room.id = hcr.id_room_connected
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` main_pl ON (main_pl.id_product = main_room.id_product AND main_pl.id_lang = ' . (int) $idLang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` conn_pl ON (conn_pl.id_product = connected_room.id_product AND conn_pl.id_lang = ' . (int) $idLang . ')
            WHERE main_room.id_hotel = ' . (int) $hotelId . '
            AND connected_room.id_hotel = ' . (int) $hotelId;
            if ($roomId > 0) {
                $sql .= ' AND main_room.id = ' . (int) $roomId;
            }
            $results = Db::getInstance()->executeS($sql);
            if ($results === false) {
                return array();
            }
            $grouped = array();
            foreach ($results as $row) {
                $mainRoomId = (int) $row['id_room_information'];
                $connType = (string) $row['connected_room_type'];
                if (!isset($grouped[$mainRoomId])) {
                    $grouped[$mainRoomId] = array();
                }
                if (!isset($grouped[$mainRoomId][$connType])) {
                    $grouped[$mainRoomId][$connType] = array();
                }
                $grouped[$mainRoomId][$connType][] = $row;
            }
            if($count){
                return count($results);
            }else{
                return $grouped;
            }
        } else {
            $sql = 'SELECT hri.*, pl.name AS room_type
            FROM `' . _DB_PREFIX_ . 'htl_room_information` hri
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = hri.id_product AND pl.id_lang = ' . (int) $idLang . ')
            WHERE hri.id_hotel = ' . (int) $hotelId . '
            AND hri.id != ' . (int) $roomId . '
            AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'htl_connected_room` cr WHERE cr.id_room = ' . (int) $roomId . ' AND cr.id_room_connected = hri.id)
            ORDER BY hri.room_num ASC';
            $results = Db::getInstance()->executeS($sql);
            return ($results === false) ? array() : $results;
        }
    }

    public static function getRoomConnectedWith($roomId)
    {
        $objHotelRoomInformation = new HotelRoomInformation($roomId);
        $idProduct = $objHotelRoomInformation->id_product;
        if (!$idProduct) {
            return array();
        }
        $sql = 'SELECT DISTINCT case 
                    WHEN hcr.id_room = ' . (int) $roomId . ' THEN hcr.id_room_connected 
                    ELSE hcr.id_room 
                END AS affected_room_id
                FROM `' . _DB_PREFIX_ . 'htl_connected_room` hcr
                INNER JOIN `' . _DB_PREFIX_ . 'htl_room_information` hri 
                    ON (hri.id = hcr.id_room OR hri.id = hcr.id_room_connected)
                WHERE (hcr.id_room = ' . (int) $roomId . ' OR hcr.id_room_connected = ' . (int) $roomId . ')
                AND hri.id != ' . (int) $roomId . '
                AND hri.id_product = ' . (int) $idProduct;
        $results = Db::getInstance()->executeS($sql);
        return $results ? array_column($results, 'affected_room_id') : array();
    }
}
