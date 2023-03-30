<?php
/**
* 2010-2023 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2023 Webkul IN
*  @license   https://store.webkul.com/license.html
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
