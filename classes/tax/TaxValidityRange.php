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

class TaxValidityRangeCore extends ObjectModel
{
    public $id_tax_validity_range;
    public $id_tax;
    public $valid_from;
    public $valid_to;

    public static $definition = array(
        'table' => 'tax_validity_range',
        'primary' => 'id_tax_validity_range',
        'fields' => array(
            'id_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'valid_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'allow_null' => true),
            'valid_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'allow_null' => true),
        ),
    );

    /**
     * All validity ranges for a tax in entry order (id_tax_validity_range order), cached per request.
     *
     * @param int $idTax
     * @return array
     */
    public static function getByTaxId($idTax)
    {
        $idTax = (int) $idTax;
        $cacheId = 'TaxValidityRange::getByTaxId-' . $idTax;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $ranges = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'tax_validity_range`
             WHERE `id_tax` = ' . $idTax . '
             ORDER BY `id_tax_validity_range` ASC'
        );
        Cache::store($cacheId, $ranges);

        return $ranges;
    }

    /**
     * Whether $date falls within any of the given ranges (OR across rows) — a blank bound on either
     * @param array    $ranges Rows as returned by getByTaxId()
     * @param DateTime $date
     * @return bool
     */
    public static function dateMatchesAnyRange(array $ranges, DateTime $date)
    {
        if (empty($ranges)) {
            return true;
        }
        foreach ($ranges as $range) {
            $from = (!empty($range['valid_from']) && $range['valid_from'] !== '0000-00-00') ? new DateTime($range['valid_from']) : null;
            $to = (!empty($range['valid_to']) && $range['valid_to'] !== '0000-00-00') ? new DateTime($range['valid_to']) : null;
            if ((!$from || $date >= $from) && (!$to || $date <= $to)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Replace all validity-range rows for $idTax with the submitted arrays, skipping rows where
     * @param int   $idTax
     * @param array $froms
     * @param array $tos
     * @return void
     */
    public static function saveAll($idTax, array $froms, array $tos)
    {
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'tax_validity_range`
             WHERE `id_tax` = ' . $idTax
        );
        foreach ($froms as $i => $from) {
            $to = isset($tos[$i]) ? $tos[$i] : '';
            $fromEmpty = ($from === '' || $from === false);
            $toEmpty = ($to === '' || $to === false);
            if ($fromEmpty && $toEmpty) {
                continue;
            }
            $range = new TaxValidityRange();
            $range->id_tax = $idTax;
            $range->valid_from = $fromEmpty ? null : $from;
            $range->valid_to = $toEmpty ? null : $to;
            $range->add();
        }
    }
}
