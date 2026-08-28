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

class TaxChildRangeCore extends ObjectModel
{
    public $id_tax_child_range;
    public $id_tax;
    public $min_age;
    public $max_age;
    public $tax_value;

    public static $definition = array(
        'table' => 'tax_child_range',
        'primary' => 'id_tax_child_range',
        'fields' => array(
            'id_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'min_age' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'max_age' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'tax_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
        ),
    );

    /**
     * All child age ranges for a tax in entry order (id_tax_child_range order), cached per request.
     *
     * @param int $idTax
     * @return array
     */
    public static function getByTaxId($idTax)
    {
        $idTax = (int) $idTax;
        $cacheId = 'TaxChildRange::getByTaxId-' . $idTax;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $ranges = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'tax_child_range`
             WHERE `id_tax` = ' . $idTax . '
             ORDER BY `id_tax_child_range` ASC'
        );
        Cache::store($cacheId, $ranges);

        return $ranges;
    }

    /**
     * Replace all child range rows for $idTax with the submitted arrays, skipping entries where tax_value is empty.
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
            'DELETE FROM `' . _DB_PREFIX_ . 'tax_child_range`
             WHERE `id_tax` = ' . (int) $idTax
        );
        foreach ($values as $i => $value) {
            if ($value === '' || $value === false) {
                continue;
            }
            $range = new TaxChildRange();
            $range->id_tax = (int) $idTax;
            $range->min_age = isset($mins[$i]) ? (int) $mins[$i] : 0;
            $range->max_age = isset($maxs[$i]) ? (int) $maxs[$i] : 0;
            $range->tax_value = (float) $value;
            $range->add();
        }
    }

    /**
     * Sum per-band tax contributions for the given child ages — each child matched to the first qualifying
     * band (band value interpreted per $childCalcType); unmatched children pay the adult's own already-computed
     * per-unit tourism tax amount flatly, since they're not governed by any band.
     *
     * @param int    $idTax
     * @param int[]  $ages                     Ages at check-in date
     * @param int    $childCalcType            0=fixed, 1=percentage — governs matched-band contributions only
     * @param float  $unitTourismTaxAmountAdult Adult's already-computed per-unit tourism tax amount (tier-aware);
     * @param bool   $useRanges                false = skip band lookup, every child is treated as unmatched
     * @return array ['total' => float, 'count' => int]
     */
    public static function getChildContribution($idTax, array $ages, $childCalcType, $unitTourismTaxAmountAdult, $useRanges = true)
    {
        $childCalcType = (int) $childCalcType;
        $unitTourismTaxAmountAdult = (float) $unitTourismTaxAmountAdult;
        $ranges = $useRanges ? self::getByTaxId((int) $idTax) : array();
        $total = 0.0;
        $count = 0;
        foreach ($ages as $age) {
            $age = (int) $age;
            $matched = false;
            foreach ($ranges as $range) {
                $maxAge = (int) $range['max_age'];
                $inLower = ($age >= (int) $range['min_age']);
                $inUpper = ($maxAge == 0 || $age <= $maxAge);
                if ($inLower && $inUpper) {
                    $bandValue = (float) $range['tax_value'];
                    $total += ($childCalcType === TaxConfiguration::CALCULATION_TYPE_FIXED) ? $bandValue : ($unitTourismTaxAmountAdult * ($bandValue / 100));
                    $count++;
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $total += $unitTourismTaxAmountAdult;
                $count++;
            }
        }
        return array('total' => $total, 'count' => $count);
    }
}
