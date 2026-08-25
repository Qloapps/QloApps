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

/**
 * @property OrderSlip $object
 */
class AdminSlipControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_slip';
        $this->className = 'OrderSlip';
        $this->list_no_link = true;

        $this->_select = ' o.`id_shop`';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'orders o ON (o.`id_order` = a.`id_order`)';
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'htl_booking_detail hbd
        ON (a.`id_order` = hbd.`id_order`) ' . HotelBranchInformation::addHotelRestriction(false, 'hbd');
        $this->_group = ' GROUP BY a.`id_order_slip`';

        $this->addRowAction('statusChange');

        $this->fields_list = array(
            'id_order_slip' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'id_order' => array(
                'title' => $this->l('Order ID'),
                'align' => 'center',
                'class' => 'fixed-width-md',
                'havingFilter' => true,
                'callback' => 'displayOrderLink',
            ),
            'date_add' => array(
                'title' => $this->l('Date issued'),
                'type' => 'date',
                'class' => 'fixed-width-md',
                'align' => 'center',
                'filter_key' => 'a!date_add'
            ),
            'id_pdf' => array(
                'title' => $this->l('PDF'),
                'align' => 'center',
                'callback' => 'printPDFIcons',
                'class' => 'fixed-width-xxl',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true
            ),
            'redeem_status' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'list' => array(
                    OrderSlip::REDEEM_STATUS_ACTIVE => $this->l('Active'),
                    OrderSlip::REDEEM_STATUS_REDEEMED => $this->l('Redeemed'),
                ),
                'align' => 'center',
                'filter_key' => 'a!redeem_status',
                'callback' => 'displayRedeemStatus',
                'class' => 'fixed-width-xxl',
            ),
            'id_cart_rule' => array(
                'title' => $this->l('Voucher'),
                'align' => 'center',
                'callback' => 'displayVoucherLink',
                'class' => 'fixed-width-xxl',
            ),
            'remark' => array(
                'title' => $this->l('Remark'),
                'filter_key' => 'a!remark',
                'align' => 'center',
                'orderby' => false,
                'callback' => 'displayRemark',
            ),
        );

        $this->_select = 'a.id_order_slip AS id_pdf';

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->l('Credit slip options'),
                'fields' =>    array(
                    'PS_CREDIT_SLIP_PREFIX' => array(
                        'title' => $this->l('Credit slip prefix'),
                        'desc' => $this->l('Prefix used for credit slips.'),
                        'size' => 6,
                        'type' => 'textLang'
                    )
                ),
                'submit' => array('title' => $this->l('Save'))
            )
        );

        parent::__construct();

        $this->_where = Shop::addSqlRestriction(false, 'o');
        $this->_conf['33'] = $this->l('Voucher generated successfully');
    }

public function initPageHeaderToolbar()
    {
        if (empty($this->display) || $this->display == 'list') {
            $this->page_header_toolbar_btn['new_credit_slip'] = array(
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('Add Credit Slip', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

   public function renderForm()
    {
        if ($this->display == 'add') {
            $orderList = Order::getOrdersWithInformations(null, null, true);
            foreach ($orderList as &$order) {
                $order['order_label'] = $order['reference'] . ' #' . (int) $order['id_order'];
            }
            unset($order);

            $this->fields_form = array(
                'legend' => array(
                    'title' => $this->l('Credit slip'),
                    'icon' => 'icon-print'
                ),
                'description' => '<strong>' . $this->l('Note: Before generating the credit slip, please review all details carefully. Once the credit slip is generated, it cannot be modified.') . '</strong>',
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Order id'),
                        'name' => 'id_order',
                        'required' => true,
                        'hint' => $this->l('Select an Order ID to generate the credit slip'),
                        'class' => 'chosen',
                        'col' => 3,
                        'desc' => $this->context->smarty->fetch(
                            _PS_ADMIN_DIR_ . '/themes/default/template/controllers/slip/_order_info_desc.tpl'
                        ),
                        'options' => array(
                            'query' => $orderList,
                            'id' => 'id_order',
                            'name' => 'order_label'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Room Type'),
                        'name' => 'id_room_type',
                        'required' => true,
                        'hint' => $this->l('Select a room type'),
                        'col' => 3,
                        'options' => array(
                            'query' => array(),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Room'),
                        'name' => 'id_booking_detail',
                        'required' => true,
                        'hint' => $this->l('Select a room to generate the credit slip for'),
                        'col' => 3,
                        'desc' => $this->context->smarty->fetch(
                            _PS_ADMIN_DIR_ . '/themes/default/template/controllers/slip/_booking_room_service_total_desc.tpl'
                        ),
                        'options' => array(
                            'query' => array(),
                            'id' => 'id',
                            'name' => 'room_label'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Amount of credit slip'),
                        'hint' => $this->l('Enter the amount of the credit slip for the customer'),
                        'name' => 'credit_slip_amount',
                        'required' => true,
                        'col' => 3,
                        'suffix' => $this->context->currency->sign,
                        'desc' => $this->context->smarty->fetch(
                            _PS_ADMIN_DIR_ . '/themes/default/template/controllers/slip/_booking_amount_desc.tpl'
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Remark'),
                        'hint' => $this->l('Enter any remark for this credit slip'),
                        'name' => 'remark',
                        'required' => true,
                        'col' => 9,
                        'rows' => 3,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Generate'),
                    'name' => 'submitCreditSlip',
                )
            );
        } else {
            $this->fields_form = array(
                'legend' => array(
                    'title' => $this->l('Print a PDF'),
                    'icon' => 'icon-print'
                ),
                'input' => array(
                    array(
                        'type' => 'date',
                        'label' => $this->l('From'),
                        'name' => 'date_from',
                        'maxlength' => 10,
                        'required' => true,
                        'hint' => $this->l('Format: 2011-12-31 (inclusive).')
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('To'),
                        'name' => 'date_to',
                        'maxlength' => 10,
                        'required' => true,
                        'hint' => $this->l('Format: 2012-12-31 (inclusive).')
                    )
            ),
            'submit' => array(
                'title' => $this->l('Generate PDF file'),
                'id' => 'submitPrint',
                'icon' => 'process-icon-download-alt'
            )
        );
        }

        $this->fields_value = array(
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d')
        );

        $this->show_toolbar = false;
        return parent::renderForm();
    }

   public function postProcess()
    {
        if (Tools::getValue('submitCreditSlip')) {
            $creditSlipAmount = trim(Tools::getValue('credit_slip_amount'));
            if (empty($creditSlipAmount)) {
                $this->errors[] = $this->l('Credit slip amount is required.');
            } elseif (!is_numeric($creditSlipAmount)) {
                $this->errors[] = $this->l('Credit slip amount is not valid');
            } elseif ((float) $creditSlipAmount <= 0) {
                $this->errors[] = $this->l('Credit slip amount must be greater than 0');
            }

            $remark = trim(Tools::getValue('remark'));
            if (empty($remark)) {
                $this->errors[] = $this->l('Remark is required.');
            } elseif (!Validate::isString($remark) || !Validate::isMessage($remark) || !Validate::isCleanHtml($remark)) {
                $this->errors[] = $this->l('Remark must be a valid text.');
            }

            if (!count($this->errors)) {
                $objOrder = new Order(Tools::getValue('id_order'));
                $creditAmount = $creditSlipAmount;
                $idBookingDetail = Tools::getValue('id_booking_detail');
                $customer = new Customer($objOrder->id_customer);
                $bookingList = array();
                $objHotelBookingDetail = new HotelBookingDetail($idBookingDetail);

                if ($idHtlBooking = $objHotelBookingDetail->id) {
                    $numDays = HotelHelper::getNumberOfDays(
                        $objHotelBookingDetail->date_from,
                        $objHotelBookingDetail->date_to
                    );

                    $idOrderDetail = $objHotelBookingDetail->id_order_detail;

                    $bookingList = array(
                        array(
                            'id_htl_booking' => $idHtlBooking,
                            'id_order_detail' => $idOrderDetail,
                            'quantity' => $numDays,
                            'num_days' => $numDays,
                            'unit_price' => (float) $creditAmount / $numDays,
                            'amount' => (float) $creditAmount,
                        )
                    );
                }

                if (!$idCreditSlip = OrderSlip::create($objOrder, $bookingList, 0, $creditAmount, $creditAmount, 0 , OrderSlip::ORDER_SLIP_TYPE_MANUAL, $remark)) {
                    $this->errors[] = $this->l('A credit slip cannot be generated. ');
                } else {

                    Hook::exec('actionOrderSlipAdd', array('order' => $objOrder, 'bookingList' => $bookingList));

                    $params['{credit_slip_url}'] = $this->context->link->getPageLink('order-slip', true);

                    @Mail::Send(
                        (int)$objOrder->id_lang,
                        'credit_slip',
                        Mail::l('New credit slip generated for you', (int)$objOrder->id_lang),
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
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token . '&conf=3');
            } else {
                $this->display = 'add';
                return;
            }
        } else if (Tools::getValue('submitAddorder_slip')) {
            if (!Validate::isDate(Tools::getValue('date_from'))) {
                $this->errors[] = $this->l('Invalid "From" date');
            }
            if (!Validate::isDate(Tools::getValue('date_to'))) {
                $this->errors[] = $this->l('Invalid "To" date');
            }
            if (!count($this->errors)) {
                $order_slips = OrderSlip::getSlipsIdByDate(Tools::getValue('date_from'), Tools::getValue('date_to'));
                if (count($order_slips)) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateOrderSlipsPDF&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')));
                }
                $this->errors[] = $this->l('No order slips were found for this period.');
            }
        } else if (Tools::isSubmit('generateVoucher')) {
            if (($idOrderSlip = Tools::getValue('id_order_slip'))
                && Validate::isLoadedObject($objOrderSlip = new OrderSlip($idOrderSlip))
            ) {
                if ($objOrderSlip->redeem_status == OrderSlip::REDEEM_STATUS_REDEEMED) {
                    $this->errors[] = Tools::displayError('The credit slip has already been redeemed.');
                }

                if (!count($this->errors)) {
                    if ($objOrderSlip->generateVoucher()) {
                        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=33');
                    } else {
                        $this->errors[] = Tools::displayError('The voucher code for this credit slip could not be generated.');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('Credit slip not found.');
            }

        } else {
            return parent::postProcess();
        }
    }

    public function processStatusChange()
    {
        $idOrderSlip = Tools::getValue('id_order_slip');
        $objOrderSlip = new OrderSlip($idOrderSlip);

        if (!Validate::isLoadedObject($objOrderSlip)) {
            $this->errors[] = $this->l('The credit slip can not be loaded.');
        } elseif ($objOrderSlip->redeem_status != OrderSlip::REDEEM_STATUS_ACTIVE) {
            $this->errors[] = $this->l('The status for this credit slip can not be changed.');
        } else {
            $objOrder = new Order($objOrderSlip->id_order);
            $objCustomer = new Customer($objOrderSlip->id_customer);

            if (!Validate::isLoadedObject($objOrder)) {
                $this->errors[] = $this->l('The related order for this credit slip can not be loaded.');
            }

            if (!Validate::isLoadedObject($objCustomer)) {
                $this->errors[] = $this->l('The related customer for this credit slip can not be loaded.');
            }
        }

        if (!count($this->errors)) {
            $objOrderSlip->redeem_status = OrderSlip::REDEEM_STATUS_REDEEMED;
            if ($objOrderSlip->save()) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
            }

            $this->errors[] = $this->l('Something went wrong while changing status.');
        }
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        if ($this->display != "add") {
            $this->content .= $this->renderList();
        }
        $this->content .= $this->renderForm();
        if ($this->display != "add") {
            $this->content .= $this->renderOptions();
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    public function initToolbar()
    {
        $this->toolbar_btn = array();
    }

    public function printPDFIcons($id_order_slip, $tr)
    {
        $order_slip = new OrderSlip((int)$id_order_slip);
        if (!Validate::isLoadedObject($order_slip)) {
            return '';
        }

        $this->context->smarty->assign(array(
            'order_slip' => $order_slip,
            'tr' => $tr
        ));

        return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
    }

    public function displayRedeemStatus($redeemStatus, $row)
    {
        $this->context->smarty->assign(array(
            'redeem_status' => $redeemStatus,
        ));

        return $this->createTemplate('_redeem_status.tpl')->fetch();
    }

    public function displayVoucherLink($idCartRule, $row)
    {
        $this->context->smarty->assign(array(
            'id_cart_rule' => (int) $idCartRule,
            'row' => $row
        ));

        return $this->createTemplate('_display_voucher_link.tpl')->fetch();
    }

    public function displayOrderLink($idOrder, $row)
    {
        $this->context->smarty->assign(array(
            'id_order' => (int) $idOrder,
        ));

        return $this->createTemplate('_display_order_link.tpl')->fetch();
    }

    public function displayRemark($remark, $row)
    {
        if (empty($remark)) {
            return '--';
        }

        $maxLength = 100;
        if (Tools::strlen($remark) > $maxLength) {
            return '<span title="' . htmlspecialchars($remark, ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars(Tools::substr($remark, 0, $maxLength), ENT_QUOTES, 'UTF-8')
                . '<strong style="cursor:pointer;">...</strong></span>';
        }

        return htmlspecialchars($remark, ENT_QUOTES, 'UTF-8');
    }

    public function displayStatusChangeLink($token, $id)
    {
        $objOrderSlip = new OrderSlip($id);
        if ($objOrderSlip->redeem_status != OrderSlip::REDEEM_STATUS_REDEEMED) {
            $statusChangeLink = self::$currentIndex.'&'.$this->identifier.'='.$id.'&action=statusChange'.
            '&token='.($token != null ? $token : $this->token);

            $this->context->smarty->assign(array(
                'status_change_link' => $statusChangeLink,
            ));

            return $this->createTemplate('_status_change_link.tpl')->fetch();
        }

        return '--';
    }

    public function ajaxProcessInitSlipStatusModal()
    {
        $response = array('success' => false);
        if ($idOrderSlip = Tools::getValue('id_order_slip')) {
            if (Validate::isLoadedObject($objOrderSlip = new OrderSlip($idOrderSlip, $this->context->language->id))) {
                $objOrder = new Order($objOrderSlip->id_order);
                $objCustomer = new Customer($objOrder->id_customer);

                $modalConfirmDelete = $this->createTemplate('modal_confirm_update.tpl');
                $modalConfirmDelete->assign(array(
                    'customer' => $objCustomer,
                    'order' => $objOrder,
                    'orderSlip' => $objOrderSlip,
                    'link' => $this->context->link
                ));
                $tpl = $this->createTemplate('modal.tpl');
                $tpl->assign(array(
                    'modal_id' => 'moduleConfirmUpdate',
                    'modal_class' => 'modal-md',
                    'modal_content' => $modalConfirmDelete->fetch(),
                    'modal_actions' => array(
                        array(
                            'type' => 'link',
                            'href' => '#',
                            'class' => 'process_update btn-primary',
                            'label' => $this->l('Change status'),
                        ),
                    ),
                ));
                $response['modalHtml'] = $tpl->fetch();
            }
        }
        $response['success'] = true;

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessGetBookingDetails()
    {
        $idOrder = (int) Tools::getValue('id_order');
        $objOrder = new Order($idOrder);
        $currency = Currency::getCurrency((int)$objOrder->id_currency);
        $objHotelBookingDetail = new HotelBookingDetail();
        $bookingDetails = $objHotelBookingDetail->getBookingDataByOrderId($idOrder);

        $objBookingDemands = new HotelBookingDemands();
        $objServiceProductOrderDetail = new ServiceProductOrderDetail();

        foreach ($bookingDetails as &$booking) {
            $booking['extra_service_total_price_tax_incl'] = 0;

            $extraDemands = $objBookingDemands->getRoomTypeBookingExtraDemands(
                $idOrder,
                $booking['id_product'],
                $booking['id_room'],
                $booking['date_from'],
                $booking['date_to'],
                0,
                0,
                1,
                $booking['id']
            );
            if ($extraDemands) {
                foreach ($extraDemands as $demand) {
                    $booking['extra_service_total_price_tax_incl'] += (float) $demand['total_price_tax_incl'];
                }
            }

            $roomServices = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
                0, 0, 0, 0, 0, 0, 0, 0, null, null, null, 0, $booking['id']
            );
            if ($roomServices && isset($roomServices[$booking['id']]['additional_services'])) {
                foreach ($roomServices[$booking['id']]['additional_services'] as $service) {
                    $booking['extra_service_total_price_tax_incl'] += (float) $service['total_price_tax_incl'];
                }
            }
        }
        unset($booking);

        $totalSlipAmount = OrderSlip::getTotalOrderSlipAmountByOrder($idOrder);
        $slipIds = array_column(OrderSlip::getSlipIdsByOrder($idOrder), 'id_order_slip');
        $objOrderReturn = new OrderReturn();
        $refundedAmount = $objOrderReturn->getRefundedAmount($idOrder);

        die(Tools::jsonEncode(array(
            'bookings' => $bookingDetails,
            'currency' => $currency,
            'total_slip_amount' => $totalSlipAmount,
            'slip_ids' => $slipIds,
            'order_total_amount' => (float) $objOrder->total_paid,
            'order_total_paid' => (float) $objOrder->total_paid_real,
            'order_refunded_amount' => (float) $refundedAmount
        )));
    }

    public function setMedia()
    {
        parent::setMedia();
        Media::addJsDef(
            array(
                'admin_order_slip_tab_link' => $this->context->link->getAdminLink('AdminSlip'),
                'admin_order_view_link' => $this->context->link->getAdminLink('AdminOrders'),
                'restoreRoomTypeId' => (int) Tools::getValue('id_room_type'),
                'restoreBookingDetailId' => (int) Tools::getValue('id_booking_detail'),
            )
        );
        $this->addJS(_PS_JS_DIR_.'admin/slips.js');
    }

}
