<?php

/**
 * 2007-2017 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade;

use Configuration;
use ConfigurationTest;

class UpgradeSelfCheck
{
    /**
     * Recommended PHP Version. If below, display a notice.
     */
    // const RECOMMENDED_PHP_VERSION = 70103;
    const RECOMMENDED_PHP_VERSION = 50400;

    /**
     * @var bool
     */
    private $fOpenOrCurlEnabled;

    /**
     * @var bool
     */
    private $zipEnabled;

    /**
     * @var bool
     */
    private $rootDirectoryWritable;

    /**
     * @var bool
     */
    private $adminAutoUpgradeDirectoryWritable;

    /**
     * @var string
     */
    private $adminAutoUpgradeDirectoryWritableReport = '';

    /**
     * @var bool
     */
    private $shopDeactivated;

    /**
     * @var bool
     */
    private $cacheDisabled;

    /**
     * @var bool
     */
    private $safeModeDisabled;

    /**
     * @var bool|mixed
     */
    private $moduleVersionIsLatest;

    /**
     * @var string
     */
    private $rootWritableReport;

    /**
     * @var false|string
     */
    private $moduleVersion;

    /**
     * @var int
     */
    private $maxExecutionTime;

    /**
     * Warning flag for an old running PHP server.
     *
     * @var bool
     */
    private $phpUpgradeNoticelink;

    /**
     * @var bool
     */
    private $prestashopReady;

    /**
     * @var string
     */
    private $configDir = '/modules/qloautoupgrade/config.xml';

    /**
     * @var Upgrader
     */
    private $upgrader;

    /**
     * Path to the root folder of PS
     *
     * @var string
     */
    private $prodRootPath;

    /**
     * Path to the admin folder of PS
     *
     * @var string
     */
    private $adminPath;

    /**
     * Path to the root folder of the upgrade module
     *
     * @var string
     */
    private $autoUpgradePath;

    /**
     * UpgradeSelfCheck constructor.
     *
     * @param Upgrader $upgrader
     * @param string $prodRootPath
     * @param string $adminPath
     * @param string $autoUpgradePath
     */
    public function __construct(Upgrader $upgrader, $prodRootPath, $adminPath, $autoUpgradePath)
    {
        $this->upgrader = $upgrader;
        $this->prodRootPath = $prodRootPath;
        $this->adminPath = $adminPath;
        $this->autoUpgradePath = $autoUpgradePath;
    }

    /**
     * @return bool
     */
    public function isFOpenOrCurlEnabled()
    {
        if (null !== $this->fOpenOrCurlEnabled) {
            return $this->fOpenOrCurlEnabled;
        }

        return $this->fOpenOrCurlEnabled = ConfigurationTest::test_fopen() || extension_loaded('curl');
    }

    /**
     * @return bool
     */
    public function isZipEnabled()
    {
        if (null !== $this->zipEnabled) {
            return $this->zipEnabled;
        }

        return $this->zipEnabled = extension_loaded('zip');
    }

    /**
     * @return bool
     */
    public function isRootDirectoryWritable()
    {
        if (null !== $this->rootDirectoryWritable) {
            return $this->rootDirectoryWritable;
        }

        return $this->rootDirectoryWritable = $this->checkRootWritable();
    }

    /**
     * @return bool
     */
    public function isAdminAutoUpgradeDirectoryWritable()
    {
        if (null !== $this->adminAutoUpgradeDirectoryWritable) {
            return $this->adminAutoUpgradeDirectoryWritable;
        }

        return $this->adminAutoUpgradeDirectoryWritable = $this->checkAdminDirectoryWritable($this->prodRootPath, $this->adminPath, $this->autoUpgradePath);
    }

    /**
     * @return string
     */
    public function getAdminAutoUpgradeDirectoryWritableReport()
    {
        return $this->adminAutoUpgradeDirectoryWritableReport;
    }

    /**
     * @return bool
     */
    public function isShopDeactivated()
    {
        if (null !== $this->shopDeactivated) {
            return $this->shopDeactivated;
        }

        return $this->shopDeactivated = $this->checkShopIsDeactivated();
    }

    /**
     * @return bool
     */
    public function isCacheDisabled()
    {
        if (null !== $this->cacheDisabled) {
            return $this->cacheDisabled;
        }

        return $this->cacheDisabled = !(defined('_PS_CACHE_ENABLED_') && false != _PS_CACHE_ENABLED_);
    }

    /**
     * @return bool
     */
    public function isSafeModeDisabled()
    {
        if (null !== $this->safeModeDisabled) {
            return $this->safeModeDisabled;
        }

        return $this->safeModeDisabled = $this->checkSafeModeIsDisabled();
    }

    /**
     * @return bool
     */
    public function isModuleVersionLatest()
    {
        if (null !== $this->moduleVersionIsLatest) {
            return $this->moduleVersionIsLatest;
        }

        return $this->moduleVersionIsLatest = $this->checkModuleVersionIsLastest($this->upgrader);
    }

    /**
     * @return string
     */
    public function getRootWritableReport()
    {
        if (null !== $this->rootWritableReport) {
            return $this->rootWritableReport;
        }

        $this->rootWritableReport = '';
        $this->isRootDirectoryWritable();

        return $this->rootWritableReport;
    }

    /**
     * @return string|false
     */
    public function getModuleVersion()
    {
        if (null !== $this->moduleVersion) {
            return $this->moduleVersion;
        }

        return $this->moduleVersion = $this->checkModuleVersion();
    }

    /**
     * @return string
     */
    public function getConfigDir()
    {
        return $this->configDir;
    }

    /**
     * @return int
     */
    public function getMaxExecutionTime()
    {
        if (null !== $this->maxExecutionTime) {
            return $this->maxExecutionTime;
        }

        return $this->maxExecutionTime = $this->checkMaxExecutionTime();
    }

    /**
     * @return bool
     */
    public function isPhpUpgradeRequired()
    {
        if (1 === (int) Configuration::get('PS_AUTOUP_IGNORE_PHP_UPGRADE')) {
            return false;
        }

        if (null !== $this->phpUpgradeNoticelink) {
            return $this->phpUpgradeNoticelink;
        }

        return $this->phpUpgradeNoticelink = $this->checkPhpVersionNeedsUpgrade();
    }

    /**
     * @return bool
     */
    public function isPrestaShopReady()
    {
        if (null === $this->prestashopReady) {
            $this->prestashopReady = $this->runPrestaShopCoreChecks();
        }

        return $this->prestashopReady || 1 === (int) Configuration::get('PS_AUTOUP_IGNORE_REQS');
    }

    /**
     * Indicates if the self check status allows going ahead with the upgrade.
     *
     * @return bool
     */
    public function isOkForUpgrade()
    {
        return
            $this->isFOpenOrCurlEnabled()
            && $this->isZipEnabled()
            && $this->isRootDirectoryWritable()
            && $this->isAdminAutoUpgradeDirectoryWritable()
            && $this->isShopDeactivated()
            && $this->isCacheDisabled()
            && $this->isModuleVersionLatest()
            && $this->isPrestaShopReady();
    }

    /**
     * @return bool
     */
    private function checkRootWritable()
    {
        // Root directory permissions cannot be checked recursively anymore, it takes too much time
        return  ConfigurationTest::test_dir('/', false, $this->rootWritableReport);
    }

    /**
     * @param Upgrader $upgrader
     *
     * @return bool
     */
    private function checkModuleVersionIsLastest(Upgrader $upgrader)
    {
        return version_compare($this->getModuleVersion(), $upgrader->autoupgrade_last_version, '>=');
    }

    /**
     * @return string|false
     */
    private function checkModuleVersion()
    {
        $configFilePath = _PS_ROOT_DIR_ . $this->configDir;

        if (file_exists($configFilePath) && $xml_module_version = simplexml_load_file($configFilePath)) {
            return (string) $xml_module_version->version;
        }

        return false;
    }

    /**
     * Check current PHP version is supported.
     *
     * @return bool
     */
    private function checkPhpVersionNeedsUpgrade()
    {
        return PHP_VERSION_ID < self::RECOMMENDED_PHP_VERSION;
    }

    /**
     * @return bool
     */
    private function checkShopIsDeactivated()
    {
        return
            !Configuration::get('PS_SHOP_ENABLE')
            || (isset($_SERVER['HTTP_HOST']) && in_array($_SERVER['HTTP_HOST'], array('127.0.0.1', 'localhost', '[::1]')));
    }

    /**
     * @param string $prodRootPath
     * @param string $adminPath
     * @param string $adminAutoUpgradePath
     *
     * @return bool
     */
    private function checkAdminDirectoryWritable($prodRootPath, $adminPath, $adminAutoUpgradePath)
    {
        $relativeDirectory = trim(str_replace($prodRootPath, '', $adminAutoUpgradePath), DIRECTORY_SEPARATOR);

        return ConfigurationTest::test_dir(
            $relativeDirectory,
            false,
            $this->adminAutoUpgradeDirectoryWritableReport
        );
    }

    /**
     * @return bool
     */
    private function checkSafeModeIsDisabled()
    {
        $safeMode = @ini_get('safe_mode');
        if (empty($safeMode)) {
            $safeMode = '';
        }

        return !in_array(strtolower($safeMode), array(1, 'on'));
    }

    /**
     * @return int
     */
    private function checkMaxExecutionTime()
    {
        return (int) @ini_get('max_execution_time');
    }

    /**
     * Ask the core to run its tests, if available.
     *
     * @return bool
     */
    public function runPrestaShopCoreChecks()
    {
        if (!class_exists('ConfigurationTest')) {
            return true;
        }

        $defaultTests = ConfigurationTest::check(ConfigurationTest::getDefaultTests());
        foreach ($defaultTests as $testResult) {
            if ($testResult !== 'ok') {
                return false;
            }
        }

        return true;
    }
}
