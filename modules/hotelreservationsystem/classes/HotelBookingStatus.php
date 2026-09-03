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

/**
 * Catalog of room-booking statuses (Assigned, Checked-in, Checked-out, No-show, Cancelled).
 */
class HotelBookingStatus extends ObjectModel
{
    public $color;
    public $is_terminal;
    public $name;

    public static $definition = array(
        'table' => 'htl_booking_status',
        'primary' => 'id_booking_status',
        'multilang' => true,
        'fields' => array(
            'color' => array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'required' => true),
            'is_terminal' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            // lang fields
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64),
        ),
    );

    /**
     * Which statuses a booking is allowed to move to from its current status.
     *
     * @param int $currentStatus one of HotelBookingDetail::STATUS_*
     * @return array list of HotelBookingDetail::STATUS_* values allowed as the next status
     */
    public static function getAllowedTransitions($currentStatus)
    {
        $transitions = array(
            HotelBookingDetail::STATUS_ASSIGNED => array(
                HotelBookingDetail::STATUS_CHECKED_IN,
                HotelBookingDetail::STATUS_NO_SHOW,
                HotelBookingDetail::STATUS_CANCELLED,
            ),
            HotelBookingDetail::STATUS_CHECKED_IN => array(
                HotelBookingDetail::STATUS_CHECKED_OUT,
                // revert: undo an accidental check-in
                HotelBookingDetail::STATUS_ASSIGNED,
            ),
            HotelBookingDetail::STATUS_CHECKED_OUT => array(
                // revert: undo an accidental check-out
                HotelBookingDetail::STATUS_ASSIGNED,
                HotelBookingDetail::STATUS_CHECKED_IN,
            ),
        );

        return isset($transitions[$currentStatus]) ? $transitions[$currentStatus] : array();
    }

    /**
     * @param int $idLang
     * @return array catalog rows (id_status = id_booking_status, color, is_terminal, name), ordered by id
     */
    public function getAllStatuses($idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $sql = 'SELECT hbs.*, hbs.`id_booking_status` AS id_status, hbsl.`name` FROM `'._DB_PREFIX_.$this->table.'` hbs
            LEFT JOIN `'._DB_PREFIX_.$this->table.'_lang` hbsl ON hbs.`id_booking_status` = hbsl.`id_booking_status` AND hbsl.`id_lang` = '.(int) $idLang.'
            ORDER BY hbs.`id_booking_status` ASC';

        return Db::getInstance()->executeS($sql);
    }
}
