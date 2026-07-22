<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
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
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

class QieIcalHelper
{
    private $icalProperties = array();
    private $icalParameters = array(
        'summary',
        'location',
        'description',
        'dtstart',
        'dtend'
    );

    private function setProperty($key, $value)
    {
        if (in_array($key, $this->icalParameters)) {
            $filterVal = $this->sanitizeValues($value, $key);
            $this->icalProperties[$key] = $filterVal;
        }
    }

    public function getBookingsICalendarValues($params = array())
    {
        $idOrders = array();
        if ($orders = $this->getBookingOrders($params)) {
            $idOrders = array_column($orders, 'id_order');
        }

        if ($idOrders) {
            $objHtlBooking = new HotelBookingDetail();
            $moduleObj = new QloIcsExport();

            // Start tags ics file
            $icsProperties = array(
                'BEGIN:VCALENDAR',
                'VERSION:2.0',
                'PRODID:-//hacksw/handcal//NONSGML v1.0//EN',
                'CALSCALE:GREGORIAN'
            );

            foreach ($idOrders as $idOrder) {
                if ($bookings = $objHtlBooking->getOrderFormatedBookinInfoByIdOrder($idOrder)) {
                    foreach ($bookings as $booking) {
                        $icsProperties[] = 'BEGIN:VEVENT';

                        $checkIn = date('Y-m-d', strtotime($booking['date_from'])) . ' ' .
                            date('H:i:s', strtotime($booking['check_in_time']));
                        $this->setProperty('dtstart', $checkIn);

                        $checkOut = date('Y-m-d', strtotime($booking['date_to'])) . ' ' .
                            date('H:i:s', strtotime($booking['check_out_time']));
                        $this->setProperty('dtend', $checkOut);

                        // description
                        $objOrder = new Order($booking['id_order']);
                        $descriptionText = $moduleObj->l('Booking details', 'QieIcalHelper') . ' - \n\n';
                        $descriptionText .= $moduleObj->l('Booking Reference', 'QieIcalHelper') . ' : ' .
                            $objOrder->reference . '\n\n';
                        $descriptionText .= $moduleObj->l('Hotel', 'QieIcalHelper') . ' : ' .
                            $booking['hotel_name'] . '\n\n';
                        $descriptionText .= $moduleObj->l('Check-In', 'QieIcalHelper') . ' : ' .
                            date('d F Y', strtotime($checkIn)) . '\n\n';
                        $descriptionText .= $moduleObj->l('Check-Out', 'QieIcalHelper') . ' : ' .
                            date('d F Y', strtotime($checkOut)) . '\n\n';
                        $descriptionText .= $moduleObj->l('Hotel Email', 'QieIcalHelper') . ' : ' .
                            $booking['email'] . '\n\n';
                        $descriptionText .= $moduleObj->l('Hotel Phone', 'QieIcalHelper') . ' : ' .
                            $booking['phone'] . '\n\n';

                        $descriptionHtml = '<p>' . $moduleObj->l('Booking details', 'QieIcalHelper') . ' - ' . '</p>';
                        $descriptionHtml .= '<p><b>' . $moduleObj->l('Booking Reference', 'QieIcalHelper') . ' :</b> ' .
                            $objOrder->reference . '</p>';
                        $descriptionHtml .= '<p><b>' . $moduleObj->l('Hotel', 'QieIcalHelper') . ' :</b> ' .
                            $booking['hotel_name'] . '</p>';
                        $descriptionHtml .= '<p><b>' . $moduleObj->l('Check-In', 'QieIcalHelper') . ' :</b> ' .
                            date('d F Y', strtotime($checkIn)) . '</p>';
                        $descriptionHtml .= '<p><b>' . $moduleObj->l('Check-Out', 'QieIcalHelper') . ' :</b> ' .
                            date('d F Y', strtotime($checkOut)) . '</p>';
                        $descriptionHtml .= '<p><b>' . $moduleObj->l('Hotel Email', 'QieIcalHelper') . ' :</b> ' .
                            $booking['email'] . '</p>';
                        $descriptionHtml .= '<p><b>' . $moduleObj->l('Hotel Phone', 'QieIcalHelper') . ' :</b> ' .
                            $booking['phone'] . '</p>';

                        // add customer name, email and phone number if available
                        if (Validate::isLoadedObject($objCustomer = new Customer($objOrder->id_customer))) {
                            if (
                                isset($objCustomer->firstname) && $objCustomer->firstname
                                && isset($objCustomer->lastname) && $objCustomer->lastname
                            ) {
                                $descriptionHtml .= '<p><b>' . $moduleObj->l('Guest Name', 'QieIcalHelper') . ' :</b> ' .
                                    $objCustomer->firstname . ' ' . $objCustomer->lastname . '</p>';
                            }

                            if (isset($objCustomer->email) && $objCustomer->email) {
                                $descriptionHtml .= '<p><b>' . $moduleObj->l('Guest Email', 'QieIcalHelper') . ' :</b> ' .
                                    $objCustomer->email . '</p>';

                                $descriptionText .= $moduleObj->l('Guest Email', 'QieIcalHelper') . ' : ' .
                                    $objCustomer->email . '\n\n';
                            }

                            if (isset($objOrder->id_address_delivery) && $objOrder->id_address_delivery) {
                                if (
                                    Validate::isLoadedObject(
                                        $objAddress = new Address($objOrder->id_address_delivery)
                                    )
                                ) {
                                    if (isset($objAddress->phone_mobile) && $objAddress->phone_mobile) {
                                        $descriptionHtml .= '<p><b>' . $moduleObj->l('Guest Mobile', 'QieIcalHelper') .
                                            ' :</b> ' . $objAddress->phone_mobile . '</p>';

                                        $descriptionText .= $moduleObj->l('Guest Mobile', 'QieIcalHelper') .
                                            ' : ' . $objAddress->phone_mobile . '\n\n';
                                    }
                                    if (isset($objAddress->phone) && $objAddress->phone) {
                                        $descriptionHtml .= '<p><b>' . $moduleObj->l('Guest Phone', 'QieIcalHelper') .
                                            ' :</b> ' . $objAddress->phone . '</p>';

                                        $descriptionText .= $moduleObj->l('Guest Phone', 'QieIcalHelper') .
                                            ' : ' . $objAddress->phone . '\n\n';
                                    }
                                }
                            }
                        }

                        $descriptionHtml .= '<p><b>' . $moduleObj->l('Booking Created On', 'QieIcalHelper') . ' :</b> ' .
                            date('d F Y', strtotime($booking['date_add'])) . '</p>';

                        $this->setProperty('description', $descriptionText);

                        // location
                        $location = $booking['city'];
                        if ($booking['state']) {
                            $location .= ', ' . $booking['state'];
                        }
                        $location .= ', ' . $booking['country'];

                        $this->setProperty('location', $location);

                        // summary
                        $summary = $booking['room_num'] . ', ' . $booking['room_type'] . ', ' . $booking['hotel_name'];
                        $this->setProperty('summary', $summary);

                        $icalProps = array();
                        foreach ($this->icalProperties as $key => $value) {
                            $icalProps[Tools::strtoupper($key . ($key === 'url' ? ';VALUE=URI' : ''))] = $value;
                            // for html compatible description
                            if (Tools::strtolower($key) == 'description') {
                                $icalProps['X-ALT-DESC;FMTTYPE=text/html'] = $descriptionHtml;
                            }
                        }

                        // set time stamp and uid
                        $icalProps['DTSTAMP'] = $this->formatDateTime('now');
                        $icalProps['UID'] = uniqid();

                        foreach ($icalProps as $key => $value) {
                            $icsProperties[] = "$key:$value";
                        }

                        // end ics tags
                        $icsProperties[] = 'END:VEVENT';
                    }
                }
            }

            $icsProperties[] = 'END:VCALENDAR';
            return implode("\r\n", $icsProperties);
        }

        return false;
    }

    private function sanitizeValues($value, $key = false)
    {
        switch ($key) {
            case 'dtend':
            case 'dtstamp':
            case 'dtstart':
                $value = $this->formatDateTime($value);
                break;
            default:
                $value = $this->escapeStringChars($value);
        }

        return $value;
    }

    private function formatDateTime($time)
    {
        return gmdate('Ymd\THis\Z', strtotime($time)) . '';
    }

    private function escapeStringChars($string)
    {
        return preg_replace('/([\,;])/', '\\\$1', $string);
    }

    // get all booking hotels
    public function getBookingHotels()
    {
        $sql = 'SELECT DISTINCT `id_hotel`, `hotel_name` FROM `' . _DB_PREFIX_ . 'htl_booking_detail`';

        return Db::getInstance()->executeS($sql);
    }

    public function getBookingOrders($params)
    {
        $sql = 'SELECT DISTINCT `id_order` FROM `' . _DB_PREFIX_ . 'htl_booking_detail` WHERE 1';

        // check if some filters are there for ics download
        if (
            isset($params['date_from'])
            && isset($params['date_to'])
            && $params['date_from']
            && $params['date_to']
        ) {
            $dateFrom = date('Y-m-d', strtotime($params['date_from']));
            $dateTo = date('Y-m-d', strtotime($params['date_to']));

            $sql .= ' AND `date_from` >= \'' . pSQL($dateFrom) . '\'' . ' AND `date_to` <= \'' . pSQL($dateTo) . '\'';
        }

        if (isset($params['hotels']) && $params['hotels']) {
            // if all hotels is not selected then add cehck for the hotels
            if (!in_array(0, $params['hotels'])) {
                $hotels = array_map('intval', $params['hotels']);
                $sql .= ' AND `id_hotel` IN (' . implode(',', $hotels) . ')';
            }
        }

        if (isset($params['id_order']) && $params['id_order']) {
            $sql .= ' AND `id_order` = ' . (int) $params['id_order'];
        }

        return Db::getInstance()->executeS($sql);
    }

    public function getOrderIdByReference($fullReference)
    {
        return Db::getInstance()->executeS(
            'SELECT id_order FROM `' . _DB_PREFIX_ . 'orders` 
        WHERE reference = "' . pSQL($fullReference) . '" ORDER BY id_order ASC'
        );
    }
}
