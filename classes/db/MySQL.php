<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Class MySQLCore
 */
class MySQLCore extends Db
{
    /** @var resource */
    protected $link;

    /* @var resource */
    protected $result;

    /**
     * Tries to connect to the database
     *
     * @see DbCore::connect()
     * @return resource
     * @throws PrestaShopDatabaseException
     */
    public function connect()
    {
        if (!defined('_PS_MYSQL_REAL_ESCAPE_STRING_')) {
            define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));
        }

        if (!$this->link = mysql_connect($this->server, $this->user, $this->password)) {
            throw new PrestaShopDatabaseException(Tools::displayError('Link to database cannot be established.'));
        }

        if (!$this->set_db($this->database)) {
            throw new PrestaShopDatabaseException(Tools::displayError('The database selection cannot be made.'));
        }

        // UTF-8 support
        if (!mysql_query('SET NAMES \'utf8\'', $this->link)) {
            throw new PrestaShopDatabaseException(Tools::displayError('PrestaShop Fatal error: no utf-8 support. Please check your server configuration.'));
        }

        return $this->link;
    }

    /**
     * Tries to connect and create a new database
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @param bool $dropit If true, drops the created database.
     * @return bool|resource
     */
    public static function createDatabase($host, $user, $password, $dbname, $dropit = false)
    {
        $link = mysql_connect($host, $user, $password);
        $success = mysql_query('CREATE DATABASE `'.str_replace('`', '\\`', $dbname).'`', $link);
        if ($dropit && (mysql_query('DROP DATABASE `'.str_replace('`', '\\`', $dbname).'`', $link) !== false)) {
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
        mysql_close($this->link);
    }

    /**
     * Executes an SQL statement, returning a result set as a result resource object.
     *
     * @see DbCore::_query()
     * @param string $sql
     * @return resource
     */
    protected function _query($sql)
    {
        return mysql_query($sql, $this->link);
    }

    /**
     * Returns the next row from the result set.
     *
     * @see DbCore::nextRow()
     * @param bool|resource $result
     * @return array|bool
     */
    public function nextRow($result = false)
    {
        $return = false;
        if (is_resource($result) && $result) {
            $return = mysql_fetch_assoc($result);
        } elseif (is_resource($this->_result) && $this->_result) {
            $return = mysql_fetch_assoc($this->_result);
        }

        return $return;
    }

    /**
     * Returns the next row from the result set.
     *
     * @see DbCore::_numRows()
     * @param resource $result
     * @return int
     */
    protected function _numRows($result)
    {
        return mysql_num_rows($result);
    }

    /**
     * Returns ID of the last inserted row.
     *
     * @see DbCore::Insert_ID()
     * @return int
     */
    public function Insert_ID()
    {
        return mysql_insert_id($this->link);
    }

    /**
     * Return the number of rows affected by the last SQL query.
     *
     * @see DbCore::Affected_Rows()
     * @return int
     */
    public function Affected_Rows()
    {
        return mysql_affected_rows($this->link);
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
        return mysql_error($this->link);
    }

    /**
     * Returns error code.
     *
     * @see DbCore::getNumberError()
     * @return int
     */
    public function getNumberError()
    {
        return mysql_errno($this->link);
    }

    /**
     * Returns database server version.
     *
     * @see DbCore::getVersion()
     * @return string
     */
    public function getVersion()
    {
        return mysql_get_server_info($this->link);
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
        return _PS_MYSQL_REAL_ESCAPE_STRING_ ? mysql_real_escape_string($str, $this->link) : addslashes($str);
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
        return mysql_select_db($db_name, $this->link);
    }

    /**
     * Returns all rows from the result set.
     *
     * @see DbCore::getAll()
     * @param bool|resource $result
     * @return array
     */
    protected function getAll($result = false)
    {
        if (!$result) {
            $result = $this->result;
        }

        $data = array();
        while ($row = $this->nextRow($result)) {
            $data[] = $row;
        }

        return $data;
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
        if (!$link = @mysql_connect($server, $user, $pwd, true)) {
            return false;
        }
        if (!@mysql_select_db($db, $link)) {
            return false;
        }

        $sql = 'SHOW TABLES LIKE \''.$prefix.'%\'';
        $result = mysql_query($sql);
        return (bool)@mysql_fetch_assoc($result);
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
     * @param string|null $engine
     * @param int $timeout
     * @return int Error code or 0 if connection was successful
     */
    public static function tryToConnect($server, $user, $pwd, $db, $new_db_link = true, $engine = null, $timeout = 5)
    {
        ini_set('mysql.connect_timeout', $timeout);
        if (!$link = @mysql_connect($server, $user, $pwd, $new_db_link)) {
            return 1;
        }
        if (!@mysql_select_db($db, $link)) {
            return 2;
        }
        @mysql_close($link);

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
        $result = mysql_query($sql);
        if (!$result) {
            $value = 'MyISAM';
        }
        $row = mysql_fetch_assoc($result);
        if (!$row || strtolower($row['Value']) != 'yes') {
            $value = 'MyISAM';
        }

        /* MySQL >= 5.6 */
        $sql = 'SHOW ENGINES';
        $result = mysql_query($sql);
        while ($row = mysql_fetch_assoc($result)) {
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
        ini_set('mysql.connect_timeout', 5);
        if (!$link = @mysql_connect($server, $user, $pwd, true)) {
            return false;
        }
        if (!@mysql_select_db($db, $link)) {
            return false;
        }

        if ($engine === null) {
            $engine = 'MyISAM';
        }

        $result = mysql_query('
		CREATE TABLE `'.$prefix.'test` (
			`test` tinyint(1) unsigned NOT NULL
		) ENGINE='.$engine, $link);

        if (!$result) {
            return mysql_error($link);
        }

        mysql_query('DROP TABLE `'.$prefix.'test`', $link);
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
        $link = @mysql_connect($server, $user, $pwd);
        $ret = mysql_query('SET NAMES \'utf8\'', $link);
        @mysql_close($link);
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
        $link = @mysql_connect($server, $user, $pwd);
        $ret = (bool)(($result = mysql_query('SELECT @@auto_increment_increment as aii', $link)) && ($row = mysql_fetch_assoc($result)) && $row['aii'] == 1);
        $ret &= (bool)(($result = mysql_query('SELECT @@auto_increment_offset as aio', $link)) && ($row = mysql_fetch_assoc($result)) && $row['aio'] == 1);
        @mysql_close($link);
        return $ret;
    }
}
