<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifier
 */
/**
 * Smarty escape modifier plugin
 * Type:     modifier
 * Name:     escape
 * Purpose:  escape string for output
 *
 * @link   https://www.smarty.net/docs/en/language.modifier.escape
 * @author Monte Ohrt <monte at ohrt dot com>
 *
 * @param string  $string        input string
 * @param string  $esc_type      escape type
 * @param string  $char_set      character set, used for htmlspecialchars() or htmlentities()
 * @param boolean $double_encode encode already encoded entitites again, used for htmlspecialchars() or htmlentities()
 *
 * @return string escaped input string
 */
function smarty_modifier_escape($string, $esc_type = 'html', $char_set = null, $double_encode = true)
{
    static $is_loaded_1 = false;
    static $is_loaded_2 = false;
    if (!$char_set) {
        $char_set = Smarty::$_CHARSET;
    }

    $string = (string)$string;

    switch ($esc_type) {
        case 'html':
            return htmlspecialchars($string, ENT_QUOTES, $char_set, $double_encode);
        // no break
        case 'htmlall':
            if (Smarty::$_MBSTRING) {
                $string = mb_convert_encoding($string, 'UTF-8', $char_set);
                return htmlentities($string, ENT_QUOTES, 'UTF-8', $double_encode);
            }
            // no MBString fallback
            return htmlentities($string, ENT_QUOTES, $char_set, $double_encode);
        // no break
        case 'url':
            return rawurlencode($string);
        case 'urlpathinfo':
            return str_replace('%2F', '/', rawurlencode($string));
        case 'quotes':
            // escape unescaped single quotes
            return preg_replace("%(?<!\\\\)'%", "\\'", $string);
        case 'hex':
            // escape every byte into hex
            // Note that the UTF-8 encoded character ä will be represented as %c3%a4
            $return = '';
            $_length = strlen($string);
            for ($x = 0; $x < $_length; $x++) {
                $return .= '%' . bin2hex($string[ $x ]);
            }
            return $return;
        case 'hexentity':
            $return = '';
            if (Smarty::$_MBSTRING) {
                if (!$is_loaded_1) {
                    if (!is_callable('smarty_mb_to_unicode')) {
                        include_once SMARTY_PLUGINS_DIR . 'shared.mb_unicode.php';
                    }
                    $is_loaded_1 = true;
                }
                $return = '';
                foreach (smarty_mb_to_unicode($string, Smarty::$_CHARSET) as $unicode) {
                    $return .= '&#x' . strtoupper(dechex($unicode)) . ';';
                }
                return $return;
            }
            // no MBString fallback
            $_length = strlen($string);
            for ($x = 0; $x < $_length; $x++) {
                $return .= '&#x' . bin2hex($string[ $x ]) . ';';
            }
            return $return;
        case 'decentity':
            $return = '';
            if (Smarty::$_MBSTRING) {
                if (!$is_loaded_1) {
                    if (!is_callable('smarty_mb_to_unicode')) {
                        include_once SMARTY_PLUGINS_DIR . 'shared.mb_unicode.php';
                    }
                    $is_loaded_1 = true;
                }
                $return = '';
                foreach (smarty_mb_to_unicode($string, Smarty::$_CHARSET) as $unicode) {
                    $return .= '&#' . $unicode . ';';
                }
                return $return;
            }
            // no MBString fallback
            $_length = strlen($string);
            for ($x = 0; $x < $_length; $x++) {
                $return .= '&#' . ord($string[ $x ]) . ';';
            }
            return $return;
        case 'javascript':
            // escape quotes and backslashes, newlines, etc.
            return strtr(
                $string,
                array(
                    '\\' => '\\\\',
                    "'"  => "\\'",
                    '"'  => '\\"',
                    "\r" => '\\r',
                    "\n" => '\\n',
                    '</' => '<\/',
                    // see https://html.spec.whatwg.org/multipage/scripting.html#restrictions-for-contents-of-script-elements
                    '<!--' => '<\!--',
                    '<s'   => '<\s',
                    '<S'   => '<\S',
	                "`" => "\\\\`",
	                "\${" => "\\\\\\$\\{"
                )
            );
        case 'mail':
            if (Smarty::$_MBSTRING) {
                if (!$is_loaded_2) {
                    if (!is_callable('smarty_mb_str_replace')) {
                        include_once SMARTY_PLUGINS_DIR . 'shared.mb_str_replace.php';
                    }
                    $is_loaded_2 = true;
                }
                return smarty_mb_str_replace(
                    array(
                        '@',
                        '.'
                    ),
                    array(
                        ' [AT] ',
                        ' [DOT] '
                    ),
                    $string
                );
            }
            // no MBString fallback
            return str_replace(
                array(
                    '@',
                    '.'
                ),
                array(
                    ' [AT] ',
                    ' [DOT] '
                ),
                $string
            );
        case 'nonstd':
            // escape non-standard chars, such as ms document quotes
            $return = '';
            if (Smarty::$_MBSTRING) {
                if (!$is_loaded_1) {
                    if (!is_callable('smarty_mb_to_unicode')) {
                        include_once SMARTY_PLUGINS_DIR . 'shared.mb_unicode.php';
                    }
                    $is_loaded_1 = true;
                }
                foreach (smarty_mb_to_unicode($string, Smarty::$_CHARSET) as $unicode) {
                    if ($unicode >= 126) {
                        $return .= '&#' . $unicode . ';';
                    } else {
                        $return .= chr($unicode);
                    }
                }
                return $return;
            }
            $_length = strlen($string);
            for ($_i = 0; $_i < $_length; $_i++) {
                $_ord = ord(substr($string, $_i, 1));
                // non-standard char, escape it
                if ($_ord >= 126) {
                    $return .= '&#' . $_ord . ';';
                } else {
                    $return .= substr($string, $_i, 1);
                }
            }
            return $return;
        default:
            trigger_error("escape: unsupported type: $esc_type - returning unmodified string", E_USER_NOTICE);
            return $string;
    }
}
