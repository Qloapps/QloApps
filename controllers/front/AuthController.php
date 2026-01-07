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

class AuthControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'authentication';
    public $auth = false;

    /**
     * @var bool create_account
     */
    protected $id_country;
    public $informations;
    public $confirmations;
    protected $ajaxExtraData;

    /**
     * Initialize auth controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        $this->confirmations = array();
        $this->informations = array();
        $this->errors = array();
        $this->ajaxExtraData = array();

        if (!Tools::getIsset('step') && $this->context->customer->isLogged() && !$this->ajax) {
            Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
        }
    }

    /**
     * Set default medias for this controller
     * @see FrontController::setMedia()
     */
    public function setMedia()
    {
        parent::setMedia();
        if (!$this->useMobileTheme()) {
            $this->addCSS(_THEME_CSS_DIR_.'authentication.css');
        }

        $this->addJqueryPlugin('typewatch');
        $this->addJS(array(
            _THEME_JS_DIR_.'tools/statesManagement.js',
            _THEME_JS_DIR_.'authentication.js',
            _PS_JS_DIR_.'validate.js'
        ));
    }

    /**
     * Run ajax process
     * @see FrontController::displayAjax()
     */
    public function displayAjax()
    {
        $this->display();
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign('genders', Gender::getGenders());

        $this->assignDate();

        $this->assignCountries();

        $newsletter = Configuration::get('PS_CUSTOMER_NWSL') || (Module::isInstalled('blocknewsletter') && Module::getInstanceByName('blocknewsletter')->active);

        $this->context->smarty->assign('birthday', (bool) Configuration::get('PS_CUSTOMER_BIRTHDATE'));
        $this->context->smarty->assign('newsletter', $newsletter);
        $this->context->smarty->assign('optin', (bool)Configuration::get('PS_CUSTOMER_OPTIN'));

        $back = Tools::getValue('back');
        $key = Tools::safeOutput(Tools::getValue('key'));

        if (!empty($key)) {
            $back .= (strpos($back, '?') !== false ? '&' : '?').'key='.$key;
        }

        // sanitize backurl for XSS protection
        $back = filter_var($back, FILTER_SANITIZE_URL);

        if ($back == Tools::secureReferrer(Tools::getValue('back'))) {
            $this->context->smarty->assign('back', html_entity_decode($back));
        } else {
            $this->context->smarty->assign('back', Tools::safeOutput($back));
        }

        if (Tools::getValue('display_guest_checkout')) {
            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
            } else {
                $countries = Country::getCountries($this->context->language->id, true);
            }

            $this->context->smarty->assign(array(
                    'inOrderProcess' => true,
                    'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                    'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                    'sl_country' => (int)$this->id_country,
                    'countries' => $countries
                ));
        }

        if (Tools::getValue('multi-shipping') == 1) {
            $this->context->smarty->assign('multi_shipping', true);
        } else {
            $this->context->smarty->assign('multi_shipping', false);
        }

        $this->context->smarty->assign('field_required', $this->context->customer->validateFieldsRequiredDatabase());

        $this->assignAddressFormat();
        // Just set $this->template value here in case it's used by Ajax
        $this->setTemplate(_PS_THEME_DIR_.'authentication.tpl');
    }

    /**
     * Assign date var to smarty
     */
    protected function assignDate()
    {
        $selectedYears = (int)(Tools::getValue('years', 0));
        $years = Tools::dateYears();
        $selectedMonths = (int)(Tools::getValue('months', 0));
        $months = Tools::dateMonths();
        $selectedDays = (int)(Tools::getValue('days', 0));
        $days = Tools::dateDays();

        $this->context->smarty->assign(array(
                'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
                'onr_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'), //retro compat
                'years' => $years,
                'sl_year' => $selectedYears,
                'months' => $months,
                'sl_month' => $selectedMonths,
                'days' => $days,
                'sl_day' => $selectedDays
            ));
    }

    /**
     * Assign countries var to smarty
     */
    protected function assignCountries()
    {
        $this->id_country = (int)Tools::getCountry();
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }
        $this->context->smarty->assign(array(
                'countries' => $countries,
                'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                'sl_country' => (int)$this->id_country,
            ));
    }

    /**
     * Assign address var to smarty
     */
    protected function assignAddressFormat()
    {
        $addressItems = array();
        $addressFormat = AddressFormat::getOrderedAddressFields((int)$this->id_country, false, true);
        $requireFormFieldsList = AddressFormat::getFieldsRequired();

        foreach ($addressFormat as $addressline) {
            foreach (explode(' ', $addressline) as $addressItem) {
                $addressItems[] = trim($addressItem);
            }
        }

        // Add missing require fields for a new user susbscription form
        foreach ($requireFormFieldsList as $fieldName) {
            if (!in_array($fieldName, $addressItems)) {
                $addressItems[] = trim($fieldName);
            }
        }

        foreach (array('inv', 'dlv') as $addressType) {
            $this->context->smarty->assign(array(
                $addressType.'_adr_fields' => $addressFormat,
                $addressType.'_all_fields' => $addressItems,
                'required_fields' => $requireFormFieldsList
            ));
        }
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        // @todo
        if (Tools::isSubmit('submitTransformAccount')) {
            $this->processTransformAccount();
        }

        if (Tools::isSubmit('SubmitLogin')) {
            $this->processSubmitLogin();
        }
    }

    /**
     * Process login
     */
    protected function processSubmitLogin()
    {
        Hook::exec('actionBeforeAuthentication');
        $passwd = trim(Tools::getValue('passwd'));
        $_POST['passwd'] = null;
        $email = trim(Tools::getValue('email'));

        if (empty($email)) {
            $this->errors[] = Tools::displayError('An email address required.');
        } elseif (!Validate::isEmail($email)) {
            $this->errors[] = Tools::displayError('Invalid email address.');
        } elseif (empty($passwd)) {
            $this->errors[] = Tools::displayError('Password is required.');
        } elseif (!Validate::isPasswd($passwd)) {
            $this->errors[] = Tools::displayError('Invalid password.');
        }

        if (!count($this->errors)) {
            $customer = new Customer();
            $authentication = $customer->getByEmail(trim($email), trim($passwd));
            if (isset($authentication->active) && !$authentication->active) {
                $this->errors[] = Tools::displayError('Your account isn\'t available at this time, please contact us');
            } elseif (!$authentication || !$customer->id) {
                $this->errors[] = Tools::displayError('Authentication failed.');
            } else {
                $this->context->updateCustomer($customer, 1);

                Hook::exec('actionAuthentication', array('customer' => $this->context->customer));

                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);

                if (!$this->ajax) {
                    $back = Tools::getValue('back','my-account');

                    if ($back == Tools::secureReferrer($back)) {
                        Tools::redirect(html_entity_decode($back));
                    }

                    Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : $back));
                }
            }
        }

        if ($this->ajax) {
            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'token' => Tools::getToken(false)
            );
            $this->ajaxDie(json_encode($return));
        } else {
            $this->context->smarty->assign('authentification_error', $this->errors);
        }
    }

    /**
     * Process the newsletter settings and set the customer infos.
     *
     * @param Customer $customer Reference on the customer Object.
     *
     * @note At this point, the email has been validated.
     */
    protected function processCustomerNewsletter(&$customer)
    {
        $blocknewsletter = Module::isInstalled('blocknewsletter') && $module_newsletter = Module::getInstanceByName('blocknewsletter');
        if ($blocknewsletter && $module_newsletter->active && !Tools::getValue('newsletter')) {
            require_once _PS_MODULE_DIR_.'blocknewsletter/blocknewsletter.php';
            if (is_callable(array($module_newsletter, 'isNewsletterRegistered')) && $module_newsletter->isNewsletterRegistered(Tools::getValue('email')) == Blocknewsletter::GUEST_REGISTERED) {
                /* Force newsletter registration as customer as already registred as guest */
                $_POST['newsletter'] = true;
            }
        }

        if (Tools::getValue('newsletter')) {
            $customer->newsletter = true;
            $customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
            $customer->newsletter_date_add = pSQL(date('Y-m-d H:i:s'));
            /** @var Blocknewsletter $module_newsletter */
            if ($blocknewsletter && $module_newsletter->active) {
                $module_newsletter->confirmSubscription(Tools::getValue('email'));
            }
        }
    }

    /**
     * Process submit for account transformation
     */
    protected function processTransformAccount()
    {
        if (!Validate::isEmail($email = trim(Tools::getValue('email_create', Tools::getValue('email')))) || empty($email)) {
            $this->errors[] = Tools::displayError('Invalid email address.');
        } elseif ($id_customer = Customer::customerExists($email, true, false)) {
            $customer = new Customer($id_customer);
            if ($customer->transformToCustomer($this->context->language->id)) {
                $this->confirmations[] = 'TRANSFORM_OK';

                $back = '';
                if ($back == Tools::secureReferrer(Tools::getValue('back'))) {
                    $back = html_entity_decode($back);
                } else {
                    $back = Tools::safeOutput($back);
                }
                $this->ajaxExtraData['redirect_link'] = $this->context->link->getPageLink('authentication').
                ($back ? '?back='.$back.'&' : '?').'guest_transform_success=1';
            } else {
                $this->errors[] = Tools::displayError('Your guest account could not be transformed to a customer account.');
            }
        }
    }

    /**
     * sendConfirmationMail
     * @param Customer $customer
     * @return bool
     */
    protected function sendConfirmationMail(Customer $customer)
    {
        if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            return true;
        }

        return Mail::Send(
            $this->context->language->id,
            'account',
            Mail::l('Welcome!'),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
            ),
            $customer->email,
            $customer->firstname.' '.$customer->lastname
        );
    }
}
