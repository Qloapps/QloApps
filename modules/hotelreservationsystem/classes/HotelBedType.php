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

class HotelBedType extends ObjectModel
{
    public $length;
    public $width;
    public $name;

    public static $definition =array(
        'table' => 'htl_bed_type',
        'primary' => 'id_bed_type',
        'multilang' => true,
        'fields' => array(
            'length' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'width' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            // lang fields
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
        ),
    );

    public function getAllBedTypes($idLang)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $sql = 'SELECT hbt.*, hbtl.`name` FROM `'._DB_PREFIX_.$this->table.'` hbt
            LEFT JOIN `'._DB_PREFIX_.$this->table.'_lang` hbtl ON hbt.`id_bed_type` = hbtl.`id_bed_type`
            WHERE 1 AND hbtl.`id_lang` ='.(int) $idLang;

        return Db::getInstance()->executeS($sql);
    }

    public function delete()
    {
        $objHotelRoomTypeBedType = new HotelRoomTypeBedType();
        $objHotelRoomTypeBedType->deleteRoomTypeBedTypes($this->id);

        return parent::delete();
    }
}
