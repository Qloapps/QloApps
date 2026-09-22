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

include_once "classes/QghcRequiredClasses.php";

class QloGoogleHotelConnector extends Module
{
    /** PS Configuration key that stores the module webservice key. */
    const CONFIG_WS_KEY = 'QGHC_WS_KEY';
    /** PS Configuration key that stores the module connection status (0 = off, 1 = connected). */
    const CONFIG_STATUS = 'QGHC_STATUS';

    public $debugging = false;
    public $logger;

    /** Sets up module properties (name, version, author) and initialises the file logger. */
    public function __construct()
    {
        $this->name = 'qlogooglehotelconnector';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->qloapps_versions_compliancy = array('min' => '1.6', 'max' => _QLOAPPS_VERSION_);
        parent::__construct();

        $this->displayName = $this->l('QloApps Google Hotel Connector');
        $this->description = $this->l('Publish your hotels on Google Hotel Ads and automatically sync room availability, rates, and booking restrictions.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->setLogger();
    }

    /** Creates the file logger. Uses DEBUG level when $debugging is true, WARNING level otherwise. */
    protected function setLogger()
    {
        if (isset($this->debugging) && $this->debugging) {
            $this->logger = new FileLogger(FileLogger::DEBUG);
        } else {
            $this->logger = new FileLogger(FileLogger::WARNING);
        }

        $this->logger->setFilename($this->getLocalPath() . 'logs/' . date('Ymd') . '.log');
    }

    /** Redirects the Modules > Configure button to the dedicated configuration page. */
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminGoogleHotelConfig'));
    }

    /** Creates module DB tables, registers all hooks, installs admin tabs, and creates the dedicated webservice key. */
    public function install()
    {
        $objModuleDb = new QghcDb();
        if (!parent::install()
            || !$this->registerModuleHooks()
            || !$objModuleDb->createTables()
            || !$this->callInstallTab()
            || !QghcWebserviceSetup::createKey()
        ) {
            return false;
        }

        // Best-effort: a failed CM connection or hotel enrollment must never fail install() —
        // any issue is surfaced via hookDashboardZoneThree instead.
        if ($this->connectToChannelManager()) {
            $this->autoEnrollHotels((int) Configuration::get('PS_LANG_DEFAULT'));
        }

        return true;
    }

    /** Registers this shop with the channel manager using the just-created webservice key.
     *  Mirrors AdminGoogleHotelConfigController::processSave(). */
    private function connectToChannelManager()
    {
        $wsKey  = QghcWebserviceSetup::getKey();
        $pmsUrl = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;
        $result = (new QghcApiService())->createConnection($pmsUrl, $wsKey);
        $isConnected = !empty($result['http_code']) && (int) $result['http_code'] === 200;

        Configuration::updateValue(self::CONFIG_WS_KEY, $wsKey);
        Configuration::updateValue(self::CONFIG_STATUS, $isConnected ? QghcProperty::GOOGLE_STATUS_CONNECTED : 0);

        return $isConnected;
    }

    /** Enables every hotel that passes required-field validation, selecting all its room
     *  types regardless of active status. Hotels that fail are left unenrolled — surfaced by
     *  QghcPropertyService::getHotelsWithIssues() on the dashboard. */
    private function autoEnrollHotels($idLang)
    {
        $hotels = QghcProperty::getAllHotels($idLang);
        if (!$hotels) {
            return;
        }

        $objHotelRoomType = new HotelRoomType();
        foreach ($hotels as $hotel) {
            $idHotel = (int) $hotel['id'];
            if (QghcPropertyService::validateRequiredFields($idHotel, $idLang)) {
                continue;
            }

            $roomTypeIds = array();
            foreach ((array) $objHotelRoomType->getRoomTypeByHotelId($idHotel, $idLang) as $roomType) {
                $roomTypeIds[] = (int) $roomType['id_product'];
            }

            QghcPropertyService::enable($idHotel, $roomTypeIds);
        }
    }

    /** Registers all PrestaShop / QloApps hooks the module needs. */
    public function registerModuleHooks()
    {
        return $this->registerHook(array(
            'actionAdminControllerSetMedia',
            'displayBackOfficeHeader',
            'dashboardZoneThree',
            'dashboardTop',
            'displayAdminAddHotelFormTop',
            'displayAdminAfterHeader',
            'displayAdminAddHotelFormTab',
            'displayAdminAddHotelFormTabContent',
            'actionObjectHotelBranchInformationAddAfter',
            'actionObjectHotelBranchInformationUpdateAfter',
            'actionAdminAddHotelListingFieldsModifier',
            // Google Hotel enrollment columns bolted onto core htl_branch_info/htl_room_type
            // (see QghcDb::createTables()) — these hooks make the new columns native
            // ObjectModel fields, same pattern qlohousekeeping uses for htl_room_information.
            'actionObjectHotelBranchInformationDefinitionModifier',
            'actionObjectHotelBranchInformationPropertiesModifier',
            'actionObjectHotelBranchInformationAddBefore',
            'actionObjectHotelRoomTypeDefinitionModifier',
            'actionObjectHotelRoomTypePropertiesModifier',
            'actionObjectHotelRoomTypeAddBefore',
            // Webservice
            'addWebserviceResources',
            // Webservice key protection — block admin edits/disable/delete of the QGHC key
            'actionObjectWebserviceKeyUpdateBefore',
            'actionObjectWebserviceKeyDeleteBefore',
            // ARI change tracking — booking events
            'actionObjectHotelBookingDetailAddAfter',
            'actionObjectHotelBookingDetailUpdateBefore',
            'actionObjectHotelBookingDetailDeleteAfter',
            // ARI change tracking — room events
            'actionObjectHotelRoomInformationAddAfter',
            'actionObjectHotelRoomInformationDeleteAfter',
            'actionValidateRoomInformation',
            'actionObjectHotelRoomDisableDatesAddAfter',
            'actionObjectHotelRoomDisableDatesDeleteAfter',
            // ARI change tracking — rate events
            'actionObjectProductUpdateBefore',
            'actionObjectHotelRoomTypeFeaturePricingAddBefore',
            'actionObjectHotelRoomTypeFeaturePricingUpdateBefore',
            'actionObjectHotelRoomTypeFeaturePricingDeleteBefore',
            // ARI change tracking — LOS / restriction events
            'actionObjectHotelRoomTypeUpdateBefore',
            'actionObjectHotelRoomTypeRestrictionDateRangeAddBefore',
            'actionObjectHotelRoomTypeRestrictionDateRangeUpdateBefore',
            'actionObjectHotelRoomTypeRestrictionDateRangeDeleteBefore',
            // ARI change tracking — booking offset events
            'actionObjectHotelOrderRestrictDateUpdateBefore',
            // Front-end structured data markup
            'displayFooterBefore',
        ));
    }

    /** Adds the Google Hotel Connector menu group with Configuration and API Logs sub-tabs to the admin sidebar. */
    public function callInstallTab()
    {
        $this->installTab('AdminGoogleHotelParent', 'Google Hotel Connector');
        $this->installTab('AdminGoogleHotelConfig', 'Configuration', 'AdminGoogleHotelParent');
        $this->installTab('AdminGoogleHotelLogs', 'API Logs', 'AdminGoogleHotelParent');
        return true;
    }

    /** Adds a single tab to the admin sidebar. Pass a parent class name to create it as a child tab. */
    public function installTab($className, $tabName, $tabParentName = false, $hiddenTab = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        if ($hiddenTab) {
            $tab->id_parent = -1;
        } elseif ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    /** Removes all module DB tables, admin tabs, the webservice key, and saved configuration values. */
    public function uninstall()
    {
        $objModuleDb = new QghcDb();
        if (!parent::uninstall() || !$this->uninstallTab()) {
            return false;
        }

        $this->disconnectFromChannelManager();

        if (!QghcWebserviceSetup::deleteKey()
            || !$this->deleteConfigVars()
            || !$objModuleDb->dropTables()
        ) {
            return false;
        }

        return true;
    }

    /** Best-effort — never blocks uninstall(), and deliberately does not check
     *  QghcProperty::countHotelsLinkedToGoogle() (unlike the manual Delete Connection
     *  button) since the enrollment tables are about to be dropped regardless. */
    private function disconnectFromChannelManager()
    {
        $wsKey = QghcWebserviceSetup::getKey();
        if ($wsKey === '') {
            return;
        }
        $pmsUrl = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;
        $result = (new QghcApiService())->deleteConnection($pmsUrl, $wsKey);

        $httpCode = !empty($result['http_code']) ? (int) $result['http_code'] : 0;
        if ($httpCode !== 200) {
            // Uninstall must never be blocked by the Channel Manager being unreachable
            // or erroring — but silently proceeding to delete our own key right after
            // this means there's no way to retry, so at least make the failure visible.
            $this->logger->log(
                'disconnectFromChannelManager failed during uninstall — CM not notified. http_code=' . $httpCode
                . ' response=' . json_encode($result),
                FileLogger::WARNING
            );
        }
    }

    /** Deletes all module configuration keys from the PS Configuration table. */
    public function deleteConfigVars()
    {
        $configKeys = array(
            self::CONFIG_WS_KEY,
            self::CONFIG_STATUS,
        );

        foreach ($configKeys as $key) {
            Configuration::deleteByName($key);
        }

        return true;
    }

    /** Removes all admin tabs that belong to this module from the sidebar. */
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

    /** Adds the Google Hotel status column to the hotel listing table in the admin. */
    public function hookActionAdminAddHotelListingFieldsModifier($params)
    {
        if (isset($params['select'])) {
            $params['select'] .= QghcProperty::getHotelListingSelect();
            $params['join'] .= QghcProperty::getHotelListingJoin();
        }

        if (!isset($params['fields']['google_status'])) {
            $params['fields']['google_status'] = array(
                'title' => $this->l('Google Hotel'),
                'align' => 'center',
                'orderby' => false,
                'search' => false,
                'callback' => 'renderStatusBadge',
                'callback_object' => $this,
            );
        }
    }

    /** Returns the badge HTML for a hotel's Google Hotel status cell (Connected / Pending / Failed / Not Connected). */
    public function renderStatusBadge($value, $row)
    {
        $statusLabels = array(
            QghcProperty::GOOGLE_STATUS_NOT_CONNECTED => $this->l('Not Connected'),
            QghcProperty::GOOGLE_STATUS_PENDING => $this->l('Pending'),
            QghcProperty::GOOGLE_STATUS_CONNECTED => $this->l('Connected'),
            QghcProperty::GOOGLE_STATUS_FAILED => $this->l('Failed'),
        );
        $badgeMap = array(
            QghcProperty::GOOGLE_STATUS_NOT_CONNECTED => 'badge-default',
            QghcProperty::GOOGLE_STATUS_PENDING => 'badge-warning',
            QghcProperty::GOOGLE_STATUS_CONNECTED => 'badge-success',
            QghcProperty::GOOGLE_STATUS_FAILED => 'badge-danger',
        );

        $status = (int)$value;

        $this->context->smarty->assign(array(
            'badge' => isset($badgeMap[$status]) ? $badgeMap[$status] : 'badge-default',
            'text' => isset($statusLabels[$status]) ? $statusLabels[$status] : '',
        ));

        return $this->context->smarty->fetch($this->getLocalPath() . 'views/templates/admin/getStatusBadge.tpl');
    }

    /** Loads the sidebar icon CSS on every admin page so the Google Hotel menu icon displays correctly. */
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS(
            $this->_path . 'views/css/admin/qlo_ghc_tab_logo.css'
        );
    }

    /** Loads the module CSS and JS on the hotel edit page, config page, logs page, and admin dashboard. */
    public function hookActionAdminControllerSetMedia($params)
    {
        $currentController = Tools::getValue('controller');
        $cssPath = $this->_path . 'views/css/admin/qlo_ghc_admin.css';
        $jsPath  = $this->_path . 'views/js/admin/qlo_ghc_admin.js';

        if (in_array($currentController, array('AdminGoogleHotelConfig', 'AdminGoogleHotelLogs'))) {
            $this->context->controller->addCSS($cssPath);
            $this->context->controller->addJS($jsPath);
        }

        if ($currentController === 'AdminAddHotel') {
            $this->context->controller->addCSS($cssPath);
            $this->context->controller->addJS($jsPath);
            Media::addJsDef(array(
                'ghcDisableHotelMsg' => $this->l('Disabling Google Hotel will remove this property and all its selected room types from Google Hotel Center.') . '\n\n' . $this->l('Are you sure you want to continue?'),
                'noRoomTypeError' => $this->l('Google Hotel: Please select at least one room type before enabling Google Hotel.'),
            ));
        }

        if ($currentController === 'AdminDashboard') {
            $this->context->controller->addCSS($cssPath);
        }
    }

    /** Permanent dashboard panel: connection status + hotels not listed on Google Hotel. */
    public function hookDashboardZoneThree($params)
    {
        $idLang = (int) $this->context->language->id;

        $this->context->smarty->assign(array(
            'is_connected' => (int) Configuration::get(self::CONFIG_STATUS) === QghcProperty::GOOGLE_STATUS_CONNECTED,
            'hotel_names' => array_values(QghcPropertyService::getHotelsWithIssues($idLang)),
            'hotel_list_url' => $this->context->link->getAdminLink('AdminAddHotel'),
            'config_url' => $this->context->link->getAdminLink('AdminGoogleHotelConfig'),
        ));

        return $this->display(__FILE__, 'dashboard_notification.tpl');
    }

    /** Transient top-of-dashboard banner — only renders when something needs attention. */
    public function hookDashboardTop($params)
    {
        $idLang = (int) $this->context->language->id;
        $isConnected = (int) Configuration::get(self::CONFIG_STATUS) === QghcProperty::GOOGLE_STATUS_CONNECTED;
        $hotelNames = array_values(QghcPropertyService::getHotelsWithIssues($idLang));

        if ($isConnected && !$hotelNames) {
            return '';
        }

        $this->context->smarty->assign(array(
            'is_connected' => $isConnected,
            'hotel_names' => $hotelNames,
            'hotel_list_url' => $this->context->link->getAdminLink('AdminAddHotel'),
            'config_url' => $this->context->link->getAdminLink('AdminGoogleHotelConfig'),
        ));

        return $this->display(__FILE__, 'dashboard_top_warning.tpl');
    }

    /** Shows a notice above the hotel edit panel if the module or this hotel isn't listed on Google Hotel. */
    public function hookDisplayAdminAddHotelFormTop($params)
    {
        $idHotel = (int) ($params['id_hotel'] ?? 0);
        if (!$idHotel) {
            return '';
        }

        $idLang = (int) $this->context->language->id;
        $isConnected = (int) Configuration::get(self::CONFIG_STATUS) === QghcProperty::GOOGLE_STATUS_CONNECTED;
        $issueReasons = QghcPropertyService::getHotelConnectionIssueReasons($idHotel, $idLang);

        if ($isConnected && !$issueReasons) {
            return '';
        }

        $this->context->smarty->assign(array(
            'is_connected' => $isConnected,
            'issue_reasons' => $issueReasons,
            'hotel_edit_config_url' => $this->context->link->getAdminLink('AdminGoogleHotelConfig'),
        ));

        return $this->display(__FILE__, 'hotel_form_top_warning.tpl');
    }

    /** Warning banner on the Manage Hotel list page — only renders when something needs attention. */
    public function hookDisplayAdminAfterHeader($params)
    {
        if (Tools::getValue('controller') !== 'AdminAddHotel'
            || Tools::getValue('id')
            || Tools::getValue('addhtl_branch_info')
        ) {
            return '';
        }

        $idLang = (int) $this->context->language->id;
        $isConnected = (int) Configuration::get(self::CONFIG_STATUS) === QghcProperty::GOOGLE_STATUS_CONNECTED;

        $editBaseUrl = $this->context->link->getAdminLink('AdminAddHotel');
        $hotelsWithIssues = array();
        foreach (QghcPropertyService::getHotelsWithIssues($idLang) as $idHotel => $hotelName) {
            $hotelsWithIssues[] = array(
                'hotel_name' => $hotelName,
                'edit_url' => $editBaseUrl . '&id=' . $idHotel . '&updatehtl_branch_info',
            );
        }

        if ($isConnected && !$hotelsWithIssues) {
            return '';
        }

        $this->context->smarty->assign(array(
            'is_connected' => $isConnected,
            'hotels_with_issues' => $hotelsWithIssues,
            'config_url' => $this->context->link->getAdminLink('AdminGoogleHotelConfig'),
        ));

        return $this->display(__FILE__, 'hotel_list_warning.tpl');
    }

    /** Adds the Google Hotel tab to the hotel edit form tab bar. */
    public function hookDisplayAdminAddHotelFormTab($params)
    {
        return $this->display(__FILE__, 'admin_hotel_tab.tpl');
    }

    /** Fires when a new hotel is created. Saves any Google Hotel form data (enable/disable, room types) submitted at the same time. */
    public function hookActionObjectHotelBranchInformationAddAfter($params)
    {
        if (!isset($params['object'])) {
            return;
        }
        $idHotel = (int) $params['object']->id;
        $this->logger->log('hookActionObjectHotelBranchInformationAddAfter id_hotel=' . $idHotel, FileLogger::DEBUG);
        QghcPropertyService::processDataSave($idHotel);
    }

    /** Fires when an existing hotel is updated. Saves any Google Hotel form data (enable/disable, room types) submitted at the same time. */
    public function hookActionObjectHotelBranchInformationUpdateAfter($params)
    {
        if (!isset($params['object'])) {
            return;
        }
        $idHotel = (int) $params['object']->id;
        $this->logger->log('hookActionObjectHotelBranchInformationUpdateAfter id_hotel=' . $idHotel, FileLogger::DEBUG);
        QghcPropertyService::processDataSave($idHotel);
    }

    /** Exposes the bolted-on Google Hotel columns as native HotelBranchInformation fields — same pattern qlohousekeeping uses for HotelRoomInformation. */
    public function hookActionObjectHotelBranchInformationDefinitionModifier($params)
    {
        $params['definition']['fields']['is_google_hotel_enabled'] = array('type' => ObjectModel::TYPE_INT);
        $params['definition']['fields']['id_google_hotel'] = array('type' => ObjectModel::TYPE_STRING);
        $params['definition']['fields']['google_status'] = array('type' => ObjectModel::TYPE_INT);
        $params['definition']['fields']['failure_reason'] = array('type' => ObjectModel::TYPE_STRING);
    }

    /** Same field additions as above, for the object's own $definition (not the static one PrestaShop caches). */
    public function hookActionObjectHotelBranchInformationPropertiesModifier($params)
    {
        $params['obj_properties']['def']['fields']['is_google_hotel_enabled'] = array('type' => ObjectModel::TYPE_INT);
        $params['obj_properties']['def']['fields']['id_google_hotel'] = array('type' => ObjectModel::TYPE_STRING);
        $params['obj_properties']['def']['fields']['google_status'] = array('type' => ObjectModel::TYPE_INT);
        $params['obj_properties']['def']['fields']['failure_reason'] = array('type' => ObjectModel::TYPE_STRING);
    }

    /** Defaults the new columns on a brand-new hotel row — without this, ObjectModel::add() would try to insert NULL into the NOT NULL flag/status columns and fail. */
    public function hookActionObjectHotelBranchInformationAddBefore($params)
    {
        if (!isset($params['object'])) {
            return;
        }
        $params['object']->is_google_hotel_enabled = 0;
        $params['object']->id_google_hotel = null;
        $params['object']->google_status = QghcProperty::GOOGLE_STATUS_PENDING;
        $params['object']->failure_reason = null;
    }

    /** Exposes the bolted-on Google Hotel columns as native HotelRoomType fields. */
    public function hookActionObjectHotelRoomTypeDefinitionModifier($params)
    {
        $params['definition']['fields']['is_google_hotel_enabled'] = array('type' => ObjectModel::TYPE_INT);
        $params['definition']['fields']['id_google_room_type'] = array('type' => ObjectModel::TYPE_STRING);
    }

    /** Same field additions as above, for the object's own $definition. */
    public function hookActionObjectHotelRoomTypePropertiesModifier($params)
    {
        $params['obj_properties']['def']['fields']['is_google_hotel_enabled'] = array('type' => ObjectModel::TYPE_INT);
        $params['obj_properties']['def']['fields']['id_google_room_type'] = array('type' => ObjectModel::TYPE_STRING);
    }

    /** Defaults the new column on a brand-new room type row — same NOT NULL-insert protection as the hotel hook above. */
    public function hookActionObjectHotelRoomTypeAddBefore($params)
    {
        if (!isset($params['object'])) {
            return;
        }
        $params['object']->is_google_hotel_enabled = 0;
        $params['object']->id_google_room_type = null;
    }

    /** Registers the ghc_api webservice resource so the channel manager can authenticate and call this PMS via the PS API. */
    public function hookAddWebserviceResources()
    {
        return array(
            'ghc_api' => array(
                'description' => 'Google Hotel Connector API',
                'specific_management' => true,
            ),
        );
    }

    // ── Webservice key protection ────────────────────────────────────────────

    public function hookActionObjectWebserviceKeyUpdateBefore($params)
    {
        $this->guardWebserviceKeyObject($params);
    }

    public function hookActionObjectWebserviceKeyDeleteBefore($params)
    {
        $this->guardWebserviceKeyObject($params);
    }

    /** Throws to abort the update/delete when the target account is QGHC's own. */
    private function guardWebserviceKeyObject($params)
    {
        if (QghcWebserviceSetup::$bypassGuard
            || !isset($params['object'])
            || !($params['object'] instanceof WebserviceKey)
            || !$this->isQghcWebserviceAccount((int) $params['object']->id)
        ) {
            return;
        }

        throw new PrestaShopException($this->l('This webservice key is managed by Google Hotel Connector and cannot be modified, disabled, or deleted here. Use the Google Hotel Connector configuration page instead.'));
    }

    /** Reads the DB-persisted description, not $params['object']->description — the object
     *  already carries submitted field values by UpdateBefore time. */
    private function isQghcWebserviceAccount($idAccount)
    {
        if (!$idAccount) {
            return false;
        }
        return (string) Db::getInstance()->getValue(
            'SELECT `description` FROM `' . _DB_PREFIX_ . 'webservice_account`
             WHERE `id_webservice_account` = ' . (int) $idAccount
        ) === QghcWebserviceSetup::WS_ACCOUNT_DESCRIPTION;
    }

    // ── ARI change-tracking helpers ──────────────────────────────────────────
    // Shared by the hooks below to avoid repeating the same date math / DB write
    // in more than one place.

    /** Returns the last bookable date (max checkout offset minus 1 day) for a hotel, in Y-m-d format. */
    private function getMaxBookableDate($idHotel)
    {
        return date('Y-m-d', strtotime(HotelOrderRestrictDate::getMaxOrderDate($idHotel) . ' -1 day'));
    }

    /** Marks a room's full bookable window as changed for both inventory and rate sync. */
    private function markRoomInventoryChanged(HotelRoomInformation $obj)
    {
        $maxBookingDate = $this->getMaxBookableDate((int)$obj->id_hotel);
        (new QghcAriUpdates())->saveChangedRow(
            (int)$obj->id_hotel,
            (int)$obj->id_product,
            date('Y-m-d'),
            $maxBookingDate,
            array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1, QghcAriUpdates::ARI_TYPE_RATE => 1)
        );
    }

    /** Marks a room's blocked/unblocked date range as changed for availability sync. */
    private function markRoomDisableDatesChanged(HotelRoomDisableDates $obj)
    {
        $objHotelRoomInfo = new HotelRoomInformation();
        $roomInfoArray    = $objHotelRoomInfo->getHotelRoomInfoByProductId((int)$obj->id_room_type);
        if (empty($roomInfoArray)) {
            return;
        }
        (new QghcAriUpdates())->saveChangedRow(
            (int)$roomInfoArray[0]['id_hotel'],
            (int)$obj->id_room_type,
            date('Y-m-d', strtotime($obj->date_from)),
            date('Y-m-d', strtotime($obj->date_to)),
            array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1)
        );
    }

    /** Marks a feature-based price rule's room type's full bookable window as changed for rate sync. */
    private function markFeaturePricingChanged(HotelRoomTypeFeaturePricing $objFeaturePrice)
    {
        $idHotel = QghcPropertyService::getHotelIdForProduct($objFeaturePrice->id_product);
        if (!$idHotel) {
            return;
        }
        $maxBookingDate = $this->getMaxBookableDate($idHotel);
        (new QghcAriUpdates())->saveChangedRow(
            $idHotel,
            (int)$objFeaturePrice->id_product,
            date('Y-m-d'),
            $maxBookingDate,
            array(QghcAriUpdates::ARI_TYPE_RATE => 1)
        );
    }

    // ── Booking events ────────────────────────────────────────────────────────

    /** Fires when a room is booked. Marks those dates as changed so the channel manager receives updated inventory. */
    public function hookActionObjectHotelBookingDetailAddAfter($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelBookingDetail)) {
            return;
        }
        $obj = $params['object'];
        // Checkout date is not a booked night — changed range is [date_from, date_to - 1 day]
        (new QghcAriUpdates())->saveChangedRow(
            (int)$obj->id_hotel,
            (int)$obj->id_product,
            date('Y-m-d', strtotime($obj->date_from)),
            date('Y-m-d', strtotime($obj->date_to . ' -1 day')),
            array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1)
        );
    }

    /** Fires when a booking is cancelled or deleted. Marks those dates as changed so availability is updated for Google. */
    public function hookActionObjectHotelBookingDetailDeleteAfter($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelBookingDetail)) {
            return;
        }
        $obj = $params['object'];
        (new QghcAriUpdates())->saveChangedRow(
            (int)$obj->id_hotel,
            (int)$obj->id_product,
            date('Y-m-d', strtotime($obj->date_from)),
            date('Y-m-d', strtotime($obj->date_to . ' -1 day')),
            array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1)
        );
    }

    /** Fires before a booking is changed. If the room type, dates, or booking status changed, marks the old and new date ranges as changed. */
    public function hookActionObjectHotelBookingDetailUpdateBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelBookingDetail)) {
            return;
        }

        $newObj = $params['object'];
        $idBooking = (int)$newObj->id;
        $oldObj = new HotelBookingDetail($idBooking);

        $oldDateFrom = date('Y-m-d', strtotime($oldObj->date_from));
        $oldDateTo = date('Y-m-d', strtotime($oldObj->date_to . ' -1 day'));
        $newDateFrom = date('Y-m-d', strtotime($newObj->date_from));
        $newDateTo = date('Y-m-d', strtotime($newObj->date_to . ' -1 day'));
        $idHotel = (int)$oldObj->id_hotel;
        $oldRoomTypeId = (int)$oldObj->id_product;
        $newRoomTypeId = (int)$newObj->id_product;

        if ($oldRoomTypeId != $newRoomTypeId) {
            // Room type changed — mark old and new room type's ARI as changed across their respective dates
            (new QghcAriUpdates())->saveChangedRow($idHotel, $oldRoomTypeId, $oldDateFrom, $oldDateTo, array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1));
            (new QghcAriUpdates())->saveChangedRow($idHotel, $newRoomTypeId, $newDateFrom, $newDateTo, array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1));
            return;
        }

        $statusChanged = false;
        // Refund state isn't tracked on this row's own is_cancelled/is_refunded columns —
        // HotelBookingDetail::$definition['fields'] never declares them, so core's own
        // Adapter_EntityMapper::load() strips them and they never hydrate here (always
        // null on both $oldObj/$newObj). A full cancellation is still caught via id_status
        // (HotelBookingDetail::changeStatus() sets it to STATUS_CANCELLED), but a refund
        // approved for a non-cancellation event type never touches id_status — the one
        // field HotelBookingDetail::processRefundInBookingTables() always zeroes out on
        // any refund, regardless of event type, is total_price_tax_excl/tax_incl.
        $fieldsToCheck = array('check_in', 'check_out', 'id_status', 'total_price_tax_excl', 'total_price_tax_incl');
        foreach ($fieldsToCheck as $field) {
            if ($oldObj->$field != $newObj->$field) {
                (new QghcAriUpdates())->saveChangedRow($idHotel, $oldRoomTypeId, $oldDateFrom, $oldDateTo, array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1));
                $statusChanged = true;
                break;
            }
        }

        if ($oldDateFrom != $newDateFrom || $oldDateTo != $newDateTo) {
            if (!$statusChanged) {
                (new QghcAriUpdates())->saveChangedRow($idHotel, $oldRoomTypeId, $oldDateFrom, $oldDateTo, array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1));
            }
            (new QghcAriUpdates())->saveChangedRow($idHotel, $newRoomTypeId, $newDateFrom, $newDateTo, array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1));
        }
    }

    // ── Room events ───────────────────────────────────────────────────────────

    /** Fires when a new physical room is added to a room type. Marks the full bookable date range as changed for inventory and rate sync. */
    public function hookActionObjectHotelRoomInformationAddAfter($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomInformation)) {
            return;
        }
        $this->markRoomInventoryChanged($params['object']);
    }

    /** Fires when a physical room is removed. Marks the full bookable date range as changed so Google's inventory count is updated. */
    public function hookActionObjectHotelRoomInformationDeleteAfter($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomInformation)) {
            return;
        }
        $this->markRoomInventoryChanged($params['object']);
    }

    /** Fires when a room's status changes (e.g. active to maintenance). If the change is permanent, marks the full bookable date range as changed. */
    public function hookActionValidateRoomInformation($params)
    {
        if (!isset($params['room_information'])) {
            return;
        }
        $newRoomInfo = $params['room_information'];
        if (!isset($newRoomInfo['id'])) {
            return;
        }

        $idRoomInfo = (int)$newRoomInfo['id'];
        $oldRoom = new HotelRoomInformation($idRoomInfo);
        $oldStatus = (int)$oldRoom->id_status;
        $newStatus = isset($newRoomInfo['id_status']) ? (int)$newRoomInfo['id_status'] : $oldStatus;

        if (($newStatus !== HotelRoomInformation::STATUS_TEMPORARY_INACTIVE) && ($oldStatus !== $newStatus)) {
            $objHotelRoomInfo = new HotelRoomInformation();
            $roomInfoArray = $objHotelRoomInfo->getHotelRoomInfoByProductId((int)$oldRoom->id_product);
            if (empty($roomInfoArray)) {
                return;
            }
            $maxBookingDate = $this->getMaxBookableDate((int)$roomInfoArray[0]['id_hotel']);
            (new QghcAriUpdates())->saveChangedRow(
                (int)$roomInfoArray[0]['id_hotel'],
                (int)$oldRoom->id_product,
                date('Y-m-d'),
                $maxBookingDate,
                array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1, QghcAriUpdates::ARI_TYPE_RATE => 1)
            );
        }
    }

    /** Fires when specific dates are blocked for a room. Marks that date range as changed so Google sees the reduced availability. */
    public function hookActionObjectHotelRoomDisableDatesAddAfter($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomDisableDates)) {
            return;
        }
        $this->markRoomDisableDatesChanged($params['object']);
    }

    /** Fires when blocked dates are removed for a room. Marks that date range as changed so the freed availability is pushed to Google. */
    public function hookActionObjectHotelRoomDisableDatesDeleteAfter($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomDisableDates)) {
            return;
        }
        $this->markRoomDisableDatesChanged($params['object']);
    }

    // ── Rate events ───────────────────────────────────────────────────────────

    /** Fires before a room type product is saved. If the base price, tax, or visibility changed, marks the full bookable date range as changed for rate or inventory sync. */
    public function hookActionObjectProductUpdateBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof Product)) {
            return;
        }
        $objProduct = $params['object'];
        $idHotel = QghcPropertyService::getHotelIdForProduct($objProduct->id);

        if (!$idHotel) {
            return;
        }
        $idRoomType = (int)$objProduct->id;
        $oldProduct = new Product($objProduct->id);

        $rateChanged = false;
        $availabilityChanged = false;

        foreach (array('price', 'id_tax_rules_group') as $field) {
            if (isset($objProduct->$field) && isset($oldProduct->$field) && $objProduct->$field != $oldProduct->$field) {
                $rateChanged = true;
                break;
            }
        }
        // active / show_at_front: enabling/disabling a room type affects both
        // availability (rooms disappear/reappear) and rates (CM needs fresh data)
        foreach (array('active', 'show_at_front') as $field) {
            if (isset($objProduct->$field) && isset($oldProduct->$field) && $objProduct->$field != $oldProduct->$field) {
                $availabilityChanged = true;
                break;
            }
        }

        if ($rateChanged || $availabilityChanged) {
            $ariData = array();
            if ($rateChanged) {
                $ariData[QghcAriUpdates::ARI_TYPE_RATE] = 1;
            }
            if ($availabilityChanged) {
                $ariData[QghcAriUpdates::ARI_TYPE_AVAILABILITY] = 1;
                $ariData[QghcAriUpdates::ARI_TYPE_RATE] = 1;
            }
            $maxBookingDate = $this->getMaxBookableDate($idHotel);
            (new QghcAriUpdates())->saveChangedRow($idHotel, $idRoomType, date('Y-m-d'), $maxBookingDate, $ariData);
        }
    }

    /** Fires before a feature-based price rule is added to a room type. Marks the full bookable date range as changed for rate sync. */
    public function hookActionObjectHotelRoomTypeFeaturePricingAddBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomTypeFeaturePricing)) {
            return;
        }
        $this->markFeaturePricingChanged($params['object']);
    }

    /** Fires before a feature-based price rule is changed. Marks the full bookable date range as changed for rate sync. */
    public function hookActionObjectHotelRoomTypeFeaturePricingUpdateBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomTypeFeaturePricing)) {
            return;
        }
        $this->markFeaturePricingChanged($params['object']);
    }

    /** Fires before a feature-based price rule is deleted. Marks the rule's specific date ranges as changed, or the full bookable range if no date ranges are defined. */
    public function hookActionObjectHotelRoomTypeFeaturePricingDeleteBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomTypeFeaturePricing)) {
            return;
        }
        $objFeaturePrice = $params['object'];
        $idHotel = QghcPropertyService::getHotelIdForProduct($objFeaturePrice->id_product);
        if (!$idHotel) {
            return;
        }
        $idRoomType = (int)$objFeaturePrice->id_product;

        // Mark the specific restriction date ranges as changed if available
        $restrictions = array();
        if (class_exists('HotelRoomTypeFeaturePricingRestriction')) {
            $objFeaturePriceRestriction = new HotelRoomTypeFeaturePricingRestriction();
            $restrictions = $objFeaturePriceRestriction->getRestrictionsByIdFeaturePrice($objFeaturePrice->id);
        }

        $hasRestrictions = false;
        if ($restrictions) {
            foreach ($restrictions as $restriction) {
                if (!empty($restriction['date_from']) && !empty($restriction['date_to'])) {
                    $hasRestrictions = true;
                    (new QghcAriUpdates())->saveChangedRow(
                        $idHotel,
                        $idRoomType,
                        date('Y-m-d', strtotime($restriction['date_from'])),
                        date('Y-m-d', strtotime($restriction['date_to'])),
                        array(QghcAriUpdates::ARI_TYPE_RATE => 1)
                    );
                }
            }
        }

        if (!$hasRestrictions) {
            $maxBookingDate = $this->getMaxBookableDate($idHotel);
            (new QghcAriUpdates())->saveChangedRow($idHotel, $idRoomType, date('Y-m-d'), $maxBookingDate, array(QghcAriUpdates::ARI_TYPE_RATE => 1));
        }
    }

    // ── LOS / restriction events ──────────────────────────────────────────────

    /** Fires before a room type record is saved. If the minimum or maximum stay (LOS) changed, marks the full bookable date range as changed for rate sync. */
    public function hookActionObjectHotelRoomTypeUpdateBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomType)) {
            return;
        }
        $objRoomType = $params['object'];
        $idHotel = QghcPropertyService::getHotelIdForProduct($objRoomType->id_product);
        if (!$idHotel) {
            return;
        }
        $oldRoomType = new HotelRoomType($objRoomType->id);
        if ($objRoomType->min_los != $oldRoomType->min_los || $objRoomType->max_los != $oldRoomType->max_los) {
            $maxBookingDate = $this->getMaxBookableDate($idHotel);
            (new QghcAriUpdates())->saveChangedRow($idHotel, (int)$objRoomType->id_product, date('Y-m-d'), $maxBookingDate, array(QghcAriUpdates::ARI_TYPE_RATE => 1));
        }
    }

    /** Fires before a new date-range stay restriction (min/max nights for a specific date period) is added. Marks that date range as changed. */
    public function hookActionObjectHotelRoomTypeRestrictionDateRangeAddBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomTypeRestrictionDateRange)) {
            return;
        }
        $obj = $params['object'];
        $idHotel = QghcPropertyService::getHotelIdForProduct($obj->id_product);
        if (!$idHotel) {
            return;
        }
        // A new restriction always marks its range as changed — no old-vs-new comparison needed
        (new QghcAriUpdates())->saveChangedRow(
            $idHotel,
            (int)$obj->id_product,
            date('Y-m-d', strtotime($obj->date_from)),
            date('Y-m-d', strtotime($obj->date_to)),
            array(QghcAriUpdates::ARI_TYPE_RATE => 1)
        );
    }

    /** Fires before a date-range stay restriction is changed. Marks both the old and new date ranges as changed so stale LOS values are cleared in Google. */
    public function hookActionObjectHotelRoomTypeRestrictionDateRangeUpdateBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomTypeRestrictionDateRange)) {
            return;
        }
        $obj = $params['object'];
        $idHotel = QghcPropertyService::getHotelIdForProduct($obj->id_product);
        if (!$idHotel) {
            return;
        }
        $oldRestriction = new HotelRoomTypeRestrictionDateRange($obj->id);

        // Always invalidate the OLD date range so dates that lose (or change) their
        // restriction are re-synced and no longer show stale LOS values.
        (new QghcAriUpdates())->saveChangedRow(
            $idHotel,
            (int)$obj->id_product,
            date('Y-m-d', strtotime($oldRestriction->date_from)),
            date('Y-m-d', strtotime($oldRestriction->date_to)),
            array(QghcAriUpdates::ARI_TYPE_RATE => 1)
        );

        // Always invalidate the NEW date range regardless of whether only dates or
        // LOS values changed — either case must be pushed to the channel manager.
        (new QghcAriUpdates())->saveChangedRow(
            $idHotel,
            (int)$obj->id_product,
            date('Y-m-d', strtotime($obj->date_from)),
            date('Y-m-d', strtotime($obj->date_to)),
            array(QghcAriUpdates::ARI_TYPE_RATE => 1)
        );
    }

    /** Fires before a date-range stay restriction is removed. Marks that date range as changed so Google receives the updated (now unrestricted) rates. */
    public function hookActionObjectHotelRoomTypeRestrictionDateRangeDeleteBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelRoomTypeRestrictionDateRange)) {
            return;
        }
        $obj = $params['object'];
        $idHotel = QghcPropertyService::getHotelIdForProduct($obj->id_product);
        if (!$idHotel) {
            return;
        }
        (new QghcAriUpdates())->saveChangedRow(
            $idHotel,
            (int)$obj->id_product,
            date('Y-m-d', strtotime($obj->date_from)),
            date('Y-m-d', strtotime($obj->date_to)),
            array(QghcAriUpdates::ARI_TYPE_RATE => 1)
        );
    }

    // ── Booking-offset events ─────────────────────────────────────────────────

    /**
     * Fires before the hotel's booking window settings are saved.
     * HotelOrderRestrictDate stores how many days in advance guests must book (min_booking_offset)
     * and how far into the future the hotel accepts bookings (max_checkout_offset).
     * When either setting changes, the entire range of bookable dates changes, so we mark ALL
     * room types as changed across the full new window to keep Google's ARI data accurate.
     */
    public function hookActionObjectHotelOrderRestrictDateUpdateBefore($params)
    {
        if (!isset($params['object']) || !($params['object'] instanceof HotelOrderRestrictDate)) {
            return;
        }
        $objOrderRestrict = $params['object'];
        $oldRestrict = new HotelOrderRestrictDate($objOrderRestrict->id);

        $offsetChanged = (
            $objOrderRestrict->max_checkout_offset != $oldRestrict->max_checkout_offset
            || $objOrderRestrict->min_booking_offset != $oldRestrict->min_booking_offset
            || $objOrderRestrict->use_global_max_checkout_offset != $oldRestrict->use_global_max_checkout_offset
            || $objOrderRestrict->use_global_min_booking_offset != $oldRestrict->use_global_min_booking_offset
        );

        if (!$offsetChanged) {
            return;
        }

        $objHotelRoomType = new HotelRoomType();
        $roomTypes = $objHotelRoomType->getRoomTypeByHotelId($objOrderRestrict->id_hotel, $this->context->language->id);

        if (!$roomTypes) {
            return;
        }

        $maxBookingDate = $this->getMaxBookableDate((int)$oldRestrict->id_hotel);

        foreach ($roomTypes as $roomType) {
            (new QghcAriUpdates())->saveChangedRow(
                (int)$objOrderRestrict->id_hotel,
                (int)$roomType['id_product'],
                date('Y-m-d'),
                $maxBookingDate,
                array(QghcAriUpdates::ARI_TYPE_AVAILABILITY => 1, QghcAriUpdates::ARI_TYPE_RATE => 1)
            );
        }
    }

    /** Renders the Google Hotel tab inside the hotel edit form (enable/disable switch, room type selection, missing fields warning). */
    public function hookDisplayAdminAddHotelFormTabContent($params)
    {
        $idHotel = (int) $params['id_hotel'];
        $idLang = (int) $this->context->language->id;

        $tabData = QghcPropertyService::getHotelTabData($idHotel, $idLang);

        // Prepare checked state for post-back (handles array input room_types[])
        $postRoomTypes = Tools::getValue('room_types', array());
        $checkedRoomTypes = array();
        if (is_array($postRoomTypes)) {
            foreach ($postRoomTypes as $rtId) {
                $checkedRoomTypes[(int) $rtId] = true;
            }
        }

        $hasWsKey = ((int) Configuration::get(self::CONFIG_STATUS) === QghcProperty::GOOGLE_STATUS_CONNECTED);

        $this->context->smarty->assign(array(
            'id_hotel' => $idHotel,
            'property' => $tabData['ghcProperty'],
            'room_types' => $tabData['roomTypes'],
            'room_type_mappings' => $tabData['roomTypeMappings'],
            'checked_room_types' => $checkedRoomTypes,
            'is_post' => Tools::isSubmit('submitAddhotel'),
            'has_ws_key' => $hasWsKey,
            'config_url' => $this->context->link->getAdminLink('AdminGoogleHotelConfig'),
            'missing_fields' => $tabData['missingFields'],
            'google_status_connected' => QghcProperty::GOOGLE_STATUS_CONNECTED,
            'google_status_pending' => QghcProperty::GOOGLE_STATUS_PENDING,
        ));

        return $this->display(__FILE__, 'admin_hotel_tab_content.tpl');
    }

    // ── Front-end SDM (Structured Data Markup) injection ─────────────────────
    // SDM is invisible Microdata added to the page body that Google reads to show
    // hotel prices and booking info directly in Google Search results — the format
    // Google's hotel price-accuracy validation requires (JSON-LD is deprecated for
    // that specific check per Google's own hotel structured-data reference).

    /** Injects Google Hotel price SDM on the hotel landing page and booking SDM on the checkout page. */
    public function hookDisplayFooterBefore($params)
    {
        $controller = Tools::getValue('controller');

        // Hotel landing page — friendly: /{id}-{name}  non-friendly: ?controller=category&id_category={id}
        if ($controller === 'category') {
            return QghcSdmService::buildLandingPageMicrodata($this->context);
        }

        // Checkout page — friendly: /quick-order  non-friendly: ?controller=order-opc (or ?controller=order)
        // Dispatcher strips hyphens from controller name before setting $_GET['controller']
        if ($controller === 'orderopc' || $controller === 'order') {
            return QghcSdmService::buildCheckoutMicrodata($this->context);
        }

        return '';
    }
}
