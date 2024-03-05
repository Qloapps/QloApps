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

class OrderConfirmationControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'order-confirmation';
    public $id_cart;
    public $id_module;
    public $id_order;
    public $reference;
    public $secure_key;

    /**
     * Initialize order confirmation controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        $this->id_cart = (int)(Tools::getValue('id_cart', 0));
        $is_guest = false;
        /* check if the cart has been made by a Guest customer, for redirect link */
        if (Cart::isGuestCartByCartId($this->id_cart)) {
            $is_guest = true;
            $redirectLink = 'index.php?controller=guest-tracking';
        } else {
            $redirectLink = 'index.php?controller=history';
        }

        $this->id_module = (int)(Tools::getValue('id_module', 0));
        $this->id_order = Order::getOrderByCartId((int)($this->id_cart));
        $this->secure_key = Tools::getValue('key', false);
        $order = new Order((int)($this->id_order));
        if ($is_guest) {
            $customer = new Customer((int)$order->id_customer);
            $redirectLink .= '&id_order='.$order->reference.'&email='.urlencode($customer->email);
        }
        if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key)) {
            Tools::redirect($redirectLink.(Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
        }
        $this->reference = $order->reference;
        if (!Validate::isLoadedObject($order) || $order->id_customer != $this->context->customer->id || $this->secure_key != $order->secure_key) {
            Tools::redirect($redirectLink);
        }

        if ($this->id_module == -1) {
            if ($order->module != 'free_order') {
                Tools::redirect($redirectLink);
            }
        } else {
            $module = Module::getInstanceById((int)($this->id_module));
            if ($order->module != $module->name) {
                Tools::redirect($redirectLink);
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

        $this->context->smarty->assign(array(
            'is_guest' => $this->context->customer->is_guest,
            'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation(),
            'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn()
        ));

        if ($this->context->customer->is_guest) {
            $this->context->smarty->assign(array(
                'id_order' => $this->id_order,
                'reference_order' => $this->reference,
                'id_order_formatted' => sprintf('#%06d', $this->id_order),
                'email' => $this->context->customer->email
            ));
            /* If guest we clear the cookie for security reason */
            $this->context->customer->mylogout();
        }
        $customer = $this->context->customer;
        $order = new Order($this->id_order);
        $cart = new Cart($order->id_cart);
        /*By webkul to show order details properly on order history page*/
        if (Module::isInstalled('hotelreservationsystem')) {
            require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
            $non_requested_rooms = 0;
            $any_back_order = 0;
            $processed_product = array();
            $orderTotalInfo = array();
            $orderTotalInfo['total_demands_price_te'] = 0;
            $orderTotalInfo['total_demands_price_ti'] = 0;
            $orderTotalInfo['total_products_te'] = 0;
            $orderTotalInfo['total_products_ti'] = 0;
            $orderTotalInfo['total_rooms_te'] = 0;
            $orderTotalInfo['total_rooms_ti'] = 0;
            $orderTotalInfo['total_service_products_te'] = 0;
            $orderTotalInfo['total_service_products_ti'] = 0;
            $orderTotalInfo['total_auto_add_services_te'] = 0;
            $orderTotalInfo['total_auto_add_services_ti'] = 0;
            $orderTotalInfo['total_services_te'] = 0;
            $orderTotalInfo['total_services_ti'] = 0;
            $orderTotalInfo['total_convenience_fee_te'] = 0;
            $orderTotalInfo['total_convenience_fee_ti'] = 0;
            $orderTotalInfo['total_discounts'] = 0;
            $orderTotalInfo['total_discounts_te'] = 0;
            $orderTotalInfo['total_tax'] = 0;
            $orderTotalInfo['total_paid'] = 0;
            $orderTotalInfo['total_paid_real'] = 0;
            $orderTotalInfo['total_wrapping'] = 0;
            $orderTotalInfo['total_order_amount'] = 0;

            $orders_has_invoice = 1;
            if ($cartOrders = Order::getAllOrdersByCartId($order->id_cart)) {
                $objHtlBranchInfo = new HotelBranchInformation();
                $obj_cart_bk_data = new HotelCartBookingData();
                $obj_htl_bk_dtl = new HotelBookingDetail();
                $obj_rm_type = new HotelRoomType();
                $orderTotalInfo['total_order_amount'] = 0;
                $hotelCartBookingData = new HotelCartBookingData();
                $objBookingDemand = new HotelBookingDemands();
                $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
                $cart_service_products = array();
                $cart_htl_data = array();
                foreach ($cartOrders as $cartOrder) {
                    $idOrder = $cartOrder['id_order'];
                    $objCartOrder = new Order($idOrder);
                    $orderProducts = $objCartOrder->getProducts();

                    if (!empty($orderProducts)) {
                        foreach ($orderProducts as $type_key => $type_value) {
                            if (in_array($type_value['product_id'], $processed_product)) {
                                continue;
                            }
                            $processed_product[] = $type_value['product_id'];

                            $product = new Product($type_value['product_id'], false, $this->context->language->id);

                            $cover_image_arr = $product->getCover($type_value['product_id']);

                            if (!empty($cover_image_arr)) {
                                $cover_img = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_arr['id_image'], 'small_default');
                            } else {
                                $cover_img = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code.'-default', 'small_default');
                            }

                            if (isset($customer->id)) {
                                $order_bk_data = $obj_htl_bk_dtl->getOnlyOrderBookingData($idOrder, $cart->id_guest, $type_value['product_id'], $customer->id);
                            } else {
                                $order_bk_data = $obj_htl_bk_dtl->getOnlyOrderBookingData($idOrder, $customer->id_guest, $type_value['product_id']);
                            }
                            if ($rm_dtl = $obj_rm_type->getRoomTypeInfoByIdProduct($type_value['product_id'])) {
                                $cart_htl_data[$type_key]['id_product'] = $type_value['product_id'];
                                $cart_htl_data[$type_key]['cover_img'] = $cover_img;
                                $cart_htl_data[$type_key]['adults'] = $rm_dtl['adults'];
                                $cart_htl_data[$type_key]['children'] = $rm_dtl['children'];

                                foreach ($order_bk_data as $data_k => $data_v) {
                                    $date_join = strtotime($data_v['date_from']).strtotime($data_v['date_to']);
                                    /*Product price when order was created*/
                                    $order_details_obj = new OrderDetail($data_v['id_order_detail']);
                                    $cart_htl_data[$type_key]['name'] = $order_details_obj->product_name;
                                    // $ord_refnd_info = $obj_ord_ref_info->getOderRefundInfoByIdOrderIdProductByDate($this->id_order, $type_value['product_id'], $data_v['date_from'], $data_v['date_to']);
                                    // if ($ord_refnd_info) {
                                    //     $stage_name = $obj_refund_stages->getNameById($ord_refnd_info['refund_stage_id']);
                                    // } else {
                                        //     $non_requested_rooms = 1;
                                        // }
                                    $stage_name = '';
                                    if (isset($cart_htl_data[$type_key]['date_diff'][$date_join])) {
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] += 1;

                                        $num_days = $cart_htl_data[$type_key]['date_diff'][$date_join]['num_days'];
                                        $var_quant = (int) $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'];

                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['adults'] += $data_v['adults'];
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['children'] += $data_v['children'];

                                        //// By webkul New way to calculate product prices with feature Prices
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_excl'] = $data_v['total_price_tax_excl']/$num_days;
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'] = $data_v['total_price_tax_incl']/$num_days;
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_incl'] = $data_v['total_price_tax_incl']*$var_quant;
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_excl'] = $data_v['total_price_tax_excl']*$var_quant;
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['is_backorder'] = $data_v['is_back_order'];
                                        if ($data_v['is_back_order']) {
                                            $any_back_order = 1;
                                        }
                                        //refund_stage
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['stage_name'] = $stage_name;
                                    } else {
                                        $num_days = $obj_htl_bk_dtl->getNumberOfDays($data_v['date_from'], $data_v['date_to']);

                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] = 1;
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['data_form'] = $data_v['date_from'];
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['data_to'] = $data_v['date_to'];
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['num_days'] = $num_days;

                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['adults'] = $data_v['adults'];
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['children'] = $data_v['children'];


                                        // By webkul New way to calculate product prices with feature Prices
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_excl'] = $data_v['total_price_tax_excl']/$num_days;
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'] = $data_v['total_price_tax_incl']/$num_days;
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_incl'] = $data_v['total_price_tax_incl'];
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_excl'] = $data_v['total_price_tax_excl'];
                                        if ($data_v['is_back_order']) {
                                            $any_back_order = 1;
                                        }
                                        //refund_stage
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['stage_name'] = $stage_name;
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['is_backorder'] = $data_v['is_back_order'];
                                    }
                                    // $orderTotalInfo['total_rooms_te'] += $data_v['total_price_tax_excl'];
                                    // $orderTotalInfo['total_rooms_ti'] += $data_v['total_price_tax_incl'];
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                        $idOrder,
                                        $type_value['product_id'],
                                        0,
                                        $data_v['date_from'],
                                        $data_v['date_to']
                                    );
                                    if (empty($cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'])) {
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] = 0;
                                    }
                                    $extraDemandPriceTI = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                        $idOrder,
                                        $type_value['product_id'],
                                        $data_v['id_room'],
                                        $data_v['date_from'],
                                        $data_v['date_to'],
                                        0,
                                        1,
                                        1
                                    );
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] += $extraDemandPriceTI;
                                    if (empty($cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_te'])) {
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_te'] = 0;
                                    }
                                    $extraDemandPriceTE = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                        $idOrder,
                                        $type_value['product_id'],
                                        $data_v['id_room'],
                                        $data_v['date_from'],
                                        $data_v['date_to'],
                                        0,
                                        1,
                                        0
                                    );
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_te'] += $extraDemandPriceTE;
                                    $orderTotalInfo['total_demands_price_ti'] += $extraDemandPriceTI;
                                    $orderTotalInfo['total_demands_price_te'] += $extraDemandPriceTE;
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_tax_excl'] = $order_details_obj->unit_price_tax_excl;
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_tax_incl'] = $order_details_obj->unit_price_tax_incl;
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_without_reduction_tax_excl'] = $order_details_obj->unit_price_tax_excl + $order_details_obj->reduction_amount_tax_excl;
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_without_reduction_tax_incl'] = $order_details_obj->unit_price_tax_incl + $order_details_obj->reduction_amount_tax_incl;

                                    $feature_price_diff = (float)($cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_without_reduction_tax_incl'] - $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl']);
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['feature_price_diff'] = $feature_price_diff;

                                    //enter hotel name
                                    $cart_htl_data[$type_key]['hotel_name'] = $data_v['hotel_name'];

                                    // add additional services products in hotel detail.
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                        $idOrder,
                                        0,
                                        0,
                                        $type_value['product_id'],
                                        $data_v['date_from'],
                                        $data_v['date_to']
                                    );
                                    if (empty($cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_ti'])) {
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_ti'] = 0;
                                    }
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_ti'] += $extraDemandPriceTI = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
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
                                    if (empty($cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_te'])) {
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_te'] = 0;
                                    }
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_te'] += $extraDemandPriceTE = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
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
                                    if (empty($cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'])) {
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'] = 0;
                                    }
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'] += $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
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
                                    if (empty($cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te'])) {
                                        $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te'] = 0;
                                    }
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te'] += $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
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
                            } else if ($product->service_product_type == Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE) {
                                $cover_image_arr = $product->getCover($type_value['product_id']);

                                if (!empty($cover_image_arr)) {
                                    $type_value['cover_img'] = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_arr['id_image'], 'small_default');
                                } else {
                                    $type_value['cover_img'] = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code.'-default', 'small_default');
                                }
                                // $orderTotalInfo['total_service_products_te'] += $type_value['total_price_tax_excl'];
                                // $orderTotalInfo['total_service_products_ti'] += $type_value['total_price_tax_incl'];
                                $cart_service_products[] = $type_value;

                            }
                        }

                        if (!empty($cart_htl_data)) {
                            $this->context->smarty->assign('cart_htl_data', $cart_htl_data);
                        }
                        if (!empty($cart_service_products)) {
                            $this->context->smarty->assign('cart_service_products', $cart_service_products);
                        }
                    }
                    if (!$objCartOrder->hasInvoice()) {
                        $orders_has_invoice = 0;
                    }
                    $orderTotalInfo['total_wrapping'] += $objCartOrder->total_wrapping;
                    $orderTotalInfo['total_rooms_te'] += $objCartOrder->getTotalProductsWithoutTaxes(false, true);
                    $orderTotalInfo['total_rooms_ti'] += $objCartOrder->getTotalProductsWithTaxes(false, true);
                    $orderTotalInfo['total_auto_add_services_te'] += $objCartOrder->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE, 1, Product::PRICE_ADDITION_TYPE_WITH_ROOM);
                    $orderTotalInfo['total_auto_add_services_ti'] += $objCartOrder->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE, 1, Product::PRICE_ADDITION_TYPE_WITH_ROOM);
                    $orderTotalInfo['total_services_te'] += $objCartOrder->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE, 0);
                    $orderTotalInfo['total_services_ti'] += $objCartOrder->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE, 0);
                    $orderTotalInfo['total_convenience_fee_te'] += $objCartOrder->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE, 1, Product::PRICE_ADDITION_TYPE_INDEPENDENT);
                    $orderTotalInfo['total_convenience_fee_ti'] += $objCartOrder->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE, 1, Product::PRICE_ADDITION_TYPE_INDEPENDENT);
                    // $orderTotalInfo['total_service_products_te'] += $objCartOrder->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE);
                    // $orderTotalInfo['total_service_products_ti'] += $objCartOrder->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE);
                    $orderTotalInfo['total_discounts'] += $objCartOrder->total_discounts;
                    $orderTotalInfo['total_discounts_te'] += $objCartOrder->total_discounts_tax_excl;
                    $orderTotalInfo['total_tax'] += $objCartOrder->total_paid_tax_incl - $objCartOrder->total_paid_tax_excl;
                    $orderTotalInfo['total_paid'] += $objCartOrder->total_paid;
                    $orderTotalInfo['total_paid_real'] += $objCartOrder->total_paid_real;
                }
            }

            $this->context->smarty->assign('orderTotalInfo', $orderTotalInfo);
            $this->context->smarty->assign('non_requested_rooms', $non_requested_rooms);
            $this->context->smarty->assign('orders_has_invoice', $orders_has_invoice);
        }

        $shw_bo_msg = Configuration::get('WK_SHOW_MSG_ON_BO');
        $bo_msg = Configuration::get('WK_BO_MESSAGE');
        $this->context->smarty->assign(
            array(
                'refund_allowed' => (int) $order->isReturnable(),
                'is_free_order' => $this->id_module == -1 && $order->module == 'free_order',
                'any_back_order' => $any_back_order,
                'shw_bo_msg' => $shw_bo_msg,
                'back_ord_msg' => $bo_msg,
                'order' => $order,
                'objCurrency' => (new Currency($order->id_currency)),
                'use_tax' => Configuration::get('PS_TAX'),
                'group_use_tax' => (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC),
            )
        );

        $this->setTemplate(_PS_THEME_DIR_.'order-confirmation.tpl');
    }

    /**
     * Execute the hook displayPaymentReturn
     */
    public function displayPaymentReturn()
    {
        if (Validate::isUnsignedId($this->id_order) && Validate::isUnsignedId($this->id_module)) {
            $params = array();
            $order = new Order($this->id_order);
            $currency = new Currency($order->id_currency);

            if (Validate::isLoadedObject($order)) {
                $params['total_to_pay'] = $order->getOrdersTotalPaid();
                $params['currency'] = $currency->sign;
                $params['objOrder'] = $order;
                $params['currencyObj'] = $currency;

                return Hook::exec('displayPaymentReturn', $params, $this->id_module);
            }
        }

        return false;
    }

    /**
     * Execute the hook displayOrderConfirmation
     */
    public function displayOrderConfirmation()
    {
        if (Validate::isUnsignedId($this->id_order)) {
            $params = array();
            $order = new Order($this->id_order);
            $currency = new Currency($order->id_currency);

            if (Validate::isLoadedObject($order)) {
                $params['total_to_pay'] = $order->getOrdersTotalPaid();
                $params['currency'] = $currency->sign;
                $params['objOrder'] = $order;
                $params['currencyObj'] = $currency;

                return Hook::exec('displayOrderConfirmation', $params);
            }
        }
        return false;
    }

    public function setMedia()
    {
        if (Tools::getValue('ajax') != 'true') {
            parent::setMedia();
            $this->addCSS(_THEME_CSS_DIR_.'history.css');
            $this->addJS(_THEME_JS_DIR_.'history.js');
            $this->addJqueryPlugin(array('fancybox')); //fancybox not found for some client theme
            $this->addJqueryUI('ui.tooltip', 'base', true);
        }
    }
}
