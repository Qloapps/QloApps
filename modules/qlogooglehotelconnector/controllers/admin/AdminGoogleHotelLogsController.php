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

class AdminGoogleHotelLogsController extends ModuleAdminController
{
    private $typeLabels = array();

    public function __construct()
    {
        // Every QghcApiLog::TYPE_* must have an entry here, or it shows as its raw
        // value in the filter dropdown, the list column, and the detail view.
        $this->typeLabels = array(
            QghcApiLog::TYPE_ARI_FULL => 'Full Availability & Rates Update',
            QghcApiLog::TYPE_ARI_CHANGE => 'Availability & Rates Change Update',
            QghcApiLog::TYPE_GOOGLE_STATUS => 'Google Status',
            QghcApiLog::TYPE_CONNECTION => 'Connection',
            QghcApiLog::TYPE_DELETE_CONNECTION => 'Delete Connection',
            QghcApiLog::TYPE_HOTEL_LIST_FEED => 'Property Detail',
        );

        $this->bootstrap = true;
        $this->table = 'qghc_api_log';
        $this->className = 'QghcApiLog';
        $this->identifier = 'id_qghc_api_log';
        $this->lang = false;
        $this->noLink = true;
        $this->list_no_link = true;
        $this->_orderBy = 'a.date_add';
        $this->_orderWay = 'DESC';

        // JOIN hotel name from htl_branch_info_lang; id_hotel = 0 = connection/global log
        $idLang = (int) Context::getContext()->language->id;
        $this->_select = 'COALESCE(hbl.`hotel_name`, \'\') AS `hotel_name`';
        $this->_join   = ' LEFT JOIN `' . _DB_PREFIX_ . 'htl_branch_info_lang` hbl' . ' ON hbl.`id` = a.`id_hotel` AND hbl.`id_lang` = ' . $idLang;

        // All real hotels for the Hotel filter (no Global/id=0 entry — that's not a hotel)
        $allHotels = QghcProperty::getAllHotels($idLang);
        $hotelList = array();
        if ($allHotels) {
            foreach ($allHotels as $row) {
                $hotelList[(int)$row['id']] = $row['hotel_name'];
            }
        }

        $this->fields_list = array(
            'id_qghc_api_log' => array(
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'id_hotel' => array(
                'title' => 'Hotel',
                'type' => 'select',
                'filter_key' => 'a!id_hotel',
                'list' => $hotelList,
                'callback' => 'formatHotel',
                'callback_object' => $this,
            ),
            'api_type' => array(
                'title' => 'Log Type',
                'type' => 'select',
                'filter_key' => 'a!api_type',
                'list' => $this->typeLabels,
                'callback' => 'formatType',
                'callback_object' => $this,
            ),
            'status' => array(
                'title' => 'Status',
                'align' => 'center',
                'type' => 'select',
                'filter_key' => 'a!status',
                'list' => array(
                    QghcApiLog::STATUS_SUCCESS => 'Success',
                    QghcApiLog::STATUS_ERROR   => 'Error',
                ),
                'callback' => 'formatStatus',
                'callback_object' => $this,
            ),
            'date_add' => array(
                'title' => 'Date',
                'type' => 'datetime',
                'align' => 'right',
                'filter_key' => 'a!date_add',
            ),
        );

        parent::__construct();

        $this->override_folder = '';
        $this->meta_title = $this->l('Google Hotel Connector - API Logs');
        $this->addRowAction('view');
    }

    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('logs.tpl');
    }

    public function renderList()
    {
        if (!$this->tabAccess['view']) {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');
            return '';
        }

        unset($this->toolbar_btn['new']);
        return parent::renderList();
    }

    public function renderView()
    {
        if (!$this->tabAccess['view']) {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');
            return '';
        }

        // core's initContent() already calls loadObject(true) before renderView() for
        // display=view and adds its own "cannot be loaded" error to $this->errors — don't
        // re-check/re-announce the same thing, just bail without a second, duplicate error.
        if (!Validate::isLoadedObject($this->object)) {
            return '';
        }

        $idLogHotel = (int) $this->object->id_hotel;
        $hotelName  = $idLogHotel ? QghcPropertyService::getHotelName($idLogHotel, (int) $this->context->language->id) : '';

        $log = array(
            'id_qghc_api_log' => (int) $this->object->id,
            'id_hotel' => $idLogHotel,
            'api_type' => $this->object->api_type,
            'status' => (int) $this->object->status,
            'message' => $this->object->message,
            'date_add' => $this->object->date_add,
            'request' => $this->object->request,
            'response' => $this->object->response,
        );

        $log['type_label']  = isset($this->typeLabels[$log['api_type']]) ? $this->typeLabels[$log['api_type']] : $log['api_type'];
        // Same fallback convention as formatHotel() in the list view: no separate
        // existence check, shows the raw ID if the hotel was since deleted instead
        // of hiding the log entirely.
        $log['hotel_label'] = $idLogHotel ? ($hotelName !== '' ? $hotelName : '#' . $idLogHotel) : $this->l('Global Setting');

        foreach (array('request', 'response') as $field) {
            if (!empty($log[$field])) {
                $decoded = json_decode($log[$field], true);
                if (is_array($decoded)) {
                    // If 'body' was stored as a raw JSON string, decode it for display
                    if (isset($decoded['body']) && is_string($decoded['body'])) {
                        $bodyDecoded = json_decode($decoded['body'], true);
                        if (is_array($bodyDecoded)) {
                            $decoded['body'] = $bodyDecoded;
                        }
                    }
                    $log[$field] = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
            }
        }

        $this->context->smarty->assign(array(
            'ghc_log'  => $log,
            'back_url' => $this->context->link->getAdminLink('AdminGoogleHotelLogs'),
        ));

        return $this->createTemplate('log_view.tpl')->fetch();
    }

    /**
     * Same as AdminController::addFiltersToBreadcrumbs() except it also resolves
     * 'select'-type filters (id_hotel, api_type, status) to their label via the
     * field's own 'list' array. Core only special-cases bool/date/datetime/range —
     * for 'select' it falls through to the raw stored value (e.g. "ari_full" or "3"
     * instead of "Full Availability & Rates Update" or the hotel name).
     */
    public function addFiltersToBreadcrumbs()
    {
        if ($this->filter && is_array($this->fields_list)) {
            $filters = array();

            foreach ($this->fields_list as $field => $t) {
                if (isset($t['filter_key'])) {
                    $field = $t['filter_key'];
                }

                if (($val = Tools::getValue($this->table.'Filter_'.$field)) || $val = $this->context->cookie->{$this->getCookieFilterPrefix().$this->table.'Filter_'.$field}) {
                    if (!is_array($val)) {
                        $filter_value = '';
                        if (isset($t['type']) && $t['type'] == 'bool') {
                            $filter_value = ((bool)$val) ? $this->l('yes') : $this->l('no');
                        } elseif (isset($t['type']) && $t['type'] == 'date' || isset($t['type']) && $t['type'] == 'datetime') {
                            $date = json_decode($val, true);
                            if (isset($date[0])) {
                                $filter_value = $date[0];
                                if (isset($date[1]) && !empty($date[1])) {
                                    $filter_value .= ' - '.$date[1];
                                }
                            }
                        } elseif (isset($t['type']) && $t['type'] == 'range') {
                            $range = json_decode($val, true);
                            if (isset($range[0]) && !empty($range[0])) {
                                if (Validate::isUnsignedInt($range[0])) {
                                    $filter_value = $range[0];
                                    if (isset($range[1]) && !empty($range[1])) {
                                        if (Validate::isUnsignedInt($range[1]) && $range[0] < $range[1]) {
                                            $filter_value .= ' - '.$range[1];
                                        }
                                    }
                                }
                            } else {
                                if (isset($range[1]) && !empty($range[1])) {
                                    if (Validate::isUnsignedInt($range[1])) {
                                        $filter_value = $range[1];
                                    }
                                }
                            }
                        } elseif (isset($t['type']) && $t['type'] == 'select' && isset($t['list'][$val])) {
                            $filter_value = htmlspecialchars($t['list'][$val], ENT_QUOTES, 'UTF-8');
                        } elseif (is_string($val)) {
                            $filter_value = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                        }
                        if (!empty($filter_value)) {
                            $filters[] = sprintf($this->l('%s: %s'), $t['title'], $filter_value);
                        }
                    } else {
                        $filter_value = '';
                        foreach ($val as $v) {
                            if (is_string($v) && !empty($v)) {
                                $filter_value .= ' - '.htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                            }
                        }
                        $filter_value = ltrim($filter_value, ' -');
                        if (!empty($filter_value)) {
                            $filters[] = sprintf($this->l('%s: %s'), $t['title'], $filter_value);
                        }
                    }
                }
            }

            if (count($filters)) {
                return sprintf($this->l('Filter by %s'), implode(', ', $filters));
            }
        }
    }

    public function formatHotel($value, $row)
    {
        if (!(int)$value) {
            return '<em>' . $this->l('Global Setting') . '</em>';
        }
        return !empty($row['hotel_name']) ? htmlspecialchars($row['hotel_name'], ENT_QUOTES, 'UTF-8'): (int)$value;
    }

    public function formatType($value, $row)
    {
        $label = isset($this->typeLabels[$value]) ? $this->typeLabels[$value] : $value;
        return htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
    }

    public function formatStatus($value, $row)
    {
        if ((int)$value === QghcApiLog::STATUS_SUCCESS) {
            return '<span class="label label-success">' . $this->l('Success') . '</span>';
        }
        return '<span class="label label-danger">' . $this->l('Error') . '</span>';
    }
}
