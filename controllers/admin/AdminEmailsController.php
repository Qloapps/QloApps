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
 * @property Mail $object
 */
class AdminEmailsControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;

        if (Configuration::get('PS_LOG_EMAILS')) {
            $this->table = 'mail';
            $this->className = 'Mail';

            $this->lang = false;
            $this->noLink = true;
            $this->list_no_link = true;
            $this->explicitSelect = true;
            $this->addRowAction('delete');

            $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'confirm' => $this->l('Delete selected items?'),
                    'icon' => 'icon-trash'
                )
            );

            foreach (Language::getLanguages() as $language) {
                $languages[$language['id_lang']] = $language['name'];
            }

            $this->fields_list = array(
                'id_mail' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
                'recipient' => array('title' => $this->l('Recipient')),
                'template' => array('title' => $this->l('Template')),
                'language' => array(
                    'title' => $this->l('Language'),
                    'type' => 'select',
                    'color' => 'color',
                    'list' => $languages,
                    'filter_key' => 'a!id_lang',
                    'filter_type' => 'int',
                    'order_key' => 'language'
                ),
                'subject' => array('title' => $this->l('Subject')),
                'date_add' => array(
                    'title' => $this->l('Sent'),
                    'type' => 'datetime',
                )
            );
            $this->_select .= 'l.name as language';
            $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'lang l ON (a.id_lang = l.id_lang)';
            $this->_use_found_rows = false;
        }

        parent::__construct();

        foreach (Contact::getContacts($this->context->language->id) as $contact) {
            $arr[] = array('email_message' => $contact['id_contact'], 'name' => $contact['name']);
        }

        $this->fields_options = array(
            'email' => array(
                'title' => $this->l('Email'),
                'icon' => 'icon-envelope',
                'fields' =>    array(
                    'PS_MAIL_EMAIL_MESSAGE' => array(
                        'title' => $this->l('Send email to'),
                        'desc' => $this->l('Where customers send messages from the order page.'),
                        'validation' => 'isUnsignedId',
                        'type' => 'select',
                        'cast' => 'intval',
                        'identifier' => 'email_message',
                        'list' => $arr
                    ),
                    'PS_MAIL_METHOD' => array(
                        'title' => '',
                        'validation' => 'isGenericName',
                        'type' => 'radio',
                        'required' => true,
                        'choices' => array(
                            3 => $this->l('Never send emails (may be useful for testing purposes)'),
                            2 => $this->l('Set my own SMTP parameters (for advanced users ONLY)')
                        )
                    ),
                    'PS_MAIL_TYPE' => array(
                        'title' => '',
                        'validation' => 'isGenericName',
                        'type' => 'radio',
                        'required' => true,
                        'choices' => array(
                            Mail::TYPE_HTML => $this->l('Send email in HTML format'),
                            Mail::TYPE_TEXT => $this->l('Send email in text format'),
                            Mail::TYPE_BOTH => $this->l('Both')
                        )
                    ),
                    'PS_LOG_EMAILS' => array(
                        'title' => $this->l('Log Emails'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'smtp' => array(
                'title' => $this->l('Email'),
                'fields' =>    array(
                    'PS_MAIL_DOMAIN' => array(
                        'title' => $this->l('Mail domain name'),
                        'hint' => $this->l('Fully qualified domain name (keep this field empty if you don\'t know).'),
                        'empty' => true, 'validation' =>
                        'isUrl',
                        'type' => 'text',
                    ),
                    'PS_MAIL_SERVER' => array(
                        'title' => $this->l('SMTP server'),
                        'hint' => $this->l('IP address or server name (e.g. smtp.mydomain.com).'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                    ),
                    'PS_MAIL_USER' => array(
                        'title' => $this->l('SMTP username'),
                        'hint' => $this->l('Leave blank if not applicable.'),
                        'validation' => 'isGenericName',
                        'type' => 'text',
                    ),
                    'PS_MAIL_PASSWD' => array(
                        'title' => $this->l('SMTP password'),
                        'hint' => $this->l('Leave blank if not applicable.'),
                        'validation' => 'isAnything',
                        'type' => 'password',
                        'autocomplete' => false
                    ),
                    'PS_MAIL_SMTP_ENCRYPTION' => array(
                        'title' => $this->l('Encryption'),
                        'hint' => $this->l('Use an encrypt protocol'),
                        'desc' => extension_loaded('openssl') ? '' : '/!\\ '.$this->l('SSL does not seem to be available on your server.'),
                        'type' => 'select',
                        'cast' => 'strval',
                        'identifier' => 'mode',
                        'list' => array(
                            array(
                                'mode' => 'off',
                                'name' => $this->l('None')
                            ),
                            array(
                                'mode' => 'tls',
                                'name' => $this->l('TLS')
                            ),
                            array(
                                'mode' => 'ssl',
                                'name' => $this->l('SSL')
                            )
                        ),
                    ),
                    'PS_MAIL_SMTP_PORT' => array(
                        'title' => $this->l('Port'),
                        'hint' => $this->l('Port number to use.'),
                        'validation' => 'isInt',
                        'type' => 'text',
                        'cast' => 'intval',
                        'class' => 'fixed-width-sm'
                    ),
                ),
                'submit' => array('title' => $this->l('Save'))
            ),
            'test' => array(
                'title' =>    $this->l('Test your email configuration'),
                'hide_multishop_checkbox' => true,
                'fields' =>    array(
                    'PS_SHOP_EMAIL' => array(
                        'title' => $this->l('Send a test email to'),
                        'type' => 'text',
                        'id' => 'testEmail',
                        'no_multishop_checkbox' => true
                    ),
                ),
                'bottom' => '<div class="row"><div class="col-lg-9 col-lg-offset-3">
					<div class="alert" id="mailResultCheck" style="display:none;"></div>
				</div></div>',
                'buttons' => array(
                    array('title' => $this->l('Send a test email'),
                        'icon' => 'process-icon-envelope',
                        'name' => 'btEmailTest',
                        'js' => 'verifyMail()',
                        'class' => 'btn btn-default pull-right'
                    )
                )
            )
        );

        if (!defined('_PS_HOST_MODE_')) {
            $this->fields_options['email']['fields']['PS_MAIL_METHOD']['choices'][1] =
                $this->l('Use PHP\'s mail() function (recommended; works in most cases)');
        }

        ksort($this->fields_options['email']['fields']['PS_MAIL_METHOD']['choices']);
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addJs(_PS_JS_DIR_.'/admin/email.js');

        Media::addJsDefL('textMsg', $this->l('This is a test message. Your server is now configured to send email.', null, true, false));
        Media::addJsDefL('textSubject', $this->l('Test message -- QloApps', null, true, false));
        Media::addJsDefL('textSendOk', $this->l('A test email has been sent to the email address you provided.', null, true, false));
        Media::addJsDefL('textSendError', $this->l('Error: Please check your configuration', null, true, false));
        Media::addJsDefL('token_mail', $this->token);
        Media::addJsDefL('errorMail', $this->l('This email address is not valid', null, true, false));
    }

    public function processDelete()
    {
        if ((int)$id_mail = Tools::getValue('id_mail', 0)) {
            $return = Mail::eraseLog((int)$id_mail);
        } else {
            $return = Mail::eraseAllLogs();
        }

        return $return;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->toolbar_btn['delete'] = array(
            'short' => 'Erase',
            'desc' => $this->l('Erase all'),
            'js' => 'if (confirm(\''.$this->l('Are you sure?').'\')) document.location = \''.Tools::safeOutput($this->context->link->getAdminLink('AdminEmails')).'&amp;token='.$this->token.'&amp;deletemail=1\';'
        );
        unset($this->toolbar_btn['new']);
    }

    public function updateOptionPsMailPasswd($value)
    {
        if (Tools::getValue('PS_MAIL_PASSWD') == '' && Configuration::get('PS_MAIL_PASSWD')) {
            return true;
        } else {
            Configuration::updateValue('PS_MAIL_PASSWD', Tools::getValue('PS_MAIL_PASSWD'));
        }
    }

    /**
     * AdminController::initContent() override
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->addToolBarModulesListButton();
        unset($this->toolbar_btn['save']);
        $back = $this->context->link->getAdminLink('AdminDashboard');

        $this->toolbar_btn['back'] = array(
            'href' => $back,
            'desc' => $this->l('Back to the dashboard')
        );

        // $this->content .= $this->renderOptions();

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));

        return parent::initContent();
    }

    public function beforeUpdateOptions()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }
        /* PrestaShop demo mode*/

        // We don't want to update the shop e-mail when sending test e-mails
        if (isset($_POST['PS_SHOP_EMAIL'])) {
            $_POST['PS_SHOP_EMAIL'] = Configuration::get('PS_SHOP_EMAIL');
        }

        if (isset($_POST['PS_MAIL_METHOD']) && $_POST['PS_MAIL_METHOD'] == 2
            && (empty($_POST['PS_MAIL_SERVER']) || empty($_POST['PS_MAIL_SMTP_PORT']))) {
            $this->errors[] = Tools::displayError('You must define an SMTP server and an SMTP port. If you do not know it, use the PHP mail() function instead.');
        }
    }

    public function ajaxProcessSendMailTest()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            die(Tools::displayError('This functionality has been disabled.'));
        }
        /* PrestaShop demo mode */
        if ($this->tabAccess['view'] === '1') {
            $smtpChecked = (trim(Tools::getValue('mailMethod')) == 'smtp');
            $smtpServer = Tools::getValue('smtpSrv');
            $content = html_entity_decode(urldecode(Tools::getValue('testMsg')));
            $subject = html_entity_decode(urldecode(Tools::getValue('testSubject')));
            $type = 'text/html';
            $to = Tools::getValue('testEmail');
            $from = Configuration::get('PS_SHOP_EMAIL');
            $smtpLogin = Tools::getValue('smtpLogin');
            $smtpPassword = Tools::getValue('smtpPassword');
            $smtpPassword = (!empty($smtpPassword)) ? urldecode($smtpPassword) : Configuration::get('PS_MAIL_PASSWD');
            $smtpPassword = str_replace(
                array('&lt;', '&gt;', '&quot;', '&amp;'),
                array('<', '>', '"', '&'),
                Tools::htmlentitiesUTF8($smtpPassword)
            );

            $smtpPort = Tools::getValue('smtpPort');
            $smtpEncryption = Tools::getValue('smtpEnc');

            $result = Mail::sendMailTest(Tools::htmlentitiesUTF8($smtpChecked), Tools::htmlentitiesUTF8($smtpServer), $content, $subject, Tools::htmlentitiesUTF8($type), Tools::htmlentitiesUTF8($to), Tools::htmlentitiesUTF8($from), Tools::htmlentitiesUTF8($smtpLogin), $smtpPassword, Tools::htmlentitiesUTF8($smtpPort), Tools::htmlentitiesUTF8($smtpEncryption));
            die($result === true ? 'ok' : $result);
        }
    }
}
