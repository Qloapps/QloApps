<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/osl-3.0-php
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
 * @license https://opensource.org/license/osl-3.0-php Open Software License version 3.0
 */

class HotelRoomTypeAmenities extends ObjectModel
{
    public $id_product;
    public $amenity_id;
    public $is_featured;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table'   => 'htl_room_type_amenity',
        'primary' => 'id_room_type_amenity',
        'fields'  => array(
            'id_product'  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'amenity_id'  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'is_featured' => array('type' => self::TYPE_INT),
            'date_add'    => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd'    => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    /**
     * @param int  $idProduct
     * @param int  $idLang       defaults to current context language
     * @param bool $featuredOnly true = is_featured=1 only; false = all active child amenities
     * @return array
     */
    public static function getAmenities($idProduct, $idLang = 0, $featuredOnly = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $filter = $featuredOnly ? 'AND hrta.`is_featured` = 1' : '';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT ha.`id_amenity` AS `id`, ha.`logo_type`, ha.`logo`, hal.`name`, hrta.`is_featured`
            FROM `'._DB_PREFIX_.'htl_room_type_amenity` hrta
            INNER JOIN `'._DB_PREFIX_.'htl_amenity` ha
                ON (ha.`id_amenity` = hrta.`amenity_id`)
            INNER JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_amenity` = hrta.`amenity_id` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE hrta.`id_product` = '.(int)$idProduct.'
                AND ha.`active` = 1
                AND ha.`id_parent` > 0
                '.$filter.'
            ORDER BY ha.`position` ASC, hal.`name` ASC'
        ) ?: array();
    }

    /**
     * @param int $idProduct
     * @return bool
     */
    public static function deleteByProduct($idProduct)
    {
        return (bool)Db::getInstance()->delete('htl_room_type_amenity', '`id_product` = '.(int)$idProduct);
    }

    /**
     * Prepare amenities tree data for a room type form, with selection state.
     *
     * @param int $idProduct
     * @param int $idLang defaults to current context language
     * @return array
     */
    public static function getRoomTypeAmenitiesTreeData($idProduct, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $objHotelAmenities = new HotelAmenities();
        $amenities = $objHotelAmenities->hotelBranchSelectedAmenities(
            array_column(self::getAmenities($idProduct), 'id'),
            $idLang
        );

        if (!$amenities) {
            return array();
        }

        foreach ($amenities as $idAmenityGroup => &$amenityGroup) {
            $amenityGroup['value'] = (int) $idAmenityGroup;
            $amenityGroup['input_name'] = 'room_type_amenity_parents';

            $selectedAmenities = 0;
            if (!empty($amenityGroup['children'])) {
                foreach ($amenityGroup['children'] as &$amenity) {
                    $amenity['value'] = (int) $amenity['id'];
                    $amenity['input_name'] = 'room_type_amenities';
                    if (!empty($amenity['selected'])) {
                        ++$selectedAmenities;
                    }
                }

                if ($selectedAmenities === count($amenityGroup['children'])) {
                    $amenityGroup['selected'] = true;
                }
            }
        }

        return $amenities;
    }

    /**
     * Delete existing assignments then save new ones for a room type.
     *
     * @param int   $idProduct
     * @param array $amenityIds
     * @param array $featuredIds amenity IDs that should be marked is_featured=1
     * @return bool
     */
    public function saveRoomTypeAmenities($idProduct, array $amenityIds, array $featuredIds = array())
    {
        static::deleteByProduct($idProduct);

        $featuredSet = array_flip(array_map('intval', $featuredIds));

        foreach (array_unique(array_filter(array_map('intval', $amenityIds))) as $amenityId) {
            $objHotelRoomTypeAmenities = new self();
            $objHotelRoomTypeAmenities->id_product  = (int)$idProduct;
            $objHotelRoomTypeAmenities->amenity_id  = (int)$amenityId;
            $objHotelRoomTypeAmenities->is_featured = isset($featuredSet[$amenityId]) ? 1 : 0;
            if (!$objHotelRoomTypeAmenities->save()) {
                return false;
            }
        }

        return true;
    }
}
