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

require_once _PS_MODULE_DIR_ . 'qlogooglehotelconnector/classes/QghcRequiredClasses.php';

class AdminGoogleHotelConfigController extends ModuleAdminController
{
    /** _conf[] code: connection created successfully. */
    const CONF_CONNECTED    = 101;
    /** _conf[] code: connection removed successfully. */
    const CONF_DISCONNECTED = 102;
    /** _conf[] code: webservice key regenerated successfully. */
    const CONF_KEY_REGENERATED = 103;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->override_folder = '';
        $this->meta_title = $this->l('Google Hotel Connector - Configuration');

        // Standard QloApps pattern for a success message that survives the
        // post-save redirect (matches blocknewsletter/wkmyallocatorconnector,
        // which both extend _conf[] with custom codes above core's own range)
        // — core's own alerts.tpl reads and displays these automatically.
        $this->_conf[self::CONF_CONNECTED]    = $this->l('Connection created successfully.');
        $this->_conf[self::CONF_DISCONNECTED] = $this->l('Connection disconnected successfully.');
        $this->_conf[self::CONF_KEY_REGENERATED] = $this->l('Webservice key regenerated successfully.');
    }

    /**
     * Runs before initContent(). After any DB write we redirect (PRG pattern)
     * so a browser refresh cannot re-submit the form.
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitConfigurationConnection')) {
            $this->processSave();
            return;
        }

        if (Tools::isSubmit('submitDeleteConfigurationConnection')) {
            $this->processDelete();
            return;
        }

        if (Tools::isSubmit('submitRegenerateWebserviceKey')) {
            $this->processRegenerateKey();
            return;
        }
    }

    /** Renders the configuration page into the admin layout. */
    public function initContent()
    {
        parent::initContent();

        if (!$this->tabAccess['view']) {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');
            return;
        }

        $this->renderForm();
        $this->setTemplate('config.tpl');
    }

    /** Builds all Smarty variables the config template needs; config.tpl is fetched once by display(). */
    public function renderForm()
    {
        $idLang = (int) $this->context->language->id;
        $isConnected = (int) Configuration::get(QloGoogleHotelConnector::CONFIG_STATUS) === QghcProperty::GOOGLE_STATUS_CONNECTED;
        $wsKey = QghcWebserviceSetup::getKey();

        // Named-constant status badges, matching renderStatusBadge()'s mapping in the
        // main module class (used for the same google_status value on the hotel list).
        $statusBadges = array(
            QghcProperty::GOOGLE_STATUS_CONNECTED => array('class' => 'label-success', 'icon' => 'icon-check', 'label' => $this->l('Verified')),
            QghcProperty::GOOGLE_STATUS_FAILED => array('class' => 'label-danger', 'icon' => 'icon-times', 'label' => $this->l('Failed')),
        );
        $pendingBadge = array('class' => 'label-warning', 'icon' => 'icon-clock-o', 'label' => $this->l('Pending'));

        $rows = QghcProperty::getEnrolledProperties($idLang);
        $configs = array();
        $hotelEditBase = $this->context->link->getAdminLink('AdminAddHotel');
        
        if ($rows) {
            foreach ($rows as $row) {
                $badge = isset($statusBadges[(int)$row['google_status']]) ? $statusBadges[(int)$row['google_status']] : $pendingBadge;
                $configs[] = array(
                    'id_hotel' => (int)$row['id_hotel'],
                    'hotel_name' => $row['hotel_name'],
                    'date_add' => $row['date_add'],
                    'edit_url' => $hotelEditBase . '&id=' . (int)$row['id_hotel'] . '&updatehtl_branch_info',
                    'status_class' => $badge['class'],
                    'status_icon' => $badge['icon'],
                    'status_label' => $badge['label'],
                );
            }
        }

        // Token must match the check in ghclogcron.php
        $cronUrl = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/qlogooglehotelconnector/ghclogcron.php?token=' . Tools::encrypt('qlogooglehotelconnector');

        $this->context->smarty->assign(array(
            'configs' => $configs,
            'ws_key' => $wsKey,
            'is_connected' => $isConnected,
            'action_url' => $this->context->link->getAdminLink('AdminGoogleHotelConfig'),
            'cron_url' => $cronUrl,
            'hotel_list_url' => $hotelEditBase,
        ));
    }

    /** Validates and saves the webservice key and connection status. Redirects with a success or error code. */
    public function processSave()
    {
        if (!$this->tabAccess['edit']) {
            $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            return;
        }

        $wsKey  = trim(Tools::getValue(QloGoogleHotelConnector::CONFIG_WS_KEY));
        $pmsUrl = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;

        if (empty($wsKey)) {
            $this->errors[] = $this->l('Webservice key is required.');
            return;
        }

        if (!QghcWebserviceSetup::keyExists($wsKey)) {
            $this->errors[] = $this->l('Invalid webservice key. The key does not exist in your webservice accounts.');
            return;
        }

        $apiService = new QghcApiService();
        $result = $apiService->createConnection($pmsUrl, $wsKey);
        $isConnected = (!empty($result['http_code']) && (int) $result['http_code'] === 200) ? 1 : 0;

        Configuration::updateValue(QloGoogleHotelConnector::CONFIG_WS_KEY, $wsKey);
        Configuration::updateValue(QloGoogleHotelConnector::CONFIG_STATUS, $isConnected ? QghcProperty::GOOGLE_STATUS_CONNECTED : 0);

        if ($isConnected) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminGoogleHotelConfig') . '&conf=' . self::CONF_CONNECTED);
        }

        // Failure: no redirect (matches core's own pattern — a redirect is only used
        // to prevent re-submitting a *successful* save on refresh). $this->errors[]
        // renders immediately via alerts.tpl on this same request.
        $httpCode = !empty($result['http_code']) ? (int)$result['http_code'] : 0;
        if ($httpCode === 0) {
            $detail = $this->l('Could not reach Google Hotel Center — check that it is running and accessible.');
        } else {
            $detail = $this->l('Google Hotel Center returned HTTP') . ' ' . $httpCode . '.';
        }
        $this->errors[] = $this->l('Failed to connect to Google Hotel Center.') . ' ' . $detail;
    }

    /** Disconnects from the channel manager. Blocks deletion if any hotels are still linked to Google Hotel. */
    public function processDelete()
    {
        if (!$this->tabAccess['delete']) {
            $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            return;
        }

        if (QghcProperty::countHotelsLinkedToGoogle() > 0) {
            $this->errors[] = $this->l('You must disable all hotels from Google Hotel before deleting the connection. Please edit each hotel and disable it in the Google Hotel tab.');
            return;
        }

        $wsKey  = QghcWebserviceSetup::getKey();
        $pmsUrl = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;

        if ($wsKey !== '') {
            $apiService = new QghcApiService();
            $result = $apiService->deleteConnection($pmsUrl, $wsKey);
            $cmSuccess = !empty($result['http_code']) && (int) $result['http_code'] === 200;

            if (!$cmSuccess) {
                $this->errors[] = $this->l('Failed to disconnect from Google Hotel Center. Please try again.');
                return;
            }
        }

        Configuration::updateValue(QloGoogleHotelConnector::CONFIG_STATUS, 0);
        Configuration::deleteByName(QloGoogleHotelConnector::CONFIG_WS_KEY);
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminGoogleHotelConfig') . '&conf=' . self::CONF_DISCONNECTED);
    }

    /** Regenerates the QGHC webservice key and re-registers it with the channel manager. */
    public function processRegenerateKey()
    {
        if (!$this->tabAccess['edit']) {
            $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            return;
        }

        $newKey = QghcWebserviceSetup::regenerateKey();
        if (!$newKey) {
            $this->errors[] = $this->l('Failed to regenerate the webservice key.');
            return;
        }

        $pmsUrl = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;
        $result = (new QghcApiService())->createConnection($pmsUrl, $newKey);
        $isConnected = !empty($result['http_code']) && (int) $result['http_code'] === 200;

        Configuration::updateValue(QloGoogleHotelConnector::CONFIG_WS_KEY, $newKey);
        Configuration::updateValue(QloGoogleHotelConnector::CONFIG_STATUS, $isConnected ? QghcProperty::GOOGLE_STATUS_CONNECTED : 0);

        if ($isConnected) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminGoogleHotelConfig') . '&conf=' . self::CONF_KEY_REGENERATED);
        }

        $httpCode = !empty($result['http_code']) ? (int) $result['http_code'] : 0;
        $detail = $httpCode === 0
            ? $this->l('Could not reach Google Hotel Center — check that it is running and accessible.')
            : $this->l('Google Hotel Center returned HTTP') . ' ' . $httpCode . '.';
        $this->errors[] = $this->l('The webservice key was regenerated, but Google Hotel Center could not be notified.') . ' ' . $detail;
    }
}
