<?php

/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
function smarty_modifiercompiler_json_encode($params) {
	return 'json_encode(' . $params[0] . (isset($params[1]) ? ', (int) ' . $params[1] : '') . ')';
}
