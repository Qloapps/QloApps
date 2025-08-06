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

namespace PrestaShop\Module\AutoUpgrade\Parameters;

use PrestaShop\Module\AutoUpgrade\Tools14;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class used for management of to do files for upgrade tasks.
 * Load / Save / Delete etc.
 */
class FileConfigurationStorage
{
    /**
     * @var string Location where all the configuration files are stored
     */
    private $configPath;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct($path)
    {
        $this->configPath = $path;
        $this->filesystem = new Filesystem();
    }

    /**
     * UpgradeConfiguration loader.
     *
     * @param string $fileName File name to load
     *
     * @return mixed or array() as default value
     */
    public function load($fileName = '')
    {
        $configFilePath = $this->configPath . $fileName;
        $config = array();

        if (file_exists($configFilePath)) {
            $config = @unserialize(base64_decode(Tools14::file_get_contents($configFilePath)));
        }

        return $config;
    }

    /**
     * @param mixed $config
     * @param string $fileName Destination name of the config file
     *
     * @return bool
     */
    public function save($config, $fileName)
    {
        $configFilePath = $this->configPath . $fileName;
        try {
            $this->filesystem->dumpFile($configFilePath, base64_encode(serialize($config)));

            return true;
        } catch (IOException $e) {
            // TODO: $e needs to be logged
            return false;
        }
    }

    /**
     * @return array Temporary files path & name
     */
    public function getFilesList()
    {
        $files = array();
        foreach (UpgradeFileNames::$tmp_files as $file) {
            $files[$file] = $this->getFilePath(constant('PrestaShop\\Module\\AutoUpgrade\\Parameters\\UpgradeFileNames::' . $file));
        }

        return $files;
    }

    /**
     * Delete all temporary files in the config folder.
     */
    public function cleanAll()
    {
        $this->filesystem->remove(self::getFilesList());
    }

    /**
     * Delete a file from the filesystem.
     *
     * @param string $fileName
     */
    public function clean($fileName)
    {
        $this->filesystem->remove($this->getFilePath($fileName));
    }

    /**
     * Check if a file exists on the filesystem.
     *
     * @param string $fileName
     */
    public function exists($fileName)
    {
        return $this->filesystem->exists($this->getFilePath($fileName));
    }

    /**
     * Generate the complete path to a given file.
     *
     * @param string $file Name
     *
     * @return string Pgit gui&
     *                ath
     */
    private function getFilePath($file)
    {
        return $this->configPath . $file;
    }
}
