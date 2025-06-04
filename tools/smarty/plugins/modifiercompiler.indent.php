<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty indent modifier plugin
 * Type:     modifier
 * Name:     indent
 * Purpose:  indent lines of text
 *
 * @link   https://www.smarty.net/manual/en/language.modifier.indent.php indent (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_indent($params)
{
    if (!isset($params[ 1 ])) {
        $params[ 1 ] = 4;
    }
    if (!isset($params[ 2 ])) {
        $params[ 2 ] = "' '";
    }
    return 'preg_replace(\'!^!m\',str_repeat(' . $params[ 2 ] . ',' . $params[ 1 ] . '),' . $params[ 0 ] . ')';
}
