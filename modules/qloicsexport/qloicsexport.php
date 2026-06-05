<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
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
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'hotelreservationsystem/classes/HotelBookingDetail.php';
require_once dirname(__FILE__) . '/classes/QieIcalHelper.php';

class QloIcsExport extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'qloicsexport';
        $this->tab = 'administration';
        $this->version = '4.0.0';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->qloapps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _QLOAPPS_VERSION_);
        
        $this->displayName = 'QloApps Booking iCalendar (.ics) File Export';
        $this->description = $this->l('Using this module, Admin can export bookings iCalendar file in .ics format.');

        parent::__construct();
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'displayAdminOrder',
                'displayAdminListBefore',
                'actionAdminControllerSetMedia',
                'actionMailAlterMessageBeforeSend',
            )
        );
    }

    public function hookActionMailAlterMessageBeforeSend($params)
    {
        if (Configuration::get('QIE_ATTACH_MAIL') 
            && $params['template'] == 'order_conf'  
            && isset($params['message']) 
            && isset($params['template_vars']['{order_name}']) 
            ) {
            $emailObject = $params['message'];
            $templateVars = isset($params['template_vars']) ? $params['template_vars'] : array();
            $orderUniqRef = $templateVars['{order_name}']; //  ex: XQEGPFGRI#1 ||  XQEGPFGRI#2

            // 1. Separate the Reference and the Index
            $orderUniqRefParts = explode('#', $orderUniqRef);
            $orderReference = $orderUniqRefParts[0];
            $orderUniqRefIndex = isset($orderUniqRefParts[1]) ? (int) $orderUniqRefParts[1] : 1;
            $objIcalHelper = new QieIcalHelper();
            $objOrder = $objIcalHelper->getOrderIdByReference($orderReference);
            if ($objOrder) {
                $targetOrderUniqRefIndex = $orderUniqRefIndex - 1;

                if (isset($objOrder[$targetOrderUniqRefIndex]['id_order'])) {
                    $idOrder = (int) $objOrder[$targetOrderUniqRefIndex]['id_order'];

                    $finalIcsContent = $objIcalHelper->getBookingsICalendarValues(array('id_order' => $idOrder));

                    if ($finalIcsContent) {
                        $filename = 'booking_' . $orderReference . '_' . $orderUniqRefIndex . '.ics';
                        $emailObject->attach(
                            $finalIcsContent,
                            $filename,
                            'text/calendar'
                        );
                    }
                }
            }
        }
    }

    public function install()
    {
        if (
            !parent::install()
            || !$this->registerModuleHooks()
            || !$this->callInstallTab()
            || !Configuration::updateValue('QIE_ATTACH_MAIL', 0)
        ) {
            return false;
        }
        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminExportIcalFile', 'order restrict configuration', false, false);
        $this->installTab('AdminQloIcsExportConfiguration', $this->l('Ics Export Configuration'), false, false);

        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }

    public function uninstall()
    {
        if (
            !parent::uninstall()
            || !$this->uninstallTab()
            || !$this->deleteConfigKeys()
        ) {
            return false;
        }

        return true;
    }

    public function deleteConfigKeys()
    {
        if (!Configuration::deleteByName('QIE_ATTACH_MAIL')) {
            return false;
        }
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

        return $res;
    }

    public function hookActionAdminControllerSetMedia()
    {
        if ('AdminOrders' == Tools::getValue('controller')) {
            $idOrder = Tools::getValue('id_order');
            $params = array();
            if ($idOrder) {
                $params['id_order'] = $idOrder;
            }
            $objIcalHelper = new QieIcalHelper();
            // show button if bookings are avialable in the system
            if ($objIcalHelper->getBookingOrders($params)) {
                // add header toolbar button for the ics file download
                $toolbarParams = array(
                    'desc' => $this->l('Export iCalendar(.ics) File'),
                    'icon' => 'process-icon-download',
                );

                if ($idOrder) {
                    $toolbarParams['href'] = $this->context->link->getAdminLink('AdminExportIcalFile') .
                        '&id_order=' . $idOrder . '&ics_method=export_order_bookings';
                    $toolbarParams['target'] = 1;
                    $toolbarParams['help'] = $this->l('Export bookings of this order iCalendar file in .ics format.');
                } else {
                    $toolbarParams['modal_target'] = '#ics-download-form';
                    $toolbarParams['help'] = $this->l('Export bookings of all orders iCalendar file in .ics format.');
                    // css and js for modal box and validate js
                    $this->context->controller->addCSS($this->_path . 'views/css/admin/qlo_ics_export.css');

                    Media::addJsDef(array('ctrlLink' => $this->context->link->getAdminLink('AdminExportIcalFile')));
                    $this->context->controller->addJS($this->_path . 'views/js/admin/qlo_ics_export.js');
                }
                $this->context->controller->page_header_toolbar_btn['ics_download'] = $toolbarParams;
            }
        }
    }
    public function getContent()
    {
        return Tools::redirectAdmin($this->context->link->getAdminLink('AdminQloIcsExportConfiguration'));
    }
    public function hookDisplayAdminListBefore()
    {
        if ('AdminOrders' == Tools::getValue('controller') && !Tools::getValue('id_order')) {
            $objHotelInfo = new HotelBranchInformation();
            $idsHotel = $objHotelInfo->getProfileAccessedHotels($this->context->employee->id_profile, 1, 1);
            if ($idsHotel) {
                $hotels = array();
                foreach ($idsHotel as $idHotel) {
                    $objHotelBranchInfo = new HotelBranchInformation($idHotel, $this->context->language->id);
                    $hotels[] = array(
                        'id_hotel' => (int) $idHotel,
                        'hotel_name' => $objHotelBranchInfo->hotel_name
                    );
                }
                array_unshift($hotels, array('id_hotel' => 0, 'hotel_name' => $this->l('All Hotels')));
                $exportLink = $this->context->link->getAdminLink('AdminExportIcalFile');

                $this->context->smarty->assign(
                    array(
                        'hotels' => $hotels,
                        'export_link' => $exportLink,
                        'allOrders' => 1
                    )
                );

                return $this->display(__FILE__, 'adminBookingExport.tpl');
            }
        }
    }
}
