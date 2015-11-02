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
 * @property Product $object
 */
class AdminStockManagementControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'product';
        $this->list_id = 'product';
        $this->className = 'Product';
        $this->lang = true;
        $this->multishop_context = Shop::CONTEXT_ALL;

        $this->fields_list = array(
            'reference' => array(
                'title' => $this->l('Product reference'),
                'filter_key' => 'a!reference'
            ),
            'ean13' => array(
                'title' => $this->l('EAN-13 or JAN barcode'),
                'filter_key' => 'a!ean13'
            ),
            'upc' => array(
                'title' => $this->l('UPC barcode'),
                'filter_key' => 'a!upc'
            ),
            'name' => array(
                'title' => $this->l('Name')
            ),
            'stock' => array(
                'title' => $this->l('Quantity'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'class' => 'fixed-width-sm',
                'align' => 'center',
                'hint' => $this->l('Quantity total for all warehouses.')
            ),
        );

        parent::__construct();

        // overrides confirmation messages specifically for this controller
        $this->_conf = array(
            1 => $this->l('The product was successfully added to your stock.'),
            2 => $this->l('The product was successfully removed from your stock.'),
            3 => $this->l('The transfer was successfully completed.'),
        );
    }

    public function initPageHeaderToolbar()
    {
        if ($this->display == 'details') {
            $this->page_header_toolbar_btn['back_to_list'] = array(
                'href' => Context::getContext()->link->getAdminLink('AdminStockManagement'),
                'desc' => $this->l('Back to list', null, null, false),
                'icon' => 'process-icon-back'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * AdminController::renderList() override
     * @see AdminController::renderList()
     */
    public function renderList()
    {
        // sets actions
        $this->addRowAction('details');
        $this->addRowAction('addstock');
        $this->addRowAction('removestock');

        if (count(Warehouse::getWarehouses()) > 1) {
            $this->addRowAction('transferstock');
        }

        // no link on list rows
        $this->list_no_link = true;

        // inits toolbar
        $this->toolbar_btn = array();

        // overrides query
        $this->_select = 'a.id_product as id, COUNT(pa.id_product_attribute) as variations';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = a.id_product)'.Shop::addSqlAssociation('product_attribute', 'pa', false);
        $this->_where = 'AND a.is_virtual = 0';
        $this->_group = 'GROUP BY a.id_product';

        // displays informations
        $this->displayInformation($this->l('This interface allows you to manage product stock and their variations.').'<br />');
        $this->displayInformation($this->l('Through this interface, you can increase and decrease product stock for an given warehouse.'));
        $this->displayInformation($this->l('Furthermore, you can move product quantities between warehouses, or within one warehouse.').'<br />');
        $this->displayInformation($this->l('If you want to increase quantities of multiple products at once, you can use the "Supply orders" page under the "Stock" menu.').'<br />');
        $this->displayInformation($this->l('Finally, you need to provide the quantity that you\'ll be adding: "Usable for sale" means that this quantity will be available in your shop(s), otherwise it will be considered reserved (i.e. for other purposes).'));

        return parent::renderList();
    }

    /**
     * AdminController::renderForm() override
     * @see AdminController::renderForm()
     */
    public function renderForm()
    {
        // gets the product
        $id_product = (int)Tools::getValue('id_product');
        $id_product_attribute = (int)Tools::getValue('id_product_attribute');

        // gets warehouses
        $warehouses_add = $warehouses_remove = Warehouse::getWarehousesByProductId($id_product, $id_product_attribute);

        // displays warning if no warehouses
        if (!$warehouses_add) {
            $this->displayWarning($this->l('You must choose a warehouses before adding stock. See Stock/Warehouses.'));
        }

        //get currencies list
        $currencies = Currency::getCurrencies();
        $id_default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $default_currency = Currency::getCurrency($id_default_currency);
        if ($default_currency) {
            $currencies = array_merge(array($default_currency, '-'), $currencies);
        }

        // switch, in order to display the form corresponding to the current action
        switch ($this->display) {
            case 'addstock' :
                // gets the last stock mvt for this product, so we can display the last unit price te and the last quantity added
                $last_sm_unit_price_te = $this->l('N/A');
                $last_sm_quantity = 0;
                $last_sm_quantity_is_usable = -1;
                $last_sm = StockMvt::getLastPositiveStockMvt($id_product, $id_product_attribute);

                // if there is a stock mvt
                if ($last_sm != false) {
                    $last_sm_currency = new Currency((int)$last_sm['id_currency']);
                    $last_sm_quantity = (int)$last_sm['physical_quantity'];
                    $last_sm_quantity_is_usable = (int)$last_sm['is_usable'];
                    if (Validate::isLoadedObject($last_sm_currency)) {
                        $last_sm_unit_price_te = Tools::displayPrice((float)$last_sm['price_te'], $last_sm_currency);
                    }
                }

                $this->displayInformation($this->l('Moving the mouse cursor over the quantity and price fields will give you the details about the last stock movement.'));
                // fields in the form
                $this->fields_form[]['form'] = array(
                    'legend' => array(
                        'title' => $this->l('Add a product to your stock.'),
                        'icon' => 'icon-long-arrow-up'
                    ),
                    'input' => array(
                        array(
                            'type' => 'hidden',
                            'name' => 'is_post',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'id_product',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'id_product_attribute',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'check',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Product reference'),
                            'name' => 'reference',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('EAN-13 or JAN barcode'),
                            'name' => 'ean13',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('UPC barcode'),
                            'name' => 'upc',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Name'),
                            'name' => 'name',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Quantity to add'),
                            'name' => 'quantity',
                            'maxlength' => 6,
                            'required' => true,
                            'hint' => array(
                                        $this->l('Indicate the physical quantity of this product that you want to add.'),
                                        $this->l('Last physical quantity added: %s items (usable for sale: %s).'),
                                        ($last_sm_quantity > 0 ? $last_sm_quantity : $this->l('N/A')),
                                        ($last_sm_quantity > 0 ? ($last_sm_quantity_is_usable >= 0 ? $this->l('Yes') : $this->l('No')) : $this->l('N/A'))),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Usable for sale?'),
                            'name' => 'usable',
                            'required' => true,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            ),
                            'hint' => $this->l('Is this quantity ready to be displayed in your shop, or is it reserved in the warehouse for other purposes?')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Warehouse'),
                            'name' => 'id_warehouse',
                            'required' => true,
                            'options' => array(
                                'query' => $warehouses_add,
                                'id' => 'id_warehouse',
                                'name' => 'name'
                            ),
                            'hint' => $this->l('Please select the warehouse that you\'ll be adding products to.')
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Unit price (tax excl.)'),
                            'name' => 'price',
                            'required' => true,
                            'size' => 10,
                            'maxlength' => 10,
                            'hint' => array(
                                $this->l('Unit purchase price or unit manufacturing cost for this product (tax excl.).'),
                                sprintf($this->l('Last unit price (tax excl.): %s.'), $last_sm_unit_price_te),
                            )
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
                            'hint' => $this->l('The currency associated to the product unit price.'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Label'),
                            'name' => 'id_stock_mvt_reason',
                            'required' => true,
                            'options' => array(
                                'query' => StockMvtReason::getStockMvtReasonsWithFilter($this->context->language->id,
                                                                                        array(Configuration::get('PS_STOCK_MVT_TRANSFER_TO')),
                                                                                        1),
                                'id' => 'id_stock_mvt_reason',
                                'name' => 'name'
                            ),
                            'hint' => $this->l('Label used in stock movements.'),
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Add to stock')
                    )
                );
                $this->fields_value['usable'] = 1;
            break;

            case 'removestock' :
                $this->fields_form[]['form'] = array(
                    'legend' => array(
                        'title' => $this->l('Remove the product from your stock.'),
                        'icon' => 'icon-long-arrow-down'
                    ),
                    'input' => array(
                        array(
                            'type' => 'hidden',
                            'name' => 'is_post',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'id_product',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'id_product_attribute',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'check',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Product reference'),
                            'name' => 'reference',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('EAN-13 or JAN barcode'),
                            'name' => 'ean13',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Name'),
                            'name' => 'name',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Quantity to remove'),
                            'name' => 'quantity',
                            'maxlength' => 6,
                            'required' => true,
                            'hint' => $this->l('Indicate the physical quantity of this product that you want to remove.'),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Usable for sale'),
                            'name' => 'usable',
                            'required' => true,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enabled')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disabled')
                                )
                            ),
                            'hint' => $this->l('Do you want to remove this quantity from the usable quantity (yes) or the physical quantity (no)?')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Warehouse'),
                            'name' => 'id_warehouse',
                            'required' => true,
                            'options' => array(
                                'query' => $warehouses_remove,
                                'id' => 'id_warehouse',
                                'name' => 'name'
                            ),
                            'hint' => $this->l('Select the warehouse you\'d like to remove the product from.')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Label'),
                            'name' => 'id_stock_mvt_reason',
                            'required' => true,
                            'options' => array(
                                'query' => StockMvtReason::getStockMvtReasonsWithFilter($this->context->language->id,
                                                                                        array(Configuration::get('PS_STOCK_MVT_TRANSFER_FROM')),
                                                                                        -1),
                                'id' => 'id_stock_mvt_reason',
                                'name' => 'name'
                            ),
                            'hint' => $this->l('Label used in stock movements.'),
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Remove from stock')
                    )
                );
            break;

            case 'transferstock' :
                $this->fields_form[]['form'] = array(
                    'legend' => array(
                        'title' => $this->l('Transfer a product from one warehouse to another'),
                        'icon' => 'icon-share-alt'
                    ),
                    'input' => array(
                        array(
                            'type' => 'hidden',
                            'name' => 'is_post',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'id_product',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'id_product_attribute',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'check',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Product reference'),
                            'name' => 'reference',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('EAN-13 or JAN barcode'),
                            'name' => 'ean13',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Name'),
                            'name' => 'name',
                            'disabled' => true,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Quantity to transfer'),
                            'name' => 'quantity',
                            'maxlength' => 6,
                            'required' => true,
                            'hint' => $this->l('Indicate the physical quantity of this product that you want to transfer.')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Source warehouse'),
                            'name' => 'id_warehouse_from',
                            'required' => true,
                            'options' => array(
                                'query' => $warehouses_remove,
                                'id' => 'id_warehouse',
                                'name' => 'name'
                            ),
                            'hint' => $this->l('Select the warehouse you\'d like to transfer the product from.')
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Is this product usable for sale in your source warehouse?'),
                            'name' => 'usable_from',
                            'required' => true,
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            ),
                            'hint' => $this->l('Is this the usable quantity for sale?')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Destination warehouse'),
                            'name' => 'id_warehouse_to',
                            'required' => true,
                            'options' => array(
                                'query' => $warehouses_add,
                                'id' => 'id_warehouse',
                                'name' => 'name'
                            ),
                            'hint' => $this->l('Select the warehouse you\'d like to transfer your product(s) to. ')
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Is this product usable for sale in your destination warehouse?'),
                            'name' => 'usable_to',
                            'required' => true,
                            'class' => 't',
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            ),
                            'hint' => $this->l('Do you want it to be for sale/usable?')
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Transfer')
                    )
                );
            break;
        }

        $this->initToolbar();
    }

    /**
     * AdminController::postProcess() override
     * @see AdminController::postProcess()
     */
    public function postProcess()
    {
        parent::postProcess();

        // Checks access
        if (Tools::isSubmit('addStock') && !($this->tabAccess['add'] === '1')) {
            $this->errors[] = Tools::displayError('You do not have the required permission to add stock.');
        }
        if (Tools::isSubmit('removeStock') && !($this->tabAccess['delete'] === '1')) {
            $this->errors[] = Tools::displayError('You do not have the required permission to delete stock');
        }
        if (Tools::isSubmit('transferStock') && !($this->tabAccess['edit'] === '1')) {
            $this->errors[] = Tools::displayError('You do not have the required permission to transfer stock.');
        }

        if (count($this->errors)) {
            return;
        }

        // Global checks when add / remove / transfer product
        if ((Tools::isSubmit('addstock') || Tools::isSubmit('removestock') || Tools::isSubmit('transferstock')) && Tools::isSubmit('is_post')) {
            // get product ID
            $id_product = (int)Tools::getValue('id_product', 0);
            if ($id_product <= 0) {
                $this->errors[] = Tools::displayError('The selected product is not valid.');
            }

            // get product_attribute ID
            $id_product_attribute = (int)Tools::getValue('id_product_attribute', 0);

            // check the product hash
            $check = Tools::getValue('check', '');
            $check_valid = md5(_COOKIE_KEY_.$id_product.$id_product_attribute);
            if ($check != $check_valid) {
                $this->errors[] = Tools::displayError('The selected product is not valid.');
            }

            // get quantity and check that the post value is really an integer
            // If it's not, we have nothing to do
            $quantity = Tools::getValue('quantity', 0);
            if (!is_numeric($quantity) || (int)$quantity <= 0) {
                $this->errors[] = Tools::displayError('The quantity value is not valid.');
            }
            $quantity = (int)$quantity;

            $token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
            $redirect = self::$currentIndex.'&token='.$token;
        }

        // Global checks when add / remove product
        if ((Tools::isSubmit('addstock') || Tools::isSubmit('removestock')) && Tools::isSubmit('is_post')) {
            // get warehouse id
            $id_warehouse = (int)Tools::getValue('id_warehouse', 0);
            if ($id_warehouse <= 0 || !Warehouse::exists($id_warehouse)) {
                $this->errors[] = Tools::displayError('The selected warehouse is not valid.');
            }

            // get stock movement reason id
            $id_stock_mvt_reason = (int)Tools::getValue('id_stock_mvt_reason', 0);
            if ($id_stock_mvt_reason <= 0 || !StockMvtReason::exists($id_stock_mvt_reason)) {
                $this->errors[] = Tools::displayError('The reason is not valid.');
            }

            // get usable flag
            $usable = Tools::getValue('usable', null);
            if (is_null($usable)) {
                $this->errors[] = Tools::displayError('You have to specify whether the product quantity is usable for sale on shops or not.');
            }
            $usable = (bool)$usable;
        }

        if (Tools::isSubmit('addstock') && Tools::isSubmit('is_post')) {
            // get product unit price
            $price = str_replace(',', '.', Tools::getValue('price', 0));
            if (!is_numeric($price)) {
                $this->errors[] = Tools::displayError('The product price is not valid.');
            }
            $price = round(floatval($price), 6);

            // get product unit price currency id
            $id_currency = (int)Tools::getValue('id_currency', 0);
            if ($id_currency <= 0 || (!($result = Currency::getCurrency($id_currency)) || empty($result))) {
                $this->errors[] = Tools::displayError('The selected currency is not valid.');
            }

            // if all is ok, add stock
            if (count($this->errors) == 0) {
                $warehouse = new Warehouse($id_warehouse);

                // convert price to warehouse currency if needed
                if ($id_currency != $warehouse->id_currency) {
                    // First convert price to the default currency
                    $price_converted_to_default_currency = Tools::convertPrice($price, $id_currency, false);

                    // Convert the new price from default currency to needed currency
                    $price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
                }

                // add stock
                $stock_manager = StockManagerFactory::getManager();

                if ($stock_manager->addProduct($id_product, $id_product_attribute, $warehouse, $quantity, $id_stock_mvt_reason, $price, $usable)) {
                    // Create warehouse_product_location entry if we add stock to a new warehouse
                    $id_wpl = (int)WarehouseProductLocation::getIdByProductAndWarehouse($id_product, $id_product_attribute, $id_warehouse);
                    if (!$id_wpl) {
                        $wpl = new WarehouseProductLocation();
                        $wpl->id_product = (int)$id_product;
                        $wpl->id_product_attribute = (int)$id_product_attribute;
                        $wpl->id_warehouse = (int)$id_warehouse;
                        $wpl->save();
                    }

                    StockAvailable::synchronize($id_product);

                    if (Tools::isSubmit('addstockAndStay')) {
                        $redirect = self::$currentIndex.'&id_product='.(int)$id_product;
                        if ($id_product_attribute) {
                            $redirect .= '&id_product_attribute='.(int)$id_product_attribute;
                        }
                        $redirect .= '&addstock&token='.$token;
                    }
                    Tools::redirectAdmin($redirect.'&conf=1');
                } else {
                    $this->errors[] = Tools::displayError('An error occurred. No stock was added.');
                }
            }
        }

        if (Tools::isSubmit('removestock') && Tools::isSubmit('is_post')) {
            // if all is ok, remove stock
            if (count($this->errors) == 0) {
                $warehouse = new Warehouse($id_warehouse);

                // remove stock
                $stock_manager = StockManagerFactory::getManager();
                $removed_products = $stock_manager->removeProduct($id_product, $id_product_attribute, $warehouse, $quantity, $id_stock_mvt_reason, $usable);

                if (count($removed_products) > 0) {
                    StockAvailable::synchronize($id_product);
                    Tools::redirectAdmin($redirect.'&conf=2');
                } else {
                    $physical_quantity_in_stock = (int)$stock_manager->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), false);
                    $usable_quantity_in_stock = (int)$stock_manager->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), true);
                    $not_usable_quantity = ($physical_quantity_in_stock - $usable_quantity_in_stock);
                    if ($usable_quantity_in_stock < $quantity) {
                        $this->errors[] = sprintf(Tools::displayError('You don\'t have enough usable quantity. Cannot remove %d items out of %d.'), (int)$quantity, (int)$usable_quantity_in_stock);
                    } elseif ($not_usable_quantity < $quantity) {
                        $this->errors[] = sprintf(Tools::displayError('You don\'t have enough usable quantity. Cannot remove %d items out of %d.'), (int)$quantity, (int)$not_usable_quantity);
                    } else {
                        $this->errors[] = Tools::displayError('It is not possible to remove the specified quantity. Therefore no stock was removed.');
                    }
                }
            }
        }

        if (Tools::isSubmit('transferstock') && Tools::isSubmit('is_post')) {
            // get source warehouse id
            $id_warehouse_from = (int)Tools::getValue('id_warehouse_from', 0);
            if ($id_warehouse_from <= 0 || !Warehouse::exists($id_warehouse_from)) {
                $this->errors[] = Tools::displayError('The source warehouse is not valid.');
            }

            // get destination warehouse id
            $id_warehouse_to = (int)Tools::getValue('id_warehouse_to', 0);
            if ($id_warehouse_to <= 0 || !Warehouse::exists($id_warehouse_to)) {
                $this->errors[] = Tools::displayError('The destination warehouse is not valid.');
            }

            // get usable flag for source warehouse
            $usable_from = Tools::getValue('usable_from', null);
            if (is_null($usable_from)) {
                $this->errors[] = Tools::displayError('You have to specify whether the product quantity in your source warehouse(s) is ready for sale or not.');
            }
            $usable_from = (bool)$usable_from;

            // get usable flag for destination warehouse
            $usable_to = Tools::getValue('usable_to', null);
            if (is_null($usable_to)) {
                $this->errors[] = Tools::displayError('You have to specify whether the product quantity in your destination warehouse(s) is ready for sale or not.');
            }
            $usable_to = (bool)$usable_to;

            // if we can process stock transfers
            if (count($this->errors) == 0) {
                // transfer stock
                $stock_manager = StockManagerFactory::getManager();

                $is_transfer = $stock_manager->transferBetweenWarehouses(
                    $id_product,
                    $id_product_attribute,
                    $quantity,
                    $id_warehouse_from,
                    $id_warehouse_to,
                    $usable_from,
                    $usable_to
                );
                StockAvailable::synchronize($id_product);
                if ($is_transfer) {
                    Tools::redirectAdmin($redirect.'&conf=3');
                } else {
                    $this->errors[] = Tools::displayError('It is not possible to transfer the specified quantity. No stock was transferred.');
                }
            }
        }
    }

    /**
     * assign default action in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items
     *
     */
    public function initToolbar()
    {
        switch ($this->display) {
            case 'addstock':
                $this->toolbar_btn['save-and-stay'] = array(
                        'short' => 'SaveAndStay',
                        'href' => '#',
                        'desc' => $this->l('Save and stay'),
                    );
            case 'removestock':
            case 'transferstock':
                $this->toolbar_btn['save'] = array(
                    'href' => '#',
                    'desc' => $this->l('Save')
                );

                // Default cancel button - like old back link
                $back = Tools::safeOutput(Tools::getValue('back', ''));
                if (empty($back)) {
                    $back = self::$currentIndex.'&token='.$this->token;
                }

                $this->toolbar_btn['cancel'] = array(
                    'href' => $back,
                    'desc' => $this->l('Cancel')
                );
            break;

            default:
                parent::initToolbar();
        }
    }

    /**
     * AdminController::init() override
     * @see AdminController::init()
     */
    public function init()
    {
        parent::init();

        if (Tools::isSubmit('addstock')) {
            $this->display = 'addstock';
            $this->toolbar_title = $this->l('Stock: Add a product');
        }

        if (Tools::isSubmit('removestock')) {
            $this->display = 'removestock';
            $this->toolbar_title = $this->l('Stock: Remove a product');
        }

        if (Tools::isSubmit('transferstock')) {
            $this->display = 'transferstock';
            $this->toolbar_title = $this->l('Stock: Transfer a product');
        }
    }

    public function renderDetails()
    {
        if (Tools::isSubmit('id_product')) {
            // override attributes
            $this->identifier = 'id_product_attribute';
            $this->list_id = 'product_attribute';
            $this->lang = false;

            $this->addRowAction('addstock');
            $this->addRowAction('removestock');

            if (count(Warehouse::getWarehouses()) > 1) {
                $this->addRowAction('transferstock');
            }

            // no link on list rows
            $this->list_no_link = true;

            // inits toolbar
            $this->toolbar_btn = array();

            // get current lang id
            $lang_id = (int)$this->context->language->id;

            // Get product id
            $product_id = (int)Tools::getValue('id_product');

            // Load product attributes with sql override
            $this->table = 'product_attribute';
            $this->list_id = 'product_attribute';
            $this->_select = 'a.id_product_attribute as id, a.id_product, a.reference, a.ean13, a.upc';
            $this->_where = 'AND a.id_product = '.$product_id;
            $this->_group = 'GROUP BY a.id_product_attribute';

            $this->fields_list = array(
                'reference' => array(
                    'title' => $this->l('Product reference'),
                    'filter_key' => 'a!reference'
                ),
                'ean13' => array(
                    'title' => $this->l('EAN-13 or JAN barcode'),
                    'filter_key' => 'a!ean13'
                ),
                'upc' => array(
                    'title' => $this->l('UPC barcode'),
                    'filter_key' => 'a!upc'
                ),
                'name' => array(
                    'title' => $this->l('Name'),
                    'orderby' => false,
                    'filter' => false,
                    'search' => false
                ),
                'stock' => array(
                    'title' => $this->l('Quantity'),
                    'orderby' => false,
                    'filter' => false,
                    'search' => false,
                    'class' => 'fixed-width-sm',
                    'align' => 'center',
                    'hint' => $this->l('Quantitity total for all warehouses.')
                ),
            );

            self::$currentIndex = self::$currentIndex.'&id_product='.(int)$product_id.'&detailsproduct';
            $this->processFilter();
            return parent::renderList();
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
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        // Check each row to see if there are combinations and get the correct action in consequence
        $nb_items = count($this->_list);

        for ($i = 0; $i < $nb_items; $i++) {
            $item = &$this->_list[$i];

            // if it's an ajax request we have to consider manipulating a product variation
            if (Tools::isSubmit('id_product')) {
                $item['name'] = Product::getProductName($item['id_product'], $item['id']);
                // no details for this row
                $this->addRowActionSkipList('details', array($item['id']));

                // specify actions in function of stock
                $this->skipActionByStock($item, true);
            }
            // If current product has variations
            elseif (array_key_exists('variations', $item) && (int)$item['variations'] > 0) {
                // we have to desactivate stock actions on current row
                $this->addRowActionSkipList('addstock', array($item['id']));
                $this->addRowActionSkipList('removestock', array($item['id']));
                $this->addRowActionSkipList('transferstock', array($item['id']));

                // does not display these informaions because this product has combinations
                $item['reference'] = '--';
                $item['ean13'] = '--';
                $item['upc'] = '--';
            } else {
                //there are no variations of current product, so we don't want to show details action
                $this->addRowActionSkipList('details', array($item['id']));

                // specify actions in function of stock
                $this->skipActionByStock($item, false);
            }
            // Checks access
            if (!($this->tabAccess['add'] === '1')) {
                $this->addRowActionSkipList('addstock', array($item['id']));
            }
            if (!($this->tabAccess['delete'] === '1')) {
                $this->addRowActionSkipList('removestock', array($item['id']));
            }
            if (!($this->tabAccess['edit'] === '1')) {
                $this->addRowActionSkipList('transferstock', array($item['id']));
            }
        }
    }

    /**
     * Check stock for a given product or product attribute
     * and manage available actions in consequence
     *
     * @param array $item                 Reference to the current item
     * @param bool  $is_product_variation Specify if it's a product or a product variation
     */
    protected function skipActionByStock(&$item, $is_product_variation = false)
    {
        $stock_manager = StockManagerFactory::getManager();

        //get stocks for this product
        if ($is_product_variation) {
            $stock = $stock_manager->getProductPhysicalQuantities($item['id_product'], $item['id']);
        } else {
            $stock = $stock_manager->getProductPhysicalQuantities($item['id'], 0);
        }

        //affects stock to the list for display
        $item['stock'] = $stock;

        if ($stock <= 0) {
            //there is no stock, we can only add stock
            $this->addRowActionSkipList('removestock', array($item['id']));
            $this->addRowActionSkipList('transferstock', array($item['id']));
        }
    }

    /**
     * AdminController::initContent() override
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $this->warnings[md5('PS_ADVANCED_STOCK_MANAGEMENT')] = $this->l('You need to activate the Advanced Stock Management feature prior to using this feature.');
            return false;
        }

        // Manage the add stock form
        if ($this->display == 'addstock' || $this->display == 'removestock' || $this->display == 'transferstock') {
            if (Tools::isSubmit('id_product') || Tools::isSubmit('id_product_attribute')) {
                // get id product and product attribute if possible
                $id_product = (int)Tools::getValue('id_product', 0);
                $id_product_attribute = (int)Tools::getValue('id_product_attribute', 0);

                $product_is_valid = false;
                $is_pack = false;
                $is_virtual = false;
                $lang_id = $this->context->language->id;
                $default_wholesale_price = 0;

                // try to load product attribute first
                if ($id_product_attribute > 0) {
                    // try to load product attribute
                    $combination = new Combination($id_product_attribute);
                    if (Validate::isLoadedObject($combination)) {
                        $product_is_valid = true;
                        $id_product = $combination->id_product;
                        $reference = $combination->reference;
                        $ean13 = $combination->ean13;
                        $upc = $combination->upc;
                        $manufacturer_reference = $combination->supplier_reference;

                        // get the full name for this combination
                        $query = new DbQuery();

                        $query->select('IFNULL(CONCAT(pl.`name`, \' : \', GROUP_CONCAT(agl.`name`, \' - \', al.`name` SEPARATOR \', \')),pl.`name`) as name');
                        $query->from('product_attribute', 'a');
                        $query->join('INNER JOIN '._DB_PREFIX_.'product_lang pl ON (pl.`id_product` = a.`id_product` AND pl.`id_lang` = '.(int)$lang_id.')
							LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.`id_product_attribute` = a.`id_product_attribute`)
							LEFT JOIN '._DB_PREFIX_.'attribute atr ON (atr.`id_attribute` = pac.`id_attribute`)
							LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.`id_attribute` = atr.`id_attribute` AND al.`id_lang` = '.(int)$lang_id.')
							LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.`id_attribute_group` = atr.`id_attribute_group` AND agl.`id_lang` = '.(int)$lang_id.')'
                        );
                        $query->where('a.`id_product_attribute` = '.$id_product_attribute);
                        $name = Db::getInstance()->getValue($query);
                        $p = new Product($id_product, false, $lang_id);
                        $default_wholesale_price = $combination->wholesale_price > 0 ? $combination->wholesale_price : $p->wholesale_price;
                    }
                }
                // try to load a simple product
                else {
                    $product = new Product($id_product, false, $lang_id);
                    if (is_int($product->id)) {
                        $product_is_valid = true;
                        $reference = $product->reference;
                        $ean13 = $product->ean13;
                        $upc = $product->upc;
                        $name = $product->name;
                        $manufacturer_reference = $product->supplier_reference;
                        $is_pack = $product->cache_is_pack;
                        $is_virtual = $product->is_virtual;
                        $default_wholesale_price = $product->wholesale_price;
                    }
                }

                if ($product_is_valid === true && $is_virtual == false) {
                    // init form
                    $this->renderForm();
                    $this->getlanguages();

                    $helper = new HelperForm();

                    $this->initPageHeaderToolbar();

                    // Check if form template has been overriden
                    if (file_exists($this->context->smarty->getTemplateDir(0).'/'.$this->tpl_folder.'form.tpl')) {
                        $helper->tpl = $this->tpl_folder.'form.tpl';
                    }

                    $this->setHelperDisplay($helper);
                    $helper->submit_action = $this->display;
                    $helper->id = null; // no display standard hidden field in the form
                    $helper->languages = $this->_languages;
                    $helper->default_form_language = $this->default_form_language;
                    $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
                    $helper->show_cancel_button = true;
                    $helper->back_url = $this->context->link->getAdminLink('AdminStockManagement');

                    $helper->fields_value = array(
                        'id_product' => $id_product,
                        'id_product_attribute' => $id_product_attribute,
                        'reference' => $reference,
                        'manufacturer_reference' => $manufacturer_reference,
                        'name' => $name,
                        'ean13' => $ean13,
                        'upc' => $upc,
                        'check' => md5(_COOKIE_KEY_.$id_product.$id_product_attribute),
                        'quantity' => Tools::getValue('quantity', ''),
                        'id_warehouse' => Tools::getValue('id_warehouse', ''),
                        'usable' => $this->fields_value['usable'] ? $this->fields_value['usable'] : Tools::getValue('usable', 1),
                        'price' => Tools::getValue('price', (float)Tools::convertPrice($default_wholesale_price, null)),
                        'id_currency' => Tools::getValue('id_currency', ''),
                        'id_stock_mvt_reason' => Tools::getValue('id_stock_mvt_reason', ''),
                        'is_post' => 1,
                    );

                    if ($this->display == 'addstock') {
                        $_POST['id_product'] = (int)$id_product;
                    }

                    if ($this->display == 'transferstock') {
                        $helper->fields_value['id_warehouse_from'] = Tools::getValue('id_warehouse_from', '');
                        $helper->fields_value['id_warehouse_to'] = Tools::getValue('id_warehouse_to', '');
                        $helper->fields_value['usable_from'] = Tools::getValue('usable_from', '1');
                        $helper->fields_value['usable_to'] = Tools::getValue('usable_to', '1');
                    }

                    $this->content .= $helper->generateForm($this->fields_form);

                    $this->context->smarty->assign(array(
                        'content' => $this->content,
                        'show_page_header_toolbar' => $this->show_page_header_toolbar,
                        'page_header_toolbar_title' => $this->page_header_toolbar_title,
                        'page_header_toolbar_btn' => $this->page_header_toolbar_btn
                    ));
                } else {
                    $this->errors[] = Tools::displayError('The specified product is not valid.');
                }
            }
        } else {
            parent::initContent();
        }
    }

     /**
     * Display addstock action link
     * @param string $token the token to add to the link
     * @param int $id the identifier to add to the link
     * @return string
     */
    public function displayAddstockLink($token = null, $id)
    {
        if (!array_key_exists('AddStock', self::$cache_lang)) {
            self::$cache_lang['AddStock'] = $this->l('Add stock');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
                '&'.$this->identifier.'='.$id.
                '&addstock&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['AddStock'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_addstock.tpl');
    }

    /**
     * Display removestock action link
     * @param string $token the token to add to the link
     * @param int $id the identifier to add to the link
     * @return string
     */
    public function displayRemovestockLink($token = null, $id)
    {
        if (!array_key_exists('RemoveStock', self::$cache_lang)) {
            self::$cache_lang['RemoveStock'] = $this->l('Remove stock');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
                '&'.$this->identifier.'='.$id.
                '&removestock&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['RemoveStock'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_removestock.tpl');
    }

    /**
     * Display transferstock action link
     * @param string $token the token to add to the link
     * @param int $id the identifier to add to the link
     * @return string
     */
    public function displayTransferstockLink($token = null, $id)
    {
        if (!array_key_exists('TransferStock', self::$cache_lang)) {
            self::$cache_lang['TransferStock'] = $this->l('Transfer stock');
        }

        $this->context->smarty->assign(array(
            'href' => self::$currentIndex.
                '&'.$this->identifier.'='.$id.
                '&transferstock&token='.($token != null ? $token : $this->token),
            'action' => self::$cache_lang['TransferStock'],
        ));

        return $this->context->smarty->fetch('helpers/list/list_action_transferstock.tpl');
    }

    public function initProcess()
    {
        if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            $this->warnings[md5('PS_ADVANCED_STOCK_MANAGEMENT')] = $this->l('You need to activate advanced stock management prior to using this feature.');
            return false;
        }

        if (Tools::getIsset('detailsproduct')) {
            $this->list_id = 'product_attribute';

            if (isset($_POST['submitReset'.$this->list_id])) {
                $this->processResetFilters();
            }
        } else {
            $this->list_id = 'product';
        }

        parent::initProcess();
    }
}
