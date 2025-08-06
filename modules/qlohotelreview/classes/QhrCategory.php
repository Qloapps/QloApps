<?php
/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
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
