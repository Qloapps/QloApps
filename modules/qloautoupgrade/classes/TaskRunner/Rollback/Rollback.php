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

namespace PrestaShop\Module\AutoUpgrade\TaskRunner\Rollback;

use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeFileNames;
use PrestaShop\Module\AutoUpgrade\TaskRunner\AbstractTask;
use PrestaShop\Module\AutoUpgrade\UpgradeContainer;

/**
 * First step executed during a rollback.
 */
class Rollback extends AbstractTask
{
    public function run()
    {
        // 1st, need to analyse what was wrong.
        $restoreName = $this->container->getState()->getRestoreName();
        $this->container->getState()->setRestoreFilesFilename($restoreName);
        $restoreDbFilenames = $this->container->getState()->getRestoreDbFilenames();

        if (empty($restoreName)) {
            $this->next = 'noRollbackFound';

            return;
        }

        $files = scandir($this->container->getProperty(UpgradeContainer::BACKUP_PATH));
        // find backup filenames, and be sure they exists
        foreach ($files as $file) {
            if (preg_match('#' . preg_quote('auto-backupfiles_' . $restoreName) . '#', $file)) {
                $this->container->getState()->setRestoreFilesFilename($file);
                break;
            }
        }
        if (!is_file($this->container->getProperty(UpgradeContainer::BACKUP_PATH) . DIRECTORY_SEPARATOR . $this->container->getState()->getRestoreFilesFilename())) {
            $this->next = 'error';
            $this->logger->error($this->translator->trans('[ERROR] File %s is missing: unable to restore files. Operation aborted.', array($this->container->getState()->getRestoreFilesFilename()), 'Modules.Autoupgrade.Admin'));

            return false;
        }
        $files = scandir($this->container->getProperty(UpgradeContainer::BACKUP_PATH) . DIRECTORY_SEPARATOR . $restoreName);
        foreach ($files as $file) {
            if (preg_match('#auto-backupdb_[0-9]{6}_' . preg_quote($restoreName) . '#', $file)) {
                $restoreDbFilenames[] = $file;
            }
        }

        // order files is important !
        sort($restoreDbFilenames);
        $this->container->getState()->setRestoreDbFilenames($restoreDbFilenames);
        if (count($restoreDbFilenames) == 0) {
            $this->next = 'error';
            $this->logger->error($this->translator->trans('[ERROR] No backup database files found: it would be impossible to restore the database. Operation aborted.', array(), 'Modules.Autoupgrade.Admin'));

            return false;
        }

        $this->next = 'restoreFiles';
        $this->logger->info($this->translator->trans('Restoring files ...', array(), 'Modules.Autoupgrade.Admin'));
        // remove tmp files related to restoreFiles
        if (file_exists($this->container->getProperty(UpgradeContainer::WORKSPACE_PATH) . DIRECTORY_SEPARATOR . UpgradeFileNames::FILES_FROM_ARCHIVE_LIST)) {
            unlink($this->container->getProperty(UpgradeContainer::WORKSPACE_PATH) . DIRECTORY_SEPARATOR . UpgradeFileNames::FILES_FROM_ARCHIVE_LIST);
        }
        if (file_exists($this->container->getProperty(UpgradeContainer::WORKSPACE_PATH) . DIRECTORY_SEPARATOR . UpgradeFileNames::FILES_TO_REMOVE_LIST)) {
            unlink($this->container->getProperty(UpgradeContainer::WORKSPACE_PATH) . DIRECTORY_SEPARATOR . UpgradeFileNames::FILES_TO_REMOVE_LIST);
        }
    }

    public function init()
    {
        // Do nothing
    }
}
