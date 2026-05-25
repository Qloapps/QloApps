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
    public $parent_amenity_id;
    public $position;
    public $active;
    public $is_featured;
    public $logo_type;
    public $logo;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table'     => 'htl_amenity',
        'primary'   => 'id_htl_amenity',
        'multilang' => true,
        'fields'    => array(
            'parent_amenity_id' => array('type' => self::TYPE_INT, 'required' => true),
            'position'          => array('type' => self::TYPE_INT),
            'active'            => array('type' => self::TYPE_INT, 'required' => true),
            'is_featured'       => array('type' => self::TYPE_INT),
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
     * Returns true if at least one amenity item (child) exists in any category.
     *
     * @return bool
     */
    public static function hasAmenityItems()
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `'._DB_PREFIX_.'htl_amenity` WHERE `parent_amenity_id` != 0'
        );
    }

    /**
     * Return all categories (parent_amenity_id = 0) with their child amenities.
     *
     * @param int $idLang
     * @return array|false
     */
    public function getAllAmenitiesTree($idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $result = array();
        $parents = Db::getInstance()->executeS(
            'SELECT ha.`id_htl_amenity`, ha.`position`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_htl_amenity` = ha.`id_htl_amenity` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`parent_amenity_id` = 0
            ORDER BY ha.`position` ASC'
        );

        if ($parents) {
            foreach ($parents as $parent) {
                $result[$parent['id_htl_amenity']] = array(
                    'id'       => $parent['id_htl_amenity'],
                    'name'     => $parent['name'],
                    'position' => $parent['position'],
                    'children' => array(),
                );

                $children = Db::getInstance()->executeS(
                    'SELECT ha.`id_htl_amenity`, ha.`active`, ha.`is_featured`, ha.`logo_type`, ha.`logo`, hal.`name`
                    FROM `'._DB_PREFIX_.'htl_amenity` ha
                    LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                        ON (hal.`id_htl_amenity` = ha.`id_htl_amenity` AND hal.`id_lang` = '.(int)$idLang.')
                    WHERE ha.`parent_amenity_id` = '.(int)$parent['id_htl_amenity']
                );

                if ($children) {
                    // normalize: rename id_htl_amenity → id for template compatibility
                    foreach ($children as &$child) {
                        $child['id'] = $child['id_htl_amenity'];
                    }
                    $result[$parent['id_htl_amenity']]['children'] = $children;
                }
            }
        }

        return $result ?: false;
    }

    /**
     * Returns all amenity categories with children, marking which are selected for a hotel/room type.
     *
     * @param array $branchAmenities rows from htl_branch_amenity for a hotel
     * @param int   $idLang
     * @return array|false
     */
    public function hotelBranchSelectedAmenitiesArray($branchAmenities, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $result = array();
        $parents = Db::getInstance()->executeS(
            'SELECT ha.`id_htl_amenity`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_htl_amenity` = ha.`id_htl_amenity` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`parent_amenity_id` = 0'
        );

        if ($parents) {
            foreach ($parents as $parent) {
                $result[$parent['id_htl_amenity']]['name'] = $parent['name'];
                $children = Db::getInstance()->executeS(
                    'SELECT ha.`id_htl_amenity`, hal.`name`
                    FROM `'._DB_PREFIX_.'htl_amenity` ha
                    LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                        ON (hal.`id_htl_amenity` = ha.`id_htl_amenity` AND hal.`id_lang` = '.(int)$idLang.')
                    WHERE ha.`parent_amenity_id` = '.(int)$parent['id_htl_amenity']
                );

                if ($children) {
                    foreach ($children as $child) {
                        $selected = 0;
                        if ($branchAmenities) {
                            foreach ($branchAmenities as $row) {
                                if ($child['id_htl_amenity'] == $row['amenity_id']) {
                                    $selected = 1;
                                    break;
                                }
                            }
                        }
                        $child['id']       = $child['id_htl_amenity'];
                        $child['selected'] = $selected;
                        $result[$parent['id_htl_amenity']]['children'][] = $child;
                    }
                }
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
            'SELECT ha.`id_htl_amenity`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_htl_amenity` = ha.`id_htl_amenity` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`id_htl_amenity` = '.(int)$id
        );
    }

    /**
     * @param int $parentId
     * @return array|false
     */
    public function getChildAmenitiesByParentAmenityId($parentId)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_htl_amenity` FROM `'._DB_PREFIX_.'htl_amenity` WHERE `parent_amenity_id` = '.(int)$parentId
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
            'SELECT `id_htl_amenity` FROM `'._DB_PREFIX_.'htl_amenity`
            WHERE `parent_amenity_id` = '.(int)$idCategory.' OR `id_htl_amenity` = '.(int)$idCategory
        );

        if ($rows) {
            foreach ($rows as $row) {
                $objHotelAmenities = new HotelAmenities((int)$row['id_htl_amenity']);
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
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal ON hal.`id_htl_amenity` = ha.`id_htl_amenity`
            WHERE hal.`name` LIKE \'%'.pSQL($query).'%\'
            AND hal.`id_lang` = '.(int)$idLang
        );
    }

    /**
     * Return all amenity categories (parent_amenity_id = 0), ordered by position.
     *
     * @param int $idLang
     * @return array
     */
    public static function getCategories($idLang = 0): array
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT ha.`id_htl_amenity`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenity` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenity_lang` hal
                ON (hal.`id_htl_amenity` = ha.`id_htl_amenity` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`parent_amenity_id` = 0
            ORDER BY ha.`position` ASC'
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
