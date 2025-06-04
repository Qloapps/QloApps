<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty strip_tags modifier plugin
 * Type:     modifier
 * Name:     strip_tags
 * Purpose:  strip html tags from text
 *
 * @link   https://www.smarty.net/docs/en/language.modifier.strip.tags.tpl strip_tags (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_strip_tags($params)
{
    if (!isset($params[ 1 ]) || $params[ 1 ] === true || trim($params[ 1 ], '"') === 'true') {
        return "preg_replace('!<[^>]*?>!', ' ', (string) {$params[0]})";
    } else {
        return 'strip_tags((string) ' . $params[ 0 ] . ')';
    }
}
