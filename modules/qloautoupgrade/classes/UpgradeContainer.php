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

namespace PrestaShop\Module\AutoUpgrade;

use PrestaShop\Module\AutoUpgrade\Log\LegacyLogger;
use PrestaShop\Module\AutoUpgrade\Log\Logger;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\CacheCleaner;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\FileFilter;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\FilesystemAdapter;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\ModuleAdapter;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\SymfonyAdapter;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\Translation;
use PrestaShop\Module\AutoUpgrade\Parameters\FileConfigurationStorage;
use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfigurationStorage;
// use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfiguration;
use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeFileNames;
use PrestaShop\Module\AutoUpgrade\Twig\TransFilterExtension;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\Translator;
use Twig_Loader_Filesystem;
use Twig_Environment;

/**
 * Class responsible of the easy (& Lazy) loading of the different services
 * available for the upgrade.
 */
class UpgradeContainer
{
    const WORKSPACE_PATH = 'workspace'; // AdminSelfUpgrade::$autoupgradePath
    const BACKUP_PATH = 'backup';
    const DOWNLOAD_PATH = 'download';
    const LATEST_PATH = 'latest'; // AdminSelfUpgrade::$latestRootDir
    const LATEST_DIR = 'lastest/';
    const TMP_PATH = 'tmp';
    const PS_ADMIN_PATH = 'ps_admin';
    const PS_ADMIN_SUBDIR = 'ps_admin_subdir';
    const PS_ROOT_PATH = 'ps_root'; // AdminSelfUpgrade::$prodRootDir
    const ARCHIVE_FILENAME = 'destDownloadFilename';
    const ARCHIVE_FILEPATH = 'destDownloadFilepath';
    const PS_VERSION = 'version';
    const QLO_VERSION = 'qlo_version';

    /**
     * @var CacheCleaner
     */
    private $cacheCleaner;

    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @var \Db
     */
    public $db;

    /**
     * @var FileConfigurationStorage
     */
    private $fileConfigurationStorage;

    /**
     * @var FileFilter
     */
    private $fileFilter;

    /**
     * @var PrestashopConfiguration
     */
    private $prestashopConfiguration;

    /**
     * @var UpgradeConfiguration
     */
    private $upgradeConfiguration;

    /**
     * @var FilesystemAdapter
     */
    private $filesystemAdapter;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ModuleAdapter
     */
    private $moduleAdapter;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var State
     */
    private $state;

    /**
     * @var SymfonyAdapter
     */
    private $symfonyAdapter;

    /**
     * @var Upgrader
     */
    private $upgrader;

    /**
     * @var Workspace
     */
    private $workspace;

    /**
     * @var ZipAction
     */
    private $zipAction;

    /**
     * AdminSelfUpgrade::$autoupgradePath
     * Ex.: /var/www/html/PrestaShop/admin-dev/autoupgrade.
     *
     * @var string Path to the base folder of the autoupgrade (in admin)
     */
    private $autoupgradeWorkDir;

    /**
     * @var string Absolute path to the admin folder
     */
    private $adminDir;

    /**
     * @var string Absolute path to ps root folder of PS
     */
    private $psRootDir;

    public function __construct($psRootDir, $adminDir, $moduleSubDir = 'qloautoupgrade')
    {
        $this->autoupgradeWorkDir = $adminDir . DIRECTORY_SEPARATOR . $moduleSubDir;
        $this->adminDir = $adminDir;
        $this->psRootDir = $psRootDir;
    }

    /**
     * @return string
     */
    public function getProperty($property)
    {
        switch ($property) {
            case self::PS_ADMIN_PATH:
                return $this->adminDir;
            case self::PS_ADMIN_SUBDIR:
                return trim(str_replace($this->getProperty(self::PS_ROOT_PATH), '', $this->getProperty(self::PS_ADMIN_PATH)), DIRECTORY_SEPARATOR);
            case self::PS_ROOT_PATH:
                return $this->psRootDir;
            case self::WORKSPACE_PATH:
                return $this->autoupgradeWorkDir;
            case self::BACKUP_PATH:
                return $this->autoupgradeWorkDir . DIRECTORY_SEPARATOR . 'backup';
            case self::DOWNLOAD_PATH:
                return $this->autoupgradeWorkDir . DIRECTORY_SEPARATOR . 'download';
            case self::LATEST_PATH:
                return $this->autoupgradeWorkDir . DIRECTORY_SEPARATOR . 'latest';
            case self::LATEST_DIR:
                return $this->autoupgradeWorkDir . DIRECTORY_SEPARATOR . 'latest' . DIRECTORY_SEPARATOR;
            case self::TMP_PATH:
                return $this->autoupgradeWorkDir . DIRECTORY_SEPARATOR . 'tmp';
            case self::ARCHIVE_FILENAME:
                return $this->getUpgradeConfiguration()->getArchiveFilename();
            case self::ARCHIVE_FILEPATH:
                return $this->getProperty(self::DOWNLOAD_PATH) . DIRECTORY_SEPARATOR . $this->getProperty(self::ARCHIVE_FILENAME);
            case self::PS_VERSION:
                return $this->getPrestaShopConfiguration()->getPrestaShopVersion();
            case self::QLO_VERSION:
                return $this->getPrestaShopConfiguration()->getQloAppsVersion();
        }
    }

    /**
     * Init and return CacheCleaner
     *
     * @return CacheCleaner
     */
    public function getCacheCleaner()
    {
        if (null !== $this->cacheCleaner) {
            return $this->cacheCleaner;
        }

        return $this->cacheCleaner = new CacheCleaner($this, $this->getLogger());
    }

    /**
     * @return Cookie
     */
    public function getCookie()
    {
        if (null !== $this->cookie) {
            return $this->cookie;
        }

        $this->cookie = new Cookie(
            $this->getProperty(self::PS_ADMIN_SUBDIR),
            $this->getProperty(self::TMP_PATH));

        return $this->cookie;
    }

    /**
     * @return \Db
     */
    public function getDb()
    {
        return \Db::getInstance();
    }

    /**
     * Return the path to the zipfile containing prestashop.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->getProperty(self::ARCHIVE_FILEPATH);
    }

    /**
     * @return FileConfigurationStorage
     */
    public function getFileConfigurationStorage()
    {
        if (null !== $this->fileConfigurationStorage) {
            return $this->fileConfigurationStorage;
        }

        $this->fileConfigurationStorage = new FileConfigurationStorage($this->getProperty(self::WORKSPACE_PATH) . DIRECTORY_SEPARATOR);

        return $this->fileConfigurationStorage;
    }

    /**
     * @return FileFilter
     */
    public function getFileFilter()
    {
        if (null !== $this->fileFilter) {
            return $this->fileFilter;
        }

        $this->fileFilter = new FileFilter($this->getUpgradeConfiguration());

        return $this->fileFilter;
    }

    /**
     * @return Upgrader
     */
    public function getUpgrader()
    {
        if (null !== $this->upgrader) {
            return $this->upgrader;
        }
        if (!defined('_PS_ROOT_DIR_')) {
            define('_PS_ROOT_DIR_', $this->getProperty(self::PS_ROOT_PATH));
        }
        // in order to not use Tools class
        // $upgrader = new Upgrader($this->getProperty(self::PS_VERSION));
        $upgrader = new Upgrader($this->getProperty(self::QLO_VERSION));
        preg_match('#([0-9]+\.[0-9]+)(?:\.[0-9]+){1,2}#', $this->getProperty(self::QLO_VERSION), $matches);
        $upgrader->branch = $matches[1];
        $upgradeConfiguration = $this->getUpgradeConfiguration();
        $channel = $upgradeConfiguration->get('channel');
        switch ($channel) {
            case 'archive':
                $upgrader->channel = 'archive';
                $upgrader->version_num = $upgradeConfiguration->get('archive.version_num');
                $upgrader->checkPSVersion(true, array('archive'));
                break;
            case 'directory':
                $upgrader->channel = 'directory';
                $upgrader->version_num = $upgradeConfiguration->get('directory.version_num');
                $upgrader->checkPSVersion(true, array('directory'));
                break;
            default:
                $upgrader->channel = $channel;
                if ($upgradeConfiguration->get('channel') == 'private' && !$upgradeConfiguration->get('private_allow_major')) {
                    $upgrader->checkPSVersion(false, array('private', 'minor'));
                } else {
                    $upgrader->checkPSVersion(false, array('minor'));
                }
        }
        $this->getState()->setInstallVersion($upgrader->version_num);
        $this->getState()->setCurrentVersion($this->getProperty(self::QLO_VERSION));
        $this->upgrader = $upgrader;

        return $this->upgrader;
    }

    /**
     * @return FilesystemAdapter
     */
    public function getFilesystemAdapter()
    {
        if (null !== $this->filesystemAdapter) {
            return $this->filesystemAdapter;
        }

        $this->filesystemAdapter = new FilesystemAdapter(
            $this->getFileFilter(),
            $this->getState()->getRestoreFilesFilename(),
            $this->getProperty(self::WORKSPACE_PATH),
            str_replace($this->getProperty(self::PS_ROOT_PATH), '', $this->getProperty(self::PS_ADMIN_PATH)), $this->getProperty(self::PS_ROOT_PATH));

        return $this->filesystemAdapter;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        if (null !== $this->logger) {
            return $this->logger;
        }

        $logFile = null;
        if (is_writable($this->getProperty(self::TMP_PATH))) {
            $logFile = $this->getProperty(self::TMP_PATH) . DIRECTORY_SEPARATOR . 'log.txt';
        }
        $this->logger = new LegacyLogger($logFile);

        return $this->logger;
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return ModuleAdapter
     */
    public function getModuleAdapter()
    {
        if (null !== $this->moduleAdapter) {
            return $this->moduleAdapter;
        }

        $this->moduleAdapter = new ModuleAdapter(
            $this->getDb(),
            $this->getTranslator(),
            $this->getProperty(self::PS_ROOT_PATH) . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR,
            $this->getProperty(self::TMP_PATH),
            $this->getState()->getInstallVersion(),
            $this->getZipAction(),
            $this->getSymfonyAdapter()
        );

        return $this->moduleAdapter;
    }

    /**
     * @return State
     */
    public function getState()
    {
        if (null !== $this->state) {
            return $this->state;
        }

        $this->state = new State();

        return $this->state;
    }

    /**
     * @return Translation
     */
    public function getTranslationAdapter()
    {
        return new Translation($this->getTranslator(), $this->getLogger(), $this->getState()->getInstalledLanguagesIso());
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return new Translator('AdminSelfUpgrade');
    }

    /**
     * @return Twig_Environment
     */
    public function getTwig()
    {
        if (null !== $this->twig) {
            return $this->twig;
        }

        // Using independant template engine for 1.6 & 1.7 compatibility
        $loader = new Twig_Loader_Filesystem();
        $loader->addPath(realpath(__DIR__ . '/..') . '/views/templates', 'ModuleAutoUpgrade');
        $twig = new Twig_Environment($loader);
        $twig->addExtension(new TransFilterExtension($this->getTranslator()));

        $this->twig = $twig;

        return $this->twig;
    }

    /**
     * @return PrestashopConfiguration
     */
    public function getPrestaShopConfiguration()
    {
        if (null !== $this->prestashopConfiguration) {
            return $this->prestashopConfiguration;
        }

        $this->prestashopConfiguration = new PrestashopConfiguration(
            $this->getProperty(self::WORKSPACE_PATH),
            $this->getProperty(self::PS_ROOT_PATH)
        );

        return $this->prestashopConfiguration;
    }

    /**
     * @return SymfonyAdapter
     */
    public function getSymfonyAdapter()
    {
        if (null !== $this->symfonyAdapter) {
            return $this->symfonyAdapter;
        }

        $this->symfonyAdapter = new SymfonyAdapter($this->getState()->getInstallVersion());

        return $this->symfonyAdapter;
    }

    /**
     * @return UpgradeConfiguration
     */
    public function getUpgradeConfiguration()
    {
        if (null !== $this->upgradeConfiguration) {
            return $this->upgradeConfiguration;
        }
        $upgradeConfigurationStorage = new UpgradeConfigurationStorage($this->getProperty(self::WORKSPACE_PATH) . DIRECTORY_SEPARATOR);
        $this->upgradeConfiguration = $upgradeConfigurationStorage->load(UpgradeFileNames::CONFIG_FILENAME);

        return $this->upgradeConfiguration;
    }

    /**
     * @return UpgradeConfigurationStorage
     */
    public function getUpgradeConfigurationStorage()
    {
        return new UpgradeConfigurationStorage($this->getProperty(self::WORKSPACE_PATH) . DIRECTORY_SEPARATOR);
    }

    /**
     * @return Workspace
     */
    public function getWorkspace()
    {
        if (null !== $this->workspace) {
            return $this->workspace;
        }

        $paths = array();
        $properties = array(
            self::WORKSPACE_PATH, self::BACKUP_PATH,
            self::DOWNLOAD_PATH, self::LATEST_PATH,
            self::TMP_PATH, );

        foreach ($properties as $property) {
            $paths[] = $this->getProperty($property);
        }

        $this->workspace = new Workspace(
            $this->getLogger(),
            $this->getTranslator(),
            $paths
        );

        return $this->workspace;
    }

    /**
     * @return ZipAction
     */
    public function getZipAction()
    {
        if (null !== $this->zipAction) {
            return $this->zipAction;
        }

        $this->zipAction = new ZipAction(
            $this->getTranslator(),
            $this->getLogger(),
            $this->getUpgradeConfiguration(),
            $this->getProperty(self::PS_ROOT_PATH));

        return $this->zipAction;
    }

    /**
     * Checks if the composer autoload exists, and loads it.
     */
    public function initPrestaShopAutoloader()
    {
        $autoloader = $this->getProperty(self::PS_ROOT_PATH) . '/vendor/autoload.php';
        if (file_exists($autoloader)) {
            require_once $autoloader;
        }

        require_once $this->getProperty(self::PS_ROOT_PATH) . '/config/defines.inc.php';
        require_once $this->getProperty(self::PS_ROOT_PATH) . '/config/autoload.php';
    }

    public function initPrestaShopCore()
    {
        require_once $this->getProperty(self::PS_ROOT_PATH) . '/config/config.inc.php';

        $id_employee = !empty($_COOKIE['id_employee']) ? $_COOKIE['id_employee'] : 1;
        \Context::getContext()->employee = new \Employee((int) $id_employee);
    }
}
