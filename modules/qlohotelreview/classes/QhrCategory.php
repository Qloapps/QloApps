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

class QhrCategory extends ObjectModel
{
    public $id_category;
    public $name;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'qhr_category',
        'primary' => 'id_category',
        'multilang' => true,
        'fields' => array(
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'lang' => true,
                'required' => true,
                'size' => 128
            ),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getAll($active = true)
    {
        $id_lang = Context::getContext()->language->id;
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'qhr_category` qc
            LEFT JOIN `'._DB_PREFIX_.'qhr_category_lang` qcl
            ON qcl.`id_category` = qc.`id_category` AND qcl.`id_lang` = '.(int) $id_lang.'
            '.($active ? 'WHERE qc.`active` = 1' : '')
        );
    }
}
