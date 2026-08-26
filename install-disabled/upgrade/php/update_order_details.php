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

function update_order_details()
{
    $res = Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.'order_detail` = \'reduction_percent\'');

    if (sizeof($res) == 0) {
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_detail` ADD `reduction_percent` DECIMAL(10, 2) NOT NULL default \'0.00\' AFTER `product_price`');
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_detail` ADD `reduction_amount` DECIMAL(20, 6) NOT NULL default \'0.000000\' AFTER `reduction_percent`');
    }
}
