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

class WkTestimonialBlockDb
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
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_testimonials_block_data` (
                `id_testimonial_block` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `designation` text NOT NULL,
                `active` tinyint(1) NOT NULL,
                `position` int(10) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_testimonial_block`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",
            
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_testimonials_block_data_lang` (
                `id_testimonial_block` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `testimonial_content` text NOT NULL,
                PRIMARY KEY (`id_testimonial_block`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;"
        );
    }

    public function dropTables()
    {
        return DB::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'htl_testimonials_block_data`,
            `'._DB_PREFIX_.'htl_testimonials_block_data_lang`
        ');
    }
}
