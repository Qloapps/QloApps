<?php
    class HotelOrderRefundRules extends ObjectModel
    {
        public $id;
        public $payment_type;
        public $deduction_value_full_pay;
        public $deduction_value_adv_pay;
        public $days;
        public $date_add;
        public $date_upd;

        public static $definition = array(
            'table' => 'htl_order_refund_rules',
            'primary' => 'id',
            'fields' => array(
                'payment_type' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'days' =>                    array('type' => self::TYPE_FLOAT),
                'deduction_value_full_pay' =>    array('type' => self::TYPE_FLOAT),
                'deduction_value_adv_pay' =>    array('type' => self::TYPE_FLOAT),
                'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
                'date_upd' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ));

        /**
         * [OrderRefundRuleById :: To get Order cancellation rules information by its Id]
         * @param [int] $id [Id of the Order cancellation rule's table which information you want]
         * @return [array|boolean] [If data found then Returns array of the order cancellation rules else returns false]
         */
        public function OrderRefundRuleById($id)
        {
            return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_order_refund_rules` WHERE id='.$id);
        }

        /**
         * [getAllOrderRefundRulesOrderByDays :: To get all refund rules available for order cancellation]
         * @return [type] [If data found then Returns array of the order cancellation rules in the decending order according to the days before which the rule is applicable else returns false]
         */
        public function getAllOrderRefundRulesOrderByDays()
        {
            return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_order_refund_rules` ORDER BY `days` DESC');
        }

        /**
         * [checkIfRuleExistsByCancellationdays :: To check If Rule Exists By Cancellation days]
         * @param [int] $days [days before cancellation]
         * @return [type] [If data found then Returns array of the order cancellation rules else returns false]
         */
        public function checkIfRuleExistsByCancellationdays($days)
        {
            return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_order_refund_rules` WHERE days='.$days);
        }
    }
