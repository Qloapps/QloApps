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

class HotelDisplayBlock extends ObjectModel
{
    public $id_hotel;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_block_data',
        'primary' => 'id_hotel_block',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public function getByIdHotel($id_hotel)
    {
        return DB::getInstance()->getValue(
            'SELECT `id_hotel_block` FROM `'._DB_PREFIX_.'htl_block_data`
            WHERE `id_hotel` = '.(int)$id_hotel
        );
    }

    public function getHotelBlockData($idLang = false, $active = null)
    {
        if (!$idLang) {
            $idLang = Contact::getContext()->language->id;
        }
        return DB::getInstance()->executeS(
            'SELECT hbd.*, hbil.`hotel_name`, hbil.`short_description`, hi.`id` AS id_cover_image FROM `'._DB_PREFIX_.'htl_block_data` hbd
            INNER JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
            ON (hbil.`id_lang` = '.(int) $idLang.' AND hbil.`id` = hbd.`id_hotel`)
            LEFT JOIN `'._DB_PREFIX_.'htl_image` hi
            ON hi.`id_hotel` = hbd.`id_hotel` AND hi.`cover` = 1
            WHERE 1
            '.(!is_null($active) ? ' AND  hbd.`active` = '.(int) $active : '')
            .' ORDER BY hbd.`position`'
        );
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
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_block_data`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT htb.`id_hotel_block`, htb.`position` FROM `'._DB_PREFIX_.'htl_block_data` htb
            WHERE htb.`id_hotel_block` = '.(int) $this->id.' ORDER BY `position` ASC'
        )) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int) $block['id_hotel_block'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_block_data` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_block_data`
            SET `position` = '.(int)$position.'
            WHERE `id_hotel_block`='.(int)$movedBlock['id_hotel_block']
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
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_block_data` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }
}
