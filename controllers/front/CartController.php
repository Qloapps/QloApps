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

class CartControllerCore extends FrontController
{
    public $php_self = 'cart';

    protected $id_product;
    protected $id_product_attribute;
    protected $id_address_delivery;
    protected $customization_id;
    protected $qty;
    public $ssl = true;

    protected $ajax_refresh = false;

    /**
     * This is not a public page, so the canonical redirection is disabled
     *
     * @param string $canonicalURL
     */
    public function canonicalRedirection($canonicalURL = '')
    {
    }

    /**
     * Initialize cart controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        // Send noindex to avoid ghost carts by bots
        header('X-Robots-Tag: noindex, nofollow', true);

        // Get page main parameters
        $this->id_product = (int)Tools::getValue('id_product', null);
        $this->id_product_attribute = (int)Tools::getValue('id_product_attribute', Tools::getValue('ipa'));
        $this->customization_id = (int)Tools::getValue('id_customization');
        $this->qty = abs(Tools::getValue('qty', 1));
        $this->id_address_delivery = (int)Tools::getValue('id_address_delivery');
    }

    public function postProcess()
    {
        // Update the cart ONLY if $this->cookies are available, in order to avoid ghost carts created by bots
        if ($this->context->cookie->exists() && !$this->errors && !($this->context->customer->isLogged() && !$this->isTokenValid())) {
            if (Tools::getIsset('add') || Tools::getIsset('update')) {
                $this->processChangeProductInCart();
                CheckoutProcess::refreshCheckoutProcess();
            } elseif (Tools::getIsset('delete')) {
                $this->processDeleteProductInCart();
                CheckoutProcess::refreshCheckoutProcess();
            } elseif (Tools::getIsset('changeAddressDelivery')) {
                $this->processChangeProductAddressDelivery();
                CheckoutProcess::refreshCheckoutProcess();
            } elseif (Tools::getIsset('allowSeperatedPackage')) {
                $this->processAllowSeperatedPackage();
                CheckoutProcess::refreshCheckoutProcess();
            } elseif (Tools::getIsset('duplicate')) {
                $this->processDuplicateProduct();
                CheckoutProcess::refreshCheckoutProcess();
            }
            // Make redirection
            if (!$this->errors && !$this->ajax) {
                $queryString = Tools::safeOutput(Tools::getValue('query', null));
                if ($queryString && !Configuration::get('PS_CART_REDIRECT')) {
                    Tools::redirect('index.php?controller=search&search='.$queryString);
                }

                // Redirect to previous page
                if (isset($_SERVER['HTTP_REFERER'])) {
                    preg_match('!http(s?)://(.*)/(.*)!', $_SERVER['HTTP_REFERER'], $regs);
                    if (isset($regs[3]) && !Configuration::get('PS_CART_REDIRECT')) {
                        $url = preg_replace('/(\?)+content_only=1/', '', $_SERVER['HTTP_REFERER']);
                        Tools::redirect($url);
                    }
                }

                Tools::redirect('index.php?controller=order&'.(isset($this->id_product) ? 'ipa='.$this->id_product : ''));
            }
        } elseif (!$this->isTokenValid()) {
            if (Tools::getValue('ajax')) {
                $this->ajaxDie(Tools::jsonEncode(array(
                    'hasError' => true,
                    'errors' => array(Tools::displayError('Impossible to add the product to the cart. Please refresh page.')),
                )));
            } else {
                Tools::redirect('index.php');
            }
        }
    }

    /**
     * This process delete a product from the cart
     */
    protected function processDeleteProductInCart()
    {
        $customization_product = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'customization`
		WHERE `id_cart` = '.(int)$this->context->cart->id.' AND `id_product` = '.(int)$this->id_product.' AND `id_customization` != '.(int)$this->customization_id);

        if (count($customization_product)) {
            $product = new Product((int)$this->id_product);
            if ($this->id_product_attribute > 0) {
                $minimal_quantity = (int)Attribute::getAttributeMinimalQty($this->id_product_attribute);
            } else {
                $minimal_quantity = (int)$product->minimal_quantity;
            }

            $total_quantity = 0;
            foreach ($customization_product as $custom) {
                $total_quantity += $custom['quantity'];
            }

            if ($total_quantity < $minimal_quantity) {
                $this->ajaxDie(Tools::jsonEncode(array(
                        'hasError' => true,
                        'errors' => array(sprintf(Tools::displayError('You must add %d minimum quantity', !Tools::getValue('ajax')), $minimal_quantity)),
                )));
            }
        }

        if ($this->context->cart->deleteProduct($this->id_product, $this->id_product_attribute, $this->customization_id, $this->id_address_delivery)) {
            Hook::exec('actionAfterDeleteProductInCart', array(
                'id_cart' => (int)$this->context->cart->id,
                'id_product' => (int)$this->id_product,
                'id_product_attribute' => (int)$this->id_product_attribute,
                'customization_id' => (int)$this->customization_id,
                'id_address_delivery' => (int)$this->id_address_delivery
            ));

            if (!Cart::getNbProducts((int)$this->context->cart->id)) {
                $this->context->cart->setDeliveryOption(null);
                $this->context->cart->gift = 0;
                $this->context->cart->gift_message = '';
                $this->context->cart->update();
            }
        }

        if (Module::isInstalled('hotelreservationsystem')) {
            require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';

            $id_cart = $this->context->cart->id;
            $id_product = (int) $this->id_product;
            $date_from = Tools::getValue('dateFrom');
            $date_to = Tools::getValue('dateTo');
            $date_from = date("Y-m-d", strtotime($date_from));
            $date_to = date("Y-m-d", strtotime($date_to));
            $objRoomType = new HotelRoomType();
            $objCartBooking = new HotelCartBookingData();
            // delete booking data from hotel booking table(do not delete from ps cart here)
            $result = $objCartBooking->deleteCartBookingData($id_cart, $id_product, 0, 0, 0, 0);
            if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($id_product)) {
                if ($id_hotel = $roomTypeInfo['id_hotel']) {
                    $obj_booking_dtl = new HotelBookingDetail();
                    if ($hotel_room_data = $obj_booking_dtl->DataForFrontSearch(
                        $date_from,
                        $date_to,
                        $id_hotel,
                        $id_product,
                        1
                    )) {
                        $total_available_rooms = $hotel_room_data['stats']['num_avail'];
                    }
                }
            }
            if ($result) {
                $this->context->cookie->avail_rooms = $total_available_rooms;
            }
        }

        $removed = CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
        if (count($removed) && (int)Tools::getValue('allow_refresh')) {
            $this->ajax_refresh = true;
        }
    }

    protected function processChangeProductAddressDelivery()
    {
        if (!Configuration::get('PS_ALLOW_MULTISHIPPING')) {
            return;
        }

        $old_id_address_delivery = (int)Tools::getValue('old_id_address_delivery');
        $new_id_address_delivery = (int)Tools::getValue('new_id_address_delivery');

        if (!count(Carrier::getAvailableCarrierList(new Product($this->id_product), null, $new_id_address_delivery))) {
            $this->ajaxDie(Tools::jsonEncode(array(
                'hasErrors' => true,
                'error' => Tools::displayError('It is not possible to deliver this product to the selected address.', false),
            )));
        }

        $this->context->cart->setProductAddressDelivery(
            $this->id_product,
            $this->id_product_attribute,
            $old_id_address_delivery,
            $new_id_address_delivery);
    }

    protected function processAllowSeperatedPackage()
    {
        if (!Configuration::get('PS_SHIP_WHEN_AVAILABLE')) {
            return;
        }

        if (Tools::getValue('value') === false) {
            $this->ajaxDie('{"error":true, "error_message": "No value setted"}');
        }

        $this->context->cart->allow_seperated_package = (bool)Tools::getValue('value');
        $this->context->cart->update();
        $this->ajaxDie('{"error":false}');
    }

    protected function processDuplicateProduct()
    {
        if (!Configuration::get('PS_ALLOW_MULTISHIPPING')) {
            return;
        }

        if (!$this->context->cart->duplicateProduct(
                $this->id_product,
                $this->id_product_attribute,
                $this->id_address_delivery,
                (int)Tools::getValue('new_id_address_delivery')
            )) {
            //$error_message = $this->l('Error durring product duplication');
            // For the moment no translations
            $error_message = 'Error durring product duplication';
        }
    }

    /**
     * This process add or update a product in the cart
     */
    protected function processChangeProductInCart()
    {
        $mode = (Tools::getIsset('update') && $this->id_product) ? 'update' : 'add';
        $date_from = Tools::getValue('dateFrom');
        $date_to = Tools::getValue('dateTo');
        $date_from = date("Y-m-d", strtotime($date_from));
        $date_to = date("Y-m-d", strtotime($date_to));
        $id_cart = $this->context->cart->id;
        $id_guest = $this->context->cart->id_guest;

        /*
        *   By Webkul
        *   This code is to check available quantity of Room before adding it to cart.
        */
        if (Module::isInstalled('hotelreservationsystem') && Module::isEnabled('hotelreservationsystem')) {
            require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';

            $obj_booking_detail = new HotelBookingDetail();
            $num_days = $obj_booking_detail->getNumberOfDays($date_from, $date_to);

            $req_rm = $this->qty;
            $this->qty = $this->qty * (int) $num_days;
            $obj_room_type = new HotelRoomType();
            $room_info_by_id_product = $obj_room_type->getRoomTypeInfoByIdProduct($this->id_product);

            if ($room_info_by_id_product) {
                $id_hotel = $room_info_by_id_product['id_hotel'];

                if ($id_hotel) {
                    /*Check Order restrict condition before adding in to cart*/
                    $max_order_date = HotelOrderRestrictDate::getMaxOrderDate($id_hotel);
                    if ($max_order_date) {
                        $max_order_date = date('Y-m-d', strtotime($max_order_date));
                        if ($max_order_date < $date_from || $max_order_date < $date_to) {
                            $this->errors[] = Tools::displayError('You can\'t Book room after date '.$max_order_date);
                        }
                    }
                    /*END*/
                    $obj_booking_dtl = new HotelBookingDetail();
                    $hotel_room_data = $obj_booking_dtl->DataForFrontSearch($date_from, $date_to, $id_hotel, $this->id_product, 1, 0, 0, -1, 0, 0, $id_cart, $id_guest);

                    $total_available_rooms = $hotel_room_data['stats']['num_avail'];

                    if ($total_available_rooms < $req_rm) {
                        die(Tools::jsonEncode(array('status' => 'unavailable_quantity', 'avail_rooms' => $total_available_rooms)));
                    }
                } else {
                    die(Tools::jsonEncode(array('status' => 'failed3')));
                }
            } else {
                die(Tools::jsonEncode(array('status' => 'failed4')));
            }
        }

        if ($this->qty == 0) {
            $this->errors[] = Tools::displayError('Null quantity.', !Tools::getValue('ajax'));
        } elseif (!$this->id_product) {
            $this->errors[] = Tools::displayError('Product not found', !Tools::getValue('ajax'));
        }

        $product = new Product($this->id_product, true, $this->context->language->id);
        if (!$product->id || !$product->active || !$product->checkAccess($this->context->cart->id_customer)) {
            $this->errors[] = Tools::displayError('This product is no longer available.', !Tools::getValue('ajax'));
            return;
        }

        $qty_to_check = $this->qty;
        $cart_products = $this->context->cart->getProducts();

        if (is_array($cart_products)) {
            foreach ($cart_products as $cart_product) {
                if ((!isset($this->id_product_attribute) || $cart_product['id_product_attribute'] == $this->id_product_attribute) &&
                    (isset($this->id_product) && $cart_product['id_product'] == $this->id_product)) {
                    $qty_to_check = $cart_product['cart_quantity'];

                    if (Tools::getValue('op', 'up') == 'down') {
                        $qty_to_check -= $this->qty;
                    } else {
                        $qty_to_check += $this->qty;
                    }

                    break;
                }
            }
        }

        // Check product quantity availability
        if ($this->id_product_attribute) {
            if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($this->id_product_attribute, $qty_to_check)) {
                $this->errors[] = Tools::displayError('There isn\'t enough product in stock.', !Tools::getValue('ajax'));
            }
        } elseif ($product->hasAttributes()) {
            $minimumQuantity = ($product->out_of_stock == 2) ? !Configuration::get('PS_ORDER_OUT_OF_STOCK') : !$product->out_of_stock;
            $this->id_product_attribute = Product::getDefaultAttribute($product->id, $minimumQuantity);
            // @todo do something better than a redirect admin !!
            if (!$this->id_product_attribute) {
                Tools::redirectAdmin($this->context->link->getProductLink($product));
            } elseif (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($this->id_product_attribute, $qty_to_check)) {
                $this->errors[] = Tools::displayError('There isn\'t enough product in stock.', !Tools::getValue('ajax'));
            }
        } elseif (!$product->checkQty($qty_to_check)) {
            $this->errors[] = Tools::displayError('There isn\'t enough product in stock.', !Tools::getValue('ajax'));
        }

        // If no errors, process product addition
        if (!$this->errors && $mode == 'add') {
            // Add cart if no cart found
            if (!$this->context->cart->id) {
                if (Context::getContext()->cookie->id_guest) {
                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                }
                $this->context->cart->add();
                if ($this->context->cart->id) {
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }
            }

            // Check customizable fields
            if (!$product->hasAllRequiredCustomizableFields() && !$this->customization_id) {
                $this->errors[] = Tools::displayError('Please fill in all of the required fields, and then save your customizations.', !Tools::getValue('ajax'));
            }

            if (!$this->errors) {
                $cart_rules = $this->context->cart->getCartRules();
                $available_cart_rules = CartRule::getCustomerCartRules($this->context->language->id, (isset($this->context->customer->id) ? $this->context->customer->id : 0), true, true, true, $this->context->cart, false, true);
                $update_quantity = $this->context->cart->updateQty($this->qty, $this->id_product, $this->id_product_attribute, $this->customization_id, Tools::getValue('op', 'up'), $this->id_address_delivery);

                /*------  BY Webkul ------*/
                /*
                * To add Rooms in hotel cart
                */
                $this->availQty = false;
                $id_customer = $this->context->cart->id_customer;
                $id_currency = $this->context->cart->id_currency;

                $hotel_room_info_arr = $hotel_room_data['rm_data'][0]['data']['available'];
                $chkQty = 0;
                foreach ($hotel_room_info_arr as $key_hotel_room_info => $val_hotel_room_info) {
                    if ($chkQty < $req_rm) {
                        $roomDemand = Tools::getValue('roomDemands');
                        $roomDemand = Tools::jsonDecode($roomDemand, true);
                        $roomDemand = Tools::jsonEncode($roomDemand);
                        $obj_htl_cart_booking_data = new HotelCartBookingData();
                        $obj_htl_cart_booking_data->id_cart = $this->context->cart->id;
                        $obj_htl_cart_booking_data->id_guest = $this->context->cart->id_guest;
                        $obj_htl_cart_booking_data->id_customer = $id_customer;
                        $obj_htl_cart_booking_data->id_currency = $id_currency;
                        $obj_htl_cart_booking_data->id_product = $val_hotel_room_info['id_product'];
                        $obj_htl_cart_booking_data->id_room = $val_hotel_room_info['id_room'];
                        $obj_htl_cart_booking_data->id_hotel = $val_hotel_room_info['id_hotel'];
                        $obj_htl_cart_booking_data->booking_type = 1;
                        $obj_htl_cart_booking_data->quantity = $num_days;
                        $obj_htl_cart_booking_data->extra_demands = $roomDemand;
                        $obj_htl_cart_booking_data->date_from = $date_from;
                        $obj_htl_cart_booking_data->date_to = $date_to;
                        $obj_htl_cart_booking_data->save();
                        ++$chkQty;
                    } else {
                        break;
                    }
                }
                $this->availQty = $total_available_rooms - $req_rm;
                $this->context->cookie->avail_rooms = $this->availQty;
                /*------  BY Webkul ------*/

                if ($update_quantity < 0) {
                    // If product has attribute, minimal quantity is set with minimal quantity of attribute
                    $minimal_quantity = ($this->id_product_attribute) ? Attribute::getAttributeMinimalQty($this->id_product_attribute) : $product->minimal_quantity;
                    $this->errors[] = sprintf(Tools::displayError('You must add %d minimum quantity', !Tools::getValue('ajax')), $minimal_quantity);
                } elseif (!$update_quantity) {
                    $this->errors[] = Tools::displayError('You already have the maximum quantity available for this product.', !Tools::getValue('ajax'));
                } elseif ((int)Tools::getValue('allow_refresh')) {
                    // If the cart rules has changed, we need to refresh the whole cart
                    $cart_rules2 = $this->context->cart->getCartRules();
                    if (count($cart_rules2) != count($cart_rules)) {
                        $this->ajax_refresh = true;
                    } elseif (count($cart_rules2)) {
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
                    } else {
                        $available_cart_rules2 = CartRule::getCustomerCartRules($this->context->language->id, (isset($this->context->customer->id) ? $this->context->customer->id : 0), true, true, true, $this->context->cart, false, true);
                        if (count($available_cart_rules2) != count($available_cart_rules)) {
                            $this->ajax_refresh = true;
                        } elseif (count($available_cart_rules2)) {
                            $rule_list = array();
                            foreach ($available_cart_rules2 as $rule) {
                                $rule_list[] = $rule['id_cart_rule'];
                            }
                            foreach ($cart_rules2 as $rule) {
                                if (!in_array($rule['id_cart_rule'], $rule_list)) {
                                    $this->ajax_refresh = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        $removed = CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
        if (count($removed) && (int)Tools::getValue('allow_refresh')) {
            $this->ajax_refresh = true;
        }
    }

    /**
     * Remove discounts on cart
     *
     * @deprecated 1.5.3.0
     */
    protected function processRemoveDiscounts()
    {
        Tools::displayAsDeprecated();
        $this->errors = array_merge($this->errors, CartRule::autoRemoveFromCart());
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->setTemplate(_PS_THEME_DIR_.'errors.tpl');
        if (!$this->ajax) {
            parent::initContent();
        }
    }

    /**
     * Display ajax content (this function is called instead of classic display, in ajax mode)
     */
    public function displayAjax()
    {
        if ($this->errors) {
            $this->ajaxDie(Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors)));
        }
        if ($this->ajax_refresh) {
            $this->ajaxDie(Tools::jsonEncode(array('refresh' => true)));
        }

        // write cookie if can't on destruct
        $this->context->cookie->write();

        if (Tools::getIsset('summary')) {
            $result = array();
            if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
                $groups = (Validate::isLoadedObject($this->context->customer)) ? $this->context->customer->getGroups() : array(1);
                if ($this->context->cart->id_address_delivery) {
                    $deliveryAddress = new Address($this->context->cart->id_address_delivery);
                }
                $id_country = (isset($deliveryAddress) && $deliveryAddress->id) ? (int)$deliveryAddress->id_country : (int)Tools::getCountry();

                Cart::addExtraCarriers($result);
            }
            $result['summary'] = $this->context->cart->getSummaryDetails(null, true);
            $result['customizedDatas'] = Product::getAllCustomizedDatas($this->context->cart->id, null, true);
            $result['HOOK_SHOPPING_CART'] = Hook::exec('displayShoppingCartFooter', $result['summary']);
            $result['HOOK_SHOPPING_CART_EXTRA'] = Hook::exec('displayShoppingCart', $result['summary']);

            foreach ($result['summary']['products'] as $key => &$product) {
                $product['quantity_without_customization'] = $product['quantity'];
                if ($result['customizedDatas'] && isset($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']])) {
                    foreach ($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']] as $addresses) {
                        foreach ($addresses as $customization) {
                            $product['quantity_without_customization'] -= (int)$customization['quantity'];
                        }
                    }
                }
            }
            if ($result['customizedDatas']) {
                Product::addCustomizationPrice($result['summary']['products'], $result['customizedDatas']);
            }

            $json = '';
            Hook::exec('actionCartListOverride', array('summary' => $result, 'json' => &$json));
            $this->ajaxDie(Tools::jsonEncode(array_merge($result, (array)Tools::jsonDecode($json, true))));
        }
        // @todo create a hook
        elseif (file_exists(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php')) {
            require_once(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php');
        }
    }
}
