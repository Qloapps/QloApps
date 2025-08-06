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

use PrestaShop\Module\AutoUpgrade\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeConfiguration;

class ZipAction
{
    /**
     * @var int Number of files added in a zip per request
     */
    private $configMaxNbFilesCompressedInARow;
    /**
     * @var int Max file size allowed in a zip file
     */
    private $configMaxFileSizeAllowed;

    private $logger;
    private $translator;

    /**
     * @var string Path to the shop, in order to remove it from the archived file paths
     */
    private $prodRootDir;

    public function __construct($translator, LoggerInterface $logger, UpgradeConfiguration $configuration, $prodRootDir)
    {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->prodRootDir = $prodRootDir;

        $this->configMaxNbFilesCompressedInARow = $configuration->getNumberOfFilesPerCall();
        $this->configMaxFileSizeAllowed = $configuration->getMaxFileToBackup();
    }

    /**
     * Add files to an archive.
     * Note the number of files added can be limited.
     *
     * @var array List of files to add
     * @var string $toFile
     */
    public function compress(&$filesList, $toFile)
    {
        $zip = $this->open($toFile, \ZipArchive::CREATE);
        if ($zip === false) {
            return false;
        }

        for ($i = 0; $i < $this->configMaxNbFilesCompressedInARow && count($filesList); ++$i) {
            $file = array_shift($filesList);

            $archiveFilename = $this->getFilepathInArchive($file);
            if (!$this->isFileWithinFileSizeLimit($file)) {
                continue;
            }

            if (!$zip->addFile($file, $archiveFilename)) {
                // if an error occur, it's more safe to delete the corrupted backup
                $zip->close();
                (new Filesystem())->remove($toFile);
                $this->logger->error($this->translator->trans(
                    'Error when trying to add %filename% to archive %archive%.',
                    array(
                        '%filename%' => $file,
                        '%archive%' => $archiveFilename,
                    ),
                    'Modules.Autoupgrade.Admin'
                ));

                return false;
            }

            $this->logger->debug($this->translator->trans(
                '%filename% added to archive. %filescount% files left.',
                array(
                    '%filename%' => $archiveFilename,
                    '%filescount%' => count($filesList),
                ),
                'Modules.Autoupgrade.Admin'
            ));
        }

        if (!$zip->close()) {
            $this->logger->error($this->translator->trans(
                'Could not close the Zip file properly. Check you are allowed to write on the disk and there is available space on it.',
                array(),
                'Modules.Autoupgrade.Admin'
            ));

            return false;
        }

        return true;
    }

    /**
     * Extract an archive to the given directory
     *
     * @return bool success
     *              we need a copy of it to be able to restore without keeping Tools and Autoload stuff
     */
    public function extract($from_file, $to_dir)
    {
        if (!is_file($from_file)) {
            $this->logger->error($this->translator->trans('%s is not a file', array($from_file), 'Modules.Autoupgrade.Admin'));

            return false;
        }

        if (!file_exists($to_dir)) {
            // ToDo: Use Filesystem from Symfony
            if (!mkdir($to_dir)) {
                $this->logger->error($this->translator->trans('Unable to create directory %s.', array($to_dir), 'Modules.Autoupgrade.Admin'));

                return false;
            }
            chmod($to_dir, 0775);
        }

        $zip = $this->open($from_file);
        if ($zip === false) {
            return false;
        }

        for ($i = 0; $i < $zip->numFiles; ++$i) {
            if (!$zip->extractTo($to_dir, array($zip->getNameIndex($i)))) {
                $this->logger->error(
                    $this->translator->trans(
                        'Could not extract %file% from backup, the destination might not be writable.',
                        ['%file%' => $zip->statIndex($i)['name']],
                        'Modules.Autoupgrade.Admin'
                    )
                );
                $zip->close();

                return false;
            }
        }

        $zip->close();
        $this->logger->debug($this->translator->trans('Content of archive %zip% is extracted', ['%zip%' => $from_file], 'Modules.Autoupgrade.Admin'));

        return true;
    }

    /**
     * Lists the files present in the given archive
     *
     * @var string Path to the file
     *
     * @return array
     */
    public function listContent($zipfile)
    {
        if (!file_exists($zipfile)) {
            return [];
        }

        $zip = $this->open($zipfile);
        if ($zip === false) {
            $this->logger->error($this->translator->trans('[ERROR] Unable to list archived files', array(), 'Modules.Autoupgrade.Admin'));

            return [];
        }

        $files = [];
        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $files[] = $zip->getNameIndex($i);
        }

        return $files;
    }

    /**
     * Get the path of a file from the archive root
     *
     * @var string Path of the file on the filesystem
     *
     * @return string Path of the file in the backup archive
     */
    private function getFilepathInArchive($filepath)
    {
        return ltrim(str_replace($this->prodRootDir, '', $filepath), DIRECTORY_SEPARATOR);
    }

    /**
     * Checks a file size matches the given limits
     *
     * @var string Path to a file
     *
     * @return bool Size is inside the maximum limit
     */
    private function isFileWithinFileSizeLimit($filepath)
    {
        $size = filesize($filepath);
        $pass = ($size < $this->configMaxFileSizeAllowed);
        if (!$pass) {
            $this->logger->debug($this->translator->trans(
                'File %filename% (size: %filesize%) has been skipped during backup.',
                array(
                    '%filename%' => $this->getFilepathInArchive($filepath),
                    '%filesize%' => $size,
                ),
                'Modules.Autoupgrade.Admin'
            ));
        }

        return $pass;
    }

    /**
     * Open an archive
     *
     * @var string Path to the archive
     * @var int ZipArchive flags
     *
     * @return false|\ZipArchive
     */
    private function open($zipFile, $flags = null)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, $flags) !== true || empty($zip->filename)) {
            $this->logger->error($this->translator->trans('Unable to open zipFile %s', array($zipFile), 'Modules.Autoupgrade.Admin'));

            return false;
        }

        return $zip;
    }
}
