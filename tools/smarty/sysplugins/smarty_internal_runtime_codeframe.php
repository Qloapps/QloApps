<?php
/**
 * Smarty Internal Extension
 * This file contains the Smarty template extension to create a code frame
 *
 * @package    Smarty
 * @subpackage Template
 * @author     Uwe Tews
 */

/**
 * Class Smarty_Internal_Extension_CodeFrame
 * Create code frame for compiled and cached templates
 */
class Smarty_Internal_Runtime_CodeFrame
{
    /**
     * Create code frame for compiled and cached templates
     *
     * @param Smarty_Internal_Template                   $_template
     * @param string                                     $content   optional template content
     * @param string                                     $functions compiled template function and block code
     * @param bool                                       $cache     flag for cache file
     * @param \Smarty_Internal_TemplateCompilerBase|null $compiler
     *
     * @return string
     */
    public function create(
        Smarty_Internal_Template $_template,
        $content = '',
        $functions = '',
        $cache = false,
        ?Smarty_Internal_TemplateCompilerBase $compiler = null
    ) {
        // build property code
        $properties[ 'version' ] = Smarty::SMARTY_VERSION;
        $properties[ 'unifunc' ] = 'content_' . str_replace(array('.', ','), '_', uniqid('', true));
        if (!$cache) {
            $properties[ 'has_nocache_code' ] = $_template->compiled->has_nocache_code;
            $properties[ 'file_dependency' ] = $_template->compiled->file_dependency;
            $properties[ 'includes' ] = $_template->compiled->includes;
        } else {
            $properties[ 'has_nocache_code' ] = $_template->cached->has_nocache_code;
            $properties[ 'file_dependency' ] = $_template->cached->file_dependency;
            $properties[ 'cache_lifetime' ] = $_template->cache_lifetime;
        }
        $output = sprintf(
			"<?php\n/* Smarty version %s, created on %s\n  from '%s' */\n\n",
            $properties[ 'version' ],
	        date("Y-m-d H:i:s"),
	        str_replace('*/', '* /', $_template->source->filepath)
        );
        $output .= "/* @var Smarty_Internal_Template \$_smarty_tpl */\n";
        $dec = "\$_smarty_tpl->_decodeProperties(\$_smarty_tpl, " . var_export($properties, true) . ',' .
               ($cache ? 'true' : 'false') . ')';
        $output .= "if ({$dec}) {\n";
        $output .= "function {$properties['unifunc']} (Smarty_Internal_Template \$_smarty_tpl) {\n";
        if (!$cache && !empty($compiler->tpl_function)) {
            $output .= '$_smarty_tpl->smarty->ext->_tplFunction->registerTplFunctions($_smarty_tpl, ';
            $output .= var_export($compiler->tpl_function, true);
            $output .= ");\n";
        }
        if ($cache && isset($_template->smarty->ext->_tplFunction)) {
            $output .= "\$_smarty_tpl->smarty->ext->_tplFunction->registerTplFunctions(\$_smarty_tpl, " .
                       var_export($_template->smarty->ext->_tplFunction->getTplFunction($_template), true) . ");\n";
        }
        $output .= "?>";
        $output .= $content;
        $output .= "<?php }\n?>";
        $output .= $functions;
        $output .= "<?php }\n";
        // remove unneeded PHP tags
        if (preg_match('/\s*\?>[\n]?<\?php\s*/', $output)) {
            $curr_split = preg_split(
                '/\s*\?>[\n]?<\?php\s*/',
                $output
            );
            preg_match_all(
                '/\s*\?>[\n]?<\?php\s*/',
                $output,
                $curr_parts
            );
            $output = '';
            foreach ($curr_split as $idx => $curr_output) {
                $output .= $curr_output;
                if (isset($curr_parts[ 0 ][ $idx ])) {
                    $output .= "\n";
                }
            }
        }
        if (preg_match('/\?>\s*$/', $output)) {
            $curr_split = preg_split(
                '/\?>\s*$/',
                $output
            );
            $output = '';
            foreach ($curr_split as $idx => $curr_output) {
                $output .= $curr_output;
            }
        }
        return $output;
    }
}
