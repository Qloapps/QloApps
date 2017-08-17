<?php
class WkHotelRoomDisplay extends ObjectModel
{
	public $id;
	public $id_product;
	public $active;
	public $position;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'htl_room_block_data',
		'primary' => 'id',
		'fields' => array(
			'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'active' =>     array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' =>   array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' =>   array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>   array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
	));

	public static function getHigherPosition()
	{
	    $sql = 'SELECT MAX(`position`)
	            FROM `'._DB_PREFIX_.'htl_room_block_data`';
	    $position = DB::getInstance()->getValue($sql);

	    return (is_numeric($position)) ? $position : - 1;
	}

	public function add($autodate = true, $nullValues = false)
	{
	    if ($this->position <= 0) {
	        $this->position = $this->getHigherPosition() + 1;
	    }

	    $return = parent::add($autodate, true);
	    return $return;
	}

	public function getHotelRoomDisplayData($active = true)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_room_block_data` WHERE 1';
		if ($active !== false) {
			$sql .= ' AND `active` = '.(int)$active;
		}
		$sql .= ' ORDER BY `position`';

		$result = DB::getInstance()->executeS($sql);
		if ($result) {
			return $result;
		}
		return false;
	}

	public function checkRoomTypeAlreadySelected($id_product, $idRoomDisplayBlock)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_room_block_data` WHERE `id_product` = '.(int)$id_product;
		if ($idRoomDisplayBlock) {
			$sql .= ' AND `id` != '.(int)$idRoomDisplayBlock;
		}

		$result = DB::getInstance()->getRow($sql);
		if ($result) {
			return $result;
		}
		return false;
	}

	public static function deleteRoomByIdProduct($id_product)
	{
		return Db::getInstance()->delete('htl_room_block_data', 'id_product = '.(int)$id_product);
	}
}