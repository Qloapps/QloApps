<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty noprint modifier plugin
 * Type:     modifier
 * Name:     noprint
 * Purpose:  return an empty string
 *
 * @author Uwe Tews
 * @return string with compiled code
 */
function smarty_modifiercompiler_noprint()
{
    return "''";
}
