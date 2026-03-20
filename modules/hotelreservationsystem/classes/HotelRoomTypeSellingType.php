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

class HotelRoomTypeSellingType extends ObjectModel
{
    const HOTEL_PROPERTY_TYPE = 1;
    const ROOM_TYPE_OBJECT_SELLING_TYPE = 2;

    public $type;
    public $active;
    public $date_add;
    public $date_upd;
    public $name;

    public static $definition = array(
        'table' => 'htl_room_type_selling_type',
        'primary' => 'id_htl_room_type_selling_type',
        'multilang' => true,
        'fields' => array(
            'type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true),
        ),
    );

    public static function getRoomTypeSellingObjectTypes($idLang = null, $active = true)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $cache_key = 'HotelRoomTypeSellingType::getRoomTypeSellingObjectTypes'.$idLang.'_'.$active;
        if(!Cache::isStored($cache_key)){
            $sql = 'SELECT hrtst.`id_htl_room_type_selling_type` AS `id_room_type_selling_object_type`, hrtstl.`name`
                FROM `'._DB_PREFIX_.'htl_room_type_selling_type` hrtst
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_selling_type_lang` hrtstl
                ON (hrtst.`id_htl_room_type_selling_type` = hrtstl.`id_htl_room_type_selling_type`
                    AND hrtstl.`id_lang` = '.(int) $idLang.')
                WHERE hrtst.`type` = '.(int) self::ROOM_TYPE_OBJECT_SELLING_TYPE.
                ($active ? ' AND hrtst.`active` = 1' : '').'
                ORDER BY hrtstl.`name` ASC';

            $res =  Db::getInstance()->executeS($sql);
        }else {
            $res = Cache::retrieve($cache_key);
        }

        return $res;

    }

    public static function getHotelPropertyTypes($idLang = null, $active = true)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $cache_key = 'HotelRoomTypeSellingType::getHotelPropertyTypes'.$idLang.'_'.$active;
        if(!Cache::isStored($cache_key)){

            $sql = 'SELECT hrtst.`id_htl_room_type_selling_type` AS `id_property_type`, hrtstl.`name`
                FROM `'._DB_PREFIX_.'htl_room_type_selling_type` hrtst
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_selling_type_lang` hrtstl
                ON (hrtst.`id_htl_room_type_selling_type` = hrtstl.`id_htl_room_type_selling_type`
                    AND hrtstl.`id_lang` = '.(int) $idLang.')
                WHERE hrtst.`type` = '.(int) self::HOTEL_PROPERTY_TYPE.
                ($active ? ' AND hrtst.`active` = 1' : '').'
                ORDER BY hrtstl.`name` ASC';

            $res =  Db::getInstance()->executeS($sql);
            
        }else{
            $res = Cache::retrieve($cache_key);
        }
        
        return $res;

    }

}
