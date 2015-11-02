<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 * @property SupplyOrder $object
 */
class AdminSupplyOrdersControllerCore extends AdminController
{
    /**
     * @var array List of warehouses
     */
    protected $warehouses;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'supply_order';

        $this->className = 'SupplyOrder';
        $this->identifier = 'id_supply_order';
        $this->lang = false;
        $this->is_template_list = false;
        $this->multishop_context = Shop::CONTEXT_ALL;

        $this->addRowAction('updatereceipt');
        $this->addRowAction('changestate');
        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('details');
        $this->list_no_link = true;

        $this->fields_list = array(
            'reference' => array(
                'title' => $this->l('Reference'),
                'havingFilter' => true
            ),
            'supplier' => array(
                'title' => $this->l('Supplier'),
                'filter_key' => 's!name'
            ),
            'warehouse' => array(
                'title' => $this->l('Warehouse'),
                'filter_key' => 'w!name'
            ),
            'state' => array(
                'title' => $this->l('Status'),
                'filter_key' => 'stl!name',
                'color' => 'color',
            ),
            'date_add' => array(
                'title' => $this->l('Creation'),
                'align' => 'left',
                'type' => 'date',
                'havingFilter' => true,
                'filter_key' => 'a!date_add'
            ),
            'date_upd' => array(
                'title' => $this->l('Last modification'),
                'align' => 'left',
                'type' => 'date',
                'havingFilter' => true,
                'filter_key' => 'a!date_upd'
            ),
            'date_delivery_expected' => array(
                'title' => $this->l('Delivery (expected)'),
                'align' => 'left',
                'type' => 'date',
                'havingFilter' => true,
                'filter_key' => 'a!date_delivery_expected'
            ),
            'id_export' => array(
                'title' => $this->l('Export'),
                'callback' => 'printExportIcons',
                'orderby' => false,
                'search' => false
            ),
        );

        // gets the list of warehouses available
        $this->warehouses = Warehouse::getWarehouses(true);
        // gets the final list of warehouses
        array_unshift($this->warehouses, array('id_warehouse' => -1, 'name' => $this->l('All Warehouses')));

        parent::__construct();
    }

    /**
     * AdminController::init() override
     * @see AdminController::init()
     */
    public function init()
    {
        if (Tools::isSubmit('submitFilterorders')) {
            $this->list_id = 'orders';
        } elseif (Tools::isSubmit('submitFiltertemplates')) {
            $this->list_id = 'templates';
        }

        parent::init();

        if (Tools::isSubmit('addsupply_order') ||
            Tools::isSubmit('submitAddsupply_order') ||
            (Tools::isSubmit('updatesupply_order') && Tools::isSubmit('id_supply_order'))) {
            // override table, lang, className and identifier for the current controller
            $this->table = 'supply_order';
            $this->className = 'SupplyOrder';
            $this->identifier = 'id_supply_order';
            $this->lang = false;

            $this->action = 'new';
            $this->display = 'add';

            if (Tools::isSubmit('updatesupply_order')) {
                if ($this->tabAccess['edit'] === '1') {
                    $this->display = 'edit';
                } else {
                    $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                }
            }
        }

        if (Tools::isSubmit('update_receipt') && Tools::isSubmit('id_supply_order')) {
            // change the display type in order to add specific actions to
            $this->display = 'update_receipt';

            // display correct toolBar
            $this->initToolbar();
        }
    }

    public function initPageHeaderToolbar()
    {
        if ($this->display == 'details') {
            $this->page_header_toolbar_btn['back'] = array(
                'href' => Context::getContext()->link->getAdminLink('AdminSupplyOrders'),
                'desc' => $this->l('Back to list', null, null, false),
                'icon' => 'process-icon-back'
            );
        } elseif (empty($this->display)) {
            $this->page_header_toolbar_btn['new_supply_order'] = array(
                'href' => self::$currentIndex.'&addsupply_order&token='.$this->token,
                'desc' => $this->l('Add new supply order', null, null, false),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['new_supply_order_template'] = array(
                'href' => self::$currentIndex.'&addsupply_order&mod=template&token='.$this->token,
                'desc' => $this->l('Add new supply order template', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * AdminController::renderForm() override
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        if (Tools::isSubmit('addsupply_order') ||
            Tools::isSubmit('updatesupply_order') ||
            Tools::isSubmit('submitAddsupply_order') ||
            Tools::isSubmit('submitUpdatesupply_order')) {
            if (Tools::isSubmit('addsupply_order') ||    Tools::isSubmit('submitAddsupply_order')) {
                $this->toolbar_title = $this->l('Stock: Create a new supply order');
            }

            $update = false;
            if (Tools::isSubmit('updatesupply_order') || Tools::isSubmit('submitUpdatesupply_order')) {
                $this->toolbar_title = $this->l('Stock: Manage supply orders');
                $update = true;
            }

            if (Tools::isSubmit('mod') && Tools::getValue('mod') === 'template' || $this->object->is_template) {
                $this->toolbar_title .= ' ('.$this->l('template').')';
            }

            $this->addJqueryUI('ui.datepicker');

            //get warehouses list
            $warehouses = Warehouse::getWarehouses(true);

            // displays warning if there are no warehouses
            if (!$warehouses) {
                $this->displayWarning($this->l('You must have at least one warehouse. See Stock/Warehouses'));
            }

            //get currencies list
            $currencies = Currency::getCurrencies(false, true, true);

            //get suppliers list
            $suppliers = array_unique(Supplier::getSuppliers(), SORT_REGULAR);

            //get languages list
            $languages = Language::getLanguages(true);

            $this->fields_form = array(
                'legend' => array(
                    'title' => $this->l('Order information'),
                    'icon' => 'icon-pencil'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Reference'),
                        'name' => 'reference',
                        'required' => true,
                        'hint' => $this->l('The reference number for your order.'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Supplier'),
                        'name' => 'id_supplier',
                        'required' => true,
                        'options' => array(
                            'query' => $suppliers,
                            'id' => 'id_supplier',
                            'name' => 'name'
                        ),
                        'hint' => array(
                            $this->l('Select the supplier you\'ll be purchasing from.'),
                            $this->l('Warning: All products already added to the order will be removed.')
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Warehouse'),
                        'name' => 'id_warehouse',
                        'required' => true,
                        'options' => array(
                            'query' => $warehouses,
                            'id' => 'id_warehouse',
                            'name' => 'name'
                        ),
                        'hint' => $this->l('Which warehouse will the order be sent to?'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Currency'),
                        'name' => 'id_currency',
                        'required' => true,
                        'options' => array(
                            'query' => $currencies,
                            'id' => 'id_currency',
                            'name' => 'name'
                        ),
                        'hint' => array(
                            $this->l('The currency of the order.'),
                            $this->l('Warning: All products already added to the order will be removed.')
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Order Language'),
                        'name' => 'id_lang',
                        'required' => true,
                        'options' => array(
                            'query' => $languages,
                            'id' => 'id_lang',
                            'name' => 'name'
                        ),
                        'hint' => $this->l('The language of the order.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Global discount percentage'),
                        'name' => 'discount_rate',
                        'required' => false,
                        'hint' => $this->l('This is the global discount percentage for the order.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Automatically load products'),
                        'name' => 'load_products',
                        'required' => false,
                        'hint' => array(
                            $this->l('This will reset the order.'),
                            $this->l('If a value specified, each of your current product (from the selected supplier and warehouse) with a quantity lower than or equal to this value will be loaded. This means that PrestaShop will pre-fill this order with the products that are low on quantity.'),
                        ),
                    ),
                ),
                'submit' => (!$update ? array('title' => $this->l('Save order')) : array()),
                'buttons' => (!$update ?
                    array(
                        'save-and-stay' => array(
                            'title' => $this->l('Save order and stay'),
                            'name' => 'submitAddsupply_orderAndStay',
                            'type' => 'submit',
                            'class' => 'btn btn-default pull-right',
                            'icon' => 'process-icon-save'
                        )
                    ) : array())
            );

            if (Tools::isSubmit('mod') && Tools::getValue('mod') === 'template' || $this->object->is_template) {
                $this->fields_form['input'][] = array(
                    'type' => 'hidden',
                    'name' => 'is_template'
                );

                $this->fields_form['input'][] = array(
                    'type' => 'hidden',
                    'name' => 'date_delivery_expected',
                );
            } else {
                $this->fields_form['input'][] = array(
                    'type' => 'date',
                    'label' => $this->l('Expected delivery date'),
                    'name' => 'date_delivery_expected',
                    'required' => true,
                    'desc' => $this->l('The expected delivery date for this order is...'),
                );
            }

            //specific discount display
            if (isset($this->object->discount_rate)) {
                $this->object->discount_rate = Tools::ps_round($this->object->discount_rate, 4);
            }

            //specific date display

            if (isset($this->object->date_delivery_expected)) {
                $date = explode(' ', $this->object->date_delivery_expected);
                if ($date) {
                    $this->object->date_delivery_expected = $date[0];
                }
            }

            $this->displayInformation(
                $this->l('If you wish to order products, they have to be available for the specified supplier/warehouse.')
                .' '.
                $this->l('See Catalog/Products/[Your Product]/Suppliers & Warehouses.')
                .'<br />'.
                $this->l('Changing the currency or the supplier will reset the order.')
                .'<br />'
                .'<br />'.
                $this->l('Please note that you can only order from one supplier at a time.')
            );
            return parent::renderForm();
        }
    }

    /**
     * AdminController::getList() override
     * @see AdminController::getList()
     *
     * @param int         $id_lang
     * @param string|null $order_by
     * @param string|null $order_way
     * @param int         $start
     * @param int|null    $limit
     * @param int|bool    $id_lang_shop
     *
     * @throws PrestaShopException
     */
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        if (Tools::isSubmit('csv_orders') || Tools::isSubmit('csv_orders_details') || Tools::isSubmit('csv_order_details')) {
            $limit = false;
        }

        // defines button specific for non-template supply orders
        if (!$this->is_template_list && $this->display != 'details') {
            // adds export csv buttons
            $this->toolbar_btn['export-csv-orders'] = array(
                'short' => 'Export Orders',
                'href' => $this->context->link->getAdminLink('AdminSupplyOrders').'&csv_orders&id_warehouse='.$this->getCurrentWarehouse(),
                'desc' => $this->l('Export Orders (CSV)'),
                'class' => 'process-icon-export'
            );

            $this->toolbar_btn['export-csv-details'] = array(
                'short' => 'Export Orders Details',
                'href' => $this->context->link->getAdminLink('AdminSupplyOrders').'&csv_orders_details&id_warehouse='.$this->getCurrentWarehouse(),
                'desc' => $this->l('Export Orders Details (CSV)'),
                'class' => 'process-icon-export'
            );

            unset($this->toolbar_btn['new']);
            if ($this->tabAccess['add'] === '1') {
                $this->toolbar_btn['new'] = array(
                    'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                    'desc' => $this->l('Add New')
                );
            }
        }

        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        // adds colors depending on the receipt state
        if ($order_by == 'quantity_expected') {
            $nb_items = count($this->_list);
            for ($i = 0; $i < $nb_items; ++$i) {
                $item = &$this->_list[$i];
                if ($item['quantity_received'] == $item['quantity_expected']) {
                    $item['color'] = '#00bb35';
                } elseif ($item['quantity_received'] > $item['quantity_expected']) {
                    $item['color'] = '#fb0008';
                }
            }
        }

        // actions filters on supply orders list
        if ($this->table == 'supply_order') {
            $nb_items = count($this->_list);

            for ($i = 0; $i < $nb_items; $i++) {
                // if the current state doesn't allow order edit, skip the edit action
                if ($this->_list[$i]['editable'] == 0) {
                    $this->addRowActionSkipList('edit', $this->_list[$i]['id_supply_order']);
                }
                if ($this->_list[$i]['enclosed'] == 1 && $this->_list[$i]['receipt_state'] == 0) {
                    $this->addRowActionSkipList('changestate', $this->_list[$i]['id_supply_order']);
                }
                if (1 != $this->_list[$i]['pending_receipt']) {
                    $this->addRowActionSkipList('updatereceipt', $this->_list[$i]['id_supply_order']);
                }
            }
        }
    }

    /**
     * AdminController::renderList() override
     * @see AdminController::renderList()
     */
    public function renderList()
    {
        $this->displayInformation($this->l('This interface allows you to manage supply orders.').'<br />');
        $this->displayInformation($this->l('You can create pre-filled order templates, from which you can build actual orders much quicker.').'<br />');

        if (count($this->warehouses) <= 1) {
            $this->displayWarning($this->l('You must choose at least one warehouse before creating supply orders. For more information, see Stock/Warehouses.'));
        }

        // assigns warehouses
        $this->tpl_list_vars['warehouses'] = $this->warehouses;
        $this->tpl_list_vars['current_warehouse'] = $this->getCurrentWarehouse();
        $this->tpl_list_vars['filter_status'] = $this->getFilterStatus();

        // overrides query
        $this->_select = '
			s.name AS supplier,
			w.name AS warehouse,
			stl.name AS state,
			st.delivery_note,
			st.editable,
			st.enclosed,
			st.receipt_state,
			st.pending_receipt,
			st.color AS color,
			a.id_supply_order as id_export';

        $this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'supply_order_state_lang` stl ON
			(
				a.id_supply_order_state = stl.id_supply_order_state
				AND stl.id_lang = '.(int)$this->context->language->id.'
			)
			LEFT JOIN `'._DB_PREFIX_.'supply_order_state` st ON a.id_supply_order_state = st.id_supply_order_state
			LEFT JOIN `'._DB_PREFIX_.'supplier` s ON a.id_supplier = s.id_supplier
			LEFT JOIN `'._DB_PREFIX_.'warehouse` w ON (w.id_warehouse = a.id_warehouse)';

        $this->_where = ' AND a.is_template = 0';

        if ($this->getCurrentWarehouse() != -1) {
            $this->_where .= ' AND a.id_warehouse = '.$this->getCurrentWarehouse();
            self::$currentIndex .= '&id_warehouse='.(int)$this->getCurrentWarehouse();
        }

        if ($this->getFilterStatus() != 0) {
            $this->_where .= ' AND st.enclosed != 1';
            self::$currentIndex .= '&filter_status=on';
        }

        $this->list_id = 'orders';
        $this->_filterHaving = null;

        if (Tools::isSubmit('submitFilter'.$this->list_id)
            || $this->context->cookie->{'submitFilter'.$this->list_id} !== false
            || Tools::getValue($this->list_id.'Orderby')
            || Tools::getValue($this->list_id.'Orderway')) {
            $this->filter = true;
            parent::processFilter();
        }

        $first_list = parent::renderList();

        if (Tools::isSubmit('csv_orders') || Tools::isSubmit('csv_orders_details') || Tools::isSubmit('csv_order_details')) {
            if (count($this->_list) > 0) {
                $this->renderCSV();
                die;
            } else {
                $this->displayWarning($this->l('There is nothing to export as a CSV file.'));
            }
        }

        // second list : templates
        $second_list = null;
        $this->is_template_list = true;
        unset($this->tpl_list_vars['warehouses']);
        unset($this->tpl_list_vars['current_warehouse']);
        unset($this->tpl_list_vars['filter_status']);

        // unsets actions
        $this->actions = array();
        unset($this->toolbar_btn['export-csv-orders']);
        unset($this->toolbar_btn['export-csv-details']);
        // adds actions
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('createsupplyorder');
        $this->addRowAction('delete');
        // unsets some fields
        unset($this->fields_list['state'],
              $this->fields_list['date_upd'],
              $this->fields_list['id_pdf'],
              $this->fields_list['date_delivery_expected'],
              $this->fields_list['id_export']);

        // $this->fields_list['date_add']['align'] = 'left';

        // adds filter, to gets only templates
        unset($this->_where);
        $this->_where = ' AND a.is_template = 1';

        if ($this->getCurrentWarehouse() != -1) {
            $this->_where .= ' AND a.id_warehouse = '.$this->getCurrentWarehouse();
        }

        // re-defines toolbar & buttons
        $this->toolbar_title = $this->l('Stock: Supply order templates');
        $this->initToolbar();
        unset($this->toolbar_btn['new']);
        $this->toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&mod=template&token='.$this->token,
            'desc' => $this->l('Add new template'),
            'imgclass' => 'new_1',
            'class' => 'process-icon-new'
        );

        $this->list_id = 'templates';
        $this->_filterHaving = null;

        if (Tools::isSubmit('submitFilter'.$this->list_id)
            || $this->context->cookie->{'submitFilter'.$this->list_id} !== false
            || Tools::getValue($this->list_id.'Orderby')
            || Tools::getValue($this->list_id.'Orderway')) {
            $this->filter = true;
            parent::processFilter();
        }
        // inits list
        $second_list = parent::renderList();

        return $first_list.$second_list;
    }

    /**
     * Init the content of change state action
     */
    public function initChangeStateContent()
    {
        $id_supply_order = (int)Tools::getValue('id_supply_order', 0);

        if ($id_supply_order <= 0) {
            $this->errors[] = Tools::displayError('The specified supply order is not valid');
            return parent::initContent();
        }

        $supply_order = new SupplyOrder($id_supply_order);
        $supply_order_state = new SupplyOrderState($supply_order->id_supply_order_state);

        if (!Validate::isLoadedObject($supply_order) || !Validate::isLoadedObject($supply_order_state)) {
            $this->errors[] = Tools::displayError('The specified supply order is not valid');
            return parent::initContent();
        }

        // change the display type in order to add specific actions to
        $this->display = 'update_order_state';
        // overrides parent::initContent();
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        // given the current state, loads available states
        $states = SupplyOrderState::getSupplyOrderStates($supply_order->id_supply_order_state);

        // gets the state that are not allowed
        $allowed_states = array();
        foreach ($states as &$state) {
            $allowed_states[] = $state['id_supply_order_state'];
            $state['allowed'] = 1;
        }
        $not_allowed_states = SupplyOrderState::getStates($allowed_states);

        // generates the final list of states
        $index = count($allowed_states);
        foreach ($not_allowed_states as &$not_allowed_state) {
            $not_allowed_state['allowed'] = 0;
            $states[$index] = $not_allowed_state;
            ++$index;
        }

        // loads languages
        $this->getlanguages();

        // defines the fields of the form to display
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Supply order status'),
                'icon' => 'icon-pencil'
            ),
            'input' => array(),
            'submit' => array(
                'title' => $this->l('Save')
            )
        );

        $this->displayInformation($this->l('Be careful when changing status. Some of those changes cannot be canceled. '));

        // sets up the helper
        $helper = new HelperForm();
        $helper->submit_action = 'submitChangestate';
        $helper->currentIndex = self::$currentIndex;
        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->toolbar_scroll = false;
        $helper->token = $this->token;
        $helper->id = null; // no display standard hidden field in the form
        $helper->languages = $this->_languages;
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->title = sprintf($this->l('Stock: Change supply order status #%s'), $supply_order->reference);
        $helper->show_cancel_button = true;
        $helper->override_folder = 'supply_orders_change_state/';

        // assigns our content
        $helper->tpl_vars['show_change_state_form'] = true;
        $helper->tpl_vars['supply_order_state'] = $supply_order_state;
        $helper->tpl_vars['supply_order'] = $supply_order;
        $helper->tpl_vars['supply_order_states'] = $states;

        // generates the form to display
        $content = $helper->generateForm($this->fields_form);

        $this->context->smarty->assign(array(
            'content' => $content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    /**
     * Init the content of change state action
     */
    public function initUpdateSupplyOrderContent()
    {
        $this->addJqueryPlugin('autocomplete');

        // load supply order
        $id_supply_order = (int)Tools::getValue('id_supply_order', null);

        if ($id_supply_order != null) {
            $supply_order = new SupplyOrder($id_supply_order);

            $currency = new Currency($supply_order->id_currency);

            if (Validate::isLoadedObject($supply_order)) {
                // load products of this order
                $products = $supply_order->getEntries();
                $product_ids = array();

                if (isset($this->order_products_errors) && is_array($this->order_products_errors)) {
                    //for each product in error array, check if it is in products array, and remove it to conserve last user values
                    foreach ($this->order_products_errors as $pe) {
                        foreach ($products as $index_p => $p) {
                            if (($p['id_product'] == $pe['id_product']) && ($p['id_product_attribute'] == $pe['id_product_attribute'])) {
                                unset($products[$index_p]);
                            }
                        }
                    }

                    // then merge arrays
                    $products = array_merge($this->order_products_errors, $products);
                }

                foreach ($products as &$item) {
                    // calculate md5 checksum on each product for use in tpl
                    $item['checksum'] = md5(_COOKIE_KEY_.$item['id_product'].'_'.$item['id_product_attribute']);
                    $item['unit_price_te'] = Tools::ps_round($item['unit_price_te'], 2);

                    // add id to ids list
                    $product_ids[] = $item['id_product'].'_'.$item['id_product_attribute'];
                }

                $this->tpl_form_vars['products_list'] = $products;
                $this->tpl_form_vars['product_ids'] = implode($product_ids, '|');
                $this->tpl_form_vars['product_ids_to_delete'] = '';
                $this->tpl_form_vars['supplier_id'] = $supply_order->id_supplier;
                $this->tpl_form_vars['currency'] = $currency;
            }
        }

        $this->tpl_form_vars['content'] = $this->content;
        $this->tpl_form_vars['token'] = $this->token;
        $this->tpl_form_vars['show_product_management_form'] = true;

        // call parent initcontent to render standard form content
        parent::initContent();
    }

    /**
     * Inits the content of 'update_receipt' action
     * Called in initContent()
     * @see AdminSuppliersOrders::initContent()
     */
    public function initUpdateReceiptContent()
    {
        $id_supply_order = (int)Tools::getValue('id_supply_order', null);

        // if there is no order to fetch
        if (null == $id_supply_order) {
            return parent::initContent();
        }

        $supply_order = new SupplyOrder($id_supply_order);

        // if it's not a valid order
        if (!Validate::isLoadedObject($supply_order)) {
            return parent::initContent();
        }

        $this->initPageHeaderToolbar();

        // re-defines fields_list
        $this->fields_list = array(
            'supplier_reference' => array(
                'title' => $this->l('Supplier reference'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'reference' => array(
                'title' => $this->l('Reference'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'ean13' => array(
                'title' => $this->l('EAN-13 or JAN barcode'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'upc' => array(
                'title' => $this->l('UPC barcode'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'quantity_received_today' => array(
                'title' => $this->l('Quantity received today?'),
                'type' => 'editable',
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'hint' => $this->l('The quantity of supplies that you received today.'),
            ),
            'quantity_received' => array(
                'title' => $this->l('Quantity received'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'badge_danger' => true,
                'badge_success' => true,
                'hint' => $this->l('The quantity of supplies that you received so far (today and the days before, if it applies).'),
            ),
            'quantity_expected' => array(
                'title' => $this->l('Quantity expected'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ),
            'quantity_left' => array(
                'title' => $this->l('Quantity left'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'hint' => $this->l('The quantity of supplies left to receive for this order.'),
            )
        );

        // attributes override
        unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);
        $this->table = 'supply_order_detail';
        $this->identifier = 'id_supply_order_detail';
        $this->className = 'SupplyOrderDetail';
        $this->list_simple_header = false;
        $this->list_no_link = true;
        $this->colorOnBackground = true;
        $this->row_hover = false;
        $this->bulk_actions = array('Update' => array('text' => $this->l('Update selected'), 'confirm' => $this->l('Update selected items?')));
        $this->addRowAction('details');

        // sets toolbar title with order reference
        $this->toolbar_title = sprintf($this->l('Receipt of products for supply order #%s'), $supply_order->reference);

        $this->lang = false;
        $lang_id = (int)$this->context->language->id; //employee lang

        // gets values corresponding to fields_list
        $this->_select = '
			a.id_supply_order_detail as id,
			a.quantity_received as quantity_received,
			a.quantity_expected as quantity_expected,
			IF (a.quantity_expected < a.quantity_received, 0, a.quantity_expected - a.quantity_received) as quantity_left,
			IF (a.quantity_expected < a.quantity_received, 0, a.quantity_expected - a.quantity_received) as quantity_received_today,
			IF (a.quantity_expected = a.quantity_received, 1, 0) badge_success,
			IF (a.quantity_expected > a.quantity_received, 1, 0) badge_danger';

        $this->_where = 'AND a.`id_supply_order` = '.(int)$id_supply_order;

        $this->_group = 'GROUP BY a.id_supply_order_detail';

        // gets the list ordered by price desc, without limit
        $this->getList($lang_id, 'quantity_expected', 'DESC', 0, Tools::getValue('supply_order_pagination'), false);

        // defines action for POST
        $action = '&id_supply_order='.$id_supply_order.'&update_receipt=1';

        // unsets some buttons
        unset($this->toolbar_btn['export-csv-orders']);
        unset($this->toolbar_btn['export-csv-details']);
        unset($this->toolbar_btn['new']);

        $this->toolbar_btn['back'] = array(
            'desc' => $this->l('Back'),
            'href' => $this->context->link->getAdminLink('AdminSupplyOrders')
        );

        // renders list
        $helper = new HelperList();
        $this->setHelperDisplay($helper);
        $helper->actions = array('details');
        $helper->force_show_bulk_actions = true;
        $helper->override_folder = 'supply_orders_receipt_history/';
        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->list_id = 'supply_order_detail';

        $helper->ajax_params = array(
            'display_product_history' => 1,
        );

        $helper->currentIndex = self::$currentIndex.$action;

        // display these global order informations
        $this->displayInformation($this->l('This interface allows you to update the quantities of this ongoing order.').'<br />');
        $this->displayInformation($this->l('Be careful! Once you update, you cannot go back unless you add new negative stock movements.').'<br />');
        $this->displayInformation($this->l('A green line means that you\'ve received exactly the quantity you expected. A red line means that you\'ve received more than expected.').'<br />');

        // generates content
        $content = $helper->generateList($this->_list, $this->fields_list);

        // assigns var
        $this->context->smarty->assign(array(
            'content' => $content,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    /**
     * AdminController::initContent() override
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $this->warnings[md5('PS_ADVANCED_STOCK_MANAGEMENT')] =
                $this->l('You need to activate the Advanced Stock Management feature prior to using this feature.');
            return false;
        }
        // Manage the add stock form
        if (Tools::isSubmit('changestate')) {
            $this->initChangeStateContent();
        } elseif (Tools::isSubmit('update_receipt') && Tools::isSubmit('id_supply_order') && !Tools::isSubmit('detailssupply_order_detail')) {
            $this->initUpdateReceiptContent();
        } elseif (Tools::isSubmit('viewsupply_order') && Tools::isSubmit('id_supply_order')) {
            $this->action = 'view';
            $this->display = 'view';
            parent::initContent();
        } elseif (Tools::isSubmit('updatesupply_order')) {
            $this->initUpdateSupplyOrderContent();
        } else {
            if (Tools::isSubmit('detailssupply_order_detail')) {
                $this->action = 'details';
                $this->display = 'details';
            }
            parent::initContent();
        }
    }

    /**
     * Ths method manage associated products to the order when updating it
     */
    public function manageOrderProducts()
    {
        // load supply order
        $id_supply_order = (int)Tools::getValue('id_supply_order', null);
        $products_already_in_order = array();

        if ($id_supply_order != null) {
            $supply_order = new SupplyOrder($id_supply_order);

            if (Validate::isLoadedObject($supply_order)) {
                // tests if the supplier or currency have changed in the supply order
                $new_supplier_id = (int)Tools::getValue('id_supplier');
                $new_currency_id = (int)Tools::getValue('id_currency');

                if (($new_supplier_id != $supply_order->id_supplier) ||
                    ($new_currency_id != $supply_order->id_currency)) {
                    // resets all products in this order
                    $supply_order->resetProducts();
                } else {
                    $products_already_in_order = $supply_order->getEntries();
                    $currency = new Currency($supply_order->id_ref_currency);

                    // gets all product ids to manage
                    $product_ids_str = Tools::getValue('product_ids', null);
                    $product_ids = explode('|', $product_ids_str);
                    $product_ids_to_delete_str = Tools::getValue('product_ids_to_delete', null);
                    $product_ids_to_delete = array_unique(explode('|', $product_ids_to_delete_str));

                    //delete products that are not managed anymore
                    foreach ($products_already_in_order as $paio) {
                        $product_ok = false;

                        foreach ($product_ids_to_delete as $id) {
                            $id_check = $paio['id_product'].'_'.$paio['id_product_attribute'];
                            if ($id_check == $id) {
                                $product_ok = true;
                            }
                        }

                        if ($product_ok === true) {
                            $entry = new SupplyOrderDetail($paio['id_supply_order_detail']);
                            $entry->delete();
                        }
                    }

                    // manage each product
                    foreach ($product_ids as $id) {
                        $errors = array();

                        // check if a checksum is available for this product and test it
                        $check = Tools::getValue('input_check_'.$id, '');
                        $check_valid = md5(_COOKIE_KEY_.$id);

                        if ($check_valid != $check) {
                            continue;
                        }

                        $pos = strpos($id, '_');
                        if ($pos === false) {
                            continue;
                        }

                        // Load / Create supply order detail
                        $entry = new SupplyOrderDetail();
                        $id_supply_order_detail = (int)Tools::getValue('input_id_'.$id, 0);
                        if ($id_supply_order_detail > 0) {
                            $existing_entry = new SupplyOrderDetail($id_supply_order_detail);
                            if (Validate::isLoadedObject($supply_order)) {
                                $entry = &$existing_entry;
                            }
                        }

                        // get product informations
                        $entry->id_product = substr($id, 0, $pos);
                        $entry->id_product_attribute = substr($id, $pos + 1);
                        $entry->unit_price_te = (float)str_replace(array(' ', ','), array('', '.'), Tools::getValue('input_unit_price_te_'.$id, 0));
                        $entry->quantity_expected = (int)str_replace(array(' ', ','), array('', '.'), Tools::getValue('input_quantity_expected_'.$id, 0));
                        $entry->discount_rate = (float)str_replace(array(' ', ','), array('', '.'), Tools::getValue('input_discount_rate_'.$id, 0));
                        $entry->tax_rate = (float)str_replace(array(' ', ','), array('', '.'), Tools::getValue('input_tax_rate_'.$id, 0));
                        $entry->reference = Tools::getValue('input_reference_'.$id, '');
                        $entry->supplier_reference = Tools::getValue('input_supplier_reference_'.$id, '');
                        $entry->ean13 = Tools::getValue('input_ean13_'.$id, '');
                        $entry->upc = Tools::getValue('input_upc_'.$id, '');

                        //get the product name in the order language
                        $entry->name = Product::getProductName($entry->id_product, $entry->id_product_attribute, $supply_order->id_lang);

                        if (empty($entry->name)) {
                            $entry->name = '';
                        }

                        if ($entry->supplier_reference == null) {
                            $entry->supplier_reference = '';
                        }

                        $entry->exchange_rate = $currency->conversion_rate;
                        $entry->id_currency = $currency->id;
                        $entry->id_supply_order = $supply_order->id;

                        $errors = $entry->validateController();

                        //get the product name displayed in the backoffice according to the employee language
                        $entry->name_displayed = Tools::getValue('input_name_displayed_'.$id, '');

                        // if there is a problem, handle error for the current product
                        if (count($errors) > 0) {
                            // add the product to error array => display again product line
                            $this->order_products_errors[] = array(
                                'id_product' =>    $entry->id_product,
                                'id_product_attribute' => $entry->id_product_attribute,
                                'unit_price_te' =>    $entry->unit_price_te,
                                'quantity_expected' => $entry->quantity_expected,
                                'discount_rate' =>    $entry->discount_rate,
                                'tax_rate' => $entry->tax_rate,
                                'name' => $entry->name,
                                'name_displayed' => $entry->name_displayed,
                                'reference' => $entry->reference,
                                'supplier_reference' => $entry->supplier_reference,
                                'ean13' => $entry->ean13,
                                'upc' => $entry->upc,
                            );

                            $error_str = '<ul>';
                            foreach ($errors as $e) {
                                $error_str .= '<li>'.sprintf($this->l('Field: %s'), $e).'</li>';
                            }
                            $error_str .= '</ul>';

                            $this->errors[] = sprintf(Tools::displayError('Please verify the product information for "%s":'), $entry->name).' '
                                .$error_str;
                        } else {
                            $entry->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * AdminController::postProcess() override
     * @see AdminController::postProcess()
     */
    public function postProcess()
    {
        $this->is_editing_order = false;

        // Checks access
        if (Tools::isSubmit('submitAddsupply_order') && !($this->tabAccess['add'] === '1')) {
            $this->errors[] = Tools::displayError('You do not have permission to add a supply order.');
        }
        if (Tools::isSubmit('submitBulkUpdatesupply_order_detail') && !($this->tabAccess['edit'] === '1')) {
            $this->errors[] = Tools::displayError('You do not have permission to edit an order.');
        }

        // Trick to use both Supply Order as template and actual orders
        if (Tools::isSubmit('is_template')) {
            $_GET['mod'] = 'template';
        }

        // checks if supply order reference is unique
        if (Tools::isSubmit('reference')) {
            // gets the reference
            $ref = pSQL(Tools::getValue('reference'));

            if (Tools::getValue('id_supply_order') != 0 && SupplyOrder::getReferenceById((int)Tools::getValue('id_supply_order')) != $ref) {
                if ((int)SupplyOrder::exists($ref) != 0) {
                    $this->errors[] = Tools::displayError('The reference has to be unique.');
                }
            } elseif (Tools::getValue('id_supply_order') == 0 && (int)SupplyOrder::exists($ref) != 0) {
                $this->errors[] = Tools::displayError('The reference has to be unique.');
            }
        }

        if ($this->errors) {
            return;
        }

        // Global checks when add / update a supply order
        if (Tools::isSubmit('submitAddsupply_order') || Tools::isSubmit('submitAddsupply_orderAndStay')) {
            $this->action = 'save';
            $this->is_editing_order = true;

            // get supplier ID
            $id_supplier = (int)Tools::getValue('id_supplier', 0);
            if ($id_supplier <= 0 || !Supplier::supplierExists($id_supplier)) {
                $this->errors[] = Tools::displayError('The selected supplier is not valid.');
            }

            // get warehouse id
            $id_warehouse = (int)Tools::getValue('id_warehouse', 0);
            if ($id_warehouse <= 0 || !Warehouse::exists($id_warehouse)) {
                $this->errors[] = Tools::displayError('The selected warehouse is not valid.');
            }

            // get currency id
            $id_currency = (int)Tools::getValue('id_currency', 0);
            if ($id_currency <= 0 || (!($result = Currency::getCurrency($id_currency)) || empty($result))) {
                $this->errors[] = Tools::displayError('The selected currency is not valid.');
            }

            // get delivery date
            if (Tools::getValue('mod') != 'template' && strtotime(Tools::getValue('date_delivery_expected')) <= strtotime('-1 day')) {
                $this->errors[] = Tools::displayError('The specified date cannot be in the past.');
            }

            // gets threshold
            $quantity_threshold = Tools::getValue('load_products');

            if (is_numeric($quantity_threshold)) {
                $quantity_threshold = (int)$quantity_threshold;
            } else {
                $quantity_threshold = null;
            }

            if (!count($this->errors)) {
                // forces date for templates
                if (Tools::isSubmit('is_template') && !Tools::getValue('date_delivery_expected')) {
                    $_POST['date_delivery_expected'] = date('Y-m-d h:i:s');
                }

                // specify initial state
                $_POST['id_supply_order_state'] = 1; //defaut creation state

                // specify global reference currency
                $_POST['id_ref_currency'] = Currency::getDefaultCurrency()->id;

                // specify supplier name
                $_POST['supplier_name'] = Supplier::getNameById($id_supplier);

                //specific discount check
                $_POST['discount_rate'] = (float)str_replace(array(' ', ','), array('', '.'), Tools::getValue('discount_rate', 0));
            }

            // manage each associated product
            $this->manageOrderProducts();

            // if the threshold is defined and we are saving the order
            if (Tools::isSubmit('submitAddsupply_order') && Validate::isInt($quantity_threshold)) {
                $this->loadProducts((int)$quantity_threshold);
            }
        }

        // Manage state change
        if (Tools::isSubmit('submitChangestate')
            && Tools::isSubmit('id_supply_order')
            && Tools::isSubmit('id_supply_order_state')) {
            if ($this->tabAccess['edit'] != '1') {
                $this->errors[] = Tools::displayError('You do not have permission to change the order status.');
            }

            // get state ID
            $id_state = (int)Tools::getValue('id_supply_order_state', 0);
            if ($id_state <= 0) {
                $this->errors[] = Tools::displayError('The selected supply order status is not valid.');
            }

            // get supply order ID
            $id_supply_order = (int)Tools::getValue('id_supply_order', 0);
            if ($id_supply_order <= 0) {
                $this->errors[] = Tools::displayError('The supply order ID is not valid.');
            }

            if (!count($this->errors)) {
                // try to load supply order
                $supply_order = new SupplyOrder($id_supply_order);

                if (Validate::isLoadedObject($supply_order)) {
                    // get valid available possible states for this order
                    $states = SupplyOrderState::getSupplyOrderStates($supply_order->id_supply_order_state);

                    foreach ($states as $state) {
                        // if state is valid, change it in the order
                        if ($id_state == $state['id_supply_order_state']) {
                            $new_state = new SupplyOrderState($id_state);
                            $old_state = new SupplyOrderState($supply_order->id_supply_order_state);

                            // special case of validate state - check if there are products in the order and the required state is not an enclosed state
                            if ($supply_order->isEditable() && !$supply_order->hasEntries() && !$new_state->enclosed) {
                                $this->errors[] = Tools::displayError('It is not possible to change the status of this order because you did not order any products.');
                            }

                            if (!count($this->errors)) {
                                $supply_order->id_supply_order_state = $state['id_supply_order_state'];
                                if ($supply_order->save()) {
                                    if ($new_state->pending_receipt) {
                                        $supply_order_details = $supply_order->getEntries();
                                        foreach ($supply_order_details as $supply_order_detail) {
                                            $is_present = Stock::productIsPresentInStock($supply_order_detail['id_product'], $supply_order_detail['id_product_attribute'], $supply_order->id_warehouse);
                                            if (!$is_present) {
                                                $stock = new Stock();

                                                $stock_params = array(
                                                    'id_product_attribute' => $supply_order_detail['id_product_attribute'],
                                                    'id_product' => $supply_order_detail['id_product'],
                                                    'physical_quantity' => 0,
                                                    'price_te' => $supply_order_detail['price_te'],
                                                    'usable_quantity' => 0,
                                                    'id_warehouse' => $supply_order->id_warehouse
                                                );

                                                // saves stock in warehouse
                                                $stock->hydrate($stock_params);
                                                $stock->add();
                                            }
                                        }
                                    }

                                    // if pending_receipt,
                                    // or if the order is being canceled,
                                    // or if the order is received completely
                                    // synchronizes StockAvailable
                                    if (($new_state->pending_receipt && !$new_state->receipt_state) ||
                                        (($old_state->receipt_state || $old_state->pending_receipt) && $new_state->enclosed && !$new_state->receipt_state) ||
                                        ($new_state->receipt_state && $new_state->enclosed)) {
                                        $supply_order_details = $supply_order->getEntries();
                                        $products_done = array();
                                        foreach ($supply_order_details as $supply_order_detail) {
                                            if (!in_array($supply_order_detail['id_product'], $products_done)) {
                                                StockAvailable::synchronize($supply_order_detail['id_product']);
                                                $products_done[] = $supply_order_detail['id_product'];
                                            }
                                        }
                                    }

                                    $token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
                                    $redirect = self::$currentIndex.'&token='.$token;
                                    $this->redirect_after = $redirect.'&conf=5';
                                }
                            }
                        }
                    }
                } else {
                    $this->errors[] = Tools::displayError('The selected supplier is not valid.');
                }
            }
        }

        // updates receipt
        if (Tools::isSubmit('submitBulkUpdatesupply_order_detail') && Tools::isSubmit('id_supply_order')) {
            $this->postProcessUpdateReceipt();
        }

        // use template to create a supply order
        if (Tools::isSubmit('create_supply_order') && Tools::isSubmit('id_supply_order')) {
            $this->postProcessCopyFromTemplate();
        }

        if ((!count($this->errors) && $this->is_editing_order) || !$this->is_editing_order) {
            parent::postProcess();
        }
    }

    /**
     * Exports CSV
     */
    protected function renderCSV()
    {
        // exports orders
        if (Tools::isSubmit('csv_orders')) {
            $ids = array();
            foreach ($this->_list as $entry) {
                $ids[] = $entry['id_supply_order'];
            }

            if (count($ids) <= 0) {
                return;
            }

            $id_lang = Context::getContext()->language->id;
            $orders = new PrestaShopCollection('SupplyOrder', $id_lang);
            $orders->where('is_template', '=', false);
            $orders->where('id_supply_order', 'in', $ids);
            $id_warehouse = $this->getCurrentWarehouse();
            if ($id_warehouse != -1) {
                $orders->where('id_warehouse', '=', $id_warehouse);
            }
            $orders->getAll();
            $csv = new CSV($orders, $this->l('supply_orders'));
            $csv->export();
        }
        // exports details for all orders
        elseif (Tools::isSubmit('csv_orders_details')) {
            // header
            header('Content-type: text/csv');
            header('Content-Type: application/force-download; charset=UTF-8');
            header('Cache-Control: no-store, no-cache');
            header('Content-disposition: attachment; filename="'.$this->l('supply_orders_details').'.csv"');

            // echoes details
            $ids = array();
            foreach ($this->_list as $entry) {
                $ids[] = $entry['id_supply_order'];
            }

            if (count($ids) <= 0) {
                return;
            }

            // for each supply order
            $keys = array('id_product', 'id_product_attribute', 'reference', 'supplier_reference', 'ean13', 'upc', 'name',
                          'unit_price_te', 'quantity_expected', 'quantity_received', 'price_te', 'discount_rate', 'discount_value_te',
                          'price_with_discount_te', 'tax_rate', 'tax_value', 'price_ti', 'tax_value_with_order_discount',
                          'price_with_order_discount_te', 'id_supply_order');
            echo sprintf("%s\n", implode(';', array_map(array('CSVCore', 'wrap'), $keys)));

            // overrides keys (in order to add FORMAT calls)
            $keys = array('sod.id_product', 'sod.id_product_attribute', 'sod.reference', 'sod.supplier_reference', 'sod.ean13',
                          'sod.upc', 'sod.name',
                          'FORMAT(sod.unit_price_te, 2)', 'sod.quantity_expected', 'sod.quantity_received', 'FORMAT(sod.price_te, 2)',
                          'FORMAT(sod.discount_rate, 2)', 'FORMAT(sod.discount_value_te, 2)',
                          'FORMAT(sod.price_with_discount_te, 2)', 'FORMAT(sod.tax_rate, 2)', 'FORMAT(sod.tax_value, 2)',
                          'FORMAT(sod.price_ti, 2)', 'FORMAT(sod.tax_value_with_order_discount, 2)',
                          'FORMAT(sod.price_with_order_discount_te, 2)', 'sod.id_supply_order');
            foreach ($ids as $id) {
                $query = new DbQuery();
                $query->select(implode(', ', $keys));
                $query->from('supply_order_detail', 'sod');
                $query->leftJoin('supply_order', 'so', 'so.id_supply_order = sod.id_supply_order');
                $id_warehouse = $this->getCurrentWarehouse();
                if ($id_warehouse != -1) {
                    $query->where('so.id_warehouse = '.(int)$id_warehouse);
                }
                $query->where('sod.id_supply_order = '.(int)$id);
                $query->orderBy('sod.id_supply_order_detail DESC');
                $resource = Db::getInstance()->query($query);
                // gets details
                while ($row = Db::getInstance()->nextRow($resource)) {
                    echo sprintf("%s\n", implode(';', array_map(array('CSVCore', 'wrap'), $row)));
                }
            }
        }
        // exports details for the given order
        elseif (Tools::isSubmit('csv_order_details') && Tools::getValue('id_supply_order')) {
            $supply_order = new SupplyOrder((int)Tools::getValue('id_supply_order'));
            if (Validate::isLoadedObject($supply_order)) {
                $details = $supply_order->getEntriesCollection();
                $details->getAll();
                $csv = new CSV($details, $this->l('supply_order').'_'.$supply_order->reference.'_details');
                $csv->export();
            }
        }
    }

    /**
     * Helper function for AdminSupplyOrdersController::postProcess()
     *
     * @see AdminSupplyOrdersController::postProcess()
     */
    protected function postProcessUpdateReceipt()
    {
        // gets all box selected
        $rows = Tools::getValue('supply_order_detailBox');
        if (!$rows) {
            $this->errors[] = Tools::displayError('You did not select any products to update.');
            return;
        }

        // final array with id_supply_order_detail and value to update
        $to_update = array();
        // gets quantity for each id_order_detail
        foreach ($rows as $row) {
            if (Tools::getValue('quantity_received_today_'.$row)) {
                $to_update[$row] = (int)Tools::getValue('quantity_received_today_'.$row);
            }
        }

        // checks if there is something to update
        if (!count($to_update)) {
            $this->errors[] = Tools::displayError('You did not select any products to update.');
            return;
        }

        $supply_order = new SupplyOrder((int)Tools::getValue('id_supply_order'));

        foreach ($to_update as $id_supply_order_detail => $quantity) {
            $supply_order_detail = new SupplyOrderDetail($id_supply_order_detail);

            if (Validate::isLoadedObject($supply_order_detail) && Validate::isLoadedObject($supply_order)) {
                // checks if quantity is valid
                // It's possible to receive more quantity than expected in case of a shipping error from the supplier
                if (!Validate::isInt($quantity) || $quantity <= 0) {
                    $this->errors[] = sprintf(Tools::displayError('Quantity (%d) for product #%d is not valid'),
                        (int)$quantity, (int)$id_supply_order_detail);
                } else {
                    // everything is valid :  updates

                    // creates the history
                    $supplier_receipt_history = new SupplyOrderReceiptHistory();
                    $supplier_receipt_history->id_supply_order_detail = (int)$id_supply_order_detail;
                    $supplier_receipt_history->id_employee = (int)$this->context->employee->id;
                    $supplier_receipt_history->employee_firstname = pSQL($this->context->employee->firstname);
                    $supplier_receipt_history->employee_lastname = pSQL($this->context->employee->lastname);
                    $supplier_receipt_history->id_supply_order_state = (int)$supply_order->id_supply_order_state;
                    $supplier_receipt_history->quantity = (int)$quantity;

                    // updates quantity received
                    $supply_order_detail->quantity_received += (int)$quantity;

                    // if current state is "Pending receipt", then we sets it to "Order received in part"
                    if (3 == $supply_order->id_supply_order_state) {
                        $supply_order->id_supply_order_state = 4;
                    }

                    // Adds to stock
                    $warehouse = new Warehouse($supply_order->id_warehouse);
                    if (!Validate::isLoadedObject($warehouse)) {
                        $this->errors[] = Tools::displayError('The warehouse could not be loaded.');
                        return;
                    }

                    $price = $supply_order_detail->unit_price_te;
                    // converts the unit price to the warehouse currency if needed
                    if ($supply_order->id_currency != $warehouse->id_currency) {
                        // first, converts the price to the default currency
                        $price_converted_to_default_currency = Tools::convertPrice($supply_order_detail->unit_price_te,
                            $supply_order->id_currency, false);

                        // then, converts the newly calculated pri-ce from the default currency to the needed currency
                        $price = Tools::ps_round(Tools::convertPrice($price_converted_to_default_currency,
                            $warehouse->id_currency, true), 6);
                    }

                    $manager = StockManagerFactory::getManager();
                    $res = $manager->addProduct($supply_order_detail->id_product,
                        $supply_order_detail->id_product_attribute,    $warehouse, (int)$quantity,
                        Configuration::get('PS_STOCK_MVT_SUPPLY_ORDER'), $price, true, $supply_order->id);

                    $location = Warehouse::getProductLocation($supply_order_detail->id_product,
                        $supply_order_detail->id_product_attribute, $warehouse->id);

                    $res = Warehouse::setProductlocation($supply_order_detail->id_product,
                        $supply_order_detail->id_product_attribute, $warehouse->id, $location ? $location : '');

                    if ($res) {
                        $supplier_receipt_history->add();
                        $supply_order_detail->save();
                        StockAvailable::synchronize($supply_order_detail->id_product);
                    } else {
                        $this->errors[] = Tools::displayError('Something went wrong when setting warehouse on product record');
                    }
                }
            }
        }

        $supply_order->id_supply_order_state = ($supply_order->id_supply_order_state == 4 && $supply_order->getAllPendingQuantity() > 0) ? 4 : 5;
        $supply_order->save();

        if (!count($this->errors)) {
            // display confirm message
            $token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
            $redirect = self::$currentIndex.'&token='.$token;
            $this->redirect_after = $redirect.'&conf=4';
        }
    }

    /**
     * Display state action link
     * @param string $token the token to add to the link
     * @param int $id the identifier to add to the link
     * @return string
     */
    public function displayUpdateReceiptLink($token = null, $id)
    {
        if (!array_key_exists('Receipt', self::$cache_lang)) {
            self::$cache_lang['Receipt'] = $this->l('Update ongoing receipt of products');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
                '&'.$this->identifier.'='.$id.
                '&update_receipt&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['Receipt'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_supply_order_receipt.tpl');
    }

    /**
     * Display receipt action link
     * @param string $token the token to add to the link
     * @param int $id the identifier to add to the link
     * @return string
     */
    public function displayChangestateLink($token = null, $id)
    {
        if (!array_key_exists('State', self::$cache_lang)) {
            self::$cache_lang['State'] = $this->l('Change status');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
                '&'.$this->identifier.'='.$id.
                '&changestate&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['State'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_supply_order_change_state.tpl');
    }

    /**
     * Display state action link
     * @param string $token the token to add to the link
     * @param int $id the identifier to add to the link
     * @return string
     */
    public function displayCreateSupplyOrderLink($token = null, $id)
    {
        if (!array_key_exists('CreateSupplyOrder', self::$cache_lang)) {
            self::$cache_lang['CreateSupplyOrder'] = $this->l('Use this template to create a supply order');
        }

        if (!array_key_exists('CreateSupplyOrderConfirm', self::$cache_lang)) {
            self::$cache_lang['CreateSupplyOrderConfirm'] = $this->l('Are you sure you want to use this template?');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
                '&'.$this->identifier.'='.$id.
                '&create_supply_order&token='.($token != null ? $token : $this->token),
            'confirm' => self::$cache_lang['CreateSupplyOrderConfirm'],
            'action' => self::$cache_lang['CreateSupplyOrder'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_supply_order_create_from_template.tpl');
    }

    public function renderDetails()
    {
        // tests if an id is submit
        if (Tools::isSubmit('id_supply_order') && !Tools::isSubmit('display_product_history')) {
            // overrides attributes
            $this->identifier = 'id_supply_order_history';
            $this->table = 'supply_order_history';
            $this->lang = false;
            $this->actions = array();
            $this->toolbar_btn = array();
            $this->list_simple_header = true;
            // gets current lang id
            $lang_id = (int)$this->context->language->id;
            // gets supply order id
            $id_supply_order = (int)Tools::getValue('id_supply_order');

            // creates new fields_list
            $this->fields_list = array(
                'history_date' => array(
                    'title' => $this->l('Last update'),
                    'align' => 'left',
                    'type' => 'datetime',
                    'havingFilter' => true
                ),
                'history_employee' => array(
                    'title' => $this->l('Employee'),
                    'align' => 'left',
                    'havingFilter' => true
                ),
                'history_state_name' => array(
                    'title' => $this->l('Status'),
                    'align' => 'left',
                    'color' => 'color',
                    'havingFilter' => true
                ),
            );
            // loads history of the given order
            unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);
            $this->_select = '
				a.`date_add` as history_date,
				CONCAT(a.`employee_lastname`, \' \', a.`employee_firstname`) as history_employee,
				sosl.`name` as history_state_name,
				sos.`color` as color';

            $this->_join = '
				LEFT JOIN `'._DB_PREFIX_.'supply_order_state` sos ON (a.`id_state` = sos.`id_supply_order_state`)
				LEFT JOIN `'._DB_PREFIX_.'supply_order_state_lang` sosl ON
				(
					a.`id_state` = sosl.`id_supply_order_state`
					AND sosl.`id_lang` = '.(int)$lang_id.'
				)';

            $this->_where = 'AND a.`id_supply_order` = '.(int)$id_supply_order;
            $this->_orderBy = 'a.date_add';
            $this->_orderWay = 'DESC';

            return parent::renderList();
        } elseif (Tools::isSubmit('id_supply_order') && Tools::isSubmit('display_product_history')) {
            $this->identifier = 'id_supply_order_receipt_history';
            $this->table = 'supply_order_receipt_history';
            $this->actions = array();
            $this->toolbar_btn = array();
            $this->list_simple_header = true;
            $this->lang = false;
            $lang_id = (int)$this->context->language->id;
            $id_supply_order_detail = (int)Tools::getValue('id_supply_order');

            unset($this->fields_list);
            $this->fields_list = array(
                'date_add' => array(
                    'title' => $this->l('Last update'),
                    'align' => 'left',
                    'type' => 'datetime',
                    'havingFilter' => true
                ),
                'employee' => array(
                    'title' => $this->l('Employee'),
                    'align' => 'left',
                    'havingFilter' => true
                ),
                'quantity' => array(
                    'title' => $this->l('Quantity received'),
                    'align' => 'left',
                    'havingFilter' => true
                ),
            );

            // loads history of the given order
            unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);
            $this->_select = 'CONCAT(a.`employee_lastname`, \' \', a.`employee_firstname`) as employee';
            $this->_where = 'AND a.`id_supply_order_detail` = '.(int)$id_supply_order_detail;
            $this->_orderBy = 'a.date_add';
            $this->_orderWay = 'DESC';

            return parent::renderList();
        }
    }

    /**
     * method call when ajax request is made for search product to add to the order
     * @TODO - Update this method to retreive the reference, ean13, upc corresponding to a product attribute
     */
    public function ajaxProcessSearchProduct()
    {
        // Get the search pattern
        $pattern = pSQL(Tools::getValue('q', false));

        if (!$pattern || $pattern == '' || strlen($pattern) < 1) {
            die();
        }

        // get supplier id
        $id_supplier = (int)Tools::getValue('id_supplier', false);

        // gets the currency
        $id_currency = (int)Tools::getValue('id_currency', false);

        // get lang from context
        $id_lang = (int)Context::getContext()->language->id;

        $query = new DbQuery();
        $query->select('
			CONCAT(p.id_product, \'_\', IFNULL(pa.id_product_attribute, \'0\')) as id,
			ps.product_supplier_reference as supplier_reference,
			IFNULL(pa.reference, IFNULL(p.reference, \'\')) as reference,
			IFNULL(pa.ean13, IFNULL(p.ean13, \'\')) as ean13,
			IFNULL(pa.upc, IFNULL(p.upc, \'\')) as upc,
			md5(CONCAT(\''._COOKIE_KEY_.'\', p.id_product, \'_\', IFNULL(pa.id_product_attribute, \'0\'))) as checksum,
			IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(DISTINCT agl.name, \' - \', al.name order by agl.name SEPARATOR \', \')), pl.name) as name
		');
        $query->from('product', 'p');
        $query->innerJoin('product_lang', 'pl', 'pl.id_product = p.id_product AND pl.id_lang = '.$id_lang);
        $query->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product');
        $query->leftJoin('product_attribute_combination', 'pac', 'pac.id_product_attribute = pa.id_product_attribute');
        $query->leftJoin('attribute', 'atr', 'atr.id_attribute = pac.id_attribute');
        $query->leftJoin('attribute_lang', 'al', 'al.id_attribute = atr.id_attribute AND al.id_lang = '.$id_lang);
        $query->leftJoin('attribute_group_lang', 'agl', 'agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$id_lang);
        $query->leftJoin('product_supplier', 'ps', 'ps.id_product = p.id_product AND ps.id_product_attribute = IFNULL(pa.id_product_attribute, 0)');
        $query->where('(pl.name LIKE \'%'.$pattern.'%\' OR p.reference LIKE \'%'.$pattern.'%\' OR ps.product_supplier_reference LIKE \'%'.$pattern.'%\')');
        $query->where('NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'product_download` pd WHERE (pd.id_product = p.id_product))');
        $query->where('p.is_virtual = 0 AND p.cache_is_pack = 0');

        if ($id_supplier) {
            $query->where('ps.id_supplier = '.$id_supplier.' OR p.id_supplier = '.$id_supplier);
        }

        $query->groupBy('p.id_product, pa.id_product_attribute');
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        foreach ($items as &$item) {
            $ids = explode('_', $item['id']);
            $prices = ProductSupplier::getProductSupplierPrice($ids[0], $ids[1], $id_supplier, true);
            if (count($prices)) {
                $item['unit_price_te'] = Tools::convertPriceFull($prices['product_supplier_price_te'], new Currency((int)$prices['id_currency']),
                    new Currency($id_currency)
                );
            }
        }
        if ($items) {
            die(Tools::jsonEncode($items));
        }

        die(1);
    }

    /**
     * @see AdminController::renderView()
     */
    public function renderView()
    {
        $this->show_toolbar = true;
        $this->toolbar_scroll = false;
        $this->table = 'supply_order_detail';
        $this->identifier = 'id_supply_order_detail';
        $this->className = 'SupplyOrderDetail';
        $this->colorOnBackground = false;
        $this->lang = false;
        $this->list_simple_header = true;
        $this->list_no_link = true;

        // gets the id supplier to view
        $id_supply_order = (int)Tools::getValue('id_supply_order');

        // gets global order information
        $supply_order = new SupplyOrder((int)$id_supply_order);

        if (Validate::isLoadedObject($supply_order)) {
            if (!$supply_order->is_template) {
                $this->displayInformation($this->l('This interface allows you to display detailed information about your order.').'<br />');
            } else {
                $this->displayInformation($this->l('This interface allows you to display detailed information about your order template.').'<br />');
            }

            $lang_id = (int)$supply_order->id_lang;

            // just in case..
            unset($this->_select, $this->_join, $this->_where, $this->_orderBy, $this->_orderWay, $this->_group, $this->_filterHaving, $this->_filter);

            // gets all information on the products ordered
            $this->_where = 'AND a.`id_supply_order` = '.(int)$id_supply_order;

            // gets the list ordered by price desc, without limit
            $this->getList($lang_id, 'price_te', 'DESC', 0, false, false);

            // gets the currency used in this order
            $currency = new Currency($supply_order->id_currency);

            // gets the warehouse where products will be received
            $warehouse = new Warehouse($supply_order->id_warehouse);

            // sets toolbar title with order reference
            if (!$supply_order->is_template) {
                $this->toolbar_title = sprintf($this->l('Details on supply order #%s'), $supply_order->reference);
            } else {
                $this->toolbar_title = sprintf($this->l('Details on supply order template #%s'), $supply_order->reference);
            }
            // re-defines fields_list
            $this->fields_list = array(
                'supplier_reference' => array(
                    'title' => $this->l('Supplier Reference'),
                    'align' => 'center',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                ),
                'reference' => array(
                    'title' => $this->l('Reference'),
                    'align' => 'center',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                ),
                'ean13' => array(
                    'title' => $this->l('EAN-13 or JAN barcode'),
                    'align' => 'center',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                ),
                'upc' => array(
                    'title' => $this->l('UPC barcode'),
                    'align' => 'center',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                ),
                'name' => array(
                    'title' => $this->l('Name'),
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                ),
                'unit_price_te' => array(
                    'title' => $this->l('Unit price (tax excl.)'),
                    'align' => 'right',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                    'type' => 'price',
                    'currency' => true,
                ),
                'quantity_expected' => array(
                    'title' => $this->l('Quantity'),
                    'align' => 'right',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                ),
                'price_te' => array(
                    'title' => $this->l('Price (tax excl.)'),
                    'align' => 'right',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                    'type' => 'price',
                    'currency' => true,
                ),
                'discount_rate' => array(
                    'title' => $this->l('Discount percentage'),
                    'align' => 'right',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                    'suffix' => '%',
                ),
                'discount_value_te' => array(
                    'title' => $this->l('Discount value (tax excl.)'),
                    'align' => 'right',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                    'type' => 'price',
                    'currency' => true,
                ),
                'price_with_discount_te' => array(
                    'title' => $this->l('Price with product discount (tax excl.)'),
                    'align' => 'right',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                    'type' => 'price',
                    'currency' => true,
                ),
                'tax_rate' => array(
                    'title' => $this->l('Tax rate'),
                    'align' => 'right',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                    'suffix' => '%',
                ),
                'tax_value' => array(
                    'title' => $this->l('Tax value'),
                    'align' => 'right',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                    'type' => 'price',
                    'currency' => true,
                ),
                'price_ti' => array(
                    'title' => $this->l('Price (tax incl.)'),
                    'align' => 'right',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                    'type' => 'price',
                    'currency' => true,
                ),
            );

            //some staff before render list
            foreach ($this->_list as &$item) {
                $item['discount_rate'] = Tools::ps_round($item['discount_rate'], 4);
                $item['tax_rate'] = Tools::ps_round($item['tax_rate'], 4);
                $item['id_currency'] = $currency->id;
            }

            // unsets some buttons
            unset($this->toolbar_btn['export-csv-orders']);
            unset($this->toolbar_btn['export-csv-details']);
            unset($this->toolbar_btn['new']);

            // renders list
            $helper = new HelperList();
            $this->setHelperDisplay($helper);
            $helper->actions = array();
            $helper->show_toolbar = false;
            $helper->toolbar_btn = $this->toolbar_btn;

            $content = $helper->generateList($this->_list, $this->fields_list);

            // display these global order informations
            $this->tpl_view_vars = array(
                'supply_order_detail_content' => $content,
                'supply_order_warehouse' => (Validate::isLoadedObject($warehouse) ? $warehouse->name : ''),
                'supply_order_reference' => $supply_order->reference,
                'supply_order_supplier_name' => $supply_order->supplier_name,
                'supply_order_creation_date' => Tools::displayDate($supply_order->date_add, null, false),
                'supply_order_last_update' => Tools::displayDate($supply_order->date_upd, null, false),
                'supply_order_expected' => Tools::displayDate($supply_order->date_delivery_expected, null, false),
                'supply_order_discount_rate' => Tools::ps_round($supply_order->discount_rate, 2),
                'supply_order_total_te' => Tools::displayPrice($supply_order->total_te, $currency),
                'supply_order_discount_value_te' => Tools::displayPrice($supply_order->discount_value_te, $currency),
                'supply_order_total_with_discount_te' => Tools::displayPrice($supply_order->total_with_discount_te, $currency),
                'supply_order_total_tax' => Tools::displayPrice($supply_order->total_tax, $currency),
                'supply_order_total_ti' => Tools::displayPrice($supply_order->total_ti, $currency),
                'supply_order_currency' => $currency,
                'is_template' => $supply_order->is_template,
            );
        }

        return parent::renderView();
    }

    /**
     * Callback used to display custom content for a given field
     * @param int $id_supply_order
     * @param string $tr
     * @return string $content
     */
    public function printExportIcons($id_supply_order, $tr)
    {
        $supply_order = new SupplyOrder((int)$id_supply_order);

        if (!Validate::isLoadedObject($supply_order)) {
            return;
        }

        $supply_order_state = new SupplyOrderState($supply_order->id_supply_order_state);
        if (!Validate::isLoadedObject($supply_order_state)) {
            return;
        }

        $content = '';
        if ($supply_order_state->editable == false) {
            $content .= '<a class="btn btn-default" href="'.$this->context->link->getAdminLink('AdminPdf')
                .'&submitAction=generateSupplyOrderFormPDF&id_supply_order='.(int)$supply_order->id.'" title="'.$this->l('Export as PDF')
                .'"><i class="icon-print"></i></a>';
        }
        if ($supply_order_state->enclosed == true && $supply_order_state->receipt_state == true) {
            $content .= '&nbsp;<a href="'.$this->context->link->getAdminLink('AdminSupplyOrders').'&id_supply_order='.(int)$supply_order->id.'
						 &csv_order_details" class="btn btn-default" title='.$this->l('Export as CSV').'">
						 <i class="icon-table"></i></a>';
        }


        return $content;
    }

    /**
     * Assigns default actions in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items
     * @see AdminSupplier::initToolbar()
     */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'update_order_state':
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );

            case 'update_receipt':
                // Default cancel button - like old back link
                if (!isset($this->no_back) || $this->no_back == false) {
                    $back = Tools::safeOutput(Tools::getValue('back', ''));
                    if (empty($back)) {
                        $back = self::$currentIndex.'&token='.$this->token;
                    }

                    $this->toolbar_btn['cancel'] = array(
                        'href' => $back,
                        'desc' => $this->l('Cancel')
                    );
                }
            break;

            case 'add':
            case 'edit':
                $this->toolbar_btn['save-and-stay'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save and stay')
                );
            default:
                parent::initToolbar();
        }
    }

    /**
     * Overrides AdminController::afterAdd()
     * @see AdminController::afterAdd()
     * @param ObjectModel $object
     * @return bool
     */
    protected function afterAdd($object)
    {
        if (is_numeric(Tools::getValue('load_products'))) {
            $this->loadProducts((int)Tools::getValue('load_products'));
        }

        $this->object = $object;
        return true;
    }

    /**
     * Loads products which quantity (hysical quantity) is equal or less than $threshold
     * @param int $threshold
     */
    protected function loadProducts($threshold)
    {
        // if there is already an order
        if (Tools::getValue('id_supply_order')) {
            $supply_order = new SupplyOrder((int)Tools::getValue('id_supply_order'));
        } else { // else, we just created a new order
            $supply_order = $this->object;
        }

        // if order is not valid, return;
        if (!Validate::isLoadedObject($supply_order)) {
            return;
        }

        // resets products if needed
        if (Tools::getValue('id_supply_order')) {
            $supply_order->resetProducts();
        }

        // gets products
        $query = new DbQuery();
        $query->select('
			ps.id_product,
			ps.id_product_attribute,
			ps.product_supplier_reference as supplier_reference,
			ps.product_supplier_price_te as unit_price_te,
			ps.id_currency,
			IFNULL(pa.reference, IFNULL(p.reference, \'\')) as reference,
			IFNULL(pa.ean13, IFNULL(p.ean13, \'\')) as ean13,
			IFNULL(pa.upc, IFNULL(p.upc, \'\')) as upc');
        $query->from('product_supplier', 'ps');
        $query->innerJoin('warehouse_product_location', 'wpl', '
			wpl.id_product = ps.id_product
			AND wpl.id_product_attribute = ps.id_product_attribute
			AND wpl.id_warehouse = '.(int)$supply_order->id_warehouse.'
		');
        $query->leftJoin('product', 'p', 'p.id_product = ps.id_product');
        $query->leftJoin('product_attribute', 'pa', '
			pa.id_product_attribute = ps.id_product_attribute
			AND p.id_product = ps.id_product
		');
        $query->where('ps.id_supplier = '.(int)$supply_order->id_supplier);

        // gets items
        $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        // loads order currency
        $order_currency = new Currency($supply_order->id_currency);
        if (!Validate::isLoadedObject($order_currency)) {
            return;
        }

        $manager = StockManagerFactory::getManager();
        foreach ($items as $item) {
            $diff = (int)$threshold;

            if ($supply_order->is_template != 1) {
                $real_quantity = (int)$manager->getProductRealQuantities($item['id_product'], $item['id_product_attribute'],
                    $supply_order->id_warehouse, true);
                $diff = (int)$threshold - (int)$real_quantity;
            }

            if ($diff >= 0) {
                // sets supply_order_detail
                $supply_order_detail = new SupplyOrderDetail();
                $supply_order_detail->id_supply_order = $supply_order->id;
                $supply_order_detail->id_currency = $order_currency->id;
                $supply_order_detail->id_product = $item['id_product'];
                $supply_order_detail->id_product_attribute = $item['id_product_attribute'];
                $supply_order_detail->reference = $item['reference'];
                $supply_order_detail->supplier_reference = $item['supplier_reference'];
                $supply_order_detail->name = Product::getProductName($item['id_product'], $item['id_product_attribute'], $supply_order->id_lang);
                $supply_order_detail->ean13 = $item['ean13'];
                $supply_order_detail->upc = $item['upc'];
                $supply_order_detail->quantity_expected = ((int)$diff == 0) ? 1 : (int)$diff;
                $supply_order_detail->exchange_rate = $order_currency->conversion_rate;

                $product_currency = new Currency($item['id_currency']);
                if (Validate::isLoadedObject($product_currency)) {
                    $supply_order_detail->unit_price_te = Tools::convertPriceFull($item['unit_price_te'], $product_currency, $order_currency);
                } else {
                    $supply_order_detail->unit_price_te = 0;
                }
                $supply_order_detail->save();
                unset($product_currency);
            }
        }

        // updates supply order
        $supply_order->update();
    }

    /**
     * Overrides AdminController::beforeAdd()
     * @see AdminController::beforeAdd()
     *
     * @param SupplyOrder $object
     *
     * @return true
     */
    public function beforeAdd($object)
    {
        if (Tools::isSubmit('is_template')) {
            $object->is_template = 1;
        }

        return true;
    }

    /**
     * Helper function for AdminSupplyOrdersController::postProcess()
     * @see AdminSupplyOrdersController::postProcess()
     */
    protected function postProcessCopyFromTemplate()
    {
        // gets SupplyOrder and checks if it is valid
        $id_supply_order = (int)Tools::getValue('id_supply_order');
        $supply_order = new SupplyOrder($id_supply_order);
        if (!Validate::isLoadedObject($supply_order)) {
            $this->errors[] = Tools::displayError('This template could not be copied.');
        }

        // gets SupplyOrderDetail
        $entries = $supply_order->getEntriesCollection($supply_order->id_lang);

        // updates SupplyOrder so that it is not a template anymore
        $language = new Language($supply_order->id_lang);
        $ref = $supply_order->reference;
        $ref .= ' ('.date($language->date_format_full).')';
        $supply_order->reference = $ref;
        $supply_order->is_template = 0;
        $supply_order->id = (int)0;
        $supply_order->save();

        // copies SupplyOrderDetail
        foreach ($entries as $entry) {
            $entry->id_supply_order = $supply_order->id;
            $entry->id = (int)0;
            $entry->save();
        }

        // redirect when done
        $token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
        $redirect = self::$currentIndex.'&token='.$token;
        $this->redirect_after = $redirect.'&conf=19';
    }

    /**
     * Gets the current warehouse used
     *
     * @return int id_warehouse
     */
    protected function getCurrentWarehouse()
    {
        static $warehouse = 0;

        if ($warehouse == 0) {
            $warehouse = -1; // all warehouses
            if ((int)Tools::getValue('id_warehouse')) {
                $warehouse = (int)Tools::getValue('id_warehouse');
            }
        }
        return $warehouse;
    }

    /**
     * Gets the current filter used
     *
     * @return int status
     */
    protected function getFilterStatus()
    {
        static $status = 0;

        $status = 0;
        if (Tools::getValue('filter_status') === 'on') {
            $status = 1;
        }

        return $status;
    }

    public function initProcess()
    {
        if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $this->warnings[md5('PS_ADVANCED_STOCK_MANAGEMENT')] =
                $this->l('You need to activate advanced stock management prior to using this feature.');
            return false;
        }
        parent::initProcess();
    }
}
