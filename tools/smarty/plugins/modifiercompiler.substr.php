<?php

/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
function smarty_modifiercompiler_substr($params) {
	return 'substr((string) ' . $params[0] . ', (int) ' . $params[1] .
		(isset($params[2]) ? ', (int) ' . $params[2] : '') . ')';
}
