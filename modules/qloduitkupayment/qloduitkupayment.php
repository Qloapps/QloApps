<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
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
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once 'classes/QdpDuitkuRequiredClasses.php';

class QloDuitkuPayment extends PaymentModule
{
    public $debugging = true;
    public $logger;

    public function __construct()
    {
        $this->name = 'qloduitkupayment';
        $this->tab = 'payments_gateways';
        $this->author = 'Duitku';
        $this->version = '4.0.0';
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->payment_type = OrderPayment::PAYMENT_TYPE_ONLINE;
        $this->qloapps_versions_compliancy = array('min' => '1.4', 'max' => _QLOAPPS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('QloApps Duitku Payment');
        $this->description = $this->l('This module allows guests to pay using Duitku payment gateway.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->setLogger();
    }

    public function install()
    {
        $objDuitkuPaymentDb = new QdpDuitkuPaymentDb();
        if (
            !parent::install()
            || !$this->callInstallTab()
            || !$this->registerHooks()
            || !$objDuitkuPaymentDb->createTable()
        ) {
            return false;
        }

        return true;
    }

    public function callInstallTab()
    {
        $this->installTab('AdminDuitkuTransaction', 'Duitku Transactions');

        return true;
    }

    public function registerHooks()
    {
        return $this->registerHook(
            array(
                'displayBackOfficeHeader',
                'actionFrontControllerSetMedia',
                'actionAdminControllerSetMedia',
                'displayPayment',
                'displayPaymentReturn'
            )
        );
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->local_path . 'views/css/admin/duitku.css');
    }

    public function installTab($className, $tabName, $tabParentName = False, $hiddenTab = False)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        if ($hiddenTab) {
            $tab->id_parent = -1;
        } elseif ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitDuitkufigForm')) {
            if ($this->validateConfigurationValues()) {
                $this->saveConfigurationValues();
                Configuration::updateValue('QDP_DUITKU_PAYMENT_CONFIGURED', 1);
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&conf=6&configure=' . $this->name);
            }
        }

        return $this->renderForm();
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDuitkufigForm';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormFieldsValue(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigFormFields()));
    }


    public function getConfigFormFields()
    {
        $cashfreeEnvironmentOptions = array(
            array(
                'id' => QdpDuitkuTransaction::QDP_DUITKU_ENVIRONMENT_SANDBOX,
                'value' => $this->l('Sandbox')
            ),
            array(
                'id' => QdpDuitkuTransaction::QDP_DUITKU_ENVIRONMENT_PRODUCTION,
                'value' => $this->l('Production')
            ),
        );

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Duitku Configuration'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Environment'),
                        'hint' => $this->l('Select current environment to use for Cashfree transaction. Select Sanbox to test configurations and production to accept payment live.'),
                        'name' => 'QDP_DUITKU_PAYMENT_ENVIRONMENT',
                        'class' => 'input fixed-width-lg',
                        'options' => array(
                            'query' => $cashfreeEnvironmentOptions,
                            'id' => 'id',
                            'name' => 'value',
                        ),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'QDP_DUITKU_PAYMENT_API_KEY',
                        'label' => $this->l('Api Key'),
                        'hint' => $this->l('Enter Api Key obtained from Duitku.'),
                        'required' => true,
                        'col' => '4',
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'QDP_DUITKU_PAYMENT_MERCHANT_CODE',
                        'label' => $this->l('Merchant Code'),
                        'hint' => $this->l('Enter merchant code obtained from Duitku.'),
                        'required' => true,
                        'col' => '4',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Expiry Period'),
                        'name' => 'QDP_DUITKU_PAYMENT_EXPIRYPERIOD',
                        'required' => true,
                        'col' => '4',
                        'hint' => $this->l('Number in minutes. Range 10 to 180.'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }


    public function getConfigFormFieldsValue()
    {
        $configKeys = array(
            'QDP_DUITKU_PAYMENT_API_KEY',
            'QDP_DUITKU_PAYMENT_MERCHANT_CODE',
            'QDP_DUITKU_PAYMENT_ENVIRONMENT',
            'QDP_DUITKU_PAYMENT_EXPIRYPERIOD'
        );
        $fieldsValue = array();
        foreach ($configKeys as $key) {
            $fieldsValue[$key] = Configuration::get($key) ? Configuration::get($key) : Tools::getValue($key);
        }

        return $fieldsValue;
    }


    public function validateConfigurationValues()
    {
        $expiryPeriod = Tools::getValue('QDP_DUITKU_PAYMENT_EXPIRYPERIOD');
        if (empty(trim(Tools::getValue('QDP_DUITKU_PAYMENT_API_KEY')))) {
            $this->context->controller->errors[] = $this->l('Please enter your Api Key.');
        }

        if (empty(trim(Tools::getValue('QDP_DUITKU_PAYMENT_MERCHANT_CODE')))) {
            $this->context->controller->errors[] = $this->l('Please enter your Merchant Code.');
        }

        if (!Validate::isCleanHtml(Tools::getValue('QDP_DUITKU_PAYMENT_API_KEY'))) {
            $this->context->controller->errors[] = $this->l('Please enter valid Api Key.');
        }

        if (!Validate::isCleanHtml(Tools::getValue('QDP_DUITKU_PAYMENT_MERCHANT_CODE'))) {
            $this->context->controller->errors[] = $this->l('Please enter a valid Merchant Code.');
        }

        if (!$expiryPeriod || !Validate::isInt($expiryPeriod) || $expiryPeriod < 10 || $expiryPeriod > 180) {
            $this->context->controller->errors[] = $this->l('Invalid expiry period value. The value should be in number between 10 to 180. It count in minutes.');
        }

        if (empty($this->context->controller->errors)) {
            return true;
        }

        return false;
    }

    public function saveConfigurationValues()
    {
        $configKeys = array(
            'QDP_DUITKU_PAYMENT_API_KEY',
            'QDP_DUITKU_PAYMENT_MERCHANT_CODE',
            'QDP_DUITKU_PAYMENT_ENVIRONMENT',
            'QDP_DUITKU_PAYMENT_EXPIRYPERIOD'
        );
        foreach ($configKeys as $key) {
            $value = Tools::getValue($key);
            Configuration::updateValue($key, Tools::getValue($key));
        }
        return true;
    }



    public function hookDisplayPayment($params)
    {
        if (Configuration::get('QDP_DUITKU_PAYMENT_CONFIGURED')) {

            if (!$this->checkCurrency($params['cart'])) {
                return;
            }

            $tplVars['duitkuunavailable'] = 0;
            $this->context->smarty->assign($tplVars);

            return $this->display(__FILE__, 'payment_web_checkout.tpl');
        }
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency'] && $currency_order->iso_code == "IDR") {
                    return true;
                }
            }
        }
        return false;
    }

    public function hookDisplayPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $objOrder = $params['objOrder'];
        $idCart = $objOrder->id_cart;
        $state = $objOrder->getCurrentState();
        if (
            $state == Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED')
            || $state == Configuration::get('PS_OS_PAYMENT_ACCEPTED')
            || $state == Configuration::get('PS_OS_AWAITING_PAYMENT')
        ) {
            $success = Tools::getValue('success');

            $objCart = new Cart((int) $idCart);

            $objCustomer = new Customer((int) $objCart->id_customer);
            $id_currency = $objCart->id_currency;
            if ($objCart->is_advance_payment) {
                $cartTotalAmount = $objCart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
            } else {
                $cartTotalAmount = $objCart->getOrderTotal(true, Cart::BOTH);
            }

            $cartAmount = round($cartTotalAmount, 2);

            $this->smarty->assign(array(
                'total_paid' => Tools::displayPrice($cartAmount, $params['currencyObj'], false),
                'status' => $success,
                'id_order' => $objOrder->id,
            ));
        } else {
            $this->smarty->assign('status', 'failed');
        }

        return $this->display(__FILE__, 'payment_return.tpl');
    }


    public function hookActionFrontControllerSetMedia()
    {
        if ((Tools::getValue('controller') == 'orderopc') || Tools::getValue('controller') == 'quick-order') {
            if (Tools::getValue('duitku_err')) {
                $this->context->controller->errors[] = $this->l(
                    'Some error occurred while payment with Duitku  gateway. Please try again or try another payment method.'
                );
            }

            Media::addJsDef(
                array(
                    'qdp_duitku_validate_url' => $this->context->link->getModuleLink('qloduitkupayment', 'validate')
                )
            );
            $this->context->controller->addCSS($this->_path . 'views/css/front/duitku_payment.css');
        }
    }

    public function hookActionAdminControllerSetMedia()
    {

        if (
            Tools::getValue('controller') == 'AdminModules'
            && Tools::getValue('configure') == 'qloduitkupayment'
        ) {
            $this->context->controller->addJs($this->_path . 'views/js/duitku_configuration.js');
        }
    }

    public function getDuitkuTransactionTokenResponse()
    {
        $cart = new Cart((int) $this->context->cart->id);
        // Validate and remove non-selected services from the cart
        ServiceProductCartDetail::validateServiceProductsInCart();

        $cartTotalAmount = $cart->is_advance_payment
            ? $cart->getOrderTotal(true, Cart::ADVANCE_PAYMENT)
            : $cart->getOrderTotal(true, Cart::BOTH);
        $paymentAmount = (int) round($cartTotalAmount);
        $merchantOrderId = QdpDuitkuTransaction::QDP_ORDER_ID_PREFIX . time() . '_' . $cart->id;

        $customer = new Customer($cart->id_customer);
        $email = $customer->email;
        $phone = $this->getCustomerPhone($customer, $cart->id_address_delivery);

        $callbackUrl = $this->context->link->getModuleLink('qloduitkupayment', 'callback');
        $returnUrl = $this->context->link->getModuleLink('qloduitkupayment', 'return');
        $expiryPeriod = (int) Configuration::get('QDP_DUITKU_PAYMENT_EXPIRYPERIOD');

        $customerVaName = $customer->firstname . ' ' . $customer->lastname;
        $productDetails = "Payment for " . Configuration::get('PS_SHOP_NAME');

        $itemDetails = array(
            array(
                'name'     => $productDetails,
                'price'    => $paymentAmount,
                'quantity' => 1
            )
        );

        $customerDetail = array(
            'firstName'   => $customer->firstname,
            'lastName'    => $customer->lastname,
            'email'       => $email,
            'phoneNumber' => $phone,
        );

        $params = array(
            'paymentAmount'   => $paymentAmount,
            'merchantOrderId' => $merchantOrderId,
            'productDetails'  => $productDetails,
            'customerDetail'  => $customerDetail,
            'itemDetails'     => $itemDetails,
            'customerVaName'  => $customerVaName,
            'email'           => $email,
            'phoneNumber'     => $phone,
            'callbackUrl'     => $callbackUrl,
            'returnUrl'       => $returnUrl,
            'expiryPeriod'    => $expiryPeriod
        );

        return QdpDuitkuServiceRequest::makeRequest(
            'createInvoice',
            QdpDuitkuTransaction::QDP_CURL_REQUEST_TYPE_POST,
            $params
        );
    }

    private function getCustomerPhone($customer, $addressId)
    {
        if (!empty($customer->phone)) {
            return $customer->phone;
        }

        $address = new Address($addressId);

        if (!empty($address->phone)) {
            return $address->phone;
        }

        return $address->phone_mobile ? $address->phone_mobile : '';
    }



    protected function setLogger()
    {
        if (isset($this->debugging) && $this->debugging) {
            $this->logger = new FileLogger(FileLogger::DEBUG);
        } else {
            $this->logger = new FileLogger(FileLogger::WARNING);
        }

        $this->logger->setFilename($this->local_path . 'logs/' . date('Ymd') . '.log');
    }

    public function uninstall()
    {
        $objDuitkuPaymentDb = new QdpDuitkuPaymentDb();
        if (
            !parent::uninstall()
            || !$objDuitkuPaymentDb->deleteTable()
            || !$this->uninstallTab()
            || !$this->deleteDuitkuConfigKeys()
        ) {
            return false;
        }

        return true;
    }

    public function deleteDuitkuConfigKeys()
    {
        $configKeys = array(
            'QDP_DUITKU_PAYMENT_API_KEY',
            'QDP_DUITKU_PAYMENT_MERCHANT_CODE',
            'QDP_DUITKU_PAYMENT_ENVIRONMENT',
            'QDP_DUITKU_PAYMENT_EXPIRYPERIOD'
        );
        foreach ($configKeys as $key) {
            Configuration::deleteByName($key);
        }

        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }
}
