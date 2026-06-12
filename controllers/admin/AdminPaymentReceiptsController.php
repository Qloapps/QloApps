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

class AdminPaymentReceiptsControllerCore extends AdminControllerCore
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->context = Context::getContext();
        $this->lang = false;
        $this->table = 'order_payment';
       
        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->l('Payment receipts options'),
                'fields' =>    array(
                    'PS_PAYMENT_RECEIPTS' => array(
                        'title' => $this->l('Enable Receipts'),
                        'desc' => $this->l('If enabled, receipts will be generated for order payments.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_PAYMENT_RECEIPTS_PREFIX' => array(
                        'title' => $this->l('Receipt prefix'),
                        'desc' => $this->l('Prefix used for receipt name (e.g. #IN00001).'),
                        'size' => 6,
                        'type' => 'textLang'
                    ),
                    'PS_PAYMENT_RECEIPTS_USE_YEAR' => array(
                        'title' => $this->l('Add current year to receipt number'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_PAYMENT_RECEIPTS_RESET' => array(
                        'title' => $this->l('Reset Receipt progressive number at beginning of the year'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_PAYMENT_RECEIPTS_START_NUMBER' => array(
                        'title' => $this->l('Receipt number'),
                        'desc' => sprintf($this->l('The next receipt will begin with this number, and then increase with each additional receipt. Set to 0 if you want to keep the current number (which is #%s).'), OrderPaymentDetail::getLastPaymentReceiptNumber() + 1),
                        'size' => 6,
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_PAYMENT_RECEIPTS_LEGAL_FREE_TEXT' => array(
                        'title' => $this->l('Legal free text'),
                        'desc' => $this->l('Use this field to display additional text on your receipt, like specific legal information. It will appear below the payment methods summary.'),
                        'size' => 50,
                        'type' => 'textareaLang',
                    ),
                    'PS_PAYMENT_RECEIPTS_FREE_TEXT' => array(
                        'title' => $this->l('Footer text'),
                        'desc' => $this->l('This text will appear at the bottom of the receipt, below your company details.'),
                        'size' => 50,
                        'type' => 'textLang',
                    )
                ),
                'submit' => array('title' => $this->l('Save'))
            )
        );
    }

    public function initContent()
    {
        $this->display = 'edit';
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->table = 'order_payment';
        $this->content .= $this->initFormByDate();

        $this->content .= $this->renderOptions();

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    public function initFormByDate()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('By date'),
                'icon' => 'icon-calendar'
            ),
            'input' => array(
                array(
                    'type' => 'date',
                    'label' => $this->l('From'),
                    'name' => 'date_from',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2011-12-31 (inclusive).')
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('To'),
                    'name' => 'date_to',
                    'maxlength' => 10,
                    'required' => true,
                    'hint' => $this->l('Format: 2012-12-31 (inclusive).')
                )
            ),
            'submit' => array(
                'title' => $this->l('Generate PDF file by date'),
                'id' => 'submitPrint',
                'icon' => 'process-icon-download-alt'
            )
        );

        $this->fields_value = array(
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d')
        );

        $this->table = 'order_payment';
        $this->show_toolbar = false;
        $this->show_form_cancel_button = false;
        $this->toolbar_title = $this->l('Print PDF invoices');
        return parent::renderForm();
    }

    public function initToolbarTitle()
    {
        $this->toolbar_title = array_unique($this->breadcrumbs);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['cancel']);
    }

    public function postProcess()
    {
        if(Tools::isSubmit('submitAddorder_payment')) {
            if (!Validate::isDate(Tools::getValue('date_from'))) {
                $this->errors[] = $this->l('Invalid "From" date');
            }

            if (!Validate::isDate(Tools::getValue('date_to'))) {
                $this->errors[] = $this->l('Invalid "To" date');
            }
            if(Tools::getValue('date_from') > Tools::getValue('date_to')) {
                $this->errors[] = $this->l('"From" date must be earlier than "To" date');
            }
            if (!count($this->errors)) {
                if (count(OrderPaymentDetail::getByDateInterval(Tools::getValue('date_from'), Tools::getValue('date_to')))) {
                   Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generatePaymentReceiptsPDF&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')));
                }

                $this->errors[] = $this->l('No payment receipt has been found for this period.');
            }
        }else{
            parent::postProcess();
        }
    }

    public function beforeUpdateOptions()
    {
        if ((int)Tools::getValue('PS_PAYMENT_RECEIPTS_START_NUMBER') != 0 && (int)Tools::getValue('PS_PAYMENT_RECEIPTS_START_NUMBER') <= OrderPaymentDetail::getLastPaymentReceiptNumber()) {
            $this->errors[] = $this->l('Invalid receipt number.').OrderPaymentDetail::getLastPaymentReceiptNumber().')';
        }
    }
}

