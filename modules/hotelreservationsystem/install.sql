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

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_type` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_product` int(11) NOT NULL,
	`id_hotel` int(11) NOT NULL,
	`adult` smallint(6) NOT NULL,
	`children` smallint(6) NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_information` (
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
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_branch_info` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`id_category` int(10) unsigned NOT NULL,
	`phone` varchar(32) DEFAULT NULL,
	`email` varchar(128) NOT NULL,
	`check_in` varchar(255) DEFAULT NULL,
	`check_out` varchar(255) DEFAULT NULL,
	`rating` int(2) unsigned NOT NULL,
	`city` varchar(64) NOT NULL,
	`state_id` int(10) unsigned NOT NULL,
	`country_id` int(10) unsigned NOT NULL,
	`zipcode` varchar(12) NOT NULL,
	`address` text DEFAULT NULL,
	`active` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`latitude` decimal(10,8) NOT NULL,
	`longitude` decimal(11,8) NOT NULL,
	`map_formated_address` text NOT NULL,
	`map_input_text` text NOT NULL,
	`active_refund` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_branch_info_lang` (
	`id` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`hotel_name` varchar(255) DEFAULT NULL,
	`short_description` text,
	`description` text,
	`policies` text,
	PRIMARY KEY (`id`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_image` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`id_hotel` int(10) unsigned NOT NULL,
	`hotel_image_id` varchar(32) NOT NULL,
	`cover` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_branch_features` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`id_hotel` int(10) unsigned NOT NULL,
	`feature_id` varchar(255) DEFAULT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_features` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`parent_feature_id` int(10) unsigned NOT NULL,
	`position` int(10) unsigned NOT NULL,
	`active` int(2) NOT NULL DEFAULT '0',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_features_lang` (
	`id` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`id`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_cart_booking_data` (
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
	`is_refunded` tinyint(1) NOT NULL DEFAULT '0',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_booking_detail` (
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
	`adult` smallint(6) NOT NULL DEFAULT '0',
	`children` smallint(6) NOT NULL DEFAULT '0',
	`is_refunded` tinyint(1) NOT NULL DEFAULT '0',
	-- `available_for_order` tinyint(1) NOT NULL DEFAULT '0',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_booking_demands` (
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
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_booking_demands_tax` (
	`id_booking_demand` int(11) NOT NULL AUTO_INCREMENT,
	`id_tax` int(11) NOT NULL,
	`unit_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.00',
  	`total_amount` DECIMAL(16, 6) NOT NULL DEFAULT '0.00',
	PRIMARY KEY (`id_booking_demand`, `id_tax`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_status` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`status` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_allotment_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_advance_payment` (
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
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_order_refund_rules` (
	`id_refund_rule` int(11) NOT NULL AUTO_INCREMENT,
	`payment_type` int(2) unsigned NOT NULL,
	`deduction_value_full_pay` decimal(20,6) NOT NULL,
	`deduction_value_adv_pay` decimal(20,6) NOT NULL,
	`days` decimal(35,0) NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_refund_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_order_refund_rules_lang` (
	`id_refund_rule` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`name` varchar(255) DEFAULT NULL,
	`description` text,
	PRIMARY KEY (`id_refund_rule`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_branch_refund_rules` (
	`id_hotel_refund_rule` int(11) NOT NULL AUTO_INCREMENT,
	`id_refund_rule` int(10) unsigned NOT NULL,
	`id_hotel` int(10) unsigned NOT NULL,
	`position` int(10) unsigned NOT NULL DEFAULT '0',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_hotel_refund_rule`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_order_restrict_date` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_hotel` int(11) NOT NULL,
	`max_order_date` datetime NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_type_feature_pricing` (
	`id_feature_price` int(11) NOT NULL AUTO_INCREMENT,
	`id_product` int(11) NOT NULL,
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
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_type_feature_pricing_lang` (
	`id_feature_price` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`feature_price_name` varchar(255) character set utf8 NOT NULL,
	PRIMARY KEY (`id_feature_price`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_htl_room_type_feature_pricing_group` (
  `id_feature_price` int(10) unsigned NOT NULL,
  `id_group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_feature_price`,`id_group`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_type_global_demand` (
	`id_global_demand` int(11) NOT NULL AUTO_INCREMENT,
	`price` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`id_tax_rules_group` int(10) unsigned NOT NULL DEFAULT '0',
	`price_calc_method` tinyint(1) NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_global_demand`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_type_global_demand_lang` (
	`id_global_demand` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`name` varchar(255) character set utf8 NOT NULL,
	PRIMARY KEY (`id_global_demand`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_type_global_demand_advance_option` (
	`id_option` int(11) NOT NULL AUTO_INCREMENT,
	`id_global_demand` int(11) NOT NULL,
	`price` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_option`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_type_global_demand_advance_option_lang` (
	`id_option` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`name` varchar(255) character set utf8 NOT NULL,
	PRIMARY KEY (`id_option`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_type_demand_price` (
	`id_room_type_demand_price` int(11) NOT NULL AUTO_INCREMENT,
	`id_product` int(10) unsigned NOT NULL,
	`id_global_demand` int(10) unsigned NOT NULL,
	`id_option` int(10) unsigned NOT NULL,
	`price` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_room_type_demand_price`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_type_demand` (
	`id_room_type_demand` int(11) NOT NULL AUTO_INCREMENT,
	`id_product` int(10) unsigned NOT NULL,
	`id_global_demand` int(10) unsigned NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_room_type_demand`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_room_disable_dates` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`id_room_type` int(11) NOT NULL,
	`id_room` int(11) NOT NULL,
	`date_from` date NOT NULL,
	`date_to` date NOT NULL,
	`reason` text,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `PREFIX_htl_access` (
  `id_profile` int(10) unsigned NOT NULL,
  `id_hotel` int(10) unsigned NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`id_profile`, `id_hotel`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
