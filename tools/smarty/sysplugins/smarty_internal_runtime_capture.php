<?php

/**
 * Runtime Extension Capture
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Runtime_Capture
{
    /**
     * Flag that this instance  will not be cached
     *
     * @var bool
     */
    public $isPrivateExtension = true;

    /**
     * Stack of capture parameter
     *
     * @var array
     */
    private $captureStack = array();

    /**
     * Current open capture sections
     *
     * @var int
     */
    private $captureCount = 0;

    /**
     * Count stack
     *
     * @var int[]
     */
    private $countStack = array();

    /**
     * Named buffer
     *
     * @var string[]
     */
    private $namedBuffer = array();

    /**
     * Flag if callbacks are registered
     *
     * @var bool
     */
    private $isRegistered = false;

    /**
     * Open capture section
     *
     * @param \Smarty_Internal_Template $_template
     * @param string                    $buffer capture name
     * @param string                    $assign variable name
     * @param string                    $append variable name
     */
    public function open(Smarty_Internal_Template $_template, $buffer, $assign, $append)
    {
        if (!$this->isRegistered) {
            $this->register($_template);
        }
        $this->captureStack[] = array(
            $buffer,
            $assign,
            $append
        );
        $this->captureCount++;
        ob_start();
    }

    /**
     * Register callbacks in template class
     *
     * @param \Smarty_Internal_Template $_template
     */
    private function register(Smarty_Internal_Template $_template)
    {
        $_template->startRenderCallbacks[] = array(
            $this,
            'startRender'
        );
        $_template->endRenderCallbacks[] = array(
            $this,
            'endRender'
        );
        $this->startRender($_template);
        $this->isRegistered = true;
    }

    /**
     * Start render callback
     *
     * @param \Smarty_Internal_Template $_template
     */
    public function startRender(Smarty_Internal_Template $_template)
    {
        $this->countStack[] = $this->captureCount;
        $this->captureCount = 0;
    }

    /**
     * Close capture section
     *
     * @param \Smarty_Internal_Template $_template
     *
     * @throws \SmartyException
     */
    public function close(Smarty_Internal_Template $_template)
    {
        if ($this->captureCount) {
            list($buffer, $assign, $append) = array_pop($this->captureStack);
            $this->captureCount--;
            if (isset($assign)) {
                $_template->assign($assign, ob_get_contents());
            }
            if (isset($append)) {
                $_template->append($append, ob_get_contents());
            }
            $this->namedBuffer[ $buffer ] = ob_get_clean();
        } else {
            $this->error($_template);
        }
    }

    /**
     * Error exception on not matching {capture}{/capture}
     *
     * @param \Smarty_Internal_Template $_template
     *
     * @throws \SmartyException
     */
    public function error(Smarty_Internal_Template $_template)
    {
        throw new SmartyException("Not matching {capture}{/capture} in '{$_template->template_resource}'");
    }

    /**
     * Return content of named capture buffer by key or as array
     *
     * @param \Smarty_Internal_Template $_template
     * @param string|null               $name
     *
     * @return string|string[]|null
     */
    public function getBuffer(Smarty_Internal_Template $_template, $name = null)
    {
        if (isset($name)) {
            return isset($this->namedBuffer[ $name ]) ? $this->namedBuffer[ $name ] : null;
        } else {
            return $this->namedBuffer;
        }
    }

    /**
     * End render callback
     *
     * @param \Smarty_Internal_Template $_template
     *
     * @throws \SmartyException
     */
    public function endRender(Smarty_Internal_Template $_template)
    {
        if ($this->captureCount) {
            $this->error($_template);
        } else {
            $this->captureCount = array_pop($this->countStack);
        }
    }
}
