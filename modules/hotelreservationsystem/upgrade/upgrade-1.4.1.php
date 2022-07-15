<?php
/**
* 2010-2022 Webkul.
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
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_4_1($module)
{
    return ($module->registerHook('actionObjectGroupDeleteBefore')
        && $module->registerHook('actionOrderStatusPostUpdate')
        && updateTables141()
        && createDataForNewTables141($module)
        && deleteUnusedTables()
    );
}

function createDataForNewTables141($module)
{
    populateBookingDetail();
    updateRefundRules();
    populateRefundData();

    return true;
}

function populateBookingDetail()
{
    $htlBookings = Db::getInstance()->executes(
        'SELECT hbd.*, o.`id_lang` FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        INNER JOIN `'._DB_PREFIX_.'orders` o ON(hbd.`id_order` = o.`id_order`)'
    );
    if($htlBookings) {
        foreach($htlBookings as $htlBooking) {
                $objHtlBranchInfo = new HotelBranchInformation($htlBooking['id_hotel'], $htlBooking['id_lang']);
                $objRoomInfo = new HotelRoomInformation($htlBooking['id_room']);
                $objHtlRoomType = new HotelRoomType();
                $roomTypeInfo = $objHtlRoomType->getRoomTypeInfoByIdProduct($htlBooking['id_product']);
                $objProduct = new Product($htlBooking['id_product'], false, $htlBooking['id_lang']);
                $objHtlBkDtl = new HotelBookingDetail($htlBooking['id']);
                $objHtlBkDtl->hotel_name = $objHtlBranchInfo->hotel_name;
                $objHtlBkDtl->room_type_name = $objProduct->name;
                $objHtlBkDtl->city = $objHtlBranchInfo->city;
                $objHtlBkDtl->state = State::getNameById($objHtlBranchInfo->state_id);
                $objHtlBkDtl->country = Country::getNameById($htlBooking['id_lang'], $objHtlBranchInfo->country_id);
                $objHtlBkDtl->zipcode = $objHtlBranchInfo->zipcode;
                $objHtlBkDtl->phone = $objHtlBranchInfo->phone;
                $objHtlBkDtl->email = $objHtlBranchInfo->email;
                $objHtlBkDtl->check_in_time = $objHtlBranchInfo->check_in;
                $objHtlBkDtl->check_out_time = $objHtlBranchInfo->check_out;
                $objHtlBkDtl->room_num = $objRoomInfo->room_num;
                $objHtlBkDtl->adult = $roomTypeInfo['adult'];
                $objHtlBkDtl->children = $roomTypeInfo['children'];
                $objHtlBkDtl->save();
        }
    }
}

function updateRefundRules()
{
    $refundRules = Db::getInstance()->executes(
        'SELECT * FROM `'._DB_PREFIX_.'htl_order_refund_rules`'
    );
    foreach($refundRules as $rule) {
        $objRefundRule = new HotelOrderRefundRules($rule['id_refund_rule']);
        foreach (Language::getLanguages(true) as $lang) {
            $objRefundRule->name[$lang['id_lang']] = 'Refund_rule_'.$lang['id_lang'];
            $objRefundRule->description[$lang['id_lang']] = 'Refund_desc_'.$lang['id_lang'];
        }
        $objRefundRule->payment_type = $objRefundRule->payment_type == 2 ? 1 : 2;
        $objRefundRule->save();
    }
}

function populateRefundData()
{
    $odrRefunds = Db::getInstance()->executes(
        'SELECT *, SUM(`refunded_amount`) as `total_refund_amount` FROM `'._DB_PREFIX_.'htl_order_refund_info` hor
        GROUP BY `id_order`'
    );
    foreach($odrRefunds as $odrRefund) {
        $refundState = $odrRefund['refund_stage_id'];
        if($refundState == 3) {
            $refundState = 4;
        } else if ($refundState == 4) {
            $refundState = 3;
        }
        $objOrderReturn = new OrderReturn();
        $objOrderReturn->id_customer = $odrRefund['id_customer'];
        $objOrderReturn->id_order = $odrRefund['id_order'];
        $objOrderReturn->question = $odrRefund['cancellation_reason'];
        // $objOrderReturn->id_transaction = $odrRefund['id_customer'];
        // $objOrderReturn->payment_mode = $odrRefund['id_customer'];
        $objOrderReturn->refunded_amount = $odrRefund['total_refund_amount'];
        $objOrderReturn->state = $refundState;
        $objOrderReturn->by_admin = 0;
        $objOrderReturn->save();
    }

    $odrRefunds = Db::getInstance()->executes(
        'SELECT hor.*, odr.`id_order_return`, hbd.`id` as `id_htl_booking`, hbd.`id_order_detail` FROM `'._DB_PREFIX_.'htl_order_refund_info` hor
        INNER JOIN `'._DB_PREFIX_.'order_return` odr ON (odr.`id_order` = hor.`id_order`)
        INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON
        (hbd.`id_order` = hor.`id_order`
        AND hbd.`id_product` = hor.`id_product`
        AND hbd.`date_from` = hor.`date_from`
        AND hbd.`date_to` = hor.`date_to`
        )
        '
    );
    $objHtlBooking = new HotelBookingDetail();
    foreach($odrRefunds as $odrRefund) {
        $numDays = $objHtlBooking->getNumberOfDays(
            $odrRefund['date_from'],
            $odrRefund['date_to']
        );
        $objOrderReturnDetail = new OrderReturnDetail();
        $objOrderReturnDetail->id_order_return = $odrRefund['id_order_return'];
        $objOrderReturnDetail->id_htl_booking = $odrRefund['id_htl_booking'];
        $objOrderReturnDetail->refunded_amount = $odrRefund['refunded_amount'];
        $objOrderReturnDetail->id_order_detail = $odrRefund['id_order_detail'];
        $objOrderReturnDetail->product_quantity = $numDays;
        $objOrderReturnDetail->save();
    }
}

function deleteUnusedTables(){

    return Db::getInstance()->execute(
        "DROP TABLE IF EXISTS `"._DB_PREFIX_."htl_customer_adv_payment`;
        DROP TABLE IF EXISTS `"._DB_PREFIX_."htl_customer_adv_product_payment`;
        DROP TABLE IF EXISTS `"._DB_PREFIX_."htl_order_refund_info`;
        DROP TABLE IF EXISTS `"._DB_PREFIX_."htl_order_refund_stages`;"
    );
}

function updateTables141()
{
    if ($sql = getModuleSql()) {
		foreach ($sql as $query) {
			if ($query) {
				if (!Db::getInstance()->execute(trim($query))) {
					return false;
				}
			}
		}
	}
	return true;
}

function getModuleSql()
{
	return array (
		"ALTER TABLE `"._DB_PREFIX_."htl_branch_info`
            ADD `active_refund` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `map_input_text`;",

		"ALTER TABLE `"._DB_PREFIX_."htl_cart_booking_data`
            CHANGE `is_refunded` `is_refunded` TINYINT(1) NOT NULL DEFAULT '0';",

        "ALTER TABLE `"._DB_PREFIX_."htl_booking_detail`
            CHANGE `is_refunded` `is_refunded` tinyint(1) NOT NULL DEFAULT '0',
            ADD `total_paid_amount` decimal(20,6) NOT NULL DEFAULT '0.000000' AFTER `total_price_tax_incl`,
            ADD `hotel_name`  varchar(255) DEFAULT NULL AFTER `is_back_order`,
            ADD `room_type_name` varchar(255) DEFAULT NULL AFTER `hotel_name`,
            ADD `city` varchar(255) NOT NULL AFTER `room_type_name`,
            ADD `state` varchar(255) DEFAULT NULL AFTER `city`,
            ADD `country` varchar(255) DEFAULT NULL AFTER `state`,
            ADD `zipcode` varchar(12) DEFAULT NULL AFTER `country`,
            ADD `phone` varchar(32) DEFAULT NULL AFTER `zipcode`,
            ADD `email` varchar(128) DEFAULT NULL AFTER `phone`,
            ADD `check_in_time` varchar(32) DEFAULT NULL AFTER `email`,
            ADD `check_out_time` varchar(32) DEFAULT NULL AFTER `check_in_time`,
            ADD `room_num` varchar(225) DEFAULT NULL AFTER `check_out_time`,
            ADD `adult` smallint(6) NOT NULL DEFAULT '0' AFTER `room_num`,
            ADD `children` smallint(6) NOT NULL DEFAULT '0' AFTER `adult`;",


        "ALTER TABLE `"._DB_PREFIX_."htl_order_refund_rules`
            CHANGE `id` `id_refund_rule` int(11) NOT NULL AUTO_INCREMENT;",

        "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_order_refund_rules_lang` (
            `id_refund_rule` int(10) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            `name` varchar(255) DEFAULT NULL,
            `description` text,
            PRIMARY KEY (`id_refund_rule`, `id_lang`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_feature_pricing_group` (
            `id_feature_price` int(10) unsigned NOT NULL,
            `id_group` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id_feature_price`,`id_group`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",

        "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_branch_refund_rules` (
            `id_hotel_refund_rule` int(11) NOT NULL AUTO_INCREMENT,
            `id_refund_rule` int(10) unsigned NOT NULL,
            `id_hotel` int(10) unsigned NOT NULL,
            `position` int(10) unsigned NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_hotel_refund_rule`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

        // "ALTER TABLE `"._DB_PREFIX_."order_return`
        // CHANGE `state` `state` int(10) unsigned NOT NULL DEFAULT '1',
        // ADD `id_transaction` varchar(100) NOT NULL DEFAULT '' AFTER `state`,
        // ADD `payment_mode` varchar(255) NOT NULL DEFAULT '' AFTER `id_transaction`,
        // ADD `refunded_amount` decimal(20,6) NOT NULL DEFAULT '0.000000' AFTER `payment_mode`,
        // ADD `by_admin` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `question`;",

        // "ALTER TABLE `"._DB_PREFIX_."order_return_detail`
        // ADD `id_order_return_detail` int(10) unsigned NOT NULL auto_increment FIRST,
        // ADD `id_htl_booking` int(11) NOT NULL AFTER `id_order_return`,
        // ADD `refunded_amount` decimal(20,6) NOT NULL DEFAULT '0.000000' AFTER id_htl_booking,
        // CHANGE `id_order_detail` `id_order_detail` int(10) unsigned NOT NULL DEFAULT '0',
        // ADD KEY `id_htl_booking` (`id_htl_booking`),
        // DROP PRIMARY KEY,
        // ADD PRIMARY KEY (`id_order_return_detail`);",
	);
}

