<?php
/**
 * Smarty shared plugin
 *
 * @package    Smarty
 * @subpackage PluginsShared
 */
if (!function_exists('smarty_mb_str_replace')) {
    /**
     * Multibyte string replace
     *
     * @param string|string[] $search  the string to be searched
     * @param string|string[] $replace the replacement string
     * @param string          $subject the source string
     * @param int             &$count  number of matches found
     *
     * @return string replaced string
     * @author Rodney Rehm
     */
    function smarty_mb_str_replace($search, $replace, $subject, &$count = 0)
    {
        if (!is_array($search) && is_array($replace)) {
            return false;
        }
        if (is_array($subject)) {
            // call mb_replace for each single string in $subject
            foreach ($subject as &$string) {
                $string = smarty_mb_str_replace($search, $replace, $string, $c);
                $count += $c;
            }
        } elseif (is_array($search)) {
            if (!is_array($replace)) {
                foreach ($search as &$string) {
                    $subject = smarty_mb_str_replace($string, $replace, $subject, $c);
                    $count += $c;
                }
            } else {
                $n = max(count($search), count($replace));
                while ($n--) {
                    $subject = smarty_mb_str_replace(current($search), current($replace), $subject, $c);
                    $count += $c;
                    next($search);
                    next($replace);
                }
            }
        } else {
            $mb_reg_charset = mb_regex_encoding();
            // Check if mbstring regex is using UTF-8
            $reg_is_unicode = !strcasecmp($mb_reg_charset, "UTF-8");
            if(!$reg_is_unicode) {
                // ...and set to UTF-8 if not
                mb_regex_encoding("UTF-8");
            }

            // See if charset used by Smarty is matching one used by regex...
            $current_charset = mb_regex_encoding();
            $convert_result = (bool)strcasecmp(Smarty::$_CHARSET, $current_charset);
            if($convert_result) {
                // ...convert to it if not.
                $subject = mb_convert_encoding($subject, $current_charset, Smarty::$_CHARSET);
                $search = mb_convert_encoding($search, $current_charset, Smarty::$_CHARSET);
                $replace = mb_convert_encoding($replace, $current_charset, Smarty::$_CHARSET);
            }

            $parts = mb_split(preg_quote($search), $subject ?? "") ?: array();
            // If original regex encoding was not unicode...
            if(!$reg_is_unicode) {
                // ...restore original regex encoding to avoid breaking the system.
                mb_regex_encoding($mb_reg_charset);
            }
            if($parts === false) {
                // This exception is thrown if call to mb_split failed.
                // Usually it happens, when $search or $replace are not valid for given mb_regex_encoding().
                // There may be other cases for it to fail, please file an issue if you find a reproducible one.
                throw new SmartyException("Source string is not a valid $current_charset sequence (probably)");
            }

            $count = count($parts) - 1;
            $subject = implode($replace, $parts);
            // Convert results back to charset used by Smarty, if needed.
            if($convert_result) {
                $subject = mb_convert_encoding($subject, Smarty::$_CHARSET, $current_charset);
            }
        }
        return $subject;
    }
}
