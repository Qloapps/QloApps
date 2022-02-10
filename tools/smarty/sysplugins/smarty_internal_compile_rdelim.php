<?php
/**
 * Smarty Internal Plugin Compile Rdelim
 * Compiles the {rdelim} tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Rdelim Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Rdelim extends Smarty_Internal_Compile_Ldelim
{
    /**
     * Compiles code for the {rdelim} tag
     * This tag does output the right delimiter.
     *
     * @param array                                 $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        parent::compile($args, $compiler);
        return $compiler->smarty->right_delimiter;
    }
}
