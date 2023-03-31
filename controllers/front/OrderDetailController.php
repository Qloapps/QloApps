<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderDetailControllerCore extends FrontController
{
    public $php_self = 'order-detail';

    public $auth = true;
    public $authRedirection = 'history';
    public $ssl = true;

    /**
     * Initialize order detail controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitMessage')) {
            $idOrder = (int)Tools::getValue('id_order');
            $msgText = Tools::getValue('msgText');

            if (!$idOrder || !Validate::isUnsignedId($idOrder)) {
                $this->errors[] = Tools::displayError('The order is no longer valid.');
            } elseif (empty($msgText)) {
                $this->errors[] = Tools::displayError('The message cannot be blank.');
            } elseif (!Validate::isMessage($msgText)) {
                $this->errors[] = Tools::displayError('This message is invalid (HTML is not allowed).');
            }
            if (!count($this->errors)) {
                $order = new Order($idOrder);
                if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                    //check if a thread already exist
                    $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($this->context->customer->email, $order->id);
                    $id_product = (int)Tools::getValue('id_product');
                    $cm = new CustomerMessage();
                    if (!$id_customer_thread) {
                        $ct = new CustomerThread();
                        $ct->id_contact = 0;
                        $ct->id_customer = (int)$order->id_customer;
                        $ct->id_shop = (int)$this->context->shop->id;
                        if ($id_product && $order->orderContainProduct($id_product)) {
                            $ct->id_product = $id_product;
                        }
                        $ct->id_order = (int)$order->id;
                        $ct->id_lang = (int)$this->context->language->id;
                        $ct->email = $this->context->customer->email;
                        $ct->status = 'open';
                        $ct->token = Tools::passwdGen(12);
                        $ct->add();
                    } else {
                        $ct = new CustomerThread((int)$id_customer_thread);
                        $ct->status = 'open';
                        $ct->update();
                    }

                    $cm->id_customer_thread = $ct->id;
                    $cm->message = $msgText;
                    $cm->ip_address = (int)ip2long($_SERVER['REMOTE_ADDR']);
                    $cm->add();

                    if (!Configuration::get('PS_MAIL_EMAIL_MESSAGE')) {
                        $to = strval(Configuration::get('PS_SHOP_EMAIL'));
                    } else {
                        $to = new Contact((int)Configuration::get('PS_MAIL_EMAIL_MESSAGE'));
                        $to = strval($to->email);
                    }
                    $toName = strval(Configuration::get('PS_SHOP_NAME'));
                    $customer = $this->context->customer;

                    $product = new Product($id_product);
                    $product_name = '';
                    if (Validate::isLoadedObject($product) && isset($product->name[(int)$this->context->language->id])) {
                        $product_name = $product->name[(int)$this->context->language->id];
                    }

                    if (Validate::isLoadedObject($customer)) {
                        Mail::Send(
                            $this->context->language->id,
                            'order_customer_comment',
                            Mail::l('Message from a customer'),
                            array(
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{email}' => $customer->email,
                                '{id_order}' => (int)$order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => Tools::nl2br($msgText),
                                '{product_name}' => $product_name
                            ),
                            $to,
                            $toName,
                            strval(Configuration::get('PS_SHOP_EMAIL')),
                            $customer->firstname.' '.$customer->lastname,
                            null,
                            null,
                            _PS_MAIL_DIR_,
                            false,
                            null,
                            null,
                            $customer->email
                        );
                    }

                    if (Tools::getValue('ajax') != 'true') {
                        Tools::redirect('index.php?controller=order-detail&id_order='.(int)$idOrder);
                    }

                    $this->context->smarty->assign('message_confirmation', true);
                } else {
                    $this->errors[] = Tools::displayError('Order not found');
                }
            }
        }
    }

    public function displayAjax()
    {
        $this->display();
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        if (!($id_order = (int) Tools::getValue('id_order')) || !Validate::isUnsignedId($id_order)) {
            $this->errors[] = Tools::displayError('Order ID required');
        } else {
            $order = new Order($id_order);
            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                // Here handle request from the ajax on order details page i.e refund, additional qty etc
                $method = Tools::getValue('method');
                // extra demands popup ajax
                if ($method == 'getRoomTypeBookingDemands') {
                    $response = array('reload' => false);
                    if (($idProduct = Tools::getValue('id_product'))
                        && ($idOrder = Tools::getValue('id_order'))
                        && ($dateFrom = Tools::getValue('date_from'))
                        && ($dateTo = Tools::getValue('date_to'))
                    ) {
                        $objBookingDemand = new HotelBookingDemands();
                        $useTax = 0;
                        $customer = Context::getContext()->customer;
                        if (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC) {
                            $useTax = 1;
                        }
                        if ($extraDemands = $objBookingDemand->getRoomTypeBookingExtraDemands(
                            $idOrder,
                            $idProduct,
                            0,
                            $dateFrom,
                            $dateTo,
                            1,
                            0,
                            $useTax
                        )) {
                            $this->context->smarty->assign(
                                array(
                                    'useTax' => $useTax,
                                    'extraDemands' => $extraDemands,
                                )
                            );
                        }
                        $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
                        if ($additionalServices = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                            $idOrder,
                            0,
                            0,
                            $idProduct,
                            $dateFrom,
                            $dateTo,
                            0,
                            0,
                            $useTax
                        )) {
                            $this->context->smarty->assign(array(
                                'useTax' => $useTax,
                                'additionalServices' => $additionalServices,
                            ));
                        }
                        $response['extra_demands'] = $this->context->smarty->fetch(_PS_THEME_DIR_.'_partials/order_booking_demands.tpl');
                    }

                    die(json_encode($response));
                } elseif ($method == 'submitRefundRequest') { // room cancellation ajax
                    $hasError = 0;
                    if (!$refundReason = Tools::getValue('cancellation_reason')) {
                        $hasError = 1;
                    } elseif (!Validate::isCleanHtml($refundReason)) {
                        $hasError = 1;
                    }
                    if ($bookingRefunds = Tools::getValue('bookings_to_refund')) {
                        if (!count($bookingRefunds = json_decode($bookingRefunds, true))) {
                            $hasError = 1;
                        } else {
                            foreach ($bookingRefunds as $idHtlBooking) {
                                $objHotelBookingDetail = new HotelBookingDetail($idHtlBooking);
                                if ($objHotelBookingDetail->id_customer != $order->id_customer
                                    || OrderReturn::getOrdersReturnDetail($order->id, 0, $idHtlBooking)
                                ) {
                                    $hasError = 1;
                                    break;
                                }
                            }
                        }
                    } else {
                        $hasError = 1;
                    }
                    $res = true;
                    if (!$hasError) {
                        if ((int)$order->total_paid_real == 0){
                            foreach ($bookingRefunds as $idHtlBooking) {
                                $objHtlBooking = new HotelBookingDetail($idHtlBooking);
                                $objHtlBooking->save();
                            }
                        }

                        $objOrderReturn = new OrderReturn();
                        $objOrderReturn->id_customer = $order->id_customer;
                        $objOrderReturn->id_order = $order->id;
                        $objOrderReturn->state = 0;
                        $objOrderReturn->by_admin = 0;
                        $objOrderReturn->question = $refundReason;
                        $objOrderReturn->save();
                        if ($objOrderReturn->id) {
                            foreach ($bookingRefunds as $idHtlBooking) {
                                $objHtlBooking = new HotelBookingDetail($idHtlBooking);
                                $numDays = $objHtlBooking->getNumberOfDays(
                                    $objHtlBooking->date_from,
                                    $objHtlBooking->date_to
                                );

                                $objOrderReturnDetail = new OrderReturnDetail();
                                $objOrderReturnDetail->id_order_return = $objOrderReturn->id;
                                $objOrderReturnDetail->id_order_detail = $objHtlBooking->id_order_detail;
                                $objOrderReturnDetail->product_quantity = $numDays;
                                $objOrderReturnDetail->id_htl_booking = $idHtlBooking;
                                $objOrderReturnDetail->save();
                            }
                        }
                        // Emails to customer, admin on refund request state change
                        $objOrderReturn->changeIdOrderReturnState(Configuration::get('PS_ORS_PENDING'));

                        die(json_encode(array('status' => 1)));
                    }
                    die(json_encode(array('status' => 0)));
                }
            } else {
                die(json_encode(array('status' => 0)));
            }
            // END ajax request handle

            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                $id_order_state = (int)$order->getCurrentState();
                $carrier = new Carrier((int)$order->id_carrier, (int)$order->id_lang);
                $addressInvoice = new Address((int)$order->id_address_invoice);
                $addressDelivery = new Address((int)$order->id_address_delivery);

                $inv_adr_fields = AddressFormat::getOrderedAddressFields($addressInvoice->id_country);
                $dlv_adr_fields = AddressFormat::getOrderedAddressFields($addressDelivery->id_country);

                $invoiceAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($addressInvoice, $inv_adr_fields);
                $deliveryAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($addressDelivery, $dlv_adr_fields);

                if ($order->total_discounts > 0) {
                    $this->context->smarty->assign('total_old', (float)$order->total_paid - $order->total_discounts);
                }
                $products = $order->getProducts();

                $order_status = new OrderState((int)$id_order_state, (int)$order->id_lang);

                $customer = new Customer($order->id_customer);

                $customerGuestDetail = false;
                if ($id_customer_guest_detail = OrderCustomerGuestDetail::isCustomerGuestBooking($order->id)) {
                    $customerGuestDetail = new OrderCustomerGuestDetail($id_customer_guest_detail);
                }


                //by webkul to show order details properly on order history page

                // if (Module::isInstalled('hotelreservationsystem')) {
                //     include_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
                    // $objHtlBranchInfo = new HotelBranchInformation();
                    $objBookingDetail = new HotelBookingDetail();
                    $objRoomType = new HotelRoomType();
                    $objBookingDemand = new HotelBookingDemands();
                    $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
                    $nonRequestedRooms = 1;
                    $anyBackOrder = 0;
                    $processedProducts = array();
                    $cartHotelData = array();
                    $cartServiceProducts = array();
                    $total_demands_price_te = 0;
                    $total_demands_price_ti = 0;
                    $total_convenience_fee_te = 0;
                    $total_convenience_fee_ti = 0;
                    if (!empty($products)) {
                        foreach ($products as $type_key => $type_value) {
                            if (in_array($type_value['product_id'], $processedProducts)) {
                                continue;
                            }
                            if ($type_value['is_booking_product']) {
                                $processedProducts[] = $type_value['product_id'];

                                $product = new Product($type_value['product_id'], false, $this->context->language->id);
                                $cover_image_arr = $product->getCover($type_value['product_id']);

                                if (!empty($cover_image_arr)) {
                                    $cover_img = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_arr['id_image'], 'small_default');
                                } else {
                                    $cover_img = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code.'-default', 'small_default');
                                }

                                if (isset($customer->id)) {
                                    $obj_cart = new Cart($order->id_cart);
                                    $order_bk_data = $objBookingDetail->getOnlyOrderBookingData($order->id, $obj_cart->id_guest, $type_value['product_id'], $customer->id);
                                } else {
                                    $order_bk_data = $objBookingDetail->getOnlyOrderBookingData($order->id, $customer->id_guest, $type_value['product_id']);
                                }
                                $cartHotelData[$type_key]['id_product'] = $type_value['product_id'];
                                $cartHotelData[$type_key]['cover_img'] = $cover_img;


                                $objBookingDemand = new HotelBookingDemands();
                                foreach ($order_bk_data as $data_k => $data_v) {
                                    $date_join = strtotime($data_v['date_from']).strtotime($data_v['date_to']);

                                    $cartHotelData[$type_key]['adults'] = $data_v['adults'];
                                    $cartHotelData[$type_key]['children'] = $data_v['children'];
                                    /*Product price when order was created*/
                                    $order_details_obj = new OrderDetail($data_v['id_order_detail']);
                                    $cartHotelData[$type_key]['name'] = $order_details_obj->product_name;
                                    $cartHotelData[$type_key]['paid_unit_price_tax_excl'] = ($order_details_obj->total_price_tax_excl)/$order_details_obj->product_quantity;
                                    $cartHotelData[$type_key]['paid_unit_price_tax_incl'] = ($order_details_obj->total_price_tax_incl)/$order_details_obj->product_quantity;

                                    if (isset($cartHotelData[$type_key]['date_diff'][$date_join])) {
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['num_rm'] += 1;

                                        $num_days = $cartHotelData[$type_key]['date_diff'][$date_join]['num_days'];
                                        $var_quant = (int) $cartHotelData[$type_key]['date_diff'][$date_join]['num_rm'];

                                        $cartHotelData[$type_key]['date_diff'][$date_join]['adults'] += $data_v['adults'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['children'] += $data_v['children'];

                                        $cartHotelData[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_excl'] = $data_v['total_price_tax_excl']/$num_days;
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'] = $data_v['total_price_tax_incl']/$num_days;
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['avg_paid_unit_price_tax_excl'] += $cartHotelData[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_excl'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['avg_paid_unit_price_tax_incl'] += $cartHotelData[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['amount_tax_incl'] += $data_v['total_price_tax_incl'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['amount_tax_excl'] += $data_v['total_price_tax_excl'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['is_backorder'] = $data_v['is_back_order'];
                                        if ($data_v['is_back_order']) {
                                            $anyBackOrder = 1;
                                        }
                                        if ($data_v['is_refunded']) {
                                            $cartHotelData[$type_key]['date_diff'][$date_join]['count_refunded'] += 1;
                                        }
                                        if ($data_v['is_cancelled']) {
                                            $cartHotelData[$type_key]['date_diff'][$date_join]['count_cancelled'] += 1;
                                            $cartHotelData[$type_key]['date_diff'][$date_join]['count_refunded'] -= 1;
                                        }
                                    } else {
                                        $num_days = $objBookingDetail->getNumberOfDays($data_v['date_from'], $data_v['date_to']);
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['num_rm'] = 1;
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['data_form'] = $data_v['date_from'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['data_to'] = $data_v['date_to'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['num_days'] = $num_days;
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['adults'] = $data_v['adults'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['children'] = $data_v['children'];

                                        $cartHotelData[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_excl'] = $data_v['total_price_tax_excl']/$num_days;
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'] = $data_v['total_price_tax_incl']/$num_days;
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['avg_paid_unit_price_tax_excl'] = $cartHotelData[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_excl'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['avg_paid_unit_price_tax_incl'] = $cartHotelData[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['amount_tax_incl'] = $data_v['total_price_tax_incl'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['amount_tax_excl'] = $data_v['total_price_tax_excl'];
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['is_backorder'] = $data_v['is_back_order'];
                                        if ($data_v['is_back_order']) {
                                            $anyBackOrder = 1;
                                        }
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['count_cancelled'] = 0;
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['count_refunded'] = 0;
                                        if ($data_v['is_refunded']) {
                                            $cartHotelData[$type_key]['date_diff'][$date_join]['count_refunded'] += 1;
                                        }
                                        if ($data_v['is_cancelled']) {
                                            $cartHotelData[$type_key]['date_diff'][$date_join]['count_cancelled'] += 1;
                                            $cartHotelData[$type_key]['date_diff'][$date_join]['count_refunded'] -= 1;
                                        }
                                    }

                                    $cartHotelData[$type_key]['date_diff'][$date_join]['is_refunded'] = $data_v['is_refunded'];

                                    $cartHotelData[$type_key]['date_diff'][$date_join]['ids_htl_booking_detail'][] = $data_v['id'];

                                    $cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                        $id_order,
                                        $type_value['product_id'],
                                        0,
                                        $data_v['date_from'],
                                        $data_v['date_to']
                                    );
                                    if (empty($cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'])) {
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] = 0;
                                    }
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] += $extraDemandPriceTI = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                        $id_order,
                                        $type_value['product_id'],
                                        $data_v['id_room'],
                                        $data_v['date_from'],
                                        $data_v['date_to'],
                                        0,
                                        1,
                                        1
                                    );
                                    if (empty($cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands_price_te'])) {
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands_price_te'] = 0;
                                    }
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands_price_te'] += $extraDemandPriceTE = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                        $id_order,
                                        $type_value['product_id'],
                                        $data_v['id_room'],
                                        $data_v['date_from'],
                                        $data_v['date_to'],
                                        0,
                                        1,
                                        0
                                    );
                                    $total_demands_price_ti += $extraDemandPriceTI;
                                    $total_demands_price_te += $extraDemandPriceTE;
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['product_price_tax_excl'] = $order_details_obj->unit_price_tax_excl;
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['product_price_tax_incl'] = $order_details_obj->unit_price_tax_incl;
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['product_price_without_reduction_tax_excl'] = $order_details_obj->unit_price_tax_excl + $order_details_obj->reduction_amount_tax_excl;
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['product_price_without_reduction_tax_incl'] = $order_details_obj->unit_price_tax_incl + $order_details_obj->reduction_amount_tax_incl;

                                    $feature_price_diff = (float)($cartHotelData[$type_key]['date_diff'][$date_join]['product_price_without_reduction_tax_incl'] - $cartHotelData[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl']);
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['feature_price_diff'] = $feature_price_diff;

                                    $cartHotelData[$type_key]['hotel_name'] = $data_v['hotel_name'];
                                    // add additional services products in hotel detail.
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                        $id_order,
                                        0,
                                        0,
                                        $type_value['product_id'],
                                        $data_v['date_from'],
                                        $data_v['date_to']
                                    );

                                    if (empty($cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_ti'])) {
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_ti'] = 0;
                                    }
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_ti'] += $additionalServicesPriceTI = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                        $id_order,
                                        0,
                                        0,
                                        $type_value['product_id'],
                                        $data_v['date_from'],
                                        $data_v['date_to'],
                                        $data_v['id_room'],
                                        1,
                                        1
                                    );
                                    if (empty($cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_te'])) {
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_te'] = 0;
                                    }
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_te'] += $additionalServicesPriceTE = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                        $id_order,
                                        0,
                                        0,
                                        $type_value['product_id'],
                                        $data_v['date_from'],
                                        $data_v['date_to'],
                                        $data_v['id_room'],
                                        1,
                                        0
                                    );
                                    // get auto added price to be displayed with room price
                                    if (empty($cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'])) {
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'] = 0;
                                    }
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'] += $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                        $id_order,
                                        0,
                                        0,
                                        $type_value['product_id'],
                                        $data_v['date_from'],
                                        $data_v['date_to'],
                                        $data_v['id_room'],
                                        1,
                                        1,
                                        1,
                                        Product::PRICE_ADDITION_TYPE_WITH_ROOM
                                    );
                                    if (empty($cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te'])) {
                                        $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te'] = 0;
                                    }
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te'] += $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                        $id_order,
                                        0,
                                        0,
                                        $type_value['product_id'],
                                        $data_v['date_from'],
                                        $data_v['date_to'],
                                        $data_v['id_room'],
                                        1,
                                        0,
                                        1,
                                        Product::PRICE_ADDITION_TYPE_WITH_ROOM
                                    );
                                }
                                // calculate averages now
                                foreach ($cartHotelData[$type_key]['date_diff'] as $key => &$value) {
                                    $value['avg_paid_unit_price_tax_excl'] = Tools::ps_round($value['avg_paid_unit_price_tax_excl'] / $value['num_rm'], 6);
                                    $value['avg_paid_unit_price_tax_incl'] = Tools::ps_round($value['avg_paid_unit_price_tax_incl'] / $value['num_rm'], 6);

                                    $value['avg_price_diff_tax_excl'] = abs(Tools::ps_round($value['avg_paid_unit_price_tax_excl'] - $value['product_price_tax_excl'], 6));
                                    $value['avg_price_diff_tax_incl'] = abs(Tools::ps_round($value['avg_paid_unit_price_tax_incl'] - $value['product_price_tax_incl'], 6));
                                }
                            } else if ($type_value['product_service_type'] == Product::SERVICE_PRODUCT_WITH_ROOMTYPE) {
                                if ($type_value['product_auto_add'] && $type_value['product_price_addition_type'] == Product::PRICE_ADDITION_TYPE_INDEPENDENT) {
                                    $total_convenience_fee_ti += $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                        $id_order,
                                        $type_value['product_id'],
                                        0,
                                        0,
                                        0,
                                        0,
                                        0,
                                        1,
                                        1,
                                        1
                                    );
                                    $total_convenience_fee_te += $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                        $id_order,
                                        $type_value['product_id'],
                                        0,
                                        0,
                                        0,
                                        0,
                                        0,
                                        1,
                                        0,
                                        1
                                    );
                                }
                            } else {
                                // get all products that are independent.
                                if ($type_value['product_service_type'] == Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE) {
                                    $product = new Product($type_value['product_id'], false, $this->context->language->id);
                                    $cover_image_arr = $product->getCover($type_value['product_id']);

                                    if (!empty($cover_image_arr)) {
                                        $type_value['cover_img'] = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_arr['id_image'], 'small_default');
                                    } else {
                                        $type_value['cover_img'] = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code.'-default', 'small_default');
                                    }
                                    $cartServiceProducts[] = $type_value;
                                }
                            }


                        }

                        $redirectTermsLink = $this->context->link->getCMSLink(new CMS(3, $this->context->language->id), null, $this->context->language->id);
                    }

                    $objHotelBookingDetail = new HotelBookingDetail();
                    $htlBookingDetail = $objHotelBookingDetail->getOrderCurrentDataByOrderId($order->id);
                    $this->context->smarty->assign(
                        array(
                            'id_cms_refund_policy' => Configuration::get('WK_GLOBAL_REFUND_POLICY_CMS'),
                            'THEME_DIR' => _THEME_DIR_,
                            'total_convenience_fee_ti' => $total_convenience_fee_ti,
                            'total_convenience_fee_te' => $total_convenience_fee_te,
                            'total_demands_price_ti' => $total_demands_price_ti,
                            'total_demands_price_te' => $total_demands_price_te,
                            'any_back_order' => $anyBackOrder,
                            'shw_bo_msg' => Configuration::get('WK_SHOW_MSG_ON_BO'),
                            'back_ord_msg' => Configuration::get('WK_BO_MESSAGE'),
                            'order_has_invoice' => $order->hasInvoice(),
                            'cart_htl_data' => $cartHotelData,
                            'cart_service_products' => $cartServiceProducts,
                            'non_requested_rooms' => $nonRequestedRooms,
                        )
                    );
                // }

                $objOrderReturn = new OrderReturn();
                $refundedAmount = 0;
                if ($refundReqBookings = $objOrderReturn->getOrderRefundRequestedBookings($order->id, 0, 1)) {
                    $refundedAmount = $objOrderReturn->getRefundedAmount($order->id);
                }
                $this->context->smarty->assign(
                    array(
                        'hasOrderPaid' => $order->hasBeenPaid(),
                        // refund info
                        'refund_allowed' => (int) $order->isReturnable(),
                        'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
                        'refundReqBookings' => $refundReqBookings,
                        'hasCompletelyRefunded' => $order->hasCompletelyRefunded(),
                        'refundedAmount' => $refundedAmount,
                        'shop_name' => strval(Configuration::get('PS_SHOP_NAME')),
                        'order' => $order,
                        'guestInformations' => (array)new Customer($order->id_customer),
                        'customerGuestDetail' => $customerGuestDetail,
                        'currency' => new Currency($order->id_currency),
                        'order_state' => (int) $id_order_state,
                        'invoiceAllowed' => (int) Configuration::get('PS_INVOICE'),
                        'invoice' => (OrderState::invoiceAvailable($id_order_state) && count($order->getInvoicesCollection())),
                        'logable' => (bool) $order_status->logable,
                        'order_history' => $order->getHistory($this->context->language->id, false, true),
                        'products' => $products,
                        'discounts' => $order->getCartRules(),
                        'carrier' => $carrier,
                        'address_invoice' => $addressInvoice,
                        'invoiceState' => (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state) ? new State($addressInvoice->id_state) : false,
                        'address_delivery' => $addressDelivery,
                        'inv_adr_fields' => $inv_adr_fields,
                        'dlv_adr_fields' => $dlv_adr_fields,
                        'invoiceAddressFormatedValues' => $invoiceAddressFormatedValues,
                        'deliveryAddressFormatedValues' => $deliveryAddressFormatedValues,
                        'deliveryState' => (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state) ? new State($addressDelivery->id_state) : false,
                        'is_guest' => false,
                        'messages' => CustomerMessage::getMessagesByOrderId((int) $order->id, false),
                        'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
                        'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
                        'isRecyclable' => Configuration::get('PS_RECYCLABLE_PACK'),
                        'use_tax' => Configuration::get('PS_TAX'),
                        'group_use_tax' => (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC),
                        'reorderingAllowed' => !(bool) Configuration::get('PS_DISALLOW_HISTORY_REORDERING'),
                    )
                );

                if ($carrier->url && $order->shipping_number) {
                    $this->context->smarty->assign('followup', str_replace('@', $order->shipping_number, $carrier->url));
                }
                $this->context->smarty->assign('HOOK_ORDERDETAILDISPLAYED', Hook::exec('displayOrderDetail', array('order' => $order)));
                Hook::exec('actionOrderDetail', array('carrier' => $carrier, 'order' => $order));

                unset($carrier, $addressInvoice, $addressDelivery);
            } else {
                $this->errors[] = Tools::displayError('This order cannot be found.');
            }
            unset($order);
        }

        $this->setTemplate(_PS_THEME_DIR_.'order-detail.tpl');
    }

    public function setMedia()
    {
        if (Tools::getValue('ajax') != 'true') {
            parent::setMedia();
            $this->addCSS(_THEME_CSS_DIR_.'history.css');
            $this->addCSS(_THEME_CSS_DIR_.'addresses.css');
        }
    }
}