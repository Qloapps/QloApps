<?php
/**
* 2010-2022 Webkul.
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
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1_8($module)
{
    $objUpgrade = new UpgradeWkHotelRoom118($module);
    return $objUpgrade->initUpgrade();
}

class UpgradeWkHotelRoom118
{
    public function __construct($module)
    {
        $this->module = $module;
    }

    public function initUpgrade()
    {
        return $this->module->unregisterHook('displayAddModuleSettingLink');
    }
}
