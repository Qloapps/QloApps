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
 * Age-band definitions for tourism taxes with has_child_rate = 1.
 * Child age is measured at check-in date.
 * All ranges for a tax share the parent's child_tax_value.
 */
class HotelTourismTaxChildRange extends ObjectModel
{
    public $id_child_range;
    public $id_tax;
    public $min_age;
    public $max_age;
    public $tax_value;
    public $position;

    public static $definition = array(
        'table' => 'htl_tourism_tax_child_range',
        'primary' => 'id_child_range',
        'fields' => array(
            'id_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'min_age' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'max_age' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'tax_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
        ),
    );

    /**
     * Return all child age ranges for a tax sorted by position.
     *
     * @param int $idTax
     * @return array
     */
    public static function getByTaxId($idTax)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'htl_tourism_tax_child_range`
             WHERE `id_tax` = ' . (int) $idTax . '
             ORDER BY `position` ASC, `min_age` ASC'
        );
    }

    /**
     * Replace all child range rows for $idTax with the submitted arrays.
     * Skips entries where tax_value is empty.
     *
     * @param int   $idTax
     * @param array $mins
     * @param array $maxs
     * @param array $values
     * @return void
     */
    public static function saveAll($idTax, array $mins, array $maxs, array $values)
    {
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'htl_tourism_tax_child_range`
             WHERE `id_tax` = ' . (int) $idTax
        );
        foreach ($values as $i => $value) {
            if ($value === '' || $value === false) {
                continue;
            }
            $range = new HotelTourismTaxChildRange();
            $range->id_tax = (int) $idTax;
            $range->min_age = isset($mins[$i]) ? (int) $mins[$i] : 0;
            $range->max_age = isset($maxs[$i]) ? (int) $maxs[$i] : 0;
            $range->tax_value = (float) $value;
            $range->position = (int) $i;
            $range->add();
        }
    }

    /**
     * Sum per-band tax contributions for the given child ages.
     * Each child is matched to the first qualifying band; unmatched children pay the adult rate
     * when $adultBaseValue is provided, otherwise they contribute 0.
     *
     * Returns array: ['total' => float, 'count' => int]
     *
     * @param int        $idTax
     * @param int[]      $ages         Ages at check-in date
     * @param int        $taxType      0=fixed, 1=percentage
     * @param float      $unitPrice    Room unit_price_tax_excl (used for percentage type)
     * @param float|null $adultBaseValue Adult tax_value (fixed amount or percentage); null = exempt unmatched children
     * @return array
     */
    public static function computeChildContribution($idTax, array $ages, $taxType, $unitPrice, $adultBaseValue = null)
    {
        $ranges = self::getByTaxId((int) $idTax);
        $total = 0.0;
        $count = 0;
        foreach ($ages as $age) {
            $matched = false;
            foreach ($ranges as $range) {
                $inLower = ((int) $age >= (int) $range['min_age']);
                $inUpper = ((int) $range['max_age'] == 0 || (int) $age <= (int) $range['max_age']);
                if ($inLower && $inUpper) {
                    $bandValue = (float) $range['tax_value'];
                    $total += ((int) $taxType === 0)
                        ? $bandValue
                        : ((float) $unitPrice * ($bandValue / 100));
                    $count++;
                    $matched = true;
                    break;
                }
            }
            if (!$matched && $adultBaseValue !== null) {
                $total += ((int) $taxType === 0)
                    ? (float) $adultBaseValue
                    : ((float) $unitPrice * ((float) $adultBaseValue / 100));
                $count++;
            }
        }
        return array('total' => $total, 'count' => $count);
    }

    /**
     * Check whether a new range [newMin, newMax] overlaps any existing range for the same tax.
     *
     * @param int $idTax
     * @param int $newMin
     * @param int $newMax
     * @param int $excludeId  Range id to exclude (for edit validation)
     * @return bool
     */
    public static function hasOverlap($idTax, $newMin, $newMax, $excludeId = 0)
    {
        $ranges = self::getByTaxId((int) $idTax);
        foreach ($ranges as $range) {
            if ($excludeId && (int) $range['id_child_range'] === (int) $excludeId) {
                continue;
            }
            $exMax = (int) $range['max_age'];
            $newMaxEff = ($newMax == 0) ? PHP_INT_MAX : (int) $newMax;
            $exMaxEff = ($exMax == 0) ? PHP_INT_MAX : $exMax;
            if ((int) $newMin <= $exMaxEff && $newMaxEff >= (int) $range['min_age']) {
                return true;
            }
        }
        return false;
    }
}
