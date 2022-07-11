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

use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfiguration;

class FileFilter
{
    /**
     * @var UpgradeConfiguration
     */
    protected $configuration;

    /**
     * @var string Autoupgrade sub directory*
     */
    protected $autoupgradeDir;

    public function __construct(UpgradeConfiguration $configuration, $autoupgradeDir = 'autoupgrade')
    {
        $this->configuration = $configuration;
        $this->autoupgradeDir = $autoupgradeDir;
    }

    /**
     * AdminSelfUpgrade::backupIgnoreAbsoluteFiles.
     *
     * @return array
     */
    public function getFilesToIgnoreOnBackup()
    {
        // during backup, do not save
        $backupIgnoreAbsoluteFiles = array(
            '/app/cache',
            '/cache/smarty/compile',
            '/cache/smarty/cache',
            '/cache/tcpdf',
            '/cache/cachefs',
            '/var/cache',

            // do not care about the two autoupgrade dir we use;
            '/modules/autoupgrade',
            '/admin/autoupgrade',
        );

        if (!$this->configuration->shouldBackupImages()) {
            $backupIgnoreAbsoluteFiles[] = '/img';
        } else {
            $backupIgnoreAbsoluteFiles[] = '/img/tmp';
        }

        return $backupIgnoreAbsoluteFiles;
    }

    /**
     * AdminSelfUpgrade::restoreIgnoreAbsoluteFiles.
     *
     * @return array
     */
    public function getFilesToIgnoreOnRestore()
    {
        $restoreIgnoreAbsoluteFiles = array(
            '/app/config/parameters.php',
            '/app/config/parameters.yml',
            '/modules/autoupgrade',
            '/admin/autoupgrade',
            '.',
            '..',
        );

        if (!$this->configuration->shouldBackupImages()) {
            $restoreIgnoreAbsoluteFiles[] = '/img';
        } else {
            $restoreIgnoreAbsoluteFiles[] = '/img/tmp';
        }

        return $restoreIgnoreAbsoluteFiles;
    }

    /**
     * AdminSelfUpgrade::excludeAbsoluteFilesFromUpgrade.
     *
     * @return array
     */
    public function getFilesToIgnoreOnUpgrade()
    {
        // do not copy install, neither app/config/parameters.php in case it would be present
        $excludeAbsoluteFilesFromUpgrade = array(
            '/app/config/parameters.php',
            '/app/config/parameters.yml',
            '/install',
            '/install-dev',
        );

        // this will exclude autoupgrade dir from admin, and autoupgrade from modules
        // If set to false, we need to preserve the default themes
        if (!$this->configuration->shouldUpdateDefaultTheme()) {
            $excludeAbsoluteFilesFromUpgrade[] = '/themes/classic';
            $excludeAbsoluteFilesFromUpgrade[] = '/themes/default-bootstrap';
            $excludeAbsoluteFilesFromUpgrade[] = '/themes/hotel-reservation-theme';
        }

        return $excludeAbsoluteFilesFromUpgrade;
    }

    /**
     * AdminSelfUpgrade::excludeAbsoluteFilesFromDelete.
     *
     * @return array
     */
    public function getFilesToIgnoreOnDelete()
    {
        $excludeAbsoluteFilesFromDelete = array(
            '/img/rf',
        );

        return $excludeAbsoluteFilesFromDelete;
    }

    /**
     * AdminSelfUpgrade::backupIgnoreFiles
     * AdminSelfUpgrade::excludeFilesFromUpgrade
     * AdminSelfUpgrade::restoreIgnoreFiles.
     *
     * These files are checked in every subfolder of the directory tree and can match
     * several time, while the others are only matching a file from the project root.
     *
     * @return array
     */
    public function getExcludeFiles()
    {
        return array(
            '.',
            '..',
            '.svn',
            '.git',
            $this->autoupgradeDir,
        );
    }
}
