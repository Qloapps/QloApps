<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class blockcart extends Module
{
    public function __construct()
    {
        $this->name = 'blockcart';
        $this->tab = 'front_office_features';
        $this->version = '1.6.4';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Cart block');
        $this->description = $this->l('Adds a block containing the customer\'s shopping cart.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function getContentVars($params)
    {
        global $errors;

        // Set currency
        if ((int) $params['cart']->id_currency && (int) $params['cart']->id_currency != $this->context->currency->id) {
            $currency = new Currency((int) $params['cart']->id_currency);
        } else {
            $currency = $this->context->currency;
        }

        $taxCalculationMethod = Group::getPriceDisplayMethod((int) Group::getCurrent()->id);

        $useTax = !($taxCalculationMethod == PS_TAX_EXC);
        $showTax = (int) (Configuration::get('PS_TAX_DISPLAY') == 1 && (int) Configuration::get('PS_TAX'));

        $products = $params['cart']->getProducts(true);

        $totalRooms = 0;
        if ($htlCartData = HotelCartBookingData::getHotelCartBookingData(0)) {
            foreach ($htlCartData as $roomTypeCart) {
                $totalRooms += $roomTypeCart['total_num_rooms'];
            }
        }

        $priceDisplayMethod = Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer);
        $nbTotalProducts = 0;
        foreach ($products as &$product) {
            $product['id'] = $product['id_product'];
            $product['link'] = $this->context->link->getProductLink(
                $product['id_product'],
                $product['link_rewrite'],
                $product['category'],
                null,
                null,
                $product['id_shop'],
                $product['id_product_attribute']
            );
            $product['image'] = $this->context->link->getImageLink(
                $product['link_rewrite'],
                $product['id_image'],
                'home_default'
            );
            $product['image_cart'] = $this->context->link->getImageLink(
                $product['link_rewrite'],
                $product['id_image'],
                'cart_default'
            );
            if ($priceDisplayMethod == PS_TAX_EXC) {
                $product['priceByLine'] = Tools::displayPrice($product['total']);
                $product['price'] = Tools::displayPrice($product['total']);
                $product['total_product_price'] = $product['total'];
            } else {
                $product['priceByLine'] = Tools::displayPrice($product['total_wt']);
                $product['price'] = Tools::displayPrice($product['total_wt']);
                $product['total_product_price'] = $product['total_wt'];
            }
            $product['price_float'] = $product['total'];
            $product['idCombination'] = isset($product['attributes_small']) ? $product['attributes_small'] : 0;
            $product['idAddressDelivery'] = isset($product['id_address_delivery']) ? $product['id_address_delivery'] : 0;
            $product['is_gift'] = (isset($product['is_gift']) && $product['is_gift'] )? true : false;
            $product['hasCustomizedDatas'] = false;
            $product['hasAttributes'] = false;
            $nbTotalProducts += (int) $product['cart_quantity'];
        }
        $cart_rules = $params['cart']->getCartRules();

        if (empty($cart_rules)) {
            $base_shipping = $params['cart']->getOrderTotal($useTax, Cart::ONLY_SHIPPING);
        } else {
            $base_shipping_with_tax = $params['cart']->getOrderTotal(true, Cart::ONLY_SHIPPING);
            $base_shipping_without_tax = $params['cart']->getOrderTotal(false, Cart::ONLY_SHIPPING);
            if ($useTax) {
                $base_shipping = $base_shipping_with_tax;
            } else {
                $base_shipping = $base_shipping_without_tax;
            }
        }
        $shipping_cost = Tools::displayPrice($base_shipping, $currency);
        $shipping_cost_float = Tools::convertPrice($base_shipping, $currency);
        $wrappingCost = (float) ($params['cart']->getOrderTotal($useTax, Cart::ONLY_WRAPPING));
        $totalToPay = $params['cart']->getOrderTotal($useTax);
        $tax_cost = 0;
        if ($showTax) {
            $totalToPayWithoutTaxes = $params['cart']->getOrderTotal(false);
            $tax_cost = Tools::displayPrice($totalToPay - $totalToPayWithoutTaxes, $currency);
        }

        // The cart content is altered for display
        $orderProcess = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order';
        foreach ($cart_rules as &$cart_rule) {
            if ($cart_rule['free_shipping']) {
                $shipping_cost = Tools::displayPrice(0, $currency);
                $shipping_cost_float = 0;
                $cart_rule['value_real'] -= Tools::convertPrice($base_shipping_with_tax, $currency);
                $cart_rule['value_tax_exc'] = Tools::convertPrice($base_shipping_without_tax, $currency);
            }
            if ($cart_rule['gift_product']) {
                foreach ($products as $key => &$product) {
                    if ($product['id_product'] == $cart_rule['gift_product']
                        && $product['id_product_attribute'] == $cart_rule['gift_product_attribute']) {
                        $product['total_wt'] = Tools::ps_round($product['total_wt'] - $product['price_wt'], (int) $currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                        $product['total'] = Tools::ps_round($product['total'] - $product['price'], (int) $currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                        if ($product['cart_quantity'] > 1) {
                            array_splice($products, $key, 0, array($product));
                            $products[$key]['cart_quantity'] = $product['cart_quantity'] - 1;
                            $product['cart_quantity'] = 1;
                        }
                        $product['is_gift'] = 1;
                        $cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'] - $product['price_wt'], (int) $currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                        $cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'] - $product['price'], (int) $currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
                    }
                }
            }
            $cart_rule['id'] = $cart_rule['id_discount'];
            $cart_rule['link'] = $this->context->link->getPageLink($orderProcess, true, NULL, "deleteDiscount=".$cart_rule['id_discount']);
            if ($priceDisplayMethod == PS_TAX_EXC) {
                $cart_rule['price'] = Tools::displayPrice($cart_rule['value_tax_exc']);
                $cart_rule['price_float'] = $cart_rule['value_tax_exc'];
            } else {
                $cart_rule['price'] = Tools::displayPrice($cart_rule['value_real']);
                $cart_rule['price_float'] = $cart_rule['value_real'];
            }

        }

        $total_free_shipping = 0;
        if ($free_shipping = Tools::convertPrice(floatval(Configuration::get('PS_SHIPPING_FREE_PRICE')), $currency)) {
            $total_free_shipping = floatval($free_shipping - ($params['cart']->getOrderTotal(true, Cart::ONLY_PRODUCTS) +
                $params['cart']->getOrderTotal(true, Cart::ONLY_DISCOUNTS)));
            $discounts = $params['cart']->getCartRules(CartRule::FILTER_ACTION_SHIPPING);
            if ($total_free_shipping < 0) {
                $total_free_shipping = 0;
            }
            if (is_array($discounts) && count($discounts)) {
                $total_free_shipping = 0;
            }
        }

        $objCartBookingData = new HotelCartBookingData();
        $totalDemandsPrice = $objCartBookingData->getCartExtraDemands(
            $this->context->cart->id,
            0,
            0,
            0,
            0,
            1
        );

        $response = array(
            'products' => $products,
            'customizedDatas' => Product::getAllCustomizedDatas((int) ($params['cart']->id)),
            'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
            'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
            'discounts' => $cart_rules,
            'nb_total_products' => (int) ($nbTotalProducts),
            'shipping_cost' => $shipping_cost,
            'shipping_cost_float' => $shipping_cost_float,
            'show_wrapping' => $wrappingCost > 0 ? true : false,
            'show_tax' => $showTax,
            'tax_cost' => $tax_cost,
            'wrapping_cost' => Tools::displayPrice($wrappingCost, $currency),
            'product_total' => Tools::displayPrice($params['cart']->getOrderTotal($useTax, Cart::ONLY_PRODUCTS), $currency),
            'totalToPay' => $totalToPay,
            'total_extra_demands' => $totalDemandsPrice,
            'total_extra_demands_format' => Tools::displayPrice($totalDemandsPrice, $currency),
            'total' => Tools::displayPrice($totalToPay, $currency),
            'order_process' => $orderProcess,
            'ajax_allowed' => (int) (Configuration::get('PS_BLOCK_CART_AJAX')) == 1 ? true : false,
            'static_token' => Tools::getToken(false),
            'free_shipping' => Tools::displayPrice($total_free_shipping),
            'free_shipping_float' => $total_free_shipping,

            'cart_booking_data' => $htlCartData,
            'total_rooms_in_cart' => $totalRooms,
        );

        if (isset($params['cookie']->avail_rooms)) {
            $response['avail_rooms'] = $params['cookie']->avail_rooms;
            unset($this->context->cookie->avail_rooms);
        }

        $response['hasError'] = false;
        if (is_array($errors) && count($errors)) {
            $response['hasError'] = true;
            $response['errors'] = $errors;
        }

        if (isset($this->context->cookie->ajax_blockcart_display)) {
            $response['colapseExpandStatus'] =  $this->context->cookie->ajax_blockcart_display;
        }

        return $response;
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitBlockCart')) {
            $ajax = Tools::getValue('PS_BLOCK_CART_AJAX');
            if ($ajax != 0 && $ajax != 1) {
                $output .= $this->displayError($this->l('Ajax: Invalid choice.'));
            } else {
                Configuration::updateValue('PS_BLOCK_CART_AJAX', (int) ($ajax));
            }

            if (($productNbr = (int) Tools::getValue('PS_BLOCK_CART_XSELL_LIMIT') < 0)) {
                $output .= $this->displayError($this->l('Please complete the "Products to display" field.'));
            } else {
                Configuration::updateValue('PS_BLOCK_CART_XSELL_LIMIT', (int) (Tools::getValue('PS_BLOCK_CART_XSELL_LIMIT')));
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }

            Configuration::updateValue('PS_BLOCK_CART_SHOW_CROSSSELLING', (int) (Tools::getValue('PS_BLOCK_CART_SHOW_CROSSSELLING')));
        }

        return $output.$this->renderForm();
    }

    public function install()
    {
        if (parent::install() == false
            || $this->registerHook('top') == false
            || $this->registerHook('header') == false
            || $this->registerHook('actionCartListOverride') == false
            || Configuration::updateValue('PS_BLOCK_CART_AJAX', 1) == false
            || Configuration::updateValue('PS_BLOCK_CART_XSELL_LIMIT', 12) == false
            || Configuration::updateValue('PS_BLOCK_CART_SHOW_CROSSSELLING', 1) == false) {
            return false;
        }

        return true;
    }

    public function hookAjaxCall($params)
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return;
        }

        $res = $this->getContentVars($params);

        if (is_array($res) && ($id_product = Tools::getValue('id_product')) && Configuration::get('PS_BLOCK_CART_SHOW_CROSSSELLING')) {
            $this->smarty->assign('orderProducts', OrderDetail::getCrossSells($id_product, $this->context->language->id, Configuration::get('PS_BLOCK_CART_XSELL_LIMIT')));
            $res['crossSelling'] = $this->display(__FILE__, 'crossselling.tpl');
        }

        return json_encode($res);
    }

    public function hookActionCartListOverride($params)
    {
        if (!Configuration::get('PS_BLOCK_CART_AJAX')) {
            return;
        }

        $res = $this->getContentVars(array('cookie' => $this->context->cookie, 'cart' => $this->context->cart));
        $params['json'] = json_encode($res);
    }

    public function hookHeader()
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return;
        }

        $this->context->controller->addCSS(($this->_path).'blockcart.css', 'all');
        if ((int) (Configuration::get('PS_BLOCK_CART_AJAX'))) {
            $this->context->controller->addJS(($this->_path).'ajax-cart.js');
            $this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll', 'bxslider'));
        }
    }

    public function hookTop($params)
    {
        $params['blockcart_top'] = true;

        if (Configuration::get('PS_CATALOG_MODE')) {
            return;
        }

        $warning_num = Configuration::get('WK_ROOM_LEFT_WARNING_NUMBER');

        /*Max date of ordering for order restrict*/
        $current_page = Dispatcher::getInstance()->getController();
        $max_order_date = 0;
        if ($current_page == 'product') {
            $id_product = Tools::getValue('id_product');
            $obj_hotel_room_type = new HotelRoomType();
            $room_info_by_product_id = $obj_hotel_room_type->getRoomTypeInfoByIdProduct($id_product);
            $hotel_id = $room_info_by_product_id['id_hotel'];
            if ($hotel_id) {
                $max_order_date = HotelOrderRestrictDate::getMaxOrderDate($hotel_id);
            }
        } elseif ($current_page == 'category') {
            $htl_id_category = Tools::getValue('id_category');
            $hotel_id = HotelBranchInformation::getHotelIdByIdCategory($htl_id_category);
            if ($hotel_id) {
                $max_order_date = HotelOrderRestrictDate::getMaxOrderDate($hotel_id);
            }
        }
        /*End*/

        // @todo this variable seems not used
        $this->smarty->assign(array(
            'max_order_date' => $max_order_date,
            'warning_num' => $warning_num,
            'module_dir' => _MODULE_DIR_,
            'current_page' => $current_page,
            'order_page' => (strpos($_SERVER['PHP_SELF'], 'order') !== false),
            'blockcart_top' => (isset($params['blockcart_top']) && $params['blockcart_top']) ? true : false,
        ));
        $res = $this->getContentVars($params);

        $this->context->smarty->assign($res);

        return $this->display(__FILE__, 'blockcart.tpl');
    }

    public function hookDisplayNav($params)
    {
        return $this->hookTop($params);
    }

    public function hookDisplayTopSubSecondaryBlock($params)
    {
        return $this->hookTop($params);
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Ajax cart'),
                        'name' => 'PS_BLOCK_CART_AJAX',
                        'is_bool' => true,
                        'desc' => $this->l('Activate Ajax mode for the cart (compatible with the default theme).'),
                        'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show cross-selling'),
                        'name' => 'PS_BLOCK_CART_SHOW_CROSSSELLING',
                        'is_bool' => true,
                        'desc' => $this->l('Activate cross-selling display for the cart.'),
                        'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled'),
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled'),
                                ),
                            ),
                        ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Products to display in cross-selling'),
                        'name' => 'PS_BLOCK_CART_XSELL_LIMIT',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Define the number of products to be displayed in the cross-selling block.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBlockCart';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab
        .'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'PS_BLOCK_CART_AJAX' => (bool) Tools::getValue('PS_BLOCK_CART_AJAX', Configuration::get('PS_BLOCK_CART_AJAX')),
            'PS_BLOCK_CART_SHOW_CROSSSELLING' => (bool) Tools::getValue('PS_BLOCK_CART_SHOW_CROSSSELLING', Configuration::get('PS_BLOCK_CART_SHOW_CROSSSELLING')),
            'PS_BLOCK_CART_XSELL_LIMIT' => (int) Tools::getValue('PS_BLOCK_CART_XSELL_LIMIT', Configuration::get('PS_BLOCK_CART_XSELL_LIMIT')),
        );
    }
}
