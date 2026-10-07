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
    public static $idBookingCm = null;
    const CM_BOOKING_LIST_TAB_CLASS = 'AdminQloappsChannelManagerConnector';

    public function __construct()
    {
        $this->name = 'qlocmconnector';
        $this->tab = 'analytics_stats';
        $this->version = '5.0.6';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->html = '';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->qloapps_versions_compliancy = array('min' => '1.7', 'max' => _QLOAPPS_VERSION_);
        parent::__construct();

        $this->displayName = $this->l('QloApps Channel Manager Connector');
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

            // is_cancelled/is_refunded excluded: not in the ObjectModel definition, and only
            // ever changed via direct SQL (handlePutRequest), which this hook can't observe.
            $fieldsToCheck = [
                'check_in',
                'check_out',
                'id_status',
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

    public function hookActionObjectHotelBookingDetailPropertiesModifier(&$params)
    {
        if (is_array($params['obj_properties']) && !isset($params['obj_properties']['rate_plan_name'])) {
            $params['obj_properties']['rate_plan_name'] = null;
        }
    }

    public function hookActionObjectHotelBookingDetailDefinitionModifier(&$params)
    {
        if (is_array($params['definition']['fields']) && !isset($params['definition']['fields']['rate_plan_name'])) {
            $params['definition']['fields']['rate_plan_name'] = array(
                'type' => HotelBookingDetail::TYPE_STRING,
                'default' => null
            );
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

    //Handles Ari update when a product (room type) is updated
    //Tracks: price, tax_rate, active, show_at_front for room types
    //Tracks: auto_add_to_cart for service products linked to room types
    public function hookActionObjectProductUpdateBefore($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof Product)) {
            $objProduct = $params['object'];

            // Check if this product is a hotel room type
            $objHotelRoomType = new HotelRoomType();
            $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($objProduct->id);

            $hasChanges = false;
            $ariData = [];

            if ($roomTypeInfo) {
                // This is a room type product - check standard fields
                $idHotel = (int)$roomTypeInfo['id_hotel'];
                $idRoomType = (int)$objProduct->id;

                // Get the old product data for comparison
                $oldProduct = new Product($objProduct->id);

                // Fields to check for room type changes
                $fieldsToCheck = [
                    'price',
                    'id_tax_rules_group',
                    'active',
                    'show_at_front'
                ];

                foreach ($fieldsToCheck as $field) {
                    if (isset($objProduct->$field) && isset($oldProduct->$field)) {
                        if ($objProduct->$field != $oldProduct->$field) {
                            $hasChanges = true;

                            // Handle active/show_at_front changes - set inventory
                            if ($field == 'active' || $field == 'show_at_front') {
                                $ariData['inventory'] = 1;
                            }

                            // Handle price/tax changes - set price
                            if ($field == 'price' || $field == 'id_tax_rules_group') {
                                $ariData['price'] = 1;
                            }
                        }
                    }
                }

                // If changes detected, save ARI update
                if ($hasChanges) {
                    // Get booking offset for the hotel
                    $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate($idHotel);

                    // Save ARI update
                    $objAriUpdates = new QcmcAriUpdates();
                    $objAriUpdates->saveAriUpdateRow(
                        $idHotel,
                        $idRoomType,
                        date('Y-m-d'),
                        $maxBookingOffset,
                        $ariData
                    );
                }
            } else {
                // This product might be a service product - check if it's linked to a room type
                // Use core method getAssociatedHotelsAndRoomType
                $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                $serviceAssociations = $objRoomTypeServiceProduct->getAssociatedHotelsAndRoomType(
                    $objProduct->id,
                    RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                );

                if (!empty($serviceAssociations['room_type'])) {
                    // This is a service product linked to room types
                    // Check if auto_add_to_cart changed
                    $oldProduct = new Product($objProduct->id);

                    if (isset($objProduct->auto_add_to_cart) && isset($oldProduct->auto_add_to_cart)) {
                        if ($objProduct->auto_add_to_cart != $oldProduct->auto_add_to_cart) {
                            // auto_add_to_cart changed - trigger price update for all linked room types
                            $objAriUpdates = new QcmcAriUpdates();

                            foreach ($serviceAssociations['room_type'] as $idRoomType) {
                                // Get hotel info for this room type
                                $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($idRoomType);

                                if ($roomTypeInfo) {
                                    $idHotel = (int)$roomTypeInfo['id_hotel'];
                                    $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate($idHotel);

                                    // Save ARI update with price key
                                    $objAriUpdates->saveAriUpdateRow(
                                        $idHotel,
                                        (int)$idRoomType,
                                        date('Y-m-d'),
                                        $maxBookingOffset,
                                        array('price' => 1)
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // Skip rate ARI when id_cart > 0 (per-booking price) or CM API request active.
    // Admin pricing rules always have id_cart = 0 and must queue rate updates.
    private function shouldSkipFeaturePriceAriUpdate($objFeaturePrice)
    {
        if ((int)$objFeaturePrice->id_cart > 0) {
            return true;
        }
        if (self::$idBookingCm !== null) {
            return true;
        }
        return false;
    }

    //Handles Ari update when feature prices (advance price) are added
    public function hookActionObjectHotelRoomTypeFeaturePricingAddBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomTypeFeaturePricing)) {
            return;
        }
        $objFeaturePrice = $params['object'];
        if ($this->shouldSkipFeaturePriceAriUpdate($objFeaturePrice)) {
            return;
        }

        $objHotelRoomType = new HotelRoomType();
        $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($objFeaturePrice->id_product);
        if (!$roomTypeInfo) {
            return;
        }

        $idHotel = (int)$roomTypeInfo['id_hotel'];
        $idRoomType = (int)$objFeaturePrice->id_product;
        $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate($idHotel);

        $objAriUpdates = new QcmcAriUpdates();
        $objAriUpdates->saveAriUpdateRow(
            $idHotel,
            $idRoomType,
            date('Y-m-d'),
            $maxBookingOffset,
            array('rate' => 1)
        );
    }

    //Handles Ari update when feature prices (advance price) settings are updated
    public function hookActionObjectHotelRoomTypeFeaturePricingUpdateBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomTypeFeaturePricing)) {
            return;
        }
        $objFeaturePrice = $params['object'];
        if ($this->shouldSkipFeaturePriceAriUpdate($objFeaturePrice)) {
            return;
        }

        $objHotelRoomType = new HotelRoomType();
        $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($objFeaturePrice->id_product);
        if (!$roomTypeInfo) {
            return;
        }

        $idHotel = (int)$roomTypeInfo['id_hotel'];
        $idRoomType = (int)$objFeaturePrice->id_product;
        $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate($idHotel);

        $objAriUpdates = new QcmcAriUpdates();
        $objAriUpdates->saveAriUpdateRow(
            $idHotel,
            $idRoomType,
            date('Y-m-d'),
            $maxBookingOffset,
            array('rate' => 1)
        );
    }

    //Handles Ari update when feature prices (advance price) are deleted
    public function hookActionObjectHotelRoomTypeFeaturePricingDeleteBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomTypeFeaturePricing)) {
            return;
        }
        $objFeaturePrice = $params['object'];
        if ($this->shouldSkipFeaturePriceAriUpdate($objFeaturePrice)) {
            return;
        }

        $objHotelRoomType = new HotelRoomType();
        $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($objFeaturePrice->id_product);
        if (!$roomTypeInfo) {
            return;
        }

        $idHotel = (int)$roomTypeInfo['id_hotel'];
        $idRoomType = (int)$objFeaturePrice->id_product;

        // Capture restriction date ranges before delete() removes them
        $objFeaturePriceRestriction = new HotelRoomTypeFeaturePricingRestriction();
        $restrictions = $objFeaturePriceRestriction->getRestrictionsByIdFeaturePrice($objFeaturePrice->id);

        $objAriUpdates = new QcmcAriUpdates();
        $hasRestrictions = false;

        if ($restrictions) {
            foreach ($restrictions as $restriction) {
                if (!empty($restriction['date_from']) && !empty($restriction['date_to'])) {
                    $hasRestrictions = true;
                    $objAriUpdates->saveAriUpdateRow(
                        $idHotel,
                        $idRoomType,
                        date('Y-m-d', strtotime($restriction['date_from'])),
                        date('Y-m-d', strtotime($restriction['date_to'])),
                        array('rate' => 1)
                    );
                }
            }
        }

        if (!$hasRestrictions) {
            $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate($idHotel);
            $objAriUpdates->saveAriUpdateRow(
                $idHotel,
                $idRoomType,
                date('Y-m-d'),
                $maxBookingOffset,
                array('rate' => 1)
            );
        }
    }

    //Handles Ari update when a service product is linked to a room type
    public function hookActionObjectRoomTypeServiceProductLinkBefore($params)
    {
        if (!isset($params['id_product']) || !isset($params['values']) || !isset($params['element_type'])) {
            return;
        }

        $idServiceProduct = (int)$params['id_product'];
        $values = $params['values'];
        $elementType = (int)$params['element_type'];

        // Only process room type associations
        if ($elementType != RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE) {
            return;
        }

        // Check if this service product is auto-added
        $objProduct = new Product($idServiceProduct);
        if (!$objProduct->auto_add_to_cart) {
            return;
        }

        $objHotelRoomType = new HotelRoomType();
        $objAriUpdates = new QcmcAriUpdates();

        foreach ($values as $idRoomType) {
            $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct((int)$idRoomType);
            if ($roomTypeInfo) {
                $idHotel = (int)$roomTypeInfo['id_hotel'];
                $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate($idHotel);

                $objAriUpdates->saveAriUpdateRow(
                    $idHotel,
                    (int)$idRoomType,
                    date('Y-m-d'),
                    $maxBookingOffset,
                    array('price' => 1)
                );
            }
        }
    }

    //Handles Ari update when a service product is unlinked from a room type
    public function hookActionObjectRoomTypeServiceProductUnlinkBefore($params)
    {
        if (!isset($params['id_product']) || !isset($params['elements'])) {
            return;
        }

        $idServiceProduct = (int)$params['id_product'];
        $elements = $params['elements'];

        if (empty($elements)) {
            return;
        }

        // Check if this service product is auto-added
        $objProduct = new Product($idServiceProduct);
        if (!$objProduct->auto_add_to_cart) {
            return;
        }

        $objHotelRoomType = new HotelRoomType();
        $objAriUpdates = new QcmcAriUpdates();

        foreach ($elements as $element) {
            if ((int)$element['element_type'] == RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE) {
                $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct((int)$element['id_element']);
                if ($roomTypeInfo) {
                    $idHotel = (int)$roomTypeInfo['id_hotel'];
                    $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate($idHotel);

                    $objAriUpdates->saveAriUpdateRow(
                        $idHotel,
                        (int)$element['id_element'],
                        date('Y-m-d'),
                        $maxBookingOffset,
                        array('price' => 1)
                    );
                }
            }
        }
    }

    //Handles Ari update when hotel room type is updated (LOS restriction changes at room type level)
    public function hookActionObjectHotelRoomTypeUpdateBefore($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelRoomType)) {
            $objRoomType = $params['object'];

            // Get hotel info for this room type
            $objHotelRoomType = new HotelRoomType();
            $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($objRoomType->id);
            if (!$roomTypeInfo) {
                return;
            }

            $idHotel = (int)$roomTypeInfo['id_hotel'];
            $idRoomType = (int)$objRoomType->id;

            // Get the old room type data for comparison
            $oldRoomType = new HotelRoomType($objRoomType->id);

            // Check if min_los or max_los changed
            $losChanged = false;
            if ($objRoomType->min_los != $oldRoomType->min_los
            || $objRoomType->max_los != $oldRoomType->max_los) {
                $losChanged = true;
            }

            if ($losChanged) {
                // Get booking offset for the hotel
                $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate($idHotel);

                // Save ARI update with los key
                $objAriUpdates = new QcmcAriUpdates();
                $objAriUpdates->saveAriUpdateRow(
                    $idHotel,
                    $idRoomType,
                    date('Y-m-d'),
                    $maxBookingOffset,
                    ['los' => 1]
                );
            }
        }
    }


    //Handles Ari update when room type restriction date range is updated (LOS changes)
    public function hookActionObjectHotelRoomTypeRestrictionDateRangeUpdateBefore($params)
    {
        $this->manageLosRestriction($params);
    }

    //Handles Ari update when room type restriction date range is added (LOS added)
    public function hookActionObjectHotelRoomTypeRestrictionDateRangeAddBefore($params)
    {
        $this->manageLosRestriction($params);
    }

    //Handles Ari update when room type restriction date range is deleted (LOS removed)
    public function hookActionObjectHotelRoomTypeRestrictionDateRangeDeleteBefore($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelRoomTypeRestrictionDateRange)) {
            $objRestriction = $params['object'];

            // Get room type info
            $objHotelRoomType = new HotelRoomType();
            $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($objRestriction->id_product);

            if (!$roomTypeInfo) {
                return;
            }

            $idHotel = (int)$roomTypeInfo['id_hotel'];
            $idRoomType = (int)$objRestriction->id_product;

            // Save ARI update with los key for the deleted restriction date range
            $objAriUpdates = new QcmcAriUpdates();
            $objAriUpdates->saveAriUpdateRow(
                $idHotel,
                $idRoomType,
                date('Y-m-d', strtotime($objRestriction->date_from)),
                date('Y-m-d', strtotime($objRestriction->date_to)),
                ['los' => 1]
            );
        }
    }

    public function hookActionAvailRoomSearchSqlModifier(&$params)
    {
        // Check if request is from trusted partners
        if (!QcmcChannelManagerApiService::isAllowedRemoteAddr($_SERVER['REMOTE_ADDR'])) {
            return;
        }

        // Check if id_order is passed in params
        if (isset($params['params']['all_params']['id_order']) && $params['params']['all_params']['id_order']) {
            $idOrder = (int) $params['params']['all_params']['id_order'];

            // Modify the checked_out subquery to exclude rooms based on order ID
            if (isset($params['exclude_room_id']['checked_out'])) {
                // Add id_order condition to exclude only bookings NOT matching current order
                $originalQuery = $params['exclude_room_id']['checked_out'];

                // Insert id_order != {idOrder} condition after is_refunded = 0
                $modifiedQuery = preg_replace(
                    '/`is_refunded` = 0 AND IF\(/',
                    '`is_refunded` = 0 AND `id_order` != ' . $idOrder . ' AND IF(',
                    $originalQuery
                );

                $params['exclude_room_id']['checked_out'] = $modifiedQuery;
            }
        }

        // [LOS BYPASS] Bypass LOS (Length of Stay) stay validation when requested by channel manager
        // This allows the channel manager to skip min_los/max_los restrictions during room search
        if (isset($params['exclude_room_id']['length_of_stay'])) {
            unset($params['exclude_room_id']['length_of_stay']);
        }
        // [END LOS BYPASS]
    }

    public function manageLosRestriction($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelRoomTypeRestrictionDateRange)) {
            $objRestriction = $params['object'];

            // Get room type info
            $objHotelRoomType = new HotelRoomType();
            $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($objRestriction->id_product);

            if (!$roomTypeInfo) {
                return;
            }

            $idHotel = (int)$roomTypeInfo['id_hotel'];
            $idRoomType = (int)$objRestriction->id_product;

            // Get the old restriction data for comparison
            $oldRestriction = new HotelRoomTypeRestrictionDateRange($objRestriction->id);

            // Check if min_los or max_los changed
            $losChanged = false;
            if ($objRestriction->min_los != $oldRestriction->min_los
                || $objRestriction->max_los != $oldRestriction->max_los) {
                $losChanged = true;
            }

            if ($losChanged) {
                // Get booking offset for the hotel
                $maxBookingOffset = HotelOrderRestrictDate::getMaxOrderDate($idHotel);

                // Save ARI update with rate = 1 for LOS changes
                $objAriUpdates = new QcmcAriUpdates();
                $objAriUpdates->saveAriUpdateRow(
                    $idHotel,
                    $idRoomType,
                    date('Y-m-d', strtotime($objRestriction->date_from)),
                    date('Y-m-d', strtotime($objRestriction->date_to)),
                    ['los' => 1]
                );
            }
        }
    }

    //Handles Ari update when hotel order restrict date is updated (booking offset changes)
    public function hookActionObjectHotelOrderRestrictDateUpdateBefore($params)
    {
        if (isset($params['object']) && ($params['object'] instanceof HotelOrderRestrictDate)) {
            $objOrderRestrict = $params['object'];

            // Get all room types for this hotel
            $objHotelRoomType = new HotelRoomType();
            $roomTypes = $objHotelRoomType->getRoomTypeByHotelId($objOrderRestrict->id_hotel, $this->context->language->id);

            if (!$roomTypes) {
                return;
            }

            // Get the old order restrict data for comparison
            $oldOrderRestrict = new HotelOrderRestrictDate($objOrderRestrict->id);

            // Check if booking offset fields changed
            $offsetChanged = false;
            if ($objOrderRestrict->max_checkout_offset != $oldOrderRestrict->max_checkout_offset
                || $objOrderRestrict->min_booking_offset != $oldOrderRestrict->min_booking_offset
                || $objOrderRestrict->use_global_max_checkout_offset != $oldOrderRestrict->use_global_max_checkout_offset
                || $objOrderRestrict->use_global_min_booking_offset != $oldOrderRestrict->use_global_min_booking_offset) {
                $offsetChanged = true;
            }

            if ($offsetChanged) {
                // Get new max booking offset date using booking offset
                $bookingOffsetDate = HotelOrderRestrictDate::getMaxOrderDate($oldOrderRestrict->id_hotel);

                // Get max checkout offset date using checkout offset
                $checkoutOffsetDate = HotelOrderRestrictDate::getMaximumCheckoutOffset($oldOrderRestrict->id_hotel);

                // Use the maximum of both as end date
                $maxBookingOffsetDays = max($bookingOffsetDate, $checkoutOffsetDate);

                // Save ARI update for all room types in this hotel
                $objAriUpdates = new QcmcAriUpdates();
                foreach ($roomTypes as $roomType) {
                    $objAriUpdates->saveAriUpdateRow(
                        (int)$objOrderRestrict->id_hotel,
                        (int)$roomType['id_product'],
                        date('Y-m-d'),
                        $bookingOffsetDate,
                        ['min_booking_offset' => HotelOrderRestrictDate::getMinimumBookingOffset($oldOrderRestrict->id_hotel), 'max_booking_offset' => $checkoutOffsetDate]
                    );
                }
            }
        }
    }


    public function hookActionPartiallyAvailRoomSearchSqlModifier(&$params)
    {
        // Check if request is from trusted partners
        if (!QcmcChannelManagerApiService::isAllowedRemoteAddr($_SERVER['REMOTE_ADDR'])) {
            return;
        }

        // Check if id_order is passed in params
        if (isset($params['params']['all_params']['id_order']) && $params['params']['all_params']['id_order']) {
            $idOrder = (int) $params['params']['all_params']['id_order'];

            // Modify the SQL queries to exclude rooms based on order ID
            if (isset($params['sql']) && is_array($params['sql'])) {
                foreach ($params['sql'] as &$sqlQuery) {
                    // Check if this is the booking detail query
                    if (strpos($sqlQuery, 'qlo_htl_booking_detail') !== false) {
                        // Add id_order exclusion to exclude only bookings NOT matching current order
                        // Insert after is_refunded = 0 condition
                        $sqlQuery = preg_replace(
                            '/`is_refunded` = 0 AND IF\(/',
                            '`is_refunded` = 0 AND `id_order` != ' . $idOrder . ' AND IF(',
                            $sqlQuery
                        );
                    }
                }
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
          || !$this->removeOldChannelManagerConnectorModule()
          || !$this->callInstallTab()
        ) {
            return false;
        }
        return true;
    }

    public function hookDisplayOrderRoomsBookingsTableHeading($params)
    {
        $objOrder = isset($params['order']) ? $params['order'] : null;
        if (!$objOrder || !Validate::isLoadedObject($objOrder)) {
            return '';
        } else if (empty($objOrder->id_channel_manager_booking)) {
            return '';
        } else {
            return '<th><span class="title_box">' . $this->l('Rate Plan') . '</span></th>';
        }
    }

    public function hookDisplayOrderRoomsBookingsTableData($params)
    {
        $objOrder = isset($params['order']) ? $params['order'] : null;
        if (!$objOrder || !Validate::isLoadedObject($objOrder)) {
            return '';
        } else if (empty($objOrder->id_channel_manager_booking)) {
            return '';
        } else {
            $ratePlanName = isset($params['data']['rate_plan_name']) ? $params['data']['rate_plan_name'] : '--';
            return '<td>' . htmlspecialchars($ratePlanName) . '</td>';
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        // Loaded on every BO page (not gated on controller_name) — the tab's menu icon
        // (.icon-AdminQloappsChannelManagerConnector) renders in the persistent sidebar
        // menu across all admin pages, not just this controller's own page.
        $this->context->controller->addCSS($this->_path.'views/css/admin/admin_tab_logo.css');
    }

    public function hookDisplayAdminListBefore()
    {
        // This tpl will only display when at least one booking has come from channel manager
        if (self::CM_BOOKING_LIST_TAB_CLASS == $this->context->controller->controller_name
            && QcmcChannelManagerOrder::hasChannelManagerBookings()
        ) {
            return $this->display(__FILE__, 'qcmc_channel_manager_connection_info.tpl');
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        // This CSS will only apply when at least one booking has come from channel manager
        if (self::CM_BOOKING_LIST_TAB_CLASS == $this->context->controller->controller_name
            && QcmcChannelManagerOrder::hasChannelManagerBookings()
        ) {
            $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/hook/qcmc_cm_connection_info.css');
        }
    }

    public function callInstallTab()
    {
        return $this->installTab(self::CM_BOOKING_LIST_TAB_CLASS, 'Channel Manager');
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false, $need_tab = true)
    {
        // Defensive: remove any existing Tab row with this class_name first (leftover from the
        // old qlochannelmanagerconnector module, or a previous partial install/upgrade run) so we
        // never end up with two rows sharing the same class_name.
        if ($existingIdTab = (int) Tab::getIdFromClassName($class_name)) {
            $existingTab = new Tab($existingIdTab);
            if (Validate::isLoadedObject($existingTab)) {
                $existingTab->delete();
            }
        }

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
        }

        // Defensive: also purge any orphaned Tab row with our class_name not attributed to this module.
        if ($idTab = (int) Tab::getIdFromClassName(self::CM_BOOKING_LIST_TAB_CLASS)) {
            $orphanTab = new Tab($idTab);
            if (Validate::isLoadedObject($orphanTab)) {
                $orphanTab->delete();
            }
        }

        return true;
    }

    public function removeOldChannelManagerConnectorModule()
    {
        $oldModuleName = 'qlochannelmanagerconnector';

        // Only attempt uninstall if the module is actually installed (Module::isInstalled()
        // checks the ps_module row directly) — don't try to load/instantiate it otherwise.
        if (Module::isInstalled($oldModuleName)) {
            $oldModule = Module::getInstanceByName($oldModuleName);
            // uninstall() already runs the old module's own dropTables() + uninstallTab() —
            // table and tab cleanup is handled by that call, nothing extra needed here.
            if (Validate::isLoadedObject($oldModule)) {
                $oldModule->uninstall();
            }
        }

        $oldModuleDir = _PS_MODULE_DIR_.$oldModuleName;
        if (is_dir($oldModuleDir)) {
            Tools::deleteDirectory($oldModuleDir);
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
                'actionObjectOrderAddBefore',
                'actionObjectOrderUpdateAfter',
                'actionAvailRoomSearchSqlModifier',
                'actionPartiallyAvailRoomSearchSqlModifier',
                // added hook to manage the rate plan name
                'actionObjectHotelBookingDetailPropertiesModifier',
                'actionObjectHotelBookingDetailDefinitionModifier',
                'displayOrderRoomsBookingsTableHeading',
                'displayOrderRoomsBookingsTableData',
                // added hook to add id_channel_manager_booking to the order object
                'actionObjectOrderPropertiesModifier',
                'actionObjectOrderDefinitionModifier',
                // added hooks to check room type status, price and restriction related changes
                'actionObjectProductUpdateBefore',
                'actionObjectHotelRoomTypeUpdateBefore',
                'actionObjectHotelRoomTypeRestrictionDateRangeUpdateBefore',
                'actionObjectHotelRoomTypeRestrictionDateRangeAddBefore',
                'actionObjectHotelOrderRestrictDateUpdateBefore',
                'actionObjectHotelRoomTypeRestrictionDateRangeDeleteBefore',
                // hooks for Advanced Price Rules (Feature Pricing) ARI updates
                'actionObjectHotelRoomTypeFeaturePricingAddBefore',
                'actionObjectHotelRoomTypeFeaturePricingUpdateBefore',
                'actionObjectHotelRoomTypeFeaturePricingDeleteBefore',
                // hooks for auto-added service product ARI updates
                'actionObjectRoomTypeServiceProductLinkBefore',
                'actionObjectRoomTypeServiceProductUnlinkBefore',
                // hooks for the absorbed channel-manager booking list admin feature
                'displayAdminListBefore',
                'displayBackOfficeHeader',
                'actionAdminControllerSetMedia',
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
}
