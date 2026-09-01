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

function shop_url()
{
    $host = Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'configuration` WHERE name="CANONICAL_URL"');
    if (!$host) {
        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
    }

    $res = true;
    $exist = Db::getInstance()->getValue('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \'PS_SHOP_DOMAIN\'');
    if ($exist) {
        $res &= Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.pSQL($host).'" WHERE `name` = \'PS_SHOP_DOMAIN\'');
    } else {
        $res &= Db::getInstance()->getValue('INSERT INTO `'._DB_PREFIX_.'configuration` (name, value) VALUES ("PS_SHOP_DOMAIN", "'.pSQL($host).'")');
    }

    $exist = Db::getInstance()->getValue('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \'PS_SHOP_DOMAIN_SSL\'');
    if ($exist) {
        $res &= Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'configuration` SET value = "'.pSQL($host).'" WHERE `name` = \'PS_SHOP_DOMAIN_SSL\'');
    } else {
        $res &= Db::getInstance()->getValue('INSERT INTO `'._DB_PREFIX_.'configuration` (name, value) VALUES ("PS_SHOP_DOMAIN_SSL", "'.pSQL($host).'")');
    }

    return $res;
}
