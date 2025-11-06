<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifier
 */

function smarty_modifier_implode($values, $separator = '')
{
	if (is_array($separator)) {
		return implode((string) ($values ?? ''), (array) $separator);
	}
	return implode((string) ($separator ?? ''), (array) $values);
}
