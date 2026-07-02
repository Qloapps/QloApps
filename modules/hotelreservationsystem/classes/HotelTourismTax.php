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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Subtype table for ps_tax rows where is_tourism_tax = 1.
 * id_tax is both PK and FK to ps_tax — set $obj->id before calling add().
 */
class HotelTourismTax extends ObjectModel
{
    public $id_tax;
    public $tax_type;
    public $is_per_night;
    public $is_per_person;
    public $tax_value;
    public $is_tiered;
    public $has_child_rate;
    public $child_tax_value;
    public $valid_from;
    public $valid_to;
    public $is_special_days_exists;
    public $special_days;

    public static $definition = array(
        'table' => 'htl_tourism_tax',
        'primary' => 'id_tax',
        'fields' => array(
            'tax_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'is_per_night' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_per_person' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'tax_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'is_tiered' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'has_child_rate' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'child_tax_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'valid_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'valid_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'is_special_days_exists' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'special_days' => array('type' => self::TYPE_STRING),
        ),
    );

    /**
     * Override add() because id_tax is a FK-as-PK (not auto-increment).
     * PrestaShop's ObjectModel::add() never writes the primary key column, so
     * MySQL would default it to 0 in non-strict mode, creating phantom rows.
     *
     * @param bool $auto_date   ignored — table has no date_add/date_upd
     * @param bool $null_values ignored
     * @return bool
     */
    public function add($auto_date = true, $null_values = false)
    {
        if (!(int) $this->id) {
            return false;
        }
        $result = Db::getInstance()->insert(
            'htl_tourism_tax',
            array(
                'id_tax' => (int) $this->id,
                'tax_type' => (int) $this->tax_type,
                'is_per_night' => (int) $this->is_per_night,
                'is_per_person' => (int) $this->is_per_person,
                'tax_value' => (float) $this->tax_value,
                'is_tiered' => (int) $this->is_tiered,
                'has_child_rate' => (int) $this->has_child_rate,
                'child_tax_value' => (float) $this->child_tax_value,
                'valid_from' => $this->valid_from ? pSQL($this->valid_from) : null,
                'valid_to' => $this->valid_to ? pSQL($this->valid_to) : null,
                'is_special_days_exists' => (int) $this->is_special_days_exists,
                'special_days' => $this->special_days ? pSQL($this->special_days) : null,
            )
        );
        if ($result) {
            $this->id_tax = (int) $this->id;
        }
        return $result;
    }

    /**
     * @param int $idTax
     * @return HotelTourismTax|false
     */
    public static function getByTaxId($idTax)
    {
        $obj = new HotelTourismTax((int) $idTax);
        return Validate::isLoadedObject($obj) ? $obj : false;
    }

    /**
     * @param int $idTax
     * @return bool
     */
    public static function existsForTax($idTax)
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'htl_tourism_tax`
             WHERE `id_tax` = ' . (int) $idTax
        );
    }

    /**
     * Remove this tax from all tourism TRGs.
     * Returns the count of deleted tax_rule rows so the caller can flash a warning.
     *
     * @param int $idTax
     * @return int
     */
    public static function cleanFromTourismTrgs($idTax)
    {
        $count = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'tax_rule` tr
             INNER JOIN `' . _DB_PREFIX_ . 'tax_rules_group` trg
                 ON trg.`id_tax_rules_group` = tr.`id_tax_rules_group`
             WHERE tr.`id_tax` = ' . (int) $idTax . '
             AND trg.`is_tourism_tax_rule` = 1'
        );
        Db::getInstance()->execute(
            'DELETE tr FROM `' . _DB_PREFIX_ . 'tax_rule` tr
             INNER JOIN `' . _DB_PREFIX_ . 'tax_rules_group` trg
                 ON trg.`id_tax_rules_group` = tr.`id_tax_rules_group`
             WHERE tr.`id_tax` = ' . (int) $idTax . '
             AND trg.`is_tourism_tax_rule` = 1'
        );
        return $count;
    }

    /**
     * Remove this tax from all standard (non-tourism) TRGs when it is promoted to a tourism tax.
     * Symmetric counterpart of cleanFromTourismTrgs().
     * Returns the count of deleted tax_rule rows so the caller can flash a warning.
     *
     * @param int $idTax
     * @return int
     */
    public static function cleanFromVatTrgs($idTax)
    {
        $count = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'tax_rule` tr
             INNER JOIN `' . _DB_PREFIX_ . 'tax_rules_group` trg
                 ON trg.`id_tax_rules_group` = tr.`id_tax_rules_group`
             WHERE tr.`id_tax` = ' . (int) $idTax . '
             AND trg.`is_tourism_tax_rule` = 0'
        );
        Db::getInstance()->execute(
            'DELETE tr FROM `' . _DB_PREFIX_ . 'tax_rule` tr
             INNER JOIN `' . _DB_PREFIX_ . 'tax_rules_group` trg
                 ON trg.`id_tax_rules_group` = tr.`id_tax_rules_group`
             WHERE tr.`id_tax` = ' . (int) $idTax . '
             AND trg.`is_tourism_tax_rule` = 0'
        );
        return $count;
    }

    /**
     * Delete the subtype row and all related tiers/child-ranges.
     */
    public function delete()
    {
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'htl_tourism_tax_tier`
             WHERE `id_tax` = ' . (int) $this->id
        );
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'htl_tourism_tax_child_range`
             WHERE `id_tax` = ' . (int) $this->id
        );
        return parent::delete();
    }
}
