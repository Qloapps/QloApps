<?php
/**
 * Smarty Internal Plugin Compile extend
 * Compiles the {extends} tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile extend Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Extends extends Smarty_Internal_Compile_Shared_Inheritance
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('file');

    /**
     * Array of names of optional attribute required by tag
     * use array('_any') if there is no restriction of attributes names
     *
     * @var array
     */
    public $optional_attributes = array();

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('file');

    /**
     * Compiles code for the {extends} tag extends: resource
     *
     * @param array                                 $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     * @throws \SmartyException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        if ($_attr[ 'nocache' ] === true) {
            $compiler->trigger_template_error('nocache option not allowed', $compiler->parser->lex->line - 1);
        }
        if (strpos($_attr[ 'file' ], '$_tmp') !== false) {
            $compiler->trigger_template_error('illegal value for file attribute', $compiler->parser->lex->line - 1);
        }
        // add code to initialize inheritance
        $this->registerInit($compiler, true);
        $this->compileEndChild($compiler, $_attr[ 'file' ]);
        $compiler->has_code = false;
        return '';
    }

    /**
     * Add code for inheritance endChild() method to end of template
     *
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     * @param null|string                           $template optional inheritance parent template
     *
     * @throws \SmartyCompilerException
     * @throws \SmartyException
     */
    private function compileEndChild(Smarty_Internal_TemplateCompilerBase $compiler, $template = null)
    {
        $inlineUids = '';
        if (isset($template) && $compiler->smarty->merge_compiled_includes) {
            $code = $compiler->compileTag('include', array($template, array('scope' => 'parent')));
            if (preg_match('/([,][\s]*[\'][a-z0-9]+[\'][,][\s]*[\']content.*[\'])[)]/', $code, $match)) {
                $inlineUids = $match[ 1 ];
            }
        }
        $compiler->parser->template_postfix[] = new Smarty_Internal_ParseTree_Tag(
            $compiler->parser,
            '<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl' .
            (isset($template) ?
                ", {$template}{$inlineUids}" :
                '') . ");\n?>"
        );
    }
}
