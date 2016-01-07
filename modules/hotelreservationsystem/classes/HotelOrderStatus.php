<?php
class HotelOrderStatus extends ObjectModel
{
	public $id;
	public $status;

	public static $definition = array(
		'table' => 'htl_order_status',
		'primary' => 'id',
		'fields' => array(
			'status' =>	array('type' => self::TYPE_STRING),
		),
	);

	/**
	 * [getAllHotelOrderStatus :: To get array all possible order statuses]
	 * @return [array|boolean] [If data found then Returns array of the order statuses else returns false]
	 */
	public static function getAllHotelOrderStatus()
	{
		$result = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."htl_order_status`");
		if ($result)
			return $result;
		return false;
	}
}