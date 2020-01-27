<?php
/**
* 2010-2019 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkBlockNavigationMenuDb
{
    public function getModuleSql()
    {
        return array (
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_custom_navigation_link` (
                `id_navigation_link` int(11) NOT NULL AUTO_INCREMENT,
                `link` text NOT NULL,
                `is_custom_link` tinyint(1) NOT NULL,
                `id_cms` int(11) NOT NULL DEFAULT '0',
                `position` int(11) unsigned NOT NULL DEFAULT '0',
                `show_at_navigation` tinyint(1) NOT NULL DEFAULT '0',
                `show_at_footer` tinyint(1) NOT NULL DEFAULT '0',
                `active` tinyint(1) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_navigation_link`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;",
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_custom_navigation_link_lang` (
                `id_navigation_link` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `name` varchar(255) NOT NULL,
                PRIMARY KEY (`id_navigation_link`, `id_lang`)
                ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 ;",
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
            `'._DB_PREFIX_.'htl_custom_navigation_link`,
            `'._DB_PREFIX_.'htl_custom_navigation_link_lang`'
        );
    }
}

