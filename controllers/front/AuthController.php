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
    protected $create_account;
    protected $id_country;

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

        if (Tools::getValue('create_account')) {
            $this->create_account = true;
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

        $newsletter = Module::isInstalled('blocknewsletter') && Module::getInstanceByName('blocknewsletter')->active;

        $this->context->smarty->assign('newsletter', $newsletter);
        $this->context->smarty->assign('optin', (bool)Configuration::get('PS_CUSTOMER_OPTIN'));

        $back = Tools::getValue('back');
        $key = Tools::safeOutput(Tools::getValue('key'));

        if (!empty($key)) {
            $back .= (strpos($back, '?') !== false ? '&' : '?').'key='.$key;
        }

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

        if (Tools::getValue('create_account')) {
            $this->context->smarty->assign('email_create', 1);
        }

        if (Tools::getValue('multi-shipping') == 1) {
            $this->context->smarty->assign('multi_shipping', true);
        } else {
            $this->context->smarty->assign('multi_shipping', false);
        }

        $this->context->smarty->assign('field_required', $this->context->customer->validateFieldsRequiredDatabase());

        $this->assignAddressFormat();

        // Call a hook to display more information on form
        $this->context->smarty->assign(array(
            'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
            'HOOK_CREATE_ACCOUNT_TOP' => Hook::exec('displayCustomerAccountFormTop')
        ));

        // Just set $this->template value here in case it's used by Ajax
        $this->setTemplate(_PS_THEME_DIR_.'authentication.tpl');

        if ($this->ajax) {
            // Call a hook to display more information on form
            $this->context->smarty->assign(array(
                    'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                    'genders' => Gender::getGenders()
                ));

            $return = array(
                'hasConfirmation' => !empty($this->confirmations),
                'confirmations' => $this->confirmations,
                'hasInformation' => !empty($this->informations),
                'informations' => $this->informations,
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'hasAjaxExtraData' => !empty($this->ajaxExtraData),
                'ajaxExtraData' => $this->ajaxExtraData,
                'page' => $this->context->smarty->fetch($this->template),
                'token' => Tools::getToken(false)
            );
            $this->ajaxDie(json_encode($return));
        }
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
        if (Tools::isSubmit('SubmitCreate')) {
            $this->processSubmitCreate();
        }

        if (Tools::isSubmit('submitAccount') || Tools::isSubmit('submitGuestAccount')) {
            $this->processSubmitAccount();
        }

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
        } else {
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
     * Process submit on an account
     */
    protected function processSubmitAccount()
    {
        Hook::exec('actionBeforeSubmitAccount');
        $this->create_account = true;
        if (Tools::isSubmit('submitAccount')) {
            $this->context->smarty->assign('email_create', 1);
        }
        // New Guest customer
        if (!Tools::getValue('is_new_customer', 1) && !Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
            $this->errors[] = Tools::displayError('You cannot create a guest account.');
        }
        if (!Tools::getValue('is_new_customer', 1)) {
            $_POST['passwd'] = md5(time()._COOKIE_KEY_);
        }
        if ($guest_email = Tools::getValue('guest_email')) {
            $_POST['email'] = $guest_email;
        }
        // Checked the user address in case he changed his email address
        if (Validate::isEmail($email = Tools::getValue('email')) && !empty($email)) {
            if (Customer::customerExists($email)) {
                $this->errors[] = Tools::displayError('An account using this email address has already been registered.', false);
            } elseif (Customer::customerExists($email, false, false)) {
                $this->errors[] = Tools::displayError('You are already registered as a guest with this email address.').'&nbsp;<button type="submit" class="btn btn-link alert-link btn-transform" name="submitTransformAccount">'.Tools::displayError('Click here').'</button>'.Tools::displayError('').' to generate a password for your account.';
            }
        }

        if (!count($this->errors)) {
            // Preparing customer
            $customer = new Customer();
            $lastnameAddress = Tools::getValue('lastname');
            $firstnameAddress = Tools::getValue('firstname');
            $_POST['lastname'] = Tools::getValue('customer_lastname', $lastnameAddress);
            $_POST['firstname'] = Tools::getValue('customer_firstname', $firstnameAddress);
            $addresses_types = array('address');

            if (!Configuration::get('PS_ORDER_PROCESS_TYPE') && Configuration::get('PS_GUEST_CHECKOUT_ENABLED') && Tools::getValue('invoice_address')) {
                $addresses_types[] = 'address_invoice';
            }

            $error_phone = false;
            if (Configuration::get('PS_ONE_PHONE_AT_LEAST')) {
                if (Tools::isSubmit('submitGuestAccount') || !Tools::getValue('is_new_customer')) {
                    if (!Tools::getValue('phone') && !Tools::getValue('phone_mobile')) {
                        $error_phone = true;
                    }
                } elseif (((Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && Configuration::get('PS_ORDER_PROCESS_TYPE'))
                        || (Configuration::get('PS_ORDER_PROCESS_TYPE') && !Tools::getValue('email_create'))
                        || (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && Tools::getValue('email_create')))
                        && (!Tools::getValue('phone') && !Tools::getValue('phone_mobile'))) {
                    $error_phone = true;
                }
            }

            if ($error_phone) {
                $this->errors[] = Tools::displayError('You must register at least one phone number.');
            }

            // if PS_CUSTOMER_ADDRESS_CREATION is disabled And customer has no addresses then create customer address
            // with the details of first hotel which rooms he has in cart
            if (!Configuration::get('PS_CUSTOMER_ADDRESS_CREATION')
                && isset($this->context->cart->id)
                && $this->context->cart->id
            ) {
                if (Module::isInstalled('hotelreservationsystem')) {
                    include_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';
                    $objHtlCart = new HotelCartBookingData();
                    if ($htlCartInfo = $objHtlCart->getCartCurrentDataByCartId($this->context->cart->id)) {
                        if (isset($htlCartInfo[0]['id_hotel']) && ($idHotel = $htlCartInfo[0]['id_hotel'])) {
                            if ($address_info = HotelBranchInformation::getAddress($idHotel)) {
                                $_POST['lastname'] = Tools::getValue('customer_lastname');
                                $_POST['firstname'] = Tools::getValue('customer_firstname');
                                $_POST['address1'] = $address_info['address1'];
                                $_POST['city'] = $address_info['city'];
                                $_POST['postcode'] = $address_info['postcode'];
                                $_POST['alias'] = 'My address';
                                $_POST['id_country'] = $address_info['id_country'];
                                $_POST['id_state'] = $address_info['id_state'];
                                $_POST['auto_generated'] = true;
                                // if form is shorter then address name will be customer name
                                $lastnameAddress = $_POST['lastname'];
                                $firstnameAddress = $_POST['firstname'];
                            }
                        }
                    }
                }
            }
            // End
            $this->errors = array_unique(array_merge($this->errors, $customer->validateController()));

            // Check the requires fields which are settings in the BO
            $this->errors = $this->errors + $customer->validateFieldsRequiredDatabase();

            if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && !$this->ajax && !Tools::isSubmit('submitGuestAccount')) {
                if (!count($this->errors)) {

                    $this->processCustomerNewsletter($customer);

                    $customer->firstname = Tools::ucwords($customer->firstname);
                    $customer->birthday = (empty($_POST['years']) ? '' : (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.(int)Tools::getValue('days'));
                    if (!Validate::isBirthDate($customer->birthday)) {
                        $this->errors[] = Tools::displayError('Invalid date of birth.');
                    }

                    // New Guest customer
                    $customer->is_guest = (Tools::isSubmit('is_new_customer') ? !Tools::getValue('is_new_customer', 1) : 0);
                    $customer->active = 1;

                    if (!count($this->errors)) {
                        if ($customer->add()) {
                            if (!$customer->is_guest) {
                                if (!$this->sendConfirmationMail($customer)) {
                                    $this->errors[] = Tools::displayError('The email cannot be sent.');
                                }
                            }

                            $this->context->updateCustomer($customer, 1);

                            $this->context->cart->update();
                            Hook::exec('actionCustomerAccountAdd', array(
                                    '_POST' => $_POST,
                                    'newCustomer' => $customer
                                ));
                            if ($this->ajax) {
                                $return = array(
                                    'hasError' => !empty($this->errors),
                                    'errors' => $this->errors,
                                    'isSaved' => true,
                                    'id_customer' => (int)$this->context->cookie->id_customer,
                                    'id_address_delivery' => $this->context->cart->id_address_delivery,
                                    'id_address_invoice' => $this->context->cart->id_address_invoice,
                                    'token' => Tools::getToken(false)
                                );
                                $this->ajaxDie(json_encode($return));
                            }

                            if (($back = Tools::getValue('back')) && $back == Tools::secureReferrer($back)) {
                                Tools::redirect(html_entity_decode($back));
                            }

                            // redirection: if cart is not empty : redirection to the cart
                            if (count($this->context->cart->getProducts(true)) > 0) {
                                $multi = (int)Tools::getValue('multi-shipping');
                                Tools::redirect('index.php?controller=order'.($multi ? '&multi-shipping='.$multi : ''));
                            }
                            // else : redirection to the account
                            else {
                                Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
                            }
                        } else {
                            $this->errors[] = Tools::displayError('An error occurred while creating your account.');
                        }
                    }
                }
            } else {
                // if registration type is in one step, we save the address

                $_POST['lastname'] = $lastnameAddress;
                $_POST['firstname'] = $firstnameAddress;
                $post_back = $_POST;
                // Preparing addresses
                foreach ($addresses_types as $addresses_type) {
                    $$addresses_type = new Address();
                    $$addresses_type->id_customer = 1;

                    if ($addresses_type == 'address_invoice') {
                        foreach ($_POST as $key => &$post) {
                            if ($tmp = Tools::getValue($key.'_invoice')) {
                                $post = $tmp;
                            }
                        }
                    }

                    $this->errors = array_unique(array_merge($this->errors, $$addresses_type->validateController()));
                    if ($addresses_type == 'address_invoice') {
                        $_POST = $post_back;
                    }

                    if (!($country = new Country($$addresses_type->id_country)) || !Validate::isLoadedObject($country)) {
                        $this->errors[] = Tools::displayError('Country cannot be loaded with address->id_country');
                    }

                    if (!$country->active) {
                        $this->errors[] = Tools::displayError('This country is not active.');
                    }

                    $postcode = $$addresses_type->postcode;
                    /* Check zip code format */
                    if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                        $this->errors[] = sprintf(Tools::displayError('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
                    } elseif (empty($postcode) && $country->need_zip_code) {
                        $this->errors[] = Tools::displayError('A Zip / Postal code is required.');
                    } elseif ($postcode && !Validate::isPostCode($postcode)) {
                        $this->errors[] = Tools::displayError('The Zip / Postal code is invalid.');
                    }

                    if ($country->need_identification_number) {
                        if (!Configuration::get('PS_CUSTOMER_ADDRESS_CREATION')) {
                            $$addresses_type->dni = null;
                        } elseif (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni'))) {
                            $this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');
                        }
                    } elseif (!$country->need_identification_number) {
                        $$addresses_type->dni = null;
                    }

                    if (Tools::isSubmit('submitAccount') || Tools::isSubmit('submitGuestAccount')) {
                        if (!($country = new Country($$addresses_type->id_country, Configuration::get('PS_LANG_DEFAULT'))) || !Validate::isLoadedObject($country)) {
                            $this->errors[] = Tools::displayError('Country is invalid');
                        }
                    }
                    $contains_state = isset($country) && is_object($country) ? (int)$country->contains_states: 0;
                    $id_state = isset($$addresses_type) && is_object($$addresses_type) ? (int)$$addresses_type->id_state: 0;
                    if ((Tools::isSubmit('submitAccount') || Tools::isSubmit('submitGuestAccount')) && $contains_state && !$id_state) {
                        $this->errors[] = Tools::displayError('This country requires you to choose a State.');
                    }
                }
            }

            if (!@checkdate(Tools::getValue('months'), Tools::getValue('days'), Tools::getValue('years')) && !(Tools::getValue('months') == '' && Tools::getValue('days') == '' && Tools::getValue('years') == '')) {
                $this->errors[] = Tools::displayError('Invalid date of birth');
            }

            if (!count($this->errors)) {
                if (Customer::customerExists(Tools::getValue('email'))) {
                    $this->errors[] = Tools::displayError('An account using this email address has already been registered. Please enter a valid password or request a new one. ', false);
                }

                $this->processCustomerNewsletter($customer);

                $customer->birthday = (empty($_POST['years']) ? '' : (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.(int)Tools::getValue('days'));
                if (!Validate::isBirthDate($customer->birthday)) {
                    $this->errors[] = Tools::displayError('Invalid date of birth');
                }

                if (!count($this->errors)) {
                    $customer->active = 1;
                    // New Guest customer
                    if (Tools::isSubmit('is_new_customer')) {
                        $customer->is_guest = !Tools::getValue('is_new_customer', 1);
                    } else {
                        $customer->is_guest = 0;
                    }
                    if (!$customer->add()) {
                        $this->errors[] = Tools::displayError('An error occurred while creating your account.');
                    } else {
                        foreach ($addresses_types as $addresses_type) {
                            $$addresses_type->id_customer = (int)$customer->id;
                            if ($addresses_type == 'address_invoice') {
                                foreach ($_POST as $key => &$post) {
                                    if ($tmp = Tools::getValue($key.'_invoice')) {
                                        $post = $tmp;
                                    }
                                }
                            }

                            $this->errors = array_unique(array_merge($this->errors, $$addresses_type->validateController()));
                            if ($addresses_type == 'address_invoice') {
                                $_POST = $post_back;
                            }
                            if (!count($this->errors) && (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') || $this->ajax || Tools::isSubmit('submitGuestAccount')) && !$$addresses_type->add()) {
                                $this->errors[] = Tools::displayError('An error occurred while creating your address.');
                            }
                        }
                        if (!count($this->errors)) {
                            if (!$customer->is_guest) {
                                $this->context->customer = $customer;
                                $customer->cleanGroups();
                                // we add the guest customer in the default customer group
                                $customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
                                if (!$this->sendConfirmationMail($customer)) {
                                    // webkul - we will not stop process on email delivery fails
                                    //$this->errors[] = Tools::displayError('The email cannot be sent.');
                                }
                            } else {
                                $customer->cleanGroups();
                                // we add the guest customer in the guest customer group
                                $customer->addGroups(array((int)Configuration::get('PS_GUEST_GROUP')));
                            }

                            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)$customer->id);
                            $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)$customer->id);
                            if (isset($address_invoice) && Validate::isLoadedObject($address_invoice)) {
                                $this->context->cart->id_address_invoice = (int)$address_invoice->id;
                            }

                            if ($this->ajax && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                                $delivery_option = array((int)$this->context->cart->id_address_delivery => (int)$this->context->cart->id_carrier.',');
                                $this->context->cart->setDeliveryOption($delivery_option);
                            }

                            $this->context->updateCustomer($customer, 1);
                            // If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
                            $this->context->cart->update();

                            // Avoid articles without delivery address on the cart
                            $this->context->cart->autosetProductAddress();

                            Hook::exec('actionCustomerAccountAdd', array(
                                    '_POST' => $_POST,
                                    'newCustomer' => $customer
                                ));
                            if ($this->ajax) {
                                $return = array(
                                    'hasError' => !empty($this->errors),
                                    'errors' => $this->errors,
                                    'isSaved' => true,
                                    'id_customer' => (int)$this->context->cookie->id_customer,
                                    'id_address_delivery' => $this->context->cart->id_address_delivery,
                                    'id_address_invoice' => $this->context->cart->id_address_invoice,
                                    'token' => Tools::getToken(false)
                                );
                                $this->ajaxDie(json_encode($return));
                            }
                            // if registration type is in two steps, we redirect to register address
                            if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && !$this->ajax && !Tools::isSubmit('submitGuestAccount')) {
                                Tools::redirect('index.php?controller=address');
                            }

                            if (($back = Tools::getValue('back')) && $back == Tools::secureReferrer($back)) {
                                Tools::redirect(html_entity_decode($back));
                            }

                            // redirection: if cart is not empty : redirection to the cart
                            if (count($this->context->cart->getProducts(true)) > 0) {
                                Tools::redirect('index.php?controller=order'.($multi = (int)Tools::getValue('multi-shipping') ? '&multi-shipping='.$multi : ''));
                            }
                            // else : redirection to the account
                            else {
                                Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
                            }
                        }
                    }
                }
            }
        }

        if (count($this->errors)) {
            //for retro compatibility to display guest account creation form on authentication page
            if (Tools::getValue('submitGuestAccount')) {
                $_GET['display_guest_checkout'] = 1;
            }

            if (!Tools::getValue('is_new_customer')) {
                unset($_POST['passwd']);
            }
            if ($this->ajax) {
                $return = array(
                    'hasError' => !empty($this->errors),
                    'errors' => $this->errors,
                    'isSaved' => false,
                    'id_customer' => 0
                );
                $this->ajaxDie(json_encode($return));
            }
            $this->context->smarty->assign('account_error', $this->errors);
        }
    }

    /**
     * Process submit on a creation
     */
    protected function processSubmitCreate()
    {
        if (!Validate::isEmail($email = trim(Tools::getValue('email_create'))) || empty($email)) {
            $this->errors[] = Tools::displayError('Invalid email address.');
        } elseif (Customer::customerExists($email)) {
            $this->errors[] = Tools::displayError('An account using this email address has already been registered. Please choose another one or sign in.', false);
            $_POST['email'] = trim(Tools::getValue('email_create'));
            unset($_POST['email_create']);
        } elseif ($id_customer = Customer::customerExists($email, true, false)) {
            $this->informations[] = Tools::displayError('You are already registered as a guest with this email address.').'&nbsp;<button type="submit" class="btn btn-link alert-link btn-transform" name="submitTransformAccount">'.Tools::displayError('Click here').'</button>'.Tools::displayError('').' to generate a password for your account.';
        } else {
            $this->create_account = true;
            $this->context->smarty->assign('email_create', Tools::safeOutput($email));
            $_POST['email'] = $email;
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
                '{passwd}' => str_repeat('*', strlen(Tools::getValue('passwd'))),
            ),
            $customer->email,
            $customer->firstname.' '.$customer->lastname
        );
    }
}