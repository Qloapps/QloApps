<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Check if all needed columns in webservice_account table exists.
 * These columns are used for the WebserviceRequest overriding.
 *
 * @return void
 */
function check_webservice_account_table()
{
    $sql = 'SHOW COLUMNS FROM '._DB_PREFIX_.'webservice_account';
    $return = DB::getInstance()->executeS($sql);
    if (count($return) < 7) {
        $sql = 'ALTER TABLE `'._DB_PREFIX_.'webservice_account` ADD `is_module` TINYINT( 2 ) NOT NULL DEFAULT \'0\' AFTER `class_name` ,
		ADD `module_name` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `is_module`';
        DB::getInstance()->executeS($sql);
    }
}
