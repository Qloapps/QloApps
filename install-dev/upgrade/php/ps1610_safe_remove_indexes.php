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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function ps1610_safe_remove_indexes()
{
    $keysToRemove = array(
        array('table' => 'shop', 'key' => 'id_group_shop'),
        array('table' => 'specific_price', 'key' => 'id_product_2'),
        array('table' => 'hook_module', 'key' => 'position'),
        array('table' => 'cart_product', 'key' => 'PRIMARY'),
        array('table' => 'cart_product', 'key' => 'cart_product_index'),
    );

    foreach ($keysToRemove as $details) {
        $indexes = Db::getInstance()->executeS('
            SHOW INDEX FROM `'._DB_PREFIX_.$details['table'].'` WHERE Key_name = \''.$details['key'].'\'
        ');
        if (count($indexes) > 0) {
            Db::getInstance()->execute('
                ALTER TABLE `'._DB_PREFIX_.$details['table'].'` DROP KEY `'.$details['key'].'`
            ');
        }
    }
}
