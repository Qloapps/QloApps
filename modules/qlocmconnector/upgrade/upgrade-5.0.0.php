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

function upgrade_module_5_0_0($module)
{
    $objCMConnector = new UpgradeQloCMConnector500($module);
    return (
        $objCMConnector->createTables()
        && $objCMConnector->addConfigKeys()
        && $objCMConnector->registerHooks()
    );
}
class UpgradeQloCMConnector500
{
    public function __construct($module)
    {
        $this->module = $module;
    }

    public function createTables()
    {
        $sql = array(
            "CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."ari_updates (
                `id_ari_updates` INT AUTO_INCREMENT,
                `id_hotel` INT NOT NULL,
                `id_room_type` INT NOT NULL,
                `date` DATETIME NOT NULL,
                `ari_data` LONGTEXT NULL,
                `status` TINYINT(1) NOT NULL DEFAULT 0,
                `date_add` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `date_upd` DATETIME NULL,
                PRIMARY KEY (`id_ari_updates`)
            )ENGINE=" . _MYSQL_ENGINE_ ." DEFAULT CHARSET=utf8",
            "ALTER TABLE `"._DB_PREFIX_."orders`
                ADD COLUMN `id_channel_manager_booking` VARCHAR(255) NULL",
        );

        $objDb = Db::getInstance();
        foreach ($sql as $query) {
            if (!$objDb->execute(trim($query))) {
                return false;
            }
        }
        return true;
    }

    public function addConfigKeys()
    {
        $configKeys = [
            'QCMC_CM_CLIENT_ID',
            'QCMC_CM_CLIENT_SECRET',
        ];

        foreach ($configKeys as $key) {
            if (!Configuration::hasKey($key)) {
                $success = Configuration::updateValue($key, '', false, null, null);
                if (!$success) {
                    return false;
                }
            }
        }

        return true;
    }

    public function registerHooks()
    {
        return $this->module->registerHook(
            array(
                'actionRoomReallocateAfter',
                'actionObjectHotelRoomDisableDatesAddAfter',
                'actionObjectHotelRoomDisableDatesDeleteAfter',
                'actionValidateRoomInformation',
                'actionObjectHotelRoomInformationAddAfter',
                'actionObjectHotelRoomInformationDeleteAfter',
                'actionObjectHotelBookingDetailAddAfter',
                'actionObjectHotelBookingDetailUpdateBefore',
                'actionObjectHotelBookingDetailDeleteAfter',
                'actionObjectOrderPropertiesModifier',
                'actionObjectOrderDefinitionModifier',
                'actionObjectOrderAddBefore',
                'actionObjectOrderUpdateAfter'
            )
        );
    }
}
