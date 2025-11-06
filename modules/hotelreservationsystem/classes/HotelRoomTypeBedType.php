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

class HotelRoomTypeBedType extends ObjectModel
{
    public $id_product;
    public $id_bed_type;

    public static $definition =array(
        'table' => 'htl_room_type_bed_type',
        'primary' => 'id_room_type_bed_type',
        'fields' => array(
            'id_bed_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    public function getRoomTypeBedTypes($idProduct)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.$this->table.'`
            WHERE `id_product` ='.(int) $idProduct;

        return Db::getInstance()->executeS($sql);
    }

    public function updateRoomTypeBedTypes($idBedTypes, $idProduct)
    {
        $res = true;
        if ($roomTypeBedTypes = $this->getRoomTypeBedTypes($idProduct)) {
            $roomTypeBedTypes = array_column($roomTypeBedTypes, 'id_room_type_bed_type', 'id_bed_type');
        }

        if ($idBedTypes) {
            foreach ($idBedTypes as $idBedType) {
                // If room type bed type mapping already exits no need to update it.
                if (isset($roomTypeBedTypes[$idBedType])) {
                    unset($roomTypeBedTypes[$idBedType]);
                } else {
                    $objHotelRoomTypeBedType = new self();
                    $objHotelRoomTypeBedType->id_product = $idProduct;
                    $objHotelRoomTypeBedType->id_bed_type = $idBedType;
                    $res &=$objHotelRoomTypeBedType->save();
                }
            }
        }

        // Removing the non selected bed types.
        if ($roomTypeBedTypes) {
            $res &= $this->deleteRoomTypeBedTypesById($roomTypeBedTypes);
        }

        return $res;
    }

    public function deleteRoomTypeBedTypesById($idRoomTypeBedTypes)
    {
        $res = true;
        if ($idRoomTypeBedTypes) {
            foreach ($idRoomTypeBedTypes as $idRoomTypeBedType) {
                $objHotelRoomTypeBedType = new self($idRoomTypeBedType);
                $res &= $objHotelRoomTypeBedType->delete();
            }
        }

        return $res;
    }

    public function deleteRoomTypeBedTypes($idBedType = false, $idProduct = false)
    {
        return Db::getInstance()->delete(
            $this->table,
            ' 1 '.($idBedType ? ' AND `id_bed_type`='.(int) $idBedType : '').($idProduct ? ' AND `id_product`='.(int) $idProduct : '')
        );
    }

}
