<?php
/**
 * Smarty write file plugin
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Monte Ohrt
 */

/**
 * Smarty Internal Write File Class
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 */
class Smarty_Internal_Runtime_WriteFile
{
    /**
     * Writes file in a safe way to disk
     *
     * @param string $_filepath complete filepath
     * @param string $_contents file content
     * @param Smarty $smarty    smarty instance
     *
     * @throws SmartyException
     * @return boolean true
     */
    public function writeFile($_filepath, $_contents, Smarty $smarty)
    {
        $_error_reporting = error_reporting();
        error_reporting($_error_reporting & ~E_NOTICE & ~E_WARNING);
        $_dirpath = dirname($_filepath);
        // if subdirs, create dir structure
        if ($_dirpath !== '.') {
            $i = 0;
            // loop if concurrency problem occurs
            // see https://bugs.php.net/bug.php?id=35326
            while (!is_dir($_dirpath)) {
                if (@mkdir($_dirpath, 0777, true)) {
                    break;
                }
                clearstatcache();
                if (++$i === 3) {
                    error_reporting($_error_reporting);
                    throw new SmartyException("unable to create directory {$_dirpath}");
                }
                sleep(1);
            }
        }
        // write to tmp file, then move to overt file lock race condition
        $_tmp_file = $_dirpath . DIRECTORY_SEPARATOR . str_replace(array('.', ','), '_', uniqid('wrt', true));
        if (!file_put_contents($_tmp_file, $_contents)) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file {$_tmp_file}");
        }
        /*
         * Windows' rename() fails if the destination exists,
         * Linux' rename() properly handles the overwrite.
         * Simply unlink()ing a file might cause other processes
         * currently reading that file to fail, but linux' rename()
         * seems to be smart enough to handle that for us.
         */
        if (Smarty::$_IS_WINDOWS) {
            // remove original file
            if (is_file($_filepath)) {
                @unlink($_filepath);
            }
            // rename tmp file
            $success = @rename($_tmp_file, $_filepath);
        } else {
            // rename tmp file
            $success = @rename($_tmp_file, $_filepath);
            if (!$success) {
                // remove original file
                if (is_file($_filepath)) {
                    @unlink($_filepath);
                }
                // rename tmp file
                $success = @rename($_tmp_file, $_filepath);
            }
        }
        if (!$success) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file {$_filepath}");
        }
        // set file permissions
        @chmod($_filepath, 0666 & ~umask());
        error_reporting($_error_reporting);
        return true;
    }
}
