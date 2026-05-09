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

class HotelBranchAmenities extends ObjectModel
{
    public $id_hotel;
    public $amenity_id;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table'   => 'htl_branch_amenity',
        'primary' => 'id_htl_branch_amenity',
        'fields'  => array(
            'id_hotel'   => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'amenity_id' => array('type' => self::TYPE_INT),
            'date_add'   => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd'   => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    /**
     * @param int $idHotel
     * @param int $idLang  defaults to current context language
     * @return array
     */
    public static function getAmenities($idHotel, $idLang = 0): array
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT ha.`id_htl_amenity` AS `id`, ha.`logo_type`, ha.`logo`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_branch_amenity` hba
            INNER JOIN `'._DB_PREFIX_.'htl_amenity` ha
                ON (ha.`id_htl_amenity` = hba.`amenity_id`)
            INNER JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_htl_amenity` = hba.`amenity_id` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE hba.`id_hotel` = '.(int)$idHotel.'
                AND ha.`active` = 1
                AND ha.`parent_amenity_id` > 0
            ORDER BY ha.`position` ASC, hal.`name` ASC'
        ) ?: array();
    }

    /**
     * Delete existing assignments then save new ones for a hotel.
     *
     * @param int   $idHotel
     * @param array $amenityIds
     * @return bool
     */
    public function saveBranchAmenities($idHotel, array $amenityIds): bool
    {
        Db::getInstance()->delete('htl_branch_amenity', '`id_hotel` = '.(int)$idHotel);

        foreach ($amenityIds as $amenityId) {
            $obj = new self();
            $obj->id_hotel   = (int)$idHotel;
            $obj->amenity_id = (int)$amenityId;
            $obj->save();
        }

        return true;
    }
}
