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

class TaxConfigurationCore extends ObjectModel
{
    const DAY_KEYS = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

    const CALCULATION_TYPE_FIXED = 0;
    const CALCULATION_TYPE_PERCENTAGE = 1;

    public $id_tax;
    public $tax_value;
    public $calculation_type;
    public $per_night;
    public $per_person;
    public $has_tiered_pricing;
    public $apply_on_child;
    public $has_child_age_range;
    public $valid_from;
    public $valid_to;
    public $special_days;

    public static $definition = array(
        'table' => 'tax_configuration',
        'primary' => 'id_tax',
        'fields' => array(
            'tax_value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'),
            'calculation_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'per_night' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'per_person' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'has_tiered_pricing' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'apply_on_child' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'has_child_age_range' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'valid_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'allow_null' => true),
            'valid_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'allow_null' => true),
            'special_days' => array('type' => self::TYPE_STRING),
        ),
    );

    /**
     * Override add() because id_tax is a FK-as-PK — the base ObjectModel::add() never writes the primary key column, which would default to 0 and create phantom rows.
     *
     * @param bool $auto_date   ignored — table has no date_add/date_upd
     * @param bool $null_values ignored
     * @return bool
     */
    public function add($auto_date = true, $null_values = false)
    {
        $idTax = (int) $this->id;
        if (!$idTax) {
            return false;
        }
        $result = Db::getInstance()->insert(
            'tax_configuration',
            array(
                'id_tax' => $idTax,
                'tax_value' => (float) $this->tax_value,
                'calculation_type' => (int) $this->calculation_type,
                'per_night' => (int) $this->per_night,
                'per_person' => (int) $this->per_person,
                'has_tiered_pricing' => (int) $this->has_tiered_pricing,
                'apply_on_child' => (int) $this->apply_on_child,
                'has_child_age_range' => (int) $this->has_child_age_range,
                'valid_from' => $this->valid_from ? pSQL($this->valid_from) : null,
                'valid_to' => $this->valid_to ? pSQL($this->valid_to) : null,
                'special_days' => $this->special_days ? pSQL($this->special_days) : null,
            )
        );
        if ($result) {
            $this->id_tax = $idTax;
        }
        return $result;
    }

    /**
     * Load a tourism tax subtype row by id_tax, cached per request.
     *
     * @param int $idTax
     * @return TaxConfiguration|false
     */
    public static function getByTaxId($idTax)
    {
        $idTax = (int) $idTax;
        $cacheId = 'TaxConfiguration::getByTaxId-' . $idTax;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $obj = new TaxConfiguration($idTax);
        $result = Validate::isLoadedObject($obj) ? $obj : false;
        Cache::store($cacheId, $result);

        return $result;
    }

    /**
     * Shared core for cleanFromTourismTaxRulesGroups()/cleanFromVatTaxRulesGroups().
     *
     * @param int  $idTax
     * @param bool $isTourismTaxRulesGroup  true = clean from tourism tax rules groups, false = clean from VAT tax rules groups
     * @return int
     */
    protected static function cleanTaxRulesGroupsByTourismFlag($idTax, $isTourismTaxRulesGroup)
    {
        $flag = $isTourismTaxRulesGroup ? 1 : 0;
        $db = Db::getInstance();
        $db->execute(
            'DELETE tr FROM `' . _DB_PREFIX_ . 'tax_rule` tr
             INNER JOIN `' . _DB_PREFIX_ . 'tax_rules_group` trg
                 ON trg.`id_tax_rules_group` = tr.`id_tax_rules_group`
             WHERE tr.`id_tax` = ' . (int) $idTax . '
             AND trg.`is_tourism_tax_rule_group` = ' . $flag
        );
        return $db->Affected_Rows();
    }

    /**
     * Remove this tax from all tourism tax rules groups.
     *
     * @param int $idTax
     * @return int
     */
    public static function cleanFromTourismTaxRulesGroups($idTax)
    {
        return self::cleanTaxRulesGroupsByTourismFlag($idTax, true);
    }

    /**
     * Remove this tax from all standard (non-tourism) tax rules groups — symmetric counterpart of cleanFromTourismTaxRulesGroups().
     *
     * @param int $idTax
     * @return int
     */
    public static function cleanFromVatTaxRulesGroups($idTax)
    {
        return self::cleanTaxRulesGroupsByTourismFlag($idTax, false);
    }

    /**
     * Format a tax rate/amount for display, VAT as a percentage and tourism tax per its own calc type.
     *
     * @param mixed    $value  ps_tax.rate (used for non-tourism rows)
     * @param array    $row    List row — must include is_tourism_tax, tourism_tax_calc_type, tourism_tax_value
     * @param Currency $currency
     * @return string
     */
    public static function getFormattedRateForDisplay($value, array $row, Currency $currency)
    {
        if (!(int) $row['is_tourism_tax']) {
            return Tools::safeOutput($value) . '%';
        }
        $displayValue = Tools::safeOutput((float) $row['tourism_tax_value']);
        if ((int) $row['tourism_tax_calc_type'] === self::CALCULATION_TYPE_PERCENTAGE) {
            return $displayValue . '%';
        }
        $currencySign = Tools::safeOutput(trim($currency->prefix . $currency->suffix));
        return $currencySign . ' ' . $displayValue;
    }

    /**
     * Delete the subtype row and all related tiers/child-ranges.
     *
     * @return bool
     */
    public function delete()
    {
        if (parent::delete()) {
            $idTax = (int) $this->id;
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'tax_price_tier`
                 WHERE `id_tax` = ' . $idTax
            );
            Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'tax_child_range`
                 WHERE `id_tax` = ' . $idTax
            );
            return true;
        }
        return false;
    }

    /**
     * Whether tourism tax should be visually folded into the displayed room/service price.
     *
     * @param float $tourismTax
     * @return bool
     */
    public static function isGrossedUp($tourismTax)
    {
        return (float) $tourismTax > 0 && (bool) Configuration::get('QLO_TOURISM_TAX_GROSSED_UP');
    }

    /**
     * Resolve the hotel's own jurisdiction address and current tourism-tax collection type — shared by every cart, order, and preview caller that needs this context.
     *
     * @param int     $idHotel          0 = no hotel context (fully standalone product)
     * @param Address $fallbackAddress  Used only when the hotel has no address on file
     * @return array ['address' => Address, 'collectionType' => int]
     */
    public static function resolveHotelAddressAndCollectionType($idHotel, Address $fallbackAddress)
    {
        $idHotel = (int) $idHotel;

        $address = null;
        if ($idHotel) {
            $hotelAddress = HotelBranchInformation::getAddress($idHotel);
            if ($hotelAddress && !empty($hotelAddress['id_address'])) {
                $address = new Address((int) $hotelAddress['id_address']);
            }
        }
        if (!$address || !Validate::isLoadedObject($address)) {
            $address = $fallbackAddress;
        }

        $collectionType = HotelBranchInformation::TAX_COLLECTION_TYPE_ONLINE;
        if ($idHotel && Validate::isLoadedObject($hotelBranch = new HotelBranchInformation($idHotel))) {
            $collectionType = (int) $hotelBranch->tourism_tax_collection_type;
        }

        return array(
            'address' => $address,
            'collectionType' => $collectionType,
        );
    }

    /**
     * Resolve the jurisdiction address, collection type, and occupancy (check-in/nights/adults/children) a service product line needs before computing its tourism tax.
     *
     * @param int      $idHotel                  0 = no hotel context (fully standalone product)
     * @param int      $idHtlBookingDetail       0 = not attached to an order-side room booking
     * @param Address  $fallbackAddress          Used only when the hotel has no address on file
     * @param int      $idHtlCartBooking         0 = not attached to a cart-side room booking (pre-order preview only —
     *                                            ignored when $idHtlBookingDetail is given)
     * @return array ['address' => Address, 'collectionType' => int, 'checkInDate' => string,
     *                'numNights' => int, 'numAdults' => int, 'childrenAges' => int[]]
     */
    public static function resolveServiceLineTaxContext($idHotel, $idHtlBookingDetail, Address $fallbackAddress, $idHtlCartBooking = 0)
    {
        $idHotel = (int) $idHotel;
        $idHtlBookingDetail = (int) $idHtlBookingDetail;
        $idHtlCartBooking = (int) $idHtlCartBooking;

        $hotelContext = self::resolveHotelAddressAndCollectionType($idHotel, $fallbackAddress);
        $address = $hotelContext['address'];
        $collectionType = $hotelContext['collectionType'];

        $roomBooking = $idHtlBookingDetail ? new HotelBookingDetail($idHtlBookingDetail) : null;
        $cartBooking = (!$roomBooking && $idHtlCartBooking) ? new HotelCartBookingData($idHtlCartBooking) : null;
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
        } elseif ($cartBooking && Validate::isLoadedObject($cartBooking)) {
            $checkInDate = $cartBooking->date_from;
            $numNights = max(1, (int) HotelHelper::getNumberOfDays($cartBooking->date_from, $cartBooking->date_to));
            $numAdults = (int) $cartBooking->adults;
            $childrenAges = !empty($cartBooking->child_ages) ? (array) json_decode($cartBooking->child_ages, true) : array();
        } else {
            $checkInDate = date('Y-m-d');
            $numNights = 1;
            $numAdults = 1;
            $childrenAges = array();
        }

        return array(
            'address' => $address,
            'collectionType' => $collectionType,
            'checkInDate' => $checkInDate,
            'numNights' => $numNights,
            'numAdults' => $numAdults,
            'childrenAges' => $childrenAges,
        );
    }

    /**
     * Preview parameters for a tax rules group — a deliberate 1-adult/1-night approximation for js/admin/price.js's live price preview.
     *
     * @param int    $idTaxRulesGroup
     * @param Address $address
     * @param int    $idLang
     * @return array List of ['tax_calc_type','is_tiered','tax_value','tiers']
     */
    public static function getPreviewParams($idTaxRulesGroup, Address $address, $idLang, $collectionType = HotelBranchInformation::TAX_COLLECTION_TYPE_ONLINE)
    {
        $idTaxRulesGroup = (int) $idTaxRulesGroup;
        if (!$idTaxRulesGroup || Tax::excludeTaxeOption() || (int) $collectionType === HotelBranchInformation::TAX_COLLECTION_TYPE_AT_HOTEL) {
            return array();
        }

        $taxCalculator = TaxManagerFactory::getManager($address, $idTaxRulesGroup)->getTaxCalculator();
        $preview = array();

        $today = new DateTime();
        $todayDayKey = self::DAY_KEYS[$today->format('N') - 1];

        foreach ($taxCalculator->taxes as $tax) {
            $tourismTax = self::getByTaxId((int) $tax->id);
            if (!$tourismTax) {
                continue;
            }

            if (!empty($tourismTax->valid_from) && $tourismTax->valid_from !== '0000-00-00'
                && $today < new DateTime($tourismTax->valid_from)
            ) {
                continue;
            }
            if (!empty($tourismTax->valid_to) && $tourismTax->valid_to !== '0000-00-00'
                && $today > new DateTime($tourismTax->valid_to)
            ) {
                continue;
            }
            if ($tourismTax->special_days) {
                $specialDays = json_decode($tourismTax->special_days, true);
                if (!is_array($specialDays) || !in_array($todayDayKey, $specialDays)) {
                    continue;
                }
            }

            $tiers = array();
            if ((bool) $tourismTax->has_tiered_pricing) {
                foreach (TaxPriceTier::getByTaxId((int) $tax->id) as $tier) {
                    $tiers[] = array(
                        'min_amount' => (float) $tier['min_amount'],
                        'max_amount' => (float) $tier['max_amount'],
                        'tax_value' => (float) $tier['tax_value'],
                    );
                }
            }
            $preview[] = array(
                'tax_calc_type' => (int) $tourismTax->calculation_type,
                'is_tiered' => (bool) $tourismTax->has_tiered_pricing,
                'tax_value' => (float) $tourismTax->tax_value,
                'tiers' => $tiers,
            );
        }

        return $preview;
    }
}
