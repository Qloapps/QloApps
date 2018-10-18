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

class WkHotelTestimonialData extends ObjectModel
{
    public $name;
    public $designation;
    public $testimonial_content;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_testimonials_block_data',
        'primary' => 'id_testimonial_block',
        'multilang' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING),
            'designation' => array('type' => self::TYPE_STRING),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            /* Lang fields */
            'testimonial_content' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
    ));

    public function getTestimonialData($active = 2, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT tm.*, tml.`testimonial_content` FROM `'._DB_PREFIX_.'htl_testimonials_block_data` tm
        INNER JOIN `'._DB_PREFIX_.'htl_testimonials_block_data_lang` AS tml ON
        (tml.`id_testimonial_block` = tm.`id_testimonial_block`)
        WHERE tml.`id_lang` = '.(int)$idLang;

        if ($active != 2) {
            $sql .= ' AND `active` = '.(int) $active;
        }
        $sql .= ' ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);
        return $result;
    }

    public function delete()
    {
        // delete image of the block
        $imgPath = _PS_MODULE_DIR_.'wktestimonialblock/views/img/hotels_testimonials_img/'.$this->id.'.jpg';
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
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_testimonials_block_data`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT htb.`id_testimonial_block`, htb.`position` FROM `'._DB_PREFIX_.'htl_testimonials_block_data` htb
            WHERE htb.`id_testimonial_block` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_testimonial_block'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_testimonials_block_data` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_testimonials_block_data`
            SET `position` = '.(int)$position.'
            WHERE `id_testimonial_block`='.(int)$movedBlock['id_testimonial_block']
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
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_testimonials_block_data` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    // enter the default demo data of the module
    public static function insertModuleDemoData()
    {
        $languages = Language::getLanguages(false);
        $HOTEL_TESIMONIAL_BLOCK_HEADING = array();
        $HOTEL_TESIMONIAL_BLOCK_CONTENT = array();
        foreach ($languages as $lang) {
            $HOTEL_TESIMONIAL_BLOCK_HEADING[$lang['id_lang']] = 'What our Guest say?';
            $HOTEL_TESIMONIAL_BLOCK_CONTENT[$lang['id_lang']] = 'Fap put a bird on it next level, sustainable disrupt
            polaroid flannel Helvetica Kickstarter quinoa bicycle rights narwhal wolf Fap put a bird on it next level.';
        }
        // update global configuration values in multilang
        Configuration::updateValue('HOTEL_TESIMONIAL_BLOCK_HEADING', $HOTEL_TESIMONIAL_BLOCK_HEADING);
        Configuration::updateValue('HOTEL_TESIMONIAL_BLOCK_CONTENT', $HOTEL_TESIMONIAL_BLOCK_CONTENT);

        $designations = array(0 => 'Eon Comics CEO', 1 => 'Ken Comics Kal', 2 => 'Jan Comics Joe');
        $names = array(0 => 'Calrk Kent', 1 => 'Calrk Kent', 2 => 'Calrk Kent');

        $testimonialContent = "It is a long established fact that a reader will be distracted by the readable
        content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less
        normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable
        English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text,
        and a search for 'lorem ipsum' will uncover many web sites still in their infancy.";

        for ($i = 0; $i < 3; $i++) {
            $objTestimonialData = new WkHotelTestimonialData();
            $objTestimonialData->name = $names[$i];
            $objTestimonialData->designation = $designations[$i];
            foreach ($languages as $lang) {
                $objTestimonialData->testimonial_content[$lang['id_lang']] = $testimonialContent;
            }
            ImageManager::resize(
                _PS_MODULE_DIR_.'wktestimonialblock/views/img/dummy_img/'.($i+1).'.png',
                _PS_MODULE_DIR_.'wktestimonialblock/views/img/hotels_testimonials_img/'.($i+1).'.jpg'
            );
            $objTestimonialData->position = WkHotelTestimonialData::getHigherPosition();
            $objTestimonialData->testimonial_image = ($i+1).'.jpg';
            $objTestimonialData->active = 1;
            $objTestimonialData->save();
        }
        return true;
    }
}
