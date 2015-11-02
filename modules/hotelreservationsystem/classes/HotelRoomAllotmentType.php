<?php
class HotelRoomAllotmentType extends ObjectModel
{
	public $id;
	public $type;

	public static $definition = array(
		'table' => 'htl_room_allotment_type',
		'primary' => 'id',
		'fields' => array(
			'type' =>	array('type' => self::TYPE_STRING),
		),
	);
}