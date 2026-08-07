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

class TourismTaxTierCore extends ObjectModel
{
    public $id_tier;
    public $id_tax;
    public $min_amount;
    public $max_amount;
    public $tax_value;

    public static $definition = array(
        'table' => 'tourism_tax_tier',
        'primary' => 'id_tier',
        'fields' => array(
            'id_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'min_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'max_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'tax_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
        ),
    );

    /**
     * All tiers for a tax in entry order (id_tier order), cached per request.
     *
     * @param int $idTax
     * @return array
     */
    public static function getByTaxId($idTax)
    {
        $idTax = (int) $idTax;
        $cacheId = 'TourismTaxTier::getByTaxId-' . $idTax;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $tiers = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'tourism_tax_tier`
             WHERE `id_tax` = ' . $idTax . '
             ORDER BY `id_tier` ASC'
        );
        Cache::store($cacheId, $tiers);

        return $tiers;
    }

    /**
     * Find the matching tier for a given unit price, or false when no tier matches.
     *
     * @param int   $idTax
     * @param float $unitPrice
     * @return array|false
     */
    public static function getMatchingTier($idTax, $unitPrice)
    {
        $idTax = (int) $idTax;
        $unitPrice = (float) $unitPrice;
        $tiers = self::getByTaxId($idTax);
        foreach ($tiers as $tier) {
            $maxAmount = (float) $tier['max_amount'];
            $inLower = ($unitPrice >= (float) $tier['min_amount']);
            $inUpper = ($maxAmount == 0 || $unitPrice < $maxAmount);
            if ($inLower && $inUpper) {
                return $tier;
            }
        }
        return false;
    }

    /**
     * Replace all tier rows for $idTax with the submitted arrays, skipping entries where tax_value is empty.
     *
     * @param int   $idTax
     * @param array $mins
     * @param array $maxs
     * @param array $values
     * @return void
     */
    public static function saveAll($idTax, $mins, $maxs, $values)
    {
        $idTax = (int) $idTax;
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'tourism_tax_tier`
             WHERE `id_tax` = ' . $idTax
        );
        foreach ($values as $i => $value) {
            if ($value === '' || $value === false) {
                continue;
            }
            $tier = new TourismTaxTier();
            $tier->id_tax = $idTax;
            $tier->min_amount = isset($mins[$i]) ? (float) $mins[$i] : 0;
            $tier->max_amount = isset($maxs[$i]) ? (float) $maxs[$i] : 0;
            $tier->tax_value = (float) $value;
            $tier->add();
        }
    }
}
