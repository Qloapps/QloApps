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

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_5_0($module)
{
    $objUpgrade = new UpgradeHotelreservationSystem150($module);
    return $objUpgrade->initUpgrade();
}

class UpgradeHotelreservationSystem150
{
    public function __construct($module)
    {
        $this->module = $module;
    }

    public function initUpgrade()
    {
        if (!$this->moveHotelImagesToNewDirectory()
            || !$this->updateTables()
            || !$this->deleteUnusedTables()
            || !$this->updateDefautData()
            || !$this->updateHooks()

        ) {
            return false;
        }
        return true;
    }

    public function updateTables()
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

    public function deleteUnusedTables()
    {
        return Db::getInstance()->execute(
            "DROP TABLE IF EXISTS `"._DB_PREFIX_."htl_room_allotment_type`;"
        );
    }

    public function updateDefautData()
    {
        if (!$this->addSettingsLinksData()
            || !$this->updateTabs()
            || !$this->updateDefaultConfiguration()
        ) {
            return false;
        }
        return true;
    }

    public function updateHooks()
    {
        if (!$this->module->unregisterHook('actionValidateOrder')
            || !$this->module->unregisterHook('actionAdminControllerSetMedia')
            || !$this->module->unregisterHook('displayAdminProductsExtra')
            || !$this->module->unregisterHook('actionProductUpdate')
            || !$this->module->registerHook('displayLeftColumn')
            || !$this->module->registerHook('actionCartSummary')
        ) {
            return false;
        }

        return true;
    }

    public function addSettingsLinksData()
    {
        $tabs = array(
            array ('icon' => 'icon-cogs',
                'link' => 'index.php?controller=AdminHotelGeneralSettings',
                'new_window' => 0,
                'unremovable' => 1,
                'active' => 1,
                'name' => 'General Settings',
                'hint' => 'Configure Your hotel general gettings using this option.',
            ),
            array ('icon' => 'icon-dollar',
                'link' => 'index.php?controller=AdminHotelFeaturePricesSettings',
                'new_window' => 0,
                'unremovable' => 1,
                'active' => 1,
                'name' => 'Advanced Price Rules',
                'hint' => 'Here set Advanced price rules for specific dates.',
            ),
            array ('icon' => 'icon-plus-square',
                'link' => 'index.php?controller=AdminRoomTypeGlobalDemand',
                'new_window' => 0,
                'unremovable' => 1,
                'active' => 1,
                'name' => 'Additional Facilities',
                'hint' => 'Here create Additional facilities and their prices for room types.',
            ),
            array ('icon' => 'icon-file-text',
                'link' => 'index.php?controller=AdminAboutHotelBlockSetting',
                'new_window' => 0,
                'unremovable' => 0,
                'active' => 1,
                'name' => 'Hotel Interior Block',
                'hint' => 'Configure Hotel Interior block. You can display hotel interior images using this block. This block will be displayed on home page.',
            ),
            array ('icon' => 'icon-th-list',
                'link' => 'index.php?controller=AdminFeaturesModuleSetting',
                'new_window' => 0,
                'unremovable' => 0,
                'active' => 1,
                'name' => 'Hotel Amenities Block',
                'hint' => 'Configure Hotels Amenities settings. You can display hotel amenities images using this block. This block will be displayed on home page.',
            )
        );

        $Languages = Language::getLanguages(true);
        $res = true;
        foreach ($tabs as $tab) {
            $objHotelSettingsLink = new HotelSettingsLink();
            $objHotelSettingsLink->icon = $tab['icon'];
            $objHotelSettingsLink->link = $tab['link'];
            $objHotelSettingsLink->new_window = $tab['new_window'];
            $objHotelSettingsLink->position = $objHotelSettingsLink->getHigherPosition();
            $objHotelSettingsLink->unremovable = $tab['unremovable'];
            $objHotelSettingsLink->active = $tab['active'];
            foreach($Languages as $lang) {
                $objHotelSettingsLink->name[$lang['id_lang']] = $tab['name'];
                $objHotelSettingsLink->hint[$lang['id_lang']] = $tab['hint'];
            }
            $res &= $objHotelSettingsLink->save();
        }

        return $res;
    }

    public function updateTabs()
    {
        $toDeleteTabs = array('AdminOrderRestrictSettings');
        if (!empty($toDeleteTabs)) {
            foreach ($toDeleteTabs as $moduleTab) {
                $id_tab = Tab::getIdFromClassName($moduleTab);
                $objTab = new Tab($id_tab);
                $objTab->delete();
            }
        }

        $this->module->installTab('AdminBookingDocument', 'Booking Documents', false, false);

        $updateTabs = array(
            'AdminHotelFeaturePricesSettings' => array('name' => 'Advanced Price Rules'),
            'AdminRoomTypeGlobalDemand' => array('name' => 'Additional Demand Configuration')
        );
        $Languages = Language::getLanguages(true);
        foreach ($updateTabs as $key => $tab) {
            $id_tab = Tab::getIdFromClassName($key);
            $objTab = new Tab($id_tab);
            foreach($Languages as $lang) {
                $objTab->name[$lang['id_lang']] = $tab['name'];
            }
            $objTab->save();
        }

        return true;
    }

    public function updateDefaultConfiguration()
    {
        Configuration::updateValue('WK_GLOBAL_CHILD_MAX_AGE', 15);
        Configuration::updateValue('WK_GLOBAL_MAX_CHILD_IN_ROOM', 3);
        Configuration::updateValue('GLOBAL_PREPARATION_TIME', 0);

        // Search Fields
        Configuration::updateValue('PS_FRONT_SEARCH_TYPE', HotelBookingDetail::SEARCH_TYPE_OWS);
        Configuration::updateValue('PS_FRONT_OWS_SEARCH_ALGO_TYPE', HotelBookingDetail::SEARCH_EXACT_ROOM_TYPE_ALGO);
        Configuration::updateValue('PS_FRONT_ROOM_UNIT_SELECTION_TYPE', HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY);
        Configuration::updateValue('PS_BACKOFFICE_SEARCH_TYPE', HotelBookingDetail::SEARCH_TYPE_OWS);
        Configuration::updateValue('PS_BACKOFFICE_OWS_SEARCH_ALGO_TYPE', HotelBookingDetail::SEARCH_ALL_ROOM_TYPE_ALGO);
        Configuration::updateValue('PS_BACKOFFICE_ROOM_BOOKING_TYPE', HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY);
        Configuration::updateValue('PS_LOS_RESTRICTION_BO', 0);

        return true;
    }

    public function moveHotelImagesToNewDirectory()
    {
        $images = Db::getInstance()->executeS('SELECT `id`, `id_hotel`, `hotel_image_id` FROM `'._DB_PREFIX_.'htl_image`');
        foreach($images as $image) {
            if (!file_exists(_PS_HOTEL_IMG_DIR_.$image['id_hotel'].'/')) {
                // Apparently sometimes mkdir cannot set the rights, and sometimes chmod can't. Trying both.
                $success = @mkdir(_PS_HOTEL_IMG_DIR_.$image['id_hotel'].'/', 0755, true);
                $chmod = @chmod(_PS_HOTEL_IMG_DIR_.$image['id_hotel'].'/', 0755);

                // Create an index.php file in the new folder
                if (($success || $chmod)
                    && !file_exists(_PS_HOTEL_IMG_DIR_.$image['id_hotel'].'/'.'index.php')
                    && file_exists(_PS_HOTEL_IMG_DIR_.'index.php')) {
                    @copy(_PS_HOTEL_IMG_DIR_.'index.php', _PS_HOTEL_IMG_DIR_.$image['id_hotel'].'/'.'index.php');
                }
            }
            // move image from old directory to new directory
            copy(
                _PS_HOTEL_IMG_DIR_.$image['hotel_image_id'].'.'.'jpg',
                _PS_HOTEL_IMG_DIR_.$image['id_hotel'].'/'.$image['id'].'.'.'jpg'
            );
            @unlink(_PS_HOTEL_IMG_DIR_.$image['hotel_image_id'].'.'.'jpg');
        }
        return true;
    }

    public function getModuleSql()
    {
        return array (
            "ALTER TABLE `"._DB_PREFIX_."htl_room_type`
                CHANGE `adult` `adults` smallint(6) NOT NULL DEFAULT '2',
                ADD `max_adults` smallint(6) NOT NULL DEFAULT '2' AFTER `children`,
                ADD `max_children` smallint(6) NOT NULL DEFAULT '0' AFTER `max_adults`,
                ADD `max_guests` smallint(6) NOT NULL DEFAULT '2' AFTER `max_children`,
                ADD `min_los` smallint(6) NOT NULL DEFAULT '1' AFTER `max_guests`,
                ADD `max_los` smallint(6) NOT NULL DEFAULT '0' AFTER `min_los`;",

            "UPDATE `"._DB_PREFIX_."htl_room_type`
                SET `max_adults` = `adults`,
                `max_children` = `children`,
                `max_guests` = adults + children;",

            "ALTER TABLE `"._DB_PREFIX_."htl_branch_info`
                DROP COLUMN `phone`,
                DROP COLUMN `city`,
	            DROP COLUMN `state_id`,
	            DROP COLUMN `country_id`,
	            DROP COLUMN `zipcode`,
	            DROP COLUMN `address`;",

            "ALTER TABLE `"._DB_PREFIX_."htl_image`
	            DROP COLUMN `hotel_image_id`;",

            "ALTER TABLE `"._DB_PREFIX_."htl_cart_booking_data`
                ADD `adults` smallint(6) NOT NULL AFTER `date_to`,
                ADD `children` smallint(6) NOT NULL AFTER `adults`,
                ADD `child_ages` text NOT NULL AFTER `children`;",

            "UPDATE `"._DB_PREFIX_."htl_cart_booking_data` hcbd
                INNER JOIN `"._DB_PREFIX_."htl_room_type` hrt ON (hcbd.`id_product` = hrt.`id_product`)
                SET hcbd.`adults` = hrt.`adults`,
                hcbd.`children` = hrt.`children`,
                hcbd.`child_ages` = \"".json_encode(array())."\";",

            "ALTER TABLE `"._DB_PREFIX_."htl_booking_detail`
                CHANGE `adult` `adults` smallint(6) NOT NULL DEFAULT '0',
                ADD `child_ages` text NOT NULL AFTER `children`,
                ADD `is_cancelled` tinyint(1) NOT NULL DEFAULT '0' AFTER `is_refunded`;",

            "UPDATE `"._DB_PREFIX_."htl_booking_detail`
                SET `child_ages` = \"".json_encode(array())."\";",

            "CREATE TABLE IF NOT EXISTS  `"._DB_PREFIX_."htl_booking_document` (
                `id_htl_booking_document` int(11) NOT NULL AUTO_INCREMENT,
                `id_htl_booking` int(11) NOT NULL,
                `title` varchar(32) NOT NULL DEFAULT '',
                `file_type` tinyint(1) NOT NULL DEFAULT '0',
                `file_name` varchar(8) NOT NULL DEFAULT '',
                `date_add` datetime NOT NULL,
                PRIMARY KEY (`id_htl_booking_document`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "ALTER TABLE `"._DB_PREFIX_."htl_order_restrict_date`
                ADD `use_global_max_order_date` tinyint(1) NOT NULL AFTER `id_hotel`,
                CHANGE `max_order_date` `max_order_date` date NOT NULL,
                ADD `use_global_preparation_time` tinyint(1) NOT NULL AFTER `max_order_date`,
                ADD `preparation_time` int(11) NOT NULL AFTER `use_global_preparation_time`;",

            "UPDATE  `"._DB_PREFIX_."htl_order_restrict_date`
                SET `use_global_max_order_date` = 1",

            "ALTER TABLE `"._DB_PREFIX_."htl_room_type_feature_pricing`
                ADD `id_cart` int(11) NOT NULL DEFAULT '0' AFTER `id_product`,
                ADD `id_guest` int(11) NOT NULL DEFAULT '0' AFTER `id_cart`,
                ADD `id_room` int(11) NOT NULL DEFAULT '0' AFTER `id_guest`;",

            "CREATE TABLE `"._DB_PREFIX_."htl_room_type_service_product` (
                `id_room_type_service_product` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_product` int(11) UNSIGNED NOT NULL,
                `position` smallint(2) unsigned NOT NULL DEFAULT '0',
                `id_element` int(11) unsigned NOT NULL,
                `element_type` tinyint(11) unsigned NOT NULL,
                PRIMARY KEY (`id_room_type_service_product`),
                KEY `id_product` (`id_product`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE `"._DB_PREFIX_."htl_room_type_service_product_price` (
                `id_room_type_service_product_price` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_product` int(11) UNSIGNED NOT NULL,
                `price` decimal(20,6) NOT NULL DEFAULT '0.000000',
                `id_tax_rules_group` int(11) unsigned NOT NULL,
                `id_element` int(11) unsigned NOT NULL,
                `element_type` tinyint(11) unsigned NOT NULL,
                PRIMARY KEY (`id_room_type_service_product_price`),
                KEY `id_product` (`id_product`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE `"._DB_PREFIX_."service_product_cart_detail` (
                `id_service_product_cart_detail` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_product` int(11) UNSIGNED NOT NULL,
                `quantity` int(11) UNSIGNED NOT NULL,
                `id_cart` int(11) unsigned NOT NULL,
                `htl_cart_booking_id` int(11) unsigned NOT NULL,
                PRIMARY KEY (`id_service_product_cart_detail`),
                KEY `id_product` (`id_product`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."service_product_order_detail` (
                `id_service_product_order_detail` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `id_order` int(11) NOT NULL,
                `id_order_detail` int(11) NOT NULL,
                `id_cart` int(11) NOT NULL,
                `id_htl_booking_detail` int(11) NOT NULL,
                `unit_price_tax_excl` decimal(20,6) NOT NULL,
                `unit_price_tax_incl` decimal(20,6) NOT NULL,
                `total_price_tax_excl` decimal(20,6) NOT NULL,
                `total_price_tax_incl` decimal(20,6) NOT NULL,
                `name` varchar(255) DEFAULT NULL,
                `quantity` INT(11) UNSIGNED NOT NULL,
                `auto_added` tinyint(1) unsigned NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_service_product_order_detail`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_room_type_restriction_date_range` (
                `id_rt_restriction` int(11) NOT NULL AUTO_INCREMENT,
                `id_product` int(11) NOT NULL,
                `min_los` smallint(6) unsigned NOT NULL DEFAULT '1',
                `max_los` smallint(6) unsigned NOT NULL DEFAULT '0',
                `date_from` date NOT NULL,
                `date_to` date NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_rt_restriction`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_settings_link` (
                `id_settings_link` int(11) NOT NULL AUTO_INCREMENT,
                `icon` varchar(32) character set utf8 NOT NULL,
                `link` text NOT NULL,
                `new_window` tinyint(1) NOT NULL DEFAULT '0',
                `position` int(11) unsigned NOT NULL DEFAULT '0',
                `unremovable` tinyint(1) NOT NULL DEFAULT '0',
                `active` tinyint(1) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_settings_link`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_settings_link_lang` (
                `id_settings_link` int(10) unsigned NOT NULL,
                `id_lang` int(10) unsigned NOT NULL,
                `name` varchar(255) character set utf8 NOT NULL,
                `hint` varchar(255) character set utf8 NOT NULL,
                PRIMARY KEY (`id_settings_link`, `id_lang`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;",
        );
    }
}
