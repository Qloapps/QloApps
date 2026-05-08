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
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table'   => 'htl_room_type_amenity',
        'primary' => 'id_htl_room_type_amenity',
        'fields'  => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'amenity_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add'   => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd'   => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    /**
     * @param int $idProduct
     * @return int[]
     */
    public static function getAmenityIdsByProduct($idProduct)
    {
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `amenity_id`
            FROM `'._DB_PREFIX_.'htl_room_type_amenity`
            WHERE `id_product` = '.(int)$idProduct
        );

        return $rows ? array_map('intval', array_column($rows, 'amenity_id')) : array();
    }

    /**
     * Delete all amenity assignments for a room type product.
     *
     * @param int $idProduct
     * @return bool
     */
    public function deleteByProductId($idProduct)
    {
        return Db::getInstance()->delete('htl_room_type_amenity', '`id_product` = '.(int)$idProduct);
    }

    /**
     * Replace all amenity assignments for a room type product.
     *
     * @param int   $idProduct
     * @param int[] $amenityIds
     * @return bool
     */
    public function assignAmenitiesToProduct($idProduct, array $amenityIds)
    {
        if (!$this->deleteByProductId($idProduct)) {
            return false;
        }

        foreach (array_unique(array_filter(array_map('intval', $amenityIds))) as $amenityId) {
            $obj = new self();
            $obj->id_product = (int)$idProduct;
            $obj->amenity_id = (int)$amenityId;
            if (!$obj->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return featured amenities assigned to a room type, for room type list display.
     *
     * @param int $idProduct
     * @param int $idLang
     * @return array
     */
    public static function getFeaturedAmenities($idProduct, $idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT ha.`id_htl_amenity` AS `id`, ha.`logo_type`, ha.`logo`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_room_type_amenity` hrta
            INNER JOIN `'._DB_PREFIX_.'htl_amenity` ha
                ON (ha.`id_htl_amenity` = hrta.`amenity_id`)
            INNER JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_htl_amenity` = hrta.`amenity_id` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE hrta.`id_product` = '.(int)$idProduct.'
                AND ha.`is_featured` = 1
                AND ha.`active` = 1
            ORDER BY ha.`position` ASC'
        ) ?: array();
    }

    /**
     * Return active child amenities assigned to a room type, formatted for front-end display.
     *
     * @param int $idProduct
     * @param int $idLang
     * @return array
     */
    public static function getFrontAmenities($idProduct, $idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT ha.`id_htl_amenity` AS `id`, ha.`logo_type`, ha.`logo`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_room_type_amenity` hrta
            INNER JOIN `'._DB_PREFIX_.'htl_amenity` ha
                ON (ha.`id_htl_amenity` = hrta.`amenity_id`)
            INNER JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_htl_amenity` = hrta.`amenity_id` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE hrta.`id_product` = '.(int)$idProduct.'
                AND ha.`active` = 1
                AND ha.`parent_amenity_id` > 0
            ORDER BY ha.`position` ASC, hal.`name` ASC'
        ) ?: array();
    }
}
