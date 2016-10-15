<?php

class HotelCustomerAdvancedProductPayment extends ObjectModel
{
    public $id;
    public $id_cart;
    public $id_order;
    public $id_room;
    public $id_hotel;
    public $id_product;
    public $id_guest;
    public $id_customer;
    public $id_currency;
    public $product_price_tax_incl;
    public $product_price_tax_excl;
    public $advance_payment_amount;
    public $date_from;
    public $date_to;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_customer_adv_product_payment',
        'primary' => 'id',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_room' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_guest' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'product_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'product_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'advance_payment_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'date_from' =>      array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_to' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );
}
