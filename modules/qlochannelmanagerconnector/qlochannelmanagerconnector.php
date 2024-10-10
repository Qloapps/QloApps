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

require_once dirname(__FILE__).'/classes/QcmcClassInclude.php';

class QloChannelManagerConnector extends Module
{
    public function __construct()
    {
        $this->name = 'qlochannelmanagerconnector';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.0';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->html = '';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');

        parent::__construct();

        $this->displayName = $this->l('Channel Manager Connector');
        $this->description = $this->l('This module checks connection with channel manager and create logs for the bookings sent from channel manager.');
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/admin/admin_tab_logo.css');
    }

    public function hookDisplayAdminListBefore()
    {
        // This tpl will only display when at least one booking has come from channel manager
        if ('AdminQloappsChannelManagerConnector' == $this->context->controller->controller_name
            && QcmcChannelManagerBooking::getChannelManagerBookings()
        ) {
            return $this->display(__FILE__, 'channel_manager_connection_info.tpl');
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        // This CSS will only apply when at least one booking has come from channel manager
        if ('AdminQloappsChannelManagerConnector' == $this->context->controller->controller_name
            && QcmcChannelManagerBooking::getChannelManagerBookings()
        ) {
            $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/hook/wk_cm_connection_info.css');
        }
    }


    public function hookActionValidateOrder($data)
    {
        // If the order request is coming from channel manager ip then only enter in database
        if (Tools::getRemoteAddr() == QcmcChannelManagerBooking::QCMC_CHANNEL_MANAGER_IP) {
            $order = $data['order'];
            if (!QcmcChannelManagerBooking::getChannelManagerBookings($order->id)) {
                $objChannelManagerBooking = new QcmcChannelManagerBooking();
                $objChannelManagerBooking->id_order = $order->id;
                $objChannelManagerBooking->save();
            }
        }
    }

    public function callInstallTab()
    {
        $this->installTab('AdminQloappsChannelManagerConnector', 'Channel Manager');

        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false, $need_tab = true)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } elseif (!$need_tab) {
            $tab->id_parent = -1;
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;
        $res = $tab->add();

        //Set position Tab
        $objTab = new Tab($tab->id);

        // position of the tab will be after hotelreservationsystem module tab
        // without calling this function we are getting hotelreservationsystem module tab cache object with old position
        ObjectModel::disableCache();
        if (Validate::isLoadedObject(
            $objTabForPosition = Tab::getInstanceFromClassName('AdminHotelReservationSystemManagement')
        )) {
            $objTab->updatePosition(0, ($objTabForPosition->position + 1));
        } else {
            $objTab->updatePosition(0, 6);
        }
        // enable cache variable again
        ObjectModel::enableCache();

        return $res;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'actionValidateOrder',
                'displayBackOfficeHeader',
                'displayAdminListBefore',
                'actionAdminControllerSetMedia'
            )
        );
    }

    public function install()
    {
        $objModuleDb = new QcmcChannelManagerConnectorDb();
        if (!parent::install()
            || !$objModuleDb->createTables()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        $objModuleDb = new QcmcChannelManagerConnectorDb();
        if (!parent::uninstall()
            || !$objModuleDb->dropTables()
            || !$this->uninstallTab()
        ) {
            return false;
        }
        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
            return true;
        }
        return false;
    }
}
