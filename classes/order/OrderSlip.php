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

class OrderSlipCore extends ObjectModel
{
    /** @var int */
    public $id_order_slip;

    /** @var int */
    public $id_customer;

    /** @var int */
    public $id_order;

    /** @var float */
    public $conversion_rate;

    /** @var float */
    public $total_products_tax_excl;

    /** @var float */
    public $total_products_tax_incl;

    /** @var float */
    public $total_shipping_tax_excl;

    /** @var float */
    public $total_shipping_tax_incl;

    /** @var int */
    public $amount;

    /** @var int */
    public $shipping_cost;

    /** @var int */
    public $shipping_cost_amount;

    /** @var int */
    public $partial;

    /** @var int */
    public $redeem_status = self::REDEEM_STATUS_ACTIVE;

    /** @var int */
    public $id_cart_rule;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var int */
    public $order_slip_type = 0;

    const REDEEM_STATUS_ACTIVE = 1;
    const REDEEM_STATUS_REDEEMED = 2;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_slip',
        'primary' => 'id_order_slip',
        'fields' => array(
            'id_customer' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'conversion_rate' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
            'total_products_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
            'total_products_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
            'total_shipping_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
            'total_shipping_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
            'amount' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'shipping_cost' =>            array('type' => self::TYPE_INT),
            'shipping_cost_amount' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'amount' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'partial' =>                array('type' => self::TYPE_INT),
            'redeem_status' =>          array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_cart_rule' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'order_slip_type' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),
        ),
    );

    protected $webserviceParameters = array(
        'objectNodeName' => 'order_slip',
        'objectsNodeName' => 'order_slips',
        'fields' => array(
            'id_customer' => array('xlink_resource'=> 'customers'),
            'id_order' => array('xlink_resource'=> 'orders'),
        ),
        'associations' => array(
            'order_slip_details' => array('resource' => 'order_slip_detail', 'setter' => false, 'virtual_entity' => true,
                'fields' => array(
                    'id' =>  array(),
                    'id_order_detail' => array('required' => true),
                    'product_quantity' => array('required' => true),
                    'amount_tax_excl' => array('required' => true),
                    'amount_tax_incl' => array('required' => true),
                )),
        ),
    );

    public function addSlipDetail($orderDetailList, $productQtyList)
    {
        foreach ($orderDetailList as $key => $id_order_detail) {
            if ($qty = (int)($productQtyList[$key])) {
                $order_detail = new OrderDetail((int)$id_order_detail);

                if (Validate::isLoadedObject($order_detail)) {
                    Db::getInstance()->insert('order_slip_detail', array(
                        'id_order_slip' => (int)$this->id,
                        'id_order_detail' => (int)$id_order_detail,
                        'product_quantity' => $qty,
                        'amount_tax_excl' => $order_detail->unit_price_tax_excl * $qty,
                        'amount_tax_incl' => $order_detail->unit_price_tax_incl * $qty
                    ));
                }
            }
        }
    }

    public static function getOrdersSlip($customer_id, $order_id = false)
    {
        return Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_slip`
		WHERE `id_customer` = '.(int)($customer_id).
        ($order_id ? ' AND `id_order` = '.(int)($order_id) : '').'
		ORDER BY `date_add` DESC');
    }

    public static function getOrdersSlipDetail($id_order_slip = false, $id_order_detail = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
        ($id_order_detail ? 'SELECT SUM(`product_quantity`) AS `total`' : 'SELECT *').
        'FROM `'._DB_PREFIX_.'order_slip_detail`'
        .($id_order_slip ? ' WHERE `id_order_slip` = '.(int)($id_order_slip) : '')
        .($id_order_detail ? ' WHERE `id_order_detail` = '.(int)($id_order_detail) : ''));
    }

    /**
     * @param int $orderSlipId
     * @param Order $order
     * @return array
     */
    public static function getOrdersSlipProducts($orderSlipId, $order)
    {
        // by webkul :: function return is customized as per booking info
        $cart_rules = $order->getCartRules(true);
        $productsRet = OrderSlip::getOrdersSlipDetail($orderSlipId);
        $order_details = $order->getProductsDetail();

        $orderDetailCustom = array();
        foreach ($order_details as $orderDtl) {
            $orderDetailCustom[$orderDtl['id_order_detail']] = $orderDtl;
        }

        $slipDetails = array();
        foreach ($productsRet as &$slipRow) {
            $objHtlBooking = new HotelBookingDetail($slipRow['id_htl_booking']);
            $slipRow['product_id'] = $objHtlBooking->id_product;
            $slipRow['date_from'] = $objHtlBooking->date_from;
            $slipRow['date_to'] = $objHtlBooking->date_to;
            $slipRow['hotel_name'] = $objHtlBooking->hotel_name;
            $slipRow['product_name'] = $objHtlBooking->room_type_name;
            $slipRow['room_num'] = $objHtlBooking->room_num;

            if (isset($orderDetailCustom[$slipRow['id_order_detail']])) {
                $slipRow = array_merge($orderDetailCustom[$slipRow['id_order_detail']], $slipRow);
            }

            $orderProduct = $order->getProducts(array($slipRow));
            $slipRow = array_merge(reset($orderProduct), $slipRow);

            $dateJoin = $slipRow['product_id'].'_'.strtotime($slipRow['date_from']).strtotime($slipRow['date_to']);
            if (isset($slipDetails[$dateJoin]['num_rooms'])) {
                $slipDetails[$dateJoin]['num_rooms'] += 1;
                $slipDetails[$dateJoin]['unit_price_tax_excl'] += $slipRow['unit_price_tax_excl'];
                $slipDetails[$dateJoin]['unit_price_tax_incl'] += $slipRow['unit_price_tax_incl'];
                $slipDetails[$dateJoin]['total_price_tax_excl'] += $slipRow['total_price_tax_excl'];
                $slipDetails[$dateJoin]['total_price_tax_incl'] += $slipRow['total_price_tax_incl'];
                $slipDetails[$dateJoin]['amount_tax_excl'] += $slipRow['amount_tax_excl'];
                $slipDetails[$dateJoin]['amount_tax_incl'] += $slipRow['amount_tax_incl'];

                $slipDetails[$dateJoin]['unit_price_tax_excl'] /= $slipDetails[$dateJoin]['num_rooms'];
                $slipDetails[$dateJoin]['unit_price_tax_incl'] /= $slipDetails[$dateJoin]['num_rooms'];
            } else {
                $slipDetails[$dateJoin] = $slipRow;
                $slipDetails[$dateJoin]['num_rooms'] = 1;
            }
        }

        return $slipDetails;
    }

    /**
     *
     * Get resume of all refund for one product line
     * @param $id_order_detail
     */
    public static function getProductSlipResume($id_order_detail)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT SUM(product_quantity) product_quantity, SUM(amount_tax_excl) amount_tax_excl, SUM(amount_tax_incl) amount_tax_incl
			FROM `'._DB_PREFIX_.'order_slip_detail`
			WHERE `id_order_detail` = '.(int)$id_order_detail);
    }

    /**
     *
     * Get refund details for one product line
     * @param $id_order_detail
     */
    public static function getProductSlipDetail($id_order_detail)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT product_quantity, amount_tax_excl, amount_tax_incl, date_add
			FROM `'._DB_PREFIX_.'order_slip_detail` osd
			LEFT JOIN `'._DB_PREFIX_.'order_slip` os
			ON os.id_order_slip = osd.id_order_slip
			WHERE osd.`id_order_detail` = '.(int)$id_order_detail);
    }

    public function getProducts()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *, osd.product_quantity
		FROM `'._DB_PREFIX_.'order_slip_detail` osd
		INNER JOIN `'._DB_PREFIX_.'order_detail` od ON osd.id_order_detail = od.id_order_detail
		WHERE osd.`id_order_slip` = '.(int)$this->id);

        $order = new Order($this->id_order);
        $products = array();
        foreach ($result as $row) {
            $order->setProductPrices($row);
            $products[] = $row;
        }
        return $products;
    }

    public static function getSlipsIdByDate($dateFrom, $dateTo)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `id_order_slip`
		FROM `'._DB_PREFIX_.'order_slip` os
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = os.`id_order`)
		WHERE os.`date_add` BETWEEN \''.pSQL($dateFrom).' 00:00:00\' AND \''.pSQL($dateTo).' 23:59:59\'
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
		ORDER BY os.`date_add` ASC');

        $slips = array();
        foreach ($result as $slip) {
            $slips[] = (int)$slip['id_order_slip'];
        }
        return $slips;
    }

    /**
     * @deprecated since 1.6.0.10 use OrderSlip::create() instead
     *
     */
    public static function createOrderSlip($order, $productList, $qtyList, $shipping_cost = false)
    {
        Tools::displayAsDeprecated();

        $product_list = array();
        foreach ($productList as $id_order_detail) {
            $order_detail = new OrderDetail((int)$id_order_detail);
            $product_list[$id_order_detail] = array(
                'id_order_detail' => $id_order_detail,
                'quantity' => $qtyList[$id_order_detail],
                'unit_price' => $order_detail->unit_price_tax_excl,
                'amount' => $order_detail->unit_price_tax_incl * $qtyList[$id_order_detail],
            );

            $shipping = $shipping_cost ? null : false;
        }

        return OrderSlip::create($order, $product_list, $shipping);
    }

    public static function create(Order $order, $booking_list, $shipping_cost = false, $amount = 0, $amount_choosen = false, $add_tax = true)
    {
        $currency = new Currency((int)$order->id_currency);
        $order_slip = new OrderSlip();
        $order_slip->id_customer = (int)$order->id_customer;
        $order_slip->id_order = (int)$order->id;
        $order_slip->conversion_rate = $currency->conversion_rate;

        if ($add_tax) {
            $add_or_remove = 'add';
            $inc_or_ex_1 = 'excl';
            $inc_or_ex_2 = 'incl';
        } else {
            $add_or_remove = 'remove';
            $inc_or_ex_1 = 'incl';
            $inc_or_ex_2 = 'excl';
        }

        $order_slip->{'total_shipping_tax_'.$inc_or_ex_1} = 0;
        $order_slip->{'total_shipping_tax_'.$inc_or_ex_2} = 0;
        $order_slip->partial = 0;

        if ($shipping_cost !== false) {
            $order_slip->shipping_cost = true;
            $carrier = new Carrier((int)$order->id_carrier);
            $address = Address::initialize($order->id_address_delivery, false);
            $tax_calculator = $carrier->getTaxCalculator($address);
            $order_slip->{'total_shipping_tax_'.$inc_or_ex_1} = ($shipping_cost === null ? $order->{'total_shipping_tax_'.$inc_or_ex_1} : (float)$shipping_cost);

            if ($tax_calculator instanceof TaxCalculator) {
                $order_slip->{'total_shipping_tax_'.$inc_or_ex_2} = Tools::ps_round($tax_calculator->{$add_or_remove.'Taxes'}($order_slip->{'total_shipping_tax_'.$inc_or_ex_1}), _PS_PRICE_COMPUTE_PRECISION_);
            } else {
                $order_slip->{'total_shipping_tax_'.$inc_or_ex_2} = $order_slip->{'total_shipping_tax_'.$inc_or_ex_1};
            }
        } else {
            $order_slip->shipping_cost = false;
        }

        $order_slip->amount = 0;
        $order_slip->{'total_products_tax_'.$inc_or_ex_1} = 0;
        $order_slip->{'total_products_tax_'.$inc_or_ex_2} = 0;

        $total_products = array();
        foreach ($booking_list as &$booking) {
            $objHtlBooking = new HotelBookingDetail($booking['id_htl_booking']);
            $order_detail = new OrderDetail((int)$objHtlBooking->id_order_detail);

            $price = (float)$booking['unit_price'];
            $numDays = (int)$booking['num_days'];

            $order_slip_resume = OrderSlip::getProductSlipResume((int)$order_detail->id);

            if ($numDays + $order_slip_resume['product_quantity'] > $order_detail->product_quantity) {
                $numDays = $order_detail->product_quantity - $order_slip_resume['product_quantity'];
            }

            if ($numDays == 0) {
                continue;
            }

            if (!Tools::isSubmit('cancelProduct') && $order->hasBeenPaid()) {
                $order_detail->product_quantity_refunded += $numDays;
            }

            $address = Address::initialize($order->id_address_tax, false);
            $id_address = (int)$address->id;
            $id_tax_rules_group = Product::getIdTaxRulesGroupByIdProduct((int)$order_detail->product_id);
            $tax_calculator = TaxManagerFactory::getManager($address, $id_tax_rules_group)->getTaxCalculator();

            $order_slip->{'total_products_tax_'.$inc_or_ex_1} += $price * $numDays;

            if (in_array(Configuration::get('PS_ROUND_TYPE'), array(Order::ROUND_ITEM, Order::ROUND_LINE))) {
                if (!isset($total_products[$id_tax_rules_group])) {
                    $total_products[$id_tax_rules_group] = 0;
                }
            } else {
                if (!isset($total_products[$id_tax_rules_group.'_'.$id_address])) {
                    $total_products[$id_tax_rules_group.'_'.$id_address] = 0;
                }
            }

            $product_tax_incl_line = Tools::ps_round($tax_calculator->{$add_or_remove.'Taxes'}($price) * $numDays, _PS_PRICE_COMPUTE_PRECISION_);

            switch (Configuration::get('PS_ROUND_TYPE')) {
                case Order::ROUND_ITEM:
                    $product_tax_incl = Tools::ps_round($tax_calculator->{$add_or_remove.'Taxes'}($price), _PS_PRICE_COMPUTE_PRECISION_) * $numDays;
                    $total_products[$id_tax_rules_group] += $product_tax_incl;
                    break;
                case Order::ROUND_LINE:
                    $product_tax_incl = $product_tax_incl_line;
                    $total_products[$id_tax_rules_group] += $product_tax_incl;
                    break;
                case Order::ROUND_TOTAL:
                    $product_tax_incl = $product_tax_incl_line;
                    $total_products[$id_tax_rules_group.'_'.$id_address] += $price * $numDays;
                    break;
            }

            $booking['unit_price_tax_'.$inc_or_ex_1] = $price;
            $booking['unit_price_tax_'.$inc_or_ex_2] = Tools::ps_round($tax_calculator->{$add_or_remove.'Taxes'}($price), _PS_PRICE_COMPUTE_PRECISION_);
            $booking['total_price_tax_'.$inc_or_ex_1] = Tools::ps_round($price * $numDays, _PS_PRICE_COMPUTE_PRECISION_);
            $booking['total_price_tax_'.$inc_or_ex_2] = Tools::ps_round($product_tax_incl, _PS_PRICE_COMPUTE_PRECISION_);
        }

        unset($product);

        foreach ($total_products as $key => $price) {
            if (Configuration::get('PS_ROUND_TYPE') == Order::ROUND_TOTAL) {
                $tmp = explode('_', $key);
                $address = Address::initialize((int)$tmp[1], true);
                $tax_calculator = TaxManagerFactory::getManager($address, $tmp[0])->getTaxCalculator();
                $order_slip->{'total_products_tax_'.$inc_or_ex_2} += Tools::ps_round($tax_calculator->{$add_or_remove.'Taxes'}($price), _PS_PRICE_COMPUTE_PRECISION_);
            } else {
                $order_slip->{'total_products_tax_'.$inc_or_ex_2} += $price;
            }
        }

        $order_slip->{'total_products_tax_'.$inc_or_ex_2} -= (float)$amount && !$amount_choosen ? (float)$amount : 0;
        $order_slip->amount = $amount_choosen ? (float)$amount : $order_slip->{'total_products_tax_'.$inc_or_ex_1};

        if ((float)$amount && !$amount_choosen) {
            $order_slip->order_slip_type = 1;
        }
        if (((float)$amount && $amount_choosen)) {
            $order_slip->order_slip_type = 2;
        }

        if (!$order_slip->add()) {
            return false;
        }

        foreach ($booking_list as $bookingRow) {
            $order_slip->addProductOrderSlip($bookingRow);
        }

        return $order_slip->id;
    }

    protected function addProductOrderSlip($booking)
    {
        return Db::getInstance()->insert('order_slip_detail', array(
            'id_order_slip' => (int)$this->id,
            'id_htl_booking' => (int)$booking['id_htl_booking'],
            'id_order_detail' => (int)$booking['id_order_detail'],
            'product_quantity' => $booking['quantity'],
            'unit_price_tax_excl' => $booking['unit_price_tax_excl'],
            'unit_price_tax_incl' => $booking['unit_price_tax_incl'],
            'total_price_tax_excl' => $booking['total_price_tax_excl'],
            'total_price_tax_incl' => $booking['total_price_tax_incl'],
            'amount_tax_excl' => $booking['total_price_tax_excl'],
            'amount_tax_incl' => $booking['total_price_tax_incl']
        ));
    }

    public static function createPartialOrderSlip($order, $amount, $shipping_cost_amount, $order_detail_list)
    {
        $currency = new Currency($order->id_currency);
        $orderSlip = new OrderSlip();
        $orderSlip->id_customer = (int)$order->id_customer;
        $orderSlip->id_order = (int)$order->id;
        $orderSlip->amount = (float)$amount;
        $orderSlip->shipping_cost = false;
        $orderSlip->shipping_cost_amount = (float)$shipping_cost_amount;
        $orderSlip->conversion_rate = $currency->conversion_rate;
        $orderSlip->partial = 1;
        if (!$orderSlip->add()) {
            return false;
        }

        $orderSlip->addPartialSlipDetail($order_detail_list);
        return true;
    }

    public function addPartialSlipDetail($order_detail_list)
    {
        foreach ($order_detail_list as $id_order_detail => $tab) {
            $order_detail = new OrderDetail($id_order_detail);
            $order_slip_resume = OrderSlip::getProductSlipResume($id_order_detail);

            if ($tab['amount'] + $order_slip_resume['amount_tax_incl'] > $order_detail->total_price_tax_incl) {
                $tab['amount'] = $order_detail->total_price_tax_incl - $order_slip_resume['amount_tax_incl'];
            }

            if ($tab['amount'] == 0) {
                continue;
            }

            if ($tab['quantity'] + $order_slip_resume['product_quantity'] > $order_detail->product_quantity) {
                $tab['quantity'] = $order_detail->product_quantity - $order_slip_resume['product_quantity'];
            }

            $tab['amount_tax_excl'] = $tab['amount_tax_incl'] = $tab['amount'];

            $id_tax = (int)Db::getInstance()->getValue('
				SELECT `id_tax`
				FROM `'._DB_PREFIX_.'order_detail_tax`
				WHERE `id_order_detail` = '.(int)$id_order_detail
            );

            if ($id_tax > 0) {
                $rate = (float)Db::getInstance()->getValue('
					SELECT `rate`
					FROM `'._DB_PREFIX_.'tax`
					WHERE `id_tax` = '.(int)$id_tax
                );

                if ($rate > 0) {
                    $rate = 1 + ($rate / 100);
                    $tab['amount_tax_excl'] = $tab['amount_tax_excl'] / $rate;
                }
            }

            if ($tab['quantity'] > 0 && $tab['quantity'] > $order_detail->product_quantity_refunded) {
                $order_detail->product_quantity_refunded = $tab['quantity'];
                $order_detail->save();
            }

            $insert_order_slip = array(
                'id_order_slip' => (int)$this->id,
                'id_order_detail' => (int)$id_order_detail,
                'product_quantity' => (int)$tab['quantity'],
                'amount_tax_excl' => (float)$tab['amount_tax_excl'],
                'amount_tax_incl' => (float)$tab['amount_tax_incl'],
            );

            Db::getInstance()->insert('order_slip_detail', $insert_order_slip);
        }
    }

    public function getEcoTaxTaxesBreakdown()
    {
        $ecotax_detail = array();
        foreach ($this->getOrdersSlipDetail((int)$this->id) as $order_slip_details) {
            $row = Db::getInstance()->getRow('
					SELECT `ecotax_tax_rate` as `rate`, `ecotax` as `ecotax_tax_excl`, `ecotax` as `ecotax_tax_incl`, `product_quantity`
					FROM `'._DB_PREFIX_.'order_detail`
					WHERE `id_order_detail` = '.(int)$order_slip_details['id_order_detail']
                );

            if (!isset($ecotax_detail[$row['rate']])) {
                $ecotax_detail[$row['rate']] = array('ecotax_tax_incl' => 0, 'ecotax_tax_excl' => 0, 'rate' => $row['rate']);
            }

            $ecotax_detail[$row['rate']]['ecotax_tax_incl'] += Tools::ps_round(($row['ecotax_tax_excl'] * $order_slip_details['product_quantity']) + ($row['ecotax_tax_excl'] * $order_slip_details['product_quantity'] * $row['rate'] / 100), 2);
            $ecotax_detail[$row['rate']]['ecotax_tax_excl'] += Tools::ps_round($row['ecotax_tax_excl'] * $order_slip_details['product_quantity'], 2);
        }

        return $ecotax_detail;
    }

    /**
     * generate voucher and return id of the created rule if successful, false otherwise
     */
    public function generateVoucher($sendMail = true)
    {
        if (!Validate::isLoadedObject($this)) {
            return false;
        }

        if ($this->redeem_status != self::REDEEM_STATUS_ACTIVE) {
            return false;
        }

        $objOrder = new Order($this->id_order);
        if (!Validate::isLoadedObject($objOrder)) {
            return false;
        }

        $objCustomer = new Customer($this->id_customer);
        if (!Validate::isLoadedObject($objCustomer)) {
            return false;
        }

        $context = Context::getContext();
        $objCartRule = new CartRule();
        $languages = Language::getLanguages();

        $creditSlipPrefix = Configuration::get('PS_CREDIT_SLIP_PREFIX', $context->language->id);
        $creditSlipID = sprintf(('%1$s%2$06d'), $creditSlipPrefix, (int) $this->id_order_slip);

        $dateFrom = $this->date_add;
        $dateTo = date('Y-m-d H:i:s', strtotime($dateFrom) + (3600 * 24 * 365.25)); /* 1 year */

        foreach ($languages as $language) {
            $objCartRule->name[$language['id_lang']] = sprintf('Voucher for credit slip #%s', $creditSlipID);
        }

        $objCartRule->description = sprintf('Order: #%s', $objOrder->id);
        $objCartRule->code = 'CS'.(int) ($this->id_order_slip).'C'.(int) ($objCustomer->id).'O'.(int) ($objOrder->id);
        $objCartRule->quantity = 1;
        $objCartRule->quantity_per_user = 1;
        $objCartRule->id_customer = $objCustomer->id;
        $objCartRule->date_from = $dateFrom;
        $objCartRule->date_to = $dateTo;
        $objCartRule->highlight = 1;
        $objCartRule->active = 1;
        $objCartRule->reduction_amount = $this->amount;
        $objCartRule->reduction_tax = true;
        $objCartRule->minimum_amount_currency = $objOrder->id_currency;
        $objCartRule->reduction_currency = $objOrder->id_currency;

        if (!$objCartRule->save()) {
            return false;
        }

        $this->redeem_status = self::REDEEM_STATUS_REDEEMED;
        $this->id_cart_rule = (int) $objCartRule->id;
        $this->save();

        if ($sendMail) {
            $objCartRule = new CartRule($this->id_cart_rule);
            $objCustomer = new Customer($objCartRule->id_customer);

            $objCurrency = new Currency($objCartRule->reduction_currency, $context->language->id);
            $mailVars['{firstname}'] = $objCustomer->firstname;
            $mailVars['{lastname}'] = $objCustomer->lastname;
            $mailVars['{credit_slip_id}'] = $creditSlipID;
            $mailVars['{voucher_code}'] = $objCartRule->code;
            $mailVars['{voucher_amount}'] = Tools::displayPrice($objCartRule->reduction_amount, $objCurrency, false);

            Mail::Send(
                $context->language->id,
                'credit_slip_voucher',
                sprintf(Mail::l('New voucher for your credit slip #%s', $context->language->id), $creditSlipID),
                $mailVars,
                $objCustomer->email,
                $objCustomer->firstname.' '.$objCustomer->lastname,
                null,
                null,
                null,
                null,
                _PS_MAIL_DIR_,
                true
            );
        }

        return $this->id_cart_rule;
    }

    public function getWsOrderSlipDetails()
    {
        $query = 'SELECT id_order_slip as id, id_order_detail, product_quantity, amount_tax_excl, amount_tax_incl
		FROM `'._DB_PREFIX_.'order_slip_detail`
		WHERE id_order_slip = '.(int)$this->id;
        $result = Db::getInstance()->executeS($query);
        return $result;
    }

    public function setWsOrderSlipDetails($values)
    {
        if (Db::getInstance()->execute('DELETE from `'._DB_PREFIX_.'order_slip_detail` where id_order_slip = '.(int)$this->id)) {
            $query = 'INSERT INTO `'._DB_PREFIX_.'order_slip_detail`(`id_order_slip`, `id_order_detail`, `product_quantity`, `amount_tax_excl`, `amount_tax_incl`) VALUES ';

            foreach ($values as $value) {
                $query .= '('.(int)$this->id.', '.(int)$value['id_order_detail'].', '.(int)$value['product_quantity'].', '.
                    (isset($value['amount_tax_excl']) ? (float)$value['amount_tax_excl'] : 'NULL').', '.
                    (isset($value['amount_tax_incl']) ? (float)$value['amount_tax_incl'] : 'NULL').
                    '),';
            }
            $query = rtrim($query, ',');
            Db::getInstance()->execute($query);
        }
        return true;
    }
}
