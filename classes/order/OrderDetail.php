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

class OrderDetailCore extends ObjectModel
{
    /** @var int */
    public $id_order_detail;

    /** @var int */
    public $id_order;

    /** @var int */
    public $id_order_invoice;

    /** @var int */
    public $product_id;

    /** @var int */
    public $id_shop;

    /** @var int */
    public $product_attribute_id;

    /** @var string */
    public $product_name;

    /** @var int */
    public $product_quantity;

    /** @var int */
    public $product_quantity_in_stock;

    /** @var int */
    public $product_quantity_return;

    /** @var int */
    public $product_quantity_refunded;

    /** @var int */
    public $product_quantity_reinjected;

    /** @var float */
    public $product_price;

    /** @var float */
    public $original_product_price;

    /** @var float */
    public $unit_price_tax_incl;

    /** @var float */
    public $unit_price_tax_excl;

    /** @var float */
    public $total_price_tax_incl;

    /** @var float */
    public $total_price_tax_excl;

    /** @var float */
    public $reduction_percent;

    /** @var float */
    public $reduction_amount;

    /** @var float */
    public $reduction_amount_tax_excl;

    /** @var float */
    public $reduction_amount_tax_incl;

    /** @var float */
    public $group_reduction;

    /** @var float */
    public $product_quantity_discount;

    /** @var string */
    public $product_ean13;

    /** @var string */
    public $product_upc;

    /** @var string */
    public $product_reference;

    /** @var string */
    public $product_supplier_reference;

    /** @var float */
    public $product_weight;

    /** @var float */
    public $ecotax;

    /** @var float */
    public $ecotax_tax_rate;

    /** @var int */
    public $discount_quantity_applied;

    /** @var string */
    public $download_hash;

    /** @var int */
    public $download_nb;

    /** @var datetime */
    public $download_deadline;

    /** @var string $tax_name **/
    public $tax_name;

    /** @var float $tax_rate **/
    public $tax_rate;

    /** @var float $tax_computation_method **/
    public $tax_computation_method;

    /** @var int Id tax rules group */
    public $id_tax_rules_group;

    /** @var int Id warehouse */
    public $id_warehouse;

    /** @var float additional shipping price tax excl */
    public $total_shipping_price_tax_excl;

    /** @var float additional shipping price tax incl */
    public $total_shipping_price_tax_incl;

    /** @var float */
    public $purchase_supplier_price;

    /** @var float */
    public $original_wholesale_price;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_detail',
        'primary' => 'id_order_detail',
        'fields' => array(
            'id_order' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_invoice' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_warehouse' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'product_id' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_attribute_id' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_name' =>                array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'product_quantity' =>            array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'product_quantity_in_stock' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'product_quantity_return' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_quantity_refunded' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_quantity_reinjected' =>array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'product_price' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'reduction_percent' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'reduction_amount' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'reduction_amount_tax_incl' =>  array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'reduction_amount_tax_excl' =>  array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'group_reduction' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'product_quantity_discount' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'product_ean13' =>                array('type' => self::TYPE_STRING, 'validate' => 'isEan13'),
            'product_upc' =>                array('type' => self::TYPE_STRING, 'validate' => 'isUpc'),
            'product_reference' =>            array('type' => self::TYPE_STRING, 'validate' => 'isReference'),
            'product_supplier_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isReference'),
            'product_weight' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tax_name' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'tax_rate' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'tax_computation_method' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_tax_rules_group' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'ecotax' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'ecotax_tax_rate' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'discount_quantity_applied' =>    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'download_hash' =>                array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'download_nb' =>                array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'download_deadline' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'unit_price_tax_incl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'unit_price_tax_excl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_price_tax_incl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_price_tax_excl' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_shipping_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'purchase_supplier_price' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'original_product_price' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'original_wholesale_price' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice')
        ),
    );

    protected $webserviceParameters = array(
        'fields' => array(
            'id_order' => array('xlink_resource' => 'orders'),
            'product_id' => array('xlink_resource' => 'products'),
            'product_attribute_id' => array('xlink_resource' => 'combinations'),
            'product_quantity_reinjected' => array(),
            'group_reduction' => array(),
            'discount_quantity_applied' => array(),
            'download_hash' => array(),
            'download_deadline' => array()
        ),
        'hidden_fields' => array('tax_rate', 'tax_name'),
        'associations' => array(
            'taxes'  => array('resource' => 'tax', 'getter' => 'getWsTaxes', 'setter' => false,
                'fields' => array('id' =>  array(), ),
            ),
        ));

    /** @var bool */
    protected $outOfStock = false;

    /** @var TaxCalculator object */
    protected $tax_calculator = null;

    /** @var Address object */
    protected $vat_address = null;

    /** @var Address object */
    protected $specificPrice = null;

    /** @var Customer object */
    protected $customer = null;

    /** @var Context object */
    protected $context = null;

    public function __construct($id = null, $id_lang = null, $context = null)
    {
        $this->context = $context;
        $id_shop = null;
        if ($this->context != null && isset($this->context->shop)) {
            $id_shop = $this->context->shop->id;
        }
        parent::__construct($id, $id_lang, $id_shop);

        if ($context == null) {
            $context = Context::getContext();
        }
        $this->context = $context->cloneContext();
    }

    public function delete()
    {
        if (!$res = parent::delete()) {
            return false;
        }

        Db::getInstance()->delete('order_detail_tax', 'id_order_detail='.(int)$this->id);

        return $res;
    }

    protected function setContext($id_shop)
    {
        if ($this->context->shop->id != $id_shop) {
            $this->context->shop = new Shop((int)$id_shop);
        }
    }

    public static function getDownloadFromHash($hash)
    {
        if ($hash == '') {
            return false;
        }
        $sql = 'SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'product_download` pd ON (od.`product_id`=pd.`id_product`)
		WHERE od.`download_hash` = \''.pSQL(strval($hash)).'\'
		AND pd.`active` = 1';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public static function incrementDownload($id_order_detail, $increment = 1)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'order_detail`
			SET `download_nb` = `download_nb` + '.(int)$increment.'
			WHERE `id_order_detail`= '.(int)$id_order_detail.'
			LIMIT 1';
        return Db::getInstance()->execute($sql);
    }

    /**
     * Returns the tax calculator associated to this order detail.
     * @since 1.5.0.1
     * @return TaxCalculator
     */
    public function getTaxCalculator()
    {
        return OrderDetail::getTaxCalculatorStatic($this->id);
    }

    /**
     * Return the tax calculator associated to this order_detail
     * @since 1.5.0.1
     * @param int $id_order_detail
     * @return TaxCalculator
     */
    public static function getTaxCalculatorStatic($id_order_detail)
    {
        $sql = 'SELECT t.*, d.`tax_computation_method`
				FROM `'._DB_PREFIX_.'order_detail_tax` t
				LEFT JOIN `'._DB_PREFIX_.'order_detail` d ON (d.`id_order_detail` = t.`id_order_detail`)
				WHERE d.`id_order_detail` = '.(int)$id_order_detail;

        $computation_method = 1;
        $taxes = array();
        if ($results = Db::getInstance()->executeS($sql)) {
            foreach ($results as $result) {
                $taxes[] = new Tax((int)$result['id_tax']);
            }

            $computation_method = $result['tax_computation_method'];
        }

        return new TaxCalculator($taxes, $computation_method);
    }

    /**
     * Save the tax calculator
     * @since 1.5.0.1
     * @deprecated Functionality moved to Order::updateOrderDetailTax
     *             because we need the full order object to do a good job here.
     *             Will no longer be supported after 1.6.1
     * @return bool
     */
    public function saveTaxCalculator(Order $order, $replace = false)
    {
        // Nothing to save
        if ($this->tax_calculator == null) {
            return true;
        }

        if (!($this->tax_calculator instanceof TaxCalculator)) {
            return false;
        }

        if (count($this->tax_calculator->taxes) == 0) {
            return true;
        }

        if ($order->total_products <= 0) {
            return true;
        }

        $shipping_tax_amount = 0;

        foreach ($order->getCartRules() as $cart_rule) {
            if ($cart_rule['free_shipping']) {
                $shipping_tax_amount = $order->total_shipping_tax_excl;
                break;
            }
        }

        $ratio = $this->unit_price_tax_excl / $order->total_products;
        $order_reduction_amount = ($order->total_discounts_tax_excl - $shipping_tax_amount) * $ratio;
        $discounted_price_tax_excl = $this->unit_price_tax_excl - $order_reduction_amount;

        $values = '';
        foreach ($this->tax_calculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $amount) {
            switch (Configuration::get('PS_ROUND_TYPE')) {
                case Order::ROUND_ITEM:
                    $unit_amount = (float)Tools::ps_round($amount, _PS_PRICE_COMPUTE_PRECISION_);
                    $total_amount = $unit_amount * $this->product_quantity;
                    break;
                case Order::ROUND_LINE:
                    $unit_amount = $amount;
                    $total_amount = Tools::ps_round($unit_amount * $this->product_quantity, _PS_PRICE_COMPUTE_PRECISION_);
                    break;
                case Order::ROUND_TOTAL:
                    $unit_amount = $amount;
                    $total_amount = $unit_amount * $this->product_quantity;
                    break;
            }

            $values .= '('.(int)$this->id.','.(int)$id_tax.','.(float)$unit_amount.','.(float)$total_amount.'),';
        }

        if ($replace) {
            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$this->id);
        }

        $values = rtrim($values, ',');
        $sql = 'INSERT INTO `'._DB_PREFIX_.'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)
				VALUES '.$values;

        return Db::getInstance()->execute($sql);
    }

    public function updateTaxAmount($order)
    {
        $this->setContext((int)$this->id_shop);
        $address = new Address((int)$order->id_address_tax);
        $tax_manager = TaxManagerFactory::getManager($address, (int)Product::getIdTaxRulesGroupByIdProduct((int)$this->product_id, $this->context));
        $this->tax_calculator = $tax_manager->getTaxCalculator();

        return $this->saveTaxCalculator($order, true);
    }

    /**
     * Get a detailed order list of an id_order
     * @param int $id_order
     * @return array
     */
    public static function getList($id_order)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` = '.(int)$id_order);
    }

    public function getTaxList()
    {
        return self::getTaxList($this->id);
    }

    public static function getTaxListStatic($id_order_detail)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'order_detail_tax`
					WHERE `id_order_detail` = '.(int)$id_order_detail;
        return Db::getInstance()->executeS($sql);
    }

    /*
     * Set virtual product information
     * @param array $product
     */
    protected function setVirtualProductInformation($product)
    {
        // Add some informations for virtual products
        $this->download_deadline = '0000-00-00 00:00:00';
        $this->download_hash = null;

        if ($id_product_download = ProductDownload::getIdFromIdProduct((int)$product['id_product'])) {
            $product_download = new ProductDownload((int)$id_product_download);
            $this->download_deadline = $product_download->getDeadLine();
            $this->download_hash = $product_download->getHash();

            unset($product_download);
        }
    }

    /**
     * Check the order status
     * @param array $product
     * @param int $id_order_state
     */
    protected function checkProductStock($product, $id_order_state)
    {
        if ($id_order_state != Configuration::get('PS_OS_CANCELED') && $id_order_state != Configuration::get('PS_OS_ERROR')) {

            /*
            *By webkul so that product quantity will not be decreased when product is ordered
             */
            /*$update_quantity = true;
            if (!StockAvailable::dependsOnStock($product['id_product'])) {
                $update_quantity = StockAvailable::updateQuantity($product['id_product'], $product['id_product_attribute'], -(int)$product['cart_quantity']);
            }

            if ($update_quantity) {
                $product['stock_quantity'] -= $product['cart_quantity'];
            }*/

            if ($product['stock_quantity'] < 0 && Configuration::get('PS_STOCK_MANAGEMENT')) {
                $this->outOfStock = true;
            }
            Product::updateDefaultAttribute($product['id_product']);
        }
    }

    /**
     * Apply tax to the product
     * @param object $order
     * @param array $product
     */
    protected function setProductTax(Order $order, $product)
    {
        $this->ecotax = Tools::convertPrice(floatval($product['ecotax']), intval($order->id_currency));

        // Exclude VAT
        if (!Tax::excludeTaxeOption()) {
            $this->setContext((int)$product['id_shop']);
            $this->id_tax_rules_group = (int)Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product'], $this->context);

            $tax_manager = TaxManagerFactory::getManager($this->vat_address, $this->id_tax_rules_group);
            $this->tax_calculator = $tax_manager->getTaxCalculator();
            $this->tax_computation_method = (int)$this->tax_calculator->computation_method;
        }

        $this->ecotax_tax_rate = 0;
        if (!empty($product['ecotax'])) {
            $this->ecotax_tax_rate = Tax::getProductEcotaxRate($order->id_address_tax);
        }
    }

    /**
     * Set specific price of the product
     * @param object $order
     */
    protected function setSpecificPrice(Order $order, $product = null)
    {
        $this->reduction_amount = 0.00;
        $this->reduction_percent = 0.00;
        $this->reduction_amount_tax_incl = 0.00;
        $this->reduction_amount_tax_excl = 0.00;

        if ($this->specificPrice) {
            switch ($this->specificPrice['reduction_type']) {
                case 'percentage':
                    $this->reduction_percent = (float)$this->specificPrice['reduction'] * 100;
                break;

                case 'amount':
                    $price = Tools::convertPrice($this->specificPrice['reduction'], $order->id_currency);
                    $this->reduction_amount = !$this->specificPrice['id_currency'] ? (float)$price : (float)$this->specificPrice['reduction'];
                    if ($product !== null) {
                        $this->setContext((int)$product['id_shop']);
                    }
                    $id_tax_rules = (int)Product::getIdTaxRulesGroupByIdProduct((int)$this->specificPrice['id_product'], $this->context);
                    $tax_manager = TaxManagerFactory::getManager($this->vat_address, $id_tax_rules);
                    $this->tax_calculator = $tax_manager->getTaxCalculator();

                    if ($this->specificPrice['reduction_tax']) {
                        $this->reduction_amount_tax_incl = $this->reduction_amount;
                        $this->reduction_amount_tax_excl = Tools::ps_round($this->tax_calculator->removeTaxes($this->reduction_amount), _PS_PRICE_COMPUTE_PRECISION_);
                    } else {
                        $this->reduction_amount_tax_incl = Tools::ps_round($this->tax_calculator->addTaxes($this->reduction_amount), _PS_PRICE_COMPUTE_PRECISION_);
                        $this->reduction_amount_tax_excl = $this->reduction_amount;
                    }
                break;
            }
        }
    }

    /**
     * Set detailed product price to the order detail
     * @param object $order
     * @param object $cart
     * @param array $product
     */
    protected function setDetailProductPrice(Order $order, Cart $cart, $product)
    {
        $this->setContext((int)$product['id_shop']);
        Product::getPriceStatic((int)$product['id_product'], true, (int)$product['id_product_attribute'], 6, null, false, true, $product['cart_quantity'], false, (int)$order->id_customer, (int)$order->id_cart, null, $specific_price, true, true, $this->context);
        $this->specificPrice = $specific_price;
        $this->original_product_price = Product::getPriceStatic($product['id_product'], false, (int)$product['id_product_attribute'], 6, null, false, false, 1, false, null, null, null, $null, true, true, $this->context);
        $this->product_price = $this->original_product_price;
        $this->unit_price_tax_incl = (float)$product['price_wt'];
        $this->unit_price_tax_excl = (float)$product['price'];
        $this->total_price_tax_incl = (float)$product['total_wt'];
        $this->total_price_tax_excl = (float)$product['total'];

        $this->purchase_supplier_price = (float)$product['wholesale_price'];
        if ($product['id_supplier'] > 0 && ($supplier_price = ProductSupplier::getProductPrice((int)$product['id_supplier'], $product['id_product'], $product['id_product_attribute'], true)) > 0) {
            $this->purchase_supplier_price = (float)$supplier_price;
        }

        $this->setSpecificPrice($order, $product);

        $this->group_reduction = (float)Group::getReduction((int)$order->id_customer);

        $shop_id = $this->context->shop->id;

        $quantity_discount = SpecificPrice::getQuantityDiscount((int)$product['id_product'], $shop_id,
        (int)$cart->id_currency, (int)$this->vat_address->id_country,
        (int)$this->customer->id_default_group, (int)$product['cart_quantity'], false, null, null, $null, true, true, $this->context);

        $unit_price = Product::getPriceStatic((int)$product['id_product'], true,
            ($product['id_product_attribute'] ? intval($product['id_product_attribute']) : null),
            2, null, false, true, 1, false, (int)$order->id_customer, null, null, $null, true, true, $this->context);
        $this->product_quantity_discount = 0.00;
        if ($quantity_discount) {
            $this->product_quantity_discount = $unit_price;
            if (Product::getTaxCalculationMethod((int)$order->id_customer) == PS_TAX_EXC) {
                $this->product_quantity_discount = Tools::ps_round($unit_price, 2);
            }

            if (isset($this->tax_calculator)) {
                $this->product_quantity_discount -= $this->tax_calculator->addTaxes($quantity_discount['price']);
            }
        }

        $this->discount_quantity_applied = (($this->specificPrice && $this->specificPrice['from_quantity'] > 1) ? 1 : 0);
    }

    /**
     * Create an order detail liable to an id_order
     * @param object $order
     * @param object $cart
     * @param array $product
     * @param int $id_order_status
     * @param int $id_order_invoice
     * @param bool $use_taxes set to false if you don't want to use taxes
     */
    protected function create(Order $order, Cart $cart, $product, $id_order_state, $id_order_invoice, $use_taxes = true, $id_warehouse = 0)
    {
        if ($use_taxes) {
            $this->tax_calculator = new TaxCalculator();
        }

        $this->id = null;

        $this->product_id = (int)$product['id_product'];
        $this->product_attribute_id = $product['id_product_attribute'] ? (int)$product['id_product_attribute'] : 0;
        $this->product_name = $product['name'].
            ((isset($product['attributes']) && $product['attributes'] != null) ?
                ' - '.$product['attributes'] : '');

        $this->product_quantity = (int)$product['cart_quantity'];
        $this->product_ean13 = empty($product['ean13']) ? null : pSQL($product['ean13']);
        $this->product_upc = empty($product['upc']) ? null : pSQL($product['upc']);
        $this->product_reference = empty($product['reference']) ? null : pSQL($product['reference']);
        $this->product_supplier_reference = empty($product['supplier_reference']) ? null : pSQL($product['supplier_reference']);
        $this->product_weight = $product['id_product_attribute'] ? (float)$product['weight_attribute'] : (float)$product['weight'];
        $this->id_warehouse = $id_warehouse;

        $product_quantity = (int)Product::getQuantity($this->product_id, $this->product_attribute_id);
        $this->product_quantity_in_stock = ($product_quantity - (int)$product['cart_quantity'] < 0) ?
            $product_quantity : (int)$product['cart_quantity'];

        $this->setVirtualProductInformation($product);
        $this->checkProductStock($product, $id_order_state);

        if ($use_taxes) {
            $this->setProductTax($order, $product);
        }
        $this->setShippingCost($order, $product);
        $this->setDetailProductPrice($order, $cart, $product);

        // Set order invoice id
        $this->id_order_invoice = (int)$id_order_invoice;

        // Set shop id
        $this->id_shop = (int)$product['id_shop'];

        // Add new entry to the table
        $this->save();

        if ($use_taxes) {
            $this->saveTaxCalculator($order);
        }
        unset($this->tax_calculator);
    }

    /**
     * Create a list of order detail for a specified id_order using cart
     * @param object $order
     * @param object $cart
     * @param int $id_order_status
     * @param int $id_order_invoice
     * @param bool $use_taxes set to false if you don't want to use taxes
    */
    public function createList(Order $order, Cart $cart, $id_order_state, $product_list, $id_order_invoice = 0, $use_taxes = true, $id_warehouse = 0)
    {
        $this->vat_address = new Address((int)$order->id_address_tax);
        $this->customer = new Customer((int)$order->id_customer);

        $this->id_order = $order->id;
        $this->outOfStock = false;

        foreach ($product_list as $product) {
            $this->create($order, $cart, $product, $id_order_state, $id_order_invoice, $use_taxes, $id_warehouse);
        }

        unset($this->vat_address);
        unset($products);
        unset($this->customer);
    }

    /**
     * Get the state of the current stock product
     * @return array
     */
    public function getStockState()
    {
        return $this->outOfStock;
    }

    /**
    * Set the additional shipping information
    *
    * @param Order $order
    * @param $product
    */
    public function setShippingCost(Order $order, $product)
    {
        $tax_rate = 0;

        $carrier = OrderInvoice::getCarrier((int)$this->id_order_invoice);
        if (isset($carrier) && Validate::isLoadedObject($carrier)) {
            $tax_rate = $carrier->getTaxesRate(new Address((int)$order->id_address_tax));
        }

        $this->total_shipping_price_tax_excl = (float)$product['additional_shipping_cost'];
        $this->total_shipping_price_tax_incl = (float)($this->total_shipping_price_tax_excl * (1 + ($tax_rate / 100)));
        $this->total_shipping_price_tax_incl = Tools::ps_round($this->total_shipping_price_tax_incl, 2);
    }

    public function getWsTaxes()
    {
        $query = new DbQuery();
        $query->select('id_tax as id');
        $query->from('order_detail_tax', 'tax');
        $query->leftJoin('order_detail', 'od', 'tax.`id_order_detail` = od.`id_order_detail`');
        $query->where('od.`id_order_detail` = '.(int)$this->id_order_detail);
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getCrossSells($id_product, $id_lang, $limit = 12)
    {
        if (!$id_product || !$id_lang) {
            return;
        }

        $front = true;
        if (!in_array(Context::getContext()->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        $orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT o.id_order
		FROM '._DB_PREFIX_.'orders o
		LEFT JOIN '._DB_PREFIX_.'order_detail od ON (od.id_order = o.id_order)
		WHERE o.valid = 1 AND od.product_id = '.(int)$id_product);

        if (count($orders)) {
            $list = '';
            foreach ($orders as $order) {
                $list .= (int)$order['id_order'].',';
            }
            $list = rtrim($list, ',');

            $order_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT DISTINCT od.product_id, p.id_product, pl.name, pl.link_rewrite, p.reference, i.id_image, product_shop.show_price,
				cl.link_rewrite category, p.ean13, p.out_of_stock, p.id_category_default '.(Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute' : '').'
				FROM '._DB_PREFIX_.'order_detail od
				LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)
				'.Shop::addSqlAssociation('product', 'p').
                (Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)Context::getContext()->shop->id.')':'').'
				LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = od.product_id'.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = od.product_id)
				'.Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1').'
				WHERE od.id_order IN ('.$list.')
					AND pl.id_lang = '.(int)$id_lang.'
					AND cl.id_lang = '.(int)$id_lang.'
					AND od.product_id != '.(int)$id_product.'
					AND product_shop.active = 1'
                    .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				ORDER BY RAND()
				LIMIT '.(int)$limit.'
			', true, false);

            $tax_calc = Product::getTaxCalculationMethod();
            if (is_array($order_products)) {
                foreach ($order_products as &$order_product) {
                    $order_product['image'] = Context::getContext()->link->getImageLink($order_product['link_rewrite'],
                        (int)$order_product['product_id'].'-'.(int)$order_product['id_image'], ImageType::getFormatedName('medium'));
                    $order_product['link'] = Context::getContext()->link->getProductLink((int)$order_product['product_id'],
                        $order_product['link_rewrite'], $order_product['category'], $order_product['ean13']);
                    if ($tax_calc == 0 || $tax_calc == 2) {
                        $order_product['displayed_price'] = Product::getPriceStatic((int)$order_product['product_id'], true, null);
                    } elseif ($tax_calc == 1) {
                        $order_product['displayed_price'] = Product::getPriceStatic((int)$order_product['product_id'], false, null);
                    }
                }
                return Product::getProductsProperties($id_lang, $order_products);
            }
        }
    }

    public function add($autodate = true, $null_values = false)
    {
        foreach ($this->def['fields'] as $field => $data) {
            if (!empty($data['required']) || !empty($data['lang'])) {
                continue;
            }
            if ($this->validateField($field, $this->$field) !== true) {
                $this->$field = '';
            }
        }

        $this->original_wholesale_price = $this->getWholeSalePrice();

        return parent::add($autodate = true, $null_values = false);
    }

    //return the product OR product attribute whole sale price
    public function getWholeSalePrice()
    {
        $product = new Product($this->product_id);
        $wholesale_price = $product->wholesale_price;

        if($this->product_attribute_id){
            $combination = new Combination((int)$this->product_attribute_id);
            if ($combination && $combination->wholesale_price != '0.000000') {
                $wholesale_price = $combination->wholesale_price;
            }
        }

        return $wholesale_price;
    }
}
