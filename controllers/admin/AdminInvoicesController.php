<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminInvoicesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'invoice';

        parent::__construct();

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->l('Invoice options'),
                'fields' =>    array(
                    'PS_INVOICE' => array(
                        'title' => $this->l('Enable invoices'),
                        'desc' => $this->l('If enabled, your customers will receive an invoice for their purchase(s).'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_INVOICE_TAXES_BREAKDOWN' => array(
                        'title' => $this->l('Enable tax breakdown'),
                        'desc' => $this->l('Show a summary of tax rates when there are several taxes.'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_PDF_IMG_INVOICE' => array(
                        'title' => $this->l('Enable room type image'),
                        'hint' => $this->l('Adds an image before product name on the invoice'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_INVOICE_PREFIX' => array(
                        'title' => $this->l('Invoice prefix'),
                        'desc' => $this->l('Prefix used for invoice name (e.g. #IN00001).'),
                        'size' => 6,
                        'type' => 'textLang'
                    ),
                    'PS_INVOICE_USE_YEAR' => array(
                        'title' => $this->l('Add current year to invoice number'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_INVOICE_RESET' => array(
                        'title' => $this->l('Reset Invoice progressive number at beginning of the year'),
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                    'PS_INVOICE_YEAR_POS' => array(
                        'title' => $this->l('Position of the year number'),
                        'cast' => 'intval',
                        'show' => true,
                        'required' => false,
                        'type' => 'radio',
                        'validation' => 'isBool',
                        'choices' => array(
                            0 => $this->l('After the progressive number'),
                            1 => $this->l('Before the progressive number')
                        )
                    ),
                    'PS_INVOICE_START_NUMBER' => array(
                        'title' => $this->l('Invoice number'),
                        'desc' => sprintf($this->l('The next invoice will begin with this number, and then increase with each additional invoice. Set to 0 if you want to keep the current number (which is #%s).'), Order::getLastInvoiceNumber() + 1),
                        'size' => 6,
                        'type' => 'text',
                        'cast' => 'intval'
                    ),
                    'PS_INVOICE_LEGAL_FREE_TEXT' => array(
                        'title' => $this->l('Legal free text'),
                        'desc' => $this->l('Use this field to display additional text on your invoice, like specific legal information. It will appear below the payment methods summary.'),
                        'size' => 50,
                        'type' => 'textareaLang',
                    ),
                    'PS_INVOICE_FREE_TEXT' => array(
                        'title' => $this->l('Footer text'),
                        'desc' => $this->l('This text will appear at the bottom of the invoice, below your company details.'),
                        'size' => 50,
                        'type' => 'textLang',
                    ),
                    'PS_INVOICE_MODEL' => array(
                        'title' => $this->l('Invoice model'),
                        'desc' => $this->l('Choose an invoice model.'),
                        'type' => 'select',
                        'identifier' => 'value',
                        'list' => $this->getInvoicesModels()
                    ),
                    'PS_PDF_USE_CACHE' => array(
                        'title' => $this->l('Use the disk as cache for PDF invoices'),
                        'desc' => $this->l('Saves memory but slows down the PDF generation.'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    )
                ),
                'submit' => array('title' => $this->l('Save'))
            )
        );
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

        $this->table = 'invoice_date';
        $this->show_toolbar = false;
        $this->show_form_cancel_button = false;
        $this->toolbar_title = $this->l('Print PDF invoices');
        return parent::renderForm();
    }

    public function initFormByStatus()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('By order status'),
                'icon' => 'icon-time'
            ),
            'input' => array(
                array(
                    'type' => 'checkboxStatuses',
                    'label' => $this->l('Order statuses'),
                    'name' => 'id_order_state',
                    'values' => array(
                        'query' => OrderState::getOrderStates($this->context->language->id),
                        'id' => 'id_order_state',
                        'name' => 'name'
                    ),
                    'hint' => $this->l('You can also export orders which have not been charged yet.')
                )
            ),
            'submit' => array(
                'title' => $this->l('Generate PDF file by status'),
                'id' => 'submitPrint2',
                'icon' => 'process-icon-download-alt'
            )
        );

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT COUNT(DISTINCT(o.id_order)) AS nbOrders, o.current_state as id_order_state
			FROM `'._DB_PREFIX_.'order_invoice` oi
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON oi.id_order = o.id_order
            INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (oi.id_order = hbd.id_order)
			WHERE o.id_shop IN('.implode(', ', Shop::getContextListShopID()).')
            '.HotelBranchInformation::addHotelRestriction(false, 'hbd').'
			AND oi.number > 0
			GROUP BY o.current_state
		 ');

        $status_stats = array();
        foreach ($result as $row) {
            $status_stats[$row['id_order_state']] = $row['nbOrders'];
        }

        $this->tpl_form_vars = array(
            'statusStats' => $status_stats,
            'style' => ''
        );

        $this->table = 'invoice_status';
        $this->show_toolbar = false;
        return parent::renderForm();
    }

    public function initContent()
    {
        $this->display = 'edit';
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->content .= $this->initFormByDate();
        $this->content .= $this->initFormByStatus();
        $this->table = 'invoice';
        $this->content .= $this->renderOptions();

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
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
        if (Tools::isSubmit('submitAddinvoice_date')) {
            if (!Validate::isDate(Tools::getValue('date_from'))) {
                $this->errors[] = $this->l('Invalid "From" date');
            }

            if (!Validate::isDate(Tools::getValue('date_to'))) {
                $this->errors[] = $this->l('Invalid "To" date');
            }

            if (!count($this->errors)) {
                if (count(OrderInvoice::getByDateInterval(Tools::getValue('date_from'), Tools::getValue('date_to')))) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateInvoicesPDF&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')));
                }

                $this->errors[] = $this->l('No invoice has been found for this period.');
            }
        } elseif (Tools::isSubmit('submitAddinvoice_status')) {
            if (!is_array($status_array = Tools::getValue('id_order_state')) || !count($status_array)) {
                $this->errors[] = $this->l('You must select at least one order status.');
            } else {
                foreach ($status_array as $id_order_state) {
                    if (count(OrderInvoice::getByStatus((int)$id_order_state))) {
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateInvoicesPDF2&id_order_state='.implode('-', $status_array));
                    }
                }

                $this->errors[] = $this->l('No invoice has been found for this status.');
            }
        } else {
            parent::postProcess();
        }
    }

    public function beforeUpdateOptions()
    {
        if ((int)Tools::getValue('PS_INVOICE_START_NUMBER') != 0 && (int)Tools::getValue('PS_INVOICE_START_NUMBER') <= Order::getLastInvoiceNumber()) {
            $this->errors[] = $this->l('Invalid invoice number.').Order::getLastInvoiceNumber().')';
        }
    }

    protected function getInvoicesModels()
    {
        $models = array(
            array(
                'value' => 'invoice',
                'name' => 'invoice'
            )
        );

        $templates_override = $this->getInvoicesModelsFromDir(_PS_THEME_DIR_.'pdf/');
        $templates_default = $this->getInvoicesModelsFromDir(_PS_PDF_DIR_);

        foreach (array_merge($templates_default, $templates_override) as $template) {
            $template_name = basename($template, '.tpl');
            $models[] = array('value' => $template_name, 'name' => $template_name);
        }
        return $models;
    }

    protected function getInvoicesModelsFromDir($directory)
    {
        $templates = false;

        if (is_dir($directory)) {
            $templates = glob($directory.'invoice-*.tpl');
        }

        if (!$templates) {
            $templates = array();
        }

        return $templates;
    }
}
