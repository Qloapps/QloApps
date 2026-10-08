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

    public function getHotelRefundRules($idHotel = 0, $idRefundRule = 0, $detailed = 0, $idLang = 0, $sortPosition = 1, $maxDate = '')
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $days = false;
        if ($maxDate) {
            $dateToday = date('Y-m-d');
            if (strtotime($maxDate) < strtotime($dateToday)) {
                return array();
            }
            $days = HotelBookingDetail::getDays($dateToday, date('Y-m-d', strtotime($maxDate)));
            $detailed = 1;
        }

        $sql = 'SELECT hrr.* ';

        if ($detailed) {
            $sql .= ', orr.`payment_type`, orr.`deduction_value_full_pay`, orr.`deduction_value_adv_pay`, orr.`days`,
            orrl.`name`, orrl.`description`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'htl_branch_refund_rules` hrr';

        if ($detailed) {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules` orr
            ON (orr.`id_refund_rule` = hrr.`id_refund_rule`)';
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules_lang` orrl
            ON (orrl.`id_refund_rule` = orr.`id_refund_rule` AND orrl.`id_lang` = '.(int) $idLang.')';
        }

        $sql .= ' WHERE 1';

        if ($idHotel) {
            $sql .= ' AND hrr.`id_hotel` = '.(int) $idHotel;
        }
        if ($idRefundRule) {
            $sql .= ' AND hrr.`id_refund_rule` = '.(int) $idRefundRule;
        }
        if ($days !== false) {
            $sql .= ' AND orr.`days` <= '.(int) $days;
        }

        if ($detailed) {
            // when multiple rules of a hotel have the same days, only the one with the lowest position is applicable
            $sql .= ' AND hrr.`position` = (
                SELECT MIN(hrr2.`position`) FROM `'._DB_PREFIX_.'htl_branch_refund_rules` hrr2
                INNER JOIN `'._DB_PREFIX_.'htl_order_refund_rules` orr2 ON (orr2.`id_refund_rule` = hrr2.`id_refund_rule`)
                WHERE hrr2.`id_hotel` = hrr.`id_hotel` AND orr2.`days` = orr.`days`
            )';
        }

        if ($sortPosition) {
            $sql .= ' ORDER BY hrr.`position`';
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
            $condition .= ' AND `id_refund_rule` NOT IN ('.implode(',', array_map('intval', $notInVals)).')';
        }
        return Db::getInstance()->delete(
            'htl_branch_refund_rules',
            $condition
        );
    }
}