<?php 
  class HotelOrderRefundInfo extends ObjectModel
  {
    public $id;
    public $id_order;
    public $id_product;
    public $id_customer;
    public $id_currency;
    public $refund_stage_id;
    public $order_amount;
    public $num_rooms;
    public $date_from;
    public $date_to;
    public $cancellation_reason;
    public $refunded_amount;
    public $date_add;
    public $date_upd;

    public static $definition = array(
		'table' => 'htl_order_refund_info',
		'primary' => 'id',
		'fields' => array(
			'id_order' =>             array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_product' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_customer' =>          array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_currency' =>          array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'refund_stage_id' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'num_rooms' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'order_amount' =>         array('type' => self::TYPE_FLOAT),
			'date_from' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_to' =>              array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'cancellation_reason' =>  array('type' => self::TYPE_HTML),
            'refunded_amount' =>               array('type' => self::TYPE_FLOAT),
			'date_add' =>             array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
			'date_upd' =>             array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
	));
    
    /**
     * [getOderRefundInfoByIdOrderIdProductByDate ::To get Booking order refund Information By Id order and Id product and By date from and date to for which the room is booked]
     * @param  [int] $id_order   [Id of the order]
     * @param  [int] $id_product [Id of the product]
     * @param  [date] $date_from  [Start date of the booking]
     * @param  [date] $date_to    [End date of the booking]
     * @return [array|false]      [If data found then Returns array of the order refund informations of the rooms else returns false]
     */
    public function getOderRefundInfoByIdOrderIdProductByDate($id_order, $id_product, $date_from, $date_to)
    {
      	$result = Db::getInstance()->getRow("SELECT * FROM `"._DB_PREFIX_."htl_order_refund_info` WHERE `id_order`=".$id_order." AND `id_product`=".$id_product." AND `date_from`='$date_from' AND `date_to`='$date_to'");
		if ($result)
			return $result;
		return false;
    }

    /**
     * [getOrderRefundInforDataByOrderId :: To get refund information of all the rooms in an order]
     * @param  [int] $id_order [Id of the order from which all rooms order refund information you want]
     * @return [array|false]           [If data found then Returns array of the order refund informations of the rooms else returns false]
     */
    public function getOrderRefundInforDataByOrderId($id_order)
    {
        $result = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."htl_order_refund_info` WHERE `id_order`=".$id_order);
        if ($result)
            return $result;
        return false;
    }

  }