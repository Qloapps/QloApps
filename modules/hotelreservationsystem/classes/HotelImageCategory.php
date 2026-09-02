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

class HotelImageCategory extends ObjectModel
{
    public $id_htl_image_category;
    public $name;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_image_category',
        'primary' => 'id_htl_image_category',
        'multilang' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getImageCategories($idLang = null)
    {
        if (!$idLang) {
            $idLang = (int) Context::getContext()->language->id;
        }

        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');

        return Db::getInstance()->executeS(
            'SELECT hic.`id_htl_image_category`, COALESCE(hicl.`name`, hiclDefault.`name`) AS `name`
            FROM `'._DB_PREFIX_.'htl_image_category` hic
            LEFT JOIN `'._DB_PREFIX_.'htl_image_category_lang` hicl
                ON (hicl.`id_htl_image_category` = hic.`id_htl_image_category`
                AND hicl.`id_lang` = '.(int) $idLang.')
            LEFT JOIN `'._DB_PREFIX_.'htl_image_category_lang` hiclDefault
                ON (hiclDefault.`id_htl_image_category` = hic.`id_htl_image_category`
                AND hiclDefault.`id_lang` = '.(int) $defaultLangId.')
            WHERE COALESCE(hicl.`name`, hiclDefault.`name`) IS NOT NULL
            ORDER BY `name` ASC'
        );
    }
}
