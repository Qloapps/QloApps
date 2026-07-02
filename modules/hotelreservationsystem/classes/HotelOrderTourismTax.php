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
 * Snapshot of tourism tax computed at order creation.
 * Written once by HotelTourismTaxCalculator::computeAndSave().
 * Only is_exempted and is_refunded may change after creation.
 */
class HotelOrderTourismTax extends ObjectModel
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
    public $id_hotel;
    public $id_tax;
    public $id_currency;
    public $tax_name;
    public $num_nights;
    public $num_adults;
    public $total_amount;
    public $collection_type;
    public $is_exempted;
    public $is_refunded;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_order_tourism_tax',
        'primary' => 'id_order_tourism_tax',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_htl_booking' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'tax_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100, 'required' => true),
            'num_nights' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'num_adults' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'total_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'collection_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'is_exempted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'is_refunded' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * @param int $idOrderDetail
     * @return array
     */
    public static function getByOrderDetail($idOrderDetail)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_order_detail` = ' . (int) $idOrderDetail
        );
    }

    /**
     * @param int $idOrder
     * @return array
     */
    public static function getByOrder($idOrder)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_order` = ' . (int) $idOrder
        );
    }

    /**
     * Return online-collected, non-exempted, non-refunded tourism tax totals for all bookings
     * of an order, keyed by id_htl_booking. One query per order — avoids N+1 in the booking loop.
     *
     * @param int $idOrder
     * @return array  [id_htl_booking => float total_amount]
     */
    public static function getOnlineTotalsByBooking($idOrder)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_htl_booking`, SUM(`total_amount`) AS total_amount
             FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_order` = ' . (int) $idOrder . '
               AND `collection_type` = 0
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
        return $result;
    }

    /**
     * Sum of total_amount for online-collected (collection_type=0), non-exempted rows for an order.
     *
     * @param int $idOrder
     * @return float
     */
    public static function getOnlineTotalForOrder($idOrder)
    {
        $total = Db::getInstance()->getValue(
            'SELECT SUM(`total_amount`) FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_order` = ' . (int) $idOrder . '
               AND `collection_type` = 0
               AND `is_exempted` = 0
               AND `is_refunded` = 0'
        );
        return (float) $total;
    }

    /**
     * Mark all rows for a booking as refunded (used on cancellation).
     *
     * @param int $idHtlBooking
     * @return bool
     */
    public static function setRefunded($idHtlBooking)
    {
        return Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             SET `is_refunded` = 1, `date_upd` = NOW()
             WHERE `id_htl_booking` = ' . (int) $idHtlBooking
        );
    }

    /**
     * Delete all snapshot rows for an order_detail.
     *
     * @param int $idOrderDetail
     * @return bool
     */
    public static function deleteByOrderDetail($idOrderDetail)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_order_detail` = ' . (int) $idOrderDetail
        );
    }

    /**
     * Delete all snapshot rows for a single booking.
     * Used by computeAndSave idempotency step — scoped to id_htl_booking so that
     * multiple bookings sharing one order_detail do not erase each other.
     *
     * @param int $idHtlBooking
     * @return bool
     */
    public static function deleteByHtlBooking($idHtlBooking)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_htl_booking` = ' . (int) $idHtlBooking
        );
    }

    /**
     * Return breakdown for invoice/PDF — grouped by id_tax, excluding exempted rows.
     *
     * @param int $idOrder
     * @return array
     */
    public static function getBreakdownForInvoice($idOrder)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_tax`, `tax_name`, `collection_type`,
                    SUM(`num_nights`) AS num_nights,
                    SUM(`num_adults`) AS num_adults,
                    SUM(`total_amount`) AS total_amount
             FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_order` = ' . (int) $idOrder . '
               AND `is_exempted` = 0
             GROUP BY `id_tax`, `tax_name`, `collection_type`'
        );
    }

    /**
     * Return tourism tax status for a single booking.
     * STATUS_NONE     — no rows exist for this booking yet
     * STATUS_APPLIED  — rows exist and at least one is not exempted
     * STATUS_EXEMPTED — rows exist but all are exempted
     *
     * @param int $idHtlBooking
     * @return string
     */
    public static function getStatusForBooking($idHtlBooking)
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `is_exempted` FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
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
     * Sum of total_amount for all non-exempted tourism tax rows for a booking.
     * Returns 0 when the booking has no rows or all rows are exempted.
     *
     * @param int $idHtlBooking
     * @return float
     */
    public static function getTotalForBooking($idHtlBooking)
    {
        return (float) Db::getInstance()->getValue(
            'SELECT SUM(`total_amount`) FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
               AND `is_exempted` = 0
               AND `is_refunded` = 0'
        );
    }

    /**
     * Mark all non-refunded rows for a booking as exempted.
     * Runs inside a DB transaction with SELECT FOR UPDATE to prevent concurrent double-exemption.
     * Writes one HotelBookingTaxExemption audit row per htl_order_tourism_tax row exempted.
     *
     * @param int $idHtlBooking
     * @param int $idEmployee  PS employee id (0 = resolved from Context)
     * @return bool
     */
    public static function exemptForBooking($idHtlBooking, $idEmployee = 0)
    {
        $db = Db::getInstance();
        $db->execute('START TRANSACTION');
        try {
            $rows = $db->executeS(
                'SELECT `id_order_tourism_tax`, `id_order`, `id_order_detail`, `collection_type`, `total_amount`
                 FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
                 WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
                   AND `is_exempted` = 0
                   AND `is_refunded` = 0
                 FOR UPDATE'
            );

            if (empty($rows)) {
                $db->execute('COMMIT');
                return true;
            }

            $db->execute(
                'UPDATE `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
                 SET `is_exempted` = 1, `date_upd` = NOW()
                 WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
                   AND `is_refunded` = 0'
            );

            $idOrderDetail = (int) $rows[0]['id_order_detail'];
            $idOrder = (int) $rows[0]['id_order'];
            $onlineAmount = 0.0;
            $totalExemptedAmount = 0.0;
            $idEmployeeResolved = $idEmployee ? (int) $idEmployee : (int) Context::getContext()->employee->id;

            foreach ($rows as $row) {
                $totalExemptedAmount += (float) $row['total_amount'];
                if ((int) $row['collection_type'] === 0) {
                    $onlineAmount += (float) $row['total_amount'];
                }
                $exemption = new HotelBookingTaxExemption();
                $exemption->id_order_tourism_tax = (int) $row['id_order_tourism_tax'];
                $exemption->id_htl_booking = (int) $idHtlBooking;
                $exemption->id_order = $idOrder;
                $exemption->id_employee = $idEmployeeResolved;
                if (!$exemption->add()) {
                    throw new Exception('Failed to save exemption audit row');
                }
            }

            $db->execute(
                'UPDATE `' . _DB_PREFIX_ . 'order_detail`
                 SET `tourism_tax_amount`   = GREATEST(0, `tourism_tax_amount` - ' . (float) $totalExemptedAmount . '),
                     `total_price_tax_incl` = `total_price_tax_incl` - ' . (float) $onlineAmount . '
                 WHERE `id_order_detail` = ' . $idOrderDetail
            );

            if ($onlineAmount > 0) {
                $db->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'orders`
                     SET `total_paid_tax_incl` = `total_paid_tax_incl` - ' . (float) $onlineAmount . ',
                         `total_paid_tax_excl` = `total_paid_tax_excl` - ' . (float) $onlineAmount . ',
                         `total_paid`          = `total_paid`          - ' . (float) $onlineAmount . '
                     WHERE `id_order` = ' . $idOrder
                );
            }

            $db->execute('COMMIT');
            return true;
        } catch (Exception $e) {
            $db->execute('ROLLBACK');
            return false;
        }
    }

    /**
     * Remove exemption for all rows of a booking (restore applied state).
     * Runs inside a DB transaction with SELECT FOR UPDATE to prevent concurrent double-restore.
     *
     * @param int $idHtlBooking
     * @return bool
     */
    public static function unExemptForBooking($idHtlBooking)
    {
        $db = Db::getInstance();
        $db->execute('START TRANSACTION');
        try {
            $rows = $db->executeS(
                'SELECT `id_order`, `id_order_detail`, `collection_type`, `total_amount`
                 FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
                 WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
                   AND `is_exempted` = 1
                   AND `is_refunded` = 0
                 FOR UPDATE'
            );

            if (empty($rows)) {
                $db->execute('COMMIT');
                return true;
            }

            $db->execute(
                'UPDATE `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
                 SET `is_exempted` = 0, `date_upd` = NOW()
                 WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
                   AND `is_refunded` = 0'
            );

            $idOrderDetail = (int) $rows[0]['id_order_detail'];
            $idOrder = (int) $rows[0]['id_order'];
            $onlineAmount = 0.0;

            foreach ($rows as $row) {
                if ((int) $row['collection_type'] === 0) {
                    $onlineAmount += (float) $row['total_amount'];
                }
            }

            $restored = (float) $db->getValue(
                'SELECT SUM(`total_amount`)
                 FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
                 WHERE `id_order_detail` = ' . $idOrderDetail . '
                   AND `is_refunded` = 0'
            );

            $db->execute(
                'UPDATE `' . _DB_PREFIX_ . 'order_detail`
                 SET `tourism_tax_amount`   = ' . $restored . ',
                     `total_price_tax_incl` = `total_price_tax_incl` + ' . (float) $onlineAmount . '
                 WHERE `id_order_detail` = ' . $idOrderDetail
            );

            if ($onlineAmount > 0) {
                $db->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'orders`
                     SET `total_paid_tax_incl` = `total_paid_tax_incl` + ' . (float) $onlineAmount . ',
                         `total_paid_tax_excl` = `total_paid_tax_excl` + ' . (float) $onlineAmount . ',
                         `total_paid`          = `total_paid`          + ' . (float) $onlineAmount . '
                     WHERE `id_order` = ' . $idOrder
                );
            }

            $db->execute('COMMIT');
            return true;
        } catch (Exception $e) {
            $db->execute('ROLLBACK');
            return false;
        }
    }

    /**
     * Apply or restore tourism tax for a single booking based on its current status.
     * STATUS_EXEMPTED → removes the exemption.
     * STATUS_NONE     → computes fresh rows, then adjusts order totals.
     * STATUS_APPLIED  → no-op (returns APPLY_OK).
     *
     * @param int $idHtlBooking
     * @return int APPLY_OK | APPLY_ERROR_RESTORE | APPLY_ERROR_NO_RULE
     */
    public static function applyForBooking($idHtlBooking)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $status = self::getStatusForBooking($idHtlBooking);

        if ($status === self::STATUS_EXEMPTED) {
            return self::unExemptForBooking($idHtlBooking) ? self::APPLY_OK : self::APPLY_ERROR_RESTORE;
        }

        if ($status === self::STATUS_NONE) {
            // If refunded rows exist, the booking was cancelled — do not create new snapshot rows.
            $hasRefundedRows = (bool) Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
                 WHERE `id_htl_booking` = ' . $idHtlBooking . ' AND `is_refunded` = 1'
            );
            if ($hasRefundedRows) {
                return self::APPLY_ERROR_REFUNDED;
            }

            if (!HotelTourismTaxCalculator::applyFromExistingBooking($idHtlBooking)) {
                return self::APPLY_ERROR_NO_RULE;
            }
            self::adjustOrderTotalsForNewBooking($idHtlBooking);
        }

        return self::APPLY_OK;
    }

    /**
     * Apply tourism tax for all bookings of an order.
     * Returns array of ['booking_id' => int, 'error' => int] for each failure.
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
                $result = self::applyForBooking($booking['id']);
                if ($result !== self::APPLY_OK) {
                    $failures[] = array('booking_id' => (int) $booking['id'], 'error' => $result);
                }
            }
        }
        return $failures;
    }

    /**
     * Exempt tourism tax for a booking only if it is currently STATUS_APPLIED.
     * Returns EXEMPT_OK on success or when already exempted, EXEMPT_ERROR_NO_RULE when tourism tax
     * was never applied to this booking (room type has no tourism TRG), EXEMPT_ERROR_SAVE on DB failure.
     *
     * @param int $idHtlBooking
     * @param int $idEmployee
     * @return int EXEMPT_OK | EXEMPT_ERROR_NO_RULE | EXEMPT_ERROR_SAVE
     */
    public static function exemptIfApplied($idHtlBooking, $idEmployee = 0)
    {
        $idHtlBooking = (int) $idHtlBooking;
        $status = self::getStatusForBooking($idHtlBooking);
        if ($status === self::STATUS_NONE) {
            $hasRefundedRows = (bool) Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
                 WHERE `id_htl_booking` = ' . $idHtlBooking . ' AND `is_refunded` = 1'
            );
            return $hasRefundedRows ? self::EXEMPT_ERROR_REFUNDED : self::EXEMPT_ERROR_NO_RULE;
        }
        if ($status === self::STATUS_APPLIED) {
            return self::exemptForBooking($idHtlBooking, $idEmployee)
                ? self::EXEMPT_OK
                : self::EXEMPT_ERROR_SAVE;
        }
        return self::EXEMPT_OK;
    }

    /**
     * Exempt tourism tax for all applicable bookings of an order.
     * Returns array of ['booking_id' => int, 'error' => int] for each failure.
     *
     * @param int $idOrder
     * @param int $idEmployee
     * @return array
     */
    public static function exemptForOrder($idOrder, $idEmployee = 0)
    {
        $failures = array();
        $objBookingDetail = new HotelBookingDetail();
        $bookings = $objBookingDetail->getBookingDataByOrderId((int) $idOrder);
        if ($bookings) {
            foreach ($bookings as $booking) {
                $result = self::exemptIfApplied($booking['id'], $idEmployee);
                if ($result !== self::EXEMPT_OK) {
                    $failures[] = array('booking_id' => (int) $booking['id'], 'error' => $result);
                }
            }
        }
        return $failures;
    }

    /**
     * After fresh-applying tourism tax to a STATUS_NONE booking, add the online
     * portion to the order totals (it was absent from the original order total).
     *
     * @param int $idHtlBooking
     * @return void
     */
    private static function adjustOrderTotalsForNewBooking($idHtlBooking)
    {
        $booking = new HotelBookingDetail((int) $idHtlBooking);
        if (!Validate::isLoadedObject($booking)) {
            return;
        }
        $onlineTotal = (float) Db::getInstance()->getValue(
            'SELECT SUM(`total_amount`)
             FROM `' . _DB_PREFIX_ . 'htl_order_tourism_tax`
             WHERE `id_htl_booking` = ' . (int) $idHtlBooking . '
               AND `collection_type` = 0
               AND `is_exempted` = 0
               AND `is_refunded` = 0'
        );
        if ($onlineTotal > 0) {
            Db::getInstance()->execute(
                'UPDATE `' . _DB_PREFIX_ . 'orders`
                 SET `total_paid_tax_incl` = `total_paid_tax_incl` + ' . $onlineTotal . ',
                     `total_paid_tax_excl` = `total_paid_tax_excl` + ' . $onlineTotal . ',
                     `total_paid`          = `total_paid`          + ' . $onlineTotal . '
                 WHERE `id_order` = ' . (int) $booking->id_order
            );
        }
    }
}
