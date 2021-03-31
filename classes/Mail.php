<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

include_once(_PS_SWIFT_DIR_.'swift_required.php');

class MailCore extends ObjectModel
{
    public $id;

    /** @var string Recipient */
    public $recipient;

    /** @var string Template */
    public $template;

    /** @var string Subject */
    public $subject;

    /** @var int Language ID */
    public $id_lang;

    /** @var int Timestamp */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'mail',
        'primary' => 'id_mail',
        'fields' => array(
            'recipient' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'copy_post' => false, 'required' => true, 'size' => 126),
            'template' => array('type' => self::TYPE_STRING, 'validate' => 'isTplName', 'copy_post' => false, 'required' => true, 'size' => 62),
            'subject' => array('type' => self::TYPE_STRING, 'validate' => 'isMailSubject', 'copy_post' => false, 'required' => true, 'size' => 254),
            'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false, 'required' => true),
        ),
    );

    const TYPE_HTML = 1;
    const TYPE_TEXT = 2;
    const TYPE_BOTH = 3;

    /**
     * Send Email
     *
     * @param int $id_lang Language ID of the email (to translate the template)
     * @param string $template Template: the name of template not be a var but a string !
     * @param string $subject Subject of the email
     * @param string $template_vars Template variables for the email
     * @param string|array $to To email
     * @param string|array $to_name To name
     * @param string $from From email
     * @param string $from_name To email
     * @param array $file_attachment Array with three parameters (content, mime and name). You can use an array of array to attach multiple files
     * @param bool $mode_smtp SMTP mode (deprecated)
     * @param string $template_path Template path
     * @param bool $die Die after error
     * @param int $id_shop Shop ID
     * @param string|array $bcc Bcc recipient(s) (email address)
     * @param string $reply_to Email address for setting the Reply-To header
     * @return bool|int Whether sending was successful. If not at all, false, otherwise amount of recipients succeeded.
     */
    public static function Send($id_lang, $template, $subject, $template_vars, $to,
        $to_name = null, $from = null, $from_name = null, $file_attachment = null, $mode_smtp = null,
        $template_path = _PS_MAIL_DIR_, $die = false, $id_shop = null, $bcc = null, $reply_to = null)
    {
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }

        $configuration = Configuration::getMultiple(array(
            'PS_SHOP_EMAIL',
            'PS_MAIL_METHOD',
            'PS_MAIL_SERVER',
            'PS_MAIL_USER',
            'PS_MAIL_PASSWD',
            'PS_SHOP_NAME',
            'PS_MAIL_SMTP_ENCRYPTION',
            'PS_MAIL_SMTP_PORT',
            'PS_MAIL_TYPE'
        ), null, null, $id_shop);

        // Returns immediatly if emails are deactivated
        if ($configuration['PS_MAIL_METHOD'] == 3) {
            return true;
        }

        $theme_path = _PS_THEME_DIR_;

        // Get the path of theme by id_shop if exist
        if (is_numeric($id_shop) && $id_shop) {
            $shop = new Shop((int)$id_shop);
            $theme_name = $shop->getTheme();

            if (_THEME_NAME_ != $theme_name) {
                $theme_path = _PS_ROOT_DIR_.'/themes/'.$theme_name.'/';
            }
        }

        if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION']) || Tools::strtolower($configuration['PS_MAIL_SMTP_ENCRYPTION']) === 'off') {
            $configuration['PS_MAIL_SMTP_ENCRYPTION'] = false;
        }
        if (!isset($configuration['PS_MAIL_SMTP_PORT'])) {
            $configuration['PS_MAIL_SMTP_PORT'] = 'default';
        }

        // Sending an e-mail can be of vital importance for the merchant, when his password is lost for example, so we must not die but do our best to send the e-mail

        if (!isset($from) || !Validate::isEmail($from)) {
            $from = $configuration['PS_SHOP_EMAIL'];
        }

        if (!Validate::isEmail($from)) {
            $from = null;
        }

        // $from_name is not that important, no need to die if it is not valid
        if (!isset($from_name) || !Validate::isMailName($from_name)) {
            $from_name = $configuration['PS_SHOP_NAME'];
        }
        if (!Validate::isMailName($from_name)) {
            $from_name = null;
        }

        // It would be difficult to send an e-mail if the e-mail is not valid, so this time we can die if there is a problem
        if (!is_array($to) && !Validate::isEmail($to)) {
            Tools::dieOrLog(Tools::displayError('Error: parameter "to" is corrupted'), $die);
            return false;
        }

        // if bcc is not null, make sure it's a vaild e-mail
        if (!is_null($bcc) && !is_array($bcc) && !Validate::isEmail($bcc)) {
            Tools::dieOrLog(Tools::displayError('Error: parameter "bcc" is corrupted'), $die);
            $bcc = null;
        }

        if (!is_array($template_vars)) {
            $template_vars = array();
        }

        // Do not crash for this error, that may be a complicated customer name
        if (is_string($to_name) && !empty($to_name) && !Validate::isMailName($to_name)) {
            $to_name = null;
        }

        if (!Validate::isTplName($template)) {
            Tools::dieOrLog(Tools::displayError('Error: invalid e-mail template'), $die);
            return false;
        }

        if (!Validate::isMailSubject($subject)) {
            Tools::dieOrLog(Tools::displayError('Error: invalid e-mail subject'), $die);
            return false;
        }

        try {

            /* Construct multiple recipients list if needed */
            $message = Swift_Message::newInstance();
            if (is_array($to) && isset($to)) {
                foreach ($to as $key => $addr) {
                    $addr = trim($addr);
                    if (!Validate::isEmail($addr)) {
                        Tools::dieOrLog(Tools::displayError('Error: invalid e-mail address'), $die);
                        return false;
                    }

                    if (is_array($to_name) && isset($to_name[$key])) {
                        $addrName = $to_name[$key];
                    } else {
                        $addrName = $to_name;
                    }

                    $addrName = (($addrName == null || $addrName == $addr || !Validate::isGenericName($addrName)) ? '' : self::mimeEncode($addrName));
                    $message->addTo($addr, $addrName);
                }
                $to_plugin = $to[0];
            } else {
                /* Simple recipient, one address */
                $to_plugin = $to;
                $to_name = (($to_name == null || $to_name == $to) ? '' : self::mimeEncode($to_name));
                $message->addTo($to, $to_name);
            }

            if (isset($bcc) && is_array($bcc)) {
                foreach ($bcc as $addr) {
                    $addr = trim($addr);
                    if (!Validate::isEmail($addr)) {
                        Tools::dieOrLog(Tools::displayError('Error: invalid e-mail address'), $die);
                        return false;
                    }
                    $message->addBcc($addr);
                }
            } elseif (isset($bcc)) {
                $message->addBcc($bcc);
            }

            /* Connect with the appropriate configuration */
            if ($configuration['PS_MAIL_METHOD'] == 2) {
                if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT'])) {
                    Tools::dieOrLog(Tools::displayError('Error: invalid SMTP server or SMTP port'), $die);
                    return false;
                }

                $connection = Swift_SmtpTransport::newInstance($configuration['PS_MAIL_SERVER'], $configuration['PS_MAIL_SMTP_PORT'], $configuration['PS_MAIL_SMTP_ENCRYPTION'])
                    ->setUsername($configuration['PS_MAIL_USER'])
                    ->setPassword($configuration['PS_MAIL_PASSWD']);

            } else {
                $connection = Swift_MailTransport::newInstance();
            }

            if (!$connection) {
                return false;
            }
            $swift = Swift_Mailer::newInstance($connection);
            /* Get templates content */
            $iso = Language::getIsoById((int)$id_lang);
            if (!$iso) {
                Tools::dieOrLog(Tools::displayError('Error - No ISO code for email'), $die);
                return false;
            }
            $iso_template = $iso.'/'.$template;

            $module_name = false;
            $override_mail = false;

            // get templatePath
            if (preg_match('#'.$shop->physical_uri.'modules/#', str_replace(DIRECTORY_SEPARATOR, '/', $template_path)) && preg_match('#modules/([a-z0-9_-]+)/#ui', str_replace(DIRECTORY_SEPARATOR, '/', $template_path), $res)) {
                $module_name = $res[1];
            }

            if ($module_name !== false && (file_exists($theme_path.'modules/'.$module_name.'/mails/'.$iso_template.'.txt') ||
                    file_exists($theme_path.'modules/'.$module_name.'/mails/'.$iso_template.'.html'))) {
                $template_path = $theme_path.'modules/'.$module_name.'/mails/';
            } elseif (file_exists($theme_path.'mails/'.$iso_template.'.txt') || file_exists($theme_path.'mails/'.$iso_template.'.html')) {
                $template_path = $theme_path.'mails/';
                $override_mail  = true;
            }
            if (!file_exists($template_path.$iso_template.'.txt') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT)) {
                Tools::dieOrLog(Tools::displayError('Error - The following e-mail template is missing:').' '.$template_path.$iso_template.'.txt', $die);
                return false;
            } elseif (!file_exists($template_path.$iso_template.'.html') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML)) {
                Tools::dieOrLog(Tools::displayError('Error - The following e-mail template is missing:').' '.$template_path.$iso_template.'.html', $die);
                return false;
            }
            $template_html = '';
            $template_txt = '';
            Hook::exec('actionEmailAddBeforeContent', array(
                'template' => $template,
                'template_html' => &$template_html,
                'template_txt' => &$template_txt,
                'id_lang' => (int)$id_lang
            ), null, true);
            $template_html .= Tools::file_get_contents($template_path.$iso_template.'.html');
            $template_txt .= strip_tags(html_entity_decode(Tools::file_get_contents($template_path.$iso_template.'.txt'), null, 'utf-8'));
            Hook::exec('actionEmailAddAfterContent', array(
                'template' => $template,
                'template_html' => &$template_html,
                'template_txt' => &$template_txt,
                'id_lang' => (int)$id_lang
            ), null, true);
            if ($override_mail && file_exists($template_path.$iso.'/lang.php')) {
                include_once($template_path.$iso.'/lang.php');
            } elseif ($module_name && file_exists($theme_path.'mails/'.$iso.'/lang.php')) {
                include_once($theme_path.'mails/'.$iso.'/lang.php');
            } elseif (file_exists(_PS_MAIL_DIR_.$iso.'/lang.php')) {
                include_once(_PS_MAIL_DIR_.$iso.'/lang.php');
            } else {
                Tools::dieOrLog(Tools::displayError('Error - The language file is missing for:').' '.$iso, $die);
                return false;
            }

            /* Create mail and attach differents parts */
            $subject = '['.Configuration::get('PS_SHOP_NAME', null, null, $id_shop).'] '.$subject;
            $message->setSubject($subject);

            $message->setCharset('utf-8');

            /* Set Message-ID - getmypid() is blocked on some hosting */
            $message->setId(Mail::generateId());

            if (!($reply_to && Validate::isEmail($reply_to))) {
                $reply_to = $from;
            }

            if (isset($reply_to) && $reply_to) {
                $message->setReplyTo($reply_to);
            }

            $template_vars = array_map(array('Tools', 'htmlentitiesDecodeUTF8'), $template_vars);
            $template_vars = array_map(array('Tools', 'stripslashes'), $template_vars);
            // by webkul to get logo from media link.
            if (Configuration::get('PS_LOGO_MAIL') !== false && (bool)Tools::file_get_contents(Context::getContext()->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO_MAIL', null, null, $id_shop)))) {
                $logo = Context::getContext()->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO_MAIL', null, null, $id_shop));
            } else {
                if ((bool)Tools::file_get_contents(Context::getContext()->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO', null, null, $id_shop)))) {
                    $logo = Context::getContext()->link->getMediaLink(_PS_IMG_.Configuration::get('PS_LOGO', null, null, $id_shop));
                } else {
                    $template_vars['{shop_logo}'] = '';
                }
            }
            ShopUrl::cacheMainDomainForShop((int)$id_shop);
            /* don't attach the logo as */

            if (isset($logo)) {
                $template_vars['{shop_logo}'] = $message->embed(Swift_Image::fromPath($logo));
            }

            if ((Context::getContext()->link instanceof Link) === false) {
                Context::getContext()->link = new Link();
            }

            $template_vars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
            $template_vars['{shop_url}'] = Context::getContext()->link->getPageLink('index', true, $id_lang, null, false, $id_shop);
            $template_vars['{my_account_url}'] = Context::getContext()->link->getPageLink('my-account', true, $id_lang, null, false, $id_shop);
            $template_vars['{guest_tracking_url}'] = Context::getContext()->link->getPageLink('guest-tracking', true, $id_lang, null, false, $id_shop);
            $template_vars['{history_url}'] = Context::getContext()->link->getPageLink('history', true, $id_lang, null, false, $id_shop);
            $template_vars['{color}'] = Tools::safeOutput(Configuration::get('PS_MAIL_COLOR', null, null, $id_shop));
            // Get extra template_vars
            $extra_template_vars = array();
            Hook::exec('actionGetExtraMailTemplateVars', array(
                'template' => $template,
                'template_vars' => $template_vars,
                'extra_template_vars' => &$extra_template_vars,
                'id_lang' => (int)$id_lang
            ), null, true);
            $template_vars = array_merge($template_vars, $extra_template_vars);
            $swift->registerPlugin(new Swift_Plugins_DecoratorPlugin(array($to_plugin => $template_vars)));
            if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT) {
                $message->addPart($template_txt, 'text/plain', 'utf-8');
            }
            if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML) {
                $message->addPart($template_html, 'text/html', 'utf-8');
            }
            if ($file_attachment && !empty($file_attachment)) {
                // Multiple attachments?
                if (!is_array(current($file_attachment))) {
                    $file_attachment = array($file_attachment);
                }

                foreach ($file_attachment as $attachment) {
                    if (isset($attachment['content']) && isset($attachment['name']) && isset($attachment['mime'])) {
                        $message->attach(Swift_Attachment::newInstance()->setFilename($attachment['name'])->setContentType($attachment['mime'])->setBody($attachment['content']));
                    }
                }
            }
            /* Send mail */
            $message->setFrom(array($from => $from_name));
            $send = $swift->send($message);

            ShopUrl::resetMainDomainCache();

            if ($send && Configuration::get('PS_LOG_EMAILS')) {
                $mail = new Mail();
                $mail->template = Tools::substr($template, 0, 62);
                $mail->subject = Tools::substr($subject, 0, 254);
                $mail->id_lang = (int)$id_lang;
                $recipients_to = $message->getTo();
                $recipients_cc = $message->getCc();
                $recipients_bcc = $message->getBcc();
                if (!is_array($recipients_to)) {
                    $recipients_to = array();
                }
                if (!is_array($recipients_cc)) {
                    $recipients_cc = array();
                }
                if (!is_array($recipients_bcc)) {
                    $recipients_bcc = array();
                }
                foreach (array_merge($recipients_to, $recipients_cc, $recipients_bcc) as $email => $recipient_name) {
                    /** @var Swift_Address $recipient */
                    $mail->id = null;
                    $mail->recipient = Tools::substr($email, 0, 126);
                    $mail->add();
                }
            }

            return $send;
        } catch (Swift_SwiftException $e) {
            PrestaShopLogger::addLog(
                'Swift Error: '.$e->getMessage(),
                3,
                null,
                'Swift_Message',
                null,
                true
            );

            return false;
        }
    }

    /**
     * @param $id_mail Mail ID
     * @return bool Whether removal succeeded
     */
    public static function eraseLog($id_mail)
    {
        return Db::getInstance()->delete('mail', 'id_mail = '.(int)$id_mail);
    }

    /**
     * @return bool
     */
    public static function eraseAllLogs()
    {
        return Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'mail');
    }

    /**
     * Send a test email
     *
     * @param bool $smtp_checked Is SMTP checked?
     * @param string $smtp_server SMTP Server hostname
     * @param string $content Content of the email
     * @param string $subject Subject of the email
     * @param bool $type Deprecated
     * @param string $to To email address
     * @param string $from From email address
     * @param string $smtp_login SMTP login name
     * @param string $smtp_password SMTP password
     * @param int $smtp_port SMTP Port
     * @param bool|string $smtp_encryption Encryption type. "off" or false disable encryption.
     * @return bool|string True if succeeded, otherwise the error message
     */
    public static function sendMailTest($smtp_checked, $smtp_server, $content, $subject, $type, $to, $from, $smtp_login, $smtp_password, $smtp_port = 25, $smtp_encryption)
    {
        $result = false;
        try {
            if ($smtp_checked) {
                if (Tools::strtolower($smtp_encryption) === 'off') {
                    $smtp_encryption = false;
                }
                $smtp = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port, $smtp_encryption)
                    ->setUsername($smtp_login)
                    ->setPassword($smtp_password);
                $swift = Swift_Mailer::newInstance($smtp);
            } else {
                $swift = Swift_Mailer::newInstance(Swift_MailTransport::newInstance());
            }

            $message = Swift_Message::newInstance();

            $message
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setBody($content);

            if ($swift->send($message)) {
                $result = true;
            }
        } catch (Swift_SwiftException $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    /**
     * This method is used to get the translation for email Object.
     * For an object is forbidden to use htmlentities,
     * we have to return a sentence with accents.
     *
     * @param string $string raw sentence (write directly in file)
     * @return mixed
     */
    public static function l($string, $id_lang = null, Context $context = null)
    {
        global $_LANGMAIL;

        if (!$context) {
            $context = Context::getContext();
        }
        if ($id_lang == null) {
            $id_lang = (!isset($context->language) || !is_object($context->language)) ? (int)Configuration::get('PS_LANG_DEFAULT') : (int)$context->language->id;
        }
        $iso_code = Language::getIsoById((int)$id_lang);

        $file_core = _PS_ROOT_DIR_.'/mails/'.$iso_code.'/lang.php';
        if (Tools::file_exists_cache($file_core) && empty($_LANGMAIL)) {
            include($file_core);
        }

        $file_theme = _PS_THEME_DIR_.'mails/'.$iso_code.'/lang.php';
        if (Tools::file_exists_cache($file_theme)) {
            include($file_theme);
        }

        if (!is_array($_LANGMAIL)) {
            return (str_replace('"', '&quot;', $string));
        }

        $key = str_replace('\'', '\\\'', $string);
        return str_replace('"', '&quot;', Tools::stripslashes((array_key_exists($key, $_LANGMAIL) && !empty($_LANGMAIL[$key])) ? $_LANGMAIL[$key] : $string));
    }

    /* Rewrite of Swift_Message::generateId() without getmypid() */
    protected static function generateId($idstring = null)
    {
        $midparams = array(
            'utctime' => gmstrftime('%Y%m%d%H%M%S'),
            'randint' => mt_rand(),
            'customstr' => (preg_match("/^(?<!\\.)[a-z0-9\\.]+(?!\\.)\$/iD", $idstring) ? $idstring : "swift") ,
            'hostname' => ((isset($_SERVER['SERVER_NAME']) && !empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : php_uname('n')),
        );
        return vsprintf("%s.%d.%s@%s", $midparams);
    }

    /**
     * Check if a multibyte character set is used for the data
     *
     * @param string $data Data
     * @return bool Whether the string uses a multibyte character set
     */
    public static function isMultibyte($data)
    {
        $length = Tools::strlen($data);
        for ($i = 0; $i < $length; $i++) {
            if (ord(($data[$i])) > 128) {
                return true;
            }
        }
        return false;
    }

    /**
     * MIME encode the string
     *
     * @param string $string The string to encode
     * @param string $charset The character set to use
     * @param string $newline The newline character(s)
     * @return mixed|string MIME encoded string
     */
    public static function mimeEncode($string, $charset = 'UTF-8', $newline = "\r\n")
    {
        if (!self::isMultibyte($string) && Tools::strlen($string) < 75) {
            return $string;
        }

        $charset = Tools::strtoupper($charset);
        $start = '=?'.$charset.'?B?';
        $end = '?=';
        $sep = $end.$newline.' '.$start;
        $length = 75 - Tools::strlen($start) - Tools::strlen($end);
        $length = $length - ($length % 4);

        if ($charset === 'UTF-8') {
            $parts = array();
            $maxchars = floor(($length * 3) / 4);
            $stringLength = Tools::strlen($string);

            while ($stringLength > $maxchars) {
                $i = (int)$maxchars;
                $result = ord($string[$i]);

                while ($result >= 128 && $result <= 191) {
                    $result = ord($string[--$i]);
                }

                $parts[] = base64_encode(Tools::substr($string, 0, $i));
                $string = Tools::substr($string, $i);
                $stringLength = Tools::strlen($string);
            }

            $parts[] = base64_encode($string);
            $string = implode($sep, $parts);
        } else {
            $string = chunk_split(base64_encode($string), $length, $sep);
            $string = preg_replace('/'.preg_quote($sep).'$/', '', $string);
        }

        return $start.$string.$end;
    }

    public function getEmailTemplateContent($templateName, $mailType, $list)
    {
        $emailConfiguration = Configuration::get('PS_MAIL_TYPE');
        if ($emailConfiguration != $mailType && $emailConfiguration != Mail::TYPE_BOTH) {
            return '';
        }
        $context = Context::getContext();
        $pathToFindEmail = array(
            _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.$context->language->iso_code.DIRECTORY_SEPARATOR.$templateName.'.tpl',
            _PS_THEME_DIR_.'mails'.DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR.$templateName.'.tpl',
            _PS_MAIL_DIR_.$context->language->iso_code.DIRECTORY_SEPARATOR.$templateName.'.tpl',
            _PS_MAIL_DIR_.'en'.DIRECTORY_SEPARATOR.$templateName.'.tpl',
        );

        foreach ($pathToFindEmail as $path) {
            if (Tools::file_exists_cache($path)) {
                $context->smarty->assign('list', $list);
                return $context->smarty->fetch($path);
            }
        }

        return '';
    }
}
