<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty unescape modifier plugin
 * Type:     modifier
 * Name:     unescape
 * Purpose:  unescape html entities
 *
 * @author Rodney Rehm
 *
 * @param array $params parameters
 * @param Smarty_Internal_TemplateCompilerBase $compiler
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_unescape($params, Smarty_Internal_TemplateCompilerBase $compiler)
{
    $compiler->template->_checkPlugins(
        array(
            array(
                'function' => 'smarty_literal_compiler_param',
                'file'     => SMARTY_PLUGINS_DIR . 'shared.literal_compiler_param.php'
            )
        )
    );

    $esc_type = smarty_literal_compiler_param($params, 1, 'html');

    if (!isset($params[ 2 ])) {
        $params[ 2 ] = '\'' . addslashes(Smarty::$_CHARSET) . '\'';
    }

    switch ($esc_type) {
        case 'entity':
        case 'htmlall':
            if (Smarty::$_MBSTRING) {
                return 'html_entity_decode(mb_convert_encoding(' . $params[ 0 ] . ', ' . $params[ 2 ] . ', \'UTF-8\'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, ' . $params[ 2 ] . ')';
            }
            return 'html_entity_decode(' . $params[ 0 ] . ', ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, ' . $params[ 2 ] . ')';
        case 'html':
            return 'htmlspecialchars_decode(' . $params[ 0 ] . ', ENT_QUOTES)';
        case 'url':
            return 'rawurldecode(' . $params[ 0 ] . ')';
        default:
            return $params[ 0 ];
    }
}
