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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_8_0($module)
{
    $objUpgrade = new UpgradeHotelreservationSystem180($module);

    return $objUpgrade->initUpgrade();
}

class UpgradeHotelreservationSystem180
{
    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function initUpgrade()
    {
        return $this->updateTables()
            && $this->seedBookingStatuses()
            && $this->seedPropertyTypes()
            && $this->backfillHotelPropertyType()
            && $this->installPropertyTypesTab()
            && $this->installRemainingTabs()
            && $this->migrateFeaturesToAmenities()
            && $this->migrateBranchFeaturesToBranchAmenities()
            && $this->dropMigratedFeatureTables()
            && $this->backfillBookingStatusHistory()
            && $this->dropObsoleteColumns()
            && $this->addHeaderImageSettingsLink()
            && $this->dropDeadDemandTables();
    }

    public function updateTables()
    {
        if ($sql = $this->getModuleSql()) {
            foreach ($sql as $query) {
                if ($query && !Db::getInstance()->execute(trim($query))) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getModuleSql()
    {
        return array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_image_category` (
                `id_htl_image_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_htl_image_category`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_image_category_lang` (
                `id_htl_image_category` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(128) NOT NULL,
                PRIMARY KEY (`id_htl_image_category`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_amenity` (
                `id_amenity` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_parent` int(10) unsigned NOT NULL,
                `position` int(10) unsigned NOT NULL,
                `active` int(2) NOT NULL DEFAULT '0',
                `logo_type` varchar(10) NOT NULL DEFAULT 'icon',
                `logo` varchar(255) NOT NULL DEFAULT '',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_amenity`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_amenity_lang` (
                `id_amenity` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                PRIMARY KEY (`id_amenity`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_branch_amenity` (
                `id_branch_amenity` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_hotel` int(10) unsigned NOT NULL,
                `amenity_id` int(10) unsigned NOT NULL,
                `is_featured` tinyint(1) NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_branch_amenity`),
                CONSTRAINT `fk_htl_branch_amenity_amenity` FOREIGN KEY (`amenity_id`)
                    REFERENCES `"._DB_PREFIX_."htl_amenity` (`id_amenity`) ON DELETE CASCADE
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_amenity` (
                `id_room_type_amenity` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_product` int(10) unsigned NOT NULL,
                `amenity_id` int(10) unsigned NOT NULL,
                `is_featured` tinyint(1) NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_room_type_amenity`),
                KEY `id_product` (`id_product`),
                CONSTRAINT `fk_htl_room_type_amenity_amenity` FOREIGN KEY (`amenity_id`)
                    REFERENCES `"._DB_PREFIX_."htl_amenity` (`id_amenity`) ON DELETE CASCADE
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_property_type` (
                `id_htl_property_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_htl_property_type`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_property_type_lang` (
                `id_htl_property_type` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(255) NOT NULL,
                PRIMARY KEY (`id_htl_property_type`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_booking_status` (
                `id_booking_status` int(11) NOT NULL AUTO_INCREMENT,
                `color` varchar(32) NOT NULL,
                `is_terminal` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id_booking_status`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_booking_status_lang` (
                `id_booking_status` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `name` varchar(64) NOT NULL,
                PRIMARY KEY (`id_booking_status`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_booking_status_history` (
                `id_booking_status_history` int(11) NOT NULL AUTO_INCREMENT,
                `id_htl_booking` int(11) NOT NULL,
                `id_status_from` int(11) DEFAULT NULL,
                `id_status_to` int(11) NOT NULL,
                `id_employee` int(11) DEFAULT NULL,
                `id_customer` int(11) DEFAULT NULL,
                `remark` text,
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_booking_status_history`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_connected_room` (
                `id_connected_room` int(11) NOT NULL AUTO_INCREMENT,
                `id_room` int(11) NOT NULL,
                `id_room_connected` int(11) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_connected_room`),
                UNIQUE KEY `uniq_room_connection` (`id_room`, `id_room_connected`),
                KEY `idx_id_room` (`id_room`),
                KEY `idx_id_room_connected` (`id_room_connected`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_header_image` (
                `id_header_image` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(512) NOT NULL,
                `description_color` VARCHAR(7) NOT NULL DEFAULT '#ffffff',
                `description_font_size` TINYINT(3) UNSIGNED NOT NULL DEFAULT '16',
                `description_font_weight` VARCHAR(10) NOT NULL DEFAULT '400',
                `position` INT(10) UNSIGNED NOT NULL DEFAULT '0',
                `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
                `date_add` DATETIME NOT NULL,
                `date_upd` DATETIME NOT NULL,
                PRIMARY KEY (`id_header_image`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_header_image_lang` (
                `id_header_image` INT(10) UNSIGNED NOT NULL,
                `id_lang` INT(11) NOT NULL,
                `title` VARCHAR(512) NOT NULL DEFAULT '',
                `description` VARCHAR(512) NOT NULL DEFAULT '',
                PRIMARY KEY (`id_header_image`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",

            "ALTER TABLE `"._DB_PREFIX_."htl_booking_detail`
                ADD COLUMN `id_selling_object` int(10) unsigned DEFAULT NULL,
                ADD COLUMN `selling_object_name` varchar(255) DEFAULT NULL,
                ADD COLUMN `selling_object_plural_name` varchar(255) DEFAULT NULL,
                ADD COLUMN `property_type_name` varchar(255) DEFAULT NULL;",

            "ALTER TABLE `"._DB_PREFIX_."htl_branch_info`
                ADD COLUMN `id_property_type` int(10) unsigned NOT NULL DEFAULT '0',
                ADD COLUMN `tourism_tax_collection_type` tinyint(1) unsigned NOT NULL DEFAULT '0';",

            "ALTER TABLE `"._DB_PREFIX_."htl_image`
                ADD COLUMN `id_htl_image_category` int(10) unsigned DEFAULT NULL,
                ADD KEY `id_hotel` (`id_hotel`),
                ADD KEY `id_htl_image_category` (`id_htl_image_category`);",

            "ALTER TABLE `"._DB_PREFIX_."htl_room_type`
                ADD COLUMN `id_selling_object` int(10) unsigned DEFAULT NULL;",
        );
    }

    /**
     * Fresh installs seed the booking-status catalog via HotelHelper::createDefaultBookingStatuses()
     * (hotelreservationsystem.php::install()) - reuse that exact same method here so an upgraded
     * site ends up with identical colors/names/is_terminal values, not a second, hand-maintained copy.
     */
    public function seedBookingStatuses()
    {
        if (Db::getInstance()->getValue('SELECT `id_booking_status` FROM `'._DB_PREFIX_.'htl_booking_status`')) {
            return true;
        }

        return (new HotelHelper())->createDefaultBookingStatuses();
    }

    /**
     * Fresh installs seed the property-type catalog via HotelHelper::createHotelDefaultPropertyTypes()
     * (hotelreservationsystem.php::install()) - reuse that exact same method here, same reasoning
     * as seedBookingStatuses() above.
     */
    public function seedPropertyTypes()
    {
        if (Db::getInstance()->getValue('SELECT `id_htl_property_type` FROM `'._DB_PREFIX_.'htl_property_type`')) {
            return true;
        }

        return (new HotelHelper())->createHotelDefaultPropertyTypes();
    }

    /**
     * htl_branch_info.id_property_type is added by updateTables() above with DEFAULT '0', which
     * is not a real row in htl_property_type - every pre-existing hotel would otherwise point at
     * a non-existent property type after upgrade. Point them at "Hotel" (the first seeded type,
     * matching the same default HotelHelper::$obj_hotel_info->id_property_type = 1 assumes
     * elsewhere) until the admin picks something else.
     */
    public function backfillHotelPropertyType()
    {
        $idDefaultPropertyType = (int) Db::getInstance()->getValue(
            'SELECT MIN(`id_htl_property_type`) FROM `'._DB_PREFIX_.'htl_property_type`'
        );

        if (!$idDefaultPropertyType) {
            return true;
        }

        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'htl_branch_info`
            SET `id_property_type` = '.$idDefaultPropertyType.'
            WHERE `id_property_type` = 0'
        );

        return true;
    }

    /**
     * Fresh installs register this tab in callInstallTab() (hotelreservationsystem.php::install());
     * upgraded sites never go through that method, so the tab - and therefore the "Manage Property
     * Types" sidebar entry - would otherwise never appear after upgrading.
     */
    public function installPropertyTypesTab()
    {
        if (Tab::getIdFromClassName('AdminHotelPropertyTypes')) {
            return true;
        }

        $this->module->installTab('AdminHotelPropertyTypes', 'Manage Property Types', 'AdminHotelReservationSystemManagement');

        return true;
    }

    public function installRemainingTabs()
    {
        if (!Tab::getIdFromClassName('AdminHotelAmenities')) {
            $this->module->installTab('AdminHotelAmenities', 'Manage Amenities', 'AdminHotelReservationSystemManagement');
        }

        if (!Tab::getIdFromClassName('AdminHotelHeaderImage')) {
            $this->module->installTab('AdminHotelHeaderImage', 'Header Image Configuration', 'AdminHotelConfigurationSetting', false);
        }

        if (!Tab::getIdFromClassName('AdminHotelImageCategory')) {
            $this->module->installTab('AdminHotelImageCategory', 'Hotel Image Category', false, false);
        }

        return true;
    }

    /**
     * htl_features/htl_features_lang -> htl_amenity/htl_amenity_lang.
     * Same shape (id/parent/position/active/name), plain column copy.
     */
    public function migrateFeaturesToAmenities()
    {
        if (!$this->tableExists('htl_features')) {
            return true;
        }

        Db::getInstance()->execute(
            'INSERT IGNORE INTO `'._DB_PREFIX_.'htl_amenity`
                (`id_amenity`, `id_parent`, `position`, `active`, `date_add`, `date_upd`)
            SELECT `id`, `parent_feature_id`, `position`, `active`, `date_add`, `date_upd`
            FROM `'._DB_PREFIX_.'htl_features`'
        );

        Db::getInstance()->execute(
            'INSERT IGNORE INTO `'._DB_PREFIX_.'htl_amenity_lang`
                (`id_amenity`, `id_lang`, `name`)
            SELECT `id`, `id_lang`, `name`
            FROM `'._DB_PREFIX_.'htl_features_lang`'
        );

        return true;
    }

    /**
     * htl_branch_features.feature_id is a comma-separated list of old feature ids per hotel
     * (there is no SQL-only way to split it that works on MySQL 5.7) - parse it here and
     * write one htl_branch_amenity row per (id_hotel, amenity_id) pair.
     */
    public function migrateBranchFeaturesToBranchAmenities()
    {
        if (!$this->tableExists('htl_branch_features')) {
            return true;
        }

        $rows = Db::getInstance()->executeS(
            'SELECT `id_hotel`, `feature_id`, `date_add`, `date_upd`
            FROM `'._DB_PREFIX_.'htl_branch_features`
            WHERE `feature_id` IS NOT NULL AND `feature_id` != \'\''
        );

        if (!$rows) {
            return true;
        }

        foreach ($rows as $row) {
            $amenityIds = array_filter(array_map('intval', explode(',', $row['feature_id'])));

            foreach ($amenityIds as $idAmenity) {
                Db::getInstance()->execute(
                    'INSERT IGNORE INTO `'._DB_PREFIX_.'htl_branch_amenity`
                        (`id_hotel`, `amenity_id`, `is_featured`, `date_add`, `date_upd`)
                    VALUES ('.(int) $row['id_hotel'].', '.(int) $idAmenity.', 0, \''.pSQL($row['date_add']).'\', \''.pSQL($row['date_upd']).'\')'
                );
            }
        }

        return true;
    }

    /**
     * Old features data has been copied into htl_amenity/htl_branch_amenity above -
     * the old tables are now safe to remove.
     */
    public function dropMigratedFeatureTables()
    {
        foreach (array('htl_features', 'htl_features_lang', 'htl_branch_features') as $tableName) {
            Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.$tableName.'`');
        }

        return true;
    }

    /**
     * htl_booking_detail.is_refunded/is_cancelled are being dropped in 1.8.0 (superseded by
     * htl_booking_status_history). Translate any existing 1/0 flags into a status-history row
     * before the columns go away, so the historical state isn't silently lost.
     */
    public function backfillBookingStatusHistory()
    {
        if (!$this->columnExists('htl_booking_detail', 'is_refunded')) {
            return true;
        }

        $bookings = Db::getInstance()->executeS(
            'SELECT `id`, `is_refunded`, `is_cancelled`, `date_upd`
            FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `is_refunded` = 1 OR `is_cancelled` = 1'
        );

        if (!$bookings) {
            return true;
        }

        foreach ($bookings as $booking) {
            $idStatusTo = $booking['is_cancelled'] ? HotelBookingDetail::STATUS_CANCELLED : HotelBookingDetail::STATUS_ASSIGNED;

            // HotelBookingDetail::changeStatus() always writes id_status and the history row
            // together - the history row alone is not enough, every booking query in the
            // codebase reads id_status directly, not the history table.
            Db::getInstance()->execute(
                'UPDATE `'._DB_PREFIX_.'htl_booking_detail`
                SET `id_status` = '.(int) $idStatusTo.'
                WHERE `id` = '.(int) $booking['id']
            );

            Db::getInstance()->execute(
                'INSERT INTO `'._DB_PREFIX_.'htl_booking_status_history`
                    (`id_htl_booking`, `id_status_from`, `id_status_to`, `id_employee`, `id_customer`, `remark`, `date_add`)
                VALUES (
                    '.(int) $booking['id'].', NULL, '.(int) $idStatusTo.', NULL, NULL,
                    \'Migrated from is_refunded/is_cancelled during the 1.8.0 upgrade.\',
                    \''.pSQL($booking['date_upd']).'\'
                )'
            );
        }

        return true;
    }

    /**
     * Drop columns superseded by the migrations above - only after their data has been
     * copied forward, never before.
     */
    public function dropObsoleteColumns()
    {
        if ($this->columnExists('htl_booking_detail', 'is_refunded')) {
            Db::getInstance()->execute(
                'ALTER TABLE `'._DB_PREFIX_.'htl_booking_detail`
                    DROP COLUMN `is_refunded`,
                    DROP COLUMN `is_cancelled`'
            );
        }

        if ($this->columnExists('htl_cart_booking_data', 'extra_demands')) {
            Db::getInstance()->execute(
                'ALTER TABLE `'._DB_PREFIX_.'htl_cart_booking_data`
                    DROP COLUMN `extra_demands`'
            );
        }

        return true;
    }

    /**
     * New "Landing Page Header Media" settings-dashboard card (#1764).
     */
    public function addHeaderImageSettingsLink()
    {
        if (Db::getInstance()->getValue(
            'SELECT `id_settings_link` FROM `'._DB_PREFIX_.'htl_settings_link`
            WHERE `link` = \'index.php?controller=AdminHotelHeaderImage\''
        )) {
            return true;
        }

        $objSettingsLink = new HotelSettingsLink();
        $objSettingsLink->icon = 'icon-picture';
        $objSettingsLink->link = 'index.php?controller=AdminHotelHeaderImage';
        $objSettingsLink->new_window = 0;
        $objSettingsLink->position = $objSettingsLink->getHigherPosition();
        $objSettingsLink->unremovable = 0;
        $objSettingsLink->active = 1;

        foreach (Language::getLanguages(true) as $lang) {
            $objSettingsLink->name[$lang['id_lang']] = 'Landing Page Header Media';
            $objSettingsLink->hint[$lang['id_lang']] = 'Configure and manage header images or videos displayed on the home page.';
        }

        return (bool) $objSettingsLink->add();
    }

    /**
     * The old "Additional Facility" demand system - confirmed zero live code references
     * (only a decade-old, never-rerun upgrade-1.4.0.php mentions these table names); nothing
     * to migrate, just drop.
     */
    public function dropDeadDemandTables()
    {
        $deadTables = array(
            'htl_booking_demands',
            'htl_booking_demands_tax',
            'htl_room_type_global_demand',
            'htl_room_type_global_demand_lang',
            'htl_room_type_global_demand_advance_option',
            'htl_room_type_global_demand_advance_option_lang',
            'htl_room_type_demand_price',
            'htl_room_type_demand',
        );

        foreach ($deadTables as $tableName) {
            Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.$tableName.'`');
        }

        return true;
    }

    private function tableExists($tableName)
    {
        return (bool) Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.pSQL($tableName).'\'');
    }

    private function columnExists($tableName, $columnName)
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = \''._DB_PREFIX_.pSQL($tableName).'\'
            AND COLUMN_NAME = \''.pSQL($columnName).'\''
        );
    }
}
