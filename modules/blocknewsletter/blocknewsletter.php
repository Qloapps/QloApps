<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Blocknewsletter extends Module
{
    const GUEST_NOT_REGISTERED = -1;
    const CUSTOMER_NOT_REGISTERED = 0;
    const GUEST_REGISTERED = 1;
    const CUSTOMER_REGISTERED = 2;

    const EXPORT_ALL_SUBSCRIBERS = 1;
    const EXPORT_SUBSCRIBERS_WITH_ACCOUNT = 2;
    const EXPORT_SUBSCRIBERS_WITHOUT_ACCOUNT = 3;
    const EXPORT_NON_SUBSCRIBERS = 4;

    public function __construct()
    {
        $this->name = 'blocknewsletter';
        $this->tab = 'front_office_features';
        $this->need_instance = 0;

        $this->controllers = array('verification');

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Newsletter block');
        $this->description = $this->l('Adds a block for newsletter subscription.');
        $this->confirmUninstall = $this->l('Are you sure that you want to delete all of your contacts?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->secure_key = Tools::encrypt($this->name);

        $this->version = '2.2.2';
        $this->author = 'PrestaShop';
        $this->error = false;
        $this->valid = false;
        $this->_files = array(
            'name' => array('newsletter_conf', 'newsletter_voucher'),
            'ext' => array(
                0 => 'html',
                1 => 'txt'
            )
        );

        $this->_html = '';

        $this->hookPrepared = false;
    }

    public function registerHooks()
    {
        return $this->registerHook(
            array(
                'header',
                'footer',
                'displayFooterNotificationHook',
                'actionCustomerAccountAdd',
                'registerGDPRConsent',
                'actionExportGDPRData',
                'actionDeleteGDPRCustomer',
                'actionObjectCustomerUpdateAfter'
            )
        );
    }

    public function hookActionObjectCustomerUpdateAfter($params)
    {
        $objCustomer = $params['object'];
        if ($objCustomer->deleted
            && ($register_status = $this->isNewsletterRegistered($objCustomer->email))
            && $register_status > 0
        ) {
            $this->unregister($objCustomer->email, $register_status);
        }
    }

    public function hookActionExportGDPRData($customer)
    {
        if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {
            $sql = "SELECT * FROM "._DB_PREFIX_."newsletter WHERE email = '".pSQL($customer['email'])."'";
            if ($res = Db::getInstance()->ExecuteS($sql)) {
                return json_encode($res);
            }
            return json_encode($this->l('Newsletter block : Unable to export customer using email.'));
        }
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email']) && Validate::isEmail($customer['email'])) {
            $sql = "DELETE FROM "._DB_PREFIX_."newsletter WHERE email = '".pSQL($customer['email'])."'";
            if (Db::getInstance()->execute($sql)) {
                return json_encode(true);
            }
            return json_encode($this->l('Newsletter block : Unable to delete customer using email.'));
        }
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHooks() || !$this->callInstallTab()) {
            return false;
        }
        Configuration::updateValue('NW_SALT', Tools::passwdGen(16));
        return Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'newsletter` (
				`id` int(6) NOT NULL AUTO_INCREMENT,
				`id_shop` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
				`id_shop_group` INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
				`email` varchar(255) NOT NULL,
				`newsletter_date_add` DATETIME NULL,
				`ip_registration_newsletter` varchar(15) NOT NULL,
				`http_referer` VARCHAR(255) NULL,
				`active` TINYINT(1) NOT NULL DEFAULT \'0\',
				PRIMARY KEY(`id`)
			) ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8'
        );
    }

    public function callInstallTab()
    {
        $result = $this->installTab('AdminParentNewsletter', 'Newsletter', false, true);
        $result &= $this->installTab('AdminNewsletter', 'Configuration', 'AdminParentNewsletter', true);

        return $result;
    }

    public function installTab($className, $tabName, $tabParentName = false, $hidden = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = array();
        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } elseif ($hidden) {
            $tab->id_parent = -1;
        } else {
            $tab->id_parent = 0;
        }
        $tab->module = $this->name;

        return $tab->add();
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminNewsletter'));
    }

    /**
     * Check if this mail is registered for newsletters
     *
     * @param string $customer_email
     *
     * @return int -1 = not a customer and not registered
     *                0 = customer not registered
     *                1 = registered in block
     *                2 = registered in customer
     */
    public function isNewsletterRegistered($customer_email)
    {
        $sql = 'SELECT `email`
				FROM '._DB_PREFIX_.'newsletter
				WHERE `email` = \''.pSQL($customer_email).'\'
				AND id_shop = '.$this->context->shop->id;

        if (Db::getInstance()->getRow($sql)) {
            return self::GUEST_REGISTERED;
        }

        $sql = 'SELECT `newsletter`
				FROM '._DB_PREFIX_.'customer
				WHERE `email` = \''.pSQL($customer_email).'\'
				AND id_shop = '.$this->context->shop->id;

        if (!$registered = Db::getInstance()->getRow($sql)) {
            return self::GUEST_NOT_REGISTERED;
        }

        if ($registered['newsletter'] == '1') {
            return self::CUSTOMER_REGISTERED;
        }

        return self::CUSTOMER_NOT_REGISTERED;
    }

    /**
     * Register in block newsletter
     */
    public function newsletterRegistration()
    {
        if (empty($_POST['email']) || !Validate::isEmail($_POST['email'])) {
            return $this->error = $this->l('Invalid email address.');
        }

        /* Unsubscription */
        elseif ($_POST['newsletter_action'] == '1') {
            $register_status = $this->isNewsletterRegistered($_POST['email']);

            if ($register_status < 1) {
                return $this->error = $this->l('This email address is not registered.');
            }

            if (!$this->unregister($_POST['email'], $register_status)) {
                return $this->error = $this->l('An error occurred while attempting to unsubscribe.');
            }

            return $this->valid = $this->l('Unsubscription successful.');
        }
        /* Subscription */
        elseif ($_POST['newsletter_action'] == '0') {
            $register_status = $this->isNewsletterRegistered($_POST['email']);
            if ($register_status > 0) {
                return $this->error = $this->l('This email address is already registered.');
            }

            $email = pSQL($_POST['email']);
            if (!$this->isRegistered($register_status)) {
                if (Configuration::get('NW_VERIFICATION_EMAIL')) {
                    // create an unactive entry in the newsletter database
                    if ($register_status == self::GUEST_NOT_REGISTERED) {
                        $this->registerGuest($email, false);
                    }

                    if (!$token = $this->getToken($email, $register_status)) {
                        return $this->error = $this->l('An error occurred during the subscription process.');
                    }

                    $this->sendVerificationEmail($email, $token);

                    return $this->valid = $this->l('A verification email has been sent. Please check your inbox.');
                } else {
                    if ($this->register($email, $register_status)) {
                        $this->valid = $this->l('You have successfully subscribed to this newsletter.');
                    } else {
                        return $this->error = $this->l('An error occurred during the subscription process.');
                    }

                    if ($code = Configuration::get('NW_VOUCHER_CODE')) {
                        $this->sendVoucher($email, $code);
                    }

                    if (Configuration::get('NW_CONFIRMATION_EMAIL')) {
                        $this->sendConfirmationEmail($email);
                    }
                }
            }
        }
    }

    public function getExportCsvHeader()
    {
        return array(
            $this->l('ID'),
            $this->l('Social Title'),
            $this->l('Email'),
            $this->l('First Name'),
            $this->l('Last Name'),
            $this->l('Subscribed'),
            $this->l('Date Added'),
        );
    }

    public function getSubscribers($subscribersType)
    {
        $subscribers = array();

        if (in_array(
            $subscribersType,
            array(
                self::EXPORT_ALL_SUBSCRIBERS,
                self::EXPORT_SUBSCRIBERS_WITH_ACCOUNT,
                self::EXPORT_NON_SUBSCRIBERS,
            )
        )) {
            $sqlCustomer = 'SELECT c.`id_customer` AS id, gl.`name` AS gender, c.`email`, c.`firstname`, c.`lastname`,
            c.`newsletter` AS subscribed, c.`newsletter_date_add`
            FROM `'._DB_PREFIX_.'customer` c
            LEFT JOIN '._DB_PREFIX_.'gender_lang gl ON (gl.`id_gender` = c.`id_gender` AND gl.`id_lang` = '.(int) $this->context->language->id.')
            WHERE c.`newsletter` = '.($subscribersType == self::EXPORT_NON_SUBSCRIBERS ? '0' : '1');

            $subscribers = array_merge($subscribers, Db::getInstance()->executeS($sqlCustomer));
        }

        if (in_array(
            $subscribersType,
            array(
                self::EXPORT_ALL_SUBSCRIBERS,
                self::EXPORT_SUBSCRIBERS_WITHOUT_ACCOUNT,
                self::EXPORT_NON_SUBSCRIBERS,
            )
        )) {
            $sqlNewsletter = 'SELECT CONCAT(\'N\', n.`id`) AS id, NULL AS id_gender, n.`email`, NULL AS firstname,
            NULL AS lastname, n.`active` AS subscribed, n.`newsletter_date_add`
            FROM `'._DB_PREFIX_.'newsletter` n WHERE n.`active` = '.($subscribersType == self::EXPORT_NON_SUBSCRIBERS ? '0' : '1');

            $subscribers = array_merge($subscribers, Db::getInstance()->executeS($sqlNewsletter));
        }

        return $subscribers;
    }

    /**
     * Return true if the registered status correspond to a registered user
     *
     * @param int $register_status
     *
     * @return bool
     */
    protected function isRegistered($register_status)
    {
        return in_array(
            $register_status,
            array(self::GUEST_REGISTERED, self::CUSTOMER_REGISTERED)
        );
    }


    /**
     * Subscribe an email to the newsletter. It will create an entry in the newsletter table
     * or update the customer table depending of the register status
     *
     * @param string $email
     * @param int    $register_status
     */
    protected function register($email, $register_status)
    {
        if ($register_status == self::GUEST_NOT_REGISTERED) {
            return $this->registerGuest($email);
        }

        if ($register_status == self::CUSTOMER_NOT_REGISTERED) {
            return $this->registerUser($email);
        }

        return false;
    }

    public function unregister($email, $register_status)
    {
        if ($register_status == self::GUEST_REGISTERED) {
            $sql = 'DELETE FROM '._DB_PREFIX_.'newsletter WHERE `email` = \''.pSQL($email).'\' AND id_shop = '.
            $this->context->shop->id;
        } elseif ($register_status == self::CUSTOMER_REGISTERED) {
            $sql = 'UPDATE '._DB_PREFIX_.'customer SET `newsletter` = 0 WHERE `email` = \''.pSQL($email).
            '\' AND id_shop = '.$this->context->shop->id;
        }

        if (!isset($sql) || !Db::getInstance()->execute($sql)) {
            return false;
        }

        return true;
    }

    /**
     * Subscribe a customer to the newsletter
     *
     * @param string $email
     *
     * @return bool
     */
    protected function registerUser($email)
    {
        $sql = 'UPDATE '._DB_PREFIX_.'customer SET `newsletter` = 1, newsletter_date_add = NOW(),
		`ip_registration_newsletter` = \''.pSQL(Tools::getRemoteAddr()).'\' WHERE `email` = \''.pSQL($email).
        '\' AND id_shop = '.$this->context->shop->id;

        return Db::getInstance()->execute($sql);
    }

    /**
     * Subscribe a guest to the newsletter
     *
     * @param string $email
     * @param bool   $active
     *
     * @return bool
     */
    protected function registerGuest($email, $active = true)
    {
        $sql = 'INSERT INTO '._DB_PREFIX_.'newsletter (id_shop, id_shop_group, email, newsletter_date_add,
		ip_registration_newsletter, http_referer, active)
				VALUES
				('.$this->context->shop->id.',
				'.$this->context->shop->id_shop_group.',
				\''.pSQL($email).'\',
				NOW(),
				\''.pSQL(Tools::getRemoteAddr()).'\',
				(
					SELECT c.http_referer
					FROM '._DB_PREFIX_.'connections c
					WHERE c.id_guest = '.(int)$this->context->customer->id.'
					ORDER BY c.date_add DESC LIMIT 1
				),
				'.(int)$active.'
				)';

        return Db::getInstance()->execute($sql);
    }


    public function activateGuest($email)
    {
        return Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'newsletter`
						SET `active` = 1
						WHERE `email` = \''.pSQL($email).'\''
        );
    }

    /**
     * Returns a guest email by token
     *
     * @param string $token
     *
     * @return string email
     */
    protected function getGuestEmailByToken($token)
    {
        $sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'newsletter`
				WHERE MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.
                pSQL($token).'\'
				AND `active` = 0';

        return Db::getInstance()->getValue($sql);
    }

    /**
     * Returns a customer email by token
     *
     * @param string $token
     *
     * @return string email
     */
    protected function getUserEmailByToken($token)
    {
        $sql = 'SELECT `email`
				FROM `'._DB_PREFIX_.'customer`
				WHERE MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) = \''.
                pSQL($token).'\' AND `newsletter` = 0';

        return Db::getInstance()->getValue($sql);
    }

    /**
     * Return a token associated to an user
     *
     * @param string $email
     * @param string $register_status
     */
    protected function getToken($email, $register_status)
    {
        if (in_array($register_status, array(self::GUEST_NOT_REGISTERED, self::GUEST_REGISTERED))) {
            $sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) as token
					FROM `'._DB_PREFIX_.'newsletter`
					WHERE `active` = 0
					AND `email` = \''.pSQL($email).'\'';
        } elseif ($register_status == self::CUSTOMER_NOT_REGISTERED) {
            $sql = 'SELECT MD5(CONCAT( `email` , `date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\' )) as token
					FROM `'._DB_PREFIX_.'customer`
					WHERE `newsletter` = 0
					AND `email` = \''.pSQL($email).'\'';
        }

        return Db::getInstance()->getValue($sql);
    }

    /**
     * Ends the registration process to the newsletter
     *
     * @param string $token
     *
     * @return string
     */
    public function confirmEmail($token)
    {
        $errors = array();
        $activated = false;

        if ($email = $this->getGuestEmailByToken($token)) {
            $activated = $this->activateGuest($email);
        } elseif ($email = $this->getUserEmailByToken($token)) {
            $activated = $this->registerUser($email);
        }

        if (!$activated) {
            $errors[] = $this->l('This email is already registered and/or invalid.');
        }

        if (!count($errors)) {
            if ($discount = Configuration::get('NW_VOUCHER_CODE')) {
                $this->sendVoucher($email, $discount);
            }

            if (Configuration::get('NW_CONFIRMATION_EMAIL')) {
                $this->sendConfirmationEmail($email);
            }
        }

        return $errors;
    }

    /**
     * Send the confirmation mails to the given $email address if needed.
     *
     * @param string $email Email where to send the confirmation
     *
     * @note the email has been verified and might not yet been registered. Called by AuthController::processCustomerNewsletter
     *
     */
    public function confirmSubscription($email)
    {
        if ($email) {
            if ($discount = Configuration::get('NW_VOUCHER_CODE')) {
                $this->sendVoucher($email, $discount);
            }

            if (Configuration::get('NW_CONFIRMATION_EMAIL')) {
                $this->sendConfirmationEmail($email);
            }
        }
    }

    /**
     * Send an email containing a voucher code
     *
     * @param $email
     * @param $code
     *
     * @return bool|int
     */
    protected function sendVoucher($email, $code)
    {
        return Mail::Send($this->context->language->id, 'newsletter_voucher', Mail::l('Newsletter voucher', $this->context->language->id), array('{discount}' => $code), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
    }

    /**
     * Send a confirmation email
     *
     * @param string $email
     *
     * @return bool
     */
    protected function sendConfirmationEmail($email)
    {
        return Mail::Send($this->context->language->id, 'newsletter_conf', Mail::l('Newsletter confirmation', $this->context->language->id), array(), pSQL($email), null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
    }

    /**
     * Send a verification email
     *
     * @param string $email
     * @param string $token
     *
     * @return bool
     */
    protected function sendVerificationEmail($email, $token)
    {
        $verif_url = Context::getContext()->link->getModuleLink(
            'blocknewsletter', 'verification', array(
                'token' => $token,
            )
        );

        return Mail::Send($this->context->language->id, 'newsletter_verif', Mail::l('Email verification', $this->context->language->id), array('{verif_url}' => $verif_url), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    protected function _prepareHook($params)
    {
        if (!$this->hookPrepared) {
            $this->context->smarty->assign(array(
                'csrf_token' => $this->secure_key,
            ));

            Media::addJsDef(array(
                'url_newsletter_subscription' => $this->context->link->getModuleLink($this->name, 'subscription'),
            ));

            $this->hookPrepared = true;
        }
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->_prepareHook($params);

        $this->smarty->assign(array('id_module' => $this->id));

        return $this->display(__FILE__, 'blocknewsletter.tpl');
    }

    // by webkul
    public function hookDisplayFooterNotificationHook($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    // By webkul
    // public function hookFooter($params)
    // {
    // 	return $this->hookDisplayLeftColumn($params);
    // }

    public function hookdisplayMaintenance($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addJS(_PS_JS_DIR_.'validate.js');

        $this->context->controller->addCSS($this->_path.'blocknewsletter.css', 'all');
        $this->context->controller->addJS($this->_path.'blocknewsletter.js');
    }

    /**
     * Deletes duplicates email in newsletter table
     *
     * @param $params
     *
     * @return bool
     */
    public function hookActionCustomerAccountAdd($params)
    {
        //if e-mail of the created user address has already been added to the newsletter through the blocknewsletter module,
        //we delete it from blocknewsletter table to prevent duplicates
        $id_shop = $params['newCustomer']->id_shop;
        $email = $params['newCustomer']->email;
        if (Validate::isEmail($email)) {
            return (bool)Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'newsletter WHERE id_shop='.(int)$id_shop.' AND email=\''.pSQL($email)."'");
        }

        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }

    public function uninstall($keep = true)
    {
        if (!parent::uninstall()
            || ($keep && !Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'newsletter'))
            || !$this->uninstallTab()
        ) {
            return false;
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
