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

class QcmcBooking extends QmkWebService
{
    public function getBookingList($params)
    {
        $output = '';
        $bookings = array();
        if($allOrders = $this->getAllOrdersId($params)) {
            $objHotelBookingDetail = new HotelBookingDetail();
            $objServiceProductOrderDetail = new ServiceProductOrderDetail();
            foreach ($allOrders as $order) {
                $booking = array();
                $booking['id_booking'] = $order['id_order'];
                $objOrder = new Order($booking['id_booking']);
                $bookingDetails = $objOrder->getProductsDetail();

                $objOrderState = new OrderState($objOrder->current_state);
                $objCustomer = new Customer($objOrder->id_customer);
                if($customerAddress = $this->getCustomerAddressByIdCutomer($objOrder->id_customer)) {
                    if(!Validate::isLoadedObject($customerState = new State($customerAddress['id_state']))) {
                        $customerState = array();
                    }
                } else {
                    $customerAddress = array();
                }
                $booking['id_property'] = (int)$params['id_property'];

                if(isset($objOrder->id_channel_manager_booking) && $objOrder->id_channel_manager_booking) {
                    $booking['id_channel_manager_booking'] = $objOrder->id_channel_manager_booking;
                }

                $booking['currency'] = Currency::getCurrency($objOrder->id_currency)['iso_code'];
                $booking['booking_status'] = $this->getBookingStatus($objOrderState, $objOrder);
                $booking['payment_status'] = '';
                $booking['source'] = $objOrder->source;
                $booking['remark'] = '';
                $booking['number_of_rooms'] = '';
                $booking['booking_date'] = $objOrder->date_add;
                $booking['modification_date'] = $objOrder->date_upd;

                $booking['guest_detail']['firstname'] = $objCustomer->firstname;
                $booking['guest_detail']['lastname'] = $objCustomer->lastname;
                $booking['guest_detail']['email'] = $objCustomer->email;
                $booking['guest_detail']['phone'] = $objCustomer->phone;
                $booking['guest_detail']['address'] = isset($customerAddress['address1']) ? $customerAddress['address1'] : '';
                $booking['guest_detail']['state'] = isset($customerState->name)?$customerState->name : '';
                $booking['guest_detail']['city'] = isset($customerAddress['city']) ? $customerAddress['city'] : '';
                $booking['guest_detail']['zip'] = isset($customerAddress['postcode']) ? $customerAddress['postcode'] : '';
                $booking['guest_detail']['state_code'] = isset($customerState->iso_code)?$customerState->iso_code: '';
                $booking['guest_detail']['country_code'] = isset($customerState->id_country) ? Country::getIsoById($customerState->id_country) : '';

                $booking['price_details']['total_price_with_tax'] = $objOrder->total_paid_tax_incl;
                $booking['price_details']['total_tax'] = Tools::ps_round(($objOrder->total_paid_tax_incl - $objOrder->total_paid_tax_excl), _PS_PRICE_COMPUTE_PRECISION_);
                $booking['price_details']['total_paid'] = $objOrder->total_paid_real;

                $roomTypes = array();

                foreach ($bookingDetails as $bookingRow) {
                    if ($bookingRow['is_booking_product']) {
                        foreach ($objHotelBookingDetail->getBookedRoomsByIdOrderDetail($bookingRow['id_order_detail'], $bookingRow['product_id']) as $value) {
                            $productId = $bookingRow['product_id'] ?? '';
                            $dateFrom  = !empty($value['date_from']) ? date('Y-m-d', strtotime($value['date_from'])) : '';
                            $dateTo    = !empty($value['date_to']) ? date('Y-m-d', strtotime($value['date_to'])) : '';

                            $key = $productId . '_' . $dateFrom . '_' . $dateTo;


                            if (!isset($roomTypes[$key])) {
                                $roomTypes[$key] = array();
                            }

                            if (!isset($roomTypes[$key]['rooms']) && isset($roomTypes[$key]['rooms'])) {
                                $roomTypes[$key]['rooms'] = array();
                            }

                            if (!isset($roomTypes[$key]['total_price_with_tax'])) {
                                $roomTypes[$key]['total_price_with_tax'] = $value['total_price_tax_incl'];
                            } else {
                                $roomTypes[$key]['total_price_with_tax'] += $value['total_price_tax_incl'];
                            }

                            if (!isset($roomTypes[$key]['total_tax'])) {
                                $roomTypes[$key]['total_tax'] = $value['total_price_tax_excl'];
                            } else {
                                $roomTypes[$key]['total_tax'] += $value['total_price_tax_excl'];
                            }

                            $roomTypes[$key]['id_room_type'] = $bookingRow['product_id'];
                            $roomTypes[$key]['id_rate_plan'] = '';
                            $roomTypes[$key]['check_in_date'] = $value['date_from'];
                            $roomTypes[$key]['check_out_date'] = $value['date_to'];

                            $data = array();
                            $data['total_price_with_tax'] = $value['total_price_tax_incl'];
                            $data['total_tax'] = $value['total_price_tax_excl'];

                            $data['occupancy'] = array(
                                'adults' => $value['adults'],
                                'children' => $value['children'],
                                'children_ages' => $value['child_ages'],
                                'infants' => '',
                            );

                            $additionalServices = $objServiceProductOrderDetail->getroomTypeServiceProducts(
                                $objOrder->id,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                null,
                                0,
                                0,
                                $value['id']
                            );

                            $serviceData = array();
                            $additionalServices = array_shift($additionalServices);
                            if (is_array($additionalServices) && count($additionalServices) && isset($additionalServices['additional_services'])) {
                                foreach ($additionalServices['additional_services'] as $service) {
                                    $serviceItem = array();
                                    $serviceItem['name'] = $service['name'];
                                    $serviceItem['quantity'] = $service['quantity'];
                                    $serviceItem['total_price_with_tax'] = $service['total_price_tax_incl'];
                                    $serviceItem['total_tax'] = ($service['total_price_tax_incl'] - $service['total_price_tax_excl']);

                                    if (WebserviceSpecificManagementBookings::API_SERVICE_PRICE_MODE_PER_DAY == $service['price_calculation_method']) {
                                        $serviceItem['price_mode'] = 'per_night';
                                    } else {
                                        $serviceItem['price_mode'] = 'per_stay';
                                    }

                                    $serviceItem['price_per_unit'] = $serviceItem['total_price_with_tax']-$serviceItem['total_tax'];
                                    $serviceItem['taxes'] = $service['product_tax'];
                                    $serviceData[] = $serviceItem;
                                }
                            }

                            $data['services'] = $serviceData;
                            $roomTypes[$key]['rooms'][] = $data;

                            // initially set number_of_rooms as 0
                            if (!isset($roomTypes[$key]['number_of_rooms'])) {
                                $roomTypes[$key]['number_of_rooms'] = 0;
                            }

                            // if a room is refunded or cancelled then the count will not be sent to the api response
                            // as we can not change the status room type booking wise for the API response
                            if (!$value['is_refunded']
                                && isset($roomTypes[$key]['number_of_rooms'])
                            ) {
                                $roomTypes[$key]['number_of_rooms'] += 1;
                            }
                        }
                    }
                }

                $booking['room_bookings'] = array_values($roomTypes);
                $bookings[] = $booking;
            }
        }
        return json_encode(array(
            'success' => true,
            'data' => $bookings
        ));
    }

    private function getAllOrdersId($params = [])
    {
        if (count($params)) {
            $sql = 'SELECT distinct o.`id_order` FROM `'._DB_PREFIX_.'orders` o LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON o.`id_order` = hbd.`id_order` WHERE true ';

            if (isset($params['id_property']) && $params['id_property']) {
                $sql .= " AND hbd.`id_hotel` = ".(int)$params['id_property'];
            }

            if (isset($params['check_in']) && $params['check_in']) {
                if (isset($params['check_in']['gte']) && $params['check_in']['gte']) {
                    $sql .= " AND hbd.`date_to` >= '".pSQL($params['check_in']['gte'])."'";
                }
            }

            if (isset($params['check_in']) && $params['check_in']) {
                if (isset($params['check_in']['gt']) && $params['check_in']['gt']) {
                    $sql .= " AND hbd.`date_to` > '".pSQL($params['check_in']['gt'])."'";
                }
            }

            if (isset($params['check_in']) && $params['check_in']) {
                if (isset($params['check_in']['lte']) && $params['check_in']['lte']) {
                    $sql .= " AND hbd.`date_to` <= '".pSQL($params['check_in']['lte'])."'";
                }
            }

            if (isset($params['check_in']) && $params['check_in']) {
                if (isset($params['check_in']['lt']) && $params['check_in']['lt']) {
                    $sql .= " AND hbd.`date_to` < '".pSQL($params['check_in']['lt'])."'";
                }
            }

            if (isset($params['check_out']) && $params['check_out']) {
                if (isset($params['check_out']['gte']) && $params['check_out']['gte']) {
                    $sql .= " AND hbd.`date_to` >= '".pSQL($params['check_out']['gte'])."'";
                }
            }

            if (isset($params['check_out']) && $params['check_out']) {
                if (isset($params['check_out']['gt']) && $params['check_out']['gt']) {
                    $sql .= " AND hbd.`date_to` > '".pSQL($params['check_out']['gt'])."'";
                }
            }

            if (isset($params['check_out']) && $params['check_out']) {
                if (isset($params['check_out']['lte']) && $params['check_out']['lte']) {
                    $sql .= " AND hbd.`date_to` <= '".pSQL($params['check_out']['lte'])."'";
                }
            }

            if (isset($params['check_out']) && $params['check_out']) {
                if (isset($params['check_out']['lt']) && $params['check_out']['lt']) {
                    $sql .= " AND hbd.`date_to` < '".pSQL($params['check_out']['lt'])."'";
                }
            }

            if (isset($params['date_updated']) && $params['date_updated']) {
                if (isset($params['date_updated']['gte']) && $params['date_updated']['gte']) {
                    $sql .= " AND o.`date_upd` >= '".pSQL($params['date_updated']['gte'])."'";
                }
            }

            if (isset($params['date_updated']) && $params['date_updated']) {
                if (isset($params['date_updated']['gt']) && $params['date_updated']['gt']) {
                    $sql .= " AND o.`date_upd` > '".pSQL($params['date_updated']['gt'])."'";
                }
            }

            if (isset($params['date_updated']) && $params['date_updated']) {
                if (isset($params['date_updated']['lte']) && $params['date_updated']['lte']) {
                    $sql .= " AND o.`date_upd` <= '".pSQL($params['date_updated']['lte'])."'";
                }
            }

            if (isset($params['date_updated']) && $params['date_updated']) {
                if (isset($params['date_updated']['lt']) && $params['date_updated']['lt']) {
                    $sql .= " AND o.`date_upd` < '".pSQL($params['date_updated']['lt'])."'";
                }
            }

            return Db::getInstance()->executeS($sql);
        } else {
            return Db::getInstance()->executeS('SELECT `id_order` FROM `'._DB_PREFIX_.'orders` WHERE 1');
        }
    }

    private function getCustomerAddressByIdCutomer($idCustomer = 0)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'address` WHERE id_customer = '.(int)$idCustomer
        );
    }

    private function getBookingStatus($objOrderState, $objOrder)
    {
        $dateAdd = new DateTime($objOrder->date_add);
        $dateUpd = new DateTime($objOrder->date_upd);

        $isMoreThanOneMinute = (abs($dateAdd->getTimestamp() - $dateUpd->getTimestamp()) > 60);


        if (($objOrderState->id == Configuration::get('PS_OS_CANCELED')) || ($objOrderState->id == Configuration::get('PS_OS_REFUND'))) {
            return 'cancelled';
        } elseif ($isMoreThanOneMinute) {
            return 'modified';
        } else {
            return 'new';
        }
    }
}
