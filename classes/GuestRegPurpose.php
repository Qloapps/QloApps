<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class GuestRegPurposeCore extends ObjectModel
{
    public $active = 1;
    public $name;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'guest_reg_purpose',
        'primary' => 'id_guest_reg_purpose',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isCatalogName',
                'required' => true,
                'size' => 255,
            ),
        ),
    );

    public static function getActiveOptions($idLang)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT a.`id_guest_reg_purpose`, al.`name`
            FROM `'._DB_PREFIX_.'guest_reg_purpose` a
            INNER JOIN `'._DB_PREFIX_.'guest_reg_purpose_lang` al
                ON (a.`id_guest_reg_purpose` = al.`id_guest_reg_purpose`
                AND al.`id_lang` = '.(int) $idLang.')
            WHERE a.`active` = 1
            ORDER BY al.`name` ASC'
        );
    }
}
