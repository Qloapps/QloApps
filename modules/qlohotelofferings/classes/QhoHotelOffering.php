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

class QhoHotelOffering extends ObjectModel
{
    public $name;
    public $description;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    const QHO_DISPLAY_PAGE_BOTH = 0;
    const QHO_DISPLAY_PAGE_HOTEL = 1;
    const QHO_DISPLAY_PAGE_HOME = 2;

    public static $definition = array(
        'table' => 'qho_offering',
        'primary' => 'id_offering',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $objModule = Module::getInstanceByName('qlohotelofferings');

        $this->image_dir = $objModule->getLocalPath().'views/img/offering_img/';
    }

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
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'qho_offering`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT qho.`id_offering`, qho.`position` FROM `'._DB_PREFIX_.'qho_offering` qho
            WHERE qho.`id_offering` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_offering'] == (int)$this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'qho_offering` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'qho_offering`
            SET `position` = '.(int)$position.'
            WHERE `id_offering`='.(int)$movedBlock['id_offering']
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
        $sql = 'UPDATE `'._DB_PREFIX_.'qho_offering` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }

    public function getOfferingsData($active = 2, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT qho.*, qhol.`description`, qhol.`name` FROM `'._DB_PREFIX_.'qho_offering` qho
        INNER JOIN `'._DB_PREFIX_.'qho_offering_lang` AS qhol ON
        (qhol.`id_offering` = qho.`id_offering`)
        WHERE qhol.`id_lang` = '.(int)$idLang;

        if ($active != 2) {
            $sql .= ' AND `active` = '.(int) $active;
        }
        $sql .= ' ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);
        return $result;
    }
}
