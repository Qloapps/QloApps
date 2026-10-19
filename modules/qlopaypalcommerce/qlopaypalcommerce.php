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

require_once dirname(__FILE__).'/classes/WkClassInclude.php';

class QloPaypalCommerce extends PaymentModule
{
    const WK_PAYPAL_COMMERCE_PAYMENT_MODE_SANDBOX = 'sandbox';
    const WK_PAYPAL_COMMERCE_PAYMENT_MODE_PRODUCTION = 'production';

    private $html = '';
    private $postErrors = array();
    private $token = '';

    private $sandboxMerchantId = '';
    private $sandboxEmail = '';
    private $sandboxClientId = '';
    private $sandboxClientSecret = '';

    private $liveMerchantId = '';
    private $liveEmail = '';
    private $liveClientId = '';
    private $liveClientSecret = '';

    public $ppMode;
    public $merchantId;
    public $paypalEmail;
    public $clientId;
    public $clientSecret;
    public $secure_key;

    public function __construct()
    {
        $this->name = 'qlopaypalcommerce';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.5';
        $this->author = 'Webkul';
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        $this->html = '';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->qloapps_versions_compliancy = array('min' => '1.6', 'max' => _QLOAPPS_VERSION_);

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
            'WK_PAYPAL_COMMERCE_SANDBOX_MERCHANT_ID',
            'WK_PAYPAL_COMMERCE_SANDBOX_EMAIL',
            'WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_ID',
            'WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_SECRET',
            'WK_PAYPAL_COMMERCE_LIVE_MERCHANT_ID',
            'WK_PAYPAL_COMMERCE_LIVE_EMAIL',
            'WK_PAYPAL_COMMERCE_LIVE_CLIENT_ID',
            'WK_PAYPAL_COMMERCE_LIVE_CLIENT_SECRET',
        ));

        if (!empty($config['WK_PAYPAL_COMMERCE_PAYMENT_MODE'])) {
            $this->ppMode = $config['WK_PAYPAL_COMMERCE_PAYMENT_MODE'];
        }
        $mode = $this->ppMode ? $this->ppMode : self::WK_PAYPAL_COMMERCE_PAYMENT_MODE_SANDBOX;

        if ($mode == self::WK_PAYPAL_COMMERCE_PAYMENT_MODE_PRODUCTION) {
            if (!empty($config['WK_PAYPAL_COMMERCE_LIVE_MERCHANT_ID'])) {
                $this->merchantId = $config['WK_PAYPAL_COMMERCE_LIVE_MERCHANT_ID'];
            }
            if (!empty($config['WK_PAYPAL_COMMERCE_LIVE_EMAIL'])) {
                $this->paypalEmail = $config['WK_PAYPAL_COMMERCE_LIVE_EMAIL'];
            }
            if (!empty($config['WK_PAYPAL_COMMERCE_LIVE_CLIENT_ID'])) {
                $this->clientId = $config['WK_PAYPAL_COMMERCE_LIVE_CLIENT_ID'];
            }
            if (!empty($config['WK_PAYPAL_COMMERCE_LIVE_CLIENT_SECRET'])) {
                $this->clientSecret = $config['WK_PAYPAL_COMMERCE_LIVE_CLIENT_SECRET'];
            }
        } else {
            if (!empty($config['WK_PAYPAL_COMMERCE_SANDBOX_MERCHANT_ID'])) {
                $this->merchantId = $config['WK_PAYPAL_COMMERCE_SANDBOX_MERCHANT_ID'];
            }
            if (!empty($config['WK_PAYPAL_COMMERCE_SANDBOX_EMAIL'])) {
                $this->paypalEmail = $config['WK_PAYPAL_COMMERCE_SANDBOX_EMAIL'];
            }
            if (!empty($config['WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_ID'])) {
                $this->clientId = $config['WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_ID'];
            }
            if (!empty($config['WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_SECRET'])) {
                $this->clientSecret = $config['WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_SECRET'];
            }
        }

        parent::__construct();

        $this->payment_type = OrderPayment::PAYMENT_TYPE_ONLINE;
    }

    public function getContent()
    {
        $this->context->controller->addJS($this->_path.'views/js/admin/paypal_config.js');

        if (!$this->checkPaypalCommerceConfigured()) {
            $this->context->controller->warnings[] = $this->l('PayPal Merchant ID, Email, Client ID and Secret must be configured.');
        }

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->context->controller->warnings[] = $this->l('No currency has been set for this module.');
        }

        if (Tools::isSubmit('btnConfigSubmit')) {
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
                    'label' => $this->l('Sandbox Merchant ID'),
                    'name' => 'WK_PAYPAL_COMMERCE_SANDBOX_MERCHANT_ID',
                    'size' => 60,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the Merchant ID of your PayPal sandbox account.'),
                ),
                array(
                    'label' => $this->l('Sandbox Account Email'),
                    'name' => 'WK_PAYPAL_COMMERCE_SANDBOX_EMAIL',
                    'size' => 60,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the PayPal Account Email of your PayPal sandbox account.'),
                ),
                array(
                    'label' => $this->l('Sandbox Client ID'),
                    'name' => 'WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_ID',
                    'size' => 100,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the Client ID from PayPal sandbox account app credentials.'),
                ),
                array(
                    'label' => $this->l('Sandbox Client Secret'),
                    'name' => 'WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_SECRET',
                    'size' => 100,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the Client Secret from PayPal sandbox account app credentials.'),
                ),
                array(
                    'label' => $this->l('Live Merchant ID'),
                    'name' => 'WK_PAYPAL_COMMERCE_LIVE_MERCHANT_ID',
                    'size' => 60,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the Merchant ID of your PayPal live account.'),
                ),
                array(
                    'label' => $this->l('Live Account Email'),
                    'name' => 'WK_PAYPAL_COMMERCE_LIVE_EMAIL',
                    'size' => 60,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the PayPal Account Email of your PayPal live account.'),
                ),
                array(
                    'label' => $this->l('Live Client ID'),
                    'name' => 'WK_PAYPAL_COMMERCE_LIVE_CLIENT_ID',
                    'size' => 100,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the Client ID from PayPal live account app credentials.'),
                ),
                array(
                    'label' => $this->l('Live Client Secret'),
                    'name' => 'WK_PAYPAL_COMMERCE_LIVE_CLIENT_SECRET',
                    'size' => 100,
                    'type' => 'text',
                    'required' => true,
                    'hint' => $this->l('Enter the Client Secret from PayPal live account app credentials.'),
                ),
            ),
            'description' => $this->l('PayPal Seller Protection not applicable'),
            'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'btnConfigSubmit',
                ),
            );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
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
        if (Tools::isSubmit('btnConfigSubmit')) {
            $mode = Tools::getValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE');

            $this->sandboxMerchantId = trim(Tools::getValue('WK_PAYPAL_COMMERCE_SANDBOX_MERCHANT_ID'));
            $this->sandboxEmail = trim(Tools::getValue('WK_PAYPAL_COMMERCE_SANDBOX_EMAIL'));
            $this->sandboxClientId = trim(Tools::getValue('WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_ID'));
            $this->sandboxClientSecret = trim(Tools::getValue('WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_SECRET'));

            $this->liveMerchantId = trim(Tools::getValue('WK_PAYPAL_COMMERCE_LIVE_MERCHANT_ID'));
            $this->liveEmail = trim(Tools::getValue('WK_PAYPAL_COMMERCE_LIVE_EMAIL'));
            $this->liveClientId = trim(Tools::getValue('WK_PAYPAL_COMMERCE_LIVE_CLIENT_ID'));
            $this->liveClientSecret = trim(Tools::getValue('WK_PAYPAL_COMMERCE_LIVE_CLIENT_SECRET'));

            if ($mode == self::WK_PAYPAL_COMMERCE_PAYMENT_MODE_PRODUCTION) {
                $wkMerchantId = $this->liveMerchantId;
                $wkEmail = $this->liveEmail;
                $wkClientID = $this->liveClientId;
                $wkClientSecret = $this->liveClientSecret;
            } else {
                $wkMerchantId = $this->sandboxMerchantId;
                $wkEmail = $this->sandboxEmail;
                $wkClientID = $this->sandboxClientId;
                $wkClientSecret = $this->sandboxClientSecret;
            }

            if (!$wkMerchantId) {
                $this->postErrors[] = $this->l('Please enter Merchant ID');
            } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $wkMerchantId)) {
                $this->postErrors[] = $this->l('Invalid Merchant ID provided.');
            }
            if (!$wkEmail) {
                $this->postErrors[] = $this->l('Please enter Account Email');
            } elseif (!Validate::isEmail($wkEmail)) {
                $this->postErrors[] = $this->l('Please enter valid Account Email');
            }
            if (!$wkClientID) {
                $this->postErrors[] = $this->l('Please enter Client ID');
            } elseif (!preg_match('/^[A-Za-z0-9._-]+$/', $wkClientID)) {
                $this->postErrors[] = $this->l('Invalid Client ID provided.');
            }
            if (!$wkClientSecret) {
                $this->postErrors[] = $this->l('Please enter Client Secret');
            } elseif (!preg_match('/^[A-Za-z0-9._-]+$/', $wkClientSecret)) {
                $this->postErrors[] = $this->l('Invalid Client Secret provided.');
            }

            // Validate PayPal credentials
            $this->validatePaypalCredentials();

            if ($mode == self::WK_PAYPAL_COMMERCE_PAYMENT_MODE_SANDBOX
                && $this->token
            ) {
                if (empty(Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_WEBHOOK_ID'))) {
                    // Create webhook URL first time
                    $this->createWebhookUrl('sandbox');
                } elseif ($this->sandboxClientId != Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_ID')) {
                    // Delete existing webhook URL if PayPal credential changed
                    WkPaypalCommerceHelper::deleteWebhookUrl();
                    $this->createWebhookUrl('sandbox');
                }
            } elseif ($mode == self::WK_PAYPAL_COMMERCE_PAYMENT_MODE_PRODUCTION
                && $this->token
            ) {
                if (empty(Configuration::get('WK_PAYPAL_COMMERCE_LIVE_WEBHOOK_ID'))) {
                    // Create webhook URL first time
                    $this->createWebhookUrl('production');
                } elseif ($this->liveClientId != Configuration::get('WK_PAYPAL_COMMERCE_LIVE_CLIENT_ID')) {
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
            $mode = Tools::getValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE');

            Configuration::updateValue('WK_PAYPAL_COMMERCE_SANDBOX_MERCHANT_ID', $this->sandboxMerchantId);
            Configuration::updateValue('WK_PAYPAL_COMMERCE_SANDBOX_EMAIL', $this->sandboxEmail);
            Configuration::updateValue('WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_ID', $this->sandboxClientId);
            Configuration::updateValue('WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_SECRET', $this->sandboxClientSecret);

            Configuration::updateValue('WK_PAYPAL_COMMERCE_LIVE_MERCHANT_ID', $this->liveMerchantId);
            Configuration::updateValue('WK_PAYPAL_COMMERCE_LIVE_EMAIL', $this->liveEmail);
            Configuration::updateValue('WK_PAYPAL_COMMERCE_LIVE_CLIENT_ID', $this->liveClientId);
            Configuration::updateValue('WK_PAYPAL_COMMERCE_LIVE_CLIENT_SECRET', $this->liveClientSecret);

            Configuration::updateValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE', $mode);

            $moduleConfig = $this->context->link->getAdminLink('AdminModules');
            Tools::redirectAdmin(
                $moduleConfig.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&conf=4'
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
                $mode = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
                $clientId = ($mode === self::WK_PAYPAL_COMMERCE_PAYMENT_MODE_PRODUCTION) ? Configuration::get('WK_PAYPAL_COMMERCE_LIVE_CLIENT_ID') : Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_ID');
                $this->context->controller->addJS('https://www.paypal.com/sdk/js?client-id='.$clientId.'&commit=true&components=buttons&debug=false&currency='.$currency['iso_code'].'&intent=capture');

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
        $mode = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
        if ($mode == self::WK_PAYPAL_COMMERCE_PAYMENT_MODE_PRODUCTION) {
            return Configuration::get('WK_PAYPAL_COMMERCE_LIVE_MERCHANT_ID')
                && Configuration::get('WK_PAYPAL_COMMERCE_LIVE_EMAIL')
                && Configuration::get('WK_PAYPAL_COMMERCE_LIVE_CLIENT_ID')
                && Configuration::get('WK_PAYPAL_COMMERCE_LIVE_CLIENT_SECRET');
        }
        return Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_MERCHANT_ID')
            && Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_EMAIL')
            && Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_ID')
            && Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_CLIENT_SECRET');
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
