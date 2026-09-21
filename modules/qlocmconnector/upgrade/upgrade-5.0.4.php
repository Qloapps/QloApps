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

function upgrade_module_5_0_4($module)
{
    $objCMConnector = new UpgradeQloCMConnector504($module);
    return (
        $objCMConnector->registerHooks()
        && $objCMConnector->updateTable()
    );
}

class UpgradeQloCMConnector504{
    public function __construct($module)
    {
        $this->module = $module;
    }

    public function registerHooks()
    {
        return $this->module->registerHook(
            array(
                'displayOrderRoomsBookingsTableHeading',
                'displayOrderRoomsBookingsTableData',
                'actionObjectHotelBookingDetailPropertiesModifier',
                'actionObjectHotelBookingDetailDefinitionModifier',
            )
        );
    }

    public function updateTable()
    {
        $exists = Db::getInstance()->getValue(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = '" . pSQL(_DB_PREFIX_ . 'htl_booking_detail') . "'
            AND COLUMN_NAME = 'rate_plan_name'"
        );
        if (!$exists) {
            $sql = "ALTER TABLE `" . _DB_PREFIX_ . "htl_booking_detail` ADD COLUMN `rate_plan_name` VARCHAR(255) DEFAULT NULL";
            if (!Db::getInstance()->execute($sql)) {
                return false;
            }
        }
        return true;
    }
}
