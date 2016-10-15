<?php
class WkHotelInteriorImage extends ObjectModel 
{
    public $id;
    public $name;
    public $display_name;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_interior_image',
        'primary' => 'id',
        'fields' => array(
            'name' =>               array('type' => self::TYPE_STRING),
            'display_name' =>       array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
            'active' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * NOTE : If you want to get all images then pass false in argument variable
     */
    public function getHotelInteriorImg($active = true)
    {
        $sql = 'SELECT `name`, `display_name`
                FROM `'._DB_PREFIX_.'htl_interior_image` WHERE 1';

        if ($active !== false) {
            $sql .= ' AND `active` = '.$active;
        }

        $sql .= ' ORDER BY position';

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        }

        return false;
    }

    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
                FROM `'._DB_PREFIX_.'htl_interior_image`';
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

    public function delete()
    {
        if (!$this->deleteHotelInteriorImg($this->id) || !parent::delete()) {
            return false;
        }

        return true;
    }

    public function deleteHotelInteriorImg($id_htl_interior)
    {
        $obj_inter_img = new WkHotelInteriorImage($id_htl_interior);
        if (unlink(_PS_MODULE_DIR_.'wkabouthotelblock/views/img/hotel_interior/'.$obj_inter_img->name))
            return true;

        return false;
    }
}