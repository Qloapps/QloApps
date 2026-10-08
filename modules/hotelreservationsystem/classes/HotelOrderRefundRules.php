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

    public function getBookingCancellationDetails($idOrder, $idOrderReturn = 0, $idHtlBooking = 0)
    {
        $bookingCancellations = array();
        $objHtlRefundRules = new HotelBranchRefundRules();
        $objServiceProductOrderDetail = new ServiceProductOrderDetail();

        if ($bookingsToRefund = OrderReturn::getOrdersReturnDetail($idOrder, $idOrderReturn, $idHtlBooking)) {
            foreach ($bookingsToRefund as $booking) {
                $bookingCancellationDetail = array();

                if (Validate::isLoadedObject($objHtlBooking = new HotelBookingDetail($booking['id_htl_booking']))) {
                    $objOrder = new Order($objHtlBooking->id_order);
                    $adPaidAmount = 0;
                    $refundValue = 0;

                    $totalServicesPrice = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        1,
                        1,
                        null,
                        null,
                        0,
                        $objHtlBooking->id
                    );
                    $totalAmount = $objHtlBooking->total_price_tax_incl + $totalServicesPrice;

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
                                    $bookingCancellationDetail['cancelation_charge'] = $totalAmount * ($refundValue / 100);
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
                                }

                                $ruleApplied = true;
                                break;
                            }
                        }

                        if (!$ruleApplied) {
                            $bookingCancellationDetail['cancelation_charge'] = $totalAmount;
                            $bookingCancellationDetail['reduction_type'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE;
                            $bookingCancellationDetail['reduction_value'] = 100;
                        }
                    } else {
                        $bookingCancellationDetail['cancelation_charge'] = $totalAmount;
                        $bookingCancellationDetail['reduction_type'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE;
                        $bookingCancellationDetail['reduction_value'] = 100;
                    }
                }

                $bookingCancellations[] = $bookingCancellationDetail;
            }
        }

        return $bookingCancellations;
    }

    public function searchByName($query, $idLang = false)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT horr.*, horrl.* FROM `'._DB_PREFIX_.'htl_order_refund_rules` horr
            LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules_lang` horrl
            ON horrl.`id_refund_rule` = horr.`id_refund_rule`
            WHERE (
                horrl.`name` LIKE \'%'.pSQL($query).'%\' OR
                horrl.`description` LIKE \'%'.pSQL($query).'%\'
            )
            AND horrl.`id_lang`='.(int) $idLang
        );
    }

}
