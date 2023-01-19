<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/classes/WkClassInclude.php';

class QloPaypalCommerce extends PaymentModule
{
    private $html = '';
    private $postErrors = array();
    private $token = '';

    public $ppMode;
    public $merchantId;
    public $paypalEmail;
    public $clientId;
    public $clientSecret;

    public function __construct()
    {
        $this->name = 'qlopaypalcommerce';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.2';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->html = '';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');

        $this->displayName = $this->l('QloApps PayPal Checkout');

        $this->description = '<b>'.$this->l('An instant global business.').'</b><br>';

        $this->description .= $this->l('One integration for all your online payment needs.');

        $this->description .= '<br><b>'.$this->l('Benefits').'</b>';

        $this->description .= '<ul>';

        $this->description .= '<li>'.$this->l('Enable a seamless buying experience for your customers that drives conversion and loyalty.').'</li>';
        $this->description .= '<li>'.$this->l('Accept PayPal payments with simplified onboarding, adaptable integration and easy account setup.').'</li>';
        $this->description .= '<li>'.$this->l('Access to 377M+ PayPal customers around the globe*, with local currency support for better money management.').'</li>';
        $this->description .= '<li>'.$this->l('Peace of mind for you and your customers with buyer and seller protection on eligible sales.').'</li>';

        $this->description .= '</ul>';

        $this->description .= '*'.$this->l('PayPal Fourth Quarters 2020 Result');

        $config = Configuration::getMultiple(array(
            'WK_PAYPAL_COMMERCE_PAYMENT_MODE',
            'WK_PAYPAL_COMMERCE_MERCHANT_ID',
            'WK_PAYPAL_COMMERCE_EMAIL',
            'WK_PAYPAL_COMMERCE_CLIENT_ID',
            'WK_PAYPAL_COMMERCE_CLIENT_SECRET'
        ));

        if (!empty($config['WK_PAYPAL_COMMERCE_PAYMENT_MODE'])) {
            $this->ppMode = $config['WK_PAYPAL_COMMERCE_PAYMENT_MODE'];
        }
        if (!empty($config['WK_PAYPAL_COMMERCE_MERCHANT_ID'])) {
            $this->merchantId = $config['WK_PAYPAL_COMMERCE_MERCHANT_ID'];
        }
        if (!empty($config['WK_PAYPAL_COMMERCE_EMAIL'])) {
            $this->paypalEmail = $config['WK_PAYPAL_COMMERCE_EMAIL'];
        }
        if (!empty($config['WK_PAYPAL_COMMERCE_CLIENT_ID'])) {
            $this->clientId = $config['WK_PAYPAL_COMMERCE_CLIENT_ID'];
        }
        if (!empty($config['WK_PAYPAL_COMMERCE_CLIENT_SECRET'])) {
            $this->clientSecret = $config['WK_PAYPAL_COMMERCE_CLIENT_SECRET'];
        }

        parent::__construct();

        $this->payment_type = OrderPayment::PAYMENT_TYPE_ONLINE;
    }

    public function getContent()
    {
        if (!$this->checkPaypalCommerceConfigured()) {
            $this->context->controller->warnings[] = $this->l('PayPal Merchant ID, Email, Client ID and Secret must be configured.');
        }

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->context->controller->warnings[] = $this->l('No currency has been set for this module.');
        }

        if (Tools::isSubmit('submit_paypal_commerce')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
            } else {
                $this->html .= $this->displayError($this->postErrors);
            }
        } else {
            $this->html .= '<br />';
        }

        $this->context->smarty->assign(
            array (
                'link' => $this->context->link,
                'secret_key' => $this->secure_key,
            )
        );

        $this->html .= $this->renderForm();

        return $this->html;
    }

    public function renderForm()
    {
        $fields_form = array();
        $fields_form['form'] = array(
            'legend' => array(
                'icon' => 'icon-cog',
                'title' => $this->l('PayPal Payment Configuration'),
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'required' => true,
                    'label' => $this->l('Transaction Environment'),
                    'name' => 'WK_PAYPAL_COMMERCE_PAYMENT_MODE',
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 'sandbox',
                                'name' => $this->l('Sandbox'),
                            ),
                            array(
                                'id' => 'production',
                                'name' => $this->l('Production'),
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'hint' => $this->l('Select PayPal payment environment either sandbox or production. You can get real transaction only on production, for testing, use sandbox.'),
                ),
                array(
                    'label' => $this->l('Merchant ID'),
                    'name' => 'WK_PAYPAL_COMMERCE_MERCHANT_ID',
                    'size' => 60,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the Merchant ID of your PayPal account.'),
                ),
                array(
                    'label' => $this->l('Account Email'),
                    'name' => 'WK_PAYPAL_COMMERCE_EMAIL',
                    'size' => 60,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the PayPal Account Email of your PayPal account.'),
                ),
                array(
                    'label' => $this->l('Client ID'),
                    'name' => 'WK_PAYPAL_COMMERCE_CLIENT_ID',
                    'size' => 100,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the Client ID from PayPal account app credentials.'),
                ),
                array(
                    'label' => $this->l('Client Secret'),
                    'name' => 'WK_PAYPAL_COMMERCE_CLIENT_SECRET',
                    'size' => 100,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the Client Secret from PayPal account app credentials.'),
                ),
            ),
            'description' => $this->l('PayPal Seller Protection not applicable'),
            'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submit_paypal_commerce',
                ),
            );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnConfigSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).
        '&configure='.$this->name.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $objModuleDb = new WkPaypalCommerceDb();
        $helper->tpl_vars = array(
            'fields_value' => $objModuleDb->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    private function postValidation()
    {
        if (Tools::isSubmit('submit_paypal_commerce')) {
            $wkMerchantId = trim(Tools::getValue('WK_PAYPAL_COMMERCE_MERCHANT_ID'));
            if (!$wkMerchantId) {
                $this->postErrors[] = $this->l('Please enter Merchant ID');
            } elseif (!preg_match(Tools::cleanNonUnicodeSupport('/[a-zA-Z0-9]+/'), $wkMerchantId)) {
                $this->postErrors[] = $this->l('Invalid Merchant ID provided.');
            }
            $wkEmail = trim(Tools::getValue('WK_PAYPAL_COMMERCE_EMAIL'));
            if (!$wkEmail) {
                $this->postErrors[] = $this->l('Please enter Account Email');
            } elseif (!Validate::isEmail($wkEmail)) {
                $this->postErrors[] = $this->l('Please enter valid Account Email');
            }
            $wkClientID = trim(Tools::getValue('WK_PAYPAL_COMMERCE_CLIENT_ID'));
            if (!$wkClientID) {
                $this->postErrors[] = $this->l('Please enter Client ID');
            }
            $wkClientSecret = trim(Tools::getValue('WK_PAYPAL_COMMERCE_CLIENT_SECRET'));
            if (!$wkClientSecret) {
                $this->postErrors[] = $this->l('Please enter Client Secret');
            }

            // Validate PayPal credentials
            $this->validatePaypalCredentials();

            if (Tools::getValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE') == 'sandbox'
                && $this->token
            ) {
                if (empty(Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_WEBHOOK_ID'))) {
                    // Create webhook URL first time
                    $this->createWebhookUrl('sandbox');
                } elseif (Tools::getValue('WK_PAYPAL_COMMERCE_CLIENT_ID') != Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID')) {
                    // Delete existing webhook URL if PayPal credential changed
                    WkPaypalCommerceHelper::deleteWebhookUrl();
                    $this->createWebhookUrl('sandbox');
                }
            } elseif (Tools::getValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE') == 'production'
                && $this->token
            ) {
                if (empty(Configuration::get('WK_PAYPAL_COMMERCE_LIVE_WEBHOOK_ID'))) {
                    // Create webhook URL first time
                    $this->createWebhookUrl('production');
                } elseif (Tools::getValue('WK_PAYPAL_COMMERCE_CLIENT_ID') != Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID')) {
                    // Delete existing webhook URL if PayPal credential changed
                    WkPaypalCommerceHelper::deleteWebhookUrl();
                    $this->createWebhookUrl('production');
                }
            }
        }
    }

    private function validatePaypalCredentials()
    {
        if (!$this->postErrors) {
            if ($response = WkPaypalCommerceHelper::getAccessToken()) {
                if ($response['success']) {
                    $this->token = $response['access_token'];
                } else {
                    $this->postErrors[] = $response['message'];
                }
            }
        }
    }

    private function createWebhookUrl($env)
    {
        if ($this->token) {
            if ($response = WkPaypalCommerceHelper::createWebhookUrl($this->token)) {
                if ($response['success']) {
                    if ($env == 'sandbox') {
                        Configuration::updateValue('WK_PAYPAL_COMMERCE_SANDBOX_WEBHOOK_ID', $response['webhook_id']);
                    } elseif ($env == 'production') {
                        Configuration::updateValue('WK_PAYPAL_COMMERCE_LIVE_WEBHOOK_ID', $response['webhook_id']);
                    }
                } else {
                    $this->postErrors[] = $response['message'];
                }
            }
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('btnConfigSubmit')) {
            Configuration::updateValue('WK_PAYPAL_COMMERCE_MERCHANT_ID', trim(Tools::getValue('WK_PAYPAL_COMMERCE_MERCHANT_ID')));
            Configuration::updateValue('WK_PAYPAL_COMMERCE_EMAIL', trim(Tools::getValue('WK_PAYPAL_COMMERCE_EMAIL')));
            Configuration::updateValue('WK_PAYPAL_COMMERCE_CLIENT_ID', trim(Tools::getValue('WK_PAYPAL_COMMERCE_CLIENT_ID')));
            Configuration::updateValue('WK_PAYPAL_COMMERCE_CLIENT_SECRET', trim(Tools::getValue('WK_PAYPAL_COMMERCE_CLIENT_SECRET')));
            Configuration::updateValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE', Tools::getValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE'));

            $moduleConfig = $this->context->link->getAdminLink('AdminModules');
            Tools::redirectAdmin(
                $moduleConfig.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4'
            );

            // redirect after saving the configuration
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.
                '&module_name='.$this->name.'&conf=4'
            );
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        // css for razorpay menu will be applicable for all pages
        $this->context->controller->addCSS($this->_path.'views/css/admin/wk_module_menu.css');
    }

    public function hookDisplayTopColumn()
    {
        if ('order' === $this->context->controller->php_self
            || 'order-opc' === $this->context->controller->php_self
        ) {
            if (Tools::getValue('pp_cancel')) {
                return $this->display(__FILE__, 'payment_cancel_ack.tpl');
            }
        }
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        if ('order' === $this->context->controller->php_self
            || 'order-opc' === $this->context->controller->php_self
        ) {
            if ($this->checkPaypalAvailability()) {
                Media::addJsDef(
                    array(
                        'paymentUrl' => $this->context->link->getModuleLink('qlopaypalcommerce', 'payment'),
                        'pp_environment' => Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE'),
                        'create_order' => $this->context->link->getModuleLink(
                            $this->name,
                            'payment',
                            array('action' => 1, 'token' => $this->secure_key),
                            true
                        ),
                        'capture_order' => $this->context->link->getModuleLink(
                            $this->name,
                            'payment',
                            array('action' => 2, 'token' => $this->secure_key),
                            true
                        ),
                        'cancel_order' => $this->context->link->getModuleLink(
                            $this->name,
                            'payment',
                            array('action' => 3, 'token' => $this->secure_key),
                            true
                        ),
                        'error_order' => $this->context->link->getModuleLink(
                            $this->name,
                            'errorpayment'
                        ),
                    )
                );

                $currency = Currency::getCurrency((int)$this->context->currency->id);

                // // add PayPal script
                $this->context->controller->addJS('https://www.paypal.com/sdk/js?client-id='.Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID').'&commit=true&components=buttons&debug=false&currency='.$currency['iso_code'].'&intent=capture');

                $this->context->controller->addCSS($this->_path.'views/css/front/wk_payment.css');

                if (Tools::getValue('pp_cancel')) {
                    $this->context->controller->addCSS($this->_path.'views/css/front/wk_payment_cancel.css');
                }
            }
        }
    }

    public function hookDisplayPayment($params)
    {
        if (!$this->checkPaypalAvailability()) {
            return;
        }

        return $this->display(__FILE__, 'payment.tpl');
    }

    public function checkPaypalAvailability()
    {
        if ($this->active
            && $this->checkCurrency($this->context->cart)
            && $this->checkPaypalCommerceConfigured()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check cart currency
     * @param  mixed $cart
     * @return void
     */
    public function checkCurrency($cart)
    {
        // check if currency of the cart is supported by the customer or not
        if (!WkPaypalCommerceHelper::checkPaypalCurrencySuuport($cart->id)) {
            return false;
        }

        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    // Check payment module is configured
    public function checkPaypalCommerceConfigured()
    {
        if (!isset($this->ppMode)
            || !isset($this->merchantId)
            || !isset($this->paypalEmail)
            || !isset($this->clientId)
            || !isset($this->clientSecret)
        ) {
            return false;
        }

        return true;
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }
        $objOrder = $params['objOrder'];
        // Returns the state of the order.
        $idOrderState = $objOrder->getCurrentState();
        $objOrderState = new OrderState($idOrderState);
        if ($objOrderState->logable) {
            if ($objOrder->is_advance_payment) {
                $order_total = $objOrder->advance_paid_amount;
            } else {
                $order_total = $objOrder->total_paid;
            }
            $this->smarty->assign(array(
                'total_to_pay' => Tools::displayPrice($order_total, $params['currencyObj'], false),
                'status' => 1,
                'id_order' => $objOrder->id,
            ));
        } else {
            $this->smarty->assign('status', 0);
        }

        return $this->display(__FILE__, 'payment_return.tpl');
    }

    public function registerModuleHooks()
    {
        return $this->registerHook(
            array(
                'displayPayment',
                'paymentReturn',
                'actionFrontControllerSetMedia',
                'displayBackOfficeHeader',
                'displayTopColumn'
            )
        );
    }

    public function callInstallTab()
    {
        $this->installTab('AdminPaypalCommerceTransaction', 'PayPal Transactions');
        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
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

    public function install()
    {
        $objModuleDb = new WkPaypalCommerceDb();
        if (!parent::install()
            || !$objModuleDb->createTables()
            || !$this->callInstallTab()
            || !$this->registerModuleHooks()
            || !Configuration::updateValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE', 'sandbox')
        ) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        $objModuleDb = new WkPaypalCommerceDb();
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !WkPaypalCommerceHelper::deleteWebhookUrl()
            || !$objModuleDb->deleteConfigVars()
            || !$objModuleDb->dropTables()
        ) {
            return false;
        }
        return true;
    }
}
