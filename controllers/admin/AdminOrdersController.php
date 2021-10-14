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

class BoOrder extends PaymentModule
{
    public $active = 1;
    public $name = 'bo_order';

    public function __construct()
    {
        $this->displayName = $this->l('Back office order');
    }
}

/**
 * @property Order $object
 */
class AdminOrdersControllerCore extends AdminController
{
    public $toolbar_title;

    protected $statuses_array = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order';
        $this->className = 'Order';
        $this->lang = false;
        $this->addRowAction('view');
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->deleted = false;
        $this->context = Context::getContext();

        $this->_select = '
        (a.total_paid - a.total_paid_real) AS `amount_due`, a.source AS order_source,
        a.id_currency,
        a.id_order AS id_pdf,
        CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
        osl.`name` AS `osname`,
        os.`color`,
        IF((SELECT so.id_order FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
        country_lang.name as cname,
        IF(a.valid, 1, 0) badge_success';

        $this->_join = '
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
        INNER JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
        INNER JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
        INNER JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.(int) $this->context->language->id.')
        LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
        LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int) $this->context->language->id.')';
        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'reference' => array(
                'title' => $this->l('Reference')
            ),
            'new' => array(
                'title' => $this->l('New client'),
                'align' => 'text-center',
                'type' => 'bool',
                'tmpTableFilter' => true,
                'orderby' => false,
                'callback' => 'printNewCustomer'
            ),
            'customer' => array(
                'title' => $this->l('Customer'),
                'havingFilter' => true,
            ),
            'order_source' => array(
                'title' => $this->l('Order Source'),
                'havingFilter' => true,
            ),
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, array(
                'company' => array(
                    'title' => $this->l('Company'),
                    'filter_key' => 'c!company'
                ),
            ));
        }

        $this->fields_list = array_merge($this->fields_list, array(
            'total_paid_tax_incl' => array(
                'title' => $this->l('Total'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true,
            ),
            'amount_due' => array(
                'title' => $this->l('Due Amount'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true,
            ),
            'payment' => array(
                'title' => $this->l('Payment')
            ),
            'osname' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname'
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'id_pdf' => array(
                'title' => $this->l('PDF'),
                'align' => 'text-center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true
            )
        ));

        if (Country::isCurrentlyUsed('country', true)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT DISTINCT c.id_country, cl.`name`
			FROM `'._DB_PREFIX_.'orders` o
			'.Shop::addSqlAssociation('orders', 'o').'
			INNER JOIN `'._DB_PREFIX_.'address` a ON a.id_address = o.id_address_delivery
			INNER JOIN `'._DB_PREFIX_.'country` c ON a.id_country = c.id_country
			INNER JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
			ORDER BY cl.name ASC');

            $country_array = array();
            foreach ($result as $row) {
                $country_array[$row['id_country']] = $row['name'];
            }

            $part1 = array_slice($this->fields_list, 0, 3);
            $part2 = array_slice($this->fields_list, 3);
            $part1['cname'] = array(
                'title' => $this->l('Delivery'),
                'type' => 'select',
                'list' => $country_array,
                'filter_key' => 'country!id_country',
                'filter_type' => 'int',
                'order_key' => 'cname'
            );
            $this->fields_list = array_merge($part1, $part2);
        }

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_ORDER;

        if (Tools::isSubmit('id_order')) {
            // Save context (in order to apply cart rule)
            $order = new Order((int)Tools::getValue('id_order'));
            $this->context->cart = new Cart($order->id_cart);
            $this->context->customer = new Customer($order->id_customer);
        }

        $this->bulk_actions = array(
            'updateOrderStatus' => array('text' => $this->l('Change Order Status'), 'icon' => 'icon-refresh')
        );

        // START send access query information to the admin controller
        $this->access_select = ' SELECT a.`id_order` FROM '._DB_PREFIX_.'orders a';
        $this->access_join = ' INNER JOIN '._DB_PREFIX_.'htl_booking_detail hbd ON (hbd.id_order = a.id_order)';
        if ($acsHtls = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1)) {
            $this->access_where = ' WHERE hbd.id_hotel IN ('.implode(',', $acsHtls).')';
        }

        parent::__construct();
    }

    public static function setOrderCurrency($echo, $tr)
    {
        $order = new Order($tr['id_order']);
        return Tools::displayPrice($echo, (int)$order->id_currency);
    }

    public function renderForm()
    {
        if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->errors[] = $this->l('You have to select a shop before creating new orders.');
        }

        $id_cart = (int)Tools::getValue('id_cart');
        $cart = new Cart((int)$id_cart);
        if ($id_cart && !Validate::isLoadedObject($cart)) {
            $this->errors[] = $this->l('This cart does not exists');
        }
        if ($id_cart && Validate::isLoadedObject($cart) && !$cart->id_customer) {
            $this->errors[] = $this->l('The cart must have a customer');
        }
        if (count($this->errors)) {
            return false;
        }

        // check page is whether ad new order page or order detail page
        if (isset($_GET['addorder'])) {
            $id_cart = Tools::getValue('cart_id');//get cart id from url
            $cart = new Cart($id_cart);
            $cart_order_exists = $cart->orderExists();
            if (!$cart_order_exists) {
                $this->context->cart = $cart;
                $cart_detail_data = array();
                $cart_detail_data_obj = new HotelCartBookingData();
                if ($cart_detail_data = $cart_detail_data_obj->getCartFormatedBookinInfoByIdCart((int) $id_cart)) {
                    $this->context->smarty->assign('cart_detail_data', $cart_detail_data);
                } else {
                    // if no rooms added in the cart and user visits add order page then redirect to BOOK NOW page
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminHotelRoomsBooking'));
                }
            }
            $this->context->smarty->assign('is_order_created', $cart_order_exists);
        }
        //end
        parent::renderForm();
        unset($this->toolbar_btn['save']);
        $this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));

        $defaults_order_state = array(
            'cheque' => (int)Configuration::get('PS_OS_CHEQUE'),
            'bankwire' => (int)Configuration::get('PS_OS_BANKWIRE'),
            'other' => (int)Configuration::get('PS_OS_PAYMENT'));
        $payment_modules = array();
        foreach (PaymentModule::getInstalledPaymentModules() as $p_module) {
            $payment_modules[] = Module::getInstanceById((int)$p_module['id_module']);
        }

        $this->context->smarty->assign(array(
            'recyclable_pack' => (int)Configuration::get('PS_RECYCLABLE_PACK'),
            'gift_wrapping' => (int)Configuration::get('PS_GIFT_WRAPPING'),
            'cart' => $cart,
            'currencies' => Currency::getCurrenciesByIdShop(Context::getContext()->shop->id),
            'langs' => Language::getLanguages(true, Context::getContext()->shop->id),
            'payment_modules' => $payment_modules,
            'order_states' => OrderState::getOrderStates((int)Context::getContext()->language->id),
            'defaults_order_state' => $defaults_order_state,
            'show_toolbar' => $this->show_toolbar,
            'toolbar_btn' => $this->toolbar_btn,
            'toolbar_scroll' => $this->toolbar_scroll,
            'PS_CATALOG_MODE' => Configuration::get('PS_CATALOG_MODE'),
            'title' => array($this->l('Orders'), $this->l('Create order'))

        ));
        $this->content .= $this->createTemplate('form.tpl')->fetch();
    }

    public function initToolbar()
    {
        if ($this->display == 'view') {
            /** @var Order $order */
            $order = $this->loadObject();
            $customer = $this->context->customer;

            if (!Validate::isLoadedObject($order)) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders'));
            }

            $this->toolbar_title[] = sprintf($this->l('Order %1$s from %2$s %3$s'), $order->reference, $customer->firstname, $customer->lastname);
            $this->addMetaTitle($this->toolbar_title[count($this->toolbar_title) - 1]);

            if ($order->hasBeenShipped()) {
                $type = $this->l('Return products');
            } elseif ($order->hasBeenPaid()) {
                $type = $this->l('Standard refund');
            } else {
                $type = $this->l('Cancel products');
            }

            if (!$order->hasBeenShipped() && !$this->lite_display) {
                $this->toolbar_btn['new'] = array(
                    'short' => 'Create',
                    'href' => '#',
                    'desc' => $this->l('Add a product'),
                    'class' => 'add_product'
                );
            }

            if (Configuration::get('PS_ORDER_RETURN') && !$this->lite_display) {
                $this->toolbar_btn['standard_refund'] = array(
                    'short' => 'Create',
                    'href' => '',
                    'desc' => $type,
                    'class' => 'process-icon-standardRefund'
                );
            }

            if ($order->hasInvoice() && !$this->lite_display) {
                $this->toolbar_btn['partial_refund'] = array(
                    'short' => 'Create',
                    'href' => '',
                    'desc' => $this->l('Partial refund'),
                    'class' => 'process-icon-partialRefund'
                );
            }
        }
        $res = parent::initToolbar();
        if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && isset($this->toolbar_btn['new']) && Shop::isFeatureActive()) {
            unset($this->toolbar_btn['new']);
        }
        return $res;
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJqueryUI('ui.datepicker');
        $this->addJS(_PS_JS_DIR_.'vendor/d3.v3.min.js');
        $api_key = (Configuration::get('PS_API_KEY')) ? 'key=' . Configuration::get('PS_API_KEY') . '&' : '';
        $protocol = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 'https' : 'http';
        $this->addJS($protocol . '://maps.google.com/maps/api/js?' . $api_key);

        if ($this->tabAccess['edit'] == 1 && $this->display == 'view') {
            $this->addJS(_PS_JS_DIR_.'admin/orders.js');
            $this->addJS(_PS_JS_DIR_.'tools.js');
            $this->addJqueryPlugin('autocomplete');
        }
    }

    public function printPDFIcons($id_order, $tr)
    {
        static $valid_order_state = array();

        $order = new Order($id_order);
        if (!Validate::isLoadedObject($order)) {
            return '';
        }

        if (!isset($valid_order_state[$order->current_state])) {
            $valid_order_state[$order->current_state] = Validate::isLoadedObject($order->getCurrentOrderState());
        }

        if (!$valid_order_state[$order->current_state]) {
            return '';
        }

        $this->context->smarty->assign(array(
            'order' => $order,
            'tr' => $tr
        ));

        return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
    }

    public function printNewCustomer($id_order, $tr)
    {
        return ($tr['new'] ? $this->l('Yes') : $this->l('No'));
    }

    public function processBulkUpdateOrderStatus()
    {
        if (Tools::isSubmit('submitUpdateOrderStatus')
            && ($id_order_state = (int)Tools::getValue('id_order_state'))) {
            if ($this->tabAccess['edit'] !== '1') {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            } else {
                $order_state = new OrderState($id_order_state);

                if (!Validate::isLoadedObject($order_state)) {
                    $this->errors[] = sprintf(Tools::displayError('Order status #%d cannot be loaded'), $id_order_state);
                } else {
                    foreach (Tools::getValue('orderBox') as $id_order) {
                        $order = new Order((int)$id_order);
                        if (!Validate::isLoadedObject($order)) {
                            $this->errors[] = sprintf(Tools::displayError('Order #%d cannot be loaded'), $id_order);
                        } else {
                            $current_order_state = $order->getCurrentOrderState();
                            if ($current_order_state->id == $order_state->id) {
                                $this->errors[] = $this->displayWarning(sprintf('Order #%d has already been assigned this status.', $id_order));
                            } else {
                                $history = new OrderHistory();
                                $history->id_order = $order->id;
                                $history->id_employee = (int)$this->context->employee->id;

                                $use_existings_payment = !$order->hasInvoice();
                                $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);

                                $carrier = new Carrier($order->id_carrier, $order->id_lang);
                                $templateVars = array();

                                if ($history->addWithemail(true, $templateVars)) {
                                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                        foreach ($order->getProducts() as $product) {
                                            if (StockAvailable::dependsOnStock($product['product_id'])) {
                                                StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                                            }
                                        }
                                    }
                                } else {
                                    $this->errors[] = sprintf(Tools::displayError('Cannot change status for order #%d.'), $id_order);
                                }
                            }
                        }
                    }
                }
            }
            if (!count($this->errors)) {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            }
        }
    }

    public function renderList()
    {
        if (Tools::isSubmit('submitBulkupdateOrderStatus'.$this->table)) {
            if (Tools::getIsset('cancel')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }

            $this->tpl_list_vars['updateOrderStatus_mode'] = true;
            $this->tpl_list_vars['order_statuses'] = $this->statuses_array;
            $this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $this->tpl_list_vars['POST'] = $_POST;
        }

        return parent::renderList();
    }

    public function postProcess()
    {
        // by webkul for reallocation of rooms
        if (Tools::isSubmit('realloc_allocated_rooms')) {
            if ($this->tabAccess['edit'] === '1') {
                $order_id = Tools::getValue('id_order');
                $current_room_id = Tools::getValue('modal_id_room');
                $current_room = Tools::getValue('modal_curr_room_num');
                $date_from = Tools::getValue('modal_date_from');
                $date_to = Tools::getValue('modal_date_to');
                $realloc_room_id = Tools::getValue('realloc_avail_rooms');

                if ($realloc_room_id == 0) {
                    $this->errors[] = Tools::displayError('Please select a room to swap with this room.');
                }
                if ($current_room_id == 0) {
                    $this->errors[] = Tools::displayError('Cuurent room is missing.');
                }
                if ($date_from == 0) {
                    $this->errors[] = Tools::displayError('Check In date is missing.');
                }
                if ($date_to == 0) {
                    $this->errors[] = Tools::displayError('Check Out date is missing.');
                }

                if (!count($this->errors)) {
                    $obj_booking_dtl = new HotelBookingDetail();
                    $room_swapped = $obj_booking_dtl->reallocateRoomWithAvailableSameRoomType($current_room_id, $date_from, $date_to, $realloc_room_id);
                    if (!$room_swapped) {
                        $this->errors[] = Tools::displayError('Some error occured. Please try again.');
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.(int) $order_id.'&vieworder&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        if (Tools::isSubmit('swap_allocated_rooms')) {
            if ($this->tabAccess['edit'] === '1') {
                $order_id = Tools::getValue('id_order');
                $current_room_id = Tools::getValue('modal_id_room');
                $current_room = Tools::getValue('modal_curr_room_num');
                $date_from = Tools::getValue('modal_date_from');
                $date_to = Tools::getValue('modal_date_to');
                $swapped_room_id = Tools::getValue('swap_avail_rooms');

                if ($swapped_room_id == 0) {
                    $this->errors[] = Tools::displayError('Please select a room to swap with this room.');
                }
                if ($current_room_id == 0) {
                    $this->errors[] = Tools::displayError('Cuurent room is missing.');
                }
                if ($date_from == 0) {
                    $this->errors[] = Tools::displayError('Check In date is missing.');
                }
                if ($date_to == 0) {
                    $this->errors[] = Tools::displayError('Check Out date is missing.');
                }

                if (!count($this->errors)) {
                    $obj_booking_dtl = new HotelBookingDetail();
                    $room_swapped = $obj_booking_dtl->swapRoomWithAvailableSameRoomType($current_room_id, $date_from, $date_to, $swapped_room_id);
                    if (!$room_swapped) {
                        $this->errors[] = Tools::displayError('Some error occured. Please try again.');
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.(int) $order_id.'&vieworder&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        // To update order status when admin changes from order detail page
        if (Tools::isSubmit('submitbookingOrderStatus')) {
            $this->changeRoomStatus();
        }

        // If id_order is sent, we instanciate a new Order object
        if (Tools::isSubmit('id_order') && Tools::getValue('id_order') > 0) {
            $order = new Order(Tools::getValue('id_order'));
            if (!Validate::isLoadedObject($order)) {
                $this->errors[] = Tools::displayError('The order cannot be found within your database.');
            }
            ShopUrl::cacheMainDomainForShop((int)$order->id_shop);
        }

        /* Update shipping number */
        if (Tools::isSubmit('submitShippingNumber') && isset($order)) {
            if ($this->tabAccess['edit'] === '1') {
                $order_carrier = new OrderCarrier(Tools::getValue('id_order_carrier'));
                if (!Validate::isLoadedObject($order_carrier)) {
                    $this->errors[] = Tools::displayError('The order carrier ID is invalid.');
                } elseif (!Validate::isTrackingNumber(Tools::getValue('tracking_number'))) {
                    $this->errors[] = Tools::displayError('The tracking number is incorrect.');
                } else {
                    // update shipping number
                    // Keep these two following lines for backward compatibility, remove on 1.6 version
                    $order->shipping_number = Tools::getValue('tracking_number');
                    $order->update();

                    // Update order_carrier
                    $order_carrier->tracking_number = pSQL(Tools::getValue('tracking_number'));
                    if ($order_carrier->update()) {
                        // Send mail to customer
                        $customer = new Customer((int)$order->id_customer);
                        $carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
                        if (!Validate::isLoadedObject($customer)) {
                            throw new PrestaShopException('Can\'t load Customer object');
                        }
                        if (!Validate::isLoadedObject($carrier)) {
                            throw new PrestaShopException('Can\'t load Carrier object');
                        }
                        $templateVars = array(
                            '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                            '{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{id_order}' => $order->id,
                            '{shipping_number}' => $order->shipping_number,
                            '{order_name}' => $order->getUniqReference()
                        );
                        if (@Mail::Send(
                            (int)$order->id_lang,
                            'in_transit',
                            Mail::l('Package in transit', (int)$order->id_lang),
                            $templateVars,
                            $customer->email,
                            $customer->firstname.' '.$customer->lastname,
                            null,
                            null,
                            null,
                            null,
                            _PS_MAIL_DIR_,
                            true,
                            (int)$order->id_shop
                        )) {
                            Hook::exec('actionAdminOrdersTrackingNumberUpdate', array('order' => $order, 'customer' => $customer, 'carrier' => $carrier), null, false, true, false, $order->id_shop);
                            Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                        } else {
                            $this->errors[] = Tools::displayError('An error occurred while sending an email to the customer.');
                        }
                    } else {
                        $this->errors[] = Tools::displayError('The order carrier cannot be updated.');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }

        /* Change order status, add a new entry in order history and send an e-mail to the customer if needed */
        elseif (Tools::isSubmit('submitState') && isset($order)) {
            if ($this->tabAccess['edit'] === '1') {
                $order_state = new OrderState(Tools::getValue('id_order_state'));

                if (!Validate::isLoadedObject($order_state)) {
                    $this->errors[] = Tools::displayError('The new order status is invalid.');
                } else {
                    $current_order_state = $order->getCurrentOrderState();
                    if ($current_order_state->id != $order_state->id) {
                        // Create new OrderHistory
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
                        $history->id_employee = (int)$this->context->employee->id;

                        $use_existings_payment = false;
                        if (!$order->hasInvoice()) {
                            $use_existings_payment = true;
                        }
                        $history->changeIdOrderState((int)$order_state->id, $order, $use_existings_payment);

                        $carrier = new Carrier($order->id_carrier, $order->id_lang);
                        $templateVars = array();

                        // Save all changes
                        if ($history->addWithemail(true, $templateVars)) {
                            // synchronizes quantities if needed..
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                foreach ($order->getProducts() as $product) {
                                    if (StockAvailable::dependsOnStock($product['product_id'])) {
                                        StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                                    }
                                }
                            }

                            Tools::redirectAdmin(self::$currentIndex.'&id_order='.(int)$order->id.'&vieworder&token='.$this->token);
                        }
                        $this->errors[] = Tools::displayError('An error occurred while changing order status, or we were unable to send an email to the customer.');
                    } else {
                        $this->errors[] = Tools::displayError('The order has already been assigned this status.');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }

        /* Add a new message for the current order and send an e-mail to the customer if needed */
        elseif (Tools::isSubmit('submitMessage') && isset($order)) {
            if ($this->tabAccess['edit'] === '1') {
                $customer = new Customer(Tools::getValue('id_customer'));
                if (!Validate::isLoadedObject($customer)) {
                    $this->errors[] = Tools::displayError('The customer is invalid.');
                } elseif (!Tools::getValue('message')) {
                    $this->errors[] = Tools::displayError('The message cannot be blank.');
                } else {
                    /* Get message rules and and check fields validity */
                    $rules = call_user_func(array('Message', 'getValidationRules'), 'Message');
                    foreach ($rules['required'] as $field) {
                        if (($value = Tools::getValue($field)) == false && (string)$value != '0') {
                            if (!Tools::getValue('id_'.$this->table) || $field != 'passwd') {
                                $this->errors[] = sprintf(Tools::displayError('field %s is required.'), $field);
                            }
                        }
                    }
                    foreach ($rules['size'] as $field => $maxLength) {
                        if (Tools::getValue($field) && Tools::strlen(Tools::getValue($field)) > $maxLength) {
                            $this->errors[] = sprintf(Tools::displayError('field %1$s is too long (%2$d chars max).'), $field, $maxLength);
                        }
                    }
                    foreach ($rules['validate'] as $field => $function) {
                        if (Tools::getValue($field)) {
                            if (!Validate::{$function}(htmlentities(Tools::getValue($field), ENT_COMPAT, 'UTF-8'))) {
                                $this->errors[] = sprintf(Tools::displayError('field %s is invalid.'), $field);
                            }
                        }
                    }

                    if (!count($this->errors)) {
                        //check if a thread already exist
                        $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, $order->id);
                        if (!$id_customer_thread) {
                            $customer_thread = new CustomerThread();
                            $customer_thread->id_contact = 0;
                            $customer_thread->id_customer = (int)$order->id_customer;
                            $customer_thread->id_shop = (int)$this->context->shop->id;
                            $customer_thread->id_order = (int)$order->id;
                            $customer_thread->id_lang = (int)$this->context->language->id;
                            $customer_thread->email = $customer->email;
                            $customer_thread->status = 'open';
                            $customer_thread->token = Tools::passwdGen(12);
                            $customer_thread->add();
                        } else {
                            $customer_thread = new CustomerThread((int)$id_customer_thread);
                        }

                        $customer_message = new CustomerMessage();
                        $customer_message->id_customer_thread = $customer_thread->id;
                        $customer_message->id_employee = (int)$this->context->employee->id;
                        $customer_message->message = Tools::getValue('message');
                        $customer_message->private = Tools::getValue('visibility');

                        if (!$customer_message->add()) {
                            $this->errors[] = Tools::displayError('An error occurred while saving the message.');
                        } elseif ($customer_message->private) {
                            Tools::redirectAdmin(self::$currentIndex.'&id_order='.(int)$order->id.'&vieworder&conf=11&token='.$this->token);
                        } else {
                            $message = $customer_message->message;
                            if (Configuration::get('PS_MAIL_TYPE', null, null, $order->id_shop) != Mail::TYPE_TEXT) {
                                $message = Tools::nl2br($customer_message->message);
                            }

                            $varsTpl = array(
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{id_order}' => $order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => $message
                            );
                            if (@Mail::Send(
                                (int)$order->id_lang,
                                'order_merchant_comment',
                                Mail::l('New message regarding your order', (int)$order->id_lang),
                                $varsTpl,
                                $customer->email,
                                $customer->firstname.' '.$customer->lastname,
                                null,
                                null,
                                null,
                                null,
                                _PS_MAIL_DIR_,
                                true,
                                (int)$order->id_shop
                            )) {
                                Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=11'.'&token='.$this->token);
                            }
                        }
                        $this->errors[] = Tools::displayError('An error occurred while sending an email to the customer.');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        }

        /* booking refunds from order */
        elseif (Tools::isSubmit('initiateRefund') && isset($order)) {
            if ($this->tabAccess['delete'] === '1') {
                $bookings = Tools::getValue('id_htl_booking');
                if ($bookings && count($bookings)) {
                    foreach ($bookings as $idHtlBooking) {
                        if (OrderReturn::getOrdersReturnDetail($order->id, 0, $idHtlBooking)) {
                            $this->errors[] = Tools::displayError('Wrong bookings found for booking cancelation.');
                            break;
                        }
                    }
                } else {
                    $this->errors[] = Tools::displayError('No booking has been selected.');
                }

                if (!$refundReason = Tools::getValue('cancellation_reason')) {
                    $this->errors[] = Tools::displayError('Please enter a reason for the booking cancellation.');
                }

                if (!count($this->errors)) {
                    $objOrderReturn = new OrderReturn();
                    $objOrderReturn->id_customer = $order->id_customer;
                    $objOrderReturn->id_order = $order->id;
                    $objOrderReturn->state = 1;
                    $objOrderReturn->by_admin = 1;
                    $objOrderReturn->question = $refundReason;
                    $objOrderReturn->save();
                    if ($objOrderReturn->id) {
                        foreach ($bookings as $idHtlBooking) {
                            $objHtlBooking = new HotelBookingDetail($idHtlBooking);
                            $numDays = $objHtlBooking->getNumberOfDays(
                                $objHtlBooking->date_from,
                                $objHtlBooking->date_to
                            );
                            $objOrderReturnDetail = new OrderReturnDetail();
                            $objOrderReturnDetail->id_order_return = $objOrderReturn->id;
                            $objOrderReturnDetail->id_order_detail = $objHtlBooking->id_order_detail;
                            $objOrderReturnDetail->product_quantity = $numDays;
                            $objOrderReturnDetail->id_htl_booking = $idHtlBooking;
                            $objOrderReturnDetail->save();
                        }
                    }
                }

                // Redirect if no errors
                if (!count($this->errors)) {
                    Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=3&token='.$this->token);
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        } elseif (Tools::isSubmit('messageReaded')) {
            Message::markAsReaded(Tools::getValue('messageReaded'), $this->context->employee->id);
        } elseif (Tools::isSubmit('submitAddPayment') && isset($order)) {
            if ($this->tabAccess['edit'] === '1') {
                $amount = str_replace(',', '.', Tools::getValue('payment_amount'));
                $currency = new Currency(Tools::getValue('payment_currency'));
                $order_has_invoice = $order->hasInvoice();
                if ($order_has_invoice) {
                    $order_invoice = new OrderInvoice(Tools::getValue('payment_invoice'));
                } else {
                    $order_invoice = null;
                }

                if (!Validate::isLoadedObject($order)) {
                    $this->errors[] = Tools::displayError('The order cannot be found');
                } elseif (!Validate::isNegativePrice($amount) || !(float)$amount) {
                    $this->errors[] = Tools::displayError('The amount is invalid.');
                } elseif (!Validate::isGenericName(Tools::getValue('payment_method'))) {
                    $this->errors[] = Tools::displayError('The selected payment method is invalid.');
                } elseif (!Validate::isString(Tools::getValue('payment_transaction_id'))) {
                    $this->errors[] = Tools::displayError('The transaction ID is invalid.');
                } elseif (!Validate::isLoadedObject($currency)) {
                    $this->errors[] = Tools::displayError('The selected currency is invalid.');
                } elseif ($order_has_invoice && !Validate::isLoadedObject($order_invoice)) {
                    $this->errors[] = Tools::displayError('The invoice is invalid.');
                } elseif (!Validate::isDate(Tools::getValue('payment_date'))) {
                    $this->errors[] = Tools::displayError('The date is invalid');
                } else {
                    if (!$order->addOrderPayment($amount, Tools::getValue('payment_method'), Tools::getValue('payment_transaction_id'), $currency, Tools::getValue('payment_date'), $order_invoice)) {
                        $this->errors[] = Tools::displayError('An error occurred during payment.');
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitEditNote')) {
            $note = Tools::getValue('note');
            $order_invoice = new OrderInvoice((int)Tools::getValue('id_order_invoice'));
            if (Validate::isLoadedObject($order_invoice) && Validate::isCleanHtml($note)) {
                if ($this->tabAccess['edit'] === '1') {
                    $order_invoice->note = $note;
                    if ($order_invoice->save()) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order_invoice->id_order.'&vieworder&conf=4&token='.$this->token);
                    } else {
                        $this->errors[] = Tools::displayError('The invoice note was not saved.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                }
            } else {
                $this->errors[] = Tools::displayError('The invoice for edit note was unable to load. ');
            }
        } elseif (Tools::isSubmit('submitAddOrder') && ($id_cart = Tools::getValue('id_cart')) &&
            ($module_name = Tools::getValue('payment_module_name')) &&
            ($id_order_state = Tools::getValue('id_order_state')) && Validate::isModuleName($module_name)) {
            if ($this->tabAccess['edit'] === '1') {
                if (!Configuration::get('PS_CATALOG_MODE')) {
                    $payment_module = Module::getInstanceByName($module_name);
                } else {
                    $payment_module = new BoOrder();
                }

                $cart = new Cart((int)$id_cart);
                Context::getContext()->currency = new Currency((int)$cart->id_currency);
                Context::getContext()->customer = new Customer((int)$cart->id_customer);

                $bad_delivery = false;
                if (($bad_delivery = (bool)!Address::isCountryActiveById((int)$cart->id_address_delivery))
                    || !Address::isCountryActiveById((int)$cart->id_address_invoice)) {
                    if ($bad_delivery) {
                        $this->errors[] = Tools::displayError('This booking address country is not active.');
                    } else {
                        $this->errors[] = Tools::displayError('This invoice address country is not active.');
                    }
                } else {
                    $employee = new Employee((int)Context::getContext()->cookie->id_employee);
                    $payment_module->validateOrder(
                        (int)$cart->id,
                        (int)$id_order_state,
                        $cart->getOrderTotal(true, Cart::BOTH),
                        $payment_module->displayName,
                        $this->l('Manual order -- Employee:').' '.
                        substr($employee->firstname, 0, 1).'. '.$employee->lastname,
                        array(),
                        null,
                        false,
                        $cart->secure_key
                    );
                    /*
                        setcookie('wk_id_cart', ' ', time() - 86400, '/');
                        setcookie('wk_id_guest', ' ', time() - 86400, '/');

                        unset($_COOKIE['wk_id_cart']);
                        unset($_COOKIE['wk_id_guest']);
                    */
                    if ($payment_module->currentOrder) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$payment_module->currentOrder.'&vieworder'.'&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to add this.');
            }
        } elseif ((Tools::isSubmit('submitAddressShipping') || Tools::isSubmit('submitAddressInvoice')) && isset($order)) {
            if ($this->tabAccess['edit'] === '1') {
                $address = new Address(Tools::getValue('id_address'));
                if (Validate::isLoadedObject($address)) {
                    // Update the address on order
                    if (Tools::isSubmit('submitAddressShipping')) {
                        $order->id_address_delivery = $address->id;
                    } elseif (Tools::isSubmit('submitAddressInvoice')) {
                        $order->id_address_invoice = $address->id;
                    }
                    $order->update();
                    Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                } else {
                    $this->errors[] = Tools::displayError('This address can\'t be loaded');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitChangeCurrency') && isset($order)) {
            if ($this->tabAccess['edit'] === '1') {
                if (Tools::getValue('new_currency') != $order->id_currency && !$order->valid) {
                    $old_currency = new Currency($order->id_currency);
                    $currency = new Currency(Tools::getValue('new_currency'));
                    if (!Validate::isLoadedObject($currency)) {
                        throw new PrestaShopException('Can\'t load Currency object');
                    }

                    // Update order detail amount
                    foreach ($order->getOrderDetailList() as $row) {
                        $order_detail = new OrderDetail($row['id_order_detail']);
                        $fields = array(
                            'ecotax',
                            'product_price',
                            'reduction_amount',
                            'total_shipping_price_tax_excl',
                            'total_shipping_price_tax_incl',
                            'total_price_tax_incl',
                            'total_price_tax_excl',
                            'product_quantity_discount',
                            'purchase_supplier_price',
                            'reduction_amount',
                            'reduction_amount_tax_incl',
                            'reduction_amount_tax_excl',
                            'unit_price_tax_incl',
                            'unit_price_tax_excl',
                            'original_product_price'

                        );
                        foreach ($fields as $field) {
                            $order_detail->{$field} = Tools::convertPriceFull($order_detail->{$field}, $old_currency, $currency);
                        }

                        $order_detail->update();
                        $order_detail->updateTaxAmount($order);
                    }

                    $id_order_carrier = (int)$order->getIdOrderCarrier();
                    if ($id_order_carrier) {
                        $order_carrier = $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
                        $order_carrier->shipping_cost_tax_excl = (float)Tools::convertPriceFull($order_carrier->shipping_cost_tax_excl, $old_currency, $currency);
                        $order_carrier->shipping_cost_tax_incl = (float)Tools::convertPriceFull($order_carrier->shipping_cost_tax_incl, $old_currency, $currency);
                        $order_carrier->update();
                    }

                    // Update order && order_invoice amount
                    $fields = array(
                        'total_discounts',
                        'total_discounts_tax_incl',
                        'total_discounts_tax_excl',
                        'total_discount_tax_excl',
                        'total_discount_tax_incl',
                        'total_paid',
                        'total_paid_tax_incl',
                        'total_paid_tax_excl',
                        'total_paid_real',
                        'total_products',
                        'total_products_wt',
                        'total_shipping',
                        'total_shipping_tax_incl',
                        'total_shipping_tax_excl',
                        'total_wrapping',
                        'total_wrapping_tax_incl',
                        'total_wrapping_tax_excl',
                    );

                    $invoices = $order->getInvoicesCollection();
                    if ($invoices) {
                        foreach ($invoices as $invoice) {
                            foreach ($fields as $field) {
                                if (isset($invoice->$field)) {
                                    $invoice->{$field} = Tools::convertPriceFull($invoice->{$field}, $old_currency, $currency);
                                }
                            }
                            $invoice->save();
                        }
                    }

                    foreach ($fields as $field) {
                        if (isset($order->$field)) {
                            $order->{$field} = Tools::convertPriceFull($order->{$field}, $old_currency, $currency);
                        }
                    }

                    // Update currency in order
                    $order->id_currency = $currency->id;
                    // Update exchange rate
                    $order->conversion_rate = (float)$currency->conversion_rate;
                    $order->update();
                } else {
                    $this->errors[] = Tools::displayError('You cannot change the currency.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitGenerateInvoice') && isset($order)) {
            if (!Configuration::get('PS_INVOICE', null, null, $order->id_shop)) {
                $this->errors[] = Tools::displayError('Invoice management has been disabled.');
            } elseif ($order->hasInvoice()) {
                $this->errors[] = Tools::displayError('This order already has an invoice.');
            } else {
                $order->setInvoice(true);
                Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
            }
        } elseif (Tools::isSubmit('submitDeleteVoucher') && isset($order)) {
            if ($this->tabAccess['edit'] === '1') {
                $order_cart_rule = new OrderCartRule(Tools::getValue('id_order_cart_rule'));
                if (Validate::isLoadedObject($order_cart_rule) && $order_cart_rule->id_order == $order->id) {
                    if ($order_cart_rule->id_order_invoice) {
                        $order_invoice = new OrderInvoice($order_cart_rule->id_order_invoice);
                        if (!Validate::isLoadedObject($order_invoice)) {
                            throw new PrestaShopException('Can\'t load Order Invoice object');
                        }

                        // Update amounts of Order Invoice
                        $order_invoice->total_discount_tax_excl -= $order_cart_rule->value_tax_excl;
                        $order_invoice->total_discount_tax_incl -= $order_cart_rule->value;

                        $order_invoice->total_paid_tax_excl += $order_cart_rule->value_tax_excl;
                        $order_invoice->total_paid_tax_incl += $order_cart_rule->value;

                        // Update Order Invoice
                        $order_invoice->update();
                    }

                    // Update amounts of order
                    $order->total_discounts -= $order_cart_rule->value;
                    $order->total_discounts_tax_incl -= $order_cart_rule->value;
                    $order->total_discounts_tax_excl -= $order_cart_rule->value_tax_excl;

                    $order->total_paid += $order_cart_rule->value;
                    $order->total_paid_tax_incl += $order_cart_rule->value;
                    $order->total_paid_tax_excl += $order_cart_rule->value_tax_excl;

                    // Delete Order Cart Rule and update Order
                    $order_cart_rule->delete();
                    $order->update();

                    Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                } else {
                    $this->errors[] = Tools::displayError('You cannot edit this cart rule.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('submitNewVoucher') && isset($order)) {
            if ($this->tabAccess['edit'] === '1') {
                if (!Tools::getValue('discount_name')) {
                    $this->errors[] = Tools::displayError('You must specify a name in order to create a new discount.');
                } elseif ((float)Tools::getValue('discount_value') <= 0) {
                    $this->errors[] = Tools::displayError('The discount value is invalid.');
                } else {
                    if ($order->hasInvoice()) {
                        // If the discount is for only one invoice
                        if (!Tools::isSubmit('discount_all_invoices')) {
                            $order_invoice = new OrderInvoice(Tools::getValue('discount_invoice'));
                            if (!Validate::isLoadedObject($order_invoice)) {
                                throw new PrestaShopException('Can\'t load Order Invoice object');
                            }
                        }
                    }

                    $cart_rules = array();
                    $discount_value = (float)str_replace(',', '.', Tools::getValue('discount_value'));
                    switch (Tools::getValue('discount_type')) {
                        // Percent type
                        case 1:
                            if ($discount_value < 100) {
                                if (isset($order_invoice)) {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($order_invoice->total_paid_tax_incl * $discount_value / 100, 2);
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($order_invoice->total_paid_tax_excl * $discount_value / 100, 2);

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                } elseif ($order->hasInvoice()) {
                                    $order_invoices_collection = $order->getInvoicesCollection();
                                    foreach ($order_invoices_collection as $order_invoice) {
                                        /** @var OrderInvoice $order_invoice */
                                        $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($order_invoice->total_paid_tax_incl * $discount_value / 100, 2);
                                        $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($order_invoice->total_paid_tax_excl * $discount_value / 100, 2);

                                        // Update OrderInvoice
                                        $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                    }
                                } else {
                                    $cart_rules[0]['value_tax_incl'] = Tools::ps_round($order->total_paid_tax_incl * $discount_value / 100, 2);
                                    $cart_rules[0]['value_tax_excl'] = Tools::ps_round($order->total_paid_tax_excl * $discount_value / 100, 2);
                                }
                            } else {
                                $this->errors[] = Tools::displayError('The discount value is invalid.');
                            }
                            break;
                        // Amount type
                        case 2:
                            if (isset($order_invoice)) {
                                if ($discount_value > $order_invoice->total_paid_tax_incl) {
                                    $this->errors[] = Tools::displayError('The discount value is greater than the order invoice total.');
                                } else {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($discount_value, 2);
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($discount_value / (1 + ($order->getTaxesAverageUsed() / 100)), 2);

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                }
                            } elseif ($order->hasInvoice()) {
                                $order_invoices_collection = $order->getInvoicesCollection();
                                foreach ($order_invoices_collection as $order_invoice) {
                                    /** @var OrderInvoice $order_invoice */
                                    if ($discount_value > $order_invoice->total_paid_tax_incl) {
                                        $this->errors[] = Tools::displayError('The discount value is greater than the order invoice total.').$order_invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$order->id_shop).')';
                                    } else {
                                        $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round($discount_value, 2);
                                        $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round($discount_value / (1 + ($order->getTaxesAverageUsed() / 100)), 2);

                                        // Update OrderInvoice
                                        $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                    }
                                }
                            } else {
                                if ($discount_value > $order->total_paid_tax_incl) {
                                    $this->errors[] = Tools::displayError('The discount value is greater than the order total.');
                                } else {
                                    $cart_rules[0]['value_tax_incl'] = Tools::ps_round($discount_value, 2);
                                    $cart_rules[0]['value_tax_excl'] = Tools::ps_round($discount_value / (1 + ($order->getTaxesAverageUsed() / 100)), 2);
                                }
                            }
                            break;
                        // Free shipping type
                        case 3:
                            if (isset($order_invoice)) {
                                if ($order_invoice->total_shipping_tax_incl > 0) {
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = $order_invoice->total_shipping_tax_incl;
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = $order_invoice->total_shipping_tax_excl;

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                }
                            } elseif ($order->hasInvoice()) {
                                $order_invoices_collection = $order->getInvoicesCollection();
                                foreach ($order_invoices_collection as $order_invoice) {
                                    /** @var OrderInvoice $order_invoice */
                                    if ($order_invoice->total_shipping_tax_incl <= 0) {
                                        continue;
                                    }
                                    $cart_rules[$order_invoice->id]['value_tax_incl'] = $order_invoice->total_shipping_tax_incl;
                                    $cart_rules[$order_invoice->id]['value_tax_excl'] = $order_invoice->total_shipping_tax_excl;

                                    // Update OrderInvoice
                                    $this->applyDiscountOnInvoice($order_invoice, $cart_rules[$order_invoice->id]['value_tax_incl'], $cart_rules[$order_invoice->id]['value_tax_excl']);
                                }
                            } else {
                                $cart_rules[0]['value_tax_incl'] = $order->total_shipping_tax_incl;
                                $cart_rules[0]['value_tax_excl'] = $order->total_shipping_tax_excl;
                            }
                            break;
                        default:
                            $this->errors[] = Tools::displayError('The discount type is invalid.');
                    }

                    $res = true;
                    foreach ($cart_rules as &$cart_rule) {
                        $cartRuleObj = new CartRule();
                        $cartRuleObj->date_from = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($order->date_add)));
                        $cartRuleObj->date_to = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        $cartRuleObj->name[Configuration::get('PS_LANG_DEFAULT')] = Tools::getValue('discount_name');
                        $cartRuleObj->quantity = 0;
                        $cartRuleObj->quantity_per_user = 1;
                        if (Tools::getValue('discount_type') == 1) {
                            $cartRuleObj->reduction_percent = $discount_value;
                        } elseif (Tools::getValue('discount_type') == 2) {
                            $cartRuleObj->reduction_amount = $cart_rule['value_tax_excl'];
                        } elseif (Tools::getValue('discount_type') == 3) {
                            $cartRuleObj->free_shipping = 1;
                        }
                        $cartRuleObj->active = 0;
                        if ($res = $cartRuleObj->add()) {
                            $cart_rule['id'] = $cartRuleObj->id;
                            $cart_rule['free_shipping'] = $cartRuleObj->free_shipping;
                        } else {
                            break;
                        }
                    }

                    if ($res) {
                        foreach ($cart_rules as $id_order_invoice => $cart_rule) {
                            // Create OrderCartRule
                            $order_cart_rule = new OrderCartRule();
                            $order_cart_rule->id_order = $order->id;
                            $order_cart_rule->id_cart_rule = $cart_rule['id'];
                            $order_cart_rule->id_order_invoice = $id_order_invoice;
                            $order_cart_rule->name = Tools::getValue('discount_name');
                            $order_cart_rule->value = $cart_rule['value_tax_incl'];
                            $order_cart_rule->value_tax_excl = $cart_rule['value_tax_excl'];
                            $order_cart_rule->free_shipping = $cart_rule['free_shipping'];
                            $res &= $order_cart_rule->add();

                            $order->total_discounts += $order_cart_rule->value;
                            $order->total_discounts_tax_incl += $order_cart_rule->value;
                            $order->total_discounts_tax_excl += $order_cart_rule->value_tax_excl;
                            $order->total_paid -= $order_cart_rule->value;
                            $order->total_paid_tax_incl -= $order_cart_rule->value;
                            $order->total_paid_tax_excl -= $order_cart_rule->value_tax_excl;
                        }

                        // Update Order
                        $res &= $order->update();
                    }

                    if ($res) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token);
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred during the OrderCartRule creation');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        } elseif (Tools::isSubmit('sendStateEmail') && Tools::getValue('sendStateEmail') > 0 && Tools::getValue('id_order') > 0) {
            if ($this->tabAccess['edit'] === '1') {
                $order_state = new OrderState((int)Tools::getValue('sendStateEmail'));

                if (!Validate::isLoadedObject($order_state)) {
                    $this->errors[] = Tools::displayError('An error occurred while loading order status.');
                } else {
                    $history = new OrderHistory((int)Tools::getValue('id_order_history'));

                    $carrier = new Carrier($order->id_carrier, $order->id_lang);
                    $templateVars = array();

                    if ($history->sendEmail($order, $templateVars)) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=10&token='.$this->token);
                    } else {
                        $this->errors[] = Tools::displayError('An error occurred while sending the e-mail to the customer.');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        parent::postProcess();
    }

    public function renderKpis()
    {
        $time = time();
        $kpis = array();

        /* The data generation is located in AdminStatsControllerCore */

        $helper = new HelperKpi();
        $helper->id = 'box-conversion-rate';
        $helper->icon = 'icon-sort-by-attributes-alt';
        //$helper->chart = true;
        $helper->color = 'color1';
        $helper->title = $this->l('Conversion Rate', null, null, false);
        $helper->subtitle = $this->l('30 days', null, null, false);
        if (ConfigurationKPI::get('CONVERSION_RATE') !== false) {
            $helper->value = ConfigurationKPI::get('CONVERSION_RATE');
        }
        if (ConfigurationKPI::get('CONVERSION_RATE_CHART') !== false) {
            $helper->data = ConfigurationKPI::get('CONVERSION_RATE_CHART');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=conversion_rate';
        $helper->refresh = (bool)(ConfigurationKPI::get('CONVERSION_RATE_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-carts';
        $helper->icon = 'icon-shopping-cart';
        $helper->color = 'color2';
        $helper->title = $this->l('Abandoned Carts', null, null, false);
        $helper->subtitle = $this->l('Today', null, null, false);
        $helper->href = $this->context->link->getAdminLink('AdminCarts').'&action=filterOnlyAbandonedCarts';
        if (ConfigurationKPI::get('ABANDONED_CARTS') !== false) {
            $helper->value = ConfigurationKPI::get('ABANDONED_CARTS');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=abandoned_cart';
        $helper->refresh = (bool)(ConfigurationKPI::get('ABANDONED_CARTS_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-average-order';
        $helper->icon = 'icon-money';
        $helper->color = 'color3';
        $helper->title = $this->l('Average Order Value', null, null, false);
        $helper->subtitle = $this->l('30 days', null, null, false);
        if (ConfigurationKPI::get('AVG_ORDER_VALUE') !== false) {
            $helper->value = sprintf($this->l('%s tax excl.'), ConfigurationKPI::get('AVG_ORDER_VALUE'));
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=average_order_value';
        $helper->refresh = (bool)(ConfigurationKPI::get('AVG_ORDER_VALUE_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-net-profit-visit';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $this->l('Net Profit per Visit', null, null, false);
        $helper->subtitle = $this->l('30 days', null, null, false);
        if (ConfigurationKPI::get('NETPROFIT_VISIT') !== false) {
            $helper->value = ConfigurationKPI::get('NETPROFIT_VISIT');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=netprofit_visit';
        $helper->refresh = (bool)(ConfigurationKPI::get('NETPROFIT_VISIT_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;
        return $helper->generate();
    }

    public function renderView()
    {
        $order = new Order(Tools::getValue('id_order'));
        if (!Validate::isLoadedObject($order)) {
            $this->errors[] = Tools::displayError('The order cannot be found within your database.');
        }

        $customer = new Customer($order->id_customer);
        $carrier = new Carrier($order->id_carrier);
        $products = $this->getProducts($order);
        $currency = new Currency((int)$order->id_currency);
        // Carrier module call
        $carrier_module_call = null;
        if ($carrier->is_module) {
            $module = Module::getInstanceByName($carrier->external_module_name);
            if (method_exists($module, 'displayInfoByCart')) {
                $carrier_module_call = call_user_func(array($module, 'displayInfoByCart'), $order->id_cart);
            }
        }

        // Retrieve addresses information
        $addressInvoice = new Address($order->id_address_invoice, $this->context->language->id);
        if (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state) {
            $invoiceState = new State((int)$addressInvoice->id_state);
        }

        if ($order->id_address_invoice == $order->id_address_delivery) {
            $addressDelivery = $addressInvoice;
            if (isset($invoiceState)) {
                $deliveryState = $invoiceState;
            }
        } else {
            $addressDelivery = new Address($order->id_address_delivery, $this->context->language->id);
            if (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state) {
                $deliveryState = new State((int)($addressDelivery->id_state));
            }
        }

        $this->toolbar_title = sprintf($this->l('Order #%1$d (%2$s) - %3$s %4$s'), $order->id, $order->reference, $customer->firstname, $customer->lastname);
        if (Shop::isFeatureActive()) {
            $shop = new Shop((int)$order->id_shop);
            $this->toolbar_title .= ' - '.sprintf($this->l('Shop: %s'), $shop->name);
        }

        // gets warehouses to ship products, if and only if advanced stock management is activated
        $warehouse_list = null;

        $order_details = $order->getOrderDetailList();
        foreach ($order_details as $order_detail) {
            $product = new Product($order_detail['product_id']);

            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                && $product->advanced_stock_management
            ) {
                $warehouses = Warehouse::getWarehousesByProductId($order_detail['product_id'], $order_detail['product_attribute_id']);
                foreach ($warehouses as $warehouse) {
                    if (!isset($warehouse_list[$warehouse['id_warehouse']])) {
                        $warehouse_list[$warehouse['id_warehouse']] = $warehouse;
                    }
                }
            }
        }

        $payment_methods = array();
        foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
            $module = Module::getInstanceByName($payment['name']);
            if (Validate::isLoadedObject($module) && $module->active) {
                $payment_methods[] = $module->displayName;
            }
        }
        // display warning if there are products out of stock
        $display_out_of_stock_warning = false;
        $current_order_state = $order->getCurrentOrderState();
        if (Configuration::get('PS_STOCK_MANAGEMENT') && (!Validate::isLoadedObject($current_order_state) || ($current_order_state->delivery != 1 && $current_order_state->shipped != 1))) {
            $display_out_of_stock_warning = true;
        }

        // products current stock (from stock_available)
        foreach ($products as &$product) {
            // Get total customized quantity for current product
            $customized_product_quantity = 0;

            if (is_array($product['customizedDatas'])) {
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customizationId => $customization) {
                        $customized_product_quantity += (int)$customization['quantity'];
                    }
                }
            }

            $product['customized_product_quantity'] = $customized_product_quantity;
            $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);
            $resume = OrderSlip::getProductSlipResume($product['id_order_detail']);
            $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
            $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
            $product['amount_refundable_tax_incl'] = $product['total_price_tax_incl'] - $resume['amount_tax_incl'];
            $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl'], $currency);

            // if the current stock requires a warning
            if ($product['current_stock'] <= 0 && $display_out_of_stock_warning) {
                $this->displayWarning($this->l('This product is out of stock: ').' '.$product['product_name']);
            }
            if ($product['id_warehouse'] != 0) {
                $warehouse = new Warehouse((int)$product['id_warehouse']);
                $product['warehouse_name'] = $warehouse->name;
                $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
                if (!empty($warehouse_location)) {
                    $product['warehouse_location'] = $warehouse_location;
                } else {
                    $product['warehouse_location'] = false;
                }
            } else {
                $product['warehouse_name'] = '--';
                $product['warehouse_location'] = false;
            }
        }

        $gender = new Gender((int)$customer->id_gender, $this->context->language->id);

        $history = $order->getHistory($this->context->language->id);

        foreach ($history as &$order_state) {
            $order_state['text-color'] = Tools::getBrightness($order_state['color']) < 128 ? 'white' : 'black';
        }
        //by webkul to get data to show hotel rooms order data on order detail page

        $cart_id = Cart::getCartIdByOrderId(Tools::getValue('id_order'));
        $order_detail_data = array();
        $cart_detail_data_obj = new HotelCartBookingData();
        $objBookingDetail = new HotelBookingDetail();

        $total_room_tax = 0;
        $totalRoomsCostTE = 0;
        $totalDemandsPriceTE = 0;
        $totalDemandsPriceTI = 0;
        if ($order_detail_data = $objBookingDetail->getOrderFormatedBookinInfoByIdOrder($order->id)) {
            $objBookingDemand = new HotelBookingDemands();
            foreach ($order_detail_data as $key => $value) {

                $order_detail_data[$key]['extra_demands'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                    $order->id,
                    $value['id_product'],
                    $value['id_room'],
                    $value['date_from'],
                    $value['date_to']
                );
                $order_detail_data[$key]['extra_demands_price_ti'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                    $order->id,
                    $value['id_product'],
                    $value['id_room'],
                    $value['date_from'],
                    $value['date_to'],
                    0,
                    1,
                    1
                );
                $totalDemandsPriceTI += $order_detail_data[$key]['extra_demands_price_ti'];
                $order_detail_data[$key]['extra_demands_price_te'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                    $order->id,
                    $value['id_product'],
                    $value['id_room'],
                    $value['date_from'],
                    $value['date_to'],
                    0,
                    1,
                    0
                );
                $totalDemandsPriceTE += $order_detail_data[$key]['extra_demands_price_te'];
                $cust_obj = new Customer($value['id_customer']);
                if ($cust_obj->firstname) {
                    $order_detail_data[$key]['alloted_cust_name'] = $cust_obj->firstname.' '.$cust_obj->lastname;
                } else {
                    $order_detail_data[$key]['alloted_cust_name'] = $this->l('No customer name found');
                }
                if ($cust_obj->email) {
                    $order_detail_data[$key]['alloted_cust_email'] = $cust_obj->email;
                } else {
                    $order_detail_data[$key]['alloted_cust_email'] = $this->l('No customer name found');
                }

                $order_detail_data[$key]['avail_rooms_to_realloc'] = $objBookingDetail->getAvailableRoomsForReallocation($value['date_from'], $value['date_to'], $value['id_product'], $value['id_hotel']);
                $order_detail_data[$key]['avail_rooms_to_swap'] = $objBookingDetail->getAvailableRoomsForSwapping($value['date_from'], $value['date_to'], $value['id_product'], $value['id_hotel'], $value['id_room']);

                /*Product price when order was created*/
                $totalRoomsCostTE += $value['total_price_tax_excl'];
                $total_room_tax += $value['total_price_tax_incl']-$value['total_price_tax_excl'];
                $num_days = $objBookingDetail->getNumberOfDays($value['date_from'], $value['date_to']);
                $order_detail_data[$key]['unit_amt_tax_excl'] = $value['total_price_tax_excl']/$num_days;
                $order_detail_data[$key]['unit_amt_tax_incl'] = $value['total_price_tax_incl']/$num_days;
                $order_detail_data[$key]['amt_with_qty_tax_excl'] = $value['total_price_tax_excl'];
                $order_detail_data[$key]['amt_with_qty_tax_incl'] = $value['total_price_tax_incl'];
            }
        }

        $objOrderReturn = new OrderReturn();
        $refundedAmount = 0;
        if ($refundReqBookings = $objOrderReturn->getOrderRefundRequestedBookings($order->id, 0, 1)) {
            $refundedAmount = $objOrderReturn->getRefundedAmount($order->id);
        }


        // get booking information by order
        $bookingOrderInfo = $objBookingDetail->getBookingDataByOrderId($order->id);

        // hotel booking statuses
        $htlOrderStatus = HotelBookingDetail::getAllHotelOrderStatus();

        $this->tpl_view_vars = array(
            // refund info
            'refund_allowed' => (int) $order->isReturnable(),
            'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
            'refundReqBookings' => $refundReqBookings,
            'hasCompletelyRefunded' => $order->hasCompletelyRefunded(),
            'refundedAmount' => $refundedAmount,

            'totalDemandsPriceTI' => $totalDemandsPriceTI,
            'totalDemandsPriceTE' => $totalDemandsPriceTE,
            'totalRoomsCostTE' => $totalRoomsCostTE,
            'total_room_tax' => $total_room_tax,
            'htl_booking_order_data' => $bookingOrderInfo,
            'hotel_order_status' => $htlOrderStatus,
            'order_detail_data' => $order_detail_data,
            /*END*/
            'order' => $order,
            'cart' => new Cart($order->id_cart),
            'customer' => $customer,
            'gender' => $gender,
            'customer_addresses' => $customer->getAddresses($this->context->language->id),
            'addresses' => array(
                'delivery' => $addressDelivery,
                'deliveryState' => isset($deliveryState) ? $deliveryState : null,
                'invoice' => $addressInvoice,
                'invoiceState' => isset($invoiceState) ? $invoiceState : null
            ),
            'customerStats' => $customer->getStats(),
            'products' => $products,
            'discounts' => $order->getCartRules(),
            'orders_total_paid_tax_incl' => $order->getOrdersTotalPaid(), // Get the sum of total_paid_tax_incl of the order with similar reference
            'total_paid' => $order->getTotalPaid(),
            'customer_thread_message' => CustomerThread::getCustomerMessages($order->id_customer, null, $order->id),
            'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
            'messages' => Message::getMessagesByOrderId($order->id, true),
            'carrier' => new Carrier($order->id_carrier),
            'history' => $history,
            'states' => OrderState::getOrderStates($this->context->language->id),
            'warehouse_list' => $warehouse_list,
            'sources' => ConnectionsSource::getOrderSources($order->id),
            'currentState' => $order->getCurrentOrderState(),
            'currency' => new Currency($order->id_currency),
            'currencies' => Currency::getCurrenciesByIdShop($order->id_shop),
            'previousOrder' => $order->getPreviousOrderId(),
            'nextOrder' => $order->getNextOrderId(),
            'current_index' => self::$currentIndex,
            'carrierModuleCall' => $carrier_module_call,
            'iso_code_lang' => $this->context->language->iso_code,
            'id_lang' => $this->context->language->id,
            'can_edit' => ($this->tabAccess['edit'] == 1),
            'current_id_lang' => $this->context->language->id,
            'invoices_collection' => $order->getInvoicesCollection(),
            'not_paid_invoices_collection' => $order->getNotPaidInvoicesCollection(),
            'payment_methods' => $payment_methods,
            'invoice_management_active' => Configuration::get('PS_INVOICE', null, null, $order->id_shop),
            'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
            'HOOK_CONTENT_ORDER' => Hook::exec(
                'displayAdminOrderContentOrder',
                array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'HOOK_CONTENT_SHIP' => Hook::exec(
                'displayAdminOrderContentShip',
                array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'HOOK_TAB_ORDER' => Hook::exec(
                'displayAdminOrderTabOrder',
                array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'HOOK_TAB_SHIP' => Hook::exec(
                'displayAdminOrderTabShip',
                array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
        );

        return parent::renderView();
    }

    public function ajaxProcessSearchProducts()
    {
        Context::getContext()->customer = new Customer((int)Tools::getValue('id_customer'));
        $currency = new Currency((int)Tools::getValue('id_currency'));
        if ($products = Product::searchByName((int)$this->context->language->id, pSQL(Tools::getValue('product_search')))) {
            foreach ($products as &$product) {
                // Formatted price
                $product['formatted_price'] = Tools::displayPrice(Tools::convertPrice($product['price_tax_incl'], $currency), $currency);
                // Concret price
                $product['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_incl'], $currency), 2);
                $product['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_excl'], $currency), 2);
                $productObj = new Product((int)$product['id_product'], false, (int)$this->context->language->id);
                $combinations = array();
                $attributes = $productObj->getAttributesGroups((int)$this->context->language->id);

                // Tax rate for this customer
                if (Tools::isSubmit('id_address')) {
                    $product['tax_rate'] = $productObj->getTaxesRate(new Address(Tools::getValue('id_address')));
                }

                $product['warehouse_list'] = array();

                foreach ($attributes as $attribute) {
                    if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                        $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                    }
                    $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';
                    $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                    $combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
                    if (!isset($combinations[$attribute['id_product_attribute']]['price'])) {
                        $price_tax_incl = Product::getPriceStatic((int)$product['id_product'], true, $attribute['id_product_attribute']);
                        $price_tax_excl = Product::getPriceStatic((int)$product['id_product'], false, $attribute['id_product_attribute']);
                        $combinations[$attribute['id_product_attribute']]['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($price_tax_incl, $currency), 2);
                        $combinations[$attribute['id_product_attribute']]['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($price_tax_excl, $currency), 2);
                        $combinations[$attribute['id_product_attribute']]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($price_tax_excl, $currency), $currency);
                    }
                    if (!isset($combinations[$attribute['id_product_attribute']]['qty_in_stock'])) {
                        $combinations[$attribute['id_product_attribute']]['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], $attribute['id_product_attribute'], (int)$this->context->shop->id);
                    }

                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
                        $product['warehouse_list'][$attribute['id_product_attribute']] = Warehouse::getProductWarehouseList($product['id_product'], $attribute['id_product_attribute']);
                    } else {
                        $product['warehouse_list'][$attribute['id_product_attribute']] = array();
                    }

                    $product['stock'][$attribute['id_product_attribute']] = Product::getRealQuantity($product['id_product'], $attribute['id_product_attribute']);
                }

                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
                    $product['warehouse_list'][0] = Warehouse::getProductWarehouseList($product['id_product']);
                } else {
                    $product['warehouse_list'][0] = array();
                }

                $product['stock'][0] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], 0, (int)$this->context->shop->id);

                foreach ($combinations as &$combination) {
                    $combination['attributes'] = rtrim($combination['attributes'], ' - ');
                }
                $product['combinations'] = $combinations;

                if ($product['customizable']) {
                    $product_instance = new Product((int)$product['id_product']);
                    $product['customization_fields'] = $product_instance->getCustomizationFields($this->context->language->id);
                }
            }

            $to_return = array(
                'products' => $products,
                'found' => true
            );
        } else {
            $to_return = array('found' => false);
        }

        $this->content = json_encode($to_return);
    }

    public function ajaxProcessSendMailValidateOrder()
    {
        if ($this->tabAccess['edit'] === '1') {
            $cart = new Cart((int)Tools::getValue('id_cart'));
            if (Validate::isLoadedObject($cart)) {
                $customer = new Customer((int)$cart->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    $mailVars = array(
                        '{order_link}' => Context::getContext()->link->getPageLink('order', false, (int)$cart->id_lang, 'step=3&recover_cart='.(int)$cart->id.'&token_cart='.md5(_COOKIE_KEY_.'recover_cart_'.(int)$cart->id)),
                        '{firstname}' => $customer->firstname,
                        '{lastname}' => $customer->lastname
                    );
                    if (Mail::Send(
                        (int)$cart->id_lang,
                        'backoffice_order',
                        Mail::l('Process the payment of your order', (int)$cart->id_lang),
                        $mailVars,
                        $customer->email,
                        $customer->firstname.' '.$customer->lastname,
                        null,
                        null,
                        null,
                        null,
                        _PS_MAIL_DIR_,
                        true,
                        $cart->id_shop
                    )) {
                        die(json_encode(array('errors' => false, 'result' => $this->l('The email was sent to your customer.'))));
                    }
                }
            }
            $this->content = json_encode(array('errors' => true, 'result' => $this->l('Error in sending the email to your customer.')));
        }
    }

    public function ajaxProcessAddProductOnOrder()
    {
        // Load object
        $id_order = (int) Tools::getValue('id_order');
        $order = new Order($id_order);
        if (!Validate::isLoadedObject($order)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The order object cannot be loaded.')
            )));
        }

        $old_cart_rules = Context::getContext()->cart->getCartRules();

        if ($order->hasBeenShipped()) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('You cannot add products to delivered orders. ')
            )));
        }

        $product_informations = $_POST['add_product'];

        /*By Webkul Code is added to add order information In our table while adding product in the process order edit from order detail page.*/
        $date_from = date('Y-m-d', strtotime($product_informations['date_from']));
        $date_to = date('Y-m-d', strtotime($product_informations['date_to']));
        $curr_date = date('Y-m-d');
        /*Validations*/
        if ($date_from == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter Check In Date.'),
            )));
        } elseif (!Validate::isDateFormat($date_from)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter a Valid Check In Date.'),
            )));
        } elseif ($date_to == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter Check Out Date.'),
            )));
        } elseif (!Validate::isDateFormat($date_to)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter a valid Check out Date.'),
            )));
        } elseif ($date_from < $curr_date) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Check In date should not be date before current date.'),
            )));
        } elseif ($date_to <= $date_from) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Check out Date Should be after Check In date.'),
            )));
        } elseif (!Validate::isUnsignedInt($product_informations['product_quantity'])) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter a valid Quantity.'),
            )));
        }

        $obj_booking_detail = new HotelBookingDetail();
        $req_rm = $product_informations['product_quantity'];
        $num_days = $obj_booking_detail->getNumberOfDays($date_from, $date_to);
        $product_informations['product_quantity'] = $product_informations['product_quantity'] * (int) $num_days;

        $obj_room_type = new HotelRoomType();
        $room_info_by_id_product = $obj_room_type->getRoomTypeInfoByIdProduct($product_informations['product_id']);

        if ($room_info_by_id_product) {
            $id_hotel = $room_info_by_id_product['id_hotel'];

            if ($id_hotel) {
                $obj_booking_dtl = new HotelBookingDetail();
                $hotel_room_data = $obj_booking_dtl->DataForFrontSearch($date_from, $date_to, $id_hotel, $product_informations['product_id'], 1, 0, 0, -1, 0, 0, $id_cart, $id_guest);

                $total_available_rooms = $hotel_room_data['stats']['num_avail'];

                if ($total_available_rooms < $req_rm) {
                    die(json_encode(array(
                        'result' => false,
                        'error' => Tools::displayError('Required number of rooms are not available.'),
                        )));
                }
            } else {
                die(json_encode(array(
                    'result' => false,
                    'error' => Tools::displayError('Some error occured Please try again.'),
                    )));
            }
        } else {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Some error occured Please try again.'),
            )));
        }

        /*END*/

        if (isset($_POST['add_invoice'])) {
            $invoice_informations = $_POST['add_invoice'];
        } else {
            $invoice_informations = array();
        }

        $idProduct = $product_informations['product_id'];
        $product = new Product($idProduct, false, $order->id_lang);
        if (!Validate::isLoadedObject($product)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The product object cannot be loaded.')
            )));
        }

        if (isset($product_informations['product_attribute_id']) && $product_informations['product_attribute_id']) {
            $combination = new Combination($product_informations['product_attribute_id']);
            if (!Validate::isLoadedObject($combination)) {
                die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The combination object cannot be loaded.')
            )));
            }
        }

        // Total method
        $total_method = Cart::BOTH_WITHOUT_SHIPPING;

        // Create new cart
        $cart = new Cart();
        $cart->id_shop_group = $order->id_shop_group;
        $cart->id_shop = $order->id_shop;
        $cart->id_customer = $order->id_customer;
        $cart->id_carrier = $order->id_carrier;
        $cart->id_address_delivery = $order->id_address_delivery;
        $cart->id_address_invoice = $order->id_address_invoice;
        $cart->id_currency = $order->id_currency;
        $cart->id_lang = $order->id_lang;
        $cart->secure_key = $order->secure_key;

        // Save new cart
        $cart->add();

        // Save context (in order to apply cart rule)
        $this->context->cart = $cart;
        $this->context->customer = new Customer($order->id_customer);

        /*By Webkul to make entries in HotelCartBookingData */
        $hotel_room_info_arr = $hotel_room_data['rm_data'][0]['data']['available'];
        $chkQty = 0;
        if ($hotel_room_info_arr) {
            foreach ($hotel_room_info_arr as $room_info) {
                if ($chkQty < $req_rm) {
                    $obj_htl_cart_booking_data = new HotelCartBookingData();
                    $obj_htl_cart_booking_data->id_cart = $this->context->cart->id;
                    $obj_htl_cart_booking_data->id_guest = $this->context->cookie->id_guest;
                    $obj_htl_cart_booking_data->id_customer = $this->context->customer->id;
                    $obj_htl_cart_booking_data->id_currency = $order->id_currency;
                    $obj_htl_cart_booking_data->id_product = $room_info['id_product'];
                    $obj_htl_cart_booking_data->id_room = $room_info['id_room'];
                    $obj_htl_cart_booking_data->id_hotel = $room_info['id_hotel'];
                    $obj_htl_cart_booking_data->booking_type = 1;
                    $obj_htl_cart_booking_data->quantity = $num_days;
                    $obj_htl_cart_booking_data->date_from = $date_from;
                    $obj_htl_cart_booking_data->date_to = $date_to;
                    $obj_htl_cart_booking_data->save();
                    ++$chkQty;
                } else {
                    break;
                }
            }
        }
        /*END*/
        // always add taxes even if there are not displayed to the customer
        $use_taxes = true;

        $initial_product_price_tax_incl = Product::getPriceStatic(
            $product->id,
            $use_taxes,
            isset($combination) ? $combination->id : null,
            2,
            null,
            false,
            true,
            1,
            false,
            $order->id_customer,
            $cart->id,
            $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)}
        );

        // Creating specific price if needed
        if ($product_informations['product_price_tax_incl'] != $initial_product_price_tax_incl) {
            $specific_price = new SpecificPrice();
            $specific_price->id_shop = 0;
            $specific_price->id_shop_group = 0;
            $specific_price->id_currency = 0;
            $specific_price->id_country = 0;
            $specific_price->id_group = 0;
            $specific_price->id_customer = $order->id_customer;
            $specific_price->id_product = $product->id;
            if (isset($combination)) {
                $specific_price->id_product_attribute = $combination->id;
            } else {
                $specific_price->id_product_attribute = 0;
            }
            $specific_price->price = $product_informations['product_price_tax_excl'];
            $specific_price->from_quantity = 1;
            $specific_price->reduction = 0;
            $specific_price->reduction_type = 'amount';
            $specific_price->reduction_tax = 0;
            $specific_price->from = '0000-00-00 00:00:00';
            $specific_price->to = '0000-00-00 00:00:00';
            $specific_price->add();
        }

        // Add product to cart
        $update_quantity = $cart->updateQty(
            $product_informations['product_quantity'],
            $product->id,
            isset($product_informations['product_attribute_id']) ? $product_informations['product_attribute_id'] : null,
            isset($combination) ? $combination->id : null,
            'up',
            0,
            new Shop($cart->id_shop)
        );

        if ($update_quantity < 0) {
            // If product has attribute, minimal quantity is set with minimal quantity of attribute
            $minimal_quantity = ($product_informations['product_attribute_id']) ? Attribute::getAttributeMinimalQty($product_informations['product_attribute_id']) : $product->minimal_quantity;
            die(json_encode(array('error' => sprintf(Tools::displayError('You must add %d minimum quantity', false), $minimal_quantity))));
        } elseif (!$update_quantity) {
            die(json_encode(array('error' => Tools::displayError('You already have the maximum quantity available for this product.', false))));
        }

        // If order is valid, we can create a new invoice or edit an existing invoice
        if ($order->hasInvoice()) {
            $order_invoice = new OrderInvoice($product_informations['invoice']);
            // Create new invoice
            if ($order_invoice->id == 0) {
                // If we create a new invoice, we calculate shipping cost
                $total_method = Cart::BOTH;
                // Create Cart rule in order to make free shipping
                if (isset($invoice_informations['free_shipping']) && $invoice_informations['free_shipping']) {
                    $cart_rule = new CartRule();
                    $cart_rule->id_customer = $order->id_customer;
                    $cart_rule->name = array(
                        Configuration::get('PS_LANG_DEFAULT') => $this->l('[Generated] CartRule for Free Shipping')
                    );
                    $cart_rule->date_from = date('Y-m-d H:i:s', time());
                    $cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 3600);
                    $cart_rule->quantity = 1;
                    $cart_rule->quantity_per_user = 1;
                    $cart_rule->minimum_amount_currency = $order->id_currency;
                    $cart_rule->reduction_currency = $order->id_currency;
                    $cart_rule->free_shipping = true;
                    $cart_rule->active = 1;
                    $cart_rule->add();

                    // Add cart rule to cart and in order
                    $cart->addCartRule($cart_rule->id);
                    $values = array(
                        'tax_incl' => $cart_rule->getContextualValue(true),
                        'tax_excl' => $cart_rule->getContextualValue(false)
                    );
                    $order->addCartRule($cart_rule->id, $cart_rule->name[Configuration::get('PS_LANG_DEFAULT')], $values);
                }

                $order_invoice->id_order = $order->id;
                if ($order_invoice->number) {
                    Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $order->id_shop);
                } else {
                    $order_invoice->number = Order::getLastInvoiceNumber() + 1;
                }

                $invoice_address = new Address((int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});
                $carrier = new Carrier((int)$order->id_carrier);
                $tax_calculator = $carrier->getTaxCalculator($invoice_address);

                $order_invoice->total_paid_tax_excl = Tools::ps_round((float)$cart->getOrderTotal(false, $total_method), 2);
                $order_invoice->total_paid_tax_incl = Tools::ps_round((float)$cart->getOrderTotal($use_taxes, $total_method), 2);
                $order_invoice->total_products = (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                $order_invoice->total_products_wt = (float)$cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
                $order_invoice->total_shipping_tax_excl = (float)$cart->getTotalShippingCost(null, false);
                $order_invoice->total_shipping_tax_incl = (float)$cart->getTotalShippingCost();

                $order_invoice->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
                $order_invoice->total_wrapping_tax_incl = abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
                $order_invoice->shipping_tax_computation_method = (int)$tax_calculator->computation_method;

                // Update current order field, only shipping because other field is updated later
                $order->total_shipping += $order_invoice->total_shipping_tax_incl;
                $order->total_shipping_tax_excl += $order_invoice->total_shipping_tax_excl;
                $order->total_shipping_tax_incl += ($use_taxes) ? $order_invoice->total_shipping_tax_incl : $order_invoice->total_shipping_tax_excl;

                $order->total_wrapping += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
                $order->total_wrapping_tax_excl += abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
                $order->total_wrapping_tax_incl += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
                $order_invoice->add();

                $order_invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl));

                $order_carrier = new OrderCarrier();
                $order_carrier->id_order = (int)$order->id;
                $order_carrier->id_carrier = (int)$order->id_carrier;
                $order_carrier->id_order_invoice = (int)$order_invoice->id;
                $order_carrier->weight = (float)$cart->getTotalWeight();
                $order_carrier->shipping_cost_tax_excl = (float)$order_invoice->total_shipping_tax_excl;
                $order_carrier->shipping_cost_tax_incl = ($use_taxes) ? (float)$order_invoice->total_shipping_tax_incl : (float)$order_invoice->total_shipping_tax_excl;
                $order_carrier->add();
            }
            // Update current invoice
            else {
                $order_invoice->total_paid_tax_excl += Tools::ps_round((float)($cart->getOrderTotal(false, $total_method)), 2);
                $order_invoice->total_paid_tax_incl += Tools::ps_round((float)($cart->getOrderTotal($use_taxes, $total_method)), 2);
                $order_invoice->total_products += (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                $order_invoice->total_products_wt += (float)$cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
                $order_invoice->update();
            }
        }

        // Create Order detail information
        $order_detail = new OrderDetail();
        $order_detail->createList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), (isset($order_invoice) ? $order_invoice->id : 0), $use_taxes, (int)Tools::getValue('add_product_warehouse'));

        // update totals amount of order
        $order->total_products += (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $order->total_products_wt += (float)$cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);

        $order->total_paid += Tools::ps_round((float)($cart->getOrderTotal(true, $total_method)), 2);
        $order->total_paid_tax_excl += Tools::ps_round((float)($cart->getOrderTotal(false, $total_method)), 2);
        $order->total_paid_tax_incl += Tools::ps_round((float)($cart->getOrderTotal($use_taxes, $total_method)), 2);

        if (isset($order_invoice) && Validate::isLoadedObject($order_invoice)) {
            $order->total_shipping = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_incl = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_excl = $order_invoice->total_shipping_tax_excl;
        }

        // discount
        $order->total_discounts += (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_excl += (float)abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_incl += (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));

        // Save changes of order
        $order->update();

        // Update weight SUM
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float)$order->getTotalWeight();
            if ($order_carrier->update()) {
                $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        // Update Tax lines
        $order_detail->updateTaxAmount($order);

        // Delete specific price if exists
        if (isset($specific_price)) {
            $specific_price->delete();
        }

        $products = $this->getProducts($order);

        // Get the last product
        $product = end($products);
        $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);
        $resume = OrderSlip::getProductSlipResume((int)$product['id_order_detail']);
        $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
        $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
        $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl']);
        $product['return_history'] = OrderReturn::getProductReturnDetail((int)$product['id_order_detail']);

        if ($product['id_warehouse'] != 0) {
            $warehouse = new Warehouse((int)$product['id_warehouse']);
            $product['warehouse_name'] = $warehouse->name;
            $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
            if (!empty($warehouse_location)) {
                $product['warehouse_location'] = $warehouse_location;
            } else {
                $product['warehouse_location'] = false;
            }
        } else {
            $product['warehouse_name'] = '--';
            $product['warehouse_location'] = false;
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /** @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$order->id_shop);
            $invoice_array[] = $invoice;
        }

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(array(
            'product' => $product,
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            'can_edit' => $this->tabAccess['edit'],
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'current_index' => self::$currentIndex,
            'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
        ));

        $this->sendChangedNotification($order);
        $new_cart_rules = Context::getContext()->cart->getCartRules();
        sort($old_cart_rules);
        sort($new_cart_rules);
        $result = array_diff($new_cart_rules, $old_cart_rules);
        $refresh = false;

        $res = true;
        foreach ($result as $cart_rule) {
            $refresh = true;
            // Create OrderCartRule
            $rule = new CartRule($cart_rule['id_cart_rule']);
            $values = array(
                    'tax_incl' => $rule->getContextualValue(true),
                    'tax_excl' => $rule->getContextualValue(false)
                    );
            $order_cart_rule = new OrderCartRule();
            $order_cart_rule->id_order = $order->id;
            $order_cart_rule->id_cart_rule = $cart_rule['id_cart_rule'];
            $order_cart_rule->id_order_invoice = $order_invoice->id;
            $order_cart_rule->name = $cart_rule['name'];
            $order_cart_rule->value = $values['tax_incl'];
            $order_cart_rule->value_tax_excl = $values['tax_excl'];
            $res &= $order_cart_rule->add();

            $order->total_discounts += $order_cart_rule->value;
            $order->total_discounts_tax_incl += $order_cart_rule->value;
            $order->total_discounts_tax_excl += $order_cart_rule->value_tax_excl;
            $order->total_paid -= $order_cart_rule->value;
            $order->total_paid_tax_incl -= $order_cart_rule->value;
            $order->total_paid_tax_excl -= $order_cart_rule->value_tax_excl;
        }

        // Update Order
        $res &= $order->update();

        /*By Webkul Entry into table HotelbookingDetail*/
        $objRoomType = new HotelRoomType();
        $objHtlBkDtl = new HotelBookingDetail();
        $inserted_id_order_detail = $objHtlBkDtl->getLastInsertedIdOrderDetail($order->id);
        $idLang = (int)$this->context->cart->id_lang;
        $obj_cart_bk_data = new HotelCartBookingData();
        if ($cart_bk_data = $obj_cart_bk_data->getOnlyCartBookingData(
            $this->context->cart->id,
            $this->context->cart->id_guest,
            $idProduct
        )) {
            foreach ($cart_bk_data as $cb_k => $cb_v) {
                $obj_cart_bk_data = new HotelCartBookingData($cb_v['id']);
                $obj_cart_bk_data->id_order = $order->id;
                $obj_cart_bk_data->save();

                $objHtlBkDtl = new HotelBookingDetail();
                $objHtlBkDtl->id_product = $idProduct;
                $objHtlBkDtl->id_order = $order->id;
                $objHtlBkDtl->id_order_detail = $inserted_id_order_detail;
                $objHtlBkDtl->id_cart = $this->context->cart->id;
                $objHtlBkDtl->id_room = $obj_cart_bk_data->id_room;
                $objHtlBkDtl->id_hotel = $obj_cart_bk_data->id_hotel;
                $objHtlBkDtl->id_customer = $this->context->customer->id;
                $objHtlBkDtl->booking_type = $obj_cart_bk_data->booking_type;
                $objHtlBkDtl->id_status = 1;
                $objHtlBkDtl->comment = $obj_cart_bk_data->comment;
                $objHtlBkDtl->date_from = $obj_cart_bk_data->date_from;
                $objHtlBkDtl->date_to = $obj_cart_bk_data->date_to;
                $total_price = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($idProduct, $obj_cart_bk_data->date_from, $obj_cart_bk_data->date_to);
                $objHtlBkDtl->total_price_tax_excl = $total_price['total_price_tax_excl'];
                $objHtlBkDtl->total_price_tax_incl = $total_price['total_price_tax_incl'];

                // Save hotel information/location/contact
                if (Validate::isLoadedObject($objRoom = new HotelRoomInformation($obj_cart_bk_data->id_room))) {
                    $objHtlBkDtl->room_num = $objRoom->room_num;
                }
                if (Validate::isLoadedObject($objHotelBranch = new HotelBranchInformation(
                    $obj_cart_bk_data->id_hotel,
                    $idLang
                ))) {
                    $objHtlBkDtl->hotel_name = $objHotelBranch->hotel_name;
                    $objHtlBkDtl->city = $objHotelBranch->city;
                    $objHtlBkDtl->state = State::getNameById($objHotelBranch->state_id);
                    $objHtlBkDtl->country = Country::getNameById($idLang, $objHotelBranch->country_id);
                    $objHtlBkDtl->zipcode = $objHotelBranch->zipcode;
                    $objHtlBkDtl->phone = $objHotelBranch->phone;
                    $objHtlBkDtl->email = $objHotelBranch->email;
                    $objHtlBkDtl->check_in_time = $objHotelBranch->check_in;
                    $objHtlBkDtl->check_out_time = $objHotelBranch->check_out;
                }
                if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($idProduct)) {
                    $objHtlBkDtl->adult = $roomTypeInfo['adult'];
                    $objHtlBkDtl->children = $roomTypeInfo['children'];
                }

                $objHtlBkDtl->save();
            }
        }

        die(json_encode(array(
            'result' => true,
            //'view' => $this->createTemplate('_product_line.tpl')->fetch(),
            'can_edit' => $this->tabAccess['add'],
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch(),
            'discount_form_html' => $this->createTemplate('_discount_form.tpl')->fetch(),
            'refresh' => $refresh
        )));
    }

    public function sendChangedNotification(Order $order = null)
    {
        if (is_null($order)) {
            $order = new Order(Tools::getValue('id_order'));
        }

        Hook::exec('actionOrderEdited', array('order' => $order));
    }

    public function ajaxProcessLoadProductInformation()
    {
        // $order_detail = new OrderDetail(Tools::getValue('id_order_detail'));
        // if (!Validate::isLoadedObject($order_detail))
        // 	die(json_encode(array(
        // 		'result' => false,
        // 		'error' => Tools::displayError('The OrderDetail object cannot be loaded.')
        // 	)));

        $id_product = Tools::getValue('id_product');
        $product = new Product($id_product);
        if (!Validate::isLoadedObject($product)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The product object cannot be loaded.')
            )));
        }

        $address = new Address(Tools::getValue('id_address'));
        if (!Validate::isLoadedObject($address)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The address object cannot be loaded.')
            )));
        }

        die(json_encode(array(
            'result' => true,
            'product' => $product,
            'tax_rate' => $product->getTaxesRate($address),
            /*Original*/
            /*'price_tax_incl' => Product::getPriceStatic($product->id, true, $order_detail->product_attribute_id, 2),
            'price_tax_excl' => Product::getPriceStatic($product->id, false, $order_detail->product_attribute_id, 2),*/
            /*Changed by webkul because attribute_id will always be 0 (No combination)*/
            'price_tax_incl' => Product::getPriceStatic($product->id, true, 0, 2),
            'price_tax_excl' => Product::getPriceStatic($product->id, false, 0, 2),
            //'reduction_percent' => $order_detail->reduction_percent
        )));
    }

    public function ajaxProcessEditProductOnOrder()
    {
        // Return value
        $res = true;
        $id_order = (int) Tools::getValue('id_order');
        $order = new Order($id_order);
        //$order_detail = new OrderDetail((int)Tools::getValue('product_id_order_detail'));
        $order_detail = new OrderDetail((int) Tools::getValue('order_detail_id'));//by webkul id_order_detail from our table
        $this->doEditProductValidation($order_detail, $order, isset($order_invoice) ? $order_invoice : null);
        if (Tools::isSubmit('product_invoice')) {
            $order_invoice = new OrderInvoice((int) Tools::getValue('product_invoice'));
        }

        /*By webkul To edit Order and cart entries when edit rooms from the orderLine when editing the order*/
        $product_informations = $_POST['add_product'];
        $new_date_from = trim(date('Y-m-d', strtotime($product_informations['date_from'])));
        $new_date_to = trim(date('Y-m-d', strtotime($product_informations['date_to'])));
        $old_date_from = trim(Tools::getValue('date_from'));
        $old_date_to = trim(Tools::getValue('date_to'));
        $id_hotel = trim(Tools::getValue('id_hotel'));
        $id_room = trim(Tools::getValue('id_room'));
        $id_product = trim(Tools::getValue('id_product'));
        $obj_booking_detail = new HotelBookingDetail();
        $product_quantity = (int) $obj_booking_detail->getNumberOfDays($new_date_from, $new_date_to);
        $old_product_quantity =  (int) $obj_booking_detail->getNumberOfDays($old_date_from, $old_date_to);
        $qty_diff = $product_quantity - $old_product_quantity;
        /*By webkul to validate fields before deleting the cart and order data form the tables*/
        if ($id_hotel == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Hotel Id is mising.'),
            )));
        } elseif ($id_room == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Room Id is missing.'),
            )));
        } elseif ($new_date_from == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter Check In Date.'),
            )));
        } elseif (!Validate::isDateFormat($new_date_from)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter a Valid Check In Date.'),
            )));
        } elseif ($new_date_to == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter Check Out Date.'),
            )));
        } elseif (!Validate::isDateFormat($new_date_to)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter a valid Check out Date.'),
            )));
        } elseif ($new_date_from < $curr_date) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Check In date should not be after current date.'),
            )));
        } elseif ($new_date_to <= $new_date_from) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Check out Date Should be after Check In date.'),
            )));
        } elseif (!Validate::isUnsignedInt($product_quantity)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Invalid quantity.'),
            )));
        }

        $rooms_booked = $obj_booking_detail->getRoomBookinInformationForDateRangeByOrder($id_room, $old_date_from, $old_date_to, $new_date_from, $new_date_to);
        if ($rooms_booked) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('This Room Unavailable For Selected Duration.'),
            )));
        }

        // By webkul to calculate rates of the product from hotelreservation syatem tables with feature prices....
        $hotelCartBookingData = new HotelCartBookingData();
        $totalProductPriceBeforeTE = (float) $order_detail->total_price_tax_excl;
        $totalProductPriceBeforeTI = (float) $order_detail->total_price_tax_incl;
        $totalProductPriceAfterTE = 0;
        $totalProductPriceAfterTI = 0;
        $bookedRooms = $obj_booking_detail->getBookedRoomsByIdOrderDetail((int) Tools::getValue('order_detail_id'), $id_product);
        if ($bookedRooms) {
            foreach ($bookedRooms as $roomInfo) {
                if ($roomInfo['id_room'] == $id_room && (strtotime($roomInfo['date_from']) == strtotime($old_date_from))) {
                    $roomTotalPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($roomInfo['id_product'], $new_date_from, $new_date_to);
                    $totalProductPriceAfterTE += (float) $roomTotalPrice['total_price_tax_excl'];
                    $totalProductPriceAfterTI += (float) $roomTotalPrice['total_price_tax_incl'];
                } else {
                    $roomTotalPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($roomInfo['id_product'], $roomInfo['date_from'], $roomInfo['date_to']);
                    $totalProductPriceAfterTE += (float) $roomInfo['total_price_tax_excl'];
                    $totalProductPriceAfterTI += (float) $roomInfo['total_price_tax_incl'];
                }
            }
        }
        // END


        /*This code is commented by webkul because in our case quantity of the product will be number of days for which room is booked*/
        // If multiple product_quantity, the order details concern a product customized
        /*$product_quantity = 0;
        if (is_array(Tools::getValue('product_quantity')))
            foreach (Tools::getValue('product_quantity') as $id_customization => $qty)
            {
                // Update quantity of each customization
                Db::getInstance()->update('customization', array('quantity' => (int)$qty), 'id_customization = '.(int)$id_customization);
                // Calculate the real quantity of the product
                $product_quantity += $qty;
            }
        else
            $product_quantity = Tools::getValue('product_quantity');*/
        /*End*/
        $product_price_tax_incl = Tools::ps_round(Tools::getValue('product_price_tax_incl'), 2);
        $product_price_tax_excl = Tools::ps_round(Tools::getValue('product_price_tax_excl'), 2);
        // Calculate differences of price (Before / After)
        //$diff_price_tax_incl = $product_price_tax_incl * $qty_diff;
        $diff_price_tax_incl = $totalProductPriceAfterTI - $totalProductPriceBeforeTI;
        //$diff_price_tax_excl = $product_price_tax_excl * $qty_diff;
        $diff_price_tax_excl = $totalProductPriceAfterTE - $totalProductPriceBeforeTE;
        //var_dump($order_invoice);
        // Apply change on OrderInvoice
        if (isset($order_invoice)) {
            // If OrderInvoice to use is different, we update the old invoice and new invoice
            if ($order_detail->id_order_invoice != $order_invoice->id) {
                $old_order_invoice = new OrderInvoice($order_detail->id_order_invoice);
                // We remove cost of products
                $old_order_invoice->total_products -= $order_detail->total_price_tax_excl;
                $old_order_invoice->total_products_wt -= $order_detail->total_price_tax_incl;

                $old_order_invoice->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
                $old_order_invoice->total_paid_tax_incl -= $order_detail->total_price_tax_incl;

                $res &= $old_order_invoice->update();

                $order_invoice->total_products += $order_detail->total_price_tax_excl;
                $order_invoice->total_products_wt += $order_detail->total_price_tax_incl;

                $order_invoice->total_paid_tax_excl += $order_detail->total_price_tax_excl;
                $order_invoice->total_paid_tax_incl += $order_detail->total_price_tax_incl;

                $order_detail->id_order_invoice = $order_invoice->id;
            }
        }

        if ($diff_price_tax_incl != 0 && $diff_price_tax_excl != 0) {
            $order_detail->unit_price_tax_excl = $product_price_tax_excl;
            $order_detail->unit_price_tax_incl = $product_price_tax_incl;

            $order_detail->total_price_tax_incl += (float)$diff_price_tax_incl;
            $order_detail->total_price_tax_excl += (float)$diff_price_tax_excl;

            if (isset($order_invoice)) {
                // Apply changes on OrderInvoice
                $order_invoice->total_products += (float)$diff_price_tax_excl;
                $order_invoice->total_products_wt += (float)$diff_price_tax_incl;

                $order_invoice->total_paid_tax_excl += (float)$diff_price_tax_excl;
                $order_invoice->total_paid_tax_incl += (float)$diff_price_tax_incl;
            }

            // Apply changes on Order
            $order = new Order($order_detail->id_order);
            $order->total_products += (float)$diff_price_tax_excl;
            $order->total_products_wt += (float)$diff_price_tax_incl;
            $order->total_paid += (float)$diff_price_tax_incl;
            $order->total_paid_tax_excl += (float)$diff_price_tax_excl;
            $order->total_paid_tax_incl += (float)$diff_price_tax_incl;

            $res &= $order->update();
        }

        $old_quantity = $order_detail->product_quantity;

        $order_detail->product_quantity = $old_quantity + $qty_diff;
        $order_detail->reduction_percent = 0;

        // update taxes
        $res &= $order_detail->updateTaxAmount($order);

        // Save order detail
        $res &= $order_detail->update();

        // Update weight SUM
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float)$order->getTotalWeight();
            $res &= $order_carrier->update();
            if ($res) {
                $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        // Save order invoice
        if (isset($order_invoice)) {
            $res &= $order_invoice->update();
        }

        // Update product available quantity
        StockAvailable::updateQuantity($order_detail->product_id, $order_detail->product_attribute_id, ($old_quantity - $order_detail->product_quantity), $order->id_shop);

        $products = $this->getProducts($order);
        // Get the last product
        $product = $products[$order_detail->id];
        $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);
        $resume = OrderSlip::getProductSlipResume($order_detail->id);
        $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
        $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
        $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl']);

        if ($product['id_warehouse'] != 0) {
            $warehouse = new Warehouse((int)$product['id_warehouse']);
            $product['warehouse_name'] = $warehouse->name;
            $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
            if (!empty($warehouse_location)) {
                $product['warehouse_location'] = $warehouse_location;
            } else {
                $product['warehouse_location'] = false;
            }
        } else {
            $product['warehouse_name'] = '--';
            $product['warehouse_location'] = false;
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /** @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$order->id_shop);
            $invoice_array[] = $invoice;
        }

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(array(
            'product' => $product,
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            'can_edit' => $this->tabAccess['edit'],
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'current_index' => self::$currentIndex,
            'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
        ));

        if (!$res) {
            die(json_encode(array(
                'result' => $res,
                'error' => Tools::displayError('An error occurred while editing the product line.')
            )));
        }

        /*By webkul to edit the Hotel Cart and Hotel Order tables when editing the room for the order detail page*/
        $update_htl_tables = $obj_booking_detail->UpdateHotelCartHotelOrderOnOrderEdit($id_order, $id_room, $old_date_from, $old_date_to, $new_date_from, $new_date_to);

        if (is_array(Tools::getValue('product_quantity'))) {
            $view = $this->createTemplate('_customized_data.tpl')->fetch();
        } else {
            $view = $this->createTemplate('_product_line.tpl')->fetch();
        }

        $this->sendChangedNotification($order);

        die(json_encode(array(
            'result' => $res,
            'view' => $view,
            'can_edit' => $this->tabAccess['add'],
            'invoices_collection' => $invoice_collection,
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch(),
            'customized_product' => is_array(Tools::getValue('product_quantity'))
        )));
    }

    public function ajaxProcessDeleteProductLine()
    {
        $res = true;
        $order_detail = new OrderDetail((int) Tools::getValue('order_detail_id'));
        $id_order = (int) Tools::getValue('id_order');
        $order = new Order($id_order);
        /*By webkul To delete Order and cart entries when deleting rooms from the orderLine when editing the order*/

        $date_from = Tools::getValue('date_from');
        $date_to = Tools::getValue('date_to');
        $id_hotel = Tools::getValue('id_hotel');
        $id_room = Tools::getValue('id_room');
        $id_product = Tools::getValue('id_product');
        $obj_booking_detail = new HotelBookingDetail();
        $product_quantity = (int) $obj_booking_detail->getNumberOfDays($date_from, $date_to);
        /*By webkul to validate fields before deleting the cart and order data form the tables*/
        if ($id_hotel == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Hotel Id is mising.'),
            )));
        } elseif ($id_room == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Room Id is missing.'),
            )));
        } elseif ($date_from == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Check In Date is missing.'),
            )));
        } elseif ($date_to == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Check Out Date is missing.'),
            )));
        } elseif ($date_to == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Check Out Date is missing.'),
            )));
        } elseif (!Validate::isUnsignedInt($product_quantity)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Invalid quantity.'),
            )));
        }

        /*END*/
        $this->doDeleteProductLineValidation($order_detail, $order);
        $bookingInfo = $obj_booking_detail->getRowByIdOrderIdProductInDateRange($id_order, $id_product, $date_from, $date_to, $id_room);
        $idHotelBooking = $bookingInfo['id'];

        $bookingPriceTaxIncl = $bookingInfo['total_price_tax_incl'];
        $bookingPriceTaxExcl = $bookingInfo['total_price_tax_excl'];

        $objBookingDemand = new HotelBookingDemands();
        $roomExtraDemandTI = $objBookingDemand->getRoomTypeBookingExtraDemands(
            $id_order,
            $id_product,
            $id_room,
            $date_from,
            $date_to,
            0,
            1,
            1
        );
        $roomExtraDemandTE = $objBookingDemand->getRoomTypeBookingExtraDemands(
            $id_order,
            $id_product,
            $id_room,
            $date_from,
            $date_to,
            0,
            1,
            0
        );

        /*$totalProductPriceBeforeTE = $order_detail->total_price_tax_excl;
        $totalProductPriceBeforeTI = $order_detail->total_price_tax_incl;*/
        // by webkul to calculate rates of the product from hotelreservation syatem tables with feature prices....
        /* $hotelCartBookingData = new HotelCartBookingData();
         $roomTypesByIdProduct = $hotelCartBookingData->getCartInfoIdCartIdProduct($this->id, $product['id_product']);*/

        /*This code below to alter the values in the order detail table*/
        //$diff_products_tax_incl = $order_detail->unit_price_tax_incl * $product_quantity;
        //$diff_products_tax_excl = $order_detail->unit_price_tax_excl * $product_quantity;
        $diff_products_tax_incl = $bookingPriceTaxIncl;
        $diff_products_tax_excl = $bookingPriceTaxExcl;

        $delete = false;
        if ($product_quantity >= $order_detail->product_quantity) {
            $delete = true;
        } else {
            // Calculate differences of price (Before / After)

            $order_detail->total_price_tax_incl -= $diff_products_tax_incl;
            $order_detail->total_price_tax_excl -= $diff_products_tax_excl;

            $old_quantity = $order_detail->product_quantity;

            $order_detail->product_quantity = $old_quantity - $product_quantity;
            $order_detail->reduction_percent = 0;

            // update taxes
            $res &= $order_detail->updateTaxAmount($order);

            // Save order detail
            $res &= $order_detail->update();
        }
        /*End*/

        // Update OrderInvoice of this OrderDetail
        if ($order_detail->id_order_invoice != 0) {
            // values changes as values are calculated accoding to the quantity of the product by webkul
            $order_invoice = new OrderInvoice($order_detail->id_order_invoice);
            $order_invoice->total_paid_tax_excl -= ($diff_products_tax_excl + $roomExtraDemandTE);
            $order_invoice->total_paid_tax_incl -= ($diff_products_tax_incl + $roomExtraDemandTI);
            $order_invoice->total_products -= $diff_products_tax_excl;
            $order_invoice->total_products_wt -= $diff_products_tax_incl;
            $res &= $order_invoice->update();
        }

        // Update Order
        // values changes as values are calculated accoding to the quantity of the product by webkul
        $order->total_paid -= ($diff_products_tax_incl + $roomExtraDemandTI);
        $order->total_paid_tax_incl -= ($diff_products_tax_incl + $roomExtraDemandTI);
        $order->total_paid_tax_excl -= ($diff_products_tax_excl + $roomExtraDemandTE);
        $order->total_products -= $diff_products_tax_excl;
        $order->total_products_wt -= $diff_products_tax_incl;

        $res &= $order->update();

        // Reinject quantity in stock
        $this->reinjectQuantity($order_detail, $order_detail->product_quantity, $delete);

        // Update weight SUM
        $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float) $order->getTotalWeight();
            $res &= $order_carrier->update();
            if ($res) {
                $order->weight = sprintf('%.3f '.Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        if (!$res) {
            die(json_encode(array(
                'result' => $res,
                'error' => Tools::displayError('An error occurred while attempting to delete the product line.')
            )));
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /** @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$order->id_shop);
            $invoice_array[] = $invoice;
        }

        // delete the demands od this booking
        $objBookingDemand->deleteBookingDemands($idHotelBooking);

        /*By webkul to delete cart and order entries from cart and order tables of hotelreservationsystem when delete booking form the order line in order detaoil page*/
        $obj_htl_cart_booking = new HotelCartBookingData();
        $delete_room_order = $obj_htl_cart_booking->deleteOrderedRoomFromCart($id_order, $id_hotel, $id_room, $date_from, $date_to);

        $obj_htl_booking = new HotelBookingDetail();
        $delete_room_order = $obj_htl_booking->deleteOrderedRoomFromOrder($id_order, $id_hotel, $id_room, $date_from, $date_to);

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(array(
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'current_index' => self::$currentIndex
        ));

        $this->sendChangedNotification($order);

        die(json_encode(array(
            'result' => $res,
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch()
        )));
    }

    protected function doEditProductValidation(OrderDetail $order_detail, Order $order, OrderInvoice $order_invoice = null)
    {
        if (!Validate::isLoadedObject($order_detail)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The Order Detail object could not be loaded.')
            )));
        }

        if (!empty($order_invoice) && !Validate::isLoadedObject($order_invoice)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The invoice object cannot be loaded.')
            )));
        }

        if (!Validate::isLoadedObject($order)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The order object cannot be loaded.')
            )));
        }

        if ($order_detail->id_order != $order->id) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('You cannot edit the order detail for this order.')
            )));
        }

        // We can't edit a delivered order
        if ($order->hasBeenDelivered()) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('You cannot edit a delivered order.')
            )));
        }

        if (!empty($order_invoice) && $order_invoice->id_order != Tools::getValue('id_order')) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('You cannot use this invoice for the order')
            )));
        }

        // Clean price
        $product_price_tax_incl = str_replace(',', '.', Tools::getValue('product_price_tax_incl'));
        $product_price_tax_excl = str_replace(',', '.', Tools::getValue('product_price_tax_excl'));

        if (!Validate::isPrice($product_price_tax_incl) || !Validate::isPrice($product_price_tax_excl)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Invalid price')
            )));
        }
        //commented by webkul here quantity is gap between booking days which is calulated and validated in ajaxProcessEditProductOnOrder();
        /*if (!is_array(Tools::getValue('product_quantity')) && !Validate::isUnsignedInt(Tools::getValue('product_quantity')))
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Invalid quantity')
            )));
        elseif (is_array(Tools::getValue('product_quantity')))
            foreach (Tools::getValue('product_quantity') as $qty)
                if (!Validate::isUnsignedInt($qty))
                    die(json_encode(array(
                        'result' => false,
                        'error' => Tools::displayError('Invalid quantity')
                    )));*/
    }

    protected function doDeleteProductLineValidation(OrderDetail $order_detail, Order $order)
    {
        if (!Validate::isLoadedObject($order_detail)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The Order Detail object could not be loaded.')
            )));
        }

        if (!Validate::isLoadedObject($order)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('The order object cannot be loaded.')
            )));
        }

        if ($order_detail->id_order != $order->id) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('You cannot delete the order detail.')
            )));
        }

        // We can't edit a delivered order
        if ($order->hasBeenDelivered()) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('You cannot edit a delivered order.')
            )));
        }
    }

    /**
     * @param $order_detail
     * @param $add_quantity
     */
    protected function checkStockAvailable($order_detail, $add_quantity)
    {
        if ($add_quantity > 0) {
            $StockAvailable = StockAvailable::getQuantityAvailableByProduct($order_detail->product_id, $order_detail->product_attribute_id, $order_detail->id_shop);
            $product = new Product($order_detail->product_id, true, null, $order_detail->id_shop);
            if (!Validate::isLoadedObject($product)) {
                die(json_encode(array(
                    'result' => false,
                    'error' => Tools::displayError('The Product object could not be loaded.')
                )));
            } else {
                if (($StockAvailable < $add_quantity) && (!$product->isAvailableWhenOutOfStock((int)$product->out_of_stock))) {
                    die(json_encode(array(
                        'result' => false,
                        'error' => Tools::displayError('This product is no longer in stock with those attributes ')
                    )));
                }
            }
        }
    }

    /**
     * @param Order $order
     * @return array
     */
    protected function getProducts($order)
    {
        $products = $order->getProducts();

        foreach ($products as &$product) {
            if ($product['image'] != null) {
                $name = 'product_mini_'.(int)$product['product_id'].(isset($product['product_attribute_id']) ? '_'.(int)$product['product_attribute_id'] : '').'.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$product['image']->getExistingImgPath().'.jpg', $name, 45, 'jpg');
                if (file_exists(_PS_TMP_IMG_DIR_.$name)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
                } else {
                    $product['image_size'] = false;
                }
            }
        }

        ksort($products);

        return $products;
    }

    /**
     * @param OrderDetail $order_detail
     * @param int $qty_cancel_product
     * @param bool $delete
     */
    protected function reinjectQuantity($order_detail, $qty_cancel_product, $delete = false)
    {
        // Reinject product
        $reinjectable_quantity = (int)$order_detail->product_quantity - (int)$order_detail->product_quantity_reinjected;
        $quantity_to_reinject = $qty_cancel_product > $reinjectable_quantity ? $reinjectable_quantity : $qty_cancel_product;
        // @since 1.5.0 : Advanced Stock Management
        $product_to_inject = new Product($order_detail->product_id, false, (int)$this->context->language->id, (int)$order_detail->id_shop);

        $product = new Product($order_detail->product_id, false, (int)$this->context->language->id, (int)$order_detail->id_shop);

        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management && $order_detail->id_warehouse != 0) {
            $manager = StockManagerFactory::getManager();
            $movements = StockMvt::getNegativeStockMvts(
                $order_detail->id_order,
                $order_detail->product_id,
                $order_detail->product_attribute_id,
                $quantity_to_reinject
            );
            $left_to_reinject = $quantity_to_reinject;
            foreach ($movements as $movement) {
                if ($left_to_reinject > $movement['physical_quantity']) {
                    $quantity_to_reinject = $movement['physical_quantity'];
                }

                $left_to_reinject -= $quantity_to_reinject;
                if (Pack::isPack((int)$product->id)) {
                    // Gets items
                    if ($product->pack_stock_type == 1 || $product->pack_stock_type == 2 || ($product->pack_stock_type == 3 && Configuration::get('PS_PACK_STOCK_TYPE') > 0)) {
                        $products_pack = Pack::getItems((int)$product->id, (int)Configuration::get('PS_LANG_DEFAULT'));
                        // Foreach item
                        foreach ($products_pack as $product_pack) {
                            if ($product_pack->advanced_stock_management == 1) {
                                $manager->addProduct(
                                    $product_pack->id,
                                    $product_pack->id_pack_product_attribute,
                                    new Warehouse($movement['id_warehouse']),
                                    $product_pack->pack_quantity * $quantity_to_reinject,
                                    null,
                                    $movement['price_te'],
                                    true
                                );
                            }
                        }
                    }
                    if ($product->pack_stock_type == 0 || $product->pack_stock_type == 2 ||
                            ($product->pack_stock_type == 3 && (Configuration::get('PS_PACK_STOCK_TYPE') == 0 || Configuration::get('PS_PACK_STOCK_TYPE') == 2))) {
                        $manager->addProduct(
                            $order_detail->product_id,
                            $order_detail->product_attribute_id,
                            new Warehouse($movement['id_warehouse']),
                            $quantity_to_reinject,
                            null,
                            $movement['price_te'],
                            true
                        );
                    }
                } else {
                    $manager->addProduct(
                        $order_detail->product_id,
                        $order_detail->product_attribute_id,
                        new Warehouse($movement['id_warehouse']),
                        $quantity_to_reinject,
                        null,
                        $movement['price_te'],
                        true
                    );
                }
            }

            $id_product = $order_detail->product_id;
            if ($delete) {
                $order_detail->delete();
            }
            StockAvailable::synchronize($id_product);
        } elseif ($order_detail->id_warehouse == 0) {
            StockAvailable::updateQuantity(
                $order_detail->product_id,
                $order_detail->product_attribute_id,
                $quantity_to_reinject,
                $order_detail->id_shop
            );

            if ($delete) {
                $order_detail->delete();
            }
        } else {
            $this->errors[] = Tools::displayError('This product cannot be re-stocked.');
        }
    }

    /**
     * @param OrderInvoice $order_invoice
     * @param float $value_tax_incl
     * @param float $value_tax_excl
     */
    protected function applyDiscountOnInvoice($order_invoice, $value_tax_incl, $value_tax_excl)
    {
        // Update OrderInvoice
        $order_invoice->total_discount_tax_incl += $value_tax_incl;
        $order_invoice->total_discount_tax_excl += $value_tax_excl;
        $order_invoice->total_paid_tax_incl -= $value_tax_incl;
        $order_invoice->total_paid_tax_excl -= $value_tax_excl;
        $order_invoice->update();
    }

    public function ajaxProcessChangePaymentMethod()
    {
        $customer = new Customer(Tools::getValue('id_customer'));
        $modules = Module::getAuthorizedModules($customer->id_default_group);
        $authorized_modules = array();

        if (!Validate::isLoadedObject($customer) || !is_array($modules)) {
            die(json_encode(array('result' => false)));
        }

        foreach ($modules as $module) {
            $authorized_modules[] = (int)$module['id_module'];
        }

        $payment_modules = array();

        foreach (PaymentModule::getInstalledPaymentModules() as $p_module) {
            if (in_array((int)$p_module['id_module'], $authorized_modules)) {
                $payment_modules[] = Module::getInstanceById((int)$p_module['id_module']);
            }
        }

        $this->context->smarty->assign(array(
            'payment_modules' => $payment_modules,
        ));

        die(json_encode(array(
            'result' => true,
            'view' => $this->createTemplate('_select_payment.tpl')->fetch(),
        )));
    }

    /*By Webkul to delete the rooms added in the cart*/
    public function ajaxProcessDeleteRoomProcess()
    {
        $dt_frm = Tools::getValue('date_from');
        $dt_to = Tools::getValue('date_to');
        $cart_id = Tools::getValue('id_cart');
        $id_product = Tools::getValue('id_product');
        $del_id = Tools::getValue('del_id');
        $room_id = Tools::getValue('id_room');
        $obj_hotel_cart_detail = new HotelCartBookingData();
        $deleted = $obj_hotel_cart_detail->deleteRowById($del_id);
        if ($deleted) {
            $obj_product_process = new HotelCartBookingData();
            $date_from = date_create($dt_frm);
            $date_to = date_create($dt_to);
            $num_cart_rooms = $obj_product_process->getCountRoomsByIdCartIdProduct($cart_id, $id_product, $dt_frm, $dt_to);

            $diff = date_diff($date_from, $date_to);
            $changed = $obj_product_process->changeProductDataByRoomId($room_id, $id_product, $diff->days, $cart_id);
            if ($changed) {
                $result['status'] = 'deleted';
                $result['cart_rooms'] = $num_cart_rooms;
            } else {
                $result['status'] = 'failed';
            }
        } else {
            $result['status'] = 'failed';
        }

        die(json_encode($result));
    }

    // To show rooms extra demands in the modal box in order details view page
    public function ajaxProcessGetRoomTypeBookingDemands()
    {
        $extraDemandsTpl = '';
        if (($idProduct = Tools::getValue('id_product'))
            && ($idOrder = Tools::getValue('id_order'))
            && ($idRoom = Tools::getValue('id_room'))
            && ($dateFrom = Tools::getValue('date_from'))
            && ($dateTo = Tools::getValue('date_to'))
        ) {
            $smartyVars = array();
            $objOrder = new Order($idOrder);
            $smartyVars['orderCurrency'] = $objOrder->id_currency;

            $objBookingDemand = new HotelBookingDemands();
            if ($extraDemands = $objBookingDemand->getRoomTypeBookingExtraDemands(
                $idOrder,
                $idProduct,
                $idRoom,
                $dateFrom,
                $dateTo
            )) {
                $smartyVars['extraDemands'] = $extraDemands;
            }

            // if admin is editing order
            if ($orderEdit = Tools::getValue('orderEdit')) {
                $smartyVars['orderEdit'] = $orderEdit;

                // get room type additional demands
                $objRoomDemands = new HotelRoomTypeDemand();
                if ($roomTypeDemands = $objRoomDemands->getRoomTypeDemands($idProduct)) {
                    foreach ($roomTypeDemands as &$demand) {
                        // if demand has advance options then set demand price as first advance option price.
                        if (isset($demand['adv_option']) && $demand['adv_option']) {
                            $demand['price'] = current($demand['adv_option'])['price'];
                        }
                    }
                    $smartyVars['roomTypeDemands'] = $roomTypeDemands;
                }
            }

            $this->context->smarty->assign($smartyVars);
            $extraDemandsTpl .= $this->context->smarty->fetch(
                _PS_ADMIN_DIR_.'/themes/default/template/controllers/orders/_room_extra_demands.tpl'
            );
        }
        die($extraDemandsTpl);
    }

    // Process to get extra demands of any room while order creation process form.tpl
    public function ajaxProcessGetRoomTypeCartDemands()
    {
        $extraDemandsTpl = '';
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
                        $extraDemandsTpl .= $this->context->smarty->fetch(
                            _PS_ADMIN_DIR_.'/themes/default/template/controllers/orders/_cart_booking_demands.tpl'
                        );
                    }
                }
            }
        }
        die($extraDemandsTpl);
    }

    // Process when admin changes extra demands of any room while order creation process form.tpl
    public function ajaxProcessChangeRoomDemands()
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

    // Process when admin edit rooms and edit rooms additional facilities
    public function ajaxProcessEditRoomExtraDemands()
    {
        if ($idHtlBooking = Tools::getValue('id_htl_booking')) {
            if (Validate::isLoadedObject($objBookingDetail = new HotelBookingDetail($idHtlBooking))) {
                $roomDemands = Tools::getValue('room_demands');
                if ($roomDemands = json_decode($roomDemands, true)) {
                    $order = new Order($objBookingDetail->id_order);
                    $vatAddress = new Address((int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                    $idLang = (int)$order->id_lang;
                    $idProduct = $objBookingDetail->id_product;
                    $objHtlBkDtl = new HotelBookingDetail();
                    $objRoomDemandPrice = new HotelRoomTypeDemandPrice();
                    foreach ($roomDemands as $demand) {
                        $idGlobalDemand = $demand['id_global_demand'];
                        $idOption = $demand['id_option'];
                        $objBookingDemand = new HotelBookingDemands();
                        $objBookingDemand->id_htl_booking = $idHtlBooking;
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
                            $numDays = $objHtlBkDtl->getNumberOfDays(
                                $objHtlBkDtl->date_from,
                                $objHtlBkDtl->date_to
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

                    die('1');
                }
            }
        }
        die('0');
    }

    // delete room extra demand while order edit
    public function ajaxProcessDeleteRoomExtraDemand()
    {
        if ($idBookingDemand = Tools::getValue('id_booking_demand')) {
            if (Validate::isLoadedObject($objBookingDemand = new HotelBookingDemands($idBookingDemand))) {
                // first delete the tax details of the booking demand
                if ($objBookingDemand->deleteBookingDemandTaxDetails($idBookingDemand)) {
                    if ($objBookingDemand->delete()) {
                        die('1');
                    }
                }
            }
        }
        die('0');
    }

    // To change the status of the room
    public function changeRoomStatus()
    {
        $idRoom = (int) Tools::getValue('id_room');
        $idOrder = (int) Tools::getValue('id_order');
        $dateFrom = Tools::getValue('date_from');
        $dateTo = Tools::getValue('date_to');
        $newStatus = (int) Tools::getValue('booking_order_status');

        // date choosen for the status change
        if ($statusDate = Tools::getValue('status_date')) {
            $statusDate = date('Y-m-d', strtotime($statusDate));
        }
        // Lets validate the fields
        if (!$idRoom) {
            $this->errors[] = Tools::displayError('Room information not found.');
        }
        if (!$idOrder) {
            $this->errors[] = Tools::displayError('Order information not found.');
        }
        if (!$dateFrom
            || !$dateTo
            || !Validate::isDate($dateFrom)
            || !Validate::isDate($dateTo)
        ) {
            $this->errors[] = Tools::displayError('Invalid dates found.');
        }

        if (!$newStatus) {
            $this->errors[] = Tools::displayError('Invalid booking status found.');
        } elseif (
            $newStatus == HotelBookingDetail::STATUS_CHECKED_IN
            || $newStatus == HotelBookingDetail::STATUS_CHECKED_OUT
        ) {
            if (!$statusDate || !Validate::isDate($statusDate)) {
                $this->errors[] = Tools::displayError('Invalid dates found.');
            } elseif ((strtotime($statusDate) < strtotime($dateFrom))
                || (strtotime($statusDate) > strtotime($dateTo))
            ) {
                $this->errors[] = Tools::displayError('Invalid dates found.');
            }
        }

        if (!count($this->errors)) {
            $objBookingDetail = new HotelBookingDetail();
            if ($roomBookingInfo = $objBookingDetail->getRoomBookingData($idRoom, $idOrder, $dateFrom, $dateTo)) {
                //  if admin choose Check-Out status
                if ($newStatus == HotelBookingDetail::STATUS_CHECKED_OUT
                    && $roomBookingInfo['check_in'] ==  '0000-00-00 00:00:00'
                ) {
                    $this->errors[] = Tools::displayError('Room status must be set to Check-In before setting the room status to Check-Out.');
                } elseif ($newStatus == HotelBookingDetail::STATUS_CHECKED_OUT
                    && $roomBookingInfo['check_in'] !=  '0000-00-00 00:00:00'
                    && strtotime($roomBookingInfo['check_in']) >= strtotime($statusDate)
                ) {
                    $this->errors[] = Tools::displayError('Check-Out date can not be before Check-In date').
                    '('.date('d-m-Y', strtotime($roomBookingInfo['check_in'])).')';
                } elseif ($newStatus == HotelBookingDetail::STATUS_CHECKED_IN && $roomBookingInfo['check_out'] ==  '0000-00-00 00:00:00'
                    && strtotime($roomBookingInfo['check_in']) >= strtotime($dateTo)
                ) {
                    $this->errors[] = Tools::displayError('Check-In date can not be after Check-Out date').
                    '('.date('d-m-Y', strtotime($roomBookingInfo['date_to'])).')';
                } elseif ($newStatus == HotelBookingDetail::STATUS_CHECKED_IN && $roomBookingInfo['check_out'] !=  '0000-00-00 00:00:00'
                    && strtotime($roomBookingInfo['check_out']) <= strtotime($statusDate)
                ) {
                    $this->errors[] = Tools::displayError('Check-In date can not be after Check-Out date').
                    '('.date('d-m-Y', strtotime($roomBookingInfo['check_out'])).')';
                } else {
                    if ($objBookingDetail->updateBookingOrderStatusByOrderId(
                        $idOrder,
                        $newStatus,
                        $idRoom,
                        $dateFrom,
                        $dateTo,
                        $statusDate
                    )) {
                        Hook::exec(
                            'actionRoomBookingStatusUpdateAfter',
                            array(
                                'id_order' => $idOrder,
                                'id_room' => $idRoom,
                                'date_from' => $dateFrom,
                                'date_to' => $dateTo
                            )
                        );

                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.(int) $idOrder.'&vieworder&token='.$this->token.'&conf=4');
                    } else {
                        $this->errors[] = Tools::displayError('Some error occurred. Please try again.');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('Invalid booking information. Please try again.');
            }
        }
    }
}
