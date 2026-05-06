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
    public $parent_feature_id;
    public $position;
    public $active;
    public $is_featured;
    public $logo_type;
    public $logo;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_amenities',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'parent_feature_id' => array('type' => self::TYPE_INT, 'required' => true),
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
            Db::getInstance()->delete('htl_branch_amenities', '`amenity_id` = '.(int)$id);
            Db::getInstance()->delete('htl_room_type_amenities', '`amenity_id` = '.(int)$id);
            $imgFile = dirname(__FILE__).'/../views/img/hotel_amenities/'.(int)$id.'.jpg';
            if (file_exists($imgFile)) {
                unlink($imgFile);
            }
            return parent::delete();
        }
        return false;
    }

    /**
     * Return all categories (parent_feature_id = 0) with their child amenities.
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
            'SELECT ha.`id`, ha.`position`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenities` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenities_lang` hal
                ON (hal.`id` = ha.`id` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`parent_feature_id` = 0
            ORDER BY ha.`position` ASC'
        );

        if ($parents) {
            foreach ($parents as $parent) {
                $result[$parent['id']] = array(
                    'id'       => $parent['id'],
                    'name'     => $parent['name'],
                    'position' => $parent['position'],
                    'children' => array(),
                );

                $children = Db::getInstance()->executeS(
                    'SELECT ha.`id`, ha.`active`, ha.`logo_type`, ha.`logo`, hal.`name`
                    FROM `'._DB_PREFIX_.'htl_amenities` ha
                    LEFT JOIN `'._DB_PREFIX_.'htl_amenities_lang` hal
                        ON (hal.`id` = ha.`id` AND hal.`id_lang` = '.(int)$idLang.')
                    WHERE ha.`parent_feature_id` = '.(int)$parent['id']
                );

                if ($children) {
                    $result[$parent['id']]['children'] = $children;
                }
            }
        }

        return $result ?: false;
    }

    /**
     * Returns all amenity categories with children, marking which children are selected for a hotel.
     *
     * @param array $branchAmenities rows from htl_branch_amenities for a hotel
     * @param int   $idLang
     * @return array|false
     */
    public function HotelBranchSelectedFeaturesArray($branchAmenities, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $result = array();
        $parents = Db::getInstance()->executeS(
            'SELECT ha.`id`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenities` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenities_lang` hal
                ON (hal.`id` = ha.`id` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`parent_feature_id` = 0'
        );

        if ($parents) {
            foreach ($parents as $parent) {
                $result[$parent['id']]['name'] = $parent['name'];
                $children = Db::getInstance()->executeS(
                    'SELECT ha.`id`, hal.`name`
                    FROM `'._DB_PREFIX_.'htl_amenities` ha
                    LEFT JOIN `'._DB_PREFIX_.'htl_amenities_lang` hal
                        ON (hal.`id` = ha.`id` AND hal.`id_lang` = '.(int)$idLang.')
                    WHERE ha.`parent_feature_id` = '.(int)$parent['id']
                );

                if ($children) {
                    foreach ($children as $child) {
                        $selected = 0;
                        if ($branchAmenities) {
                            foreach ($branchAmenities as $row) {
                                if ($child['id'] == $row['amenity_id']) {
                                    $selected = 1;
                                    break;
                                }
                            }
                        }
                        $child['selected'] = $selected;
                        $result[$parent['id']]['children'][] = $child;
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
    public function getFeatureInfoById($id, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        return Db::getInstance()->getRow(
            'SELECT ha.`id`, hal.`name`
            FROM `'._DB_PREFIX_.'htl_amenities` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenities_lang` hal
                ON (hal.`id` = ha.`id` AND hal.`id_lang` = '.(int)$idLang.')
            WHERE ha.`id` = '.(int)$id
        );
    }

    /**
     * @param int $parentId
     * @return array|false
     */
    public function getChildFeaturesByParentFeatureId($parentId)
    {
        return Db::getInstance()->executeS(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_amenities` WHERE `parent_feature_id` = '.(int)$parentId
        );
    }

    /**
     * Delete a category and all its child amenities.
     *
     * @param int $deleteId
     * @return bool
     */
    public function deleteHotelFeatures($deleteId)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_amenities`
            WHERE `parent_feature_id` = '.(int)$deleteId.' OR `id` = '.(int)$deleteId
        );

        if ($rows) {
            foreach ($rows as $row) {
                $obj = new HotelAmenities((int)$row['id']);
                $obj->delete();
            }
        }

        return true;
    }

    /**
     * @param string $query
     * @param int    $idLang
     * @return array|false
     */
    public function searchByName($query, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT ha.*, hal.* FROM `'._DB_PREFIX_.'htl_amenities` ha
            LEFT JOIN `'._DB_PREFIX_.'htl_amenities_lang` hal ON hal.`id` = ha.`id`
            WHERE hal.`name` LIKE \'%'.pSQL($query).'%\'
            AND hal.`id_lang` = '.(int)$idLang
        );
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
