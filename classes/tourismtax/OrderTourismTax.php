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

class OrderTourismTaxCore extends ObjectModel
{
    const STATUS_NONE = 0;
    const STATUS_APPLIED = 1;
    const STATUS_EXEMPTED = 2;

    const APPLY_OK = 0;
    const APPLY_ERROR_RESTORE = 1;
    const APPLY_ERROR_NO_RULE = 2;
    const APPLY_ERROR_REFUNDED = 3;

    const EXEMPT_OK = 0;
    const EXEMPT_ERROR_NO_RULE = 1;
    const EXEMPT_ERROR_SAVE = 2;
    const EXEMPT_ERROR_REFUNDED = 3;

    public $id_order_tourism_tax;
    public $id_order;
    public $id_order_detail;
    public $id_htl_booking;
    public $id_service_product_order_detail;
    public $id_hotel;
    public $id_tax;
    public $id_currency;
    public $num_nights;
    public $num_adults;
    public $total_amount;
    public $is_exempted;
    public $is_refunded;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'order_tourism_tax',
        'primary' => 'id_order_tourism_tax',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_htl_booking' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_service_product_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'num_nights' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'num_adults' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'total_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'is_exempted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_refunded' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Applied (non-exempted, non-refunded) tourism tax totals for every booking of an order, keyed by id_htl_booking — cached per order.
     *
     * @param int $idOrder
     * @return array  [id_htl_booking => float total_amount]
     */
    public static function getAppliedTotalsByBooking($idOrder)
    {
        $idOrder = (int) $idOrder;
        $cacheId = 'OrderTourismTax::getAppliedTotalsByBooking-' . $idOrder;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $rows = Db::getInstance()->executeS(
            'SELECT `id_htl_booking`, SUM(`total_amount`) AS total_amount
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `id_order` = ' . $idOrder . '
               AND `is_exempted` = 0
               AND `is_refunded` = 0
             GROUP BY `id_htl_booking`'
        );
        $result = array();
        if ($rows) {
            foreach ($rows as $row) {
                $result[(int) $row['id_htl_booking']] = (float) $row['total_amount'];
            }
        }
        Cache::store($cacheId, $result);

        return $result;
    }

    /**
     * Applied (non-exempted, non-refunded) tourism tax totals for every service line of an order, keyed by the room booking it's attached to — cached per order.
     *
     * @param int $idOrder
     * @return array  [id_htl_booking_detail => float total_amount]
     */
    public static function getAppliedServiceTotalsByBooking($idOrder)
    {
        $idOrder = (int) $idOrder;
        $cacheId = 'OrderTourismTax::getAppliedServiceTotalsByBooking-' . $idOrder;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $rows = Db::getInstance()->executeS(
            'SELECT spod.`id_htl_booking_detail`, SUM(tt.`total_amount`) AS total_amount
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax` tt
             INNER JOIN `' . _DB_PREFIX_ . 'service_product_order_detail` spod
                ON spod.`id_service_product_order_detail` = tt.`id_service_product_order_detail`
             WHERE tt.`id_order` = ' . $idOrder . '
               AND spod.`id_htl_booking_detail` != 0
               AND tt.`is_exempted` = 0
               AND tt.`is_refunded` = 0
             GROUP BY spod.`id_htl_booking_detail`'
        );
        $result = array();
        if ($rows) {
            foreach ($rows as $row) {
                $result[(int) $row['id_htl_booking_detail']] = (float) $row['total_amount'];
            }
        }
        Cache::store($cacheId, $result);

        return $result;
    }

    /**
     * Status + total (non-exempted, non-refunded) for every booking of an order in one query.
     *
     * @param int $idOrder
     * @return array  [id_htl_booking => ['status' => int, 'total' => float]]
     */
    public static function getStatusAndTotalByBooking($idOrder)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_htl_booking`,
                    SUM(CASE WHEN `is_exempted` = 0 THEN `total_amount` ELSE 0 END) AS total_amount,
                    SUM(CASE WHEN `is_exempted` = 0 THEN 1 ELSE 0 END) AS applied_count
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `id_order` = ' . (int) $idOrder . '
               AND `is_refunded` = 0
             GROUP BY `id_htl_booking`'
        );
        $result = array();
        if ($rows) {
            foreach ($rows as $row) {
                $result[(int) $row['id_htl_booking']] = array(
                    'status' => ((int) $row['applied_count'] > 0) ? self::STATUS_APPLIED : self::STATUS_EXEMPTED,
                    'total' => (float) $row['total_amount'],
                );
            }
        }
        return $result;
    }

    /**
     * Mark all rows for a booking as refunded (used on room cancellation).
     *
     * @param int $idHtlBooking
     * @return bool
     */
    public static function setRefundedByHtlBooking($idHtlBooking)
    {
        return self::setRefundedForScope('id_htl_booking', (int) $idHtlBooking);
    }

    /**
     * Mark all rows for a service-product order-detail line as refunded (used on service refund).
     *
     * @param int $idServiceProductOrderDetail
     * @return bool
     */
    public static function setRefundedByServiceProductOrderDetail($idServiceProductOrderDetail)
    {
        return self::setRefundedForScope('id_service_product_order_detail', (int) $idServiceProductOrderDetail);
    }

    /**
     * Shared core for setRefundedByHtlBooking()/setRefundedByServiceProductOrderDetail().
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @return bool
     */
    protected static function setRefundedForScope($scopeColumn, $scopeValue)
    {
        return Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_tourism_tax`
             SET `is_refunded` = 1, `date_upd` = NOW()
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue
        );
    }

    /**
     * Delete all snapshot rows for a scope.
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @return bool
     */
    protected static function deleteForScope($scopeColumn, $scopeValue)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue
        );
    }

    /**
     * Hard-delete every tourism tax row for a booking and its attached service lines (room removed from an already-placed order).
     *
     * @param int   $idHtlBooking
     * @param int[] $serviceLineIds  ServiceProductOrderDetail ids attached to this booking
     * @return void
     */
    public static function hardDeleteForBooking($idHtlBooking, array $serviceLineIds)
    {
        self::deleteForScope('id_htl_booking', (int) $idHtlBooking);
        foreach ($serviceLineIds as $idServiceLine) {
            self::deleteForScope('id_service_product_order_detail', (int) $idServiceLine);
        }
    }

    /**
     * Sum of applied (non-exempted) total_amount for a booking/service-line scope — exemption-aware.
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @return float
     */
    protected static function getScopedTotal($scopeColumn, $scopeValue)
    {
        return (float) Db::getInstance()->getValue(
            'SELECT COALESCE(SUM(CASE WHEN `is_exempted` = 0 THEN `total_amount` ELSE 0 END), 0)
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue
        );
    }

    /**
     * Whether any row currently exists for this scope with is_exempted = 1.
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @return bool
     */
    protected static function wasScopeExempted($scopeColumn, $scopeValue)
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue . ' AND `is_exempted` = 1'
        );
    }

    /**
     * Compute via TaxCalculator::getTourismTaxRows() and persist tourism tax for one room-booking or service-product line.
     *
     * @param int      $idTaxRulesGroup             Tourism TRG assigned to the product
     * @param Address  $address                     Jurisdiction address (the hotel's own, not the customer's)
     * @param float    $unitPriceTaxExcl             Room/service unit_price_tax_excl (% base and tier lookup)
     * @param string   $checkInDate                  'YYYY-MM-DD'
     * @param int      $numNights
     * @param int      $numAdults
     * @param int[]    $childrenAges                 Ages at check-in date
     * @param int      $idCurrency
     * @param int      $collectionType               HotelBranchInformation::tourism_tax_collection_type snapshot
     * @param int      $idLang
     * @param int      $quantity                     Line quantity multiplier. Rooms: always 1.
     * @param int      $idOrder
     * @param int      $idOrderDetail
     * @param int      $idHtlBooking                 0 for service lines
     * @param int      $idServiceProductOrderDetail  0 for room bookings
     * @param int      $idHotel
     * @param bool     $isManualApply                true only from the admin Apply-button flow
     * @return void
     */
    public static function saveTourismTax(
        $idTaxRulesGroup,
        Address $address,
        $unitPriceTaxExcl,
        $checkInDate,
        $numNights,
        $numAdults,
        array $childrenAges,
        $idCurrency,
        $collectionType,
        $idLang,
        $quantity,
        $idOrder,
        $idOrderDetail,
        $idHtlBooking,
        $idServiceProductOrderDetail,
        $idHotel,
        $isManualApply = false
    ) {
        $idTaxRulesGroup = (int) $idTaxRulesGroup;
        $idOrderDetail = (int) $idOrderDetail;
        if (!Configuration::get('QLO_USE_TOURISM_TAX') || !$idTaxRulesGroup || !$idOrderDetail) {
            return;
        }

        $taxCalculator = TaxManagerFactory::getManager($address, $idTaxRulesGroup)->getTaxCalculator();
        $rows = $taxCalculator->getTourismTaxRows(
            $unitPriceTaxExcl,
            $checkInDate,
            $numNights,
            $numAdults,
            $childrenAges,
            $idCurrency,
            $collectionType,
            $idLang,
            $quantity,
            $isManualApply
        )['rows'];

        $idOrder = (int) $idOrder;
        $idHtlBooking = (int) $idHtlBooking;
        $idHotel = (int) $idHotel;
        $idCurrency = (int) $idCurrency;
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        $scopeColumn = $idServiceProductOrderDetail ? 'id_service_product_order_detail' : 'id_htl_booking';
        $scopeValue = $idServiceProductOrderDetail ?: $idHtlBooking;

        $wasExempted = self::wasScopeExempted($scopeColumn, $scopeValue);
        $prevTotal = self::getScopedTotal($scopeColumn, $scopeValue);
        self::deleteForScope($scopeColumn, $scopeValue);

        $totalTourismTax = 0.0;
        foreach ($rows as $row) {
            $rowAmount = (float) $row['total_amount'];

            self::insertRow(
                $idOrder,
                $idOrderDetail,
                $idHtlBooking,
                $idServiceProductOrderDetail,
                $idHotel,
                (int) $row['id_tax'],
                $idCurrency,
                (int) $row['num_nights'],
                (int) $row['num_adults'],
                $rowAmount,
                $wasExempted ? 1 : 0
            );

            if (!$wasExempted) {
                $totalTourismTax += $rowAmount;
            }
        }

        self::adjustOrderDetailTotals($idOrderDetail, $prevTotal, $totalTourismTax);
    }

    /**
     * Insert one persisted tourism tax snapshot row for a matched tax on a booking/service line.
     *
     * @param int    $idOrder
     * @param int    $idOrderDetail
     * @param int    $idHtlBooking                    0 for service lines
     * @param int    $idServiceProductOrderDetail      0 for room bookings
     * @param int    $idHotel
     * @param int    $idTax
     * @param int    $idCurrency
     * @param int    $numNights
     * @param int    $numAdults
     * @param float  $totalAmount
     * @param int    $isExempted
     * @return bool
     */
    protected static function insertRow(
        $idOrder,
        $idOrderDetail,
        $idHtlBooking,
        $idServiceProductOrderDetail,
        $idHotel,
        $idTax,
        $idCurrency,
        $numNights,
        $numAdults,
        $totalAmount,
        $isExempted = 0
    ) {
        $row = new OrderTourismTax();
        $row->id_order = (int) $idOrder;
        $row->id_order_detail = (int) $idOrderDetail;
        $row->id_htl_booking = (int) $idHtlBooking;
        $row->id_service_product_order_detail = (int) $idServiceProductOrderDetail;
        $row->id_hotel = (int) $idHotel;
        $row->id_tax = (int) $idTax;
        $row->id_currency = (int) $idCurrency;
        $row->num_nights = (int) $numNights;
        $row->num_adults = (int) $numAdults;
        $row->total_amount = (float) $totalAmount;
        $row->is_exempted = (int) $isExempted;
        $row->is_refunded = 0;

        return $row->add();
    }

    /**
     * Delta-adjust order_detail's own tourism tax totals after (re)computing a booking/service line.
     *
     * @param int   $idOrderDetail
     * @param float $prevTotal  Previous exemption-aware total_amount sum for this scope
     * @param float $newTotal   New exemption-aware total_amount sum for this scope
     * @return bool
     */
    protected static function adjustOrderDetailTotals($idOrderDetail, $prevTotal, $newTotal)
    {
        $idOrderDetail = (int) $idOrderDetail;
        $prevTotal = (float) $prevTotal;
        $newTotal = (float) $newTotal;

        return Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_detail`
             SET `tourism_tax_amount` = GREATEST(0, `tourism_tax_amount`
                                         - ' . $prevTotal . '
                                         + ' . $newTotal . '),
                 `total_price_tax_incl` = `total_price_tax_incl`
                                         - ' . $prevTotal . '
                                         + ' . $newTotal . ',
                 `unit_price_tax_incl` = `unit_price_tax_incl`
                                         + COALESCE((' . $newTotal . ' - ' . $prevTotal . ') / NULLIF(`product_quantity`, 0), 0)
             WHERE `id_order_detail` = ' . $idOrderDetail
        );
    }

    /**
     * Breakdown for invoice/PDF — grouped by id_tax, excluding exempted or refunded rows, cached per order+lang.
     *
     * @param int $idOrder
     * @param int|null $idLang  defaults to the current context language
     * @return array
     */
    public static function getBreakdownForInvoice($idOrder, $idLang = null)
    {
        $idOrder = (int) $idOrder;
        $idLang = $idLang !== null ? (int) $idLang : (int) Context::getContext()->language->id;
        $cacheId = 'OrderTourismTax::getBreakdownForInvoice-' . $idOrder . '-' . $idLang;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $result = Db::getInstance()->executeS(
            'SELECT ott.`id_tax`, tl.`name` AS tax_name,
                    SUM(ott.`num_nights`) AS num_nights,
                    SUM(ott.`num_adults`) AS num_adults,
                    SUM(ott.`total_amount`) AS total_amount
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax` ott
             LEFT JOIN `' . _DB_PREFIX_ . 'tax_lang` tl
                ON tl.`id_tax` = ott.`id_tax` AND tl.`id_lang` = ' . $idLang . '
             WHERE ott.`id_order` = ' . $idOrder . '
               AND ott.`is_exempted` = 0
               AND ott.`is_refunded` = 0
             GROUP BY ott.`id_tax`, tl.`name`'
        );
        Cache::store($cacheId, $result);

        return $result;
    }

    /**
     * The single source of truth for an order's tourism tax total — applied, non-exempted, non-refunded.
     *
     * @param int $idOrder
     * @return float
     */
    public static function getOrderTourismTaxTotal($idOrder)
    {
        return (float) array_sum(array_column(self::getBreakdownForInvoice((int) $idOrder), 'total_amount'));
    }

    /**
     * Tourism tax status for a single booking — STATUS_NONE (no rows), STATUS_APPLIED (any non-exempted row), STATUS_EXEMPTED (all rows exempted).
     *
     * @param int $idHtlBooking
     * @return string
     */
    protected static function getStatusForBooking($idHtlBooking)
    {
        return self::getStatusForScope('id_htl_booking', (int) $idHtlBooking);
    }

    /**
     * Same state machine as getStatusForBooking(), scoped to a service product order-detail line.
     *
     * @param int $idServiceProductOrderDetail
     * @return string
     */
    protected static function getStatusForServiceLine($idServiceProductOrderDetail)
    {
        return self::getStatusForScope('id_service_product_order_detail', (int) $idServiceProductOrderDetail);
    }

    /**
     * Shared core for getStatusForBooking()/getStatusForServiceLine() — same STATUS_NONE/APPLIED/EXEMPTED state machine, only the scope column differs.
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @return string
     */
    protected static function getStatusForScope($scopeColumn, $scopeValue)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `is_exempted` FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue . '
               AND `is_refunded` = 0'
        );
        if (empty($rows)) {
            return self::STATUS_NONE;
        }
        foreach ($rows as $row) {
            if (!(int) $row['is_exempted']) {
                return self::STATUS_APPLIED;
            }
        }
        return self::STATUS_EXEMPTED;
    }

    /**
     * Mark all non-refunded rows for a booking as exempted.
     *
     * @param int         $idHtlBooking
     * @param int         $idEmployee  PS employee id (0 = resolved from Context)
     * @param string|null $note        Reason for exemption, entered by the employee
     * @return bool
     */
    protected static function exemptForBooking($idHtlBooking, $idEmployee = 0, $note = null)
    {
        return self::exemptForScope('id_htl_booking', (int) $idHtlBooking, $idEmployee, $note);
    }

    /**
     * Same as exemptForBooking(), scoped to a service product order-detail line.
     *
     * @param int         $idServiceProductOrderDetail
     * @param int         $idEmployee
     * @param string|null $note
     * @return bool
     */
    protected static function exemptForServiceLine($idServiceProductOrderDetail, $idEmployee = 0, $note = null)
    {
        return self::exemptForScope('id_service_product_order_detail', (int) $idServiceProductOrderDetail, $idEmployee, $note);
    }

    /**
     * Mark all non-refunded rows for a scope as exempted, writing one audit row per row exempted (not wrapped in a DB transaction).
     *
     * @param string      $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int         $scopeValue
     * @param int         $idEmployee   PS employee id (0 = resolved from Context)
     * @param string|null $note         Reason for exemption, entered by the employee
     * @return bool
     */
    protected static function exemptForScope($scopeColumn, $scopeValue, $idEmployee = 0, $note = null)
    {
        $scopeValue = (int) $scopeValue;
        $db = Db::getInstance();
        $rows = $db->executeS(
            'SELECT `id_order_tourism_tax`, `id_order`, `id_order_detail`, `id_htl_booking`,
                    `id_service_product_order_detail`, `total_amount`
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . $scopeValue . '
               AND `is_exempted` = 0
               AND `is_refunded` = 0'
        );

        if (empty($rows)) {
            return true;
        }

        $db->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_tourism_tax`
             SET `is_exempted` = 1, `date_upd` = NOW()
             WHERE `' . bqSQL($scopeColumn) . '` = ' . $scopeValue . '
               AND `is_refunded` = 0'
        );

        $idOrderDetail = (int) $rows[0]['id_order_detail'];
        $idOrder = (int) $rows[0]['id_order'];
        $totalExemptedAmount = 0.0;
        $idEmployeeResolved = $idEmployee ? (int) $idEmployee : (int) Context::getContext()->employee->id;

        foreach ($rows as $row) {
            $totalExemptedAmount += (float) $row['total_amount'];
            $exemption = new OrderTourismTaxExemption();
            $exemption->id_order_tourism_tax = (int) $row['id_order_tourism_tax'];
            $exemption->id_htl_booking = (int) $row['id_htl_booking'];
            $exemption->id_service_product_order_detail = (int) $row['id_service_product_order_detail'];
            $exemption->id_order = $idOrder;
            $exemption->id_employee = $idEmployeeResolved;
            $exemption->note = $note;
            if (!$exemption->add()) {
                PrestaShopLogger::addLog(
                    'OrderTourismTax::exemptForScope - Failed to save exemption audit row',
                    3,
                    null,
                    'HotelBookingDetail',
                    $scopeValue,
                    true
                );
                return false;
            }
        }

        self::adjustOrderDetailTotals($idOrderDetail, $totalExemptedAmount, 0.0);
        self::adjustOrderAndInvoiceTotals($idOrder, -$totalExemptedAmount);

        return true;
    }

    /**
     * Remove exemption for all rows of a booking (restore applied state).
     *
     * @param int $idHtlBooking
     * @return bool
     */
    protected static function unExemptForBooking($idHtlBooking)
    {
        return self::unExemptForScope('id_htl_booking', (int) $idHtlBooking);
    }

    /**
     * Same as unExemptForBooking(), scoped to a service product order-detail line.
     *
     * @param int $idServiceProductOrderDetail
     * @return bool
     */
    protected static function unExemptForServiceLine($idServiceProductOrderDetail)
    {
        return self::unExemptForScope('id_service_product_order_detail', (int) $idServiceProductOrderDetail);
    }

    /**
     * Remove exemption for all rows of a scope, recomputing tourism_tax_amount from every non-refunded, non-exempted row sharing the order_detail (not wrapped in a DB transaction).
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @return bool
     */
    protected static function unExemptForScope($scopeColumn, $scopeValue)
    {
        $scopeValue = (int) $scopeValue;
        $db = Db::getInstance();
        $rows = $db->executeS(
            'SELECT `id_order`, `id_order_detail`, `total_amount`
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . $scopeValue . '
               AND `is_exempted` = 1
               AND `is_refunded` = 0'
        );

        if (empty($rows)) {
            return true;
        }

        $db->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_tourism_tax`
             SET `is_exempted` = 0, `date_upd` = NOW()
             WHERE `' . bqSQL($scopeColumn) . '` = ' . $scopeValue . '
               AND `is_refunded` = 0'
        );

        $idOrderDetail = (int) $rows[0]['id_order_detail'];
        $idOrder = (int) $rows[0]['id_order'];
        $restoredAmount = 0.0;

        foreach ($rows as $row) {
            $restoredAmount += (float) $row['total_amount'];
        }

        $restored = (float) $db->getValue(
            'SELECT SUM(`total_amount`)
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `id_order_detail` = ' . $idOrderDetail . '
               AND `is_exempted` = 0
               AND `is_refunded` = 0'
        );

        $productQuantity = (int) $db->getValue(
            'SELECT `product_quantity` FROM `' . _DB_PREFIX_ . 'order_detail`
             WHERE `id_order_detail` = ' . $idOrderDetail
        );
        $unitDelta = $productQuantity > 0 ? ($restoredAmount / $productQuantity) : 0.0;

        $db->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_detail`
             SET `tourism_tax_amount` = ' . $restored . ',
                 `total_price_tax_incl` = `total_price_tax_incl` + ' . $restoredAmount . ',
                 `unit_price_tax_incl` = `unit_price_tax_incl` + ' . $unitDelta . '
             WHERE `id_order_detail` = ' . $idOrderDetail
        );

        self::adjustOrderAndInvoiceTotals($idOrder, $restoredAmount);

        return true;
    }

    /**
     * Whether a scope has any refunded tourism tax rows — apply/exempt must not touch a cancelled booking/service line.
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @return bool
     */
    protected static function hasRefundedRowsForScope($scopeColumn, $scopeValue)
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue . ' AND `is_refunded` = 1'
        );
    }

    /**
     * Resolve every parameter saveTourismTax() needs for an already-placed room booking, from the booking's own hotel (not the order's single id_address_tax column).
     *
     * @param int $idHtlBooking
     * @return array|false  Ordered args for self::saveTourismTax(), or false when the
     *                      booking/order/TRG/address can't be resolved
     */
    public static function buildRoomTaxParams($idHtlBooking)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $booking = new HotelBookingDetail($idHtlBooking);
        if (!Validate::isLoadedObject($booking)) {
            return false;
        }

        $order = new Order((int) $booking->id_order);
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        $idTaxRulesGroup = Product::getIdTourismTaxRulesGroupByIdProduct((int) $booking->id_product);
        if (!$idTaxRulesGroup) {
            return false;
        }

        $idHotel = (int) $booking->id_hotel;
        $hotelContext = TourismTax::resolveHotelAddressAndCollectionType($idHotel, new Address((int) $order->id_address_tax));
        $address = $hotelContext['address'];
        if (!Validate::isLoadedObject($address)) {
            return false;
        }
        $collectionType = $hotelContext['collectionType'];

        $numNights = (int) HotelHelper::getNumberOfDays($booking->date_from, $booking->date_to);
        if ($numNights <= 0) {
            return false;
        }

        $unitPriceTaxExcl = Tools::ps_round((float) $booking->total_price_tax_excl / $numNights, 6);

        $childrenAges = array();
        if ($booking->child_ages) {
            $decoded = json_decode($booking->child_ages, true);
            if (is_array($decoded)) {
                $childrenAges = array_map('intval', $decoded);
            }
        }

        return array(
            'idTaxRulesGroup' => $idTaxRulesGroup,
            'address' => $address,
            'unitPriceTaxExcl' => $unitPriceTaxExcl,
            'checkInDate' => $booking->date_from,
            'numNights' => $numNights,
            'numAdults' => (int) $booking->adults,
            'childrenAges' => $childrenAges,
            'idCurrency' => (int) $order->id_currency,
            'collectionType' => $collectionType,
            'idLang' => (int) Context::getContext()->language->id,
            'quantity' => 1,
            'idOrder' => (int) $booking->id_order,
            'idOrderDetail' => (int) $booking->id_order_detail,
            'idHtlBooking' => $idHtlBooking,
            'idServiceProductOrderDetail' => 0,
            'idHotel' => $idHotel,
        );
    }

    /**
     * Same as buildRoomTaxParams(), for an already-placed service product line.
     *
     * @param int $idServiceProductOrderDetail
     * @return array|false
     */
    public static function buildServiceLineTaxParams($idServiceProductOrderDetail)
    {
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        $serviceLine = new ServiceProductOrderDetail($idServiceProductOrderDetail);
        if (!Validate::isLoadedObject($serviceLine)) {
            return false;
        }

        $order = new Order((int) $serviceLine->id_order);
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        $idTaxRulesGroup = Product::getIdTourismTaxRulesGroupByIdProduct((int) $serviceLine->id_product);
        if (!$idTaxRulesGroup) {
            return false;
        }

        $idHotel = (int) $serviceLine->id_hotel;
        $hotelContext = TourismTax::resolveHotelAddressAndCollectionType($idHotel, new Address((int) $order->id_address_tax));
        $address = $hotelContext['address'];
        if (!Validate::isLoadedObject($address)) {
            return false;
        }
        $collectionType = $hotelContext['collectionType'];

        $idHtlBookingDetail = (int) $serviceLine->id_htl_booking_detail;
        $roomBooking = $idHtlBookingDetail ? new HotelBookingDetail($idHtlBookingDetail) : null;
        if ($roomBooking && Validate::isLoadedObject($roomBooking)) {
            $checkInDate = $roomBooking->date_from;
            $numNights = max(1, (int) HotelHelper::getNumberOfDays($roomBooking->date_from, $roomBooking->date_to));
            $numAdults = (int) $roomBooking->adults;
            $childrenAges = array();
            if ($roomBooking->child_ages) {
                $decoded = json_decode($roomBooking->child_ages, true);
                if (is_array($decoded)) {
                    $childrenAges = array_map('intval', $decoded);
                }
            }
        } else {
            $checkInDate = date('Y-m-d');
            $numNights = 1;
            $numAdults = 1;
            $childrenAges = array();
        }

        return array(
            'idTaxRulesGroup' => $idTaxRulesGroup,
            'address' => $address,
            'unitPriceTaxExcl' => (float) $serviceLine->unit_price_tax_excl,
            'checkInDate' => $checkInDate,
            'numNights' => $numNights,
            'numAdults' => $numAdults,
            'childrenAges' => $childrenAges,
            'idCurrency' => (int) $order->id_currency,
            'collectionType' => $collectionType,
            'idLang' => (int) Context::getContext()->language->id,
            'quantity' => (int) $serviceLine->quantity,
            'idOrder' => (int) $serviceLine->id_order,
            'idOrderDetail' => (int) $serviceLine->id_order_detail,
            'idHtlBooking' => 0,
            'idServiceProductOrderDetail' => $idServiceProductOrderDetail,
            'idHotel' => $idHotel,
        );
    }

    /**
     * Apply or restore tourism tax for a booking's room based on its current status (exempted → restore, none → compute fresh, applied → no-op).
     *
     * @param int $idHtlBooking
     * @return int APPLY_OK | APPLY_ERROR_RESTORE | APPLY_ERROR_NO_RULE | APPLY_ERROR_REFUNDED
     */
    protected static function applyForRoom($idHtlBooking)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $status = self::getStatusForBooking($idHtlBooking);

        if ($status === self::STATUS_EXEMPTED) {
            return self::unExemptForBooking($idHtlBooking) ? self::APPLY_OK : self::APPLY_ERROR_RESTORE;
        }

        if ($status === self::STATUS_NONE) {
            if (self::hasRefundedRowsForScope('id_htl_booking', $idHtlBooking)) {
                return self::APPLY_ERROR_REFUNDED;
            }

            $params = self::buildRoomTaxParams($idHtlBooking);
            if (!$params) {
                return self::APPLY_ERROR_NO_RULE;
            }
            self::saveTourismTax(
                $params['idTaxRulesGroup'],
                $params['address'],
                $params['unitPriceTaxExcl'],
                $params['checkInDate'],
                $params['numNights'],
                $params['numAdults'],
                $params['childrenAges'],
                $params['idCurrency'],
                $params['collectionType'],
                $params['idLang'],
                $params['quantity'],
                $params['idOrder'],
                $params['idOrderDetail'],
                $params['idHtlBooking'],
                $params['idServiceProductOrderDetail'],
                $params['idHotel'],
                true
            );
            self::adjustOrderTotalsForNewBooking($idHtlBooking);
        }

        return self::APPLY_OK;
    }

    /**
     * Same state machine as applyForRoom(), for a single service line.
     *
     * @param int $idServiceProductOrderDetail
     * @return int APPLY_OK | APPLY_ERROR_RESTORE | APPLY_ERROR_NO_RULE | APPLY_ERROR_REFUNDED
     */
    protected static function applyForServiceLine($idServiceProductOrderDetail)
    {
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        $status = self::getStatusForServiceLine($idServiceProductOrderDetail);

        if ($status === self::STATUS_EXEMPTED) {
            return self::unExemptForServiceLine($idServiceProductOrderDetail) ? self::APPLY_OK : self::APPLY_ERROR_RESTORE;
        }

        if ($status === self::STATUS_NONE) {
            if (self::hasRefundedRowsForScope('id_service_product_order_detail', $idServiceProductOrderDetail)) {
                return self::APPLY_ERROR_REFUNDED;
            }

            $params = self::buildServiceLineTaxParams($idServiceProductOrderDetail);
            if (!$params) {
                return self::APPLY_ERROR_NO_RULE;
            }
            self::saveTourismTax(
                $params['idTaxRulesGroup'],
                $params['address'],
                $params['unitPriceTaxExcl'],
                $params['checkInDate'],
                $params['numNights'],
                $params['numAdults'],
                $params['childrenAges'],
                $params['idCurrency'],
                $params['collectionType'],
                $params['idLang'],
                $params['quantity'],
                $params['idOrder'],
                $params['idOrderDetail'],
                $params['idHtlBooking'],
                $params['idServiceProductOrderDetail'],
                $params['idHotel'],
                true
            );
            self::adjustOrderTotalsForNewServiceLine($idServiceProductOrderDetail);
        }

        return self::APPLY_OK;
    }

    /**
     * Apply or restore tourism tax for a booking's room and each of its active service lines independently.
     *
     * @param int $idHtlBooking
     * @return array List of ['scope' => 'room', 'booking_id' => int, 'result' => int]
     *               or ['scope' => 'service', 'id_service_product_order_detail' => int, 'result' => int]
     */
    public static function applyForBooking($idHtlBooking)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $results = array(
            array('scope' => 'room', 'booking_id' => $idHtlBooking, 'result' => self::applyForRoom($idHtlBooking)),
        );

        foreach (ServiceProductOrderDetail::getActiveIdsByHtlBookingDetail($idHtlBooking) as $idServiceLine) {
            $results[] = array(
                'scope' => 'service',
                'id_service_product_order_detail' => $idServiceLine,
                'result' => self::applyForServiceLine($idServiceLine),
            );
        }

        return $results;
    }

    /**
     * Apply tourism tax for every room and service line of every booking in an order; returns only the failing target entries.
     *
     * @param int $idOrder
     * @return array
     */
    public static function applyForOrder($idOrder)
    {
        $failures = array();
        $objBookingDetail = new HotelBookingDetail();
        $bookings = $objBookingDetail->getBookingDataByOrderId((int) $idOrder);
        if ($bookings) {
            foreach ($bookings as $booking) {
                foreach (self::applyForBooking($booking['id']) as $targetResult) {
                    if ($targetResult['result'] !== self::APPLY_OK) {
                        $failures[] = $targetResult;
                    }
                }
            }
        }
        return $failures;
    }

    /**
     * Exempt tourism tax for a booking's room only if it is currently STATUS_APPLIED.
     *
     * @param int         $idHtlBooking
     * @param int         $idEmployee
     * @param string|null $note  Reason for exemption, entered by the employee
     * @return int EXEMPT_OK | EXEMPT_ERROR_NO_RULE | EXEMPT_ERROR_SAVE | EXEMPT_ERROR_REFUNDED
     */
    protected static function exemptIfAppliedForRoom($idHtlBooking, $idEmployee = 0, $note = null)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $status = self::getStatusForBooking($idHtlBooking);
        if ($status === self::STATUS_NONE) {
            return self::hasRefundedRowsForScope('id_htl_booking', $idHtlBooking) ? self::EXEMPT_ERROR_REFUNDED : self::EXEMPT_ERROR_NO_RULE;
        }
        if ($status === self::STATUS_APPLIED) {
            return self::exemptForBooking($idHtlBooking, $idEmployee, $note)
                ? self::EXEMPT_OK
                : self::EXEMPT_ERROR_SAVE;
        }
        return self::EXEMPT_OK;
    }

    /**
     * Same state machine as exemptIfAppliedForRoom(), for a single service line.
     *
     * @param int         $idServiceProductOrderDetail
     * @param int         $idEmployee
     * @param string|null $note
     * @return int EXEMPT_OK | EXEMPT_ERROR_NO_RULE | EXEMPT_ERROR_SAVE | EXEMPT_ERROR_REFUNDED
     */
    protected static function exemptIfAppliedForServiceLine($idServiceProductOrderDetail, $idEmployee = 0, $note = null)
    {
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        $status = self::getStatusForServiceLine($idServiceProductOrderDetail);
        if ($status === self::STATUS_NONE) {
            return self::hasRefundedRowsForScope('id_service_product_order_detail', $idServiceProductOrderDetail) ? self::EXEMPT_ERROR_REFUNDED : self::EXEMPT_ERROR_NO_RULE;
        }
        if ($status === self::STATUS_APPLIED) {
            return self::exemptForServiceLine($idServiceProductOrderDetail, $idEmployee, $note)
                ? self::EXEMPT_OK
                : self::EXEMPT_ERROR_SAVE;
        }
        return self::EXEMPT_OK;
    }

    /**
     * Exempt tourism tax for a booking's room and each of its active service lines — same one-call-multiple-targets shape as applyForBooking().
     *
     * @param int         $idHtlBooking
     * @param int         $idEmployee
     * @param string|null $note  Reason for exemption, applied to every target touched
     * @return array See applyForBooking() for the entry shape.
     */
    public static function exemptIfApplied($idHtlBooking, $idEmployee = 0, $note = null)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $results = array(
            array('scope' => 'room', 'booking_id' => $idHtlBooking, 'result' => self::exemptIfAppliedForRoom($idHtlBooking, $idEmployee, $note)),
        );

        foreach (ServiceProductOrderDetail::getActiveIdsByHtlBookingDetail($idHtlBooking) as $idServiceLine) {
            $results[] = array(
                'scope' => 'service',
                'id_service_product_order_detail' => $idServiceLine,
                'result' => self::exemptIfAppliedForServiceLine($idServiceLine, $idEmployee, $note),
            );
        }

        return $results;
    }

    /**
     * Exempt tourism tax for every room and service line of every booking in an order; returns only the failing target entries.
     *
     * @param int         $idOrder
     * @param int         $idEmployee
     * @param string|null $note  Reason for exemption, applied to every booking in the order
     * @return array
     */
    public static function exemptForOrder($idOrder, $idEmployee = 0, $note = null)
    {
        $failures = array();
        $objBookingDetail = new HotelBookingDetail();
        $bookings = $objBookingDetail->getBookingDataByOrderId((int) $idOrder);
        if ($bookings) {
            foreach ($bookings as $booking) {
                foreach (self::exemptIfApplied($booking['id'], $idEmployee, $note) as $targetResult) {
                    if ($targetResult['result'] !== self::EXEMPT_OK) {
                        $failures[] = $targetResult;
                    }
                }
            }
        }
        return $failures;
    }

    /**
     * After fresh-applying tourism tax to a STATUS_NONE booking, add the applied total to the order's own totals.
     *
     * @param int $idHtlBooking
     * @return void
     */
    protected static function adjustOrderTotalsForNewBooking($idHtlBooking)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $booking = new HotelBookingDetail($idHtlBooking);
        if (!Validate::isLoadedObject($booking)) {
            return;
        }
        self::adjustOrderTotalsForNewRows('id_htl_booking', $idHtlBooking, (int) $booking->id_order);
    }

    /**
     * Same as adjustOrderTotalsForNewBooking(), for a single service line.
     *
     * @param int $idServiceProductOrderDetail
     * @return void
     */
    protected static function adjustOrderTotalsForNewServiceLine($idServiceProductOrderDetail)
    {
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        $serviceLine = new ServiceProductOrderDetail($idServiceProductOrderDetail);
        if (!Validate::isLoadedObject($serviceLine)) {
            return;
        }
        self::adjustOrderTotalsForNewRows('id_service_product_order_detail', $idServiceProductOrderDetail, (int) $serviceLine->id_order);
    }

    /**
     * Shared core for adjustOrderTotalsForNewBooking()/adjustOrderTotalsForNewServiceLine().
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @param int    $idOrder
     * @return void
     */
    protected static function adjustOrderTotalsForNewRows($scopeColumn, $scopeValue, $idOrder)
    {
        $appliedTotal = (float) Db::getInstance()->getValue(
            'SELECT SUM(`total_amount`)
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue . '
               AND `is_exempted` = 0
               AND `is_refunded` = 0'
        );
        self::adjustOrderAndInvoiceTotals($idOrder, $appliedTotal);
    }

    /**
     * Apply an applied-tourism-tax delta to both the order's own totals and every invoice already generated for it.
     *
     * @param int   $idOrder
     * @param float $delta  Signed — positive when restoring/adding tax, negative when exempting it
     */
    protected static function adjustOrderAndInvoiceTotals($idOrder, $delta)
    {
        $delta = (float) $delta;
        if (!$delta) {
            return;
        }

        $idOrder = (int) $idOrder;
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'orders`
             SET `total_paid_tax_incl` = `total_paid_tax_incl` + ' . $delta . ',
                 `total_paid_tax_excl` = `total_paid_tax_excl` + ' . $delta . ',
                 `total_paid` = `total_paid` + ' . $delta . '
             WHERE `id_order` = ' . $idOrder
        );
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_invoice`
             SET `total_paid_tax_incl` = `total_paid_tax_incl` + ' . $delta . ',
                 `total_paid_tax_excl` = `total_paid_tax_excl` + ' . $delta . '
             WHERE `id_order` = ' . $idOrder
        );
    }
}
