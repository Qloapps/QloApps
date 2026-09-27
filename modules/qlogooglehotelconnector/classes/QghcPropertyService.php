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
 * Business logic for enabling/disabling hotels on Google Hotel Connector.
 * Enrollment state lives on columns bolted onto htl_branch_info/htl_room_type — all
 * CRUD on those columns is delegated to QghcProperty and QghcRoomType. Only
 * cross-domain queries (joining into address, product_lang, etc.) live here.
 */
class QghcPropertyService
{
    private static function l($string)
    {
        return Translate::getModuleTranslation('qlogooglehotelconnector', $string, 'QghcPropertyService');
    }

    /**
     * Total price for a room/date range, pinned to the shop's default currency
     * regardless of the visitor's session — keeps the ARI feed and JSON-LD from
     * ever diverging on currency.
     *
     * @param int    $idProduct
     * @param string $dateFrom
     * @param string $dateTo
     * @param int    $qty
     * @return array  Same shape as HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice().
     */
    public static function getRoomTypeTotalPriceInDefaultCurrency($idProduct, $dateFrom, $dateTo, $qty = 1)
    {
        $context           = Context::getContext();
        $sessionCurrency   = $context->currency;
        $context->currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));

        $priceData = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($idProduct, $dateFrom, $dateTo, $qty);

        $context->currency = $sessionCurrency;

        return $priceData;
    }

    /**
     * Final total price for a room/date range — the room price (tax incl/excl, from
     * getRoomTypeTotalPriceInDefaultCurrency() above) PLUS any Convenience-Fee-type
     * auto-add services (tax incl/excl). Deliberately separate from that method: it
     * returns the bare room rate and is also used by the ARI feed (QghcAri.php), which
     * must NOT include Convenience Fee. This method exists only for Google Hotel SDM
     * (QghcSdmService), which has no cart to read a real checkout total from, so it
     * recomputes the same two core-pricing calls Cart::getSummaryDetails() itself
     * relies on for "Total rooms cost" + "Convenience Fees" — see
     * HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice() (via the method above) and
     * RoomTypeServiceProduct::getAutoAddServices(..., PRICE_ADDITION_TYPE_INDEPENDENT).
     * Cart rules/discounts and customer-selected extra demands are intentionally
     * excluded — those are session/customer-specific, not a property of the room type
     * itself, so they don't belong in a generic structured-data price.
     *
     * @param int    $idProduct
     * @param string $dateFrom
     * @param string $dateTo
     * @param int    $qty
     * @return array  Same shape as getRoomTypeTotalPriceInDefaultCurrency(), with any
     *                Convenience Fee already added into both tax-incl and tax-excl totals.
     */
    public static function getRoomTypeFinalPriceForSdm($idProduct, $dateFrom, $dateTo, $qty = 1)
    {
        $priceData = self::getRoomTypeTotalPriceInDefaultCurrency($idProduct, $dateFrom, $dateTo, $qty);

        $context           = Context::getContext();
        $sessionCurrency   = $context->currency;
        $context->currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));

        $feesInclTax = RoomTypeServiceProduct::getAutoAddServices($idProduct, $dateFrom, $dateTo, Product::PRICE_ADDITION_TYPE_INDEPENDENT, true, 1);
        $feesExclTax = RoomTypeServiceProduct::getAutoAddServices($idProduct, $dateFrom, $dateTo, Product::PRICE_ADDITION_TYPE_INDEPENDENT, false, 1);

        $context->currency = $sessionCurrency;

        if ($feesInclTax) {
            foreach ($feesInclTax as $fee) {
                $priceData['total_price_tax_incl'] += (float) $fee['price'];
            }
        }
        if ($feesExclTax) {
            foreach ($feesExclTax as $fee) {
                $priceData['total_price_tax_excl'] += (float) $fee['price'];
            }
        }

        return $priceData;
    }

    /**
     * ISO code of the shop's default currency.
     *
     * @return string
     */
    public static function getDefaultCurrencyIsoCode()
    {
        static $isoCode = null;
        if (null === $isoCode) {
            $isoCode = (new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT')))->iso_code;
        }
        return $isoCode;
    }

    /**
     * Validates required Google Hotel fields for a hotel.
     * Reads coordinates directly from DB — ObjectModel cache may hold stale 0 values
     * from an update() call that ran earlier in the same request.
     *
     * @param int $idHotel
     * @param int $idLang
     * @return string[] Error messages; empty = all fields valid.
     */
    public static function validateRequiredFields($idHotel, $idLang)
    {
        $hotelAddress = HotelBranchInformation::getAddress((int)$idHotel, (int)$idLang);
        $errors       = array();

        if (empty($hotelAddress['address1'])) {
            $errors[] = self::l('Google Hotel: Street address is required before enabling Google Hotel.');
        }
        if (empty($hotelAddress['city'])) {
            $errors[] = self::l('Google Hotel: City is required before enabling Google Hotel.');
        }
        if (empty($hotelAddress['id_country'])) {
            $errors[] = self::l('Google Hotel: Country is required before enabling Google Hotel.');
        }
        if (!empty($hotelAddress['id_country']) && Country::containsStates((int)$hotelAddress['id_country']) && empty($hotelAddress['id_state'])) {
            $errors[] = self::l('Google Hotel: State / Region is required before enabling Google Hotel.');
        }

        if (!self::hasValidCoordinates($idHotel)) {
            $errors[] = self::l('Google Hotel: Latitude and longitude are required before enabling Google Hotel.');
        }

        return $errors;
    }

    /**
     * Reasons a hotel isn't listed on Google Hotel — empty means it's fine (connected, or
     * enrolled and legitimately PENDING Google's routine weekly review, which is not an
     * issue).
     *
     * @param int $idHotel
     * @param int $idLang
     * @return string[]
     */
    public static function getHotelConnectionIssueReasons($idHotel, $idLang)
    {
        $property = QghcProperty::getByHotelId($idHotel);

        if ($property) {
            if ((int) $property->google_status === QghcProperty::GOOGLE_STATUS_FAILED) {
                return array($property->failure_reason
                    ? self::l('Google Hotel: Google could not verify this hotel — ') . $property->failure_reason
                    : self::l('Google Hotel: Google reported an issue connecting this hotel. Please contact support.'));
            }
            return array();
        }

        $errors = self::validateRequiredFields($idHotel, $idLang);
        if ($errors) {
            return $errors;
        }

        return array(self::l('Google Hotel: This hotel is not yet enabled for Google Hotel.'));
    }

    /**
     * Hotels not currently listed on Google Hotel, for any reason, except one legitimately
     * PENDING Google's routine review. Re-validates live on every call — no schema to
     * persist this, and unnecessary at typical hotel-chain PMS hotel counts.
     *
     * @param int $idLang
     * @return array id_hotel => hotel_name
     */
    public static function getHotelsWithIssues($idLang)
    {
        $issues = array();

        foreach ((array) QghcProperty::getAllHotels($idLang) as $hotel) {
            $idHotel = (int) $hotel['id'];
            if (self::getHotelConnectionIssueReasons($idHotel, $idLang)) {
                $issues[$idHotel] = $hotel['hotel_name'];
            }
        }

        return $issues;
    }

    /**
     * Checks whether a hotel has valid coordinates set.
     * Validates with Validate::isFloat() to match core's own check when saving a hotel
     * (see AdminAddHotelController::processSave(), which validates latitude/longitude
     * the same way before storing them) — a non-numeric value is treated the same as unset.
     * Only (0.0, 0.0) together counts as "unset" — latitude 0.0 (the Equator) and
     * longitude 0.0 (the Prime Meridian) are each real, valid coordinates on their own,
     * so one of them being 0.0 while the other has a real value is a legitimate location.
     * Reads directly from DB — ObjectModel cache may hold stale 0 values from an update()
     * call that ran earlier in the same request.
     *
     * @param int $idHotel
     * @return bool
     */
    private static function hasValidCoordinates($idHotel)
    {
        $coords = Db::getInstance()->getRow(
            'SELECT `latitude`, `longitude` FROM `' . _DB_PREFIX_ . 'htl_branch_info`
             WHERE `id` = ' . (int)$idHotel
        );

        if (!$coords || !Validate::isFloat($coords['latitude']) || !Validate::isFloat($coords['longitude'])) {
            return false;
        }

        return (float)$coords['latitude'] != 0.0 || (float)$coords['longitude'] != 0.0;
    }

    /**
     * Disables QGHC for a hotel.
     * QghcProperty::delete() cascades to remove all room type mappings automatically.
     *
     * @param int $idHotel
     */
    public static function disable($idHotel)
    {
        $property = QghcProperty::getByHotelId($idHotel);
        if (!$property) {
            return;
        }

        try {
            Db::getInstance()->execute('START TRANSACTION');
            $property->delete();
            Db::getInstance()->execute('COMMIT');
        } catch (Exception $e) {
            Db::getInstance()->execute('ROLLBACK');
            return;
        }
    }

    /**
     * Enables QGHC for a hotel and syncs the given room type IDs.
     * Creates the property row if it does not yet exist. Room types not in
     * $selectedRoomTypes are un-enrolled (and their Google ID cleared); room types
     * already enrolled that stay in $selectedRoomTypes are left untouched, keeping
     * whatever Google ID the CM already assigned them — only actually-deselected room
     * types ever lose their Google ID.
     *
     * @param int   $idHotel
     * @param int[] $selectedRoomTypes  htl_room_type.id_product values to connect.
     */
    public static function enable($idHotel, array $selectedRoomTypes)
    {
        $property = QghcProperty::getByHotelId($idHotel);

        if (!$property) {
            $property = QghcProperty::createForHotel($idHotel);
            if (!$property) {
                return;
            }
        }

        $idQghcProperty = (int)$property->id;

        try {
            Db::getInstance()->execute('START TRANSACTION');

            QghcRoomType::deleteExcept($idQghcProperty, $selectedRoomTypes);

            if (!empty($selectedRoomTypes)) {
                $idList   = implode(',', array_map('intval', $selectedRoomTypes));
                $roomRows = Db::getInstance()->executeS(
                    'SELECT `id_product` AS id_room_type FROM `' . _DB_PREFIX_ . 'htl_room_type`
                     WHERE `id_product` IN (' . $idList . ')'
                );

                if ($roomRows) {
                    foreach ($roomRows as $room) {
                        QghcRoomType::insertForProperty($idQghcProperty, (int)$room['id_room_type']);
                    }
                }
            }

            Db::getInstance()->execute('COMMIT');
        } catch (Exception $e) {
            Db::getInstance()->execute('ROLLBACK');
        }
    }

    /**
     * Loads all data required for the Google Hotel tab in the hotel edit form.
     *
     * @param int $idHotel
     * @param int $idLang
     * @return array Keys: ghcProperty, roomTypes, roomTypeMappings, missingFields
     */
    public static function getHotelTabData($idHotel, $idLang)
    {
        $data = array(
            'ghcProperty'      => array(),
            'roomTypes'        => array(),
            'roomTypeMappings' => array(),
            'missingFields'    => array(),
        );

        if (!$idHotel) {
            return $data;
        }

        $property = QghcProperty::getByHotelId($idHotel);
        if ($property) {
            $data['ghcProperty'] = array(
                'id'           => (int)$property->id,
                'id_hotel'     => (int)$property->id_hotel,
                'google_status'=> (int)$property->google_status,
            );

            foreach (QghcRoomType::getMappingsForHotel($idHotel) as $idRoomType) {
                $data['roomTypeMappings'][$idRoomType] = true;
            }
        }

        $objRoomType       = new HotelRoomType();
        $data['roomTypes'] = (array) $objRoomType->getRoomTypeByHotelId((int)$idHotel, (int)$idLang);

        /* Collect missing required fields for inline UI display. */
        $hotelAddress = HotelBranchInformation::getAddress((int)$idHotel, (int)$idLang);

        if (empty($hotelAddress['address1'])) {
            $data['missingFields'][] = self::l('Street address');
        }
        if (empty($hotelAddress['city'])) {
            $data['missingFields'][] = self::l('City');
        }
        if (empty($hotelAddress['id_country'])) {
            $data['missingFields'][] = self::l('Country');
        }
        if (!empty($hotelAddress['id_country']) && Country::containsStates((int)$hotelAddress['id_country']) && empty($hotelAddress['id_state'])) {
            $data['missingFields'][] = self::l('State / Region');
        }

        if (!self::hasValidCoordinates($idHotel)) {
            $data['missingFields'][] = self::l('Latitude and Longitude');
        }

        return $data;
    }

    /**
     * Returns the Google-assigned hotel ID for a property, or empty string if not set.
     *
     * @param int $idHotel
     * @return string
     */
    public static function getGoogleHotelId($idHotel)
    {
        $property = QghcProperty::getByHotelId($idHotel);
        if (!$property || !$property->id_google_hotel) {
            return '';
        }
        return (string)$property->id_google_hotel;
    }

    /**
     * Maps the Channel Manager's own `google_status` values (0=NOT_SENT, 1=ACTIVE,
     * 2=PENDING, 3=FAILED) onto QghcProperty's internal constants. These two numbering
     * schemes are NOT the same (CM's ACTIVE=1 happens to match our CONNECTED=1, but
     * CM's PENDING=2 and FAILED=3 do not match our PENDING=0/FAILED=2) — never store or
     * compare the CM's raw value directly against QghcProperty::GOOGLE_STATUS_* elsewhere.
     *
     * @param int $cmStatus
     * @return int One of QghcProperty::GOOGLE_STATUS_*
     */
    private static function mapGoogleStatus($cmStatus)
    {
        switch ((int) $cmStatus) {
            case 1: // ACTIVE
                return QghcProperty::GOOGLE_STATUS_CONNECTED;
            case 3: // FAILED
                return QghcProperty::GOOGLE_STATUS_FAILED;
            case 0: // NOT_SENT
            case 2: // PENDING
            default:
                return QghcProperty::GOOGLE_STATUS_PENDING;
        }
    }

    /**
     * Applies the property/room-type Google IDs and status pushed by the Channel Manager's
     * `google_status` webservice callback.
     *
     * @param array $values Keys: google_status (0-3, see mapGoogleStatus()), failure_reason,
     *                       properties (id_hotel => id_google_hotel), room_types (id_product => id_google_room_type), send_mail
     * @return array Keys: google_status, updated_properties (int[]), updated_room_types (int[])
     */
    public static function applyGoogleStatusUpdate(array $values)
    {
        $googleStatus  = self::mapGoogleStatus(isset($values['google_status']) ? $values['google_status'] : 0);
        $failureReason = ($googleStatus === QghcProperty::GOOGLE_STATUS_FAILED && !empty($values['failure_reason']))
            ? (string) $values['failure_reason']
            : null;
        $properties   = (isset($values['properties']) && is_array($values['properties'])) ? $values['properties'] : array();
        $roomTypes    = (isset($values['room_types']) && is_array($values['room_types'])) ? $values['room_types'] : array();
        $sendMail     = !empty($values['send_mail']);

        $updatedProperties = array();
        foreach ($properties as $idHotel => $idGoogleHotel) {
            $idHotel = (int)$idHotel;
            if (!$idHotel) {
                continue;
            }

            $property = QghcProperty::getByHotelId($idHotel);
            if (!$property) {
                continue;
            }

            $property->applyGoogleStatus($idGoogleHotel, $googleStatus, $failureReason);
            $updatedProperties[] = $idHotel;

            if ($sendMail) {
                if ($googleStatus === QghcProperty::GOOGLE_STATUS_CONNECTED) {
                    self::sendGoogleHotelLiveEmail($idHotel);
                } elseif ($googleStatus === QghcProperty::GOOGLE_STATUS_FAILED) {
                    self::sendGoogleHotelFailedEmail($idHotel, $failureReason);
                }
            }
        }

        $updatedRoomTypes = array();
        foreach ($roomTypes as $idProduct => $idGoogleRoomType) {
            $idProduct = (int)$idProduct;
            if (!$idProduct) {
                continue;
            }

            $roomType = QghcRoomType::getByRoomTypeId($idProduct);
            if (!$roomType) {
                continue;
            }

            $roomType->applyGoogleRoomType($idGoogleRoomType);
            $updatedRoomTypes[] = $idProduct;
        }

        return array(
            'google_status'      => $googleStatus,
            'updated_properties' => $updatedProperties,
            'updated_room_types' => $updatedRoomTypes,
        );
    }

    /**
     * Emails the shop owner that a hotel is now live on Google Hotel Center.
     * Best-effort — Mail::Send() failures must never fail the webservice response
     * that triggered this (see applyGoogleStatusUpdate()).
     *
     * @param int $idHotel
     */
    private static function sendGoogleHotelLiveEmail($idHotel)
    {
        $shopEmail = (string) Configuration::get('PS_SHOP_EMAIL');
        if (!Validate::isEmail($shopEmail)) {
            return;
        }

        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $idShop = (int) Configuration::get('PS_SHOP_DEFAULT');

        $idCategory = (int) Db::getInstance()->getValue(
            'SELECT `id_category` FROM `' . _DB_PREFIX_ . 'htl_branch_info` WHERE `id` = ' . (int) $idHotel
        );
        $hotelUrl = $idCategory
            ? Context::getContext()->link->getCategoryLink(
                new Category($idCategory, $idLang, $idShop),
                null,
                $idLang,
                null,
                $idShop
            )
            : '';

        Mail::Send(
            $idLang,
            'google_hotel_live',
            Mail::l('Your hotel is now live on Google Hotel Center', $idLang),
            array(
                '{hotel_name}' => self::getHotelName($idHotel, $idLang),
                '{hotel_url}' => $hotelUrl,
            ),
            $shopEmail,
            (string) Configuration::get('PS_SHOP_NAME'),
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'qlogooglehotelconnector/mails/'
        );
    }

    /**
     * Emails the shop owner that a hotel FAILED Google Hotel Center verification.
     * Best-effort — Mail::Send() failures must never fail the webservice response
     * that triggered this (see applyGoogleStatusUpdate()).
     *
     * @param int         $idHotel
     * @param string|null $failureReason
     */
    private static function sendGoogleHotelFailedEmail($idHotel, $failureReason)
    {
        $shopEmail = (string) Configuration::get('PS_SHOP_EMAIL');
        if (!Validate::isEmail($shopEmail)) {
            return;
        }

        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');

        Mail::Send(
            $idLang,
            'google_hotel_failed',
            Mail::l('Google Hotel Center could not verify your hotel', $idLang),
            array(
                '{hotel_name}' => self::getHotelName($idHotel, $idLang),
                '{failure_reason}' => $failureReason ? $failureReason : self::l('No specific reason was provided.'),
            ),
            $shopEmail,
            (string) Configuration::get('PS_SHOP_NAME'),
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'qlogooglehotelconnector/mails/'
        );
    }

    /**
     * Returns the hotel display name for the given language.
     *
     * @param int $idHotel
     * @param int $idLang
     * @return string
     */
    public static function getHotelName($idHotel, $idLang)
    {
        return (string)Db::getInstance()->getValue(
            'SELECT `hotel_name` FROM `' . _DB_PREFIX_ . 'htl_branch_info_lang`
             WHERE `id` = ' . (int)$idHotel . ' AND `id_lang` = ' . (int)$idLang
        );
    }

    /**
     * Returns only the room types actually enrolled in Google Hotel for this hotel.
     *
     * @param int $idHotel
     * @param int $idLang
     * @return array
     */
    public static function getHotelRoomTypesForSdm($idHotel, $idLang)
    {
        return Db::getInstance()->executeS(
            'SELECT hrt.`id_product`, pl.`name` AS room_name,
                    hrt.`id_google_room_type`,
                    hrt.`max_adults`, hrt.`max_children`
             FROM `' . _DB_PREFIX_ . 'htl_room_type` hrt
             INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                 ON pl.`id_product` = hrt.`id_product`
                AND pl.`id_lang` = ' . (int)$idLang . '
             WHERE hrt.`id_hotel` = ' . (int)$idHotel . '
               AND hrt.`is_google_hotel_enabled` = 1'
        );
    }

    /**
     * Returns the hotel ID for a room type product, or null if not found.
     * Used by ARI change-tracking hooks in the main module class.
     *
     * @param int $idProduct
     * @return int|null
     */
    public static function getHotelIdForProduct($idProduct)
    {
        $objHotelRoomType = new HotelRoomType();
        $roomTypeInfo     = $objHotelRoomType->getRoomTypeInfoByIdProduct($idProduct);
        return $roomTypeInfo ? (int)$roomTypeInfo['id_hotel'] : null;
    }

    /**
     * Builds the Google Hotel property + room type feed for the `google-hotel-properties` webservice endpoint.
     * Cross-domain query (htl_branch_info, address, htl_room_type, product_lang) — kept as raw SQL.
     *
     * @return string JSON-encoded feed
     */
    public static function getPropertiesFeed()
    {
        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $idShop = (int) Configuration::get('PS_SHOP_DEFAULT');

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT
                hbi.`id` AS id_pms_property,

                hbl.`hotel_name`,
                COALESCE(hbl.`description`, \'\')  AS `description`,
                COALESCE(hbi.`email`, \'\')         AS `email`,
                hbi.`latitude`,
                hbi.`longitude`,
                hbi.`id_category`,

                COALESCE(a.`address1`, \'\') AS `address1`,
                COALESCE(a.`address2`, \'\') AS `address2`,
                COALESCE(a.`city`, \'\')     AS `city`,
                COALESCE(a.`postcode`, \'\') AS `zip_code`,
                COALESCE(a.`phone`, \'\')    AS `phone`,

                COALESCE(c.`iso_code`, \'\')    AS `country_code`,
                COALESCE(c.`call_prefix`, \'\') AS `dial_code`,

                COALESCE(s.`name`, \'\') AS `state`,

                hrt.`id_product`          AS id_pms_room_type,
                COALESCE(pl.`name`, \'\') AS room_type_name,

                hrt.`max_adults`,
                hrt.`max_children`,
                hrt.`adults` AS base_occupancy,

                (
                    SELECT COUNT(*)
                    FROM `' . _DB_PREFIX_ . 'htl_room_information` hri
                    WHERE hri.`id_product` = hrt.`id_product`
                      AND hri.`id_hotel`   = hbi.`id`
                      AND hri.`id_status` != ' . (int) HotelRoomInformation::STATUS_INACTIVE . '
                ) AS total_rooms

            FROM `' . _DB_PREFIX_ . 'htl_branch_info` hbi

            LEFT JOIN `' . _DB_PREFIX_ . 'htl_branch_info_lang` hbl
                ON hbl.`id` = hbi.`id` AND hbl.`id_lang` = ' . $idLang . '

            LEFT JOIN `' . _DB_PREFIX_ . 'address` a
                ON a.`id_hotel` = hbi.`id` AND a.`deleted` = 0

            LEFT JOIN `' . _DB_PREFIX_ . 'country` c
                ON c.`id_country` = a.`id_country`

            LEFT JOIN `' . _DB_PREFIX_ . 'state` s
                ON s.`id_state` = a.`id_state`

            INNER JOIN `' . _DB_PREFIX_ . 'htl_room_type` hrt
                ON hrt.`id_hotel` = hbi.`id`
               AND hrt.`is_google_hotel_enabled` = 1

            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON pl.`id_product` = hrt.`id_product`
               AND pl.`id_lang`    = ' . $idLang . '
               AND pl.`id_shop`    = ' . $idShop . '

            WHERE hbi.`is_google_hotel_enabled` = 1

            ORDER BY hbi.`id` ASC, hrt.`id_product` ASC'
        );

        if (!$rows) {
            return json_encode(array('success' => true, 'data' => array()));
        }

        $currency = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT `iso_code` FROM `' . _DB_PREFIX_ . 'currency`
             WHERE `id_currency` = ' . (int) Configuration::get('PS_CURRENCY_DEFAULT')
        );
        $currencyCode = $currency ? $currency['iso_code'] : '';

        $timezone = Configuration::get('PS_TIMEZONE');
        if (!$timezone) {
            $timezone = @date_default_timezone_get();
        }

        // Global setting (not per-hotel) — tells the CM whether hotel_url below is a
        // pretty rewritten slug or a plain index.php?...&id_category=... query string.
        $isFriendlyUrl = (bool) Configuration::get('PS_REWRITING_SETTINGS');

        $hotels = array();
        foreach ($rows as $row) {
            $idHotel = (int) $row['id_pms_property'];

            if (!isset($hotels[$idHotel])) {
                $idCategory = (int) $row['id_category'];
                $hotelUrl = $idCategory
                    ? Context::getContext()->link->getCategoryLink(
                        new Category($idCategory, $idLang, $idShop),
                        null,
                        $idLang,
                        null,
                        $idShop
                    )
                    : '';

                $hotels[$idHotel] = array(
                    'id'              => (string) $idHotel,
                    'name'            => $row['hotel_name'],
                    'property_type'   => 'hotel',
                    'description'     => trim(strip_tags($row['description'])),
                    'email'           => $row['email'],
                    'phone'           => $row['phone'],
                    'dial_code'       => $row['dial_code'],
                    'currency'        => $currencyCode,
                    'country_code'    => $row['country_code'],
                    'state'           => $row['state'],
                    'city'            => $row['city'],
                    'address1'        => $row['address1'],
                    'address2'        => $row['address2'],
                    'zip_code'        => $row['zip_code'],
                    'latitude'        => $row['latitude'],
                    'longitude'       => $row['longitude'],
                    'timezone'        => $timezone,
                    'currency_code'   => $currencyCode,
                    'is_friendly_url' => $isFriendlyUrl,
                    'hotel_url'       => $hotelUrl,
                    'room_types'      => array(),
                );
            }

            $hotels[$idHotel]['room_types'][] = array(
                'id'             => (string) $row['id_pms_room_type'],
                'id_property'    => (string) $idHotel,
                'name'           => $row['room_type_name'],
                'total_rooms'    => (int) $row['total_rooms'],
                'base_occupancy' => (int) $row['base_occupancy'],
                'max_adults'     => (int) $row['max_adults'],
                'max_children'   => (int) $row['max_children'],
                'max_infants'    => 0,
            );
        }

        return json_encode(array('success' => true, 'data' => array_values($hotels)));
    }

    /**
     * Processes the GHC enable/disable form submission after a hotel is saved.
     * Uses a static guard so multiple hooks firing in the same request only run once.
     *
     * @param int $idHotel
     */
    public static function processDataSave($idHotel)
    {
        static $dataSaved = false;

        if ($dataSaved) {
            return;
        }

        $hotelStatus = Tools::getValue('hotel_status');

        if (false === $hotelStatus || !$idHotel) {
            return;
        }

        $dataSaved = true;

        if (0 === (int) $hotelStatus) {
            self::disable($idHotel);
            return;
        }

        $idLang      = (int) Context::getContext()->language->id;
        $fieldErrors = self::validateRequiredFields($idHotel, $idLang);
        if (!empty($fieldErrors)) {
            return;
        }

        $selectedRoomTypes = Tools::getValue('room_types', array());
        $selectedRoomTypes = is_array($selectedRoomTypes)
            ? array_unique(array_map('intval', $selectedRoomTypes))
            : array();

        if (empty($selectedRoomTypes)) {
            return;
        }

        self::enable($idHotel, $selectedRoomTypes);
    }
}
