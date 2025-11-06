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

    public static function saveDefaultCategories()
    {
        $categories = array('Food', 'Room Service');

        $languages = Language::getLanguages();
        foreach ($categories as $category) {
            $objCategory = new QhrCategory();

            $name = array();
            foreach ($languages as $language) {
                $name[$language['id_lang']] = $category;
            }

            $objCategory->name = $name;
            $objCategory->active = 1;

            if (!$objCategory->save()) {
                return false;
            }
        }

        return true;
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

    public static function truncateUserData()
    {
        $userTables = array('qhr_hotel_review',
            'qhr_review_category_rating',
            'qhr_review_usefulness',
            'qhr_review_report',
            'qhr_review_reply'
        );
        foreach ($userTables as $table) {
            DB::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.bqSQL($table).'`');
        }
        QhrHotelReview::cleanImagesDirectory();
    }

    public static function getModuleDefaultConfig()
    {
        return array(
            'QHR_ADMIN_APPROVAL_ENABLED' => 1,
            'QHR_MAX_IMAGES_PER_REVIEW' => 5,
            'QHR_REVIEWS_AT_ONCE' => 5,
            'QHR_REVIEW_APPROVAL_EMAIL_ENABLED' => 0,
            'QHR_REVIEW_MGMT_REPLY_EMAIL_ENABLED' => 0,
        );
    }
}
