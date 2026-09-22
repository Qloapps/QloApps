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
* Do not edit or add to this file if you wish to upgrade this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class QghcRoomType
{
    public $id_room_type;
    public $id_google_room_type;

    /**
     * Returns the QghcRoomType instance for a PMS room type product ID, or null if not enrolled.
     *
     * @param int $idRoomType  htl_room_type.id_product
     * @return QghcRoomType|null
     */
    public static function getByRoomTypeId($idRoomType)
    {
        $row = Db::getInstance()->getRow(
            'SELECT `is_google_hotel_enabled`, `id_google_room_type` FROM `' . _DB_PREFIX_ . 'htl_room_type`
             WHERE `id_product` = ' . (int) $idRoomType
        );

        if (!$row || !$row['is_google_hotel_enabled']) {
            return null;
        }

        $obj = new self();
        $obj->id_room_type        = (int) $idRoomType;
        $obj->id_google_room_type = $row['id_google_room_type'];

        return $obj;
    }

    /**
     * Enrolls one room type under a hotel.
     *
     * @param int $idHotel
     * @param int $idRoomType  htl_room_type.id_product
     * @return bool
     */
    public static function insertForProperty($idHotel, $idRoomType)
    {
        return Db::getInstance()->update(
            'htl_room_type',
            array('is_google_hotel_enabled' => 1),
            '`id_product` = ' . (int) $idRoomType . ' AND `id_hotel` = ' . (int) $idHotel
        );
    }

    /**
     * Stores the Google-assigned room type ID received from the Channel Manager.
     *
     * @param string $idGoogleRoomType
     * @return bool
     */
    public function applyGoogleRoomType($idGoogleRoomType)
    {
        $this->id_google_room_type = (string) $idGoogleRoomType;

        return Db::getInstance()->update(
            'htl_room_type',
            array('id_google_room_type' => pSQL($this->id_google_room_type)),
            '`id_product` = ' . (int) $this->id_room_type
        );
    }

    /**
     * Un-enrolls all room types for a hotel.
     *
     * @param int $idHotel
     * @return bool
     */
    public static function deleteByPropertyId($idHotel)
    {
        return Db::getInstance()->update(
            'htl_room_type',
            array('is_google_hotel_enabled' => 0, 'id_google_room_type' => null),
            '`id_hotel` = ' . (int) $idHotel
        );
    }

    /**
     * Un-enrolls only the room types of a hotel NOT in $keepRoomTypeIds, clearing their
     * Google ID. Room types already enrolled and staying in $keepRoomTypeIds are left
     * completely untouched — including whatever Google ID the CM already assigned them.
     * Used by QghcPropertyService::enable() so re-selecting a hotel's room types never
     * wipes the Google ID of a room type that was never actually deselected.
     *
     * @param int   $idHotel
     * @param int[] $keepRoomTypeIds  htl_room_type.id_product values to leave untouched.
     * @return bool
     */
    public static function deleteExcept($idHotel, array $keepRoomTypeIds)
    {
        $keepIds  = array_map('intval', $keepRoomTypeIds);
        $keepList = $keepIds ? implode(',', $keepIds) : '0';

        return Db::getInstance()->update(
            'htl_room_type',
            array('is_google_hotel_enabled' => 0, 'id_google_room_type' => null),
            '`id_hotel` = ' . (int) $idHotel . ' AND `id_product` NOT IN (' . $keepList . ')'
        );
    }

    /**
     * Returns id_product values of all room types enrolled for a hotel.
     * Used by QghcAri to restrict ARI output to only connected room types.
     *
     * @param int $idHotel
     * @return int[]
     */
    public static function getConnectedProductIds($idHotel)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'htl_room_type`
             WHERE `id_hotel` = ' . (int) $idHotel . ' AND `is_google_hotel_enabled` = 1'
        );

        $ids = array();
        if ($rows) {
            foreach ($rows as $row) {
                $ids[] = (int) $row['id_product'];
            }
        }
        return $ids;
    }

    /**
     * Returns id_product values enrolled for a hotel (for the hotel-edit tab checkbox state).
     *
     * @param int $idHotel
     * @return int[]
     */
    public static function getMappingsForHotel($idHotel)
    {
        return self::getConnectedProductIds($idHotel);
    }
}
