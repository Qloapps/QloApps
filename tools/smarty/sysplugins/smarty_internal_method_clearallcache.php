<?php

/**
 * Smarty Method ClearAllCache
 *
 * Smarty::clearAllCache() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_ClearAllCache
{
    /**
     * Valid for Smarty object
     *
     * @var int
     */
    public $objMap = 1;

    /**
     * Empty cache folder
     *
     * @api  Smarty::clearAllCache()
     * @link http://www.smarty.net/docs/en/api.clear.all.cache.tpl
     *
     * @param \Smarty $smarty
     * @param integer $exp_time expiration time
     * @param string  $type     resource type
     *
     * @return int number of cache files deleted
     * @throws \SmartyException
     */
    public function clearAllCache(Smarty $smarty, $exp_time = null, $type = null)
    {
        $smarty->_clearTemplateCache();
        // load cache resource and call clearAll
        $_cache_resource = Smarty_CacheResource::load($smarty, $type);
        return $_cache_resource->clearAll($smarty, $exp_time);
    }
}
