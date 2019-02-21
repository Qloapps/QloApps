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

class WkHotelFeaturesData extends ObjectModel
{
    public $feature_title;
    public $feature_description;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_features_block_data',
        'primary' => 'id_features_block',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            /* Lang fields */
            'feature_title' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
            'feature_description' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
        )
    );

    public function getHotelAmenities($active = 2, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT fb.*, fbl.`feature_title`, fbl.`feature_description`
				FROM `'._DB_PREFIX_.'htl_features_block_data` fb
				INNER JOIN `'._DB_PREFIX_.'htl_features_block_data_lang` fbl
                ON (fbl.`id_features_block` = fb.`id_features_block`)
                WHERE fbl.`id_lang` = '.(int)$idLang;
        if ($active != 2) {
            $sql .= ' AND `active` = '.(int) $active;
        }
        $sql .= ' ORDER BY `position`';

        return Db::getInstance()->executeS($sql);
    }

    public function delete()
    {
        // delete image of the block
        $imgPath = _PS_MODULE_DIR_.'wkhotelfeaturesblock/views/img/hotels_features_img/'.$this->id.'.jpg';
        if (file_exists($imgPath)) {
            unlink($imgPath);
        }
        $return = parent::delete();
        /* Reinitializing position */
        $this->cleanPositions();
        return $return;
    }

    public static function getHigherPosition()
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_features_block_data`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT htb.`id_features_block`, htb.`position` FROM `'._DB_PREFIX_.'htl_features_block_data` htb
            WHERE htb.`id_features_block` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_features_block'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_features_block_data` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_features_block_data`
            SET `position` = '.(int)$position.'
            WHERE `id_features_block`='.(int)$movedBlock['id_features_block']
        ));
    }

    /**
     * Reorder blocks position
     * Call it after deleting a blocks.
     * @return bool $return
     */
    public function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_features_block_data` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    // enter the default demo data of the module
    public function insertModuleDemoData()
    {
        Configuration::updateValue('HOTEL_AMENITIES_BLOCK_NAV_LINK', 1);

        $languages = Language::getLanguages(false);
        $HOTEL_AMENITIES_HEADING = array();
        $HOTEL_AMENITIES_DESCRIPTION = array();
        foreach ($languages as $lang) {
            $HOTEL_AMENITIES_HEADING[$lang['id_lang']] = 'Amenities';
            $HOTEL_AMENITIES_DESCRIPTION[$lang['id_lang']] = 'Families travelling with kids will find Amboseli national park a safari destination matched to no other, with less tourist traffic, breathtaking open space.';
        }
        Configuration::updateValue('HOTEL_AMENITIES_HEADING', $HOTEL_AMENITIES_HEADING);
        Configuration::updateValue('HOTEL_AMENITIES_DESCRIPTION', $HOTEL_AMENITIES_DESCRIPTION);

        $amenityTitle = array('luxurious Rooms', 'World class cheffs', 'Restaurants', 'Gym & Spa');
        $amenityDescription  = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s';

        for ($i = 0; $i < 4; $i++) {
            $objFeatureData = new WkHotelFeaturesData();
            foreach ($languages as $lang) {
                $objFeatureData->feature_title[$lang['id_lang']] = $amenityTitle[$i];
                $objFeatureData->feature_description[$lang['id_lang']] = $amenityDescription;
            }
            $objFeatureData->active = 1;
            $objFeatureData->position = WkHotelFeaturesData::getHigherPosition();
            if ($objFeatureData->save()) {
                $srcPath = _PS_MODULE_DIR_.'wkhotelfeaturesblock/views/img/dummy_img/'.$objFeatureData->id.'.jpg';
                if (file_exists($srcPath)) {
                    if (ImageManager::isRealImage($srcPath)
                        && ImageManager::isCorrectImageFileExt($srcPath)
                    ) {
                        ImageManager::resize(
                            $srcPath,
                            _PS_MODULE_DIR_.'wkhotelfeaturesblock/views/img/hotels_features_img/'.$objFeatureData->id.'.jpg'
                        );
                    }
                }
            }
        }
        return true;
    }
}
