<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */
/**
 * Smarty escape modifier plugin
 * Type:     modifier
 * Name:     escape
 * Purpose:  escape string for output
 *
 * @link   https://www.smarty.net/docsv2/en/language.modifier.escape count_characters (Smarty online manual)
 * @author Rodney Rehm
 *
 * @param array                                $params parameters
 * @param Smarty_Internal_TemplateCompilerBase $compiler
 *
 * @return string with compiled code
 * @throws SmartyException
 */
function smarty_modifiercompiler_escape($params, Smarty_Internal_TemplateCompilerBase $compiler)
{
    $compiler->template->_checkPlugins(
        array(
            array(
                'function' => 'smarty_literal_compiler_param',
                'file'     => SMARTY_PLUGINS_DIR . 'shared.literal_compiler_param.php'
            )
        )
    );
    try {
        $esc_type = smarty_literal_compiler_param($params, 1, 'html');
        $char_set = smarty_literal_compiler_param($params, 2, Smarty::$_CHARSET);
        $double_encode = smarty_literal_compiler_param($params, 3, true);
        if (!$char_set) {
            $char_set = Smarty::$_CHARSET;
        }
        switch ($esc_type) {
            case 'html':
                return 'htmlspecialchars((string)' . $params[ 0 ] . ', ENT_QUOTES, ' . var_export($char_set, true) . ', ' .
                    var_export($double_encode, true) . ')';
            // no break
            case 'htmlall':
                if (Smarty::$_MBSTRING) {
                    return 'htmlentities(mb_convert_encoding((string)' . $params[ 0 ] . ', \'UTF-8\', ' .
                        var_export($char_set, true) . '), ENT_QUOTES, \'UTF-8\', ' .
                        var_export($double_encode, true) . ')';
                }
                // no MBString fallback
                return 'htmlentities((string)' . $params[ 0 ] . ', ENT_QUOTES, ' . var_export($char_set, true) . ', ' .
                    var_export($double_encode, true) . ')';
            // no break
            case 'url':
                return 'rawurlencode((string)' . $params[ 0 ] . ')';
            case 'urlpathinfo':
                return 'str_replace("%2F", "/", rawurlencode((string)' . $params[ 0 ] . '))';
            case 'quotes':
                // escape unescaped single quotes
                return 'preg_replace("%(?<!\\\\\\\\)\'%", "\\\'", (string)' . $params[ 0 ] . ')';
            case 'javascript':
                // escape quotes and backslashes, newlines, etc.
                // see https://html.spec.whatwg.org/multipage/scripting.html#restrictions-for-contents-of-script-elements
                return 'strtr((string)' .
                       $params[ 0 ] .
                       ', array("\\\\" => "\\\\\\\\", "\'" => "\\\\\'", "\"" => "\\\\\"", "\\r" => "\\\\r", 
                       "\\n" => "\\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\\\`", "\${" => "\\\\\\$\\{"))';
        }
    } catch (SmartyException $e) {
        // pass through to regular plugin fallback
    }
    // could not optimize |escape call, so fallback to regular plugin
    if ($compiler->template->caching && ($compiler->tag_nocache | $compiler->nocache)) {
        $compiler->required_plugins[ 'nocache' ][ 'escape' ][ 'modifier' ][ 'file' ] =
            SMARTY_PLUGINS_DIR . 'modifier.escape.php';
        $compiler->required_plugins[ 'nocache' ][ 'escape' ][ 'modifier' ][ 'function' ] =
            'smarty_modifier_escape';
    } else {
        $compiler->required_plugins[ 'compiled' ][ 'escape' ][ 'modifier' ][ 'file' ] =
            SMARTY_PLUGINS_DIR . 'modifier.escape.php';
        $compiler->required_plugins[ 'compiled' ][ 'escape' ][ 'modifier' ][ 'function' ] =
            'smarty_modifier_escape';
    }
    return 'smarty_modifier_escape(' . join(', ', $params) . ')';
}
