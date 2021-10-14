<?php
/**
 * Smarty Internal Plugin Compile Object Function
 * Compiles code for registered objects as function
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Object Function Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Private_Object_Function extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('_any');

    /**
     * Compiles code for the execution of function plugin
     *
     * @param array                                 $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param array                                 $parameter array with compilation parameter
     * @param string                                $tag       name of function
     * @param string                                $method    name of method to call
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     * @throws \SmartyException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter, $tag, $method)
    {
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        unset($_attr[ 'nocache' ]);
        $_assign = null;
        if (isset($_attr[ 'assign' ])) {
            $_assign = $_attr[ 'assign' ];
            unset($_attr[ 'assign' ]);
        }
        // method or property ?
        if (is_callable(array($compiler->smarty->registered_objects[ $tag ][ 0 ], $method))) {
            // convert attributes into parameter array string
            if ($compiler->smarty->registered_objects[ $tag ][ 2 ]) {
                $_paramsArray = array();
                foreach ($_attr as $_key => $_value) {
                    if (is_int($_key)) {
                        $_paramsArray[] = "$_key=>$_value";
                    } else {
                        $_paramsArray[] = "'$_key'=>$_value";
                    }
                }
                $_params = 'array(' . implode(',', $_paramsArray) . ')';
                $output = "\$_smarty_tpl->smarty->registered_objects['{$tag}'][0]->{$method}({$_params},\$_smarty_tpl)";
            } else {
                $_params = implode(',', $_attr);
                $output = "\$_smarty_tpl->smarty->registered_objects['{$tag}'][0]->{$method}({$_params})";
            }
        } else {
            // object property
            $output = "\$_smarty_tpl->smarty->registered_objects['{$tag}'][0]->{$method}";
        }
        if (!empty($parameter[ 'modifierlist' ])) {
            $output = $compiler->compileTag(
                'private_modifier',
                array(),
                array('modifierlist' => $parameter[ 'modifierlist' ], 'value' => $output)
            );
        }
        if (empty($_assign)) {
            return "<?php echo {$output};?>\n";
        } else {
            return "<?php \$_smarty_tpl->assign({$_assign},{$output});?>\n";
        }
    }
}
