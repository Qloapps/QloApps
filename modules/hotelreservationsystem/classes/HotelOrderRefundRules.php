<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HotelOrderRefundRules extends ObjectModel
{
    public $payment_type;
    public $deduction_value_full_pay;
    public $deduction_value_adv_pay;
    public $days;

    // lang fields
    public $name;
    public $description;

    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_order_refund_rules',
        'primary' => 'id_refund_rule',
        'multilang' => true,
        'fields' => array(
            'payment_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'days' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'deduction_value_full_pay' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'deduction_value_adv_pay' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),

            // lang fields
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'lang' => true, 'required' => true),
            'description' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'lang' => true, 'required' => true),
    ));

    protected $webserviceParameters = array(
        'objectsNodeName' => 'hotel_refund_rules',
        'objectNodeName' => 'hotel_refund_rule',
        'fields' => array(),
    );

    const WK_REFUND_RULE_PAYMENT_TYPE_FIXED = 1;
    const WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE = 2;

    //Overrided ObjectModet::delete() to delete all the dependencies of the hotel
    public function delete()
    {
        $objBranchRefundRules = new HotelBranchRefundRules();
        $objBranchRefundRules->deleteHotelRefundRules(0, $this->id);
        return parent::delete();
    }

    // $sortHotelPosition will have id_hotel according to which to be sorted
    public function getAllOrderRefundRules($idLang = false, $sortHotelPosition = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT orr.*, orrl.*';

        if ($sortHotelPosition) {
            $sql .= ', IF(brr.`position`, brr.`position`, (SELECT MAX(`position`) FROM `'._DB_PREFIX_.'htl_branch_refund_rules` WHERE `id_hotel` = '.(int)$sortHotelPosition.')+1) as position';
        }

        $sql .= ' FROM `'._DB_PREFIX_.'htl_order_refund_rules` orr';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules_lang` orrl
        ON (orrl.`id_refund_rule` = orr.`id_refund_rule` AND orrl.`id_lang` = '.(int)$idLang.')';

        if ($sortHotelPosition) {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_branch_refund_rules` brr ON (brr.`id_refund_rule` = orr.`id_refund_rule` AND brr.`id_hotel` = '.(int)$sortHotelPosition.')';
        }

        if ($sortHotelPosition) {
            $sql .= ' order by `position` ';
        }

        return Db::getInstance()->executeS($sql);
    }

    /**
     * [OrderRefundRuleById :: To get Order cancellation rules information by its Id]
     * @param [int] $id [Id of the Order cancellation rule's table which information you want]
     * @return [array|boolean] [If data found then Returns array of the order cancellation rules else returns false]
     */
    public function OrderRefundRuleById($idRefundRule)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'htl_order_refund_rules` WHERE `id_refund_rule`='.(int)$idRefundRule
        );
    }

    /**
     * [getAllOrderRefundRulesOrderByDays :: To get all refund rules available for order cancellation]
     * @return [type] [If data found then Returns array of the order cancellation rules in the decending order according to the days before which the rule is applicable else returns false]
     */
    public function getAllOrderRefundRulesOrderByDays()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_order_refund_rules` ORDER BY `days` DESC'
        );
    }

    /**
     * [checkIfRuleExistsByCancellationdays :: To check If Rule Exists By Cancellation days]
     * @param [int] $days [days before cancellation]
     * @return [type] [If data found then Returns array of the order cancellation rules else returns false]
     */
    public function checkIfRuleExistsByCancelationdays($days)
    {
        return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_order_refund_rules` WHERE days='.$days);
    }

    public function getBookingCancellationDetails($idOrder, $idOrderReturn = 0, $idHtlBooking = 0)
    {
        $bookingCancellations = array();
        $objHtlRefundRules = new HotelBranchRefundRules();
        $objHotelBookingDemands = new HotelBookingDemands();
        $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();

        if ($bookingsToRefund = OrderReturn::getOrdersReturnDetail($idOrder, $idOrderReturn, $idHtlBooking)) {
            foreach ($bookingsToRefund as $booking) {
                $bookingCancellationDetail = array();

                if (Validate::isLoadedObject($objHtlBooking = new HotelBookingDetail($booking['id_htl_booking']))) {
                    $objOrder = new Order($objHtlBooking->id_order);
                    $adPaidAmount = 0;
                    $refundValue = 0;

                    $totalDemandsPrice = $objHotelBookingDemands->getRoomTypeBookingExtraDemands(
                        $objHtlBooking->id_order,
                        $objHtlBooking->id_product,
                        $objHtlBooking->id_room,
                        $objHtlBooking->date_from,
                        $objHtlBooking->date_to,
                        1,
                        1
                    );

                    $totalServicesPrice = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom(
                        $objHtlBooking->id,
                        1,
                        1
                    );
                    $paidAmount = $objHtlBooking->total_paid_amount + $totalDemandsPrice + $totalServicesPrice;

                    if ($refundRules = $objHtlRefundRules->getHotelRefundRules($objHtlBooking->id_hotel, 0, 1)) {
                        $orderCurrency = $objOrder->id_currency;
                        $defaultCurrency = Configuration::get('PS_CURRENCY_DEFAULT');

                        $objDefaultCurrency = new Currency($defaultCurrency);
                        $objOrderCurrency = new Currency($orderCurrency);

                        $explodeDate = explode(' ', $booking['date_add']);
                        $dateRequest = date('Y-m-d', strtotime($explodeDate[0]));
                        $startDate = date_create($objHtlBooking->date_from);
                        $dateRequest = date_create($dateRequest);
                        $daysDifference = date_diff($startDate, $dateRequest);

                        $daysBeforeCancel = (int) $daysDifference->format('%a');
                        $ruleApplied = false;
                        foreach ($refundRules as $refRule) {
                            if ($daysBeforeCancel >= $refRule['days']) {
                                if ($objOrder->is_advance_payment) {
                                    $refundValue = $refRule['deduction_value_adv_pay'];
                                } else {
                                    $refundValue = $refRule['deduction_value_full_pay'];
                                }
                                $bookingCancellationDetail['reduction_value'] = $refundValue;

                                if ($refRule['payment_type'] == HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE) {
                                    $bookingCancellationDetail['reduction_type'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE;
                                    $bookingCancellationDetail['cancelation_charge'] = $paidAmount * ($refundValue / 100);
                                } else {
                                    $bookingCancellationDetail['reduction_type'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_FIXED;
                                    if ($defaultCurrency != $orderCurrency) {
                                        $bookingCancellationDetail['cancelation_charge'] = Tools::convertPriceFull(
                                            $refundValue,
                                            $objDefaultCurrency,
                                            $objOrderCurrency
                                        );
                                    } else {
                                        $bookingCancellationDetail['cancelation_charge'] = $refundValue;
                                    }

                                    // if deduction amount is more than the order total cost
                                    if ($bookingCancellationDetail['cancelation_charge'] > $paidAmount) {
                                        $bookingCancellationDetail['cancelation_charge'] = $paidAmount;
                                    }
                                }

                                $ruleApplied = true;
                                break;
                            }
                        }

                        if (!$ruleApplied) {
                            $bookingCancellationDetail['cancelation_charge'] = $paidAmount;
                            $bookingCancellationDetail['reduction_type'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE;
                            $bookingCancellationDetail['reduction_value'] = 100;
                        }
                    } else {
                        $bookingCancellationDetail['cancelation_charge'] = $paidAmount;
                        $bookingCancellationDetail['reduction_type'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE;
                        $bookingCancellationDetail['reduction_value'] = 100;
                    }
                }

                $bookingCancellations[] = $bookingCancellationDetail;
            }
        }

        return $bookingCancellations;
    }

    public static function getApplicableRefundRules($idOrder)
    {
        $idLang = Context::getContext()->language->id;

        $maxDate = Db::getInstance()->getValue(
            'SELECT MAX(DATE(hbd.`date_from`))
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`id_order` = '.(int) $idOrder
        );

        if ($maxDate) {
            $dateToday = date('Y-m-d');

            if (strtotime($maxDate) >= strtotime($dateToday)) {
                $days = HotelBookingDetail::getDays($dateToday, $maxDate); // always returns positive

                $sql = 'SELECT hbrr.`id_hotel_refund_rule`, hbrr.`id_refund_rule`, hbrr.`id_hotel`, hbrr.`position`,
                horrl.`name`, horrl.`description`, horr.`payment_type`, horr.`deduction_value_full_pay`,
                horr.`deduction_value_adv_pay`, horr.`days`
                FROM `'._DB_PREFIX_.'htl_branch_refund_rules` hbrr
                LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules` horr
                ON (horr.`id_refund_rule` = hbrr.`id_refund_rule`)
                LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules_lang` horrl
                ON (horrl.`id_refund_rule` = horr.`id_refund_rule` AND horrl.`id_lang` = '.(int) $idLang.')
                LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                ON (hbd.`id_hotel` = hbrr.`id_hotel` AND hbd.`id_order` = '.(int) $idOrder.')
                WHERE horr.`days` <= '.(int) $days.'
                GROUP BY hbrr.`id_refund_rule`
                ORDER BY hbrr.`position`';

                if ($result = Db::getInstance()->executeS($sql)) {
                    return $result;
                }
            }
        }

        return array();

    }

    public function searchOrderRefundRulesByName($name, $idLang = false)
    {

        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT horr.*, horrl.* FROM `'._DB_PREFIX_.'htl_order_refund_rules` horr
            LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules_lang` horrl
            ON horrl.`id_refund_rule` = horr.`id_refund_rule`
            WHERE (horrl.`name` LIKE \'%'.pSQL($name).'%\' OR horrl.`description` LIKE \'%'.pSQL($name).'%\')
            AND `id_lang`='.(int)$idLang
        );
    }

}
