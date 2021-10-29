<?php
/**
 * Smarty Internal Plugin Compile Make_Nocache
 * Compiles the {make_nocache} tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Make_Nocache Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Make_Nocache extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $option_flags = array();

    /**
     * Array of names of required attribute required by tag
     *
     * @var array
     */
    public $required_attributes = array('var');

    /**
     * Shorttag attribute order defined by its names
     *
     * @var array
     */
    public $shorttag_order = array('var');

    /**
     * Compiles code for the {make_nocache} tag
     *
     * @param array                                 $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        if ($compiler->template->caching) {
            $output = "<?php \$_smarty_tpl->smarty->ext->_make_nocache->save(\$_smarty_tpl, {$_attr[ 'var' ]});\n?>\n";
            $compiler->template->compiled->has_nocache_code = true;
            $compiler->suppressNocacheProcessing = true;
            return $output;
        } else {
            return true;
        }
    }
}
