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

class QghcProperty
{
    const GOOGLE_STATUS_NOT_CONNECTED = -1;
    const GOOGLE_STATUS_PENDING = 0;
    const GOOGLE_STATUS_CONNECTED = 1;
    const GOOGLE_STATUS_FAILED = 2;

    /** @var int Set to the hotel's own id — there's no separate property row anymore. */
    public $id;
    public $id_hotel;
    public $id_google_hotel;
    public $google_status;
    public $failure_reason;

    private function __construct($idHotel, array $row)
    {
        $this->id             = (int) $idHotel;
        $this->id_hotel       = (int) $idHotel;
        $this->id_google_hotel = $row['id_google_hotel'];
        $this->google_status  = (int) $row['google_status'];
        $this->failure_reason = $row['failure_reason'];
    }

    /**
     * Returns the QghcProperty instance for a hotel, or null if not enrolled.
     *
     * @param int $idHotel
     * @return QghcProperty|null
     */
    public static function getByHotelId($idHotel)
    {
        $row = Db::getInstance()->getRow(
            'SELECT `is_google_hotel_enabled`, `id_google_hotel`, `google_status`, `failure_reason`
             FROM `' . _DB_PREFIX_ . 'htl_branch_info`
             WHERE `id` = ' . (int) $idHotel
        );

        if (!$row || !$row['is_google_hotel_enabled']) {
            return null;
        }

        return new self($idHotel, $row);
    }

    /**
     * Enrolls a hotel and returns the instance. google_status resets to 0 (pending)
     * until the Channel Manager assigns a Google ID.
     *
     * @param int $idHotel
     * @return QghcProperty|null  null on DB failure
     */
    public static function createForHotel($idHotel)
    {
        $updated = Db::getInstance()->update(
            'htl_branch_info',
            array(
                'is_google_hotel_enabled' => 1,
                'google_status' => self::GOOGLE_STATUS_PENDING,
            ),
            '`id` = ' . (int) $idHotel
        );

        if (!$updated) {
            return null;
        }

        return new self($idHotel, array('id_google_hotel' => null, 'google_status' => self::GOOGLE_STATUS_PENDING, 'failure_reason' => null));
    }

    /**
     * Stores the Google-assigned hotel ID, connection status, and (when FAILED) the
     * rejection reason(s) received from the Channel Manager.
     *
     * @param string      $idGoogleHotel
     * @param int         $googleStatus
     * @param string|null $failureReason
     * @return bool
     */
    public function applyGoogleStatus($idGoogleHotel, $googleStatus, $failureReason = null)
    {
        $this->id_google_hotel = (string) $idGoogleHotel;
        $this->google_status   = (int) $googleStatus;
        $this->failure_reason  = $failureReason;

        return Db::getInstance()->update(
            'htl_branch_info',
            array(
                'id_google_hotel' => pSQL($this->id_google_hotel),
                'google_status' => $this->google_status,
                'failure_reason' => $this->failure_reason !== null ? pSQL($this->failure_reason) : null,
            ),
            '`id` = ' . (int) $this->id_hotel
        );
    }

    /**
     * Un-enrolls this hotel (and, via cascade, all its room type mappings) — resets
     * the flag columns to their disabled defaults. Never touches the hotel row itself
     * beyond these columns.
     *
     * @return bool
     */
    public function delete()
    {
        QghcRoomType::deleteByPropertyId((int) $this->id_hotel);

        return Db::getInstance()->update(
            'htl_branch_info',
            array(
                'is_google_hotel_enabled' => 0,
                'id_google_hotel' => null,
                'google_status' => 0,
                'failure_reason' => null,
            ),
            '`id` = ' . (int) $this->id_hotel
        );
    }

    /**
     * Returns the number of hotels currently enrolled in Google Hotel (any status).
     *
     * @return int
     */
    public static function countHotelsLinkedToGoogle()
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'htl_branch_info`
             WHERE `is_google_hotel_enabled` = 1'
        );
    }

    /**
     * Returns all enrolled properties with their hotel name for the config page.
     *
     * @param int $idLang
     * @return array|false
     */
    public static function getEnrolledProperties($idLang)
    {
        return Db::getInstance()->executeS(
            'SELECT hbi.`id` AS `id_hotel`, hbi.`google_status`, hbi.`date_upd` AS `date_add`,
                    COALESCE(hbl.`hotel_name`, \'\') AS `hotel_name`
             FROM `' . _DB_PREFIX_ . 'htl_branch_info` hbi
             LEFT JOIN `' . _DB_PREFIX_ . 'htl_branch_info_lang` hbl
                 ON hbl.`id` = hbi.`id` AND hbl.`id_lang` = ' . (int) $idLang . '
             WHERE hbi.`is_google_hotel_enabled` = 1
             ORDER BY hbl.`hotel_name` ASC'
        );
    }

    /**
     * Returns every hotel with its name, for the API Logs page's Hotel filter dropdown.
     *
     * @param int $idLang
     * @return array|false
     */
    public static function getAllHotels($idLang)
    {
        return Db::getInstance()->executeS(
            'SELECT hbi.`id`, COALESCE(hbl.`hotel_name`, \'\') AS `hotel_name`
             FROM `' . _DB_PREFIX_ . 'htl_branch_info` hbi
             LEFT JOIN `' . _DB_PREFIX_ . 'htl_branch_info_lang` hbl
                 ON hbl.`id` = hbi.`id` AND hbl.`id_lang` = ' . (int)$idLang . '
             ORDER BY hbl.`hotel_name` ASC'
        );
    }

    /**
     * Returns the SELECT fragment added to the hotel listing query. The enrollment
     * columns now live on the same row being listed (alias `a`) — no join needed.
     * Used by hookActionAdminAddHotelListingFieldsModifier.
     *
     * @return string
     */
    public static function getHotelListingSelect()
    {
        return ', IF(a.`is_google_hotel_enabled` = 1, a.`google_status`, '
            . self::GOOGLE_STATUS_NOT_CONNECTED . ') AS `google_status`';
    }

    /**
     * No join needed anymore — kept as a no-op so the one caller (which appends this
     * to a query string) doesn't need to change.
     *
     * @return string
     */
    public static function getHotelListingJoin()
    {
        return '';
    }
}
