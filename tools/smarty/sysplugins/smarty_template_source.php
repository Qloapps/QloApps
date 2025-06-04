<?php

/**
 * Smarty Resource Data Object
 * Meta Data Container for Template Files
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 */
class Smarty_Template_Source
{
    /**
     * Unique Template ID
     *
     * @var string
     */
    public $uid = null;

    /**
     * Template Resource (Smarty_Internal_Template::$template_resource)
     *
     * @var string
     */
    public $resource = null;

    /**
     * Resource Type
     *
     * @var string
     */
    public $type = null;

    /**
     * Resource Name
     *
     * @var string
     */
    public $name = null;

    /**
     * Source Filepath
     *
     * @var string
     */
    public $filepath = null;

    /**
     * Source Timestamp
     *
     * @var integer
     */
    public $timestamp = null;

    /**
     * Source Existence
     *
     * @var boolean
     */
    public $exists = false;

    /**
     * Source File Base name
     *
     * @var string
     */
    public $basename = null;

    /**
     * The Components an extended template is made of
     *
     * @var \Smarty_Template_Source[]
     */
    public $components = null;

    /**
     * Resource Handler
     *
     * @var \Smarty_Resource
     */
    public $handler = null;

    /**
     * Smarty instance
     *
     * @var Smarty
     */
    public $smarty = null;

    /**
     * Resource is source
     *
     * @var bool
     */
    public $isConfig = false;

    /**
     * Template source content eventually set by default handler
     *
     * @var string
     */
    public $content = null;

    /**
     * Name of the Class to compile this resource's contents with
     *
     * @var string
     */
    public $compiler_class = 'Smarty_Internal_SmartyTemplateCompiler';

    /**
     * Name of the Class to tokenize this resource's contents with
     *
     * @var string
     */
    public $template_lexer_class = 'Smarty_Internal_Templatelexer';

    /**
     * Name of the Class to parse this resource's contents with
     *
     * @var string
     */
    public $template_parser_class = 'Smarty_Internal_Templateparser';

    /**
     * create Source Object container
     *
     * @param Smarty $smarty   Smarty instance this source object belongs to
     * @param string $resource full template_resource
     * @param string $type     type of resource
     * @param string $name     resource name
     *
     * @throws   \SmartyException
     * @internal param \Smarty_Resource $handler Resource Handler this source object communicates with
     */
    public function __construct(Smarty $smarty, $resource, $type, $name)
    {
        $this->handler =
            isset($smarty->_cache[ 'resource_handlers' ][ $type ]) ? $smarty->_cache[ 'resource_handlers' ][ $type ] :
                Smarty_Resource::load($smarty, $type);
        $this->smarty = $smarty;
        $this->resource = $resource;
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * initialize Source Object for given resource
     * Either [$_template] or [$smarty, $template_resource] must be specified
     *
     * @param Smarty_Internal_Template|null $_template         template object
     * @param Smarty|null                   $smarty            smarty object
     * @param string                        $template_resource resource identifier
     *
     * @return Smarty_Template_Source Source Object
     * @throws SmartyException
     */
    public static function load(
        ?Smarty_Internal_Template $_template = null,
        ?Smarty $smarty = null,
        $template_resource = null
    ) {
        if ($_template) {
            $smarty = $_template->smarty;
            $template_resource = $_template->template_resource;
        }
        if (empty($template_resource)) {
            throw new SmartyException('Source: Missing  name');
        }
        // parse resource_name, load resource handler, identify unique resource name
        if (preg_match('/^([A-Za-z0-9_\-]{2,})[:]([\s\S]*)$/', $template_resource, $match)) {
            $type = $match[ 1 ];
            $name = $match[ 2 ];
        } else {
            // no resource given, use default
            // or single character before the colon is not a resource type, but part of the filepath
            $type = $smarty->default_resource_type;
            $name = $template_resource;
        }
        // create new source  object
        $source = new Smarty_Template_Source($smarty, $template_resource, $type, $name);
        $source->handler->populate($source, $_template);
        if (!$source->exists && isset($_template->smarty->default_template_handler_func)) {
            Smarty_Internal_Method_RegisterDefaultTemplateHandler::_getDefaultTemplate($source);
            $source->handler->populate($source, $_template);
        }
        return $source;
    }

    /**
     * Get source time stamp
     *
     * @return int
     */
    public function getTimeStamp()
    {
        if (!isset($this->timestamp)) {
            $this->handler->populateTimestamp($this);
        }
        return $this->timestamp;
    }

    /**
     * Get source content
     *
     * @return string
     * @throws \SmartyException
     */
    public function getContent()
    {
        return isset($this->content) ? $this->content : $this->handler->getContent($this);
    }
}
