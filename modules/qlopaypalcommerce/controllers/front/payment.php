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

class QloPaypalCommercePaymentModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();

        $cart = $this->context->cart;

        // cehck cart details
        if ($cart->id_customer == 0
            || !$this->module->active
        ) {
            Tools::redirect($this->context->link->getPageLink('order-opc', true, null));
        }

        // Check that this payment option is still available in case the customer
        // changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'qlopaypalcommerce') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'payment'));
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect($this->context->link->getPageLink('order-opc', true, null));
        }
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->module->secure_key != Tools::getValue('token')) {
            die('Invalid token.');
        }

        if (Tools::isSubmit('action')) {
            $action = (int)Tools::getValue('action');
            switch ($action) {
                case 1:
                    $json = Tools::file_get_contents('php://input');
                    $orderDetails = Tools::jsonDecode($json, true);

                    $cart = $this->context->cart;

                    WkPaypalCommerceHelper::logMsg('payment', 'Payment initiated...', true);
                    WkPaypalCommerceHelper::logMsg('payment', 'Environment: '. Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE'));
                    WkPaypalCommerceHelper::logMsg('payment', 'Cart ID: '. $cart->id);
                    WkPaypalCommerceHelper::logMsg('payment', 'Customer ID: '. $cart->id_customer);
                    WkPaypalCommerceHelper::logMsg('payment', 'Currency ID: '. $cart->id_currency);
                    WkPaypalCommerceHelper::logMsg('payment', 'Cart Total: '. $cart->getOrderTotal(true, Cart::BOTH));
                    WkPaypalCommerceHelper::logMsg('payment', 'Payment request data: ');

                    $ppCommerce = new PayPalCommerce();
                    header('Content-Type: application/json');

                    // create order
                    $orderDetails['original'] = $this->getOrderDetails();
                    WkPaypalCommerceHelper::logMsg('payment', Tools::jsonEncode($orderDetails));

                    $ppOrderData = $ppCommerce->orders->create($orderDetails);

                    WkPaypalCommerceHelper::logMsg('payment', 'Payment response data: ', true);
                    WkPaypalCommerceHelper::logMsg('payment', Tools::jsonEncode($ppOrderData));

                    die(Tools::jsonEncode($ppOrderData));
                case 2:
                    $json = Tools::file_get_contents('php://input');
                    $orderData = Tools::jsonDecode($json, true);

                    if ($orderData['orderID'] && $orderData['getOrderData']) {
                        $ppCommerce = new PayPalCommerce();
                        $returnData = $ppCommerce->orders->capture($orderData['orderID']);
                        header('Content-Type: application/json');
                        echo Tools::jsonEncode($returnData['data']);
                        die;
                    } elseif (Tools::getIsset('order_id')) {
                        $orderID = Tools::getValue('order_id');
                        $ppCommerce = new PayPalCommerce();
                        $returnData = $ppCommerce->orders->get($orderID);
                        $cart = $this->context->cart;
                        $customer = new Customer($cart->id_customer);

                        WkPaypalCommerceHelper::logMsg('payment', 'Payment response...', true);
                        WkPaypalCommerceHelper::logMsg('payment', 'Cart ID: '. $cart->id);
                        WkPaypalCommerceHelper::logMsg('payment', 'Customer ID: '. $cart->id_customer);
                        WkPaypalCommerceHelper::logMsg('payment', 'Currency ID: '. $cart->id_currency);
                        WkPaypalCommerceHelper::logMsg('payment', 'Cart Total: '. $cart->getOrderTotal(true, Cart::BOTH));
                        WkPaypalCommerceHelper::logMsg('payment', Tools::jsonEncode($returnData));

                        if (isset($returnData['data']['id']) && !empty($returnData['data']['id'])) {
                            // Payment success
                            $paypalOrderID = $returnData['data']['id'];

                            // Save order data
                            $this->saveOrderData($returnData);
                            if ($paypalOrderID) {
                                $currency = $this->context->currency;
                                $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
                                if ($cart->is_advance_payment) {
                                    $total = (float)$cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
                                } else {
                                    $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
                                }

                                // set order status
                                if ($returnData['data']['status'] == 'COMPLETED') {
                                    if ($cart->is_advance_payment) {
                                        $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                                    } else {
                                        $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                                    }
                                } else {
                                    $orderStatus = Configuration::get('PS_OS_AWAITING_PAYMENT');
                                }

                                // create order for the payment
                                $this->module->validateOrder(
                                    $cart->id,
                                    $orderStatus,
                                    $total,
                                    $this->module->l('PayPal Checkout', 'payment'),
                                    null,
                                    null,
                                    (int)$currency->id,
                                    false,
                                    $customer->secure_key
                                );

                                // Update order id in paypal_seller_order table
                                $objOrder = new Order($this->module->currentOrder);
                                WkPayPalCommerceOrder::updateOrderReference(
                                    $paypalOrderID,
                                    $objOrder->reference
                                );

                                WkPaypalCommerceHelper::logMsg('payment', 'Order ID: '. $this->module->currentOrder);
                                WkPaypalCommerceHelper::logMsg('payment', '--------------', true);

                                $link = new Link();
                                $orderLink = $link->getPageLink(
                                    'order-confirmation',
                                    null,
                                    (int)Context::getContext()->language->id,
                                    array(
                                        'id_cart' => (int)$cart->id,
                                        'id_module' => (int)$this->module->id,
                                        'id_order' => (int)$this->module->currentOrder,
                                        'key' => $customer->secure_key,
                                    )
                                );
                                Tools::redirect($orderLink);
                            } else {
                                WkPaypalCommerceHelper::logMsg('payment', 'Payment status'. $returnData['data']['status']);
                                WkPaypalCommerceHelper::logMsg('payment', '--------------', true);
                                Tools::redirect('index.php?controller=order&step=1');
                            }
                        } else {
                            WkPaypalCommerceHelper::logMsg('payment', 'Payment failed...', true);
                            WkPaypalCommerceHelper::logMsg(
                                'payment',
                                'Payment failed message: '.$returnData['data']['name']
                            );
                            WkPaypalCommerceHelper::logMsg(
                                'payment',
                                'Payment failed message: '.$returnData['data']['message']
                            );
                            WkPaypalCommerceHelper::logMsg('payment', '--------------', true);
                            $failedLink = $this->context->link->getModuleLink(
                                'qlopaypalcommerce',
                                'errorpayment',
                                array(
                                    'err_name' => $returnData['data']['name'],
                                    'err_msg' => $returnData['data']['message']
                                )
                            );
                            Tools::redirect($failedLink);
                        }
                    }
                    break;
                case 3:
                    WkPaypalCommerceHelper::logMsg('payment', 'Payment cancelled by customer...', true);
                    WkPaypalCommerceHelper::logMsg('payment', Tools::jsonEncode(Tools::getAllValues()));
                    WkPaypalCommerceHelper::logMsg('payment', '--------------', true);

                    Tools::redirect($this->context->link->getPageLink('order-opc', true, null).'?pp_cancel=1');
                    break;
                default:
                    # code...
                    break;
            }
        }
        die;
    }

    // Save order data
    private function saveOrderData($orderData)
    {
        if ($orderData) {
            $purchaseUnits = $orderData['data']['purchase_units'];
            foreach ($purchaseUnits as $purchase) {
                $transaction_id = $purchase['payments']['captures'][0]['id'];
                $payment_status = $purchase['payments']['captures'][0]['status'];
                $payment_total = $purchase['payments']['captures'][0]['amount']['value'];
                $payment_curr = $purchase['payments']['captures'][0]['amount']['currency_code'];

                $cart = $this->context->cart;
                $currency = Currency::getCurrency((int) $cart->id_currency);

                // total order amount
                if ($cart->is_advance_payment) {
                    $cartTotalAmountTI = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
                } else {
                    $cartTotalAmountTI = $cart->getOrderTotal(true, Cart::BOTH);
                }

                $orderObj = new WkPayPalCommerceOrder();
                $orderObj->order_reference = '';
                $orderObj->id_cart = (int)$cart->id;
                $orderObj->id_currency = (int)$cart->id_currency;
                $orderObj->environment = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
                $orderObj->id_customer = (int)$cart->id_customer;
                $orderObj->order_total = (float)$cartTotalAmountTI;
                $orderObj->checkout_currency = $currency['iso_code'];

                // PayPal Returned Data
                $orderObj->pp_paid_total = (float)$payment_total;
                $orderObj->pp_paid_currency = $payment_curr;
                $orderObj->pp_reference_id = $purchase['reference_id'];
                $orderObj->pp_order_id = $orderData['data']['id'];
                $orderObj->pp_transaction_id = $transaction_id;
                $orderObj->pp_payment_status = $payment_status;
                $orderObj->response = Tools::jsonEncode($orderData);
                $orderObj->order_date = date('Y-m-d H:i:s');
                $orderObj->save();
            }

            return true;
        }

        return false;
    }

    // Create order data to send to PayPal
    private function getOrderDetails()
    {
        $orderData = array();
        $orderData['intent'] = 'CAPTURE';

        $cart = $this->context->cart;
        $customer = new Customer((int)$cart->id_customer);

        $ppHelper = new WkPaypalCommerceHelper();
        $bilAddr = $ppHelper->getSimpleAddress(
            (int)$cart->id_customer,
            (int)$cart->id_address_invoice
        );

        $orderData['payer'] = array(
            'name' => array(
                'given_name' => $customer->firstname,
                'surname' => $customer->lastname
            ),
            'email_address' => $customer->email
        );

        $orderData['payer']['address'] = array(
            'address_line_1' => $bilAddr['address1'],
            'address_line_2' => $bilAddr['address2'],
            'admin_area_2' => $bilAddr['city'],
            'admin_area_1' => $bilAddr['state_iso'],
            'postal_code' => $bilAddr['postcode'],
            'country_code' => $bilAddr['country_iso'],
        );

        $timestamp = time().rand(100, 999);
        $currency = Currency::getCurrency((int) $cart->id_currency);

        $discountTI = $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
        $discountTE = $cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS);


        // total cart amount
        // items totals will be calculated after removing discount from the total cart as additional facility is also considered as an item
        if ($cart->is_advance_payment) {
            $cartTotalAmountTI = $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
            $cartTotalAmountTE = $cart->getOrderTotal(false, Cart::ADVANCE_PAYMENT);

            $itemTotalAmountTI = $cartTotalAmountTI + $discountTI;
            $itemTotalAmountTE = $cartTotalAmountTE + $discountTE;
        } else {
            $cartTotalAmountTI = $cart->getOrderTotal(true, Cart::BOTH);
            $cartTotalAmountTE = $cart->getOrderTotal(false, Cart::BOTH);

            $itemTotalAmountTI = $cartTotalAmountTI + $discountTI;
            $itemTotalAmountTE = $cartTotalAmountTE + $discountTE;
        }

        $orderData['purchase_units'][0] = array(
            'reference_id' => $timestamp,
            'description' => "Payment for " . Configuration::get('PS_SHOP_NAME'),
            // Maxlength 22
            'soft_descriptor' => Tools::substr(Configuration::get('PS_SHOP_NAME'), 0, 21),
            'payment_group_id' => 1,
            'custom_id' => $cart->id,
            'invoice_id' => 'INV-' . $timestamp,
        );

        $orderData['purchase_units'][0]['amount'] = array(
            'currency_code' => $currency['iso_code'],
            'value' => Tools::ps_round($cartTotalAmountTI, 2),
            'breakdown' => array(
                'item_total' => array(
                    'currency_code' => $currency['iso_code'],
                    'value' => Tools::ps_round($itemTotalAmountTE, 2),
                ),
                'shipping' => array(
                    'currency_code' => $currency['iso_code'],
                    'value' => 0,
                ),
                'shipping_discount' => array(
                    'currency_code' => $currency['iso_code'],
                    'value' => 0,
                ),
                'tax_total' => array(
                    'currency_code' => $currency['iso_code'],
                    'value' => Tools::ps_round(($itemTotalAmountTI - $itemTotalAmountTE), 2),
                ),
                'discount' => array(
                    'currency_code' => $currency['iso_code'],
                    'value' => Tools::ps_round($discountTI, 2),
                ),
            )
        );

        // set item details (for rounding issue, paypal said not to send item details)
        // $items = $ppHelper->getPaypalOrderItemDetails($cart->id);
        // $orderData['purchase_units'][0]['items'] = array_values($items);

        $orderData['purchase_units'][0]['payee'] = array(
            'merchant_id' => Configuration::get('WK_PAYPAL_COMMERCE_MERCHANT_ID')
        );

        $orderData['application_context'] = array(
            'user_action' => 'PAY_NOW',
            'shipping_preference' => 'NO_SHIPPING',
        );

        return $orderData;
    }

    /**
     * getPriceDecimalPrecision
     * @return void
     */
    private function getPriceDecimalPrecision()
    {
        return _PS_PRICE_COMPUTE_PRECISION_;
    }
}
