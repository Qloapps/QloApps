<?php
/**
 * Project:     Smarty: the PHP compiling template engine
 * File:        Smarty.class.php
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * For questions, help, comments, discussion, etc., please join the
 * Smarty mailing list. Send a blank e-mail to
 * smarty-discussion-subscribe@googlegroups.com
 *
 * @link      http://www.smarty.net/
 * @copyright 2018 New Digital Group, Inc.
 * @copyright 2018 Uwe Tews
 * @author    Monte Ohrt <monte at ohrt dot com>
 * @author    Uwe Tews   <uwe dot tews at gmail dot com>
 * @author    Rodney Rehm
 * @package   Smarty
 */
/**
 * set SMARTY_DIR to absolute path to Smarty library files.
 * Sets SMARTY_DIR only if user application has not already defined it.
 */
if (!defined('SMARTY_DIR')) {
    /**
     *
     */
    define('SMARTY_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
/**
 * set SMARTY_SYSPLUGINS_DIR to absolute path to Smarty internal plugins.
 * Sets SMARTY_SYSPLUGINS_DIR only if user application has not already defined it.
 */
if (!defined('SMARTY_SYSPLUGINS_DIR')) {
    /**
     *
     */
    define('SMARTY_SYSPLUGINS_DIR', SMARTY_DIR . 'sysplugins' . DIRECTORY_SEPARATOR);
}
if (!defined('SMARTY_PLUGINS_DIR')) {
    /**
     *
     */
    define('SMARTY_PLUGINS_DIR', SMARTY_DIR . 'plugins' . DIRECTORY_SEPARATOR);
}
if (!defined('SMARTY_MBSTRING')) {
    /**
     *
     */
    define('SMARTY_MBSTRING', function_exists('mb_get_info'));
}
if (!defined('SMARTY_RESOURCE_CHAR_SET')) {
    // UTF-8 can only be done properly when mbstring is available!
    /**
     * @deprecated in favor of Smarty::$_CHARSET
     */
    define('SMARTY_RESOURCE_CHAR_SET', SMARTY_MBSTRING ? 'UTF-8' : 'ISO-8859-1');
}
if (!defined('SMARTY_RESOURCE_DATE_FORMAT')) {
    /**
     * @deprecated in favor of Smarty::$_DATE_FORMAT
     */
    define('SMARTY_RESOURCE_DATE_FORMAT', '%b %e, %Y');
}
/**
 * Load Smarty_Autoloader
 */
if (!class_exists('Smarty_Autoloader')) {
    include dirname(__FILE__) . '/bootstrap.php';
}
/**
 * Load always needed external class files
 */
require_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_data.php';
require_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_extension_handler.php';
require_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_templatebase.php';
require_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_template.php';
require_once SMARTY_SYSPLUGINS_DIR . 'smarty_resource.php';
require_once SMARTY_SYSPLUGINS_DIR . 'smarty_variable.php';
require_once SMARTY_SYSPLUGINS_DIR . 'smarty_template_source.php';
require_once SMARTY_SYSPLUGINS_DIR . 'smarty_template_resource_base.php';
require_once SMARTY_SYSPLUGINS_DIR . 'smarty_internal_resource_file.php';

/**
 * This is the main Smarty class
 *
 * @package Smarty
 *
 * The following methods will be dynamically loaded by the extension handler when they are called.
 * They are located in a corresponding Smarty_Internal_Method_xxxx class
 *
 * @method int clearAllCache(int $exp_time = null, string $type = null)
 * @method int clearCache(string $template_name, string $cache_id = null, string $compile_id = null, int $exp_time = null, string $type = null)
 * @method int compileAllTemplates(string $extension = '.tpl', bool $force_compile = false, int $time_limit = 0, $max_errors = null)
 * @method int compileAllConfig(string $extension = '.conf', bool $force_compile = false, int $time_limit = 0, $max_errors = null)
 * @method int clearCompiledTemplate($resource_name = null, $compile_id = null, $exp_time = null)
 */
class Smarty extends Smarty_Internal_TemplateBase
{
    /**
     * smarty version
     */
    const SMARTY_VERSION = '3.1.39';
    /**
     * define variable scopes
     */
    const SCOPE_LOCAL    = 1;
    const SCOPE_PARENT   = 2;
    const SCOPE_TPL_ROOT = 4;
    const SCOPE_ROOT     = 8;
    const SCOPE_SMARTY   = 16;
    const SCOPE_GLOBAL   = 32;
    /**
     * define caching modes
     */
    const CACHING_OFF              = 0;
    const CACHING_LIFETIME_CURRENT = 1;
    const CACHING_LIFETIME_SAVED   = 2;
    /**
     * define constant for clearing cache files be saved expiration dates
     */
    const CLEAR_EXPIRED = -1;
    /**
     * define compile check modes
     */
    const COMPILECHECK_OFF       = 0;
    const COMPILECHECK_ON        = 1;
    const COMPILECHECK_CACHEMISS = 2;
    /**
     * define debug modes
     */
    const DEBUG_OFF        = 0;
    const DEBUG_ON         = 1;
    const DEBUG_INDIVIDUAL = 2;
    /**
     * modes for handling of "<?php ... ?>" tags in templates.
     */
    const PHP_PASSTHRU = 0; //-> print tags as plain text
    const PHP_QUOTE    = 1; //-> escape tags as entities
    const PHP_REMOVE   = 2; //-> escape tags as entities
    const PHP_ALLOW    = 3; //-> escape tags as entities
    /**
     * filter types
     */
    const FILTER_POST     = 'post';
    const FILTER_PRE      = 'pre';
    const FILTER_OUTPUT   = 'output';
    const FILTER_VARIABLE = 'variable';
    /**
     * plugin types
     */
    const PLUGIN_FUNCTION         = 'function';
    const PLUGIN_BLOCK            = 'block';
    const PLUGIN_COMPILER         = 'compiler';
    const PLUGIN_MODIFIER         = 'modifier';
    const PLUGIN_MODIFIERCOMPILER = 'modifiercompiler';

    /**
     * assigned global tpl vars
     */
    public static $global_tpl_vars = array();

    /**
     * Flag denoting if Multibyte String functions are available
     */
    public static $_MBSTRING = SMARTY_MBSTRING;

    /**
     * The character set to adhere to (e.g. "UTF-8")
     */
    public static $_CHARSET = SMARTY_RESOURCE_CHAR_SET;

    /**
     * The date format to be used internally
     * (accepts date() and strftime())
     */
    public static $_DATE_FORMAT = SMARTY_RESOURCE_DATE_FORMAT;

    /**
     * Flag denoting if PCRE should run in UTF-8 mode
     */
    public static $_UTF8_MODIFIER = 'u';

    /**
     * Flag denoting if operating system is windows
     */
    public static $_IS_WINDOWS = false;

    /**
     * auto literal on delimiters with whitespace
     *
     * @var boolean
     */
    public $auto_literal = true;

    /**
     * display error on not assigned variables
     *
     * @var boolean
     */
    public $error_unassigned = false;

    /**
     * look up relative file path in include_path
     *
     * @var boolean
     */
    public $use_include_path = false;

    /**
     * flag if template_dir is normalized
     *
     * @var bool
     */
    public $_templateDirNormalized = false;

    /**
     * joined template directory string used in cache keys
     *
     * @var string
     */
    public $_joined_template_dir = null;

    /**
     * flag if config_dir is normalized
     *
     * @var bool
     */
    public $_configDirNormalized = false;

    /**
     * joined config directory string used in cache keys
     *
     * @var string
     */
    public $_joined_config_dir = null;

    /**
     * default template handler
     *
     * @var callable
     */
    public $default_template_handler_func = null;

    /**
     * default config handler
     *
     * @var callable
     */
    public $default_config_handler_func = null;

    /**
     * default plugin handler
     *
     * @var callable
     */
    public $default_plugin_handler_func = null;

    /**
     * flag if template_dir is normalized
     *
     * @var bool
     */
    public $_compileDirNormalized = false;

    /**
     * flag if plugins_dir is normalized
     *
     * @var bool
     */
    public $_pluginsDirNormalized = false;

    /**
     * flag if template_dir is normalized
     *
     * @var bool
     */
    public $_cacheDirNormalized = false;

    /**
     * force template compiling?
     *
     * @var boolean
     */
    public $force_compile = false;

    /**
     * use sub dirs for compiled/cached files?
     *
     * @var boolean
     */
    public $use_sub_dirs = false;

    /**
     * allow ambiguous resources (that are made unique by the resource handler)
     *
     * @var boolean
     */
    public $allow_ambiguous_resources = false;

    /**
     * merge compiled includes
     *
     * @var boolean
     */
    public $merge_compiled_includes = false;

    /*
    * flag for behaviour when extends: resource  and {extends} tag are used simultaneous
    *   if false disable execution of {extends} in templates called by extends resource.
    *   (behaviour as versions < 3.1.28)
    *
    * @var boolean
    */
    public $extends_recursion = true;

    /**
     * force cache file creation
     *
     * @var boolean
     */
    public $force_cache = false;

    /**
     * template left-delimiter
     *
     * @var string
     */
    public $left_delimiter = "{";

    /**
     * template right-delimiter
     *
     * @var string
     */
    public $right_delimiter = "}";

    /**
     * array of strings which shall be treated as literal by compiler
     *
     * @var array string
     */
    public $literals = array();

    /**
     * class name
     * This should be instance of Smarty_Security.
     *
     * @var string
     * @see Smarty_Security
     */
    public $security_class = 'Smarty_Security';

    /**
     * implementation of security class
     *
     * @var Smarty_Security
     */
    public $security_policy = null;

    /**
     * controls handling of PHP-blocks
     *
     * @var integer
     */
    public $php_handling = self::PHP_PASSTHRU;

    /**
     * controls if the php template file resource is allowed
     *
     * @var bool
     */
    public $allow_php_templates = false;

    /**
     * debug mode
     * Setting this to true enables the debug-console.
     *
     * @var boolean
     */
    public $debugging = false;

    /**
     * This determines if debugging is enable-able from the browser.
     * <ul>
     *  <li>NONE => no debugging control allowed</li>
     *  <li>URL => enable debugging when SMARTY_DEBUG is found in the URL.</li>
     * </ul>
     *
     * @var string
     */
    public $debugging_ctrl = 'NONE';

    /**
     * Name of debugging URL-param.
     * Only used when $debugging_ctrl is set to 'URL'.
     * The name of the URL-parameter that activates debugging.
     *
     * @var string
     */
    public $smarty_debug_id = 'SMARTY_DEBUG';

    /**
     * Path of debug template.
     *
     * @var string
     */
    public $debug_tpl = null;

    /**
     * When set, smarty uses this value as error_reporting-level.
     *
     * @var int
     */
    public $error_reporting = null;

    /**
     * Controls whether variables with the same name overwrite each other.
     *
     * @var boolean
     */
    public $config_overwrite = true;

    /**
     * Controls whether config values of on/true/yes and off/false/no get converted to boolean.
     *
     * @var boolean
     */
    public $config_booleanize = true;

    /**
     * Controls whether hidden config sections/vars are read from the file.
     *
     * @var boolean
     */
    public $config_read_hidden = false;

    /**
     * locking concurrent compiles
     *
     * @var boolean
     */
    public $compile_locking = true;

    /**
     * Controls whether cache resources should use locking mechanism
     *
     * @var boolean
     */
    public $cache_locking = false;

    /**
     * seconds to wait for acquiring a lock before ignoring the write lock
     *
     * @var float
     */
    public $locking_timeout = 10;

    /**
     * resource type used if none given
     * Must be an valid key of $registered_resources.
     *
     * @var string
     */
    public $default_resource_type = 'file';

    /**
     * caching type
     * Must be an element of $cache_resource_types.
     *
     * @var string
     */
    public $caching_type = 'file';

    /**
     * config type
     *
     * @var string
     */
    public $default_config_type = 'file';

    /**
     * check If-Modified-Since headers
     *
     * @var boolean
     */
    public $cache_modified_check = false;

    /**
     * registered plugins
     *
     * @var array
     */
    public $registered_plugins = array();

    /**
     * registered objects
     *
     * @var array
     */
    public $registered_objects = array();

    /**
     * registered classes
     *
     * @var array
     */
    public $registered_classes = array();

    /**
     * registered filters
     *
     * @var array
     */
    public $registered_filters = array();

    /**
     * registered resources
     *
     * @var array
     */
    public $registered_resources = array();

    /**
     * registered cache resources
     *
     * @var array
     */
    public $registered_cache_resources = array();

    /**
     * autoload filter
     *
     * @var array
     */
    public $autoload_filters = array();

    /**
     * default modifier
     *
     * @var array
     */
    public $default_modifiers = array();

    /**
     * autoescape variable output
     *
     * @var boolean
     */
    public $escape_html = false;

    /**
     * start time for execution time calculation
     *
     * @var int
     */
    public $start_time = 0;

    /**
     * required by the compiler for BC
     *
     * @var string
     */
    public $_current_file = null;

    /**
     * internal flag to enable parser debugging
     *
     * @var bool
     */
    public $_parserdebug = false;

    /**
     * This object type (Smarty = 1, template = 2, data = 4)
     *
     * @var int
     */
    public $_objType = 1;

    /**
     * Debug object
     *
     * @var Smarty_Internal_Debug
     */
    public $_debug = null;

    /**
     * template directory
     *
     * @var array
     */
    protected $template_dir = array('./templates/');

    /**
     * flags for normalized template directory entries
     *
     * @var array
     */
    protected $_processedTemplateDir = array();

    /**
     * config directory
     *
     * @var array
     */
    protected $config_dir = array('./configs/');

    /**
     * flags for normalized template directory entries
     *
     * @var array
     */
    protected $_processedConfigDir = array();

    /**
     * compile directory
     *
     * @var string
     */
    protected $compile_dir = './templates_c/';

    /**
     * plugins directory
     *
     * @var array
     */
    protected $plugins_dir = array();

    /**
     * cache directory
     *
     * @var string
     */
    protected $cache_dir = './cache/';

    /**
     * removed properties
     *
     * @var string[]
     */
    protected $obsoleteProperties = array(
        'resource_caching', 'template_resource_caching', 'direct_access_security',
        '_dir_perms', '_file_perms', 'plugin_search_order',
        'inheritance_merge_compiled_includes', 'resource_cache_mode',
    );

    /**
     * List of private properties which will call getter/setter on a direct access
     *
     * @var string[]
     */
    protected $accessMap = array(
        'template_dir' => 'TemplateDir', 'config_dir' => 'ConfigDir',
        'plugins_dir'  => 'PluginsDir', 'compile_dir' => 'CompileDir',
        'cache_dir'    => 'CacheDir',
    );

    /**
     * Initialize new Smarty object
     */
    public function __construct()
    {
        $this->_clearTemplateCache();
        parent::__construct();
        if (is_callable('mb_internal_encoding')) {
            mb_internal_encoding(Smarty::$_CHARSET);
        }
        $this->start_time = microtime(true);
        if (isset($_SERVER[ 'SCRIPT_NAME' ])) {
            Smarty::$global_tpl_vars[ 'SCRIPT_NAME' ] = new Smarty_Variable($_SERVER[ 'SCRIPT_NAME' ]);
        }
        // Check if we're running on windows
        Smarty::$_IS_WINDOWS = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        // let PCRE (preg_*) treat strings as ISO-8859-1 if we're not dealing with UTF-8
        if (Smarty::$_CHARSET !== 'UTF-8') {
            Smarty::$_UTF8_MODIFIER = '';
        }
    }

    /**
     * Enable error handler to mute expected messages
     *
     * @return     boolean
     * @deprecated
     */
    public static function muteExpectedErrors()
    {
        return Smarty_Internal_ErrorHandler::muteExpectedErrors();
    }

    /**
     * Disable error handler muting expected messages
     *
     * @deprecated
     */
    public static function unmuteExpectedErrors()
    {
        restore_error_handler();
    }

    /**
     * Check if a template resource exists
     *
     * @param string $resource_name template name
     *
     * @return bool status
     * @throws \SmartyException
     */
    public function templateExists($resource_name)
    {
        // create source object
        $source = Smarty_Template_Source::load(null, $this, $resource_name);
        return $source->exists;
    }

    /**
     * Loads security class and enables security
     *
     * @param string|Smarty_Security $security_class if a string is used, it must be class-name
     *
     * @return Smarty                 current Smarty instance for chaining
     * @throws \SmartyException
     */
    public function enableSecurity($security_class = null)
    {
        Smarty_Security::enableSecurity($this, $security_class);
        return $this;
    }

    /**
     * Disable security
     *
     * @return Smarty current Smarty instance for chaining
     */
    public function disableSecurity()
    {
        $this->security_policy = null;
        return $this;
    }

    /**
     * Add template directory(s)
     *
     * @param string|array $template_dir directory(s) of template sources
     * @param string       $key          of the array element to assign the template dir to
     * @param bool         $isConfig     true for config_dir
     *
     * @return Smarty          current Smarty instance for chaining
     */
    public function addTemplateDir($template_dir, $key = null, $isConfig = false)
    {
        if ($isConfig) {
            $processed = &$this->_processedConfigDir;
            $dir = &$this->config_dir;
            $this->_configDirNormalized = false;
        } else {
            $processed = &$this->_processedTemplateDir;
            $dir = &$this->template_dir;
            $this->_templateDirNormalized = false;
        }
        if (is_array($template_dir)) {
            foreach ($template_dir as $k => $v) {
                if (is_int($k)) {
                    // indexes are not merged but appended
                    $dir[] = $v;
                } else {
                    // string indexes are overridden
                    $dir[ $k ] = $v;
                    unset($processed[ $key ]);
                }
            }
        } else {
            if ($key !== null) {
                // override directory at specified index
                $dir[ $key ] = $template_dir;
                unset($processed[ $key ]);
            } else {
                // append new directory
                $dir[] = $template_dir;
            }
        }
        return $this;
    }

    /**
     * Get template directories
     *
     * @param mixed $index    index of directory to get, null to get all
     * @param bool  $isConfig true for config_dir
     *
     * @return array|string list of template directories, or directory of $index
     */
    public function getTemplateDir($index = null, $isConfig = false)
    {
        if ($isConfig) {
            $dir = &$this->config_dir;
        } else {
            $dir = &$this->template_dir;
        }
        if ($isConfig ? !$this->_configDirNormalized : !$this->_templateDirNormalized) {
            $this->_normalizeTemplateConfig($isConfig);
        }
        if ($index !== null) {
            return isset($dir[ $index ]) ? $dir[ $index ] : null;
        }
        return $dir;
    }

    /**
     * Set template directory
     *
     * @param string|array $template_dir directory(s) of template sources
     * @param bool         $isConfig     true for config_dir
     *
     * @return \Smarty current Smarty instance for chaining
     */
    public function setTemplateDir($template_dir, $isConfig = false)
    {
        if ($isConfig) {
            $this->config_dir = array();
            $this->_processedConfigDir = array();
        } else {
            $this->template_dir = array();
            $this->_processedTemplateDir = array();
        }
        $this->addTemplateDir($template_dir, null, $isConfig);
        return $this;
    }

    /**
     * Add config directory(s)
     *
     * @param string|array $config_dir directory(s) of config sources
     * @param mixed        $key        key of the array element to assign the config dir to
     *
     * @return Smarty current Smarty instance for chaining
     */
    public function addConfigDir($config_dir, $key = null)
    {
        return $this->addTemplateDir($config_dir, $key, true);
    }

    /**
     * Get config directory
     *
     * @param mixed $index index of directory to get, null to get all
     *
     * @return array configuration directory
     */
    public function getConfigDir($index = null)
    {
        return $this->getTemplateDir($index, true);
    }

    /**
     * Set config directory
     *
     * @param $config_dir
     *
     * @return Smarty       current Smarty instance for chaining
     */
    public function setConfigDir($config_dir)
    {
        return $this->setTemplateDir($config_dir, true);
    }

    /**
     * Adds directory of plugin files
     *
     * @param null|array|string $plugins_dir
     *
     * @return Smarty current Smarty instance for chaining
     */
    public function addPluginsDir($plugins_dir)
    {
        if (empty($this->plugins_dir)) {
            $this->plugins_dir[] = SMARTY_PLUGINS_DIR;
        }
        $this->plugins_dir = array_merge($this->plugins_dir, (array)$plugins_dir);
        $this->_pluginsDirNormalized = false;
        return $this;
    }

    /**
     * Get plugin directories
     *
     * @return array list of plugin directories
     */
    public function getPluginsDir()
    {
        if (empty($this->plugins_dir)) {
            $this->plugins_dir[] = SMARTY_PLUGINS_DIR;
            $this->_pluginsDirNormalized = false;
        }
        if (!$this->_pluginsDirNormalized) {
            if (!is_array($this->plugins_dir)) {
                $this->plugins_dir = (array)$this->plugins_dir;
            }
            foreach ($this->plugins_dir as $k => $v) {
                $this->plugins_dir[ $k ] = $this->_realpath(rtrim($v, '/\\') . DIRECTORY_SEPARATOR, true);
            }
            $this->_cache[ 'plugin_files' ] = array();
            $this->_pluginsDirNormalized = true;
        }
        return $this->plugins_dir;
    }

    /**
     * Set plugins directory
     *
     * @param string|array $plugins_dir directory(s) of plugins
     *
     * @return Smarty       current Smarty instance for chaining
     */
    public function setPluginsDir($plugins_dir)
    {
        $this->plugins_dir = (array)$plugins_dir;
        $this->_pluginsDirNormalized = false;
        return $this;
    }

    /**
     * Get compiled directory
     *
     * @return string path to compiled templates
     */
    public function getCompileDir()
    {
        if (!$this->_compileDirNormalized) {
            $this->_normalizeDir('compile_dir', $this->compile_dir);
            $this->_compileDirNormalized = true;
        }
        return $this->compile_dir;
    }

    /**
     *
     * @param  string $compile_dir directory to store compiled templates in
     *
     * @return Smarty current Smarty instance for chaining
     */
    public function setCompileDir($compile_dir)
    {
        $this->_normalizeDir('compile_dir', $compile_dir);
        $this->_compileDirNormalized = true;
        return $this;
    }

    /**
     * Get cache directory
     *
     * @return string path of cache directory
     */
    public function getCacheDir()
    {
        if (!$this->_cacheDirNormalized) {
            $this->_normalizeDir('cache_dir', $this->cache_dir);
            $this->_cacheDirNormalized = true;
        }
        return $this->cache_dir;
    }

    /**
     * Set cache directory
     *
     * @param string $cache_dir directory to store cached templates in
     *
     * @return Smarty current Smarty instance for chaining
     */
    public function setCacheDir($cache_dir)
    {
        $this->_normalizeDir('cache_dir', $cache_dir);
        $this->_cacheDirNormalized = true;
        return $this;
    }

    /**
     * creates a template object
     *
     * @param string  $template   the resource handle of the template file
     * @param mixed   $cache_id   cache id to be used with this template
     * @param mixed   $compile_id compile id to be used with this template
     * @param object  $parent     next higher level of Smarty variables
     * @param boolean $do_clone   flag is Smarty object shall be cloned
     *
     * @return \Smarty_Internal_Template template object
     * @throws \SmartyException
     */
    public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = true)
    {
        if ($cache_id !== null && (is_object($cache_id) || is_array($cache_id))) {
            $parent = $cache_id;
            $cache_id = null;
        }
        if ($parent !== null && is_array($parent)) {
            $data = $parent;
            $parent = null;
        } else {
            $data = null;
        }
        if (!$this->_templateDirNormalized) {
            $this->_normalizeTemplateConfig(false);
        }
        $_templateId = $this->_getTemplateId($template, $cache_id, $compile_id);
        $tpl = null;
        if ($this->caching && isset(Smarty_Internal_Template::$isCacheTplObj[ $_templateId ])) {
            $tpl = $do_clone ? clone Smarty_Internal_Template::$isCacheTplObj[ $_templateId ] :
                Smarty_Internal_Template::$isCacheTplObj[ $_templateId ];
            $tpl->inheritance = null;
            $tpl->tpl_vars = $tpl->config_vars = array();
        } elseif (!$do_clone && isset(Smarty_Internal_Template::$tplObjCache[ $_templateId ])) {
            $tpl = clone Smarty_Internal_Template::$tplObjCache[ $_templateId ];
            $tpl->inheritance = null;
            $tpl->tpl_vars = $tpl->config_vars = array();
        } else {
            /* @var Smarty_Internal_Template $tpl */
            $tpl = new $this->template_class($template, $this, null, $cache_id, $compile_id, null, null);
            $tpl->templateId = $_templateId;
        }
        if ($do_clone) {
            $tpl->smarty = clone $tpl->smarty;
        }
        $tpl->parent = $parent ? $parent : $this;
        // fill data if present
        if (!empty($data) && is_array($data)) {
            // set up variable values
            foreach ($data as $_key => $_val) {
                $tpl->tpl_vars[ $_key ] = new Smarty_Variable($_val);
            }
        }
        if ($this->debugging || $this->debugging_ctrl === 'URL') {
            $tpl->smarty->_debug = new Smarty_Internal_Debug();
            // check URL debugging control
            if (!$this->debugging && $this->debugging_ctrl === 'URL') {
                $tpl->smarty->_debug->debugUrl($tpl->smarty);
            }
        }
        return $tpl;
    }

    /**
     * Takes unknown classes and loads plugin files for them
     * class name format: Smarty_PluginType_PluginName
     * plugin filename format: plugintype.pluginname.php
     *
     * @param string $plugin_name class plugin name to load
     * @param bool   $check       check if already loaded
     *
     * @return string |boolean filepath of loaded file or false
     * @throws \SmartyException
     */
    public function loadPlugin($plugin_name, $check = true)
    {
        return $this->ext->loadPlugin->loadPlugin($this, $plugin_name, $check);
    }

    /**
     * Get unique template id
     *
     * @param string                    $template_name
     * @param null|mixed                $cache_id
     * @param null|mixed                $compile_id
     * @param null                      $caching
     * @param \Smarty_Internal_Template $template
     *
     * @return string
     * @throws \SmartyException
     */
    public function _getTemplateId(
        $template_name,
        $cache_id = null,
        $compile_id = null,
        $caching = null,
        Smarty_Internal_Template $template = null
    ) {
        $template_name = (strpos($template_name, ':') === false) ? "{$this->default_resource_type}:{$template_name}" :
            $template_name;
        $cache_id = $cache_id === null ? $this->cache_id : $cache_id;
        $compile_id = $compile_id === null ? $this->compile_id : $compile_id;
        $caching = (int)($caching === null ? $this->caching : $caching);
        if ((isset($template) && strpos($template_name, ':.') !== false) || $this->allow_ambiguous_resources) {
            $_templateId =
                Smarty_Resource::getUniqueTemplateName((isset($template) ? $template : $this), $template_name) .
                "#{$cache_id}#{$compile_id}#{$caching}";
        } else {
            $_templateId = $this->_joined_template_dir . "#{$template_name}#{$cache_id}#{$compile_id}#{$caching}";
        }
        if (isset($_templateId[ 150 ])) {
            $_templateId = sha1($_templateId);
        }
        return $_templateId;
    }

    /**
     * Normalize path
     *  - remove /./ and /../
     *  - make it absolute if required
     *
     * @param string $path     file path
     * @param bool   $realpath if true - convert to absolute
     *                         false - convert to relative
     *                         null - keep as it is but
     *                         remove /./ /../
     *
     * @return string
     */
    public function _realpath($path, $realpath = null)
    {
        $nds = array('/' => '\\', '\\' => '/');
        preg_match(
            '%^(?<root>(?:[[:alpha:]]:[\\\\/]|/|[\\\\]{2}[[:alpha:]]+|[[:print:]]{2,}:[/]{2}|[\\\\])?)(?<path>(.*))$%u',
            $path,
            $parts
        );
        $path = $parts[ 'path' ];
        if ($parts[ 'root' ] === '\\') {
            $parts[ 'root' ] = substr(getcwd(), 0, 2) . $parts[ 'root' ];
        } else {
            if ($realpath !== null && !$parts[ 'root' ]) {
                $path = getcwd() . DIRECTORY_SEPARATOR . $path;
            }
        }
        // normalize DIRECTORY_SEPARATOR
        $path = str_replace($nds[ DIRECTORY_SEPARATOR ], DIRECTORY_SEPARATOR, $path);
        $parts[ 'root' ] = str_replace($nds[ DIRECTORY_SEPARATOR ], DIRECTORY_SEPARATOR, $parts[ 'root' ]);
        do {
            $path = preg_replace(
                array('#[\\\\/]{2}#', '#[\\\\/][.][\\\\/]#', '#[\\\\/]([^\\\\/.]+)[\\\\/][.][.][\\\\/]#'),
                DIRECTORY_SEPARATOR,
                $path,
                -1,
                $count
            );
        } while ($count > 0);
        return $realpath !== false ? $parts[ 'root' ] . $path : str_ireplace(getcwd(), '.', $parts[ 'root' ] . $path);
    }

    /**
     * Empty template objects cache
     */
    public function _clearTemplateCache()
    {
        Smarty_Internal_Template::$isCacheTplObj = array();
        Smarty_Internal_Template::$tplObjCache = array();
    }

    /**
     * @param boolean $use_sub_dirs
     */
    public function setUseSubDirs($use_sub_dirs)
    {
        $this->use_sub_dirs = $use_sub_dirs;
    }

    /**
     * @param int $error_reporting
     */
    public function setErrorReporting($error_reporting)
    {
        $this->error_reporting = $error_reporting;
    }

    /**
     * @param boolean $escape_html
     */
    public function setEscapeHtml($escape_html)
    {
        $this->escape_html = $escape_html;
    }

    /**
     * Return auto_literal flag
     *
     * @return boolean
     */
    public function getAutoLiteral()
    {
        return $this->auto_literal;
    }

    /**
     * Set auto_literal flag
     *
     * @param boolean $auto_literal
     */
    public function setAutoLiteral($auto_literal = true)
    {
        $this->auto_literal = $auto_literal;
    }

    /**
     * @param boolean $force_compile
     */
    public function setForceCompile($force_compile)
    {
        $this->force_compile = $force_compile;
    }

    /**
     * @param boolean $merge_compiled_includes
     */
    public function setMergeCompiledIncludes($merge_compiled_includes)
    {
        $this->merge_compiled_includes = $merge_compiled_includes;
    }

    /**
     * Get left delimiter
     *
     * @return string
     */
    public function getLeftDelimiter()
    {
        return $this->left_delimiter;
    }

    /**
     * Set left delimiter
     *
     * @param string $left_delimiter
     */
    public function setLeftDelimiter($left_delimiter)
    {
        $this->left_delimiter = $left_delimiter;
    }

    /**
     * Get right delimiter
     *
     * @return string $right_delimiter
     */
    public function getRightDelimiter()
    {
        return $this->right_delimiter;
    }

    /**
     * Set right delimiter
     *
     * @param string
     */
    public function setRightDelimiter($right_delimiter)
    {
        $this->right_delimiter = $right_delimiter;
    }

    /**
     * @param boolean $debugging
     */
    public function setDebugging($debugging)
    {
        $this->debugging = $debugging;
    }

    /**
     * @param boolean $config_overwrite
     */
    public function setConfigOverwrite($config_overwrite)
    {
        $this->config_overwrite = $config_overwrite;
    }

    /**
     * @param boolean $config_booleanize
     */
    public function setConfigBooleanize($config_booleanize)
    {
        $this->config_booleanize = $config_booleanize;
    }

    /**
     * @param boolean $config_read_hidden
     */
    public function setConfigReadHidden($config_read_hidden)
    {
        $this->config_read_hidden = $config_read_hidden;
    }

    /**
     * @param boolean $compile_locking
     */
    public function setCompileLocking($compile_locking)
    {
        $this->compile_locking = $compile_locking;
    }

    /**
     * @param string $default_resource_type
     */
    public function setDefaultResourceType($default_resource_type)
    {
        $this->default_resource_type = $default_resource_type;
    }

    /**
     * @param string $caching_type
     */
    public function setCachingType($caching_type)
    {
        $this->caching_type = $caching_type;
    }

    /**
     * Test install
     *
     * @param null $errors
     */
    public function testInstall(&$errors = null)
    {
        Smarty_Internal_TestInstall::testInstall($this, $errors);
    }

    /**
     * Get Smarty object
     *
     * @return Smarty
     */
    public function _getSmartyObj()
    {
        return $this;
    }

    /**
     * <<magic>> Generic getter.
     * Calls the appropriate getter function.
     * Issues an E_USER_NOTICE if no valid getter is found.
     *
     * @param string $name property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->accessMap[ $name ])) {
            $method = 'get' . $this->accessMap[ $name ];
            return $this->{$method}();
        } elseif (isset($this->_cache[ $name ])) {
            return $this->_cache[ $name ];
        } elseif (in_array($name, $this->obsoleteProperties)) {
            return null;
        } else {
            trigger_error('Undefined property: ' . get_class($this) . '::$' . $name, E_USER_NOTICE);
        }
        return null;
    }

    /**
     * <<magic>> Generic setter.
     * Calls the appropriate setter function.
     * Issues an E_USER_NOTICE if no valid setter is found.
     *
     * @param string $name  property name
     * @param mixed  $value parameter passed to setter
     *
     */
    public function __set($name, $value)
    {
        if (isset($this->accessMap[ $name ])) {
            $method = 'set' . $this->accessMap[ $name ];
            $this->{$method}($value);
        } elseif (in_array($name, $this->obsoleteProperties)) {
            return;
        } elseif (is_object($value) && method_exists($value, $name)) {
            $this->$name = $value;
        } else {
            trigger_error('Undefined property: ' . get_class($this) . '::$' . $name, E_USER_NOTICE);
        }
    }

    /**
     * Normalize and set directory string
     *
     * @param string $dirName cache_dir or compile_dir
     * @param string $dir     filepath of folder
     */
    private function _normalizeDir($dirName, $dir)
    {
        $this->{$dirName} = $this->_realpath(rtrim($dir, "/\\") . DIRECTORY_SEPARATOR, true);
        if (class_exists('Smarty_Internal_ErrorHandler', false)) {
            if (!isset(Smarty_Internal_ErrorHandler::$mutedDirectories[ $this->{$dirName} ])) {
                Smarty_Internal_ErrorHandler::$mutedDirectories[ $this->{$dirName} ] = null;
            }
        }
    }

    /**
     * Normalize template_dir or config_dir
     *
     * @param bool $isConfig true for config_dir
     */
    private function _normalizeTemplateConfig($isConfig)
    {
        if ($isConfig) {
            $processed = &$this->_processedConfigDir;
            $dir = &$this->config_dir;
        } else {
            $processed = &$this->_processedTemplateDir;
            $dir = &$this->template_dir;
        }
        if (!is_array($dir)) {
            $dir = (array)$dir;
        }
        foreach ($dir as $k => $v) {
            if (!isset($processed[ $k ])) {
                $dir[ $k ] = $v = $this->_realpath(rtrim($v, "/\\") . DIRECTORY_SEPARATOR, true);
                $processed[ $k ] = true;
            }
        }
        $isConfig ? $this->_configDirNormalized = true : $this->_templateDirNormalized = true;
        $isConfig ? $this->_joined_config_dir = join('#', $this->config_dir) :
            $this->_joined_template_dir = join('#', $this->template_dir);
    }
}
