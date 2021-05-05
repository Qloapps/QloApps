<?php
/**
 * Smarty Internal Plugin Smarty Template  Base
 * This file contains the basic shared methods for template handling
 *
 * @package    Smarty
 * @subpackage Template
 * @author     Uwe Tews
 */

/**
 * Class with shared smarty/template methods
 *
 * @package    Smarty
 * @subpackage Template
 *
 * @property int $_objType
 *
 * The following methods will be dynamically loaded by the extension handler when they are called.
 * They are located in a corresponding Smarty_Internal_Method_xxxx class
 *
 * @method Smarty_Internal_TemplateBase addAutoloadFilters(mixed $filters, string $type = null)
 * @method Smarty_Internal_TemplateBase addDefaultModifiers(mixed $modifiers)
 * @method Smarty_Internal_TemplateBase addLiterals(mixed $literals)
 * @method Smarty_Internal_TemplateBase createData(Smarty_Internal_Data $parent = null, string $name = null)
 * @method array getAutoloadFilters(string $type = null)
 * @method string getDebugTemplate()
 * @method array getDefaultModifier()
 * @method array getLiterals()
 * @method array getTags(mixed $template = null)
 * @method object getRegisteredObject(string $object_name)
 * @method Smarty_Internal_TemplateBase registerCacheResource(string $name, Smarty_CacheResource $resource_handler)
 * @method Smarty_Internal_TemplateBase registerClass(string $class_name, string $class_impl)
 * @method Smarty_Internal_TemplateBase registerDefaultConfigHandler(callback $callback)
 * @method Smarty_Internal_TemplateBase registerDefaultPluginHandler(callback $callback)
 * @method Smarty_Internal_TemplateBase registerDefaultTemplateHandler(callback $callback)
 * @method Smarty_Internal_TemplateBase registerResource(string $name, mixed $resource_handler)
 * @method Smarty_Internal_TemplateBase setAutoloadFilters(mixed $filters, string $type = null)
 * @method Smarty_Internal_TemplateBase setDebugTemplate(string $tpl_name)
 * @method Smarty_Internal_TemplateBase setDefaultModifiers(mixed $modifiers)
 * @method Smarty_Internal_TemplateBase setLiterals(mixed $literals)
 * @method Smarty_Internal_TemplateBase unloadFilter(string $type, string $name)
 * @method Smarty_Internal_TemplateBase unregisterCacheResource(string $name)
 * @method Smarty_Internal_TemplateBase unregisterObject(string $object_name)
 * @method Smarty_Internal_TemplateBase unregisterPlugin(string $type, string $name)
 * @method Smarty_Internal_TemplateBase unregisterFilter(string $type, mixed $callback)
 * @method Smarty_Internal_TemplateBase unregisterResource(string $name)
 */
abstract class Smarty_Internal_TemplateBase extends Smarty_Internal_Data
{
    /**
     * Set this if you want different sets of cache files for the same
     * templates.
     *
     * @var string
     */
    public $cache_id = null;

    /**
     * Set this if you want different sets of compiled files for the same
     * templates.
     *
     * @var string
     */
    public $compile_id = null;

    /**
     * caching enabled
     *
     * @var int
     */
    public $caching = Smarty::CACHING_OFF;

    /**
     * check template for modifications?
     *
     * @var int
     */
    public $compile_check = Smarty::COMPILECHECK_ON;

    /**
     * cache lifetime in seconds
     *
     * @var integer
     */
    public $cache_lifetime = 3600;

    /**
     * Array of source information for known template functions
     *
     * @var array
     */
    public $tplFunctions = array();

    /**
     * universal cache
     *
     * @var array()
     */
    public $_cache = array();

    /**
     * fetches a rendered Smarty template
     *
     * @param string $template   the resource handle of the template file or template object
     * @param mixed  $cache_id   cache id to be used with this template
     * @param mixed  $compile_id compile id to be used with this template
     * @param object $parent     next higher level of Smarty variables
     *
     * @throws Exception
     * @throws SmartyException
     * @return string rendered template output
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        $result = $this->_execute($template, $cache_id, $compile_id, $parent, 0);
        return $result === null ? ob_get_clean() : $result;
    }

    /**
     * displays a Smarty template
     *
     * @param string $template   the resource handle of the template file or template object
     * @param mixed  $cache_id   cache id to be used with this template
     * @param mixed  $compile_id compile id to be used with this template
     * @param object $parent     next higher level of Smarty variables
     *
     * @throws \Exception
     * @throws \SmartyException
     */
    public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        // display template
        $this->_execute($template, $cache_id, $compile_id, $parent, 1);
    }

    /**
     * test if cache is valid
     *
     * @api  Smarty::isCached()
     * @link http://www.smarty.net/docs/en/api.is.cached.tpl
     *
     * @param null|string|\Smarty_Internal_Template $template   the resource handle of the template file or template
     *                                                          object
     * @param mixed                                 $cache_id   cache id to be used with this template
     * @param mixed                                 $compile_id compile id to be used with this template
     * @param object                                $parent     next higher level of Smarty variables
     *
     * @return bool cache status
     * @throws \Exception
     * @throws \SmartyException
     */
    public function isCached($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        return $this->_execute($template, $cache_id, $compile_id, $parent, 2);
    }

    /**
     * fetches a rendered Smarty template
     *
     * @param string $template   the resource handle of the template file or template object
     * @param mixed  $cache_id   cache id to be used with this template
     * @param mixed  $compile_id compile id to be used with this template
     * @param object $parent     next higher level of Smarty variables
     * @param string $function   function type 0 = fetch,  1 = display, 2 = isCache
     *
     * @return mixed
     * @throws \Exception
     * @throws \SmartyException
     */
    private function _execute($template, $cache_id, $compile_id, $parent, $function)
    {
        $smarty = $this->_getSmartyObj();
        $saveVars = true;
        if ($template === null) {
            if (!$this->_isTplObj()) {
                throw new SmartyException($function . '():Missing \'$template\' parameter');
            } else {
                $template = $this;
            }
        } elseif (is_object($template)) {
            /* @var Smarty_Internal_Template $template */
            if (!isset($template->_objType) || !$template->_isTplObj()) {
                throw new SmartyException($function . '():Template object expected');
            }
        } else {
            // get template object
            $saveVars = false;
            $template = $smarty->createTemplate($template, $cache_id, $compile_id, $parent ? $parent : $this, false);
            if ($this->_objType === 1) {
                // set caching in template object
                $template->caching = $this->caching;
            }
        }
        // make sure we have integer values
        $template->caching = (int)$template->caching;
        // fetch template content
        $level = ob_get_level();
        try {
            $_smarty_old_error_level =
                isset($smarty->error_reporting) ? error_reporting($smarty->error_reporting) : null;
            if ($this->_objType === 2) {
                /* @var Smarty_Internal_Template $this */
                $template->tplFunctions = $this->tplFunctions;
                $template->inheritance = $this->inheritance;
            }
            /* @var Smarty_Internal_Template $parent */
            if (isset($parent->_objType) && ($parent->_objType === 2) && !empty($parent->tplFunctions)) {
                $template->tplFunctions = array_merge($parent->tplFunctions, $template->tplFunctions);
            }
            if ($function === 2) {
                if ($template->caching) {
                    // return cache status of template
                    if (!isset($template->cached)) {
                        $template->loadCached();
                    }
                    $result = $template->cached->isCached($template);
                    Smarty_Internal_Template::$isCacheTplObj[ $template->_getTemplateId() ] = $template;
                } else {
                    return false;
                }
            } else {
                if ($saveVars) {
                    $savedTplVars = $template->tpl_vars;
                    $savedConfigVars = $template->config_vars;
                }
                ob_start();
                $template->_mergeVars();
                if (!empty(Smarty::$global_tpl_vars)) {
                    $template->tpl_vars = array_merge(Smarty::$global_tpl_vars, $template->tpl_vars);
                }
                $result = $template->render(false, $function);
                $template->_cleanUp();
                if ($saveVars) {
                    $template->tpl_vars = $savedTplVars;
                    $template->config_vars = $savedConfigVars;
                } else {
                    if (!$function && !isset(Smarty_Internal_Template::$tplObjCache[ $template->templateId ])) {
                        $template->parent = null;
                        $template->tpl_vars = $template->config_vars = array();
                        Smarty_Internal_Template::$tplObjCache[ $template->templateId ] = $template;
                    }
                }
            }
            if (isset($_smarty_old_error_level)) {
                error_reporting($_smarty_old_error_level);
            }
            return $result;
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            if (isset($_smarty_old_error_level)) {
                error_reporting($_smarty_old_error_level);
            }
            throw $e;
        }
    }

    /**
     * Registers plugin to be used in templates
     *
     * @api  Smarty::registerPlugin()
     * @link http://www.smarty.net/docs/en/api.register.plugin.tpl
     *
     * @param string   $type       plugin type
     * @param string   $name       name of template tag
     * @param callable $callback   PHP callback to register
     * @param bool     $cacheable  if true (default) this function is cache able
     * @param mixed    $cache_attr caching attributes if any
     *
     * @return \Smarty|\Smarty_Internal_Template
     * @throws \SmartyException
     */
    public function registerPlugin($type, $name, $callback, $cacheable = true, $cache_attr = null)
    {
        return $this->ext->registerPlugin->registerPlugin($this, $type, $name, $callback, $cacheable, $cache_attr);
    }

    /**
     * load a filter of specified type and name
     *
     * @api  Smarty::loadFilter()
     * @link http://www.smarty.net/docs/en/api.load.filter.tpl
     *
     * @param string $type filter type
     * @param string $name filter name
     *
     * @return bool
     * @throws \SmartyException
     */
    public function loadFilter($type, $name)
    {
        return $this->ext->loadFilter->loadFilter($this, $type, $name);
    }

    /**
     * Registers a filter function
     *
     * @api  Smarty::registerFilter()
     * @link http://www.smarty.net/docs/en/api.register.filter.tpl
     *
     * @param string      $type filter type
     * @param callable    $callback
     * @param string|null $name optional filter name
     *
     * @return \Smarty|\Smarty_Internal_Template
     * @throws \SmartyException
     */
    public function registerFilter($type, $callback, $name = null)
    {
        return $this->ext->registerFilter->registerFilter($this, $type, $callback, $name);
    }

    /**
     * Registers object to be used in templates
     *
     * @api  Smarty::registerObject()
     * @link http://www.smarty.net/docs/en/api.register.object.tpl
     *
     * @param string $object_name
     * @param object $object                     the referenced PHP object to register
     * @param array  $allowed_methods_properties list of allowed methods (empty = all)
     * @param bool   $format                     smarty argument format, else traditional
     * @param array  $block_methods              list of block-methods
     *
     * @return \Smarty|\Smarty_Internal_Template
     * @throws \SmartyException
     */
    public function registerObject(
        $object_name,
        $object,
        $allowed_methods_properties = array(),
        $format = true,
        $block_methods = array()
    ) {
        return $this->ext->registerObject->registerObject(
            $this,
            $object_name,
            $object,
            $allowed_methods_properties,
            $format,
            $block_methods
        );
    }

    /**
     * @param int $compile_check
     */
    public function setCompileCheck($compile_check)
    {
        $this->compile_check = (int)$compile_check;
    }

    /**
     * @param int $caching
     */
    public function setCaching($caching)
    {
        $this->caching = (int)$caching;
    }

    /**
     * @param int $cache_lifetime
     */
    public function setCacheLifetime($cache_lifetime)
    {
        $this->cache_lifetime = $cache_lifetime;
    }

    /**
     * @param string $compile_id
     */
    public function setCompileId($compile_id)
    {
        $this->compile_id = $compile_id;
    }

    /**
     * @param string $cache_id
     */
    public function setCacheId($cache_id)
    {
        $this->cache_id = $cache_id;
    }
}
