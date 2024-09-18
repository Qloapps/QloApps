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

class QcmcChannelManagerBooking extends ObjectModel
{
    public $id_order;
    public $date_add;

    public static $definition = array(
        'table' => 'qcmc_channel_manager_booking',
        'primary' => 'id_channel_manager_booking',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    const QCMC_CHANNEL_MANAGER_IP = '54.216.64.42';

    /**
     * Returns bookings created by api by channel manager
     * @param integer $idOrder : send id_order if you want booking of a particular order
     * @param string $orderWay : Send order way (DESC|ASC) sorted by date_add
     * @return [array of bookings | false]
     */
    public static function getChannelManagerBookings($idOrder = 0, $orderWay = 'ASC')
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'qcmc_channel_manager_booking` WHERE 1';

        if ($idOrder) {
            $sql .= ' AND `id_order` = '.(int)$idOrder;
        }

        $sql .= ' ORDER BY `date_add` '.$orderWay;

        if ($result = Db::getInstance()->executeS($sql)) {
            if ($idOrder) {
                // if needs only one order row then send only one row from the array
                $result = $result[0];
            }
        }

        return $result;
    }
}
