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
* Do not edit or add to this file if you wish to upgrade QloApps to newer
* versions in the future. If you wish to customize QloApps for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class SourceCore extends ObjectModel
{
    public $name;
    public $id_source_type;
    public $source_code;
    public $position;
    public $unremovable;
    public $active;
    public $deleted = 0;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'source',
        'primary' => 'id_source',
        'multilang' => true,
        'fields' => array(
            'id_source_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'source_code' =>   array('type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true, 'size' => 64),
            'position' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'unremovable' =>   array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'deleted' =>       array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>      array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>      array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
        ),
    );

    public function add($autodate = true, $nullValues = false)
    {
        if ($this->position <= 0) {
            $this->position = Source::getHigherPosition($this->id_source_type) + 1;
        }

        return parent::add($autodate, $nullValues);
    }

    public static function getHigherPosition($idSourceType)
    {
        $position = Db::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'source`
            WHERE `id_source_type` = '.(int)$idSourceType.' AND `deleted` = 0'
        );

        return is_numeric($position) ? (int)$position : -1;
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
            SELECT `position`, `id_source`
            FROM `'._DB_PREFIX_.'source`
            WHERE `id_source_type` = '.(int)$this->id_source_type.' AND `deleted` = 0
            ORDER BY `position` ASC'
        )) {
            return false;
        }

        $moved_item = null;
        foreach ($res as $row) {
            if ((int)$row['id_source'] == (int)$this->id) {
                $moved_item = $row;
            }
        }

        if (!$moved_item) {
            return false;
        }

        return (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'source`
            SET `position` = `position` '.($way ? '- 1' : '+ 1').'
            WHERE `id_source_type` = '.(int)$this->id_source_type.' AND `deleted` = 0 AND `position`
            '.($way
                ? '> '.(int)$moved_item['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_item['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'source`
            SET `position` = '.(int)$position.'
            WHERE `id_source` = '.(int)$moved_item['id_source']));
    }

    public static function codeExists($code, $idExclude = 0)
    {
        return (bool)Db::getInstance()->getValue(
            'SELECT `id_source` FROM `'._DB_PREFIX_.'source`
            WHERE `source_code` = \''.pSQL($code).'\' AND `id_source` != '.(int)$idExclude
        );
    }

    public static function getIdByCode($code)
    {
        return (int)Db::getInstance()->getValue(
            'SELECT `id_source` FROM `'._DB_PREFIX_.'source`
            WHERE `source_code` = \''.pSQL($code).'\' AND `deleted` = 0'
        );
    }

    public static function getUnremovableIds()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_source` FROM `'._DB_PREFIX_.'source` WHERE `unremovable` = 1 AND `deleted` = 0'
        );

        return array_map('intval', array_column($rows, 'id_source'));
    }

    public static function getActiveListForType($idSourceType, $idLang)
    {
        return Db::getInstance()->executeS('
            SELECT a.`id_source`, al.`name`
            FROM `'._DB_PREFIX_.'source` a
            LEFT JOIN `'._DB_PREFIX_.'source_lang` al ON (al.`id_source` = a.`id_source` AND al.`id_lang` = '.(int)$idLang.')
            WHERE a.`id_source_type` = '.(int)$idSourceType.' AND a.`deleted` = 0 AND a.`active` = 1
            ORDER BY a.`position` ASC
        ');
    }

    public static function getSourcesUsedInOrders($idLang)
    {
        return Db::getInstance()->executeS('
            SELECT DISTINCT o.`id_source`, sl.`name`
            FROM `'._DB_PREFIX_.'orders` o
            INNER JOIN `'._DB_PREFIX_.'source` s ON (s.`id_source` = o.`id_source`)
            LEFT JOIN `'._DB_PREFIX_.'source_lang` sl ON (sl.`id_source` = s.`id_source` AND sl.`id_lang` = '.(int)$idLang.')
        ');
    }

    public static function cleanPositions($idSourceType)
    {
        $return = true;

        $result = Db::getInstance()->executeS('
            SELECT `id_source`
            FROM `'._DB_PREFIX_.'source`
            WHERE `id_source_type` = '.(int)$idSourceType.' AND `deleted` = 0
            ORDER BY `position` ASC'
        );

        $i = 0;
        foreach ($result as $row) {
            $return = Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'source`
                SET `position` = '.(int)$i++.'
                WHERE `id_source` = '.(int)$row['id_source']
            ) && $return;
        }

        return $return;
    }

    public function isRemovable()
    {
        return !$this->unremovable;
    }

    public function isUsedByOrder()
    {
        return (bool)Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `'._DB_PREFIX_.'orders` WHERE `id_source` = '.(int)$this->id
        );
    }

    public function mergeOrdersInto($idTargetSource)
    {
        return (bool)Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'orders` SET `id_source` = '.(int)$idTargetSource.' WHERE `id_source` = '.(int)$this->id
        );
    }

    public static function getTotalOrderCount()
    {
        return (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'orders`');
    }

    public function delete()
    {
        if (!$this->isRemovable()) {
            return false;
        }

        if ($this->isUsedByOrder()) {
            $this->deleted = 1;
            if (!$this->update()) {
                return false;
            }
        } elseif (!parent::delete()) {
            return false;
        }

        return Source::cleanPositions($this->id_source_type);
    }

    public static function getDefaultSourceId()
    {
        return (int)Configuration::get('PS_DEFAULT_BOOKING_SOURCE');
    }
}
