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

function migrate_channel_manager_connector_180()
{
    if (!Module::isInstalled('qlocmconnector')) {
        $qloCmConnector = Module::getInstanceByName('qlocmconnector');
        if (Validate::isLoadedObject($qloCmConnector) && !$qloCmConnector->install()) {
            return false;
        }
    }

    // qlochannelmanagerconnector's own files are already removed from the release zip by this
    // point, so qlocmconnector's own self-cleanup (run from inside its install()) can't reach it.
    // Its historical booking-link table (qcmc_channel_manager_booking) holds real booking data and
    // is intentionally left in place, not dropped — only the now-dead module registration itself
    // is cleaned up here.
    $idOldModule = (int) Db::getInstance()->getValue(
        'SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \'qlochannelmanagerconnector\''
    );
    if ($idOldModule) {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module` WHERE `id_module` = '.$idOldModule);
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_shop` WHERE `id_module` = '.$idOldModule);
    }

    return true;
}
