<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
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

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->image_dir = _PS_MODULE_DIR_.'wkabouthotelblock/views/img/hotel_interior/';
        $this->image_name = $this->name;
    }

    /**
     * NOTE : If you want to get all images then pass false in argument variable
     */
    public function getHotelInteriorImg($active = 2)
    {
        $sql = 'SELECT `id_interior_image`, `name`, `display_name`, `position`
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

    /**
     * Deletes current interior image block from the database
     * @return bool `true` if delete was successful
     */
    public function delete()
    {
        if (!parent::delete()
            || !$this->deleteImage(true)
            || !$this->cleanPositions()
        ) {
            return false;
        }
        return true;
    }

    public function getHigherPosition()
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
    public function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_interior_image` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    public function insertModuleDemoData()
    {
        $languages = Language::getLanguages(false);
        $HOTEL_INTERIOR_HEADING = array();
        $HOTEL_INTERIOR_DESCRIPTION = array();
        $htlInteriorHeadingLang = array(
            'en' => 'Explore the Interiors!',
            'nl' => 'Verken de interieurs!',
            'fr' => 'Explorez les intérieurs!',
            'de' => 'Entdecken Sie die Innenräume!',
            'ru' => 'Исследуйте интерьеры!',
            'es' => '¡Explora los interiores!',
        );
        $htlInteriorDescLang = array(
            'en' => 'Step into the sophisticated elegance of our hotel, where every detail is designed with your comfort in mind.',
            'nl' => 'Stap in de verfijnde elegantie van ons hotel, waar elk detail is ontworpen met uw comfort in gedachten.',
            'fr' => 'Entrez dans l\'élégance sophistiquée de notre hôtel, où chaque détail est conçu pour votre confort.',
            'de' => 'Treten Sie ein in die raffinierte Eleganz unseres Hotels, wo jedes Detail mit Ihrem Komfort im Hinterkopf gestaltet ist.',
            'ru' => 'Погрузитесь в утонченную элегантность нашего отеля, где каждая деталь создана с заботой о вашем комфорте.',
            'es' => 'Sumérgete en la elegancia sofisticada de nuestro hotel, donde cada detalle está diseñado pensando en tu comodidad.',
        );
        foreach ($languages as $lang) {
            if (isset($htlInteriorHeadingLang[$lang['iso_code']])) {
                $HOTEL_INTERIOR_HEADING[$lang['id_lang']] = $htlInteriorHeadingLang[$lang['iso_code']];
                $HOTEL_INTERIOR_DESCRIPTION[$lang['id_lang']] = $htlInteriorDescLang[$lang['iso_code']];
            } else {
                $HOTEL_INTERIOR_HEADING[$lang['id_lang']] = $htlInteriorHeadingLang['en'];
                $HOTEL_INTERIOR_DESCRIPTION[$lang['id_lang']] = $htlInteriorDescLang['en'];
            }
        }
        // update global configuration values in multilang
        Configuration::updateValue('HOTEL_INTERIOR_HEADING', $HOTEL_INTERIOR_HEADING);
        Configuration::updateValue('HOTEL_INTERIOR_DESCRIPTION', $HOTEL_INTERIOR_DESCRIPTION);

        // Database Entry
        for ($i = 1; $i <= 12; $i++) {
            $imgName = $i;
            $srcPath = _PS_MODULE_DIR_.'wkabouthotelblock/views/img/dummy_img/'.$imgName.'.jpg';
            if (file_exists($srcPath)) {
                if (ImageManager::isRealImage($srcPath)
                    && ImageManager::isCorrectImageFileExt($srcPath)
                ) {
                    if (ImageManager::resize(
                        $srcPath,
                        _PS_MODULE_DIR_.'wkabouthotelblock/views/img/hotel_interior/'.$imgName.'.jpg'
                    )) {
                        $objHtlInteriorImg = new WkHotelInteriorImage();
                        $objHtlInteriorImg->name = $imgName;
                        $objHtlInteriorImg->display_name = 'Dummy Image '.$i;
                        $objHtlInteriorImg->position = $this->getHigherPosition();
                        $objHtlInteriorImg->active = 1;
                        $objHtlInteriorImg->save();
                    }
                }
            }
        }

        return true;
    }
}
