<?php
/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminOrderRefundRequestsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_return';
        $this->className = 'OrderReturn';
        $this->list_no_link = true;
        $this->context = Context::getContext();

        $this->_select = ' COUNT(IF(a.`state` = '.(int) Configuration::get('PS_ORS_PENDING').', 1, NULL)) AS total_pending_requests,
        ord.`id_currency`, ord.`total_paid_tax_incl` AS total_order, SUM(a.`refunded_amount`) AS refunded_amount, CONCAT(firstname, " ", lastname) AS cust_name, os.`color`, os.`id_order_state`';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'orders` ord ON (a.`id_order` = ord.`id_order`)';
        $this->_join .= 'LEFT JOIN '._DB_PREFIX_.'order_state os ON (os.`id_order_state` = ord.`current_state`)';
        $this->_join .= 'LEFT JOIN '._DB_PREFIX_.'order_state_lang osl ON (osl.`id_order_state` = os.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'customer` cust ON (cust.`id_customer` = ord.`id_customer`)';

        $this->_orderWay = 'DESC';
        $this->_group = 'GROUP BY ord.`id_order`';

        $orderStatuses = OrderState::getOrderStates($this->context->language->id);
        $ordStatuses = array();
        foreach ($orderStatuses as $status) {
            $ordStatuses[$status['id_order_state']] = $status['name'];
        }
        /*for showing status of booking with badge_danger or success*/
        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('Order ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'setOrderLink',
                'havingFilter' => true,
            ),
            'cust_name' => array(
                'title' => $this->l('Customer Name'),
                'align' => 'center',
                'havingFilter' => true,
                'callback' => 'setCustomerLink',
            ),
            'total_order' => array(
                'title' => $this->l('Total Order Amount'),
                'align' => 'center',
                'callback' => 'setOrderCurrency',
                'havingFilter' => true,
            ),
            'refunded_amount' => array(
                'title' => $this->l('Refunded Amount'),
                'align' => 'center',
                'callback' => 'setOrderCurrency',
                'havingFilter' => true,
            ),
            'total_pending_requests' => array(
                'title' => $this->l('Pending Requests'),
                'align' => 'center',
                'havingFilter' => true,
            ),
        );
        $this->addRowAction('view');
        $this->identifier = 'id_order_return';

        // START send access query information to the admin controller
        $this->access_select = ' SELECT a.`id_order_return` FROM '._DB_PREFIX_.'order_return a';
        $this->access_join = ' INNER JOIN '._DB_PREFIX_.'htl_booking_detail hbd ON (hbd.id_order = a.id_order)';
        if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1)) {
            $this->access_where = ' WHERE hbd.id_hotel IN ('.implode(',', $acsHtls).')';
        }

        parent::__construct();

        // work on renderlist filter on render view page
        if (Tools::isSubmit('submitResetorder_return') && Tools::getValue('id_order')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrderRefundRequests').'&id_order='.Tools::getValue('id_order').'&view'.$this->table);
        }

        $this->_conf[101] = $this->l('Refund request has been denied successfully.');
        $this->_conf[102] = $this->l('Refund request has been completed successfully.');
    }

    public function setOrderCurrency($echo, $row)
    {
        return Tools::displayPrice($echo, (int) $row['id_currency']);
    }

    public function setOrderLink($idOrder, $row)
    {
        return '<a href="'.$this->context->link->getAdminLink('AdminOrders').'&id_order='.$idOrder.'&vieworder">#'.$idOrder.'</a>';
    }

    public function setCustomerLink($customerName, $row)
    {
        return '<a href="'.$this->context->link->getAdminLink('AdminCustomers').'&id_customer='.$row['id_customer'].'&viewcustomer">'.$customerName.' (#'.$row['id_customer'].')</a>';
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function displayViewLink($token, $idOrderReturn, $name = null)
    {
        if (!Tools::getValue('id_order')) {
            $objOrderReturn = new OrderReturn($idOrderReturn);
            return '<a href="'.$this->context->link->getAdminLink('AdminOrderRefundRequests').'&id_order_return='.(int)$idOrderReturn.'&id_order='.(int)$objOrderReturn->id_order.'&view'.$this->table.'" title="'.$this->l('Cancel').'">
                <i class="icon-search-plus"></i> '.$this->l('View').
            '</a>';
        } else {
            return '<a href="'.$this->context->link->getAdminLink('AdminOrderRefundRequests').'&id_order_return='.(int)$idOrderReturn.
            '&view'.$this->table.'" title="'.$this->l('Cancel').'">
                <i class="icon-search-plus"></i> '.$this->l('View').
            '</a>';
        }
    }

    public function renderView()
    {
        if (!($objOrderReturn = $this->loadObject(true))) {
            return;
        }
        $refundStatuses = OrderReturnStateCore::getOrderReturnStates($this->context->language->id);
        if ($idOrder = Tools::getValue('id_order')) {
            // work on renderlist filter on renderview page
            if (!Tools::isSubmit('submitFilterorder_return')) {
                $this->processResetFilters();
            }

            $this->table = 'order_return';
            $this->identifier = 'id_order_return';

            /*for showing status of booking with badge_danger or success*/
            $this->_select = ' CONCAT(cust.`firstname`, " ", cust.`lastname`) AS `cust_name`, ors.`color`, orsl.`name` as `status_name`, ord.`id_currency`';
            $this->_join = ' LEFT JOIN `'._DB_PREFIX_.'customer` cust ON (cust.`id_customer` = a.`id_customer`)';
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'orders` ord ON (ord.`id_order` = a.`id_order`)';
            $this->_join .= 'LEFT JOIN '._DB_PREFIX_.'order_return_state ors ON (ors.`id_order_return_state` = a.`state`)';
            $this->_join .= 'LEFT JOIN '._DB_PREFIX_.'order_return_state_lang orsl ON (orsl.`id_order_return_state` = a.`state` AND orsl.`id_lang` = '.(int)$this->context->language->id.')';

            $this->_where = ' AND a.`id_order`='. (int)$idOrder;
            $this->_group = '';

            $retStatuses = array();
            foreach ($refundStatuses as $status) {
                $retStatuses[$status['id_order_return_state']] = $status['name'];
            }
            $this->fields_list = array(
                'id_order_return' => array(
                    'title' => $this->l('Request ID'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                ),
                'id_order' => array(
                    'title' => $this->l('Order ID'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'callback' => 'setOrderLink',
                    'havingFilter' => true,
                    'filter_key' => 'a!id_order',
                    'filter_type' => 'int',
                ),
                'cust_name' => array(
                    'title' => $this->l('Customer Name'),
                    'align' => 'center',
                    'havingFilter' => true,
                    'callback' => 'setCustomerLink',
                ),
                'status_name' => array(
                    'title' => $this->l('Refund Status'),
                    'type' => 'select',
                    'color' => 'color',
                    'list' => $retStatuses,
                    'filter_key' => 'ors!id_order_return_state',
                    'filter_type' => 'int',
                ),
                'refunded_amount' => array(
                    'title' => $this->l('Refunded Amount'),
                    'align' => 'center',
                    'callback' => 'setOrderCurrency',
                    'havingFilter' => true,
                ),
                'date_add' => array(
                    'title' => $this->l('Requested Date'),
                    'type' => 'datetime',
                    'havingFilter' => true,
                    'filter_key' => 'a!date_add',
                ),
            );

            $this->identifier = 'id_order_return';

            if (Tools::isSubmit('submitFilterorder_return')) {
                $this->processFilter();
            }

            return parent::renderList();
        } else {
            $objCustomer = new Customer($objOrderReturn->id_customer);
            $objOrder = new Order($objOrderReturn->id_order);
            $orderCurrency = new Currency($objOrder->id_currency);

            $objRefundRules = new HotelOrderRefundRules();
            if ($refundReqBookings = $objOrderReturn->getOrderRefundRequestedBookings($objOrderReturn->id_order, $objOrderReturn->id)){
                foreach ($refundReqBookings as &$booking) {
                    $bookingCharges = $objRefundRules->getBookingCancellationDetails(
                        $objOrderReturn->id_order,
                        $objOrderReturn->id,
                        $booking['id']
                    );
                    $booking = array_merge($booking, array_shift($bookingCharges));
                }
            }

            $paymentMethods = array();
            foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
                $module = Module::getInstanceByName($payment['name']);
                if (Validate::isLoadedObject($module) && $module->active) {
                    $paymentMethods[] = $module->displayName;
                }
            }

            $this->context->smarty->assign(
                array (
                    'orderTotalPaid' => $objOrder->getTotalPaid(),
                    'customer_name' => $objCustomer->firstname.' '.$objCustomer->lastname,
                    'customer_email' => $objCustomer->email,
                    'orderReturnInfo' => (array)$objOrderReturn,
                    'refundReqBookings' => $refundReqBookings,
                    'orderInfo' => (array) $objOrder,
                    'orderCurrency' => (array) $orderCurrency,
                    'currentOrderStateInfo' => (array) new OrderState($objOrder->current_state,
                    $this->context->language->id),
                    'currentStateInfo' => (array) new OrderReturnState($objOrderReturn->state,
                    $this->context->language->id),
                    'current_id_lang' => $this->context->language->id,
                    'refundStatuses' => $refundStatuses,
                    'isRefundCompleted' => $objOrderReturn->hasBeenCompleted(),
                    'paymentMethods' => $paymentMethods,
                    'name_controller' => Tools::getValue('controller'),
                    'info_icon_path' => $this->context->link->getMediaLink(_MODULE_DIR_.'hotelreservationsystem/views/img/Slices/icon-info.svg')
                )
            );

            return parent::renderView();
        }
    }

    public function postProcess()
    {
        // for the filteration
        if (Tools::isSubmit('submitFilterorder_return') && Tools::isSubmit('order_returnFilter_ors!id_order_return_state')) {
            self::$currentIndex .= '&id_order_return=1&id_order=1&vieworder_return';
        }

        /*If Admin update the status of the order cancellation request*/
        if (Tools::isSubmit('submitRefundReqBookings') || Tools::isSubmit('submitRefundReqBookingsAndStay')) {
            $idOrderReturn = Tools::getValue('id_order_return');
            $idsReturnDetail = Tools::getValue('id_order_return_detail');
            if (Validate::isLoadedObject($objOrderReturn = new OrderReturn($idOrderReturn))) {
                $objOrder = new Order($objOrderReturn->id_order);
                $orderTotalPaid = $objOrder->getTotalPaid();
                $idRefundState = Tools::getValue('id_refund_state');
                if (Validate::isLoadedObject($objRefundState = new OrderReturnState($idRefundState))) {
                    if ($objRefundState->refunded) {
                        $refundedAmounts = Tools::getValue('refund_amounts');
                        if ((float) $orderTotalPaid > 0) {
                            if ($idsReturnDetail && count($idsReturnDetail)) {
                                if ($refundedAmounts) {
                                    foreach ($idsReturnDetail as $idRetDetail) {
                                        if (!isset($refundedAmounts[$idRetDetail]) || !Validate::isPrice($refundedAmounts[$idRetDetail])) {
                                            $this->errors[] = $this->l('Invalid refund amount(s) entered.');
                                        }
                                    }
                                } else {
                                    $this->errors[] = $this->l('Invalid refund amount(s) entered.');
                                }

                                // If there are no errors in the refund amounts the check validations depends on refund amount
                                if (!count($this->errors)) {
                                    $totalRefundAmount = array_sum($refundedAmounts);
                                    if (Tools::isSubmit('generateCreditSlip')) {
                                        if ($totalRefundAmount <= 0) {
                                            $this->errors[] = $this->l('Invalid refund amount(s) for generating credit slip.');
                                        }
                                    }
                                    if (Tools::isSubmit('generateDiscount')) {
                                        if ($totalRefundAmount <= 0) {
                                            $this->errors[] = $this->l('Invalid refund amount(s) for generating voucher.');
                                        }
                                    }

                                    if (Tools::isSubmit('refundTransactionAmount')) {
                                        if ($totalRefundAmount <= 0) {
                                            $this->errors[] = $this->l('Invalid refund amount(s) for entering refund transaction details.');
                                        } else {
                                            $paymentMode = Tools::getValue('payment_method');
                                            if (!$paymentMode) {
                                                $paymentMode = Tools::getValue('other_payment_mode');
                                                if (!$paymentMode) {
                                                    $this->errors[] = $this->l('Please enter the payment mode of the refund transaction.');
                                                } elseif (!Validate::isGenericName($paymentMode)) {
                                                    $this->errors[] = $this->l('Invalid payment mode entered.');
                                                }
                                            }

                                            $idTransaction = Tools::getValue('id_transaction');
                                            if (!$idTransaction) {
                                                $this->errors[] = $this->l('Please enter the transaction id of the refund transaction.');
                                            } elseif (!Validate::isGenericName($idTransaction)) {
                                                $this->errors[] = $this->l('Invalid transaction id entered.');
                                            }
                                        }
                                    }
                                }
                            } else {
                                $this->errors[] = $this->l('Select at least one booking for refund.');
                            }
                        }
                    }
                } else {
                    $this->errors[] = $this->l('Invalid refund state.');
                }
            } else {
                $this->errors[] = $this->l('Invalid refund information found.');
            }

            if (!count($this->errors)) {
                $bookingList = array();
                $totalRefundedAmount = 0;

                // If refund is completed then work on the booking list
                if ($objRefundState->refunded) {
                    foreach ($idsReturnDetail as $idRetDetail) {
                        $objOrderReturnDetail = new OrderReturnDetail($idRetDetail);
                        // set booking as refunded if return state is refunded/denied
                        $idHtlBooking = $objOrderReturnDetail->id_htl_booking;
                        $reduction_amount = array(
                            'total_price_tax_excl' => 0,
                            'total_price_tax_incl' => 0,
                            'total_products_tax_excl' => 0,
                            'total_products_tax_incl' => 0,
                        );

                        $objHtlBooking = new HotelBookingDetail($idHtlBooking);
                        // perform booking refund processes in the booking tables
                        $objHtlBooking->processRefundInBookingTables();

                        // save individual booking amount for every booking refund
                        $refundedAmount = $refundedAmounts[$idRetDetail];
                        $objOrderReturnDetail->refunded_amount = $refundedAmount;
                        $objOrderReturnDetail->save();

                        // sum the refund amount for total order refund amount
                        $totalRefundedAmount += $refundedAmount;

                        if (Tools::isSubmit('generateCreditSlip')) {
                            $numDays = $objHtlBooking->getNumberOfDays(
                                $objHtlBooking->date_from,
                                $objHtlBooking->date_to
                            );

                            $objHtlBooking = new HotelBookingDetail($idHtlBooking);
                            $idOrderDetail = $objHtlBooking->id_order_detail;

                            $bookingList[$idHtlBooking] = array(
                                'id_htl_booking' => $idHtlBooking,
                                'id_order_detail' => $idOrderDetail,
                                'quantity' => $numDays,
                                'num_days' => $numDays,
                                'unit_price' => $refundedAmount / $numDays,
                                'amount' => $refundedAmount,
                            );
                        }
                    }

                    // if bookings are refunded then set the payment information
                    if ((float) $orderTotalPaid > 0) {
                        if (Tools::isSubmit('refundTransactionAmount')) {
                            $objOrderReturn->payment_mode = $paymentMode;
                            $objOrderReturn->id_transaction = $idTransaction;
                        } elseif (Tools::isSubmit('generateDiscount')) {
                            $objOrderReturn->payment_mode = 'Voucher';
                        } elseif (!((float) $orderTotalPaid)) {
                            $objOrderReturn->payment_mode = 'Unpaid by customer';
                            $objOrderReturn->id_transaction = '-';
                        }
                    }
                }

                $objOrderReturn->refunded_amount = $totalRefundedAmount;
                if ($objOrderReturn->save()) {
                    // change state of the order refund
                    $objOrderReturn->changeIdOrderReturnState($idRefundState);

                    // change state of the order to refunded if all the room bookings in the order are completely refunded
                    if ($objRefundState->refunded) {
                        $idOrderState = 0;
                        if ($objOrder->hasCompletelyRefunded(Order::ORDER_COMPLETE_REFUND_FLAG)) {
                            $idOrderState = Configuration::get('PS_OS_REFUND');
                        } elseif ($objOrder->hasCompletelyRefunded(Order::ORDER_COMPLETE_REFUND_FLAG)) {
                            $idOrderState = Configuration::get('PS_OS_CANCELED');
                        }

                        // If order is completely refunded or cancelled then change the order state
                        if ($idOrderState) {
                            // check if order is paid the set status of the order to refunded
                            $objOrderHistory = new OrderHistory();
                            $objOrderHistory->id_order = (int)$objOrder->id;

                            $useExistingPayment = false;
                            if (!$objOrder->hasInvoice()) {
                                $useExistingPayment = true;
                            }

                            $objOrderHistory->changeIdOrderState($idOrderState, $objOrder, $useExistingPayment);
                            $objOrderHistory->addWithemail();
                        }
                    }

                    // E-mail params
                    if ((Tools::isSubmit('generateCreditSlip') || Tools::isSubmit('generateDiscount')) && !count($this->errors)) {
                        $customer = new Customer((int)($objOrder->id_customer));
                        $params['{lastname}'] = $customer->lastname;
                        $params['{firstname}'] = $customer->firstname;
                        $params['{id_order}'] = $objOrder->id;
                        $params['{order_name}'] = $objOrder->getUniqReference();
                    }

                    // Generate credit slip
                    if (Tools::isSubmit('generateCreditSlip') && !count($this->errors)) {
                        if (!$idCreditSlip = OrderSlip::create($objOrder, $bookingList, 0, 0, 0, 0)) {
                            $this->errors[] = $this->l('A credit slip cannot be generated. ');
                        } else {
                            $objOrderReturn->id_return_type = $idCreditSlip;
                            $objOrderReturn->return_type = OrderReturn::RETURN_TYPE_ORDER_SLIP;
                            $objOrderReturn->save();

                            Hook::exec('actionOrderSlipAdd', array('order' => $objOrder, 'bookingList' => $bookingList));

                            $params['{credit_slip_url}'] = $this->context->link->getPageLink('order-slip', true);

                            @Mail::Send(
                                (int)$objOrder->id_lang,
                                'credit_slip',
                                Mail::l('New credit slip regarding your order', (int)$objOrder->id_lang),
                                $params,
                                $customer->email,
                                $customer->firstname.' '.$customer->lastname,
                                null,
                                null,
                                null,
                                null,
                                _PS_MAIL_DIR_,
                                true,
                                (int)$objOrder->id_shop
                            );
                        }
                    } elseif (Tools::isSubmit('generateDiscount') && !count($this->errors)) {
                        // Generate voucher
                        $cartrule = new CartRule();
                        $language_ids = Language::getIDs();
                        $cartrule->description = sprintf($this->l('Voucher for order #%d'), $objOrder->id);
                        foreach ($language_ids as $id_lang) {
                            // Define a temporary name
                            $cartrule->name[$id_lang] = 'V0C'.(int)($objOrder->id_customer).'O'.(int)($objOrder->id);
                        }
                        // Define a temporary code
                        $cartrule->code = 'V0C'.(int)($objOrder->id_customer).'O'.(int)($objOrder->id);

                        $cartrule->quantity = 1;
                        $cartrule->quantity_per_user = 1;
                        // Specific to the customer
                        $cartrule->id_customer = $objOrder->id_customer;
                        $now = time();
                        $cartrule->date_from = date('Y-m-d H:i:s', $now);
                        $cartrule->date_to = date('Y-m-d H:i:s', $now + (3600 * 24 * 365.25)); /* 1 year */
                        $cartrule->active = 1;

                        $cartrule->reduction_amount = $totalRefundedAmount;
                        $cartrule->reduction_tax = true;
                        $cartrule->minimum_amount_currency = $objOrder->id_currency;
                        $cartrule->reduction_currency = $objOrder->id_currency;

                        if (!$cartrule->add()) {
                            $this->errors[] = $this->errors('You cannot generate a voucher.');
                        } else {
                            $objOrderReturn->id_return_type = $cartrule->id;
                            $objOrderReturn->return_type = OrderReturn::RETURN_TYPE_CART_RULE;
                            $objOrderReturn->save();
                            // Update the voucher code and name
                            foreach ($language_ids as $id_lang) {
                                $cartrule->name[$id_lang] = 'V'.(int)($cartrule->id).'C'.(int)($objOrder->id_customer).'O'.$objOrder->id;
                            }
                            $cartrule->code = 'V'.(int)($cartrule->id).'C'.(int)($objOrder->id_customer).'O'.$objOrder->id;
                            if (!$cartrule->update()) {
                                $this->errors[] = $this->l('You cannot generate a voucher.');
                            } else {
                                $currency = $this->context->currency;
                                $params['{voucher_amount}'] = Tools::displayPrice($cartrule->reduction_amount, $currency, false);
                                $params['{voucher_num}'] = $cartrule->code;

                                @Mail::Send(
                                    (int)$objOrder->id_lang,
                                    'voucher',
                                    sprintf(Mail::l('New voucher for your order #%s', (int)$objOrder->id_lang), $objOrder->reference),
                                    $params,
                                    $customer->email,
                                    $customer->firstname.' '.$customer->lastname,
                                    null,
                                    null,
                                    null,
                                    null,
                                    _PS_MAIL_DIR_,
                                    true,
                                    (int)$objOrder->id_shop
                                );
                            }
                        }
                    }

                    // redirect with success if process completed successfully
                    $confirmation = 4;
                    if ($objRefundState->denied) {
                        $confirmation = 101;
                    } elseif ($objRefundState->refunded) {
                        $confirmation = 102;
                    }

                    if (Tools::isSubmit('submitRefundReqBookingsAndStay')) {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&conf='.$confirmation.'&id_order_return='.$idOrderReturn.
                            '&vieworder_return&token='.$this->token
                        );
                    } else {
                        Tools::redirectAdmin(
                            self::$currentIndex.'&conf='.$confirmation.'&id_order='.$objOrder->id.
                            '&id_order_return='.$idOrderReturn.'&vieworder_return&token='.$this->token
                        );
                    }
                }

            }

            $this->display = 'view';
        }

        parent::postProcess();
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJqueryUI('ui.tooltip', 'base', true);

        $this->addJs(_MODULE_DIR_.$this->module->name.'/views/js/admin/wk_refund_request.js');
        $this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/admin/wk_refund_request.css');
    }
}
