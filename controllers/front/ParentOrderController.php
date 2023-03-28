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
 * Class FreeOrder to use PaymentModule (abstract class, cannot be instancied)
 */
class FreeOrder extends PaymentModule
{
    public $active = 1;
    public $name = 'free_order';
    public $displayName = 'free_order';
}

class ParentOrderControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'order';

    public $nbProducts;

    /**
     * Initialize parent order controller
     * @see FrontController::init()
     */
    public function init()
    {
        $this->isLogged = $this->context->customer->id && Customer::customerIdExistsStatic((int)$this->context->cookie->id_customer);

        parent::init();

        /* Disable some cache related bugs on the cart/order */
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

        $this->nbProducts = $this->context->cart->nbProducts();

        if (!$this->context->customer->isLogged(true) && $this->useMobileTheme() && Tools::getValue('step')) {
            Tools::redirect($this->context->link->getPageLink('authentication', true, (int)$this->context->language->id));
        }

        // Redirect to the good order process
        if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0 && Dispatcher::getInstance()->getController() != 'order') {
            Tools::redirect('index.php?controller=order');
        }

        if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1 && Dispatcher::getInstance()->getController() != 'orderopc') {
            if (Tools::getIsset('step') && Tools::getValue('step') == 3) {
                Tools::redirect('index.php?controller=order-opc&isPaymentStep=true');
            }
            Tools::redirect('index.php?controller=order-opc');
        }

        if (Configuration::get('PS_CATALOG_MODE')) {
            $this->errors[] = Tools::displayError('This store has not accepted your new order.');
        }

        if (Tools::isSubmit('submitReorder') && $id_order = (int)Tools::getValue('id_order')) {
            $oldCart = new Cart(Order::getCartIdStatic($id_order, $this->context->customer->id));
            $duplication = $oldCart->duplicate();
            if (!$duplication || !Validate::isLoadedObject($duplication['cart'])) {
                $this->errors[] = Tools::displayError('Sorry. We cannot renew your order.');
            } elseif (!$duplication['success']) {
                $this->errors[] = Tools::displayError('Some items are no longer available, and we are unable to renew your order.');
            } else {
                $this->context->cookie->id_cart = $duplication['cart']->id;
                $context = $this->context;
                $context->cart = $duplication['cart'];
                CartRule::autoAddToCart($context);
                $this->context->cookie->write();
                if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
                    Tools::redirect('index.php?controller=order-opc');
                }
                Tools::redirect('index.php?controller=order');
            }
        }

        if ($this->nbProducts) {
            if (CartRule::isFeatureActive()) {
                if (Tools::isSubmit('submitAddDiscount')) {
                    if (!($code = trim(Tools::getValue('discount_name')))) {
                        $this->errors[] = Tools::displayError('You must enter a voucher code.');
                    } elseif (!Validate::isCleanHtml($code)) {
                        $this->errors[] = Tools::displayError('The voucher code is invalid.');
                    } else {
                        if (($cartRule = new CartRule(CartRule::getIdByCode($code))) && Validate::isLoadedObject($cartRule)) {
                            if ($error = $cartRule->checkValidity($this->context, false, true)) {
                                $this->errors[] = $error;
                            } else {
                                $this->context->cart->addCartRule($cartRule->id);
                                CartRule::autoAddToCart($this->context);
                                if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
                                    Tools::redirect('index.php?controller=order-opc&addingCartRule=1');
                                }
                                Tools::redirect('index.php?controller=order&addingCartRule=1');
                            }
                        } else {
                            $this->errors[] = Tools::displayError('This voucher does not exists.');
                        }
                    }
                    $this->context->smarty->assign(array(
                        'errors' => $this->errors,
                        'discount_name' => Tools::safeOutput($code)
                    ));
                } elseif (($id_cart_rule = (int)Tools::getValue('deleteDiscount')) && Validate::isUnsignedId($id_cart_rule)) {
                    $this->context->cart->removeCartRule($id_cart_rule);
                    CartRule::autoAddToCart($this->context);
                    Tools::redirect('index.php?controller=order-opc');
                }
            }
            /* Is there only virtual product in cart */
            if ($isVirtualCart = $this->context->cart->isVirtualCart()) {
                $this->setNoCarrier();
            }
        }

        $this->context->smarty->assign('back', Tools::safeOutput(Tools::getValue('back')));
    }

    public function setMedia()
    {
        parent::setMedia();

        if (!$this->useMobileTheme()) {
            // Adding CSS style sheet
            $this->addCSS(_THEME_CSS_DIR_.'addresses.css');
        }

        // Adding JS files
        $this->addJS(_THEME_JS_DIR_.'tools.js');  // retro compat themes 1.5
        if ((Configuration::get('PS_ORDER_PROCESS_TYPE') == 0 && Tools::getValue('step') == 1) || Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
            $this->addJS(_THEME_JS_DIR_.'order-address.js');
        }
        $this->addJqueryPlugin('fancybox');

        if (in_array((int)Tools::getValue('step'), array(0, 2, 3)) || Configuration::get('PS_ORDER_PROCESS_TYPE')) {
            $this->addJqueryPlugin('typewatch');
            $this->addJS(_THEME_JS_DIR_.'cart-summary.js');
        }
    }

    /**
     * Check if order is free
     * @return bool
     */
    protected function _checkFreeOrder()
    {
        // let see if any due amount for order status
        $dueAmount = 0;
        // check if customer has chosen advance payment option for this cart
        if ($this->context->cart->is_advance_payment) {
            $orderTotal = $this->context->cart->getOrderTotal(true, CART::ADVANCE_PAYMENT);
            $dueAmount = ($this->context->cart->getOrderTotal() - $orderTotal);
        } else {
            $orderTotal = $this->context->cart->getOrderTotal();
        }
        if ($orderTotal <= 0) {
            $order = new FreeOrder();
            $order->free_order_class = true;

            // order status payment accepted if no amount is pending for the booking
            if ($dueAmount > 0) {
                $orderStatus = Configuration::get('PS_OS_AWAITING_PAYMENT');
            } else {
                $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
            }

            $order->validateOrder($this->context->cart->id, $orderStatus, 0, Tools::displayError('Free order', false), null, array(), null, false, $this->context->cart->secure_key);
            return (int)Order::getOrderByCartId($this->context->cart->id);
        }
        return false;
    }

    protected function _updateMessage($messageContent)
    {
        if ($messageContent) {
            if (!Validate::isMessage($messageContent)) {
                $this->errors[] = Tools::displayError('Invalid message');
            } elseif ($oldMessage = Message::getMessageByCartId((int)$this->context->cart->id)) {
                $message = new Message((int)$oldMessage['id_message']);
                $message->message = $messageContent;
                $message->update();
            } else {
                $message = new Message();
                $message->message = $messageContent;
                $message->id_cart = (int)$this->context->cart->id;
                $message->id_customer = (int)$this->context->cart->id_customer;
                $message->add();
            }
        } else {
            if ($oldMessage = Message::getMessageByCartId($this->context->cart->id)) {
                $message = new Message($oldMessage['id_message']);
                $message->delete();
            }
        }
        return true;
    }

    protected function _processCarrier()
    {
        $this->context->cart->recyclable = (int)Tools::getValue('recyclable');
        $this->context->cart->gift = (int)Tools::getValue('gift');
        if ((int)Tools::getValue('gift')) {
            if (!Validate::isMessage(Tools::getValue('gift_message'))) {
                $this->errors[] = Tools::displayError('Invalid gift message.');
            } else {
                $this->context->cart->gift_message = strip_tags(Tools::getValue('gift_message'));
            }
        }

        if (isset($this->context->customer->id) && $this->context->customer->id) {
            $address = new Address((int)$this->context->cart->id_address_delivery);
            if (!($id_zone = Address::getZoneById($address->id))) {
                $this->errors[] = Tools::displayError('No zone matches your address.');
            }
        } else {
            $id_zone = (int)Country::getIdZone((int)Tools::getCountry());
        }

        if (Tools::getIsset('delivery_option')) {
            if ($this->validateDeliveryOption(Tools::getValue('delivery_option'))) {
                $this->context->cart->setDeliveryOption(Tools::getValue('delivery_option'));
            }
        } elseif (Tools::getIsset('id_carrier')) {
            // For retrocompatibility reason, try to transform carrier to an delivery option list
            $delivery_option_list = $this->context->cart->getDeliveryOptionList();
            if (count($delivery_option_list) == 1) {
                $delivery_option = reset($delivery_option_list);
                $key = Cart::desintifier(Tools::getValue('id_carrier'));
                foreach ($delivery_option_list as $id_address => $options) {
                    if (isset($options[$key])) {
                        $this->context->cart->id_carrier = (int)Tools::getValue('id_carrier');
                        $this->context->cart->setDeliveryOption(array($id_address => $key));
                        if (isset($this->context->cookie->id_country)) {
                            unset($this->context->cookie->id_country);
                        }
                        if (isset($this->context->cookie->id_state)) {
                            unset($this->context->cookie->id_state);
                        }
                    }
                }
            }
        }

        Hook::exec('actionCarrierProcess', array('cart' => $this->context->cart));

        if (!$this->context->cart->update()) {
            return false;
        }

        // Carrier has changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);

        return true;
    }

    /**
     * Validate get/post param delivery option
     *
     * @param array $delivery_option
     *
     * @return bool
     */
    protected function validateDeliveryOption($delivery_option)
    {
        if (!is_array($delivery_option)) {
            return false;
        }

        foreach ($delivery_option as $option) {
            if (!preg_match('/(\d+,)?\d+/', $option)) {
                return false;
            }
        }

        return true;
    }

    protected function _assignSummaryInformations()
    {
        $summary = $this->context->cart->getSummaryDetails();
        $customizedDatas = Product::getAllCustomizedDatas($this->context->cart->id);

        // override customization tax rate with real tax (tax rules)
        if ($customizedDatas) {
            foreach ($summary['products'] as &$productUpdate) {
                $productId = (int)isset($productUpdate['id_product']) ? $productUpdate['id_product'] : $productUpdate['product_id'];
                $productAttributeId = (int)isset($productUpdate['id_product_attribute']) ? $productUpdate['id_product_attribute'] : $productUpdate['product_attribute_id'];

                if (isset($customizedDatas[$productId][$productAttributeId])) {
                    $productUpdate['tax_rate'] = Tax::getProductTaxRate($productId, Cart::getIdAddressForTaxCalculation($productId));
                }
            }

            Product::addCustomizationPrice($summary['products'], $customizedDatas);
        }

        $cart_product_context = Context::getContext()->cloneContext();
        foreach ($summary['products'] as $key => &$product) {
            $product['quantity'] = $product['cart_quantity'];// for compatibility with 1.2 themes

            if ($cart_product_context->shop->id != $product['id_shop']) {
                $cart_product_context->shop = new Shop((int)$product['id_shop']);
            }
            $product['price_without_specific_price'] = Product::getPriceStatic(
                $product['id_product'],
                !Product::getTaxCalculationMethod(),
                $product['id_product_attribute'],
                6,
                null,
                false,
                false,
                1,
                false,
                null,
                null,
                null,
                $null,
                true,
                true,
                $cart_product_context);

            if (Product::getTaxCalculationMethod()) {
                $product['is_discounted'] = Tools::ps_round($product['price_without_specific_price'], _PS_PRICE_COMPUTE_PRECISION_) != Tools::ps_round($product['price'], _PS_PRICE_COMPUTE_PRECISION_);
            } else {
                $product['is_discounted'] = Tools::ps_round($product['price_without_specific_price'], _PS_PRICE_COMPUTE_PRECISION_) != Tools::ps_round($product['price_wt'], _PS_PRICE_COMPUTE_PRECISION_);
            }
        }

        // Get available cart rules and unset the cart rules already in the cart
        $available_cart_rules = CartRule::getCustomerCartRules($this->context->language->id, (isset($this->context->customer->id) ? $this->context->customer->id : 0), true, true, true, $this->context->cart, false, true);
        $cart_cart_rules = $this->context->cart->getCartRules();
        foreach ($available_cart_rules as $key => $available_cart_rule) {
            foreach ($cart_cart_rules as $cart_cart_rule) {
                if ($available_cart_rule['id_cart_rule'] == $cart_cart_rule['id_cart_rule']) {
                    unset($available_cart_rules[$key]);
                    continue 2;
                }
            }
        }

        $show_option_allow_separate_package = (!$this->context->cart->isAllProductsInStock(true) && Configuration::get('PS_SHIP_WHEN_AVAILABLE'));
        $advanced_payment_api = (bool)Configuration::get('PS_ADVANCED_PAYMENT_API');

        $this->context->smarty->assign($summary);
        $this->context->smarty->assign(array(
            'token_cart' => Tools::getToken(false),
            'isLogged' => $this->isLogged,
            'isVirtualCart' => $this->context->cart->isVirtualCart(),
            'productNumber' => $this->context->cart->nbProducts(),
            'voucherAllowed' => CartRule::isFeatureActive(),
            'shippingCost' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
            'shippingCostTaxExc' => $this->context->cart->getOrderTotal(false, Cart::ONLY_SHIPPING),
            'customizedDatas' => $customizedDatas,
            'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
            'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
            'lastProductAdded' => $this->context->cart->getLastProduct(),
            'displayVouchers' => $available_cart_rules,
            'show_option_allow_separate_package' => $show_option_allow_separate_package,
            'smallSize' => Image::getSize(ImageType::getFormatedName('small')),
            'advanced_payment_api' => $advanced_payment_api

        ));

        $this->context->smarty->assign(array(
            'HOOK_SHOPPING_CART' => Hook::exec('displayShoppingCartFooter', $summary),
            'HOOK_SHOPPING_CART_EXTRA' => Hook::exec('displayShoppingCart', $summary)
        ));
    }

    protected function _assignAddress()
    {
        //if guest checkout disabled and flag is_guest  in cookies is actived
        if (Configuration::get('PS_GUEST_CHECKOUT_ENABLED') == 0 && ((int)$this->context->customer->is_guest != Configuration::get('PS_GUEST_CHECKOUT_ENABLED'))) {
            $this->context->customer->logout();
            Tools::redirect('');
        } elseif (!Customer::getAddressesTotalById($this->context->customer->id)) {
            $multi = (int)Tools::getValue('multi-shipping');
            Tools::redirect('index.php?controller=address&back='.urlencode('order.php?step=1'.($multi ? '&multi-shipping='.$multi : '')));
        }

        $customer = $this->context->customer;
        if (Validate::isLoadedObject($customer)) {
            /* Getting customer addresses */
            $customerAddresses = $customer->getAddresses($this->context->language->id);

            // Getting a list of formated address fields with associated values
            $formatedAddressFieldsValuesList = array();

            foreach ($customerAddresses as $i => $address) {
                if (!Address::isCountryActiveById((int)$address['id_address'])) {
                    unset($customerAddresses[$i]);
                }
                $tmpAddress = new Address($address['id_address']);
                $formatedAddressFieldsValuesList[$address['id_address']]['ordered_fields'] = AddressFormat::getOrderedAddressFields($address['id_country']);
                $formatedAddressFieldsValuesList[$address['id_address']]['formated_fields_values'] = AddressFormat::getFormattedAddressFieldsValues(
                    $tmpAddress,
                    $formatedAddressFieldsValuesList[$address['id_address']]['ordered_fields']);

                unset($tmpAddress);
            }

            $customerAddresses = array_values($customerAddresses);

            if (!count($customerAddresses) && !Tools::isSubmit('ajax')) {
                $bad_delivery = false;
                if (($bad_delivery = (bool)!Address::isCountryActiveById((int)$this->context->cart->id_address_delivery)) || !Address::isCountryActiveById((int)$this->context->cart->id_address_invoice)) {
                    $params = array();
                    if ($this->step) {
                        $params['step'] = (int)$this->step;
                    }
                    if ($multi = (int)Tools::getValue('multi-shipping')) {
                        $params['multi-shipping'] = $multi;
                    }
                    $back_url = $this->context->link->getPageLink('order', true, (int)$this->context->language->id, $params);

                    $params = array('back' => $back_url, 'id_address' => ($bad_delivery ? (int)$this->context->cart->id_address_delivery : (int)$this->context->cart->id_address_invoice));
                    if ($multi) {
                        $params['multi-shipping'] = $multi;
                    }

                    Tools::redirect($this->context->link->getPageLink('address', true, (int)$this->context->language->id, $params));
                }
            }
            $this->context->smarty->assign(array(
                'addresses' => $customerAddresses,
                'formatedAddressFieldsValuesList' => $formatedAddressFieldsValuesList)
            );

            /* Setting default addresses for cart */
            if (count($customerAddresses)) {
                if ((!isset($this->context->cart->id_address_delivery) || empty($this->context->cart->id_address_delivery)) || !Address::isCountryActiveById((int)$this->context->cart->id_address_delivery)) {
                    $this->context->cart->id_address_delivery = (int)$customerAddresses[0]['id_address'];
                    $update = 1;
                }
                if ((!isset($this->context->cart->id_address_invoice) || empty($this->context->cart->id_address_invoice)) || !Address::isCountryActiveById((int)$this->context->cart->id_address_invoice)) {
                    $this->context->cart->id_address_invoice = (int)$customerAddresses[0]['id_address'];
                    $update = 1;
                }

                /* Update cart addresses only if needed */
                if (isset($update) && $update) {
                    $this->context->cart->update();
                    if (!$this->context->cart->isMultiAddressDelivery()) {
                        $this->context->cart->setNoMultishipping();
                    }
                    // Address has changed, so we check if the cart rules still apply
                    CartRule::autoRemoveFromCart($this->context);
                    CartRule::autoAddToCart($this->context);
                }
            }

            /* If delivery address is valid in cart, assign it to Smarty */
            if (isset($this->context->cart->id_address_delivery)) {
                $deliveryAddress = new Address((int)$this->context->cart->id_address_delivery);
                if (Validate::isLoadedObject($deliveryAddress) && ($deliveryAddress->id_customer == $customer->id)) {
                    $this->context->smarty->assign('delivery', $deliveryAddress);
                }
            }

            /* If invoice address is valid in cart, assign it to Smarty */
            if (isset($this->context->cart->id_address_invoice)) {
                $invoiceAddress = new Address((int)$this->context->cart->id_address_invoice);
                if (Validate::isLoadedObject($invoiceAddress) && ($invoiceAddress->id_customer == $customer->id)) {
                    $this->context->smarty->assign('invoice', $invoiceAddress);
                }
            }
        }
        if ($oldMessage = Message::getMessageByCartId((int)$this->context->cart->id)) {
            $this->context->smarty->assign('oldMessage', $oldMessage['message']);
        }
    }

    protected function _assignCarrier()
    {
        $carriers = $this->context->cart->simulateCarriersOutput(null, true);
        $checked = $this->context->cart->simulateCarrierSelectedOutput(false);
        $delivery_option_list = $this->context->cart->getDeliveryOptionList();
        $delivery_option = $this->context->cart->getDeliveryOption(null, false);
        $this->setDefaultCarrierSelection($delivery_option_list);

        $this->context->smarty->assign(array(
            'address_collection' => $this->context->cart->getAddressCollection(),
            'delivery_option_list' => $delivery_option_list,
            'carriers' => $carriers,
            'checked' => $checked,
            'delivery_option' => $delivery_option
        ));

        $advanced_payment_api = (bool)Configuration::get('PS_ADVANCED_PAYMENT_API');

        $vars = array(
            'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
                'carriers' => $carriers,
                'checked' => $checked,
                'delivery_option_list' => $delivery_option_list,
                'delivery_option' => $delivery_option
            )),
            'advanced_payment_api' => $advanced_payment_api
        );

        Cart::addExtraCarriers($vars);

        $this->context->smarty->assign($vars);
    }

    protected function _assignWrappingAndTOS()
    {
        // Wrapping fees
        $wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
        $wrapping_fees_tax_inc = $this->context->cart->getGiftWrappingPrice();

        // TOS
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $this->link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool)Configuration::get('PS_SSL_ENABLED'));
        if (!strpos($this->link_conditions, '?')) {
            $this->link_conditions .= '?content_only=1';
        } else {
            $this->link_conditions .= '&content_only=1';
        }

        $free_shipping = false;
        foreach ($this->context->cart->getCartRules() as $rule) {
            if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                $free_shipping = true;
                break;
            }
        }
        $this->context->smarty->assign(array(
            'free_shipping' => $free_shipping,
            'checkedTOS' => (int)$this->context->cookie->checkedTOS,
            'recyclablePackAllowed' => (int)Configuration::get('PS_RECYCLABLE_PACK'),
            'giftAllowed' => (int)Configuration::get('PS_GIFT_WRAPPING'),
            'cms_id' => (int)Configuration::get('PS_CONDITIONS_CMS_ID'),
            'conditions' => (int)Configuration::get('PS_CONDITIONS'),
            'link_conditions' => $this->link_conditions,
            'recyclable' => (int)$this->context->cart->recyclable,
            'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
            'carriers' => $this->context->cart->simulateCarriersOutput(),
            'checked' => $this->context->cart->simulateCarrierSelectedOutput(),
            'address_collection' => $this->context->cart->getAddressCollection(),
            'delivery_option' => $this->context->cart->getDeliveryOption(null, false),
            'gift_wrapping_price' => (float)$wrapping_fees,
            'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, $this->context->currency),
            'override_tos_display' => Hook::exec('overrideTOSDisplay'),
            'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, $this->context->currency)));
    }

    protected function _assignPayment()
    {
        if ((bool)Configuration::get('PS_ADVANCED_PAYMENT_API')) {
            $this->addJS(_THEME_JS_DIR_.'advanced-payment-api.js');

            // TOS
            $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
            $this->link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool)Configuration::get('PS_SSL_ENABLED'));
            if (!strpos($this->link_conditions, '?')) {
                $this->link_conditions .= '?content_only=1';
            } else {
                $this->link_conditions .= '&content_only=1';
            }

            $this->context->smarty->assign(array(
                'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                'HOOK_ADVANCED_PAYMENT' => Hook::exec('advancedPaymentOptions', array(), null, true),
                'link_conditions' => $this->link_conditions
            ));
        } else {
            $this->context->smarty->assign(array(
                'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                'HOOK_PAYMENT' => Hook::exec('displayPayment'),
            ));
        }
    }

    /**
     * Set id_carrier to 0 (no shipping price)
     */
    protected function setNoCarrier()
    {
        $this->context->cart->setDeliveryOption(null);
        $this->context->cart->update();
    }

    /**
     * Decides what the default carrier is and update the cart with it
     *
     * @todo this function must be modified - id_carrier is now delivery_option
     *
     * @param array $carriers
     *
     * @deprecated since 1.5.0
     *
     * @return number the id of the default carrier
     */
    protected function setDefaultCarrierSelection($carriers)
    {
        if (!$this->context->cart->getDeliveryOption(null, true)) {
            $this->context->cart->setDeliveryOption($this->context->cart->getDeliveryOption());
        }
    }

    /**
     * Decides what the default carrier is and update the cart with it
     *
     * @param array $carriers
     *
     * @deprecated since 1.5.0
     *
     * @return number the id of the default carrier
     */
    protected function _setDefaultCarrierSelection($carriers)
    {
        $this->context->cart->id_carrier = Carrier::getDefaultCarrierSelection($carriers, (int)$this->context->cart->id_carrier);

        if ($this->context->cart->update()) {
            return $this->context->cart->id_carrier;
        }
        return 0;
    }
}
