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

class RoomTypeSellingObjectCore extends ObjectModel
{
    public $active;
    public $date_add;
    public $date_upd;
    public $name;

    public static $definition = array(
        'table' => 'room_type_selling_object',
        'primary' => 'id_room_type_selling_object',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true),
        ),
    );

    public function delete()
    {
        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'product` SET `id_room_type_selling_object` = NULL
            WHERE `id_room_type_selling_object` = '.(int)$this->id
        );

        return parent::delete();
    }

    public static function getRoomTypeSellingObject($idLang = null, $active = true)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $cache_key = 'RoomTypeSellingObject::getRoomTypeSellingObject'.(int)$idLang.'_'.(int)$active;
        if (!Cache::isStored($cache_key)) {
            $sql = 'SELECT hrst.`id_room_type_selling_object` AS `id_room_type_selling_object`, hrstl.`name`
                FROM `'._DB_PREFIX_.'room_type_selling_object` hrst
                LEFT JOIN `'._DB_PREFIX_.'room_type_selling_object_lang` hrstl
                ON (hrst.`id_room_type_selling_object` = hrstl.`id_room_type_selling_object`
                    AND hrstl.`id_lang` = '.(int)$idLang.')'.
                ($active ? ' WHERE hrst.`active` = 1' : '').
                ' ORDER BY hrstl.`name` ASC';

            $res = Db::getInstance()->executeS($sql);
            Cache::store($cache_key, $res);
        }

        return Cache::retrieve($cache_key);
    }
}
