<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HotelBranchRefundRules extends ObjectModel
{
    public $id_hotel;
    public $id_refund_rule;
    public $position;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_branch_refund_rules',
        'primary' => 'id_hotel_refund_rule',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_refund_rule' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
    ));

    public function getHotelRefundRules($idHotel = 0, $idRefundRule = 0, $detailed = 0, $idLang = 0, $sortPosition = 1)
    {
        $sql = 'SELECT hrr.* ';

        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        if ($detailed) {
            $sql .= ', orr.*, orrl.*';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'htl_branch_refund_rules` hrr';

        if ($detailed) {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules` orr
            ON (orr.`id_refund_rule` = hrr.`id_refund_rule`)';
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules_lang` orrl
            ON (orrl.`id_refund_rule` = orr.`id_refund_rule` AND orrl.`id_lang` = '.(int)$idLang.')';
        }

        $sql .= ' WHERE 1';

        if ($idHotel) {
            $sql .= ' AND `id_hotel` = '.(int)$idHotel;
        }
        if ($idRefundRule) {
            $sql .= ' AND `id_refund_rule` = '.(int)$idRefundRule;
        }

        if ($sortPosition) {
            $sql .= ' order by `position`';
        }

        return Db::getInstance()->executeS($sql);
    }

    public function deleteHotelRefundRules($idHotel = 0, $idRefundRule = 0, $notInVals = array())
    {
        $condition = '1';

        if ($idHotel) {
            $condition .= ' AND `id_hotel` = '.(int)$idHotel;
        }
        if ($idRefundRule) {
            $condition .= ' AND `id_refund_rule` = '.(int)$idRefundRule;
        }
        if ($notInVals) {
            $condition .= ' AND `id_refund_rule` NOT IN ('.implode(',', $notInVals).')';
        }
        return Db::getInstance()->delete(
            'htl_branch_refund_rules',
            $condition
        );
    }
}