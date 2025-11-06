<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty cat modifier plugin
 * Type:     modifier
 * Name:     cat
 * Date:     Feb 24, 2003
 * Purpose:  catenate a value to a variable
 * Input:    string to catenate
 * Example:  {$var|cat:"foo"}
 *
 * @link   https://www.smarty.net/manual/en/language.modifier.cat.php cat
 *           (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_cat($params)
{
    return '(' . implode(').(', $params) . ')';
}
