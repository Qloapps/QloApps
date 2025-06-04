<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty default modifier plugin
 * Type:     modifier
 * Name:     default
 * Purpose:  designate default value for empty variables
 *
 * @link   https://www.smarty.net/manual/en/language.modifier.default.php default (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_default($params)
{
    $output = $params[ 0 ];
    if (!isset($params[ 1 ])) {
        $params[ 1 ] = "''";
    }
    array_shift($params);
    foreach ($params as $param) {
        $output = '(($tmp = ' . $output . ' ?? null)===null||$tmp===\'\' ? ' . $param . ' ?? null : $tmp)';
    }
    return $output;
}
