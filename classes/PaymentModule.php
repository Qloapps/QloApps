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

abstract class PaymentModuleCore extends Module
{
    const DEBUG_MODE = false;

    /** @var int Current order's id */
    public $currentOrder;
    public $currencies = true;
    public $currencies_mode = 'checkbox';
    public $payment_type = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT;

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Insert currencies availability
        if ($this->currencies_mode == 'checkbox') {
            if (!$this->addCheckboxCurrencyRestrictionsForModule()) {
                return false;
            }
        } elseif ($this->currencies_mode == 'radio') {
            if (!$this->addRadioCurrencyRestrictionsForModule()) {
                return false;
            }
        } else {
            Tools::displayError('No currency mode for payment module');
        }

        // Insert countries availability
        $return = $this->addCheckboxCountryRestrictionsForModule();

        if (!Configuration::get('CONF_'.strtoupper($this->name).'_FIXED')) {
            Configuration::updateValue('CONF_'.strtoupper($this->name).'_FIXED', '0.2');
        }
        if (!Configuration::get('CONF_'.strtoupper($this->name).'_VAR')) {
            Configuration::updateValue('CONF_'.strtoupper($this->name).'_VAR', '2');
        }
        if (!Configuration::get('CONF_'.strtoupper($this->name).'_FIXED_FOREIGN')) {
            Configuration::updateValue('CONF_'.strtoupper($this->name).'_FIXED_FOREIGN', '0.2');
        }
        if (!Configuration::get('CONF_'.strtoupper($this->name).'_VAR_FOREIGN')) {
            Configuration::updateValue('CONF_'.strtoupper($this->name).'_VAR_FOREIGN', '2');
        }

        return $return;
    }

    public function uninstall()
    {
        if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_country` WHERE id_module = '.(int)$this->id)
            || !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_currency` WHERE id_module = '.(int)$this->id)
            || !Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_group` WHERE id_module = '.(int)$this->id)) {
            return false;
        }
        return parent::uninstall();
    }


    /**
     * Add checkbox currency restrictions for a new module
     * @param array $shops
     *
     * @return bool
     */
    public function addCheckboxCurrencyRestrictionsForModule(array $shops = array())
    {
        if (!$shops) {
            $shops = Shop::getShops(true, null, true);
        }

        foreach ($shops as $s) {
            if (!Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'module_currency` (`id_module`, `id_shop`, `id_currency`)
					SELECT '.(int)$this->id.', "'.(int)$s.'", `id_currency` FROM `'._DB_PREFIX_.'currency` WHERE deleted = 0')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add radio currency restrictions for a new module
     * @param array $shops
     *
     * @return bool
     */
    public function addRadioCurrencyRestrictionsForModule(array $shops = array())
    {
        if (!$shops) {
            $shops = Shop::getShops(true, null, true);
        }

        foreach ($shops as $s) {
            if (!Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'module_currency` (`id_module`, `id_shop`, `id_currency`)
				VALUES ('.(int)$this->id.', "'.(int)$s.'", -2)')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add checkbox country restrictions for a new module
     * @param array $shops
     *
     * @return bool
     */
    public function addCheckboxCountryRestrictionsForModule(array $shops = array())
    {
        $countries = Country::getCountries((int)Context::getContext()->language->id, true); //get only active country
        $country_ids = array();
        foreach ($countries as $country) {
            $country_ids[] = $country['id_country'];
        }
        return Country::addModuleRestrictions($shops, $countries, array(array('id_module' => (int)$this->id)));
    }

    /**
     * Validate an order in database
     * Function called from a payment module
     *
     * @param int $id_cart
     * @param int $id_order_state
     * @param float   $amount_paid    Amount really paid by customer (in the default currency)
     * @param string  $payment_method Payment method (eg. 'Credit card')
     * @param null    $message        Message to attach to order
     * @param array   $extra_vars
     * @param null    $currency_special
     * @param bool    $dont_touch_amount
     * @param bool    $secure_key
     * @param Shop    $shop
     *
     * @return bool
     * @throws PrestaShopException
     */
    public function validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method = 'Unknown',
        $message = null, $extra_vars = array(), $currency_special = null, $dont_touch_amount = false,
        $secure_key = false, Shop $shop = null)
    {
        if (self::DEBUG_MODE) {
            PrestaShopLogger::addLog('PaymentModule::validateOrder - Function called', 1, null, 'Cart', (int)$id_cart, true);
        }

        if (!isset($this->context)) {
            $this->context = Context::getContext();
        }
        $this->context->cart = new Cart((int)$id_cart);
        $this->context->customer = new Customer((int)$this->context->cart->id_customer);
        // The tax cart is loaded before the customer so re-cache the tax calculation method
        $this->context->cart->setTaxCalculationMethod();

        $this->context->language = new Language((int)$this->context->cart->id_lang);
        $this->context->shop = ($shop ? $shop : new Shop((int)$this->context->cart->id_shop));
        ShopUrl::resetMainDomainCache();
        $id_currency = $currency_special ? (int)$currency_special : (int)$this->context->cart->id_currency;
        $this->context->currency = new Currency((int)$id_currency, null, (int)$this->context->shop->id);
        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
            $context_country = $this->context->country;
        }

        $order_status = new OrderState((int)$id_order_state, (int)$this->context->language->id);
        if (!Validate::isLoadedObject($order_status)) {
            PrestaShopLogger::addLog('PaymentModule::validateOrder - Order Status cannot be loaded', 3, null, 'Cart', (int)$id_cart, true);
            throw new PrestaShopException('Can\'t load Order status');
        }

        if (!$this->active) {
            PrestaShopLogger::addLog('PaymentModule::validateOrder - Module is not active', 3, null, 'Cart', (int)$id_cart, true);
            die(Tools::displayError());
        }

        // Does order already exists ?
        if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->OrderExists() == false) {
            if ($secure_key !== false && $secure_key != $this->context->cart->secure_key) {
                PrestaShopLogger::addLog('PaymentModule::validateOrder - Secure key does not match', 3, null, 'Cart', (int)$id_cart, true);
                die(Tools::displayError());
            }

            // For each package, generate an order
            $delivery_option_list = $this->context->cart->getDeliveryOptionList();
            $package_list = $this->context->cart->getPackageList();
            $cart_delivery_option = $this->context->cart->getDeliveryOption();

            // If some delivery options are not defined, or not valid, use the first valid option
            foreach ($delivery_option_list as $id_address => $package) {
                if (!isset($cart_delivery_option[$id_address]) || !array_key_exists($cart_delivery_option[$id_address], $package)) {
                    foreach ($package as $key => $val) {
                        $cart_delivery_option[$id_address] = $key;
                        break;
                    }
                }
            }

            $order_list = array();
            $order_detail_list = array();

            do {
                $reference = Order::generateReference();
            } while (Order::getByReference($reference)->count());

            $this->currentOrderReference = $reference;

            $order_creation_failed = false;
            $cart_total_paid = (float)Tools::ps_round((float)$this->context->cart->getOrderTotal(true, Cart::BOTH), 2);

            if ($this->context->cart->is_advance_payment) {
                $cart_total_paid = (float)Tools::ps_round(
                    (float)$this->context->cart->getOrderTotal(true, CART::ADVANCE_PAYMENT),
                    2
                );
            }

            foreach ($cart_delivery_option as $id_address => $key_carriers) {
                foreach ($delivery_option_list[$id_address][$key_carriers]['carrier_list'] as $id_carrier => $data) {
                    foreach ($data['package_list'] as $id_package) {
                        // Rewrite the id_warehouse
                        $package_list[$id_address][$id_package]['id_warehouse'] = (int)$this->context->cart->getPackageIdWarehouse($package_list[$id_address][$id_package], (int)$id_carrier);
                        $package_list[$id_address][$id_package]['id_carrier'] = $id_carrier;
                    }
                }
            }
            // Make sure CartRule caches are empty
            CartRule::cleanCache();
            $cart_rules = $this->context->cart->getCartRules();
            foreach ($cart_rules as $cart_rule) {
                if (($rule = new CartRule((int)$cart_rule['obj']->id)) && Validate::isLoadedObject($rule)) {
                    if ($error = $rule->checkValidity($this->context, true, true)) {
                        $this->context->cart->removeCartRule((int)$rule->id);
                        if (isset($this->context->cookie) && isset($this->context->cookie->id_customer) && $this->context->cookie->id_customer && !empty($rule->code)) {
                            if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
                                Tools::redirect('index.php?controller=order-opc&submitAddDiscount=1&discount_name='.urlencode($rule->code));
                            }
                            Tools::redirect('index.php?controller=order&submitAddDiscount=1&discount_name='.urlencode($rule->code));
                        } else {
                            $rule_name = isset($rule->name[(int)$this->context->cart->id_lang]) ? $rule->name[(int)$this->context->cart->id_lang] : $rule->code;
                            $error = sprintf(Tools::displayError('CartRule ID %1s (%2s) used in this cart is not valid and has been withdrawn from cart'), (int)$rule->id, $rule_name);
                            PrestaShopLogger::addLog($error, 3, '0000002', 'Cart', (int)$this->context->cart->id);
                        }
                    }
                }
            }

            Hook::exec('actionPackageListGenerateOrder', array('package_list' => &$package_list));
            foreach ($package_list as $id_address => $packageByAddress) {
                foreach ($packageByAddress as $id_package => $package) {
                    /** @var Order $order */
                    $order = new Order();
                    $order->product_list = $package['product_list'];

                    if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
                        $address = new Address((int)$id_address);
                        $this->context->country = new Country((int)$address->id_country, (int)$this->context->cart->id_lang);
                        if (!$this->context->country->active) {
                            throw new PrestaShopException('The delivery address country is not active.');
                        }
                    }

                    $carrier = null;
                    if (!$this->context->cart->isVirtualCart() && isset($package['id_carrier'])) {
                        $carrier = new Carrier((int)$package['id_carrier'], (int)$this->context->cart->id_lang);
                        $order->id_carrier = (int)$carrier->id;
                        $id_carrier = (int)$carrier->id;
                    } else {
                        $order->id_carrier = 0;
                        $id_carrier = 0;
                    }

                    $order->id_customer = (int)$this->context->cart->id_customer;
                    $order->id_address_invoice = (int)$this->context->cart->id_address_invoice;
                    $order->id_address_delivery = (int)$id_address;
                    $addressInfo = HotelBranchInformation::getAddress($package['id_hotel']);

                    $order->id_address_tax = $addressInfo['id_address'];
                    $order->id_currency = $this->context->currency->id;
                    $order->id_lang = (int)$this->context->cart->id_lang;
                    $order->id_cart = (int)$this->context->cart->id;
                    $order->reference = $reference;
                    $order->id_shop = (int)$this->context->shop->id;
                    $order->id_shop_group = (int)$this->context->shop->id_shop_group;

                    $order->secure_key = ($secure_key ? pSQL($secure_key) : pSQL($this->context->customer->secure_key));
                    $order->payment = $payment_method;
                    $order->payment_type = $this->payment_type;
                    if (isset($this->name)) {
                        $order->module = $this->name;
                    }
                    $order->recyclable = $this->context->cart->recyclable;
                    $order->gift = (int)$this->context->cart->gift;
                    $order->gift_message = $this->context->cart->gift_message;
                    $order->mobile_theme = $this->context->cart->mobile_theme;
                    $order->conversion_rate = $this->context->currency->conversion_rate;
                    $amount_paid = !$dont_touch_amount ? Tools::ps_round((float)$amount_paid, 2) : $amount_paid;
                    $order->total_paid_real = 0;

                    $order->total_products = (float)$this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $order->product_list, $id_carrier);
                    $order->total_products_wt = (float)$this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS, $order->product_list, $id_carrier);
                    $order->total_discounts_tax_excl = (float)abs($this->context->cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS, $order->product_list, $id_carrier));
                    $order->total_discounts_tax_incl = (float)abs($this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS, $order->product_list, $id_carrier));
                    $order->total_discounts = $order->total_discounts_tax_incl;

                    $order->total_shipping_tax_excl = (float)$this->context->cart->getPackageShippingCost((int)$id_carrier, false, null, $order->product_list);
                    $order->total_shipping_tax_incl = (float)$this->context->cart->getPackageShippingCost((int)$id_carrier, true, null, $order->product_list);
                    $order->total_shipping = $order->total_shipping_tax_incl;

                    if (!is_null($carrier) && Validate::isLoadedObject($carrier)) {
                        $order->carrier_tax_rate = $carrier->getTaxesRate(new Address((int)$this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
                    }

                    $order->total_wrapping_tax_excl = (float)abs($this->context->cart->getOrderTotal(false, Cart::ONLY_WRAPPING, $order->product_list, $id_carrier));
                    $order->total_wrapping_tax_incl = (float)abs($this->context->cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $order->product_list, $id_carrier));
                    $order->total_wrapping = $order->total_wrapping_tax_incl;

                    $order->total_paid_tax_excl = (float)Tools::ps_round((float)$this->context->cart->getOrderTotal(false, Cart::BOTH, $order->product_list, $id_carrier), _PS_PRICE_COMPUTE_PRECISION_);
                    $order->total_paid_tax_incl = (float)Tools::ps_round((float)$this->context->cart->getOrderTotal(true, Cart::BOTH, $order->product_list, $id_carrier), _PS_PRICE_COMPUTE_PRECISION_);

                    $order->total_paid = $order->total_paid_tax_incl;
                    $order->round_mode = Configuration::get('PS_PRICE_ROUND_MODE');
                    $order->round_type = Configuration::get('PS_ROUND_TYPE');

                    $order->invoice_date = '0000-00-00 00:00:00';
                    $order->delivery_date = '0000-00-00 00:00:00';

                    //source of the order from where booking came
                    if (isset($this->orderSource) && $this->orderSource) {
                        $order->source = $this->orderSource;
                    } else {
                        $order->source = Configuration::get('PS_SHOP_DOMAIN');
                    }

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - Order is about to be added', 1, null, 'Cart', (int)$id_cart, true);
                    }

                    if ($this->name == 'wsorder') {
                        $order->with_occupancy = 0;
                    } else {
                        if (defined('_PS_ADMIN_DIR_')) {
                            if (Configuration::get('PS_BACKOFFICE_ROOM_BOOKING_TYPE') == HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY) {
                                $order->with_occupancy = 1;
                            }
                        } else {
                            if (Configuration::get('PS_FRONT_ROOM_UNIT_SELECTION_TYPE') == HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY) {
                                $order->with_occupancy = 1;
                            }
                        }
                    }

                    // advance payment information
                    $order->is_advance_payment = $this->context->cart->is_advance_payment;
                    if ($order->is_advance_payment) {
                        $order->advance_paid_amount = (float)Tools::ps_round(
                            (float)$this->context->cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT, $order->product_list, $id_carrier),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        if ($totalOrder = $this->context->cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT, null, $id_carrier)) {
                            $order->amount_paid = (float)Tools::ps_round(
                                (($order->advance_paid_amount * $amount_paid) / $totalOrder),
                                _PS_PRICE_COMPUTE_PRECISION_
                            );
                        }
                    } else {
                        $order->advance_paid_amount = (float)Tools::ps_round(
                            (float)$this->context->cart->getOrderTotal(true, Cart::BOTH, $order->product_list, $id_carrier),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        if ($orderTotal = $this->context->cart->getOrderTotal(true, Cart::BOTH, null, $id_carrier)) {
                            $order->amount_paid = (float)Tools::ps_round(
                                (($order->advance_paid_amount * $amount_paid) / $orderTotal),
                                _PS_PRICE_COMPUTE_PRECISION_
                            );
                        }
                    }

                    // Creating order
                    $result = $order->add();

                    if (!$result) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - Order cannot be created', 3, null, 'Cart', (int)$id_cart, true);
                        throw new PrestaShopException('Can\'t save Order');
                    }

                    // save customer guest information
                    if ($id_customer_guest_detail = CartCustomerGuestDetail::getCartCustomerGuest($this->context->cart->id)) {
                        if (Validate::isLoadedObject($objCartCustomerGuestDetail = new CartCustomerGuestDetail(
                            $id_customer_guest_detail
                        ))) {
                            $objOrderCustomerGuestDetail = new OrderCustomerGuestDetail();
                            $objOrderCustomerGuestDetail->id_gender = $objCartCustomerGuestDetail->id_gender;
                            $objOrderCustomerGuestDetail->firstname = $objCartCustomerGuestDetail->firstname;
                            $objOrderCustomerGuestDetail->lastname = $objCartCustomerGuestDetail->lastname;
                            $objOrderCustomerGuestDetail->email = $objCartCustomerGuestDetail->email;
                            $objOrderCustomerGuestDetail->phone = $objCartCustomerGuestDetail->phone;
                            $objOrderCustomerGuestDetail->id_order = (int)$order->id;
                            $objOrderCustomerGuestDetail->save();
                        }
                    }

                    // Amount paid by customer is not the right one -> Status = payment error
                    // We don't use the following condition to avoid the float precision issues : http://www.php.net/manual/en/language.types.float.php
                    // if ($order->total_paid != $order->total_paid_real)
                    // We use number_format in order to compare two string
                    // If webservice order request then no need to impose equal amounts(total cart and sent amount) condition
                    if ($order_status->logable
                        && number_format($cart_total_paid, _PS_PRICE_COMPUTE_PRECISION_) != number_format($amount_paid, _PS_PRICE_COMPUTE_PRECISION_)
                        && $this->name != 'wsorder'
                    ) {
                        // if customer is paying full payment amount
                        $id_order_state = Configuration::get('PS_OS_ERROR');
                    }

                    $order_list[] = $order;

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - OrderDetail is about to be added', 1, null, 'Cart', (int)$id_cart, true);
                    }

                    // Insert new Order detail list using cart for the current order
                    $order_detail = new OrderDetail(null, null, $this->context);
                    $order_detail->createList($order, $this->context->cart, $id_order_state, $order->product_list, 0, true, $package_list[$id_address][$id_package]['id_warehouse']);
                    $order_detail_list[] = $order_detail;

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - OrderCarrier is about to be added', 1, null, 'Cart', (int)$id_cart, true);
                    }

                    // Adding an entry in order_carrier table
                    if (!is_null($carrier)) {
                        $order_carrier = new OrderCarrier();
                        $order_carrier->id_order = (int)$order->id;
                        $order_carrier->id_carrier = (int)$id_carrier;
                        $order_carrier->weight = (float)$order->getTotalWeight();
                        $order_carrier->shipping_cost_tax_excl = (float)$order->total_shipping_tax_excl;
                        $order_carrier->shipping_cost_tax_incl = (float)$order->total_shipping_tax_incl;
                        $order_carrier->add();
                    }
                }
            }

            // The country can only change if the address used for the calculation is the delivery address, and if multi-shipping is activated
            if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
                $this->context->country = $context_country;
            }

            if (!$this->context->country->active) {
                PrestaShopLogger::addLog('PaymentModule::validateOrder - Country is not active', 3, null, 'Cart', (int)$id_cart, true);
                throw new PrestaShopException('The order address country is not active.');
            }

            if (self::DEBUG_MODE) {
                PrestaShopLogger::addLog('PaymentModule::validateOrder - Payment is about to be added', 1, null, 'Cart', (int)$id_cart, true);
            }

            // Register Payment only if the order status validate the order
            if ($order_status->logable) {
                // $order is the last order loop in the foreach
                // The method addOrderPayment of the class Order make a create a paymentOrder
                // linked to the order reference and not to the order id
                if (isset($extra_vars['transaction_id'])) {
                    $transaction_id = $extra_vars['transaction_id'];
                } else {
                    $transaction_id = null;
                }

                if (!isset($order) || !Validate::isLoadedObject($order) || !$order->addOrderPayment($amount_paid, null, $transaction_id, null, null, null, $this->payment_type, false)) {
                    PrestaShopLogger::addLog('PaymentModule::validateOrder - Cannot save Order Payment', 3, null, 'Cart', (int)$id_cart, true);
                    throw new PrestaShopException('Can\'t save Order Payment');
                }

                // now add payment detail for order
                if ($payment = OrderPayment::getByOrderReference($order->reference)) {
                    if ($payment = array_shift($payment)) {
                        foreach($order_list as $order) {
                            $order->addOrderPaymentDetail($payment, $order->amount_paid);
                        }
                    }
                }
            }

            // Next !
            $only_one_gift = false;
            $cart_rule_used = array();
            $products = $this->context->cart->getProducts();

            // Make sure CartRule caches are empty
            CartRule::cleanCache();
            $objRoomType = new HotelRoomType();
            foreach ($order_detail_list as $key => $order_detail) {
                /** @var OrderDetail $order_detail */

                $order = $order_list[$key];
                if (!$order_creation_failed && isset($order->id)) {
                    // first set if_hotel as 0 and get the hotel id from room type info -> below
                    $idHotel = 0;
                    if (!$secure_key) {
                        $message .= '<br />'.Tools::displayError('Warning: the secure key is empty, check your payment account before validation');
                    }
                    // Optional message to attach to this order
                    if (isset($message) & !empty($message)) {
                        $msg = new Message();
                        $message = strip_tags($message, '<br>');
                        if (Validate::isCleanHtml($message)) {
                            if (self::DEBUG_MODE) {
                                PrestaShopLogger::addLog('PaymentModule::validateOrder - Message is about to be added', 1, null, 'Cart', (int)$id_cart, true);
                            }
                            $msg->message = $message;
                            $msg->id_cart = (int)$id_cart;
                            $msg->id_customer = (int)($order->id_customer);
                            $msg->id_order = (int)$order->id;
                            $msg->private = 1;
                            $msg->add();
                        }
                    }

                    // Insert new Order detail list using cart for the current order
                    //$orderDetail = new OrderDetail(null, null, $this->context);
                    //$orderDetail->createList($order, $this->context->cart, $id_order_state);

                    // Construct order detail table for the email
                    $products_list = '';
                    $virtual_product = true;

                    $product_var_tpl_list = array();
                    $orderServiceProducts = array();
                    $objProduct = new Product();

                    foreach ($order->product_list as $product) {
                        $price = Product::getPriceStatic((int)$product['id_product'], false, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart);
                        $price_wt = Product::getPriceStatic((int)$product['id_product'], true, ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart);


                        $product_price = Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt;

                        $product_var_tpl = array(
                            'reference' => $product['reference'],
                            'name' => $product['name'].(isset($product['attributes']) ? ' - '.$product['attributes'] : ''),
                            'unit_price' => Tools::displayPrice($product_price, $this->context->currency, false),
                            'price' => Tools::displayPrice($product_price * $product['quantity'], $this->context->currency, false),
                            'quantity' => $product['quantity'],
                            'customization' => array()
                        );

                        $customized_datas = Product::getAllCustomizedDatas((int)$order->id_cart);
                        if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {
                            $product_var_tpl['customization'] = array();
                            foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
                                $customization_text = '';
                                if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                                    foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                                        $customization_text .= $text['name'].': '.$text['value'].'<br />';
                                    }
                                }

                                if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                                    $customization_text .= sprintf(Tools::displayError('%d image(s)'), count($customization['datas'][Product::CUSTOMIZE_FILE])).'<br />';
                                }

                                $customization_quantity = (int)$product['customization_quantity'];

                                $product_var_tpl['customization'][] = array(
                                    'customization_text' => $customization_text,
                                    'customization_quantity' => $customization_quantity,
                                    'quantity' => Tools::displayPrice($customization_quantity * $product_price, $this->context->currency, false)
                                );
                            }
                        }

                        $product_var_tpl_list[] = $product_var_tpl;
                        // Check if is not a virutal product for the displaying of shipping
                        if (!$product['is_virtual']) {
                            $virtual_product &= false;
                        }
                        // If id_hotel is not available then find id_hotel for sending mails hotel wise
                        if (!$idHotel) {
                            if ($roomDetail = $objRoomType->getRoomTypeInfoByIdProduct((int)$product['id_product'])) {
                                $idHotel = $roomDetail['id_hotel'];
                            }
                        }
                        if (!$product['booking_product'] && $product['service_product_type'] == Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE) {
                            $cover_image_arr = $objProduct->getCover($product['id_product']);

                            if (!empty($cover_image_arr)) {
                                $cover_img = $this->context->link->getImageLink($product['link_rewrite'], $product['id_product'].'-'.$cover_image_arr['id_image'], 'small_default');
                            } else {
                                $cover_img = $this->context->link->getImageLink($product['link_rewrite'], $this->context->language->iso_code."-default", 'small_default');
                            }
                            $product_var_tpl['cover_img'] = $cover_img;
                            $orderServiceProducts[] = $product_var_tpl;
                        }
                    } // end foreach ($products)

                    $product_list_txt = '';
                    $product_list_html = '';
                    if (count($product_var_tpl_list) > 0) {
                        $product_list_txt = $this->getEmailTemplateContent('order_conf_product_list.txt', Mail::TYPE_TEXT, $product_var_tpl_list);
                        $product_list_html = $this->getEmailTemplateContent('order_conf_product_list.tpl', Mail::TYPE_HTML, $product_var_tpl_list);
                    }

                    $cart_rules_list = array();
                    foreach ($cart_rules as $cart_rule) {
                        if ($cart_rule['obj']->reduction_product > 0 && !$order->orderContainProduct($cart_rule['obj']->reduction_product)) {
                            continue;
                        }

                        $package = array('id_carrier' => $order->id_carrier, 'id_address' => $order->id_address_delivery, 'products' => $order->product_list);
                        $values = array(
                            'tax_incl' => $cart_rule['obj']->getContextualValue(true, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package),
                            'tax_excl' => $cart_rule['obj']->getContextualValue(false, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package)
                        );

                        // If the reduction is not applicable to this order, then continue with the next one
                        if (!$values['tax_excl']) {
                            continue;
                        }

                        // IF
                        //	The value of the voucher is greater than the total of the order
                        //	Partial use is allowed
                        //	This is an "amount" reduction, not a reduction in % or a gift
                        // THEN
                        //	The voucher is cloned with a new value corresponding to the remainder
                        $reduction_amount_converted = $cart_rule['obj']->reduction_amount;
                        if ((int) $cart_rule['obj']->reduction_currency !== (int) $this->context->cart->id_currency) {
                            $reduction_amount_converted = Tools::convertPriceFull(
                                $cart_rule['obj']->reduction_amount,
                                new Currency($cart_rule['obj']->reduction_currency),
                                new Currency($this->context->cart->id_currency)
                            );
                        }
                        if ($cart_rule['obj']->reduction_tax) {
                            $remaining_amount = $values['tax_incl'] - $order->total_products_wt;
                        } else {
                            $remaining_amount = $values['tax_excl'] - $order->total_products;
                        }
                        if ($remaining_amount > 0 && $cart_rule['obj']->partial_use == 1 && $reduction_amount_converted > 0) {
                            // Create a new voucher from the original
                            $voucher = new CartRule((int)$cart_rule['obj']->id); // We need to instantiate the CartRule without lang parameter to allow saving it
                            unset($voucher->id);

                            // Set a new voucher code
                            $voucher->code = empty($voucher->code) ? substr(md5($order->id.'-'.$order->id_customer.'-'.$cart_rule['obj']->id), 0, 16) : $voucher->code.'-2';
                            if (preg_match('/\-([0-9]{1,2})\-([0-9]{1,2})$/', $voucher->code, $matches) && $matches[1] == $matches[2]) {
                                $voucher->code = preg_replace('/'.$matches[0].'$/', '-'.(intval($matches[1]) + 1), $voucher->code);
                            }

                            // Set the new voucher value
                            $voucher->reduction_amount = $remaining_amount;
                            if ($voucher->reduction_tax) {
                                // Add total shipping amout only if reduction amount > total shipping
                                if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_incl) {
                                    $voucher->reduction_amount -= $order->total_shipping_tax_incl;
                                }
                            } else {
                                // Add total shipping amout only if reduction amount > total shipping
                                if ($voucher->free_shipping == 1 && $voucher->reduction_amount >= $order->total_shipping_tax_excl) {
                                    $voucher->reduction_amount -= $order->total_shipping_tax_excl;
                                }
                            }
                            if ($voucher->reduction_amount <= 0) {
                                continue;
                            }

                            if ($this->context->customer->isGuest()) {
                                $voucher->id_customer = 0;
                            } else {
                                $voucher->id_customer = $order->id_customer;
                            }

                            $voucher->quantity = 1;
                            $voucher->reduction_currency = $order->id_currency;
                            $voucher->quantity_per_user = 1;
                            $voucher->free_shipping = 0;
                            if ($voucher->add()) {
                                // If the voucher has conditions, they are now copied to the new voucher
                                CartRule::copyConditions($cart_rule['obj']->id, $voucher->id);

                                $params = array(
                                    '{voucher_amount}' => Tools::displayPrice($voucher->reduction_amount, $this->context->currency, false),
                                    '{voucher_num}' => $voucher->code,
                                    '{firstname}' => $this->context->customer->firstname,
                                    '{lastname}' => $this->context->customer->lastname,
                                    '{id_order}' => $order->reference,
                                    '{order_name}' => $order->getUniqReference()
                                );
                                Mail::Send(
                                    (int)$order->id_lang,
                                    'voucher',
                                    sprintf(Mail::l('New voucher for your order %s', (int)$order->id_lang), $order->reference),
                                    $params,
                                    $this->context->customer->email,
                                    $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                    null, null, null, null, _PS_MAIL_DIR_, false, (int)$order->id_shop
                                );
                            }
                        }

                        $order->addCartRule($cart_rule['obj']->id, $cart_rule['obj']->name, $values, 0, $cart_rule['obj']->free_shipping);

                        if ($id_order_state != Configuration::get('PS_OS_ERROR') && $id_order_state != Configuration::get('PS_OS_CANCELED') && !in_array($cart_rule['obj']->id, $cart_rule_used)) {
                            $cart_rule_used[] = $cart_rule['obj']->id;

                            // Create a new instance of Cart Rule without id_lang, in order to update its quantity
                            $cart_rule_to_update = new CartRule((int)$cart_rule['obj']->id);
                            $cart_rule_to_update->quantity = max(0, $cart_rule_to_update->quantity - 1);
                            $cart_rule_to_update->update();
                        }

                        $cart_rules_list[] = array(
                            'voucher_name' => $cart_rule['obj']->name,
                            'voucher_reduction' => ($values['tax_incl'] != 0.00 ? '-' : '').Tools::displayPrice($values['tax_incl'], $this->context->currency, false)
                        );
                    }

                    $cart_rules_list_txt = '';
                    $cart_rules_list_html = '';
                    if (count($cart_rules_list) > 0) {
                        $cart_rules_list_txt = $this->getEmailTemplateContent('order_conf_cart_rules.txt', Mail::TYPE_TEXT, $cart_rules_list);
                        $cart_rules_list_html = $this->getEmailTemplateContent('order_conf_cart_rules.tpl', Mail::TYPE_HTML, $cart_rules_list);
                    }

                    // Specify order id for message
                    $old_message = Message::getMessageByCartId((int)$this->context->cart->id);
                    if ($old_message && !$old_message['private']) {
                        $update_message = new Message((int)$old_message['id_message']);
                        $update_message->id_order = (int)$order->id;
                        $update_message->update();

                        // Add this message in the customer thread
                        $customer_thread = new CustomerThread();
                        $customer_thread->id_contact = 0;
                        $customer_thread->id_customer = (int)$order->id_customer;
                        $customer_thread->id_shop = (int)$this->context->shop->id;
                        $customer_thread->id_order = (int)$order->id;
                        $customer_thread->id_lang = (int)$this->context->language->id;
                        $customer_thread->email = $this->context->customer->email;
                        $customer_thread->status = 'open';
                        $customer_thread->token = Tools::passwdGen(12);
                        $customer_thread->add();

                        $customer_message = new CustomerMessage();
                        $customer_message->id_customer_thread = $customer_thread->id;
                        $customer_message->id_employee = 0;
                        $customer_message->message = $update_message->message;
                        $customer_message->private = 0;

                        if (!$customer_message->add()) {
                            $this->errors[] = Tools::displayError('An error occurred while saving message');
                        }
                    }

                    // update order in htl tables
                    $objRoomType = new HotelRoomType();

                    $objAdvancedPayment = new HotelAdvancedPayment();

                    $vatAddress = new Address((int)$order->id_address_tax);

                    $idLang = (int)$this->context->cart->id_lang;
                    $normalProducts = array();
                    foreach ($order->product_list as $product) {
                        $objCartBookingData = new HotelCartBookingData();
                        $idProduct = $product['id_product'];
                        $cartBookingData = $objCartBookingData->getOnlyCartBookingData($this->context->cart->id, $this->context->cart->id_guest, $idProduct);
                        if ($cartBookingData) {
                            foreach ($cartBookingData as $bookingInfo) {
                                $objCartBookingData = new HotelCartBookingData($bookingInfo['id']);
                                $objCartBookingData->id_order = $order->id;
                                $objCartBookingData->id_customer = $this->context->customer->id;
                                $objCartBookingData->save();

                                $objBookingDetail = new HotelBookingDetail();
                                $id_order_detail = $objBookingDetail->getPsOrderDetailIdByIdProduct($idProduct, $order->id);
                                $objBookingDetail->id_product = $idProduct;
                                $objBookingDetail->id_order = $order->id;
                                $objBookingDetail->id_order_detail = $id_order_detail;
                                $objBookingDetail->id_cart = $this->context->cart->id;
                                $objBookingDetail->id_room = $objCartBookingData->id_room;
                                $objBookingDetail->id_hotel = $objCartBookingData->id_hotel;
                                $objBookingDetail->id_customer = $this->context->customer->id;
                                $objBookingDetail->booking_type = $objCartBookingData->booking_type;
                                $objBookingDetail->id_status = 1;
                                $objBookingDetail->comment = $objCartBookingData->comment;

                                // For Back Order(Because of cart lock)
                                if ($objCartBookingData->is_back_order) {
                                    $objBookingDetail->is_back_order = 1;
                                }
                                $total_price = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                                    $idProduct,
                                    $objCartBookingData->date_from,
                                    $objCartBookingData->date_to,
                                    0,
                                    Group::getCurrent()->id,
                                    $objCartBookingData->id_cart,
                                    $objCartBookingData->id_guest,
                                    $objCartBookingData->id_room,
                                    0
                                );
                                $objBookingDetail->date_from = $objCartBookingData->date_from;
                                $objBookingDetail->date_to = $objCartBookingData->date_to;
                                $objBookingDetail->total_price_tax_excl = Tools::ps_round($total_price['total_price_tax_excl'], 6);
                                $objBookingDetail->total_price_tax_incl = Tools::ps_round($total_price['total_price_tax_incl'], 6);
                                $objBookingDetail->adults = $objCartBookingData->adults;
                                $objBookingDetail->children = $objCartBookingData->children;
                                $objBookingDetail->child_ages = $objCartBookingData->child_ages;

                                // Save hotel information/location/contact
                                if (Validate::isLoadedObject($objRoom = new HotelRoomInformation($objCartBookingData->id_room))) {
                                    $objBookingDetail->room_num = $objRoom->room_num;
                                }
                                if (Validate::isLoadedObject($objHotelBranch = new HotelBranchInformation(
                                    $objCartBookingData->id_hotel,
                                    $idLang
                                ))) {
                                    $objBookingDetail->hotel_name = $objHotelBranch->hotel_name;
                                    $objBookingDetail->room_type_name = $product['name'];
                                    $objBookingDetail->email = $objHotelBranch->email;
                                    $objBookingDetail->check_in_time = $objHotelBranch->check_in;
                                    $objBookingDetail->check_out_time = $objHotelBranch->check_out;
                                    if ($hotelAddress = HotelBranchInformation::getAddress($objCartBookingData->id_hotel)) {
                                        $objBookingDetail->city = $hotelAddress['city'];
                                        $objBookingDetail->state = $hotelAddress['state'];
                                        $objBookingDetail->country = $hotelAddress['country'];
                                        $objBookingDetail->zipcode = $hotelAddress['postcode'];
                                        $objBookingDetail->phone = $hotelAddress['phone'];
                                    }
                                }

                                /*for saving details of the advance payment product wise*/
                                $objBookingDetail->total_paid_amount = Tools::ps_round($total_price['total_price_tax_incl'], 5);
                                if ($this->context->cart->is_advance_payment) {
                                    $prod_adv_payment = $objAdvancedPayment->getIdAdvPaymentByIdProduct($idProduct);
                                    if (!$prod_adv_payment
                                        || (isset($prod_adv_payment['payment_type']) && $prod_adv_payment['payment_type'])
                                    ) {
                                        $objBookingDetail->total_paid_amount = $objAdvancedPayment->getRoomMinAdvPaymentAmount(
                                            $idProduct,
                                            $objCartBookingData->date_from,
                                            $objCartBookingData->date_to
                                        );
                                    }
                                }
                                if ($objBookingDetail->save()) {
                                    // save extra demands info
                                    if ($objCartBookingData->extra_demands
                                        && ($extraDemands = json_decode($objCartBookingData->extra_demands, true))
                                    ) {
                                        $objRoomDemandPrice = new HotelRoomTypeDemandPrice();
                                        foreach ($extraDemands as $demand) {
                                            $idGlobalDemand = $demand['id_global_demand'];
                                            $idOption = $demand['id_option'];
                                            $objBookingDemand = new HotelBookingDemands();
                                            $objBookingDemand->id_htl_booking = $objBookingDetail->id;
                                            $objGlobalDemand = new HotelRoomTypeGlobalDemand($idGlobalDemand, $idLang);
                                            if ($idOption) {
                                                $objOption = new HotelRoomTypeGlobalDemandAdvanceOption($idOption, $idLang);
                                                $objBookingDemand->name = $objOption->name;
                                            } else {
                                                $idOption = 0;
                                                $objBookingDemand->name = $objGlobalDemand->name;
                                            }
                                            $objBookingDemand->unit_price_tax_excl = HotelRoomTypeDemand::getPriceStatic(
                                                $idProduct,
                                                $idGlobalDemand,
                                                $idOption,
                                                0
                                            );
                                            $objBookingDemand->unit_price_tax_incl = HotelRoomTypeDemand::getPriceStatic(
                                                $idProduct,
                                                $idGlobalDemand,
                                                $idOption,
                                                1
                                            );
                                            $qty = 1;
                                            if ($objGlobalDemand->price_calc_method == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY) {
                                                $numDays = $objBookingDetail->getNumberOfDays(
                                                    $objBookingDetail->date_from,
                                                    $objBookingDetail->date_to
                                                );
                                                if ($numDays > 1) {
                                                    $qty *= $numDays;
                                                }
                                            }
                                            $objBookingDemand->total_price_tax_excl = $objBookingDemand->unit_price_tax_excl * $qty;
                                            $objBookingDemand->total_price_tax_incl = $objBookingDemand->unit_price_tax_incl * $qty;

                                            $objBookingDemand->price_calc_method = $objGlobalDemand->price_calc_method;
                                            $objBookingDemand->id_tax_rules_group = $objGlobalDemand->id_tax_rules_group;
                                            $taxManager = TaxManagerFactory::getManager(
                                                $vatAddress,
                                                $objGlobalDemand->id_tax_rules_group
                                            );
                                            $taxCalc = $taxManager->getTaxCalculator();
                                            $objBookingDemand->tax_computation_method = (int)$taxCalc->computation_method;
                                            if ($objBookingDemand->save()) {
                                                $objBookingDemand->tax_calculator = $taxCalc;
                                                $objBookingDemand->id_global_demand = $idGlobalDemand;
                                                // Now save tax details of the extra demand
                                                $objBookingDemand->setBookingDemandTaxDetails();
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $normalProducts[] = $product;
                        }
                    }
                    if (!empty($normalProducts)) {
                        $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
                        foreach($normalProducts as $product) {
                            $idProduct = $product['id_product'];
                            if ($cartAdditialServices = $objRoomTypeServiceProductCartDetail->getServiceProductsInCart(
                                $this->context->cart->id,
                                $product['id_product'],
                                isset($product['id_hotel']) ? $product['id_hotel'] : 0,
                                0,
                                0,
                                0,
                                0,
                                0,
                                null,
                                null
                            )) {
                                $idOrderDetail = $objBookingDetail->getPsOrderDetailIdByIdProduct($idProduct, $order->id);
                                foreach ($cartAdditialServices as $additionalService) {
                                    $roomBookingDetail = $objBookingDetail->getRowByIdOrderIdProductInDateRange(
                                        $order->id,
                                        $additionalService['room_type_id_product'],
                                        $additionalService['date_from'],
                                        $additionalService['date_to'],
                                        $additionalService['id_room']
                                    );
                                    $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
                                    $objRoomTypeServiceProductOrderDetail->id_product = $idProduct;
                                    $objRoomTypeServiceProductOrderDetail->id_order = $order->id;
                                    $objRoomTypeServiceProductOrderDetail->id_order_detail = $idOrderDetail;
                                    $objRoomTypeServiceProductOrderDetail->id_cart = $this->context->cart->id;
                                    $objRoomTypeServiceProductOrderDetail->id_htl_booking_detail = $roomBookingDetail['id'];
                                    $objRoomTypeServiceProductOrderDetail->unit_price_tax_excl = $additionalService['unit_price_tax_excl'];
                                    $objRoomTypeServiceProductOrderDetail->unit_price_tax_incl = $additionalService['unit_price_tax_incl'];
                                    $objRoomTypeServiceProductOrderDetail->total_price_tax_excl = $additionalService['total_price_tax_excl'];
                                    $objRoomTypeServiceProductOrderDetail->total_price_tax_incl = $additionalService['total_price_tax_incl'];
                                    $objRoomTypeServiceProductOrderDetail->name = $product['name'];
                                    $objRoomTypeServiceProductOrderDetail->quantity = $additionalService['quantity'];
                                    $objRoomTypeServiceProductOrderDetail->auto_added = $additionalService['auto_add_to_cart'];
                                    $objRoomTypeServiceProductOrderDetail->save();
                                }
                            }
                        }
                    }

                    // delete cart feature prices after booking creation success
                    HotelRoomTypeFeaturePricing::deleteByIdCart($id_cart);

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - Hook validateOrder is about to be called', 1, null, 'Cart', (int)$id_cart, true);
                    }

                    // Hook validate order
                    Hook::exec('actionValidateOrder', array(
                        'cart' => $this->context->cart,
                        'order' => $order,
                        'customer' => $this->context->customer,
                        'currency' => $this->context->currency,
                        'orderStatus' => $order_status
                    ));

                    foreach ($this->context->cart->getProducts() as $product) {
                        if ($order_status->logable) {
                            ProductSale::addProductSale((int)$product['id_product'], (int)$product['cart_quantity']);
                        }
                    }

                    if (self::DEBUG_MODE) {
                        PrestaShopLogger::addLog('PaymentModule::validateOrder - Order Status is about to be added', 1, null, 'Cart', (int)$id_cart, true);
                    }

                    // Set the order status
                    $new_history = new OrderHistory();
                    $new_history->id_order = (int)$order->id;
                    if ($order_status->logable && $order->is_advance_payment && $order->advance_paid_amount < $order->total_paid_tax_incl) {
                        $new_history->changeIdOrderState((int)Configuration::get('PS_OS_PARTIAL_PAYMENT'), $order, true);
                    } else {
                        $new_history->changeIdOrderState((int)$id_order_state, $order, true);
                    }
                    $new_history->addWithemail(true, $extra_vars);

                    // Switch to back order if needed
                    if (Configuration::get('PS_STOCK_MANAGEMENT') && ($order_detail->getStockState() || $order_detail->product_quantity_in_stock <= 0)) {
                        $history = new OrderHistory();
                        $history->id_order = (int)$order->id;
                        $history->changeIdOrderState(Configuration::get($order->valid ? 'PS_OS_OUTOFSTOCK_PAID' : 'PS_OS_OUTOFSTOCK_UNPAID'), $order, true);
                        $history->addWithemail();
                    }

                    unset($order_detail);

                    // Order is reloaded because the status just changed
                    $order = new Order((int)$order->id);

                    // Send an e-mail to customer (one order = one email)
                    if ($id_order_state != Configuration::get('PS_OS_ERROR') && $id_order_state != Configuration::get('PS_OS_CANCELED') && $this->context->customer->id) {
                        $invoice = new Address($order->id_address_invoice);
                        $delivery = new Address($order->id_address_delivery);
                        $delivery_state = $delivery->id_state ? new State($delivery->id_state) : false;
                        $invoice_state = $invoice->id_state ? new State($invoice->id_state) : false;

                        // changing mail format
                        $cart_booking_data = $this->cartBookingDataForMail($order);
                        $cart_booking_data_text = $this->getEmailTemplateContent('hotel-booking-cart-data-text.tpl', Mail::TYPE_TEXT, $cart_booking_data['cart_htl_data']);
                        $cart_booking_data_html = $this->getEmailTemplateContent('hotel-booking-cart-data.tpl', Mail::TYPE_HTML, $cart_booking_data['cart_htl_data']);

                        $extra_demands_details_html = $this->getEmailTemplateContent('booking_extra_demands.tpl', Mail::TYPE_HTML, $cart_booking_data['cart_htl_data']);
                        $extra_demands_details_text = $this->getEmailTemplateContent('booking_extra_demands_text.tpl', Mail::TYPE_HTML, $cart_booking_data['cart_htl_data']);

                        $normal_products_data_html = $this->getEmailTemplateContent('hotel-service-product-data.tpl', Mail::TYPE_TEXT, $orderServiceProducts);
                        $normal_products_data_txt = $this->getEmailTemplateContent('hotel-service-product-data-text.tpl', Mail::TYPE_TEXT, $orderServiceProducts);

                        // total room price
                        $room_price_tax_excl = $order->getTotalProductsWithoutTaxes(false, true);
                        $room_price_tax_incl = $order->getTotalProductsWithTaxes(false, true);
                        $room_tax = ($order->getTotalProductsWithTaxes(false, true) - $order->getTotalProductsWithoutTaxes(false, true));

                        // extra services
                        $additional_service_price_tax_excl = ($order->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $cart_booking_data['total_extra_demands_te']);
                        $additional_service_price_tax_incl = ($order->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $cart_booking_data['total_extra_demands_ti']);
                        $additional_service_tax = (($order->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $cart_booking_data['total_extra_demands_ti']) - ($order->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $cart_booking_data['total_extra_demands_te']));

                        // convenience fee price
                        $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
                        $total_convenience_fee_ti = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                            $order->id,
                            0,
                            0,
                            0,
                            0,
                            0,
                            0,
                            1,
                            1,
                            1,
                            Product::PRICE_ADDITION_TYPE_INDEPENDENT
                        );
                        $total_convenience_fee_te = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                            $order->id,
                            0,
                            0,
                            0,
                            0,
                            0,
                            0,
                            1,
                            0,
                            1,
                            Product::PRICE_ADDITION_TYPE_INDEPENDENT
                        );
                        $additional_service_price_tax_excl = $additional_service_price_tax_excl - $total_convenience_fee_te;
                        $additional_service_price_tax_incl = $additional_service_price_tax_incl - $total_convenience_fee_ti;
                        $room_price_tax_excl = $room_price_tax_excl + $additional_service_price_tax_excl;
                        $room_price_tax_incl = $room_price_tax_incl + $additional_service_price_tax_incl;

                        // service products
                        // $service_products_price_tax_excl = $order->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE);
                        // $service_products_price_tax_incl = $order->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE);
                        // $service_products_tax = ($order->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE) - $order->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE));

                        $data = array(
                            '{cart_booking_data_html}' => $cart_booking_data_html,
                            '{cart_booking_data_text}' => $cart_booking_data_text,
                            '{extra_demands_details_html}' => $extra_demands_details_html,
                            '{extra_demands_details_text}' => $extra_demands_details_text,
                            '{normal_products_data_html}' => $normal_products_data_html,
                            '{normal_products_data_txt}' => $normal_products_data_txt,
                            '{total_extra_demands_te}' => Tools::displayPrice(
                                $cart_booking_data['total_extra_demands_te'],
                                $this->context->currency,
                                false
                            ),
                            '{extra_demands_tax}' => Tools::displayPrice(
                                ($cart_booking_data['total_extra_demands_ti']-$cart_booking_data['total_extra_demands_te']),
                                $this->context->currency,
                                false
                            ),
                            '{delivery_block_txt}' => $this->_getFormatedAddress($delivery, "\n"),
                            '{invoice_block_txt}' => $this->_getFormatedAddress($invoice, "\n"),
                            '{delivery_block_html}' => $this->_getFormatedAddress($delivery, '<br />', array(
                                'firstname'    => '<span style="font-weight:bold;">%s</span>',
                                'lastname'    => '<span style="font-weight:bold;">%s</span>'
                            )),
                            '{invoice_block_html}' => $this->_getFormatedAddress($invoice, '<br />', array(
                                    'firstname'    => '<span style="font-weight:bold;">%s</span>',
                                    'lastname'    => '<span style="font-weight:bold;">%s</span>'
                            )),
                            '{delivery_company}' => $delivery->company,
                            '{delivery_firstname}' => $delivery->firstname,
                            '{delivery_lastname}' => $delivery->lastname,
                            '{delivery_address1}' => $delivery->address1,
                            '{delivery_address2}' => $delivery->address2,
                            '{delivery_city}' => $delivery->city,
                            '{delivery_postal_code}' => $delivery->postcode,
                            '{delivery_country}' => $delivery->country,
                            '{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
                            '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
                            '{delivery_other}' => $delivery->other,
                            '{invoice_company}' => $invoice->company,
                            '{invoice_vat_number}' => $invoice->vat_number,
                            '{invoice_firstname}' => $invoice->firstname,
                            '{invoice_lastname}' => $invoice->lastname,
                            '{invoice_address2}' => $invoice->address2,
                            '{invoice_address1}' => $invoice->address1,
                            '{invoice_city}' => $invoice->city,
                            '{invoice_postal_code}' => $invoice->postcode,
                            '{invoice_country}' => $invoice->country,
                            '{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
                            '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
                            '{invoice_other}' => $invoice->other,
                            '{order_name}' => $order->getUniqReference(),
                            '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
                            '{carrier}' => ($virtual_product || !isset($carrier->name)) ? Tools::displayError('No carrier') : $carrier->name,
                            '{payment}' => Tools::substr($order->payment, 0, 32),
                            '{products}' => $product_list_html,
                            '{products_txt}' => $product_list_txt,
                            '{discounts}' => $cart_rules_list_html,
                            '{discounts_txt}' => $cart_rules_list_txt,
                            '{total_paid}' => Tools::displayPrice($order->total_paid, $this->context->currency, false),
                            '{total_products}' => Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? $order->total_products : $order->total_products_wt, $this->context->currency, false),
                            '{total_discounts}' => Tools::displayPrice($order->total_discounts, $this->context->currency, false),
                            '{total_shipping}' => Tools::displayPrice($order->total_shipping, $this->context->currency, false),
                            '{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $this->context->currency, false),
                            // '{total_tax_paid}' => Tools::displayPrice(($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl), $this->context->currency, false),
                            '{total_tax_paid}' => Tools::displayPrice(($order->total_paid_tax_incl - $order->total_paid_tax_excl), $this->context->currency, false),
                            // additional data
                            '{room_price_tax_excl}' => Tools::displayPrice($room_price_tax_excl, $this->context->currency, false),
                            '{room_price_tax_incl}' => Tools::displayPrice($room_price_tax_incl, $this->context->currency, false),
                            '{room_tax}' => Tools::displayPrice($room_tax, $this->context->currency, false),
                            // '{service_products_price_tax_excl}' => Tools::displayPrice($service_products_price_tax_excl, $this->context->currency, false),
                            // '{service_products_price_tax_incl}' => Tools::displayPrice($service_products_price_tax_incl, $this->context->currency, false),
                            // '{service_products_tax}' => Tools::displayPrice($service_products_tax, $this->context->currency, false),
                            '{additional_service_price_tax_excl}' => Tools::displayPrice($additional_service_price_tax_excl, $this->context->currency, false),
                            '{additional_service_price_tax_incl}' => Tools::displayPrice($additional_service_price_tax_incl, $this->context->currency, false),
                            '{additional_service_tax}' => Tools::displayPrice($additional_service_tax, $this->context->currency, false),
                            '{total_convenience_fee_ti}' => Tools::displayPrice($total_convenience_fee_ti, $this->context->currency, false),
                            '{total_convenience_fee_te}' => Tools::displayPrice($total_convenience_fee_te, $this->context->currency, false),
                        );

                        if (is_array($extra_vars)) {
                            $data = array_merge($data, $extra_vars);
                        }

                        // Join PDF invoice
                        if ((int)Configuration::get('PS_INVOICE') && $order_status->invoice && $order->invoice_number) {
                            $order_invoice_list = $order->getInvoicesCollection();
                            Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));
                            $pdf = new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, $this->context->smarty);
                            $file_attachement['content'] = $pdf->render(false);
                            $file_attachement['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)$order->id_lang, null, $order->id_shop).sprintf('%06d', $order->invoice_number).'.pdf';
                            $file_attachement['mime'] = 'application/pdf';
                        } else {
                            $file_attachement = null;
                        }

                        if (self::DEBUG_MODE) {
                            PrestaShopLogger::addLog('PaymentModule::validateOrder - Mail is about to be sent', 1, null, 'Cart', (int)$id_cart, true);
                        }

                        // Send order confirmation mails to the reciepients according to the order mail configuration
                        if (Configuration::get('PS_ORDER_CONF_MAIL_TO_CUSTOMER')){
                            if (Validate::isEmail($this->context->customer->email)) {
                                // send customer information
                                $data['{firstname}'] = $this->context->customer->firstname;
                                $data['{lastname}'] = $this->context->customer->lastname;
                                $data['{email}'] = $this->context->customer->email;
                                Mail::Send(
                                    (int)$order->id_lang,
                                    'order_conf',
                                    Mail::l('Order confirmation', (int)$order->id_lang),
                                    $data,
                                    $this->context->customer->email,
                                    $this->context->customer->firstname.' '.$this->context->customer->lastname,
                                    null,
                                    null,
                                    $file_attachement,
                                    null, _PS_MAIL_DIR_, false, (int)$order->id_shop
                                );
                            }
                            // send mail to customer guest if customer booked for someone other.
                            if ($id_customer_guest_detail = OrderCustomerGuestDetail::isCustomerGuestBooking($order->id)) {
                                if ($objOrderCustomerGuestDetail = new OrderCustomerGuestDetail(
                                    $id_customer_guest_detail
                                )) {
                                    if (Validate::isEmail($objOrderCustomerGuestDetail->email)) {
                                        $data['{firstname}'] = $objOrderCustomerGuestDetail->firstname;
                                        $data['{lastname}'] = $objOrderCustomerGuestDetail->lastname;
                                        $data['{email}'] = $objOrderCustomerGuestDetail->email;
                                        Mail::Send(
                                            (int)$order->id_lang,
                                            'order_conf',
                                            Mail::l('Order confirmation', (int)$order->id_lang),
                                            $data,
                                            $objOrderCustomerGuestDetail->email,
                                            $objOrderCustomerGuestDetail->firstname.' '.$objOrderCustomerGuestDetail->lastname,
                                            null,
                                            null,
                                            $file_attachement,
                                            null, _PS_MAIL_DIR_, false, (int)$order->id_shop
                                        );
                                    }
                                }
                            }
                        }
                        if (Configuration::get('PS_ORDER_CONF_MAIL_TO_SUPERADMIN')){
                            // send superadmin information
                            if (Validate::isLoadedObject($superAdmin = new Employee(_PS_ADMIN_PROFILE_))) {
                                if (Validate::isEmail($superAdmin->email)) {
                                    $data['{customer_name}'] = $this->context->customer->firstname.' '.$this->context->customer->lastname;
                                    $data['{customer_email}'] = $this->context->customer->email;
                                    $data['{firstname}'] = $superAdmin->firstname;
                                    $data['{lastname}'] = $superAdmin->lastname;
                                    $data['{email}'] = $superAdmin->email;
                                    Mail::Send(
                                        (int)$order->id_lang,
                                        'order_conf_admin',
                                        Mail::l('Order confirmation', (int)$order->id_lang),
                                        $data,
                                        $superAdmin->email,
                                        $superAdmin->firstname.' '.$superAdmin->lastname,
                                        null,
                                        null,
                                        $file_attachement,
                                        null, _PS_MAIL_DIR_, false, (int)$order->id_shop
                                    );
                                }
                            }
                        }
                        if ($idHotel
                            && Validate::isLoadedObject($objHotel = new HotelBranchInformation($idHotel))
                        ) {
                            if (Configuration::get('PS_ORDER_CONF_MAIL_TO_HOTEL_MANAGER')){
                                // send hotel information
                                $data['{firstname}'] = '';
                                $data['{lastname}'] = '';
                                $data['{email}'] = $objHotel->email;
                                $data['{customer_name}'] = $this->context->customer->firstname.' '.$this->context->customer->lastname;
                                $data['{customer_email}'] = $this->context->customer->email;
                                if (Validate::isEmail($objHotel->email)) {
                                    Mail::Send(
                                        (int)$order->id_lang,
                                        'order_conf_admin',
                                        Mail::l('Order confirmation', (int)$order->id_lang),
                                        $data,
                                        $objHotel->email,
                                        null,
                                        null,
                                        null,
                                        $file_attachement,
                                        null, _PS_MAIL_DIR_, false, (int)$order->id_shop
                                    );
                                }
                            }
                            if (Configuration::get('PS_ORDER_CONF_MAIL_TO_EMPLOYEE')){
                                if ($htlAccesses = $objHotel->getHotelAccess($idHotel)) {
                                    $data['{customer_name}'] = $this->context->customer->firstname.' '.$this->context->customer->lastname;
                                    $data['{customer_email}'] = $this->context->customer->email;
                                    foreach ($htlAccesses as $access) {
                                        if ($access['id_profile'] != _PS_ADMIN_PROFILE_) {
                                            if ($htlEmployees = Employee::getEmployeesByProfile($access['id_profile'])) {
                                                foreach ($htlEmployees as $empl) {
                                                    if (Validate::isEmail($empl['email'])) {
                                                        // send hotel manager (employee) have permission for this hotel
                                                        $data['{firstname}'] = $empl['firstname'];
                                                        $data['{lastname}'] = $empl['lastname'];
                                                        $data['{email}'] = $empl['email'];
                                                        Mail::Send(
                                                            (int)$order->id_lang,
                                                            'order_conf_admin',
                                                            Mail::l('Order confirmation', (int)$order->id_lang),
                                                            $data,
                                                            $empl['email'],
                                                            $empl['firstname'].' '.$empl['lastname'],
                                                            null,
                                                            null,
                                                            $file_attachement,
                                                            null, _PS_MAIL_DIR_, false, (int)$order->id_shop
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // updates stock in shops
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                        $product_list = $order->getProducts();
                        foreach ($product_list as $product) {
                            // if the available quantities depends on the physical stock
                            if (StockAvailable::dependsOnStock($product['product_id'])) {
                                // synchronizes
                                StockAvailable::synchronize($product['product_id'], $order->id_shop);
                            }
                        }
                    }

                    $order->updateOrderDetailTax();
                } else {
                    $error = Tools::displayError('Order creation failed');
                    PrestaShopLogger::addLog($error, 4, '0000002', 'Cart', intval($order->id_cart));
                    die($error);
                }
            } // End foreach $order_detail_list

            // Use the last order as currentOrder
            if (isset($order) && $order->id) {
                $this->currentOrder = (int)$order->id;
            }

            if (self::DEBUG_MODE) {
                PrestaShopLogger::addLog('PaymentModule::validateOrder - End of validateOrder', 1, null, 'Cart', (int)$id_cart, true);
            }

            return true;
        } else {
            $error = Tools::displayError('Cart cannot be loaded or a booking has already been created using this cart.');
            PrestaShopLogger::addLog($error, 4, '0000001', 'Cart', intval($this->context->cart->id));
            die($error);
        }
    }

    /**
     * @deprecated 1.6.0.7
     * @param mixed $content
     * @return mixed
     */
    public function formatProductAndVoucherForEmail($content)
    {
        Tools::displayAsDeprecated();
        return $content;
    }

    /**
     * @param Object Address $the_address that needs to be txt formated
     * @return String the txt formated address block
     */
    protected function _getTxtFormatedAddress($the_address)
    {
        $adr_fields = AddressFormat::getOrderedAddressFields($the_address->id_country, false, true);
        $r_values = array();
        foreach ($adr_fields as $fields_line) {
            $tmp_values = array();
            foreach (explode(' ', $fields_line) as $field_item) {
                $field_item = trim($field_item);
                $tmp_values[] = $the_address->{$field_item};
            }
            $r_values[] = implode(' ', $tmp_values);
        }

        $out = implode("\n", $r_values);
        return $out;
    }

    /**
     * @param Object Address $the_address that needs to be txt formated
     * @return String the txt formated address block
     */

    protected function _getFormatedAddress(Address $the_address, $line_sep, $fields_style = array())
    {
        return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
    }

    /**
     * @param int $id_currency : this parameter is optionnal but on 1.5 version of Prestashop, it will be REQUIRED
     * @return Currency
     */
    public function getCurrency($current_id_currency = null)
    {
        if (!(int)$current_id_currency) {
            $current_id_currency = Context::getContext()->currency->id;
        }

        if (!$this->currencies) {
            return false;
        }
        if ($this->currencies_mode == 'checkbox') {
            $currencies = Currency::getPaymentCurrencies($this->id);
            return $currencies;
        } elseif ($this->currencies_mode == 'radio') {
            $currencies = Currency::getPaymentCurrenciesSpecial($this->id);
            $currency = $currencies['id_currency'];
            if ($currency == -1) {
                $id_currency = (int)$current_id_currency;
            } elseif ($currency == -2) {
                $id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
            } else {
                $id_currency = $currency;
            }
        }
        if (!isset($id_currency) || empty($id_currency)) {
            return false;
        }
        $currency = new Currency((int)$id_currency);
        return $currency;
    }

    /**
     * Allows specified payment modules to be used by a specific currency
     *
     * @since 1.4.5
     * @param int $id_currency
     * @param array $id_module_list
     * @return bool
     */
    public static function addCurrencyPermissions($id_currency, array $id_module_list = array())
    {
        $values = '';
        if (count($id_module_list) == 0) {
            // fetch all installed module ids
            $modules = PaymentModuleCore::getInstalledPaymentModules();
            foreach ($modules as $module) {
                $id_module_list[] = $module['id_module'];
            }
        }

        foreach ($id_module_list as $id_module) {
            $values .= '('.(int)$id_module.','.(int)$id_currency.'),';
        }

        if (!empty($values)) {
            return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_currency` (`id_module`, `id_currency`)
			VALUES '.rtrim($values, ','));
        }

        return true;
    }

    /**
     * List all installed and active payment modules
     * @see Module::getPaymentModules() if you need a list of module related to the user context
     *
     * @since 1.4.5
     * @return array module informations
     */
    public static function getInstalledPaymentModules()
    {
        $hook_payment = 'Payment';
        if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\'')) {
            $hook_payment = 'displayPayment';
        }

        return Db::getInstance()->executeS('
		SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
		FROM `'._DB_PREFIX_.'module` m
		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`'
        .Shop::addSqlRestriction(false, 'hm').'
		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
		INNER JOIN `'._DB_PREFIX_.'module_shop` ms ON (m.`id_module` = ms.`id_module` AND ms.id_shop='.(int)Context::getContext()->shop->id.')
		WHERE h.`name` = \''.pSQL($hook_payment).'\'');
    }

    public static function preCall($module_name)
    {
        if (!parent::preCall($module_name)) {
            return false;
        }

        if (($module_instance = Module::getInstanceByName($module_name))) {
            /** @var PaymentModule $module_instance */
            if (!$module_instance->currencies || ($module_instance->currencies && count(Currency::checkPaymentCurrencies($module_instance->id)))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fetch the content of $template_name inside the folder
     * current_theme/mails/current_iso_lang/ if found, otherwise in
     * current_theme/mails/en/ if found, otherwise in
     * mails/current_iso_lang/ if found, otherwise in
     * mails/en/
     *
     * @param string  $template_name template name with extension
     * @param int     $mail_type     Mail::TYPE_HTML or Mail::TYPE_TEXT
     * @param array   $var           sent to smarty as 'list'
     *
     * @return string
     */
    protected function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }

        $pathToFindEmail = array(
            _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$template_name,
            _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR.$template_name,
            _PS_MAIL_DIR_.$this->context->language->iso_code.DIRECTORY_SEPARATOR.$template_name,
            _PS_MAIL_DIR_.'en'.DIRECTORY_SEPARATOR.$template_name,
        );

        foreach ($pathToFindEmail as $path) {
            if (Tools::file_exists_cache($path)) {
                $this->context->smarty->assign('list', $var);
                return $this->context->smarty->fetch($path);
            }
        }

        return '';
    }

    public function cartBookingDataForMail($order)
    {
        $result = array();
        $customer = new Customer($order->id_customer);
        // To show order details properly on order history page
        $products = $order->getProducts();
        if (Module::isInstalled('hotelreservationsystem')) {
            require_once(_PS_MODULE_DIR_.'hotelreservationsystem/define.php');
            $obj_cart_bk_data = new HotelCartBookingData();
            $obj_htl_bk_dtl = new HotelBookingDetail();
            $obj_rm_type = new HotelRoomType();
            $objBookingDemand = new HotelBookingDemands();
            $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
            $result['total_extra_demands_te'] = 0;
            $result['total_extra_demands_ti'] = 0;
            $result['total_additional_services_te'] =0;
            $result['total_additional_services_ti'] =0;
            $cart_htl_data = array();
            if (!empty($products)) {
                foreach ($products as $type_key => $type_value) {
                    $product = new Product($type_value['product_id'], false, $this->context->language->id);
                    $cover_image_arr = $product->getCover($type_value['product_id']);

                    if (!empty($cover_image_arr)) {
                        $cover_img = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_arr['id_image'], 'small_default');
                    } else {
                        $cover_img = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code."-default", 'small_default');
                    }

                    if (isset($customer->id)) {
                        $cart_obj = new Cart($order->id_cart);
                        $cart_bk_data = $obj_htl_bk_dtl->getOnlyOrderBookingData($order->id, $cart_obj->id_guest, $type_value['product_id'], $customer->id);
                    } else {
                        $cart_bk_data = $obj_htl_bk_dtl->getOnlyOrderBookingData($order->id, $customer->id_guest, $type_value['product_id']);
                    }
                    if ($cart_bk_data) {
                        $rm_dtl = $obj_rm_type->getRoomTypeInfoByIdProduct($type_value['product_id']);

                        $cart_htl_data[$type_key]['id_product'] = $type_value['product_id'];
                        $cart_htl_data[$type_key]['cover_img']    = $cover_img;
                        $cart_htl_data[$type_key]['name']        = $product->name;
                        $cart_htl_data[$type_key]['hotel_name'] = $rm_dtl['hotel_name'];

                        foreach ($cart_bk_data as $data_k => $data_v) {
                            $date_join = strtotime($data_v['date_from']).strtotime($data_v['date_to']);

                            if (isset($cart_htl_data[$type_key]['date_diff'][$date_join])) {
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] += 1;

                                $num_days = $cart_htl_data[$type_key]['date_diff'][$date_join]['num_days'];
                                $vart_quant = (int)$cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'];

                                $cart_htl_data[$type_key]['date_diff'][$date_join]['adults'] += $data_v['adults'];
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['children'] += $data_v['children'];




                                $roomTypeDateRangePrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($type_value['id_product'], $data_v['date_from'], $data_v['date_to']);


                                $cart_htl_data[$type_key]['date_diff'][$date_join]['amount'] = $roomTypeDateRangePrice['total_price_tax_incl']*$vart_quant;
                                // extra demands prices
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to']
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_te'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    0,
                                    1,
                                    0
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    0,
                                    1,
                                    1
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
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
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
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

                                $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'] = $data_v['total_price_tax_incl']/$num_days;
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['avg_paid_unit_price_tax_incl'] += ($cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'] + $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te']);
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_incl'] += ($data_v['total_price_tax_incl'] + $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te']);
                            } else {
                                $num_days = $obj_htl_bk_dtl->getNumberOfDays($data_v['date_from'], $data_v['date_to']);

                                $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] = 1;
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['data_form'] = $data_v['date_from'];
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['data_to'] = $data_v['date_to'];
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['num_days'] = $num_days;
                                /*$amount = Product::getPriceStatic($type_value['product_id'], true, null, 6, null, false, true, 1);
                                $amount *= $num_days;*/
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['adults'] = $data_v['adults'];
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['children'] = $data_v['children'];



                                    // extra demands prices
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to']
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_te'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    0,
                                    1,
                                    0
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                    $order->id,
                                    $type_value['product_id'],
                                    0,
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    0,
                                    1,
                                    1
                                );

                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
                                    0,
                                    0,
                                    $type_value['product_id'],
                                    $data_v['date_from'],
                                    $data_v['date_to']
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_ti'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
                                    0,
                                    0,
                                    $type_value['product_id'],
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    $data_v['id_room'],
                                    1,
                                    1
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_te'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
                                    0,
                                    0,
                                    $type_value['product_id'],
                                    $data_v['date_from'],
                                    $data_v['date_to'],
                                    $data_v['id_room'],
                                    1,
                                    0
                                );
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_ti'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
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
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                                    $order->id,
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

                                $cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'] = $data_v['total_price_tax_incl']/$num_days;
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['avg_paid_unit_price_tax_incl'] = ($cart_htl_data[$type_key]['date_diff'][$date_join]['paid_unit_price_tax_incl'] + $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te']);
                                $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_incl'] = ($data_v['total_price_tax_incl'] + $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_auto_add_te']);

                                $result['total_extra_demands_te'] += $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_te'];
                                $result['total_extra_demands_ti'] += $cart_htl_data[$type_key]['date_diff'][$date_join]['extra_demands_price_ti'];
                                $result['total_additional_services_te'] += $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_te'];
                                $result['total_additional_services_ti'] += $cart_htl_data[$type_key]['date_diff'][$date_join]['additional_services_price_ti'];
                            }
                        }
                    }
                    // calculate averages now
                    foreach ($cart_htl_data[$type_key]['date_diff'] as $key => &$value) {
                        $value['avg_paid_unit_price_tax_incl'] = Tools::ps_round($value['avg_paid_unit_price_tax_incl'] / $value['num_rm'], 6);
                    }
                }
            }
            $result['cart_htl_data'] = $cart_htl_data;
        }
        return $result;
    }
}
