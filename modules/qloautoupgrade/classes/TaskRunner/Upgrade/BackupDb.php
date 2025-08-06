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

use PrestaShop\Module\AutoUpgrade\Tools14;
use PrestaShop\Module\AutoUpgrade\Parameters\UpgradeFileNames;
use PrestaShop\Module\AutoUpgrade\TaskRunner\AbstractTask;
use PrestaShop\Module\AutoUpgrade\UpgradeContainer;

class BackupDb extends AbstractTask
{
    public function run()
    {
        if (!$this->container->getUpgradeConfiguration()->get('PS_AUTOUP_BACKUP')) {
            $this->stepDone = true;
            $this->container->getState()->setDbStep(0);
            $this->logger->info($this->translator->trans('Database backup skipped. Now upgrading files...', array(), 'Modules.Autoupgrade.Admin'));
            $this->next = 'upgradeFiles';

            return true;
        }

        $timeAllowed = $this->container->getUpgradeConfiguration()->getNumberOfFilesPerCall();
        $relative_backup_path = str_replace(_PS_ROOT_DIR_, '', $this->container->getProperty(UpgradeContainer::BACKUP_PATH));
        $report = '';
        if (!\ConfigurationTest::test_dir($relative_backup_path, false, $report)) {
            $this->logger->error($this->translator->trans('Backup directory is not writable (%path%).', array('%path%' => $this->container->getProperty(UpgradeContainer::BACKUP_PATH)), 'Modules.Autoupgrade.Admin'));
            $this->next = 'error';
            $this->error = true;

            return false;
        }

        $this->stepDone = false;
        $this->next = 'backupDb';
        $start_time = time();
        $time_elapsed = 0;

        $ignore_stats_table = array(_DB_PREFIX_ . 'connections',
            _DB_PREFIX_ . 'connections_page',
            _DB_PREFIX_ . 'connections_source',
            _DB_PREFIX_ . 'guest',
            _DB_PREFIX_ . 'statssearch', );

        // INIT LOOP
        if (!$this->container->getFileConfigurationStorage()->exists(UpgradeFileNames::DB_TABLES_TO_BACKUP_LIST)) {
            if (!is_dir($this->container->getProperty(UpgradeContainer::BACKUP_PATH) . DIRECTORY_SEPARATOR . $this->container->getState()->getBackupName())) {
                mkdir($this->container->getProperty(UpgradeContainer::BACKUP_PATH) . DIRECTORY_SEPARATOR . $this->container->getState()->getBackupName());
            }
            $this->container->getState()->setDbStep(0);
            $tablesToBackup = $this->container->getDb()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . '%"', true, false);
            $this->container->getFileConfigurationStorage()->save($tablesToBackup, UpgradeFileNames::DB_TABLES_TO_BACKUP_LIST);
        }

        if (!isset($tablesToBackup)) {
            $tablesToBackup = $this->container->getFileConfigurationStorage()->load(UpgradeFileNames::DB_TABLES_TO_BACKUP_LIST);
        }
        $found = 0;
        $views = '';
        $fp = false;
        $backupfile = null;

        // MAIN BACKUP LOOP //
        $written = 0;
        do {
            if (null !== $this->container->getState()->getBackupTable()) {
                // only insert (schema already done)
                $table = $this->container->getState()->getBackupTable();
                $lines = $this->container->getState()->getBackupLines();
            } else {
                if (count($tablesToBackup) == 0) {
                    break;
                }
                $table = current(array_shift($tablesToBackup));
                $this->container->getState()->setBackupLoopLimit(0);
            }

            // Skip tables which do not start with _DB_PREFIX_
            if (strlen($table) <= strlen(_DB_PREFIX_) || strncmp($table, _DB_PREFIX_, strlen(_DB_PREFIX_)) != 0) {
                continue;
            }

            // Ignore stat tables
            if (in_array($table, $ignore_stats_table)) {
                continue;
            }

            if ($written == 0 || $written > $this->container->getUpgradeConfiguration()->getMaxSizeToWritePerCall()) {
                // increment dbStep will increment filename each time here
                $this->container->getState()->setDbStep($this->container->getState()->getDbStep() + 1);
                // new file, new step
                $written = 0;
                if (is_resource($fp)) {
                    fclose($fp);
                }
                $backupfile = $this->container->getProperty(UpgradeContainer::BACKUP_PATH) . DIRECTORY_SEPARATOR . $this->container->getState()->getBackupName() . DIRECTORY_SEPARATOR . $this->container->getState()->getBackupDbFilename();
                $backupfile = preg_replace('#_XXXXXX_#', '_' . str_pad($this->container->getState()->getDbStep(), 6, '0', STR_PAD_LEFT) . '_', $backupfile);

                // start init file
                // Figure out what compression is available and open the file
                if (file_exists($backupfile)) {
                    $this->next = 'error';
                    $this->error = true;
                    $this->logger->error($this->translator->trans('Backup file %s already exists. Operation aborted.', array($backupfile), 'Modules.Autoupgrade.Admin'));
                }

                if (function_exists('bzopen')) {
                    $backupfile .= '.bz2';
                    $fp = bzopen($backupfile, 'w');
                } elseif (function_exists('gzopen')) {
                    $backupfile .= '.gz';
                    $fp = gzopen($backupfile, 'w');
                } else {
                    $fp = fopen($backupfile, 'w');
                }

                if ($fp === false) {
                    $this->logger->error($this->translator->trans('Unable to create backup database file %s.', array(addslashes($backupfile)), 'Modules.Autoupgrade.Admin'));
                    $this->next = 'error';
                    $this->error = true;
                    $this->logger->info($this->translator->trans('Error during database backup.', array(), 'Modules.Autoupgrade.Admin'));

                    return false;
                }

                $written += fwrite($fp, '/* Backup ' . $this->container->getState()->getDbStep() . ' for ' . Tools14::getHttpHost(false, false) . __PS_BASE_URI__ . "\n *  at " . date('r') . "\n */\n");
                $written += fwrite($fp, "\n" . 'SET SESSION sql_mode = \'\';' . "\n\n");
                $written += fwrite($fp, "\n" . 'SET NAMES \'utf8\';' . "\n\n");
                $written += fwrite($fp, "\n" . 'SET FOREIGN_KEY_CHECKS=0;' . "\n\n");
                // end init file
            }

            // start schema : drop & create table only
            if (null === $this->container->getState()->getBackupTable()) {
                // Export the table schema
                $schema = $this->container->getDb()->executeS('SHOW CREATE TABLE `' . $table . '`', true, false);

                if (count($schema) != 1 ||
                    !(isset($schema[0]['Table'], $schema[0]['Create Table'])
                        || isset($schema[0]['View'], $schema[0]['Create View']))) {
                    fclose($fp);
                    if (file_exists($backupfile)) {
                        unlink($backupfile);
                    }
                    $this->logger->error($this->translator->trans('An error occurred while backing up. Unable to obtain the schema of %s', array($table), 'Modules.Autoupgrade.Admin'));
                    $this->logger->info($this->translator->trans('Error during database backup.', array(), 'Modules.Autoupgrade.Admin'));
                    $this->next = 'error';
                    $this->error = true;

                    return false;
                }

                // case view
                if (isset($schema[0]['View'])) {
                    $views .= '/* Scheme for view' . $schema[0]['View'] . " */\n";
                    // If some *upgrade* transform a table in a view, drop both just in case
                    $views .= 'DROP VIEW IF EXISTS `' . $schema[0]['View'] . '`;' . "\n";
                    $views .= 'DROP TABLE IF EXISTS `' . $schema[0]['View'] . '`;' . "\n";
                    $views .= preg_replace('#DEFINER=[^\s]+\s#', 'DEFINER=CURRENT_USER ', $schema[0]['Create View']) . ";\n\n";
                    $written += fwrite($fp, "\n" . $views);
                    $ignore_stats_table[] = $schema[0]['View'];
                }
                // case table
                elseif (isset($schema[0]['Table'])) {
                    // Case common table
                    $written += fwrite($fp, '/* Scheme for table ' . $schema[0]['Table'] . " */\n");
                    if (!in_array($schema[0]['Table'], $ignore_stats_table)) {
                        // If some *upgrade* transform a table in a view, drop both just in case
                        $written += fwrite($fp, 'DROP VIEW IF EXISTS `' . $schema[0]['Table'] . '`;' . "\n");
                        $written += fwrite($fp, 'DROP TABLE IF EXISTS `' . $schema[0]['Table'] . '`;' . "\n");
                        // CREATE TABLE
                        $written += fwrite($fp, $schema[0]['Create Table'] . ";\n\n");
                    }
                    // schema created, now we need to create the missing vars
                    $this->container->getState()->setBackupTable($table);
                    $lines = explode("\n", $schema[0]['Create Table']);
                    $this->container->getState()->setBackupLines($lines);
                }
            }
            // end of schema

            // POPULATE TABLE
            if (!in_array($table, $ignore_stats_table)) {
                do {
                    $backup_loop_limit = $this->container->getState()->getBackupLoopLimit();
                    $data = $this->container->getDb()->executeS('SELECT * FROM `' . $table . '` LIMIT ' . (int) $backup_loop_limit . ',200', false, false);
                    $this->container->getState()->setBackupLoopLimit($this->container->getState()->getBackupLoopLimit() + 200);
                    $sizeof = $this->container->getDb()->numRows();
                    if ($data && ($sizeof > 0)) {
                        // Export the table data
                        $written += fwrite($fp, "INSERT INTO `" . $table . "` VALUES \n");
                        $i = 1;
                        while ($row = $this->container->getDb()->nextRow($data)) {
                            // this starts a row
                            $s = '(';
                            foreach ($row as $field => $value) {
                                if ($value === null) {
                                    $s .= 'NULL,';
                                } else {
                                    $s .= "'" . $this->container->getDb()->escape($value, true) . "',";
                                }
                            }
                            $s = rtrim($s, ',');

                            if ($i < $sizeof) {
                                $s .= "),\n";
                            } else {
                                $s .= ");\n";
                            }

                            $written += fwrite($fp, $s);
                            ++$i;
                        }
                        $time_elapsed = time() - $start_time;
                    } else {
                        $this->container->getState()->setBackupTable(null);
                        break;
                    }
                } while (($time_elapsed < $timeAllowed) && ($written < $this->container->getUpgradeConfiguration()->getMaxSizeToWritePerCall()));
            }
            ++$found;
            $time_elapsed = time() - $start_time;
            $this->logger->debug($this->translator->trans('%s table has been saved.', array($table), 'Modules.Autoupgrade.Admin'));
        } while (($time_elapsed < $timeAllowed) && ($written < $this->container->getUpgradeConfiguration()->getMaxSizeToWritePerCall()));

        // end of loop
        if (is_resource($fp)) {
            $written += fwrite($fp, "\n" . 'SET FOREIGN_KEY_CHECKS=1;' . "\n\n");
            fclose($fp);
            $fp = null;
        }

        $this->container->getFileConfigurationStorage()->save($tablesToBackup, UpgradeFileNames::DB_TABLES_TO_BACKUP_LIST);

        if (count($tablesToBackup) > 0) {
            $this->logger->debug($this->translator->trans('%s tables have been saved.', array($found), 'Modules.Autoupgrade.Admin'));
            $this->next = 'backupDb';
            $this->stepDone = false;
            if (count($tablesToBackup)) {
                $this->logger->info($this->translator->trans('Database backup: %s table(s) left...', array(count($tablesToBackup)), 'Modules.Autoupgrade.Admin'));
            }

            return true;
        }
        if ($found == 0 && !empty($backupfile)) {
            if (file_exists($backupfile)) {
                unlink($backupfile);
            }
            $this->logger->error($this->translator->trans('No valid tables were found to back up. Backup of file %s canceled.', array($backupfile), 'Modules.Autoupgrade.Admin'));
            $this->logger->info($this->translator->trans('Error during database backup for file %s.', array($backupfile), 'Modules.Autoupgrade.Admin'));
            $this->error = true;

            return false;
        } else {
            $this->container->getState()
                ->setBackupLoopLimit(null)
                ->setBackupLines(null)
                ->setBackupTable(null);
            if ($found) {
                $this->logger->info($this->translator->trans('%s tables have been saved.', array($found), 'Modules.Autoupgrade.Admin'));
            }
            $this->stepDone = true;
            // reset dbStep at the end of this step
            $this->container->getState()->setDbStep(0);

            $this->logger->info($this->translator->trans('Database backup done in filename %s. Now upgrading files...', array($this->container->getState()->getBackupName()), 'Modules.Autoupgrade.Admin'));
            $this->next = 'upgradeFiles';

            return true;
        }
    }
}
