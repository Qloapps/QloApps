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

class ProductCommentsDb
{
    public function getModuleSql()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_comment` (
                `id_product_comment` int(10) unsigned NOT NULL auto_increment,
                `id_product` int(10) unsigned NOT NULL,
                `id_customer` int(10) unsigned NOT NULL,
                `id_guest` int(10) unsigned NULL,
                `title` varchar(64) NULL,
                `content` text NOT NULL,
                `customer_name` varchar(64) NULL,
                `grade` float unsigned NOT NULL,
                `validate` tinyint(1) NOT NULL,
                `deleted` tinyint(1) NOT NULL,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_product_comment`),
                KEY `id_product` (`id_product`),
                KEY `id_customer` (`id_customer`),
                KEY `id_guest` (`id_guest`)
            ) ENGINE="._MYSQL_ENGINE_."  DEFAULT CHARSET=utf8;",
            
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_comment_criterion` (
                `id_product_comment_criterion` int(10) unsigned NOT NULL auto_increment,
                `id_product_comment_criterion_type` tinyint(1) NOT NULL,
                `active` tinyint(1) NOT NULL,
                PRIMARY KEY (`id_product_comment_criterion`)
            ) ENGINE="._MYSQL_ENGINE_."  DEFAULT CHARSET=utf8;",
            
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_comment_criterion_product` (
                `id_product` int(10) unsigned NOT NULL,
                `id_product_comment_criterion` int(10) unsigned NOT NULL,
                PRIMARY KEY(`id_product`, `id_product_comment_criterion`),
                KEY `id_product_comment_criterion` (`id_product_comment_criterion`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
            
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_comment_criterion_lang` (
                `id_product_comment_criterion` INT(11) UNSIGNED NOT NULL ,
                `id_lang` INT(11) UNSIGNED NOT NULL ,
                `name` VARCHAR(64) NOT NULL ,
                PRIMARY KEY ( `id_product_comment_criterion` , `id_lang` )
            ) ENGINE="._MYSQL_ENGINE_."  DEFAULT CHARSET=utf8;",
            
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_comment_criterion_category` (
                `id_product_comment_criterion` int(10) unsigned NOT NULL,
                `id_category` int(10) unsigned NOT NULL,
                PRIMARY KEY(`id_product_comment_criterion`, `id_category`),
                KEY `id_category` (`id_category`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
            
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_comment_grade` (
                `id_product_comment` int(10) unsigned NOT NULL,
                `id_product_comment_criterion` int(10) unsigned NOT NULL,
                `grade` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_product_comment`, `id_product_comment_criterion`),
                KEY `id_product_comment_criterion` (`id_product_comment_criterion`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
            
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_comment_usefulness` (
                `id_product_comment` int(10) unsigned NOT NULL,
                `id_customer` int(10) unsigned NOT NULL,
                `usefulness` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY (`id_product_comment`, `id_customer`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
            
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_comment_report` (
                `id_product_comment` int(10) unsigned NOT NULL,
                `id_customer` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_product_comment`, `id_customer`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
            
            "INSERT IGNORE INTO `"._DB_PREFIX_."product_comment_criterion` VALUES ('1', '1', '1');",
            
            "INSERT IGNORE INTO `"._DB_PREFIX_."product_comment_criterion_lang` (`id_product_comment_criterion`, `id_lang`, `name`)
            (
                SELECT '1', l.`id_lang`, 'Quality'
                FROM `"._DB_PREFIX_."lang` l
            );",
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
            `'._DB_PREFIX_.'product_comment`,
            `'._DB_PREFIX_.'product_comment_criterion`,
            `'._DB_PREFIX_.'product_comment_criterion_product`,
            `'._DB_PREFIX_.'product_comment_criterion_lang`,
            `'._DB_PREFIX_.'product_comment_criterion_category`,
            `'._DB_PREFIX_.'product_comment_grade`,
            `'._DB_PREFIX_.'product_comment_usefulness`,
            `'._DB_PREFIX_.'product_comment_report`'
        );
    }
}
