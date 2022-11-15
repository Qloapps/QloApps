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

namespace PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade;

use PrestaShop\Module\AutoUpgrade\UpgradeContainer;
use PrestaShop\Module\AutoUpgrade\TaskRunner\AbstractTask;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\FilesystemAdapter;

/**
 * Download PrestaShop archive according to the chosen channel.
 */
class Download extends AbstractTask
{
    public function run()
    {
        if (!\ConfigurationTest::test_fopen() && !\ConfigurationTest::test_curl()) {
            $this->logger->error($this->translator->trans('You need allow_url_fopen or cURL enabled for automatic download to work. You can also manually upload it in filepath %s.', array($this->container->getFilePath()), 'Modules.Autoupgrade.Admin'));
            $this->next = 'error';

            return;
        }

        $upgrader = $this->container->getUpgrader();
        // regex optimization
        preg_match('#([0-9]+\.[0-9]+)(?:\.[0-9]+){1,2}#', _PS_VERSION_, $matches);
        $upgrader->channel = $this->container->getUpgradeConfiguration()->get('channel');
        $upgrader->branch = $matches[1];
        if ($this->container->getUpgradeConfiguration()->get('channel') == 'private' && !$this->container->getUpgradeConfiguration()->get('private_allow_major')) {
            $upgrader->checkPSVersion(false, array('private', 'minor'));
        } else {
            $upgrader->checkPSVersion(false, array('minor'));
        }

        if ($upgrader->channel == 'private') {
            $upgrader->link = $this->container->getUpgradeConfiguration()->get('private_release_link');
            $upgrader->md5 = $this->container->getUpgradeConfiguration()->get('private_release_md5');
        }
        $this->logger->debug($this->translator->trans('Downloading from %s', array($upgrader->link), 'Modules.Autoupgrade.Admin'));
        $this->logger->debug($this->translator->trans('File will be saved in %s', array($this->container->getFilePath()), 'Modules.Autoupgrade.Admin'));
        if (file_exists($this->container->getProperty(UpgradeContainer::DOWNLOAD_PATH))) {
            FilesystemAdapter::deleteDirectory($this->container->getProperty(UpgradeContainer::DOWNLOAD_PATH), false);
            $this->logger->debug($this->translator->trans('Download directory has been emptied', array(), 'Modules.Autoupgrade.Admin'));
        }
        $report = '';
        $relative_download_path = str_replace(_PS_ROOT_DIR_, '', $this->container->getProperty(UpgradeContainer::DOWNLOAD_PATH));
        if (\ConfigurationTest::test_dir($relative_download_path, false, $report)) {
            $res = $upgrader->downloadLast($this->container->getProperty(UpgradeContainer::DOWNLOAD_PATH), $this->container->getProperty(UpgradeContainer::ARCHIVE_FILENAME));
            if ($res) {
                $md5file = md5_file(realpath($this->container->getProperty(UpgradeContainer::ARCHIVE_FILEPATH)));
                if ($md5file == $upgrader->md5) {
                    $this->next = 'unzip';
                    $this->logger->debug($this->translator->trans('Download complete.', array(), 'Modules.Autoupgrade.Admin'));
                    $this->logger->info($this->translator->trans('Download complete. Now extracting...', array(), 'Modules.Autoupgrade.Admin'));
                } else {
                    $this->logger->error($this->translator->trans('Download complete but MD5 sum does not match (%s).', array($md5file), 'Modules.Autoupgrade.Admin'));
                    $this->logger->info($this->translator->trans('Download complete but MD5 sum does not match (%s). Operation aborted.', array($md5file), 'Modules.Autoupgrade.Admin'));
                    $this->next = 'error';
                }
            } else {
                if ($upgrader->channel == 'private') {
                    $this->logger->error($this->translator->trans('Error during download. The private key may be incorrect.', array(), 'Modules.Autoupgrade.Admin'));
                } else {
                    $this->logger->error($this->translator->trans('Error during download', array(), 'Modules.Autoupgrade.Admin'));
                }
                $this->next = 'error';
            }
        } else {
            $this->logger->error($this->translator->trans('Download directory %s is not writable.', array($this->container->getProperty(UpgradeContainer::DOWNLOAD_PATH)), 'Modules.Autoupgrade.Admin'));
            $this->next = 'error';
        }
    }
}
