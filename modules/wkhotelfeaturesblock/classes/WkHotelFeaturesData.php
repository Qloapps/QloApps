<?php
class WkHotelFeaturesData extends ObjectModel
{
	public $id;
	public $feature_title;
	public $feature_description;
	public $feature_image;
	public $active;
	public $position;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'htl_features_block_data',
		'primary' => 'id',
		'fields' => array(
			'feature_image' =>			array('type' => self::TYPE_STRING),
			'feature_title' =>			array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
			'feature_description' =>	array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
			'active' =>             	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' =>           	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' =>           	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>           	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
	));

	public static function getHigherPosition()
	{
	    $sql = 'SELECT MAX(`position`)
	            FROM `'._DB_PREFIX_.'htl_features_block_data`';
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

	public function getHotelAmenities($active = true)
	{
		$sql = "SELECT `feature_image`, `feature_title`, `feature_description`
				FROM `"._DB_PREFIX_."htl_features_block_data`
				WHERE 1";
		if ($active !== false) {
			$sql .= " AND `active` = ".(int)$active;
		}
		$sql .= " ORDER BY `position`";

		$result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        }
        return false;
	}
}