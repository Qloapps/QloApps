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

class QcmcChannelManagerOrder
{
    /**
     * An order is treated as channel-manager-sourced if id_channel_manager_booking is set,
     * OR if source isn't the shop's own domain (covers CM bookings where the channel manager
     * didn't send id_channel_manager_booking in the booking_notification request).
     */
    const CM_WHERE = " ( `id_channel_manager_booking` IS NOT NULL AND `id_channel_manager_booking` != '' )
        OR `source` != '%s' ";

    public static function hasChannelManagerBookings()
    {
        // Db::getValue() -> Db::getRow() already appends its own "LIMIT 1" — don't add one here.
        $sql = 'SELECT 1 FROM `'._DB_PREFIX_.'orders` WHERE '.self::getWhere();

        return (bool) Db::getInstance()->getValue($sql);
    }

    public static function getLastChannelManagerBookingDate()
    {
        $sql = 'SELECT `date_add` FROM `'._DB_PREFIX_.'orders` WHERE '.self::getWhere().'
            ORDER BY `date_add` DESC';

        return Db::getInstance()->getValue($sql);
    }

    protected static function getWhere()
    {
        return sprintf(self::CM_WHERE, pSQL(Configuration::get('PS_SHOP_DOMAIN')));
    }
}
