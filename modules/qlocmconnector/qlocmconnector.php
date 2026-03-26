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

include_once "classes/QcmcRequiredClasses.php";

class QloCMConnector extends Module
{
    public function __construct()
    {
        $this->name = 'qlocmconnector';
        $this->tab = 'analytics_stats';
        $this->version = '5.0.2';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->html = '';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->qloapps_versions_compliancy = array('min' => '1.7', 'max' => _QLOAPPS_VERSION_);
        parent::__construct();

        $this->displayName = $this->l('QloApps PMS and Channel Manager Connector');
        $this->description = $this->l('This module connects the PMS with Channel Manager using a simplified API endpoint.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitQcmcConfigForm')) {
            if ($this->validateConfigurationValues()) {
                if($this->saveConfigurationValues()) {
                    Tools::redirectAdmin(
                        $this->context->link->getAdminLink('AdminModules', true)
                        . '&conf=6&configure=' . $this->name
                    );
                }
            }
        }

        $this->html .= $this->renderForm();
        $this->html .= $this->renderCronInfo();

        return $this->html;
    }

    public function validateConfigurationValues()
    {
        if (empty(trim(Tools::getValue('QCMC_CM_CLIENT_ID')))) {
            $this->context->controller->errors[] =
                $this->l('Please enter Channel Manager API Client ID.');
        }

        if (empty(trim(Tools::getValue('QCMC_CM_CLIENT_SECRET')))) {
            $this->context->controller->errors[] =
                $this->l('Please enter Channel Manager API Client Secret.');
        }

        if (!Validate::isCleanHtml(Tools::getValue('QCMC_CM_CLIENT_ID'))) {
            $this->context->controller->errors[] =
                $this->l('Invalid Client ID.');
        }

        if (!Validate::isCleanHtml(Tools::getValue('QCMC_CM_CLIENT_SECRET'))) {
            $this->context->controller->errors[] =
                $this->l('Invalid Client Secret.');
        }

        return empty($this->context->controller->errors);
    }

    public function saveConfigurationValues()
    {
        $newClientId = trim(Tools::getValue('QCMC_CM_CLIENT_ID'));
        $newClientSecret = trim(Tools::getValue('QCMC_CM_CLIENT_SECRET'));

        $oldClientId = Configuration::get('QCMC_CM_CLIENT_ID');
        $oldClientSecret = Configuration::get('QCMC_CM_CLIENT_SECRET');

        if ($newClientId !== $oldClientId || $newClientSecret !== $oldClientSecret) {
            $objQcmcChannelManagerApiService = new QcmcChannelManagerApiService();
            $result = $objQcmcChannelManagerApiService->getAccessToken($newClientId, $newClientSecret);
            if(!empty($result['access_token'])) {
                if($objQcmcChannelManagerApiService->updateAccessToken($result['access_token'])) {
                    Configuration::updateValue('QCMC_CM_CLIENT_ID', $newClientId);
                    Configuration::updateValue('QCMC_CM_CLIENT_SECRET', $newClientSecret);
                    return true;
                }
            } else {
                $this->context->controller->errors[] =
                    $this->l('Wrong credentials used.');

                return false;
            }

        }
        return true;
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitQcmcConfigForm';
        $helper->currentIndex =
            $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormFieldsValue(),
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigFormFields()));
    }

    public function getConfigFormFieldsValue()
    {
        $configKeys = array(
            'QCMC_CM_CLIENT_ID',
            'QCMC_CM_CLIENT_SECRET'
        );
        $fieldsValue = array();
        foreach ($configKeys as $key) {
            $fieldsValue[$key] = Configuration::get($key) ? Configuration::get($key) : Tools::getValue($key);
        }

        return $fieldsValue;
    }

    public function getConfigFormFields()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Channel Manager API Credentials'),
                    'icon'  => 'icon-cogs',
                ),
                'description' => '
                    <strong>'.$this->l('How to get Channel Manager API Credentials').'</strong>
                    <ol>
                        <li>'.$this->l('Log in to your').'<strong>'.$this->l(' Channel Manager Account.').'</strong></li>
                        <li>'.$this->l('Navigate to Account Settings.').'</li>
                        <li>'.$this->l('Generate your').'<strong>'. $this->l(' API Credentials.').'</strong></li>
                        <li>'.$this->l('Enter your credentials into the fields below.').'</li>
                    </ol>
                ',
                'input' => array(
                    array(
                        'type'     => 'text',
                        'name'     => 'QCMC_CM_CLIENT_ID',
                        'label'    => $this->l('API Client ID'),
                        'hint'     => $this->l('Client ID from your Channel Manager account API credentials'),
                        'required' => true,
                        'col'      => '4',
                    ),
                    array(
                        'type'     => 'text',
                        'name'     => 'QCMC_CM_CLIENT_SECRET',
                        'label'    => $this->l('API Client Secret'),
                        'hint'     => $this->l('Client Secret from your Channel Manager account API credentials'),
                        'required' => true,
                        'col'      => '4',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    public function renderCronInfo()
    {
        $cronUrl = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.
            'modules/qlocmconnector/qlochannelmanagerapicron.php?token='.$this->secure_key;

        $this->context->smarty->assign(array(
            'cron_url'      => $cronUrl,
            'curl_missing' => !extension_loaded('curl'),
        ));

        return $this->display(__FILE__, 'views/templates/admin/cron_info.tpl');
    }

    public function hookAddWebserviceResources()
    {
        $resources = array(
            'cm_api' => array(
                'description' => 'Channel Manager Connector API',
                'specific_management' => true
            ),
        );

        return $resources;
    }

    //Handles Ari update when room reallocate to new room type
    public function hookActionRoomReallocateAfter($params)
    {
        $objAriUpdates = new QcmcAriUpdates();
        $idHotelBookingTo = (int)(isset($params['id_htl_booking_to']) ? $params['id_htl_booking_to'] : 0);
        $objectHotelBookingFrom = isset($params['objectHotelBookingFrom']) ? $params['objectHotelBookingFrom'] : NULL;

        if(Validate::isLoadedObject($objectHotelBookingFrom)) {
            if (Validate::isLoadedObject($objOrder = new Order((int)$objectHotelBookingFrom->id_order))) {
                if($objOrder->id_channel_manager_booking == NULL && ($objOrder->source == Configuration::get('PS_SHOP_DOMAIN'))) return;
            }
            
            $objAriUpdates->saveAriUpdateRow(
                (int)$objectHotelBookingFrom->id_hotel,
                (int)$objectHotelBookingFrom->id_product,
                $objectHotelBookingFrom->date_from,
                $objectHotelBookingFrom->date_to
            );
        }

        if (Validate::isLoadedObject($objectHotelBookingTo = new HotelBookingDetail($idHotelBookingTo))) {
            $objAriUpdates->saveAriUpdateRow(
                (int)$objectHotelBookingTo->id_hotel,
                (int)$objectHotelBookingTo->id_product,
                $objectHotelBookingTo->date_from,
                $objectHotelBookingTo->date_to
            );
        }
    }

    //Handles Ari update when any room of a room type is temp. disable for given range
    public function hookActionObjectHotelRoomDisableDatesAddAfter($params)
    {
        if (isset($params['object']) &&  ($params['object'] instanceof HotelRoomDisableDates)) {
            $objAriUpdates = new QcmcAriUpdates();
            $objHotelRoomInfo = new HotelRoomInformation();

            $objDisableDates = $params['object'];
            $roomInfoArray = $objHotelRoomInfo->getHotelRoomInfoByProductId((int)$objDisableDates->id_room_type);

            $objAriUpdates->saveAriUpdateRow(
                (int)$roomInfoArray[0]['id_hotel'],
                (int)$objDisableDates->id_room_type,
                date('Y-m-d', strtotime($objDisableDates->date_from)),
                date('Y-m-d', strtotime($objDisableDates->date_to))
            );
        }
    }

    //Handles Ari update when temp. disable for given range is removed from any room type
    public function hookActionObjectHotelRoomDisableDatesDeleteAfter($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelRoomDisableDates)) {
            $objAriUpdates = new QcmcAriUpdates();
            $objHotelRoomInfo = new HotelRoomInformation();

            $objDisableDatesDelete = $params['object'];

            $roomInfoArray = $objHotelRoomInfo->getHotelRoomInfoByProductId((int)$objDisableDatesDelete->id_room_type);

            $objAriUpdates->saveAriUpdateRow(
                (int)$roomInfoArray[0]['id_hotel'],
                (int)$objDisableDatesDelete->id_room_type,
                pSQL($objDisableDatesDelete->date_from),
                pSQL($objDisableDatesDelete->date_to)
            );
        }
    }

    //Handles Ari update when any room of a room type is set active/inactive
    public function hookActionValidateRoomInformation($params)
    {
        if(isset($params['room_information'])) {
            $newRoomInfo = $params['room_information'];

            if(isset($newRoomInfo['id'])) {
                $idRoomInfo = (int)$newRoomInfo['id'];

                $objAriUpdates = new QcmcAriUpdates();
                $objHotelRoomInfo = new HotelRoomInformation();

                $oldRoom = new HotelRoomInformation($idRoomInfo);
                $oldStatus = (int)$oldRoom->id_status;
                $newStatus = isset($newRoomInfo['id_status']) ? (int)$newRoomInfo['id_status'] : $oldStatus;

                if (($newStatus !== HotelRoomInformation::STATUS_TEMPORARY_INACTIVE) && ($oldStatus !== $newStatus)) {
                    $roomInfoArray = $objHotelRoomInfo->getHotelRoomInfoByProductId((int)$oldRoom->id_product);
                    $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate((int)$roomInfoArray[0]['id_hotel']);

                    $objAriUpdates->saveAriUpdateRow(
                        (int)$roomInfoArray[0]['id_hotel'],
                        (int)$oldRoom->id_product,
                        date('Y-m-d'),
                        $maxBookingOffset
                    );
                }
            }
        }
    }

    //Handles Ari update when a new room is added in any room type
    public function hookActionObjectHotelRoomInformationAddAfter($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelRoomInformation)) {
            $objAriUpdates = new QcmcAriUpdates();

            $objHotelRoomInfoAdd = $params['object'];

            $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate((int)$objHotelRoomInfoAdd->id_hotel);

            $objAriUpdates->saveAriUpdateRow(
                (int)$objHotelRoomInfoAdd->id_hotel,
                (int)$objHotelRoomInfoAdd->id_product,
                date('Y-m-d'),
                $maxBookingOffset
            );
        }
    }

    //Handles Ari update when a room is deleted from any room type
    public function hookActionObjectHotelRoomInformationDeleteAfter($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelRoomInformation)) {
            $objAriUpdates = new QcmcAriUpdates();

            $objHotelRoomInfoDelete = $params['object'];

            $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate((int)$objHotelRoomInfoDelete->id_hotel);
            
            $objAriUpdates->saveAriUpdateRow(
                (int)$objHotelRoomInfoDelete->id_hotel,
                (int)$objHotelRoomInfoDelete->id_product,
                date('Y-m-d'),
                $maxBookingOffset
            );
        }
    }

    //Handles Ari update when an order is updated for any field which affects the availablity
    public function hookActionObjectHotelBookingDetailUpdateBefore($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelBookingDetail)) {

            $objHotelBookingDetail = $params['object'];
            if (Validate::isLoadedObject($objOrder = new Order((int)$objHotelBookingDetail->id_order))) {
                if($objOrder->id_channel_manager_booking == NULL && ($objOrder->source == Configuration::get('PS_SHOP_DOMAIN'))) return;
            }

            $objAriUpdates = new QcmcAriUpdates();

            $idBooking  = (int)$objHotelBookingDetail->id;
            $oldBooking = new HotelBookingDetail($idBooking);

            $oldRoomTypeId = (int)$oldBooking->id_product;
            $idHotel       = (int)$oldBooking->id_hotel;
            $oldDateFrom   = date('Y-m-d', strtotime($oldBooking->date_from));
            $oldDateTo     = date('Y-m-d', strtotime($oldBooking->date_to));

            $newRoomTypeId = (int)$objHotelBookingDetail->id_product;
            $newDateFrom   = date('Y-m-d', strtotime($objHotelBookingDetail->date_from));
            $newDateTo     = date('Y-m-d', strtotime($objHotelBookingDetail->date_to));

            $fieldsToCheck = [
                'check_in',
                'check_out',
                'id_status',
                'is_cancelled',
                'is_refunded',
            ];

            foreach ($fieldsToCheck as $field) {
                if ($oldBooking->$field != $objHotelBookingDetail->$field) {
                    $objAriUpdates->saveAriUpdateRow(
                        $idHotel,
                        $oldRoomTypeId,
                        $oldDateFrom,
                        $oldDateTo
                    );
                    break;
                }
            }

            if (($oldRoomTypeId == $newRoomTypeId) && (($oldDateFrom != $newDateFrom) || ($oldDateTo != $newDateTo))) {
                $objAriUpdates->saveAriUpdateRow(
                    $idHotel,
                    $oldRoomTypeId,
                    $oldDateFrom,
                    $oldDateTo
                );

                $objAriUpdates->saveAriUpdateRow(
                    $idHotel,
                    $newRoomTypeId,
                    $newDateFrom,
                    $newDateTo
                );
            }
        }
    }

    //Handles Ari update when a new order is added, it also handles the cond. where a room is added to an existing order
    public function hookActionObjectHotelBookingDetailAddAfter($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelBookingDetail)) {
            $objAriUpdates = new QcmcAriUpdates();
            $objHotelBookingDetail = $params['object'];

            if (Validate::isLoadedObject($objOrder = new Order((int)$objHotelBookingDetail->id_order))) {
                if($objOrder->id_channel_manager_booking == NULL && ($objOrder->source == Configuration::get('PS_SHOP_DOMAIN'))) return;
            }

            $objAriUpdates->saveAriUpdateRow(
                (int)$objHotelBookingDetail->id_hotel,
                (int)$objHotelBookingDetail->id_product,
                date('Y-m-d', strtotime($objHotelBookingDetail->date_from)),
                date('Y-m-d', strtotime($objHotelBookingDetail->date_to))
            );
        }
    }

    //Handles Ari update when an order is deleted, it also handles the cond. where a room is deleted from an existing order
    public function hookActionObjectHotelBookingDetailDeleteAfter($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelBookingDetail)) {
            $objAriUpdates = new QcmcAriUpdates();
            $objHotelBookingDetail = $params['object'];

            if (Validate::isLoadedObject($objOrder = new Order((int)$objHotelBookingDetail->id_order))) {
                if($objOrder->id_channel_manager_booking == NULL && ($objOrder->source == Configuration::get('PS_SHOP_DOMAIN'))) return;
            }

            $objAriUpdates->saveAriUpdateRow(
                (int)$objHotelBookingDetail->id_hotel,
                (int)$objHotelBookingDetail->id_product,
                date('Y-m-d', strtotime($objHotelBookingDetail->date_from)),
                date('Y-m-d', strtotime($objHotelBookingDetail->date_to))
            );
        }
    }

    public function hookActionObjectOrderPropertiesModifier(&$params)
    {
        if(is_array($params['obj_properties']) && !isset($params['obj_properties']['id_channel_manager_booking'])){
            $params['obj_properties']['id_channel_manager_booking'] = null;
        }
    }

    public function hookActionObjectOrderDefinitionModifier(&$params)
    {
        if(is_array($params['definition']['fields']) && !isset($params['definition']['fields']['id_channel_manager_booking'])){
            $params['definition']['fields']['id_channel_manager_booking'] = array('type' => Order::TYPE_STRING);
        }
    }

    public function hookActionObjectOrderAddBefore(&$params)
    {
        if (!isset($params['object']->id_channel_manager_booking)) {
            $params['object']->id_channel_manager_booking = NULL;
        }
    }

    public function hookActionObjectOrderUpdateAfter(&$params)
    {
        if (!isset($params['object']->id_channel_manager_booking)) {
            $params['object']->id_channel_manager_booking = NULL;
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/admin/admin_tab_logo.css');
    }

    public function hookDisplayAdminListBefore()
    {
        // This tpl will only display when at least one booking has come from channel manager
        if ('AdminCMConnectorBookings' == $this->context->controller->controller_name
            && QcmcChannelManagerBooking::getChannelManagerBookings()
        ) {
            return $this->display(__FILE__, 'channel_manager_connection_info.tpl');
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        // This CSS will only apply when at least one booking has come from channel manager
        if ('AdminCMConnectorBookings' == $this->context->controller->controller_name
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

    public function addConfigurationValues()
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

    public function install()
    {
        $objModuleDb = new QcmcCMConnectorDb();
        if ( !parent::install()
          || !$this->registerModuleHooks()
          || !$objModuleDb->createTables()
          || !$this->addConfigurationValues()
          || !$this->callInstallTab()
        ) {
            return false;
        }
        return true;
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'addWebserviceResources',
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
                'actionObjectOrderUpdateAfter',
                'actionValidateOrder',
                'displayBackOfficeHeader',
                'displayAdminListBefore',
                'actionAdminControllerSetMedia'
            )
        );
    }

    public function deleteConfigurationValues()
    {
        $configKeys = [
            'QCMC_CM_CLIENT_ID',
            'QCMC_CM_CLIENT_SECRET',
            'QCMC_CM_ACCESS_TOKEN',
        ];

        foreach ($configKeys as $key) {
            Configuration::deleteByName($key);
        }

        return true;
    }

    public function uninstall()
    {
        $objModuleDb = new QcmcCMConnectorDb();
        if ( !parent::uninstall()
          || !$objModuleDb->dropTables()
          || !$this->deleteConfigurationValues()
          || !$this->uninstallTab()
        ) {
            return false;
        }
        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminCMConnector', 'Channel Manager Connector');
        $this->installTab('AdminCMConnectorConfiguration', 'Configuration', 'AdminCMConnector');
        $this->installTab('AdminCMConnectorBookings', 'Channel Manager Bookings', 'AdminCMConnector');

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
