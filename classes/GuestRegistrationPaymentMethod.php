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

class GuestRegistrationPaymentMethodCore extends ObjectModel
{
    public $active = 1;
    public $name;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'guest_reg_payment_method',
        'primary' => 'id_guest_reg_payment_method',
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

    /**
     * @param int|null $active  1 = active only, 0 = inactive only, null = all
     * @param int      $idLang  0 falls back to current context language
     *
     * @return array|false
     */
    public static function getRegistrationPaymentMethods($active = null, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = (int)Context::getContext()->language->id;
        }

        $activeCondition = ($active !== null) ? ' AND a.`active` = '.(int)$active : '';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT a.`id_guest_reg_payment_method`, al.`name`
            FROM `'._DB_PREFIX_.'guest_reg_payment_method` a
            INNER JOIN `'._DB_PREFIX_.'guest_reg_payment_method_lang` al
                ON (a.`id_guest_reg_payment_method` = al.`id_guest_reg_payment_method`
                AND al.`id_lang` = '.(int)$idLang.')
            WHERE 1'.$activeCondition.'
            ORDER BY al.`name` ASC'
        );
    }

    /**
     * @return array  All rows with names for every installed language, for the admin management table.
     */
    public static function getRegistrationPaymentMethodRows(): array
    {
        $languages = Language::getLanguages(false);
        $rows = array();

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT a.`id_guest_reg_payment_method`, a.`active`, a.`date_add`, al.`id_lang`, al.`name`
            FROM `'._DB_PREFIX_.'guest_reg_payment_method` a
            LEFT JOIN `'._DB_PREFIX_.'guest_reg_payment_method_lang` al
                ON (a.`id_guest_reg_payment_method` = al.`id_guest_reg_payment_method`)
            ORDER BY a.`id_guest_reg_payment_method` ASC'
        );

        if ($result) {
            foreach ($result as $row) {
                $id = (int)$row['id_guest_reg_payment_method'];
                if (!isset($rows[$id])) {
                    $rows[$id] = array(
                        'id'       => $id,
                        'form_key' => $id,
                        'active'   => (int)$row['active'],
                        'date_add' => $row['date_add'],
                        'name'     => array(),
                    );
                    foreach ($languages as $language) {
                        $rows[$id]['name'][(int)$language['id_lang']] = '';
                    }
                }
                if ((int)$row['id_lang']) {
                    $rows[$id]['name'][(int)$row['id_lang']] = $row['name'];
                }
            }
        }

        return array_values($rows);
    }

    /**
     * @return array  All persisted IDs.
     */
    public static function getRegistrationPaymentMethodIds(): array
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `id_guest_reg_payment_method` FROM `'._DB_PREFIX_.'guest_reg_payment_method`'
        );

        return $result ? array_map('intval', array_column($result, 'id_guest_reg_payment_method')) : array();
    }
}
