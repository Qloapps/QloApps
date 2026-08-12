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

    const SCOPE_ROOM = 0;
    const SCOPE_ROOM_STATUS = 1;
    const SCOPE_SERVICE = 2;

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
            'is_refunded' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Whether a booking's tourism tax (room + every attached service) is currently excluded from totals.
     *
     * @param int $idHtlBooking
     * @return bool
     */
    public static function isBookingExempted($idHtlBooking)
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT 1 FROM `' . _DB_PREFIX_ . 'order_tourism_tax_exemption`
             WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
               AND `id_service_product_order_detail` = 0'
        );
    }

    /**
     * Whether a standalone (no room attachment) service line is currently excluded from totals.
     *
     * @param int $idServiceProductOrderDetail
     * @return bool
     */
    public static function isServiceLineExempted($idServiceProductOrderDetail)
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT 1 FROM `' . _DB_PREFIX_ . 'order_tourism_tax_exemption`
             WHERE `id_htl_booking` = 0
               AND `id_service_product_order_detail` = ' . (int) $idServiceProductOrderDetail
        );
    }

    /**
     * Delete the exemption marker for a booking or standalone service line — id_htl_booking=0 for the
     * latter, id_service_product_order_detail=0 for the former (their own two-shape storage key).
     * Kept as a shared helper since it's used 3 times (hardDeleteForBooking, applyBooking, applyServiceLine).
     *
     * @param int $idHtlBooking
     * @param int $idServiceProductOrderDetail
     * @return bool
     */
    protected static function deleteExemptionMarker($idHtlBooking, $idServiceProductOrderDetail)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'order_tourism_tax_exemption`
             WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
               AND `id_service_product_order_detail` = ' . (int) $idServiceProductOrderDetail
        );
    }

    /**
     * Applied (non-exempted, non-refunded) tourism tax totals for every booking of an order, keyed by id_htl_booking — cached per order.
     *
     * @param int $idOrder
     * @param int $scope self::SCOPE_ROOM (default)      — float per room, keyed by id_htl_booking (room's own tax only)
     *                    self::SCOPE_ROOM_STATUS         — ['status' => int, 'total' => float] per room, same keys
     *                    self::SCOPE_SERVICE             — float per room, keyed by the attached room's id_htl_booking
     * @return array  keyed as described above per $scope
     */
    public static function getAppliedTourismTaxTotals($idOrder, $scope = self::SCOPE_ROOM)
    {
        $idOrder = (int) $idOrder;
        $cacheId = 'OrderTourismTax::getAppliedTourismTaxTotals-' . $idOrder . '-' . $scope;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $result = array();
        if ($scope === self::SCOPE_SERVICE) {
            $sql = 'SELECT spod.`id_htl_booking_detail`, SUM(tt.`total_amount`) AS total_amount
                 FROM `' . _DB_PREFIX_ . 'order_tourism_tax` tt
                 INNER JOIN `' . _DB_PREFIX_ . 'service_product_order_detail` spod
                    ON spod.`id_service_product_order_detail` = tt.`id_service_product_order_detail`
                 LEFT JOIN `' . _DB_PREFIX_ . 'order_tourism_tax_exemption` ex
                    ON (spod.`id_htl_booking_detail` != 0 AND ex.`id_htl_booking` = spod.`id_htl_booking_detail`)
                    OR (spod.`id_htl_booking_detail` = 0 AND ex.`id_service_product_order_detail` = tt.`id_service_product_order_detail` AND ex.`id_htl_booking` = 0)
                 WHERE tt.`id_order` = ' . $idOrder . '
                   AND spod.`id_htl_booking_detail` != 0
                   AND tt.`is_refunded` = 0
                   AND ex.`id_exemption` IS NULL
                 GROUP BY spod.`id_htl_booking_detail`';
            $rows = Db::getInstance()->executeS($sql);
            foreach ((array) $rows as $row) {
                $result[(int) $row['id_htl_booking_detail']] = (float) $row['total_amount'];
            }
        } else {
            $rows = Db::getInstance()->executeS(
                'SELECT tt.`id_htl_booking`,
                        SUM(CASE WHEN ex.`id_exemption` IS NULL THEN tt.`total_amount` ELSE 0 END) AS total_amount,
                        MAX(CASE WHEN ex.`id_exemption` IS NULL THEN 1 ELSE 0 END) AS applied_count
                 FROM `' . _DB_PREFIX_ . 'order_tourism_tax` tt
                 LEFT JOIN `' . _DB_PREFIX_ . 'order_tourism_tax_exemption` ex
                    ON ex.`id_htl_booking` = tt.`id_htl_booking`
                 WHERE tt.`id_order` = ' . $idOrder . '
                   AND tt.`id_htl_booking` != 0
                   AND tt.`is_refunded` = 0
                 GROUP BY tt.`id_htl_booking`'
            );
            foreach ((array) $rows as $row) {
                if ($scope === self::SCOPE_ROOM_STATUS) {
                    $result[(int) $row['id_htl_booking']] = array(
                        'status' => ((int) $row['applied_count'] > 0) ? self::STATUS_APPLIED : self::STATUS_EXEMPTED,
                        'total' => (float) $row['total_amount'],
                    );
                } else {
                    $result[(int) $row['id_htl_booking']] = (float) $row['total_amount'];
                }
            }
        }
        Cache::store($cacheId, $result);

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
     * Hard-delete every tourism tax row for a booking and its attached service lines (room removed from an already-placed order),
     * plus the exemption marker if one exists.
     *
     * @param int   $idHtlBooking
     * @param int[] $serviceLineIds  ServiceProductOrderDetail ids attached to this booking
     * @return void
     */
    public static function hardDeleteForBooking($idHtlBooking, array $serviceLineIds)
    {
        $idHtlBooking = (int) $idHtlBooking;
        self::deleteForScope('id_htl_booking', $idHtlBooking);
        foreach ($serviceLineIds as $idServiceLine) {
            self::deleteForScope('id_service_product_order_detail', (int) $idServiceLine);
        }
        self::deleteExemptionMarker($idHtlBooking, 0);
    }

    /**
     * Sum of total_amount for a booking/service-line scope (every row counts — exemption is never
     * stored on the row itself, it's derived from marker existence at read time).
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @return float
     */
    protected static function getScopedTotal($scopeColumn, $scopeValue)
    {
        return (float) Db::getInstance()->getValue(
            'SELECT COALESCE(SUM(`total_amount`), 0)
             FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue
        );
    }

    /**
     * Whether tourism tax has never been computed for this scope — no rows exist yet, and none were
     * ever refunded either. True means a fresh compute (buildRoomTaxParams()/buildServiceLineTaxParams()
     * + saveTourismTaxFromParams()) is needed instead of restoring/reusing existing rows.
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @return bool
     */
    protected static function hasNeverBeenComputed($scopeColumn, $scopeValue)
    {
        if (self::hasRefundedRowsForScope($scopeColumn, $scopeValue)) {
            return false;
        }

        return !(bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'order_tourism_tax`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue
        );
    }

    /**
     * Whether a scope's tax is currently excluded from totals — resolves a service line to its parent
     * room's marker when attached, its own marker when standalone. Used by saveTourismTax() to decide
     * whether a freshly (re)computed amount should move the denormalized display totals.
     *
     * @param int $idHtlBooking                 0 for a service-line scope
     * @param int $idServiceProductOrderDetail   0 for a room-booking scope
     * @return bool
     */
    protected static function isScopeExempted($idHtlBooking, $idServiceProductOrderDetail)
    {
        if ($idHtlBooking) {
            return self::isBookingExempted($idHtlBooking);
        }
        if ($idServiceProductOrderDetail) {
            $serviceLine = new ServiceProductOrderDetail((int) $idServiceProductOrderDetail);
            if (Validate::isLoadedObject($serviceLine) && $serviceLine->id_htl_booking_detail) {
                return self::isBookingExempted((int) $serviceLine->id_htl_booking_detail);
            }
            return self::isServiceLineExempted($idServiceProductOrderDetail);
        }
        return false;
    }

    /**
     * Compute and save tourism tax for one room-booking or service-product line.
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
        $idHotel
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
            $quantity
        )['rows'];

        $idOrder = (int) $idOrder;
        $idHtlBooking = (int) $idHtlBooking;
        $idHotel = (int) $idHotel;
        $idCurrency = (int) $idCurrency;
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        $scopeColumn = $idServiceProductOrderDetail ? 'id_service_product_order_detail' : 'id_htl_booking';
        $scopeValue = $idServiceProductOrderDetail ?: $idHtlBooking;

        $prevTotal = self::getScopedTotal($scopeColumn, $scopeValue);
        self::deleteForScope($scopeColumn, $scopeValue);

        $totalTourismTax = 0.0;
        foreach ($rows as $row) {
            $rowAmount = (float) $row['total_amount'];

            $taxRow = new OrderTourismTax();
            $taxRow->id_order = $idOrder;
            $taxRow->id_order_detail = $idOrderDetail;
            $taxRow->id_htl_booking = $idHtlBooking;
            $taxRow->id_service_product_order_detail = $idServiceProductOrderDetail;
            $taxRow->id_hotel = $idHotel;
            $taxRow->id_tax = (int) $row['id_tax'];
            $taxRow->id_currency = $idCurrency;
            $taxRow->num_nights = (int) $row['num_nights'];
            $taxRow->num_adults = (int) $row['num_adults'];
            $taxRow->total_amount = $rowAmount;
            $taxRow->is_refunded = 0;
            $taxRow->add();

            $totalTourismTax += $rowAmount;
        }

        if (!self::isScopeExempted($idHtlBooking, $idServiceProductOrderDetail)) {
            self::adjustDependentTotals($idOrderDetail, $idHtlBooking, $idServiceProductOrderDetail, $prevTotal, $totalTourismTax);
        }
    }

    /**
     * Delta-adjust order_detail's (and, when relevant, htl_booking_detail's/service_product_order_detail's)
     * own denormalized tourism tax totals after (re)computing or including/excluding a booking/service line.
     *
     * @param int   $idOrderDetail
     * @param int   $idHtlBooking                 0 for a service-line scope (no room booking row)
     * @param int   $idServiceProductOrderDetail  0 for a room-booking scope (no service line row)
     * @param float $prevTotal  Previously-synced total_amount for this scope
     * @param float $newTotal   Newly-synced total_amount for this scope
     * @return bool
     */
    protected static function adjustDependentTotals($idOrderDetail, $idHtlBooking, $idServiceProductOrderDetail, $prevTotal, $newTotal)
    {
        $idOrderDetail = (int) $idOrderDetail;
        $idHtlBooking = (int) $idHtlBooking;
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        $prevTotal = (float) $prevTotal;
        $newTotal = (float) $newTotal;
        $db = Db::getInstance();

        $result = $db->execute(
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

        if ($idHtlBooking) {
            $result = $db->execute(
                'UPDATE `' . _DB_PREFIX_ . 'htl_booking_detail`
                 SET `total_price_tax_incl` = `total_price_tax_incl`
                                             - ' . $prevTotal . '
                                             + ' . $newTotal . '
                 WHERE `id` = ' . $idHtlBooking
            ) && $result;
        }

        if ($idServiceProductOrderDetail) {
            $result = $db->execute(
                'UPDATE `' . _DB_PREFIX_ . 'service_product_order_detail`
                 SET `total_price_tax_incl` = `total_price_tax_incl`
                                             - ' . $prevTotal . '
                                             + ' . $newTotal . ',
                     `unit_price_tax_incl` = `unit_price_tax_incl`
                                             + COALESCE((' . $newTotal . ' - ' . $prevTotal . ') / NULLIF(`quantity`, 0), 0)
                 WHERE `id_service_product_order_detail` = ' . $idServiceProductOrderDetail
            ) && $result;
        }

        return $result;
    }

    /**
     * Breakdown for invoice/PDF — grouped by id_tax, excluding exempted (via marker) or refunded rows, cached per order+lang.
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
             LEFT JOIN `' . _DB_PREFIX_ . 'service_product_order_detail` spod
                ON spod.`id_service_product_order_detail` = ott.`id_service_product_order_detail`
             LEFT JOIN `' . _DB_PREFIX_ . 'order_tourism_tax_exemption` ex
                ON (ott.`id_htl_booking` != 0 AND ex.`id_htl_booking` = ott.`id_htl_booking`)
                OR (ott.`id_htl_booking` = 0 AND spod.`id_htl_booking_detail` != 0 AND ex.`id_htl_booking` = spod.`id_htl_booking_detail`)
                OR (ott.`id_htl_booking` = 0 AND (spod.`id_htl_booking_detail` = 0 OR spod.`id_htl_booking_detail` IS NULL) AND ex.`id_service_product_order_detail` = ott.`id_service_product_order_detail` AND ex.`id_htl_booking` = 0)
             LEFT JOIN `' . _DB_PREFIX_ . 'tax_lang` tl
                ON tl.`id_tax` = ott.`id_tax` AND tl.`id_lang` = ' . $idLang . '
             WHERE ott.`id_order` = ' . $idOrder . '
               AND ott.`is_refunded` = 0
               AND ex.`id_exemption` IS NULL
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
     * Call saveTourismTax() with a buildRoomTaxParams()/buildServiceLineTaxParams() result.
     *
     * @param array $params
     * @return void
     */
    protected static function saveTourismTaxFromParams(array $params)
    {
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
            $params['idHotel']
        );
    }

    /**
     * Exempt a booking's tourism tax — one marker row covers the room and every service attached to
     * it. Idempotent: exempting an already-exempted booking is a no-op success.
     *
     * @param int         $idHtlBooking
     * @param int         $idEmployee   PS employee id (0 = resolved from Context)
     * @param string|null $note         Reason for exemption, entered by the employee
     * @return int EXEMPT_OK | EXEMPT_ERROR_NO_RULE | EXEMPT_ERROR_SAVE | EXEMPT_ERROR_REFUNDED
     */
    public static function exemptBooking($idHtlBooking, $idEmployee = 0, $note = null)
    {
        $idHtlBooking = (int) $idHtlBooking;
        if (self::isBookingExempted($idHtlBooking)) {
            return self::EXEMPT_OK;
        }
        if (self::hasRefundedRowsForScope('id_htl_booking', $idHtlBooking)) {
            return self::EXEMPT_ERROR_REFUNDED;
        }

        $booking = new HotelBookingDetail($idHtlBooking);
        if (!Validate::isLoadedObject($booking)) {
            return self::EXEMPT_ERROR_NO_RULE;
        }

        $objOrderTourismTaxExemption = new OrderTourismTaxExemption();
        $objOrderTourismTaxExemption->id_htl_booking = $idHtlBooking;
        $objOrderTourismTaxExemption->id_service_product_order_detail = 0;
        $objOrderTourismTaxExemption->id_order = (int) $booking->id_order;
        $objOrderTourismTaxExemption->id_employee = $idEmployee ? (int) $idEmployee : (int) Context::getContext()->employee->id;
        $objOrderTourismTaxExemption->note = $note;
        if (!$objOrderTourismTaxExemption->add()) {
            return self::EXEMPT_ERROR_SAVE;
        }

        self::syncDenormalizedTotalsForBooking($idHtlBooking, false);

        return self::EXEMPT_OK;
    }

    /**
     * Apply (or re-apply) tourism tax for a booking — deletes the exemption marker if one exists,
     * restoring the room + every attached service's tax into the denormalized display totals. If
     * nothing has ever been computed for the room and/or a given service line (e.g. config was off
     * at order time), computes it fresh instead.
     *
     * @param int $idHtlBooking
     * @return int APPLY_OK | APPLY_ERROR_RESTORE | APPLY_ERROR_REFUNDED
     */
    public static function applyBooking($idHtlBooking)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $wasExempted = self::isBookingExempted($idHtlBooking);

        if ($wasExempted) {
            if (self::hasRefundedRowsForScope('id_htl_booking', $idHtlBooking)) {
                return self::APPLY_ERROR_REFUNDED;
            }
            if (!self::deleteExemptionMarker($idHtlBooking, 0)) {
                return self::APPLY_ERROR_RESTORE;
            }
        }

        if (self::hasNeverBeenComputed('id_htl_booking', $idHtlBooking)) {
            $params = self::buildRoomTaxParams($idHtlBooking);
            if ($params) {
                self::saveTourismTaxFromParams($params);
                self::adjustOrderTotalsForNewRows('id_htl_booking', $idHtlBooking, $params['idOrder']);
            }
        }

        foreach (ServiceProductOrderDetail::getActiveIdsByHtlBookingDetail($idHtlBooking) as $idServiceLine) {
            if (!self::hasNeverBeenComputed('id_service_product_order_detail', $idServiceLine)) {
                continue;
            }
            $svcParams = self::buildServiceLineTaxParams($idServiceLine);
            if ($svcParams) {
                self::saveTourismTaxFromParams($svcParams);
                self::adjustOrderTotalsForNewRows('id_service_product_order_detail', $idServiceLine, $svcParams['idOrder']);
            }
        }

        if ($wasExempted) {
            self::syncDenormalizedTotalsForBooking($idHtlBooking, true);
        }

        return self::APPLY_OK;
    }

    /**
     * Exempt a single standalone (no room attachment) service line directly — the one case a
     * booking-level marker can't cover, since there's no booking to attach it to.
     *
     * @param int         $idServiceProductOrderDetail
     * @param int         $idEmployee
     * @param string|null $note
     * @return int EXEMPT_OK | EXEMPT_ERROR_NO_RULE | EXEMPT_ERROR_SAVE | EXEMPT_ERROR_REFUNDED
     */
    public static function exemptServiceLine($idServiceProductOrderDetail, $idEmployee = 0, $note = null)
    {
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        if (self::isServiceLineExempted($idServiceProductOrderDetail)) {
            return self::EXEMPT_OK;
        }
        if (self::hasRefundedRowsForScope('id_service_product_order_detail', $idServiceProductOrderDetail)) {
            return self::EXEMPT_ERROR_REFUNDED;
        }

        $serviceLine = new ServiceProductOrderDetail($idServiceProductOrderDetail);
        if (!Validate::isLoadedObject($serviceLine) || $serviceLine->id_htl_booking_detail) {
            // Room-attached lines must go through exemptBooking() instead.
            return self::EXEMPT_ERROR_NO_RULE;
        }

        $objOrderTourismTaxExemption = new OrderTourismTaxExemption();
        $objOrderTourismTaxExemption->id_htl_booking = 0;
        $objOrderTourismTaxExemption->id_service_product_order_detail = $idServiceProductOrderDetail;
        $objOrderTourismTaxExemption->id_order = (int) $serviceLine->id_order;
        $objOrderTourismTaxExemption->id_employee = $idEmployee ? (int) $idEmployee : (int) Context::getContext()->employee->id;
        $objOrderTourismTaxExemption->note = $note;
        if (!$objOrderTourismTaxExemption->add()) {
            return self::EXEMPT_ERROR_SAVE;
        }

        $total = self::getScopedTotal('id_service_product_order_detail', $idServiceProductOrderDetail);
        if ($total) {
            self::adjustDependentTotals((int) $serviceLine->id_order_detail, 0, $idServiceProductOrderDetail, $total, 0.0);
            self::adjustOrderAndInvoiceTotals((int) $serviceLine->id_order, -$total);
        }

        return self::EXEMPT_OK;
    }

    /**
     * Apply (or re-apply) tourism tax for a single standalone service line.
     *
     * @param int $idServiceProductOrderDetail
     * @return int APPLY_OK | APPLY_ERROR_RESTORE | APPLY_ERROR_REFUNDED
     */
    public static function applyServiceLine($idServiceProductOrderDetail)
    {
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        $wasExempted = self::isServiceLineExempted($idServiceProductOrderDetail);

        if ($wasExempted) {
            if (self::hasRefundedRowsForScope('id_service_product_order_detail', $idServiceProductOrderDetail)) {
                return self::APPLY_ERROR_REFUNDED;
            }
            if (!self::deleteExemptionMarker(0, $idServiceProductOrderDetail)) {
                return self::APPLY_ERROR_RESTORE;
            }
        }

        if (self::hasNeverBeenComputed('id_service_product_order_detail', $idServiceProductOrderDetail)) {
            $params = self::buildServiceLineTaxParams($idServiceProductOrderDetail);
            if ($params) {
                self::saveTourismTaxFromParams($params);
                self::adjustOrderTotalsForNewRows('id_service_product_order_detail', $idServiceProductOrderDetail, $params['idOrder']);
            }
        }

        if ($wasExempted) {
            $total = self::getScopedTotal('id_service_product_order_detail', $idServiceProductOrderDetail);
            if ($total) {
                $serviceLine = new ServiceProductOrderDetail($idServiceProductOrderDetail);
                if (Validate::isLoadedObject($serviceLine)) {
                    self::adjustDependentTotals((int) $serviceLine->id_order_detail, 0, $idServiceProductOrderDetail, 0.0, $total);
                    self::adjustOrderAndInvoiceTotals((int) $serviceLine->id_order, $total);
                }
            }
        }

        return self::APPLY_OK;
    }

    /**
     * Exempt tourism tax for every booking (room + attached services) of an order; returns only the failing entries.
     * Standalone service lines aren't part of any booking, so this doesn't reach them — same as before.
     *
     * @param int         $idOrder
     * @param int         $idEmployee
     * @param string|null $note  Reason for exemption, applied to every booking in the order
     * @return array  [{'id_htl_booking' => int, 'result' => int}, ...] — failures only
     */
    public static function exemptForOrder($idOrder, $idEmployee = 0, $note = null)
    {
        $failures = array();
        $objBookingDetail = new HotelBookingDetail();
        $bookings = $objBookingDetail->getBookingDataByOrderId((int) $idOrder);
        if ($bookings) {
            foreach ($bookings as $booking) {
                $result = self::exemptBooking($booking['id'], $idEmployee, $note);
                if ($result !== self::EXEMPT_OK) {
                    $failures[] = array('id_htl_booking' => (int) $booking['id'], 'result' => $result);
                }
            }
        }
        return $failures;
    }

    /**
     * Apply tourism tax for every booking of an order; returns only the failing entries.
     *
     * @param int $idOrder
     * @return array  [{'id_htl_booking' => int, 'result' => int}, ...] — failures only
     */
    public static function applyForOrder($idOrder)
    {
        $failures = array();
        $objBookingDetail = new HotelBookingDetail();
        $bookings = $objBookingDetail->getBookingDataByOrderId((int) $idOrder);
        if ($bookings) {
            foreach ($bookings as $booking) {
                $result = self::applyBooking($booking['id']);
                if ($result !== self::APPLY_OK) {
                    $failures[] = array('id_htl_booking' => (int) $booking['id'], 'result' => $result);
                }
            }
        }
        return $failures;
    }

    /**
     * After fresh-computing tourism tax for a scope that had no prior rows (e.g. config was off at
     * order time), fold the newly-applied total into the order's own totals — adjustDependentTotals()
     * (called inside saveTourismTax()) only syncs order_detail/htl_booking_detail/service_product_order_detail,
     * never orders/order_invoice.
     *
     * @param string $scopeColumn  'id_htl_booking' or 'id_service_product_order_detail'
     * @param int    $scopeValue
     * @param int    $idOrder
     * @return void
     */
    protected static function adjustOrderTotalsForNewRows($scopeColumn, $scopeValue, $idOrder)
    {
        $appliedTotal = self::getScopedTotal($scopeColumn, $scopeValue);
        self::adjustOrderAndInvoiceTotals($idOrder, $appliedTotal);
    }

    /**
     * Sum the room's own tax + every attached service line's tax and apply that whole delta once —
     * used on exemptBooking()/applyBooking() to move the booking's tourism tax in or out of the
     * denormalized display totals in one pass, rather than per-row.
     *
     * @param int  $idHtlBooking
     * @param bool $nowIncluded  true = restoring (apply), false = removing (exempt)
     * @return void
     */
    protected static function syncDenormalizedTotalsForBooking($idHtlBooking, $nowIncluded)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $booking = new HotelBookingDetail($idHtlBooking);
        if (!Validate::isLoadedObject($booking)) {
            return;
        }
        $totalDelta = 0.0;

        $roomTotal = self::getScopedTotal('id_htl_booking', $idHtlBooking);
        if ($roomTotal) {
            $prev = $nowIncluded ? 0.0 : $roomTotal;
            $new = $nowIncluded ? $roomTotal : 0.0;
            self::adjustDependentTotals((int) $booking->id_order_detail, $idHtlBooking, 0, $prev, $new);
            $totalDelta += ($new - $prev);
        }

        foreach (ServiceProductOrderDetail::getActiveIdsByHtlBookingDetail($idHtlBooking) as $idServiceLine) {
            $serviceTotal = self::getScopedTotal('id_service_product_order_detail', $idServiceLine);
            if (!$serviceTotal) {
                continue;
            }
            $serviceLine = new ServiceProductOrderDetail($idServiceLine);
            if (!Validate::isLoadedObject($serviceLine)) {
                continue;
            }
            $prev = $nowIncluded ? 0.0 : $serviceTotal;
            $new = $nowIncluded ? $serviceTotal : 0.0;
            self::adjustDependentTotals((int) $serviceLine->id_order_detail, 0, $idServiceLine, $prev, $new);
            $totalDelta += ($new - $prev);
        }

        self::adjustOrderAndInvoiceTotals((int) $booking->id_order, $totalDelta);
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