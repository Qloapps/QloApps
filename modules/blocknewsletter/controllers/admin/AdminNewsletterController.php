<?php
/**
* 2010-2023 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
*/

class AdminNewsletterController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'merged';
        $this->identifier = 'id_merged';
        $this->allow_export = true;

        $this->_filterCustomer = '';
        $this->_filterNewsletter = '';

        parent::__construct();

        $this->addRowAction('viewCustomer');
        $this->addRowAction('delete');

        $genderList = array();
        $genders = Gender::getGenders($this->context->language->id);
        foreach ($genders as $gender) {
            $genderList[$gender->id_gender] = $gender->name;
        }

        $this->fields_list = array(
            'id_merged' => array(
                'title' => $this->l('ID'),
                'filter_type' => 'int',
                'filter_keys' => array(
                    'customer' => 'c.`id_customer`',
                    'newsletter' => 'n.`id`',
                ),
            ),
            'id_gender' => array(
                'title' => $this->l('Social Title'),
                'type' => 'select',
                'list' => $genderList,
                'filter_type' => 'int',
                'filter_key' => 'id_gender',
                'filter_keys' => array(
                    'customer' => 'c.`id_gender`',
                ),
            ),
            'firstname' => array(
                'title' => $this->l('First Name'),
                'filter_keys' => array(
                    'customer' => 'c.`firstname`',
                ),
            ),
            'lastname' => array(
                'title' => $this->l('Last Name'),
                'filter_keys' => array(
                    'customer' => 'c.`lastname`',
                ),
            ),
            'email' => array(
                'title' => $this->l('Email'),
                'filter_keys' => array(
                    'customer' => 'c.`email`',
                    'newsletter' => 'n.`email`',
                ),
            ),
            'subscribed' => array(
                'title' => $this->l('Subscribed'),
                'type' => 'bool',
                'active' => 'subscribed',
                'filter_keys' => array(
                    'customer' => 'c.`newsletter`',
                    'newsletter' => 'n.`active`',
                ),
                'ajax' => true,
                'search' => false,
                'orderby' => false,
            ),
            'newsletter_date_add' => array(
                'title' => $this->l('Subscribed On'),
                'type' => 'datetime',
                'filter_keys' => array(
                    'customer' => 'c.`newsletter_date_add`',
                    'newsletter' => 'n.`newsletter_date_add`',
                ),
            ),
        );

        $this->list_no_link = true;

        $this->bulk_actions = array(
            'divider' => array(
                'text' => 'divider'
            ),
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->_conf[101] = $this->l('The customer has been unsubscribed from newsletter successfully.');
        $this->_conf[102] = $this->l('The selection has been unsubscribed from newsletter successfully.');
    }

    public function displayEnableLink($token, $id, $value, $active, $id_category = null, $id_product = null)
    {
        $tpl = $this->context->smarty->createTemplate(
            $this->module->getTemplatePath(
                '/views/templates/admin/'.$this->tpl_folder.'/helpers/list/list_action_enable.tpl'
            )
        );

        $tpl->assign(array(
            'enabled' => (bool) $value,
            'url_enable' => self::$currentIndex.'&action=unsubscribeNewsletter&'.$this->identifier.'='.$id.
            '&token='.($token != null ? $token : $this->token),
        ));

        return $tpl->fetch();
    }

    public function displayViewCustomerLink($token = null, $id, $name = null)
    {
        if (Customer::customerIdExistsStatic($id)) {
            $tpl = $this->context->smarty->createTemplate(
                $this->module->getTemplatePath(
                    '/views/templates/admin/'.$this->tpl_folder.'/helpers/list/view-customer-link.tpl'
                )
            );

            $tpl->assign(array(
                'id_merged' => $id,
                'disabled' => !Validate::isUnsignedInt($id),
            ));

            return $tpl->fetch();
        } else {
            $this->addRowActionSkipList('viewCustomer', $id);
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();

        unset($this->toolbar_btn['save']);
        $this->toolbar_btn['export'] = array(
            'href' => self::$currentIndex.'&export'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Export')
        );
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = $this->l('Configuration');
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->display == 'options') {
            $this->content = $this->renderOptions();
            $this->content .= $this->renderList();
            $this->content .= $this->renderFormExport();
            $this->context->smarty->assign(array('content' => $this->content));

            unset($this->toolbar_btn['save']);
        }
    }

    public function renderOptions()
    {
        $this->fields_options = array(
            'general' => array(
                'title' => $this->l('Configuration'),
                'icon' => 'icon-cogs',
                'fields' => array(
                    'NW_VERIFICATION_EMAIL' => array(
                        'type' => 'bool',
                        'title' => $this->l('Would you like to send a verification email after subscription?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                    ),
                    'NW_CONFIRMATION_EMAIL' => array(
                        'type' => 'bool',
                        'title' => $this->l('Would you like to send a confirmation email after subscription?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                    ),
                    'NW_VOUCHER_CODE' => array(
                        'type' => 'text',
                        'title' => $this->l('Welcome voucher code'),
                        'desc' => sprintf(
                            $this->l('You can create a voucher from %s page. Leave blank to disable this feature.'),
                            '<a href="'.$this->context->link->getAdminLink('AdminCartRules').'" target="_blank">'.$this->l('Cart Rules').'</a>'
                        ),
                        'class' => 'fixed-width-xl',
                        'validation' => 'isUnsignedInt',
                        'required' => true,
                        'cast' => 'intval',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitNewsletterOptions',
                )
            ),
        );

        return parent::renderOptions();
    }

    public function renderList()
    {
        $this->tpl_list_vars['title'] = $this->l('Subscribers');
        $this->tpl_list_vars['icon'] = 'icon-list';

        return parent::renderList();
    }

    public function renderFormExport()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Export Email Addresses'),
                'icon' => 'icon-share-alt'
            ),
            'description' => $this->l('Use this option to export list of email addresses as a .CSV file.'),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Newsletter subscribers'),
                    'name' => 'subscribersType',
                    'class' => 'fixed-width-xl',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => Blocknewsletter::EXPORT_ALL_SUBSCRIBERS,
                                'name' => $this->l('All subscribers'),
                            ),
                            array(
                                'id' => Blocknewsletter::EXPORT_SUBSCRIBERS_WITH_ACCOUNT,
                                'name' => $this->l('Subscribers with account'),
                            ),
                            array(
                                'id' => Blocknewsletter::EXPORT_SUBSCRIBERS_WITHOUT_ACCOUNT,
                                'name' => $this->l('Subscribers without account'),
                            ),
                            array(
                                'id' => Blocknewsletter::EXPORT_NON_SUBSCRIBERS,
                                'name' => $this->l('Non-subscribers'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Filter customers who have subscribed to the newsletter or not, and who have an account or not.'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Export .CSV file'),
                'icon' => 'process-icon- icon-share-alt',
                'id' => 'submit-export-email-addresses',
                'name' => 'submitExportEmailAddresses',
            ),
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitNewsletterOptions')) {
            $voucherCode = Tools::getValue('NW_VOUCHER_CODE');
            if (Tools::strlen($voucherCode) > 0) {
                if (!Validate::isDiscountName($voucherCode)) {
                    $this->errors[] = $this->l('The voucher code is invalid.');
                } elseif (!CartRule::cartRuleExists($voucherCode)) {
                    $this->errors[] = $this->l('The voucher code does not exist.');
                }
            }

            if (!count($this->errors)) {
                Configuration::updateValue('NW_VERIFICATION_EMAIL', Tools::getValue('NW_VERIFICATION_EMAIL'));
                Configuration::updateValue('NW_CONFIRMATION_EMAIL', Tools::getValue('NW_CONFIRMATION_EMAIL'));
                Configuration::updateValue('NW_VOUCHER_CODE', $voucherCode);

                Tools::redirectAdmin(self::$currentIndex.'&conf=6&token='.$this->token);
            }
        } elseif (Tools::isSubmit('submitExportEmailAddresses')) {
            $subscribersType = Tools::getValue('subscribersType');
            $subscribers = $this->module->getSubscribers($subscribersType);

            if (count($subscribers)) {
                array_unshift($subscribers, $this->module->getExportCsvHeader());

                $fileName = 'newsletter_emails_'.date('YmdHis').'.csv';
                $filePath = $this->module->getLocalPath().'export/'.$fileName;

                $fileHandle = fopen($filePath, 'w+');
                foreach ($subscribers as $subscriber) {
                    fputcsv($fileHandle, $subscriber);
                }

                if (Tools::file_exists_cache($filePath)) {
                    if (ob_get_level() && ob_get_length() > 0) {
                        ob_end_clean();
                    }

                    header('Content-Type: text/csv');
                    header('Content-Type: application/force-download; charset=UTF-8');
                    header('Cache-Control: no-store, no-cache');
                    header('Content-Disposition: attachment; filename="'.$fileName.'"');

                    @set_time_limit(0);
                    readfile($filePath);
                    exit;
                }
            } else {
                $this->errors[] = $this->l('No email addresses found to export.');
            }
        }

        parent::postProcess();
    }

    private function unsubscribeNewsletter($idMerged)
    {
        $result = false;
        $email = '';
        if (preg_match('/(^N)/', $idMerged)) {
            $idMerged = (int) substr($idMerged, 1);
            $email = Db::getInstance()->getValue(
                'SELECT n.`email`
                FROM `'._DB_PREFIX_.'newsletter` n
                WHERE n.`id` = '.(int) $idMerged
            );
        } else {
            $objCustomer = new Customer($idMerged);
            if (Validate::isLoadedObject($objCustomer)) {
                $email = $objCustomer->email;
            } else {
                $this->errors[] = $this->l('Customer object can not be loaded.');
            }
        }

        if ($email && !count($this->errors)) {
            $registrationStatus = $this->module->isNewsletterRegistered($email);

            if ($registrationStatus < 1) {
                $this->errors[] = $this->l('This email address is not registered.');
            }

            if (!($result = $this->module->unregister($email, $registrationStatus))) {
                $this->errors[] = $this->l('An error occurred while attempting to unsubscribe.');
            }
        }

        return $result;
    }

    public function processUnsubscribeNewsletter()
    {
        $idMerged = Tools::getValue('id_merged');

        if ($this->unsubscribeNewsletter($idMerged)) {
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=101');
        } else {
            $this->errors[] = $this->l('Newsletter unsubscription failed. Please try again.');
        }
    }

    public function processDelete()
    {
        $idMerged = Tools::getValue('id_merged');

        if ($this->unsubscribeNewsletter($idMerged)) {
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=101');
        } else {
            $this->errors[] = $this->l('Newsletter unsubscription failed. Please try again.');
        }
    }

    public function processBulkDelete()
    {
        $result = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $idMerged) {
                $deleteOk = $this->unsubscribeNewsletter($idMerged);
                $result &= $deleteOk;

                if (!$deleteOk) {
                    $this->errors[] = sprintf($this->l('Can\'t delete #%d'), $idMerged);
                }
            }

            if ($result) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=2');
            } else {
                $this->errors[] = $this->l('Something went wrong. Please try again.');
            }
        } else {
            $this->errors[] = $this->l('You must select at least one element to delete.');
        }
    }

    public function getList(
        $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $idLangShop = false
    ) {
        $useLimit = true;
        if ($limit === false) {
            $useLimit = false;
        } elseif (empty($limit)) {
            if (isset($this->context->cookie->{$this->list_id.'_pagination'}) && $this->context->cookie->{$this->list_id.'_pagination'}) {
                $limit = $this->context->cookie->{$this->list_id.'_pagination'};
            } else {
                $limit = $this->_default_pagination;
            }
        }

        $prefix = 'newsletter';
        if (empty($orderBy)) {
            if ($this->context->cookie->{$prefix.$this->list_id.'Orderby'}) {
                $orderBy = $this->context->cookie->{$prefix.$this->list_id.'Orderby'};
            } elseif ($this->_orderBy) {
                $orderBy = $this->_orderBy;
            } else {
                $orderBy = $this->_defaultOrderBy;
            }
        }

        if (empty($orderWay)) {
            if ($this->context->cookie->{$prefix.$this->list_id.'Orderway'}) {
                $orderWay = $this->context->cookie->{$prefix.$this->list_id.'Orderway'};
            } elseif ($this->_orderWay) {
                $orderWay = $this->_orderWay;
            } else {
                $orderWay = $this->_defaultOrderWay;
            }
        }

        $sqlWhere = '';
        do {
            $sqlWhere = ' '.(isset($this->_where) ? $this->_where.' ' : '').
            ($this->deleted ? 'AND a.`deleted` = 0 ' : '').
            (isset($this->_filter) ? $this->_filter : '').
            (isset($this->_group) ? $this->_group.' ' : '');

            $this->_orderBy = $orderBy;
            $this->_orderWay = $orderWay = Tools::strtoupper($orderWay);

            $sqlOrderBy = ' ORDER BY '.$orderBy.' '.pSQL($orderWay);
            $sqlLimit = ' '.(($useLimit === true) ? ' LIMIT '.(int) $start.', '.(int) $limit : '');

            $this->_listsql = 'SELECT c.`id_customer` AS id_merged, gl.`name` AS id_gender, c.`email`, c.`firstname`, c.`lastname`, c.`newsletter` AS subscribed, c.`newsletter_date_add`
            FROM `'._DB_PREFIX_.'customer` c
            LEFT JOIN '._DB_PREFIX_.'gender_lang gl ON (gl.`id_gender` = c.`id_gender` AND gl.`id_lang` = '.(int) $this->context->language->id.')
            WHERE c.`newsletter` = 1 '.$this->_filterCustomer.'
            UNION
            SELECT CONCAT(\'N\', n.`id`) AS id_merged, NULL AS id_gender, n.`email`, NULL AS firstname, NULL AS lastname, n.`active` AS subscribed, n.`newsletter_date_add`
            FROM `'._DB_PREFIX_.'newsletter` n WHERE n.`active` = 1 '.$this->_filterNewsletter.$sqlOrderBy;

            $listCount = 'SELECT FOUND_ROWS() AS `'._DB_PREFIX_.$this->table.'`';

            $this->_list = Db::getInstance()->executeS($this->_listsql, true, false);

            if ($this->_list === false) {
                $this->_list_error = Db::getInstance()->getMsgError();
                break;
            }

            $this->_listTotal = Db::getInstance()->getValue($listCount, false);

            if ($useLimit === true) {
                $start = (int) $start - (int) $limit;
                if ($start < 0) {
                    break;
                }
            } else {
                break;
            }
        } while (empty($this->_list));

        Hook::exec('action'.$this->controller_name.'ListingResultsModifier', array(
            'list' => &$this->_list,
            'list_total' => &$this->_listTotal,
        ));
    }

    public function processFilter()
    {
        Hook::exec('action'.$this->controller_name.'ListingFieldsModifier', array(
            'fields' => &$this->fields_list,
        ));

        $this->ensureListIdDefinition();

        $prefix = $this->getCookieFilterPrefix();

        if (isset($this->list_id)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$prefix.$key});
                } elseif (stripos($key, $this->list_id.'Filter_') === 0) {
                    $this->context->cookie->{$prefix.$key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
            }

            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->list_id.'Filter_') === 0) {
                    $this->context->cookie->{$prefix.$key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
                if (stripos($key, $this->list_id.'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $this->_defaultOrderBy) {
                        unset($this->context->cookie->{$prefix.$key});
                    } else {
                        $this->context->cookie->{$prefix.$key} = $value;
                    }
                } elseif (stripos($key, $this->list_id.'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $this->_defaultOrderWay) {
                        unset($this->context->cookie->{$prefix.$key});
                    } else {
                        $this->context->cookie->{$prefix.$key} = $value;
                    }
                }
            }
        }

        $filters = $this->context->cookie->getFamily($prefix.$this->list_id.'Filter_');
        foreach ($filters as $key => $value) {
            if ($value != null && !strncmp($key, $prefix.$this->list_id.'Filter_', 7 + Tools::strlen($prefix.$this->list_id))) {
                $key = Tools::substr($key, 7 + Tools::strlen($prefix.$this->list_id));

                if (!array_key_exists($key, $this->fields_list)) {
                    continue;
                }

                $field = $this->fields_list[$key];
                foreach ($field['filter_keys'] as $table => $filterKey) {
                    $key = $filterKey;

                    $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
                    if (($type == 'date' || $type == 'datetime' || $type == 'range') && is_string($value)) {
                        $value = json_decode($value, true);
                    }

                    $sqlFilter = '';
                    // only for date filtering (from, to)
                    if (is_array($value)) {
                        if ($type == 'range') {
                            if (isset($value[0]) && !empty($value[0])) {
                                if (!Validate::isUnsignedInt($value[0])) {
                                    $this->errors[] = Tools::displayError('The \'From\' value is invalid');
                                } else {
                                    $sqlFilter .= ' AND '.pSQL($key).' >= '.pSQL($value[0]);
                                }
                            }
                            if (isset($value[1]) && !empty($value[1])) {
                                if (!Validate::isUnsignedInt($value[1])) {
                                    $this->errors[] = Tools::displayError('The \'From\' value is invalid');
                                } elseif (isset($value[0]) && !empty($value[0]) && $value[0] > $value[1]) {
                                    $this->errors[] = Tools::displayError('The \'To\' value cannot be less than from value');
                                } else {
                                    $sqlFilter .= ' AND '.pSQL($key).' <= '.pSQL($value[1]);
                                }
                            }
                        } else {
                            if (isset($value[0]) && !empty($value[0])) {
                                if (!Validate::isDate($value[0])) {
                                    $this->errors[] = Tools::displayError('The \'From\' date format is invalid (YYYY-MM-DD)');
                                } else {
                                    $sqlFilter .= ' AND '.pSQL($key).' >= \''.pSQL(Tools::dateFrom($value[0])).'\'';
                                }
                            }

                            if (isset($value[1]) && !empty($value[1])) {
                                if (!Validate::isDate($value[1])) {
                                    $this->errors[] = Tools::displayError('The \'To\' date format is invalid (YYYY-MM-DD)');
                                } elseif (isset($value[0]) && !empty($value[0]) && strtotime($value[0]) > strtotime($value[1])) {
                                    $this->errors[] = Tools::displayError('The \'To\' date cannot be before than from date');
                                } else {
                                    $sqlFilter .= ' AND '.pSQL($key).' <= \''.pSQL(Tools::dateTo($value[1])).'\'';
                                }
                            }
                        }
                    } else {
                        $sqlFilter .= ' AND ';
                        if ($type == 'int' || $type == 'bool') {
                            $sqlFilter .= pSQL($key).' = '.(int) $value.' ';
                        } elseif ($type == 'decimal') {
                            $sqlFilter .= pSQL($key).' = '.(float) $value.' ';
                        } elseif ($type == 'select') {
                            $sqlFilter .= pSQL($key).' = \''.pSQL($value).'\' ';
                        } elseif ($type == 'price') {
                            $value = (float) str_replace(',', '.', $value);
                            $sqlFilter .= pSQL($key).' = '.pSQL(trim($value)).' ';
                        } else {
                            $sqlFilter .= pSQL($key).' LIKE \'%'.pSQL(trim($value)).'%\' ';
                        }
                    }

                    if ($table == 'customer') {
                        $this->_filterCustomer .= $sqlFilter;
                    } elseif ($table == 'newsletter') {
                        $this->_filterNewsletter .= $sqlFilter;
                    }
                }
            }
        }
    }
}
