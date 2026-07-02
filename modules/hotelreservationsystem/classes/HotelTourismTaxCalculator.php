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
 * Sole computation class for tourism tax.
 * Must NOT be called after an order is confirmed — use htl_order_tourism_tax snapshot instead.
 */
class HotelTourismTaxCalculator
{
    /**
     * Compute tourism tax rows for a single room booking line.
     *
     * Returns an array of result rows — one entry per matched ps_tax.
     * Returns an empty array when the feature is disabled or no tax applies.
     *
     * @param int    $idTaxRulesGroup   Tourism TRG assigned to the room type product
     * @param Address $address          Hotel address used for geo-routing
     * @param float  $unitPriceTaxExcl  Room unit_price_tax_excl (used as % base and tier lookup)
     * @param string $checkInDate       'YYYY-MM-DD'
     * @param int    $numNights
     * @param int    $numAdults
     * @param array  $childrenAges      Integer ages at check-in date
     * @param int    $idCurrency        Cart currency for amount conversion
     * @param int    $collectionType    Snapshot of htl_branch_info.tourism_tax_collection_type
     * @param int    $idLang
     * @return array
     */
    public static function compute(
        $idTaxRulesGroup,
        Address $address,
        $unitPriceTaxExcl,
        $checkInDate,
        $numNights,
        $numAdults,
        array $childrenAges,
        $idCurrency,
        $collectionType,
        $idLang
    ) {
        if (!Configuration::get('QLO_USE_TOURISM_TAX') || !(int) $idTaxRulesGroup) {
            return array();
        }

        if ((int) $numNights <= 0) {
            return array();
        }

        $rows = self::resolveApplicableTaxes(
            (int) $idTaxRulesGroup,
            $address,
            (int) $idLang
        );

        if (empty($rows)) {
            return array();
        }

        $checkIn     = new DateTime($checkInDate);
        $isoDayIndex = (int) $checkIn->format('N') - 1;

        $results = array();

        foreach ($rows as $row) {
            $taxParams = $row['params'];

            if (!self::isValidForDate($taxParams, $checkInDate, $isoDayIndex)) {
                continue;
            }

            $isTiered = (bool) $taxParams['is_tiered'];
            $taxType = (int) $taxParams['tax_type'];
            $isPerNight = (bool) $taxParams['is_per_night'];
            $isPerPerson = (bool) $taxParams['is_per_person'];
            $hasChildRate = (bool) $taxParams['has_child_rate'];

            $baseValue = (float) $taxParams['tax_value'];
            if ($isTiered) {
                $tier = HotelTourismTaxTier::matchTier((int) $row['id_tax'], (float) $unitPriceTaxExcl);
                if (!$tier) {
                    continue;
                }
                $baseValue = (float) $tier['tax_value'];
            }

            $adultMultiplier = 1;
            if ($isPerNight) {
                $adultMultiplier *= (int) $numNights;
            }
            if ($isPerPerson) {
                $adultMultiplier *= (int) $numAdults;
            }

            if ($taxType === 0) {
                $unitAmountAdult = $baseValue;
                $totalAmountAdult = $baseValue * $adultMultiplier;
            } else {
                $unitAmountAdult = (float) $unitPriceTaxExcl * ($baseValue / 100);
                $totalAmountAdult = $unitAmountAdult * $adultMultiplier;
            }

            $unitAmountChild = 0.0;
            $totalAmountChild = 0.0;
            $numQualifyingChildren = 0;

            if ($hasChildRate && !empty($childrenAges)) {
                $contribution = HotelTourismTaxChildRange::computeChildContribution(
                    (int) $row['id_tax'],
                    $childrenAges,
                    $taxType,
                    (float) $unitPriceTaxExcl,
                    $baseValue
                );
                $numQualifyingChildren = (int) $contribution['count'];
                if ($numQualifyingChildren > 0) {
                    $nightMultiplier = $isPerNight ? (int) $numNights : 1;
                    $totalAmountChild = $contribution['total'] * $nightMultiplier;
                    $unitAmountChild = $contribution['total'] / $numQualifyingChildren;
                }
            }

            $unitAmountAdult = Tools::convertPrice($unitAmountAdult, $idCurrency);
            $unitAmountChild = Tools::convertPrice($unitAmountChild, $idCurrency);
            $totalAmountAdult = Tools::convertPrice($totalAmountAdult, $idCurrency);
            $totalAmountChild = Tools::convertPrice($totalAmountChild, $idCurrency);
            $totalAmount = round($totalAmountAdult + $totalAmountChild, 6);

            $results[] = array(
                'id_tax' => (int) $row['id_tax'],
                'tax_name' => (string) $row['tax_name'],
                'num_nights' => (int) $numNights,
                'num_adults' => (int) $numAdults,
                'total_amount' => $totalAmount,
                'collection_type' => (int) $collectionType,
            );
        }

        return $results;
    }

    /**
     * Compute and persist tourism tax rows for a confirmed order_detail line.
     * Runs inside a single DB transaction. Safe to call more than once (idempotent via DELETE+INSERT).
     *
     * Returns the sum of total_amount where collection_type=0 (for Cart::getOrderTotal).
     *
     * @param int    $idOrder
     * @param int    $idOrderDetail
     * @param int    $idHtlBooking
     * @param int    $idHotel
     * @param int    $idTaxRulesGroup
     * @param Address $address
     * @param float  $unitPriceTaxExcl
     * @param string $checkInDate
     * @param int    $numNights
     * @param int    $numAdults
     * @param array  $childrenAges
     * @param int    $idCurrency
     * @param int    $collectionType
     * @param int    $idLang
     * @return float  Online-charged tourism tax total for this order detail
     */
    public static function computeAndSave(
        $idOrder,
        $idOrderDetail,
        $idHtlBooking,
        $idHotel,
        $idTaxRulesGroup,
        Address $address,
        $unitPriceTaxExcl,
        $checkInDate,
        $numNights,
        $numAdults,
        array $childrenAges,
        $idCurrency,
        $collectionType,
        $idLang
    ) {
        $rows = self::compute(
            $idTaxRulesGroup,
            $address,
            $unitPriceTaxExcl,
            $checkInDate,
            $numNights,
            $numAdults,
            $childrenAges,
            $idCurrency,
            $collectionType,
            $idLang
        );

        $db = Db::getInstance();
        $db->execute('START TRANSACTION');

        try {
            // Capture per-booking previous totals before deleting rows.
            // Both queries scoped to id_htl_booking so multiple bookings sharing one
            // order_detail row accumulate independently and do not overwrite each other.
            $prevOnlineTotal = (float) $db->getValue(
                'SELECT COALESCE(SUM(`total_amount`), 0)
                 FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
                 WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
                   AND `collection_type` = 0
                   AND `is_exempted` = 0
                   AND `is_refunded` = 0'
            );
            $prevTotalTourismTax = (float) $db->getValue(
                'SELECT COALESCE(SUM(`total_amount`), 0)
                 FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
                 WHERE `id_htl_booking` = ' . (int) $idHtlBooking
            );

            HotelOrderTourismTax::deleteByHtlBooking((int) $idHtlBooking);

            $now = date('Y-m-d H:i:s');
            $totalTourismTax = 0.0;
            $newOnlineTotal = 0.0;
            foreach ($rows as $row) {
                $db->insert('htl_order_tourism_tax', array(
                    'id_order' => (int) $idOrder,
                    'id_order_detail' => (int) $idOrderDetail,
                    'id_htl_booking' => (int) $idHtlBooking,
                    'id_hotel' => (int) $idHotel,
                    'id_tax' => (int) $row['id_tax'],
                    'id_currency' => (int) $idCurrency,
                    'tax_name' => pSQL($row['tax_name']),
                    'num_nights' => (int) $row['num_nights'],
                    'num_adults' => (int) $row['num_adults'],
                    'total_amount' => (float) $row['total_amount'],
                    'collection_type' => (int) $row['collection_type'],
                    'is_exempted' => 0,
                    'is_refunded' => 0,
                    'date_add' => pSQL($now),
                    'date_upd' => pSQL($now),
                ));
                $totalTourismTax += (float) $row['total_amount'];
                if ((int) $row['collection_type'] === 0) {
                    $newOnlineTotal += (float) $row['total_amount'];
                }
            }

            // Delta-update both fields so multiple bookings on one order_detail accumulate.
            // tourism_tax_amount: subtract this booking's old total, add new total.
            // total_price_tax_incl: subtract old online total, add new online total.
            $db->execute(
                'UPDATE `' . _DB_PREFIX_ . 'order_detail`
                 SET `tourism_tax_amount`  = GREATEST(0, `tourism_tax_amount`
                                             - ' . (float) $prevTotalTourismTax . '
                                             + ' . (float) $totalTourismTax . '),
                     `total_price_tax_incl` = `total_price_tax_incl`
                                             - ' . (float) $prevOnlineTotal . '
                                             + ' . (float) $newOnlineTotal . '
                 WHERE `id_order_detail` = ' . (int) $idOrderDetail
            );

            $db->execute('COMMIT');
        } catch (Exception $e) {
            $db->execute('ROLLBACK');
            throw $e;
        }

        return $newOnlineTotal;
    }

    /**
     * Resolve the applicable ps_tax + htl_tourism_tax rows for a TRG and hotel address.
     * Follows the same geo-routing logic as TaxRulesTaxManagerCore::getTaxCalculator().
     * behavior=0 on the most specific row → stop after first match.
     * behavior=1 → accumulate all matched rows.
     *
     * @param int    $idTaxRulesGroup
     * @param Address $address
     * @param int    $idLang
     * @return array  Each entry: ['id_tax', 'tax_name', 'params' => htl_tourism_tax row, 'behavior']
     */
    protected static function resolveApplicableTaxes($idTaxRulesGroup, Address $address, $idLang)
    {
        $postcode = !empty($address->postcode) ? $address->postcode : '0';

        $taxRows = Db::getInstance()->executeS(
            'SELECT tr.`id_tax`, tr.`behavior`,
                    tl.`name` AS tax_name,
                    ht.*
             FROM `' . _DB_PREFIX_ . 'tax_rule` tr
             JOIN `' . _DB_PREFIX_ . 'tax` t
                ON t.`id_tax` = tr.`id_tax`
               AND t.`active` = 1
               AND t.`deleted` = 0
               AND t.`is_tourism_tax` = 1
             JOIN `' . _DB_PREFIX_ . 'tax_rules_group` trg
                ON trg.`id_tax_rules_group` = tr.`id_tax_rules_group`
               AND trg.`active` = 1
               AND trg.`is_tourism_tax_rule` = 1
             LEFT JOIN `' . _DB_PREFIX_ . 'tax_lang` tl
                ON tl.`id_tax` = t.`id_tax`
               AND tl.`id_lang` = ' . (int) $idLang . '
             JOIN `' . _DB_PREFIX_ . 'htl_tourism_tax` ht
                ON ht.`id_tax` = t.`id_tax`
             WHERE tr.`id_tax_rules_group` = ' . (int) $idTaxRulesGroup . '
               AND tr.`id_country` = ' . (int) $address->id_country . '
               AND tr.`id_state` IN (0, ' . (int) $address->id_state . ')
               AND tr.`id_tax` != 0
               AND (\'' . pSQL($postcode) . '\' BETWEEN tr.`zipcode_from` AND tr.`zipcode_to`
                    OR (tr.`zipcode_to` = 0 AND tr.`zipcode_from` IN (0, \'' . pSQL($postcode) . '\')))
             ORDER BY tr.`zipcode_from` DESC, tr.`zipcode_to` DESC,
                      tr.`id_state` DESC, tr.`id_country` DESC'
        );

        if (empty($taxRows)) {
            return array();
        }

        $resolved = array();
        $firstBehavior = null;

        foreach ($taxRows as $taxRow) {
            if ($firstBehavior === null) {
                $firstBehavior = (int) $taxRow['behavior'];
            }

            $resolved[] = array(
                'id_tax' => (int) $taxRow['id_tax'],
                'tax_name' => (string) ($taxRow['tax_name'] ?: ''),
                'params' => $taxRow,
                'behavior' => (int) $taxRow['behavior'],
            );

            if ($firstBehavior === 0) {
                break;
            }
        }

        return $resolved;
    }

    /**
     * Check whether a tourism tax is valid for the given check-in date.
     *
     * @param array  $params       htl_tourism_tax row
     * @param string $checkInDate  'YYYY-MM-DD'
     * @param int    $isoDayIndex  0=Mon…6=Sun (DateTime::format('N') - 1)
     * @return bool
     */
    protected static function isValidForDate(array $params, $checkInDate, $isoDayIndex)
    {
        $checkIn = new DateTime($checkInDate);

        if (!empty($params['valid_from']) && $params['valid_from'] !== '0000-00-00') {
            $from = new DateTime($params['valid_from']);
            if ($checkIn < $from) {
                return false;
            }
        }

        if (!empty($params['valid_to']) && $params['valid_to'] !== '0000-00-00') {
            $to = new DateTime($params['valid_to']);
            if ($checkIn > $to) {
                return false;
            }
        }

        if ((int) $params['is_special_days_exists']) {
            $dayNames = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
            $specialDays = json_decode($params['special_days'], true);
            if (!is_array($specialDays) || !in_array($dayNames[$isoDayIndex], $specialDays)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply (compute + save) tourism tax for a booking on an already-confirmed order.
     * Fetches all required parameters from the existing DB records.
     * Returns false when the booking has no tourism TRG assigned or data is missing.
     *
     * @param int $idHtlBooking
     * @return bool
     */
    public static function applyFromExistingBooking($idHtlBooking)
    {
        $booking = new HotelBookingDetail((int) $idHtlBooking);
        if (!Validate::isLoadedObject($booking)) {
            return false;
        }

        $order = new Order((int) $booking->id_order);
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        $idTaxRulesGroup = (int) Db::getInstance()->getValue(
            'SELECT `id_tourism_tax_rules_group`
             FROM `' . _DB_PREFIX_ . 'product_shop`
             WHERE `id_product` = ' . (int) $booking->id_product . '
               AND `id_shop` = ' . (int) $order->id_shop
        );
        if (!$idTaxRulesGroup) {
            return false;
        }

        $address = new Address((int) $order->id_address_tax);
        if (!Validate::isLoadedObject($address)) {
            return false;
        }

        $objBranch = new HotelBranchInformation((int) $booking->id_hotel);
        if (!Validate::isLoadedObject($objBranch)) {
            return false;
        }
        $collectionType = (int) $objBranch->tourism_tax_collection_type;

        $numNights = (int) HotelHelper::getNumberOfDays($booking->date_from, $booking->date_to);
        if ($numNights <= 0) {
            return false;
        }

        $unitPriceTaxExcl = round((float) $booking->total_price_tax_excl / $numNights, 6);

        $childrenAges = array();
        if ($booking->child_ages) {
            $decoded = json_decode($booking->child_ages, true);
            if (is_array($decoded)) {
                $childrenAges = array_map('intval', $decoded);
            }
        }

        self::computeAndSave(
            (int) $booking->id_order,
            (int) $booking->id_order_detail,
            (int) $idHtlBooking,
            (int) $booking->id_hotel,
            $idTaxRulesGroup,
            $address,
            $unitPriceTaxExcl,
            $booking->date_from,
            $numNights,
            (int) $booking->adults,
            $childrenAges,
            (int) $order->id_currency,
            $collectionType,
            (int) Context::getContext()->language->id
        );

        return true;
    }

    /**
     * Compute cart-time total for display (does not save).
     * Used by Cart::getOrderTotal() before order confirmation.
     *
     * @param int     $idTaxRulesGroup
     * @param Address $address
     * @param float   $unitPriceTaxExcl
     * @param string  $checkInDate
     * @param int     $numNights
     * @param int     $numAdults
     * @param array   $childrenAges
     * @param int     $idCurrency
     * @param int     $collectionType
     * @param int     $idLang
     * @return float  Online-charged total only
     */
    public static function computeOnlineTotal(
        $idTaxRulesGroup,
        Address $address,
        $unitPriceTaxExcl,
        $checkInDate,
        $numNights,
        $numAdults,
        array $childrenAges,
        $idCurrency,
        $collectionType,
        $idLang
    ) {
        if ((int) $collectionType !== 0) {
            return 0.0;
        }
        $rows = self::compute(
            $idTaxRulesGroup, $address, $unitPriceTaxExcl, $checkInDate,
            $numNights, $numAdults, $childrenAges, $idCurrency, $collectionType, $idLang
        );
        $total = 0.0;
        foreach ($rows as $row) {
            $total += (float) $row['total_amount'];
        }
        return $total;
    }
}
