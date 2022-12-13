<?php
/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

class HotelSettingsLink extends ObjectModel
{
    public $id_settings_link;
    public $name;
    public $hint;
    public $icon;
    public $link;
    public $new_window;
    public $position;
    public $unremovable;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_settings_link',
        'primary' => 'id_settings_link',
        'multilang' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'lang' => true, 'required' => true),
            'hint' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'lang' => true, 'required' => true),
            'icon' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true),
            'link' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => true),
            'new_window' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'unremovable' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public function getAll($active = true)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_settings_link` hsl
            LEFT JOIN `'._DB_PREFIX_.'htl_settings_link_lang` hsll
            ON hsll.`id_settings_link` = hsl.`id_settings_link` AND hsll.`id_lang` = '.(int) Context::getContext()->language->id.
            ($active ? ' WHERE hsl.`active` = 1' : '').'
            ORDER BY hsl.`position`'
        );
    }

    public function generateLink($link = null, $id_employee = null)
    {
        if ($link) {
            $this->link = $link;
        }

        $context = Context::getContext();
        if ($this->link == '../') {
            return $context->shop->getBaseURL();
        } else {
            preg_match('/controller=(.+)(&.+)?$/', $this->link, $adminTab);
            if (isset($adminTab[1])) {
                if (strpos($adminTab[1], '&')) {
                    $adminTab[1] = substr($adminTab[1], 0, strpos($adminTab[1], '&'));
                }

                $token = Tools::getAdminToken($adminTab[1].(int) Tab::getIdFromClassName($adminTab[1]).(int) ($id_employee ? $id_employee : $context->employee->id));
                return $link.'&token='.$token;
            }
        }

        return $link;
    }

    public function getUnremovableLinks()
    {
        return Db::getInstance()->executeS(
            'SELECT *
            FROM `'._DB_PREFIX_.'htl_settings_link` hsl
            WHERE hsl.`unremovable` = 1'
        );
    }

    public function getHigherPosition()
    {
        $position = Db::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_settings_link`'
        );

        $result = (is_numeric($position)) ? $position : -1;

        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$result = Db::getInstance()->executeS(
            'SELECT hsl.`id_settings_link`, hsl.`position`
            FROM `'._DB_PREFIX_.'htl_settings_link` hsl
            WHERE hsl.`id_settings_link` = '.(int) $this->id.' ORDER BY `position` ASC')
        ) {
            return false;
        }

        $movedBlock = false;
        foreach ($result as $block) {
            if ((int)$block['id_settings_link'] == (int) $this->id) {
                $movedBlock = $block;
            }
        }

        if ($movedBlock === false) {
            return false;
        }

        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_settings_link` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int) $movedBlock['position'].' AND `position` <= '.(int) $position : '< '
            .(int) $movedBlock['position'].' AND `position` >= '.(int) $position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_settings_link`
            SET `position` = '.(int) $position.'
            WHERE `id_settings_link`='.(int) $movedBlock['id_settings_link']
        ));
    }

    public function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_settings_link` SET `position` = @i:=@i+1 ORDER BY `position` ASC';

        return Db::getInstance()->execute($sql);
    }
}