<?php
/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PrestaShopBackupCore
{
    /** @var int Object id */
    public $id;
    /** @var string Last error messages */
    public $error;
/** @var string default backup directory. */
    public static $backupDir = '/backups/';
    /** @var string custom backup directory. */
    public $customBackupDir = null;

    public $psBackupAll = true;
    public $psBackupDropTable = true;

    /**
     * Creates a new backup object
     *
     * @param string $filename Filename of the backup file
     */
    public function __construct($filename = null)
    {
        if ($filename) {
            $this->id = $this->getRealBackupPath($filename);
        }

        $psBackupAll = Configuration::get('PS_BACKUP_ALL');
        $psBackupDropTable = Configuration::get('PS_BACKUP_DROP_TABLE');
        $this->psBackupAll = $psBackupAll !== false ? $psBackupAll : true;
        $this->psBackupDropTable = $psBackupDropTable !== false ? $psBackupDropTable : true;
    }

    /**
     * you can set a different path with that function
     *
     * @TODO include the prefix name
     * @param string $dir
     * @return bool bo
     */
    public function setCustomBackupPath($dir)
    {
        $custom_dir = DIRECTORY_SEPARATOR.trim($dir, '/').DIRECTORY_SEPARATOR;
        if (is_dir((defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_).$custom_dir)) {
            $this->customBackupDir = $custom_dir;
        } else {
            return false;
        }

        return true;
    }

    /**
     * get the path to use for backup (customBackupDir if specified, or default)
     *
     * @param string $filename filename to use
     * @return string full path
     */
    public function getRealBackupPath($filename = null)
    {
        $backupDir = PrestaShopBackup::getBackupPath($filename);
        if (!empty($this->customBackupDir)) {
            $backupDir = str_replace((defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_).self::$backupDir,
                (defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_).$this->customBackupDir, $backupDir);

            if (strrpos($backupDir, DIRECTORY_SEPARATOR)) {
                $backupDir .= DIRECTORY_SEPARATOR;
            }
        }
        return $backupDir;
    }

    /**
     * Get the full path of the backup file
     *
     * @param string $filename prefix of the backup file (datetime will be the second part)
     * @return string The full path of the backup file, or false if the backup file does not exists
     */
    public static function getBackupPath($filename = '')
    {
        $backupdir = realpath((defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_).self::$backupDir);

        if ($backupdir === false) {
            die(Tools::displayError('"Backup" directory does not exist.'));
        }

        // Check the realpath so we can validate the backup file is under the backup directory
        if (!empty($filename)) {
            $backupfile = realpath($backupdir.DIRECTORY_SEPARATOR.$filename);
        } else {
            $backupfile = $backupdir.DIRECTORY_SEPARATOR;
        }

        if ($backupfile === false || strncmp($backupdir, $backupfile, strlen($backupdir)) != 0) {
            die(Tools::displayError());
        }

        return $backupfile;
    }

    /**
     * Check if a backup file exist
     *
     * @param string $filename prefix of the backup file (datetime will be the second part)
     * @return bool true if backup file exist
     */
    public static function backupExist($filename)
    {
        $backupdir = realpath((defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_).self::$backupDir);

        if ($backupdir === false) {
            die(Tools::displayError('"Backup" directory does not exist.'));
        }

        return @filemtime($backupdir.DIRECTORY_SEPARATOR.$filename);
    }
    /**
     * Get the URL used to retrieve this backup file
     *
     * @return string The url used to request the backup file
     */
    public function getBackupURL()
    {
        return __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/backup.php?filename='.basename($this->id);
    }

    /**
     * Delete the current backup file
     *
     * @return bool Deletion result, true on success
     */
    public function delete()
    {
        if (!$this->id || !unlink($this->id)) {
            $this->error = Tools::displayError('Error deleting').' '.($this->id ? '"'.$this->id.'"' :
                Tools::displayError('Invalid ID'));
            return false;
        }
        return true;
    }

    /**
     * Deletes a range of backup files
     *
     * @return bool True on success
     */
    public function deleteSelection($list)
    {
        foreach ($list as $file) {
            $backup = new PrestaShopBackup($file);
            if (!$backup->delete()) {
                $this->error = $backup->error;
                return false;
            }
        }
        return true;
    }

    /**
     * Creates a new backup file
     *
     * @return bool true on successful backup
     */
    public function add()
    {
        if (!$this->psBackupAll) {
            $ignore_insert_table = array(_DB_PREFIX_.'connections', _DB_PREFIX_.'connections_page', _DB_PREFIX_
                .'connections_source', _DB_PREFIX_.'guest', _DB_PREFIX_.'statssearch');
        } else {
            $ignore_insert_table = array();
        }

        // Generate some random number, to make it extra hard to guess backup file names
        $rand = dechex(mt_rand(0, min(0xffffffff, mt_getrandmax())));
        $date = time();
        $backupfile = $this->getRealBackupPath().$date.'-'.$rand.'.sql';

        // Figure out what compression is available and open the file
        if (function_exists('bzopen')) {
            $backupfile .= '.bz2';
            $fp = @bzopen($backupfile, 'w');
        } elseif (function_exists('gzopen')) {
            $backupfile .= '.gz';
            $fp = @gzopen($backupfile, 'w');
        } else {
            $fp = @fopen($backupfile, 'w');
        }

        if ($fp === false) {
            echo Tools::displayError('Unable to create backup file').' "'.addslashes($backupfile).'"';
            return false;
        }

        $this->id = realpath($backupfile);

        fwrite($fp, '/* Backup for '.Tools::getHttpHost(false, false).__PS_BASE_URI__."\n *  at ".date($date)."\n */\n");
        fwrite($fp, "\n".'SET NAMES \'utf8\';'."\n\n");

        // Find all tables
        $tables = Db::getInstance()->executeS('SHOW TABLES');
        $found = 0;
        foreach ($tables as $table) {
            $table = current($table);

            // Skip tables which do not start with _DB_PREFIX_
            if (strlen($table) < strlen(_DB_PREFIX_) || strncmp($table, _DB_PREFIX_, strlen(_DB_PREFIX_)) != 0) {
                continue;
            }

            // Export the table schema
            $schema = Db::getInstance()->executeS('SHOW CREATE TABLE `'.$table.'`');

            if (count($schema) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table'])) {
                fclose($fp);
                $this->delete();
                echo Tools::displayError('An error occurred while backing up. Unable to obtain the schema of').' "'.$table;
                return false;
            }

            fwrite($fp, '/* Scheme for table '.$schema[0]['Table']." */\n");

            if ($this->psBackupDropTable) {
                fwrite($fp, 'DROP TABLE IF EXISTS `'.$schema[0]['Table'].'`;'."\n");
            }

            fwrite($fp, $schema[0]['Create Table'].";\n\n");

            if (!in_array($schema[0]['Table'], $ignore_insert_table)) {
                $data = Db::getInstance()->query('SELECT * FROM `'.$schema[0]['Table'].'`', false);
                $sizeof = DB::getInstance()->NumRows();
                $lines = explode("\n", $schema[0]['Create Table']);

                if ($data && $sizeof > 0) {
                    // Export the table data
                    fwrite($fp, 'INSERT INTO `'.$schema[0]['Table']."` VALUES\n");
                    $i = 1;
                    while ($row = DB::getInstance()->nextRow($data)) {
                        $s = '(';

                        foreach ($row as $field => $value) {
                            $tmp = "'".pSQL($value, true)."',";
                            if ($tmp != "'',") {
                                $s .= $tmp;
                            } else {
                                foreach ($lines as $line) {
                                    if (strpos($line, '`'.$field.'`') !== false) {
                                        if (preg_match('/(.*NOT NULL.*)/Ui', $line)) {
                                            $s .= "'',";
                                        } else {
                                            $s .= 'NULL,';
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                        $s = rtrim($s, ',');

                        if ($i % 200 == 0 && $i < $sizeof) {
                            $s .= ");\nINSERT INTO `".$schema[0]['Table']."` VALUES\n";
                        } elseif ($i < $sizeof) {
                            $s .= "),\n";
                        } else {
                            $s .= ");\n";
                        }

                        fwrite($fp, $s);
                        ++$i;
                    }
                }
            }
            $found++;
        }

        fclose($fp);
        if ($found == 0) {
            $this->delete();
            echo Tools::displayError('No valid tables were found to backup.');
            return false;
        }

        return true;
    }
}
