<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class OrderReturnDetailCore extends ObjectModel
{
    /** @var int */
    public $id_order_return;

    /** @var int */
    public $id_htl_booking;

    /** @var float amount of the refund transaction */
    public $refunded_amount;

    public $id_order_detail;
    public $id_customization;
    public $product_quantity;

    const REFUND_REQUEST_STATUS_LATEST = 1;
    const REFUND_REQUEST_STATUS_PENDING = 2;
    const REFUND_REQUEST_STATUS_COMPLETED = 3;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_return_detail',
        'primary' => 'id_order_return_detail',
        'fields' => array(
            'id_order_return' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_htl_booking' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'default' => 0),
            'id_customization' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'default' => 0),
            'product_quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'default' => 0),
            'refunded_amount' => array('type' => self::TYPE_FLOAT),
        ),
    );
}
