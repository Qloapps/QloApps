<?php

class HotelCustomerAdvancedPayment extends ObjectModel
{
    public $id;
    public $id_cart;
    public $id_order;
    public $id_guest;
    public $id_customer;
    public $id_currency;
    public $total_paid_amount;
    public $total_order_amount;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_customer_adv_payment',
        'primary' => 'id',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_guest' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'total_paid_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'total_order_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
    ), );

    /**
     * [getClientAdvPaymentDtl :: To get details of advance payment of a customer for his order by customer's guest id and id_cart].
     *
     * @param [int] $id_cart  [Id of the cart]
     * @param [int] $id_guest [Customer's guest id]
     *
     * @return [array|false] [if data found returns array containing advance payment details of an order which cart id is 										$id_cart by a customer which guest id is $id_guest else returns false ]
     */
    public function getClientAdvPaymentDtl($id_cart, $id_guest)
    {
        $result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_customer_adv_payment` WHERE `id_cart`='.$id_cart.' AND `id_guest`='.$id_guest);

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * [getCstAdvPaymentDtlByIdOrder :: To get information about advance payment of an order which order id is $id_order].
     *
     * @param [int] $id_order [Id of the order]
     *
     * @return [array|false] [if data found returns array containing advance payment details of an order which order id is 										$id_order else returns false ]
     */
    public function getCstAdvPaymentDtlByIdOrder($id_order)
    {
        $result = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_customer_adv_payment` WHERE `id_order`='.$id_order);

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * [getOrdertTotal :: To get Total of the order In case of advance payment 
     * 						if order id is passed returns the total paid amount for the order
     * 						else returns Order total calculated by By passed arguments $id_cart and $id_guest
     * 						].
     *
     * @param [int] $id_cart  [Id of the cart]
     * @param [int] $id_guest [Customer's guest ID]
     * @param [int] $id_order [Id of the order]
     *
     * @return [float] [Returns Total of the order in case of advance payment]
     */
    public function getOrdertTotal($id_cart, $id_guest, $id_order = 0)
    {
        $context = Context::getContext();
        $cart_rules = $context->cart->getCartRules();
        $total_discount = 0;
        if ($cart_rules) {
            foreach ($cart_rules as $discount) {
                if ($discount['reduction_currency'] != $context->cart->id_currency) {
                    $discount['reduction_amount'] = Tools::convertPriceFull($discount['reduction_amount'], new Currency($discount['reduction_currency']), $context->currency);
                }
                $total_discount += $discount['reduction_amount'];
            }
        }

        if (!$id_order) {
            $result = $this->getClientAdvPaymentDtl($id_cart, $id_guest);
            if ($result) {
                if ($result['id_currency'] != $context->cart->id_currency) {
                    $order_total = Tools::convertPriceFull($result['total_paid_amount'], new Currency($result['id_currency']), $context->currency);
                } else {
                    $order_total = $result['total_paid_amount'];
                }
                if ($total_discount) {
                    $order_total = ($order_total - $total_discount) > 0 ? ($order_total - $total_discount) : 0;
                }
            } else {
                $order_total = $context->cart->getOrderTotal(true, Cart::BOTH);
            }

            return $order_total;
        } else {
            $result = $this->getCstAdvPaymentDtlByIdOrder($id_order);
            if ($result) {
                $order_total = $result['total_paid_amount'];
                if ($total_discount) {
                    $order_total = ($order_total - $total_discount) > 0 ? ($order_total - $total_discount) : 0;
                }
            } else {
                $order = new Order($id_order);
                $order_total = $order->getOrdersTotalPaid();
            }

            return $order_total;
        }
    }

    public function updateAdvancePaymentInfoOnOrderEdit($id_order)
    {
        $order_adv_dtl = $this->getCstAdvPaymentDtlByIdOrder($id_order);
        if ($order_adv_dtl) {
            $obj_customer_adv = new HotelCustomerAdvancedPayment($order_adv_dtl['id']);
            if (Validate::isLoadedObject($obj_customer_adv)) {
                $order = new Order($id_order);
                if (Validate::isLoadedObject($order)) {
                    $obj_customer_adv->total_order_amount = $order->total_paid;
                    $obj_customer_adv->save();
                }
            }
        }
    }
}
