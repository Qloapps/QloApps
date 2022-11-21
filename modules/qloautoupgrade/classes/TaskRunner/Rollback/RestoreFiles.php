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
 * ajaxProcessRestoreFiles restore the previously saved files,
 * and delete files that weren't archived.
 */
class RestoreFiles extends AbstractTask
{
    public function run()
    {
        // loop
        $this->next = 'restoreFiles';
        if (!file_exists($this->container->getProperty(UpgradeContainer::WORKSPACE_PATH) . DIRECTORY_SEPARATOR . UpgradeFileNames::FILES_FROM_ARCHIVE_LIST)
            || !file_exists($this->container->getProperty(UpgradeContainer::WORKSPACE_PATH) . DIRECTORY_SEPARATOR . UpgradeFileNames::FILES_TO_REMOVE_LIST)) {
            // cleanup current PS tree
            $fromArchive = $this->container->getZipAction()->listContent($this->container->getProperty(UpgradeContainer::BACKUP_PATH) . DIRECTORY_SEPARATOR . $this->container->getState()->getRestoreFilesFilename());
            foreach ($fromArchive as $k => $v) {
                $fromArchive[DIRECTORY_SEPARATOR . $v] = DIRECTORY_SEPARATOR . $v;
            }

            $this->container->getFileConfigurationStorage()->save($fromArchive, UpgradeFileNames::FILES_FROM_ARCHIVE_LIST);
            // get list of files to remove
            $toRemove = $this->container->getFilesystemAdapter()->listFilesToRemove();
            $toRemoveOnly = array();

            // let's reverse the array in order to make possible to rmdir
            // remove fullpath. This will be added later in the loop.
            // we do that for avoiding fullpath to be revealed in a text file
            foreach ($toRemove as $k => $v) {
                $vfile = str_replace($this->container->getProperty(UpgradeContainer::PS_ROOT_PATH), '', $v);
                $toRemove[] = str_replace($this->container->getProperty(UpgradeContainer::PS_ROOT_PATH), '', $vfile);

                if (!isset($fromArchive[$vfile]) && is_file($v)) {
                    $toRemoveOnly[$vfile] = str_replace($this->container->getProperty(UpgradeContainer::PS_ROOT_PATH), '', $vfile);
                }
            }

            $this->logger->debug($this->translator->trans('%s file(s) will be removed before restoring the backup files.', array(count($toRemoveOnly)), 'Modules.Autoupgrade.Admin'));
            $this->container->getFileConfigurationStorage()->save($toRemoveOnly, UpgradeFileNames::FILES_TO_REMOVE_LIST);

            if (empty($fromArchive) || empty($toRemove)) {
                if (empty($fromArchive)) {
                    $this->logger->error($this->translator->trans('[ERROR] Backup file %s does not exist.', array(UpgradeFileNames::FILES_FROM_ARCHIVE_LIST), 'Modules.Autoupgrade.Admin'));
                }
                if (empty($toRemove)) {
                    $this->logger->error($this->translator->trans('[ERROR] File "%s" does not exist.', array(UpgradeFileNames::FILES_TO_REMOVE_LIST), 'Modules.Autoupgrade.Admin'));
                }
                $this->logger->info($this->translator->trans('Unable to remove upgraded files.', array(), 'Modules.Autoupgrade.Admin'));
                $this->next = 'error';

                return false;
            }
        }

        if (!empty($fromArchive)) {
            $filepath = $this->container->getProperty(UpgradeContainer::BACKUP_PATH) . DIRECTORY_SEPARATOR . $this->container->getState()->getRestoreFilesFilename();
            $destExtract = $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH);

            $res = $this->container->getZipAction()->extract($filepath, $destExtract);
            if (!$res) {
                $this->next = 'error';
                $this->logger->error($this->translator->trans(
                    'Unable to extract file %filename% into directory %directoryname%.',
                    array(
                        '%filename%' => $filepath,
                        '%directoryname%' => $destExtract,
                    ),
                    'Modules.Autoupgrade.Admin'
                ));

                return false;
            }

            if (!empty($toRemoveOnly)) {
                foreach ($toRemoveOnly as $fileToRemove) {
                    @unlink($this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . $fileToRemove);
                }
            }

            $this->next = 'restoreDb';
            $this->logger->debug($this->translator->trans('Files restored.', array(), 'Modules.Autoupgrade.Admin'));
            $this->logger->info($this->translator->trans('Files restored. Now restoring database...', array(), 'Modules.Autoupgrade.Admin'));

            return true;
        }
    }

    public function init()
    {
        // Do nothing
    }
}
