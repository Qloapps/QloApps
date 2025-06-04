<?php

/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty strlen modifier plugin
 * Type:     modifier
 * Name:     strlen
 * Purpose:  return the length of the given string
 *
 * @link   https://www.smarty.net/docs/en/language.modifier.strlen.tpl strlen (Smarty online manual)
  *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_strlen($params) {
    return 'strlen((string) ' . $params[0] . ')';
}
