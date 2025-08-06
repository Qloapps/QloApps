<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
*/

class AdminPaypalCommerceTransactionController extends ModuleAdminController
{
    public function __construct()
    {
        $this->identifier = 'id_paypal_commerce_order';
        parent::__construct();

        $this->bootstrap = true;
        $this->list_no_link = true;

        $this->table = 'wk_paypal_commerce_order';
        $this->className = 'WKPayPalCommerceOrder';

        $this->_select = 'a.*, CONCAT(c.firstname, \' \', c.lastname) as customer_name, c.email';
        $this->_join = ' LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer = a.id_customer)';
        $this->_where = ' AND a.`id_cart` != 0';
        $this->_orderBy = 'id_paypal_commerce_order';
        $this->_defaultOrderWay = 'DESC';

        $this->toolbar_title = $this->l('PayPal Transactions');

        $this->fields_list = array(
            'order_reference' => array(
                'title' => $this->l('Order Reference'),
                'align' => 'center',
                'havingFilter' => true,
                'hint' => $this->l('Order reference in QloApps of the PayPal transaction'),
            ),
            'pp_transaction_id' => array(
                'title' => $this->l('PayPal Transaction ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'havingFilter' => true,
                'hint' => $this->l('Id of the PayPal transaction'),
            ),
            'pp_paid_total' => array(
                'title' => $this->l('Transaction Total'),
                'align' => 'center',
                'havingFilter' => true,
                'callback' => 'setCurrency',
                'hint' => $this->l('Total amount paid in the PayPal transaction'),
            ),
            'customer_name' => array(
                'title' => $this->l('Customer'),
                'align' => 'center',
                'havingFilter' => true,
                'align' => 'center',
                'callback' => 'getCustomerInfo',
                'havingFilter' => true,
                'filter_key' => 'customer_name',
                'hint' => $this->l('Customer in the QloApps who did PayPal transaction.'),
            ),
            'pp_payment_status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'hint' => $this->l('Payment Status'),
                'orderBy' => false,
                'hint' => $this->l('Current status of the PayPal transaction.'),
            ),
            'order_date' => array(
                'title' => $this->l('Order Date'),
                'type' => 'datetime',
                'align' => 'center',
                'hint' => $this->l('Date of order creation.'),
            )
        );
    }

    public function getCustomerInfo($customerName, $row)
    {
        return '<a href="'.$this->context->link->getAdminLink('AdminCustomers').'&id_customer='.$row['id_customer'].'&viewcustomer">'.$customerName.'<br>'.'('.$row['email'].')</a>';
    }

    public function setCurrency($val, $row)
    {
        $objCart = new Cart($row['id_cart']);
        $currency = new Currency($objCart->id_currency);

        return Tools::displayPrice($val, $currency);
    }

    public function initPageHeaderToolbar()
    {
        if ($this->display == 'edit' || $this->display == 'add' || $this->display == 'view') {
            $this->page_header_toolbar_btn['back_to_list'] = array(
                'href' => Context::getContext()->link->getAdminLink(
                    'AdminPaypalCommerceTransaction',
                    true
                ),
                'desc' => $this->l('Back to list'),
                'icon' => 'process-icon-back'
            );
        }
        parent::initPageHeaderToolbar();
    }

    public function renderList()
    {
        $this->addRowAction('view');
        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function renderView()
    {
        $idTrans = (int)Tools::getValue('id_paypal_commerce_order');
        if ($idTrans > 0) {
            $smartyVars = array();
            $refundData = array();
            $totalRefunded = 0;
            $transactionData = WKPayPalCommerceOrder::getTransactionDetails((int)$idTrans);
            $orderCurrency = new Currency((int)$transactionData['id_currency']);
            $refundData = WkPaypalCommerceRefund::getRefundListByTransID((int)$idTrans);
            $totalRefundedFormatted = WkPaypalCommerceRefund::getTotalRefundedAmount((int)$idTrans, true);

            $totalRefunded = WkPaypalCommerceRefund::getTotalRefundedAmount((int)$idTrans, false);
            $remainingRefund = (float)($transactionData['pp_paid_total'] - $totalRefunded);
            $response = Tools::jsonDecode($transactionData['response'], true);

            // Buyer making a payment in a different currency (ex: EUR) which is different from the default currency of merchant (Ex: USD), In all those cross currency cases, After Capture, transaction will fall into Pending state and will require merchant to manually go to his PayPal account and accept the payment.
            $objPPOrder = new WKPayPalCommerceOrder();
            if (isset($response['data']['purchase_units'][0]['payments']['captures'][0]['status_details']['reason'])
                && ($transactionData['pp_payment_status'] == 'PENDING' || $transactionData['pp_payment_status'] == 'DENIED')
                && isset($response['data']['purchase_units'][0]['payments']['captures'][0]['status_details']['reason'])
            ) {
                if (isset($objPPOrder->ppStatusDetail[$response['data']['purchase_units'][0]['payments']['captures'][0]['status_details']['reason']])) {
                    $smartyVars['ppstatusDetailMsg'] = $objPPOrder->ppStatusDetail[$response['data']['purchase_units'][0]['payments']['captures'][0]['status_details']['reason']];
                }
            }

            $smartyVars['transaction_data'] = $transactionData;
            $smartyVars['refund_data'] = $refundData;
            $smartyVars['refunded_amount'] = $totalRefundedFormatted;
            $smartyVars['remaining_refund'] = $remainingRefund;
            $smartyVars['remaining_refund_format'] = Tools::displayPrice($remainingRefund, $orderCurrency);
            $smartyVars['currency'] = $orderCurrency;
            $smartyVars['WK_PAYPAL_COMMERCE_REFUND_TYPE_FULL'] = WkPaypalCommerceRefund::WK_PAYPAL_COMMERCE_REFUND_TYPE_FULL;
            $smartyVars['WK_PAYPAL_COMMERCE_REFUND_TYPE_PARTIAL'] = WkPaypalCommerceRefund::WK_PAYPAL_COMMERCE_REFUND_TYPE_PARTIAL;

            $this->context->smarty->assign($smartyVars);
        }
        $this->base_tpl_view = 'view.tpl';
        return parent::renderView();
    }

    public function postProcess()
    {

        if (Tools::getValue('refund_paypal_form')
            && (Tools::getValue('token')) == Tools::getAdminTokenLite('AdminPaypalCommerceTransaction')
        ) {
            $idTrans = (int)Tools::getValue('id_paypal_commerce_order');
            $refundAmt = (float)Tools::getValue('refund_amount');
            $refundReason = Tools::getValue('refund_reason');
            $refundType = (int)Tools::getValue('refund_type');

            if (!$refundType) {
                $this->errors[] = $this->l('Invalid refund type.');
            }
            if (!Validate::isPrice($refundAmt)) {
                $this->errors[] = $this->l('Invalid refund amount.');
            }
            if (!Validate::isMessage($refundReason)) {
                $this->errors[] = $this->l('Enter valid refund reason (only alphanumeric allowed).');
            }

            if ((!count($this->errors))) {
                $transactionData = WKPayPalCommerceOrder::getTransactionDetails((int)$idTrans);
                if ($transactionData) {
                    $totalRefunded = WkPaypalCommerceRefund::getTotalRefundedAmount((int)$idTrans, false);
                    if ($remainingRefund = (float)($transactionData['pp_paid_total'] - $totalRefunded)) {
                        // if refund type is partial then check the amount to be refunded
                        if (($refundType == WkPaypalCommerceRefund::WK_PAYPAL_COMMERCE_REFUND_TYPE_PARTIAL)
                            && ($refundAmt > $remainingRefund)
                        ) {
                            $orderCurrency = new Currency((int)$transactionData['id_currency']);
                            $this->errors[] = $this->l('Invalid refund amount. Max. available amount for refund is').' '.Tools::displayPrice($remainingRefund, $orderCurrency);
                        }

                        // if no errors the proceed for the refund
                        if ((!count($this->errors))) {
                            if ($refundType == WkPaypalCommerceRefund::WK_PAYPAL_COMMERCE_REFUND_TYPE_FULL) {
                                $refundAmt = (float)($transactionData['pp_paid_total'] - $totalRefunded);
                            }
                            if ($transactionData['pp_paid_total'] == $refundAmt) {
                                $refundType = WkPaypalCommerceRefund::WK_PAYPAL_COMMERCE_REFUND_TYPE_FULL;
                            }
                            if ($refundAmt) {
                                $postData = array();
                                $postData['amount'] = array(
                                    'currency_code' => $transactionData['pp_paid_currency'],
                                    'value' => $refundAmt
                                );
                                $postData['transaction_id'] = $transactionData['pp_transaction_id'];
                                $postData['refund_reason'] = $refundReason;
                                $postData['auth_assertion'] = WkPaypalCommerceHelper::payPalAuthAssertion();

                                WkPaypalCommerceHelper::logMsg('refund', 'Refund initiated...', true);
                                WkPaypalCommerceHelper::logMsg('refund', 'Environment: '. Configuration::get('MP_PAYPAL_PAYMENT_MODE'));
                                WkPaypalCommerceHelper::logMsg('refund', 'Order Ref: '. $transactionData['order_reference']);
                                WkPaypalCommerceHelper::logMsg('refund', 'Customer ID: '. $transactionData['id_customer']);
                                WkPaypalCommerceHelper::logMsg('refund', 'PayPal Transaction ID: '. $transactionData['pp_transaction_id']);
                                WkPaypalCommerceHelper::logMsg('refund', 'PayPal Order ID: '. $transactionData['pp_order_id']);
                                WkPaypalCommerceHelper::logMsg('refund', 'Refund request data: ');
                                WkPaypalCommerceHelper::logMsg('refund', Tools::jsonEncode($postData));

                                $objPPCommerce = new PayPalCommerce();
                                $refundData = $objPPCommerce->orders->refund($postData);

                                // check if refund id is there
                                if (isset($refundData['data']['status']) && isset($refundData['data']['id'])) {
                                    $refundID = $refundData['data']['id'];

                                    WkPaypalCommerceHelper::logMsg('refund', 'Refund success: ', true);
                                    WkPaypalCommerceHelper::logMsg('refund', 'PayPal Refund Id: '. $refundID);
                                    WkPaypalCommerceHelper::logMsg('refund', 'Refund reponse data: ');
                                    WkPaypalCommerceHelper::logMsg('refund', Tools::jsonEncode($refundData));
                                    WkPaypalCommerceHelper::logMsg('refund', '----------------------- ', true);

                                    $refundObj = new WkPaypalCommerceRefund();
                                    $refundObj->order_trans_id = (int)$idTrans;
                                    $refundObj->paypal_refund_id = $refundID;
                                    $refundObj->refund_amount = (float)$refundAmt;
                                    $refundObj->refund_type = (int)$refundType;
                                    $refundObj->currency_code = $transactionData['pp_paid_currency'];
                                    $refundObj->refund_reason = $refundReason;
                                    $refundObj->response = Tools::jsonEncode($refundData);
                                    $refundObj->refund_status = $refundData['data']['status'];
                                    if ($refundObj->save()) {
                                        $urlString = '&viewwk_paypal_commerce_order=&id_paypal_commerce_order=' . (int)Tools::getValue('id_paypal_commerce_order');

                                        Tools::redirectAdmin(self::$currentIndex.$urlString.'&conf=4&token='.$this->token);
                                    }
                                } else {
                                    WkPaypalCommerceHelper::logMsg('refund', 'Refund failed: ', true);
                                    WkPaypalCommerceHelper::logMsg('refund', 'Refund reponse data: ');
                                    WkPaypalCommerceHelper::logMsg('refund', Tools::jsonEncode($refundData));
                                    WkPaypalCommerceHelper::logMsg('refund', '----------------------- ', true);
                                    $this->errors[] = $refundData['data']['message'];
                                }
                            } else {
                                $this->errors[] = $this->l('Refund is already done for this transaction.');
                            }
                        }
                    } else {
                        $this->errors[] = $this->l('Refund is already done for this transaction.');
                    }
                } else {
                    $this->errors[] = $this->l('Invalid request');
                }
            }
        }

        parent::postProcess();
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJS(_PS_MODULE_DIR_ . 'qlopaypalcommerce/views/js/admin/wk_paypal_transaction.js');
        $this->addCSS(_PS_MODULE_DIR_ . 'qlopaypalcommerce/views/css/admin/wk_paypal_transaction.css');
    }
}
