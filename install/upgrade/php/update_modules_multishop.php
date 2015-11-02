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

function update_modules_multishop()
{
    $block_cms_installed = (bool)Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'module` WHERE name = "blockcms"');
    if ($block_cms_installed) {
        Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blocklink_shop` (
			`id_blocklink` int(2) NOT NULL AUTO_INCREMENT,
			`id_shop` varchar(255) NOT NULL,
			PRIMARY KEY(`id_blocklink`, `id_shop`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8');

        Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'cms_block_shop` (
			`id_cms_block` int(10) unsigned NOT NULL auto_increment,
			`id_shop` int(10) unsigned NOT NULL,
			PRIMARY KEY (`id_cms_block`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');

        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'cms_block_shop (cms_block, id_shop)
			(SELECT id_cms_block, 1 FROM '._DB_PREFIX_.'cms_block)');
    }
    
    $block_link_installed = (bool)Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'module` WHERE name = "blocklink"');
    if ($block_link_installed) {
        Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'blocklink_shop` (
			`id_blocklink` int(2) NOT NULL AUTO_INCREMENT,
			`id_shop` varchar(255) NOT NULL,
			PRIMARY KEY(`id_blocklink`, `id_shop`))
			ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8');
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'blocklink_shop` (id_blocklink, id_shop)
			(SELECT id_blocklink, 1 FROM `'._DB_PREFIX_.'blocklink`)');
    }
    return true;
}
