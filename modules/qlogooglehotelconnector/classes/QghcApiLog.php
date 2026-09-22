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

class QghcApiLog extends ObjectModel
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 0;

    // `api_type` column values — every producer (QghcApiService, WebserviceSpecificManagementGhcApi)
    // and consumer (AdminGoogleHotelLogsController::$typeLabels) must use these, never a raw string,
    // or a new type silently shows as its raw value in the admin list (already happened once).
    const TYPE_UNKNOWN = 'unknown';
    const TYPE_CONNECTION = 'connection';
    const TYPE_DELETE_CONNECTION = 'delete_connection';
    const TYPE_GOOGLE_STATUS = 'google_status';
    const TYPE_HOTEL_LIST_FEED = 'hotel_list_feed';
    const TYPE_ARI_FULL = 'ari_full';
    const TYPE_ARI_CHANGE = 'ari_change';

    public $id_hotel;
    public $api_type;
    public $request;
    public $response;
    public $status;
    public $message;
    public $date_add;

    public static $definition = array(
        'table'   => 'qghc_api_log',
        'primary' => 'id_qghc_api_log',
        'fields'  => array(
            'id_hotel' => array('type' => self::TYPE_INT),
            'api_type' => array('type' => self::TYPE_STRING, 'size' => 50),
            'request'  => array('type' => self::TYPE_STRING),
            'response' => array('type' => self::TYPE_STRING),
            'status'   => array('type' => self::TYPE_INT),
            'message'  => array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE),
        ),
    );

    /**
     * Records one webservice request/response cycle.
     * Uses a raw Db::insert() rather than ObjectModel::add() since `request`/`response`
     * hold arbitrary-length JSON bodies — add()'s default pSQL($value, false) would run
     * strip_tags()/nl2br() on them, mangling any `<`/`>` or newline in the JSON. The
     * `true` htmlOK flag below skips that for these two fields.
     *
     * @param int    $idHotel
     * @param string $apiType
     * @param string $request
     * @param string $response
     * @param int    $status   self::STATUS_SUCCESS or self::STATUS_ERROR
     * @param string $message
     * @return bool
     */
    public static function insert($idHotel, $apiType, $request, $response, $status, $message)
    {
        try {
            $inserted = (bool) Db::getInstance()->insert('qghc_api_log', array(
                'id_hotel' => (int) $idHotel,
                'api_type' => pSQL($apiType),
                'request'  => pSQL($request, true),
                'response' => pSQL($response, true),
                'status'   => (int) $status,
                'message'  => pSQL($message),
                'date_add' => date('Y-m-d H:i:s'),
            ));
            if (!$inserted) {
                error_log('QghcApiLog::insert failed (no exception) for api_type=' . $apiType);
            }
            return $inserted;
        } catch (\Exception $e) {
            error_log('QghcApiLog::insert failed: ' . $e->getMessage());
            return false;
        }
    }
}
