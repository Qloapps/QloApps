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
 * @property OrderReturn $object
 */
class AdminReturnControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'order_return';
        $this->className = 'OrderReturn';
        $this->colorOnBackground = true;
        $this->_select = 'ors.color, orsl.`name`, o.`id_shop`';
        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'order_return_state ors ON (ors.`id_order_return_state` = a.`state`)';
        $this->_join .= 'LEFT JOIN '._DB_PREFIX_.'order_return_state_lang orsl ON (orsl.`id_order_return_state` = a.`state` AND orsl.`id_lang` = '.(int)$this->context->language->id.')';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'orders o ON (o.`id_order` = a.`id_order`)';

        $this->fields_list = array(
            'id_order_return' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
            'id_order' => array('title' => $this->l('Order ID'), 'width' => 100, 'align' => 'center', 'filter_key'=>'a!id_order'),
            'name' => array('title' => $this->l('Status'),'color' => 'color', 'width' => 'auto', 'align' => 'left'),
            'date_add' => array('title' => $this->l('Date issued'), 'width' => 150, 'type' => 'date', 'align' => 'right', 'filter_key'=>'a!date_add'),
        );

        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->l('Merchandise return (RMA) options'),
                'fields' =>    array(
                    'PS_ORDER_RETURN' => array(
                        'title' => $this->l('Enable returns'),
                        'desc' => $this->l('Would you like to allow merchandise returns in your shop?'),
                        'cast' => 'intval', 'type' => 'bool'),
                    'PS_ORDER_RETURN_NB_DAYS' => array(
                        'title' => $this->l('Time limit of validity'),
                        'desc' => $this->l('How many days after the delivery date does the customer have to return a product?'),
                        'cast' => 'intval',
                        'type' => 'text',
                        'size' => '2'),
                    'PS_RETURN_PREFIX' => array(
                        'title' => $this->l('Returns prefix'),
                        'desc' => $this->l('Prefix used for return name (e.g. RE00001).'),
                        'size' => 6,
                        'type' => 'textLang'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
        );

        parent::__construct();

        $this->_where = Shop::addSqlRestriction(false, 'o');
        $this->_use_found_rows = false;
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Return Merchandise Authorization (RMA)'),
                'image' => '../img/admin/return.gif'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_order'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_customer'
                ),
                array(
                    'type' => 'text_customer',
                    'label' => $this->l('Customer'),
                    'name' => '',
                    'size' => '',
                    'required' => false,
                ),
                array(
                    'type' => 'text_order',
                    'label' => $this->l('Order'),
                    'name' => '',
                    'size' => '',
                    'required' => false,
                ),
                array(
                    'type' => 'free',
                    'label' => $this->l('Customer explanation'),
                    'name' => 'question',
                    'size' => '',
                    'required' => false,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Status'),
                    'name' => 'state',
                    'required' => false,
                    'options' => array(
                        'query' => OrderReturnState::getOrderReturnStates($this->context->language->id),
                        'id' => 'id_order_return_state',
                        'name' => 'name'
                    ),
                    'desc' => $this->l('Merchandise return (RMA) status.')
                ),
                array(
                    'type' => 'list_products',
                    'label' => $this->l('Products'),
                    'name' => '',
                    'size' => '',
                    'required' => false,
                    'desc' => $this->l('List of products in return package.')
                ),
                array(
                    'type' => 'pdf_order_return',
                    'label' => $this->l('Return slip'),
                    'name' => '',
                    'size' => '',
                    'required' => false,
                    'desc' => $this->l('The link is only available after validation and before the parcel gets delivered.')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        $order = new Order($this->object->id_order);
        $quantity_displayed = array();
        // Customized products */
        if ($returned_customizations = OrderReturn::getReturnedCustomizedProducts((int)($this->object->id_order))) {
            foreach ($returned_customizations as $returned_customization) {
                $quantity_displayed[(int)$returned_customization['id_order_detail']] = isset($quantity_displayed[(int)$returned_customization['id_order_detail']]) ? $quantity_displayed[(int)$returned_customization['id_order_detail']] + (int)$returned_customization['product_quantity'] : (int)$returned_customization['product_quantity'];
            }
        }

        // Classic products
        $products = OrderReturn::getOrdersReturnProducts($this->object->id, $order);

        // Prepare customer explanation for display
        $this->object->question = '<span class="normal-text">'.nl2br($this->object->question).'</span>';

        $this->tpl_form_vars = array(
            'customer' => new Customer($this->object->id_customer),
            'url_customer' => 'index.php?tab=AdminCustomers&id_customer='.(int)$this->object->id_customer.'&viewcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id),
            'text_order' => sprintf($this->l('Order #%1$d from %2$s'), $order->id, Tools::displayDate($order->date_upd)),
            'url_order' => 'index.php?tab=AdminOrders&id_order='.(int)$order->id.'&vieworder&token='.Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$this->context->employee->id),
            'picture_folder' => _THEME_PROD_PIC_DIR_,
            'returnedCustomizations' => $returned_customizations,
            'customizedDatas' => Product::getAllCustomizedDatas((int)($order->id_cart)),
            'products' => $products,
            'quantityDisplayed' => $quantity_displayed,
            'id_order_return' => $this->object->id,
            'state_order_return' => $this->object->state,
        );

        return parent::renderForm();
    }

    public function initToolbar()
    {
        // If display list, we don't want the "add" button
        if (!$this->display || $this->display == 'list') {
            return;
        } elseif ($this->display != 'options') {
            $this->toolbar_btn['save-and-stay'] = array(
                'short' => 'SaveAndStay',
                'href' => '#',
                'desc' => $this->l('Save and stay'),
                'force_desc' => true,
            );
        }

        parent::initToolbar();
    }

    public function postProcess()
    {
        $this->context = Context::getContext();
        if (Tools::isSubmit('deleteorder_return_detail')) {
            if ($this->tabAccess['delete'] === '1') {
                if (($id_order_detail = (int)(Tools::getValue('id_order_detail'))) && Validate::isUnsignedId($id_order_detail)) {
                    if (($id_order_return = (int)(Tools::getValue('id_order_return'))) && Validate::isUnsignedId($id_order_return)) {
                        $orderReturn = new OrderReturn($id_order_return);
                        if (!Validate::isLoadedObject($orderReturn)) {
                            die(Tools::displayError());
                        }
                        if ((int)($orderReturn->countProduct()) > 1) {
                            if (OrderReturn::deleteOrderReturnDetail($id_order_return, $id_order_detail, (int)(Tools::getValue('id_customization', 0)))) {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=4token='.$this->token);
                            } else {
                                $this->errors[] = Tools::displayError('An error occurred while deleting the details of your order return.');
                            }
                        } else {
                            $this->errors[] = Tools::displayError('You need at least one product.');
                        }
                    } else {
                        $this->errors[] = Tools::displayError('The order return is invalid.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('The order return content is invalid.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        } elseif (Tools::isSubmit('submitAddorder_return') || Tools::isSubmit('submitAddorder_returnAndStay')) {
            if ($this->tabAccess['edit'] === '1') {
                if (($id_order_return = (int)(Tools::getValue('id_order_return'))) && Validate::isUnsignedId($id_order_return)) {
                    $orderReturn = new OrderReturn($id_order_return);
                    $order = new Order($orderReturn->id_order);
                    $customer = new Customer($orderReturn->id_customer);
                    $orderReturn->state = (int)(Tools::getValue('state'));
                    if ($orderReturn->save()) {
                        $orderReturnState = new OrderReturnState($orderReturn->state);
                        $vars = array(
                        '{lastname}' => $customer->lastname,
                        '{firstname}' => $customer->firstname,
                        '{id_order_return}' => $id_order_return,
                        '{state_order_return}' => (isset($orderReturnState->name[(int)$order->id_lang]) ? $orderReturnState->name[(int)$order->id_lang] : $orderReturnState->name[(int)Configuration::get('PS_LANG_DEFAULT')]));
                        Mail::Send((int)$order->id_lang, 'order_return_state', Mail::l('Your order return status has changed', $order->id_lang),
                            $vars, $customer->email, $customer->firstname.' '.$customer->lastname, null, null, null,
                            null, _PS_MAIL_DIR_, true, (int)$order->id_shop);

                        if (Tools::isSubmit('submitAddorder_returnAndStay')) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token.'&updateorder_return&id_order_return='.(int)$id_order_return);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                        }
                    }
                } else {
                    $this->errors[] = Tools::displayError('No order return ID has been specified.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        parent::postProcess();
    }
}
