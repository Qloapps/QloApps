<?php

/**
 * Inline Runtime Methods render, setSourceByUid, setupSubTemplate
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 **/
class Smarty_Internal_Runtime_UpdateCache
{
    /**
     * check client side cache
     *
     * @param \Smarty_Template_Cached  $cached
     * @param Smarty_Internal_Template $_template
     * @param string                   $content
     */
    public function cacheModifiedCheck(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template, $content)
    {
    }

    /**
     * Cache was invalid , so render from compiled and write to cache
     *
     * @param \Smarty_Template_Cached   $cached
     * @param \Smarty_Internal_Template $_template
     * @param                           $no_output_filter
     *
     * @throws \Exception
     */
    public function updateCache(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template, $no_output_filter)
    {
        ob_start();
        if (!isset($_template->compiled)) {
            $_template->loadCompiled();
        }
        $_template->compiled->render($_template);
        if ($_template->smarty->debugging) {
            $_template->smarty->_debug->start_cache($_template);
        }
        $this->removeNoCacheHash($cached, $_template, $no_output_filter);
        $compile_check = (int)$_template->compile_check;
        $_template->compile_check = Smarty::COMPILECHECK_OFF;
        if ($_template->_isSubTpl()) {
            $_template->compiled->unifunc = $_template->parent->compiled->unifunc;
        }
        if (!$_template->cached->processed) {
            $_template->cached->process($_template, true);
        }
        $_template->compile_check = $compile_check;
        $cached->getRenderedTemplateCode($_template);
        if ($_template->smarty->debugging) {
            $_template->smarty->_debug->end_cache($_template);
        }
    }

    /**
     * Sanitize content and write it to cache resource
     *
     * @param \Smarty_Template_Cached  $cached
     * @param Smarty_Internal_Template $_template
     * @param bool                     $no_output_filter
     *
     * @throws \SmartyException
     */
    public function removeNoCacheHash(
        Smarty_Template_Cached $cached,
        Smarty_Internal_Template $_template,
        $no_output_filter
    ) {
        $php_pattern = '/(<%|%>|<\?php|<\?|\?>|<script\s+language\s*=\s*[\"\']?\s*php\s*[\"\']?\s*>)/';
        $content = ob_get_clean();
        $hash_array = $cached->hashes;
        $hash_array[ $_template->compiled->nocache_hash ] = true;
        $hash_array = array_keys($hash_array);
        $nocache_hash = '(' . implode('|', $hash_array) . ')';
        $_template->cached->has_nocache_code = false;
        // get text between non-cached items
        $cache_split =
            preg_split(
                "!/\*%%SmartyNocache:{$nocache_hash}%%\*\/(.+?)/\*/%%SmartyNocache:{$nocache_hash}%%\*/!s",
                $content
            );
        // get non-cached items
        preg_match_all(
            "!/\*%%SmartyNocache:{$nocache_hash}%%\*\/(.+?)/\*/%%SmartyNocache:{$nocache_hash}%%\*/!s",
            $content,
            $cache_parts
        );
        $content = '';
        // loop over items, stitch back together
        foreach ($cache_split as $curr_idx => $curr_split) {
            if (preg_match($php_pattern, $curr_split)) {
                // escape PHP tags in template content
                $php_split = preg_split(
                    $php_pattern,
                    $curr_split
                );
                preg_match_all(
                    $php_pattern,
                    $curr_split,
                    $php_parts
                );
                foreach ($php_split as $idx_php => $curr_php) {
                    $content .= $curr_php;
                    if (isset($php_parts[ 0 ][ $idx_php ])) {
                        $content .= "<?php echo '{$php_parts[ 1 ][ $idx_php ]}'; ?>\n";
                    }
                }
            } else {
                $content .= $curr_split;
            }
            if (isset($cache_parts[ 0 ][ $curr_idx ])) {
                $_template->cached->has_nocache_code = true;
                $content .= $cache_parts[ 2 ][ $curr_idx ];
            }
        }
        if (!$no_output_filter && !$_template->cached->has_nocache_code
            && (isset($_template->smarty->autoload_filters[ 'output' ])
                || isset($_template->smarty->registered_filters[ 'output' ]))
        ) {
            $content = $_template->smarty->ext->_filterHandler->runFilter('output', $content, $_template);
        }
        // write cache file content
        $this->writeCachedContent($_template, $content);
    }

    /**
     * Writes the content to cache resource
     *
     * @param Smarty_Internal_Template $_template
     * @param string                   $content
     *
     * @return bool
     */
    public function writeCachedContent(Smarty_Internal_Template $_template, $content)
    {
        if ($_template->source->handler->recompiled || !$_template->caching
        ) {
            // don't write cache file
            return false;
        }
        if (!isset($_template->cached)) {
            $_template->loadCached();
        }
        $content = $_template->smarty->ext->_codeFrame->create($_template, $content, '', true);
        return $this->write($_template, $content);
    }

    /**
     * Write this cache object to handler
     *
     * @param Smarty_Internal_Template $_template template object
     * @param string                   $content   content to cache
     *
     * @return bool success
     */
    public function write(Smarty_Internal_Template $_template, $content)
    {
        if (!$_template->source->handler->recompiled) {
            $cached = $_template->cached;
            if ($cached->handler->writeCachedContent($_template, $content)) {
                $cached->content = null;
                $cached->timestamp = time();
                $cached->exists = true;
                $cached->valid = true;
                $cached->cache_lifetime = $_template->cache_lifetime;
                $cached->processed = false;
                if ($_template->smarty->cache_locking) {
                    $cached->handler->releaseLock($_template->smarty, $cached);
                }
                return true;
            }
            $cached->content = null;
            $cached->timestamp = false;
            $cached->exists = false;
            $cached->valid = false;
            $cached->processed = false;
        }
        return false;
    }
}
