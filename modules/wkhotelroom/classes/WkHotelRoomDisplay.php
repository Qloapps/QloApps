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

class WkHotelRoomDisplay extends ObjectModel
{
    public $id_product;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_room_block_data',
        'primary' => 'id_room_block',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
    ));

    public function gerRoomByIdProduct($id_product)
    {
        return DB::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_block_data` WHERE `id_product` = '.(int)$id_product
        );
    }

    public function getHotelRoomDisplayData($active = true, $checkShowAtFront = true)
    {
        $sql = 'SELECT hrbd.* FROM `'._DB_PREFIX_.'htl_room_block_data` hrbd';

        if ($checkShowAtFront) {
            $sql .= ' INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hrbd.`id_product`)';
        }

        $sql .= ' WHERE 1';

        if ($active) {
            $sql .= ' AND hrbd.`active` = 1';
        }

        if ($checkShowAtFront) {
            $sql .= ' AND p.`show_at_front` = 1';
        }

        $sql .= ' ORDER BY hrbd.`position`';

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
            $sql .= ' AND `id_room_block` != '.(int)$idRoomDisplayBlock;
        }
        $result = DB::getInstance()->getRow($sql);
        if ($result) {
            return $result;
        }
        return false;
    }

    public function deleteRoomByIdProduct($id_product)
    {
        return Db::getInstance()->delete('htl_room_block_data', 'id_product = '.(int)$id_product);
    }

    public function delete()
    {
        $return = parent::delete();
        /* Reinitializing position */
        $this->cleanPositions();
        return $return;
    }

    public function getHigherPosition()
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_room_block_data`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT htb.`id_room_block`, htb.`position` FROM `'._DB_PREFIX_.'htl_room_block_data` htb
            WHERE htb.`id_room_block` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_room_block'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_room_block_data` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_room_block_data`
            SET `position` = '.(int)$position.'
            WHERE `id_room_block`='.(int)$movedBlock['id_room_block']
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
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_room_block_data` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    // enter the default demo data of the module
    public function insertModuleDemoData()
    {
        $languages = Language::getLanguages(false);
        $HOTEL_ROOM_DISPLAY_HEADING = array();
        $HOTEL_ROOM_DISPLAY_DESCRIPTION = array();
        $htlRoomHeading = array(
            'en' => 'Our Rooms',
            'nl' => 'Onze Kamers',
            'fr' => 'Nos Chambres',
            'de' => 'Unsere Zimmer',
            'ru' => 'Наши номера',
            'es' => 'Nuestras Habitaciones',
        );
        $htlRoomDesc = array(
            'en' => 'Relax in the comfort of our rooms. With modern amenities, serene decor, and stunning lake or city views, each room offers a peaceful retreat for your stay.',
            'nl' => 'Ontspan in het comfort van onze kamers. Met moderne voorzieningen, een serene inrichting en een prachtig uitzicht op het meer of de stad, biedt elke kamer een rustige toevluchtsoord voor uw verblijf.',
            'fr' => 'Détendez-vous dans le confort de nos chambres. Avec des équipements modernes, une décoration sereine et une vue imprenable sur le lac ou la ville, chaque chambre offre un refuge paisible pour votre séjour.',
            'de' => 'Entspannen Sie im Komfort unserer Zimmer. Mit modernen Annehmlichkeiten, ruhiger Dekoration und herrlichem Blick auf den See oder die Stadt bietet jeder Raum einen friedlichen Rückzugsort für Ihren Aufenthalt.',
            'ru' => 'Отдохните в комфорте наших номеров. С современными удобствами, спокойным декором и потрясающим видом на озеро или город, каждый номер предлагает мирное убежище для вашего пребывания.',
            'es' => 'Relájese en la comodidad de nuestras habitaciones. Con comodidades modernas, decoración serena e impresionantes vistas al lago o a la ciudad, cada habitación ofrece un retiro tranquilo para su estancia.',
        );
        foreach ($languages as $lang) {
            if (isset($htlRoomHeading[$lang['iso_code']])) {
                $HOTEL_ROOM_DISPLAY_HEADING[$lang['id_lang']] = $htlRoomHeading[$lang['iso_code']];
                $HOTEL_ROOM_DISPLAY_DESCRIPTION[$lang['id_lang']] = $htlRoomDesc[$lang['iso_code']];
            } else {
                $HOTEL_ROOM_DISPLAY_HEADING[$lang['id_lang']] = $htlRoomHeading['en'];
                $HOTEL_ROOM_DISPLAY_DESCRIPTION[$lang['id_lang']] = $htlRoomDesc['en'];
            }
        }

        // update global configuration values in multilang
        Configuration::updateValue('HOTEL_ROOM_DISPLAY_HEADING', $HOTEL_ROOM_DISPLAY_HEADING);
        Configuration::updateValue('HOTEL_ROOM_DISPLAY_DESCRIPTION', $HOTEL_ROOM_DISPLAY_DESCRIPTION);
        if ($roomTypes = HotelHelper::getPsProducts(Configuration::get('PS_LANG_DEFAULT'), 0, 5, 1)) {
            foreach ($roomTypes as $product) {
                if (Validate::isLoadedObject($objProduct = new Product($product['id_product']))) {
                    $objRoomBlock = new WkHotelRoomDisplay();
                    $objRoomBlock->id_product = $product['id_product'];
                    $objRoomBlock->position = $this->getHigherPosition();
                    $objRoomBlock->active = $objProduct->active;
                    $objRoomBlock->save();
                }
            }
        }
        return true;
    }
}
