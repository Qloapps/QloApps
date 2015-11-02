<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Core_Foundation_FileSystem_FileSystem
{
    /**
     * Replaces directory separators with the system's native one
     * and trims the trailing separator.
     */
    public function normalizePath($path)
    {
        return rtrim(
            str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path),
            DIRECTORY_SEPARATOR
        );
    }

    private function joinTwoPaths($a, $b)
    {
        return $this->normalizePath($a) . DIRECTORY_SEPARATOR . $this->normalizePath($b);
    }

    /**
     * Joins an arbitrary number of paths, normalizing them along the way.
     */
    public function joinPaths()
    {
        if (func_num_args() < 2) {
            throw new Core_Foundation_FileSystem_Exception('joinPaths requires at least 2 arguments.');
        } else if (func_num_args() === 2) {
            $arg_O = func_get_arg(0);
            $arg_1 = func_get_arg(1);

            return $this->joinTwoPaths($arg_O, $arg_1);
        } else if (func_num_args() > 2) {
            $func_args = func_get_args();
            $arg_0 = func_get_arg(0);

            return $this->joinPaths(
                $arg_0,
                call_user_func_array(
                    array($this,
                          'joinPaths'),
                    array_slice($func_args, 1)
                )
            );
        }
    }

    /**
     * Performs a depth first listing of directory entries.
     * Throws exception if $path is not a file.
     * If $path is a file and not a directory, just gets the file info for it
     * and return it in an array.
     * @return an array of SplFileInfo object indexed by file path
     */
    public function listEntriesRecursively($path)
    {
        if (!file_exists($path)) {
            throw new Core_Foundation_FileSystem_Exception(
                sprintf(
                    'No such file or directory: %s',
                    $path
                )
            );
        }

        if (!is_dir($path)) {
            throw new Core_Foundation_FileSystem_Exception(
                sprintf(
                    '%s is not a directory',
                    $path
                )
            );
        }

        $entries = array();

        foreach (scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $newPath = $this->joinPaths($path, $entry);
            $info = new SplFileInfo($newPath);

            $entries[$newPath] = $info;

            if ($info->isDir()) {
                $entries = array_merge(
                    $entries,
                    $this->listEntriesRecursively($newPath)
                );
            }
        }

        return $entries;
    }

    /**
     * Filter used by listFilesRecursively.
     */
    private function matchOnlyFiles(SplFileInfo $info)
    {
        return $info->isFile();
    }

    /**
     * Same as listEntriesRecursively but returns only files.
     */
    public function listFilesRecursively($path)
    {
        return array_filter(
            $this->listEntriesRecursively($path),
            array($this, 'matchOnlyFiles')
        );
    }
}
