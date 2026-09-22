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

class QghcSdmService
{
    /** Fallback check-in/check-out time-of-day when a hotel hasn't set its own. */
    const DEFAULT_CHECKIN_TIME  = '15:00:00';
    const DEFAULT_CHECKOUT_TIME = '11:00:00';

    /**
     * Builds hotel SDM data for the hotel landing/category page.
     *
     * @param Context $context
     * @return array|null  schema.org/Hotel array, or null if not applicable.
     */
    public static function buildLandingPageData(Context $context)
    {
        $idCategory = (int)Tools::getValue('id_category');
        if (!$idCategory) {
            return null;
        }

        $idHotel = (int)HotelBranchInformation::getHotelIdByIdCategory($idCategory);
        if (!$idHotel) {
            return null;
        }

        $dateFrom = Tools::getValue('date_from');
        $dateTo   = Tools::getValue('date_to');
        if (!$dateFrom || !$dateTo) {
            return null;
        }

        $tsFrom = strtotime($dateFrom);
        $tsTo   = strtotime($dateTo);
        if (false === $tsFrom || false === $tsTo || $tsTo <= $tsFrom) {
            return null;
        }

        $dateFrom = date('Y-m-d', $tsFrom);
        $dateTo   = date('Y-m-d', $tsTo);
        $idLang   = (int)$context->language->id;

        $hotelName  = QghcPropertyService::getHotelName($idHotel, $idLang);
        $hotelInfo  = new HotelBranchInformation($idHotel, $idLang);
        $countryIso = $hotelInfo->id_country ? Country::getIsoById((int)$hotelInfo->id_country) : '';
        $roomTypes  = QghcPropertyService::getHotelRoomTypesForSdm($idHotel, $idLang);

        if (empty($roomTypes)) {
            return null;
        }

        $checkinDateTime  = $dateFrom . 'T' . self::resolveTimeOfDay($hotelInfo->check_in, self::DEFAULT_CHECKIN_TIME);
        $checkoutDateTime = $dateTo   . 'T' . self::resolveTimeOfDay($hotelInfo->check_out, self::DEFAULT_CHECKOUT_TIME);

        $objBookingDetail = new HotelBookingDetail();
        $availData = $objBookingDetail->dataForFrontSearch(array(
            'date_from'        => $dateFrom,
            'date_to'          => $dateTo,
            'hotel_id'         => $idHotel,
            'only_search_data' => 1,
        ));

        $currency      = QghcPropertyService::getDefaultCurrencyIsoCode();
        $containsPlace = array();

        foreach ($roomTypes as $rt) {
            $rtIdProduct = (int)$rt['id_product'];
            $isAvailable = !empty($availData['rm_data'][$rtIdProduct]['data']['available']);

            $offer = array(
                '@type'        => array('Offer', 'LodgingReservation'),
                'checkinTime'  => $checkinDateTime,
                'checkoutTime' => $checkoutDateTime,
                'availability' => $isAvailable
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/SoldOut',
            );

            if ($isAvailable) {
                $priceData = QghcPropertyService::getRoomTypeFinalPriceForSdm(
                    $rtIdProduct,
                    $dateFrom,
                    $dateTo,
                    1
                );
                if (!empty($priceData['total_price_tax_incl'])) {
                    $offer['priceSpecification'] = array(
                        '@type'         => 'CompoundPriceSpecification',
                        'price'         => round((float)$priceData['total_price_tax_incl'], 2),
                        'priceCurrency' => $currency,
                    );
                }
            }

            $roomIdentifier = (!empty($rt['id_google_room_type']))
                ? (string)$rt['id_google_room_type']
                : (string)$rtIdProduct;

            $roomEntry = array(
                '@type'      => array('HotelRoom', 'Product'),
                'identifier' => $roomIdentifier,
                'name'       => $rt['room_name'],
            );

            $occupancy = self::buildOccupancy($rt);
            if (null !== $occupancy) {
                $roomEntry['occupancy'] = $occupancy;
            }

            $roomEntry['offers'] = $offer;

            $containsPlace[] = $roomEntry;
        }

        if (empty($containsPlace)) {
            return null;
        }

        $makesOffer = self::buildMakesOffer($containsPlace, $checkinDateTime, $checkoutDateTime);

        return self::buildHotelSdmArray($idHotel, $hotelName, $hotelInfo, $countryIso, $containsPlace, $makesOffer);
    }

    /**
     * Builds hotel SDM for the hotel landing/category page, as Microdata.
     *
     * @param Context $context
     * @return string  Hidden Microdata markup, or empty string if not applicable.
     */
    public static function buildLandingPageMicrodata(Context $context)
    {
        $data = self::buildLandingPageData($context);
        return $data ? self::wrapMicrodata($data) : '';
    }

    /**
     * Builds hotel SDM data for each hotel in the checkout cart.
     *
     * @param Context $context
     * @return array  List of schema.org/Hotel arrays, one per hotel in the cart.
     */
    public static function buildCheckoutData(Context $context)
    {
        $idCart = (int)$context->cart->id;
        if (!$idCart) {
            return array();
        }

        $objCartData = new HotelCartBookingData();
        $cartRows    = $objCartData->getCartCurrentDataByCartId($idCart);
        if (empty($cartRows)) {
            return array();
        }

        $idLang   = (int)$context->language->id;
        $currency = QghcPropertyService::getDefaultCurrencyIsoCode();

        $hotelProducts = array();
        foreach ($cartRows as $row) {
            $idHotel   = (int)$row['id_hotel'];
            $idProduct = (int)$row['id_product'];
            $dateKey   = $row['date_from'] . '|' . $row['date_to'];

            if (!isset($hotelProducts[$idHotel][$idProduct][$dateKey])) {
                $hotelProducts[$idHotel][$idProduct][$dateKey] = array(
                    'date_from' => $row['date_from'],
                    'date_to'   => $row['date_to'],
                    'qty'       => 0,
                );
            }
            $hotelProducts[$idHotel][$idProduct][$dateKey]['qty']++;
        }

        $hotels = array();

        foreach ($hotelProducts as $idHotel => $products) {
            $hotelName  = QghcPropertyService::getHotelName($idHotel, $idLang);
            $hotelInfo  = new HotelBranchInformation($idHotel, $idLang);
            $countryIso = $hotelInfo->id_country ? Country::getIsoById((int)$hotelInfo->id_country) : '';

            // Only room types actually enrolled in Google Hotel may appear in the markup.
            $enrolledRooms = array();
            foreach (QghcPropertyService::getHotelRoomTypesForSdm($idHotel, $idLang) as $enrolledRow) {
                $enrolledRooms[(int)$enrolledRow['id_product']] = $enrolledRow;
            }
            $containsPlace = array();

            foreach ($products as $idProduct => $dateGroups) {
                if (!isset($enrolledRooms[$idProduct])) {
                    continue;
                }
                $roomName  = $enrolledRooms[$idProduct]['room_name'];
                $occupancy = self::buildOccupancy($enrolledRooms[$idProduct]);

                foreach ($dateGroups as $info) {
                    $dateFrom = date('Y-m-d', strtotime($info['date_from']));
                    $dateTo   = date('Y-m-d', strtotime($info['date_to']));
                    $qty      = (int)$info['qty'];

                    $checkinDateTime  = $dateFrom . 'T' . self::resolveTimeOfDay($hotelInfo->check_in, self::DEFAULT_CHECKIN_TIME);
                    $checkoutDateTime = $dateTo   . 'T' . self::resolveTimeOfDay($hotelInfo->check_out, self::DEFAULT_CHECKOUT_TIME);

                    $priceData = QghcPropertyService::getRoomTypeFinalPriceForSdm(
                        $idProduct,
                        $dateFrom,
                        $dateTo,
                        $qty
                    );

                    $offer = array(
                        '@type'        => array('Offer', 'LodgingReservation'),
                        'checkinTime'  => $checkinDateTime,
                        'checkoutTime' => $checkoutDateTime,
                        'availability' => 'https://schema.org/InStock',
                    );

                    if (!empty($priceData['total_price_tax_incl'])) {
                        $offer['priceSpecification'] = array(
                            '@type'         => 'CompoundPriceSpecification',
                            'price'         => round((float)$priceData['total_price_tax_incl'], 2),
                            'priceCurrency' => $currency,
                        );
                    }

                    $roomIdentifier = (!empty($enrolledRooms[$idProduct]['id_google_room_type']))
                        ? (string)$enrolledRooms[$idProduct]['id_google_room_type']
                        : (string)$idProduct;

                    $roomEntry = array(
                        '@type'      => array('HotelRoom', 'Product'),
                        'identifier' => $roomIdentifier,
                        'name'       => $roomName,
                    );

                    if (null !== $occupancy) {
                        $roomEntry['occupancy'] = $occupancy;
                    }

                    $roomEntry['offers'] = $offer;

                    $containsPlace[] = $roomEntry;
                }
            }

            if (empty($containsPlace)) {
                continue;
            }

            $lastCheckin  = $containsPlace[count($containsPlace) - 1]['offers']['checkinTime'];
            $lastCheckout = $containsPlace[count($containsPlace) - 1]['offers']['checkoutTime'];
            $makesOffer   = self::buildMakesOffer($containsPlace, $lastCheckin, $lastCheckout);

            $hotels[] = self::buildHotelSdmArray($idHotel, $hotelName, $hotelInfo, $countryIso, $containsPlace, $makesOffer);
        }

        return $hotels;
    }

    /**
     * Builds hotel SDM for each hotel in the checkout cart, as Microdata.
     *
     * @param Context $context
     * @return string  One or more hidden Microdata blocks, or empty string.
     */
    public static function buildCheckoutMicrodata(Context $context)
    {
        $output = '';
        foreach (self::buildCheckoutData($context) as $hotelData) {
            $output .= self::wrapMicrodata($hotelData);
        }
        return $output;
    }

    /**
     * Assembles a schema.org/Hotel array — the data wrapMicrodata() serializes.
     *
     * @param int                    $idHotel
     * @param string                 $hotelName
     * @param HotelBranchInformation $hotelInfo
     * @param string                 $countryIso
     * @param array                  $containsPlace
     * @param array                  $makesOffer     Hotel-level Offer/LodgingReservation (required by Google's spec).
     * @return array
     */
    public static function buildHotelSdmArray($idHotel, $hotelName, $hotelInfo, $countryIso, $containsPlace, $makesOffer)
    {
        $postalAddress = array('@type' => 'PostalAddress');
        if ($hotelInfo->address) {
            $postalAddress['streetAddress'] = $hotelInfo->address;
        }
        if ($hotelInfo->city) {
            $postalAddress['addressLocality'] = $hotelInfo->city;
        }
        if (!empty($hotelInfo->id_country) && !empty($hotelInfo->id_state)
            && Country::containsStates((int)$hotelInfo->id_country)
        ) {
            $stateName = State::getNameById((int)$hotelInfo->id_state);
            if ($stateName) {
                $postalAddress['addressRegion'] = $stateName;
            }
        }
        if ($hotelInfo->zipcode) {
            $postalAddress['postalCode'] = $hotelInfo->zipcode;
        }
        if ($countryIso) {
            $postalAddress['addressCountry'] = $countryIso;
        }

        $idGoogleHotel = QghcPropertyService::getGoogleHotelId((int)$idHotel);

        return array(
            '@context'      => 'https://schema.org',
            '@type'         => 'Hotel',
            'name'          => $hotelName,
            'identifier'    => $idGoogleHotel ? $idGoogleHotel : (string)$idHotel,
            'address'       => $postalAddress,
            'containsPlace' => $containsPlace,
            'makesOffer'    => $makesOffer,
        );
    }

    /**
     * Builds the required Hotel-level `makesOffer` from the lowest-priced available
     * room, falling back to SoldOut if nothing is available.
     *
     * @param array  $containsPlace
     * @param string $checkinDateTime
     * @param string $checkoutDateTime
     * @return array
     */
    private static function buildMakesOffer($containsPlace, $checkinDateTime, $checkoutDateTime)
    {
        $lowestPrice = null;
        $currency    = null;

        foreach ($containsPlace as $room) {
            $roomOffer = $room['offers'];
            if (!isset($roomOffer['priceSpecification']['price'])) {
                continue;
            }
            $price = (float)$roomOffer['priceSpecification']['price'];
            if (null === $lowestPrice || $price < $lowestPrice) {
                $lowestPrice = $price;
                $currency    = $roomOffer['priceSpecification']['priceCurrency'];
            }
        }

        $makesOffer = array(
            '@type'        => array('Offer', 'LodgingReservation'),
            'checkinTime'  => $checkinDateTime,
            'checkoutTime' => $checkoutDateTime,
            'availability' => (null !== $lowestPrice)
                ? 'https://schema.org/InStock'
                : 'https://schema.org/SoldOut',
        );

        if (null !== $lowestPrice) {
            $makesOffer['priceSpecification'] = array(
                '@type'         => 'CompoundPriceSpecification',
                'price'         => $lowestPrice,
                'priceCurrency' => $currency,
            );
        }

        return $makesOffer;
    }

    /**
     * Builds HotelRoom.occupancy from a room type's max_adults/max_children capacity.
     *
     * @param array $roomRow  Row from QghcPropertyService::getHotelRoomTypesForSdm().
     * @return array|null  Null if the room type has no capacity configured.
     */
    private static function buildOccupancy($roomRow)
    {
        $capacity = (int)($roomRow['max_adults'] ?? 0) + (int)($roomRow['max_children'] ?? 0);
        if ($capacity <= 0) {
            return null;
        }

        return array(
            '@type' => 'QuantitativeValue',
            'value' => $capacity,
        );
    }

    /**
     * Resolves a stored "HH:MM"-style hotel check-in/check-out time to "HH:MM:SS",
     * falling back to a sane industry-default when the hotel hasn't set one.
     *
     * @param string|null $rawTime
     * @param string      $fallback  "HH:MM:SS"
     * @return string
     */
    private static function resolveTimeOfDay($rawTime, $fallback)
    {
        if ($rawTime && false !== strtotime($rawTime)) {
            return date('H:i:s', strtotime($rawTime));
        }
        return $fallback;
    }

    /**
     * Renders the same SDM array as hidden Microdata (itemscope/itemtype/itemprop) —
     * hidden the same way a JSON-LD <script> tag is: never rendered visually, read the
     * same way by Google's structured-data parser either way. Emitted in the body (not
     * <head>, where a <div> is not valid content) via hookDisplayFooterBefore.
     *
     * @param array $sdm  A schema.org/Hotel array from buildHotelSdmArray().
     * @return string
     */
    public static function wrapMicrodata($sdm)
    {
        $html = '<div itemscope itemtype="https://schema.org/Hotel" style="display:none">';
        $html .= self::microdataMeta('identifier', $sdm['identifier']);
        $html .= self::microdataText('name', $sdm['name']);
        $html .= self::microdataAddress($sdm['address']);

        foreach ($sdm['containsPlace'] as $room) {
            $html .= self::microdataRoom($room);
        }

        $html .= self::microdataOffer('makesOffer', $sdm['makesOffer']);
        $html .= '</div>';

        return $html;
    }

    /** @return string A <div itemprop="address" itemscope itemtype=".../PostalAddress"> block. */
    private static function microdataAddress($address)
    {
        $html = '<div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">';
        if (isset($address['streetAddress'])) {
            $html .= self::microdataText('streetAddress', $address['streetAddress']);
        }
        if (isset($address['addressLocality'])) {
            $html .= self::microdataText('addressLocality', $address['addressLocality']);
        }
        if (isset($address['addressRegion'])) {
            $html .= self::microdataText('addressRegion', $address['addressRegion']);
        }
        if (isset($address['postalCode'])) {
            $html .= self::microdataText('postalCode', $address['postalCode']);
        }
        if (isset($address['addressCountry'])) {
            $html .= self::microdataText('addressCountry', $address['addressCountry']);
        }
        $html .= '</div>';
        return $html;
    }

    /** @return string A <div itemprop="containsPlace" itemscope itemtype=".../HotelRoom .../Product"> block.
     *  Dual-typed HotelRoom+Product to match Google's own official Hotel price
     *  structured-data Microdata example verbatim. This also makes Google's unrelated
     *  Merchant Listings feature evaluate the same node against product-feed fields
     *  (gtin/brand/shippingDetails/etc.) that don't apply to a hotel room and will show
     *  as "invalid" there — that is expected and does not affect Hotels-feature
     *  price-accuracy validation, which is what this module is certified against. Do not
     *  drop the Product type to silence Merchant Listings; that breaks containsPlace's
     *  type for the Hotels feature instead (confirmed via Rich Results Test). */
    private static function microdataRoom($room)
    {
        $html = '<div itemprop="containsPlace" itemscope itemtype="https://schema.org/HotelRoom https://schema.org/Product">';
        $html .= self::microdataMeta('identifier', $room['identifier']);
        $html .= self::microdataText('name', $room['name']);

        if (isset($room['occupancy'])) {
            $html .= '<div itemprop="occupancy" itemscope itemtype="https://schema.org/QuantitativeValue">';
            $html .= self::microdataMeta('value', $room['occupancy']['value']);
            $html .= '</div>';
        }

        $html .= self::microdataOffer('offers', $room['offers']);
        $html .= '</div>';
        return $html;
    }

    /** @return string A <div itemprop="$itemprop" itemscope itemtype=".../Offer .../LodgingReservation"> block. */
    private static function microdataOffer($itemprop, $offer)
    {
        $html = '<div itemprop="' . self::esc($itemprop) . '" itemscope itemtype="https://schema.org/Offer https://schema.org/LodgingReservation">';
        $html .= self::microdataMeta('checkinTime', $offer['checkinTime']);
        $html .= self::microdataMeta('checkoutTime', $offer['checkoutTime']);
        $html .= '<link itemprop="availability" href="' . self::esc($offer['availability']) . '">';

        if (isset($offer['priceSpecification'])) {
            $html .= '<div itemprop="priceSpecification" itemscope itemtype="https://schema.org/CompoundPriceSpecification">';
            $html .= self::microdataMeta('price', $offer['priceSpecification']['price']);
            $html .= self::microdataMeta('priceCurrency', $offer['priceSpecification']['priceCurrency']);
            // Every price this module emits is built from total_price_tax_incl (see
            // QghcPropertyService::getRoomTypeFinalPriceForSdm()), so this is always true.
            $html .= self::microdataMeta('valueAddedTaxIncluded', 'True');
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    /** @return string A <meta itemprop="$itemprop" content="$value"> tag — valid HTML5 flow content when itemprop is set. */
    private static function microdataMeta($itemprop, $value)
    {
        return '<meta itemprop="' . self::esc($itemprop) . '" content="' . self::esc($value) . '">';
    }

    /** @return string A <span itemprop="$itemprop">$value</span> tag for text that would normally be visible. */
    private static function microdataText($itemprop, $value)
    {
        return '<span itemprop="' . self::esc($itemprop) . '">' . self::esc($value) . '</span>';
    }

    private static function esc($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
