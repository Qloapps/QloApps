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

class HotelAmenities extends ObjectModel
{
    public $name;
    public $id_parent;
    public $position;
    public $active;
    public $logo_type;
    public $logo;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table'     => 'htl_amenity',
        'primary'   => 'id_amenity',
        'multilang' => true,
        'fields'    => array(
            'id_parent' => array('type' => self::TYPE_INT, 'required' => true),
            'position'          => array('type' => self::TYPE_INT),
            'active'            => array('type' => self::TYPE_INT, 'required' => true),
            'logo_type'         => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'logo'              => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'date_add'          => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd'          => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            // lang fields
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
        ),
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'hotel_amenities',
        'objectNodeName'  => 'hotel_amenity',
        'fields'          => array(),
    );

    public function delete()
    {
        if ($id = $this->id) {
            $imgFile = dirname(__FILE__).'/../views/img/hotel_amenities/'.(int)$id.'.jpg';
            if (file_exists($imgFile)) {
                unlink($imgFile);
            }
            return parent::delete();
        }
        return false;
    }

    /**
     * Return all categories (id_parent = 0) with their child amenities.
     *
     * @param int $idLang
     * @return array|false
     */
    public function getAmenitiesTree($idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $rows = Db::getInstance()->executeS(
            'SELECT ha.`id_amenity`, ha.`id_parent`, ha.`position`, ha.`active`, ha.`logo_type`, ha.`logo`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_amenity` = ha.`id_amenity` AND hal.`id_lang` = '.(int)$idLang.')
            ORDER BY ha.`id_parent` ASC, ha.`position` ASC'
        );

        if (!$rows) {
            return false;
        }

        $result = array();
        $amenitiesByCategory = array();

        foreach ($rows as $row) {
            if ($row['id_parent'] == 0) {
                $result[$row['id_amenity']] = array(
                    'id'       => $row['id_amenity'],
                    'name'     => $row['name'],
                    'position' => $row['position'],
                    'children' => array(),
                );
            } else {
                $row['id'] = $row['id_amenity'];
                $amenitiesByCategory[$row['id_parent']][] = $row;
            }
        }

        foreach ($amenitiesByCategory as $categoryId => $amenities) {
            if (isset($result[$categoryId])) {
                $result[$categoryId]['children'] = $amenities;
            }
        }

        return $result ?: false;
    }

    /**
     * Returns all amenity categories with children, marking which are selected for a hotel/room type.
     *
     * @param array $selectedIds flat array of selected amenity IDs
     * @param int   $idLang
     * @return array|false
     */
    public function hotelBranchSelectedAmenities($selectedIds, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $rows = Db::getInstance()->executeS(
            'SELECT ha.`id_amenity`, ha.`id_parent`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_amenity` = ha.`id_amenity` AND hal.`id_lang` = '.(int)$idLang.')
            ORDER BY ha.`id_parent` ASC'
        );

        if (!$rows) {
            return array();
        }

        $result = array();

        foreach ($rows as $row) {
            if ($row['id_parent'] == 0) {
                $result[$row['id_amenity']]['name'] = $row['name'];
            } else {
                $result[$row['id_parent']]['children'][] = array(
                    'id_amenity' => $row['id_amenity'],
                    'id'         => $row['id_amenity'],
                    'name'       => $row['name'],
                    'selected'   => in_array($row['id_amenity'], $selectedIds) ? 1 : 0,
                );
            }
        }

        return $result ?: false;
    }

    /**
     * @param int $id
     * @param int $idLang
     * @return array|false
     */
    public function getAmenityInfoById($id, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        return Db::getInstance()->getRow(
            'SELECT ha.`id_amenity`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_amenity` = ha.`id_amenity` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`id_amenity` = '.(int)$id
        );
    }

    /**
     * @param int $parentId
     * @return array|false
     */
    public function getChildAmenitiesByParentAmenityId($parentId)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_amenity` FROM `'._DB_PREFIX_.'htl_amenity` WHERE `id_parent` = '.(int)$parentId
        );
    }

    /**
     * Delete a category and all its child amenities.
     *
     * @param int $deleteId
     * @return bool
     */
    public function deleteCategory($idCategory)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_amenity` FROM `'._DB_PREFIX_.'htl_amenity`
            WHERE `id_parent` = '.(int)$idCategory.' OR `id_amenity` = '.(int)$idCategory
        );

        if ($rows) {
            foreach ($rows as $row) {
                $objHotelAmenities = new HotelAmenities((int)$row['id_amenity']);
                $objHotelAmenities->delete();
            }
        }

        return true;
    }

    /**
     * @param string $query
     * @param int    $idLang
     * @return array|false
     */
    public function searchByName($query, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT ha.*, hal.* FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal ON hal.`id_amenity` = ha.`id_amenity`
            WHERE hal.`name` LIKE \'%'.pSQL($query).'%\'
            AND hal.`id_lang` = '.(int)$idLang
        );
    }

    /**
     * Return all amenity categories (id_parent = 0), ordered by position.
     *
     * @param int $idLang
     * @return array
     */
    public static function getCategories($idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT ha.`id_amenity`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_amenity` = ha.`id_amenity` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`id_parent` = 0
            ORDER BY ha.`position` ASC'
        ) ?: array();
    }

    /**
     * Return all active child amenities (id_parent > 0).
     *
     * @param int $idLang
     * @return array
     */
    public static function getAmenities($idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT ha.`id_amenity`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_amenity` = ha.`id_amenity` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`id_parent` > 0
                AND ha.`active` = 1
            ORDER BY hal.`name` ASC'
        ) ?: array();
    }

    /**
     * Return image path for amenity logo stored in views/img/hotel_amenities/.
     *
     * @param int $id
     * @return string
     */
    public static function getAmenityImagePath($id)
    {
        return _MODULE_DIR_.'hotelreservationsystem/views/img/hotel_amenities/'.(int)$id.'.jpg';
    }
}
