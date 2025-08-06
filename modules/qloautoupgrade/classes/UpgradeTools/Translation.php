<?php

/*
 * 2007-2018 PrestaShop
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
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade\UpgradeTools;

use PrestaShop\Module\AutoUpgrade\Tools14;
use PrestaShop\Module\AutoUpgrade\Log\LoggerInterface;

class Translation
{
    private $installedLanguagesIso;

    private $logger;
    private $translator;

    public function __construct($translator, LoggerInterface $logger, $installedLanguagesIso)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->installedLanguagesIso = $installedLanguagesIso;
    }

    /**
     * getTranslationFileType.
     *
     * @param string $file filepath to check
     *
     * @return string type of translation item
     */
    public function getTranslationFileType($file)
    {
        $type = false;
        // line shorter
        $separator = addslashes(DIRECTORY_SEPARATOR);
        $translation_dir = $separator . 'translations' . $separator;

        $regex_module = '#' . $separator . 'modules' . $separator . '.*' . $translation_dir . '(' . implode('|', $this->installedLanguagesIso) . ')\.php#';

        if (preg_match($regex_module, $file)) {
            $type = 'module';
        } elseif (preg_match('#' . $translation_dir . '(' . implode('|', $this->installedLanguagesIso) . ')' . $separator . 'admin\.php#', $file)) {
            $type = 'back office';
        } elseif (preg_match('#' . $translation_dir . '(' . implode('|', $this->installedLanguagesIso) . ')' . $separator . 'errors\.php#', $file)) {
            $type = 'error message';
        } elseif (preg_match('#' . $translation_dir . '(' . implode('|', $this->installedLanguagesIso) . ')' . $separator . 'fields\.php#', $file)) {
            $type = 'field';
        } elseif (preg_match('#' . $translation_dir . '(' . implode('|', $this->installedLanguagesIso) . ')' . $separator . 'pdf\.php#', $file)) {
            $type = 'pdf';
        } elseif (preg_match('#' . $separator . 'themes' . $separator . '(default|prestashop)' . $separator . 'lang' . $separator . '(' . implode('|', $this->installedLanguagesIso) . ')\.php#', $file)) {
            $type = 'front office';
        }

        return $type;
    }

    public function isTranslationFile($file)
    {
        return $this->getTranslationFileType($file) !== false;
    }

    /**
     * merge the translations of $orig into $dest, according to the $type of translation file.
     *
     * @param string $orig file from upgrade package
     * @param string $dest filepath of destination
     * @param string $type type of translation file (module, back office, front office, field, pdf, error)
     *
     * @return bool
     */
    public function mergeTranslationFile($orig, $dest, $type)
    {
        switch ($type) {
            case 'front office':
                $var_name = '_LANG';
                break;
            case 'back office':
                $var_name = '_LANGADM';
                break;
            case 'error message':
                $var_name = '_ERRORS';
                break;
            case 'field':
                $var_name = '_FIELDS';
                break;
            case 'module':
                $var_name = '_MODULE';
                break;
            case 'pdf':
                $var_name = '_LANGPDF';
                break;
            case 'mail':
                $var_name = '_LANGMAIL';
                break;
            default:
                return false;
        }

        if (!file_exists($orig)) {
            $this->logger->notice($this->translator->trans('[NOTICE] File %s does not exist, merge skipped.', array($orig), 'Modules.Autoupgrade.Admin'));

            return true;
        }
        include $orig;
        if (!isset($$var_name)) {
            $this->logger->warning($this->translator->trans(
                '[WARNING] %variablename% variable missing in file %filename%. Merge skipped.',
                array(
                    '%variablename%' => $var_name,
                    '%filename%' => $orig,
                ),
                'Modules.Autoupgrade.Admin'
            ));

            return true;
        }
        $var_orig = $$var_name;

        if (!file_exists($dest)) {
            $this->logger->notice($this->translator->trans('[NOTICE] File %s does not exist, merge skipped.', array($dest), 'Modules.Autoupgrade.Admin'));

            return false;
        }
        include $dest;
        if (!isset($$var_name)) {
            // in that particular case : file exists, but variable missing, we need to delete that file
            // (if not, this invalid file will be copied in /translations during upgradeDb process)
            if ('module' == $type && file_exists($dest)) {
                unlink($dest);
            }
            $this->logger->warning($this->translator->trans(
                '[WARNING] %variablename% variable missing in file %filename%. File %filename% deleted and merge skipped.',
                array(
                    '%variablename%' => $var_name,
                    '%filename%' => $dest,
                ),
                'Modules.Autoupgrade.Admin'
            ));

            return false;
        }
        $var_dest = $$var_name;

        $merge = array_merge($var_orig, $var_dest);

        $fd = fopen($dest, 'w');
        if ($fd === false) {
            return false;
        }
        fwrite($fd, "<?php\n\nglobal \$" . $var_name . ";\n\$" . $var_name . " = array();\n");
        foreach ($merge as $k => $v) {
            if (get_magic_quotes_gpc()) {
                $v = stripslashes($v);
            }
            if ('mail' == $type) {
                fwrite($fd, '$' . $var_name . '[\'' . $this->escape($k) . '\'] = \'' . $this->escape($v) . '\';' . "\n");
            } else {
                fwrite($fd, '$' . $var_name . '[\'' . $this->escape($k, true) . '\'] = \'' . $this->escape($v, true) . '\';' . "\n");
            }
        }
        fwrite($fd, "\n?>");
        fclose($fd);

        return true;
    }

    /**
     * Escapes illegal characters in a string.
     * Extracted from DB class, in order to avoid dependancies.
     *
     * @see DbCore::_escape()
     *
     * @param string $str
     * @param bool $html_ok Does data contain HTML code ? (optional)
     *
     * @return string
     */
    private function escape($str, $html_ok = false)
    {
        $search = array('\\', "\0", "\n", "\r", "\x1a", "'", '"');
        $replace = array('\\\\', '\\0', '\\n', '\\r', "\Z", "\'", '\"');
        $str = str_replace($search, $replace, $str);
        if (!$html_ok) {
            return strip_tags(Tools14::nl2br($str));
        }

        return $str;
    }
}
