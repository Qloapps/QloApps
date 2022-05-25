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

use PrestaShop\Module\AutoUpgrade\AjaxResponse;
use PrestaShop\Module\AutoUpgrade\TaskRunner\ChainedTasks;
use PrestaShop\Module\AutoUpgrade\UpgradeContainer;

/**
 * Execute the whole upgrade process in a single request.
 */
class AllUpgradeTasks extends ChainedTasks
{
    const initialTask = 'upgradeNow';
    const TASKS_WITH_RESTART = ['upgradeFiles', 'upgradeDb'];

    protected $step = self::initialTask;

    /**
     * Customize the execution context with several options
     * > action: Replace the initial step to run
     * > channel: Makes a specific upgrade (minor, major etc.)
     * > data: Loads an encoded array of data coming from another request.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        if (!empty($options['action'])) {
            $this->step = $options['action'];
        }

        if (!empty($options['channel'])) {
            $this->container->getUpgradeConfiguration()->merge(array(
                'channel' => $options['channel'],
                // Switch on default theme if major upgrade (i.e: 1.6 -> 1.7)
                'PS_AUTOUP_CHANGE_DEFAULT_THEME' => ($options['channel'] === 'major'),
            ));
            $this->container->getUpgrader()->channel = $options['channel'];
            $this->container->getUpgrader()->checkPSVersion(true);
        }

        if (!empty($options['data'])) {
            $this->container->getState()->importFromEncodedData($options['data']);
        }
    }

    /**
     * For some steps, we may require a new request to be made.
     * For instance, in case of obsolete autoloader or loaded classes after a file copy.
     */
    protected function checkIfRestartRequested(AjaxResponse $response)
    {
        if (parent::checkIfRestartRequested($response)) {
            return true;
        }

        if (!$response->getStepDone()) {
            return false;
        }

        if (!in_array($this->step, self::TASKS_WITH_RESTART)) {
            return false;
        }

        $this->logger->info('Restart requested. Please run the following command to continue your upgrade:');
        $args = $_SERVER['argv'];
        foreach ($args as $key => $arg) {
            if (strpos($arg, '--data') === 0) {
                unset($args[$key]);
            }
        }
        $this->logger->info('$ ' . implode(' ', $args) . ' --action=' . $response->getNext() . ' --data=' . $this->getEncodedResponse());

        return true;
    }

    /**
     * Set default config on first run.
     */
    public function init()
    {
        if ($this->step === self::initialTask) {
            parent::init();
            $this->container->getState()->initDefault(
                $this->container->getUpgrader(),
                $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH),
                $this->container->getProperty(UpgradeContainer::PS_VERSION));
        }
    }
}
