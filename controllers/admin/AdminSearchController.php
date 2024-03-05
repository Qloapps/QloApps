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

class AdminSearchControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->controllers = array(
            'AdminProducts' => 1,
            'AdminCategories' => 1,
            'AdminFeatures' => 1,
            'AdminOrders' => 1,
            'AdminOrderRefundRules' => 1,
            'AdminRoomTypeGlobalDemand' => 1,
            'AdminGroups' => 1,
            'AdminHotelFeatures' => 1,
            'AdminCustomers' => 1,
            'AdminAddHotel' => 1,
            'AdminNormalProducts' => 1,
            'AdminAddresses' => 1,
            'AdminCustomerThreads' => 1,
            'AdminModules' => 1
        );
    }

    public function postProcess()
    {
        $this->context = Context::getContext();
        $this->query = trim(Tools::getValue('bo_query'));
        $searchType = (int)Tools::getValue('bo_search_type');
        /* Handle empty search field */
        if (!empty($this->query)) {
            if ($this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
                $this->setControllerAccesses();
            }

            if (!$searchType && strlen($this->query) > 1) {
                $this->searchFeatures();
            }

            /* Product research */
            if (!$searchType || $searchType == 1) {
                /* Handle product ID */
                if ($searchType == 1 && (int)$this->query && Validate::isUnsignedInt((int)$this->query)) {
                    if (($product = new Product($this->query)) && Validate::isLoadedObject($product)) {
                        if ($product->booking_product) {
                            Tools::redirectAdmin('index.php?tab=AdminProducts&id_product='.(int)($product->id).'&updateproduct&token='.Tools::getAdminTokenLite('AdminProducts'));
                        } else {
                            Tools::redirectAdmin('index.php?tab=AdminNormalProducts&id_product='.(int)($product->id).'&updateproduct&token='.Tools::getAdminTokenLite('AdminNormalProducts'));
                        }
                    }
                }

                /* Normal catalog search */
                $this->searchCatalog();
            }

            /* Customer */
            if (!$searchType || $searchType == 2 || $searchType == 6) {
                if (!$searchType || $searchType == 2) {
                    /* Handle customer ID */
                    if ($searchType && (int)$this->query && Validate::isUnsignedInt((int)$this->query)) {
                        if (($customer = new Customer($this->query)) && Validate::isLoadedObject($customer)) {
                            Tools::redirectAdmin('index.php?tab=AdminCustomers&id_customer='.(int)$customer->id.'&viewcustomer'.'&token='.Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id));
                        }
                    }

                    /* Normal customer search */
                    $this->searchCustomer();
                }

                if ($searchType == 6) {
                    $this->searchIP();
                }

                if (isset($this->_list['customers']) && is_array($this->_list['customers']) && count($this->_list['customers'])) {
                    $this->addHotelRestrictionsToSearchedCustomers('customers');
                }
            }

            /* Order */
            if (!$searchType || $searchType == 3) {
                if (Validate::isUnsignedInt(trim($this->query)) && (int)$this->query && ($order = new Order((int)$this->query)) && Validate::isLoadedObject($order)) {
                    if ($searchType == 3) {
                        Tools::redirectAdmin('index.php?tab=AdminOrders&id_order='.(int)$order->id.'&vieworder'.'&token='.Tools::getAdminTokenLite('AdminOrders'));
                    } else {
                        $row = get_object_vars($order);
                        $idHotel = HotelBookingDetail::getIdHotelByIdOrder($row['id']);
                        if (in_array($idHotel, HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1))) {
                            $row['id_order'] = $row['id'];
                            $customer = $order->getCustomer();
                            $row['customer'] = $customer->firstname.' '.$customer->lastname;
                            $order_state = $order->getCurrentOrderState();
                            $row['osname'] = $order_state->name[$this->context->language->id];
                            $this->_list['orders'] = array($row);
                        }

                    }
                } else {
                    $orders = Order::getByReference($this->query);
                    $nb_orders = count($orders);
                    if ($nb_orders == 1 && $searchType == 3) {
                        Tools::redirectAdmin('index.php?tab=AdminOrders&id_order='.(int)$orders[0]->id.'&vieworder'.'&token='.Tools::getAdminTokenLite('AdminOrders'));
                    } elseif ($nb_orders) {
                        $this->_list['orders'] = array();
                        foreach ($orders as $order) {
                            /** @var Order $order */
                            $row = get_object_vars($order);
                            $idHotel = HotelBookingDetail::getIdHotelByIdOrder($row['id']);
                            if (in_array($idHotel, HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1))) {
                                $row['id_order'] = $row['id'];
                                $customer = $order->getCustomer();
                                $row['customer'] = $customer->firstname.' '.$customer->lastname;
                                $order_state = $order->getCurrentOrderState();
                                $row['osname'] = $order_state->name[$this->context->language->id];
                                $this->_list['orders'][] = $row;
                            }
                        }
                    }
                }

                $this->searchOrderMessages();
            }

            /* Invoices */
            if ($searchType == 4) {
                if (Validate::isOrderInvoiceNumber($this->query) && ($invoice = OrderInvoice::getInvoiceByNumber($this->query))) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateInvoicePDF&id_order='.(int)($invoice->id_order));
                }
                $this->errors[] = Tools::displayError('No invoice was found with this ID:').' '.Tools::htmlentitiesUTF8($this->query);
            }

            /* Cart */
            if ($searchType == 5) {
                if ((int)$this->query && Validate::isUnsignedInt((int)$this->query) && ($cart = new Cart($this->query)) && Validate::isLoadedObject($cart)) {
                    Tools::redirectAdmin('index.php?tab=AdminCarts&id_cart='.(int)($cart->id).'&viewcart'.'&token='.Tools::getAdminToken('AdminCarts'.(int)(Tab::getIdFromClassName('AdminCarts')).(int)$this->context->employee->id));
                }
                $this->errors[] = Tools::displayError('No cart was found with this ID:').' '.Tools::htmlentitiesUTF8($this->query);
            }
            /* IP */
            // 6 - but it is included in the customer block

            /* Module search */
            if (!$searchType || $searchType == 7) {
                /* Handle module name */
                if ($searchType == 7 && Validate::isModuleName($this->query) and ($module = Module::getInstanceByName($this->query)) && Validate::isLoadedObject($module)) {
                    Tools::redirectAdmin('index.php?tab=AdminModules&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name).'&token='.Tools::getAdminTokenLite('AdminModules'));
                }

                /* Normal catalog search */
                $this->searchModule();
            }

            if (!$searchType || $searchType == 8) {
                if ($searchType == 8 && (int)$this->query && Validate::isUnsignedInt((int)$this->query)) {
                    if (($objHotelBranchInfo = new HotelBranchInformation((int) $this->query))
                        && Validate::isLoadedObject($objHotelBranchInfo)
                    ) {
                        Tools::redirectAdmin('index.php?tab=AdminAddHotel&id='.$objHotelBranchInfo->id.'&updatehtl_branch_info'.'&token='.Tools::getAdminTokenLite('AdminAddHotel'));
                    }
                }

                $this->searchHotel();
            }

            if (!$searchType) {
                $this->searchAddress();
                $this->searchHotelFeatures();
                $this->searchAdditionalFacilities();
                $this->searchRefundRules();
            }
        }

        $this->display = 'view';
    }

    public function setControllerAccesses()
    {
        $sql = 'SELECT a.`view`, t.`class_name` FROM `'._DB_PREFIX_.'access` a
            LEFT JOIN `'._DB_PREFIX_.'tab` t ON (t.`id_tab` = a.`id_tab`)
            WHERE t.`class_name` IN ("'.implode('", "', array_keys($this->controllers)).'")
            AND a.`id_profile` = '.(int) $this->context->employee->id_profile.'
        ';

        if ($tabs = Db::getInstance()->executeS($sql)) {
            foreach ($tabs as $tab) {
                $this->controllers[$tab['class_name']] = $tab['view'];
            }
        }
    }

    public function searchIP()
    {
        if (!ip2long(trim($this->query))) {
            $this->errors[] = Tools::displayError('This is not a valid IP address:').' '.Tools::htmlentitiesUTF8($this->query);
            return;
        }
        $this->_list['customers'] = Customer::searchByIp($this->query);
    }

    /**
    * Search a specific string in the products and categories
    *
    * @params string $query String to find in the catalog
    */
    public function searchCatalog()
    {
        if (isset($this->controllers['AdminProducts'])
            && $this->controllers['AdminProducts']
            && ($this->_list['products'] = Product::searchByName($this->context->language->id, $this->query))
        ) {
            $objRoomType = new HotelRoomType();
            $accessableHotels = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1);
            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
            foreach ($this->_list['products'] as $key => $product) {
                if ($roomInfo = $objRoomType->getRoomTypeInfoByIdProduct($product['id_product'])) {
                    if (!in_array($roomInfo['id_hotel'], $accessableHotels)) {
                        unset($this->_list['products'][$key]);
                    }
                } else if ($associatedData = $objRoomTypeServiceProduct->getAssociatedHotelsAndRoomType($product['id_product'])) {
                    $count = 0;
                    foreach ($associatedData['room_types'] as $id_room_type) {
                        $objRoomType = new HotelRoomType($id_room_type);
                        if (in_array($objRoomType->id_hotel, $accessableHotels)) {
                            $count += 1;
                        }
                    }

                    if ($count) {
                        $this->_list['service_products'][] = $this->_list['products'][$key];
                    }

                    unset($this->_list['products'][$key]);
                }
            }
        }

        if (isset($this->controllers['AdminCategories']) && $this->controllers['AdminCategories']) {
            $this->_list['categories'] = Category::searchByName($this->context->language->id, $this->query);
        }

        if (isset($this->controllers['AdminFeatures']) && $this->controllers['AdminFeatures']) {
            $this->_list['catalog_features'] = Feature::searchByName($this->query, $this->context->language->id);
        }
    }

    /**
    * Search a specific name in the customers
    *
    * @params string $query String to find in the catalog
    */
    public function searchCustomer()
    {

        if (isset($this->controllers['AdminCustomers'])
            && $this->controllers['AdminCustomers']
        ) {
            $this->_list['customers'] = Customer::searchByName($this->query);
        }

        $objGroup = new Group();
        if (isset($this->controllers['AdminGroups']) && $this->controllers['AdminGroups']) {
            $this->_list['groups'] = $objGroup->getRelatedGroups($this->query);
        }
    }

    public function searchModule()
    {
        if (isset($this->controllers['AdminModules']) && $this->controllers['AdminModules']) {
            $this->_list['modules'] = array();
            $all_modules = Module::getModulesOnDisk(true, true, Context::getContext()->employee->id);
            foreach ($all_modules as $module) {
                if (stripos($module->name, $this->query) !== false || stripos($module->displayName, $this->query) !== false || stripos($module->description, $this->query) !== false) {
                    $module->linkto = 'index.php?tab=AdminModules&tab_module='.$module->tab.'&module_name='.$module->name.'&anchor='.ucfirst($module->name).'&token='.Tools::getAdminTokenLite('AdminModules');
                    $this->_list['modules'][] = $module;
                }
            }

            if (!is_numeric(trim($this->query)) && !Validate::isEmail($this->query)) {
                $iso_lang = Tools::strtolower(Context::getContext()->language->iso_code);
                $iso_country = Tools::strtolower(Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT')));
                if (($json_content = Tools::file_get_contents('https://api-addons.prestashop.com/'._PS_VERSION_.'/search/'.urlencode($this->query).'/'.$iso_country.'/'.$iso_lang.'/')) != false) {
                    $results = json_decode($json_content, true);
                    if (isset($results['id'])) {
                        $this->_list['addons']  = array($results);
                    } else {
                        $this->_list['addons']  =  $results;
                    }
                }
            }
        }
    }

    /**
    * Search a feature in all store
    *
    * @params string $query String to find in the catalog
    */
    public function searchFeatures()
    {
        $this->_list['features'] = array();

        global $_LANGADM;
        if ($_LANGADM === null) {
            return;
        }

        $tabs = array();
        $key_match = array();
        $result = Db::getInstance()->executeS('
		SELECT class_name, name
		FROM '._DB_PREFIX_.'tab t
		INNER JOIN '._DB_PREFIX_.'tab_lang tl ON (t.id_tab = tl.id_tab AND tl.id_lang = '.(int)$this->context->employee->id_lang.')
		LEFT JOIN '._DB_PREFIX_.'access a ON (a.id_tab = t.id_tab AND a.id_profile = '.(int)$this->context->employee->id_profile.')
		WHERE active = 1
		'.($this->context->employee->id_profile != 1 ? 'AND view = 1' : '').
        (defined('_PS_HOST_MODE_') ? ' AND t.`hide_host_mode` = 0' : '')
        );
        foreach ($result as $row) {
            $tabs[strtolower($row['class_name'])] = $row['name'];
            $key_match[strtolower($row['class_name'])] = $row['class_name'];
        }
        foreach (AdminTab::$tabParenting as $key => $value) {
            $value = stripslashes($value);
            if (!isset($tabs[strtolower($key)]) || !isset($tabs[strtolower($value)])) {
                continue;
            }
            $tabs[strtolower($key)] = $tabs[strtolower($value)];
            $key_match[strtolower($key)] = $key;
        }

        $this->_list['features'] = array();
        foreach ($_LANGADM as $key => $value) {
            if (stripos($value, $this->query) !== false) {
                $value = stripslashes($value);
                $key = strtolower(substr($key, 0, -32));
                if (in_array($key, array('AdminTab', 'index'))) {
                    continue;
                }
                // if class name doesn't exists, just ignore it
                if (!isset($tabs[$key])) {
                    continue;
                }
                if (!isset($this->_list['features'][$tabs[$key]])) {
                    $this->_list['features'][$tabs[$key]] = array();
                }
                $this->_list['features'][$tabs[$key]][] = array('link' => Context::getContext()->link->getAdminLink($key_match[$key]), 'value' => Tools::safeOutput($value));
            }
        }
    }

    public function searchHotel()
    {
        if (class_exists('HotelBranchInformation')) {
            if (isset($this->controllers['AdminAddHotel']) && $this->controllers['AdminAddHotel']) {
                $objHotelBranchInformation = new HotelBranchInformation();
                $this->_list['hotels'] = $objHotelBranchInformation->getAccessibleHotelByName($this->query);
            }
        }
    }

    public function searchOrderMessages()
    {
        if (class_exists('CustomerMessage')) {
            if (isset($this->controllers['AdminCustomerThreads']) && $this->controllers['AdminCustomerThreads']) {
                $objCustomerMessage = new CustomerMessage();
                if ($this->_list['order_messages'] = $objCustomerMessage->searchCustomerMessage($this->query)) {
                    $accesibleHotels = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1);
                    foreach ($this->_list['order_messages'] as $key => $msg) {
                        if ($msg['id_order']) {
                            // To set set restriction on the messages belonging to an order. While the other messages will be show to all
                            $idHotel = HotelBookingDetail::getIdHotelByIdOrder($msg['id_order']);
                            if (!in_array($idHotel, $accesibleHotels)) {
                                unset($this->_list['order_messages'][$key]);
                            }
                        }
                    }
                }
            }
        }
    }

    public function searchAddress()
    {
        if (isset($this->controllers['AdminAddresses']) && $this->controllers['AdminAddresses']) {
            $objAddress = new Address();
            if ($this->_list['customer_address'] = $objAddress->getCustomersAddresses($this->query)) {
                $this->addHotelRestrictionsToSearchedCustomers('customer_address');
            }
        }
    }

    public function searchHotelFeatures()
    {
        if (class_exists('HotelFeatures')) {
            if (isset($this->controllers['AdminHotelFeatures']) && $this->controllers['AdminHotelFeatures']) {
                $objHotelFeatures = new HotelFeatures();
                if ($hotelFeatures = $objHotelFeatures->searchHotelFeatureByName($this->query)) {
                    $features = array();
                    foreach ($hotelFeatures as $key => $hotelFeature) {
                        $features[$hotelFeature['id']]['name'] = $hotelFeature['name'];
                        if ($hotelFeature['parent_feature_id']) {
                            $features[$hotelFeature['id']]['id'] = $hotelFeature['parent_feature_id'];
                        } else {
                            $features[$hotelFeature['id']]['id'] = $hotelFeature['id'];
                        }
                    }

                    $this->_list['hotel_features'] = $features;
                }
            }
        }
    }

    public function searchAdditionalFacilities()
    {
        if (class_exists('HotelRoomTypeGlobalDemand')) {
            if (isset($this->controllers['AdminRoomTypeGlobalDemand']) && $this->controllers['AdminRoomTypeGlobalDemand']) {
                $objHotelRoomTypeGlobalDemands = new HotelRoomTypeGlobalDemand();
                if ($globalDemads = $objHotelRoomTypeGlobalDemands->searchRoomTypeDemandsByName($this->query)) {
                    foreach ($globalDemads as $key => $demand) {
                        if (!(int) $globalDemads[$key]['price']) {
                            $globalDemads[$key]['price'] = $globalDemads[$key]['option_price'];
                        }

                        $globalDemads[$key]['per_day_price_calc'] = $this->l('No');
                        if ($globalDemads[$key]['price_calc_method'] == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY) {
                            $globalDemads[$key]['per_day_price_calc'] = $this->l('Yes');
                        }
                    }

                    $this->_list['global_demands'] = $globalDemads;
                }
            }
        }
    }

    public function searchRefundRules()
    {
        if (class_exists('HotelOrderRefundRules')) {
            if (isset($this->controllers['AdminOrderRefundRulesController']) && $this->controllers['AdminOrderRefundRulesController']) {
                $objRefundRule = new HotelOrderRefundRules();
                if ($refundRules = $objRefundRule->searchOrderRefundRulesByName($this->query)) {
                    foreach ($refundRules as $key => $rule) {
                        $refundRules[$key]['deduction_type'] = $this->l('Percentage');
                        if ($rule['payment_type'] == HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_FIXED) {
                            $refundRules[$key]['deduction_type'] = $this->l('Fixed Amount');
                            $refundRules[$key]['deduction_value_full_pay'] = Tools::displayPrice($rule['deduction_value_full_pay']);
                            $refundRules[$key]['deduction_value_adv_pay'] = Tools::displayPrice($rule['deduction_value_adv_pay']);
                        } else if ($rule['payment_type'] == HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE) {
                            $refundRules[$key]['deduction_value_full_pay'] = $rule['deduction_value_full_pay'].' %';
                            $refundRules[$key]['deduction_value_adv_pay'] = $rule['deduction_value_adv_pay'].' %';
                        }
                    }

                    $this->_list['refund_rules'] = $refundRules;
                }
            }
        }
    }

    protected function addHotelRestrictionsToSearchedCustomers($index)
    {
        $accessibleHotels = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1);
        foreach ($this->_list[$index] as $key => $item) {
            if ($this->context->employee->id_profile != _PS_ADMIN_PROFILE_) {
                $customerBelongsToHotel = 0;
                if(isset($item['id_customer'])) {
                    $customerOrders = Order::getCustomerOrders($item['id_customer']);
                    if (count($customerOrders)) {
                        foreach($customerOrders as $order) {
                            $idHotel = HotelBookingDetail::getIdHotelByIdOrder($order['id_order']);
                            if (!in_array($idHotel, $accessibleHotels)) {
                                $customerBelongsToHotel += 1;
                            }
                        }
                    }
                }

                if (!$customerBelongsToHotel) {
                    unset($this->_list[$index][$key]);
                }
            }
        }
    }

    protected function initGroupList()
    {
        $this->show_toolbar = false;
        $this->fields_list['groups'] = array(
            'id_group' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
            'name' => array('title' => $this->l('Name'), 'align' => 'center', 'width' => 65),
            'reduction' => array('title' => $this->l('Discount')),
            'show_prices' => array('title' => $this->l('Show prices'), 'callback' => 'printShowPricesIcon'),
            'date_add' => array('title' => $this->l('Creation date'), 'width' => 130, 'align' => 'right', 'type' => 'datetime'),
        );
    }

    public function printShowPricesIcon($id_group, $tr)
    {
        $group = new Group($tr['id_group']);
        if (!Validate::isLoadedObject($group)) {
            return;
        }
        return '<a class="list-action-enable'.($group->show_prices ? ' action-enabled' : ' action-disabled').'" href="index.php?tab=AdminGroups&amp;id_group='.(int)$group->id.'&amp;changeShowPricesVal&amp;token='.Tools::getAdminTokenLite('AdminGroups').'">
				'.($group->show_prices ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>').
            '</a>';
    }

    protected function initOrderList()
    {
        $this->show_toolbar = false;
        $this->fields_list['orders'] = array(
            'reference' => array('title' => $this->l('Reference'), 'align' => 'center', 'width' => 65),
            'id_order' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
            'customer' => array('title' => $this->l('Customer')),
            'total_paid_tax_incl' => array('title' => $this->l('Total'), 'width' => 70, 'align' => 'right', 'type' => 'price', 'currency' => true),
            'payment' => array( 'title' => $this->l('Payment'), 'width' => 100),
            'osname' => array('title' => $this->l('Status'), 'width' => 280),
            'date_add' => array('title' => $this->l('Date'), 'width' => 130, 'align' => 'right', 'type' => 'datetime'),
        );
    }

    protected function initGlobalDemandList()
    {
        $this->show_toolbar = false;
        $this->fields_list['global_demands'] = array(
            'id_global_demand' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
            'name' => array('title' => $this->l('Name')),
            'option_name' => array('title' => $this->l('Advance Option Name')),
            'price' => array('title' => $this->l('Price'), 'type' => 'price', 'currency' => true),
            'per_day_price_calc' => array('title' => $this->l('Per day price calculation'))
        );
    }

    protected function initRefundRuleList()
    {
        $this->show_toolbar = false;
        $this->fields_list['refund_rules'] = array(
            'id_refund_rule' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
            'name' => array('title' => $this->l('Name')),
            'payment_type' => array('title' => $this->l('Payment Type')),
            'deduction_value_full_pay' => array('title' => $this->l('Full Payment Deduction Percentage/Amount')),
            'deduction_value_adv_pay' => array('title' => $this->l('Full Payment Deduction Percentage/Amount')),
            'days' => array('title' => $this->l('Days Before Check-in')),
        );
    }

    protected function initOrderMessagesList()
    {
        $this->show_toolbar = false;
        $this->fields_list['order_messages'] = array(
            'id_customer_thread' => array('title' => $this->l('ID '), 'align' => 'center', 'width' => 65),
            'customer_name' => array('title' => $this->l('Customer Name'), 'align' => 'center'),
            'email' => array('title' => $this->l('Customer email')),
            'message' => array('title' => $this->l('Message'), 'width' => 70),
            'status' => array('title' => $this->l('Status'), 'width' => 280),
        );
    }

    protected function initCustomerList()
    {
        $genders_icon = array('default' => 'unknown.gif');
        $genders = array(0 => $this->l('?'));
        foreach (Gender::getGenders() as $gender) {
            /** @var Gender $gender */
            $genders_icon[$gender->id] = '../genders/'.(int)$gender->id.'.jpg';
            $genders[$gender->id] = $gender->name;
        }
        $this->fields_list['customers'] = (array(
            'id_customer' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
            'id_gender' => array('title' => $this->l('Social title'), 'align' => 'center', 'icon' => $genders_icon, 'list' => $genders, 'width' => 25),
            'firstname' => array('title' => $this->l('First Name'), 'align' => 'left', 'width' => 150),
            'lastname' => array('title' => $this->l('Name'), 'align' => 'left', 'width' => 'auto'),
            'email' => array('title' => $this->l('Email address'), 'align' => 'left', 'width' => 250),
            'birthday' => array('title' => $this->l('Birth date'), 'align' => 'center', 'type' => 'date', 'width' => 75),
            'date_add' => array('title' => $this->l('Registration date'), 'align' => 'center', 'type' => 'date', 'width' => 75),
            'orders' => array('title' => $this->l('Orders'), 'align' => 'center', 'width' => 50),
            'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'width' => 25),
        ));
    }

    protected function initProductList()
    {
        $this->show_toolbar = false;
        $this->fields_list['products'] = array(
            'id_product' => array('title' => $this->l('ID'), 'width' => 25),
            'name' => array('title' => $this->l('Name'), 'width' => 'auto'),
            // 'reference' => array('title' => $this->l('Reference'), 'align' => 'center', 'width' => 150),
            'price_tax_excl' => array('title' => $this->l('Price (tax excl.)'), 'align' => 'right', 'type' => 'price', 'width' => 60),
            'price_tax_incl' => array('title' => $this->l('Price (tax incl.)'), 'align' => 'right', 'type' => 'price', 'width' => 60),
            'active' => array('title' => $this->l('Active'), 'width' => 70, 'active' => 'status', 'align' => 'center', 'type' => 'bool')
        );
    }

    protected function initServiceProdList()
    {
        $this->show_toolbar = false;
        $this->fields_list['service_products'] = array(
            'id_product' => array('title' => $this->l('ID'), 'width' => 25),
            'name' => array('title' => $this->l('Name'), 'width' => 'auto'),
            'price_tax_excl' => array('title' => $this->l('Price (tax excl.)'), 'align' => 'right', 'type' => 'price', 'width' => 60),
            'price_tax_incl' => array('title' => $this->l('Price (tax incl.)'), 'align' => 'right', 'type' => 'price', 'width' => 60),
            'active' => array('title' => $this->l('Active'), 'width' => 70, 'active' => 'status', 'align' => 'center', 'type' => 'bool')
        );
    }

    protected function initFeatureList()
    {
        $this->show_toolbar = false;
        $this->fields_list['catalog_features'] = array(
            'id_feature' => array('title' => $this->l('ID'), 'width' => 25),
            'name' => array('title' => $this->l('Name'), 'width' => 'auto'),
            'logo' => array('title' => $this->l('Logo'),'image' => 'rf', 'align' => 'right', 'type' => 'price', 'width' => 60),
        );
    }

    protected function initHotelList()
    {
        $this->show_toolbar = false;
        $this->fields_list['hotels'] = array(
            'id' => array('title' => $this->l('ID'), 'width' => 25),
            'hotel_name' => array('title' => $this->l('Name'), 'width' => 'auto'),
            'city' => array('title' => $this->l('City'), 'align' => 'right'),
            'state_name' => array('title' => $this->l('State'), 'align' => 'right'),
            'country_name' => array('title' => $this->l('Country'), 'align' => 'right'),
            'active' => array('title' => $this->l('Active'), 'width' => 70, 'active' => 'status', 'align' => 'center', 'type' => 'bool')
        );
    }

    protected function initAddressList()
    {
        $this->show_toolbar = false;
        $this->fields_list['customer_address'] = array(
            'id_address' => array('title' => $this->l('ID'), 'width' => 25),
            'firstname' => array('title' => $this->l('First Name'), 'width' => 'auto'),
            'lastname' => array('title' => $this->l('Last Name'), 'align' => 'right'),
            'address1' => array('title' => $this->l('Address'), 'align' => 'right'),
            'postcode' => array('title' => $this->l('Zip/Postal Code'), 'align' => 'right'),
            'city' => array('title' => $this->l('City'), 'width' => 70)
        );
    }

    protected function initHotelFeatureList()
    {
        $this->show_toolbar = false;
        $this->fields_list['hotel_features'] = array(
            'name' => array('title' => $this->l('name'), 'width' => 'auto'),
        );
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('highlight');
    }

    /* Override because we don't want any buttons */
    public function initToolbar()
    {
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = $this->l('Search results', null, null, false);
    }

    public function renderView()
    {
        $this->tpl_view_vars['query'] = Tools::safeOutput($this->query);
        $this->tpl_view_vars['show_toolbar'] = true;

        if (count($this->errors)) {
            return parent::renderView();
        } else {
            $nb_results = 0;
            foreach ($this->_list as $list) {
                if ($list != false) {
                    $nb_results += count($list);
                }
            }
            $this->tpl_view_vars['nb_results'] = $nb_results;

            if (isset($this->_list['features']) && count($this->_list['features'])) {
                $this->tpl_view_vars['features'] = $this->_list['features'];
            }
            if (isset($this->_list['categories']) && count($this->_list['categories'])) {
                $categories = array();
                foreach ($this->_list['categories'] as $category) {
                    $categories[] = getPath($this->context->link->getAdminLink('AdminCategories', false), $category['id_category']);
                }
                $this->tpl_view_vars['categories'] = $categories;
            }
            if (isset($this->_list['products']) && $this->_list['products']&& count($this->_list['products'])) {
                $view = '';
                $this->initProductList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_product';
                $helper->actions = array('edit');
                $helper->show_toolbar = false;
                $helper->table = 'product';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminProducts', false);
                $query = trim(Tools::getValue('bo_query'));
                $searchType = (int)Tools::getValue('bo_search_type');

                if ($query) {
                    $helper->currentIndex .= '&bo_query='.$query.'&bo_search_type='.$searchType;
                }

                $helper->token = Tools::getAdminTokenLite('AdminProducts');

                if ($this->_list['products']) {
                    $view = $helper->generateList($this->_list['products'], $this->fields_list['products']);
                }

                $this->tpl_view_vars['num_products'] = count($this->_list['products']);
                $this->tpl_view_vars['products'] = $view;
            }
            if (isset($this->_list['service_products']) && $this->_list['service_products']&& count($this->_list['service_products'])) {
                $view = '';
                $this->initServiceProdList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_product';
                $helper->actions = array('edit');
                $helper->show_toolbar = false;
                $helper->table = 'product';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminNormalProducts', false);

                $query = trim(Tools::getValue('bo_query'));
                $searchType = (int)Tools::getValue('bo_search_type');

                if ($query) {
                    $helper->currentIndex .= '&bo_query='.$query.'&bo_search_type='.$searchType;
                }

                $helper->token = Tools::getAdminTokenLite('AdminNormalProducts');

                if ($this->_list['service_products']) {
                    $view = $helper->generateList($this->_list['service_products'], $this->fields_list['service_products']);
                }

                $this->tpl_view_vars['num_service_products'] = count($this->_list['service_products']);
                $this->tpl_view_vars['service_products'] = $view;
            }
            if (isset($this->_list['catalog_features']) && $this->_list['catalog_features']&& count($this->_list['catalog_features'])) {
                $view = '';
                $this->initFeatureList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_feature';
                $helper->imageType = 'jpg';
                $helper->actions = array('edit');
                $helper->show_toolbar = false;
                $helper->table = 'feature';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminFeatures', false);

                $query = trim(Tools::getValue('bo_query'));
                $searchType = (int)Tools::getValue('bo_search_type');

                if ($query) {
                    $helper->currentIndex .= '&bo_query='.$query.'&bo_search_type='.$searchType;
                }

                $helper->token = Tools::getAdminTokenLite('AdminFeatures');

                if ($this->_list['catalog_features']) {
                    $view = $helper->generateList($this->_list['catalog_features'], $this->fields_list['catalog_features']);
                }

                $this->tpl_view_vars['num_catalog_features'] = count($this->_list['catalog_features']);
                $this->tpl_view_vars['catalog_features'] = $view;
            }
            if (isset($this->_list['customer_address']) && $this->_list['customer_address']&& count($this->_list['customer_address'])) {
                $view = '';
                $this->initAddressList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_address';
                $helper->actions = array('edit');
                $helper->show_toolbar = false;
                $helper->table = 'address';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminAddresses', false);

                $query = trim(Tools::getValue('bo_query'));
                $searchType = (int)Tools::getValue('bo_search_type');

                if ($query) {
                    $helper->currentIndex .= '&bo_query='.$query.'&bo_search_type='.$searchType;
                }

                $helper->token = Tools::getAdminTokenLite('AdminAddresses');

                if ($this->_list['customer_address']) {
                    $view = $helper->generateList($this->_list['customer_address'], $this->fields_list['customer_address']);
                }

                $this->tpl_view_vars['num_customer_address'] = count($this->_list['customer_address']);
                $this->tpl_view_vars['customer_address'] = $view;
            }
            if (isset($this->_list['order_messages']) && $this->_list['order_messages']&& count($this->_list['order_messages'])) {
                $view = '';
                $this->initOrderMessagesList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_customer_message';
                $helper->actions = array('edit');
                $helper->show_toolbar = false;
                $helper->table = 'feature';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminCustomerThreads', false);

                $query = trim(Tools::getValue('bo_query'));
                $searchType = (int)Tools::getValue('bo_search_type');

                if ($query) {
                    $helper->currentIndex .= '&bo_query='.$query.'&bo_search_type='.$searchType;
                }

                $helper->token = Tools::getAdminTokenLite('AdminCustomerThreads');

                if ($this->_list['order_messages']) {
                    $view = $helper->generateList($this->_list['order_messages'], $this->fields_list['order_messages']);
                }

                $this->tpl_view_vars['num_order_messages'] = count($this->_list['order_messages']);
                $this->tpl_view_vars['order_messages'] = $view;
            }
            if (isset($this->_list['hotels']) && $this->_list['hotels']&& count($this->_list['hotels'])) {
                $view = '';
                $this->initHotelList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id';
                $helper->actions = array('edit');
                $helper->show_toolbar = false;
                $helper->table = 'htl_branch_info';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminAddHotel', false);

                $query = trim(Tools::getValue('bo_query'));
                $searchType = (int)Tools::getValue('bo_search_type');

                if ($query) {
                    $helper->currentIndex .= '&bo_query='.$query.'&bo_search_type='.$searchType;
                }

                $helper->token = Tools::getAdminTokenLite('AdminAddHotel');

                if ($this->_list['hotels']) {
                    $view = $helper->generateList($this->_list['hotels'], $this->fields_list['hotels']);
                }

                $this->tpl_view_vars['num_hotels'] = count($this->_list['hotels']);
                $this->tpl_view_vars['hotels'] = $view;
            }
            if (isset($this->_list['customers']) && count($this->_list['customers'])) {
                $view = '';
                $this->initCustomerList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_customer';
                $helper->actions = array('edit', 'view');
                $helper->show_toolbar = false;
                $helper->table = 'customer';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminCustomers', false);
                $helper->token = Tools::getAdminTokenLite('AdminCustomers');

                if ($this->_list['customers']) {
                    foreach ($this->_list['customers'] as $key => $val) {
                        $this->_list['customers'][$key]['orders'] = Order::getCustomerNbOrders((int)$val['id_customer']);
                    }
                    $view = $helper->generateList($this->_list['customers'], $this->fields_list['customers']);
                }
                $this->tpl_view_vars['num_customers'] = count($this->_list['customers']);
                $this->tpl_view_vars['customers'] = $view;
            }

            if (isset($this->_list['hotel_features']) && count($this->_list['hotel_features'])) {
                $view = '';
                $this->initHotelFeatureList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id';
                $helper->actions = array('edit', 'view');
                $helper->show_toolbar = false;
                $helper->table = 'htl_features';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminHotelFeatures', false);
                $helper->token = Tools::getAdminTokenLite('AdminHotelFeatures');

                if ($this->_list['hotel_features']) {
                    $view = $helper->generateList($this->_list['hotel_features'], $this->fields_list['hotel_features']);
                }

                $this->tpl_view_vars['num_hotel_features'] = count($this->_list['hotel_features']);
                $this->tpl_view_vars['hotel_features'] = $view;
            }
            if (isset($this->_list['groups']) && count($this->_list['groups'])) {
                $view = '';
                $this->initGroupList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_group';
                $helper->actions = array('edit');
                $helper->show_toolbar = false;
                $helper->table = 'group';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminGroups', false);

                $query = trim(Tools::getValue('bo_query'));
                $searchType = (int)Tools::getValue('bo_search_type');

                if ($query) {
                    $helper->currentIndex .= '&bo_query='.$query.'&bo_search_type='.$searchType;
                }

                $helper->token = Tools::getAdminTokenLite('AdminGroups');

                if ($this->_list['groups']) {
                    $view = $helper->generateList($this->_list['groups'], $this->fields_list['groups']);
                }

                $this->tpl_view_vars['num_groups'] = count($this->_list['groups']);
                $this->tpl_view_vars['groups'] = $view;
            }

            if (isset($this->_list['global_demands']) && count($this->_list['global_demands'])) {
                $view = '';
                $this->initGlobalDemandList();
                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_global_demand';
                $helper->actions = array('edit');
                $helper->show_toolbar = false;
                $helper->table = 'htl_room_type_global_demand';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminRoomTypeGlobalDemand', false);
                $helper->token = Tools::getAdminTokenLite('AdminRoomTypeGlobalDemand');

                if ($this->_list['global_demands']) {
                    $view = $helper->generateList($this->_list['global_demands'], $this->fields_list['global_demands']);
                }

                $this->tpl_view_vars['num_global_demands'] = count($this->_list['global_demands']);
                $this->tpl_view_vars['global_demands'] = $view;
            }

            if (isset($this->_list['refund_rules']) && count($this->_list['refund_rules'])) {
                $view = '';
                $this->initRefundRuleList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_refund_rule';
                $helper->actions = array('edit');
                $helper->show_toolbar = false;
                $helper->table = 'htl_order_refund_rules';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminOrderRefundRules', false);
                $helper->token = Tools::getAdminTokenLite('AdminOrderRefundRules');

                if ($this->_list['refund_rules']) {
                    $view = $helper->generateList($this->_list['refund_rules'], $this->fields_list['refund_rules']);
                }

                $this->tpl_view_vars['num_refund_rules'] = count($this->_list['refund_rules']);
                $this->tpl_view_vars['refund_rules'] = $view;
            }

            if (isset($this->_list['orders']) && count($this->_list['orders'])) {
                $view = '';
                $this->initOrderList();

                $helper = new HelperList();
                $helper->shopLinkType = '';
                $helper->simple_header = true;
                $helper->identifier = 'id_order';
                $helper->actions = array('view');
                $helper->show_toolbar = false;
                $helper->table = 'order';
                $helper->currentIndex = $this->context->link->getAdminLink('AdminOrders', false);
                $helper->token = Tools::getAdminTokenLite('AdminOrders');

                if ($this->_list['orders']) {
                    $view = $helper->generateList($this->_list['orders'], $this->fields_list['orders']);
                }
                $this->tpl_view_vars['num_orders'] = count($this->_list['orders']);
                $this->tpl_view_vars['orders'] = $view;
            }

            if (isset($this->_list['modules']) && count($this->_list['modules'])) {
                $this->tpl_view_vars['modules'] = $this->_list['modules'];
            }
            if (isset($this->_list['addons']) && count($this->_list['addons'])) {
                $this->tpl_view_vars['addons'] = $this->_list['addons'];
            }

            return parent::renderView();
        }
    }
}
