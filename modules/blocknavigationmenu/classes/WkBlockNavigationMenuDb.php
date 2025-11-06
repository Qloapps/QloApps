<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
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

