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

namespace PrestaShop\Module\AutoUpgrade\TaskRunner\Miscellaneous;

use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeFileNames;
use PrestaShop\Module\AutoUpgrade\UpgradeContainer;
use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfigurationStorage;
use PrestaShop\Module\AutoUpgrade\TaskRunner\AbstractTask;
use PrestaShop\Module\AutoUpgrade\Upgrader;

/**
 * update configuration after validating the new values.
 */
class UpdateConfig extends AbstractTask
{
    public function run()
    {
        // nothing next
        $this->next = '';

        // Was coming from AdminSelfUpgrade::currentParams before
        $request = $this->getRequestParams();
        $config = array();
        // update channel
        if (isset($request['channel'])) {
            $config['channel'] = $request['channel'];
            $config['archive.filename'] = Upgrader::DEFAULT_FILENAME;
            // Switch on default theme if major upgrade (i.e: 1.6 -> 1.7)
            $config['PS_AUTOUP_CHANGE_DEFAULT_THEME'] = ($request['channel'] === 'major');
        }
        if (isset($request['private_release_link'], $request['private_release_md5'])) {
            $config['channel'] = 'private';
            $config['private_release_link'] = $request['private_release_link'];
            $config['private_release_md5'] = $request['private_release_md5'];
            $config['private_allow_major'] = $request['private_allow_major'];
        }
        // if (!empty($request['archive_name']) && !empty($request['archive_num']))
        if (!empty($request['archive_prestashop'])) {
            $file = $request['archive_prestashop'];
            if (!file_exists($this->container->getProperty(UpgradeContainer::DOWNLOAD_PATH) . DIRECTORY_SEPARATOR . $file)) {
                $this->error = true;
                $this->logger->info($this->translator->trans('File %s does not exist. Unable to select that channel.', array($file), 'Modules.Autoupgrade.Admin'));

                return false;
            }
            if (empty($request['archive_num'])) {
                $this->error = true;
                $this->logger->info($this->translator->trans('Version number is missing. Unable to select that channel.', array(), 'Modules.Autoupgrade.Admin'));

                return false;
            }
            $config['channel'] = 'archive';
            $config['archive.filename'] = $request['archive_prestashop'];
            $config['archive.version_num'] = $request['archive_num'];
            // $config['archive_name'] = $request['archive_name'];
            $this->logger->info($this->translator->trans('Upgrade process will use archive.', array(), 'Modules.Autoupgrade.Admin'));
        }
        if (isset($request['directory_num'])) {
            $config['channel'] = 'directory';
            if (empty($request['directory_num']) || strpos($request['directory_num'], '.') === false) {
                $this->error = true;
                $this->logger->info($this->translator->trans('Version number is missing. Unable to select that channel.', array(), 'Modules.Autoupgrade.Admin'));

                return false;
            }

            $config['directory.version_num'] = $request['directory_num'];
        }
        if (isset($request['skip_backup'])) {
            $config['skip_backup'] = $request['skip_backup'];
        }

        if (!$this->writeConfig($config)) {
            $this->error = true;
            $this->logger->info($this->translator->trans('Error on saving configuration', array(), 'Modules.Autoupgrade.Admin'));
        }
    }

    protected function getRequestParams()
    {
        return empty($_REQUEST['params']) ? array() : $_REQUEST['params'];
    }

    /**
     * update module configuration (saved in file UpgradeFiles::configFilename) with $new_config.
     *
     * @param array $config
     *
     * @return bool true if success
     */
    private function writeConfig($config)
    {
        if (!$this->container->getFileConfigurationStorage()->exists(UpgradeFileNames::CONFIG_FILENAME) && !empty($config['channel'])) {
            $this->container->getUpgrader()->channel = $config['channel'];
            $this->container->getUpgrader()->checkPSVersion();

            $this->container->getState()->setInstallVersion($this->container->getUpgrader()->version_num);
        }

        $this->container->getUpgradeConfiguration()->merge($config);
        $this->logger->info($this->translator->trans('Configuration successfully updated.', array(), 'Modules.Autoupgrade.Admin') . ' <strong>' . $this->translator->trans('This page will now be reloaded and the module will check if a new version is available.', array(), 'Modules.Autoupgrade.Admin') . '</strong>');

        return (new UpgradeConfigurationStorage($this->container->getProperty(UpgradeContainer::WORKSPACE_PATH) . DIRECTORY_SEPARATOR))->save($this->container->getUpgradeConfiguration(), UpgradeFileNames::CONFIG_FILENAME);
    }
}
