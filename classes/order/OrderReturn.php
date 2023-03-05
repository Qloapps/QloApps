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

class OrderReturnCore extends ObjectModel
{
    /** @var int */
    public $id_order_return;

    /** @var int */
    public $id_customer;

    /** @var int */
    public $id_order;

    /** @var string id of the refund transaction */
    public $id_transaction;
    /** @var string payment mode of the refund transaction */
    public $payment_mode;
    /** @var float amount of the refund transaction */
    public $refunded_amount;

    /** @var int current state of the refund*/
    public $state;

    /** @var string message content */
    public $question;

    /** @var int if request raised by the admin */
    public $by_admin;

    /** @var int id_cart_rule or id_order_slip */
    public $id_return_type;

    /** @var int whether $id_return_type is cart_rule or order_slip */
    public $return_type;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /** possible values for $return_type */
    const RETURN_TYPE_CART_RULE = 1;
    const RETURN_TYPE_ORDER_SLIP = 2;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_return',
        'primary' => 'id_order_return',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'question' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'id_transaction' => array('type' => self::TYPE_STRING),
            'payment_mode' => array('type' => self::TYPE_STRING),
            'refunded_amount' => array('type' => self::TYPE_FLOAT),
            'state' => array('type' => self::TYPE_INT),
            'by_admin' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_return_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'return_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function addReturnDetail($order_detail_list, $product_qty_list, $customization_ids, $customization_qty_input)
    {
        /* Classic product return */
        if ($order_detail_list) {
            foreach ($order_detail_list as $key => $order_detail) {
                if ($qty = (int)$product_qty_list[$key]) {
                    Db::getInstance()->insert('order_return_detail', array('id_order_return' => (int)$this->id, 'id_order_detail' => (int)$order_detail, 'product_quantity' => $qty, 'id_customization' => 0));
                }
            }
        }
        /* Customized product return */
        if ($customization_ids) {
            foreach ($customization_ids as $order_detail_id => $customizations) {
                foreach ($customizations as $customization_id) {
                    if ($quantity = (int)$customization_qty_input[(int)$customization_id]) {
                        Db::getInstance()->insert('order_return_detail', array('id_order_return' => (int)$this->id, 'id_order_detail' => (int)$order_detail_id, 'product_quantity' => $quantity, 'id_customization' => (int)$customization_id));
                    }
                }
            }
        }
    }

    public function checkEnoughProduct($order_detail_list, $product_qty_list, $customization_ids, $customization_qty_input)
    {
        $order = new Order((int)$this->id_order);
        if (!Validate::isLoadedObject($order)) {
            die(Tools::displayError());
        }
        $products = $order->getProducts();
        /* Products already returned */
        $order_return = OrderReturn::getOrdersReturn($order->id_customer, $order->id, true);
        foreach ($order_return as $or) {
            $order_return_products = OrderReturn::getOrdersReturnProducts($or['id_order_return'], $order);
            foreach ($order_return_products as $key => $orp) {
                $products[$key]['product_quantity'] -= (int)$orp['product_quantity'];
            }
        }
        /* Quantity check */
        if ($order_detail_list) {
            foreach (array_keys($order_detail_list) as $key) {
                if ($qty = (int)$product_qty_list[$key]) {
                    if ($products[$key]['product_quantity'] - $qty < 0) {
                        return false;
                    }
                }
            }
        }
        /* Customization quantity check */
        if ($customization_ids) {
            $ordered_customizations = Customization::getOrderedCustomizations((int)$order->id_cart);
            foreach ($customization_ids as $customizations) {
                foreach ($customizations as $customization_id) {
                    $customization_id = (int)$customization_id;
                    if (!isset($ordered_customizations[$customization_id])) {
                        return false;
                    }
                    $quantity = (isset($customization_qty_input[$customization_id]) ? (int)$customization_qty_input[$customization_id] : 0);
                    if ((int)$ordered_customizations[$customization_id]['quantity'] - $quantity < 0) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function hasBeenCompleted()
    {
        if (Validate::isLoadedObject($objReturnState = new OrderReturnState($this->state))) {
            if ($objReturnState->denied || $objReturnState->refunded) {
                return true;
            }
        }
        return false;
    }

    public function countProduct()
    {
        if (!$data = Db::getInstance()->getRow('
		SELECT COUNT(`id_order_return`) AS total
		FROM `'._DB_PREFIX_.'order_return_detail`
		WHERE `id_order_return` = '.(int)$this->id)) {
            return false;
        }
        return (int)($data['total']);
    }

    public function getOrderRefundRequestedBookings($idOrder, $idOrderReturn = 0, $onlyBookingIds = 0, $customerView = 0)
    {
        $sql = 'SELECT hbd.*, ord.* FROM `'._DB_PREFIX_.'order_return` orr';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'order_return_detail` ord ON (orr.`id_order_return` = ord.`id_order_return`)';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id` = ord.`id_htl_booking`)';
        $sql .= ' WHERE orr.`id_order` = '.(int)$idOrder;

        if ($idOrderReturn) {
            $sql .= ' AND ord.`id_order_return` = '.(int)$idOrderReturn;
        }

        if ($returnDetails = Db::getInstance()->executeS($sql)) {
            if ($onlyBookingIds) {
                return array_column($returnDetails, 'id_htl_booking');
            }

            if ($customerView) {
                $returnsCustView = array();
            }

            $objBookingDemands = new HotelBookingDemands();
            $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
            foreach ($returnDetails as &$bookingRow) {
                $bookingRow['extra_demands_price_tax_incl'] = $objBookingDemands->getRoomTypeBookingExtraDemands(
                    $bookingRow['id_order'],
                    $bookingRow['id_product'],
                    $bookingRow['id_room'],
                    $bookingRow['date_from'],
                    $bookingRow['date_to'],
                    0,
                    1
                );
                $bookingRow['extra_demands_price_tax_excl'] = $objBookingDemands->getRoomTypeBookingExtraDemands(
                    $bookingRow['id_order'],
                    $bookingRow['id_product'],
                    $bookingRow['id_room'],
                    $bookingRow['date_from'],
                    $bookingRow['date_to'],
                    0,
                    1,
                    0
                );
                $bookingRow['additional_services_tax_excl'] = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
                    $id_order,
                    0,
                    0,
                    $type_value['product_id'],
                    $data_v['date_from'],
                    $data_v['date_to'],
                    $data_v['id_room'],
                    1,
                    0
                );

                if ($customerView) {
                    $dateJoin = $bookingRow['id_product'].'_'.strtotime($bookingRow['date_from']).strtotime($bookingRow['date_to']);
                    if (isset($returnsCustView[$dateJoin]['num_rooms'])) {
                        $returnsCustView[$dateJoin]['num_rooms'] += 1;
                        $returnsCustView[$dateJoin]['refunded_amount'] += $bookingRow['refunded_amount'];
                        $returnsCustView[$dateJoin]['total_price_tax_incl'] += $bookingRow['total_price_tax_incl'];
                        $returnsCustView[$dateJoin]['total_paid_amount'] += $bookingRow['total_paid_amount'];
                        $returnsCustView[$dateJoin]['extra_demands_price_tax_incl'] += $bookingRow['extra_demands_price_tax_incl'];
                        $returnsCustView[$dateJoin]['extra_demands_price_tax_excl'] += $bookingRow['extra_demands_price_tax_excl'];
                    } else {
                        unset($bookingRow['id_room']);
                        unset($bookingRow['room_num']);
                        unset($bookingRow['id_htl_booking']);
                        $returnsCustView[$dateJoin] = $bookingRow;
                        $returnsCustView[$dateJoin]['num_rooms'] = 1;
                    }
                }
            }
            if ($customerView) {
                return $returnsCustView;
            }
        }
        return $returnDetails;
    }

    public static function getOrdersReturn($customer_id, $order_id = false, $no_denied = false, $only_customer = 0, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $sql = 'SELECT *, orr.*, COUNT(ord.`id_order_return_detail`) AS total_rooms
        FROM `'._DB_PREFIX_.'order_return` orr
        LEFT JOIN `'._DB_PREFIX_.'order_return_detail` ord
        ON (ord.`id_order_return` = orr.`id_order_return`)
        LEFT JOIN `'._DB_PREFIX_.'order_slip` ors
        ON (ors.`id_order_slip` = orr.`id_return_type` AND orr.`return_type` = '.(int) self::RETURN_TYPE_ORDER_SLIP.')
        WHERE orr.`id_customer` = '.(int)$customer_id.
        ($only_customer ? ' AND orr.`by_admin` = 0' : '').
        ($order_id ? ' AND orr.`id_order` = '.(int)$order_id : '').
        ($no_denied ? ' AND orr.`state` != 4' : '').'
        GROUP BY orr.`id_order_return`
        ORDER BY orr.`date_add` DESC';
        $data = Db::getInstance()->executeS($sql);
        foreach ($data as $k => $or) {
            $state = new OrderReturnState($or['state']);
            $data[$k]['state_name'] = $state->name[$context->language->id];
            $data[$k]['state_color'] = $state->color;
            $data[$k]['reference'] = Order::getUniqReferenceOf($or['id_order']);
        }

        return $data;
    }

    public static function getOrdersReturnDetail($idOrder, $idOrderReturn = 0, $idHtlBooking = 0, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT orr.*, ord.*, orsl.`name`, ors.`refunded`, ors.`denied`, ors.`color` FROM `'._DB_PREFIX_.'order_return` orr';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'order_return_detail` ord ON (orr.`id_order_return` = ord.`id_order_return`)';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'order_return_state` ors ON (orr.`state` = ors.`id_order_return_state`)';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'order_return_state_lang` orsl ON (ors.`id_order_return_state` = orsl.`id_order_return_state` AND orsl.`id_lang` = '.(int)$idLang.')';
        $sql .= ' WHERE orr.`id_order` = '.(int)$idOrder;

        if ($idOrderReturn) {
            $sql .= ' AND ord.`id_order_return` = '.(int)$idOrderReturn;
        }

        if ($idHtlBooking) {
            $sql .= ' AND ord.`id_htl_booking` = '.(int)$idHtlBooking;
        }

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @param int $order_return_id
     * @param Order $order
     * @return array
     */
    public static function getOrdersReturnProducts($order_return_id, $order)
    {
        $products_ret = OrderReturn::getOrdersReturnDetail($order_return_id);
        $products = $order->getProducts();
        $tmp = array();
        foreach ($products_ret as $return_detail) {
            $tmp[$return_detail['id_order_detail']]['quantity'] = isset($tmp[$return_detail['id_order_detail']]['quantity']) ? $tmp[$return_detail['id_order_detail']]['quantity'] + (int)$return_detail['product_quantity'] : (int)$return_detail['product_quantity'];
            $tmp[$return_detail['id_order_detail']]['customizations'] = (int)$return_detail['id_customization'];
        }
        $res_tab = array();
        foreach ($products as $key => $product) {
            if (isset($tmp[$product['id_order_detail']])) {
                $res_tab[$key] = $product;
                $res_tab[$key]['product_quantity'] = $tmp[$product['id_order_detail']]['quantity'];
                $res_tab[$key]['customizations'] = $tmp[$product['id_order_detail']]['customizations'];
            }
        }
        return $res_tab;
    }

    public static function getReturnedCustomizedProducts($id_order)
    {
        $returns = Customization::getReturnedCustomizations($id_order);
        $order = new Order((int)$id_order);
        if (!Validate::isLoadedObject($order)) {
            die(Tools::displayError());
        }
        $products = $order->getProducts();

        foreach ($returns as &$return) {
            $return['product_id'] = (int)$products[(int)$return['id_order_detail']]['product_id'];
            $return['product_attribute_id'] = (int)$products[(int)$return['id_order_detail']]['product_attribute_id'];
            $return['name'] = $products[(int)$return['id_order_detail']]['product_name'];
            $return['reference'] = $products[(int)$return['id_order_detail']]['product_reference'];
            $return['id_address_delivery'] = $products[(int)$return['id_order_detail']]['id_address_delivery'];
        }
        return $returns;
    }

    public static function deleteOrderReturnDetail($id_order_return, $id_order_detail, $id_customization = 0)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_return_detail` WHERE `id_order_detail` = '.(int)$id_order_detail.' AND `id_order_return` = '.(int)$id_order_return.' AND `id_customization` = '.(int)$id_customization);
    }

    /**
     *
     * Get return details for one product line
     * @param $id_order_detail
     */
    public static function getProductReturnDetail($id_order_detail)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT product_quantity, date_add, orsl.name as state
			FROM `'._DB_PREFIX_.'order_return_detail` ord
			LEFT JOIN `'._DB_PREFIX_.'order_return` o
			ON o.id_order_return = ord.id_order_return
			LEFT JOIN `'._DB_PREFIX_.'order_return_state_lang` orsl
			ON orsl.id_order_return_state = o.state AND orsl.id_lang = '.(int)Context::getContext()->language->id.'
			WHERE ord.`id_order_detail` = '.(int)$id_order_detail);
    }

    /**
     *
     * Add returned quantity to products list
     * @param array $products
     * @param int $id_order
     */
    public static function addReturnedQuantity(&$products, $id_order)
    {
        $details = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT od.id_order_detail, GREATEST(od.product_quantity_return, IFNULL(SUM(ord.product_quantity),0)) as qty_returned
			FROM '._DB_PREFIX_.'order_detail od
			LEFT JOIN '._DB_PREFIX_.'order_return_detail ord
			ON ord.id_order_detail = od.id_order_detail
			WHERE od.id_order = '.(int)$id_order.'
			GROUP BY od.id_order_detail'
        );
        if (!$details) {
            return;
        }

        $detail_list = array();
        foreach ($details as $detail) {
            $detail_list[$detail['id_order_detail']] = $detail;
        }

        foreach ($products as &$product) {
            if (isset($detail_list[$product['id_order_detail']]['qty_returned'])) {
                $product['qty_returned'] = $detail_list[$product['id_order_detail']]['qty_returned'];
            }
        }
    }

    /**
     * Sets the new order return state
     * @param int $newOrderReturnState
     */
    public function changeIdOrderReturnState($newOrderReturnState, $idLang = 0)
    {
        $objOrder = new Order($this->id_order);
        if (!$idLang) {
            $idLang = $objOrder->id_lang;
        }
        if (Validate::isLoadedObject(
            $objOrderReturnState = new OrderReturnState((int)$newOrderReturnState, $idLang)
        )) {
            if ($this->state != $newOrderReturnState) {
                $this->state = $newOrderReturnState;
                $this->save();

                if ($objOrderReturnState->send_email_to_customer
                    || $objOrderReturnState->send_email_to_superadmin
                    || $objOrderReturnState->send_email_to_employee
                    || $objOrderReturnState->send_email_to_hotelier
                ) {
                    // Lets create data for the email templates
                    $objMail = new Mail();
                    $idHotel = 0;

                    $data = array (
                        '{status_name}' => $objOrderReturnState->name,
                        '{status_color}' => $objOrderReturnState->color,
                        '{order_reference}' => $objOrder->reference,
                        '{order_date}' => date('Y-m-d h:i:s', strtotime($objOrder->date_add)),
                        '{refunded_amount}' => Tools::displayPrice($this->refunded_amount, new Currency($objOrder->id_currency)),
                        '{payment_mode}' => $this->payment_mode,
                        '{id_transaction}' => $this->id_transaction,
                    );

                    // if mail is true for the customer then send mail to customer with selected template
                    $objCustomer = new Customer($this->id_customer);
                    if ($objOrderReturnState->send_email_to_customer && $objOrderReturnState->customer_template) {
                        if ($refundReqBookings = $this->getOrderRefundRequestedBookings(
                            $this->id_order,
                            $this->id,
                            0,
                            1
                        )) {
                            $idHotel = reset($refundReqBookings)['id_hotel'];
                        }

                        $refBookHtml = $objMail->getEmailTemplateContent('refund_request_detail_customer', Mail::TYPE_HTML, $refundReqBookings);
                        $refBookTxt = $objMail->getEmailTemplateContent('refund_request_detail_customer', Mail::TYPE_TEXT, $refundReqBookings);

                        $data['{refundBookingHtml}'] = $refBookHtml;
                        $data['{refundBookingTxt}'] = $refBookTxt;

                        // send customer information
                        $link = new Link();
                        $data['{refund_reqests_url}'] = $link->getPageLink('order-follow');
                        $data['{firstname}'] = $objCustomer->firstname;
                        $data['{lastname}'] = $objCustomer->lastname;

                        Mail::Send(
                            (int)$idLang,
                            $objOrderReturnState->customer_template,
                            $objOrderReturnState->name,
                            $data,
                            $objCustomer->email,
                            $objCustomer->firstname.' '.$objCustomer->lastname,
                            null,
                            null,
                            null,
                            null,
                            _PS_MAIL_DIR_,
                            false,
                            (int)$objOrder->id_shop
                        );
                    }

                    if ($objOrderReturnState->admin_template) {
                        if ($refundReqBookings = $this->getOrderRefundRequestedBookings(
                            $this->id_order,
                            $this->id
                        )) {
                            $idHotel = $refundReqBookings[0]['id_hotel'];
                        }

                        $refBookHtml = $objMail->getEmailTemplateContent('refund_request_detail_admin', Mail::TYPE_HTML, $refundReqBookings);
                        $refBookTxt = $objMail->getEmailTemplateContent('refund_request_detail_admin', Mail::TYPE_TEXT, $refundReqBookings);

                        $data['{cancelation_reason}'] = $this->question;
                        $data['{refundBookingHtml}'] = $refBookHtml;
                        $data['{refundBookingTxt}'] = $refBookTxt;

                        // send mail to the super admin
                        if ($objOrderReturnState->send_email_to_superadmin) {
                            // send superadmin information
                            if (Validate::isLoadedObject($superAdmin = new Employee(_PS_ADMIN_PROFILE_))) {
                                if (Validate::isEmail($superAdmin->email)) {
                                    $data['{customer_name}'] = $objCustomer->firstname.' '.$objCustomer->lastname;
                                    $data['{customer_email}'] = $objCustomer->email;
                                    $data['{firstname}'] = $superAdmin->firstname;
                                    $data['{lastname}'] = $superAdmin->lastname;

                                    Mail::Send(
                                        (int)$idLang,
                                        $objOrderReturnState->admin_template,
                                        $objOrderReturnState->name,
                                        $data,
                                        $superAdmin->email,
                                        $superAdmin->firstname.' '.$superAdmin->lastname,
                                        null,
                                        null,
                                        null,
                                        null,
                                        _PS_MAIL_DIR_,
                                        false,
                                        (int)$objOrder->id_shop
                                    );
                                }
                            }
                        }
                        if ($idHotel
                            && Validate::isLoadedObject($objHotel = new HotelBranchInformation($idHotel))
                        ) {
                            // send mail to the hotelier
                            if ($objOrderReturnState->send_email_to_hotelier) {
                                $data['{customer_name}'] = $objCustomer->firstname.' '.$objCustomer->lastname;
                                $data['{customer_email}'] = $objCustomer->email;
                                $data['{firstname}'] = '';
                                $data['{lastname}'] = '';
                                $data['{email}'] = $objHotel->email;

                                Mail::Send(
                                    (int)$idLang,
                                    $objOrderReturnState->admin_template,
                                    $objOrderReturnState->name,
                                    $data,
                                    $objHotel->email,
                                    null,
                                    null,
                                    null,
                                    null,
                                    null,
                                    _PS_MAIL_DIR_,
                                    false,
                                    (int)$objOrder->id_shop
                                );
                            }

                            // send mail to the employee
                            if ($objOrderReturnState->send_email_to_employee) {
                                if ($htlAccesses = $objHotel->getHotelAccess($idHotel)) {
                                    $data['{customer_name}'] = $objCustomer->firstname.' '.$objCustomer->lastname;
                                    $data['{customer_email}'] = $objCustomer->email;
                                    foreach ($htlAccesses as $access) {
                                        if ($access['id_profile'] != _PS_ADMIN_PROFILE_) {
                                            if ($htlEmployees = Employee::getEmployeesByProfile($access['id_profile'])) {
                                                foreach ($htlEmployees as $empl) {
                                                    if (Validate::isEmail($empl['email'])) {
                                                        // send hotel manager (employee) have permission for this hotel
                                                        $data['{firstname}'] = $empl['firstname'];
                                                        $data['{lastname}'] = $empl['lastname'];
                                                        $data['{email}'] = $empl['email'];

                                                        Mail::Send(
                                                            (int)$idLang,
                                                            $objOrderReturnState->admin_template,
                                                            $objOrderReturnState->name,
                                                            $data,
                                                            $empl['email'],
                                                            $empl['firstname'].' '.$empl['lastname'],
                                                            null,
                                                            null,
                                                            null,
                                                            null,
                                                            _PS_MAIL_DIR_,
                                                            false,
                                                            (int)$objOrder->id_shop
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // executes hook
                Hook::exec(
                    'actionOrderReFundStatusPostUpdate',
                    array(
                        'new_order_return_status' => $newOrderReturnState,
                        'id_order_return' => (int) $this->id_order_return,
                    )
                );

                return true;
            }
        }

        return false;
    }

    public function getRefundedAmount($idOrder, $idOrderReturn = 0, $idHtlBooking = 0)
    {
        $sql = 'SELECT SUM(ord.`refunded_amount`) FROM `'._DB_PREFIX_.'order_return_detail` ord';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'order_return` orr ON (orr.`id_order_return` = ord.`id_order_return`)';
        $sql .= ' WHERE orr.`id_order` = '.(int)$idOrder;

        if ($idOrderReturn) {
            $sql .= ' AND ord.`id_order_return` = '.(int)$idOrderReturn;
        }

        if ($idHtlBooking) {
            $sql .= ' AND ord.`id_htl_booking` = '.(int)$idHtlBooking;
        }

        return Db::getInstance()->getValue($sql);
    }
}
