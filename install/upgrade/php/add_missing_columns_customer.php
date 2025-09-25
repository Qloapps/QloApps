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

function add_missing_columns_customer()
{
    $db = Db::getInstance();
    $res = true;
    $current_fields = $db->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'customer`');
    foreach ($current_fields as $k => $field) {
        $current_fields[$k] = $field['Field'];
    }

    $missing_fields = array(
        'id_risk' => 'ALTER TABLE `'._DB_PREFIX_.'customer`
			ADD `id_risk` int(10) unsigned NOT NULL DEFAULT "1"',
        'company' => 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD `company` varchar(64)',
        'siret' => 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD `siret` varchar(14)',
        'ape' => 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD `ape` varchar(5)',
        'website' => 'ALTER TABLE `'._DB_PREFIX_.'customer` ADD `website` varchar(128)',
        'outstanding_allow_amount' => 'ALTER TABLE `'._DB_PREFIX_.'customer`
			ADD `outstanding_allow_amount` DECIMAL( 10,6 ) NOT NULL default "0.00"',
        'show_public_prices' => 'ALTER TABLE `'._DB_PREFIX_.'customer`
			ADD `show_public_prices` tinyint(1) unsigned NOT NULL default "0"',
        'max_payment_days' => 'ALTER TABLE `'._DB_PREFIX_.'customer`
			ADD `max_payment_days` int(10) unsigned NOT NULL default "60"'
    );
    
    foreach ($missing_fields as $field => $query) {
        if (!in_array($field, $current_fields)) {
            $res &= $db->execute($query);
        }
    }

    return $res;
}
