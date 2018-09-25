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

class WkHotelInteriorImage extends ObjectModel
{
    public $name;
    public $display_name;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_interior_image',
        'primary' => 'id_interior_image',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING),
            'display_name' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * NOTE : If you want to get all images then pass false in argument variable
     */
    public function getHotelInteriorImg($active = 2)
    {
        $sql = 'SELECT `name`, `display_name`, `position`
                FROM `'._DB_PREFIX_.'htl_interior_image` WHERE 1';

        if ($active != 2) {
            $sql .= ' AND `active` = '.(int) $active;
        }
        $sql .= ' ORDER BY position';

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        }
        return false;
    }

    public function delete()
    {
        // delete image of the block
        if (!$this->deleteHotelInteriorImg($this->id)) {
            return false;
        }
        $return = parent::delete();
        /* Reinitializing position */
        $this->cleanPositions();
        return $return;
    }

    public function deleteHotelInteriorImg($id_htl_interior)
    {
        $obj_inter_img = new WkHotelInteriorImage($id_htl_interior);
        if (unlink(_PS_MODULE_DIR_.'wkabouthotelblock/views/img/hotel_interior/'.$obj_inter_img->name)) {
            return true;
        }
        return false;
    }

    public static function getHigherPosition()
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_interior_image`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT hib.`id_interior_image`, hib.`position` FROM `'._DB_PREFIX_.'htl_interior_image` hib
            WHERE hib.`id_interior_image` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_interior_image'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_interior_image` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_interior_image`
            SET `position` = '.(int)$position.'
            WHERE `id_interior_image`='.(int)$movedBlock['id_interior_image']
        ));
    }

    /**
     * Reorder blocks position
     * Call it after deleting a blocks.
     * @return bool $return
     */
    public static function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_interior_image` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    public static function insertModuleDemoData()
    {
        $languages = Language::getLanguages(false);
        $HOTEL_INTERIOR_HEADING = array();
        $HOTEL_INTERIOR_DESCRIPTION = array();
        foreach ($languages as $lang) {
            $HOTEL_INTERIOR_HEADING[$lang['id_lang']] = 'Interior';
            $HOTEL_INTERIOR_DESCRIPTION[$lang['id_lang']] = 'Families travelling with kids will find Amboseli national
            park a safari destination matched to no other, with less tourist traffic, breathtaking open space.';
        }
        // update global configuration values in multilang
        Configuration::updateValue('HOTEL_INTERIOR_HEADING', $HOTEL_INTERIOR_HEADING);
        Configuration::updateValue('HOTEL_INTERIOR_DESCRIPTION', $HOTEL_INTERIOR_DESCRIPTION);

        // Database Entry
        for ($i = 1; $i <= 12; $i++) {
            do {
                $tmp_name = uniqid().'.jpg';
            } while (file_exists(_PS_MODULE_DIR_.'wkabouthotelblock/views/img/hotel_interior/'.$tmp_name));
            ImageManager::resize(
                _PS_MODULE_DIR_.'wkabouthotelblock/views/img/dummy_img/'.$i.'.jpg',
                _PS_MODULE_DIR_.'wkabouthotelblock/views/img/hotel_interior/'.$tmp_name
            );
            $objHtlInteriorImg = new WkHotelInteriorImage();
            $objHtlInteriorImg->name = $tmp_name;
            $objHtlInteriorImg->display_name = 'Dummy Image '.$i;
            $objHtlInteriorImg->position = self::getHigherPosition();
            $objHtlInteriorImg->active = 1;
            $objHtlInteriorImg->add();
        }

        return true;
    }
}
