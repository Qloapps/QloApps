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

class OrderOpcControllerCore extends ParentOrderController
{
    public $php_self = 'order-opc';
    public $isLogged;

    protected $ajax_refresh = false;

    /**
     * Initialize order opc controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        if ($this->nbProducts) {
            $this->context->smarty->assign('virtual_cart', $this->context->cart->isVirtualCart());
        }

        $this->context->smarty->assign('is_multi_address_delivery', $this->context->cart->isMultiAddressDelivery() || ((int)Tools::getValue('multi-shipping') == 1));
        $this->context->smarty->assign('open_multishipping_fancybox', (int)Tools::getValue('multi-shipping') == 1);

        if ($this->context->cart->nbProducts()) {
            if (Tools::isSubmit('ajax')) {
                if (Tools::isSubmit('method')) {
                    switch (Tools::getValue('method')) {
                        case 'updateMessage':
                            if (Tools::isSubmit('message')) {
                                $txt_message = urldecode(Tools::getValue('message'));
                                $this->_updateMessage($txt_message);
                                if (count($this->errors)) {
                                    $this->ajaxDie('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
                                }
                                $this->ajaxDie(true);
                            }
                            break;

                        case 'updateCarrierAndGetPayments':
                            if ((Tools::isSubmit('delivery_option') || Tools::isSubmit('id_carrier')) && Tools::isSubmit('recyclable') && Tools::isSubmit('gift') && Tools::isSubmit('gift_message')) {
                                $this->_assignWrappingAndTOS();
                                if ($this->_processCarrier()) {
                                    $carriers = $this->context->cart->simulateCarriersOutput();
                                    $return = array_merge(array(
                                        'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                                        'HOOK_PAYMENT' => $this->_getPaymentMethods(),
                                        'carrier_data' => $this->_getCarrierList(),
                                        'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array('carriers' => $carriers))
                                        ),
                                        $this->getFormatedSummaryDetail()
                                    );
                                    Cart::addExtraCarriers($return);
                                    $this->ajaxDie(json_encode($return));
                                } else {
                                    $this->errors[] = Tools::displayError('An error occurred while updating the cart.');
                                }
                                if (count($this->errors)) {
                                    $this->ajaxDie('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
                                }
                                exit;
                            }
                            break;

                        case 'updateTOSStatusAndGetPayments':
                            if (Tools::isSubmit('checked')) {
                                $this->context->cookie->checkedTOS = (int)Tools::getValue('checked');
                                $this->ajaxDie(json_encode(array(
                                    'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                                    'HOOK_PAYMENT' => $this->_getPaymentMethods()
                                )));
                            }
                            break;

                        case 'getCarrierList':
                            $this->ajaxDie(json_encode($this->_getCarrierList()));
                            break;

                        case 'editCustomer':
                            if (!$this->isLogged || !$this->context->customer->is_guest) {
                                exit;
                            }

                            if (Validate::isEmail($email = Tools::getValue('email')) && !empty($email)) {
                                if (Customer::customerExists($email)) {
                                    $this->errors[] = Tools::displayError('An account using this email address has already been registered.', false);
                                }
                            }

                            if (Tools::getValue('years')) {
                                $this->context->customer->birthday = (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.(int)Tools::getValue('days');
                            }

                            $_POST['lastname'] = $_POST['customer_lastname'];
                            $_POST['firstname'] = $_POST['customer_firstname'];
                            $this->errors = array_merge($this->errors, $this->context->customer->validateController());
                            $this->context->customer->newsletter = (int)Tools::isSubmit('newsletter');
                            $this->context->customer->optin = (int)Tools::isSubmit('optin');
                            $this->context->customer->is_guest = (Tools::isSubmit('is_new_customer') ? !Tools::getValue('is_new_customer', 1) : 0);

                            if ($idAddressDelivery = Tools::getValue('opc_id_address_delivery')) {
                                $objAddress = new Address($idAddressDelivery);
                                if (Validate::isLoadedObject($objAddress)) {
                                    $phoneMobile = Tools::getValue('phone_mobile');

                                    if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && !$phoneMobile) {
                                        $this->errors[] = Tools::displayError('Mobile phone number is a required field.', false);
                                    }

                                    if (!Validate::isPhoneNumber($phoneMobile)) {
                                        $this->errors[] = Tools::displayError('Please enter a valid Mobile phone number.', false);
                                    }

                                    if (!count($this->errors)) {
                                        $objAddress->phone_mobile = $phoneMobile;
                                        if (!$objAddress->save()) {
                                            $this->errors[] = Tools::displayError('Something went wrong while saving phone number. Please try again.', false);
                                        }
                                    }
                                }
                            }

                            $return = array(
                                'hasError' => !empty($this->errors),
                                'errors' => $this->errors,
                                'id_customer' => (int)$this->context->customer->id,
                                'token' => Tools::getToken(false)
                            );
                            if (!count($this->errors)) {
                                $return['isSaved'] = (bool)$this->context->customer->update();
                            } else {
                                $return['isSaved'] = false;
                            }
                            $this->ajaxDie(json_encode($return));
                            break;

                        case 'transformGuestAccount':
                            $passwd = Tools::getValue('passwd');

                            if (!$passwd) {
                                $this->errors[] = Tools::displayError('Please enter a password.');
                            }

                            if ($passwd && !Validate::isPasswd($passwd)) {
                                $this->errors[] = Tools::displayError('Please enter a valid password.');
                            }

                            if (!count($this->errors)) {
                                $customer = new Customer($this->context->customer->id);
                                if ($customer->transformToCustomer($this->context->language->id, $passwd)) {
                                    $this->context->updateCustomer($customer);
                                } else {
                                    $this->errors[] = Tools::displayError('An error occurred while transforming your account into a registered customer.');
                                }
                            }

                            $return = array(
                                'hasError' => !empty($this->errors),
                                'errors' => $this->errors,
                            );

                            $this->ajaxDie(json_encode($return));
                            break;

                        case 'getAddressBlockAndCarriersAndPayments':
                            if ($this->context->customer->isLogged() || $this->context->customer->isGuest()) {
                                // check if customer have addresses
                                if (!Customer::getAddressesTotalById($this->context->customer->id)) {
                                    $this->ajaxDie(json_encode(array('no_address' => 1)));
                                }
                                if (file_exists(_PS_MODULE_DIR_.'blockuserinfo/blockuserinfo.php')) {
                                    include_once(_PS_MODULE_DIR_.'blockuserinfo/blockuserinfo.php');
                                    $block_user_info = new BlockUserInfo();
                                }
                                $this->context->smarty->assign('isVirtualCart', $this->context->cart->isVirtualCart());
                                $this->_processAddressFormat();
                                $this->_assignAddress();

                                if (!($formated_address_fields_values_list = $this->context->smarty->getTemplateVars('formatedAddressFieldsValuesList'))) {
                                    $formated_address_fields_values_list = array();
                                }

                                // Wrapping fees
                                $wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
                                $wrapping_fees_tax_inc = $this->context->cart->getGiftWrappingPrice();
                                $is_adv_api = Tools::getValue('isAdvApi');

                                if ($is_adv_api) {
                                    $tpl = 'order-address-advanced.tpl';
                                    $this->context->smarty->assign(
                                        array('products' => $this->context->cart->getProducts())
                                    );
                                } else {
                                    $tpl = 'order-address.tpl';
                                }

                                $return = array_merge(array(
                                    'order_opc_adress' => $this->context->smarty->fetch(_PS_THEME_DIR_.$tpl),
                                    'block_user_info' => '',
                                    // 'block_user_info_nav' => (isset($block_user_info) ? $block_user_info->hookDisplayTop(array()) : ''), //changed to below line
                                    'block_user_info_nav' => (isset($block_user_info) ? $block_user_info->displayUserInfo(array()) : ''),
                                    'formatedAddressFieldsValuesList' => $formated_address_fields_values_list,
                                    'carrier_data' => ($is_adv_api ? '' : $this->_getCarrierList()),
                                    'HOOK_TOP_PAYMENT' => ($is_adv_api ? '' : Hook::exec('displayPaymentTop')),
                                    'HOOK_PAYMENT' => ($is_adv_api ? '' : $this->_getPaymentMethods()),
                                    'no_address' => 0,
                                    'gift_price' => Tools::displayPrice(Tools::convertPrice(
                                        Product::getTaxCalculationMethod() == 1 ? $wrapping_fees : $wrapping_fees_tax_inc,
                                        new Currency((int)$this->context->cookie->id_currency)))
                                    ),
                                    $this->getFormatedSummaryDetail()
                                );
                                $this->ajaxDie(json_encode($return));
                            }
                            die(Tools::displayError());
                            break;

                        case 'makeFreeOrder':
                            /* Bypass payment step if total is 0 */
                            if (($id_order = $this->_checkFreeOrder()) && $id_order) {
                                $order = new Order((int)$id_order);
                                $email = $this->context->customer->email;
                                if ($this->context->customer->is_guest) {
                                    $this->context->customer->logout();
                                } // If guest we clear the cookie for security reason
                                $this->ajaxDie('freeorder:'.$order->reference.':'.$email);
                            }
                            exit;
                            break;

                        case 'updateAddressesSelected':
                            if ($this->context->customer->isLogged(true)) {
                                $address_delivery = new Address((int)Tools::getValue('id_address_delivery'));
                                $this->context->smarty->assign('isVirtualCart', $this->context->cart->isVirtualCart());
                                $address_invoice = ((int)Tools::getValue('id_address_delivery') == (int)Tools::getValue('id_address_invoice') ? $address_delivery : new Address((int)Tools::getValue('id_address_invoice')));
                                if ($address_delivery->id_customer != $this->context->customer->id || $address_invoice->id_customer != $this->context->customer->id) {
                                    $this->errors[] = Tools::displayError('This address is not yours.');
                                } elseif (!Address::isCountryActiveById((int)Tools::getValue('id_address_delivery'))) {
                                    $this->errors[] = Tools::displayError('This address is not in a valid area.');
                                } elseif (!Validate::isLoadedObject($address_delivery) || !Validate::isLoadedObject($address_invoice) || $address_invoice->deleted || $address_delivery->deleted) {
                                    $this->errors[] = Tools::displayError('This address is invalid.');
                                } else {
                                    $this->context->cart->id_address_delivery = (int)Tools::getValue('id_address_delivery');
                                    $this->context->cart->id_address_invoice = Tools::isSubmit('same') ? $this->context->cart->id_address_delivery : (int)Tools::getValue('id_address_invoice');
                                    if (!$this->context->cart->update()) {
                                        $this->errors[] = Tools::displayError('An error occurred while updating your cart.');
                                    }

                                    $infos = Address::getCountryAndState((int)$this->context->cart->id_address_delivery);
                                    if (isset($infos['id_country']) && $infos['id_country']) {
                                        $country = new Country((int)$infos['id_country']);
                                        $this->context->country = $country;
                                    }

                                    // Address has changed, so we check if the cart rules still apply
                                    $cart_rules = $this->context->cart->getCartRules();
                                    CartRule::autoRemoveFromCart($this->context);
                                    CartRule::autoAddToCart($this->context);
                                    if ((int)Tools::getValue('allow_refresh')) {
                                        // If the cart rules has changed, we need to refresh the whole cart
                                        $cart_rules2 = $this->context->cart->getCartRules();
                                        if (count($cart_rules2) != count($cart_rules)) {
                                            $this->ajax_refresh = true;
                                        } else {
                                            $rule_list = array();
                                            foreach ($cart_rules2 as $rule) {
                                                $rule_list[] = $rule['id_cart_rule'];
                                            }
                                            foreach ($cart_rules as $rule) {
                                                if (!in_array($rule['id_cart_rule'], $rule_list)) {
                                                    $this->ajax_refresh = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }

                                    if (!$this->context->cart->isMultiAddressDelivery()) {
                                        $this->context->cart->setNoMultishipping();
                                    } // As the cart is no multishipping, set each delivery address lines with the main delivery address

                                    if (!count($this->errors)) {
                                        $result = $this->_getCarrierList();
                                        // Wrapping fees
                                        $wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
                                        $wrapping_fees_tax_inc = $this->context->cart->getGiftWrappingPrice();
                                        $result = array_merge($result, array(
                                            'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                                            'HOOK_PAYMENT' => $this->_getPaymentMethods(),
                                            'gift_price' => Tools::displayPrice(Tools::convertPrice(Product::getTaxCalculationMethod() == 1 ? $wrapping_fees : $wrapping_fees_tax_inc, new Currency((int)$this->context->cookie->id_currency))),
                                            'carrier_data' => $this->_getCarrierList(),
                                            'refresh' => (bool)$this->ajax_refresh),
                                            $this->getFormatedSummaryDetail()
                                        );
                                        $this->ajaxDie(json_encode($result));
                                    }
                                }
                                if (count($this->errors)) {
                                    $this->ajaxDie(json_encode(array(
                                        'hasError' => true,
                                        'errors' => $this->errors
                                    )));
                                }
                            }
                            die(Tools::displayError());
                            break;

                        case 'multishipping':
                            $this->_assignSummaryInformations();
                            $this->context->smarty->assign('product_list', $this->context->cart->getProducts());

                            if ($this->context->customer->id) {
                                $this->context->smarty->assign('address_list', $this->context->customer->getAddresses($this->context->language->id));
                            } else {
                                $this->context->smarty->assign('address_list', array());
                            }
                            $this->setTemplate(_PS_THEME_DIR_.'order-address-multishipping-products.tpl');
                            $this->display();
                            $this->ajaxDie();
                            break;

                        case 'cartReload':
                            $this->_assignSummaryInformations();
                            if ($this->context->customer->id) {
                                $this->context->smarty->assign('address_list', $this->context->customer->getAddresses($this->context->language->id));
                            } else {
                                $this->context->smarty->assign('address_list', array());
                            }
                            $this->context->smarty->assign('opc', true);
                            $this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
                            $this->display();
                            $this->ajaxDie();
                            break;

                        case 'noMultiAddressDelivery':
                            $this->context->cart->setNoMultishipping();
                            $this->ajaxDie();
                            break;
                        case 'getRoomTypeBookingDemands':
                            $this->ajaxDie($this->getRoomTypeBookingServices());
                            exit;
                            break;
                        case 'changeRoomDemands':
                            $this->ajaxDie($this->changeRoomDemands());
                            exit;
                            break;
                        case 'submitCustomerGuestDetail':
                            $this->ajaxDie($this->submitCustomerGuestDetail());
                            exit;
                            break;
                        default:
                            throw new PrestaShopException('Unknown method "'.Tools::getValue('method').'"');
                    }
                } else {
                    throw new PrestaShopException('Method is not defined');
                }
            }
        } elseif (Tools::isSubmit('ajax')) {
            $this->errors[] = Tools::displayError('There is no product in your cart.');
            $this->ajaxDie('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        if (!$this->useMobileTheme()) {
            // Adding CSS style sheet
            $this->addCSS(_THEME_CSS_DIR_.'order-opc.css');
            // Adding JS files
            $this->addJS(_THEME_JS_DIR_.'order-opc.js');
            $this->addJqueryPlugin('scrollTo');
        } else {
            $this->addJS(_THEME_MOBILE_JS_DIR_.'opc.js');
        }

        $this->addJS(array(
            _THEME_JS_DIR_.'tools/statesManagement.js',
            _THEME_JS_DIR_.'order-carrier.js',
            _PS_JS_DIR_.'validate.js'
        ));
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        // check all service products are available
        RoomTypeServiceProductCartDetail::validateServiceProductsInCart();

        parent::initContent();

        // check ORDER RESTRICT condition before payment by the customer
        $orderRestrictErr = HotelOrderRestrictDate::validateOrderRestrictDateOnPayment($this);

        /* id_carrier is not defined in database before choosing a carrier, set it to a default one to match a potential cart _rule */
        if (empty($this->context->cart->id_carrier)) {
            $checked = $this->context->cart->simulateCarrierSelectedOutput();
            $checked = ((int)Cart::desintifier($checked));
            $this->context->cart->id_carrier = $checked;
            $this->context->cart->update();
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);
        }

        // SHOPPING CART
        $this->_assignSummaryInformations();
        // WRAPPING AND TOS
        $this->_assignWrappingAndTOS();

        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }

        // If a rule offer free-shipping, force hidding shipping prices
        $free_shipping = false;
        foreach ($this->context->cart->getCartRules() as $rule) {
            if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                $free_shipping = true;
                break;
            }
        }
        if ($this->context->cart->id_address_delivery) {
            // send address formatted layout data
            if (Validate::isLoadedObject($address = new Address($this->context->cart->id_address_delivery))) {
                $addressLayout = AddressFormat::getFormattedLayoutData($address);
                $orderedAddressFields = AddressFormat::getOrderedAddressFields($address->id_country, false, true);
                $this->context->smarty->assign(
                    array(
                        'orderedAddressFields' => $orderedAddressFields,
                        'addressLayout' => $addressLayout
                    )
                );
            }
        }

        // if any error is there in the checkout process the reset the steps of checkout
        if (count($this->errors)) {
            CheckoutProcess::refreshCheckoutProcess();
        }

        // assign checkout process steps
        $this->setCheckoutProcess();

        // if there is cookie variable in the url the redirect
        if (Tools::getValue('proceed_to_customer_dtl') || Tools::getValue('proceed_to_payment')) {
            Tools::redirect($this->context->link->getPageLink('order-opc', null, $this->context->language->id));
        }

        $this->context->smarty->assign('checkout_process_steps', $this->checkoutProcess->getSteps());

        // set room type demands
        // $objGlobalDemand = new HotelRoomTypeGlobalDemand();
        // $allDemands = $objGlobalDemand->getAllDemands();
        // $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->context->smarty->assign(
            array(
                'orderRestrictErr' => $orderRestrictErr,
                // 'allDemands' => $allDemands,
                // 'defaultcurrencySign' => $objCurrency->sign,
                'THEME_DIR' => _THEME_DIR_,
                'PS_CUSTOMER_ADDRESS_CREATION' => Configuration::get('PS_CUSTOMER_ADDRESS_CREATION'),
                'PS_ROOM_PRICE_AUTO_ADD_BREAKDOWN' => Configuration::get('PS_ROOM_PRICE_AUTO_ADD_BREAKDOWN'),
                'free_shipping' => $free_shipping,
                'isGuest' => isset($this->context->cookie->is_guest) ? $this->context->cookie->is_guest : 0,
                'countries' => $countries,
                'sl_country' => (int)Tools::getCountry(),
                'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                'errorCarrier' => Tools::displayError('You must choose a carrier.', false),
                'errorTOS' => Tools::displayError('You must accept the Terms of Service.', false),
                'isPaymentStep' => isset($_GET['isPaymentStep']) && $_GET['isPaymentStep'],
                'genders' => Gender::getGenders(),
                'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
                'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
                'HOOK_CREATE_ACCOUNT_TOP' => Hook::exec('displayCustomerAccountFormTop')
            )
        );
        $years = Tools::dateYears();
        $months = Tools::dateMonths();
        $days = Tools::dateDays();
        $this->context->smarty->assign(
            array(
                'years' => $years,
                'months' => $months,
                'days' => $days,
            )
        );

        /* Load guest informations */
        if ($this->isLogged) {
            if ($this->context->cookie->is_guest) {
                $this->context->smarty->assign('guestInformations', $this->_getGuestInformations());
            } else {
                $this->context->smarty->assign('guestInformations', (array)$this->context->customer);
            }
        }
        // ADDRESS
        if ($this->isLogged) {
            $this->_assignAddress();
        }
        // CARRIER
        $this->_assignCarrier();
        // PAYMENT
        $this->_assignPayment();
        // GUEST BOOKING
        if ($this->isLogged) {
            if ($id_customer_guest_detail = CartCustomerGuestDetail::getCartCustomerGuest($this->context->cart->id)) {
                $this->context->smarty->assign(
                    'customer_guest_detail', CartCustomerGuestDetail::getCustomerGuestDetail($id_customer_guest_detail)
                );
            }
            $this->context->smarty->assign('id_customer_guest_detail', $id_customer_guest_detail);
        }
        Tools::safePostVars();

        $newsletter = Configuration::get('PS_CUSTOMER_NWSL') || (Module::isInstalled('blocknewsletter') && Module::getInstanceByName('blocknewsletter')->active);
        $this->context->smarty->assign('newsletter', $newsletter);
        $this->context->smarty->assign('optin', (bool)Configuration::get('PS_CUSTOMER_OPTIN'));
        $this->context->smarty->assign('field_required', $this->context->customer->validateFieldsRequiredDatabase());

        $this->_processAddressFormat();

        $link = new Link();

        if (Tools::getValue('deleteFromOrderLine')) {
            $id_product = Tools::getValue('id_product');
            $date_from = Tools::getValue('date_from');
            $date_to = Tools::getValue('date_to');
            $objCartBookingData = new HotelCartBookingData();
            if ($cart_data_dlt = $objCartBookingData->deleteRoomDataFromOrderLine(
                $this->context->cart->id,
                $this->context->cart->id_guest,
                $id_product,
                $date_from,
                $date_to
            )) {
                Tools::redirect($link->getPageLink('order-opc', null, $this->context->language->id));
            }
        }

        if ((bool)Configuration::get('PS_ADVANCED_PAYMENT_API')) {
            $this->addJS(_THEME_JS_DIR_ . 'advanced-payment-api.js');
            $this->setTemplate(_PS_THEME_DIR_ . 'order-opc-advanced.tpl');
        } else {
            // set used objects in the below code
            // $objBookingDetail = new HotelBookingDetail();
            // $objHtlRoomType = new HotelRoomType();

            $cartProducts = $this->context->cart->getProducts();

            if (!empty($cartProducts)) {

                if ($cartBookingInfo = HotelCartBookingData::getHotelCartBookingData()) {
                    // ddd($cartBookingInfo);
                    $this->context->smarty->assign('cart_htl_data', $cartBookingInfo);
                }
                $objHotelServiceProductCartDetail = new HotelServiceProductCartDetail();
                if ($normalCartProduct = $objHotelServiceProductCartDetail->getHotelProducts($this->context->cart->id, 0, 0, 0, null, null, true)) {
                    $this->context->smarty->assign('cart_normal_data', $normalCartProduct);
                }

                // For Advanced Payment work
                $objAdvPayment = new HotelAdvancedPayment();
                if ($objAdvPayment->isAdvancePaymentAvailableForCurrentCart()) {
                    if (Tools::isSubmit('submitAdvPayment')) {
                        if (Tools::getValue('payment_type') == Order::ORDER_PAYMENT_TYPE_ADVANCE) {
                            $this->context->cart->is_advance_payment = 1;
                        } else {
                            $this->context->cart->is_advance_payment = 0;
                        }
                        $this->context->cart->save();

                        Tools::redirect($this->context->link->getPageLink('order-opc'));
                    }

                    // set if advance payment is selected by the customer
                    if ($this->context->cart->is_advance_payment) {
                        $this->context->smarty->assign('is_advance_payment', 1);
                    }

                    // get advance payment amount and send data to the template
                    $advPaymentAmount = $this->context->cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
                    $this->context->smarty->assign(array(
                        'advance_payment_active'=> 1,
                        'advPaymentAmount'=> $advPaymentAmount,
                        'dueAmount'=> ($this->context->cart->getOrderTotal() - $advPaymentAmount),
                    ));
                }
            }

            $this->setTemplate(_PS_THEME_DIR_.'order-opc.tpl');
        }
    }

    // sets checkout process steps as per current values
    private function setCheckoutProcess()
    {
        $this->checkoutProcess = new CheckoutProcess();
        // add the steps you want to add in checkout process
        $this->checkoutProcess
            ->addStep(new CheckoutCartSummaryStep())
            ->addStep(new CheckoutCustomerDetailsStep())
            ->addStep(new CheckoutPaymentStep());

        $this->checkoutProcess->handleRequest();

        // for making steps synced
        $this->checkoutProcess
            ->setNextStepReachable()
            ->markCurrentStep()
            ->invalidateAllStepsAfterCurrent();
    }

    protected function _getGuestInformations()
    {
        $customer = $this->context->customer;
        $address_delivery = new Address($this->context->cart->id_address_delivery);

        $id_address_invoice = $this->context->cart->id_address_invoice != $this->context->cart->id_address_delivery ? (int)$this->context->cart->id_address_invoice : 0;
        $address_invoice = new Address($id_address_invoice);

        if ($customer->birthday) {
            $birthday = explode('-', $customer->birthday);
        } else {
            $birthday = array('0', '0', '0');
        }

        return array(
            'id_customer' => (int)$customer->id,
            'email' => Tools::htmlentitiesUTF8($customer->email),
            'customer_lastname' => Tools::htmlentitiesUTF8($customer->lastname),
            'customer_firstname' => Tools::htmlentitiesUTF8($customer->firstname),
            'newsletter' => (int)$customer->newsletter,
            'optin' => (int)$customer->optin,
            'id_address_delivery' => (int)$this->context->cart->id_address_delivery,
            'company' => Tools::htmlentitiesUTF8($address_delivery->company),
            'lastname' => Tools::htmlentitiesUTF8($address_delivery->lastname),
            'firstname' => Tools::htmlentitiesUTF8($address_delivery->firstname),
            'dni' => Tools::htmlentitiesUTF8($address_delivery->dni),
            'address1' => Tools::htmlentitiesUTF8($address_delivery->address1),
            'postcode' => Tools::htmlentitiesUTF8($address_delivery->postcode),
            'city' => Tools::htmlentitiesUTF8($address_delivery->city),
            'phone' => Tools::htmlentitiesUTF8($address_delivery->phone),
            'phone_mobile' => Tools::htmlentitiesUTF8($address_delivery->phone_mobile),
            'id_country' => (int)$address_delivery->id_country,
            'id_state' => (int)$address_delivery->id_state,
            'id_gender' => (int)$customer->id_gender,
            'sl_year' => $birthday[0],
            'sl_month' => $birthday[1],
            'sl_day' => $birthday[2],
            'company_invoice' => Tools::htmlentitiesUTF8($address_invoice->company),
            'lastname_invoice' => Tools::htmlentitiesUTF8($address_invoice->lastname),
            'firstname_invoice' => Tools::htmlentitiesUTF8($address_invoice->firstname),
            'dni_invoice' => Tools::htmlentitiesUTF8($address_invoice->dni),
            'address1_invoice' => Tools::htmlentitiesUTF8($address_invoice->address1),
            'address2_invoice' => Tools::htmlentitiesUTF8($address_invoice->address2),
            'postcode_invoice' => Tools::htmlentitiesUTF8($address_invoice->postcode),
            'city_invoice' => Tools::htmlentitiesUTF8($address_invoice->city),
            'phone_invoice' => Tools::htmlentitiesUTF8($address_invoice->phone),
            'phone_mobile_invoice' => Tools::htmlentitiesUTF8($address_invoice->phone_mobile),
            'id_country_invoice' => (int)$address_invoice->id_country,
            'id_state_invoice' => (int)$address_invoice->id_state,
            'id_address_invoice' => $id_address_invoice,
            'invoice_company' => Tools::htmlentitiesUTF8($address_invoice->company),
            'invoice_lastname' => Tools::htmlentitiesUTF8($address_invoice->lastname),
            'invoice_firstname' => Tools::htmlentitiesUTF8($address_invoice->firstname),
            'invoice_vat_number' => Tools::htmlentitiesUTF8($address_invoice->vat_number),
            'invoice_dni' => Tools::htmlentitiesUTF8($address_invoice->dni),
            'invoice_address' => $this->context->cart->id_address_invoice !== $this->context->cart->id_address_delivery,
            'invoice_address1' => Tools::htmlentitiesUTF8($address_invoice->address1),
            'invoice_address2' => Tools::htmlentitiesUTF8($address_invoice->address2),
            'invoice_postcode' => Tools::htmlentitiesUTF8($address_invoice->postcode),
            'invoice_city' => Tools::htmlentitiesUTF8($address_invoice->city),
            'invoice_phone' => Tools::htmlentitiesUTF8($address_invoice->phone),
            'invoice_phone_mobile' => Tools::htmlentitiesUTF8($address_invoice->phone_mobile),
            'invoice_id_country' => (int)$address_invoice->id_country,
            'invoice_id_state' => (int)$address_invoice->id_state,
        );
    }

    protected function _assignCarrier()
    {
        if (!$this->isLogged) {
            $carriers = $this->context->cart->simulateCarriersOutput();
            $old_message = Message::getMessageByCartId((int)$this->context->cart->id);
            $this->context->smarty->assign(array(
                'HOOK_EXTRACARRIER' => null,
                'HOOK_EXTRACARRIER_ADDR' => null,
                'oldMessage' => isset($old_message['message'])? $old_message['message'] : '',
                'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
                    'carriers' => $carriers,
                    'checked' => $this->context->cart->simulateCarrierSelectedOutput(),
                    'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
                    'delivery_option' => $this->context->cart->getDeliveryOption(null, true)
                ))
            ));
        } else {
            parent::_assignCarrier();
        }
    }

    protected function _assignPayment()
    {
        if ((bool)Configuration::get('PS_ADVANCED_PAYMENT_API')) {
            $this->context->smarty->assign(array(
                'HOOK_TOP_PAYMENT' => ($this->isLogged ? Hook::exec('displayPaymentTop') : ''),
                'HOOK_PAYMENT' => $this->_getPaymentMethods(),
                'HOOK_ADVANCED_PAYMENT' => Hook::exec('advancedPaymentOptions', array(), null, true),
                'link_conditions' => $this->link_conditions
            ));
        } else {
            $this->context->smarty->assign(array(
                'HOOK_TOP_PAYMENT' => ($this->isLogged ? Hook::exec('displayPaymentTop') : ''),
                'HOOK_PAYMENT' => $this->_getPaymentMethods()
            ));
        }
    }

    protected function _getPaymentMethods()
    {
        if (!$this->isLogged) {
            return '<p class="warning">'.Tools::displayError('Please sign in to see payment methods.').'</p>';
        }
        if ($this->context->cart->OrderExists()) {
            return '<p class="warning">'.Tools::displayError('Error: This order has already been validated.').'</p>';
        }
        if (!$this->context->cart->id_customer || !Customer::customerIdExistsStatic($this->context->cart->id_customer) || Customer::isBanned($this->context->cart->id_customer)) {
            return '<p class="warning">'.Tools::displayError('Error: No customer.').'</p>';
        }
        $address_delivery = new Address($this->context->cart->id_address_delivery);
        $address_invoice = ($this->context->cart->id_address_delivery == $this->context->cart->id_address_invoice ? $address_delivery : new Address($this->context->cart->id_address_invoice));
        if (!$this->context->cart->id_address_delivery || !$this->context->cart->id_address_invoice || !Validate::isLoadedObject($address_delivery) || !Validate::isLoadedObject($address_invoice) || $address_invoice->deleted || $address_delivery->deleted) {
            return '<p class="warning">'.Tools::displayError('Error: Please select an address.').'</p>';
        }
        if (count($this->context->cart->getDeliveryOptionList()) == 0 && !$this->context->cart->isVirtualCart()) {
            if ($this->context->cart->isMultiAddressDelivery()) {
                return '<p class="warning">'.Tools::displayError('Error: None of your chosen carriers deliver to some of the addresses you have selected.').'</p>';
            } else {
                return '<p class="warning">'.Tools::displayError('Error: None of your chosen carriers deliver to the address you have selected.').'</p>';
            }
        }
        if (!$this->context->cart->getDeliveryOption(null, false) && !$this->context->cart->isVirtualCart()) {
            return '<p class="warning">'.Tools::displayError('Error: Please choose a carrier.').'</p>';
        }
        if (!$this->context->cart->id_currency) {
            return '<p class="warning">'.Tools::displayError('Error: No currency has been selected.').'</p>';
        }
        if (!$this->context->cookie->checkedTOS && Configuration::get('PS_CONDITIONS')) {
            return '<p class="warning">'.Tools::displayError('Please accept the Terms of Service.').'</p>';
        }

        /* If some products have disappear */
        if (is_array($product = $this->context->cart->checkQuantities(true))) {
            return '<p class="warning">'.sprintf(Tools::displayError('An item (%s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.'), $product['name']).'</p>';
        }

        if ((int)$id_product = $this->context->cart->checkProductsAccess()) {
            return '<p class="warning">'.sprintf(Tools::displayError('An item in your cart is no longer available (%s). You cannot proceed with your order.'), Product::getProductName((int)$id_product)).'</p>';
        }

        /* Check minimal amount */
        $currency = Currency::getCurrency((int)$this->context->cart->id_currency);

        $minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
        if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase) {
            return '<p class="warning">'.sprintf(
                Tools::displayError('A minimum purchase total of %1s (tax excl.) is required to validate your order, current purchase total is %2s (tax excl.).'),
                Tools::displayPrice($minimal_purchase, $currency), Tools::displayPrice($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS), $currency)
            ).'</p>';
        }


        // check if customer has chosen advance payment option for this cart
        if ($this->context->cart->is_advance_payment) {
            $orderTotal = $this->context->cart->getOrderTotal(true, CART::ADVANCE_PAYMENT);
        } else {
            $orderTotal = $this->context->cart->getOrderTotal();
        }

        /* Bypass payment step if total is 0 */
        if ($orderTotal <= 0) {
            return '<p class="center"><button class="button btn btn-default button-medium" name="confirmOrder" id="confirmOrder" onclick="confirmFreeOrder();" type="submit"> <span>'.Tools::displayError('I confirm my order.').'</span></button></p>';
        }

        $return = Hook::exec('displayPayment');
        if (!$return) {
            return '<p class="warning">'.Tools::displayError('No payment method is available for use at this time. ').'</p>';
        }
        return $return;
    }

    protected function _getCarrierList()
    {
        $address_delivery = new Address($this->context->cart->id_address_delivery);

        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, Configuration::get('PS_SSL_ENABLED'));
        if (!strpos($link_conditions, '?')) {
            $link_conditions .= '?content_only=1';
        } else {
            $link_conditions .= '&content_only=1';
        }

        $carriers = $this->context->cart->simulateCarriersOutput();
        $delivery_option = $this->context->cart->getDeliveryOption(null, false, false);

        $wrapping_fees = $this->context->cart->getGiftWrappingPrice(false);
        $wrapping_fees_tax_inc = $this->context->cart->getGiftWrappingPrice();
        $old_message = Message::getMessageByCartId((int)$this->context->cart->id);

        $free_shipping = false;
        foreach ($this->context->cart->getCartRules() as $rule) {
            if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                $free_shipping = true;
                break;
            }
        }

        $this->context->smarty->assign('isVirtualCart', $this->context->cart->isVirtualCart());

        $vars = array(
            'advanced_payment_api' => (bool)Configuration::get('PS_ADVANCED_PAYMENT_API'),
            'free_shipping' => $free_shipping,
            'checkedTOS' => (int)$this->context->cookie->checkedTOS,
            'recyclablePackAllowed' => (int)Configuration::get('PS_RECYCLABLE_PACK'),
            'giftAllowed' => (int)Configuration::get('PS_GIFT_WRAPPING'),
            'cms_id' => (int)Configuration::get('PS_CONDITIONS_CMS_ID'),
            'conditions' => (int)Configuration::get('PS_CONDITIONS'),
            'link_conditions' => $link_conditions,
            'recyclable' => (int)$this->context->cart->recyclable,
            'gift_wrapping_price' => (float)$wrapping_fees,
            'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, $this->context->currency),
            'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, $this->context->currency),
            'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
            'carriers' => $carriers,
            'checked' => $this->context->cart->simulateCarrierSelectedOutput(),
            'delivery_option' => $delivery_option,
            'address_collection' => $this->context->cart->getAddressCollection(),
            'opc' => true,
            'oldMessage' => isset($old_message['message'])? $old_message['message'] : '',
            'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
                'carriers' => $carriers,
                'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
                'delivery_option' => $delivery_option
            ))
        );

        Cart::addExtraCarriers($vars);

        $this->context->smarty->assign($vars);

        if (!Address::isCountryActiveById((int)$this->context->cart->id_address_delivery) && $this->context->cart->id_address_delivery != 0) {
            $this->errors[] = Tools::displayError('This address is not in a valid area.');
        } elseif ((!Validate::isLoadedObject($address_delivery) || $address_delivery->deleted) && $this->context->cart->id_address_delivery != 0) {
            $this->errors[] = Tools::displayError('This address is invalid.');
        } else {
            $result = array(
                'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
                    'carriers' => $carriers,
                    'delivery_option_list' => $this->context->cart->getDeliveryOptionList(),
                    'delivery_option' => $this->context->cart->getDeliveryOption(null, true)
                )),
                'carrier_block' => $this->context->smarty->fetch(_PS_THEME_DIR_.'order-carrier.tpl')
            );

            Cart::addExtraCarriers($result);
            return $result;
        }
        if (count($this->errors)) {
            return array(
                'hasError' => true,
                'errors' => $this->errors,
                'carrier_block' => $this->context->smarty->fetch(_PS_THEME_DIR_.'order-carrier.tpl')
            );
        }
    }

    protected function _processAddressFormat()
    {
        $address_delivery = new Address((int)$this->context->cart->id_address_delivery);
        $address_invoice = new Address((int)$this->context->cart->id_address_invoice);

        $inv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_delivery->id_country, false, true);
        $dlv_adr_fields = AddressFormat::getOrderedAddressFields((int)$address_invoice->id_country, false, true);
        $require_form_fields_list = AddressFormat::getFieldsRequired();

        // Add missing require fields for a new user susbscription form
        foreach ($require_form_fields_list as $field_name) {
            if (!in_array($field_name, $dlv_adr_fields)) {
                $dlv_adr_fields[] = trim($field_name);
            }
        }

        foreach ($require_form_fields_list as $field_name) {
            if (!in_array($field_name, $inv_adr_fields)) {
                $inv_adr_fields[] = trim($field_name);
            }
        }

        $inv_all_fields = array();
        $dlv_all_fields = array();

        foreach (array('inv', 'dlv') as $adr_type) {
            foreach (${$adr_type.'_adr_fields'} as $fields_line) {
                foreach (explode(' ', $fields_line) as $field_item) {
                    ${$adr_type.'_all_fields'}[] = trim($field_item);
                }
            }

            ${$adr_type.'_adr_fields'} = array_unique(${$adr_type.'_adr_fields'});
            ${$adr_type.'_all_fields'} = array_unique(${$adr_type.'_all_fields'});

            $this->context->smarty->assign(
                array(
                    $adr_type.'_adr_fields' => ${$adr_type.'_adr_fields'},
                    $adr_type.'_all_fields' => ${$adr_type.'_all_fields'},
                    'required_fields' => $require_form_fields_list
                )
            );
        }
    }

    protected function getFormatedSummaryDetail()
    {
        $result = array('summary' => $this->context->cart->getSummaryDetails(),
                        'customizedDatas' => Product::getAllCustomizedDatas($this->context->cart->id, null, true));

        foreach ($result['summary']['products'] as $key => &$product) {
            $product['quantity_without_customization'] = $product['quantity'];
            if ($result['customizedDatas']) {
                if (isset($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']])) {
                    foreach ($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']] as $addresses) {
                        foreach ($addresses as $customization) {
                            $product['quantity_without_customization'] -= (int)$customization['quantity'];
                        }
                    }
                }
            }
        }

        if ($result['customizedDatas']) {
            Product::addCustomizationPrice($result['summary']['products'], $result['customizedDatas']);
        }
        return $result;
    }

    public function getRoomTypeBookingServices()
    {
        $response = array('reload' => true);
        if ($idProduct = Tools::getValue('id_product')) {
            if (($dateFrom = Tools::getValue('date_from'))
                && ($dateTo = Tools::getValue('date_to'))
            ) {
                $objCartBookingData = new HotelCartBookingData();
                if ($selectedRoomDemands = $objCartBookingData->getCartExtraDemands(
                    $this->context->cart->id,
                    $idProduct,
                    0,
                    $dateFrom,
                    $dateTo
                )) {
                    // get room type additional demands
                    $objRoomDemands = new HotelRoomTypeDemand();
                    if ($roomTypeDemands = $objRoomDemands->getRoomTypeDemands($idProduct)) {
                        foreach ($roomTypeDemands as &$demand) {
                            // if demand has advance options then set demand price as first advance option price.
                            if (isset($demand['adv_option']) && $demand['adv_option']) {
                                $demand['price'] = current($demand['adv_option'])['price'];
                            }
                        }
                        foreach ($selectedRoomDemands as &$selectedDemand) {
                            if (isset($selectedDemand['extra_demands']) && $selectedDemand['extra_demands']) {
                                $extraDmd = array();
                                foreach ($selectedDemand['extra_demands'] as $sDemand) {
                                    $selectedDemand['selected_global_demands'][] = $sDemand['id_global_demand'];
                                    $extraDmd[$sDemand['id_global_demand'].'-'.$sDemand['id_option']] = $sDemand;
                                }
                                $selectedDemand['extra_demands'] = $extraDmd;
                            }
                        }
                        $this->context->smarty->assign('roomTypeDemands', $roomTypeDemands);
                        $this->context->smarty->assign('selectedRoomDemands', $selectedRoomDemands);

                    }
                }
                $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
                $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                $roomTypeServiceProducts = $objRoomTypeServiceProduct->getServiceProductsData($idProduct, 1, 0, true, 1);
                $cartRooms = $objCartBookingData->getHotelCartRoomsInfoByRoomType($this->context->cart->id, $idProduct,$dateFrom, $dateTo);
                foreach($cartRooms as &$room) {
                    $room['selected_service'] = $objRoomTypeServiceProductCartDetail->getRoomServiceProducts(
                        $room['id'],
                        0,
                        null,
                        null
                    );
                }
                $this->context->smarty->assign(array(
                    'roomTypeServiceProducts' => $roomTypeServiceProducts,
                    'cartRooms' => $cartRooms
                ));
            }
        }
        $response['extra_demands'] = $this->context->smarty->fetch(
            _PS_THEME_DIR_.'_partials/cart_booking_demands.tpl'
        );

        return json_encode($response);
    }

    public function changeRoomDemands()
    {
        if ($idCartBooking = Tools::getValue('id_cart_booking')) {
            if (Validate::isLoadedObject($objCartbookingCata = new HotelCartBookingData($idCartBooking))) {
                $roomDemands = Tools::getValue('room_demands');
                $roomDemands = json_decode($roomDemands, true);
                $roomDemands = json_encode($roomDemands);
                $objCartbookingCata->extra_demands = $roomDemands;
                if ($objCartbookingCata->save()) {
                    die('1');
                }
            }
        }
        die('0');
    }

    public function submitCustomerGuestDetail()
    {
        $customerGuestDetail = Tools::getValue('customer_guest_detail');
        $this->context->cookie->__set('customer_details_proceeded', 0);
        $this->context->cookie->checkedTOS = false;
        if ($customerGuestDetail) {
            if ($id_customer_guest_detail = CartCustomerGuestDetail::getCartCustomerGuest($this->context->cart->id)) {
                $objCustomerGuestDetail = new CartCustomerGuestDetail($id_customer_guest_detail);
            } else {
                $objCustomerGuestDetail = new CartCustomerGuestDetail();
            }

            $customerGuestDetailGender = Tools::getValue('customer_guest_detail_gender');
            $customerGuestDetailFirstname = Tools::getValue('customer_guest_detail_firstname');
            $customerGuestDetailLastname = Tools::getValue('customer_guest_detail_lastname');
            $customerGuestDetailEmail = Tools::getValue('customer_guest_detail_email');
            $customerGuestDetailPhone = Tools::getValue('customer_guest_detail_phone');
            if (trim($customerGuestDetailGender) && Validate::isUnsignedInt($customerGuestDetailGender)) {
                $objCustomerGuestDetail->id_gender = $customerGuestDetailGender;
            }
            if (trim($customerGuestDetailFirstname) && Validate::isName($customerGuestDetailFirstname)) {
                $objCustomerGuestDetail->firstname = $customerGuestDetailFirstname;
            }
            if (trim($customerGuestDetailLastname) && Validate::isName($customerGuestDetailLastname)) {
                $objCustomerGuestDetail->lastname = $customerGuestDetailLastname;
            }
            if (trim($customerGuestDetailEmail) && Validate::isEmail($customerGuestDetailEmail)) {
                $objCustomerGuestDetail->email = $customerGuestDetailEmail;
            }
            if (trim($customerGuestDetailPhone) && Validate::isPhoneNumber($customerGuestDetailPhone)) {
                $objCustomerGuestDetail->phone = $customerGuestDetailPhone;
            }
            $objCustomerGuestDetail->id_cart = $this->context->cart->id;
            $objCustomerGuestDetail->save();
        } else {
            if ($id_customer_guest_detail = CartCustomerGuestDetail::getCartCustomerGuest($this->context->cart->id)) {
                if (Validate::isLoadedObject($objCustomerGuestDetail = new CartCustomerGuestDetail($id_customer_guest_detail))) {
                    $objCustomerGuestDetail->delete();
                }
            }
        }
        $this->context->cart->save();
    }
}
