<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
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
     * Initialize order detail controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }

    /**
     * Start forms process.
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitMessage')) {
            $idOrder = (int) Tools::getValue('id_order');
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
                    $id_product = (int) Tools::getValue('id_product');
                    $cm = new CustomerMessage();
                    if (!$id_customer_thread) {
                        $ct = new CustomerThread();
                        $ct->id_contact = 0;
                        $ct->id_customer = (int) $order->id_customer;
                        $ct->id_shop = (int) $this->context->shop->id;
                        if ($id_product && $order->orderContainProduct($id_product)) {
                            $ct->id_product = $id_product;
                        }
                        $ct->id_order = (int) $order->id;
                        $ct->id_lang = (int) $this->context->language->id;
                        $ct->email = $this->context->customer->email;
                        $ct->status = 'open';
                        $ct->token = Tools::passwdGen(12);
                        $ct->add();
                    } else {
                        $ct = new CustomerThread((int) $id_customer_thread);
                        $ct->status = 'open';
                        $ct->update();
                    }

                    $cm->id_customer_thread = $ct->id;
                    $cm->message = $msgText;
                    $cm->ip_address = (int) ip2long($_SERVER['REMOTE_ADDR']);
                    $cm->add();

                    if (!Configuration::get('PS_MAIL_EMAIL_MESSAGE')) {
                        $to = strval(Configuration::get('PS_SHOP_EMAIL'));
                    } else {
                        $to = new Contact((int) Configuration::get('PS_MAIL_EMAIL_MESSAGE'));
                        $to = strval($to->email);
                    }
                    $toName = strval(Configuration::get('PS_SHOP_NAME'));
                    $customer = $this->context->customer;

                    $product = new Product($id_product);
                    $product_name = '';
                    if (Validate::isLoadedObject($product) && isset($product->name[(int) $this->context->language->id])) {
                        $product_name = $product->name[(int) $this->context->language->id];
                    }

                    if (Validate::isLoadedObject($customer)) {
                        Mail::Send(
                            $this->context->language->id, 'order_customer_comment', Mail::l('Message from a customer'),
                            array(
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{email}' => $customer->email,
                                '{id_order}' => (int) $order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => Tools::nl2br($msgText),
                                '{product_name}' => $product_name,
                            ),
                            $to, $toName, $customer->email, $customer->firstname.' '.$customer->lastname
                        );
                    }

                    if (Tools::getValue('ajax') != 'true') {
                        Tools::redirect('index.php?controller=order-detail&id_order='.(int) $idOrder);
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
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        //By webkul to save order refund Info in our table
        $saveRefundInfo = Tools::getValue('saveRefundInfo');
        $saveTotalOrderRefundInfo = Tools::getValue('saveTotalOrderRefundInfo');

        /*If requesst for only one room cancellation*/
        if (isset($saveRefundInfo) && $saveRefundInfo) {
            $id_order = Tools::getValue('id_order');
            $id_customer = Tools::getValue('id_customer');
            $id_currency = Tools::getValue('id_currency');
            $id_product = Tools::getValue('id_product');
            $num_rooms = Tools::getValue('num_rooms');
            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');
            $amount = Tools::getValue('amount');
            $cancellation_reason = Tools::getValue('cancellation_reason');

            $objHtlRefundInfo = new HotelOrderRefundInfo();
            $objHtlRefundInfo->id_order = $id_order;
            $objHtlRefundInfo->id_product = $id_product;
            $objHtlRefundInfo->id_customer = $id_customer;
            $objHtlRefundInfo->id_currency = $id_currency;
            $objHtlRefundInfo->refund_stage_id = 1;
            $objHtlRefundInfo->order_amount = $amount;
            $objHtlRefundInfo->cancellation_reason = $cancellation_reason;
            $objHtlRefundInfo->num_rooms = $num_rooms;
            $objHtlRefundInfo->date_from = $date_from;
            $objHtlRefundInfo->date_to = $date_to;
            $objHtlRefundInfo->save();

            $id_shop = Context::getContext()->shop->id;

            //customer info
            $obj_customer = new Customer($id_customer);

            //product info
            $prod_obj = new Product($id_product, false, $this->context->language->id);
            $product_name = $prod_obj->name;

            //currency data
            $obj_currency = new Currency($id_currency);
            $currency_sign = $obj_currency->sign;

            $admin_data = new EmployeeCore(1);
            $templateVars = array(
                '{shop_name}' => Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop)),
                '{history_url}' => Context::getContext()->link->getPageLink('history', true, Context::getContext()->language->id, null, false, $id_shop),
                '{my_account_url}' => Context::getContext()->link->getPageLink('my-account', true, Context::getContext()->language->id, null, false, $id_shop),
                '{currency_sign}' => $currency_sign,
                '{amount}' => $amount,
                '{date_from}' => $date_from,
                '{date_to}' => $date_to,
                '{cust_name}' => $obj_customer->firstname.' '.$obj_customer->lastname,
                '{cust_email}' => $obj_customer->email,
                '{product_name}' => $product_name,
                '{admin_email}' => $admin_data->email,
            );
            $to = $obj_customer->email;
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
            $temp_path = _PS_MODULE_DIR_.'hotelreservationsystem/mails/';
            $template_name = 'request_processed';

            if ($objHtlRefundInfo->id) {
                if (Mail::Send($id_lang, $template_name, Mail::l('Order Cancellation Status', $id_lang), $templateVars, $to, null, null, null,  null, null, $temp_path, false, null, null)) {
                    $mail_err = 0;
                } else {
                    $mail_err = 1;
                }

                die(Tools::jsonEncode(array('status' => 'success', 'mail_err' => $mail_err)));
            } else {
                die(Tools::jsonEncode(array('status' => 'failed', 'mail_err' => $mail_err)));
            }
        }

        /*If requesst for only all rooms cancellation in the order*/
        if (isset($saveTotalOrderRefundInfo) && $saveTotalOrderRefundInfo) {
            $total_order_json_string_data = Tools::getValue('total_order_data');
            $total_order_data = Tools::jsonDecode($total_order_json_string_data, true);
            $id_customer = Tools::getValue('id_customer');
            $id_currency = Tools::getValue('id_currency');
            $cancellation_reason = Tools::getValue('cancellation_reason');
            $id_order = Tools::getValue('id_order');

            if (isset($total_order_data) && $total_order_data) {
                $objHtlRefund = new HotelOrderRefundInfo();
                foreach ($total_order_data as $data_k => $data_v) {
                    foreach ($data_v['date_diff'] as $rm_k => $rm_v) {
                        $exist_info_refund = $objHtlRefund->getOderRefundInfoByIdOrderIdProductByDate($id_order, $data_v['id_product'], $rm_v['data_form'], $rm_v['data_to']);

                        // any room existing in htl_order_refund_info
                        if (!$exist_info_refund) {
                            $objHtlRefundInfo = new HotelOrderRefundInfo();
                            $objHtlRefundInfo->id_order = $id_order;
                            $objHtlRefundInfo->id_product = $data_v['id_product'];
                            $objHtlRefundInfo->id_customer = $id_customer;
                            $objHtlRefundInfo->id_currency = $id_currency;
                            $objHtlRefundInfo->refund_stage_id = 1;
                            $objHtlRefundInfo->order_amount = $rm_v['amount_tax_incl'];
                            $objHtlRefundInfo->cancellation_reason = $cancellation_reason;
                            $objHtlRefundInfo->num_rooms = $rm_v['num_rm'];
                            $objHtlRefundInfo->date_from = $rm_v['data_form'];
                            $objHtlRefundInfo->date_to = $rm_v['data_to'];
                            $objHtlRefundInfo->save();
                        }
                    }
                }
            }
            $id_shop = Context::getContext()->shop->id;
            $obj_order = new Order($id_order);

            //customer info
            $obj_customer = new Customer($id_customer);

            $admin_data = new EmployeeCore(1);
            $templateVars = array(
                '{shop_name}' => Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop)),
                '{history_url}' => Context::getContext()->link->getPageLink('history', true, Context::getContext()->language->id, null, false, $id_shop),
                '{my_account_url}' => Context::getContext()->link->getPageLink('my-account', true, Context::getContext()->language->id, null, false, $id_shop),
                '{cust_name}' => $obj_customer->firstname.' '.$obj_customer->lastname,
                '{order_reference}' => $obj_order->reference,
            );
            $to = $obj_customer->email;
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
            $temp_path = _PS_MODULE_DIR_.'hotelreservationsystem/mails/';
            $template_name = 'total_order_processed';

            if ($objHtlRefundInfo->id) {
                if (Mail::Send($id_lang, $template_name, Mail::l('Order Cancellation Status', $id_lang), $templateVars, $to, null, null, null,  null, null, $temp_path, false, null, null)) {
                    $mail_err = 0;
                } else {
                    $mail_err = 1;
                }
                die(Tools::jsonEncode(array('status' => 'success', 'mail_err' => $mail_err)));
            } else {
                die(Tools::jsonEncode(array('status' => 'failed')));
            }
        }
        //end

        if (!($id_order = (int) Tools::getValue('id_order')) || !Validate::isUnsignedId($id_order)) {
            $this->errors[] = Tools::displayError('Order ID required');
        } else {
            $order = new Order($id_order);
            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                $id_order_state = (int) $order->getCurrentState();
                $carrier = new Carrier((int) $order->id_carrier, (int) $order->id_lang);
                $addressInvoice = new Address((int) $order->id_address_invoice);
                $addressDelivery = new Address((int) $order->id_address_delivery);

                $inv_adr_fields = AddressFormat::getOrderedAddressFields($addressInvoice->id_country);
                $dlv_adr_fields = AddressFormat::getOrderedAddressFields($addressDelivery->id_country);

                $invoiceAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($addressInvoice, $inv_adr_fields);
                $deliveryAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($addressDelivery, $dlv_adr_fields);

                if ($order->total_discounts > 0) {
                    $this->context->smarty->assign('total_old', (float) $order->total_paid - $order->total_discounts);
                }
                $products = $order->getProducts();

                /* DEPRECATED: customizedDatas @since 1.5 */
                $customizedDatas = Product::getAllCustomizedDatas((int) $order->id_cart);
                Product::addCustomizationPrice($products, $customizedDatas);

                OrderReturn::addReturnedQuantity($products, $order->id);
                $order_status = new OrderState((int) $id_order_state, (int) $order->id_lang);

                $customer = new Customer($order->id_customer);

                //by webkul to show order details properly on order history page
                if (Module::isInstalled('hotelreservationsystem')) {
                    require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';

                    $obj_cart_bk_data = new HotelCartBookingData();
                    $obj_htl_bk_dtl = new HotelBookingDetail();
                    $obj_rm_type = new HotelRoomType();
                    $non_requested_rooms = 0;
                    $any_back_order = 0;
                    $processed_product = array();
                    if (!empty($products)) {
                        foreach ($products as $type_key => $type_value) {
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
                                $obj_cart = new Cart($order->id_cart);
                                $order_bk_data = $obj_htl_bk_dtl->getOnlyOrderBookingData($order->id, $obj_cart->id_guest, $type_value['product_id'], $customer->id);
                            } else {
                                $order_bk_data = $obj_htl_bk_dtl->getOnlyOrderBookingData($order->id, $customer->id_guest, $type_value['product_id']);
                            }
                            $rm_dtl = $obj_rm_type->getRoomTypeInfoByIdProduct($type_value['product_id']);

                            $cart_htl_data[$type_key]['id_product'] = $type_value['product_id'];
                            $cart_htl_data[$type_key]['cover_img'] = $cover_img;
                            $cart_htl_data[$type_key]['adult'] = $rm_dtl['adult'];
                            $cart_htl_data[$type_key]['children'] = $rm_dtl['children'];

                            // by webkul to calculate rates of the product from hotelreservation syatem tables with feature prices....

                            $hotelCartBookingData = new HotelCartBookingData();
                            //END
                            foreach ($order_bk_data as $data_k => $data_v) {
                                $date_join = strtotime($data_v['date_from']).strtotime($data_v['date_to']);

                                /*Product price when order was created*/
                                $order_details_obj = new OrderDetail($data_v['id_order_detail']);
                                $prod_ord_dtl_name = $order_details_obj->product_name;
                                $cart_htl_data[$type_key]['name'] = $prod_ord_dtl_name;

                                $cart_htl_data[$type_key]['paid_unit_price_tax_excl'] = ($order_details_obj->total_price_tax_excl)/$order_details_obj->product_quantity;
                                $cart_htl_data[$type_key]['paid_unit_price_tax_incl'] = ($order_details_obj->total_price_tax_incl)/$order_details_obj->product_quantity;

                                //work on entring refund data
                                $obj_ord_ref_info = new HotelOrderRefundInfo();
                                $ord_refnd_info = $obj_ord_ref_info->getOderRefundInfoByIdOrderIdProductByDate($id_order, $type_value['product_id'], $data_v['date_from'], $data_v['date_to']);
                                if ($ord_refnd_info) {
                                    $obj_refund_stages = new HotelOrderRefundStages();
                                    $stage_name = $obj_refund_stages->getNameById($ord_refnd_info['refund_stage_id']);
                                } else {
                                    $stage_name = '';
                                    $non_requested_rooms = 1;
                                }
                                if (isset($cart_htl_data[$type_key]['date_diff'][$date_join])) {
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] += 1;

                                    $num_days = $cart_htl_data[$type_key]['date_diff'][$date_join]['num_days'];
                                    $var_quant = (int) $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'];

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

                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_excl'] = $data_v['total_price_tax_excl']/$num_days;
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'] = $data_v['total_price_tax_incl']/$num_days;
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_incl'] = $data_v['total_price_tax_incl'];
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_excl'] = $data_v['total_price_tax_excl'];
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['is_backorder'] = $data_v['is_back_order'];
                                    if ($data_v['is_back_order']) {
                                        $any_back_order = 1;
                                    }
                                    //refund_stage
                                    $cart_htl_data[$type_key]['date_diff'][$date_join]['stage_name'] = $stage_name;
                                }

                                $cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_tax_excl'] = $order_details_obj->unit_price_tax_excl;
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_tax_incl'] = $order_details_obj->unit_price_tax_incl;
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_without_reduction_tax_excl'] = $order_details_obj->unit_price_tax_excl + $order_details_obj->reduction_amount_tax_excl;
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_without_reduction_tax_incl'] = $order_details_obj->unit_price_tax_incl + $order_details_obj->reduction_amount_tax_incl;

                                $feature_price_diff = (float)($cart_htl_data[$type_key]['date_diff'][$date_join]['product_price_without_reduction_tax_incl'] - $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl']);
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['feature_price_diff'] = $feature_price_diff;
                            }
                        }
                        $redirect_link_terms = $this->context->link->getCMSLink(new CMS(3, $this->context->language->id), null, $this->context->language->id);
                        $this->context->smarty->assign('redirect_link_terms', $redirect_link_terms);
                        $this->context->smarty->assign('cart_htl_data', $cart_htl_data);
                        $this->context->smarty->assign('non_requested_rooms', $non_requested_rooms);

                        //For Advanced Payment
                        $obj_customer_adv = new HotelCustomerAdvancedPayment();
                        $order_adv_dtl = $obj_customer_adv->getCstAdvPaymentDtlByIdOrder($order->id);
                        if ($order_adv_dtl) {
                            $this->context->smarty->assign('order_adv_dtl', $order_adv_dtl);
                        }

                        $shw_bo_msg = Configuration::get('WK_SHOW_MSG_ON_BO');
                        $bo_msg = Configuration::get('WK_BO_MESSAGE');
                        $this->context->smarty->assign(array(
                            'any_back_order' => $any_back_order,
                            'shw_bo_msg' => $shw_bo_msg,
                            'back_ord_msg' => $bo_msg,
                            ));
                        $order_has_invoice = $order->hasInvoice();
                        $this->context->smarty->assign('order_has_invoice', $order_has_invoice);
                    }
                }
                //end
                $this->context->smarty->assign(array(
                    'shop_name' => strval(Configuration::get('PS_SHOP_NAME')),
                    'order' => $order,
                    'return_allowed' => (int) $order->isReturnable(),
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
                    /* DEPRECATED: customizedDatas @since 1.5 */
                    'customizedDatas' => $customizedDatas,
                    /* DEPRECATED: customizedDatas @since 1.5 */
                    'reorderingAllowed' => !(bool) Configuration::get('PS_DISALLOW_HISTORY_REORDERING'),
                ));

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
