<?php
/**
 * This file is part of Smarty.
 *
 * (c) 2015 Uwe Tews
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Smarty Internal Plugin Compile Block Class
 *
 * @author Uwe Tews <uwe.tews@googlemail.com>
 */
class Smarty_Internal_Compile_Block extends Smarty_Internal_Compile_Shared_Inheritance
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('name');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('name');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $option_flags = array('hide', 'nocache');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('assign');

    /**
     * Compiles code for the {block} tag
     *
     * @param array                                 $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param array                                 $parameter array with compilation parameter
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        if (!isset($compiler->_cache[ 'blockNesting' ])) {
            $compiler->_cache[ 'blockNesting' ] = 0;
        }
        if ($compiler->_cache[ 'blockNesting' ] === 0) {
            // make sure that inheritance gets initialized in template code
            $this->registerInit($compiler);
            $this->option_flags = array('hide', 'nocache', 'append', 'prepend');
        } else {
            $this->option_flags = array('hide', 'nocache');
        }
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);
        ++$compiler->_cache[ 'blockNesting' ];
        $_className = 'Block_' . preg_replace('![^\w]+!', '_', uniqid(mt_rand(), true));
        $compiler->_cache[ 'blockName' ][ $compiler->_cache[ 'blockNesting' ] ] = $_attr[ 'name' ];
        $compiler->_cache[ 'blockClass' ][ $compiler->_cache[ 'blockNesting' ] ] = $_className;
        $compiler->_cache[ 'blockParams' ][ $compiler->_cache[ 'blockNesting' ] ] = array();
        $compiler->_cache[ 'blockParams' ][ 1 ][ 'subBlocks' ][ trim($_attr[ 'name' ], '"\'') ][] = $_className;
        $this->openTag(
            $compiler,
            'block',
            array(
                $_attr, $compiler->nocache, $compiler->parser->current_buffer,
                $compiler->template->compiled->has_nocache_code,
                $compiler->template->caching
            )
        );
        $compiler->saveRequiredPlugins(true);
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
        $compiler->parser->current_buffer = new Smarty_Internal_ParseTree_Template();
        $compiler->template->compiled->has_nocache_code = false;
        $compiler->suppressNocacheProcessing = true;
    }
}

/**
 * Smarty Internal Plugin Compile BlockClose Class
 */
class Smarty_Internal_Compile_Blockclose extends Smarty_Internal_Compile_Shared_Inheritance
{
    /**
     * Compiles code for the {/block} tag
     *
     * @param array                                 $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param array                                 $parameter array with compilation parameter
     *
     * @return bool true
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        list($_attr, $_nocache, $_buffer, $_has_nocache_code, $_caching) = $this->closeTag($compiler, array('block'));
        // init block parameter
        $_block = $compiler->_cache[ 'blockParams' ][ $compiler->_cache[ 'blockNesting' ] ];
        unset($compiler->_cache[ 'blockParams' ][ $compiler->_cache[ 'blockNesting' ] ]);
        $_name = $_attr[ 'name' ];
        $_assign = isset($_attr[ 'assign' ]) ? $_attr[ 'assign' ] : null;
        unset($_attr[ 'assign' ], $_attr[ 'name' ]);
        foreach ($_attr as $name => $stat) {
            if ((is_bool($stat) && $stat !== false) || (!is_bool($stat) && $stat !== 'false')) {
                $_block[ $name ] = 'true';
            }
        }
        $_className = $compiler->_cache[ 'blockClass' ][ $compiler->_cache[ 'blockNesting' ] ];
        // get compiled block code
        $_functionCode = $compiler->parser->current_buffer;
        // setup buffer for template function code
        $compiler->parser->current_buffer = new Smarty_Internal_ParseTree_Template();
        $output = "<?php\n";
        $output .= "/* {block {$_name}} */\n";
        $output .= "class {$_className} extends Smarty_Internal_Block\n";
        $output .= "{\n";
        foreach ($_block as $property => $value) {
            $output .= "public \${$property} = " . var_export($value, true) . ";\n";
        }
        $output .= "public function callBlock(Smarty_Internal_Template \$_smarty_tpl) {\n";
        $output .= $compiler->compileRequiredPlugins();
        $compiler->restoreRequiredPlugins();
        if ($compiler->template->compiled->has_nocache_code) {
            $output .= "\$_smarty_tpl->cached->hashes['{$compiler->template->compiled->nocache_hash}'] = true;\n";
        }
        if (isset($_assign)) {
            $output .= "ob_start();\n";
        }
        $output .= "?>\n";
        $compiler->parser->current_buffer->append_subtree(
            $compiler->parser,
            new Smarty_Internal_ParseTree_Tag(
                $compiler->parser,
                $output
            )
        );
        $compiler->parser->current_buffer->append_subtree($compiler->parser, $_functionCode);
        $output = "<?php\n";
        if (isset($_assign)) {
            $output .= "\$_smarty_tpl->assign({$_assign}, ob_get_clean());\n";
        }
        $output .= "}\n";
        $output .= "}\n";
        $output .= "/* {/block {$_name}} */\n\n";
        $output .= "?>\n";
        $compiler->parser->current_buffer->append_subtree(
            $compiler->parser,
            new Smarty_Internal_ParseTree_Tag(
                $compiler->parser,
                $output
            )
        );
        $compiler->blockOrFunctionCode .= $compiler->parser->current_buffer->to_smarty_php($compiler->parser);
        $compiler->parser->current_buffer = new Smarty_Internal_ParseTree_Template();
        // restore old status
        $compiler->template->compiled->has_nocache_code = $_has_nocache_code;
        $compiler->tag_nocache = $compiler->nocache;
        $compiler->nocache = $_nocache;
        $compiler->parser->current_buffer = $_buffer;
        $output = "<?php \n";
        if ($compiler->_cache[ 'blockNesting' ] === 1) {
            $output .= "\$_smarty_tpl->inheritance->instanceBlock(\$_smarty_tpl, '$_className', $_name);\n";
        } else {
            $output .= "\$_smarty_tpl->inheritance->instanceBlock(\$_smarty_tpl, '$_className', $_name, \$this->tplIndex);\n";
        }
        $output .= "?>\n";
        --$compiler->_cache[ 'blockNesting' ];
        if ($compiler->_cache[ 'blockNesting' ] === 0) {
            unset($compiler->_cache[ 'blockNesting' ]);
        }
        $compiler->has_code = true;
        $compiler->suppressNocacheProcessing = true;
        return $output;
    }
}
