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

class HotelReservationSystemDb
{
    public function getModuleSql()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `id_hotel` int(11) NOT NULL,
                `adults` smallint(6) NOT NULL DEFAULT '2',
                `children` smallint(6) NOT NULL DEFAULT '0',
                `max_adults` smallint(6) NOT NULL DEFAULT '2',
                `max_children` smallint(6) NOT NULL DEFAULT '0',
                `max_guests` smallint(6) NOT NULL DEFAULT '2',
                `min_los` smallint(6) NOT NULL DEFAULT '1',
                `max_los` smallint(6) NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_information` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `id_hotel` int(11) NOT NULL,
                `room_num` varchar(225) NOT NULL,
                `id_status` int(11) NOT NULL,
                `floor` text NOT NULL,
                `comment` text NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_branch_info` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_category` int(10) unsigned NOT NULL,
                `email` varchar(128) NOT NULL,
                `check_in` varchar(255) DEFAULT NULL,
                `check_out` varchar(255) DEFAULT NULL,
                `rating` int(2) unsigned NOT NULL,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `latitude` decimal(10,8) NOT NULL,
                `longitude` decimal(11,8) NOT NULL,
                `map_formated_address` text NOT NULL,
                `map_input_text` text NOT NULL,
                `active_refund` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_branch_info_lang` (
                `id` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `hotel_name` varchar(255) DEFAULT NULL,
                `short_description` text,
                `description` text,
                `policies` text,
                PRIMARY KEY (`id`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_image` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_hotel` int(10) unsigned NOT NULL,
                `cover` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY  (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_branch_features` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_hotel` int(10) unsigned NOT NULL,
                `feature_id` varchar(255) DEFAULT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_features` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `parent_feature_id` int(10) unsigned NOT NULL,
                `position` int(10) unsigned NOT NULL,
                `active` int(2) NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_features_lang` (
                `id` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                PRIMARY KEY (`id`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_cart_booking_data` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_cart` int(11) NOT NULL,
                `id_guest` int(11) NOT NULL,
                `id_order` int(11) NOT NULL,
                `id_customer` int(11) NOT NULL,
                `id_currency` int(11) NOT NULL,
                `id_product` int(11) NOT NULL,
                `id_room` int(11) NOT NULL,
                `id_hotel` int(11) NOT NULL,
                `quantity` int(11) NOT NULL,
                `booking_type` tinyint(4) NOT NULL,
                `comment` text NOT NULL,
                `is_back_order` tinyint(4) NOT NULL,
                `extra_demands` text NOT NULL,
                `date_from` datetime NOT NULL,
                `date_to` datetime NOT NULL,
                `adults` smallint(6) NOT NULL,
                `children` smallint(6) NOT NULL,
                `child_ages` text NOT NULL,
                `is_refunded` tinyint(1) NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_booking_detail` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `id_order` int(11) NOT NULL,
                `id_order_detail` int(11) NOT NULL,
                `id_cart` int(11) NOT NULL,
                `id_room` int(11) NOT NULL,
                `id_hotel` int(11) NOT NULL,
                `id_customer` int(11) NOT NULL,
                `booking_type` tinyint(4) NOT NULL,
                `id_status` int(11) NOT NULL,
                `comment` text NOT NULL,
                `check_in` datetime NOT NULL,
                `check_out` datetime NOT NULL,
                `date_from` datetime NOT NULL,
                `date_to` datetime NOT NULL,
                `total_price_tax_excl` decimal(20,6) NOT NULL,
                `total_price_tax_incl` decimal(20,6) NOT NULL,
                `total_paid_amount` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `is_back_order` tinyint(4) NOT NULL,
                `hotel_name` varchar(255) DEFAULT NULL,
                `room_type_name` varchar(255) DEFAULT NULL,
                `city` varchar(255) NOT NULL,
                `state` varchar(255) DEFAULT NULL,
                `country` varchar(255) DEFAULT NULL,
                `zipcode` varchar(12) DEFAULT NULL,
                `phone` varchar(32) DEFAULT NULL,
                `email` varchar(128) DEFAULT NULL,
                `check_in_time` varchar(32) DEFAULT NULL,
                `check_out_time` varchar(32) DEFAULT NULL,
                `room_num` varchar(225) DEFAULT NULL,
                `adults` smallint(6) NOT NULL DEFAULT '0',
                `children` smallint(6) NOT NULL DEFAULT '0',
                `child_ages` text NOT NULL,
                `is_refunded` tinyint(1) NOT NULL DEFAULT '0',
                -- `available_for_order` tinyint(1) NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_booking_demands` (
                `id_booking_demand` int(11) NOT NULL AUTO_INCREMENT,
                `id_htl_booking` int(11) NOT NULL,
                `name` varchar(255) character set utf8 NOT NULL,
                `unit_price_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `unit_price_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `total_price_tax_excl` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `total_price_tax_incl` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `price_calc_method` int(11) unsigned DEFAULT '0',
                `id_tax_rules_group` int(11) unsigned DEFAULT '0',
                `tax_computation_method` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_booking_demand`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_booking_demands_tax` (
                `id_booking_demand` int(11) NOT NULL AUTO_INCREMENT,
                `id_tax` int(11) NOT NULL,
                `unit_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.00',
                  `total_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.00',
                PRIMARY KEY (`id_booking_demand`, `id_tax`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_status` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `status` text NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_advance_payment` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `payment_type` tinyint(4) NOT NULL,
                `value` decimal(20,6) NOT NULL,
                `id_currency` int(11) NOT NULL,
                `tax_include` tinyint(4) NOT NULL,
                `calculate_from` tinyint(4) NOT NULL,
                `active` tinyint(4) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_order_refund_rules` (
                `id_refund_rule` int(11) NOT NULL AUTO_INCREMENT,
                `payment_type` int(2) unsigned NOT NULL,
                `deduction_value_full_pay` decimal(20,6) NOT NULL,
                `deduction_value_adv_pay` decimal(20,6) NOT NULL,
                `days` decimal(35,0) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_refund_rule`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_order_refund_rules_lang` (
                `id_refund_rule` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(255) DEFAULT NULL,
                `description` text,
                PRIMARY KEY (`id_refund_rule`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_branch_refund_rules` (
                `id_hotel_refund_rule` int(11) NOT NULL AUTO_INCREMENT,
                `id_refund_rule` int(10) unsigned NOT NULL,
                `id_hotel` int(10) unsigned NOT NULL,
                `position` int(10) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_hotel_refund_rule`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_order_restrict_date` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_hotel` int(11) NOT NULL,
                `use_global_max_order_date` tinyint(1) NOT NULL,
                `max_order_date` date NOT NULL,
                `use_global_preparation_time` tinyint(1) NOT NULL,
                `preparation_time` int(11) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_feature_pricing` (
                `id_feature_price` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `id_cart` int(11) NOT NULL DEFAULT '0',
                `id_guest` int(11) NOT NULL DEFAULT '0',
                `id_room` int(11) NOT NULL DEFAULT '0',
                `date_from` date NOT NULL,
                `date_to` date NOT NULL,
                `is_special_days_exists` tinyint(1) NOT NULL,
                `date_selection_type` tinyint(1) NOT NULL,
                `special_days` text,
                `impact_way` tinyint(1) NOT NULL,
                `impact_type` tinyint(1) NOT NULL,
                `impact_value` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_feature_price`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_feature_pricing_lang` (
                `id_feature_price` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `feature_price_name` varchar(255) character set utf8 NOT NULL,
                PRIMARY KEY (`id_feature_price`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_feature_pricing_group` (
                `id_feature_price` int(10) unsigned NOT NULL,
                `id_group` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_feature_price`,`id_group`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_global_demand` (
                `id_global_demand` int(11) NOT NULL AUTO_INCREMENT,
                `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `id_tax_rules_group` int(10) unsigned NOT NULL DEFAULT '0',
                `price_calc_method` tinyint(1) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_global_demand`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_global_demand_lang` (
                `id_global_demand` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(255) character set utf8 NOT NULL,
                PRIMARY KEY (`id_global_demand`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_global_demand_advance_option` (
                `id_option` int(11) NOT NULL AUTO_INCREMENT,
                `id_global_demand` int(11) NOT NULL,
                `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_option`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_global_demand_advance_option_lang` (
                `id_option` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(255) character set utf8 NOT NULL,
                PRIMARY KEY (`id_option`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_demand_price` (
                `id_room_type_demand_price` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(10) unsigned NOT NULL,
                `id_global_demand` int(10) unsigned NOT NULL,
                `id_option` int(10) unsigned NOT NULL,
                `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_room_type_demand_price`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_demand` (
                `id_room_type_demand` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(10) unsigned NOT NULL,
                `id_global_demand` int(10) unsigned NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_room_type_demand`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_disable_dates` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_room_type` int(11) NOT NULL,
                `id_room` int(11) NOT NULL,
                `date_from` date NOT NULL,
                `date_to` date NOT NULL,
                `reason` text,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_restriction_date_range` (
            `id_rt_restriction` int(11) NOT NULL AUTO_INCREMENT,
            `id_product` int(11) NOT NULL,
            `min_los` smallint(6) unsigned NOT NULL DEFAULT '1',
            `max_los` smallint(6) unsigned NOT NULL DEFAULT '0',
            `date_from` date NOT NULL,
            `date_to` date NOT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_rt_restriction`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_access` (
                `id_profile` int(10) unsigned NOT NULL,
                `id_hotel` int(10) unsigned NOT NULL,
                `access` int(11) NOT NULL,
                PRIMARY KEY (`id_profile`, `id_hotel`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_settings_link` (
                `id_settings_link` int(11) NOT NULL AUTO_INCREMENT,
                `icon` varchar(32) character set utf8 NOT NULL,
                `link` text NOT NULL,
                `new_window` tinyint(1) NOT NULL DEFAULT '0',
                `position` int(11) unsigned NOT NULL DEFAULT '0',
                `unremovable` tinyint(1) NOT NULL DEFAULT '0',
                `active` tinyint(1) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_settings_link`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "INSERT INTO `"._DB_PREFIX_."htl_settings_link` (`id_settings_link`, `icon`, `link`, `new_window`, `position`, `unremovable`, `active`, `date_add`, `date_upd`) VALUES
            (1, 'icon-cogs', 'index.php?controller=AdminHotelGeneralSettings', 0, 0, 1, 1, NOW(), NOW()),
            (2, 'icon-list-alt', 'index.php?controller=AdminOrderRestrictSettings', 0, 1, 1, 1, NOW(), NOW()),
            (3, 'icon-dollar', 'index.php?controller=AdminHotelFeaturePricesSettings', 0, 2, 1, 1, NOW(), NOW()),
            (4, 'icon-plus-square', 'index.php?controller=AdminRoomTypeGlobalDemand', 0, 3, 1, 1, NOW(), NOW()),
            (5, 'icon-file-text', 'index.php?controller=AdminAboutHotelBlockSetting', 0, 4, 0, 1, NOW(), NOW()),
            (6, 'icon-th-list', 'index.php?controller=AdminFeaturesModuleSetting', 0, 5, 0, 1, NOW(), NOW());",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_settings_link_lang` (
                `id_settings_link` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(255) character set utf8 NOT NULL,
                `hint` varchar(255) character set utf8 NOT NULL,
                PRIMARY KEY (`id_settings_link`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "INSERT INTO `"._DB_PREFIX_."htl_settings_link_lang` (`id_settings_link`, `id_lang`, `name`, `hint`)
            SELECT 1, `id_lang`, 'General Settings', 'Configure Your hotel general gettings using this option.'
            FROM `"._DB_PREFIX_."lang`
            ORDER BY `id_lang`;",

            "INSERT INTO `"._DB_PREFIX_."htl_settings_link_lang` (`id_settings_link`, `id_lang`, `name`, `hint`)
            SELECT 2, `id_lang`, 'Order Restrict', 'Configure if you want to restrict orders till a specific date for your hotels.'
            FROM `"._DB_PREFIX_."lang`
            ORDER BY `id_lang`;",

            "INSERT INTO `"._DB_PREFIX_."htl_settings_link_lang` (`id_settings_link`, `id_lang`, `name`, `hint`)
            SELECT 3, `id_lang`, 'Advanced Price Rules', 'Here set Advanced price rules for specific dates.'
            FROM `"._DB_PREFIX_."lang`
            ORDER BY `id_lang`;",

            "INSERT INTO `"._DB_PREFIX_."htl_settings_link_lang` (`id_settings_link`, `id_lang`, `name`, `hint`)
            SELECT 4, `id_lang`, 'Additional Facilities', 'Here create Additional facilities and their prices for room types.'
            FROM `"._DB_PREFIX_."lang`
            ORDER BY `id_lang`;",

            "INSERT INTO `"._DB_PREFIX_."htl_settings_link_lang` (`id_settings_link`, `id_lang`, `name`, `hint`)
            SELECT 5, `id_lang`, 'Hotel Interior Block', 'Configure Hotel Interior block. You can display hotel interior images using this block. This block will be displayed on home page.'
            FROM `"._DB_PREFIX_."lang`
            ORDER BY `id_lang`;",

            "INSERT INTO `"._DB_PREFIX_."htl_settings_link_lang` (`id_settings_link`, `id_lang`, `name`, `hint`)
            SELECT 6, `id_lang`, 'Hotel Amenities Block', 'Configure Hotels Amenities settings. You can display hotel amenities images using this block. This block will be displayed on home page.'
            FROM `"._DB_PREFIX_."lang`
            ORDER BY `id_lang`;",
        );
    }

    public function createTables()
    {
        if ($sql = $this->getModuleSql()) {
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

    public function dropTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_room_type`,
            `'._DB_PREFIX_.'htl_room_information`,
            `'._DB_PREFIX_.'htl_branch_info`,
            `'._DB_PREFIX_.'htl_branch_info_lang`,
            `'._DB_PREFIX_.'htl_image`,
            `'._DB_PREFIX_.'htl_branch_features`,
            `'._DB_PREFIX_.'htl_features`,
            `'._DB_PREFIX_.'htl_features_lang`,
            `'._DB_PREFIX_.'htl_cart_booking_data`,
            `'._DB_PREFIX_.'htl_booking_detail`,
            `'._DB_PREFIX_.'htl_booking_demands`,
            `'._DB_PREFIX_.'htl_booking_demands_tax`,
            `'._DB_PREFIX_.'htl_room_status`,
            `'._DB_PREFIX_.'htl_room_allotment_type`,
            `'._DB_PREFIX_.'htl_advance_payment`,
            `'._DB_PREFIX_.'htl_order_refund_rules`,
            `'._DB_PREFIX_.'htl_order_refund_rules_lang`,
            `'._DB_PREFIX_.'htl_branch_refund_rules`,
            `'._DB_PREFIX_.'htl_order_restrict_date`,
            `'._DB_PREFIX_.'htl_room_type_feature_pricing`,
            `'._DB_PREFIX_.'htl_room_type_feature_pricing_lang`,
            `'._DB_PREFIX_.'htl_room_type_feature_pricing_group`,
            `'._DB_PREFIX_.'htl_room_type_global_demand`,
            `'._DB_PREFIX_.'htl_room_type_global_demand_lang`,
            `'._DB_PREFIX_.'htl_room_type_global_demand_advance_option`,
            `'._DB_PREFIX_.'htl_room_type_global_demand_advance_option_lang`,
            `'._DB_PREFIX_.'htl_room_type_demand_price`,
            `'._DB_PREFIX_.'htl_room_type_demand`,
            `'._DB_PREFIX_.'htl_room_disable_dates`,
            `'._DB_PREFIX_.'htl_settings_link`,
            `'._DB_PREFIX_.'htl_settings_link_lang`,
            `'._DB_PREFIX_.'htl_room_type_restriction_date_range`,
            `'._DB_PREFIX_.'htl_access`'
        );
    }
}
