<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class TranslateCore
{
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
    public static function getModuleTranslation($module, $string, $source, $sprintf = null, $js = false)
    {
        global $_MODULES, $_MODULE, $_LANGADM;

        static $lang_cache = array();
        // $_MODULES is a cache of translations for all module.
        // $translations_merged is a cache of wether a specific module's translations have already been added to $_MODULES
        static $translations_merged = array();

        $name = $module instanceof Module ? $module->name : $module;

        $language = Context::getContext()->language;

        if (!isset($translations_merged[$name]) && isset(Context::getContext()->language)) {
            $files_by_priority = array(
                // Translations in theme
                _PS_THEME_DIR_.'modules/'.$name.'/translations/'.$language->iso_code.'.php',
                _PS_THEME_DIR_.'modules/'.$name.'/'.$language->iso_code.'.php',
                // PrestaShop 1.5 translations
                _PS_MODULE_DIR_.$name.'/translations/'.$language->iso_code.'.php',
                // PrestaShop 1.4 translations
                _PS_MODULE_DIR_.$name.'/'.$language->iso_code.'.php'
            );
            foreach ($files_by_priority as $file) {
                if (file_exists($file)) {
                    include_once($file);
                    $_MODULES = !empty($_MODULES) ? $_MODULES + $_MODULE : $_MODULE; //we use "+" instead of array_merge() because array merge erase existing values.
                    $translations_merged[$name] = true;
                }
            }
        }
        $string = preg_replace("/\\\*'/", "\'", $string);
        $key = md5($string);

        $cache_key = $name.'|'.$string.'|'.$source.'|'.(int)$js;

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
+                $file = substr($source, 0, -10);
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

            if ($js) {
                $ret = addslashes($ret);
            } else {
                $ret = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
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
    public static function getPdfTranslation($string, $sprintf = null)
    {
        global $_LANGPDF;

        $iso = Context::getContext()->language->iso_code;

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
}
