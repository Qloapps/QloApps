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

global $smarty;
$smarty->setTemplateDir(_PS_THEME_DIR_);

if (Configuration::get('PS_HTML_THEME_COMPRESSION')) {
    $smarty->registerFilter('output', 'smartyMinifyHTML');
}
if (Configuration::get('PS_JS_HTML_THEME_COMPRESSION')) {
    $smarty->registerFilter('output', 'smartyPackJSinHTML');
}

function smartyTranslate($params, $smarty)
{
    global $_LANG;

    if (!isset($params['js'])) {
        $params['js'] = false;
    }
    if (!isset($params['pdf'])) {
        $params['pdf'] = false;
    }
    if (!isset($params['mod'])) {
        $params['mod'] = false;
    }
    if (!isset($params['sprintf'])) {
        $params['sprintf'] = array();
    }

    if (!isset($params['d'])) {
        $params['d'] = null;
    }

    if (!is_null($params['d'])) {
        if (isset($params['tags'])) {
            $backTrace = debug_backtrace();

            $errorMessage = sprintf(
                'Unable to translate "%s" in %s. tags() is not supported anymore, please use sprintf().',
                $params['s'],
                $backTrace[0]['args'][1]->template_resource
            );

            if (_PS_MODE_DEV_) {
                throw new Exception($errorMessage);
            } else {
                PrestaShopLogger::addLog($errorMessage);
            }
        }

        if (!is_array($params['sprintf'])) {
            $backTrace = debug_backtrace();

            $errorMessage = sprintf(
                'Unable to translate "%s" in %s. sprintf() parameter should be an array.',
                $params['s'],
                $backTrace[0]['args'][1]->template_resource
            );

            if (_PS_MODE_DEV_) {
                throw new Exception($errorMessage);
            } else {
                PrestaShopLogger::addLog($errorMessage);

                return $params['s'];
            }
        }
    }

    $string = str_replace('\'', '\\\'', $params['s']);
    $filename = ((!isset($smarty->compiler_object) || !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());

    $basename = basename($filename, '.tpl');
    $key = $basename.'_'.md5($string);

    if (isset($smarty->source) && (strpos($smarty->source->filepath, DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR) !== false)) {
        $key = 'override_'.$key;
    }

    if ($params['mod']) {
        return Translate::smartyPostProcessTranslation(Translate::getModuleTranslation($params['mod'], $params['s'], $basename, $params['sprintf'], $params['js']), $params);
    } elseif ($params['pdf']) {
        return Translate::smartyPostProcessTranslation(Translate::getPdfTranslation($params['s'], $params['sprintf']), $params);
    }

    if ($_LANG != null && isset($_LANG[$key])) {
        $msg = $_LANG[$key];
    } elseif ($_LANG != null && isset($_LANG[Tools::strtolower($key)])) {
        $msg = $_LANG[Tools::strtolower($key)];
    } else {
        $msg = $params['s'];
    }

    if ($msg != $params['s'] && !$params['js']) {
        $msg = stripslashes($msg);
    } elseif ($params['js']) {
        $msg = addslashes($msg);
    }

    if ($params['sprintf'] !== null) {
        $msg = Translate::checkAndReplaceArgs($msg, $params['sprintf']);
    }

    return Translate::smartyPostProcessTranslation($params['js'] ? $msg : Tools::safeOutput($msg), $params);
}
