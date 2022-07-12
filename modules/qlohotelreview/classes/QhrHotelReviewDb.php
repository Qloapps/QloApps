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

class QhrHotelReviewDb
{
    public static function createTables()
    {
        if ($sqls = QhrHotelReviewDb::getModuleSqls()) {
            foreach ($sqls as $query) {
                if (trim($query) != '') {
                    if (!DB::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public static function getModuleSqls()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qhr_hotel_review` (
                `id_hotel_review` INT(10) NOT NULL AUTO_INCREMENT,
                `id_hotel` INT(10) NOT NULL,
                `id_order` INT(10) NOT NULL,
                `rating` FLOAT UNSIGNED NOT NULL,
                `subject` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `status_abusive` TINYINT(1) DEFAULT 0,
                `status` TINYINT(1) DEFAULT 0,
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_hotel_review`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qhr_category` (
                `id_category` INT(10) NOT NULL AUTO_INCREMENT,
                `active` TINYINT(1) DEFAULT 1,
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_category`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qhr_category_lang` (
                `id_category` INT(10) NOT NULL,
                `id_lang` INT(10) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`id_category`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qhr_review_category_rating` (
                `id_hotel_review` INT(10) NOT NULL,
                `id_category` INT(10) NOT NULL,
                `rating` FLOAT UNSIGNED NOT NULL,
                PRIMARY KEY (`id_hotel_review`, `id_category`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qhr_review_usefulness` (
                `id_hotel_review` INT(10) NOT NULL,
                `id_customer` INT(10) NOT NULL,
                PRIMARY KEY (`id_hotel_review`, `id_customer`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qhr_review_report` (
                `id_hotel_review` INT(10) NOT NULL,
                `id_customer` INT(10) NOT NULL,
                PRIMARY KEY (`id_hotel_review`, `id_customer`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qhr_review_reply` (
                `id_review_reply` INT(10) NOT NULL AUTO_INCREMENT,
                `id_hotel_review` INT(10) NOT NULL,
                `id_employee` INT(10) NOT NULL DEFAULT 0,
                `message` TEXT NOT NULL,
                `date_add` DATETIME NOT NULL,
                PRIMARY KEY (`id_review_reply`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8",
        );
    }

    public static function deleteTables()
    {
        return DB::getInstance()->execute(
            "DROP TABLE IF EXISTS
            `"._DB_PREFIX_."qhr_hotel_review`,
            `"._DB_PREFIX_."qhr_category`,
            `"._DB_PREFIX_."qhr_category_lang`,
            `"._DB_PREFIX_."qhr_review_category_rating`,
            `"._DB_PREFIX_."qhr_review_usefulness`,
            `"._DB_PREFIX_."qhr_review_report`,
            `"._DB_PREFIX_."qhr_review_reply`;"
        );
    }

    public static function getModuleDefaultConfig()
    {
        return array(
            'QHR_ADMIN_APPROVAL_ENABLED' => 1,
            'QHR_MAX_IMAGES_PER_REVIEW' => 5,
            'QHR_REVIEWS_PER_PAGE' => 3,
            'QHR_REVIEW_APPROVAL_EMAIL_ENABLED' => 0,
            'QHR_REVIEW_MGMT_REPLY_EMAIL_ENABLED' => 0,
        );
    }
}
