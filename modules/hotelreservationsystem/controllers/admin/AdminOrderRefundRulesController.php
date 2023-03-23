<?php
class AdminOrderRefundRulesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'HotelOrderRefundRules';
        $this->table = 'htl_order_refund_rules';

        $this->_select = ' IF(a.payment_type='.(int) HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE.' , CONCAT(round(a.deduction_value_full_pay, 2), " ",  "%"), a.deduction_value_full_pay) AS payment_amount_full_pay';
        $this->_select .= ' ,IF(a.payment_type='.(int) HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE.' , CONCAT(round(a.deduction_value_adv_pay, 2), " ",  "%"), a.deduction_value_adv_pay) AS payment_amount_adv_pay';
        $this->_select .= ' ,IF(a.payment_type='.(int) HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE.' , \''.$this->l('Percentage').'\', \''.$this->l('Fixed Amount').'\') AS payment_way';
        $this->_select .= ' ,orrl.`name`';

        $this->identifier  = 'id_refund_rule';

        parent::__construct();

        $this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'htl_order_refund_rules_lang` orrl
        ON (a.id_refund_rule = orrl.id_refund_rule AND orrl.`id_lang` = '.(int) $this->context->language->id.')';

        $CMSs = array ();
        if ($allCms = CMS::getCMSPages($this->context->language->id)) {
            foreach ($allCms as $key => $cms) {
                $CMSs[$key]['id_cms'] = $cms['id_cms'];
                $CMSs[$key]['name'] = $cms['meta_title'];
            }
        }

        $this->fields_options = array(
            'modulesetting' => array(
                'title' =>    $this->l('Order Refund Setting'),
                'fields' =>    array(
                    'WK_ORDER_REFUND_ALLOWED' => array(
                        'title' => $this->l('Enable Order Refund'),
                        'hint' => $this->l('Enable, if you want to enable order refund feature.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool',
                        'required' => true
                    ),
                    'WK_GLOBAL_REFUND_POLICY_CMS' => array(
                        'title' => $this->l('Detailed refund policy'),
                        'type' => 'select',
                        'list' => $CMSs,
                        'identifier' => 'id_cms',
                        'hint' => $this->l('Select CMS for detailed refund policy for the customer.'),
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        $paymentType = array(
            HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE => 'Percentage', HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_FIXED => 'Fixed'
        );

        $this->fields_list = array(
            'id_refund_rule' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
            ),
            'name' => array(
                'title' => $this->l('Name'),
            ),
            'payment_way' => array(
                'title' => $this->l('Payment Type'),
                'align' => 'center',
                'type' => 'select',
                'filter_key' => 'a!payment_type',
                'list' => $paymentType,
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
                'title' => $this->l('Days Before Check-in'),
                'align' => 'center',
            )
        );

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
    }

    public function initContent()
    {
        parent::initContent();
        // to customize the view as per our requirements
        if ($this->display != 'add' && $this->display != 'edit') {
            $this->content = $this->renderOptions();
            $this->content .= $this->renderList();
            $this->context->smarty->assign('content', $this->content);
        }
    }

    /**
     * [setOrderCurrency description] - A callback function for setting currency sign with amount
     * @param [type] $echo [description]
     * @param [type] $tr   [description]
     */
    public function setOrderCurrency($echo, $tr)
    {
        $currency_default = Configuration::get('PS_CURRENCY_DEFAULT');
        return Tools::displayPrice($echo, (int)$currency_default);
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = array(
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new refund rule')
        );
    }

    public function renderForm()
    {
        $smartyVars = array();
        $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $smartyVars['objCurrency'] = $objCurrency;

        if ($this->display == 'edit'
            && $refundRuleInfo = $this->loadObject(true)
        ) {
            $smartyVars['edit'] = 1;
            $idRefundRule = Tools::getValue('id_refund_rule');
            $objRefundRule = new HotelOrderRefundRules();
            $smartyVars['refund_rules_info'] = (array)$refundRuleInfo;
        }

        //lang vars
        $currentLangId = Configuration::get('PS_LANG_DEFAULT');
        $smartyVars['languages'] = Language::getLanguages(false);
        $smartyVars['currentLang'] = Language::getLanguage((int) $currentLangId);

        $smartyVars['WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE;
        $smartyVars['WK_REFUND_RULE_PAYMENT_TYPE_FIXED'] = HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_FIXED;
        $smartyVars['ps_img_dir'] = _PS_IMG_.'l/';
        $this->context->smarty->assign($smartyVars);

        Media::addJsDef(
            array (
                'img_dir_l' => _PS_IMG_.'l/',
                'default_currency_sign' => $objCurrency->sign,
                'WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE' => HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE,
                'WK_REFUND_RULE_PAYMENT_TYPE_FIXED' => HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_FIXED,
            )
        );

        $this->fields_form = array(
                'submit' => array(
                    'title' => $this->l('Save')
                )
            );
        return parent::renderForm();
    }

    public function processSave()
    {
        $idRefundRule = Tools::getValue('id_refund_rule');
        $paymentType = Tools::getValue('refund_payment_type');
        $fullPayAmount = Tools::getValue('deduction_value_full_pay');
        $advPayAmount = Tools::getValue('deduction_value_adv_pay');
        $cancelationDays =  Tools::getValue('cancelation_days');

        // check if field is atleast in default language. Not available in default prestashop
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $defaultLanguage = Language::getLanguage((int) $defaultLangId);
        $languages = Language::getLanguages(false);
        if (!trim(Tools::getValue('name_'.$defaultLangId))) {
            $this->errors[] = $this->l('Name is required at least in ').$defaultLanguage['name'];
        } elseif (!trim(Tools::getValue('description_'.$defaultLangId))) {
            $this->errors[] = $this->l('Description is required at least in ').$defaultLanguage['name'];
        } else {
            foreach ($languages as $lang) {
                // validate non required fields
                if (trim(Tools::getValue('name_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('name_'.$lang['id_lang']))) {
                        $this->errors[] = $this->l('Invalid name in ').$lang['name'];
                    }
                }
                if (trim(Tools::getValue('description_'.$lang['id_lang']))) {
                    if (!Validate::isGenericName(Tools::getValue('description_'.$lang['id_lang']))) {
                        $this->errors[] = sprintf($this->l('Description is not valid in %s'), $lang['name']);
                    }
                }
            }
        }

        if ($paymentType == '') {
            $this->errors[] = $this->l('Invalid payment type.');
        }
        if (!$fullPayAmount) {
            $this->errors[] = $this->l('Enter deduction value for full payment.');
        }
        if (!$advPayAmount) {
            $this->errors[] = $this->l('Enter deduction value for advance payment.');
        }

        if ($paymentType == HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_PERCENTAGE) {
            if (!Validate::isFloat($fullPayAmount)) {
                $this->errors[] = $this->l('Enter a valid deduction percentage for full payment.');
            } elseif ($fullPayAmount > 100 || $fullPayAmount < 0) {
                $this->errors[] = $this->l('Enter a valid percentage(0 < % and 100 >= %) for full payment.');
            }
            if (!Validate::isFloat($advPayAmount)) {
                $this->errors[] = $this->l('Enter a valid deduction percentage for advance payment.');
            } elseif ($advPayAmount > 100 || $advPayAmount < 0) {
                $this->errors[] = $this->l('Enter a valid percentage(0 < % and 100 > %) for advance payment.');
            }
        } elseif ($paymentType == HotelOrderRefundRules::WK_REFUND_RULE_PAYMENT_TYPE_FIXED) {
            if (!Validate::isPrice($fullPayAmount)) {
                $this->errors[] = $this->l('Enter a valid deduction amount for full payment.');
            }
            if (!Validate::isPrice($advPayAmount)) {
                $this->errors[] = $this->l('Enter a valid deduction amount for advance payment.');
            }
        }
        $objRefundRule = new HotelOrderRefundRules($idRefundRule);
        if ($cancelationDays == '') {
            $this->errors[] = $this->l('Enter number of days before check-in date for this rule to be applicable.');
        } else if (!Validate::isUnsignedInt($cancelationDays)) {
            $this->errors[] = $this->l('Enter valid number of days.');
        } else if ($objRefundRule->checkIfRuleExistsByCancelationdays($cancelationDays)) {
            if ($idRefundRule) {
                if ($objRefundRule->days != $cancelationDays) {
                    $this->errors[] = $this->l('Refund rule for ').$cancelationDays.$this->l(' days already exists.');
                }
            } else {
                $this->errors[] = $this->l('Refund rule for ').$cancelationDays.$this->l(' days already exists.');
            }
        }

        if (!count($this->errors)) {
            if ($idRefundRule) {
                $objRefundRule = new HotelOrderRefundRules($idRefundRule);
            } else {
                $objRefundRule = new HotelOrderRefundRules();
            }

            foreach ($languages as $lang) {
                if (!trim(Tools::getValue('name_'.$lang['id_lang']))) {
                    $objRefundRule->name[$lang['id_lang']] = Tools::getValue(
                        'name_'.$defaultLangId
                    );
                } else {
                    $objRefundRule->name[$lang['id_lang']] = Tools::getValue(
                        'name_'.$lang['id_lang']
                    );
                }

                if (!trim(Tools::getValue('description_'.$lang['id_lang']))) {
                    $objRefundRule->description[$lang['id_lang']] = trim(Tools::getValue('description_'.$defaultLangId));
                } else {
                    $objRefundRule->description[$lang['id_lang']] = trim(Tools::getValue('description_'.$lang['id_lang']));
                }
            }

            $objRefundRule->payment_type = $paymentType;
            $objRefundRule->deduction_value_full_pay = $fullPayAmount;
            $objRefundRule->deduction_value_adv_pay = $advPayAmount;
            $objRefundRule->days = $cancelationDays;
            $objRefundRule->save();

            if ($newIdRefundRule = $objRefundRule->id) {
                if (Tools::isSubmit('submitAdd'.$this->table.'AndStay')) {
                    if ($idRefundRule) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_refund_rule='.(int)$newIdRefundRule.'&update'.$this->table.'&conf=4&token='.$this->token);
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&id_refund_rule='.(int)$newIdRefundRule.'&update'.$this->table.'&conf=3&token='.$this->token);
                    }
                } else {
                    if ($idRefundRule) {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                    } else {
                        Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = $this->l('Some error occurred. Please try again.');
            }
        } else {
            if ($idRefundRule) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJs(_MODULE_DIR_.$this->module->name.'/views/js/admin/wk_refund_rule.js');
        $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/HotelReservationAdmin.js');
    }
}
