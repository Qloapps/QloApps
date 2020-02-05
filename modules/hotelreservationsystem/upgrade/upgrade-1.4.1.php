<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_1($object, $install = false)
{
    $QLO_VERSION = Configuration::get('_QLO_INSTALL_VERSION_');

    if ((!$QLO_VERSION) || (empty($QLO_VERSION)) || ($QLO_VERSION < $object->version)) {
        if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'htl_booking_demands_tax` (
				`id_booking_demand` int(11) NOT NULL AUTO_INCREMENT,
				`id_tax` int(11) NOT NULL,
				`unit_amount` DECIMAL(16, 6) NOT NULL DEFAULT \'0.00\',
				`total_amount` DECIMAL(16, 6) NOT NULL DEFAULT \'0.00\',
				PRIMARY KEY (`id_booking_demand`, `id_tax`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;')) {
            return false;
        }
		
        if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'htl_access` (
			  `id_profile` int(10) unsigned NOT NULL,
			  `id_hotel` int(10) unsigned NOT NULL,
			  `access` int(11) NOT NULL,
			  PRIMARY KEY (`id_profile`, `id_hotel`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;')) {
            return false;
        }
		
	if (!Db::getInstance()->Execute('
			ALTER TABLE `'._DB_PREFIX_.'htl_booking_demands` DROP COLUMN `price`;')) {
            return false;
        }
		
	if (!Db::getInstance()->Execute('
			ALTER TABLE `'._DB_PREFIX_.'htl_booking_demands` 
				ADD COLUMN `unit_price_tax_excl` decimal(20,6) NOT NULL DEFAULT \'0.000000\', 
				ADD COLUMN `unit_price_tax_incl` decimal(20,6) NOT NULL DEFAULT \'0.000000\', 
				ADD COLUMN `total_price_tax_excl` decimal(20,6) NOT NULL DEFAULT \'0.000000\', 
				ADD COLUMN `total_price_tax_incl` decimal(20,6) NOT NULL DEFAULT \'0.000000\', 
				ADD COLUMN `price_calc_method` int(11) unsigned DEFAULT \'0\', 
				ADD COLUMN `id_tax_rules_group` int(11) unsigned DEFAULT \'0\', 
				ADD COLUMN `tax_computation_method` tinyint(1) unsigned NOT NULL DEFAULT \'0\' ;')) {
            return false;
        }
		
	if (!Db::getInstance()->Execute('
			ALTER TABLE `'._DB_PREFIX_.'htl_room_type_global_demand` 
				ADD COLUMN `id_tax_rules_group` int(10) unsigned NOT NULL DEFAULT \'0\',
				ADD COLUMN `price_calc_method` tinyint(1) NOT NULL;')) {
            return false;
        }
	if (!Db::getInstance()->Execute("
	            CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_custom_navigation_link` (
                `id_navigation_link` int(11) NOT NULL AUTO_INCREMENT,
                `link` text NOT NULL,
                `is_custom_link` tinyint(1) NOT NULL,
                `id_cms` int(11) NOT NULL DEFAULT '0',
                `position` int(11) unsigned NOT NULL DEFAULT '0',
                `show_at_navigation` tinyint(1) NOT NULL DEFAULT '0',
                `show_at_footer` tinyint(1) NOT NULL DEFAULT '0',
                `active` tinyint(1) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_navigation_link`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")) {
            return false;
        }
	
	if (!Db::getInstance()->Execute("
            CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_custom_navigation_link_lang` (
                `id_navigation_link` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `name` varchar(255) NOT NULL,
                PRIMARY KEY (`id_navigation_link`, `id_lang`)
                ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 ;")) {
            return false;
        }

        Configuration::updateValue('_QLO_INSTALL_VERSION_', '1.4.1');
	
	//Update theme name
	if (!Db::getInstance()->Execute("
		UPDATE `"._DB_PREFIX_."theme` SET name='hotel-reservation-theme'
    		  WHERE name='hotel-theme' AND directory='hotel-reservation-theme';")) {
            return false;
        }

	//Add news Hook
	$object->registerHook(
            array (
                'actionObjectProfileAddAfter',
                'actionObjectProfileDeleteBefore',
            )
        );
    }
    return true;
}
