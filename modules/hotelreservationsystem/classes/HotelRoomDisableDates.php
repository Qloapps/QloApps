<?php
class HotelRoomDisableDates extends ObjectModel
{
	public $id;
	public $id_room_type;
	public $id_room;
	public $date_from;
	public $date_to;
	public $reason;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'htl_room_disable_dates',
		'primary' => 'id',
		'fields' => array(
			'id_room_type' =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_room' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'date_from' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
			'date_to' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
			'reason' => 		array('type' => self::TYPE_STRING),
			'date_add' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate')
		),
	);

	public function getRoomDisableDates($id_room)
	{
		return Db::getInstance()->executeS('SELECT `date_from`, `date_to`, `reason` FROM `'._DB_PREFIX_.'htl_room_disable_dates` WHERE `id_room`='.(int)$id_room);
	}

	public function checkIfRoomAlreadyDisabled($params)
	{
		return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_disable_dates` WHERE `id_room` = '.(int)$params['id_room'].' AND `date_from` <= \''.pSQL($params['date_from']).'\' AND `date_to` >= \''.pSQL($params['date_to']).'\'');
	}

	public function updateDisableDateRanges($params)
	{
		if ($this->deleteDisabledDatesForDateRange($params)) {
			$roomDisableDates = new HotelRoomDisableDates();
			$roomDisableDates->id_room = $params['id_room'];
			$roomDisableDates->date_from = $params['date_from'];
			$roomDisableDates->date_to = $params['date_to'];
			$roomDisableDates->reason = $params['reason'];
			return $roomDisableDates->save();
		}
		return false;
	}

	public function deleteDisabledDatesForDateRange($params)
	{
		return Db::getInstance()->delete('htl_room_disable_dates', '`id_room` = '.(int)$params['id_room'].' AND `date_from` >= \''.pSQL($params['date_from']).'\' AND `date_to` <= \''.pSQL($params['date_to']).'\'');
	}

	public function deleteRoomDisableDates($id_room)
	{
		return Db::getInstance()->delete('htl_room_disable_dates', '`id_room`='.(int)$id_room);
	}
}