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

class BusinessSourceCore extends ObjectModel
{
    public $name;
    public $code;
    public $position;
    public $unremovable;
    public $active;
    public $deleted = 0;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'business_source',
        'primary' => 'id_business_source',
        'multilang' => true,
        'fields' => array(
            'code' =>             array('type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true, 'size' => 64),
            'position' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'unremovable' =>      array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' =>           array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'deleted' =>          array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>         array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>         array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
        ),
    );

    public function add($autodate = true, $nullValues = false)
    {
        if ($this->position <= 0) {
            $this->position = BusinessSource::getHigherPosition() + 1;
        }

        return parent::add($autodate, $nullValues);
    }

    public function update($nullValues = false)
    {
        $result = parent::update($nullValues);

        if ($result && !$this->active) {
            $this->disableSources();
        }

        return $result;
    }

    public function disableSources()
    {
        return (bool)Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'source` SET `active` = 0
            WHERE `id_business_source` = '.(int)$this->id.' AND `deleted` = 0'
        );
    }

    public static function getHigherPosition()
    {
        $position = Db::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'business_source` WHERE `deleted` = 0'
        );

        return is_numeric($position) ? (int)$position : -1;
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
            SELECT `position`, `id_business_source`
            FROM `'._DB_PREFIX_.'business_source`
            WHERE `deleted` = 0
            ORDER BY `position` ASC'
        )) {
            return false;
        }

        $moved_item = null;
        foreach ($res as $row) {
            if ((int)$row['id_business_source'] == (int)$this->id) {
                $moved_item = $row;
            }
        }

        if (!$moved_item) {
            return false;
        }

        return (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'business_source`
            SET `position` = `position` '.($way ? '- 1' : '+ 1').'
            WHERE `deleted` = 0 AND `position`
            '.($way
                ? '> '.(int)$moved_item['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_item['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'business_source`
            SET `position` = '.(int)$position.'
            WHERE `id_business_source` = '.(int)$moved_item['id_business_source']));
    }

    public static function cleanPositions()
    {
        $return = true;

        $result = Db::getInstance()->executeS('
            SELECT `id_business_source`
            FROM `'._DB_PREFIX_.'business_source`
            WHERE `deleted` = 0
            ORDER BY `position` ASC'
        );

        $i = 0;
        foreach ($result as $row) {
            $return = Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'business_source`
                SET `position` = '.(int)$i++.'
                WHERE `id_business_source` = '.(int)$row['id_business_source']
            ) && $return;
        }

        return $return;
    }

    public static function codeExists($code, $idExclude = 0)
    {
        return (bool)Db::getInstance()->getValue(
            'SELECT `id_business_source` FROM `'._DB_PREFIX_.'business_source`
            WHERE `code` = \''.pSQL($code).'\' AND `id_business_source` != '.(int)$idExclude
        );
    }

    public static function getUnremovableIds()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_business_source` FROM `'._DB_PREFIX_.'business_source` WHERE `unremovable` = 1 AND `deleted` = 0'
        );

        return array_map('intval', array_column($rows, 'id_business_source'));
    }

    public static function getActiveList($idLang)
    {
        return Db::getInstance()->executeS('
            SELECT bs.`id_business_source`, bsl.`name`
            FROM `'._DB_PREFIX_.'business_source` bs
            LEFT JOIN `'._DB_PREFIX_.'business_source_lang` bsl
                ON (bsl.`id_business_source` = bs.`id_business_source` AND bsl.`id_lang` = '.(int)$idLang.')
            WHERE bs.`deleted` = 0
            ORDER BY bs.`position` ASC
        ');
    }

    public function isRemovable()
    {
        return !$this->unremovable;
    }

    /**
     * @return bool True if any Booking Source under this Business Source — deleted or not —
     * is referenced by an order. A soft-deleted Booking Source still referenced by a historical
     * order must keep resolving to a Business Source name too, so this still blocks deletion.
     */
    public function hasSourceUsedByOrder()
    {
        return (bool)Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `'._DB_PREFIX_.'source` s
            INNER JOIN `'._DB_PREFIX_.'orders` o ON o.`id_source` = s.`id_source`
            WHERE s.`id_business_source` = '.(int)$this->id
        );
    }

    /**
     * @return int Total orders placed across every Booking Source under this Business Source —
     * the denominator for each Booking Source's own percentage within this category's
     * drill-down list (as opposed to a share of every order in the system).
     */
    public function getOrderCount()
    {
        return (int)Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `'._DB_PREFIX_.'source` s
            INNER JOIN `'._DB_PREFIX_.'orders` o ON o.`id_source` = s.`id_source`
            WHERE s.`id_business_source` = '.(int)$this->id
        );
    }

    /**
     * Deleting a Business Source deletes every Booking Source under it (each through its own
     * delete() rule — soft-deleted if order-linked, hard-deleted otherwise). If any of them
     * turns out to be order-linked, this Business Source is soft-deleted too, the same way, so
     * historical orders keep resolving a category name; otherwise it hard-deletes for real.
     */
    public function delete()
    {
        if (!$this->isRemovable()) {
            return false;
        }

        $keepSoft = $this->hasSourceUsedByOrder();

        $sources = Db::getInstance()->executeS(
            'SELECT `id_source` FROM `'._DB_PREFIX_.'source` WHERE `id_business_source` = '.(int)$this->id.' AND `deleted` = 0'
        );
        foreach ($sources as $row) {
            $objSource = new Source((int)$row['id_source']);
            if (!$objSource->delete()) {
                return false;
            }
        }

        if ($keepSoft) {
            $this->deleted = 1;
            if (!$this->update()) {
                return false;
            }
        } elseif (!parent::delete()) {
            return false;
        }

        return BusinessSource::cleanPositions();
    }
}
