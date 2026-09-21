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

function fix_cms_shop_1520()
{
    $res = true;
    $db = Db::getInstance();
    //test if cms_shop with 2 underscore is present to rename it.
    $result = $db->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'_cms_shop"');
    if (!is_array($result) || !count($result) || !$result) {
        $res &= create_table_cms_shop();
        if ($res) {
            insert_table_cms_to_cms_shop();
        }
    }
    //test if cms_shop with 1 underscore is present and create if not.
    $result = $db->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'cms_shop"');
    if (!is_array($result) || !count($result) || !$result) {
        $res &= create_table_cms_shop();
        if ($res) {
            insert_table_cms_to_cms_shop();
        }
    }
}

function insert_table_cms_to_cms_shop()
{
    // /!\ : _cms_shop and _cms are wrong tables name (fixed in 1.5.0.12.sql : upgrade_cms_15_rename() )
    $res &= Db::getInstance()->execute(
        'INSERT INTO `'._DB_PREFIX_.'cms_shop` (id_shop, id_cms)
	 	(SELECT 1, id_cms FROM '._DB_PREFIX_.'_cms)');
}

function create_table_cms_shop()
{
    return Db::getInstance()->execute(
            'CREATE TABLE `'._DB_PREFIX_.'cms_shop` (
				`id_cms` INT( 11 ) UNSIGNED NOT NULL,
				`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
			PRIMARY KEY (`id_cms`, `id_shop`),
			KEY `id_shop` (`id_shop`)
			) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8;');
}
