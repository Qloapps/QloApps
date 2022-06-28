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

class BackupFinder
{
    /**
     * @var string[]
     */
    private $availableBackups;

    /**
     * @var string
     */
    private $backupPath;

    /**
     * BackupFinder constructor.
     *
     * @param string $backupPath
     */
    public function __construct($backupPath)
    {
        $this->backupPath = $backupPath;
    }

    /**
     * @return array
     */
    public function getAvailableBackups()
    {
        if (null === $this->availableBackups) {
            $this->availableBackups = $this->buildBackupList();
        }

        return $this->availableBackups;
    }

    /**
     * @return array
     */
    private function buildBackupList()
    {
        return array_intersect(
            $this->getBackupDbAvailable($this->backupPath),
            $this->getBackupFilesAvailable($this->backupPath)
        );
    }

    /**
     * @param string $backupPath
     *
     * @return array
     */
    private function getBackupDbAvailable($backupPath)
    {
        $array = array();

        $files = scandir($backupPath);

        foreach ($files as $file) {
            if ($file[0] == 'V' && is_dir($backupPath . DIRECTORY_SEPARATOR . $file)) {
                $array[] = $file;
            }
        }

        return $array;
    }

    /**
     * @param string $backupPath
     *
     * @return array
     */
    private function getBackupFilesAvailable($backupPath)
    {
        $array = array();
        $files = scandir($backupPath);

        foreach ($files as $file) {
            if ($file[0] != '.' && substr($file, 0, 16) == 'auto-backupfiles') {
                $array[] = preg_replace('#^auto-backupfiles_(.*-[0-9a-f]{1,8})\..*$#', '$1', $file);
            }
        }

        return $array;
    }
}
