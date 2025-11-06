<?php

/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty nl2br modifier plugin
 * Type:     modifier
 * Name:     nl2br
 * Purpose:  insert HTML line breaks before all newlines in a string
 *
 * @link   https://www.smarty.net/docs/en/language.modifier.nl2br.tpl nl2br (Smarty online manual)
  *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_nl2br($params) {
    return 'nl2br((string) ' . $params[0] . ', (bool) ' . ($params[1] ?? true) . ')';
}
