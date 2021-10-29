<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty count_paragraphs modifier plugin
 * Type:     modifier
 * Name:     count_paragraphs
 * Purpose:  count the number of paragraphs in a text
 *
 * @link   http://www.smarty.net/manual/en/language.modifier.count.paragraphs.php
 *          count_paragraphs (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_count_paragraphs($params)
{
    // count \r or \n characters
    return '(preg_match_all(\'#[\r\n]+#\', ' . $params[ 0 ] . ', $tmp)+1)';
}
