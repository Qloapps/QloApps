<?php

/*
 * 2007-2018 PrestaShop
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade\UpgradeTools;

use PrestaShop\Module\AutoUpgrade\Tools14;

class FilesystemAdapter
{
    private $restoreFilesFilename;

    private $fileFilter;

    private $autoupgradeDir;
    private $adminSubDir;
    private $prodRootDir;

    /**
     * Somes elements to find in a folder.
     * If one of them cannot be found, we can consider that the release is invalid.
     *
     * @var array
     */
    private $releaseFileChecks = array(
        'files' => array(
            'index.php',
            'config/defines.inc.php',
        ),
        'folders' => array(
            'classes',
            'controllers',
        ),
    );

    public function __construct(FileFilter $fileFilter, $restoreFilesFilename,
        $autoupgradeDir, $adminSubDir, $prodRootDir)
    {
        $this->fileFilter = $fileFilter;
        $this->restoreFilesFilename = $restoreFilesFilename;

        $this->autoupgradeDir = $autoupgradeDir;
        $this->adminSubDir = $adminSubDir;
        $this->prodRootDir = $prodRootDir;
    }

    /**
     * Delete directory and subdirectories.
     *
     * @param string $dirname Directory name
     */
    public static function deleteDirectory($dirname, $delete_self = true)
    {
        return Tools14::deleteDirectory($dirname, $delete_self);
    }

    public function listFilesInDir($dir, $way = 'backup', $list_directories = false)
    {
        $list = array();
        $dir = rtrim($dir, '/') . DIRECTORY_SEPARATOR;
        $allFiles = false;
        if (is_dir($dir) && is_readable($dir)) {
            $allFiles = scandir($dir);
        }
        if (!is_array($allFiles)) {
            return $list;
        }
        foreach ($allFiles as $file) {
            $fullPath = $dir . $file;
            // skip broken symbolic links
            if (is_link($fullPath) && !is_readable($fullPath)) {
                continue;
            }
            if ($this->isFileSkipped($file, $fullPath, $way)) {
                continue;
            }
            if (is_dir($fullPath)) {
                $list = array_merge($list, $this->listFilesInDir($fullPath, $way, $list_directories));
                if ($list_directories) {
                    $list[] = $fullPath;
                }
            } else {
                $list[] = $fullPath;
            }
        }

        return $list;
    }

    /**
     * this function list all files that will be remove to retrieve the filesystem states before the upgrade.
     *
     * @return array of files to delete
     */
    public function listFilesToRemove()
    {
        $prev_version = preg_match('#auto-backupfiles_V([0-9.]*)_#', $this->restoreFilesFilename, $matches);
        if ($prev_version) {
            $prev_version = $matches[1];
        }

        // if we can't find the diff file list corresponding to _PS_VERSION_ and prev_version,
        // let's assume to remove every files
        $toRemove = $this->listFilesInDir($this->prodRootDir, 'restore', true);

        // if a file in "ToRemove" has been skipped during backup,
        // just keep it
        foreach ($toRemove as $key => $file) {
            $filename = substr($file, strrpos($file, '/') + 1);
            $toRemove[$key] = preg_replace('#^/admin#', $this->adminSubDir, $file);
            // this is a really sensitive part, so we add an extra checks: preserve everything that contains "autoupgrade"
            if ($this->isFileSkipped($filename, $file, 'backup') || strpos($file, $this->autoupgradeDir) !== false) {
                unset($toRemove[$key]);
            }
        }

        return $toRemove;
    }

    /**
     * Retrieve a list of sample files to be deleted from the release.
     *
     * @param array $directoryList
     *
     * @return array Files to remove from the release
     */
    public function listSampleFilesFromArray(array $directoryList)
    {
        $res = array();
        foreach ($directoryList as $directory) {
            $res = array_merge($res, $this->listSampleFiles($directory['path'], $directory['filter']));
        }

        return $res;
    }

    /**
     * listSampleFiles will make a recursive call to scandir() function
     * and list all file which match to the $fileext suffixe (this can be an extension or whole filename).
     *
     * @param string $dir directory to look in
     * @param string $fileext suffixe filename
     *
     * @return array of files
     */
    public function listSampleFiles($dir, $fileext = '.jpg')
    {
        $res = array();
        $dir = rtrim($dir, '/') . DIRECTORY_SEPARATOR;
        $toDel = false;
        if (is_dir($dir) && is_readable($dir)) {
            $toDel = scandir($dir);
        }
        // copied (and kind of) adapted from AdminImages.php
        if (is_array($toDel)) {
            foreach ($toDel as $file) {
                if ($file[0] != '.') {
                    if (preg_match('#' . preg_quote($fileext, '#') . '$#i', $file)) {
                        $res[] = $dir . $file;
                    } elseif (is_dir($dir . $file)) {
                        $res = array_merge($res, $this->listSampleFiles($dir . $file, $fileext));
                    }
                }
            }
        }

        return $res;
    }

    /**
     *	bool _skipFile : check whether a file is in backup or restore skip list.
     *
     * @param string $file : current file or directory name eg:'.svn' , 'settings.inc.php'
     * @param string $fullpath : current file or directory fullpath eg:'/home/web/www/prestashop/app/config/parameters.php'
     * @param string $way : 'backup' , 'upgrade'
     * @param string $temporaryWorkspace : If needed, another folder than the shop root can be used (used for releases)
     */
    public function isFileSkipped($file, $fullpath, $way = 'backup', $temporaryWorkspace = null)
    {
        $fullpath = str_replace('\\', '/', $fullpath); // wamp compliant
        $rootpath = str_replace(
            '\\',
            '/',
            (null !== $temporaryWorkspace) ? $temporaryWorkspace : $this->prodRootDir
        );

        if (in_array($file, $this->fileFilter->getExcludeFiles())) {
            return true;
        }

        $ignoreList = array();
        if ('backup' === $way) {
            $ignoreList = $this->fileFilter->getFilesToIgnoreOnBackup();
        } elseif ('restore' === $way) {
            $ignoreList = $this->fileFilter->getFilesToIgnoreOnRestore();
        } elseif ('upgrade' === $way) {
            $ignoreList = $this->fileFilter->getFilesToIgnoreOnUpgrade();
        } elseif ('delete' === $way) {
            $ignoreList = $this->fileFilter->getFilesToIgnoreOnDelete();
        }

        foreach ($ignoreList as $path) {
            $path = str_replace(DIRECTORY_SEPARATOR . 'admin', DIRECTORY_SEPARATOR . $this->adminSubDir, $path);
            if (strstr($fullpath, $rootpath . $path)) {
                return true;
            }
        }

        // by default, don't skip
        return false;
    }

    /**
     * Check a directory has some files available in every release of PrestaShop.
     *
     * @param string $path Workspace to check
     *
     * @return bool
     */
    public function isReleaseValid($path)
    {
        foreach ($this->releaseFileChecks as $type => $elements) {
            foreach ($elements as $element) {
                $fullPath = $path . DIRECTORY_SEPARATOR . $element;
                if ('files' === $type && !is_file($fullPath)) {
                    return false;
                }
                if ('folders' === $type && !is_dir($fullPath)) {
                    return false;
                }
            }
        }

        return true;
    }
}
