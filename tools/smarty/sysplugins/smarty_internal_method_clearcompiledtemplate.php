<?php

/**
 * Smarty Method ClearCompiledTemplate
 *
 * Smarty::clearCompiledTemplate() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_ClearCompiledTemplate
{
    /**
     * Valid for Smarty object
     *
     * @var int
     */
    public $objMap = 1;

    /**
     * Delete compiled template file
     *
     * @api  Smarty::clearCompiledTemplate()
     * @link http://www.smarty.net/docs/en/api.clear.compiled.template.tpl
     *
     * @param \Smarty $smarty
     * @param string  $resource_name template name
     * @param string  $compile_id    compile id
     * @param integer $exp_time      expiration time
     *
     * @return int number of template files deleted
     * @throws \SmartyException
     */
    public function clearCompiledTemplate(Smarty $smarty, $resource_name = null, $compile_id = null, $exp_time = null)
    {
        // clear template objects cache
        $smarty->_clearTemplateCache();
        $_compile_dir = $smarty->getCompileDir();
        if ($_compile_dir === '/') { //We should never want to delete this!
            return 0;
        }
        $_compile_id = isset($compile_id) ? preg_replace('![^\w]+!', '_', $compile_id) : null;
        $_dir_sep = $smarty->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';
        if (isset($resource_name)) {
            $_save_stat = $smarty->caching;
            $smarty->caching = Smarty::CACHING_OFF;
            /* @var Smarty_Internal_Template $tpl */
            $tpl = $smarty->createTemplate($resource_name);
            $smarty->caching = $_save_stat;
            if (!$tpl->source->handler->uncompiled && !$tpl->source->handler->recompiled && $tpl->source->exists) {
                $_resource_part_1 = basename(str_replace('^', DIRECTORY_SEPARATOR, $tpl->compiled->filepath));
                $_resource_part_1_length = strlen($_resource_part_1);
            } else {
                return 0;
            }
            $_resource_part_2 = str_replace('.php', '.cache.php', $_resource_part_1);
            $_resource_part_2_length = strlen($_resource_part_2);
        }
        $_dir = $_compile_dir;
        if ($smarty->use_sub_dirs && isset($_compile_id)) {
            $_dir .= $_compile_id . $_dir_sep;
        }
        if (isset($_compile_id)) {
            $_compile_id_part = $_compile_dir . $_compile_id . $_dir_sep;
            $_compile_id_part_length = strlen($_compile_id_part);
        }
        $_count = 0;
        try {
            $_compileDirs = new RecursiveDirectoryIterator($_dir);
            // NOTE: UnexpectedValueException thrown for PHP >= 5.3
        } catch (Exception $e) {
            return 0;
        }
        $_compile = new RecursiveIteratorIterator($_compileDirs, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($_compile as $_file) {
            if (substr(basename($_file->getPathname()), 0, 1) === '.') {
                continue;
            }
            $_filepath = (string)$_file;
            if ($_file->isDir()) {
                if (!$_compile->isDot()) {
                    // delete folder if empty
                    @rmdir($_file->getPathname());
                }
            } else {
                // delete only php files
                if (substr($_filepath, -4) !== '.php') {
                    continue;
                }
                $unlink = false;
                if ((!isset($_compile_id) ||
                     (isset($_filepath[ $_compile_id_part_length ]) &&
                      $a = !strncmp($_filepath, $_compile_id_part, $_compile_id_part_length)))
                    && (!isset($resource_name) || (isset($_filepath[ $_resource_part_1_length ])
                                                   && substr_compare(
                                                          $_filepath,
                                                          $_resource_part_1,
                                                          -$_resource_part_1_length,
                                                          $_resource_part_1_length
                                                      ) === 0) || (isset($_filepath[ $_resource_part_2_length ])
                                                                   && substr_compare(
                                                                          $_filepath,
                                                                          $_resource_part_2,
                                                                          -$_resource_part_2_length,
                                                                          $_resource_part_2_length
                                                                      ) === 0))
                ) {
                    if (isset($exp_time)) {
                        if (is_file($_filepath) && time() - filemtime($_filepath) >= $exp_time) {
                            $unlink = true;
                        }
                    } else {
                        $unlink = true;
                    }
                }
                if ($unlink && is_file($_filepath) && @unlink($_filepath)) {
                    $_count++;
                    if (function_exists('opcache_invalidate')
                        && (!function_exists('ini_get') || strlen(ini_get('opcache.restrict_api')) < 1)
                    ) {
                        opcache_invalidate($_filepath, true);
                    } elseif (function_exists('apc_delete_file')) {
                        apc_delete_file($_filepath);
                    }
                }
            }
        }
        return $_count;
    }
}
