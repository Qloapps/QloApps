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
 * @property Cart $object
 */
class AdminCartsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'cart';
        $this->className = 'Cart';
        $this->lang = false;
        $this->explicitSelect = true;

        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->allow_export = true;
        $this->_orderWay = 'DESC';
        $this->context = Context::getContext();
        $abandonedCartText = $this->l('Abandoned cart');

        $this->_select = 'CONCAT(c.`firstname`, \' \', c.`lastname`) `customer`, a.id_cart total,
        TIME_TO_SEC(TIMEDIFF(\''.pSQL(date('Y-m-d H:i:00', time())).'\', a.`date_add`)) AS `time_diff`,
        IFNULL(GROUP_CONCAT(o.`id_order`), 0) AS `ids_order`,
        IF (IFNULL(o.id_order, \''.$this->l('Non ordered cart').'\') = \''.$this->l('Non ordered cart').'\', IF(TIME_TO_SEC(TIMEDIFF(\''.pSQL(date('Y-m-d H:i:00', time())).'\', a.`date_add`)) > 86400, \''.$abandonedCartText.'\', \''.$this->l('Non ordered cart').'\'), GROUP_CONCAT(o.`id_order`)) AS filter_ids_order,
		IF(o.id_order, 1, 0) badge_success, IF(o.id_order, 0, 1) badge_danger, IF(co.id_guest, 1, 0) id_guest';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = a.id_customer)
		LEFT JOIN '._DB_PREFIX_.'currency cu ON (cu.id_currency = a.id_currency)
		LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_cart = a.id_cart)
		LEFT JOIN `'._DB_PREFIX_.'connections` co ON (a.id_guest = co.id_guest AND TIME_TO_SEC(TIMEDIFF(\''.pSQL(date('Y-m-d H:i:00', time())).'\', co.`date_add`)) < 1800)';
        $this->_group = ' GROUP BY a.`id_cart`';

        $this->fields_list = array(
            'id_cart' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'ids_order' => array(
                'title' => $this->l('Order ID'),
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'filter_ids_order',
                'callback' => 'getOrderColumn',
            ),
            'customer' => array(
                'title' => $this->l('Customer'),
                'havingFilter' => true,
            ),
            'total' => array(
                'title' => $this->l('Total'),
                'callback' => 'getOrderTotalUsingTaxCalculationMethod',
                'orderby' => false,
                'search' => false,
                'align' => 'text-right',
                'badge_success' => true
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'text-left',
                'type' => 'datetime',
                'class' => 'fixed-width-lg',
                'filter_key' => 'a!date_add'
            ),
            'id_guest' => array(
                'title' => $this->l('Online'),
                'align' => 'text-center',
                'type' => 'bool',
                'havingFilter' => true,
                'class' => 'fixed-width-xs',
                'icon' => array(0 => 'icon-', 1 => 'icon-user')
            )
        );
        $this->shopLinkType = 'shop';

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        // START send access query information to the admin controller
        $this->access_select = ' SELECT a.`id_cart` FROM '._DB_PREFIX_.'cart a';
        $this->access_join = ' INNER JOIN '._DB_PREFIX_.'htl_cart_booking_data hcb ON (hcb.id_cart = a.id_cart)';
        if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1)) {
            $this->access_where = ' WHERE hcb.id_hotel IN ('.implode(',', $acsHtls).')';
        }

        parent::__construct();
        $this->list_no_link = true;
        if (Tools::getValue('action') == 'filterOnlyAbandonedCarts') {
            // Resetting the previously selected filters if selected from the KPI
            $this->processResetFilters();
            $dateFrom = date('Y-m-d', strtotime('-2 day'));
            $dateTo = date('Y-m-d', strtotime('-1 day'));
            $_POST[$this->table.'Filter_'.$this->fields_list['date_add']['filter_key']] = '["'.$dateFrom.'", "'.$dateTo.'"]';
            $_POST[$this->table.'Filter_filter_ids_order'] = $abandonedCartText;
            $_POST['submitFilter'.$this->table] = true;
        }

    }

    public function getOrderColumn($idsOrder, $tr)
    {
        $smartyVars = array();
        if ($idsOrder) {
            $idsOrder = explode(',', $idsOrder);
            $smartyVars['type'] = 'orders';
            $smartyVars['ids_order'] = $idsOrder;
        } else {
            if ($tr['time_diff'] > 86400) {
                $smartyVars['type'] = 'abandoned';
            } else {
                $smartyVars['type'] = 'non_orderd';
            }
        }
        $smartyVars['link'] = $this->context->link;

        $tpl = $this->createTemplate('_orders.tpl');
        $tpl->assign($smartyVars);

        return $tpl->fetch();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['export_cart'] = array(
                'href' => self::$currentIndex.'&exportcart&token='.$this->token,
                'desc' => $this->l('Export carts', null, null, false),
                'icon' => 'process-icon-export'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function renderKpis()
    {
        $time = time();
        $kpis = array();

        $daysForConversionRate = Configuration::get('PS_KPI_CONVERSION_RATE_NB_DAYS');

        $helper = new HelperKpi();
        $helper->id = 'box-conversion-rate';
        $helper->icon = 'icon-sort-by-attributes-alt';
        //$helper->chart = true;
        $helper->color = 'color1';
        $helper->title = $this->l('Conversion Rate', null, null, false);
        $helper->subtitle = $daysForConversionRate.' '.$this->l('days', null, null, false);
        if (ConfigurationKPI::get('CONVERSION_RATE_CHART') !== false) {
            $helper->data = ConfigurationKPI::get('CONVERSION_RATE_CHART');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=conversion_rate';
        $kpis[] = $helper;

        $helper = new HelperKpi();
        $helper->id = 'box-carts';
        $helper->icon = 'icon-shopping-cart';
        $helper->color = 'color2';
        $helper->title = $this->l('Abandoned Carts', null, null, false);
        $date_from = date(Context::getContext()->language->date_format_lite, strtotime('-2 day'));
        $date_to = date(Context::getContext()->language->date_format_lite, strtotime('-1 day'));
        $helper->subtitle = sprintf($this->l('From %s to %s', null, null, false), $date_from, $date_to);
        $helper->href = $this->context->link->getAdminLink('AdminCarts').'&action=filterOnlyAbandonedCarts';
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=abandoned_cart';
        $kpis[] = $helper;

        $daysForAvgOrderVal = Configuration::get('PS_ORDER_KPI_AVG_ORDER_VALUE_NB_DAYS');
        $helper = new HelperKpi();
        $helper->id = 'box-average-order';
        $helper->icon = 'icon-money';
        $helper->color = 'color3';
        $helper->title = $this->l('Average Order Value', null, null, false);
        $helper->subtitle = $daysForAvgOrderVal.' '.$this->l('days', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=average_order_value';
        $kpis[] = $helper;

        $daysForProfitPerVisitor = Configuration::get('PS_ORDER_KPI_PER_VISITOR_PROFIT_NB_DAYS');
        $helper = new HelperKpi();
        $helper->id = 'box-net-profit-visitor';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $this->l('Net Profit per Visitor', null, null, false);
        $helper->subtitle = $daysForProfitPerVisitor.' '.$this->l('days', null, null, false);
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=netprofit_visit';
        $kpis[] = $helper;

        Hook::exec('action'.$this->controller_name.'KPIListingModifier', array(
            'kpis' => &$kpis,
        ));

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;
        return $helper->generate();
    }


    public function renderView()
    {
        /** @var Cart $cart */
        if (!($cart = $this->loadObject(true))) {
            return;
        }
        $customer = new Customer($cart->id_customer);
        $currency = new Currency($cart->id_currency);
        $this->context->cart = $cart;
        $this->context->currency = $currency;
        $this->context->customer = $customer;
        $this->toolbar_title = sprintf($this->l('Cart #%06d'), $this->context->cart->id);
        $products = $cart->getProducts();
        $customized_datas = Product::getAllCustomizedDatas((int)$cart->id);
        Product::addCustomizationPrice($products, $customized_datas);
        $summary = $cart->getSummaryDetails();

        /* Display orders information */
        $cartOrders = Order::getAllOrdersByCartId($cart->id);
        if ($cartOrders) {
            $objOrder = new Order($cartOrders[0]['id_order']);
            if (Validate::isLoadedObject($objOrder)) {
                $tax_calculation_method = $objOrder->getTaxCalculationMethod();
                $id_shop = (int)$objOrder->id_shop;
            }
        } else {
            $id_shop = (int)$cart->id_shop;
            $tax_calculation_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        }

        if ($tax_calculation_method == PS_TAX_EXC) {
            $total_products = $summary['total_products'];
            $total_discounts = $summary['total_discounts_tax_exc'];
            $total_wrapping = $summary['total_wrapping_tax_exc'];
            $total_price = $summary['total_price_without_tax'];
            $total_shipping = $summary['total_shipping_tax_exc'];
        } else {
            $total_products = $summary['total_products_wt'];
            $total_discounts = $summary['total_discounts'];
            $total_wrapping = $summary['total_wrapping'];
            $total_price = $summary['total_price'];
            $total_shipping = $summary['total_shipping'];
        }
        foreach ($products as $k => &$product) {
            if ($tax_calculation_method == PS_TAX_EXC) {
                $product['product_price'] = $product['price'];
                $product['product_total'] = $product['total'];
            } else {
                $product['product_price'] = $product['price_wt'];
                $product['product_total'] = $product['total_wt'];
            }
            $image = array();
            if (isset($product['id_product_attribute']) && (int)$product['id_product_attribute']) {
                $image = Db::getInstance()->getRow('SELECT id_image FROM '._DB_PREFIX_.'product_attribute_image WHERE id_product_attribute = '.(int)$product['id_product_attribute']);
            }
            if (!isset($image['id_image'])) {
                $image = Db::getInstance()->getRow('SELECT id_image FROM '._DB_PREFIX_.'image WHERE id_product = '.(int)$product['id_product'].' AND cover = 1');
            }

            $product['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], isset($product['id_product_attribute']) ? $product['id_product_attribute'] : null, (int)$id_shop);

            if (isset($image['id_image'])) {
                $image_product = new Image($image['id_image']);
            }
            $product['image'] = (isset($image['id_image']) ? ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$image_product->getExistingImgPath().'.jpg', 'product_mini_'.(int)$product['id_product'].(isset($product['id_product_attribute']) ? '_'.(int)$product['id_product_attribute'] : '').'.jpg', 45, 'jpg') : '--');
        }

        $helper = new HelperKpi();
        $helper->id = 'box-kpi-cart';
        $helper->icon = 'icon-shopping-cart';
        $helper->color = 'color1';
        $helper->title = $this->l('Total Cart', null, null, false);
        $helper->subtitle = sprintf($this->l('Cart #%06d', null, null, false), $cart->id);
        $helper->value = Tools::displayPrice($total_price, $currency);
        $kpi = $helper->generate();
        // by webkul to show rooms available in the cart
        $cartHtlData = array();
        $objHotelCartBookingData = new HotelCartBookingData();
        $objHotelRoomType = new HotelRoomType();
        if ($cartHtlData = $objHotelCartBookingData->getCartFormatedBookinInfoByIdCart((int) $cart->id)) {
            foreach ($cartHtlData as $key => $value) {
                $cartHtlData[$key]['room_type_info'] = $objHotelRoomType->getRoomTypeInfoByIdProduct($value['id_product']);
            }
        } else {
            $cartHtlData = array();
        }
        //end
        $this->tpl_view_vars = array(
            'cart_htl_data' => $cartHtlData,//by webkul hotel rooms in order data
            'kpi' => $kpi,
            'products' => $products,
            'discounts' => $cart->getCartRules(),
            'cart_orders' => $cartOrders,
            'cart' => $cart,
            'currency' => $currency,
            'customer' => $customer,
            'customer_stats' => $customer->getStats(),
            'total_products' => $total_products,
            'total_discounts' => $total_discounts,
            'total_wrapping' => $total_wrapping,
            'total_price' => $total_price,
            'total_shipping' => $total_shipping,
            'customized_datas' => $customized_datas,
            'tax_calculation_method' => $tax_calculation_method
        );

        return parent::renderView();
    }

    ##################################################################
        #FUNCTION EDITED BY WEBKUL FOR SETTING CUSTOMER DATA IN THE CART.
    ##################################################################
    public function ajaxPreProcess()
    {
        if ($this->tabAccess['edit'] === '1') {
            // prevent cart creation when kpi visibility or kpi view is updated.
            // @todo: move the below cart creation process required function.
            if (in_array(Tools::getValue('action'), array('changeKpiVisibility', 'saveKpiView'))) {
                return;
            }
            $id_customer = (int)Tools::getValue('id_customer');
            $customer = new Customer((int)$id_customer);
            $this->context->customer = $customer;
            $id_cart = (int)Tools::getValue('id_cart');
            if (!$id_cart) {
                $id_cart = $customer->getLastCart(false);
            }
            $this->context->cart = new Cart((int)$id_cart);

            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);

            if (!$this->context->cart->id) {
                $this->context->cart->recyclable = 0;
                $this->context->cart->gift = 0;
            }
            /*if (!$this->context->cart->id_customer)
                $this->context->cart->id_customer = $id_customer;*/
            if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->OrderExists()) {
                return;
            }
            if (!$this->context->cart->secure_key) {
                $this->context->cart->secure_key = $this->context->customer->secure_key;
            }
            if (!$this->context->cart->id_shop) {
                $this->context->cart->id_shop = (int)$this->context->shop->id;
            }
            if (!$this->context->cart->id_lang) {
                $this->context->cart->id_lang = (($id_lang = (int)Tools::getValue('id_lang')) ? $id_lang : Configuration::get('PS_LANG_DEFAULT'));
            }
            if (!$this->context->cart->id_currency) {
                $this->context->cart->id_currency = (($id_currency = (int)Tools::getValue('id_currency')) ? $id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
            }

            $addresses = $customer->getAddresses((int)$this->context->cart->id_lang);
            $id_address_delivery = (int)Tools::getValue('id_address_delivery');
            $id_address_invoice = (int)Tools::getValue('id_address_invoice');

            if (!$this->context->cart->id_address_invoice && isset($addresses[0])) {
                $this->context->cart->id_address_invoice = (int)$addresses[0]['id_address'];
            } elseif ($id_address_invoice) {
                $this->context->cart->id_address_invoice = (int)$id_address_delivery;
            }
            if (!$this->context->cart->id_address_delivery && isset($addresses[0])) {
                $this->context->cart->id_address_delivery = $addresses[0]['id_address'];
            } elseif ($id_address_delivery) {
                $this->context->cart->id_address_delivery = (int)$id_address_delivery;
            }
            $this->context->cart->setNoMultishipping();

            /*Code ADDED By webkul*/
            if ($this->context->cart->id_customer != $id_customer) {
                $this->context->cart->id_customer = $id_customer;
                $this->context->cart->secure_key = $this->context->customer->secure_key;
                $addresses = $customer->getAddresses((int)$this->context->cart->id_lang);
                $this->context->cart->id_address_invoice = (int)$addresses[0]['id_address'];
                $this->context->cart->id_address_delivery = (int)$addresses[0]['id_address'];
                $this->context->cart->setNoMultishipping();
            }
            /*END*/

            $this->context->cart->save();
            $currency = new Currency((int)$this->context->cart->id_currency);
            $this->context->currency = $currency;
        }
    }

    public function ajaxProcessDeleteProduct()
    {
        if ($this->tabAccess['edit'] === '1') {
            $errors = array();
            if ((!$id_product = (int)Tools::getValue('id_product')) || !Validate::isInt($id_product)) {
                $errors[] = Tools::displayError('Invalid product');
            }
            if (($id_product_attribute = (int)Tools::getValue('id_product_attribute')) && !Validate::isInt($id_product_attribute)) {
                $errors[] = Tools::displayError('Invalid combination');
            }
            if (count($errors)) {
                die(json_encode($errors));
            }
            if ($this->context->cart->deleteProduct($id_product, $id_product_attribute, (int)Tools::getValue('id_customization'))) {
                echo json_encode($this->ajaxReturnVars());
            }
        }
    }

    public function ajaxProcessUpdateCustomizationFields()
    {
        $errors = array();
        if ($this->tabAccess['edit'] === '1') {
            $errors = array();
            if (Tools::getValue('only_display') != 1) {
                if (!$this->context->cart->id || (!$id_product = (int)Tools::getValue('id_product'))) {
                    return;
                }
                $product = new Product((int)$id_product);
                if (!$customization_fields = $product->getCustomizationFieldIds()) {
                    return;
                }
                foreach ($customization_fields as $customization_field) {
                    $field_id = 'customization_'.$id_product.'_'.$customization_field['id_customization_field'];
                    if ($customization_field['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                        if (!Tools::getValue($field_id)) {
                            if ($customization_field['required']) {
                                $errors[] = Tools::displayError('Please fill in all the required fields.');
                            }
                            continue;
                        }
                        if (!Validate::isMessage(Tools::getValue($field_id))) {
                            $errors[] = Tools::displayError('Invalid message');
                        }
                        $this->context->cart->addTextFieldToProduct((int)$product->id, (int)$customization_field['id_customization_field'], Product::CUSTOMIZE_TEXTFIELD, Tools::getValue($field_id));
                    } elseif ($customization_field['type'] == Product::CUSTOMIZE_FILE) {
                        if (!isset($_FILES[$field_id]) || !isset($_FILES[$field_id]['tmp_name']) || empty($_FILES[$field_id]['tmp_name'])) {
                            if ($customization_field['required']) {
                                $errors[] = Tools::displayError('Please fill in all the required fields.');
                            }
                            continue;
                        }
                        if ($error = ImageManager::validateUpload($_FILES[$field_id], (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE'))) {
                            $errors[] = $error;
                        }
                        if (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES[$field_id]['tmp_name'], $tmp_name)) {
                            $errors[] = Tools::displayError('An error occurred during the image upload process.');
                        }
                        $file_name = md5(uniqid(rand(), true));
                        if (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name)) {
                            continue;
                        } elseif (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name.'_small', (int)Configuration::get('PS_PRODUCT_PICTURE_WIDTH'), (int)Configuration::get('PS_PRODUCT_PICTURE_HEIGHT'))) {
                            $errors[] = Tools::displayError('An error occurred during the image upload process.');
                        } elseif (!chmod(_PS_UPLOAD_DIR_.$file_name, 0777) || !chmod(_PS_UPLOAD_DIR_.$file_name.'_small', 0777)) {
                            $errors[] = Tools::displayError('An error occurred during the image upload process.');
                        } else {
                            $this->context->cart->addPictureToProduct((int)$product->id, (int)$customization_field['id_customization_field'], Product::CUSTOMIZE_FILE, $file_name);
                        }
                        unlink($tmp_name);
                    }
                }
            }
            $this->setMedia();
            $this->initFooter();
            $this->context->smarty->assign(array('customization_errors' => implode('<br />', $errors),
                                                            'css_files' => $this->css_files));
            return $this->smartyOutputContent('controllers/orders/form_customization_feedback.tpl');
        }
    }

    public function ajaxProcessUpdateQty()
    {
        if ($this->tabAccess['edit'] === '1') {
            $errors = array();
            if (!$this->context->cart->id) {
                return;
            }
            if ($this->context->cart->OrderExists()) {
                $errors[] = Tools::displayError('An order has already been placed with this cart.');
            } elseif (!($id_product = (int)Tools::getValue('id_product')) || !($product = new Product((int)$id_product, true, $this->context->language->id))) {
                $errors[] = Tools::displayError('Invalid product');
            } elseif (!($qty = Tools::getValue('qty')) || $qty == 0) {
                $errors[] = Tools::displayError('Invalid quantity');
            }

            // Don't try to use a product if not instanciated before due to errors
            if (isset($product) && $product->id) {
                if (($id_product_attribute = Tools::getValue('id_product_attribute')) != 0) {
                    if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty((int)$id_product_attribute, (int)$qty)) {
                        $errors[] = Tools::displayError('There is not enough product in stock.');
                    }
                } elseif (!$product->checkQty((int)$qty)) {
                    $errors[] = Tools::displayError('There is not enough product in stock.');
                }
                if (!($id_customization = (int)Tools::getValue('id_customization', 0)) && !$product->hasAllRequiredCustomizableFields()) {
                    $errors[] = Tools::displayError('Please fill in all the required fields.');
                }
                $this->context->cart->save();
            } else {
                $errors[] = Tools::displayError('This product cannot be added to the cart.');
            }

            if (!count($errors)) {
                if ((int)$qty < 0) {
                    $qty = str_replace('-', '', $qty);
                    $operator = 'down';
                } else {
                    $operator = 'up';
                }

                if (!($qty_upd = $this->context->cart->updateQty($qty, $id_product, (int)$id_product_attribute, (int)$id_customization, $operator))) {
                    $errors[] = Tools::displayError('You already have the maximum quantity available for this product.');
                } elseif ($qty_upd < 0) {
                    $minimal_qty = $id_product_attribute ? Attribute::getAttributeMinimalQty((int)$id_product_attribute) : $product->minimal_quantity;
                    $errors[] = sprintf(Tools::displayError('You must add a minimum quantity of %d', false), $minimal_qty);
                }
            }

            echo json_encode(array_merge($this->ajaxReturnVars(), array('errors' => $errors)));
        }
    }

    public function ajaxProcessUpdateDeliveryOption()
    {
        if ($this->tabAccess['edit'] === '1') {
            $delivery_option = Tools::getValue('delivery_option');
            if ($delivery_option !== false) {
                $this->context->cart->setDeliveryOption(array($this->context->cart->id_address_delivery => $delivery_option));
            }
            if (Validate::isBool(($recyclable = (int)Tools::getValue('recyclable')))) {
                $this->context->cart->recyclable = $recyclable;
            }
            if (Validate::isBool(($gift = (int)Tools::getValue('gift')))) {
                $this->context->cart->gift = $gift;
            }
            if (Validate::isMessage(($gift_message = pSQL(Tools::getValue('gift_message'))))) {
                $this->context->cart->gift_message = $gift_message;
            }
            $this->context->cart->save();
            echo json_encode($this->ajaxReturnVars());
        }
    }

    public function ajaxProcessUpdateOrderMessage()
    {
        if ($this->tabAccess['edit'] === '1') {
            $id_message = false;
            if ($old_message = Message::getMessageByCartId((int)$this->context->cart->id)) {
                $id_message = $old_message['id_message'];
            }
            $message = new Message((int)$id_message);
            if ($message_content = Tools::getValue('message')) {
                if (Validate::isMessage($message_content)) {
                    $message->message = $message_content;
                    $message->id_cart = (int)$this->context->cart->id;
                    $message->id_customer = (int)$this->context->cart->id_customer;
                    $message->save();
                }
            } elseif (Validate::isLoadedObject($message)) {
                $message->delete();
            }
            echo json_encode($this->ajaxReturnVars());
        }
    }

    public function ajaxProcessUpdateCurrency()
    {
        if ($this->tabAccess['edit'] === '1') {
            $currency = new Currency((int)Tools::getValue('id_currency'));
            if (Validate::isLoadedObject($currency) && !$currency->deleted && $currency->active) {
                $this->context->cart->id_currency = (int)$currency->id;
                $this->context->currency = $currency;
                $this->context->cart->save();
            }
            #################################################################
            #Code is added by webkul to change current cart tpl dinamically
            #################################################################
            $id_cart = Tools::getValue('id_cart');//get cart id from url
            $cart_detail_data = array();
            $cart_detail_data_obj = new HotelCartBookingData();
            $cart_detail_data_obj->updateIdCurrencyByIdCart($id_cart, $currency->id);
            $cart_detail_data = $cart_detail_data_obj->getCartFormatedBookinInfoByIdCart((int) $id_cart);
            $this->context->smarty->assign(array(
                'cart_detail_data' => $cart_detail_data,
                'currency' => new Currency((int)$this->context->cart->id_currency),
            ));

            $tpl_path = 'default/template/controllers/orders/_current_cart_details_data.tpl';
            $cart_dtl_tpl = $this->context->smarty->fetch(_PS_BO_ALL_THEMES_DIR_.$tpl_path);

            $result = $this->ajaxReturnVars();
            $result['cart_detail_html'] = $cart_dtl_tpl;//tpl is added to the returned array
            $this->ajaxDie(json_encode($result));
        }
    }
    public function ajaxProcessUpdateLang()
    {
        if ($this->tabAccess['edit'] === '1') {
            $lang = new Language((int)Tools::getValue('id_lang'));
            if (Validate::isLoadedObject($lang) && $lang->active) {
                $this->context->cart->id_lang = (int)$lang->id;
                $this->context->cart->save();
            }
            echo json_encode($this->ajaxReturnVars());
        }
    }

    public function ajaxProcessDuplicateOrder()
    {
        if ($this->tabAccess['edit'] === '1') {
            $errors = array();
            if (!$id_order = Tools::getValue('id_order')) {
                $errors[] = Tools::displayError('Invalid order');
            }
            $cart = Cart::getCartByOrderId($id_order);
            $new_cart = $cart->duplicate();
            if (!$new_cart || !Validate::isLoadedObject($new_cart['cart'])) {
                $errors[] = Tools::displayError('The order cannot be renewed.');
            } elseif (!$new_cart['success']) {
                $errors[] = Tools::displayError('The order cannot be renewed.');
            } else {
                $this->context->cart = $new_cart['cart'];
                echo json_encode($this->ajaxReturnVars());
            }
        }
    }

    public function ajaxProcessDeleteVoucher()
    {
        if ($this->tabAccess['edit'] === '1') {
            if ($this->context->cart->removeCartRule((int)Tools::getValue('id_cart_rule'))) {
                echo json_encode($this->ajaxReturnVars());
            }
        }
    }

    public function ajaxProcessupdateFreeShipping()
    {
        if ($this->tabAccess['edit'] === '1') {
            if (!$id_cart_rule = CartRule::getIdByCode(CartRule::BO_ORDER_CODE_PREFIX.(int)$this->context->cart->id)) {
                $cart_rule = new CartRule();
                $cart_rule->code = CartRule::BO_ORDER_CODE_PREFIX.(int)$this->context->cart->id;
                $cart_rule->name = array(Configuration::get('PS_LANG_DEFAULT') => $this->l('Free Shipping', 'AdminTab', false, false));
                $cart_rule->id_customer = (int)$this->context->cart->id_customer;
                $cart_rule->free_shipping = true;
                $cart_rule->quantity = 1;
                $cart_rule->quantity_per_user = 1;
                $cart_rule->minimum_amount_currency = (int)$this->context->cart->id_currency;
                $cart_rule->reduction_currency = (int)$this->context->cart->id_currency;
                $cart_rule->date_from = date('Y-m-d H:i:s', time());
                $cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 36000);
                $cart_rule->active = 1;
                $cart_rule->add();
            } else {
                $cart_rule = new CartRule((int)$id_cart_rule);
            }

            $this->context->cart->removeCartRule((int)$cart_rule->id);
            if (Tools::getValue('free_shipping')) {
                $this->context->cart->addCartRule((int)$cart_rule->id);
            }

            echo json_encode($this->ajaxReturnVars());
        }
    }

    public function ajaxProcessAddVoucher()
    {
        if ($this->tabAccess['edit'] === '1') {
            $errors = array();
            if (!($id_cart_rule = Tools::getValue('id_cart_rule')) || !$cart_rule = new CartRule((int)$id_cart_rule)) {
                $errors[] = Tools::displayError('Invalid voucher.');
            } elseif ($err = $cart_rule->checkValidity($this->context)) {
                $errors[] = $err;
            }
            if (!count($errors)) {
                if (!$this->context->cart->addCartRule((int)$cart_rule->id)) {
                    $errors[] = Tools::displayError('Can\'t add the voucher.');
                }
            }
            echo json_encode(array_merge($this->ajaxReturnVars(), array('errors' => $errors)));
        }
    }

    public function ajaxProcessUpdateAddress()
    {
        if ($this->tabAccess['edit'] === '1') {
            echo json_encode(array('addresses' => $this->context->customer->getAddresses((int)$this->context->cart->id_lang)));
        }
    }

    public function ajaxProcessUpdateAddresses()
    {
        if ($this->tabAccess['edit'] === '1') {
            if (($id_address_delivery = (int)Tools::getValue('id_address_delivery')) &&
                ($address_delivery = new Address((int)$id_address_delivery)) &&
                $address_delivery->id_customer == $this->context->cart->id_customer) {
                $this->context->cart->id_address_delivery = (int)$address_delivery->id;
            }

            if (($id_address_invoice = (int)Tools::getValue('id_address_invoice')) &&
                ($address_invoice = new Address((int)$id_address_invoice)) &&
                $address_invoice->id_customer = $this->context->cart->id_customer) {
                $this->context->cart->id_address_invoice = (int)$address_delivery->id;
            }//For same id_address_delivery and id_address_invoice By webkul
                /*$this->context->cart->id_address_invoice = (int)$address_invoice->id;*/
            $this->context->cart->save();

            echo json_encode($this->ajaxReturnVars());
        }
    }

    protected function getCartSummary()
    {
        $summary = $this->context->cart->getSummaryDetails(null, true);
        $currency = Context::getContext()->currency;
        if (count($summary['products'])) {
            foreach ($summary['products'] as &$product) {
                $product['numeric_price'] = $product['price'];
                $product['numeric_total'] = $product['total'];
                $product['price'] = str_replace($currency->sign, '', Tools::displayPrice($product['price'], $currency));
                $product['total'] = str_replace($currency->sign, '', Tools::displayPrice($product['total'], $currency));
                $product['image_link'] = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default');
                if (!isset($product['attributes_small'])) {
                    $product['attributes_small'] = '';
                }
                $product['customized_datas'] = Product::getAllCustomizedDatas((int)$this->context->cart->id, null, true);
            }
        }
        if (count($summary['discounts'])) {
            foreach ($summary['discounts'] as &$voucher) {
                $voucher['value_real'] = Tools::displayPrice($voucher['value_real'], $currency);
            }
        }

        if (isset($summary['gift_products']) && count($summary['gift_products'])) {
            foreach ($summary['gift_products'] as &$product) {
                $product['image_link'] = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default');
                if (!isset($product['attributes_small'])) {
                    $product['attributes_small'] = '';
                }
            }
        }


        return $summary;
    }

    protected function getDeliveryOptionList()
    {
        $delivery_option_list_formated = array();
        $delivery_option_list = $this->context->cart->getDeliveryOptionList();

        if (!count($delivery_option_list)) {
            return array();
        }

        $id_default_carrier = (int)Configuration::get('PS_CARRIER_DEFAULT');
        foreach (current($delivery_option_list) as $key => $delivery_option) {
            $name = '';
            $first = true;
            $id_default_carrier_delivery = false;
            foreach ($delivery_option['carrier_list'] as $carrier) {
                if (!$first) {
                    $name .= ', ';
                } else {
                    $first = false;
                }

                $name .= $carrier['instance']->name;

                if ($delivery_option['unique_carrier']) {
                    $name .= ' - '.$carrier['instance']->delay[$this->context->employee->id_lang];
                }

                if (!$id_default_carrier_delivery) {
                    $id_default_carrier_delivery = (int)$carrier['instance']->id;
                }
                if ($carrier['instance']->id == $id_default_carrier) {
                    $id_default_carrier_delivery = $id_default_carrier;
                }
                if (!$this->context->cart->id_carrier) {
                    $this->context->cart->setDeliveryOption(array($this->context->cart->id_address_delivery => (int)$carrier['instance']->id.','));
                    $this->context->cart->save();
                }
            }
            $delivery_option_list_formated[] = array('name' => $name, 'key' => $key);
        }
        return $delivery_option_list_formated;
    }

    public function displayAjaxSearchCarts()
    {
        $id_customer = (int)Tools::getValue('id_customer');
        $customer = new Customer($id_customer);
        $this->context->customer = $customer;

        $carts = Cart::getCustomerCarts((int)$id_customer);
        $orders = Order::getCustomerOrders((int)$id_customer);

        if (count($carts)) {
            foreach ($carts as $key => &$cart) {
                $cart_obj = new Cart((int)$cart['id_cart']);
                if ($cart['id_cart'] == $this->context->cart->id || !Validate::isLoadedObject($cart_obj) || $cart_obj->OrderExists()) {
                    unset($carts[$key]);
                }
                $currency = new Currency((int)$cart['id_currency']);
                $cart['total_price'] = Tools::displayPrice($cart_obj->getOrderTotal(), $currency);
            }
        }
        if (count($orders)) {
            foreach ($orders as &$order) {
                $order['total_paid_real'] = Tools::displayPrice($order['total_paid_real'], $currency);
            }
        }
        if ($orders || $carts) {
            $to_return = array_merge($this->ajaxReturnVars(),
                                            array('carts' => $carts,
                                                     'orders' => $orders,
                                                     'found' => true));
        } else {
            $to_return = array_merge($this->ajaxReturnVars(), array('found' => false));
        }

        echo json_encode($to_return);
    }

    public function ajaxReturnVars()
    {
        $id_cart = (int)$this->context->cart->id;
        $message_content = '';
        if ($message = Message::getMessageByCartId((int)$this->context->cart->id)) {
            $message_content = $message['message'];
        }
        $cart_rules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING);

        $free_shipping = false;
        if (count($cart_rules)) {
            foreach ($cart_rules as $cart_rule) {
                if ($cart_rule['id_cart_rule'] == CartRule::getIdByCode(CartRule::BO_ORDER_CODE_PREFIX.(int)$this->context->cart->id)) {
                    $free_shipping = true;
                    break;
                }
            }
        }

        $cart_detail_data_obj = new HotelCartBookingData();
        $cart_detail_data = $cart_detail_data_obj->getCartFormatedBookinInfoByIdCart((int) $id_cart);
        $is_backdate_order = false;
        $currentDate = strtotime(date('Y-m-d'));
        foreach ($cart_detail_data as $cartRoom) {
            if (strtotime($cartRoom['date_from']) < $currentDate) {
                $is_backdate_order = true;
            }
        }
        $addresses = $this->context->customer->getAddresses((int)$this->context->cart->id_lang);

        foreach ($addresses as &$data) {
            $address = new Address((int)$data['id_address']);
            $data['formated_address'] = AddressFormat::generateAddress($address, array(), "<br />");
        }

        return array(
            'summary' => $this->getCartSummary(),
            'cart_detail_data' => $cart_detail_data,
            'is_backdate_order' => $is_backdate_order,
            'delivery_option_list' => $this->getDeliveryOptionList(),
            'cart' => $this->context->cart,
            'currency' => new Currency($this->context->cart->id_currency),
            'addresses' => $addresses,
            'id_cart' => $id_cart,
            'order_message' => $message_content,
            'link_order' => $this->context->link->getPageLink(
                'order', false,
                (int)$this->context->cart->id_lang,
                'step=3&recover_cart='.$id_cart.'&token_cart='.md5(_COOKIE_KEY_.'recover_cart_'.$id_cart)
            ),
            'free_shipping' => (int)$free_shipping
        );
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function displayAjaxGetSummary()
    {
        echo json_encode($this->ajaxReturnVars());
    }

    public function ajaxProcessUpdateProductPrice()
    {
        $params = Tools::getValue('params');
        $id_booking_data = (int) $params['id_booking_data'];
        $id_cart = (int) $params['id_cart'];
        $id_product = (int) $params['id_product'];
        $id_room = (int) $params['id_room'];
        $date_from = $params['date_from'];
        $date_to = $params['date_to'];
        $price = (float) $params['price'];

        $this->context->cart = new Cart($id_cart);

        $date_from = date('Y-m-d', strtotime($date_from));
        $date_to = date('Y-m-d', strtotime($date_to));

        if ($this->tabAccess['edit'] === '1') {
            HotelRoomTypeFeaturePricing::deleteByIdCart($id_cart, $id_product, $id_room, $date_from, $date_to);
            $feature_price_name = array();
            foreach (Language::getIDs(true) as $id_lang) {
                $feature_price_name[$id_lang] = 'Auto-generated';
            }

            $hrt_feature_price = new HotelRoomTypeFeaturePricing();
            $hrt_feature_price->id_product = $id_product;
            $hrt_feature_price->id_cart = $id_cart;
            $hrt_feature_price->id_guest = (int) $this->context->cookie->id_guest;
            $hrt_feature_price->id_room = $id_room;
            $hrt_feature_price->feature_price_name = $feature_price_name;
            $hrt_feature_price->date_selection_type = HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE;
            $hrt_feature_price->date_from = $date_from;
            $hrt_feature_price->date_to = $date_to;
            $hrt_feature_price->is_special_days_exists = 0;
            $hrt_feature_price->special_days = json_encode(false);
            $hrt_feature_price->impact_way = HotelRoomTypeFeaturePricing::IMPACT_WAY_FIX_PRICE;
            $hrt_feature_price->impact_type = HotelRoomTypeFeaturePricing::IMPACT_TYPE_FIXED_PRICE;
            $hrt_feature_price->impact_value = $price;
            $hrt_feature_price->active = 1;
            $hrt_feature_price->groupBox = array_column(Group::getGroups($this->context->language->id), 'id_group');
            $hrt_feature_price->add();

            $objHotelCartBookingData = new HotelCartBookingData();
            $bookingsInfo = $objHotelCartBookingData->getCartFormatedBookinInfoByIdCart($id_cart);
            foreach ($bookingsInfo as &$bookingInfo) {
                if ($bookingInfo['id'] == $id_booking_data) {
                    $amt_with_qty = $bookingInfo['amt_with_qty'];
                    $bookingInfo['amt_with_qty'] = Tools::displayPrice($amt_with_qty);
                    $bookingInfo['total_price'] = Tools::displayPrice($amt_with_qty + $bookingInfo['demand_price'] + $bookingInfo['additional_service_price']);
                    $response = array(
                        'curr_booking_info' => $bookingInfo,
                        'cart_info' => $this->ajaxReturnVars(),
                    );

                    die(json_encode($response));
                }
            }
        }
    }

    public function ajaxProcessupdateRoomOccupancy()
    {
        $params = Tools::getValue('params');
        $idBookingData = (int) $params['id_booking_data'];
        $idCart = (int) $params['id_cart'];
        $occupancy =  $params['occupancy'];

        $this->context->cart = new Cart($idCart);

        if ($this->tabAccess['edit'] === '1') {
            // validate occupancy is correct
            $hasError = false;
            if (is_array($occupancy)) {
                if (!$occupancy['adults'] || !Validate::isUnsignedInt($occupancy['adults'])) {
                    $hasError = true;
                }
                if (!Validate::isUnsignedInt($occupancy['children'])) {
                    $hasError = true;
                }
                if ($occupancy['children']) {
                    if (!isset($occupancy['child_ages']) || $occupancy['children'] != count($occupancy['child_ages'])) {
                        $hasError = true;
                    } else {
                        foreach($occupancy['child_ages'] as $age) {
                            if (!Validate::isUnsignedInt($occupancy['adults'])) {
                                $hasError = true;
                            }
                        }
                    }
                }
            } else {
                $hasError = true;
            }

            if (!$hasError) {
                $objHotelCartBookingData = new HotelCartBookingData($idBookingData);
                $objRoomType = new HotelRoomType();
                if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($objHotelCartBookingData->id_product)) {
                    if ($occupancy['adults'] > $roomTypeInfo['max_adults']) {
                        $hasError = true;
                    }
                    if ($occupancy['children'] > $roomTypeInfo['max_children']) {
                        $hasError = true;
                    }
                    if ($occupancy['adults'] + $occupancy['children'] > $roomTypeInfo['max_guests']) {
                        $hasError = true;
                    }
                    if (!$hasError) {
                        $objHotelCartBookingData->adults = $occupancy['adults'];
                        $objHotelCartBookingData->children = $occupancy['children'];
                        $objHotelCartBookingData->child_ages = json_encode($occupancy['child_ages']);
                        $objHotelCartBookingData->save();
                    }
                }
            }
            $objHotelCartBookingData = new HotelCartBookingData();
            $bookingsInfo = $objHotelCartBookingData->getCartFormatedBookinInfoByIdCart($idCart);
            foreach ($bookingsInfo as &$bookingInfo) {
                if ($bookingInfo['id'] == $idBookingData) {
                    $amtWithQty = $bookingInfo['amt_with_qty'];
                    $bookingInfo['amt_with_qty'] = Tools::displayPrice($amtWithQty);
                    $bookingInfo['total_price'] = Tools::displayPrice($amtWithQty + $bookingInfo['demand_price']);
                    $response = array(
                        'curr_booking_info' => $bookingInfo,
                        'cart_info' => $this->ajaxReturnVars(),
                    );

                    die(json_encode($response));
                }
            }
        }
    }

    // Process to get extra demands of any room while order creation process form.tpl
    public function ajaxProcessGetRoomTypeCartDemands()
    {
        $response = array('status' => false);
        if ($idProduct = Tools::getValue('id_product')) {
            if (($dateFrom = Tools::getValue('date_from'))
                && ($dateTo = Tools::getValue('date_to'))
                && ($idRoom = Tools::getValue('id_room'))
                && ($idCart = Tools::getValue('id_cart'))
            ) {
                $objCartBookingData = new HotelCartBookingData();
                if ($selectedRoomDemands = $objCartBookingData->getCartExtraDemands(
                    $idCart,
                    $idProduct,
                    $idRoom,
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
                            $objRoom = new HotelRoomInformation($selectedDemand['id_room']);
                            $selectedDemand['room_num'] = $objRoom->room_num;
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
                $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
                $roomTypeServiceProducts = $objRoomTypeServiceProduct->getServiceProductsData($idProduct, 1, 0, false, 2, null);
                if ($selectedRoomServiceProduct =  $objCartBookingData->getRoomRowByIdProductIdRoomInDateRange(
                    $idCart,
                    $idProduct,
                    $dateFrom,
                    $dateTo,
                    $idRoom
                )) {
                    $selectedRoomServiceProduct['selected_service'] = $objRoomTypeServiceProductCartDetail->getRoomServiceProducts(
                        $selectedRoomServiceProduct['id'],
                        0,
                        null,
                        null
                    );
                }
                $this->context->smarty->assign(array(
                    'roomTypeServiceProducts' => $roomTypeServiceProducts,
                    'selectedRoomServiceProduct' => $selectedRoomServiceProduct
                ));
                $htlCartBoookingata =  $objCartBookingData->getRoomRowByIdProductIdRoomInDateRange(
                    $idCart,
                    $idProduct,
                    $dateFrom,
                    $dateTo,
                    $idRoom
                );
            }
        }
        $response['status'] = true;
        $response['html_exta_demands'] = $this->context->smarty->fetch(
            _PS_ADMIN_DIR_.'/themes/default/template/controllers/orders/_cart_booking_demands.tpl'
        );
        $this->ajaxDie(json_encode($response));
    }

    // Process when admin changes extra demands of any room while order creation process form.tpl
    public function ajaxProcessChangeRoomDemands()
    {
        $response = array('status' => false);
        if ($idCartBooking = Tools::getValue('id_cart_booking')) {
            if (Validate::isLoadedObject($objCartbookingCata = new HotelCartBookingData($idCartBooking))) {
                $roomDemands = Tools::getValue('room_demands');
                $roomDemands = json_decode($roomDemands, true);
                $roomDemands = json_encode($roomDemands);
                $objCartbookingCata->extra_demands = $roomDemands;
                if ($objCartbookingCata->save()) {
                    $response['status'] = true;
                }
            }
        }
        $this->ajaxDie(json_encode($response));
    }

    public static function getOrderTotalUsingTaxCalculationMethod($id_cart)
    {
        $context = Context::getContext();
        $context->cart = new Cart($id_cart);
        $context->currency = new Currency((int)$context->cart->id_currency);
        $context->customer = new Customer((int)$context->cart->id_customer);
        return Cart::getTotalCart($id_cart, true, Cart::BOTH_WITHOUT_SHIPPING);
    }

    public function displayDeleteLink($token = null, $id, $name = null)
    {
        // don't display ordered carts
        foreach ($this->_list as $row) {
            if ($row['id_cart'] == $id && isset($row['id_order']) && is_numeric($row['id_order'])) {
                return ;
            }
        }

        return $this->helper->displayDeleteLink($token, $id, $name);
    }

    public function renderList()
    {
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->l('Bad SQL query', 'Helper').'<br />'.htmlspecialchars($this->_list_error));
            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper->is_cms = $this->is_cms;
        $skip_list = array();

        foreach ($this->_list as $row) {
            if (isset($row['ids_order']) && $row['ids_order'] != '0') {
                $skip_list[] = $row['id_cart'];
            }
        }

        if (array_key_exists('delete', $helper->list_skip_actions)) {
            $helper->list_skip_actions['delete'] = array_merge($helper->list_skip_actions['delete'], (array)$skip_list);
        } else {
            $helper->list_skip_actions['delete'] = (array)$skip_list;
        }

        $list = $helper->generateList($this->_list, $this->fields_list);
        return $list;
    }
}
