<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty upper modifier plugin
 * Type:     modifier
 * Name:     lower
 * Purpose:  convert string to uppercase
 *
 * @link   https://www.smarty.net/manual/en/language.modifier.upper.php lower (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_upper($params)
{
    if (Smarty::$_MBSTRING) {
        return 'mb_strtoupper((string) ' . $params[ 0 ] . ' ?? \'\', \'' . addslashes(Smarty::$_CHARSET) . '\')';
    }
    // no MBString fallback
    return 'strtoupper((string) ' . $params[ 0 ] . ' ?? \'\')';
}
