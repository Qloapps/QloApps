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

    public static function getConnectedRooms($idRoom = null, $idLang = null)
    {
        $idLang = $idLang ? (int) $idLang : (int) Context::getContext()->language->id;

        if ($idRoom === null) {
            $sql = 'SELECT
                    hcr.id_connected_room,
                    hcr.id_room,
                    hcr.id_room_connected,
                    conn_pl.name AS room_type_name,
                    connected_room.room_num AS connected_room_name
                FROM `' . _DB_PREFIX_ . 'htl_connected_room` hcr
                INNER JOIN `' . _DB_PREFIX_ . 'htl_room_information` connected_room
                    ON connected_room.id = hcr.id_room_connected
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` conn_pl
                    ON (conn_pl.id_product = connected_room.id_product AND conn_pl.id_lang = ' . $idLang . ')
                ORDER BY hcr.id_room, connected_room.room_num';

            $results = Db::getInstance()->executeS($sql);
            if ($results === false) {
                return [];
            }
            $map = [];
            foreach ($results as $row) {
                $map[(int) $row['id_room']][] = $row;
            }
            return $map;
        }

        $idRoom = (int) $idRoom;
        if ($idRoom <= 0) {
            return array();
        }

        $sql = 'SELECT
                hcr.id_connected_room,
                hcr.id_room,
                hcr.id_room_connected,
                conn_pl.name AS room_type_name,
                connected_room.room_num AS connected_room_name
            FROM `' . _DB_PREFIX_ . 'htl_connected_room` hcr
            INNER JOIN `' . _DB_PREFIX_ . 'htl_room_information` connected_room
                ON connected_room.id = hcr.id_room_connected
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` conn_pl
                ON (conn_pl.id_product = connected_room.id_product AND conn_pl.id_lang = ' . $idLang . ')
            WHERE hcr.id_room = ' . $idRoom . '
            ORDER BY connected_room.room_num';

        $results = Db::getInstance()->executeS($sql);
        return ($results === false) ? array() : $results;
    }

    public static function getNotConnectedRooms($idRoom, $idLang = null)
    {
        $idRoom = (int) $idRoom;
        if ($idRoom <= 0) {
            return array();
        }
        $idLang = $idLang ? (int) $idLang : (int) Context::getContext()->language->id;

        $hotelId = (int) (new HotelRoomInformation($idRoom))->id_hotel;

        $sql = 'SELECT hri.id AS id_room, hri.id_product AS id_room_type, pl.name AS room_type_name, hri.room_num AS room_name
            FROM `' . _DB_PREFIX_ . 'htl_room_information` hri
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (pl.id_product = hri.id_product AND pl.id_lang = ' . $idLang . ')
            WHERE hri.id != ' . $idRoom . '
            AND hri.id_hotel = ' . $hotelId;
        $sql .= ' AND NOT EXISTS (
                SELECT 1 FROM `' . _DB_PREFIX_ . 'htl_connected_room` cr
                WHERE cr.id_room = ' . $idRoom . ' AND cr.id_room_connected = hri.id
            )
            ORDER BY pl.name ASC, hri.room_num ASC';

        $results = Db::getInstance()->executeS($sql);
        if ($results === false) {
            return array();
        }

        $grouped = array();
        foreach ($results as $row) {
            $typeId = (int) $row['id_room_type'];
            if (!isset($grouped[$typeId])) {
                $grouped[$typeId] = array(
                    'id_room_type' => $typeId,
                    'name'         => $row['room_type_name'],
                    'rooms'        => array(),
                );
            }
            $grouped[$typeId]['rooms'][] = array(
                'id_room' => (int) $row['id_room'],
                'name'    => $row['room_name'],
            );
        }
        return $grouped;
    }

    public static function getTotalConnectedRooms($idRoom = 0, $idRoomConnected = 0)
    {
        $idRoom = (int) $idRoom;
        $idRoomConnected = (int) $idRoomConnected;

        if ($idRoom <= 0 && $idRoomConnected > 0) {
            $sql = 'SELECT DISTINCT id_room
                FROM `' . _DB_PREFIX_ . 'htl_connected_room`
                WHERE id_room_connected = ' . $idRoomConnected;

            $results = Db::getInstance()->executeS($sql);
            if ($results === false) {
                return array();
            }
            return array_column($results, 'id_room');
        }

        $sql = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'htl_connected_room` hcr
            INNER JOIN `' . _DB_PREFIX_ . 'htl_room_information` hri
                ON hri.id = hcr.id_room_connected
            WHERE hcr.id_room = ' . $idRoom;

        return (int) Db::getInstance()->getValue($sql);
    }

}
