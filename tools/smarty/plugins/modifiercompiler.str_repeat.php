<?php

/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty str_repeat modifier plugin
 * Type:     modifier
 * Name:     str_repeat
 * Purpose:  returns string repeated times times
 *
 * @link   https://www.smarty.net/docs/en/language.modifier.str_repeat.tpl str_repeat (Smarty online manual)
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_str_repeat($params) {
    return 'str_repeat((string) ' . $params[0] . ', (int) ' . $params[1] . ')';
}
