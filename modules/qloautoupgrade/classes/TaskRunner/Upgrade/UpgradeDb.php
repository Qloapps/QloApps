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

use PrestaShop\Module\AutoUpgrade\TaskRunner\AbstractTask;
use PrestaShop\Module\AutoUpgrade\UpgradeException;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\CoreUpgrader\CoreUpgrader;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\SettingsFileWriter;

class UpgradeDb extends AbstractTask
{
    public function run()
    {
        try {
            $this->getCoreUpgrader()->doUpgrade();
        } catch (UpgradeException $e) {
            $this->next = 'error';
            $this->error = true;
            foreach ($e->getQuickInfos() as $log) {
                $this->logger->debug($log);
            }
            $this->logger->error($this->translator->trans('Error during database upgrade. You may need to restore your database.', array(), 'Modules.Autoupgrade.Admin'));
            $this->logger->error($e->getMessage());

            return false;
        }
        $this->next = 'upgradeModules';
        $this->logger->info($this->translator->trans('Database upgraded. Now upgrading your Addons modules...', array(), 'Modules.Autoupgrade.Admin'));

        return true;
    }

    public function getCoreUpgrader()
    {
        // if (version_compare($this->container->getState()->getInstallVersion(), '1.5.1.0', '<=')) {
        //     return new CoreUpgrader151($this->container, $this->logger);
        // }
        return new CoreUpgrader($this->container, $this->logger);
    }

    public function init()
    {
        $this->container->getCacheCleaner()->cleanFolders();
        $this->container->getCacheCleaner()->cleanClassIndex();

        // Migrating settings file
        $this->container->initPrestaShopAutoloader();
        (new SettingsFileWriter($this->container, $this->translator))->migrateSettingsFile($this->logger);
        parent::init();
    }
}
