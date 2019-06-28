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
 * @property OrderInvoice $object
 */
class AdminOutstandingControllerCore  extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_invoice';
        $this->className = 'OrderInvoice';
        $this->addRowAction('view');

        $this->context = Context::getContext();

        $this->_select = '`id_order_invoice` AS `id_invoice`,
		`id_order_invoice` AS `outstanding`,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		c.`outstanding_allow_amount`,
		r.`color`,
		rl.`name` AS `risk`';
        $this->_join = 'LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = a.`id_order`)
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
		LEFT JOIN `'._DB_PREFIX_.'risk` r ON (r.`id_risk` = c.`id_risk`)
		LEFT JOIN `'._DB_PREFIX_.'risk_lang` rl ON (r.`id_risk` = rl.`id_risk` AND rl.`id_lang` = '.(int)$this->context->language->id.')';
        $this->_where = 'AND number > 0';
        $this->_use_found_rows = false;

        $risks = array();
        foreach (Risk::getRisks() as $risk) {
            /** @var Risk $risk */
            $risks[$risk->id] = $risk->name;
        }

        $this->fields_list = array(
            'number' => array(
                'title' => $this->l('Invoice')
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'type' => 'date',
                'align' => 'right',
                'filter_key' => 'a!date_add'
            ),
            'customer' => array(
                'title' => $this->l('Customer'),
                'filter_key' => 'customer',
                'tmpTableFilter' => true
            ),
            'company' => array(
                'title' => $this->l('Company'),
                'align' => 'center'
            ),
            'risk' => array(
                'title' => $this->l('Risk'),
                'align' => 'center',
                'orderby' => false,
                'type' => 'select',
                'color' => 'color',
                'list' => $risks,
                'filter_key' => 'r!id_risk',
                'filter_type' => 'int'
            ),
            'outstanding_allow_amount' => array(
                'title' => $this->l('Outstanding Allowance'),
                'align' => 'center',
                'prefix' => '<b>',
                'suffix' => '</b>',
                'type' => 'price'
            ),
            'outstanding' => array(
                'title' => $this->l('Current Outstanding'),
                'align' => 'center',
                'callback' => 'printOutstandingCalculation',
                'orderby' => false,
                'search' => false
            ),
            'id_invoice' => array(
                'title' => $this->l('Invoice'),
                'align' => 'center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false
            )
        );

        parent::__construct();
    }

    /**
     * Toolbar initialisation
     * @return bool Force true (Hide New button)
     */
    public function initToolbar()
    {
        return true;
    }

    /**
     * Column callback for print PDF incon
     * @param $id_invoice integer Invoice ID
     * @param $tr array Row data
     * @return string HTML content
     */
    public function printPDFIcons($id_invoice, $tr)
    {
        $this->context->smarty->assign(array(
            'id_invoice' => $id_invoice
        ));

        return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
    }

    public function printOutstandingCalculation($id_invoice, $tr)
    {
        $order_invoice = new OrderInvoice($id_invoice);
        if (!Validate::isLoadedObject($order_invoice)) {
            throw new PrestaShopException('object OrderInvoice cannot be loaded');
        }
        $order = new Order($order_invoice->id_order);
        if (!Validate::isLoadedObject($order)) {
            throw new PrestaShopException('object Order cannot be loaded');
        }
        $customer = new Customer((int)$order->id_customer);
        if (!Validate::isLoadedObject($order_invoice)) {
            throw new PrestaShopException('object Customer cannot be loaded');
        }

        return '<b>'.Tools::displayPrice($customer->getOutstanding(), Context::getContext()->currency).'</b>';
    }

    /**
     * View render
     * @throws PrestaShopException Invalid objects
     */
    public function renderView()
    {
        $order_invoice = new OrderInvoice((int)Tools::getValue('id_order_invoice'));
        if (!Validate::isLoadedObject($order_invoice)) {
            throw new PrestaShopException('object OrderInvoice cannot be loaded');
        }
        $order = new Order($order_invoice->id_order);
        if (!Validate::isLoadedObject($order)) {
            throw new PrestaShopException('object Order cannot be loaded');
        }

        $link = $this->context->link->getAdminLink('AdminOrders');
        $link .= '&vieworder&id_order='.$order->id;
        $this->redirect_after = $link;
        $this->redirect();
    }
}
