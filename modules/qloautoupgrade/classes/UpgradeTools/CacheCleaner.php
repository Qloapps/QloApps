<?php

/*
 * 2007-2019 PrestaShop
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
 *  @copyright  2007-2019 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\Module\AutoUpgrade\UpgradeTools;

use PrestaShop\Module\AutoUpgrade\UpgradeContainer;
use PrestaShop\Module\AutoUpgrade\Log\LoggerInterface;

class CacheCleaner
{
    /**
     * @var UpgradeContainer
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(UpgradeContainer $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function cleanFolders()
    {
        $dirsToClean = array(
            $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/app/cache/',
            $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/cache/smarty/cache/',
            $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/cache/smarty/compile/',
            $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/var/cache/',
        );

        $defaultThemeNames = array(
            'default',
            'prestashop',
            'default-boostrap',
            'classic',
        );

        if (defined('_THEME_NAME_') && $this->container->getUpgradeConfiguration()->shouldUpdateDefaultTheme() && in_array(_THEME_NAME_, $defaultThemeNames)) {
            $dirsToClean[] = $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/themes/' . _THEME_NAME_ . '/cache/';
        }

        foreach ($dirsToClean as $dir) {
            if (!file_exists($dir)) {
                $this->logger->debug($this->container->getTranslator()->trans('[SKIP] directory "%s" does not exist and cannot be emptied.', array(str_replace($this->container->getProperty(UpgradeContainer::PS_ROOT_PATH), '', $dir)), 'Modules.Autoupgrade.Admin'));
                continue;
            }
            foreach (scandir($dir) as $file) {
                if ($file[0] === '.' || $file === 'index.php') {
                    continue;
                }
                // ToDo: Use Filesystem instead ?
                if (is_file($dir . $file)) {
                    unlink($dir . $file);
                } elseif (is_dir($dir . $file . DIRECTORY_SEPARATOR)) {
                    FilesystemAdapter::deleteDirectory($dir . $file . DIRECTORY_SEPARATOR);
                }
                $this->logger->debug($this->container->getTranslator()->trans('[CLEANING CACHE] File %s removed', array($file), 'Modules.Autoupgrade.Admin'));
            }
        }
    }

    public function cleanClassIndex()
    {    
        $filePath = $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/cache/';
        $file = 'class_index.php';
        if (is_file($filePath . $file)) {
            unlink($filePath . $file);
        }
        $this->logger->debug($this->container->getTranslator()->trans('[CLEANING CACHE] File %s removed', array($file), 'Modules.Autoupgrade.Admin'));
    }
}
