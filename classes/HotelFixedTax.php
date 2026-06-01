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
* Do not edit or add to this file if you wish to upgrade QloApps to newer
* versions in the future. If you wish to customize QloApps for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class HotelFixedTax extends ObjectModel
{
    public $id_hotel;
    public $name;
    public $amount;
    public $price_calc_type;
    public $occupancy_based_price;
    public $apply_on_child;
    public $apply_on_infant;
    public $active;
    public $date_add;
    public $date_upd;

    const FIXED_TAX_CALCULATION_METHOD_PER_STAY  = Product::PRICE_CALCULATION_METHOD_PER_BOOKING;
    const FIXED_TAX_CALCULATION_METHOD_PER_NIGHT = Product::PRICE_CALCULATION_METHOD_PER_DAY;

    public static $definition = array(
        'table'     => 'htl_fixed_tax',
        'primary'   => 'id_fixed_tax',
        'multilang' => true,
        'fields'    => array(
            'id_hotel'              => array('type' => self::TYPE_INT,   'validate' => 'isUnsignedId',  'required' => true),
            'amount'                => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'price_calc_type'       => array('type' => self::TYPE_INT,   'validate' => 'isUnsignedInt'),
            'occupancy_based_price' => array('type' => self::TYPE_BOOL,  'validate' => 'isBool'),
            'apply_on_child'        => array('type' => self::TYPE_BOOL,  'validate' => 'isBool'),
            'apply_on_infant'       => array('type' => self::TYPE_BOOL,  'validate' => 'isBool'),
            'active'                => array('type' => self::TYPE_BOOL,  'validate' => 'isBool'),
            'date_add'              => array('type' => self::TYPE_DATE,  'validate' => 'isDate'),
            'date_upd'              => array('type' => self::TYPE_DATE,  'validate' => 'isDate'),
            'name' => array(
                'type'     => self::TYPE_STRING,
                'lang'     => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size'     => 255,
            ),
        ),
    );

    public function delete()
    {
        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_booking_fixed_tax`
             SET `id_fixed_tax` = NULL
             WHERE `id_fixed_tax` = '.(int)$this->id
        );
        return parent::delete();
    }

    /**
     * Check whether a tax name already exists for the given hotel in the given language.
     * Pass $excludeId when editing to avoid a false conflict with the record being updated.
     */
    public static function nameExists($name, $idHotel, $idLang, $excludeId = 0)
    {
        return (bool)Db::getInstance()->getValue(
            'SELECT ft.`id_fixed_tax`
             FROM `'._DB_PREFIX_.'htl_fixed_tax` ft
             LEFT JOIN `'._DB_PREFIX_.'htl_fixed_tax_lang` ftl
                 ON ftl.`id_fixed_tax` = ft.`id_fixed_tax`
                 AND ftl.`id_lang` = '.(int)$idLang.'
             WHERE ft.`id_hotel` = '.(int)$idHotel.'
               AND ftl.`name` = \''.pSQL($name).'\'
               AND ft.`id_fixed_tax` != '.(int)$excludeId
        );
    }

    /**
     * Get all (optionally only active) fixed taxes for a hotel.
     * Returns [] when PS_TAX is disabled — this is the single PS_TAX gate for live calcs.
     */
    public static function getHotelFixedTaxesInfo($idHotel, $idLang = null, $activeOnly = true)
    {
        if (!Configuration::get('PS_TAX')) {
            return array();
        }

        if ($idLang === null) {
            $idLang = (int)Context::getContext()->language->id;
        }

        $query = new DbQuery();
        $query->select('ft.`id_fixed_tax`, ft.`id_hotel`, ft.`amount`, ft.`price_calc_type`,
                        ft.`occupancy_based_price`, ft.`apply_on_child`, ft.`apply_on_infant`, ft.`active`,
                        ftl.`name`');
        $query->from('htl_fixed_tax', 'ft');
        $query->leftJoin(
            'htl_fixed_tax_lang',
            'ftl',
            'ftl.`id_fixed_tax` = ft.`id_fixed_tax` AND ftl.`id_lang` = '.(int)$idLang
        );
        $query->where('ft.`id_hotel` = '.(int)$idHotel);
        if ($activeOnly) {
            $query->where('ft.`active` = 1');
        }
        $query->orderBy('ft.`id_fixed_tax` ASC');

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if (!$rows) {
            return array();
        }

        foreach ($rows as &$row) {
            $row['id_fixed_tax']          = (int)$row['id_fixed_tax'];
            $row['id_hotel']              = (int)$row['id_hotel'];
            $row['amount']                = (float)$row['amount'];
            $row['price_calc_type']       = (int)$row['price_calc_type'];
            $row['occupancy_based_price'] = (bool)$row['occupancy_based_price'];
            $row['apply_on_child']        = (bool)$row['apply_on_child'];
            $row['apply_on_infant']       = (bool)$row['apply_on_infant'];
            $row['active']                = (bool)$row['active'];
            $row['name']                  = $row['name'] ? $row['name'] : '';
        }
        unset($row);

        return $rows;
    }

    /**
     * Calculate the tax amount for one tax row applied to one booking.
     * Handles per-stay vs per-night and optional occupancy multiplier.
     */
    protected static function calculateAmountForBooking(
        array $taxRow,
        $dateFrom,
        $dateTo,
        $adults = 0,
        $children = 0
    ) {
        $amount = (float)$taxRow['amount'];
        if ($amount <= 0) {
            return 0.0;
        }

        if ((int)$taxRow['price_calc_type'] === self::FIXED_TAX_CALCULATION_METHOD_PER_NIGHT) {
            $nights = (int)(new DateTime($dateTo))->diff(new DateTime($dateFrom))->days;
            if ($nights <= 0) {
                return 0.0;
            }
            $amount *= $nights;
        }

        if (!empty($taxRow['occupancy_based_price'])) {
            $eligible = max(0, (int)$adults);
            if (!empty($taxRow['apply_on_child'])) {
                $eligible += max(0, (int)$children);
            }
            if ($eligible <= 0) {
                return 0.0;
            }
            $amount *= $eligible;
        }

        return Tools::ps_round((float)$amount, _PS_PRICE_COMPUTE_PRECISION_);
    }

    /**
     * Parse child_ages JSON and return child/infant counts.
     */
    public static function parseChildAges($childAges)
    {
        if (is_string($childAges)) {
            $childAges = json_decode($childAges, true);
        }
        if (!is_array($childAges)) {
            return array('children' => 0, 'infants' => 0);
        }

        return array('children' => count($childAges), 'infants' => 0);
    }

    /**
     * Returns total + per-tax breakdown for bookings in a cart (live calculation).
     * Supports filtering by hotel, room type, room, or date range.
     *
     * Return: ['total' => float, 'breakdown' => [taxName => ['id_fixed_tax', 'tax_name', 'total_amount']]]
     */
    public static function getFixedTaxesInCart(
        $idCart,
        $idHotel = 0,
        $idRoomType = 0,
        $idRoom = 0,
        $dateFrom = '',
        $dateTo = '',
        $idLang = null
    ) {
        $result = array('total' => 0.0, 'breakdown' => array());

        if (!Configuration::get('PS_TAX')) {
            return $result;
        }

        if ($idLang === null) {
            $idLang = (int)Context::getContext()->language->id;
        }

        $query = new DbQuery();
        $query->select('*');
        $query->from('htl_cart_booking_data');
        $query->where('`id_cart` = '.(int)$idCart);
        if ($idHotel) {
            $query->where('`id_hotel` = '.(int)$idHotel);
        }
        if ($idRoomType) {
            $query->where('`id_product` = '.(int)$idRoomType);
        }
        if ($idRoom) {
            $query->where('`id_room` = '.(int)$idRoom);
        }
        if ($dateFrom && $dateTo) {
            $query->where('`date_from` = \''.pSQL($dateFrom).'\' AND `date_to` = \''.pSQL($dateTo).'\'');
        }

        $bookings = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if (!$bookings) {
            return $result;
        }

        $taxesByHotel = array();
        foreach (array_unique(array_column($bookings, 'id_hotel')) as $hId) {
            $taxesByHotel[(int)$hId] = self::getHotelFixedTaxesInfo((int)$hId, $idLang);
        }

        $byId = array();

        foreach ($bookings as $booking) {
            $taxes = isset($taxesByHotel[(int)$booking['id_hotel']]) ? $taxesByHotel[(int)$booking['id_hotel']] : array();
            if (!$taxes) {
                continue;
            }

            $parsed   = self::parseChildAges($booking['child_ages']);
            $children = $parsed['children'];

            foreach ($taxes as $tax) {
                $taxAmount = self::calculateAmountForBooking(
                    $tax,
                    $booking['date_from'],
                    $booking['date_to'],
                    (int)$booking['adults'],
                    $children
                );

                if ($taxAmount <= 0) {
                    continue;
                }

                $taxId   = (int)$tax['id_fixed_tax'];
                $taxName = trim($tax['name']) !== '' ? trim($tax['name']) : ('Tax #'.$taxId);

                $result['total'] += $taxAmount;
                if (!isset($byId[$taxId])) {
                    $byId[$taxId] = array(
                        'id_fixed_tax' => $taxId,
                        'tax_name'     => $taxName,
                        'total_amount' => 0.0,
                    );
                }
                $byId[$taxId]['total_amount'] += $taxAmount;
            }
        }

        foreach ($byId as $taxData) {
            $key = $taxData['tax_name'];
            if (!isset($result['breakdown'][$key])) {
                $result['breakdown'][$key] = $taxData;
            } else {
                $result['breakdown'][$key]['total_amount'] += $taxData['total_amount'];
            }
        }

        $result['total'] = Tools::ps_round((float)$result['total'], _PS_PRICE_COMPUTE_PRECISION_);

        return $result;
    }

    /**
     * Snapshot fixed taxes for one booking detail record.
     * Idempotent: deletes existing rows before inserting.
     * Call immediately after HotelBookingDetail::save().
     */
    public static function saveBookingFixedTaxes($idHtlBookingDetail, array $cartBookingData, $idLang = null)
    {
        if (!$idHtlBookingDetail || !Configuration::get('PS_TAX')) {
            return true;
        }

        if ($idLang === null) {
            $idLang = (int)Context::getContext()->language->id;
        }

        self::deleteByBookingDetail((int)$idHtlBookingDetail);

        $idHotel = (int)$cartBookingData['id_hotel'];
        $taxes   = self::getHotelFixedTaxesInfo($idHotel, $idLang);
        if (!$taxes) {
            return true;
        }

        $parsed   = self::parseChildAges(isset($cartBookingData['child_ages']) ? $cartBookingData['child_ages'] : '[]');
        $children = $parsed['children'];
        $dateFrom = $cartBookingData['date_from'];
        $dateTo   = $cartBookingData['date_to'];
        $adults   = (int)(isset($cartBookingData['adults']) ? $cartBookingData['adults'] : 0);

        $success = true;
        foreach ($taxes as $tax) {
            $taxAmount = self::calculateAmountForBooking($tax, $dateFrom, $dateTo, $adults, $children);
            if ($taxAmount <= 0) {
                continue;
            }

            $taxId   = (int)$tax['id_fixed_tax'];
            $taxName = trim($tax['name']) !== '' ? trim($tax['name']) : ('Tax #'.$taxId);

            $success = $success && Db::getInstance()->insert('htl_booking_fixed_tax', array(
                'id_htl_booking_detail' => (int)$idHtlBookingDetail,
                'id_fixed_tax'          => $taxId,
                'tax_name'              => pSQL($taxName),
                'amount'                => (float)$taxAmount,
                'date_add'              => date('Y-m-d H:i:s'),
            ));
        }

        return $success;
    }

    /**
     * Delete all snapshot rows for a booking detail.
     */
    public static function deleteByBookingDetail($idHtlBookingDetail)
    {
        return Db::getInstance()->delete(
            'htl_booking_fixed_tax',
            '`id_htl_booking_detail` = '.(int)$idHtlBookingDetail
        );
    }

    /**
     * Get fixed taxes for a placed order from the snapshot table.
     * No PS_TAX gate — reads historical amounts already charged; disabling tax
     * after booking must not hide amounts on invoices/order views.
     *
     * Return: [
     *   'total'             => float,
     *   'breakdown'         => [taxName => ['id_fixed_tax', 'tax_name', 'total_amount']],
     *   'by_booking_detail' => [id_htl_booking_detail => float]
     * ]
     */
    public static function getFixedTaxesForOrder($idOrder, $idLang = null)
    {
        $result = array('total' => 0.0, 'breakdown' => array(), 'by_booking_detail' => array());

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT bft.`id_booking_fixed_tax`, bft.`id_htl_booking_detail`,
                    bft.`id_fixed_tax`, bft.`tax_name`, bft.`amount`
             FROM `'._DB_PREFIX_.'htl_booking_fixed_tax` bft
             INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` bd
                ON bd.`id` = bft.`id_htl_booking_detail`
             WHERE bd.`id_order` = '.(int)$idOrder
        );

        if (!$rows) {
            return $result;
        }

        $byId = array();
        foreach ($rows as $row) {
            $taxName = trim($row['tax_name']);
            $amount  = (float)$row['amount'];

            $result['total'] += $amount;

            $key = $row['id_fixed_tax'] !== null ? 'id_'.(int)$row['id_fixed_tax'] : 'name_'.$taxName;
            if (!isset($byId[$key])) {
                $byId[$key] = array(
                    'id_fixed_tax' => $row['id_fixed_tax'] !== null ? (int)$row['id_fixed_tax'] : null,
                    'tax_name'     => $taxName,
                    'total_amount' => 0.0,
                );
            }
            $byId[$key]['total_amount'] += $amount;

            $bdId = (int)$row['id_htl_booking_detail'];
            if (!isset($result['by_booking_detail'][$bdId])) {
                $result['by_booking_detail'][$bdId] = 0.0;
            }
            $result['by_booking_detail'][$bdId] += $amount;
        }

        foreach ($byId as $taxData) {
            $nameKey = $taxData['tax_name'];
            if (!isset($result['breakdown'][$nameKey])) {
                $result['breakdown'][$nameKey] = $taxData;
            } else {
                $result['breakdown'][$nameKey]['total_amount'] += $taxData['total_amount'];
            }
        }

        $result['total'] = Tools::ps_round((float)$result['total'], _PS_PRICE_COMPUTE_PRECISION_);

        return $result;
    }
}
