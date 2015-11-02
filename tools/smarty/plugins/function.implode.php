<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {implode} plugin
 *
 * Type:     function<br>
 * Name:     implode<br>
 * Purpose:  implode Array
 * Use: {implode value="" separator=""}
 *
 * @link http://www.smarty.net/manual/en/language.function.fetch.php {fetch}
 *       (Smarty online manual)
 * 
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 * @return string|null if the assign parameter is passed, Smarty assigns the result to a template variable
 */
function smarty_function_implode($params, $template)
{
    if (!isset($params['value']))
    {
        trigger_error("[plugin] implode parameter 'value' cannot be empty", E_USER_NOTICE);
        return;
    }

    if (empty($params['separator']))
		$params['separator'] = ',';

	return implode($params['value'], $params['separator']);
}

?>