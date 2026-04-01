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

class AdminDuitkuTransactionController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'QdpDuitkuTransaction';
        $this->table = 'qdp_duitku_payment_transaction';
        $this->identifier = 'id_duitku_payment_transaction';
        // Hotel wise access
        $this->context = Context::getContext();
        $this->access_select = ' SELECT a.`id_duitku_payment_transaction` FROM ' . _DB_PREFIX_ . 'qdp_duitku_payment_transaction as a';
        $this->access_join = ' INNER JOIN ' . _DB_PREFIX_ . 'orders ord ON (a.id_cart = ord.id_cart)';
        $this->access_join .= ' INNER JOIN ' . _DB_PREFIX_ . 'htl_booking_detail hbd ON (hbd.id_order = ord.id_order)';
        if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1)) {
            $this->access_where = ' WHERE hbd.id_hotel IN (' . implode(',', $acsHtls) . ')';
        }

        $this->list_no_link = true;
        parent::__construct();

        $this->_new_list_header_design = true;

        $this->_select = 'GROUP_CONCAT(distinct ord.`id_order`) as `id_orders`, ord.`reference`, ord.`id_order`, c.`email` AS email,
            c.`id_customer`, CONCAT(c.`firstname`, \' \', c.`lastname`) AS `name`';
        $this->_join .= ' LEFT JOIN ' . _DB_PREFIX_ . 'orders ord on (a.`id_cart` = ord.`id_cart`)';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = ord.`id_customer`)';
        $this->_group = ' GROUP BY a.`id_duitku_payment_transaction`';
        $this->_orderWay = 'DESC';


        $paymentStatus = QdpDuitkuTransaction::getPaymentStatuses();
        $this->fields_list = array(
            'id_transaction' => array(
                'title' => $this->l('Transaction ID'),
                'align' => 'center',
                'hint' => $this->l('Duitku transaction reference  of payment done by the customer.')
            ),
            'reference' => array(
                'title' => $this->l('Order Reference'),
                'callback' => 'getOrderLink',
                'align' => 'center',
                'hint' => $this->l('Order reference of the Duitku payment.'),
            ),
            'id_customer' => array(
                'title' => $this->l('Customer'),
                'align' => 'center',
                'callback' => 'getCustomerInfo',
                'havingFilter' => true,
                'filter_key' => 'email',
                'hint' => $this->l('Customer who paid using Duitku payment.'),
            ),
            'cart_total' => array(
                'title' => $this->l('Transaction Amount'),
                'align' => 'right',
                'class' => 'fixed-width-lg',
                'callback' => 'checkCurrency',
                'hint' => $this->l('Total amount paid by the customer.')
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'type' => 'select',
                'list' => $paymentStatus,
                'filter_key' => 'a!status',
                'callback' => 'getRenderListColumnAsBadge',
                'hint' => $this->l('Current status of the transaction.')
            ),
            'date_add' => array(
                'title' => $this->l('Order Date'),
                'type' => 'datetime',
                'havingFilter' => true,
                'hint' => $this->l('Transaction date.'),
            ),
        );
    }

    public function renderList()
    {
        unset($this->toolbar_btn['new']);
        $this->addRowAction('edit');
        return parent::renderList();
    }

    public function getOrderLink($orderReference, $row)
    {
        $orders = explode(',', $row['id_orders']);
        $this->context->smarty->assign(
            array(
                'order_reference' => $orderReference,
                'details' => $row,
                'orders' => $orders,
                'admin_order_link' => $this->context->link->getAdminLink('AdminOrders'),
            )
        );

        return $this->createTemplate('getOrderLink.tpl')->fetch();
    }

    public function checkCurrency($val, $row)
    { 
        $currency = new Currency($row['id_currency']);
        return Tools::displayPrice($val, $currency);
    }

    public function getCustomerInfo($idCustomer, $row)
    {
        $this->context->smarty->assign(
            array(
                'id_customer' => $idCustomer,
                'details' => $row
            )
        );

        return $this->createTemplate('getCustomerInfo.tpl')->fetch();
    }

    public function getRenderListColumnAsBadge($columnValue)
    {
        $refundStatuses = QdpDuitkuTransaction::getPaymentStatuses();
        $badgeMap = array(
            QdpDuitkuTransaction::QDP_TRANSACTION_COMPLETED => 'badge-success',
            QdpDuitkuTransaction::QDP_TRANSACTION_AWAITING => 'badge-primary',
            QdpDuitkuTransaction::QDP_TRANSACTION_FAILED => 'badge-danger',
        );
        $this->context->smarty->assign(
            array(
                'badge' => $badgeMap[$columnValue],
                'text' => $refundStatuses[$columnValue]
            )
        );

        return $this->createTemplate('getStatusBadge.tpl')->fetch();
    }

    public function displayEditLink($token, $id, $name = null)
    {
        $this->context->smarty->assign(
            array(
                'table' => $this->table,
                'token' => $this->token,
                'current' => self::$currentIndex,
                'id_duitku_payment_transaction' => $id
            )
        );

        return $this->createTemplate('getViewIcon.tpl')->fetch();
    }

    public function renderView()
    {
        $this->page_header_toolbar_title = $this->l('Transaction details');

        return parent::renderView();
    }

    public function renderForm()
    {

        $this->page_header_toolbar_title = $this->l('Transaction details');
        $this->page_header_toolbar_btn['add'] = array(
            'href' => self::$currentIndex . '&token=' . $this->token,
            'desc' => $this->l('Back to list'),
            'icon' => 'process-icon-back'
        );

        if (($id = Tools::getValue('id_duitku_payment_transaction')) || ($id = Tools::getValue('id'))) {
            if (Validate::isLoadedObject($objDuitkuTransaction = new QdpDuitkuTransaction((int) $id))) {
                $idCart = $objDuitkuTransaction->id_cart;
                if (Validate::isLoadedObject($objCart = new Cart((int) $idCart))) {
                    $transactionInfo = (array) $objDuitkuTransaction;
                    if (Validate::isLoadedObject($objOrder = new Order(Order::getOrderByCartId($idCart)))) {
                        $transactionInfo['reference'] = $objOrder->reference;
                    } else {
                        $transactionInfo['reference'] = $this->l('N/A');
                    }

                    $objCustomer = new Customer((int) $objOrder->id_customer);
                    $transactionInfo['customer_link'] = $this->context->link->getAdminLink(
                        'AdminCustomers',
                        Tools::getAdminTokenLite('AdminCustomers')
                    ) . '&id_customer=' . (int) $objCustomer->id . '&viewcustomer';
                    $transactionInfo['customer_name'] = $objCustomer->firstname . ' ' . $objCustomer->lastname;
                    $transactionInfo['email'] = $objCustomer->email;
                    $objCurrency = new Currency($objCart->id_currency);
                    $this->context->smarty->assign(
                        array(
                            'QDP_DUITKU_ENVIRONMENT_SANDBOX' =>
                            QdpDuitkuTransaction::QDP_DUITKU_ENVIRONMENT_SANDBOX,
                            'currency' => $objCurrency,
                            'TRANSACTION_COMPLETED' => QdpDuitkuTransaction::QDP_TRANSACTION_COMPLETED,
                            'TRANSACTION_AWAITING' => QdpDuitkuTransaction::QDP_TRANSACTION_AWAITING,
                            'TRANSACTION_FAILED' => QdpDuitkuTransaction::QDP_TRANSACTION_FAILED,
                            'transaction_info' => $transactionInfo,
                        )
                    );
                }
            }
        }

        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        return parent::renderForm();
    }
}
