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
 * Tiered price brackets for tourism taxes with is_tiered = 1.
 * Boundary rule: min_amount <= unit_price_tax_excl < max_amount (max_amount=0 = open-ended).
 */
class HotelTourismTaxTier extends ObjectModel
{
    public $id_tier;
    public $id_tax;
    public $min_amount;
    public $max_amount;
    public $tax_value;
    public $position;

    public static $definition = array(
        'table' => 'htl_tourism_tax_tier',
        'primary' => 'id_tier',
        'fields' => array(
            'id_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'min_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'max_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'tax_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
        ),
    );

    /**
     * Return all tiers for a tax sorted by position.
     *
     * @param int $idTax
     * @return array
     */
    public static function getByTaxId($idTax)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'htl_tourism_tax_tier`
             WHERE `id_tax` = ' . (int) $idTax . '
             ORDER BY `position` ASC, `min_amount` ASC'
        );
    }

    /**
     * Find the matching tier for a given unit price.
     * Returns the tier row or false when no tier matches.
     *
     * @param int   $idTax
     * @param float $unitPrice
     * @return array|false
     */
    public static function matchTier($idTax, $unitPrice)
    {
        $tiers = self::getByTaxId((int) $idTax);
        foreach ($tiers as $tier) {
            $inLower = ((float) $unitPrice >= (float) $tier['min_amount']);
            $inUpper = ((float) $tier['max_amount'] == 0 || (float) $unitPrice < (float) $tier['max_amount']);
            if ($inLower && $inUpper) {
                return $tier;
            }
        }
        return false;
    }

    /**
     * Replace all tier rows for $idTax with the submitted arrays.
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
            'DELETE FROM `' . _DB_PREFIX_ . 'htl_tourism_tax_tier`
             WHERE `id_tax` = ' . (int) $idTax
        );
        foreach ($values as $i => $value) {
            if ($value === '' || $value === false) {
                continue;
            }
            $tier = new HotelTourismTaxTier();
            $tier->id_tax = (int) $idTax;
            $tier->min_amount = isset($mins[$i]) ? (float) $mins[$i] : 0;
            $tier->max_amount = isset($maxs[$i]) ? (float) $maxs[$i] : 0;
            $tier->tax_value = (float) $value;
            $tier->position = (int) $i;
            $tier->add();
        }
    }

    /**
     * Check whether a new tier [newMin, newMax) overlaps any existing tier for the same tax.
     *
     * @param int   $idTax
     * @param float $newMin
     * @param float $newMax  0 = open-ended
     * @param int   $excludeId  Tier id to exclude (for edit validation)
     * @return bool
     */
    public static function hasOverlap($idTax, $newMin, $newMax, $excludeId = 0)
    {
        $tiers = self::getByTaxId((int) $idTax);
        foreach ($tiers as $tier) {
            if ($excludeId && (int) $tier['id_tier'] === (int) $excludeId) {
                continue;
            }
            $exMin = (float) $tier['min_amount'];
            $exMax = (float) $tier['max_amount'];
            $newMaxEff = ((float) $newMax == 0) ? PHP_FLOAT_MAX : (float) $newMax;
            $exMaxEff = ($exMax == 0) ? PHP_FLOAT_MAX : $exMax;
            if ($newMin < $exMaxEff && $newMaxEff > $exMin) {
                return true;
            }
        }
        return false;
    }
}
