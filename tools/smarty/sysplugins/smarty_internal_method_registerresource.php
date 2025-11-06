<?php

/**
 * Smarty Method RegisterResource
 *
 * Smarty::registerResource() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_RegisterResource
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;

    /**
     * Registers a resource to fetch a template
     *
     * @api  Smarty::registerResource()
     * @link https://www.smarty.net/docs/en/api.register.resource.tpl
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param string                                                          $name             name of resource type
     * @param Smarty_Resource                                           $resource_handler instance of Smarty_Resource
     *
     * @return \Smarty|\Smarty_Internal_Template
     */
    public function registerResource(Smarty_Internal_TemplateBase $obj, $name, Smarty_Resource $resource_handler)
    {
        $smarty = $obj->_getSmartyObj();
        $smarty->registered_resources[ $name ] = $resource_handler;
        return $obj;
    }
}
