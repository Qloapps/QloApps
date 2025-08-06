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

class WkHotelFeaturesBlockDb
{
    public function createTables()
    {
        if ($sqls = $this->getModuleSqls()) {
            foreach ($sqls as $query) {
                if ($query) {
                    if (!DB::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function getModuleSqls()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_features_block_data` (
                `id_features_block` int(11) NOT NULL AUTO_INCREMENT,
                `active` tinyint(1) NOT NULL,
                `position` int(11) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_features_block`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_features_block_data_lang` (
                `id_features_block` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `feature_title` text NOT NULL,
                `feature_description` text NOT NULL,
                PRIMARY KEY (`id_features_block`, `id_lang`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;"
        );
    }

    public function dropTables()
    {
        return DB::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_features_block_data`,
            `'._DB_PREFIX_.'htl_features_block_data_lang`;
        ');
    }
}
