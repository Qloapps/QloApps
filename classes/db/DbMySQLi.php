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

/**
 * Class DbMySQLiCore
 *
 * @since 1.5.0,1
 */
class DbMySQLiCore extends Db
{
    /** @var mysqli */
    protected $link;

    /* @var mysqli_result */
    protected $result;

    /**
     * Tries to connect to the database
     *
     * @see DbCore::connect()
     * @return mysqli
     * @throws PrestaShopDatabaseException
     */
    public function connect()
    {
        $socket = false;
        $port = false;
        if (Tools::strpos($this->server, ':') !== false) {
            list($server, $port) = explode(':', $this->server);
            if (is_numeric($port) === false) {
                $socket = $port;
                $port = false;
            }
        } elseif (Tools::strpos($this->server, '/') !== false) {
            $socket = $this->server;
        }

        if ($socket) {
            $this->link = @new mysqli(null, $this->user, $this->password, $this->database, null, $socket);
        } elseif ($port) {
            $this->link = @new mysqli($server, $this->user, $this->password, $this->database, $port);
        } else {
            $this->link = @new mysqli($this->server, $this->user, $this->password, $this->database);
        }

        // Do not use object way for error because this work bad before PHP 5.2.9
        if (mysqli_connect_error()) {
            throw new PrestaShopDatabaseException(sprintf(Tools::displayError('Link to database cannot be established: %s'), mysqli_connect_error()));
        }

        // UTF-8 support
        if (!$this->link->query('SET NAMES \'utf8\'')) {
            throw new PrestaShopDatabaseException(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));
        }

        return $this->link;
    }

    /**
     * Tries to connect and create a new database
     *
     * @param string $host
     * @param string|null $user
     * @param string|null $password
     * @param string|null $database
     * @param bool $dropit If true, drops the created database.
     * @return bool|mysqli_result
     */
    public static function createDatabase($host, $user = null, $password = null, $database = null, $dropit = false)
    {
        if (strpos($host, ':') !== false) {
            list($host, $port) = explode(':', $host);
            $link = @new mysqli($host, $user, $password, null, $port);
        } else {
            $link = @new mysqli($host, $user, $password);
        }
        $success = $link->query('CREATE DATABASE `'.str_replace('`', '\\`', $database).'`');
        if ($dropit && ($link->query('DROP DATABASE `'.str_replace('`', '\\`', $database).'`') !== false)) {
            return true;
        }
        return $success;
    }

    /**
     * Destroys the database connection link
     *
     * @see DbCore::disconnect()
     */
    public function disconnect()
    {
        @$this->link->close();
    }

    /**
     * Executes an SQL statement, returning a result set as a mysqli_result object or true/false.
     *
     * @see DbCore::_query()
     * @param string $sql
     * @return bool|mysqli_result
     */
    protected function _query($sql)
    {
        return $this->link->query($sql);
    }

    /**
     * Returns the next row from the result set.
     *
     * @see DbCore::nextRow()
     * @param bool|mysqli_result $result
     * @return array|bool
     */
    public function nextRow($result = false)
    {
        if (!$result) {
            $result = $this->result;
        }

        if (!is_object($result)) {
            return false;
        }

        return $result->fetch_assoc();
    }

    /**
     * Returns all rows from the result set.
     *
     * @see DbCore::getAll()
     * @param bool|mysqli_result $result
     * @return array|false
     */
    protected function getAll($result = false)
    {
        if (!$result) {
            $result = $this->result;
        }

        if (!is_object($result)) {
            return false;
        }

        if (method_exists($result, 'fetch_all')) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $ret = array();

            while ($row = $this->nextRow($result)) {
                $ret[] = $row;
            }

            return $ret;
        }
    }

    /**
     * Returns row count from the result set.
     *
     * @see DbCore::_numRows()
     * @param bool|mysqli_result $result
     * @return int
     */
    protected function _numRows($result)
    {
        return $result->num_rows;
    }

    /**
     * Returns ID of the last inserted row.
     *
     * @see DbCore::Insert_ID()
     * @return string|int
     */
    public function Insert_ID()
    {
        return $this->link->insert_id;
    }

    /**
     * Return the number of rows affected by the last SQL query.
     *
     * @see DbCore::Affected_Rows()
     * @return int
     */
    public function Affected_Rows()
    {
        return $this->link->affected_rows;
    }

    /**
     * Returns error message.
     *
     * @see DbCore::getMsgError()
     * @param bool $query
     * @return string
     */
    public function getMsgError($query = false)
    {
        return $this->link->error;
    }

    /**
     * Returns error code.
     *
     * @see DbCore::getNumberError()
     * @return int
     */
    public function getNumberError()
    {
        return $this->link->errno;
    }

    /**
     * Returns database server version.
     *
     * @see DbCore::getVersion()
     * @return string
     */
    public function getVersion()
    {
        return $this->getValue('SELECT VERSION()');
    }

    /**
     * Escapes illegal characters in a string.
     *
     * @see DbCore::_escape()
     * @param string $str
     * @return string
     */
    public function _escape($str)
    {
        return $this->link->real_escape_string($str);
    }

    /**
     * Switches to a different database.
     *
     * @see DbCore::set_db()
     * @param string $db_name
     * @return bool
     */
    public function set_db($db_name)
    {
        return $this->link->query('USE `'.bqSQL($db_name).'`');
    }

    /**
     * Try a connection to the database and check if at least one table with same prefix exists
     *
     * @see Db::hasTableWithSamePrefix()
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @param string $db Database name
     * @param string $prefix Tables prefix
     * @return bool
     */
    public static function hasTableWithSamePrefix($server, $user, $pwd, $db, $prefix)
    {
        $link = @new mysqli($server, $user, $pwd, $db);
        if (mysqli_connect_error()) {
            return false;
        }

        $sql = 'SHOW TABLES LIKE \''.$prefix.'%\'';
        $result = $link->query($sql);
        return (bool)$result->fetch_assoc();
    }

    /**
     * Try a connection to the database
     *
     * @see Db::checkConnection()
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @param string $db Database name
     * @param bool $newDbLink
     * @param string|bool $engine
     * @param int $timeout
     * @return int Error code or 0 if connection was successful
     */
    public static function tryToConnect($server, $user, $pwd, $db, $new_db_link = true, $engine = null, $timeout = 5)
    {
        $link = mysqli_init();
        if (!$link) {
            return -1;
        }

        if (!$link->options(MYSQLI_OPT_CONNECT_TIMEOUT, $timeout)) {
            return 1;
        }

        // There is an @ because mysqli throw a warning when the database does not exists
        if (!@$link->real_connect($server, $user, $pwd, $db)) {
            return (mysqli_connect_errno() == 1049) ? 2 : 1;
        }

        $link->close();
        return 0;
    }

    /**
     * Selects best table engine.
     *
     * @return string
     */
    public function getBestEngine()
    {
        $value = 'InnoDB';

        $sql = 'SHOW VARIABLES WHERE Variable_name = \'have_innodb\'';
        $result = $this->link->query($sql);
        if (!$result) {
            $value = 'MyISAM';
        }
        $row = $result->fetch_assoc();
        if (!$row || strtolower($row['Value']) != 'yes') {
            $value = 'MyISAM';
        }

        /* MySQL >= 5.6 */
        $sql = 'SHOW ENGINES';
        $result = $this->link->query($sql);
        while ($row = $result->fetch_assoc()) {
            if ($row['Engine'] == 'InnoDB') {
                if (in_array($row['Support'], array('DEFAULT', 'YES'))) {
                    $value = 'InnoDB';
                }
                break;
            }
        }

        return $value;
    }

    /**
     * Tries to connect to the database and create a table (checking creation privileges)
     *
     * @param string $server
     * @param string $user
     * @param string $pwd
     * @param string $db
     * @param string $prefix
     * @param string|null $engine Table engine
     * @return bool|string True, false or error
     */
    public static function checkCreatePrivilege($server, $user, $pwd, $db, $prefix, $engine = null)
    {
        $link = @new mysqli($server, $user, $pwd, $db);
        if (mysqli_connect_error()) {
            return false;
        }

        if ($engine === null) {
            $engine = 'MyISAM';
        }

        $result = $link->query('
		CREATE TABLE `'.$prefix.'test` (
			`test` tinyint(1) unsigned NOT NULL
		) ENGINE='.$engine);

        if (!$result) {
            return $link->error;
        }

        $link->query('DROP TABLE `'.$prefix.'test`');
        return true;
    }

    /**
     * Try a connection to the database and set names to UTF-8
     *
     * @see Db::checkEncoding()
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @return bool
     */
    public static function tryUTF8($server, $user, $pwd)
    {
        $link = @new mysqli($server, $user, $pwd);
        $ret = $link->query("SET NAMES 'UTF8'");
        $link->close();
        return $ret;
    }

    /**
     * Checks if auto increment value and offset is 1
     *
     * @param string $server
     * @param string $user
     * @param string $pwd
     * @return bool
     */
    public static function checkAutoIncrement($server, $user, $pwd)
    {
        $link = @new mysqli($server, $user, $pwd);
        $ret = (bool)(($result = $link->query('SELECT @@auto_increment_increment as aii')) && ($row = $result->fetch_assoc()) && $row['aii'] == 1);
        $ret &= (bool)(($result = $link->query('SELECT @@auto_increment_offset as aio')) && ($row = $result->fetch_assoc()) && $row['aio'] == 1);
        $link->close();
        return $ret;
    }
}
