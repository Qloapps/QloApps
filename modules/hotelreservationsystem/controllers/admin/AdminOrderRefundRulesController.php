<?php
class AdminOrderRefundRulesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'HotelOrderRefundRules';
        $this->table = 'htl_order_refund_rules';
        $this->_select = ' IF(a.payment_type=1 , CONCAT(round(a.deduction_value_full_pay, 2), " ",  "%"), a.deduction_value_full_pay) AS payment_amount_full_pay';
        $this->_select .= ' ,IF(a.payment_type=1 , CONCAT(round(a.deduction_value_adv_pay, 2), " ",  "%"), a.deduction_value_adv_pay) AS payment_amount_adv_pay';
        $this->_select .= ' ,IF(a.payment_type=1 , \''.$this->l('Percentage').'\', \''.$this->l('Fixed Amount').'\') AS payment_way';
        $this->context = Context::getContext();
        $this->fields_list = array();

        $payment_type = array(1 => 'Percentage', 0 => 'Fixed');

        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'payment_way' => array(
                'title' => $this->l('Payment Type'),
                'align' => 'center',
                'type' => 'select',
                'filter_key' => 'a!payment_type',
                'list' => $payment_type,
            ),
            'payment_amount_full_pay' => array(
                'title' => $this->l('Full Payment Deduction Percentage/Amount'),
                'align' => 'center',
                'type' => 'price',
                'currency' => true,
                'filter_key' => 'a!deduction_value_full_pay',
                'callback' => 'setOrderCurrency',
            ),
            'payment_amount_adv_pay' => array(
                'title' => $this->l('Adv. Payment Deduction Percentage/Amount'),
                'align' => 'center',
                'type' => 'price',
                'currency' => true,
                'filter_key' => 'a!deduction_value_adv_pay',
                'callback' => 'setOrderCurrency',
            ),
            'days' => array(
                'title' => $this->l('Days'),
                'align' => 'center',
            ));
        $this->identifier  = 'id';

        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
                                              'icon' => 'icon-trash',
                                              'confirm' => $this->l('Delete selected items?'))
                                    );
        parent::__construct();
    }

    /**
     * [setOrderCurrency description] - A callback function for setting currency sign with amount
     * @param [type] $echo [description]
     * @param [type] $tr   [description]
     */
    public static function setOrderCurrency($echo, $tr)
    {
        $currency_default = Configuration::get('PS_CURRENCY_DEFAULT');
        return Tools::displayPrice($echo, (int)$currency_default);
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new Refund Rule')
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function renderForm()
    {
        $obj_currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $currency_sign = (new Currency(Configuration::get('PS_CURRENCY_DEFAULT')))->sign;
        if ($this->display == 'edit') {
            $this->context->smarty->assign('edit', 1);
            $refund_rule_id = Tools::getValue('id');
            $htl_order_refund_rules_obj = new HotelOrderRefundRules();
            $htl_order_refund_rules = $htl_order_refund_rules_obj->OrderRefundRuleById($refund_rule_id);
            $this->context->smarty->assign('defaultcurrency_sign', $currency_sign);
            $this->context->smarty->assign('refund_rules_info', $htl_order_refund_rules);
        }
        $this->context->smarty->assign('defaultcurrency_sign', $currency_sign);

        $this->fields_form = array(
                'submit' => array(
                    'title' => $this->l('Save')
                )
            );
        return parent::renderForm();
    }

    public function processSave()
    {
        $order_refund_rule_id = Tools::getValue('id');
        $payment_type = Tools::getValue('refund_payment_type');
        $amount_full_pay = Tools::getValue('deduction_value_full_pay');
        $amount_adv_pay = Tools::getValue('deduction_value_adv_pay');
        $cancellation_days =  Tools::getValue('cancellation_days');


        if ($payment_type == '') {
            $this->errors[] = Tools::displayError('Payment Type is required field.');
        }

        if (!$amount_full_pay) {
            $this->errors[] = Tools::displayError('Enter Deduction Value For Full Payment.');
        }
        if (!$amount_adv_pay) {
            $this->errors[] = Tools::displayError('Enter Deduction value for Advance Payment.');
        }

        //payment_type =1 is percentage and 2 is fixed amount
        if ($payment_type == 1) {
            if (!Validate::isFloat($amount_full_pay)) {
                $this->errors[] = Tools::displayError('Enter a valid Deduction percentage.');
            } elseif ($amount_full_pay > 100 || $amount_full_pay < 0) {
                $this->errors[] = Tools::displayError('Enter a valid percentage(0 < % and 100 >= %).');
            }
            if (!Validate::isFloat($amount_adv_pay)) {
                $this->errors[] = Tools::displayError('Enter a valid Deduction percentage.');
            } elseif ($amount_adv_pay > 100 || $amount_adv_pay < 0) {
                $this->errors[] = Tools::displayError('Enter a valid percentage(0 < % and 100 > %).');
            }
        } elseif ($payment_type == 2) {
            if (!Validate::isPrice($amount_full_pay)) {
                $this->errors[] = Tools::displayError('Enter a valid Deduction amount.');
            }
            if (!Validate::isPrice($amount_adv_pay)) {
                $this->errors[] = Tools::displayError('Enter a valid Deduction amount.');
            }
        }
        $refundRules = new HotelOrderRefundRules();
        if ($cancellation_days == '') {
            $this->errors[] = Tools::displayError('Enter How many days before Check In date rule will be aplied.');
        } else if (!Validate::isUnsignedInt($cancellation_days)) {
            $this->errors[] = Tools::displayError('Enter valid number of days.');
        } else if ($refundRules->checkIfRuleExistsByCancellationdays($cancellation_days)) {
            if ($order_refund_rule_id) {
                $order_refund_rule = new HotelOrderRefundRules($order_refund_rule_id);
                if ($order_refund_rule->days != $cancellation_days) {
                    $this->errors[] = Tools::displayError('Refund rule for ').$cancellation_days.Tools::displayError(' days already exists.');
                }
            } else {
                $this->errors[] = Tools::displayError('Refund rule for ').$cancellation_days.Tools::displayError(' days already exists.');
            }
        }

        if (!count($this->errors)) {
            if ($order_refund_rule_id) {
                $obj_order_refund_rules = new HotelOrderRefundRules($order_refund_rule_id);
            } else {
                $obj_order_refund_rules = new HotelOrderRefundRules();
            }

            $obj_order_refund_rules->payment_type = $payment_type;
            $obj_order_refund_rules->deduction_value_full_pay = $amount_full_pay;
            $obj_order_refund_rules->deduction_value_adv_pay = $amount_adv_pay;
            $obj_order_refund_rules->days = $cancellation_days;

            $obj_order_refund_rules->save();

            $new_order_refund_rule_id = $obj_order_refund_rules->id;

            if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                if ($order_refund_rule_id) {
                    Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$new_order_refund_rule_id.'&update'.$this->table.'&conf=4&token='.$this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$new_order_refund_rule_id.'&update'.$this->table.'&conf=3&token='.$this->token);
                }
            } else {
                if ($order_refund_rule_id) {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                }
            }
        } else {
            if ($order_refund_rule_id) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
    }
}
