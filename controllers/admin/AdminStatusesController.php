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

/**
 * @property OrderState $object
 */
class AdminStatusesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_state';
        $this->className = 'OrderState';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
        $this->context = Context::getContext();
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os'
        );
        parent::__construct();
    }

    public function init()
    {
        if (Tools::isSubmit('addorder_return_state')) {
            $this->display = 'add';
        }
        if (Tools::isSubmit('updateorder_return_state')) {
            $this->display = 'edit';
        }

        return parent::init();
    }

    /**
     * init all variables to render the order status list
     */
    protected function initOrderStatutsList()
    {
        $this->fields_list = array(
            'id_order_state' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'color' => 'color'
            ),
            'logo' => array(
                'title' => $this->l('Icon'),
                'align' => 'text-center',
                'image' => 'os',
                'orderby' => false,
                'search' => false,
                'class' => 'fixed-width-xs'
            ),
            'send_email' => array(
                'title' => $this->l('Send email to customer'),
                'align' => 'text-center',
                'active' => 'sendEmail',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'delivery' => array(
                'title' => $this->l('Delivery'),
                'align' => 'text-center',
                'active' => 'delivery',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm'
            )
            ,
            'invoice' => array(
                'title' => $this->l('Invoice'),
                'align' => 'text-center',
                'active' => 'invoice',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm'
            ),
            'template' => array(
                'title' => $this->l('Email template')
            )
        );
    }

    /**
     * init all variables to render the order return list
     */
    protected function initOrdersReturnsList()
    {
        $this->table = 'order_return_state';
        $this->className = 'OrderReturnState';
        $this->_defaultOrderBy = $this->identifier = 'id_order_return_state';
        $this->list_id = 'order_return_state';
        $this->deleted = false;
        $this->_orderBy = null;

        $this->fields_list = array(
            'id_order_return_state' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'align' => 'left',
                'width' => 'auto',
                'color' => 'color'
            )
        );
    }

    protected function initOrderReturnsForm()
    {
        $id_order_return_state = (int)Tools::getValue('id_order_return_state');

        // Create Object OrderReturnState
        $order_return_state = new OrderReturnState($id_order_return_state);

        //init field form variable for order return form
        $this->fields_form = array();

        //$this->initToolbar();
        $this->getlanguages();
        $helper = new HelperForm();
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->table = 'order_return_state';
        $helper->identifier = 'id_order_return_state';
        $helper->id = $order_return_state->id;
        $helper->toolbar_scroll = false;
        $helper->languages = $this->_languages;
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;

        if ($order_return_state->id) {
            $helper->fields_value = array(
                'name' => $this->getFieldValue($order_return_state, 'name'),
                'color' => $this->getFieldValue($order_return_state, 'color'),
            );
        } else {
            $helper->fields_value = array(
                'name' => $this->getFieldValue($order_return_state, 'name'),
                'color' => "#ffffff",
            );
        }

        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->title = $this->l('Edit Return Status');
        return $helper;
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_order_state'] = array(
                'href' => self::$currentIndex.'&addorder_state&token='.$this->token,
                'desc' => $this->l('Add new order status', null, null, false),
                'icon' => 'process-icon-new'
            );
            $this->page_header_toolbar_btn['new_order_return_state'] = array(
                'href' => self::$currentIndex.'&addorder_return_state&token='.$this->token,
                'desc' => $this->l('Add new order return status', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Function used to render the list to display for this controller
     */
    public function renderList()
    {
        //init and render the first list
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $unremovable_os = array();
        $buf = Db::getInstance()->executeS('SELECT id_order_state FROM '._DB_PREFIX_.'order_state WHERE unremovable = 1');
        foreach ($buf as $row) $unremovable_os[] = $row['id_order_state'];
        $this->addRowActionSkipList('delete', $unremovable_os);

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            )
        );
        $this->initOrderStatutsList();
        $lists = parent::renderList();

        //init and render the second list
        $this->list_skip_actions = array();
        $this->_filter = false;
        $this->addRowActionSkipList('delete', array(1, 2, 3, 4, 5));
        $this->initOrdersReturnsList();
        $this->checkFilterForOrdersReturnsList();

        // call postProcess() to take care of actions and filters
        $this->postProcess();
        $this->toolbar_title = $this->l('Return statuses');

        parent::initToolbar();
        $lists .= parent::renderList();

        return $lists;
    }

    protected function checkFilterForOrdersReturnsList()
    {
        // test if a filter is applied for this list
        if (Tools::isSubmit('submitFilter'.$this->table) || $this->context->cookie->{'submitFilter'.$this->table} !== false) {
            $this->filter = true;
        }

        // test if a filter reset request is required for this list
        if (isset($_POST['submitReset'.$this->table])) {
            $this->action = 'reset_filters';
        } else {
            $this->action = '';
        }
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Order status'),
                'icon' => 'icon-time'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Status name'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => array(
                        $this->l('Order status (e.g. \'Pending\').'),
                        $this->l('Invalid characters: numbers and').' !<>,;?=+()@#"{}_$%:'
                    )
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Icon'),
                    'name' => 'icon',
                    'hint' => $this->l('Upload an icon from your computer (File type: .gif, suggested size: 16x16).')
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Color'),
                    'name' => 'color',
                    'hint' => $this->l('Status will be highlighted in this color. HTML colors only.').' "lightblue", "#CC6600")'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'logable',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->l('Consider the associated order as validated.'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'invoice',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->l('Allow a customer to download and view PDF versions of his/her invoices.'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'hidden',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->l('Hide this status in all customer orders.'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'send_email',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->l('Send an email to the customer when his/her order status has changed.'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'pdf_invoice',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on',  'name' => $this->l('Attach invoice PDF to email.'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'pdf_delivery',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on',  'name' => $this->l('Attach delivery slip PDF to email.'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'shipped',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on',  'name' => $this->l('Set the order as shipped.'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'paid',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->l('Set the order as paid.'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'delivery',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->l('Show delivery PDF.'), 'val' => '1'),
                            ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'select_template',
                    'label' => $this->l('Template'),
                    'name' => 'template',
                    'lang' => true,
                    'options' => array(
                        'query' => $this->getTemplates(),
                        'id' => 'id',
                        'name' => 'name',
                        'folder' => 'folder'
                    ),
                    'hint' => array(
                        $this->l('Only letters, numbers and underscores ("_") are allowed.'),
                        $this->l('Email template for both .html and .txt.')
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        if (Tools::isSubmit('updateorder_state') || Tools::isSubmit('addorder_state')) {
            return $this->renderOrderStatusForm();
        } elseif (Tools::isSubmit('updateorder_return_state') || Tools::isSubmit('addorder_return_state')) {
            return $this->renderOrderReturnsForm();
        } else {
            return parent::renderForm();
        }
    }

    protected function renderOrderStatusForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = array(
            'logable_on' => $this->getFieldValue($obj, 'logable'),
            'invoice_on' => $this->getFieldValue($obj, 'invoice'),
            'hidden_on' => $this->getFieldValue($obj, 'hidden'),
            'send_email_on' => $this->getFieldValue($obj, 'send_email'),
            'shipped_on' => $this->getFieldValue($obj, 'shipped'),
            'paid_on' => $this->getFieldValue($obj, 'paid'),
            'delivery_on' => $this->getFieldValue($obj, 'delivery'),
            'pdf_delivery_on' => $this->getFieldValue($obj, 'pdf_delivery'),
            'pdf_invoice_on' => $this->getFieldValue($obj, 'pdf_invoice'),
        );

        if ($this->getFieldValue($obj, 'color') !== false) {
            $this->fields_value['color'] = $this->getFieldValue($obj, 'color');
        } else {
            $this->fields_value['color'] = "#ffffff";
        }

        return parent::renderForm();
    }

    protected function renderOrderReturnsForm()
    {
        $helper = $this->initOrderReturnsForm();
        $helper->show_cancel_button = true;

        $back = Tools::safeOutput(Tools::getValue('back', ''));
        if (empty($back)) {
            $back = self::$currentIndex.'&token='.$this->token;
        }
        if (!Validate::isCleanHtml($back)) {
            die(Tools::displayError());
        }

        $helper->back_url = $back;

        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Return status'),
                'icon' => 'icon-time'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Status name'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => array(
                        $this->l('Order\'s return status name.'),
                        $this->l('Invalid characters: numbers and').' !<>,;?=+()@#"�{}_$%:'
                    )
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Color'),
                    'name' => 'color',
                    'hint' => $this->l('Status will be highlighted in this color. HTML colors only.').' "lightblue", "#CC6600")'
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );
        return $helper->generateForm($this->fields_form);
    }

    protected function getTemplates()
    {
        $theme = new Theme($this->context->shop->id_theme);
        $default_path = '../mails/';
        $theme_path = '../themes/'.$theme->directory.'/mails/'; // Mail templates can also be found in the theme folder

        $array = array();
        foreach (Language::getLanguages(false) as $language) {
            $iso_code = $language['iso_code'];

            // If there is no folder for the given iso_code in /mails or in /themes/[theme_name]/mails, we bypass this language
            if (!@filemtime(_PS_ADMIN_DIR_.'/'.$default_path.$iso_code) && !@filemtime(_PS_ADMIN_DIR_.'/'.$theme_path.$iso_code)) {
                continue;
            }

            $theme_templates_dir = _PS_ADMIN_DIR_.'/'.$theme_path.$iso_code;
            $theme_templates = is_dir($theme_templates_dir) ? scandir($theme_templates_dir) : array();
            // We merge all available emails in one array
            $templates = array_unique(array_merge(scandir(_PS_ADMIN_DIR_.'/'.$default_path.$iso_code), $theme_templates));
            foreach ($templates as $key => $template) {
                if (!strncmp(strrev($template), 'lmth.', 5)) {
                    $search_result = array_search($template, $theme_templates);
                    $array[$iso_code][] = array(
                                'id' => substr($template, 0, -5),
                                'name' => substr($template, 0, -5),
                                'folder' => ((!empty($search_result)?$theme_path:$default_path))
                    );
                }
            }
        }

        return $array;
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table.'Orderby') || Tools::isSubmit($this->table.'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('submitAddorder_return_state')) {
            $id_order_return_state = Tools::getValue('id_order_return_state');

            // Create Object OrderReturnState
            $order_return_state = new OrderReturnState((int)$id_order_return_state);

            $order_return_state->color = Tools::getValue('color');
            $order_return_state->name = array();
            foreach (Language::getIDs(false) as $id_lang) {
                $order_return_state->name[$id_lang] = Tools::getValue('name_'.$id_lang);
            }

            // Update object
            if (!$order_return_state->save()) {
                $this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current order\'s return status.');
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
            }
        }

        if (Tools::isSubmit('submitBulkdeleteorder_return_state')) {
            $this->className = 'OrderReturnState';
            $this->table = 'order_return_state';
            $this->boxes = Tools::getValue('order_return_stateBox');
            parent::processBulkDelete();
        }

        if (Tools::isSubmit('deleteorder_return_state')) {
            $id_order_return_state = Tools::getValue('id_order_return_state');

            // Create Object OrderReturnState
            $order_return_state = new OrderReturnState((int)$id_order_return_state);

            if (!$order_return_state->delete()) {
                $this->errors[] = Tools::displayError('An error has occurred: Can\'t delete the current order\'s return status.');
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$this->token);
            }
        }

        if (Tools::isSubmit('submitAdd'.$this->table)) {
            $this->deleted = false; // Disabling saving historisation
            $_POST['invoice'] = (int)Tools::getValue('invoice_on');
            $_POST['logable'] = (int)Tools::getValue('logable_on');
            $_POST['send_email'] = (int)Tools::getValue('send_email_on');
            $_POST['hidden'] = (int)Tools::getValue('hidden_on');
            $_POST['shipped'] = (int)Tools::getValue('shipped_on');
            $_POST['paid'] = (int)Tools::getValue('paid_on');
            $_POST['delivery'] = (int)Tools::getValue('delivery_on');
            $_POST['pdf_delivery'] = (int)Tools::getValue('pdf_delivery_on');
            $_POST['pdf_invoice'] = (int)Tools::getValue('pdf_invoice_on');
            if (!$_POST['send_email']) {
                foreach (Language::getIDs(false) as $id_lang) {
                    $_POST['template_'.$id_lang] = '';
                }
            }

            return parent::postProcess();
        } elseif (Tools::isSubmit('delete'.$this->table)) {
            $order_state = new OrderState(Tools::getValue('id_order_state'), $this->context->language->id);
            if (!$order_state->isRemovable()) {
                $this->errors[] = $this->l('For security reasons, you cannot delete default order statuses.');
            } else {
                return parent::postProcess();
            }
        } elseif (Tools::isSubmit('submitBulkdelete'.$this->table)) {
            foreach (Tools::getValue($this->table.'Box') as $selection) {
                $order_state = new OrderState((int)$selection, $this->context->language->id);
                if (!$order_state->isRemovable()) {
                    $this->errors[] = $this->l('For security reasons, you cannot delete default order statuses.');
                    break;
                }
            }

            if (!count($this->errors)) {
                return parent::postProcess();
            }
        } else {
            return parent::postProcess();
        }
    }

    protected function filterToField($key, $filter)
    {
        if ($this->table == 'order_state') {
            $this->initOrderStatutsList();
        } elseif ($this->table == 'order_return_state') {
            $this->initOrdersReturnsList();
        }

        return parent::filterToField($key, $filter);
    }

    protected function afterImageUpload()
    {
        parent::afterImageUpload();

        if (($id_order_state = (int)Tools::getValue('id_order_state')) &&
             isset($_FILES) && count($_FILES) && file_exists(_PS_ORDER_STATE_IMG_DIR_.$id_order_state.'.gif')) {
            $current_file = _PS_TMP_IMG_DIR_.'order_state_mini_'.$id_order_state.'_'.$this->context->shop->id.'.gif';

            if (file_exists($current_file)) {
                unlink($current_file);
            }
        }

        return true;
    }

    public function ajaxProcessSendEmailOrderState()
    {
        $id_order_state = (int)Tools::getValue('id_order_state');

        $sql = 'UPDATE '._DB_PREFIX_.'order_state SET `send_email`= NOT `send_email` WHERE id_order_state='.$id_order_state;
        $result = Db::getInstance()->execute($sql);

        if ($result) {
            echo json_encode(array('success' => 1, 'text' => $this->l('The status has been updated successfully.')));
        } else {
            echo json_encode(array('success' => 0, 'text' => $this->l('An error occurred while updating this meta.')));
        }
    }

    public function ajaxProcessDeliveryOrderState()
    {
        $id_order_state = (int)Tools::getValue('id_order_state');

        $sql = 'UPDATE '._DB_PREFIX_.'order_state SET `delivery`= NOT `delivery` WHERE id_order_state='.$id_order_state;
        $result = Db::getInstance()->execute($sql);

        if ($result) {
            echo json_encode(array('success' => 1, 'text' => $this->l('The status has been updated successfully.')));
        } else {
            echo json_encode(array('success' => 0, 'text' => $this->l('An error occurred while updating this meta.')));
        }
    }

    public function ajaxProcessInvoiceOrderState()
    {
        $id_order_state = (int)Tools::getValue('id_order_state');

        $sql = 'UPDATE '._DB_PREFIX_.'order_state SET `invoice`= NOT `invoice` WHERE id_order_state='.$id_order_state;
        $result = Db::getInstance()->execute($sql);

        if ($result) {
            echo json_encode(array('success' => 1, 'text' => $this->l('The status has been updated successfully.')));
        } else {
            echo json_encode(array('success' => 0, 'text' => $this->l('An error occurred while updating this meta.')));
        }
    }
}
