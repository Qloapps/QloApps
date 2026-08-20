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

class OrderTaxDetailCore extends ObjectModel
{
    const STATUS_NONE = 0;
    const STATUS_APPLIED = 1;
    const STATUS_EXEMPTED = 2;

    const APPLY_OK = 0;
    const APPLY_ERROR_RESTORE = 1;
    const APPLY_ERROR_REFUNDED = 3;

    const EXEMPT_OK = 0;
    const EXEMPT_ERROR_NO_RULE = 1;
    const EXEMPT_ERROR_SAVE = 2;
    const EXEMPT_ERROR_REFUNDED = 3;

    const SCOPE_ROOM = 0;
    const SCOPE_ROOM_STATUS = 1;
    const SCOPE_SERVICE = 2;

    const SCOPE_COLUMN_ROOM = 'id_htl_booking';
    const SCOPE_COLUMN_SERVICE = 'id_service_product_order_detail';

    public $id_order_tax_detail;
    public $id_order;
    public $id_order_detail;
    public $id_htl_booking;
    public $id_service_product_order_detail;
    public $id_tax;
    public $unit_amount;
    public $total_amount;
    public $date_add;

    public static $definition = array(
        'table' => 'order_tax_detail',
        'primary' => 'id_order_tax_detail',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_htl_booking' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_service_product_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'unit_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'total_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
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
            'SELECT 1 FROM `' . _DB_PREFIX_ . 'order_tax_exemption`
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
            'SELECT 1 FROM `' . _DB_PREFIX_ . 'order_tax_exemption`
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
            'DELETE FROM `' . _DB_PREFIX_ . 'order_tax_exemption`
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
        $cacheId = 'OrderTaxDetail::getAppliedTourismTaxTotals-' . $idOrder . '-' . $scope;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $result = array();
        if ($scope === self::SCOPE_SERVICE) {
            $sql = 'SELECT spod.`id_htl_booking_detail`, SUM(tt.`total_amount`) AS total_amount
                 FROM `' . _DB_PREFIX_ . 'order_tax_detail` tt
                 INNER JOIN `' . _DB_PREFIX_ . 'tax` t ON t.`id_tax` = tt.`id_tax` AND t.`is_tourism_tax` = 1
                 INNER JOIN `' . _DB_PREFIX_ . 'service_product_order_detail` spod
                    ON spod.`id_service_product_order_detail` = tt.`id_service_product_order_detail`
                 LEFT JOIN `' . _DB_PREFIX_ . 'order_tax_exemption` ex
                    ON (spod.`id_htl_booking_detail` != 0 AND ex.`id_htl_booking` = spod.`id_htl_booking_detail`)
                    OR (spod.`id_htl_booking_detail` = 0 AND ex.`id_service_product_order_detail` = tt.`id_service_product_order_detail` AND ex.`id_htl_booking` = 0)
                 WHERE tt.`id_order` = ' . $idOrder . '
                   AND spod.`id_htl_booking_detail` != 0
                   AND spod.`is_refunded` = 0
                   AND ex.`id_order_tax_exemption` IS NULL
                 GROUP BY spod.`id_htl_booking_detail`';
            $rows = Db::getInstance()->executeS($sql);
            foreach ((array) $rows as $row) {
                $result[(int) $row['id_htl_booking_detail']] = (float) $row['total_amount'];
            }
        } else {
            $rows = Db::getInstance()->executeS(
                'SELECT tt.`id_htl_booking`,
                        SUM(CASE WHEN ex.`id_order_tax_exemption` IS NULL THEN tt.`total_amount` ELSE 0 END) AS total_amount,
                        MAX(CASE WHEN ex.`id_order_tax_exemption` IS NULL THEN 1 ELSE 0 END) AS applied_count
                 FROM `' . _DB_PREFIX_ . 'order_tax_detail` tt
                 INNER JOIN `' . _DB_PREFIX_ . 'tax` t ON t.`id_tax` = tt.`id_tax` AND t.`is_tourism_tax` = 1
                 LEFT JOIN `' . _DB_PREFIX_ . 'htl_booking_detail` hbd
                    ON hbd.`id` = tt.`id_htl_booking`
                 LEFT JOIN `' . _DB_PREFIX_ . 'order_tax_exemption` ex
                    ON ex.`id_htl_booking` = tt.`id_htl_booking`
                 WHERE tt.`id_order` = ' . $idOrder . '
                   AND tt.`id_htl_booking` != 0
                   AND hbd.`is_refunded` = 0
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
     * Delete all snapshot rows for a scope.
     *
     * @param string $scopeColumn  self::SCOPE_COLUMN_ROOM or self::SCOPE_COLUMN_SERVICE
     * @param int    $scopeValue
     * @return bool
     */
    protected static function deleteForScope($scopeColumn, $scopeValue)
    {
        return Db::getInstance()->execute(
            'DELETE otd FROM `' . _DB_PREFIX_ . 'order_tax_detail` otd
             INNER JOIN `' . _DB_PREFIX_ . 'tax` t ON t.`id_tax` = otd.`id_tax` AND t.`is_tourism_tax` = 1
             WHERE otd.`' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue
        );
    }

    /**
     * Delete every row (VAT and tourism alike) for a scope — used only when the booking/service line
     * itself is being permanently removed, unlike deleteForScope() which must stay tourism-only since
     * it runs mid-lifecycle while VAT rows for the same scope still need to survive.
     *
     * @param string $scopeColumn  self::SCOPE_COLUMN_ROOM or self::SCOPE_COLUMN_SERVICE
     * @param int    $scopeValue
     * @return bool
     */
    protected static function deleteAllForScope($scopeColumn, $scopeValue)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'order_tax_detail`
             WHERE `' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue
        );
    }

    /**
     *
     * @param int $idOrderDetail
     * @param int $idHtlBooking                 0 for a service-line scope
     * @param int $idServiceProductOrderDetail   0 for a room-booking scope
     * @return bool
     */
    public static function updateVatScoping($idOrderDetail, $idHtlBooking, $idServiceProductOrderDetail)
    {
        return Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_tax_detail`
             SET `id_htl_booking` = ' . (int) $idHtlBooking . ',
                 `id_service_product_order_detail` = ' . (int) $idServiceProductOrderDetail . '
             WHERE `id_order_detail` = ' . (int) $idOrderDetail . '
               AND `id_htl_booking` = 0
               AND `id_service_product_order_detail` = 0'
        );
    }

    /**
     * Hard-delete every tax row — VAT and tourism alike — for a booking and its attached service
     * lines (room removed from an already-placed order), plus the exemption marker if one exists.
     *
     * @param int   $idHtlBooking
     * @param int[] $serviceLineIds  ServiceProductOrderDetail ids attached to this booking
     * @return void
     */
    public static function hardDeleteForBooking($idHtlBooking, array $serviceLineIds)
    {
        $idHtlBooking = (int) $idHtlBooking;
        self::deleteAllForScope(self::SCOPE_COLUMN_ROOM, $idHtlBooking);
        foreach ($serviceLineIds as $idServiceLine) {
            self::deleteAllForScope(self::SCOPE_COLUMN_SERVICE, (int) $idServiceLine);
        }
        self::deleteExemptionMarker($idHtlBooking, 0);
    }

    /**
     * Sum of total_amount for a booking/service-line scope (every row counts — exemption is never
     * stored on the row itself, it's derived from marker existence at read time).
     *
     * @param string $scopeColumn  self::SCOPE_COLUMN_ROOM or self::SCOPE_COLUMN_SERVICE
     * @param int    $scopeValue
     * @return float
     */
    protected static function getScopedTotal($scopeColumn, $scopeValue)
    {
        return (float) Db::getInstance()->getValue(
            'SELECT COALESCE(SUM(otd.`total_amount`), 0)
             FROM `' . _DB_PREFIX_ . 'order_tax_detail` otd
             INNER JOIN `' . _DB_PREFIX_ . 'tax` t ON t.`id_tax` = otd.`id_tax` AND t.`is_tourism_tax` = 1
             WHERE otd.`' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue
        );
    }

    /**
     * Whether tourism tax has never been computed for this scope — no rows exist yet, and none were
     * ever refunded either. True means a fresh compute (buildRoomTaxParams()/buildServiceLineTaxParams()
     * + saveTourismTaxFromParams()) is needed instead of restoring/reusing existing rows.
     *
     * @param string $scopeColumn  self::SCOPE_COLUMN_ROOM or self::SCOPE_COLUMN_SERVICE
     * @param int    $scopeValue
     * @return bool
     */
    protected static function hasNeverBeenComputed($scopeColumn, $scopeValue)
    {
        if (self::hasRefundedRowsForScope($scopeColumn, $scopeValue)) {
            return false;
        }

        return !(bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'order_tax_detail` otd
             INNER JOIN `' . _DB_PREFIX_ . 'tax` t ON t.`id_tax` = otd.`id_tax` AND t.`is_tourism_tax` = 1
             WHERE otd.`' . bqSQL($scopeColumn) . '` = ' . (int) $scopeValue
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
        $idServiceProductOrderDetail
    ) {
        $idTaxRulesGroup = (int) $idTaxRulesGroup;
        $idOrderDetail = (int) $idOrderDetail;
        $idOrder = (int) $idOrder;
        $idHtlBooking = (int) $idHtlBooking;
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        if (!Configuration::get('QLO_USE_TOURISM_TAX') || !$idTaxRulesGroup || !$idOrderDetail) {
            return;
        }

        if ($idServiceProductOrderDetail) {
            $serviceLine = new ServiceProductOrderDetail($idServiceProductOrderDetail);
            if (Validate::isLoadedObject($serviceLine) && $serviceLine->id_htl_booking_detail) {
                $roomBooking = new HotelBookingDetail((int) $serviceLine->id_htl_booking_detail);
                if (Validate::isLoadedObject($roomBooking) && !Product::getIdTourismTaxRulesGroupByIdProduct((int) $roomBooking->id_product)) {
                    $idTaxRulesGroup = 0;
                }
            }
        }

        $taxAmounts = array();
        if ($idTaxRulesGroup) {
            $taxCalculator = TaxManagerFactory::getManager($address, $idTaxRulesGroup)->getTaxCalculator();
            $taxAmounts = $taxCalculator->getTaxesAmount(
                $unitPriceTaxExcl,
                $checkInDate,
                $numNights,
                $numAdults,
                $childrenAges,
                $collectionType,
                $quantity,
                $idCurrency
            );
        }

        $scopeColumn = $idServiceProductOrderDetail ? self::SCOPE_COLUMN_SERVICE : self::SCOPE_COLUMN_ROOM;
        $scopeValue = $idServiceProductOrderDetail ?: $idHtlBooking;

        self::deleteForScope($scopeColumn, $scopeValue);

        foreach ($taxAmounts as $idTax => $rowAmount) {
            $rowAmount = (float) $rowAmount;

            $tourismTax = TaxConfiguration::getByTaxId((int) $idTax);
            $divisor = ($tourismTax && $tourismTax->per_night) ? max(1, (int) $numNights) : max(1, (int) $quantity);

            $taxRow = new OrderTaxDetail();
            $taxRow->id_order = $idOrder;
            $taxRow->id_order_detail = $idOrderDetail;
            $taxRow->id_htl_booking = $idHtlBooking;
            $taxRow->id_service_product_order_detail = $idServiceProductOrderDetail;
            $taxRow->id_tax = (int) $idTax;
            $taxRow->unit_amount = Tools::ps_round($rowAmount / $divisor, 6);
            $taxRow->total_amount = $rowAmount;
            $taxRow->add();
        }

        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_detail`
             SET `id_tourism_tax_rule_group` = ' . $idTaxRulesGroup . '
             WHERE `id_order_detail` = ' . $idOrderDetail
        );

        self::recomputeInclusivePrice($idOrderDetail, $idHtlBooking, $idServiceProductOrderDetail);
    }

    /**
     * Recompute order_detail.total_price_tax_incl and unit_price_tax_incl from the sum of VAT + tourism tax rows for a scope.
     *
     * @param int $idOrderDetail
     * @param int $idHtlBooking                 0 for a service-line scope (no room booking row)
     * @param int $idServiceProductOrderDetail  0 for a room-booking scope (no service line row)
     * @return bool
     */
    protected static function recomputeInclusivePrice($idOrderDetail, $idHtlBooking, $idServiceProductOrderDetail)
    {
        $idOrderDetail = (int) $idOrderDetail;
        $idHtlBooking = (int) $idHtlBooking;
        $idServiceProductOrderDetail = (int) $idServiceProductOrderDetail;
        $db = Db::getInstance();

        $orderDetailRow = $db->getRow(
            'SELECT `total_price_tax_excl`, `product_auto_add`, `product_price_addition_type`, `id_order`
             FROM `' . _DB_PREFIX_ . 'order_detail`
             WHERE `id_order_detail` = ' . $idOrderDetail
        );
        $totalPriceTaxExcl = (float) $orderDetailRow['total_price_tax_excl'];

        $phantomVat = 0.0;
        if ($idServiceProductOrderDetail
            && $orderDetailRow['product_auto_add']
            && (int) $orderDetailRow['product_price_addition_type'] === Product::PRICE_ADDITION_TYPE_WITH_ROOM
        ) {
            $serviceLine = new ServiceProductOrderDetail($idServiceProductOrderDetail);
            if (Validate::isLoadedObject($serviceLine) && $serviceLine->id_htl_booking_detail) {
                $roomBooking = new HotelBookingDetail((int) $serviceLine->id_htl_booking_detail);
                if (Validate::isLoadedObject($roomBooking)) {
                    $priceInfo = RoomTypeServiceProductPrice::getProductRoomTypePriceAndTax(
                        (int) $serviceLine->id_product,
                        (int) $roomBooking->id_product,
                        RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                    );
                    if ($priceInfo && isset($priceInfo['id_tax_rules_group'])) {
                        $order = new Order((int) $orderDetailRow['id_order']);
                        $hotelContext = TaxConfiguration::resolveHotelAddressAndCollectionType(
                            (int) $roomBooking->id_hotel,
                            new Address((int) $order->id_address_tax)
                        );
                        $address = $hotelContext['address'];
                        if (Validate::isLoadedObject($address)) {
                            $taxCalculator = TaxManagerFactory::getManager($address, (int) $priceInfo['id_tax_rules_group'])->getTaxCalculator();
                            $phantomVat = $taxCalculator->addTaxes($totalPriceTaxExcl) - $totalPriceTaxExcl;
                        }
                    }
                }
            }
        }

        $taxSum = (float) $db->getValue(
            'SELECT COALESCE(SUM(
                    CASE WHEN t.`is_tourism_tax` = 1 AND ex.`id_order_tax_exemption` IS NOT NULL THEN 0
                         ELSE ott.`total_amount`
                    END
                ), 0)
             FROM `' . _DB_PREFIX_ . 'order_tax_detail` ott
             INNER JOIN `' . _DB_PREFIX_ . 'tax` t ON t.`id_tax` = ott.`id_tax`
             LEFT JOIN `' . _DB_PREFIX_ . 'service_product_order_detail` spod
                ON spod.`id_service_product_order_detail` = ott.`id_service_product_order_detail`
             LEFT JOIN `' . _DB_PREFIX_ . 'order_tax_exemption` ex
                ON (ott.`id_htl_booking` != 0 AND ex.`id_htl_booking` = ott.`id_htl_booking`)
                OR (ott.`id_htl_booking` = 0 AND spod.`id_htl_booking_detail` != 0 AND ex.`id_htl_booking` = spod.`id_htl_booking_detail`)
                OR (ott.`id_htl_booking` = 0 AND (spod.`id_htl_booking_detail` = 0 OR spod.`id_htl_booking_detail` IS NULL) AND ex.`id_service_product_order_detail` = ott.`id_service_product_order_detail` AND ex.`id_htl_booking` = 0)
             WHERE ott.`id_order_detail` = ' . $idOrderDetail
        );

        $totalPriceTaxIncl = $totalPriceTaxExcl + $taxSum + $phantomVat;

        $result = $db->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_detail`
             SET `total_price_tax_incl` = ' . $totalPriceTaxIncl . ',
                 `unit_price_tax_incl` = COALESCE(' . $totalPriceTaxIncl . ' / NULLIF(`product_quantity`, 0), 0)
             WHERE `id_order_detail` = ' . $idOrderDetail
        );

        if ($idHtlBooking) {
            $result = $db->execute(
                'UPDATE `' . _DB_PREFIX_ . 'htl_booking_detail`
                 SET `total_price_tax_incl` = ' . $totalPriceTaxIncl . '
                 WHERE `id` = ' . $idHtlBooking
            ) && $result;
        }

        if ($idServiceProductOrderDetail) {
            $result = $db->execute(
                'UPDATE `' . _DB_PREFIX_ . 'service_product_order_detail`
                 SET `total_price_tax_incl` = ' . $totalPriceTaxIncl . ',
                     `unit_price_tax_incl` = COALESCE(' . $totalPriceTaxIncl . ' / NULLIF(`quantity`, 0), 0)
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
        $cacheId = 'OrderTaxDetail::getBreakdownForInvoice-' . $idOrder . '-' . $idLang;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $result = Db::getInstance()->executeS(
            'SELECT ott.`id_tax`, tl.`name` AS tax_name,
                    SUM(ott.`total_amount`) AS total_amount
             FROM `' . _DB_PREFIX_ . 'order_tax_detail` ott
             INNER JOIN `' . _DB_PREFIX_ . 'tax` t ON t.`id_tax` = ott.`id_tax` AND t.`is_tourism_tax` = 1
             LEFT JOIN `' . _DB_PREFIX_ . 'service_product_order_detail` spod
                ON spod.`id_service_product_order_detail` = ott.`id_service_product_order_detail`
             LEFT JOIN `' . _DB_PREFIX_ . 'htl_booking_detail` hbd
                ON hbd.`id` = ott.`id_htl_booking`
             LEFT JOIN `' . _DB_PREFIX_ . 'order_tax_exemption` ex
                ON (ott.`id_htl_booking` != 0 AND ex.`id_htl_booking` = ott.`id_htl_booking`)
                OR (ott.`id_htl_booking` = 0 AND spod.`id_htl_booking_detail` != 0 AND ex.`id_htl_booking` = spod.`id_htl_booking_detail`)
                OR (ott.`id_htl_booking` = 0 AND (spod.`id_htl_booking_detail` = 0 OR spod.`id_htl_booking_detail` IS NULL) AND ex.`id_service_product_order_detail` = ott.`id_service_product_order_detail` AND ex.`id_htl_booking` = 0)
             LEFT JOIN `' . _DB_PREFIX_ . 'tax_lang` tl
                ON tl.`id_tax` = ott.`id_tax` AND tl.`id_lang` = ' . $idLang . '
             WHERE ott.`id_order` = ' . $idOrder . '
               AND ((ott.`id_htl_booking` != 0 AND hbd.`is_refunded` = 0)
                    OR (ott.`id_htl_booking` = 0 AND spod.`is_refunded` = 0))
               AND ex.`id_order_tax_exemption` IS NULL
             GROUP BY ott.`id_tax`, tl.`name`'
        );
        Cache::store($cacheId, $result);

        return $result;
    }

    /**
     * Applied (non-exempted, non-refunded) tourism tax total per order_detail line. Same
     * exemption/refund-aware join as getBreakdownForInvoice(), grouped by id_order_detail instead of
     * id_tax.
     *
     * @param int $idOrder
     * @return array  [id_order_detail => total_amount]
     */
    public static function getAmountsByOrderDetail($idOrder)
    {
        $idOrder = (int) $idOrder;
        $cacheId = 'OrderTaxDetail::getAmountsByOrderDetail-' . $idOrder;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $rows = Db::getInstance()->executeS(
            'SELECT ott.`id_order_detail`, SUM(ott.`total_amount`) AS total_amount
             FROM `' . _DB_PREFIX_ . 'order_tax_detail` ott
             INNER JOIN `' . _DB_PREFIX_ . 'tax` t ON t.`id_tax` = ott.`id_tax` AND t.`is_tourism_tax` = 1
             LEFT JOIN `' . _DB_PREFIX_ . 'service_product_order_detail` spod
                ON spod.`id_service_product_order_detail` = ott.`id_service_product_order_detail`
             LEFT JOIN `' . _DB_PREFIX_ . 'htl_booking_detail` hbd
                ON hbd.`id` = ott.`id_htl_booking`
             LEFT JOIN `' . _DB_PREFIX_ . 'order_tax_exemption` ex
                ON (ott.`id_htl_booking` != 0 AND ex.`id_htl_booking` = ott.`id_htl_booking`)
                OR (ott.`id_htl_booking` = 0 AND spod.`id_htl_booking_detail` != 0 AND ex.`id_htl_booking` = spod.`id_htl_booking_detail`)
                OR (ott.`id_htl_booking` = 0 AND (spod.`id_htl_booking_detail` = 0 OR spod.`id_htl_booking_detail` IS NULL) AND ex.`id_service_product_order_detail` = ott.`id_service_product_order_detail` AND ex.`id_htl_booking` = 0)
             WHERE ott.`id_order` = ' . $idOrder . '
               AND ((ott.`id_htl_booking` != 0 AND hbd.`is_refunded` = 0)
                    OR (ott.`id_htl_booking` = 0 AND spod.`is_refunded` = 0))
               AND ex.`id_order_tax_exemption` IS NULL
             GROUP BY ott.`id_order_detail`'
        );

        $result = array();
        foreach ((array) $rows as $row) {
            $result[(int) $row['id_order_detail']] = (float) $row['total_amount'];
        }
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
     * Whether a scope is refunded — apply/exempt must not touch a cancelled booking/service line.
     * Reads the authoritative flag directly (htl_booking_detail/service_product_order_detail),
     * not a stored copy on order_tax_detail.
     *
     * @param string $scopeColumn  self::SCOPE_COLUMN_ROOM or self::SCOPE_COLUMN_SERVICE
     * @param int    $scopeValue
     * @return bool
     */
    protected static function hasRefundedRowsForScope($scopeColumn, $scopeValue)
    {
        $scopeValue = (int) $scopeValue;
        if ($scopeColumn === self::SCOPE_COLUMN_ROOM) {
            return (bool) Db::getInstance()->getValue(
                'SELECT `is_refunded` FROM `' . _DB_PREFIX_ . 'htl_booking_detail` WHERE `id` = ' . $scopeValue
            );
        }
        return (bool) Db::getInstance()->getValue(
            'SELECT `is_refunded` FROM `' . _DB_PREFIX_ . 'service_product_order_detail`
             WHERE `id_service_product_order_detail` = ' . $scopeValue
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
        $hotelContext = TaxConfiguration::resolveHotelAddressAndCollectionType($idHotel, new Address((int) $order->id_address_tax));
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
        $hotelContext = TaxConfiguration::resolveHotelAddressAndCollectionType($idHotel, new Address((int) $order->id_address_tax));
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
            $params['idServiceProductOrderDetail']
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
        if (self::hasRefundedRowsForScope(self::SCOPE_COLUMN_ROOM, $idHtlBooking)) {
            return self::EXEMPT_ERROR_REFUNDED;
        }

        $booking = new HotelBookingDetail($idHtlBooking);
        if (!Validate::isLoadedObject($booking)) {
            return self::EXEMPT_ERROR_NO_RULE;
        }

        $objOrderTaxExemption = new OrderTaxExemption();
        $objOrderTaxExemption->id_htl_booking = $idHtlBooking;
        $objOrderTaxExemption->id_service_product_order_detail = 0;
        $objOrderTaxExemption->id_order = (int) $booking->id_order;
        $objOrderTaxExemption->id_employee = $idEmployee ? (int) $idEmployee : (int) Context::getContext()->employee->id;
        $objOrderTaxExemption->note = $note;
        if (!$objOrderTaxExemption->add()) {
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
            if (self::hasRefundedRowsForScope(self::SCOPE_COLUMN_ROOM, $idHtlBooking)) {
                return self::APPLY_ERROR_REFUNDED;
            }
            if (!self::deleteExemptionMarker($idHtlBooking, 0)) {
                return self::APPLY_ERROR_RESTORE;
            }
        }

        if (self::hasNeverBeenComputed(self::SCOPE_COLUMN_ROOM, $idHtlBooking)) {
            $params = self::buildRoomTaxParams($idHtlBooking);
            if ($params) {
                $params['collectionType'] = HotelBranchInformation::TAX_COLLECTION_TYPE_ONLINE;
                self::saveTourismTaxFromParams($params);
                self::adjustOrderTotalsForNewRows(self::SCOPE_COLUMN_ROOM, $idHtlBooking, $params['idOrder']);
            }
        }

        foreach (ServiceProductOrderDetail::getActiveIdsByHtlBookingDetail($idHtlBooking) as $idServiceLine) {
            if (!self::hasNeverBeenComputed(self::SCOPE_COLUMN_SERVICE, $idServiceLine)) {
                continue;
            }
            $svcParams = self::buildServiceLineTaxParams($idServiceLine);
            if ($svcParams) {
                $svcParams['collectionType'] = HotelBranchInformation::TAX_COLLECTION_TYPE_ONLINE;
                self::saveTourismTaxFromParams($svcParams);
                self::adjustOrderTotalsForNewRows(self::SCOPE_COLUMN_SERVICE, $idServiceLine, $svcParams['idOrder']);
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
        if (self::hasRefundedRowsForScope(self::SCOPE_COLUMN_SERVICE, $idServiceProductOrderDetail)) {
            return self::EXEMPT_ERROR_REFUNDED;
        }

        $serviceLine = new ServiceProductOrderDetail($idServiceProductOrderDetail);
        if (!Validate::isLoadedObject($serviceLine) || $serviceLine->id_htl_booking_detail) {
            return self::EXEMPT_ERROR_NO_RULE;
        }

        $objOrderTaxExemption = new OrderTaxExemption();
        $objOrderTaxExemption->id_htl_booking = 0;
        $objOrderTaxExemption->id_service_product_order_detail = $idServiceProductOrderDetail;
        $objOrderTaxExemption->id_order = (int) $serviceLine->id_order;
        $objOrderTaxExemption->id_employee = $idEmployee ? (int) $idEmployee : (int) Context::getContext()->employee->id;
        $objOrderTaxExemption->note = $note;
        if (!$objOrderTaxExemption->add()) {
            return self::EXEMPT_ERROR_SAVE;
        }

        $total = self::getScopedTotal(self::SCOPE_COLUMN_SERVICE, $idServiceProductOrderDetail);
        if ($total) {
            self::recomputeInclusivePrice((int) $serviceLine->id_order_detail, 0, $idServiceProductOrderDetail);
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
            if (self::hasRefundedRowsForScope(self::SCOPE_COLUMN_SERVICE, $idServiceProductOrderDetail)) {
                return self::APPLY_ERROR_REFUNDED;
            }
            if (!self::deleteExemptionMarker(0, $idServiceProductOrderDetail)) {
                return self::APPLY_ERROR_RESTORE;
            }
        }

        if (self::hasNeverBeenComputed(self::SCOPE_COLUMN_SERVICE, $idServiceProductOrderDetail)) {
            $params = self::buildServiceLineTaxParams($idServiceProductOrderDetail);
            if ($params) {
                $params['collectionType'] = HotelBranchInformation::TAX_COLLECTION_TYPE_ONLINE;
                self::saveTourismTaxFromParams($params);
                self::adjustOrderTotalsForNewRows(self::SCOPE_COLUMN_SERVICE, $idServiceProductOrderDetail, $params['idOrder']);
            }
        }

        if ($wasExempted) {
            $total = self::getScopedTotal(self::SCOPE_COLUMN_SERVICE, $idServiceProductOrderDetail);
            if ($total) {
                $serviceLine = new ServiceProductOrderDetail($idServiceProductOrderDetail);
                if (Validate::isLoadedObject($serviceLine)) {
                    self::recomputeInclusivePrice((int) $serviceLine->id_order_detail, 0, $idServiceProductOrderDetail);
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
     * order time), fold the newly-applied total into the order's own totals — recomputeInclusivePrice()
     * (called inside saveTourismTax()) only syncs order_detail/htl_booking_detail/service_product_order_detail,
     * never orders/order_invoice.
     *
     * @param string $scopeColumn  self::SCOPE_COLUMN_ROOM or self::SCOPE_COLUMN_SERVICE
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

        $roomTotal = self::getScopedTotal(self::SCOPE_COLUMN_ROOM, $idHtlBooking);
        if ($roomTotal) {
            $prev = $nowIncluded ? 0.0 : $roomTotal;
            $new = $nowIncluded ? $roomTotal : 0.0;
            self::recomputeInclusivePrice((int) $booking->id_order_detail, $idHtlBooking, 0);
            $totalDelta += ($new - $prev);
        }

        foreach (ServiceProductOrderDetail::getActiveIdsByHtlBookingDetail($idHtlBooking) as $idServiceLine) {
            $serviceTotal = self::getScopedTotal(self::SCOPE_COLUMN_SERVICE, $idServiceLine);
            if (!$serviceTotal) {
                continue;
            }
            $serviceLine = new ServiceProductOrderDetail($idServiceLine);
            if (!Validate::isLoadedObject($serviceLine)) {
                continue;
            }
            $prev = $nowIncluded ? 0.0 : $serviceTotal;
            $new = $nowIncluded ? $serviceTotal : 0.0;
            self::recomputeInclusivePrice((int) $serviceLine->id_order_detail, 0, $idServiceLine);
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
             SET `total_paid_tax_incl` = GREATEST(0, `total_paid_tax_incl` + ' . $delta . '),
                 `total_paid_tax_excl` = GREATEST(0, `total_paid_tax_excl` + ' . $delta . '),
                 `total_paid` = GREATEST(0, `total_paid` + ' . $delta . ')
             WHERE `id_order` = ' . $idOrder
        );
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_invoice`
             SET `total_paid_tax_incl` = GREATEST(0, `total_paid_tax_incl` + ' . $delta . '),
                 `total_paid_tax_excl` = GREATEST(0, `total_paid_tax_excl` + ' . $delta . ')
             WHERE `id_order` = ' . $idOrder
        );
    }
}