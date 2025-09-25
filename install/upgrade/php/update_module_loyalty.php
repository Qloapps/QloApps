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
* backward compatibility vouchers should be available in all categories
*/
function update_module_loyalty()
{
    $ps_loyalty_point_value = Db::getInstance()->getValue('SELECT value
		FROM `'._DB_PREFIX_.'configuration`
		WHERE name="PS_LOYALTY_POINT_VALUE"');
    if ($ps_loyalty_point_value !== false) {
        $category_list = '';
        $categories = Db::getInstance()->executeS('SELECT id_category FROM `'._DB_PREFIX_.'category`');
        foreach ($categories as $category) {
            $category_list .= $category['id_category'].',';
        }

        if (!empty($category_list)) {
            $category_list = rtrim($category_list, ',');

            $exist = Db::getInstance()->getValue('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \'PS_LOYALTY_VOUCHER_CATEGORY\'');
            if ($exist) {
                $res = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.pSQL($category_list).'" WHERE `name` = \'PS_LOYALTY_VOUCHER_CATEGORY\'');
            } else {
                $res = Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (name, value) VALUES ("PS_LOYALTY_VOUCHER_CATEGORY", "'.pSQL($category_list).'"');
            }
        }
    }
}
