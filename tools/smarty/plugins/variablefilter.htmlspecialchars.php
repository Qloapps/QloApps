<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFilter
 */
/**
 * Smarty htmlspecialchars variablefilter plugin
 *
 * @param string                    $source input string
 * @param \Smarty_Internal_Template $template
 *
 * @return string filtered output
 */
function smarty_variablefilter_htmlspecialchars($source, Smarty_Internal_Template $template)
{
    return htmlspecialchars((string) $source, ENT_QUOTES, Smarty::$_CHARSET);
}
