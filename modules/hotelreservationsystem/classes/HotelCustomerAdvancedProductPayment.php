<?php
/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HotelCustomerAdvancedProductPayment extends ObjectModel
{
    public $id;
    public $id_cart;
    public $id_order;
    public $id_room;
    public $id_hotel;
    public $id_product;
    public $quantity;
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
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
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

    /**
     * [getProductAdvancePaymentDetails :: To get Advance payment Information By id_product]
     * @param  [int] $id_product [id of the order which Advance payment Information you want]
     * @param  [int] $id_product [id of the product which Advance payment Information you want]
     * @return [array|false]     [Returns array if information of advance payment of that id_product and id_order found otherwise returs false]
     */
    public function getProductAdvancePaymentDetails($id_order, $id_product)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_customer_adv_product_payment` WHERE `id_order`='.(int)$id_order.' AND `id_product`='.(int)$id_product);
    }

    public function getRoomTypeAdvancePaymentMaountByDuration($id_order, $id_product, $date_from, $date_to)
    {
        return Db::getInstance()->getValue('SELECT SUM(`advance_payment_amount`) FROM `'._DB_PREFIX_.'htl_customer_adv_product_payment` WHERE `id_order`='.(int)$id_order.' AND `id_product`='.(int)$id_product.' AND `date_from`=\''.pSQL($date_from).'\' AND `date_to`=\''.pSQL($date_to).'\'');
    }
}
