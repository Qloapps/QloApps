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
 * @since 1.5.0
 */
class TranslateCore
{
    public static $ignore_folder = array('.', '..', '.svn', '.git', '.htaccess', 'index.php');

    /**
     * Get a translation for an admin controller
     *
     * @param $string
     * @param string $class
     * @param bool $addslashes
     * @param bool $htmlentities
     * @return string
     */
    public static function getAdminTranslation($string, $class = 'AdminTab', $addslashes = false, $htmlentities = true, $sprintf = null)
    {
        static $modules_tabs = null;

        // @todo remove global keyword in translations files and use static
        global $_LANGADM;

        if ($modules_tabs === null) {
            $modules_tabs = Tab::getModuleTabList();
        }

        if ($_LANGADM == null) {
            $iso = Context::getContext()->language->iso_code;
            if (empty($iso)) {
                $iso = Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT'));
            }
            if (file_exists(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php')) {
                include_once(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');
            }
        }

        if (isset($modules_tabs[strtolower($class)])) {
            $class_name_controller = $class.'controller';
            // if the class is extended by a module, use modules/[module_name]/xx.php lang file
            if (class_exists($class_name_controller) && Module::getModuleNameFromClass($class_name_controller)) {
                return Translate::getModuleTranslation(Module::$classInModule[$class_name_controller], $string, $class_name_controller, $sprintf, $addslashes);
            }
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);
        if (isset($_LANGADM[$class.$key])) {
            $str = $_LANGADM[$class.$key];
        } else {
            $str = Translate::getGenericAdminTranslation($string, $key, $_LANGADM);
        }

        if ($htmlentities) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'utf-8');
        }
        $str = str_replace('"', '&quot;', $str);

        if ($sprintf !== null) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return ($addslashes ? addslashes($str) : stripslashes($str));
    }

    /**
     * Return the translation for a string if it exists for the base AdminController or for helpers
     *
     * @param $string string to translate
     * @param null $key md5 key if already calculated (optional)
     * @param array $lang_array Global array of admin translations
     * @return string translation
     */
    public static function getGenericAdminTranslation($string, $key = null, &$lang_array)
    {
        $string = preg_replace("/\\\*'/", "\'", $string);
        if (is_null($key)) {
            $key = md5($string);
        }

        if (isset($lang_array['AdminController'.$key])) {
            $str = $lang_array['AdminController'.$key];
        } elseif (isset($lang_array['Helper'.$key])) {
            $str = $lang_array['Helper'.$key];
        } elseif (isset($lang_array['AdminTab'.$key])) {
            $str = $lang_array['AdminTab'.$key];
        } else {
            // note in 1.5, some translations has moved from AdminXX to helper/*.tpl
            $str = $string;
        }

        return $str;
    }

    /**
     * Get a translation for a module
     *
     * @param string|Module $module
     * @param string $string
     * @param string $source
     * @return string
     */
    public static function getModuleTranslation($module, $string, $source, $sprintf = null, $addslashes = false, $language = null)
    {
        global $_MODULES, $_MODULE, $_LANGADM;

        static $lang_cache = array();
        // $_MODULES is a cache of translations for all module.
        // $translations_merged is a cache of wether a specific module's translations have already been added to $_MODULES
        static $translations_merged = array();

        // inialize $force_refresh as false and use only when $lang is provided to load new language translations
        $force_refresh = false;

        $name = $module instanceof Module ? $module->name : $module;

        if (!($language !== null && Validate::isLoadedObject($language))) {
            $language = Context::getContext()->language;
        } else {
            $force_refresh = true;
        }

        if ((!isset($translations_merged[$name]) && isset(Context::getContext()->language)) || $force_refresh) {
            $files_by_priority = array(
                // Translations in theme
                _PS_THEME_DIR_.'modules/'.$name.'/translations/'.$language->iso_code.'.php',
                _PS_THEME_DIR_.'modules/'.$name.'/'.$language->iso_code.'.php',
                // PrestaShop 1.5 translations
                _PS_MODULE_DIR_.$name.'/translations/'.$language->iso_code.'.php',
                // PrestaShop 1.4 translations
                _PS_MODULE_DIR_.$name.'/'.$language->iso_code.'.php'
            );
            // if force_refresh is set reverse the list because array_merge will overrite the previous file with new file
            if ($force_refresh) {
                $files_by_priority = array_reverse($files_by_priority);
            }
            foreach ($files_by_priority as $file) {
                if (file_exists($file)) {
                    include_once($file);
                    $_MODULES = !empty($_MODULES) ? ($force_refresh ? array_merge($_MODULES, $_MODULE) : $_MODULES + $_MODULE) : $_MODULE; //we use "+" instead of array_merge() when force_refresh is false because array merge erase existing values.
                    $translations_merged[$name] = true;
                }
            }
        }
        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        $cache_key = $name.'|'.$string.'|'.$source.'|'.(int)$addslashes;

        if (!isset($lang_cache[$cache_key])) {
            if ($_MODULES == null) {
                if ($sprintf !== null) {
                    $string = Translate::checkAndReplaceArgs($string, $sprintf);
                }
                return str_replace('"', '&quot;', $string);
            }

            $current_key = strtolower('<{'.$name.'}'._THEME_NAME_.'>'.$source).'_'.$key;
            $default_key = strtolower('<{'.$name.'}prestashop>'.$source).'_'.$key;

            if ('controller' == substr($source, -10, 10)) {
                 $file = substr($source, 0, -10);
                 $current_key_file = strtolower('<{'.$name.'}'._THEME_NAME_.'>'.$file).'_'.$key;
                 $default_key_file = strtolower('<{'.$name.'}prestashop>'.$file).'_'.$key;
            }

            if (isset($current_key_file) && !empty($_MODULES[$current_key_file])) {
                $ret = stripslashes($_MODULES[$current_key_file]);
            } elseif (isset($default_key_file) && !empty($_MODULES[$default_key_file])) {
                $ret = stripslashes($_MODULES[$default_key_file]);
            } elseif (!empty($_MODULES[$current_key])) {
                $ret = stripslashes($_MODULES[$current_key]);
            } elseif (!empty($_MODULES[$default_key])) {
                $ret = stripslashes($_MODULES[$default_key]);
            }
            // if translation was not found in module, look for it in AdminController or Helpers
            elseif (!empty($_LANGADM)) {
                $ret = stripslashes(Translate::getGenericAdminTranslation($string, $key, $_LANGADM));
            } else {
                $ret = stripslashes($string);
            }

            if ($sprintf !== null) {
                $ret = Translate::checkAndReplaceArgs($ret, $sprintf);
            }


            $ret = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
            if ($addslashes) {
                $ret = addslashes($ret);
            }

            if ($sprintf === null) {
                $lang_cache[$cache_key] = $ret;
            } else {
                return $ret;
            }
        }
        return $lang_cache[$cache_key];
    }

    /**
     * Get a translation for a PDF
     *
     * @param string $string
     * @return string
     */
    public static function getPdfTranslation($string, $sprintf = null, $language = null)
    {
        global $_LANGPDF;

        if (!($language !== null && Validate::isLoadedObject($language))) {
            $iso = Context::getContext()->language->iso_code;
        } else {
            $iso = $language->iso_code;
        }

        if (!Validate::isLangIsoCode($iso)) {
            Tools::displayError(sprintf('Invalid iso lang (%s)', Tools::safeOutput($iso)));
        }

        $override_i18n_file = _PS_THEME_DIR_.'pdf/lang/'.$iso.'.php';
        $i18n_file = _PS_TRANSLATIONS_DIR_.$iso.'/pdf.php';
        if (file_exists($override_i18n_file)) {
            $i18n_file = $override_i18n_file;
        }

        if (!include($i18n_file)) {
            Tools::displayError(sprintf('Cannot include PDF translation language file : %s', $i18n_file));
        }

        if (!isset($_LANGPDF) || !is_array($_LANGPDF)) {
            return str_replace('"', '&quot;', $string);
        }

        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        $str = (array_key_exists('PDF'.$key, $_LANGPDF) ? $_LANGPDF['PDF'.$key] : $string);

        if ($sprintf !== null) {
            $str = Translate::checkAndReplaceArgs($str, $sprintf);
        }

        return $str;
    }

    /**
     * Check if string use a specif syntax for sprintf and replace arguments if use it
     *
     * @param $string
     * @param $args
     * @return string
     */
    public static function checkAndReplaceArgs($string, $args)
    {
        if (preg_match_all('#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#', $string, $matches) && !is_null($args)) {
            if (!is_array($args)) {
                $args = array($args);
            }

            return vsprintf($string, $args);
        }
        return $string;
    }

    /**
    * Perform operations on translations after everything is escaped and before displaying it
    */
    public static function postProcessTranslation($string, $params)
    {
        // If tags were explicitely provided, we want to use them *after* the translation string is escaped.
        if (!empty($params['tags'])) {
            foreach ($params['tags'] as $index => $tag) {
                // Make positions start at 1 so that it behaves similar to the %1$d etc. sprintf positional params
                $position = $index + 1;
                // extract tag name
                $match = array();
                if (preg_match('/^\s*<\s*(\w+)/', $tag, $match)) {
                    $opener = $tag;
                    $closer = '</'.$match[1].'>';

                    $string = str_replace('['.$position.']', $opener, $string);
                    $string = str_replace('[/'.$position.']', $closer, $string);
                    $string = str_replace('['.$position.'/]', $opener.$closer, $string);
                }
            }
        }

        return $string;
    }

    /**
     * Compatibility method that just calls postProcessTranslation.
     * @deprecated renamed this to postProcessTranslation, since it is not only used in relation to smarty.
     */
    public static function smartyPostProcessTranslation($string, $params)
    {
        return Translate::postProcessTranslation($string, $params);
    }

    /**
     * Helper function to make calls to postProcessTranslation more readable.
     */
    public static function ppTags($string, $tags)
    {
        return Translate::postProcessTranslation($string, array('tags' => $tags));
    }

        /**
     * This method parse a file by type of translation and type file
     *
     * @param $content
     * @param $type_translation : front, back, errors, modules...
     * @param string|bool $type_file : (tpl|php)
     * @param string $module_name : name of the module
     * @return array
     */
    public static function userParseFile($content, $type_translation, $type_file = false, $module_name = '')
    {
        switch ($type_translation) {
            case 'front':
                // Parsing file in Front office
                $regex = '/\{l\s*s=([\'\"])'._PS_TRANS_PATTERN_.'\1(\s*sprintf=.*)?(\s*js=1)?\s*\}/U';
                break;

            case 'back':
                // Parsing file in Back office
                if ($type_file == 'php') {
                    $regex = '/this->l\((\')'._PS_TRANS_PATTERN_.'\'[\)|\,]/U';
                } elseif ($type_file == 'specific') {
                    $regex = '/Translate::getAdminTranslation\((\')'._PS_TRANS_PATTERN_.'\'(?:,.*)*\)/U';
                } else {
                    $regex = '/\{l\s*s\s*=([\'\"])'._PS_TRANS_PATTERN_.'\1(\s*sprintf=.*)?(\s*js=1)?(\s*slashes=1)?.*\}/U';
                }
                break;

            case 'errors':
                // Parsing file for all errors syntax
                $regex = '/Tools::displayError\((\')'._PS_TRANS_PATTERN_.'\'(,\s*(.+))?\)/U';
                break;

            case 'modules':
                // Parsing modules file
                if ($type_file == 'php') {
                    $regex = '/->l\((\')'._PS_TRANS_PATTERN_.'\'(, ?\'(.+)\')?(, ?(.+))?\)/U';
                } else {
                    // In tpl file look for something that should contain mod='module_name' according to the documentation
                    $regex = '/\{l\s*s=([\'\"])'._PS_TRANS_PATTERN_.'\1.*\s+mod=\''.$module_name.'\'.*\}/U';
                }
                break;

            case 'pdf':
                // Parsing PDF file
                if ($type_file == 'php') {
                    $regex = array(
                        '/HTMLTemplate.*::l\((\')'._PS_TRANS_PATTERN_.'\'[\)|\,]/U',
                        '/->l\((\')'._PS_TRANS_PATTERN_.'\'(, ?\'(.+)\')?(, ?(.+))?\)/U'
                    );
                } else {
                    $regex = '/\{l\s*s=([\'\"])'._PS_TRANS_PATTERN_.'\1(\s*sprintf=.*)?(\s*js=1)?(\s*pdf=\'true\')?\s*\}/U';
                }
                break;
        }

        if (!is_array($regex)) {
            $regex = array($regex);
        }

        $strings = array();
        foreach ($regex as $regex_row) {
            $matches = array();
            $n = preg_match_all($regex_row, $content, $matches);
            for ($i = 0; $i < $n; $i += 1) {
                $quote = $matches[1][$i];
                $string = $matches[2][$i];

                if ($quote === '"') {
                    // Escape single quotes because the core will do it when looking for the translation of this string
                    $string = str_replace('\'', '\\\'', $string);
                    // Unescape double quotes
                    $string = preg_replace('/\\\\+"/', '"', $string);
                }

                $strings[] = $string;
            }
        }

        return array_unique($strings);
    }

    /**
     * Recursively list files in directory $dir
     *
     * @param string $dir
     * @param array  $list
     * @param string $file_ext
     *
     * @return array
     */
    public static function listFiles($dir, $list = array(), $file_ext = 'tpl')
    {
        $dir = rtrim($dir, '/').DIRECTORY_SEPARATOR;

        $to_parse = scandir($dir);
        // copied (and kind of) adapted from AdminImages.php
        foreach ($to_parse as $file) {
            if (!in_array($file, self::$ignore_folder)) {
                if (preg_match('#'.preg_quote($file_ext, '#').'$#i', $file)) {
                    $list[$dir][] = $file;
                } elseif (is_dir($dir.$file)) {
                    $list = self::listFiles($dir.$file, $list, $file_ext);
                }
            }
        }

        return $list;
    }

    /**
     * Find sentence which use %d, %s, %%, %1$d, %1$s...
     *
     * @param $key : english sentence
     * @return array|bool return list of matches
     */
    public static function checkIfKeyUseSprintf($key)
    {
        if (preg_match_all('#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#', $key, $matches)) {
            return implode(', ', $matches[0]);
        }

        return false;
    }

    public static function getTranslationsCountFrontOffice($theme, $isoCode)
    {
        $themeDir = _PS_ROOT_DIR_.'/themes/'.$theme.'/';
        $langFile = $themeDir.'lang/'.$isoCode.'.php';

        // include global translation file
        if (Tools::file_exists_cache($langFile)) {
            include $langFile;
        } else {
            $_LANG = array();
        }

        // count translatable strings in theme tpl files
        $directories = array();
        $directories['tpl'] = array(_PS_ALL_THEMES_DIR_ => scandir(_PS_ALL_THEMES_DIR_));
        self::$ignore_folder[] = 'modules';
        $directories['tpl'] = array_merge($directories['tpl'], self::listFiles($themeDir));
        if (isset($directories['tpl'][$themeDir.'pdf/'])) {
            unset($directories['tpl'][$themeDir.'pdf/']);
        }

        if (Tools::file_exists_cache(_PS_THEME_OVERRIDE_DIR_)) {
            $directories['tpl'] = array_merge($directories['tpl'], self::listFiles(_PS_THEME_OVERRIDE_DIR_));
        }

        // process tpl files
        $countTotal = 0;
        $countTranslated = 0;
        foreach ($directories['tpl'] as $dir => $files) {
            $prefix = $dir == _PS_THEME_OVERRIDE_DIR_ ? 'override_' : '';

            foreach ($files as $file) {
                if (preg_match('/^(.*).tpl$/', $file) && (Tools::file_exists_cache($filePath = $dir.$file))) {
                    $prefixKey = $prefix.substr(basename($file), 0, -4);
                    $fileContent = Tools::file_get_contents($filePath);
                    $matches = self::userParseFile($fileContent, 'front');

                    foreach ($matches as $key) {
                        $countTotal++;

                        if (!empty($key)) {
                            if (isset($_LANG[$prefixKey.'_'.md5($key)])) {
                                $countTranslated++;
                            }
                        }
                    }
                }
            }
        }

        return array('total' => $countTotal, 'translated' => $countTranslated);
    }

    public static function getTranslationsCountBackOffice($isoCode)
    {
        $translationsFile = _PS_TRANSLATIONS_DIR_.$isoCode.'/admin.php';

        // include global translation file
        if (Tools::file_exists_cache($translationsFile)) {
            include $translationsFile;
        } else {
            $_LANGADM = array();
        }

        // list directories with translatable files
        $directories = array(
            'php' => array(
                _PS_ADMIN_CONTROLLER_DIR_ => scandir(_PS_ADMIN_CONTROLLER_DIR_),
                _PS_OVERRIDE_DIR_.'controllers/admin/' => scandir(_PS_OVERRIDE_DIR_.'controllers/admin/'),
                _PS_CLASS_DIR_.'helper/' => scandir(_PS_CLASS_DIR_.'helper/'),
                _PS_CLASS_DIR_.'controller/' => array('AdminController.php'),
                _PS_CLASS_DIR_ => array('PaymentModule.php'),
            ),
            'tpl' => self::listFiles(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'themes/'),
            'specific' => array(
                _PS_ADMIN_DIR_.DIRECTORY_SEPARATOR => array(
                    'header.inc.php',
                    'footer.inc.php',
                    'index.php',
                    'functions.php',
                ),
            ),
        );

        // list directories from override folders
        if (Tools::file_exists_cache(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'templates')) {
            $directories['tpl'] = array_merge($directories['tpl'], self::listFiles(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'templates'));
        }

        // process files
        $countTotal = 0;
        $countTranslated = 0;
        foreach ($directories['php'] as $dir => $files) {
            foreach ($files as $file) {
                // check if $file is a PHP file and corresponding override file exists
                if (preg_match('/^(.*)\.php$/', $file) && Tools::file_exists_cache($filePath = $dir.$file) && !in_array($file, self::$ignore_folder)) {
                    $prefixKey = basename($file);
                    // -4 becomes -14 to remove the ending "Controller.php" from the filename
                    if (strpos($file, 'Controller.php') !== false) {
                        $prefixKey = basename(substr($file, 0, -14));
                    } elseif (strpos($file, 'Helper') !== false) {
                        $prefixKey = 'Helper';
                    }

                    if ($prefixKey == 'Admin') {
                        $prefixKey = 'AdminController';
                    }

                    if ($prefixKey == 'PaymentModule.php') {
                        $prefixKey = 'PaymentModule';
                    }

                    $fileContent = Tools::file_get_contents($filePath);
                    $matches = self::userParseFile($fileContent, 'back', 'php');

                    foreach ($matches as $key) {
                        $countTotal++;

                        if (!empty($key)) {
                            if (isset($_LANGADM[$prefixKey.md5($key)])) {
                                $countTranslated++;
                            }
                        }
                    }
                }
            }
        }

        foreach ($directories['specific'] as $dir => $files) {
            foreach ($files as $file) {
                if (Tools::file_exists_cache($filePath = $dir.$file) && !in_array($file, self::$ignore_folder)) {
                    $prefixKey = 'index';

                    $fileContent = Tools::file_get_contents($filePath);
                    $matches = self::userParseFile($fileContent, 'back', 'specific');

                    foreach ($matches as $key) {
                        $countTotal++;

                        if (!empty($key)) {
                            if (isset($_LANGADM[$prefixKey.md5($key)])) {
                                $countTranslated++;
                            }
                        }
                    }
                }
            }
        }

        foreach ($directories['tpl'] as $dir => $files) {
            foreach ($files as $file) {
                if (preg_match('/^(.*).tpl$/', $file) && Tools::file_exists_cache($filePath = $dir.$file)) {
                    // get controller name instead of file name
                    $prefixKey = Tools::toCamelCase(str_replace(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'themes', '', $filePath), true);
                    $pos = strrpos($prefixKey, DIRECTORY_SEPARATOR);
                    $tmp = substr($prefixKey, 0, $pos);

                    if (preg_match('#controllers#', $tmp)) {
                        $parentClass = explode(DIRECTORY_SEPARATOR, str_replace('/', DIRECTORY_SEPARATOR, $tmp));
                        $override = array_search('override', $parentClass);
                        if ($override !== false) {
                            // case override/controllers/admin/templates/controller_name
                            $prefixKey = 'Admin'.ucfirst($parentClass[$override + 4]);
                        } else {
                            // case admin_name/themes/theme_name/template/controllers/controller_name
                            $key = array_search('controllers', $parentClass);
                            $prefixKey = 'Admin'.ucfirst($parentClass[$key + 1]);
                        }
                    } else {
                        $prefixKey = 'Admin'.ucfirst(substr($tmp, strrpos($tmp, DIRECTORY_SEPARATOR) + 1, $pos));
                    }

                    // Adding list, form, option in Helper Translations
                    $listPrefixKey = array('AdminHelpers', 'AdminList', 'AdminView', 'AdminOptions', 'AdminForm',
                        'AdminCalendar', 'AdminTree', 'AdminUploader', 'AdminDataviz', 'AdminKpi', 'AdminModule_list', 'AdminModulesList');
                    if (in_array($prefixKey, $listPrefixKey)) {
                        $prefixKey = 'Helper';
                    }

                    // Adding the folder backup/download/ in AdminBackup Translations
                    if ($prefixKey == 'AdminDownload') {
                        $prefixKey = 'AdminBackup';
                    }

                    // use the prefix 'AdminController' (like old php files 'header', 'footer.inc', 'index', 'login', 'password', 'functions'
                    if ($prefixKey == 'Admin' || $prefixKey == 'AdminTemplate') {
                        $prefixKey = 'AdminController';
                    }

                    $fileContent = Tools::file_get_contents($filePath);
                    $matches = self::userParseFile($fileContent, 'back', 'tpl');

                    // get string translation for each tpl file
                    foreach ($matches as $key) {
                        $countTotal++;

                        if (!empty($key)) {
                            if (isset($_LANGADM[$prefixKey.md5($key)])) {
                                $countTranslated++;
                            }
                        }
                    }
                }
            }
        }

        return array('total' => $countTotal, 'translated' => $countTranslated);
    }
}
