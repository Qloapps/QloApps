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

class GuestTrackingControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'guest-tracking';

    /**
     * Initialize guest tracking controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        if ($this->context->customer->isLogged()) {
            Tools::redirect('history.php');
        }
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitGuestTracking') || Tools::isSubmit('submitTransformGuestToCustomer')) {
            // These lines are here for retrocompatibility with old theme
            $idOrder = Tools::getValue('id_order');
            $order_collection = array();
            if ($idOrder) {
                if (is_numeric($idOrder)) {
                    $order = new Order((int)$idOrder);
                    if (Validate::isLoadedObject($order)) {
                        $order_collection = Order::getByReference($order->reference);
                    }
                } else {
                    $order_collection = Order::getByReference($idOrder);
                }
            }

            // Get order reference, ignore package reference (after the #, on the order reference)
            $order_reference = current(explode('#', Tools::getValue('order_reference')));
            // Ignore $result_number
            if (!empty($order_reference)) {
                $order_collection = Order::getByReference($order_reference);
            }

            $email = Tools::getValue('email');

            if (empty($order_reference) && empty($idOrder)) {
                $this->errors[] = Tools::displayError('Please provide your order\'s reference number.');
            } elseif (empty($email)) {
                $this->errors[] = Tools::displayError('Please provide a valid email address.');
            } elseif (!Validate::isEmail($email)) {
                $this->errors[] = Tools::displayError('Please provide a valid email address.');
            } elseif (!Customer::customerExists($email, false, false)) {
                $this->errors[] = Tools::displayError('There is no account associated with this email address.');
            } elseif (Customer::customerExists($email, false, true)) {
                $this->errors[] = Tools::displayError('This page is for guest accounts only. Since your guest account has already been transformed into a customer account, you can no longer view your order here. Please log in to your customer account to view this order');
                $this->context->smarty->assign('show_login_link', true);
            } elseif (!count($order_collection)) {
                $this->errors[] = Tools::displayError('Invalid order reference');
            } elseif (!$order_collection->getFirst()->isAssociatedAtGuest($email)) {
                $this->errors[] = Tools::displayError('Invalid order reference');
            } else {
                $this->assignOrderTracking($order_collection);
                if (Tools::isSubmit('submitTransformGuestToCustomer')) {
                    $customer = new Customer((int)$order->id_customer);
                    if (!Validate::isLoadedObject($customer)) {
                        $this->errors[] = Tools::displayError('Invalid customer');
                    } elseif (!Tools::getValue('password')) {
                        $this->errors[] = Tools::displayError('Invalid password.');
                    } elseif (!$customer->transformToCustomer($this->context->language->id, Tools::getValue('password'))) {
                        // @todo clarify error message
                        $this->errors[] = Tools::displayError('An error occurred while transforming a guest into a registered customer.');
                    } else {
                        $this->context->smarty->assign('transformSuccess', true);
                    }
                }
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $method = Tools::getValue('method');
        if ($method == 'getRoomTypeBookingDemands') {
            $extraDemandsTpl = '';
            if (($idProduct = Tools::getValue('id_product'))
                && ($idOrder = Tools::getValue('id_order'))
                && ($dateFrom = Tools::getValue('date_from'))
                && ($dateTo = Tools::getValue('date_to'))
            ) {
                $objBookingDemand = new HotelBookingDemands();
                $order = new Order($idOrder);
                $customer = new Customer((int)$order->id_customer);
                $useTax = 0;
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
                    $extraDemandsTpl .= $this->context->smarty->fetch(_PS_THEME_DIR_.'_partials/order_booking_demands.tpl');
                }
            }
            die($extraDemandsTpl);
        }

        /* Handle brute force attacks */
        if (count($this->errors)) {
            sleep(1);
        }

        $this->context->smarty->assign(array(
            'action' => $this->context->link->getPageLink('guest-tracking.php', true),
            'errors' => $this->errors,
        ));
        $this->setTemplate(_PS_THEME_DIR_.'guest-tracking.tpl');
    }

    /**
     * Assigns template vars related to order tracking information
     *
     * @param PrestaShopCollection $order_collection
     *
     * @throws PrestaShopException
     */
    protected function assignOrderTracking($order_collection)
    {
        $customer = new Customer((int)$order_collection->getFirst()->id_customer);

        $order_collection = ($order_collection->getAll());

        $order_list = array();
        foreach ($order_collection as $order) {
            $order_list[] = $order;
        }

        //by webkul to show order details properly on order history page
        if ($hotelresInstalled = Module::isInstalled('hotelreservationsystem')) {
            include_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
            $objHtlBranchInfo = new HotelBranchInformation();
            $objBookingDetail = new HotelBookingDetail();
            $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
            $objRoomType = new HotelRoomType();

            $nonRequestedRooms = 0;
            $anyBackOrder = 0;
            $processedProducts = array();
            $cartHotelData = array();
        }

        foreach ($order_list as &$order) {
            $idOrder = $order->id;
            /** @var Order $order */
            $order->id_order_state = (int)$order->getCurrentState();
            $order->invoice = (OrderState::invoiceAvailable((int)$order->id_order_state) && $order->invoice_number);
            $order->order_history = $order->getHistory((int)$this->context->language->id, false, true);
            $order->carrier = new Carrier((int)$order->id_carrier, (int)$order->id_lang);
            $order->address_invoice = new Address((int)$order->id_address_invoice);
            $order->address_delivery = new Address((int)$order->id_address_delivery);
            $order->inv_adr_fields = AddressFormat::getOrderedAddressFields($order->address_invoice->id_country);
            $order->dlv_adr_fields = AddressFormat::getOrderedAddressFields($order->address_delivery->id_country);
            $order->invoiceAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($order->address_invoice, $order->inv_adr_fields);
            $order->deliveryAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($order->address_delivery, $order->dlv_adr_fields);
            $order->currency = new Currency($order->id_currency);
            $order->discounts = $order->getCartRules();
            $order->invoiceState = (Validate::isLoadedObject($order->address_invoice) && $order->address_invoice->id_state) ? new State((int)$order->address_invoice->id_state) : false;
            $order->deliveryState = (Validate::isLoadedObject($order->address_delivery) && $order->address_delivery->id_state) ? new State((int)$order->address_delivery->id_state) : false;
            $order->products = $order->getProducts();
            $order->customizedDatas = Product::getAllCustomizedDatas((int)$order->id_cart);
            Product::addCustomizationPrice($order->products, $order->customizedDatas);
            $order->total_old = $order->total_discounts > 0 ? (float)$order->total_paid - (float)$order->total_discounts : false;

            if ($order->carrier->url && $order->shipping_number) {
                $order->followup = str_replace('@', $order->shipping_number, $order->carrier->url);
            }
            $order->hook_orderdetaildisplayed = Hook::exec('displayOrderDetail', array('order' => $order));

            // enter the details of the booking the order
            if ($hotelresInstalled) {
                if ($orderProducts = $order->getProducts()) {
                    $total_demands_price_te = 0;
                    $total_demands_price_ti = 0;
                    $total_convenience_fee_te = 0;
                    $total_convenience_fee_ti = 0;
                    foreach ($orderProducts as $type_key => $type_value) {
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
                                    $idOrder,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to']
                                );
                                if (empty($cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'])) {
                                    $cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] = 0;
                                }
                                $cartHotelData[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] += $extraDemandPriceTI = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $idOrder,
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
                                    $idOrder,
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
                                    $idOrder,
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
                                    $idOrder,
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
                                    $idOrder,
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
                                    $idOrder,
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
                                    $idOrder,
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
                            // get auto added price to be displayed with room price
                            if (empty($cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'])) {
                                $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'] = 0;
                            }
                            $cartHotelData[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'] += $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                $idOrder,
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
                                $idOrder,
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
                                    $idOrder,
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
                                    $idOrder,
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
                }
            }
            // end booking details entries
            $redirectTermsLink = $this->context->link->getCMSLink(
                new CMS(3, $this->context->language->id),
                null, $this->context->language->id
            );

            $customerGuestDetail = false;
            if ($id_customer_guest_detail = OrderCustomerGuestDetail::isCustomerGuestBooking($order->id)) {
                $customerGuestDetail = new OrderCustomerGuestDetail($id_customer_guest_detail);
            }

            $this->context->smarty->assign(
                array(
                    'total_convenience_fee_ti' => $total_convenience_fee_ti,
                    'total_convenience_fee_te' => $total_convenience_fee_te,
                    'total_demands_price_ti' => $total_demands_price_ti,
                    'total_demands_price_te' => $total_demands_price_te,
                    'any_back_order' => $anyBackOrder,
                    'shw_bo_msg' => Configuration::get('WK_SHOW_MSG_ON_BO'),
                    'back_ord_msg' => Configuration::get('WK_BO_MESSAGE'),
                    'order_has_invoice' => $order->hasInvoice(),
                    'cart_htl_data' => $cartHotelData,
                    'non_requested_rooms' => $nonRequestedRooms,
                    'customerGuestDetail' => $customerGuestDetail,
                )
            );
            //end

            Hook::exec('actionOrderDetail', array('carrier' => $order->carrier, 'order' => $order));
        }

        $this->context->smarty->assign(array(
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'order_collection' => $order_list,
            'refund_allowed' => false,
            'invoiceAllowed' => (int)Configuration::get('PS_INVOICE'),
            'is_guest' => true,
            'group_use_tax' => (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC),
            'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
            'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
            'use_tax' => Configuration::get('PS_TAX'),
            'guestInformations' => (array)$customer,
        ));
    }

    public function setMedia()
    {
        parent::setMedia();

        Media::addJsDef(
            array(
                'historyUrl' => $this->context->link->getPageLink('guest-tracking.php', true)
            )
        );

        $this->addJS(_THEME_JS_DIR_.'history.js');
        $this->addCSS(_THEME_CSS_DIR_.'history.css');
        $this->addCSS(_THEME_CSS_DIR_.'addresses.css');
    }

    protected function processAddressFormat(Address $delivery, Address $invoice)
    {
        $inv_adr_fields = AddressFormat::getOrderedAddressFields($invoice->id_country, false, true);
        $dlv_adr_fields = AddressFormat::getOrderedAddressFields($delivery->id_country, false, true);

        $this->context->smarty->assign('inv_adr_fields', $inv_adr_fields);
        $this->context->smarty->assign('dlv_adr_fields', $dlv_adr_fields);
    }
}
