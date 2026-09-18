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

function update_module_product_comments()
{
    if (Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name`="productcomments"')) {
        Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'product_comment_usefulness` (
			  `id_product_comment` int(10) unsigned NOT NULL,
			  `id_customer` int(10) unsigned NOT NULL,
			  `usefulness` tinyint(1) unsigned NOT NULL,
			  PRIMARY KEY (`id_product_comment`, `id_customer`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');

        Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'product_comment_report` (
			  `id_product_comment` int(10) unsigned NOT NULL,
			  `id_customer` int(10) unsigned NOT NULL,
			  PRIMARY KEY (`id_product_comment`, `id_customer`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
    }
}
