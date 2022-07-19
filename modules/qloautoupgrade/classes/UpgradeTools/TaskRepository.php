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

use PrestaShop\Module\AutoUpgrade\UpgradeContainer;

class TaskRepository
{
    public static function get($step, UpgradeContainer $container)
    {
        switch ($step) {
            // MISCELLANEOUS (upgrade configuration, checks etc.)
            case 'checkFilesVersion':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Miscellaneous\CheckFilesVersion($container);
            case 'compareReleases':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Miscellaneous\CompareReleases($container);
            case 'getChannelInfo':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Miscellaneous\GetChannelInfo($container);
            case 'updateConfig':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Miscellaneous\UpdateConfig($container);

            // ROLLBACK
            case 'noRollbackFound':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Rollback\NoRollbackFound($container);
            case 'restoreDb':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Rollback\RestoreDb($container);
            case 'restoreFiles':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Rollback\RestoreFiles($container);
            case 'rollback':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Rollback\Rollback($container);
            case 'rollbackComplete':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Rollback\RollbackComplete($container);

            // // UPGRADE
            case 'backupDb':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\BackupDb($container);
            case 'backupFiles':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\BackupFiles($container);
            case 'cleanDatabase':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\CleanDatabase($container);
            case 'download':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\Download($container);
            case 'removeSamples':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\RemoveSamples($container);
            case 'upgradeComplete':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\UpgradeComplete($container);
            case 'upgradeDb':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\UpgradeDb($container);
            case 'upgradeFiles':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\UpgradeFiles($container);
            case 'upgradeModules':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\UpgradeModules($container);
            case 'upgradeNow':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\UpgradeNow($container);
            case 'unzip':
                return new \PrestaShop\Module\AutoUpgrade\TaskRunner\Upgrade\Unzip($container);
        }
        error_log('Unknown step ' . $step);

        // return new \PrestaShop\Module\AutoUpgrade\TaskRunner\NullTask($container);
    }
}
