<?php
/**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

class BlockWishListDb
{
    public function getModuleSql()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wishlist` (
                `id_wishlist` int(10) unsigned NOT NULL auto_increment,
                `id_customer` int(10) unsigned NOT NULL,
                `token` varchar(64) character set utf8 NOT NULL,
                `name` varchar(64) character set utf8 NOT NULL,
                `counter` int(10) unsigned NULL,
                `id_shop` int(10) unsigned default 1,
                `id_shop_group` int(10) unsigned default 1,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                `default` int(10) unsigned default 0,
                PRIMARY KEY  (`id_wishlist`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
              
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wishlist_email` (
                `id_wishlist` int(10) unsigned NOT NULL,
                `email` varchar(128) character set utf8 NOT NULL,
                `date_add` datetime NOT NULL
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
              
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wishlist_product` (
                `id_wishlist_product` int(10) NOT NULL auto_increment,
                `id_wishlist` int(10) unsigned NOT NULL,
                `id_product` int(10) unsigned NOT NULL,
                `id_product_attribute` int(10) unsigned NOT NULL,
                `quantity` int(10) unsigned NOT NULL,
                `priority` int(10) unsigned NOT NULL,
                PRIMARY KEY  (`id_wishlist_product`)
            ) ENGINE="._MYSQL_ENGINE_."  DEFAULT CHARSET=utf8;",
              
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."wishlist_product_cart` (
                `id_wishlist_product` int(10) unsigned NOT NULL,
                `id_cart` int(10) unsigned NOT NULL,
                `quantity` int(10) unsigned NOT NULL,
                `date_add` datetime NOT NULL
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
        );
    }

    public function createTables()
    {
        if ($sql = $this->getModuleSql()) {
            foreach ($sql as $query) {
                if ($query) {
                    if (!Db::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function dropTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'wishlist`,
            `'._DB_PREFIX_.'wishlist_email`,
            `'._DB_PREFIX_.'wishlist_product`,
            `'._DB_PREFIX_.'wishlist_product_cart`'
        );
    }
}
