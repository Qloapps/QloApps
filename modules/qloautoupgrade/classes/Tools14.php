<?php

/**
 * 2007-2016 PrestaShop.
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2016 PrestaShop SA
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade;

use Tab;

class Tools14
{
    protected static $file_exists_cache = array();
    protected static $_forceCompile;
    protected static $_caching;

    /**
     * Random password generator.
     *
     * @param int $length Desired length (optional)
     *
     * @return string Password
     */
    public static function passwdGen($length = 8)
    {
        $str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0, $passwd = ''; $i < $length; ++$i) {
            $passwd .= self::substr($str, mt_rand(0, self::strlen($str) - 1), 1);
        }

        return $passwd;
    }

    /**
     * Redirect user to another page.
     *
     * @param string $url Desired URL
     * @param string $baseUri Base URI (optional)
     */
    public static function redirect($url, $baseUri = __PS_BASE_URI__)
    {
        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
            global $link;
            if (strpos($url, $baseUri) !== false && strpos($url, $baseUri) == 0) {
                $url = substr($url, strlen($baseUri));
            }
            $explode = explode('?', $url, 2);
            // don't use ssl if url is home page
            // used when logout for example
            $useSSL = !empty($url);
            $url = $link->getPageLink($explode[0], $useSSL);
            if (isset($explode[1])) {
                $url .= '?' . $explode[1];
            }
            $baseUri = '';
        }

        if (isset($_SERVER['HTTP_REFERER']) && ($url == $_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: ' . $baseUri . $url);
        }
        exit;
    }

    /**
     * Redirect url wich allready PS_BASE_URI.
     *
     * @param string $url Desired URL
     */
    public static function redirectLink($url)
    {
        if (!preg_match('@^https?://@i', $url)) {
            global $link;
            if (strpos($url, __PS_BASE_URI__) !== false && strpos($url, __PS_BASE_URI__) == 0) {
                $url = substr($url, strlen(__PS_BASE_URI__));
            }
            $explode = explode('?', $url, 2);
            $url = $link->getPageLink($explode[0]);
            if (isset($explode[1])) {
                $url .= '?' . $explode[1];
            }
        }

        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect user to another admin page.
     *
     * @param string $url Desired URL
     */
    public static function redirectAdmin($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * getProtocol return the set protocol according to configuration (http[s]).
     *
     * @param bool true if require ssl
     *
     * @return string (http|https)
     */
    public static function getProtocol($use_ssl = null)
    {
        return null !== $use_ssl && $use_ssl ? 'https://' : 'http://';
    }

    /**
     * getHttpHost return the <b>current</b> host used, with the protocol (http or https) if $http is true
     * This function should not be used to choose http or https domain name.
     * Use Tools14::getShopDomain() or Tools14::getShopDomainSsl instead.
     *
     * @param bool $http
     * @param bool $entities
     *
     * @return string host
     */
    public static function getHttpHost($http = false, $entities = false)
    {
        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
        if ($entities) {
            $host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $host;
        }

        return $host;
    }

    /**
     * getShopDomain returns domain name according to configuration and ignoring ssl.
     *
     * @param bool $http if true, return domain name with protocol
     * @param bool $entities if true,
     *
     * @return string domain
     */
    public static function getShopDomain($http = false, $entities = false)
    {
        if (!($domain = Configuration::get('PS_SHOP_DOMAIN'))) {
            $domain = self::getHttpHost();
        }
        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $domain = 'http://' . $domain;
        }

        return $domain;
    }

    /**
     * getShopDomainSsl returns domain name according to configuration and depending on ssl activation.
     *
     * @param bool $http if true, return domain name with protocol
     * @param bool $entities if true,
     *
     * @return string domain
     */
    public static function getShopDomainSsl($http = false, $entities = false)
    {
        if (!($domain = Configuration::get('PS_SHOP_DOMAIN_SSL'))) {
            $domain = self::getHttpHost();
        }
        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }
        if ($http) {
            $domain = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $domain;
        }

        return $domain;
    }

    /**
     * Get the server variable SERVER_NAME.
     *
     * @return string server name
     */
    public static function getServerName()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER']) {
            return $_SERVER['HTTP_X_FORWARDED_SERVER'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Get the server variable REMOTE_ADDR, or the first ip of HTTP_X_FORWARDED_FOR (when using proxy).
     *
     * @return string $remote_addr ip of client
     */
    public static function getRemoteAddr()
    {
        // This condition is necessary when using CDN, don't remove it.
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR']) || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR'])))) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                return $ips[0];
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Check if the current page use SSL connection on not.
     *
     * @return bool uses SSL
     */
    public static function usingSecureMode()
    {
        if (isset($_SERVER['HTTPS'])) {
            return $_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) == 'on';
        }
        // $_SERVER['SSL'] exists only in some specific configuration
        if (isset($_SERVER['SSL'])) {
            return $_SERVER['SSL'] == 1 || strtolower($_SERVER['SSL']) == 'on';
        }

        return false;
    }

    /**
     * Get the current url prefix protocol (https/http).
     *
     * @return string protocol
     */
    public static function getCurrentUrlProtocolPrefix()
    {
        if (self::usingSecureMode()) {
            return 'https://';
        } else {
            return 'http://';
        }
    }

    /**
     * Secure an URL referrer.
     *
     * @param string $referrer URL referrer
     *
     * @return secured referrer
     */
    public static function secureReferrer($referrer)
    {
        if (preg_match('/^http[s]?:\/\/' . self::getServerName() . '(:' . _PS_SSL_PORT_ . ')?\/.*$/Ui', $referrer)) {
            return $referrer;
        }

        return __PS_BASE_URI__;
    }

    /**
     * Get a value from $_POST / $_GET
     * if unavailable, take a default value.
     *
     * @param string $key Value key
     * @param mixed $defaultValue (optional)
     *
     * @return mixed Value
     */
    public static function getValue($key, $defaultValue = false)
    {
        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }
        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));

        if (is_string($ret) === true) {
            $ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
        }

        return !is_string($ret) ? $ret : stripslashes($ret);
    }

    public static function getIsset($key)
    {
        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }

        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    /**
     * Change language in cookie while clicking on a flag.
     *
     * @return string iso code
     */
    public static function setCookieLanguage()
    {
        global $cookie;

        /* If language does not exist or is disabled, erase it */
        if ($cookie->id_lang) {
            $lang = new Language((int) $cookie->id_lang);
            if (!Validate::isLoadedObject($lang) || !$lang->active) {
                $cookie->id_lang = null;
            }
        }

        /* Automatically detect language if not already defined */
        if (!$cookie->id_lang && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $array = explode(',', self::strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            if (self::strlen($array[0]) > 2) {
                $tab = explode('-', $array[0]);
                $string = $tab[0];
            } else {
                $string = $array[0];
            }
            if (Validate::isLanguageIsoCode($string)) {
                $lang = new Language((int) (Language::getIdByIso($string)));
                if (Validate::isLoadedObject($lang) && $lang->active) {
                    $cookie->id_lang = (int) ($lang->id);
                }
            }
        }

        /* If language file not present, you must use default language file */
        if (!$cookie->id_lang || !Validate::isUnsignedId($cookie->id_lang)) {
            $cookie->id_lang = (int) (Configuration::get('PS_LANG_DEFAULT'));
        }

        $iso = Language::getIsoById((int) $cookie->id_lang);
        @include_once _PS_THEME_DIR_ . 'lang/' . $iso . '.php';

        return $iso;
    }

    /**
     * Set cookie id_lang.
     */
    public static function switchLanguage()
    {
        global $cookie;

        if ($id_lang = (int) (self::getValue('id_lang')) && Validate::isUnsignedId($id_lang)) {
            $cookie->id_lang = $id_lang;
        }
    }

    /**
     * Set cookie currency from POST or default currency.
     *
     * @return Currency object
     */
    public static function setCurrency()
    {
        global $cookie;

        if (self::isSubmit('SubmitCurrency')) {
            if (isset($_POST['id_currency']) && is_numeric($_POST['id_currency'])) {
                $currency = Currency::getCurrencyInstance((int) ($_POST['id_currency']));
                if (is_object($currency) && $currency->id && !$currency->deleted) {
                    $cookie->id_currency = (int) ($currency->id);
                }
            }
        }

        if ((int) $cookie->id_currency) {
            $currency = Currency::getCurrencyInstance((int) $cookie->id_currency);
            if (is_object($currency) && (int) $currency->id && (int) $currency->deleted != 1 && $currency->active) {
                return $currency;
            }
        }
        $currency = Currency::getCurrencyInstance((int) (Configuration::get('PS_CURRENCY_DEFAULT')));
        if (is_object($currency) && $currency->id) {
            $cookie->id_currency = (int) ($currency->id);
        }

        return $currency;
    }

    /**
     * Return price with currency sign for a given product.
     *
     * @param float $price Product price
     * @param object $currency Current currency (object, id_currency, NULL => getCurrent())
     *
     * @return string Price correctly formated (sign, decimal separator...)
     */
    public static function displayPrice($price, $currency = null, $no_utf8 = false)
    {
        if ($currency === null) {
            $currency = Currency::getCurrent();
        }
        /* if you modified this function, don't forget to modify the Javascript function formatCurrency (in tools.js) */
        if (is_int($currency)) {
            $currency = Currency::getCurrencyInstance((int) ($currency));
        }
        $c_char = (is_array($currency) ? $currency['sign'] : $currency->sign);
        $c_format = (is_array($currency) ? $currency['format'] : $currency->format);
        $c_decimals = (is_array($currency) ? (int) ($currency['decimals']) : (int) ($currency->decimals)) * _PS_PRICE_DISPLAY_PRECISION_;
        $c_blank = (is_array($currency) ? $currency['blank'] : $currency->blank);
        $blank = ($c_blank ? ' ' : '');
        $ret = 0;
        if (($isNegative = ($price < 0))) {
            $price *= -1;
        }
        $price = self::ps_round($price, $c_decimals);
        switch ($c_format) {
            /* X 0,000.00 */
            case 1:
                $ret = $c_char . $blank . number_format($price, $c_decimals, '.', ',');
                break;
            /* 0 000,00 X*/
            case 2:
                $ret = number_format($price, $c_decimals, ',', ' ') . $blank . $c_char;
                break;
            /* X 0.000,00 */
            case 3:
                $ret = $c_char . $blank . number_format($price, $c_decimals, ',', '.');
                break;
            /* 0,000.00 X */
            case 4:
                $ret = number_format($price, $c_decimals, '.', ',') . $blank . $c_char;
                break;
        }
        if ($isNegative) {
            $ret = '-' . $ret;
        }
        if ($no_utf8) {
            return str_replace('€', chr(128), $ret);
        }

        return $ret;
    }

    public static function displayPriceSmarty($params, &$smarty)
    {
        if (array_key_exists('currency', $params)) {
            $currency = Currency::getCurrencyInstance((int) ($params['currency']));
            if (Validate::isLoadedObject($currency)) {
                return self::displayPrice($params['price'], $currency, false);
            }
        }

        return self::displayPrice($params['price']);
    }

    /**
     * Return price converted.
     *
     * @param float $price Product price
     * @param object $currency Current currency object
     * @param bool $to_currency convert to currency or from currency to default currency
     */
    public static function convertPrice($price, $currency = null, $to_currency = true)
    {
        if ($currency === null) {
            $currency = Currency::getCurrent();
        } elseif (is_numeric($currency)) {
            $currency = Currency::getCurrencyInstance($currency);
        }

        $c_id = (is_array($currency) ? $currency['id_currency'] : $currency->id);
        $c_rate = (is_array($currency) ? $currency['conversion_rate'] : $currency->conversion_rate);

        if ($c_id != (int) (Configuration::get('PS_CURRENCY_DEFAULT'))) {
            if ($to_currency) {
                $price *= $c_rate;
            } else {
                $price /= $c_rate;
            }
        }

        return $price;
    }

    /**
     * Display date regarding to language preferences.
     *
     * @param array $params Date, format...
     * @param object $smarty Smarty object for language preferences
     *
     * @return string Date
     */
    public static function dateFormat($params, &$smarty)
    {
        return self::displayDate($params['date'], $smarty->ps_language->id, (isset($params['full']) ? $params['full'] : false));
    }

    /**
     * Display date regarding to language preferences.
     *
     * @param string $date Date to display format UNIX
     * @param int $id_lang Language id
     * @param bool $full With time or not (optional)
     *
     * @return string Date
     */
    public static function displayDate($date, $id_lang, $full = false, $separator = '-')
    {
        if (!$date || !($time = strtotime($date))) {
            return $date;
        }
        if (!Validate::isDate($date) || !Validate::isBool($full)) {
            die(self::displayError('Invalid date'));
        }

        $language = Language::getLanguage((int) $id_lang);

        return date($full ? $language['date_format_full'] : $language['date_format_lite'], $time);
    }

    /**
     * Sanitize a string.
     *
     * @param string $string String to sanitize
     * @param bool $full String contains HTML or not (optional)
     *
     * @return string Sanitized string
     */
    public static function safeOutput($string, $html = false)
    {
        if (!$html) {
            $string = strip_tags($string);
        }

        return @self::htmlentitiesUTF8($string, ENT_QUOTES);
    }

    public static function htmlentitiesUTF8($string, $type = ENT_QUOTES)
    {
        if (is_array($string)) {
            return array_map(array('Tools', 'htmlentitiesUTF8'), $string);
        }

        return htmlentities($string, $type, 'utf-8');
    }

    public static function htmlentitiesDecodeUTF8($string)
    {
        if (is_array($string)) {
            return array_map(array('Tools', 'htmlentitiesDecodeUTF8'), $string);
        }

        return html_entity_decode($string, ENT_QUOTES, 'utf-8');
    }

    public static function safePostVars()
    {
        $_POST = array_map(array('Tools', 'htmlentitiesUTF8'), $_POST);
    }

    /**
     * Delete directory and subdirectories.
     *
     * @param string $dirname Directory name
     */
    public static function deleteDirectory($dirname, $delete_self = true)
    {
        $dirname = rtrim($dirname, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (file_exists($dirname)) {
            if ($files = scandir($dirname)) {
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..' && $file != '.svn') {
                        if (is_file($dirname . $file)) {
                            unlink($dirname . $file);
                        } elseif (is_dir($dirname . $file . DIRECTORY_SEPARATOR)) {
                            self::deleteDirectory($dirname . $file . DIRECTORY_SEPARATOR, true);
                        }
                    }
                }
                if ($delete_self && file_exists($dirname)) {
                    if (!rmdir($dirname)) {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Display an error according to an error code.
     *
     * @param string $string Error message
     * @param bool $htmlentities By default at true for parsing error message with htmlentities
     */
    public static function displayError($string = 'Fatal error', $htmlentities = true)
    {
        return $string;
        global $_ERRORS, $cookie;

        $iso = strtolower(Language::getIsoById((is_object($cookie) && $cookie->id_lang) ? (int) $cookie->id_lang : (int) Configuration::get('PS_LANG_DEFAULT')));
        @include_once _PS_TRANSLATIONS_DIR_ . $iso . '/errors.php';

        if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $string == 'Fatal error') {
            return '<pre>' . print_r(debug_backtrace(), true) . '</pre>';
        }
        if (!is_array($_ERRORS)) {
            return str_replace('"', '&quot;', $string);
        }
        $key = md5(str_replace('\'', '\\\'', $string));
        $str = (isset($_ERRORS) && is_array($_ERRORS) && key_exists($key, $_ERRORS)) ? ($htmlentities ? htmlentities($_ERRORS[$key], ENT_COMPAT, 'UTF-8') : $_ERRORS[$key]) : $string;

        return str_replace('"', '&quot;', stripslashes($str));
    }

    /**
     * Display an error with detailed object.
     *
     * @param mixed $object
     * @param bool $kill
     *
     * @return $object if $kill = false;
     */
    public static function dieObject($object, $kill = true)
    {
        echo '<pre style="text-align: left;">';
        print_r($object);
        echo '</pre><br />';
        if ($kill) {
            die('END');
        }

        return $object;
    }

    /**
     * ALIAS OF dieObject() - Display an error with detailed object.
     *
     * @param object $object Object to display
     */
    public static function d($object, $kill = true)
    {
        return self::dieObject($object, $kill = true);
    }

    /**
     * ALIAS OF dieObject() - Display an error with detailed object but don't stop the execution.
     *
     * @param object $object Object to display
     */
    public static function p($object)
    {
        return self::dieObject($object, false);
    }

    /**
     * Check if submit has been posted.
     *
     * @param string $submit submit name
     */
    public static function isSubmit($submit)
    {
        return
            isset($_POST[$submit]) || isset($_POST[$submit . '_x']) || isset($_POST[$submit . '_y'])
            || isset($_GET[$submit]) || isset($_GET[$submit . '_x']) || isset($_GET[$submit . '_y'])
        ;
    }

    /**
     * Get meta tages for a given page.
     *
     * @param int $id_lang Language id
     *
     * @return array Meta tags
     */
    public static function getMetaTags($id_lang, $page_name)
    {
        global $maintenance;

        if (!(isset($maintenance) && (!in_array(self::getRemoteAddr(), explode(',', Configuration::get('PS_MAINTENANCE_IP')))))) {
            /* Products specifics meta tags */
            if ($id_product = self::getValue('id_product')) {
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`, `description_short`
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = p.`id_product`)
            WHERE pl.id_lang = ' . (int) ($id_lang) . ' AND pl.id_product = ' . (int) ($id_product) . ' AND p.active = 1');
                if ($row) {
                    if (empty($row['meta_description'])) {
                        $row['meta_description'] = strip_tags($row['description_short']);
                    }

                    return self::completeMetaTags($row, $row['name']);
                }
            }

            /* Categories specifics meta tags */
            elseif ($id_category = self::getValue('id_category')) {
                $page_number = (int) self::getValue('p');
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`, `description`
            FROM `' . _DB_PREFIX_ . 'category_lang`
            WHERE id_lang = ' . (int) ($id_lang) . ' AND id_category = ' . (int) ($id_category));
                if ($row) {
                    if (empty($row['meta_description'])) {
                        $row['meta_description'] = strip_tags($row['description']);
                    }

                    // Paginate title
                    if (!empty($row['meta_title'])) {
                        $row['meta_title'] = $row['meta_title'] . (!empty($page_number) ? ' (' . $page_number . ')' : '') . ' - ' . Configuration::get('PS_SHOP_NAME');
                    } else {
                        $row['meta_title'] = $row['name'] . (!empty($page_number) ? ' (' . $page_number . ')' : '') . ' - ' . Configuration::get('PS_SHOP_NAME');
                    }

                    return self::completeMetaTags($row, $row['name']);
                }
            }

            /* Manufacturers specifics meta tags */
            elseif ($id_manufacturer = self::getValue('id_manufacturer')) {
                $page_number = (int) self::getValue('p');
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`
            FROM `' . _DB_PREFIX_ . 'manufacturer_lang` ml
            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (ml.`id_manufacturer` = m.`id_manufacturer`)
            WHERE ml.id_lang = ' . (int) ($id_lang) . ' AND ml.id_manufacturer = ' . (int) ($id_manufacturer));
                if ($row) {
                    if (empty($row['meta_description'])) {
                        $row['meta_description'] = strip_tags($row['meta_description']);
                    }
                    $row['meta_title'] .= $row['name'] . (!empty($page_number) ? ' (' . $page_number . ')' : '');
                    $row['meta_title'] .= ' - ' . Configuration::get('PS_SHOP_NAME');

                    return self::completeMetaTags($row, $row['meta_title']);
                }
            }

            /* Suppliers specifics meta tags */
            elseif ($id_supplier = self::getValue('id_supplier')) {
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT `name`, `meta_title`, `meta_description`, `meta_keywords`
            FROM `' . _DB_PREFIX_ . 'supplier_lang` sl
            LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (sl.`id_supplier` = s.`id_supplier`)
            WHERE sl.id_lang = ' . (int) ($id_lang) . ' AND sl.id_supplier = ' . (int) ($id_supplier));

                if ($row) {
                    if (empty($row['meta_description'])) {
                        $row['meta_description'] = strip_tags($row['meta_description']);
                    }
                    if (!empty($row['meta_title'])) {
                        $row['meta_title'] = $row['meta_title'] . ' - ' . Configuration::get('PS_SHOP_NAME');
                    }

                    return self::completeMetaTags($row, $row['name']);
                }
            }

            /* CMS specifics meta tags */
            elseif ($id_cms = self::getValue('id_cms')) {
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT `meta_title`, `meta_description`, `meta_keywords`
            FROM `' . _DB_PREFIX_ . 'cms_lang`
            WHERE id_lang = ' . (int) ($id_lang) . ' AND id_cms = ' . (int) ($id_cms));
                if ($row) {
                    $row['meta_title'] = $row['meta_title'] . ' - ' . Configuration::get('PS_SHOP_NAME');

                    return self::completeMetaTags($row, $row['meta_title']);
                }
            }

            /* CMS category specifics meta tags */
            elseif ($id_cms = self::getValue('id_cms_category')) {
                $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
            SELECT `meta_title`, `meta_description`, `meta_keywords`
            FROM `' . _DB_PREFIX_ . 'cms_category_lang`
            WHERE id_lang = ' . (int) ($id_lang) . ' AND id_cms_category = ' . (int) ($id_cms));
                if ($row) {
                    $row['meta_title'] = $row['meta_title'] . ' - ' . Configuration::get('PS_SHOP_NAME');

                    return self::completeMetaTags($row, $row['meta_title']);
                }
            }
        }

        /* Default meta tags */
        return self::getHomeMetaTags($id_lang, $page_name);
    }

    /**
     * Get meta tags for a given page.
     *
     * @param int $id_lang Language id
     *
     * @return array Meta tags
     */
    public static function getHomeMetaTags($id_lang, $page_name)
    {
        /* Metas-tags */
        $metas = Meta::getMetaByPage($page_name, $id_lang);
        $ret['meta_title'] = (isset($metas['title']) && $metas['title']) ? $metas['title'] . ' - ' . Configuration::get('PS_SHOP_NAME') : Configuration::get('PS_SHOP_NAME');
        $ret['meta_description'] = (isset($metas['description']) && $metas['description']) ? $metas['description'] : '';
        $ret['meta_keywords'] = (isset($metas['keywords']) && $metas['keywords']) ? $metas['keywords'] : '';

        return $ret;
    }

    public static function completeMetaTags($metaTags, $defaultValue)
    {
        global $cookie;

        if (empty($metaTags['meta_title'])) {
            $metaTags['meta_title'] = $defaultValue . ' - ' . Configuration::get('PS_SHOP_NAME');
        }
        if (empty($metaTags['meta_description'])) {
            $metaTags['meta_description'] = Configuration::get('PS_META_DESCRIPTION', (int) ($cookie->id_lang)) ? Configuration::get('PS_META_DESCRIPTION', (int) ($cookie->id_lang)) : '';
        }
        if (empty($metaTags['meta_keywords'])) {
            $metaTags['meta_keywords'] = Configuration::get('PS_META_KEYWORDS', (int) ($cookie->id_lang)) ? Configuration::get('PS_META_KEYWORDS', (int) ($cookie->id_lang)) : '';
        }

        return $metaTags;
    }

    /**
     * Encrypt password.
     *
     * @param object $object Object to display
     */
    public static function encrypt($passwd)
    {
        return md5(pSQL(_COOKIE_KEY_ . $passwd));
    }

    /**
     * Get token to prevent CSRF.
     *
     * @param string $token token to encrypt
     */
    public static function getToken($page = true)
    {
        global $cookie;
        if ($page === true) {
            return self::encrypt($cookie->id_customer . $cookie->passwd . $_SERVER['SCRIPT_NAME']);
        } else {
            return self::encrypt($cookie->id_customer . $cookie->passwd . $page);
        }
    }

    /**
     * Encrypt password.
     *
     * @param object $object Object to display
     */
    public static function getAdminToken($string)
    {
        return !empty($string) ? self::encrypt($string) : false;
    }

    public static function getAdminTokenLite($tab)
    {
        global $cookie;

        return self::getAdminToken($tab . (int) Tab::getIdFromClassName($tab) . (int) $cookie->id_employee);
    }

    /**
     * Get the user's journey.
     *
     * @param int $id_category Category ID
     * @param string $path Path end
     * @param bool $linkOntheLastItem Put or not a link on the current category
     * @param string [optionnal] $categoryType defined what type of categories is used (products or cms)
     */
    public static function getPath($id_category, $path = '', $linkOntheLastItem = false, $categoryType = 'products')
    {
        global $link, $cookie;

        if ($id_category == 1) {
            return '<span class="navigation_end">' . $path . '</span>';
        }

        $pipe = Configuration::get('PS_NAVIGATION_PIPE');
        if (empty($pipe)) {
            $pipe = '>';
        }

        $fullPath = '';

        if ($categoryType === 'products') {
            $category = Db::getInstance()->getRow('
        SELECT id_category, level_depth, nleft, nright
        FROM ' . _DB_PREFIX_ . 'category
        WHERE id_category = ' . (int) $id_category);

            if (isset($category['id_category'])) {
                $categories = Db::getInstance()->ExecuteS('
            SELECT c.id_category, cl.name, cl.link_rewrite
            FROM ' . _DB_PREFIX_ . 'category c
            LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category = c.id_category)
            WHERE c.nleft <= ' . (int) $category['nleft'] . ' AND c.nright >= ' . (int) $category['nright'] . ' AND cl.id_lang = ' . (int) ($cookie->id_lang) . ' AND c.id_category != 1
            ORDER BY c.level_depth ASC
            LIMIT ' . (int) $category['level_depth']);

                $n = 1;
                $nCategories = (int) sizeof($categories);
                foreach ($categories as $category) {
                    $fullPath .=
                        (($n < $nCategories || $linkOntheLastItem) ? '<a href="' . self::safeOutput($link->getCategoryLink((int) $category['id_category'], $category['link_rewrite'])) . '" title="' . htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8') . '">' : '') .
                        htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8') .
                        (($n < $nCategories || $linkOntheLastItem) ? '</a>' : '') .
                        (($n++ != $nCategories || !empty($path)) ? '<span class="navigation-pipe">' . $pipe . '</span>' : '');
                }

                return $fullPath . $path;
            }
        } elseif ($categoryType === 'CMS') {
            $category = new CMSCategory((int) ($id_category), (int) ($cookie->id_lang));
            if (!Validate::isLoadedObject($category)) {
                die(self::displayError());
            }
            $categoryLink = $link->getCMSCategoryLink($category);

            if ($path != $category->name) {
                $fullPath .= '<a href="' . self::safeOutput($categoryLink) . '">' . htmlentities($category->name, ENT_NOQUOTES, 'UTF-8') . '</a><span class="navigation-pipe">' . $pipe . '</span>' . $path;
            } else {
                $fullPath = ($linkOntheLastItem ? '<a href="' . self::safeOutput($categoryLink) . '">' : '') . htmlentities($path, ENT_NOQUOTES, 'UTF-8') . ($linkOntheLastItem ? '</a>' : '');
            }

            return self::getPath((int) ($category->id_parent), $fullPath, $linkOntheLastItem, $categoryType);
        }
    }

    /**
     * @param string [optionnal] $type_cat defined what type of categories is used (products or cms)
     */
    public static function getFullPath($id_category, $end, $type_cat = 'products')
    {
        global $cookie;

        $pipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');

        if ($type_cat === 'products') {
            $category = new Category((int) ($id_category), (int) ($cookie->id_lang));
        } elseif ($type_cat === 'CMS') {
            $category = new CMSCategory((int) ($id_category), (int) ($cookie->id_lang));
        }

        if (!Validate::isLoadedObject($category)) {
            $id_category = 1;
        }
        if ($id_category == 1) {
            return htmlentities($end, ENT_NOQUOTES, 'UTF-8');
        }

        return self::getPath($id_category, $category->name, true, $type_cat) . '<span class="navigation-pipe">' . $pipe . '</span> <span class="navigation_product">' . htmlentities($end, ENT_NOQUOTES, 'UTF-8') . '</span>';
    }

    /**
     * @deprecated
     */
    public static function getCategoriesTotal()
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT COUNT(`id_category`) AS total FROM `' . _DB_PREFIX_ . 'category`');

        return (int) ($row['total']);
    }

    /**
     * @deprecated
     */
    public static function getProductsTotal()
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT COUNT(`id_product`) AS total FROM `' . _DB_PREFIX_ . 'product`');

        return (int) ($row['total']);
    }

    /**
     * @deprecated
     */
    public static function getCustomersTotal()
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT COUNT(`id_customer`) AS total FROM `' . _DB_PREFIX_ . 'customer`');

        return (int) ($row['total']);
    }

    /**
     * @deprecated
     */
    public static function getOrdersTotal()
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT COUNT(`id_order`) AS total FROM `' . _DB_PREFIX_ . 'orders`');

        return (int) ($row['total']);
    }

    /*
    ** Historyc translation function kept for compatibility
    ** Removing soon
    */
    public static function historyc_l($key, $translations)
    {
        global $cookie;
        if (!$translations || !is_array($translations)) {
            die(self::displayError());
        }
        $iso = strtoupper(Language::getIsoById($cookie->id_lang));
        $lang = key_exists($iso, $translations) ? $translations[$iso] : false;

        return ($lang && is_array($lang) && key_exists($key, $lang)) ? stripslashes($lang[$key]) : $key;
    }

    /**
     * Return the friendly url from the provided string.
     *
     * @param string $str
     * @param bool $utf8_decode => needs to be marked as deprecated
     *
     * @return string
     */
    public static function link_rewrite($str, $utf8_decode = false)
    {
        return self::str2url($str);
    }

    /**
     * Return a friendly url made from the provided string
     * If the mbstring library is available, the output is the same as the js function of the same name.
     *
     * @param string $str
     *
     * @return string
     */
    public static function str2url($str)
    {
        if (function_exists('mb_strtolower')) {
            $str = mb_strtolower($str, 'utf-8');
        }

        $str = trim($str);
        $str = self::replaceAccentedChars($str);

        // Remove all non-whitelist chars.
        $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]-]/', '', $str);
        $str = preg_replace('/[\s\'\:\/\[\]-]+/', ' ', $str);
        $str = preg_replace('/[ ]/', '-', $str);
        $str = preg_replace('/[\/]/', '-', $str);

        // If it was not possible to lowercase the string with mb_strtolower, we do it after the transformations.
        // This way we lose fewer special chars.
        if (!function_exists('mb_strtolower')) {
            $str = strtolower($str);
        }

        return $str;
    }

    /**
     * Replace all accented chars by their equivalent non accented chars.
     *
     * @param string $str
     *
     * @return string
     */
    public static function replaceAccentedChars($str)
    {
        $str = preg_replace('/[\x{0105}\x{0104}\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}]/u', 'a', $str);
        $str = preg_replace('/[\x{00E7}\x{010D}\x{0107}\x{0106}]/u', 'c', $str);
        $str = preg_replace('/[\x{010F}]/u', 'd', $str);
        $str = preg_replace('/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{011B}\x{0119}\x{0118}]/u', 'e', $str);
        $str = preg_replace('/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}]/u', 'i', $str);
        $str = preg_replace('/[\x{0142}\x{0141}\x{013E}\x{013A}]/u', 'l', $str);
        $str = preg_replace('/[\x{00F1}\x{0148}]/u', 'n', $str);
        $str = preg_replace('/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{00D3}]/u', 'o', $str);
        $str = preg_replace('/[\x{0159}\x{0155}]/u', 'r', $str);
        $str = preg_replace('/[\x{015B}\x{015A}\x{0161}]/u', 's', $str);
        $str = preg_replace('/[\x{00DF}]/u', 'ss', $str);
        $str = preg_replace('/[\x{0165}]/u', 't', $str);
        $str = preg_replace('/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{016F}]/u', 'u', $str);
        $str = preg_replace('/[\x{00FD}\x{00FF}]/u', 'y', $str);
        $str = preg_replace('/[\x{017C}\x{017A}\x{017B}\x{0179}\x{017E}]/u', 'z', $str);
        $str = preg_replace('/[\x{00E6}]/u', 'ae', $str);
        $str = preg_replace('/[\x{0153}]/u', 'oe', $str);

        return $str;
    }

    /**
     * Truncate strings.
     *
     * @param string $str
     * @param int $maxLen Max length
     * @param string $suffix Suffix optional
     *
     * @return string $str truncated
     */
    /* CAUTION : Use it only on module hookEvents.
    ** For other purposes use the smarty function instead */
    public static function truncate($str, $maxLen, $suffix = '...')
    {
        if (self::strlen($str) <= $maxLen) {
            return $str;
        }
        $str = utf8_decode($str);

        return utf8_encode(substr($str, 0, $maxLen - self::strlen($suffix)) . $suffix);
    }

    /**
     * Generate date form.
     *
     * @param int $year Year to select
     * @param int $month Month to select
     * @param int $day Day to select
     *
     * @return array $tab html data with 3 cells :['days'], ['months'], ['years']
     */
    public static function dateYears()
    {
        $tab = array();
        for ($i = date('Y'); $i >= 1900; --$i) {
            $tab[] = $i;
        }

        return $tab;
    }

    public static function dateDays()
    {
        for ($i = 1; $i != 32; ++$i) {
            $tab[] = $i;
        }

        return $tab;
    }

    public static function dateMonths()
    {
        for ($i = 1; $i != 13; ++$i) {
            $tab[$i] = date('F', mktime(0, 0, 0, $i, date('m'), date('Y')));
        }

        return $tab;
    }

    public static function hourGenerate($hours, $minutes, $seconds)
    {
        return implode(':', array($hours, $minutes, $seconds));
    }

    public static function dateFrom($date)
    {
        $tab = explode(' ', $date);
        if (!isset($tab[1])) {
            $date .= ' ' . self::hourGenerate(0, 0, 0);
        }

        return $date;
    }

    public static function dateTo($date)
    {
        $tab = explode(' ', $date);
        if (!isset($tab[1])) {
            $date .= ' ' . self::hourGenerate(23, 59, 59);
        }

        return $date;
    }

    /**
     * @deprecated
     */
    public static function getExactTime()
    {
        return time() + microtime();
    }

    public static function strtolower($str)
    {
        if (is_array($str)) {
            return false;
        }
        if (function_exists('mb_strtolower')) {
            return mb_strtolower($str, 'utf-8');
        }

        return strtolower($str);
    }

    public static function strlen($str, $encoding = 'UTF-8')
    {
        if (is_array($str)) {
            return false;
        }
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, $encoding);
        }

        return strlen($str);
    }

    public static function stripslashes($string)
    {
        if (_PS_MAGIC_QUOTES_GPC_) {
            $string = stripslashes($string);
        }

        return $string;
    }

    public static function strtoupper($str)
    {
        if (is_array($str)) {
            return false;
        }
        if (function_exists('mb_strtoupper')) {
            return mb_strtoupper($str, 'utf-8');
        }

        return strtoupper($str);
    }

    public static function substr($str, $start, $length = false, $encoding = 'utf-8')
    {
        if (is_array($str)) {
            return false;
        }
        if (function_exists('mb_substr')) {
            return mb_substr($str, (int) ($start), ($length === false ? self::strlen($str) : (int) ($length)), $encoding);
        }

        return substr($str, $start, ($length === false ? self::strlen($str) : (int) ($length)));
    }

    public static function ucfirst($str)
    {
        return self::strtoupper(self::substr($str, 0, 1)) . self::substr($str, 1);
    }

    public static function orderbyPrice(&$array, $orderWay)
    {
        foreach ($array as &$row) {
            $row['price_tmp'] = Product::getPriceStatic($row['id_product'], true, ((isset($row['id_product_attribute']) && !empty($row['id_product_attribute'])) ? (int) ($row['id_product_attribute']) : null), 2);
        }
        if (strtolower($orderWay) == 'desc') {
            uasort($array, 'cmpPriceDesc');
        } else {
            uasort($array, 'cmpPriceAsc');
        }
        foreach ($array as &$row) {
            unset($row['price_tmp']);
        }
    }

    public static function iconv($from, $to, $string)
    {
        if (function_exists('iconv')) {
            return iconv($from, $to . '//TRANSLIT', str_replace('¥', '&yen;', str_replace('£', '&pound;', str_replace('€', '&euro;', $string))));
        }

        return html_entity_decode(htmlentities($string, ENT_NOQUOTES, $from), ENT_NOQUOTES, $to);
    }

    public static function isEmpty($field)
    {
        return $field === '' || $field === null;
    }

    /**
     * @deprecated
     **/
    public static function getTimezones($select = false)
    {
        static $_cache = 0;

        // One select
        if ($select) {
            // No cache
            if (!$_cache) {
                $tmz = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `name` FROM ' . _DB_PREFIX_ . 'timezone WHERE id_timezone = ' . (int) ($select));
                $_cache = $tmz['name'];
            }

            return $_cache;
        }

        // Multiple select
        $tmz = Db::getInstance(_PS_USE_SQL_SLAVE_)->s('SELECT * FROM ' . _DB_PREFIX_ . 'timezone');
        $tab = array();
        foreach ($tmz as $timezone) {
            $tab[$timezone['id_timezone']] = str_replace('_', ' ', $timezone['name']);
        }

        return $tab;
    }

    /**
     * @deprecated
     **/
    public static function ps_set_magic_quotes_runtime($var)
    {
        if (function_exists('set_magic_quotes_runtime')) {
            set_magic_quotes_runtime($var);
        }
    }

    public static function ps_round($value, $precision = 0)
    {
        $method = (int) (Configuration::get('PS_PRICE_ROUND_MODE'));
        if ($method == PS_ROUND_UP) {
            return self::ceilf($value, $precision);
        } elseif ($method == PS_ROUND_DOWN) {
            return self::floorf($value, $precision);
        }

        return round($value, $precision);
    }

    public static function ceilf($value, $precision = 0)
    {
        $precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
        $tmp = $value * $precisionFactor;
        $tmp2 = (string) $tmp;
        // If the current value has already the desired precision
        if (strpos($tmp2, '.') === false) {
            return $value;
        }
        if ($tmp2[strlen($tmp2) - 1] == 0) {
            return $value;
        }

        return ceil($tmp) / $precisionFactor;
    }

    public static function floorf($value, $precision = 0)
    {
        $precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
        $tmp = $value * $precisionFactor;
        $tmp2 = (string) $tmp;
        // If the current value has already the desired precision
        if (strpos($tmp2, '.') === false) {
            return $value;
        }
        if ($tmp2[strlen($tmp2) - 1] == 0) {
            return $value;
        }

        return floor($tmp) / $precisionFactor;
    }

    /**
     * file_exists() wrapper with cache to speedup performance.
     *
     * @param string $filename File name
     *
     * @return bool Cached result of file_exists($filename)
     */
    public static function file_exists_cache($filename)
    {
        if (!isset(self::$file_exists_cache[$filename])) {
            self::$file_exists_cache[$filename] = file_exists($filename);
        }

        return self::$file_exists_cache[$filename];
    }

    /**
     * Check config & source file to settle which dl method to use
     */
    public static function shouldUseFopen($url)
    {
        return in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url);
    }

    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 5)
    {
        if (!extension_loaded('openssl') && strpos('https://', $url) === true) {
            $url = str_replace('https', 'http', $url);
        }
        if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
            $stream_context = @stream_context_create(array('http' => array('timeout' => $curl_timeout, 'header' => "User-Agent:MyAgent/1.0\r\n")));
        }
        if (self::shouldUseFopen($url)) {
            $var = @file_get_contents($url, $use_include_path, $stream_context);

            /* PSCSX-3205 buffer output ? */
            if (self::getValue('ajaxMode') && ob_get_level() && ob_get_length() > 0) {
                ob_clean();
            }

            if ($var) {
                return $var;
            }
        } elseif (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $opts = stream_context_get_options($stream_context);
            if (isset($opts['http']['method']) && self::strtolower($opts['http']['method']) == 'post') {
                curl_setopt($curl, CURLOPT_POST, true);
                if (isset($opts['http']['content'])) {
                    parse_str($opts['http']['content'], $datas);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
                }
            }
            $content = curl_exec($curl);
            curl_close($curl);

            return $content;
        } else {
            return false;
        }
    }

    public static function simplexml_load_file($url, $class_name = null)
    {
        return @simplexml_load_string(self::file_get_contents($url), $class_name);
    }

    public static function minifyHTML($html_content)
    {
        if (strlen($html_content) > 0) {
            //set an alphabetical order for args
            $html_content = preg_replace_callback(
                '/(<[a-zA-Z0-9]+)((\s?[a-zA-Z0-9]+=[\"\\\'][^\"\\\']*[\"\\\']\s?)*)>/', array('Tools', 'minifyHTMLpregCallback'), $html_content);

            require_once _PS_TOOL_DIR_ . 'minify_html/minify_html.class.php';
            $html_content = str_replace(chr(194) . chr(160), '&nbsp;', $html_content);
            $html_content = Minify_HTML::minify($html_content, array('xhtml', 'cssMinifier', 'jsMinifier'));

            if (Configuration::get('PS_HIGH_HTML_THEME_COMPRESSION')) {
                //$html_content = preg_replace('/"([^\>\s"]*)"/i', '$1', $html_content);//FIXME create a js bug
                $html_content = preg_replace('/<!DOCTYPE \w[^\>]*dtd\">/is', '', $html_content);
                $html_content = preg_replace('/\s\>/is', '>', $html_content);
                $html_content = str_replace('</li>', '', $html_content);
                $html_content = str_replace('</dt>', '', $html_content);
                $html_content = str_replace('</dd>', '', $html_content);
                $html_content = str_replace('</head>', '', $html_content);
                $html_content = str_replace('<head>', '', $html_content);
                $html_content = str_replace('</html>', '', $html_content);
                $html_content = str_replace('</body>', '', $html_content);
                //$html_content = str_replace('</p>', '', $html_content);//FIXME doesnt work...
                $html_content = str_replace("</option>\n", '', $html_content); //TODO with bellow
                $html_content = str_replace('</option>', '', $html_content);
                $html_content = str_replace('<script type=text/javascript>', '<script>', $html_content); //Do a better expreg
                $html_content = str_replace("<script>\n", '<script>', $html_content); //Do a better expreg
            }

            return $html_content;
        }

        return false;
    }

    /**
     * Translates a string with underscores into camel case (e.g. first_name -> firstName).
     *
     * @prototype string public static function toCamelCase(string $str[, bool $capitaliseFirstChar = false])
     */
    public static function toCamelCase($str, $capitaliseFirstChar = false)
    {
        $str = strtolower($str);
        if ($capitaliseFirstChar) {
            $str = ucfirst($str);
        }

        return preg_replace_callback('/_([a-z])/', create_function('$c', 'return strtoupper($c[1]);'), $str);
    }

    public static function getBrightness($hex)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    }

    public static function minifyHTMLpregCallback($preg_matches)
    {
        $args = array();
        preg_match_all('/[a-zA-Z0-9]+=[\"\\\'][^\"\\\']*[\"\\\']/is', $preg_matches[2], $args);
        $args = $args[0];
        sort($args);
        $output = $preg_matches[1] . ' ' . implode(' ', $args) . '>';

        return $output;
    }

    public static function packJSinHTML($html_content)
    {
        if (strlen($html_content) > 0) {
            $htmlContentCopy = $html_content;
            $html_content = preg_replace_callback(
                '/\\s*(<script\\b[^>]*?>)([\\s\\S]*?)(<\\/script>)\\s*/i', array('Tools', 'packJSinHTMLpregCallback'), $html_content);

            // If the string is too big preg_replace return an error
            // In this case, we don't compress the content
            if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) {
                error_log('ERROR: PREG_BACKTRACK_LIMIT_ERROR in function packJSinHTML');

                return $htmlContentCopy;
            }

            return $html_content;
        }

        return false;
    }

    public static function packJSinHTMLpregCallback($preg_matches)
    {
        $preg_matches[1] = $preg_matches[1] . '/* <![CDATA[ */';
        $preg_matches[2] = self::packJS($preg_matches[2]);
        $preg_matches[count($preg_matches) - 1] = '/* ]]> */' . $preg_matches[count($preg_matches) - 1];
        unset($preg_matches[0]);
        $output = implode('', $preg_matches);

        return $output;
    }

    public static function packJS($js_content)
    {
        if (!empty($js_content)) {
            require_once _PS_TOOL_DIR_ . 'js_minify/jsmin.php';
            try {
                $js_content = JSMin::minify($js_content);
            } catch (Exception $e) {
                if (_PS_MODE_DEV_) {
                    echo $e->getMessage();
                }

                return $js_content;
            }
        }

        return $js_content;
    }

    public static function minifyCSS($css_content, $fileuri = false)
    {
        global $current_css_file;

        $current_css_file = $fileuri;
        if (strlen($css_content) > 0) {
            $css_content = preg_replace('#/\*.*?\*/#s', '', $css_content);
            $css_content = preg_replace_callback('#url\((?:\'|")?([^\)\'"]*)(?:\'|")?\)#s', array('Tools', 'replaceByAbsoluteURL'), $css_content);

            $css_content = preg_replace('#\s+#', ' ', $css_content);
            $css_content = str_replace("\t", '', $css_content);
            $css_content = str_replace("\n", '', $css_content);
            //$css_content = str_replace('}', "}\n", $css_content);

            $css_content = str_replace('; ', ';', $css_content);
            $css_content = str_replace(': ', ':', $css_content);
            $css_content = str_replace(' {', '{', $css_content);
            $css_content = str_replace('{ ', '{', $css_content);
            $css_content = str_replace(', ', ',', $css_content);
            $css_content = str_replace('} ', '}', $css_content);
            $css_content = str_replace(' }', '}', $css_content);
            $css_content = str_replace(';}', '}', $css_content);
            $css_content = str_replace(':0px', ':0', $css_content);
            $css_content = str_replace(' 0px', ' 0', $css_content);
            $css_content = str_replace(':0em', ':0', $css_content);
            $css_content = str_replace(' 0em', ' 0', $css_content);
            $css_content = str_replace(':0pt', ':0', $css_content);
            $css_content = str_replace(' 0pt', ' 0', $css_content);
            $css_content = str_replace(':0%', ':0', $css_content);
            $css_content = str_replace(' 0%', ' 0', $css_content);

            return trim($css_content);
        }

        return false;
    }

    public static function replaceByAbsoluteURL($matches)
    {
        global $current_css_file;

        $protocol_link = self::getCurrentUrlProtocolPrefix();

        if (array_key_exists(1, $matches)) {
            $tmp = dirname($current_css_file) . '/' . $matches[1];

            return 'url(\'' . $protocol_link . self::getMediaServer($tmp) . $tmp . '\')';
        }

        return false;
    }

    /**
     * addJS load a javascript file in the header.
     *
     * @param mixed $js_uri
     */
    public static function addJS($js_uri)
    {
        global $js_files;
        if (!isset($js_files)) {
            $js_files = array();
        }
        // avoid useless operation...
        if (in_array($js_uri, $js_files)) {
            return true;
        }

        // detect mass add
        if (!is_array($js_uri) && !in_array($js_uri, $js_files)) {
            $js_uri = array($js_uri);
        } else {
            foreach ($js_uri as $key => $js) {
                if (in_array($js, $js_files)) {
                    unset($js_uri[$key]);
                }
            }
        }

        //overriding of modules js files
        foreach ($js_uri as $key => &$file) {
            if (!preg_match('/^http(s?):\/\//i', $file)) {
                $different = 0;
                $override_path = str_replace(__PS_BASE_URI__ . 'modules/', _PS_ROOT_DIR_ . '/themes/' . _THEME_NAME_ . '/js/modules/', $file, $different);
                if ($different && file_exists($override_path)) {
                    $file = str_replace(__PS_BASE_URI__ . 'modules/', __PS_BASE_URI__ . 'themes/' . _THEME_NAME_ . '/js/modules/', $file, $different);
                } else {
                    // remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
                    $url_data = parse_url($file);
                    $file_uri = _PS_ROOT_DIR_ . self::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
                    // check if js files exists
                    if (!file_exists($file_uri)) {
                        unset($js_uri[$key]);
                    }
                }
            }
        }

        // adding file to the big array...
        $js_files = array_merge($js_files, $js_uri);

        return true;
    }

    /**
     * addCSS allows you to add stylesheet at any time.
     *
     * @param mixed $css_uri
     * @param string $css_media_type
     *
     * @return true
     */
    public static function addCSS($css_uri, $css_media_type = 'all')
    {
        global $css_files;

        if (is_array($css_uri)) {
            foreach ($css_uri as $file => $media_type) {
                self::addCSS($file, $media_type);
            }

            return true;
        }

        //overriding of modules css files
        $different = 0;
        $override_path = str_replace(__PS_BASE_URI__ . 'modules/', _PS_ROOT_DIR_ . '/themes/' . _THEME_NAME_ . '/css/modules/', $css_uri, $different);
        if ($different && file_exists($override_path)) {
            $css_uri = str_replace(__PS_BASE_URI__ . 'modules/', __PS_BASE_URI__ . 'themes/' . _THEME_NAME_ . '/css/modules/', $css_uri, $different);
        } else {
            // remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
            $url_data = parse_url($css_uri);
            $file_uri = _PS_ROOT_DIR_ . self::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);
            // check if css files exists
            if (!file_exists($file_uri)) {
                return true;
            }
        }

        // detect mass add
        $css_uri = array($css_uri => $css_media_type);

        // adding file to the big array...
        if (is_array($css_files)) {
            $css_files = array_merge($css_files, $css_uri);
        } else {
            $css_files = $css_uri;
        }

        return true;
    }

    /**
     * Combine Compress and Cache CSS (ccc) calls.
     */
    public static function cccCss()
    {
        global $css_files;
        //inits
        $css_files_by_media = array();
        $compressed_css_files = array();
        $compressed_css_files_not_found = array();
        $compressed_css_files_infos = array();
        $protocolLink = self::getCurrentUrlProtocolPrefix();

        // group css files by media
        foreach ($css_files as $filename => $media) {
            if (!array_key_exists($media, $css_files_by_media)) {
                $css_files_by_media[$media] = array();
            }

            $infos = array();
            $infos['uri'] = $filename;
            $url_data = parse_url($filename);
            $infos['path'] = _PS_ROOT_DIR_ . self::str_replace_once(__PS_BASE_URI__, '/', $url_data['path']);
            $css_files_by_media[$media]['files'][] = $infos;
            if (!array_key_exists('date', $css_files_by_media[$media])) {
                $css_files_by_media[$media]['date'] = 0;
            }
            $css_files_by_media[$media]['date'] = max(
                file_exists($infos['path']) ? @filemtime($infos['path']) : 0,
                $css_files_by_media[$media]['date']
            );

            if (!array_key_exists($media, $compressed_css_files_infos)) {
                $compressed_css_files_infos[$media] = array('key' => '');
            }
            $compressed_css_files_infos[$media]['key'] .= $filename;
        }

        // get compressed css file infos
        foreach ($compressed_css_files_infos as $media => &$info) {
            $key = md5($info['key'] . $protocolLink);
            $filename = _PS_THEME_DIR_ . 'cache/' . $key . '_' . $media . '.css';
            $info = array(
                'key' => $key,
                'date' => file_exists($filename) ? @filemtime($filename) : 0,
            );
        }
        // aggregate and compress css files content, write new caches files
        foreach ($css_files_by_media as $media => $media_infos) {
            $cache_filename = _PS_THEME_DIR_ . 'cache/' . $compressed_css_files_infos[$media]['key'] . '_' . $media . '.css';
            if ($media_infos['date'] > $compressed_css_files_infos[$media]['date']) {
                $compressed_css_files[$media] = '';
                foreach ($media_infos['files'] as $file_infos) {
                    if (file_exists($file_infos['path'])) {
                        $compressed_css_files[$media] .= self::minifyCSS(file_get_contents($file_infos['path']), $file_infos['uri']);
                    } else {
                        $compressed_css_files_not_found[] = $file_infos['path'];
                    }
                }
                if (!empty($compressed_css_files_not_found)) {
                    $content = '/* WARNING ! file(s) not found : "' .
                        implode(',', $compressed_css_files_not_found) .
                        '" */' . "\n" . $compressed_css_files[$media];
                } else {
                    $content = $compressed_css_files[$media];
                }
                file_put_contents($cache_filename, $content);
                chmod($cache_filename, 0777);
            }
            $compressed_css_files[$media] = $cache_filename;
        }

        // rebuild the original css_files array
        $css_files = array();
        foreach ($compressed_css_files as $media => $filename) {
            $url = str_replace(_PS_THEME_DIR_, _THEMES_DIR_ . _THEME_NAME_ . '/', $filename);
            $css_files[$protocolLink . self::getMediaServer($url) . $url] = $media;
        }
    }

    /**
     * Combine Compress and Cache (ccc) JS calls.
     */
    public static function cccJS()
    {
        global $js_files;
        //inits
        $compressed_js_files_not_found = array();
        $js_files_infos = array();
        $js_files_date = 0;
        $compressed_js_file_date = 0;
        $compressed_js_filename = '';
        $js_external_files = array();
        $protocolLink = self::getCurrentUrlProtocolPrefix();

        // get js files infos
        foreach ($js_files as $filename) {
            $expr = explode(':', $filename);

            if ($expr[0] == 'http') {
                $js_external_files[] = $filename;
            } else {
                $infos = array();
                $infos['uri'] = $filename;
                $url_data = parse_url($filename);
                $infos['path'] = _PS_ROOT_DIR_ . self::str_replace_once(__PS_BASE_URI__, '/', $url_data['path']);
                $js_files_infos[] = $infos;

                $js_files_date = max(
                    file_exists($infos['path']) ? @filemtime($infos['path']) : 0,
                    $js_files_date
                );
                $compressed_js_filename .= $filename;
            }
        }

        // get compressed js file infos
        $compressed_js_filename = md5($compressed_js_filename);

        $compressed_js_path = _PS_THEME_DIR_ . 'cache/' . $compressed_js_filename . '.js';
        $compressed_js_file_date = file_exists($compressed_js_path) ? @filemtime($compressed_js_path) : 0;

        // aggregate and compress js files content, write new caches files
        if ($js_files_date > $compressed_js_file_date) {
            $content = '';
            foreach ($js_files_infos as $file_infos) {
                if (file_exists($file_infos['path'])) {
                    $content .= self::file_get_contents($file_infos['path']) . ';';
                } else {
                    $compressed_js_files_not_found[] = $file_infos['path'];
                }
            }
            $content = self::packJS($content);

            if (!empty($compressed_js_files_not_found)) {
                $content = '/* WARNING ! file(s) not found : "' .
                    implode(',', $compressed_js_files_not_found) .
                    '" */' . "\n" . $content;
            }

            file_put_contents($compressed_js_path, $content);
            chmod($compressed_js_path, 0777);
        }

        // rebuild the original js_files array
        $url = str_replace(_PS_ROOT_DIR_ . '/', __PS_BASE_URI__, $compressed_js_path);
        $js_files = array_merge(array($protocolLink . self::getMediaServer($url) . $url), $js_external_files);
    }

    private static $_cache_nb_media_servers = null;

    public static function getMediaServer($filename)
    {
        if (self::$_cache_nb_media_servers === null) {
            if (_MEDIA_SERVER_1_ == '') {
                self::$_cache_nb_media_servers = 0;
            } else {
                self::$_cache_nb_media_servers = 3;
            }
        }

        if (self::$_cache_nb_media_servers && ($id_media_server = (abs(crc32($filename)) % self::$_cache_nb_media_servers + 1))) {
            return constant('_MEDIA_SERVER_' . $id_media_server . '_');
        }

        return self::getHttpHost();
    }

    public static function generateHtaccess($path, $rewrite_settings, $cache_control, $specific = '', $disableMuliviews = false)
    {
        $tab = array('ErrorDocument' => array(), 'RewriteEngine' => array(), 'RewriteRule' => array());
        $multilang = (Language::countActiveLanguages() > 1);

        // ErrorDocument
        $tab['ErrorDocument']['comment'] = '# Catch 404 errors';
        $tab['ErrorDocument']['content'] = '404 ' . __PS_BASE_URI__ . '404.php';

        // RewriteEngine
        $tab['RewriteEngine']['comment'] = '# URL rewriting module activation';

        // RewriteRules
        $tab['RewriteRule']['comment'] = '# URL rewriting rules';

        // Compatibility with the old image filesystem
        if (Configuration::get('PS_LEGACY_IMAGES')) {
            $tab['RewriteRule']['content']['^([a-z0-9]+)\-([a-z0-9]+)(\-[_a-zA-Z0-9-]*)/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1-$2$3.jpg [L]';
            $tab['RewriteRule']['content']['^([0-9]+)\-([0-9]+)/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1-$2.jpg [L]';
        }

        // Rewriting for product image id < 100 millions
        $tab['RewriteRule']['content']['^([0-9])(\-[_a-zA-Z0-9-]*)?/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1/$1$2.jpg [L]';
        $tab['RewriteRule']['content']['^([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1/$2/$1$2$3.jpg [L]';
        $tab['RewriteRule']['content']['^([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1/$2/$3/$1$2$3$4.jpg [L]';
        $tab['RewriteRule']['content']['^([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1/$2/$3/$4/$1$2$3$4$5.jpg [L]';
        $tab['RewriteRule']['content']['^([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1/$2/$3/$4/$5/$1$2$3$4$5$6.jpg [L]';
        $tab['RewriteRule']['content']['^([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1/$2/$3/$4/$5/$6/$1$2$3$4$5$6$7.jpg [L]';
        $tab['RewriteRule']['content']['^([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1/$2/$3/$4/$5/$6/$7/$1$2$3$4$5$6$7$8.jpg [L]';
        $tab['RewriteRule']['content']['^([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])([0-9])(\-[_a-zA-Z0-9-]*)?/[_a-zA-Z0-9-]*\.jpg$'] = _PS_PROD_IMG_ . '$1/$2/$3/$4/$5/$6/$7/$8/$1$2$3$4$5$6$7$8$9.jpg [L]';

        $tab['RewriteRule']['content']['^c/([0-9]+)(\-[_a-zA-Z0-9-]*)/[_a-zA-Z0-9-]*\.jpg$'] = 'img/c/$1$2.jpg [L]';
        $tab['RewriteRule']['content']['^c/([a-zA-Z-]+)/[a-zA-Z0-9-]+\.jpg$'] = 'img/c/$1.jpg [L]';

        if ($multilang) {
            $tab['RewriteRule']['content']['^([a-z]{2})/[a-zA-Z0-9-]*/([0-9]+)\-[a-zA-Z0-9-]*\.html'] = 'product.php?id_product=$2&isolang=$1 [QSA,L]';
            $tab['RewriteRule']['content']['^([a-z]{2})/([0-9]+)\-[a-zA-Z0-9-]*\.html'] = 'product.php?id_product=$2&isolang=$1 [QSA,L]';
            $tab['RewriteRule']['content']['^([a-z]{2})/([0-9]+)\-[a-zA-Z0-9-]*(/[a-zA-Z0-9-]*)+'] = 'category.php?id_category=$2&isolang=$1 [QSA,L]';
            $tab['RewriteRule']['content']['^([a-z]{2})/([0-9]+)\-[a-zA-Z0-9-]*'] = 'category.php?id_category=$2&isolang=$1 [QSA,L]';
            $tab['RewriteRule']['content']['^([a-z]{2})/content/([0-9]+)\-[a-zA-Z0-9-]*'] = 'cms.php?isolang=$1&id_cms=$2 [QSA,L]';
            $tab['RewriteRule']['content']['^([a-z]{2})/content/category/([0-9]+)\-[a-zA-Z0-9-]*'] = 'cms.php?isolang=$1&id_cms_category=$2 [QSA,L]';
            $tab['RewriteRule']['content']['^([a-z]{2})/([0-9]+)__[a-zA-Z0-9-]*'] = 'supplier.php?isolang=$1&id_supplier=$2 [QSA,L]';
            $tab['RewriteRule']['content']['^([a-z]{2})/([0-9]+)_[a-zA-Z0-9-]*'] = 'manufacturer.php?isolang=$1&id_manufacturer=$2 [QSA,L]';
        }

        // PS BASE URI automaticaly prepend the string, do not use PS defines for the image directories
        $tab['RewriteRule']['content']['^([0-9]+)(\-[_a-zA-Z0-9-]*)/[_a-zA-Z0-9-]*\.jpg$'] = 'img/c/$1$2.jpg [L]';

        $tab['RewriteRule']['content']['^([0-9]+)\-[a-zA-Z0-9-]*\.html'] = 'product.php?id_product=$1 [QSA,L]';
        $tab['RewriteRule']['content']['^[a-zA-Z0-9-]*/([0-9]+)\-[a-zA-Z0-9-]*\.html'] = 'product.php?id_product=$1 [QSA,L]';
        // Notice : the id_category rule has to be after product rules.
        // If not, category with number in their name will result a bug
        $tab['RewriteRule']['content']['^([0-9]+)\-[a-zA-Z0-9-]*(/[a-zA-Z0-9-]*)+'] = 'category.php?id_category=$1 [QSA,L]';
        $tab['RewriteRule']['content']['^([0-9]+)\-[a-zA-Z0-9-]*'] = 'category.php?id_category=$1 [QSA,L]';
        $tab['RewriteRule']['content']['^([0-9]+)__([a-zA-Z0-9-]*)'] = 'supplier.php?id_supplier=$1 [QSA,L]';
        $tab['RewriteRule']['content']['^([0-9]+)_([a-zA-Z0-9-]*)'] = 'manufacturer.php?id_manufacturer=$1 [QSA,L]';
        $tab['RewriteRule']['content']['^content/([0-9]+)\-([a-zA-Z0-9-]*)'] = 'cms.php?id_cms=$1 [QSA,L]';
        $tab['RewriteRule']['content']['^content/category/([0-9]+)\-([a-zA-Z0-9-]*)'] = 'cms.php?id_cms_category=$1 [QSA,L]';

        // Compatibility with the old URLs
        if (!Configuration::get('PS_INSTALL_VERSION') || version_compare(Configuration::get('PS_INSTALL_VERSION'), '1.4.0.7') == -1) {
            // This is a nasty copy/paste of the previous links, but with "lang-en" instead of "en"
            // Do not update it when you add something in the one at the top, it's only for the old links
            $tab['RewriteRule']['content']['^lang-([a-z]{2})/([a-zA-Z0-9-]*)/([0-9]+)\-([a-zA-Z0-9-]*)\.html'] = 'product.php?id_product=$3&isolang=$1 [QSA,L]';
            $tab['RewriteRule']['content']['^lang-([a-z]{2})/([0-9]+)\-([a-zA-Z0-9-]*)\.html'] = 'product.php?id_product=$2&isolang=$1 [QSA,L]';
            $tab['RewriteRule']['content']['^lang-([a-z]{2})/([0-9]+)\-([a-zA-Z0-9-]*)'] = 'category.php?id_category=$2&isolang=$1 [QSA,L]';
            $tab['RewriteRule']['content']['^content/([0-9]+)\-([a-zA-Z0-9-]*)'] = 'cms.php?id_cms=$1 [QSA,L]';
            $tab['RewriteRule']['content']['^content/category/([0-9]+)\-([a-zA-Z0-9-]*)'] = 'cms.php?id_cms_category=$1 [QSA,L]';
        }

        Language::loadLanguages();
        $default_meta = Meta::getMetasByIdLang((int) Configuration::get('PS_LANG_DEFAULT'));

        if ($multilang) {
            foreach (Language::getLanguages() as $language) {
                foreach (Meta::getMetasByIdLang($language['id_lang']) as $key => $meta) {
                    if (!empty($meta['url_rewrite']) && Validate::isLinkRewrite($meta['url_rewrite'])) {
                        $tab['RewriteRule']['content']['^' . $language['iso_code'] . '/' . $meta['url_rewrite'] . '$'] = $meta['page'] . '.php?isolang=' . $language['iso_code'] . ' [QSA,L]';
                    } elseif (array_key_exists($key, $default_meta) && $default_meta[$key]['url_rewrite'] != '') {
                        $tab['RewriteRule']['content']['^' . $language['iso_code'] . '/' . $default_meta[$key]['url_rewrite'] . '$'] = $default_meta[$key]['page'] . '.php?isolang=' . $language['iso_code'] . ' [QSA,L]';
                    }
                }
                $tab['RewriteRule']['content']['^' . $language['iso_code'] . '$'] = $language['iso_code'] . '/ [QSA,L]';
                $tab['RewriteRule']['content']['^' . $language['iso_code'] . '/([^?&]*)$'] = '$1?isolang=' . $language['iso_code'] . ' [QSA,L]';
            }
        } else {
            foreach ($default_meta as $key => $meta) {
                if (!empty($meta['url_rewrite'])) {
                    $tab['RewriteRule']['content']['^' . $meta['url_rewrite'] . '$'] = $meta['page'] . '.php [QSA,L]';
                } elseif (array_key_exists($key, $default_meta) && $default_meta[$key]['url_rewrite'] != '') {
                    $tab['RewriteRule']['content']['^' . $default_meta[$key]['url_rewrite'] . '$'] = $default_meta[$key]['page'] . '.php [QSA,L]';
                }
            }
        }

        if (!$writeFd = @fopen($path, 'w')) {
            return false;
        }

        // PS Comments
        fwrite($writeFd, "# .htaccess automaticaly generated by PrestaShop e-commerce open-source solution\n");
        fwrite($writeFd, "# WARNING: PLEASE DO NOT MODIFY THIS FILE MANUALLY. IF NECESSARY, ADD YOUR SPECIFIC CONFIGURATION WITH THE HTACCESS GENERATOR IN BACK OFFICE\n");
        fwrite($writeFd, "# http://www.prestashop.com - http://www.prestashop.com/forums\n\n");
        if (!empty($specific)) {
            fwrite($writeFd, $specific);
        }

        // RewriteEngine
        fwrite($writeFd, "\n<IfModule mod_rewrite.c>\n");

        if ($disableMuliviews) {
            fwrite($writeFd, "\n# Disable Multiviews\nOptions -Multiviews\n\n");
        }

        fwrite($writeFd, $tab['RewriteEngine']['comment'] . "\nRewriteEngine on\n\n");
        fwrite($writeFd, $tab['RewriteRule']['comment'] . "\n");
        // Webservice
        fwrite($writeFd, 'RewriteRule ^api/?(.*)$ ' . __PS_BASE_URI__ . "webservice/dispatcher.php?url=$1 [QSA,L]\n");

        // Classic URL rewriting
        if ($rewrite_settings) {
            foreach ($tab['RewriteRule']['content'] as $rule => $url) {
                fwrite($writeFd, 'RewriteRule ' . $rule . ' ' . __PS_BASE_URI__ . $url . "\n");
            }
        }

        fwrite($writeFd, "</IfModule>\n\n");

        // ErrorDocument
        fwrite($writeFd, $tab['ErrorDocument']['comment'] . "\nErrorDocument " . $tab['ErrorDocument']['content'] . "\n");

        // Cache control
        if ($cache_control) {
            $cacheControl = '
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType image/gif "access plus 1 month"
ExpiresByType image/jpeg "access plus 1 month"
ExpiresByType image/png "access plus 1 month"
ExpiresByType text/css "access plus 1 week"
ExpiresByType text/javascript "access plus 1 week"
ExpiresByType application/javascript "access plus 1 week"
ExpiresByType application/x-javascript "access plus 1 week"
ExpiresByType image/x-icon "access plus 1 year"
</IfModule>

FileETag INode MTime Size
<IfModule mod_deflate.c>
AddOutputFilterByType DEFLATE text/html
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE text/javascript
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
            ';
            fwrite($writeFd, $cacheControl);
        }
        fclose($writeFd);

        Module::hookExec('afterCreateHtaccess');

        return true;
    }

    /**
     * jsonDecode convert json string to php array / object.
     *
     * @param string $json
     * @param bool $assoc (since 1.4.2.4) if true, convert to associativ array
     *
     * @return array
     */
    public static function jsonDecode($json, $assoc = false)
    {
        if (function_exists('json_decode')) {
            return json_decode($json, $assoc);
        } else {
            include_once _PS_TOOL_DIR_ . 'json/json.php';
            $pearJson = new Services_JSON(($assoc) ? SERVICES_JSON_LOOSE_TYPE : 0);

            return $pearJson->decode($json);
        }
    }

    /**
     * Convert an array to json string.
     *
     * @param array $data
     *
     * @return string json
     */
    public static function jsonEncode($data)
    {
        if (function_exists('json_encode')) {
            return json_encode($data);
        } else {
            include_once _PS_TOOL_DIR_ . 'json/json.php';
            $pearJson = new Services_JSON();

            return $pearJson->encode($data);
        }
    }

    /**
     * Display a warning message indicating that the method is deprecated.
     */
    public static function displayAsDeprecated()
    {
        return;
    }

    /**
     * Display a warning message indicating that the parameter is deprecated.
     */
    public static function displayParameterAsDeprecated($parameter)
    {
        return;
    }

    public static function enableCache($level = 1)
    {
        global $smarty;

        if (!Configuration::get('PS_SMARTY_CACHE')) {
            return;
        }
        if ($smarty->force_compile == 0 && $smarty->caching == $level) {
            return;
        }
        self::$_forceCompile = (int) ($smarty->force_compile);
        self::$_caching = (int) ($smarty->caching);
        $smarty->force_compile = 0;
        $smarty->caching = (int) ($level);
    }

    public static function restoreCacheSettings()
    {
        global $smarty;

        if (isset(self::$_forceCompile)) {
            $smarty->force_compile = (int) (self::$_forceCompile);
        }
        if (isset(self::$_caching)) {
            $smarty->caching = (int) (self::$_caching);
        }
    }

    public static function isCallable($function)
    {
        $disabled = explode(',', ini_get('disable_functions'));

        return !in_array($function, $disabled) && is_callable($function);
    }

    public static function pRegexp($s, $delim)
    {
        $s = str_replace($delim, '\\' . $delim, $s);
        foreach (array('?', '[', ']', '(', ')', '{', '}', '-', '.', '+', '*', '^', '$') as $char) {
            $s = str_replace($char, '\\' . $char, $s);
        }

        return $s;
    }

    public static function str_replace_once($needle, $replace, $haystack)
    {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return $haystack;
        }

        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    /**
     * Function property_exists does not exist in PHP < 5.1.
     *
     * @param object or class $class
     * @param string $property
     *
     * @return bool
     */
    public static function property_exists($class, $property)
    {
        if (function_exists('property_exists')) {
            return property_exists($class, $property);
        }

        if (is_object($class)) {
            $vars = get_object_vars($class);
        } else {
            $vars = get_class_vars($class);
        }

        return array_key_exists($property, $vars);
    }

    /**
     * @desc identify the version of php
     *
     * @return string
     */
    public static function checkPhpVersion()
    {
        $version = null;

        if (defined('PHP_VERSION')) {
            $version = PHP_VERSION;
        } else {
            $version = phpversion('');
        }

        //Case management system of ubuntu, php version return 5.2.4-2ubuntu5.2
        if (strpos($version, '-') !== false) {
            $version = substr($version, 0, strpos($version, '-'));
        }

        return $version;
    }

    /**
     * @desc selection of Smarty depending on the version of php
     */
    public static function selectionVersionSmarty()
    {
        //Smarty 3 requirements PHP 5.2 +
        if (strnatcmp(self::checkPhpVersion(), '5.2.0') >= 0) {
            Configuration::updateValue('PS_FORCE_SMARTY_2', 0);
        } else {
            Configuration::updateValue('PS_FORCE_SMARTY_2', 1);
        }
    }

    /**
     * Get products order field name for queries.
     *
     * @param string $type by|way
     * @param string $value If no index given, use default order from admin -> pref -> products
     */
    public static function getProductsOrder($type, $value = null, $prefix = false)
    {
        switch ($type) {
            case 'by':
                $orderByPrefix = '';
                if ($prefix) {
                    if ($value == 'id_product' || $value == 'date_add' || $value == 'price') {
                        $orderByPrefix = 'p.';
                    } elseif ($value == 'name') {
                        $orderByPrefix = 'pl.';
                    } elseif ($value == 'manufacturer') {
                        $orderByPrefix = 'm.';
                    } elseif ($value == 'position' || empty($value)) {
                        $orderByPrefix = 'cp.';
                    }
                }

                $value = (null === $value || $value === false || $value === '') ? (int) Configuration::get('PS_PRODUCTS_ORDER_BY') : $value;
                $list = array(0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity');

                return $orderByPrefix . ((isset($list[$value])) ? $list[$value] : ((in_array($value, $list)) ? $value : 'position'));
                break;

            case 'way':
                $value = (null === $value || $value === false || $value === '') ? (int) Configuration::get('PS_PRODUCTS_ORDER_WAY') : $value;
                $list = array(0 => 'asc', 1 => 'desc');

                return (isset($list[$value])) ? $list[$value] : ((in_array($value, $list)) ? $value : 'asc');
                break;
        }
    }

    /**
     * Convert a shorthand byte value from a PHP configuration directive to an integer value.
     *
     * @param string $value value to convert
     *
     * @return int
     */
    public static function convertBytes($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = substr($value, 0, $value_length - 1);
            $unit = strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }

            return $qty;
        }
    }

    public static function display404Error()
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        include dirname(__FILE__) . '/../404.php';
        die;
    }

    /**
     * Display error and dies or silently log the error.
     *
     * @param string $msg
     * @param bool $die
     *
     * @return success of logging
     */
    public static function dieOrLog($msg, $die = true)
    {
        if ($die || (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_)) {
            die($msg);
        }

        return Logger::addLog($msg);
    }

    /**
     * Clear cache for Smarty.
     *
     * @param objet $smarty
     */
    public static function clearCache($smarty)
    {
        if (!Configuration::get('PS_FORCE_SMARTY_2')) {
            $smarty->clearAllCache();
        } else {
            $smarty->clear_all_cache();
        }
    }

    /**
     * getMemoryLimit allow to get the memory limit in octet.
     *
     * @since 1.4.5.0
     *
     * @return int the memory limit value in octet
     */
    public static function getMemoryLimit()
    {
        $memory_limit = @ini_get('memory_limit');

        if (preg_match('/[0-9]+k/i', $memory_limit)) {
            return 1024 * (int) $memory_limit;
        }

        if (preg_match('/[0-9]+m/i', $memory_limit)) {
            return 1024 * 1024 * (int) $memory_limit;
        }

        if (preg_match('/[0-9]+g/i', $memory_limit)) {
            return 1024 * 1024 * 1024 * (int) $memory_limit;
        }

        return $memory_limit;
    }

    public static function isX86_64arch()
    {
        return PHP_INT_MAX == '9223372036854775807';
    }

    /**
     * apacheModExists return true if the apache module $name is loaded.
     *
     * @TODO move this method in class Information (when it will exist)
     *
     * @param string $name module name
     *
     * @return bool true if exists
     *
     * @since 1.4.5.0
     */
    public static function apacheModExists($name)
    {
        if (function_exists('apache_get_modules')) {
            static $apacheModuleList = null;

            if (!is_array($apacheModuleList)) {
                $apacheModuleList = apache_get_modules();
            }

            // we need strpos (example, evasive can be evasive20)
            foreach ($apacheModuleList as $module) {
                if (strpos($module, $name) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function nl2br($str)
    {
        return str_replace(array("\r\n", "\r", "\n"), '<br />', $str);
    }

    /**
     * Copy a file to another place
     *
     * @return bool True if the copy succeded
     */
    public static function copy($source, $destination, $stream_context = null)
    {
        if (null === $stream_context && !preg_match('/^https?:\/\//', $source)) {
            return @copy($source, $destination);
        }

        $destFile = fopen($destination, 'wb');
        if (!is_resource($destFile)) {
            return false;
        }

        if (self::shouldUseFopen($source)) {
            $sourceFile = fopen($source);
            // If something else than false, the data was stored
            $result = (file_put_contents($destination, $sourceFile) !== false);
            fclose($sourceFile);
        } elseif (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $source);
            curl_setopt($ch, CURLOPT_FILE, $destFile);
            $result = curl_exec($ch);
            curl_close($ch);
        }

        fclose($destFile);

        return $result;
    }
}
