<?php

/**
 * Smarty Template Resource Base Object
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 */
abstract class Smarty_Template_Resource_Base
{
    /**
     * Compiled Filepath
     *
     * @var string
     */
    public $filepath = null;

    /**
     * Compiled Timestamp
     *
     * @var integer|bool
     */
    public $timestamp = false;

    /**
     * Compiled Existence
     *
     * @var boolean
     */
    public $exists = false;

    /**
     * Template Compile Id (Smarty_Internal_Template::$compile_id)
     *
     * @var string
     */
    public $compile_id = null;

    /**
     * Compiled Content Loaded
     *
     * @var boolean
     */
    public $processed = false;

    /**
     * unique function name for compiled template code
     *
     * @var string
     */
    public $unifunc = '';

    /**
     * flag if template does contain nocache code sections
     *
     * @var bool
     */
    public $has_nocache_code = false;

    /**
     * resource file dependency
     *
     * @var array
     */
    public $file_dependency = array();

    /**
     * Content buffer
     *
     * @var string
     */
    public $content = null;

    /**
     * Included sub templates
     * - index name
     * - value use count
     *
     * @var int[]
     */
    public $includes = array();

    /**
     * Flag if this is a cache resource
     *
     * @var bool
     */
    public $isCache = false;

    /**
     * Process resource
     *
     * @param Smarty_Internal_Template $_template template object
     */
    abstract public function process(Smarty_Internal_Template $_template);

    /**
     * get rendered template content by calling compiled or cached template code
     *
     * @param \Smarty_Internal_Template $_template
     * @param string                    $unifunc function with template code
     *
     * @throws \Exception
     */
    public function getRenderedTemplateCode(Smarty_Internal_Template $_template, $unifunc = null)
    {
        $smarty = &$_template->smarty;
        $_template->isRenderingCache = $this->isCache;
        $level = ob_get_level();
        try {
            if (!isset($unifunc)) {
                $unifunc = $this->unifunc;
            }
            if (empty($unifunc) || !function_exists($unifunc)) {
                throw new SmartyException("Invalid compiled template for '{$_template->template_resource}'");
            }
            if ($_template->startRenderCallbacks) {
                foreach ($_template->startRenderCallbacks as $callback) {
                    call_user_func($callback, $_template);
                }
            }
            $unifunc($_template);
            foreach ($_template->endRenderCallbacks as $callback) {
                call_user_func($callback, $_template);
            }
            $_template->isRenderingCache = false;
        } catch (Exception $e) {
            $_template->isRenderingCache = false;
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            if (isset($smarty->security_policy)) {
                $smarty->security_policy->endTemplate();
            }
            throw $e;
        }
    }

    /**
     * Get compiled time stamp
     *
     * @return int
     */
    public function getTimeStamp()
    {
        if ($this->exists && !$this->timestamp) {
            $this->timestamp = filemtime($this->filepath);
        }
        return $this->timestamp;
    }
}
