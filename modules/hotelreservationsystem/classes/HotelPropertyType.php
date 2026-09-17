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

class HotelPropertyType extends ObjectModel
{
    public $active;
    public $date_add;
    public $date_upd;
    public $name;

    public static $definition = array(
        'table' => 'htl_property_type',
        'primary' => 'id_htl_property_type',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true),
        ),
    );

    /**
     * Check if this property type is currently assigned to any property.
     *
     * @return bool
     */
    public function isUsed()
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `'._DB_PREFIX_.'htl_branch_info` WHERE `id_property_type` = '.(int) $this->id
        );
    }


    /**
     * Get the list of property types in the requested language.
     *
     * @param int|null $idLang
     * @param bool $active
     *
     * @return array
     */
    public static function getPropertyTypes($idLang = null, $active = true)
    {
        $idLang = $idLang ? (int) $idLang : (int) Context::getContext()->language->id;
        $active = (bool) $active;
        $cacheKey = 'HotelPropertyType::getPropertyTypes'.$idLang.'_'.(int) $active;

        if (!Cache::isStored($cacheKey)) {
            $sql = 'SELECT hpt.`id_htl_property_type` AS `id_property_type`, hptl.`name`
                FROM `'._DB_PREFIX_.'htl_property_type` hpt
                LEFT JOIN `'._DB_PREFIX_.'htl_property_type_lang` hptl
                    ON (hpt.`id_htl_property_type` = hptl.`id_htl_property_type`
                        AND hptl.`id_lang` = '.$idLang.')'.
                ($active ? ' WHERE hpt.`active` = 1' : '').
                ' ORDER BY hptl.`name` ASC';

            Cache::store($cacheKey, Db::getInstance()->executeS($sql));
        }

        return Cache::retrieve($cacheKey);
    }
}
