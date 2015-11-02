<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class GuestTrackingControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'guest-tracking';

    /**
     * Initialize guest tracking controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        if ($this->context->customer->isLogged()) {
            Tools::redirect('history.php');
        }
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitGuestTracking') || Tools::isSubmit('submitTransformGuestToCustomer')) {
            // These lines are here for retrocompatibility with old theme
            $id_order = Tools::getValue('id_order');
            $order_collection = array();
            if ($id_order) {
                if (is_numeric($id_order)) {
                    $order = new Order((int)$id_order);
                    if (Validate::isLoadedObject($order)) {
                        $order_collection = Order::getByReference($order->reference);
                    }
                } else {
                    $order_collection = Order::getByReference($id_order);
                }
            }

            // Get order reference, ignore package reference (after the #, on the order reference)
            $order_reference = current(explode('#', Tools::getValue('order_reference')));
            // Ignore $result_number
            if (!empty($order_reference)) {
                $order_collection = Order::getByReference($order_reference);
            }

            $email = Tools::getValue('email');

            if (empty($order_reference) && empty($id_order)) {
                $this->errors[] = Tools::displayError('Please provide your order\'s reference number.');
            } elseif (empty($email)) {
                $this->errors[] = Tools::displayError('Please provide a valid email address.');
            } elseif (!Validate::isEmail($email)) {
                $this->errors[] = Tools::displayError('Please provide a valid email address.');
            } elseif (!Customer::customerExists($email, false, false)) {
                $this->errors[] = Tools::displayError('There is no account associated with this email address.');
            } elseif (Customer::customerExists($email, false, true)) {
                $this->errors[] = Tools::displayError('This page is for guest accounts only. Since your guest account has already been transformed into a customer account, you can no longer view your order here. Please log in to your customer account to view this order');
                $this->context->smarty->assign('show_login_link', true);
            } elseif (!count($order_collection)) {
                $this->errors[] = Tools::displayError('Invalid order reference');
            } elseif (!$order_collection->getFirst()->isAssociatedAtGuest($email)) {
                $this->errors[] = Tools::displayError('Invalid order reference');
            } else {
                $this->assignOrderTracking($order_collection);
                if (Tools::isSubmit('submitTransformGuestToCustomer')) {
                    $customer = new Customer((int)$order->id_customer);
                    if (!Validate::isLoadedObject($customer)) {
                        $this->errors[] = Tools::displayError('Invalid customer');
                    } elseif (!Tools::getValue('password')) {
                        $this->errors[] = Tools::displayError('Invalid password.');
                    } elseif (!$customer->transformToCustomer($this->context->language->id, Tools::getValue('password'))) {
                        // @todo clarify error message
                        $this->errors[] = Tools::displayError('An error occurred while transforming a guest into a registered customer.');
                    } else {
                        $this->context->smarty->assign('transformSuccess', true);
                    }
                }
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        /* Handle brute force attacks */
        if (count($this->errors)) {
            sleep(1);
        }

        $this->context->smarty->assign(array(
            'action' => $this->context->link->getPageLink('guest-tracking.php', true),
            'errors' => $this->errors,
        ));
        $this->setTemplate(_PS_THEME_DIR_.'guest-tracking.tpl');
    }

    /**
     * Assigns template vars related to order tracking information
     *
     * @param PrestaShopCollection $order_collection
     *
     * @throws PrestaShopException
     */
    protected function assignOrderTracking($order_collection)
    {
        $customer = new Customer((int)$order_collection->getFirst()->id_customer);

        $order_collection = ($order_collection->getAll());

        $order_list = array();
        foreach ($order_collection as $order) {
            $order_list[] = $order;
        }

        foreach ($order_list as &$order) {
            /** @var Order $order */
            $order->id_order_state = (int)$order->getCurrentState();
            $order->invoice = (OrderState::invoiceAvailable((int)$order->id_order_state) && $order->invoice_number);
            $order->order_history = $order->getHistory((int)$this->context->language->id, false, true);
            $order->carrier = new Carrier((int)$order->id_carrier, (int)$order->id_lang);
            $order->address_invoice = new Address((int)$order->id_address_invoice);
            $order->address_delivery = new Address((int)$order->id_address_delivery);
            $order->inv_adr_fields = AddressFormat::getOrderedAddressFields($order->address_invoice->id_country);
            $order->dlv_adr_fields = AddressFormat::getOrderedAddressFields($order->address_delivery->id_country);
            $order->invoiceAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($order->address_invoice, $order->inv_adr_fields);
            $order->deliveryAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($order->address_delivery, $order->dlv_adr_fields);
            $order->currency = new Currency($order->id_currency);
            $order->discounts = $order->getCartRules();
            $order->invoiceState = (Validate::isLoadedObject($order->address_invoice) && $order->address_invoice->id_state) ? new State((int)$order->address_invoice->id_state) : false;
            $order->deliveryState = (Validate::isLoadedObject($order->address_delivery) && $order->address_delivery->id_state) ? new State((int)$order->address_delivery->id_state) : false;
            $order->products = $order->getProducts();
            $order->customizedDatas = Product::getAllCustomizedDatas((int)$order->id_cart);
            Product::addCustomizationPrice($order->products, $order->customizedDatas);
            $order->total_old = $order->total_discounts > 0 ? (float)$order->total_paid - (float)$order->total_discounts : false;

            if ($order->carrier->url && $order->shipping_number) {
                $order->followup = str_replace('@', $order->shipping_number, $order->carrier->url);
            }
            $order->hook_orderdetaildisplayed = Hook::exec('displayOrderDetail', array('order' => $order));

            Hook::exec('actionOrderDetail', array('carrier' => $order->carrier, 'order' => $order));
        }

        $this->context->smarty->assign(array(
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'order_collection' => $order_list,
            'return_allowed' => false,
            'invoiceAllowed' => (int)Configuration::get('PS_INVOICE'),
            'is_guest' => true,
            'group_use_tax' => (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC),
            'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
            'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
            'use_tax' => Configuration::get('PS_TAX'),
            ));
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS(_THEME_CSS_DIR_.'history.css');
        $this->addCSS(_THEME_CSS_DIR_.'addresses.css');
    }

    protected function processAddressFormat(Address $delivery, Address $invoice)
    {
        $inv_adr_fields = AddressFormat::getOrderedAddressFields($invoice->id_country, false, true);
        $dlv_adr_fields = AddressFormat::getOrderedAddressFields($delivery->id_country, false, true);

        $this->context->smarty->assign('inv_adr_fields', $inv_adr_fields);
        $this->context->smarty->assign('dlv_adr_fields', $dlv_adr_fields);
    }
}
