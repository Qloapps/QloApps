<?php
class WkHotelTestimonialData extends ObjectModel
{
	public $id;
	public $name;
	public $designation;
	public $testimonial_content;
	public $testimonial_image;
	public $active;
	public $position;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'htl_testimonials_block_data',
		'primary' => 'id',
		'fields' => array(
			'name' =>					array('type' => self::TYPE_STRING),
			'designation'=> 			array('type' => self::TYPE_STRING),
			'testimonial_content' =>	array('type' => self::TYPE_STRING),
			'testimonial_image' =>		array('type' => self::TYPE_STRING),
			'active' =>             	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' =>           	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' =>           	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>           	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
	));

	public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
                FROM `'._DB_PREFIX_.'htl_testimonials_block_data`';
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

    public function getTestimonialData($active = true)
    {
    	$sql = 'SELECT `name`, `designation`, `testimonial_content`, `testimonial_image`, `active`, `position`
                FROM `'._DB_PREFIX_.'htl_testimonials_block_data` WHERE 1';

        if ($active !== false) {
            $sql .= ' AND `active` = '.$active;
        }

        $sql .= ' ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        }

        return false;
    }
}