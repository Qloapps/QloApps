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
    protected $all_order_sources = array();

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
        CONCAT(c.`firstname`, \' \', c.`lastname`) AS `customer`,
        osl.`name` AS `osname`, os.`color`,
        IF((SELECT so.id_order FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
        IF(a.valid, 1, 0) badge_success,
        hbil.`hotel_name`,
        (SELECT COUNT(hbd.`id`) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd WHERE hbd.`id_order` = a.`id_order`) as num_rooms,
        (SELECT SUM(hbd.`adults`) + SUM(hbd.`children`) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd WHERE hbd.`id_order` = a.`id_order`) as total_guests_count,
        (SELECT CONCAT(
            SUM(hbd.`adults`),
            \' '.$this->l('Adult(s)').' \',
            IF(SUM(hbd.`children`), CONCAT(SUM(hbd.`children`), \' '.$this->l('Children').'\'), \'\')
        ) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd WHERE hbd.`id_order` = a.`id_order`) as total_guests,
        (SELECT SUM(DATEDIFF(hbd.`date_to`, hbd.`date_from`)) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd WHERE hbd.`id_order` = a.`id_order`) as los';

        $this->_join = '
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
        LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
        LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int) $this->context->language->id.')
        LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id_order` = a.`id_order`)
        LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil ON (hbil.`id` = hbd.`id_hotel`)';

        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;

        $this->_group = ' GROUP BY hbd.`id_order`';

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }
        $all_order_sources = Db::getInstance()->executeS('SELECT DISTINCT(`source`) FROM  `'._DB_PREFIX_.'orders`');
        foreach ($all_order_sources as $source) {
            $this->all_order_sources[$source['source']] = $source['source'];
        }

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'reference' => array(
                'title' => $this->l('Reference')
            ),
            'customer' => array(
                'title' => $this->l('Customer'),
                'havingFilter' => true,
                'optional' => true,
                'visible_default' => true
            ),
            'order_source' => array(
                'title' => $this->l('Order Source'),
                'type' => 'select',
                'filter_key' => 'a!source',
                'list' => $this->all_order_sources,
                'optional' => true,
                'visible_default' => true
            ),
            'hotel_name' => array(
                'title' => $this->l('Hotel'),
                'filter_key' => 'hbil!hotel_name',
                'optional' => true,
                'visible_default' => true
            ),
            'date_from' => array(
                'title' => $this->l('Check-in'),
                'filter_key' => 'hbd!date_from',
                'type'=>'date',
                'displayed' => false,
            ),
            'date_to' => array(
                'title' => $this->l('Check-out'),
                'filter_key' => 'hbd!date_to',
                'type'=>'date',
                'displayed' => false,
            ),
            'room_type_name' => array(
                'title' => $this->l('Room type'),
                'filter_key' => 'hbd!room_type_name',
                'type'=>'text',
                'displayed' => false,
            ),
            'total_guests' => array(
                'title' => $this->l('Guests'),
                'type' => 'range',
                'filter_key' => 'total_guests_count',
                'optional' => true,
                'havingFilter' => true,
            ),
            'num_rooms' => array(
                'title' => $this->l('No. of rooms'),
                'align' => 'text-center',
                'type' => 'range',
                'optional' => true,
                'havingFilter' => true,
                'visible_default' => true
            ),
            'los' => array(
                'title' => $this->l('Stay period'),
                'align' => 'text-center',
                'type' => 'range',
                'havingFilter' => true,
                'optional' => true,
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
                'title' => $this->l('Order Total'),
                'align' => 'text-right',
                'type' => 'range',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true,
            ),
            'amount_due' => array(
                'title' => $this->l('Due Amount'),
                'align' => 'text-right',
                'type' => 'range',
                'currency' => true,
                'havingFilter' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true,
                'optional' => true,
                'visible_default' => true
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
                'order_key' => 'osname',
                'optional' => true,
                'visible_default' => true
            ),
            'date_add' => array(
                'title' => $this->l('Order date'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
                'optional' => true,
                'visible_default' => true
            ),
            'id_pdf' => array(
                'title' => $this->l('PDF'),
                'align' => 'text-center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
                'optional' => true,
            )
        ));

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
                $this->context->currency = new Currency((int)$cart->id_currency);
                $cart_detail_data = array();
                $cart_detail_data_obj = new HotelCartBookingData();
                $objHotelServiceProductCartDetail = new HotelServiceProductCartDetail();
                if ($cart_detail_data = $cart_detail_data_obj->getCartFormatedBookinInfoByIdCart((int) $id_cart)) {
                    $objRoomType = new HotelRoomType();
                    foreach ($cart_detail_data as $key => $cart_data) {

                        $cart_detail_data[$key]['room_type_info'] = $objRoomType->getRoomTypeInfoByIdProduct($cart_data['id_product']);
                    }
                    $this->context->smarty->assign('cart_detail_data', $cart_detail_data);
                }
                if ($normalCartProduct = $objHotelServiceProductCartDetail->getHotelProducts($this->context->cart->id)) {
                    $this->context->smarty->assign('cart_normal_data', $normalCartProduct);
                }

                if (empty($cart_detail_data) && empty($normalCartProduct)) {
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
            'awaiting_payment' => (int)Configuration::get('PS_OS_AWAITING_PAYMENT'),
            'other' => (int)Configuration::get('PS_OS_PAYMENT_ACCEPTED'));
        $payment_modules = array();
        foreach (PaymentModule::getInstalledPaymentModules() as $p_module) {
            $payment_modules[] = Module::getInstanceById((int)$p_module['id_module']);
        }

        $occupancyRequiredForBooking = false;
        if (Configuration::get('PS_BACKOFFICE_ROOM_BOOKING_TYPE') == HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY) {
            $occupancyRequiredForBooking = true;
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
            'title' => array($this->l('Orders'), $this->l('Create order')),
            'currency' => new Currency((int)$cart->id_currency),
            'max_child_in_room' => Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM'),
            'max_child_age' => Configuration::get('WK_GLOBAL_CHILD_MAX_AGE'),
            'occupancy_required_for_booking' => $occupancyRequiredForBooking,
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

            if ($idHotel = HotelBookingDetail::getIdHotelByIdOrder($order->id)) {
                $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
                $this->toolbar_title[] = sprintf($this->l('Order %1$s - %2$s'), $order->reference, $objHotelBranchInformation->hotel_name);
            } else {
                $this->toolbar_title[] = sprintf($this->l('Order %1$s'), $order->reference);
            }
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

    public function initModal()
    {
        parent::initModal();
        $this->modals[] = $this->getBookingDocumentsModal();
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJqueryUI('ui.datepicker');
        $this->addJS(_PS_JS_DIR_.'vendor/d3.v3.min.js');

        if ($this->tabAccess['edit'] == 1 && $this->display == 'view') {
            $this->addJS(_PS_JS_DIR_.'admin/orders.js');
            // $this->addJS(_PS_JS_DIR_.'admin/orders-product-event.js');
            // $this->addJS(_PS_JS_DIR_.'admin/orders-room-event.js');
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
                    $objBookingDetail = new HotelBookingDetail();
                    $room_swapped = $objBookingDetail->reallocateRoomWithAvailableSameRoomType($current_room_id, $date_from, $date_to, $realloc_room_id);
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
                    $objBookingDetail = new HotelBookingDetail();
                    $room_swapped = $objBookingDetail->swapRoomWithAvailableSameRoomType($current_room_id, $date_from, $date_to, $swapped_room_id);
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
                    $objOrderReturn->state = Configuration::get('PS_ORS_PENDING');
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
                $payment_type = Tools::getValue('payment_type');
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
                } elseif (!Validate::isUnsignedInt($payment_type)) {
                    $this->errors[] = Tools::displayError('Payment source is invalid');
                } else {
                    if (!$order->addOrderPayment(
                        $amount,
                        Tools::getValue('payment_method'),
                        Tools::getValue('payment_transaction_id'),
                        $currency,
                        Tools::getValue('payment_date'),
                        $order_invoice,
                        $payment_type
                    )) {
                        if (!validate::isPrice($order->total_paid_real)) {
                            $this->errors[] = Tools::displayError('Order total payments cannot be less than 0.');
                        } else {
                            $this->errors[] = Tools::displayError('An error occurred during payment.');
                        }
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

                    if (isset($this->context->cookie->id_cart)) {
                        unset($this->context->cookie->id_cart);
                    }
                    if (isset($this->context->cookie->id_guest)) {
                        unset($this->context->cookie->id_guest);
                    }

                    if ($payment_module->currentOrder) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$payment_module->currentOrder.'&vieworder'.'&token='.$this->token.'&conf=3');
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

    // public function renderKpis()
    // {
    //     $time = time();
    //     $kpis = array();

    //     $helper = new HelperKpi();
    //     $helper->id = 'box-conversion-rate';
    //     $helper->icon = 'icon-sort-by-attributes-alt';
    //     //$helper->chart = true;
    //     $helper->color = 'color1';
    //     $helper->title = $this->l('Conversion Rate', null, null, false);
    //     $helper->subtitle = $this->l('30 days', null, null, false);
    //     if (ConfigurationKPI::get('CONVERSION_RATE_CHART') !== false) {
    //         $helper->data = ConfigurationKPI::get('CONVERSION_RATE_CHART');
    //     }
    //     $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=conversion_rate';
    //     $kpis[] = $helper->generate();

    //     $helper = new HelperKpi();
    //     $helper->id = 'box-carts';
    //     $helper->icon = 'icon-shopping-cart';
    //     $helper->color = 'color2';
    //     $helper->title = $this->l('Abandoned Carts', null, null, false);
    //     $helper->subtitle = $this->l('Today', null, null, false);
    //     $helper->href = $this->context->link->getAdminLink('AdminCarts').'&action=filterOnlyAbandonedCarts';
    //     $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=abandoned_cart';
    //     $kpis[] = $helper->generate();

    //     $helper = new HelperKpi();
    //     $helper->id = 'box-average-order';
    //     $helper->icon = 'icon-money';
    //     $helper->color = 'color3';
    //     $helper->title = $this->l('Average Order Value', null, null, false);
    //     $helper->subtitle = $this->l('30 days', null, null, false);
    //     $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=average_order_value';
    //     $kpis[] = $helper->generate();

    //     $helper = new HelperKpi();
    //     $helper->id = 'box-net-profit-visit';
    //     $helper->icon = 'icon-user';
    //     $helper->color = 'color4';
    //     $helper->title = $this->l('Net Profit per Visit', null, null, false);
    //     $helper->subtitle = $this->l('30 days', null, null, false);
    //     $helper->source = $this->context->link->getAdminLink('AdminStats').'&ajax=1&action=getKpi&kpi=netprofit_visit';
    //     $kpis[] = $helper->generate();

    //     $helper = new HelperKpiRow();
    //     $helper->kpis = $kpis;
    //     return $helper->generate();
    // }

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

        // get details if booking is done for some other guest
        $customerGuestDetail = false;
        if ($id_customer_guest_detail = OrderCustomerGuestDetail::isCustomerGuestBooking($order->id)) {
            $customerGuestDetail = new OrderCustomerGuestDetail($id_customer_guest_detail);
            $customerGuestDetail->gender = new Gender($customerGuestDetail->id_gender, $this->context->language->id);
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

        $orderServiceProducts = array();
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

            // add service products in order detail
            if ($product['product_service_type'] == Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE) {
                $orderServiceProducts[] = $product;
            }
        }

        $gender = new Gender((int)$customer->id_gender, $this->context->language->id);

        $history = $order->getHistory($this->context->language->id);

        foreach ($history as &$order_state) {
            $order_state['text-color'] = Tools::getBrightness($order_state['color']) < 128 ? 'white' : 'black';
        }

        $order_payment_detail = $order->getOrderPaymentDetail();
        foreach ($order_payment_detail as &$payment_detail) {
            $payment = new OrderPayment($payment_detail['id_order_payment']);
            if ($invoice = $payment->getOrderInvoice($order->id)) {
                $payment_detail['invoice_number'] = $invoice->getInvoiceNumberFormatted($this->context->language->id, $order->id_shop);
            }
        }


        //by webkul to get data to show hotel rooms order data on order detail page

        $cart_id = Cart::getCartIdByOrderId(Tools::getValue('id_order'));
        $order_detail_data = array();
        $cart_detail_data_obj = new HotelCartBookingData();
        $objBookingDetail = new HotelBookingDetail();
        $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();

        $total_room_tax = 0;
        $totalRoomsCostTE = 0;
        $totalConvenienceFeeTE = 0;
        $totalConvenienceFeeTI = 0;
        $totalDemandsPriceTE = 0;
        $totalDemandsPriceTI = 0;
        if ($order_detail_data = $objBookingDetail->getOrderFormatedBookinInfoByIdOrder($order->id)) {
            $objBookingDemand = new HotelBookingDemands();
            $objHotelRoomType = new HotelRoomType();
            foreach ($order_detail_data as $key => $value) {
                $order_detail_data[$key]['total_room_price_te'] = $value['total_price_tax_excl'];
                $order_detail_data[$key]['total_room_price_ti'] = $value['total_price_tax_incl'];

                $order_detail_data[$key]['extra_demands'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
                    $order->id,
                    $value['id_product'],
                    $value['id_room'],
                    $value['date_from'],
                    $value['date_to']
                );
                $order_detail_data[$key]['total_room_price_ti'] += $order_detail_data[$key]['extra_demands_price_ti'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
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
                $order_detail_data[$key]['total_room_price_te'] += $order_detail_data[$key]['extra_demands_price_te'] = $objBookingDemand->getRoomTypeBookingExtraDemands(
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

                $order_detail_data[$key]['additional_services'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                    $order->id,
                    0,
                    0,
                    $value['id_product'],
                    $value['date_from'],
                    $value['date_to'],
                    $value['id_room']
                );
                $order_detail_data[$key]['total_room_price_ti'] += $order_detail_data[$key]['additional_services_price_ti'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                    $order->id,
                    0,
                    0,
                    $value['id_product'],
                    $value['date_from'],
                    $value['date_to'],
                    $value['id_room'],
                    1,
                    1
                );
                $order_detail_data[$key]['total_room_price_te'] += $order_detail_data[$key]['additional_services_price_te'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                    $order->id,
                    0,
                    0,
                    $value['id_product'],
                    $value['date_from'],
                    $value['date_to'],
                    $value['id_room'],
                    1,
                    0
                );

                $order_detail_data[$key]['convenience_fee_ti'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                    $order->id,
                    0,
                    0,
                    $value['id_product'],
                    $value['date_from'],
                    $value['date_to'],
                    $value['id_room'],
                    1,
                    1,
                    1,
                    Product::PRICE_ADDITION_TYPE_INDEPENDENT
                );
                $order_detail_data[$key]['total_room_price_ti'] += $order_detail_data[$key]['convenience_fee_ti'];
                $totalConvenienceFeeTI += $order_detail_data[$key]['convenience_fee_ti'];

                $order_detail_data[$key]['convenience_fee_te'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                    $order->id,
                    0,
                    0,
                    $value['id_product'],
                    $value['date_from'],
                    $value['date_to'],
                    $value['id_room'],
                    1,
                    0,
                    1,
                    Product::PRICE_ADDITION_TYPE_INDEPENDENT
                );

                $order_detail_data[$key]['total_room_price_te'] += $order_detail_data[$key]['convenience_fee_te'];
                $totalConvenienceFeeTE += $order_detail_data[$key]['convenience_fee_te'];

                $order_detail_data[$key]['additional_services_price_auto_add_ti'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                    $order->id,
                    0,
                    0,
                    $value['id_product'],
                    $value['date_from'],
                    $value['date_to'],
                    $value['id_room'],
                    1,
                    1,
                    1,
                    Product::PRICE_ADDITION_TYPE_WITH_ROOM
                );
                $order_detail_data[$key]['total_room_price_ti'] += $order_detail_data[$key]['additional_services_price_auto_add_ti'];

                $order_detail_data[$key]['additional_services_price_auto_add_te'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                    $order->id,
                    0,
                    0,
                    $value['id_product'],
                    $value['date_from'],
                    $value['date_to'],
                    $value['id_room'],
                    1,
                    0,
                    1,
                    Product::PRICE_ADDITION_TYPE_WITH_ROOM
                );
                $order_detail_data[$key]['total_room_price_te'] += $order_detail_data[$key]['additional_services_price_auto_add_te'];

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
                $order_detail_data[$key]['room_type_info'] = $objHotelRoomType->getRoomTypeInfoByIdProduct($value['id_product']);
                $order_detail_data[$key]['total_room_tax'] = $order_detail_data[$key]['total_room_price_ti'] - $order_detail_data[$key]['total_room_price_te'];
            }
        }

        $objOrderReturn = new OrderReturn();
        $refundedAmount = 0;
        if ($refundReqBookings = $objOrderReturn->getOrderRefundRequestedBookings($order->id, 0, 1)) {
            $refundedAmount = $objOrderReturn->getRefundedAmount($order->id);
        }

        // get booking information by order
        $bookingOrderInfo = $objBookingDetail->getBookingDataByOrderId($order->id);
        foreach ($bookingOrderInfo as &$bookingOrderRoomInfo) {
            $bookingOrderRoomInfo['num_checkin_documents'] = HotelBookingDocument::getCountByIdHtlBooking($bookingOrderRoomInfo['id']);
        }

        $objHotelBookingDetail = new HotelBookingDetail();
        $htlBookingDetail = $objHotelBookingDetail->getOrderCurrentDataByOrderId($order->id);
        $isCancelledRoom = in_array(1, array_column($htlBookingDetail, 'is_cancelled'));

        // hotel booking statuses
        $htlOrderStatus = HotelBookingDetail::getAllHotelOrderStatus();

        // applicable refund policies
        $applicableRefundPolicies = HotelOrderRefundRules::getApplicableRefundRules($order->id);
        $this->tpl_view_vars = array(
            // refund info
            'refund_allowed' => (int) $order->isReturnable(),
            'applicable_refund_policies' => $applicableRefundPolicies,
            'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
            'refundReqBookings' => $refundReqBookings,
            'hasCompletelyRefunded' => $order->hasCompletelyRefunded(),
            'refundedAmount' => $refundedAmount,
            'totalDemandsPriceTI' => $totalDemandsPriceTI,
            'totalDemandsPriceTE' => $totalDemandsPriceTE,
            'totalConvenienceFeeTI' => $totalConvenienceFeeTI,
            'totalConvenienceFeeTE' => $totalConvenienceFeeTE,
            'totalRoomsCostTE' => $totalRoomsCostTE,
            'total_room_tax' => $total_room_tax,
            'htl_booking_order_data' => $bookingOrderInfo,
            'hotel_order_status' => $htlOrderStatus,
            'order_detail_data' => $order_detail_data,
            'max_child_in_room' => Configuration::get('WK_GLOBAL_MAX_CHILD_IN_ROOM'),
            'max_child_age' => Configuration::get('WK_GLOBAL_CHILD_MAX_AGE'),
            'order_service_products' => $orderServiceProducts,
            /*END*/
            'order' => $order,
            'cart' => new Cart($order->id_cart),
            'customer' => $customer,
            'gender' => $gender,
            'customerGuestDetail' => $customerGuestDetail,
            'genders' => Gender::getGenders(),
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
            'total_paid' => $order->getTotalPaid(),
            'customer_thread_message' => CustomerThread::getCustomerMessages($order->id_customer, null, $order->id),
            'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
            'messages' => Message::getMessagesByOrderId($order->id, true),
            'carrier' => new Carrier($order->id_carrier),
            'history' => $history,
            'order_payment_detail' => $order_payment_detail,
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
            'payment_types' => $this->getPaymentsTypes(),
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
            'isCancelledRoom' => $isCancelledRoom,
        );

        return parent::renderView();
    }

    public function getBookingDocumentsModal()
    {
        $modalContent = $this->context->smarty->fetch('controllers/orders/_booking_documents_modal.tpl');

        // set modal details
        $modal = array(
            'modal_id' => 'booking-documents-modal',
            'modal_class' => 'modal-md',
            'modal_title' => $this->l('Documents'),
            'modal_content' => $modalContent,
            'modal_actions' => array(), // required to show Close button
        );

        return $modal;
    }

    public function ajaxProcessGetBookingDocuments()
    {
        $response = array('status' => false);

        $idHtlBooking = (int) Tools::getValue('id_htl_booking');
        $objHotelBookingDetail = new HotelBookingDetail($idHtlBooking);
        if (Validate::isLoadedObject($objHotelBookingDetail)) {
            $response['html'] = $this->getRenderedBookingDocuments($idHtlBooking);
            $response['status'] = true;
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessUploadBookingDocument()
    {
        $response = array('status' => false);

        $idHtlBooking = (int) Tools::getValue('id_htl_booking');
        $title = Tools::getValue('title');

        // validations
        $objHotelBookingDetail = new HotelBookingDetail($idHtlBooking);
        if (!Validate::isLoadedObject($objHotelBookingDetail)) {
            $this->errors[] = $this->l('Booking detail not found.');
        }

        if (!$title) {
            $title = '--';
        } else {
            if (!Validate::isCatalogName($title)) {
                $this->errors[] = $this->l('Please enter a valid Title.');
            }
        }

        $objHotelBookingDocument = new HotelBookingDocument();
        $objHotelBookingDocument->setFileInfoForUploadedDocument('booking_document');
        if (!count($objHotelBookingDocument->fileInfo)) {
            $this->errors[] = $this->l('Please select a file to upload.');
        } elseif ($objHotelBookingDocument->fileInfo['size'] > Tools::getMaxUploadSize()) {
            $this->errors[] = $this->l('Uploaded file size is too large.');
        } elseif(!(ImageManager::isRealImage($objHotelBookingDocument->fileInfo['tmp_name'])
            || $objHotelBookingDocument->fileInfo['mime'] == 'application/pdf')
        ) {
            $this->errors[] = $this->l('Please upload an image or a PDF file only. Allowed image formats: .gif, .jpg, .jpeg and .png');
        }

        if (!count($this->errors)) {
            $objHotelBookingDocument = new HotelBookingDocument();
            $objHotelBookingDocument->setFileInfoForUploadedDocument('booking_document');
            $objHotelBookingDocument->id_htl_booking = $idHtlBooking;
            $objHotelBookingDocument->title = $title;
            $objHotelBookingDocument->setFileType();
            if ($objHotelBookingDocument->save()) {
                $objHotelBookingDocument->saveDocumentFile();

                $response['html'] = $this->getRenderedBookingDocuments($idHtlBooking);
                $response['num_checkin_documents'] = HotelBookingDocument::getCountByIdHtlBooking($idHtlBooking);
                $response['status'] = true;
            } else {
                $this->errors[] = $this->l('Document upload failed.');
            }
        } else {
            $this->context->smarty->assign(array(
                'errors' => $this->errors,
            ));

            $response['errors'] = $this->context->smarty->fetch('alerts.tpl');
            $response['status'] = false;
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessDeleteBookingDocument()
    {
        $response = array('status' => false);

        $idHtlBookingDocument = (int) Tools::getValue('id_htl_booking_document');

        $objHotelBookingDocument = new HotelBookingDocument($idHtlBookingDocument);
        if (Validate::isLoadedObject($objHotelBookingDocument)) {
            $idHtlBooking = $objHotelBookingDocument->id_htl_booking;
            if ($objHotelBookingDocument->delete()) {
                $response['html'] = $this->getRenderedBookingDocuments($idHtlBooking);
                $response['num_checkin_documents'] = HotelBookingDocument::getCountByIdHtlBooking($idHtlBooking);
                $response['status'] = true;
            }
        }

        $this->ajaxDie(json_encode($response));
    }

    public function ajaxProcessUpdateGuestDetails()
    {
        $response = array(
            'success' => false
        );
        if (Validate::isLoadedObject($order = new Order(Tools::getValue('id_order')))) {
            if ($id_customer_guest_detail = OrderCustomerGuestDetail::isCustomerGuestBooking($order->id)) {
                if (Validate::isLoadedObject($objCustomerGuestDetail = new OrderCustomerGuestDetail($id_customer_guest_detail))) {
                    $id_gender = Tools::getValue('id_gender');
                    $firstname = Tools::getValue('firstname');
                    $lastname = Tools::getValue('lastname');
                    $email = Tools::getValue('email');
                    $phone = Tools::getValue('phone');
                    $objCustomerGuestDetail->id_gender = $id_gender;
                    $objCustomerGuestDetail->firstname = $firstname;
                    $objCustomerGuestDetail->lastname = $lastname;
                    $objCustomerGuestDetail->email = $email;
                    $objCustomerGuestDetail->phone = $phone;
                    if ($objCustomerGuestDetail->validateGuestInfo()) {
                        if ($objCustomerGuestDetail->save()) {
                            $response['success'] = true;
                            $gender = new Gender($objCustomerGuestDetail->id_gender, $this->context->language->id);
                            $response['data']['guest_name'] = $gender->name.' '.$objCustomerGuestDetail->firstname.' '.$objCustomerGuestDetail->lastname ;
                            $response['data']['guest_email'] = $objCustomerGuestDetail->email;
                            $response['data']['guest_phone'] = $objCustomerGuestDetail->phone;
                            $response['msg'] = $this->l('Guest details are updated.');
                        } else {
                            $response['errors'][] = $this->l('Unable to save guest details.');
                        }
                    } else {
                        $response['errors'][] = $this->l('Invalid guest details, please check and try again.');
                    }
                } else {
                    $response['errors'][] = $this->l('Guest details not found.');
                }
            } else {
                $response['errors'][] = $this->l('Guest details not found.');
            }
        }

        $this->ajaxDie(json_encode($response));
    }

    private function getRenderedBookingDocuments($idHtlBooking)
    {
        $bookingDocuments = HotelBookingDocument::getDocumentsByIdHtlBooking($idHtlBooking);

        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            'booking_documents' => $bookingDocuments,
            'pdf_icon_link' => $this->context->link->getBaseLink().'modules/hotelreservationsystem/views/img/pdf-icon.jpg',
        ));

        return $this->context->smarty->fetch('controllers/orders/_booking_document_line.tpl');
    }

    public function ajaxProcessSearchProducts()
    {
        Context::getContext()->customer = new Customer((int)Tools::getValue('id_customer'));
        $currency = new Currency((int)Tools::getValue('id_currency'));
        $bookingProduct = (bool)Tools::getValue('booking_product', true);
        $to_return = array('found' => false);
        if (Validate::isLoadedObject($order = new Order(Tools::getValue('id_order')))) {
            $objBookingDetail = new HotelBookingDetail();
            $hotelBookingDetail = $objBookingDetail->getOrderCurrentDataByOrderId($order->id);
            if (count($hotelBookingDetail)) {
                $idHotel = reset($hotelBookingDetail)['id_hotel'];
            } else {
                $idHotel = false;
            }

            if ($products = Product::searchByName((int)$this->context->language->id, pSQL(Tools::getValue('product_search')), null, $idHotel)) {
                $objRoomType = new HotelRoomType();
                foreach ($products as $key => &$product) {

                    if (((bool)$product['booking_product']) != $bookingProduct) {
                        unset($products[$key]);
                        continue;
                    }

                    // get product room type informatin
                    if ($roomTypeDetail = $objRoomType->getRoomTypeInfoByIdProduct($product['id_product'])) {
                        $product['room_type_info'] = $roomTypeDetail;
                    }

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

                if (!empty($products)) {
                    $to_return = array(
                        'products' => $products,
                        'found' => true
                    );
                }
            }
        }
        $this->content = json_encode($to_return);
    }

    // public function ajaxProcessSearchProducts()
    // {
    //     Context::getContext()->customer = new Customer((int)Tools::getValue('id_customer'));
    //     $currency = new Currency((int)Tools::getValue('id_currency'));
    //     if ($products = Product::searchByName((int)$this->context->language->id, pSQL(Tools::getValue('product_search')))) {
    //         foreach ($products as &$product) {
    //             // Formatted price
    //             $product['formatted_price'] = Tools::displayPrice(Tools::convertPrice($product['price_tax_incl'], $currency), $currency);
    //             // Concret price
    //             $product['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_incl'], $currency), 2);
    //             $product['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_excl'], $currency), 2);
    //             $productObj = new Product((int)$product['id_product'], false, (int)$this->context->language->id);
    //             $combinations = array();
    //             $attributes = $productObj->getAttributesGroups((int)$this->context->language->id);

    //             // Tax rate for this customer
    //             if (Tools::isSubmit('id_address')) {
    //                 $product['tax_rate'] = $productObj->getTaxesRate(new Address(Tools::getValue('id_address')));
    //             }

    //             $product['warehouse_list'] = array();

    //             foreach ($attributes as $attribute) {
    //                 if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
    //                     $combinations[$attribute['id_product_attribute']]['attributes'] = '';
    //                 }
    //                 $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';
    //                 $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
    //                 $combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
    //                 if (!isset($combinations[$attribute['id_product_attribute']]['price'])) {
    //                     $price_tax_incl = Product::getPriceStatic((int)$product['id_product'], true, $attribute['id_product_attribute']);
    //                     $price_tax_excl = Product::getPriceStatic((int)$product['id_product'], false, $attribute['id_product_attribute']);
    //                     $combinations[$attribute['id_product_attribute']]['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($price_tax_incl, $currency), 2);
    //                     $combinations[$attribute['id_product_attribute']]['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($price_tax_excl, $currency), 2);
    //                     $combinations[$attribute['id_product_attribute']]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($price_tax_excl, $currency), $currency);
    //                 }
    //                 if (!isset($combinations[$attribute['id_product_attribute']]['qty_in_stock'])) {
    //                     $combinations[$attribute['id_product_attribute']]['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], $attribute['id_product_attribute'], (int)$this->context->shop->id);
    //                 }

    //                 if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
    //                     $product['warehouse_list'][$attribute['id_product_attribute']] = Warehouse::getProductWarehouseList($product['id_product'], $attribute['id_product_attribute']);
    //                 } else {
    //                     $product['warehouse_list'][$attribute['id_product_attribute']] = array();
    //                 }

    //                 $product['stock'][$attribute['id_product_attribute']] = Product::getRealQuantity($product['id_product'], $attribute['id_product_attribute']);
    //             }

    //             if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
    //                 $product['warehouse_list'][0] = Warehouse::getProductWarehouseList($product['id_product']);
    //             } else {
    //                 $product['warehouse_list'][0] = array();
    //             }

    //             $product['stock'][0] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], 0, (int)$this->context->shop->id);

    //             foreach ($combinations as &$combination) {
    //                 $combination['attributes'] = rtrim($combination['attributes'], ' - ');
    //             }
    //             $product['combinations'] = $combinations;

    //             if ($product['customizable']) {
    //                 $product_instance = new Product((int)$product['id_product']);
    //                 $product['customization_fields'] = $product_instance->getCustomizationFields($this->context->language->id);
    //             }
    //         }

    //         $to_return = array(
    //             'products' => $products,
    //             'found' => true
    //         );
    //     } else {
    //         $to_return = array('found' => false);
    //     }

    //     $this->content = json_encode($to_return);
    // }

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

    public function ajaxProcessAddServiceProductOnOrder()
    {
        // Load object
        $order = new Order((int)Tools::getValue('id_order'));
        if (!Validate::isLoadedObject($order)) {
            die(Tools::jsonEncode(array(
                'result' => false,
                'error' => Tools::displayError('The order object cannot be loaded.')
            )));
        }

        $old_cart_rules = Context::getContext()->cart->getCartRules();

        if ($order->hasBeenShipped()) {
            die(Tools::jsonEncode(array(
                'result' => false,
                'error' => Tools::displayError('You cannot add products to delivered orders. ')
            )));
        }

        $product_informations = $_POST['add_product'];
        if (isset($_POST['add_invoice'])) {
            $invoice_informations = $_POST['add_invoice'];
        } else {
            $invoice_informations = array();
        }
        $product = new Product($product_informations['product_id'], false, $order->id_lang);
        if (!Validate::isLoadedObject($product)) {
            die(Tools::jsonEncode(array(
                'result' => false,
                'error' => Tools::displayError('The product object cannot be loaded.')
            )));
        }

        if (isset($product_informations['product_attribute_id']) && $product_informations['product_attribute_id']) {
            $combination = new Combination($product_informations['product_attribute_id']);
            if (!Validate::isLoadedObject($combination)) {
                die(Tools::jsonEncode(array(
                'result' => false,
                'error' => Tools::displayError('The combination object cannot be loaded.')
            )));
            }
        }

        if ($product->booking_product || Product::SERVICE_PRODUCT_WITH_ROOMTYPE == $product->service_product_type) {
            die(Tools::jsonEncode(array(
                'result' => false,
                'error' => Tools::displayError('The product cannot be added through this method.')
            )));
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
            $order->id_address_tax
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
        $update_quantity = $cart->updateQty($product_informations['product_quantity'], $product->id, isset($product_informations['product_attribute_id']) ? $product_informations['product_attribute_id'] : null,
            isset($combination) ? $combination->id : null, 'up', 0, new Shop($cart->id_shop));

        if ($update_quantity < 0) {
            // If product has attribute, minimal quantity is set with minimal quantity of attribute
            $minimal_quantity = ($product_informations['product_attribute_id']) ? Attribute::getAttributeMinimalQty($product_informations['product_attribute_id']) : $product->minimal_quantity;
            die(Tools::jsonEncode(array('error' => sprintf(Tools::displayError('You must add %d minimum quantity', false), $minimal_quantity))));
        } elseif (!$update_quantity) {
            die(Tools::jsonEncode(array('error' => Tools::displayError('You already have the maximum quantity available for this product.', false))));
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
        $product['refund_history'] = OrderSlip::getProductSlipDetail((int)$product['id_order_detail']);
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

        die(Tools::jsonEncode(array(
            'result' => true,
            'view' => $this->createTemplate('_product_line.tpl')->fetch(),
            'can_edit' => $this->tabAccess['add'],
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch(),
            'discount_form_html' => $this->createTemplate('_discount_form.tpl')->fetch(),
            'refresh' => $refresh
        )));
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
        $occupancy = Tools::getValue('occupancy');
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
        }
        if ($order->with_occupancy) {
            if ($occupancy) {
                foreach($occupancy as $key =>$roomOccupancy) {
                    if (!$roomOccupancy['adults'] || !Validate::isUnsignedInt($roomOccupancy['adults'])) {
                        die(json_encode(array(
                            'result' => false,
                            'error' => sprintf(Tools::displayError('Invalid number of adults for Room %s.'), ($key + 1)),
                        )));
                    } elseif (!Validate::isUnsignedInt($roomOccupancy['children'])) {
                        die(json_encode(array(
                            'result' => false,
                            'error' => sprintf(Tools::displayError('Invalid number of children for Room %s.'), ($key + 1)),
                        )));
                    }
                    if ($roomOccupancy['children'] > 0) {
                        if (!isset($roomOccupancy['child_ages']) || ($roomOccupancy['children'] != count($roomOccupancy['child_ages']))) {
                            die(json_encode(array(
                                'result' => false,
                            'error' => sprintf(Tools::displayError('Please provide all children age for Room %s.'), ($key + 1)),
                            )));
                        } else {
                            foreach($roomOccupancy['child_ages'] as $childAge) {
                                if (!Validate::isUnsignedInt($childAge)) {
                                    die(json_encode(array(
                                        'result' => false,
                                        'error' => sprintf(Tools::displayError('Invalid children age for Room %s.'), ($key + 1)),
                                    )));
                                }
                            }
                        }
                    }
                }
            } else {
                die(json_encode(array(
                    'result' => false,
                    'error' => Tools::displayError('Invalid occupancy.'),
                )));
            }
        } elseif (!Validate::isUnsignedInt($product_informations['product_quantity'])) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please Enter a valid Quantity.'),
            )));
        }


        if ($order->with_occupancy) {
            $req_rm = count($occupancy);
        } else {
            $req_rm = $product_informations['product_quantity'];
        }
        $obj_booking_detail = new HotelBookingDetail();
        $num_days = $obj_booking_detail->getNumberOfDays($date_from, $date_to);
        $product_informations['product_quantity'] = $product_informations['product_quantity'] * (int) $num_days;

        $obj_room_type = new HotelRoomType();
        $room_info_by_id_product = $obj_room_type->getRoomTypeInfoByIdProduct($product_informations['product_id']);

        if ($room_info_by_id_product) {
            $id_hotel = $room_info_by_id_product['id_hotel'];

            if ($id_hotel) {
                $objBookingDetail = new HotelBookingDetail();
                $bookingParams = array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'hotel_id' => $id_hotel,
                    'id_room_type' => $product_informations['product_id'],
                    'only_search_data' => 1,
                    'id_cart' => $id_cart,
                    'id_guest' => $id_guest,
                );
                $hotel_room_data = $objBookingDetail->dataForFrontSearch($bookingParams);
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
        $hotel_room_info_arr = $hotel_room_data['rm_data'][$idProduct]['data']['available'];
        $chkQty = 0;
        if ($hotel_room_info_arr) {
            foreach ($hotel_room_info_arr as $key => $room_info) {
                if ($chkQty < $req_rm) {
                    $objCartBookingData = new HotelCartBookingData();
                    $objCartBookingData->id_cart = $this->context->cart->id;
                    $objCartBookingData->id_guest = $this->context->cookie->id_guest;
                    $objCartBookingData->id_customer = $order->id_customer;
                    $objCartBookingData->id_currency = $order->id_currency;
                    $objCartBookingData->id_product = $room_info['id_product'];
                    $objCartBookingData->id_room = $room_info['id_room'];
                    $objCartBookingData->id_hotel = $room_info['id_hotel'];
                    $objCartBookingData->booking_type = 1;
                    $objCartBookingData->quantity = $num_days;
                    $objCartBookingData->date_from = $date_from;
                    $objCartBookingData->date_to = $date_to;

                    if ($order->with_occupancy) {
                        $room_occupancy = array_shift($occupancy);
                        $objCartBookingData->adults = $room_occupancy['adults'];
                        $objCartBookingData->children = $room_occupancy['children'];
                        $objCartBookingData->child_ages = $room_occupancy['children'] ? json_encode($room_occupancy['child_ages']) : json_encode(array());
                    } else {
                        $objCartBookingData->adults = $room_info_by_id_product['adults'];
                        $objCartBookingData->children = $room_info_by_id_product['children'];
                        $objCartBookingData->child_ages = json_encode(array());
                    }
                    $objCartBookingData->save();
                    ++$chkQty;

                    // create feature price if needed
                    if ($createFeaturePrice) {
                        $featurePriceParams['id_room'] = $room_info['id_room'];
                        $featurePriceParams = array_merge($featurePriceParams, array('date_from' => $date_from, 'date_to' => $date_to));
                        $this->createFeaturePrice($featurePriceParams);
                    }
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
            $order->id_address_tax
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
            ($req_rm * $num_days),
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
        $objBookingDetail = new HotelBookingDetail();
        $inserted_id_order_detail = $objBookingDetail->getLastInsertedIdOrderDetail($order->id);
        $idLang = (int)$this->context->cart->id_lang;
        $objCartBookingData = new HotelCartBookingData();
        if ($cartBookingData = $objCartBookingData->getOnlyCartBookingData(
            $this->context->cart->id,
            $this->context->cart->id_guest,
            $idProduct
        )) {
            foreach ($cartBookingData as $cb_k => $cb_v) {
                $objCartBookingData = new HotelCartBookingData($cb_v['id']);
                $objCartBookingData->id_order = $order->id;
                $objCartBookingData->save();

                $objBookingDetail = new HotelBookingDetail();
                $objBookingDetail->id_product = $idProduct;
                $objBookingDetail->id_order = $order->id;
                $objBookingDetail->id_order_detail = $inserted_id_order_detail;
                $objBookingDetail->id_cart = $this->context->cart->id;
                $objBookingDetail->id_room = $objCartBookingData->id_room;
                $objBookingDetail->id_hotel = $objCartBookingData->id_hotel;
                $objBookingDetail->id_customer = $order->id_customer;
                $objBookingDetail->booking_type = $objCartBookingData->booking_type;
                $objBookingDetail->id_status = 1;
                $objBookingDetail->comment = $objCartBookingData->comment;
                $objBookingDetail->room_type_name = Product::getProductName($idProduct, null, $order->id_lang);

                $objBookingDetail->date_from = $objCartBookingData->date_from;
                $objBookingDetail->date_to = $objCartBookingData->date_to;
                $objBookingDetail->adults = $objCartBookingData->adults;
                $objBookingDetail->children = $objCartBookingData->children;
                $objBookingDetail->child_ages = $objCartBookingData->child_ages;

                $total_price = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                    $idProduct,
                    $objCartBookingData->date_from,
                    $objCartBookingData->date_to,
                    0,
                    Group::getCurrent()->id,
                    $this->context->cart->id,
                    $this->context->cookie->id_guest,
                    $objCartBookingData->id_room,
                    0
                );
                $objBookingDetail->total_price_tax_excl = $total_price['total_price_tax_excl'];
                $objBookingDetail->total_price_tax_incl = $total_price['total_price_tax_incl'];
                $objBookingDetail->total_paid_amount = Tools::ps_round($total_price['total_price_tax_incl'], 5);

                // Save hotel information/location/contact
                if (Validate::isLoadedObject($objRoom = new HotelRoomInformation($objCartBookingData->id_room))) {
                    $objBookingDetail->room_num = $objRoom->room_num;
                }
                if (Validate::isLoadedObject($objHotelBranch = new HotelBranchInformation(
                    $objCartBookingData->id_hotel,
                    $idLang
                ))) {
                    $addressInfo = $objHotelBranch->getAddress($objCartBookingData->id_hotel);
                    $objBookingDetail->hotel_name = $objHotelBranch->hotel_name;
                    $objBookingDetail->city = $addressInfo['city'];
                    $objBookingDetail->state = State::getNameById($addressInfo['id_state']);
                    $objBookingDetail->country = Country::getNameById($idLang, $addressInfo['id_country']);
                    $objBookingDetail->zipcode = $addressInfo['postcode'];;
                    $objBookingDetail->phone = $addressInfo['phone'];
                    $objBookingDetail->email = $objHotelBranch->email;
                    $objBookingDetail->check_in_time = $objHotelBranch->check_in;
                    $objBookingDetail->check_out_time = $objHotelBranch->check_out;
                }
                if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($idProduct)) {
                    $objBookingDetail->adults = $objCartBookingData->adults;
                    $objBookingDetail->children = $objCartBookingData->children;
                    $objBookingDetail->child_ages = $objCartBookingData->child_ages;
                }

                $objBookingDetail->save();
            }
        }

        // delete cart feature prices after room addition success
        HotelRoomTypeFeaturePricing::deleteByIdCart($cart->id);
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

    /**
     * This function is called when order is changed (Add/Edit/Delete room on order)
     */
    public function sendChangedNotification(Order $order = null)
    {
        if (is_null($order)) {
            $order = new Order(Tools::getValue('id_order'));
        }

        $this->updateOrderStatusOnOrderChange($order);

        // load updated object
        $order = new Order(Tools::getValue('id_order'));

        Hook::exec('actionOrderEdited', array('order' => $order));
    }

    /**
     * This function is called to manage order status when order is changed
     */
    public function updateOrderStatusOnOrderChange($objOrder)
    {
        // check if new order amount is greater that old order amount and order payment is accepted
        // then update order status to partial payment accepted
        $currentOrderState = $objOrder->getCurrentOrderState();
        $psOsPartialPaymentAccepted = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
        if ($currentOrderState->paid == 1 && $currentOrderState->id != $psOsPartialPaymentAccepted) {
            // calculate due amount
            $dueAmount = $objOrder->total_paid_tax_incl - $objOrder->total_paid_real;
            if ($dueAmount > 0) {
                // now change order status to partial payment
                $objOrderHistory = new OrderHistory();
                $objOrderHistory->id_order = $objOrder->id;
                $objOrderHistory->id_employee = (int) $this->context->employee->id;

                $useExistingPayment = false;
                if (!$objOrder->hasInvoice()) {
                    $useExistingPayment = true;
                }

                $objOrderHistory->changeIdOrderState($psOsPartialPaymentAccepted, $objOrder, $useExistingPayment);
                $objOrderHistory->add();
            }
        }
    }

    public function ajaxProcessLoadProductInformation()
    {
        $order_detail = new OrderDetail(Tools::getValue('id_order_detail'));
        if (!Validate::isLoadedObject($order_detail))
        	die(json_encode(array(
        		'result' => false,
        		'error' => Tools::displayError('The OrderDetail object cannot be loaded.')
        	)));

        $product = new Product($order_detail->product_id);
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
            'price_tax_incl' => Product::getPriceStatic($product->id, true, $order_detail->product_attribute_id, 2),
            'price_tax_excl' => Product::getPriceStatic($product->id, false, $order_detail->product_attribute_id, 2),
            /*Changed by webkul because attribute_id will always be 0 (No combination)*/
            // 'price_tax_incl' => Product::getPriceStatic($product->id, true, 0, 2),
            // 'price_tax_excl' => Product::getPriceStatic($product->id, false, 0, 2),
            'reduction_percent' => $order_detail->reduction_percent
        )));
    }

    protected function createFeaturePrice($params)
    {
        $feature_price_name = array();
        foreach (Language::getIDs(true) as $id_lang) {
            $feature_price_name[$id_lang] = 'Auto-generated';
        }

        $hrt_feature_price = new HotelRoomTypeFeaturePricing();
        $hrt_feature_price->id_product = (int) $params['id_product'];
        $hrt_feature_price->id_cart = (int) $params['id_cart'];
        $hrt_feature_price->id_guest = (int) $params['id_guest'];
        $hrt_feature_price->id_room = (int) $params['id_room'];
        $hrt_feature_price->feature_price_name = $feature_price_name;
        $hrt_feature_price->date_selection_type = HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE;
        $hrt_feature_price->date_from = date('Y-m-d', strtotime($params['date_from']));
        $hrt_feature_price->date_to = date('Y-m-d', strtotime($params['date_to']));
        $hrt_feature_price->is_special_days_exists = 0;
        $hrt_feature_price->special_days = json_encode(false);
        $hrt_feature_price->impact_way = HotelRoomTypeFeaturePricing::IMPACT_WAY_FIX_PRICE;
        $hrt_feature_price->impact_type = HotelRoomTypeFeaturePricing::IMPACT_TYPE_FIXED_PRICE;
        $hrt_feature_price->impact_value = $params['price'];
        $hrt_feature_price->active = 1;
        $hrt_feature_price->groupBox = array_column(Group::getGroups($this->context->language->id), 'id_group');
        $hrt_feature_price->add();
    }

    public function ajaxProcessEditRoomOnOrder()
    {
        // Return value
        $res = true;
        $id_order = (int) Tools::getValue('id_order');
        $order = new Order($id_order);
        $cart = new Cart($order->id_cart);
        $customer = new Cart($order->id_customer);
        //$order_detail = new OrderDetail((int)Tools::getValue('product_id_order_detail'));
        $order_detail = new OrderDetail((int) Tools::getValue('id_order_detail'));//by webkul id_order_detail from our table
        $this->doEditRoomValidation($order_detail, $order, isset($order_invoice) ? $order_invoice : null);

        if (Tools::isSubmit('product_invoice')) {
            $order_invoice = new OrderInvoice((int) Tools::getValue('product_invoice'));
        }
        /*By webkul To edit Order and cart entries when edit rooms from the orderLine when editing the order*/
        $product_informations = $_POST['edit_product'];
        $new_date_from = trim(date('Y-m-d', strtotime($product_informations['date_from'])));
        $new_date_to = trim(date('Y-m-d', strtotime($product_informations['date_to'])));
        $old_date_from = trim(Tools::getValue('date_from'));
        $old_date_to = trim(Tools::getValue('date_to'));
        $id_hotel = trim(Tools::getValue('id_hotel'));
        $id_room = trim(Tools::getValue('id_room'));
        $id_product = trim(Tools::getValue('id_product'));
        $room_unit_price = trim(Tools::getValue('room_unit_price'));
        $obj_booking_detail = new HotelBookingDetail();
        $product_quantity = (int) $obj_booking_detail->getNumberOfDays($new_date_from, $new_date_to);
        $old_product_quantity =  (int) $obj_booking_detail->getNumberOfDays($old_date_from, $old_date_to);
        $qty_diff = $product_quantity - $old_product_quantity;
        if ($order->with_occupancy) {
            $occupancy = array_shift(Tools::getValue('occupancy'));
            $adults = $occupancy['adults'];
            $children = $occupancy['children'];
            $child_ages = $occupancy['child_ages'];
        }

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
        } elseif ($room_unit_price == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please enter unit price.'),
            )));
        } elseif (!Validate::isPrice($room_unit_price)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Please enter a valid unit price.'),
            )));
        } elseif (!Validate::isUnsignedInt($product_quantity)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Invalid quantity.'),
            )));
        }

        // validations if order is with occupancy
        if ($order->with_occupancy) {
            if (!isset($adults) || !$adults || !Validate::isUnsignedInt($adults)) {
                die(json_encode(array(
                    'result' => false,
                    'error' => Tools::displayError('Invalid number of adults.'),
                )));
            } elseif (!Validate::isUnsignedInt($children)) {
                die(json_encode(array(
                    'result' => false,
                    'error' => Tools::displayError('Invalid number of children.'),
                )));
            }

            if ($children > 0) {
                if (!isset($child_ages) || ($children != count($child_ages))) {
                    die(json_encode(array(
                        'result' => false,
                        'error' => Tools::displayError('Please provide all children age.'),
                    )));
                } else {
                    foreach($child_ages as $childAge) {
                        if (!Validate::isUnsignedInt($childAge)) {
                            die(json_encode(array(
                                'result' => false,
                                'error' => Tools::displayError('Invalid children age.'),
                            )));
                        }
                    }
                }
            }
        }

        $rooms_booked = $obj_booking_detail->getRoomBookinInformationForDateRangeByOrder($id_room, $old_date_from, $old_date_to, $new_date_from, $new_date_to);
        if ($rooms_booked) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('This Room Unavailable For Selected Duration.'),
            )));
        }

        // By webkul to calculate rates of the product from hotelreservationsystem tables with feature prices....
        // add feature price for updated price

        $hotelCartBookingData = new HotelCartBookingData();
        $totalProductPriceBeforeTE = (float) $order_detail->total_price_tax_excl;
        $totalProductPriceBeforeTI = (float) $order_detail->total_price_tax_incl;
        $totalProductPriceAfterTE = 0;
        $totalProductPriceAfterTI = 0;
        $totalRoomPriceAfterTE = 0;
        $totalRoomPriceAfterTI = 0;
        $bookedRooms = $obj_booking_detail->getBookedRoomsByIdOrderDetail((int) Tools::getValue('id_order_detail'), $id_product);
        if ($bookedRooms) {
            $params = array(
                'id_cart' => $cart->id,
                'id_guest' => $cart->id_guest,
                'price' => $room_unit_price,
            );

            foreach ($bookedRooms as $roomInfo) {
                $params['id_product'] = $roomInfo['id_product'];
                $params['id_room'] = $roomInfo['id_room'];

                if ($roomInfo['id_room'] == $id_room && (strtotime($roomInfo['date_from']) == strtotime($old_date_from))) {
                    $params = array_merge($params, array('date_from' => $new_date_from, 'date_to' => $new_date_to));
                    $this->createFeaturePrice($params);

                    $roomTotalPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                        $roomInfo['id_product'],
                        $new_date_from,
                        $new_date_to,
                        0,
                        Group::getCurrent()->id,
                        $cart->id,
                        $cart->id_guest,
                        $roomInfo['id_room'],
                        0
                    );

                    $totalProductPriceAfterTE += (float) $roomTotalPrice['total_price_tax_excl'];
                    $totalProductPriceAfterTI += (float) $roomTotalPrice['total_price_tax_incl'];

                    $totalRoomPriceAfterTE += (float) $roomTotalPrice['total_price_tax_excl'];
                    $totalRoomPriceAfterTI += (float) $roomTotalPrice['total_price_tax_incl'];
                } else {
                    $totalProductPriceAfterTE += (float) $roomInfo['total_price_tax_excl'];
                    $totalProductPriceAfterTI += (float) $roomInfo['total_price_tax_incl'];
                }
            }
        }

        // delete cart feature prices after booking update success
        HotelRoomTypeFeaturePricing::deleteByIdCart($cart->id);
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

        // get extra demands of the room before changing in the booking table
        $objBookingDemand = new HotelBookingDemands();
        $extraDemands = $objBookingDemand->getRoomTypeBookingExtraDemands(
            $id_order,
            0,
            $id_room,
            $old_date_from,
            $old_date_to
        );

        // set occupancy details
        $occupancy = array(
            'adults' => $adults,
            'children' => $children,
            'child_ages' => $child_ages
        );

        /*By webkul to edit the Hotel Cart and Hotel Order tables when editing the room for the order detail page*/
        $new_total_price = array(
            'tax_excl' => $totalRoomPriceAfterTE,
            'tax_incl' => $totalRoomPriceAfterTI,
        );
        if ($update_htl_tables = $obj_booking_detail->UpdateHotelCartHotelOrderOnOrderEdit(
            $id_order,
            $id_room,
            $old_date_from,
            $old_date_to,
            $new_date_from,
            $new_date_to,
            $occupancy,
            $new_total_price
        )) {
            // update extra demands total prices if dates are changes (price calc method for each day)
            if ($extraDemands) {
                $objOrder = new Order($id_order);
                foreach ($extraDemands as $demand) {
                    if (isset($demand['extra_demands']) && $demand['extra_demands']) {
                        foreach ($demand['extra_demands'] as $rDemand) {
                            if ($rDemand['price_calc_method'] == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY) {
                                $objBookingDemand = new HotelBookingDemands($rDemand['id_booking_demand']);

                                // change order total
                                $objOrder->total_paid_tax_excl -= $objBookingDemand->total_price_tax_excl;
                                $objOrder->total_paid_tax_incl -= $objBookingDemand->total_price_tax_incl;
                                $objOrder->total_paid -= $objBookingDemand->total_price_tax_incl;

                                $numDays = $obj_booking_detail->getNumberOfDays($new_date_from, $new_date_to);
                                $demandPriceTE = $objBookingDemand->unit_price_tax_excl * $numDays;
                                $demandPriceTI = $objBookingDemand->unit_price_tax_incl * $numDays;

                                $objOrder->total_paid_tax_excl += $demandPriceTE;
                                $objOrder->total_paid_tax_incl += $demandPriceTI;
                                $objOrder->total_paid += $demandPriceTI;

                                $objBookingDemand->total_price_tax_excl = $demandPriceTE;
                                $objBookingDemand->total_price_tax_incl = $demandPriceTI;

                                $objBookingDemand->save();
                            }
                        }
                    }
                }
                // change order total save
                $objOrder->save();
            }
        }

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

    public function ajaxProcessEditProductOnOrder()
    {
        // Return value
        $res = true;

        $order = new Order((int)Tools::getValue('id_order'));
        $order_detail = new OrderDetail((int)Tools::getValue('product_id_order_detail'));
        if (Tools::isSubmit('product_invoice')) {
            $order_invoice = new OrderInvoice((int)Tools::getValue('product_invoice'));
        }

        // If multiple product_quantity, the order details concern a product customized
        $product_quantity = 0;
        if (is_array(Tools::getValue('product_quantity'))) {
            foreach (Tools::getValue('product_quantity') as $id_customization => $qty) {
                // Update quantity of each customization
                Db::getInstance()->update('customization', array('quantity' => (int)$qty), 'id_customization = ' . (int)$id_customization);
                // Calculate the real quantity of the product
                $product_quantity += $qty;
            }
        } else {
            $product_quantity = Tools::getValue('product_quantity');
        }

        $this->checkStockAvailable($order_detail, ($product_quantity - $order_detail->product_quantity));

        // Check fields validity
        $this->doEditProductValidation($order_detail, $order, isset($order_invoice) ? $order_invoice : null);

        // If multiple product_quantity, the order details concern a product customized
        $product_quantity = 0;
        if (is_array(Tools::getValue('product_quantity'))) {
            foreach (Tools::getValue('product_quantity') as $id_customization => $qty) {
                // Update quantity of each customization
                Db::getInstance()->update('customization', array('quantity' => (int)$qty), 'id_customization = '.(int)$id_customization);
                // Calculate the real quantity of the product
                $product_quantity += $qty;
            }
        } else {
            $product_quantity = Tools::getValue('product_quantity');
        }

        $product_price_tax_incl = $order_detail->unit_price_tax_incl;
        $product_price_tax_excl = $order_detail->unit_price_tax_excl;
        $total_products_tax_incl = $product_price_tax_incl * $product_quantity;
        $total_products_tax_excl = $product_price_tax_excl * $product_quantity;

        // Calculate differences of price (Before / After)
        $diff_price_tax_incl = $total_products_tax_incl - $order_detail->total_price_tax_incl;
        $diff_price_tax_excl = $total_products_tax_excl - $order_detail->total_price_tax_excl;

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

            $order_detail->total_price_tax_incl += $diff_price_tax_incl;
            $order_detail->total_price_tax_excl += $diff_price_tax_excl;

            if (isset($order_invoice)) {
                // Apply changes on OrderInvoice
                $order_invoice->total_products += $diff_price_tax_excl;
                $order_invoice->total_products_wt += $diff_price_tax_incl;

                $order_invoice->total_paid_tax_excl += $diff_price_tax_excl;
                $order_invoice->total_paid_tax_incl += $diff_price_tax_incl;
            }

            // Apply changes on Order
            $order = new Order($order_detail->id_order);
            $order->total_products += $diff_price_tax_excl;
            $order->total_products_wt += $diff_price_tax_incl;

            $order->total_paid += $diff_price_tax_incl;
            $order->total_paid_tax_excl += $diff_price_tax_excl;
            $order->total_paid_tax_incl += $diff_price_tax_incl;

            $res &= $order->update();
        }

        $old_quantity = $order_detail->product_quantity;

        $order_detail->product_quantity = $product_quantity;
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
        $product['refund_history'] = OrderSlip::getProductSlipDetail($order_detail->id);
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
            die(Tools::jsonEncode(array(
                'result' => $res,
                'error' => Tools::displayError('An error occurred while editing the product line.')
            )));
        }

        if (is_array(Tools::getValue('product_quantity'))) {
            $view = $this->createTemplate('_customized_data.tpl')->fetch();
        } else {
            $view = $this->createTemplate('_product_line.tpl')->fetch();
        }

        $this->sendChangedNotification($order);

        die(Tools::jsonEncode(array(
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

    public function ajaxProcessDeleteRoomLine()
    {
        $res = true;
        $order_detail = new OrderDetail((int) Tools::getValue('id_order_detail'));
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
        Hook::exec('actionOrderProductLineDeleteBefore', array('order' => $order));
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

        $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
        $additionlServicesTI = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom(
            $idHotelBooking,
            1,
            1
        );

        $additionlServicesTE = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom(
            $idHotelBooking,
            1,
            0
        );

        $selectedAdditonalServices = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom(
            $idHotelBooking
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
        // update order detail for selected aditional services
        foreach ($selectedAdditonalServices['additional_services'] as $service) {
            $serviceOrderDetail = new OrderDetail($service['id_order_detail']);
            if ($service['quantity'] >= $serviceOrderDetail->product_quantity) {
                $serviceOrderDetail->delete();
            } else {
                $order_detail->total_price_tax_incl -= $service['total_price_tax_incl'];
                $order_detail->total_price_tax_excl -= $service['total_price_tax_excl'];

                $serviceOldQuantity = $serviceOrderDetail->product_quantity;
                $serviceOrderDetail->product_quantity = $serviceOldQuantity - $service['quantity'];

                // update taxes
                $res &= $order_detail->updateTaxAmount($order);

                // Save order detail
                $res &= $order_detail->update();
            }
        }
        /*End*/

        // Update OrderInvoice of this OrderDetail
        if ($order_detail->id_order_invoice != 0) {
            // values changes as values are calculated accoding to the quantity of the product by webkul
            $order_invoice = new OrderInvoice($order_detail->id_order_invoice);
            $order_invoice->total_paid_tax_excl -= ($diff_products_tax_excl + $roomExtraDemandTE + $additionlServicesTE);
            $order_invoice->total_paid_tax_incl -= ($diff_products_tax_incl + $roomExtraDemandTI + $additionlServicesTI);
            $order_invoice->total_products -= $diff_products_tax_excl;
            $order_invoice->total_products_wt -= $diff_products_tax_incl;
            $res &= $order_invoice->update();
        }

        // Update Order
        // values changes as values are calculated accoding to the quantity of the product by webkul
        $order->total_paid -= ($diff_products_tax_incl + $roomExtraDemandTI + $additionlServicesTI);
        $order->total_paid_tax_incl -= ($diff_products_tax_incl + $roomExtraDemandTI + $additionlServicesTI);
        $order->total_paid_tax_excl -= ($diff_products_tax_excl + $roomExtraDemandTE + $additionlServicesTE);
        $order->total_products -= ($diff_products_tax_excl + $additionlServicesTE);
        $order->total_products_wt -= ($diff_products_tax_incl + $additionlServicesTI);

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

        $objRoomTypeServiceProductOrderDetail->deleteRoomSevices($idHotelBooking);

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

    public function ajaxProcessDeleteProductLine()
    {
        $res = true;

        $order_detail = new OrderDetail((int)Tools::getValue('id_order_detail'));
        $order = new Order((int)Tools::getValue('id_order'));

        $this->doDeleteProductLineValidation($order_detail, $order);

        // Update OrderInvoice of this OrderDetail
        if ($order_detail->id_order_invoice != 0) {
            $order_invoice = new OrderInvoice($order_detail->id_order_invoice);
            $order_invoice->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
            $order_invoice->total_paid_tax_incl -= $order_detail->total_price_tax_incl;
            $order_invoice->total_products -= $order_detail->total_price_tax_excl;
            $order_invoice->total_products_wt -= $order_detail->total_price_tax_incl;
            $res &= $order_invoice->update();
        }

        // Update Order
        $order->total_paid -= $order_detail->total_price_tax_incl;
        $order->total_paid_tax_incl -= $order_detail->total_price_tax_incl;
        $order->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
        $order->total_products -= $order_detail->total_price_tax_excl;
        $order->total_products_wt -= $order_detail->total_price_tax_incl;

        $res &= $order->update();

        // Reinject quantity in stock
        $this->reinjectQuantity($order_detail, $order_detail->product_quantity, true);

        // Update weight SUM
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float)$order->getTotalWeight();
            $res &= $order_carrier->update();
            if ($res) {
                $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        if (!$res) {
            die(Tools::jsonEncode(array(
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

        die(Tools::jsonEncode(array(
            'result' => $res,
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch()
        )));
    }

    protected function doEditProductValidation(OrderDetail $order_detail, Order $order, OrderInvoice $order_invoice = null)
    {
        $this->doEditValidation($order_detail, $order, $order_invoice);

        if (!is_array(Tools::getValue('product_quantity')) && !Validate::isUnsignedInt(Tools::getValue('product_quantity'))) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Invalid quantity')
            )));
        } elseif (is_array(Tools::getValue('product_quantity'))) {
            foreach (Tools::getValue('product_quantity') as $qty) {
                if (!Validate::isUnsignedInt($qty)) {
                    die(json_encode(array(
                        'result' => false,
                        'error' => Tools::displayError('Invalid quantity')
                    )));
                }
            }
        }
    }

    protected function doEditRoomValidation(OrderDetail $order_detail, Order $order, OrderInvoice $order_invoice = null)
    {
        $this->doEditValidation($order_detail, $order, $order_invoice);

        $product_price_tax_incl = str_replace(',', '.', Tools::getValue('product_price_tax_incl'));
        $product_price_tax_excl = str_replace(',', '.', Tools::getValue('product_price_tax_excl'));

        if (!Validate::isPrice($product_price_tax_incl) || !Validate::isPrice($product_price_tax_excl)) {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Invalid price')
            )));
        }

        $product_informations = Tools::getValue('edit_product');
        $old_date_from = date('Y-m-d', strtotime(trim(Tools::getValue('date_from'))));
        $old_date_to = date('Y-m-d', strtotime(trim(Tools::getValue('date_to'))));
        $new_date_from = trim(date('Y-m-d', strtotime($product_informations['date_from'])));
        $new_date_to = trim(date('Y-m-d', strtotime($product_informations['date_to'])));
        $obj_booking_detail = new HotelBookingDetail();
        $product_quantity = (int) $obj_booking_detail->getNumberOfDays($new_date_from, $new_date_to);

        if (trim(Tools::getValue('id_hotel')) == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Hotel Id is mising.'),
            )));
        } elseif (trim(Tools::getValue('id_room')) == '') {
            die(json_encode(array(
                'result' => false,
                'error' => Tools::displayError('Room Id is missing.'),
            )));
        } elseif (trim(date('Y-m-d', strtotime($product_informations['date_from']))) == '') {
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
    }

    protected function doEditValidation(OrderDetail $order_detail, Order $order, OrderInvoice $order_invoice = null)
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

    protected function getPaymentsTypes()
    {
        return array(
            OrderPayment::PAYMENT_TYPE_ONLINE => array(
                'key' => 'PAYMENT_TYPE_ONLINE',
                'value' => OrderPayment::PAYMENT_TYPE_ONLINE,
                'name' => $this->l('Online')
            ),
            OrderPayment::PAYMENT_TYPE_PAY_AT_HOTEL => array(
                'key' => 'PAYMENT_TYPE_PAY_AT_HOTEL',
                'value' => OrderPayment::PAYMENT_TYPE_PAY_AT_HOTEL,
                'name' => $this->l('Pay at hotel')
            ),
            OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT => array(
                'key' => 'PAYMENT_TYPE_REMOTE_PAYMENT',
                'value' => OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT,
                'name' => $this->l('Remote payment')
            ),
        );
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

    public function ajaxProcessDeleteProductProcess()
    {
        $errors = array();
        if ((!$id_product = (int)Tools::getValue('id_product')) || !Validate::isInt($id_product)) {
            $errors[] = Tools::displayError('Invalid product');
        }
        $cart_id = Tools::getValue('id_cart');
        if (!Validate::isLoadedObject($objCart = new Cart($cart_id))) {
            $errors[] = Tools::displayError('Cart not found');
        }
        if (count($errors)) {
            die(json_encode($errors));
        }
        if ($objCart->deleteProduct($id_product)) {
            die(json_encode(array('status' => 'deleted')));
        }
    }

    // To show rooms extra demands in the modal box in order details view page
    public function ajaxProcessGetRoomTypeBookingDemands()
    {
        if (($idProduct = Tools::getValue('id_product'))
            && ($idOrder = Tools::getValue('id_order'))
            && ($idRoom = Tools::getValue('id_room'))
            && ($dateFrom = Tools::getValue('date_from'))
            && ($dateTo = Tools::getValue('date_to'))
        ) {
            $smartyVars = array();
            $objOrder = new Order($idOrder);
            $smartyVars['orderCurrency'] = $objOrder->id_currency;
            $smartyVars['link'] = $this->context->link;

            $objBookingDemand = new HotelBookingDetail();
            $htlBookingDetail = $objBookingDemand->getRowByIdOrderIdProductInDateRange(
                $idOrder,
                $idProduct,
                $dateFrom,
                $dateTo,
                $idRoom
            );

            $smartyVars['id_booking_detail'] = $htlBookingDetail['id'];

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

            $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
            if ($additionalServices = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom(
                $htlBookingDetail['id']
            )) {
                $smartyVars['additionalServices'] = $additionalServices;
            }

            if ($orderEdit = Tools::getValue('orderEdit')) {
                $smartyVars['orderEdit'] = $orderEdit;

                // get room type additional demands
                $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                if ($roomTypeServiceProducts = $objRoomTypeServiceProduct->getServiceProductsData($idProduct, 1, 0, false, 2, null)) {
                    if ($additionalServices) {
                        foreach ($roomTypeServiceProducts as $key => $product) {
                            if (in_array($product['id_product'], array_column($additionalServices['additional_services'], 'id_product'))) {
                                unset($roomTypeServiceProducts[$key]);
                            }
                        }
                    }
                    $smartyVars['roomTypeServiceProducts'] = $roomTypeServiceProducts;
                }
            }
            $this->context->smarty->assign($smartyVars);
        }

        $extraDemandsTpl = $this->context->smarty->fetch(
            _PS_ADMIN_DIR_.'/themes/default/template/controllers/orders/_room_extra_services_modal.tpl'
        );
        die($extraDemandsTpl);
    }

    public function processRenderServicesPanel($idOrder, $idProduct, $dateFrom, $dateTo, $idRoom, $orderEdit)
    {
        $smartyVars = array();
        $objBookingDemand = new HotelBookingDetail();
        $htlBookingDetail = $objBookingDemand->getRowByIdOrderIdProductInDateRange(
            $idOrder,
            $idProduct,
            $dateFrom,
            $dateTo,
            $idRoom
        );

        $smartyVars['id_booking_detail'] = $htlBookingDetail['id'];
        $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
        if ($additionalServices = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom(
            $htlBookingDetail['id']
        )) {
            $smartyVars['additionalServices'] = $additionalServices;
        }

        if ($orderEdit) {
            $smartyVars['orderEdit'] = $orderEdit;

            // get room type additional demands
            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
            if ($roomTypeServiceProducts = $objRoomTypeServiceProduct->getServiceProductsData($idProduct, 1, 0, false, 2, null)) {
                foreach ($roomTypeServiceProducts as $key => $product) {
                    if (in_array($product['id_product'], array_column($additionalServices['additional_services'], 'id_product'))) {
                        unset($roomTypeServiceProducts[$key]);
                    }
                }
                $smartyVars['roomTypeServiceProducts'] = $roomTypeServiceProducts;
            }
        }
        $smartyVars['show_active'] = true;
        $this->context->smarty->assign($smartyVars);

        $servicesTpl = $this->context->smarty->fetch(
            _PS_ADMIN_DIR_.'/themes/default/template/controllers/orders/_room_services_block.tpl'
        );
        return $servicesTpl;
    }

    public function processRenderFacilitiesBlock($idOrder, $idProduct, $dateFrom, $dateTo, $idRoom, $orderEdit)
    {
        $smartyVars = array();
        $objOrder = new Order($idOrder);
        $smartyVars['orderCurrency'] = $objOrder->id_currency;

        $objBookingDemand = new HotelBookingDetail();
        $htlBookingDetail = $objBookingDemand->getRowByIdOrderIdProductInDateRange(
            $idOrder,
            $idProduct,
            $dateFrom,
            $dateTo,
            $idRoom
        );

        $smartyVars['id_booking_detail'] = $htlBookingDetail['id'];

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
        if ($orderEdit) {
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

        $servicesTpl = $this->context->smarty->fetch(
            _PS_ADMIN_DIR_.'/themes/default/template/controllers/orders/_room_facilities_block.tpl'
        );
        return $servicesTpl;
    }

    public function ajaxProcessUpdateRoomAdditionalServices()
    {
        $response = array('hasError' => false);
        $id_room_type_service_product_order_detail = Tools::getValue('id_room_type_service_product_order_detail');
        $quantity = Tools::getValue('qty');

        if (Validate::isLoadedObject($objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail($id_room_type_service_product_order_detail))) {
            $objOrderDetail = new OrderDetail($objRoomTypeServiceProductOrderDetail->id_order_detail);
            if ($objOrderDetail->product_allow_multiple_quantity) {
                if (!Validate::isUnsignedInt($quantity)) {
                    $response['hasError'] = true;
                    $response['errors'] = $this->l('Invalid quantity provided');
                } else {
                    $objRoomTypeServiceProductOrderDetail->quantity = $quantity;
                    $oldPriceTaxExcl = $objRoomTypeServiceProductOrderDetail->total_price_tax_excl;
                    $oldPriceTaxIncl = $objRoomTypeServiceProductOrderDetail->total_price_tax_incl;
                    $objRoomTypeServiceProductOrderDetail->total_price_tax_excl = $objRoomTypeServiceProductOrderDetail->unit_price_tax_excl * $quantity;
                    $objRoomTypeServiceProductOrderDetail->total_price_tax_incl = $objRoomTypeServiceProductOrderDetail->unit_price_tax_incl * $quantity;
                    $res = true;
                    if ($res &= $objRoomTypeServiceProductOrderDetail->save()) {
                        $order = new Order($objRoomTypeServiceProductOrderDetail->id_order);
                        $priceDiffTaxExcl = $objRoomTypeServiceProductOrderDetail->total_price_tax_excl - $oldPriceTaxExcl;
                        $priceDiffTaxIncl = $objRoomTypeServiceProductOrderDetail->total_price_tax_incl - $oldPriceTaxIncl;

                        $objOrderDetail->product_quantity = $quantity;
                        $objOrderDetail->total_price_tax_excl += $priceDiffTaxExcl;
                        $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;

                        $res &= $objOrderDetail->updateTaxAmount($order);

                        $res &= $objOrderDetail->update();

                        $order->total_paid_tax_excl += $priceDiffTaxExcl;
                        $order->total_paid_tax_incl += $priceDiffTaxIncl;
                        $order->total_paid += $priceDiffTaxIncl;

                        $res &= $order->update();
                    }
                }
                if (!$res) {
                    $response['hasError'] = true;
                    $response['errors'] = $this->l('Error while updating service, please try refresing the page');
                }
            } else {
                $response['hasError'] = true;
                $response['errors'] = $this->l('cannot order multiple quanitity for this service');
            }
            $objHotelBookingDetail = new HotelBookingDetail($objRoomTypeServiceProductOrderDetail->id_htl_booking_detail);
            $response['service_panel']= $servicesBlock = $this->processRenderServicesPanel(
                $objOrderDetail->id_order,
                $objHotelBookingDetail->id_product,
                $objHotelBookingDetail->date_from,
                $objHotelBookingDetail->date_to,
                $objHotelBookingDetail->id_room,
                true
            );
        } else {
            $response['hasError'] = true;
            $response['errors'] = $this->l('Additional service not found');
        }

        die(json_encode($response));
    }

    public function ajaxProcessDeleteRoomAdditionalService()
    {
        $response = array('hasError' => false);
        $id_room_type_service_product_order_detail = Tools::getValue('id_room_type_service_product_order_detail');

        if (Validate::isLoadedObject($objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail($id_room_type_service_product_order_detail))) {
            $objOrderDetail = new OrderDetail($objRoomTypeServiceProductOrderDetail->id_order_detail);
            $priceTaxExcl = $objRoomTypeServiceProductOrderDetail->total_price_tax_excl;
            $priceTaxIncl = $objRoomTypeServiceProductOrderDetail->total_price_tax_incl;
            $quantity = $objRoomTypeServiceProductOrderDetail->quantity;
            $res = true;
            $objHotelBookingDetail = new HotelBookingDetail($objRoomTypeServiceProductOrderDetail->id_htl_booking_detail);

            if ($res &= $objRoomTypeServiceProductOrderDetail->delete()) {
                $order = new Order($objRoomTypeServiceProductOrderDetail->id_order);
                if ($quantity >= $objOrderDetail->product_quantity) {
                    $objOrderDetail->delete();
                } else {
                    $objOrderDetail->product_quantity -= $quantity;

                    $objOrderDetail->total_price_tax_excl -= $priceTaxExcl;
                    $objOrderDetail->total_price_tax_incl -= $priceTaxIncl;

                    $res &= $objOrderDetail->updateTaxAmount($order);

                    $res &= $objOrderDetail->update();
                }

                $order->total_paid_tax_excl -= $priceTaxExcl;
                $order->total_paid_tax_incl -= $priceTaxIncl;
                $order->total_paid -= $priceTaxIncl;

                $res &= $order->update();
            }
            if ($res) {
                $response['service_panel']= $servicesBlock = $this->processRenderServicesPanel(
                    $objOrderDetail->id_order,
                    $objHotelBookingDetail->id_product,
                    $objHotelBookingDetail->date_from,
                    $objHotelBookingDetail->date_to,
                    $objHotelBookingDetail->id_room,
                    true
                );
            }
        } else {
            $response['hasError'] = true;
            $response['errors'] = $this->l('Additional service not found');
        }

        die(json_encode($response));
    }

    public function ajaxProcessAddRoomAdditionalServices()
    {
        $idBookingDetail = Tools::getValue('id_booking_detail');
        $response = array('hasError' => false);
        if ($selectedServices = Tools::getValue('selected_service')) {
            // valiadate services being added
            if (Validate::isLoadedObject($objHotelBookingDetail = new HotelBookingDetail($idBookingDetail))) {
                $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                $qty = Tools::getValue('service_qty');
                foreach ($selectedServices as $key => $service) {
                    if ($objRoomTypeServiceProduct->isRoomTypeLinkedWithProduct($objHotelBookingDetail->id_product, $service)) {
                        $objProduct = new Product($service, false, $this->context->language->id);
                        if ($objProduct->allow_multiple_quantity) {
                            if (!Validate::isUnsignedInt($qty[$service])) {
                                $response['hasError'] = true;
                                $response['errors'][] = sprintf($this->l('The quantity you\'ve entered is invalid for %s.'), $objProduct->name);
                            }
                        } else {
                            $qty[$service] = 1;
                        }
                        $selectedServices[$key] = array(
                            'id' => $service,
                            'qty' => $qty[$service],
                            'name' => $objProduct->name,
                        );
                    } else {
                        $response['hasError'] = true;
                        $response['errors'][] = sprintf($this->l('The service %s is not avaialable for current room.'), $objProduct->name);
                    }
                }

                $objHotelCartBookingData = new HotelCartBookingData();
                $roomHtlCartInfo = $objHotelCartBookingData->getRoomRowByIdProductIdRoomInDateRange(
                    $objHotelBookingDetail->id_cart,
                    $objHotelBookingDetail->id_product,
                    $objHotelBookingDetail->date_from,
                    $objHotelBookingDetail->date_to,
                    $objHotelBookingDetail->id_room
                );

                // add services in room
                if (!$response['hasError']) {
                    foreach ($selectedServices as $service) {
                        $order = new Order($objHotelBookingDetail->id_order);

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

                        $this->context->cart = $cart;
                        $this->context->customer = new Customer($order->id_customer);

                        $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
                        $objRoomTypeServiceProductCartDetail->addServiceProductInCart(
                            $service['id'],
                            $service['qty'],
                            $cart->id,
                            $roomHtlCartInfo['id']
                        );
                        $productList = $cart->getProducts();
                        $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
                        $objHotelRoomType = new HotelRoomType();
                        $roomInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($objHotelBookingDetail->id_product);
                        $totalPriceChangeTaxExcl = 0;
                        $totalPriceChangeTaxIncl = 0;
                        foreach ($productList as &$product) {
                            $totalPriceChangeTaxExcl += $totalPriceTaxExcl = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                $roomInfo['id'],
                                $product['cart_quantity'],
                                $objHotelBookingDetail->date_from,
                                $objHotelBookingDetail->date_to,
                                false
                            );
                            $totalPriceChangeTaxIncl += $totalPriceTaxIncl = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                $roomInfo['id'],
                                $product['cart_quantity'],
                                $objHotelBookingDetail->date_from,
                                $objHotelBookingDetail->date_to,
                                true
                            );
                            $unitPriceTaxExcl = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                $roomInfo['id'],
                                1,
                                $objHotelBookingDetail->date_from,
                                $objHotelBookingDetail->date_to,
                                false
                            );
                            $unitPriceTaxIncl = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                $roomInfo['id'],
                                1,
                                $objHotelBookingDetail->date_from,
                                $objHotelBookingDetail->date_to,
                                true
                            );
                            switch (Configuration::get('PS_ROUND_TYPE')) {
                                case Order::ROUND_TOTAL:
                                    $product['total'] = $totalPriceTaxExcl;
                                    $product['total_wt'] = $totalPriceTaxIncl;
                                    break;
                                case Order::ROUND_LINE:
                                    $product['total'] = Tools::ps_round($totalPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $product['total_wt'] = Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                    break;

                                case Order::ROUND_ITEM:
                                default:
                                    $product['total'] = Tools::ps_round($totalPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_) * (int)$product['cart_quantity'];
                                    $product['total_wt'] = Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_) * (int)$product['cart_quantity'];
                                    break;
                            }


                        }

                        $order_detail = new OrderDetail();
                        $order_detail->createList($order, $cart, $order->getCurrentOrderState(), $productList, (isset($order_invoice) ? $order_invoice->id : 0), true);

                        $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
                        $objRoomTypeServiceProductOrderDetail->id_product = $product['id_product'];
                        $objRoomTypeServiceProductOrderDetail->id_order = $objHotelBookingDetail->id_order;
                        $objRoomTypeServiceProductOrderDetail->id_order_detail = $order_detail->id;
                        $objRoomTypeServiceProductOrderDetail->id_cart = $cart->id;
                        $objRoomTypeServiceProductOrderDetail->id_htl_booking_detail = $objHotelBookingDetail->id;
                        $objRoomTypeServiceProductOrderDetail->unit_price_tax_excl = $unitPriceTaxExcl;
                        $objRoomTypeServiceProductOrderDetail->unit_price_tax_incl = $unitPriceTaxIncl;
                        $objRoomTypeServiceProductOrderDetail->total_price_tax_excl = $totalPriceTaxExcl;
                        $objRoomTypeServiceProductOrderDetail->total_price_tax_incl = $totalPriceTaxIncl;
                        $objRoomTypeServiceProductOrderDetail->name = $product['name'];
                        $objRoomTypeServiceProductOrderDetail->quantity = $product['cart_quantity'];
                        $objRoomTypeServiceProductOrderDetail->save();

                        // update totals amount of order
                        $order->total_products += (float)$totalPriceChangeTaxExcl;
                        $order->total_products_wt += (float)$totalPriceChangeTaxIncl;

                        $order->total_paid += Tools::ps_round((float)$totalPriceChangeTaxIncl, 2);
                        $order->total_paid_tax_excl += Tools::ps_round((float)($totalPriceChangeTaxExcl), 2);
                        $order->total_paid_tax_incl += Tools::ps_round((float)($totalPriceChangeTaxIncl), 2);

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
                    }
                }
                $response['service_panel'] = $servicesBlock = $this->processRenderServicesPanel(
                    $order->id,
                    $objHotelBookingDetail->id_product,
                    $objHotelBookingDetail->date_from,
                    $objHotelBookingDetail->date_to,
                    $objHotelBookingDetail->id_room,
                    true
                );
            } else {
                $response['hasError'] = true;
                $response['errors'][] = $this->l('Room not found');
            }
        }

        die(json_encode($response));
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
        $response = array('success' => false);
        if ($idHtlBooking = Tools::getValue('id_htl_booking')) {
            if (Validate::isLoadedObject($objBookingDetail = new HotelBookingDetail($idHtlBooking))) {
                $roomDemands = Tools::getValue('room_demands');
                if ($roomDemands = json_decode($roomDemands, true)) {
                    $order = new Order($objBookingDetail->id_order);
                    $vatAddress = new Address((int)$order->id_address_tax);
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
                                $objBookingDetail->date_from,
                                $objBookingDetail->date_to
                            );
                            if ($numDays > 1) {
                                $qty *= $numDays;
                            }
                        }
                        $objBookingDemand->total_price_tax_excl = $objBookingDemand->unit_price_tax_excl * $qty;
                        $objBookingDemand->total_price_tax_incl = $objBookingDemand->unit_price_tax_incl * $qty;

                        $order_detail = new OrderDetail($objBookingDetail->id_order_detail);
                        // Update OrderInvoice of this OrderDetail
                        if ($order_detail->id_order_invoice != 0) {
                            // values changes as values are calculated accoding to the quantity of the product by webkul
                            $order_invoice = new OrderInvoice($order_detail->id_order_invoice);
                            $order_invoice->total_paid_tax_excl += $objBookingDemand->total_price_tax_excl;
                            $order_invoice->total_paid_tax_incl += $objBookingDemand->total_price_tax_incl;
                            $res &= $order_invoice->update();
                        }

                        // change order total
                        $order->total_paid_tax_excl += $objBookingDemand->total_price_tax_excl;
                        $order->total_paid_tax_incl += $objBookingDemand->total_price_tax_incl;
                        $order->total_paid += $objBookingDemand->total_price_tax_incl;

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
                    if ($order->save()) {
                        $response['facilities_panel'] = $this->processRenderFacilitiesBlock(
                            $order->id,
                            $objBookingDetail->id_product,
                            $objBookingDetail->date_from,
                            $objBookingDetail->date_to,
                            $objBookingDetail->id_room,
                            true
                        );
                        $response['success'] = true;
                    }
                }
            }
        }
        die(json_encode($response));
    }

    // delete room extra demand while order edit
    public function ajaxProcessDeleteRoomExtraDemand()
    {
        $response = array('success' => false);
        $res = true;
        if ($idBookingDemand = Tools::getValue('id_booking_demand')) {
            if (Validate::isLoadedObject($objBookingDemand = new HotelBookingDemands($idBookingDemand))) {
                // first delete the tax details of the booking demand
                if ($objBookingDemand->deleteBookingDemandTaxDetails($idBookingDemand)) {
                    if ($objBookingDemand->delete()) {
                        if (Validate::isLoadedObject($objBookingDetail = new HotelBookingDetail($objBookingDemand->id_htl_booking))) {
                            // change order total
                            $order = new Order($objBookingDetail->id_order);
                            $order->total_paid_tax_excl -= $objBookingDemand->total_price_tax_excl;
                            $order->total_paid_tax_incl -= $objBookingDemand->total_price_tax_incl;
                            $order->total_paid -= $objBookingDemand->total_price_tax_incl;
                            $order->save();

                            $order_detail = new OrderDetail($objBookingDetail->id_order_detail);
                            // Update OrderInvoice of this OrderDetail
                            if ($order_detail->id_order_invoice != 0) {
                                // values changes as values are calculated accoding to the quantity of the product by webkul
                                $order_invoice = new OrderInvoice($order_detail->id_order_invoice);
                                $order_invoice->total_paid_tax_excl -= $objBookingDemand->total_price_tax_excl;
                                $order_invoice->total_paid_tax_incl -= $objBookingDemand->total_price_tax_incl;
                                $res &= $order_invoice->update();
                            }
                            if ($res) {
                                $response['facilities_panel'] = $this->processRenderFacilitiesBlock(
                                    $order->id,
                                    $objBookingDetail->id_product,
                                    $objBookingDetail->date_from,
                                    $objBookingDetail->date_to,
                                    $objBookingDetail->id_room,
                                    true
                                );
                                $response['success'] = true;
                            }
                        }
                    }
                }
            }
        }
        die(json_encode($response));
    }

    public function ajaxProcessUpdateServiceProduct()
    {
        $operator = Tools::getValue('operator', 'up');
        $idServiceProduct = Tools::getValue('id_product');
        $idCartBooking = Tools::getValue('id_cart_booking');
        $qty = Tools::getValue('qty');

        if (Validate::isLoadedObject($objHotelCartBookingData = new HotelCartBookingData($idCartBooking))) {
            $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();

            if ($operator == 'up') {
                $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                if ($objRoomTypeServiceProduct->isRoomTypeLinkedWithProduct($objHotelCartBookingData->id_product, $idServiceProduct)) {
                    // validate quanitity
                    if (Validate::isLoadedObject($objProduct = new Product($idServiceProduct))) {
                        if ($objProduct->allow_multiple_quantity) {
                            if (!Validate::isUnsignedInt($qty)) {
                                $this->errors[] = Tools::displayError('The quantity code you\'ve entered is invalid.');
                            // } elseif ($objProduct->max_quantity && $qty > $objProduct->max_quantity) {
                            //     $this->errors[] = Tools::displayError(sprintf('cannot add more than %d quantity.', $objProduct->max_quantity));
                            }
                        } else {
                            $qty = 1;
                        }
                    } else {
                        $this->errors[] = Tools::displayError('This Service is not available.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('This Service is not available with selected room.');
                }
            }

            if (empty($this->errors)) {
                if ($objRoomTypeServiceProductCartDetail->updateCartServiceProduct(
                    $idCartBooking,
                    $idServiceProduct,
                    $qty,
                    $objHotelCartBookingData->id_cart,
                    $operator
                )) {
                    $this->ajaxDie(json_encode(array(
                        'hasError' => false
                    )));
                } else {
                    $this->errors[] = Tools::displayError('Unable to update services. Please try reloading the page.');
                }

            }
        } else {
            $this->errors[] = Tools::displayError('Room not found. Please try reloading the page.');
        }
        $this->ajaxDie(json_encode(array(
            'hasError' => true,
            'errors' => $this->errors
        )));
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