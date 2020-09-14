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

class WkFooterPaymentBlockInfo extends ObjectModel
{
    public $name;
    public $active;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_footer_payment_block_info',
        'primary' => 'id_payment_block',
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
    ));

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        $this->image_dir = _PS_MODULE_DIR_.'wkfooterpaymentblock/views/img/payment_img/';
    }

    /**
     * Deletes current payment block from the database
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

    public function getAllPaymentBlocks($active = null, $orderBy = '', $orderWay = 'ASC')
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_footer_payment_block_info` WHERE 1';
        if (!is_null($active)) {
            $sql .= ' AND `active` = '.(int)$active;
        }
        if (Validate::isOrderBy($orderBy) && Validate::isOrderBy($orderWay)) {
            $sql .= ' ORDER BY '.pSQL($orderBy).' '.pSQL($orderWay);
        }
        return Db::getInstance()->executeS($sql);
    }

    public static function getHigherPosition()
    {
        $position = DB::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_footer_payment_block_info`'
        );
        $result = (is_numeric($position)) ? $position : -1;
        return $result + 1;
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS(
            'SELECT hpb.`id_payment_block`, hpb.`position` FROM `'._DB_PREFIX_.'htl_footer_payment_block_info` hpb
            WHERE hpb.`id_payment_block` = '.(int) $this->id.' ORDER BY `position` ASC'
        )
        ) {
            return false;
        }

        $movedPaymentBlock = false;
        foreach ($res as $paymentBlock) {
            if ((int)$paymentBlock['id_payment_block'] == (int)$this->id) {
                $movedPaymentBlock = $paymentBlock;
            }
        }

        if ($movedPaymentBlock === false) {
            return false;
        }
        return (Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_footer_payment_block_info` SET `position`= `position` '.($way ? '- 1' : '+ 1').
            ' WHERE `position`'.($way ? '> '.
            (int)$movedPaymentBlock['position'].' AND `position` <= '.(int)$position : '< '
            .(int)$movedPaymentBlock['position'].' AND `position` >= '.(int)$position)
        ) && Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_footer_payment_block_info`
            SET `position` = '.(int)$position.'
            WHERE `id_payment_block`='.(int)$movedPaymentBlock['id_payment_block']
        ));
    }

    /**
     * Reorder payment blocks position
     * Call it after deleting a payment blocks.
     * @return bool $return
     */
    public static function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_footer_payment_block_info` SET `position` = @i:=@i+1 ORDER BY `position` ASC';
        return (bool) Db::getInstance()->execute($sql);
    }
}
