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

function upgrade_module_1_4_0($module)
{
    return ($module->registerHook('actionObjectProfileAddAfter')
        && $module->registerHook('actionObjectProfileDeleteBefore')
        && updateTables()
        && createDataForNewTables($module)
    );
}

function createDataForNewTables()
{
    $htlBranches = Db::getInstance()->executes(
        'SELECT p.`id_profile`, hbi.`id` as id_hotel, \'1\' as access FROM `'._DB_PREFIX_.'htl_branch_info` hbi
        CROSS JOIN `'._DB_PREFIX_.'profile` p'
    );

    return Db::getInstance()->insert('htl_access', $htlBranches);
}

function updateTables()
{
    return Db::getInstance()->execute(
        'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'htl_booking_demands_tax` (
            `id_booking_demand` int(11) NOT NULL AUTO_INCREMENT,
            `id_tax` int(11) NOT NULL,
            `unit_amount` DECIMAL(16, 6) NOT NULL DEFAULT \'0.00\',
            `total_amount` DECIMAL(16, 6) NOT NULL DEFAULT \'0.00\',
            PRIMARY KEY (`id_booking_demand`, `id_tax`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

        ALTER TABLE `'._DB_PREFIX_.'htl_booking_demands`
            CHANGE `price` `unit_price_tax_excl`decimal(20,6) NOT NULL DEFAULT \'0.000000\',
            ADD `unit_price_tax_incl` decimal(20,6) NOT NULL DEFAULT \'0.000000\' AFTER `unit_price_tax_excl`,
            ADD `total_price_tax_excl` decimal(20,6) NOT NULL DEFAULT \'0.000000\' AFTER `unit_price_tax_incl`,
            ADD `total_price_tax_incl` decimal(20,6) NOT NULL DEFAULT \'0.000000\' AFTER `total_price_tax_excl`,
            ADD `price_calc_method` int(11) NOT NULL DEFAULT \'0\' AFTER `total_price_tax_incl`,
            ADD `id_tax_rules_group` int(11) NOT NULL DEFAULT \'0\' AFTER `price_calc_method`,
            ADD `tax_computation_method` tinyint(1) NOT NULL DEFAULT \'0\' AFTER `id_tax_rules_group`;

        ALTER TABLE `'._DB_PREFIX_.'htl_room_type_global_demand`
            ADD `id_tax_rules_group` int(10) unsigned NOT NULL DEFAULT \'0\' AFTER `price`,
            ADD `price_calc_method` tinyint(1) NOT NULL AFTER `id_tax_rules_group`;

        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'htl_access` (
            `id_profile` int(10) unsigned NOT NULL,
            `id_hotel` int(10) unsigned NOT NULL,
            `access` int(11) NOT NULL,
            PRIMARY KEY (`id_profile`, `id_hotel`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
    );
}


