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

if (file_exists(_PS_ROOT_DIR_.'/config/settings.inc.php')) {
    include_once(_PS_ROOT_DIR_.'/config/settings.inc.php');
}

/**
 * Class DbCore
 */
abstract class DbCore
{
    /** @var int Constant used by insert() method */
    const INSERT = 1;

    /** @var int Constant used by insert() method */
    const INSERT_IGNORE = 2;

    /** @var int Constant used by insert() method */
    const REPLACE = 3;

    /** @var int Constant used by insert() method */
    const ON_DUPLICATE_KEY = 4;

    /** @var string Server (eg. localhost) */
    protected $server;

    /**  @var string Database user (eg. root) */
    protected $user;

    /** @var string Database password (eg. can be empty !) */
    protected $password;

    /** @var string Database name */
    protected $database;

    /** @var bool */
    protected $is_cache_enabled;

    /** @var PDO|mysqli|resource Resource link */
    protected $link;

    /** @var PDOStatement|mysqli_result|resource|bool SQL cached result */
    protected $result;

    /** @var array List of DB instances */
    public static $instance = array();

    /** @var array List of server settings */
    public static $_servers = array();

    /** @var null Flag used to load slave servers only once.
     * See loadSlaveServers() method.
     */
    public static $_slave_servers_loaded = null;

    /**
     * Store last executed query
     *
     * @var string
     */
    protected $last_query;

    /**
     * Store hash of the last executed query
     *
     * @var string
     */
    protected $last_query_hash;

    /**
     * Last cached query
     *
     * @var string
     */
    protected $last_cached;

    /**
     * Opens a database connection
     *
     * @return PDO|mysqli|resource
     */
    abstract public function connect();

    /**
     * Closes database connection
     */
    abstract public function disconnect();

    /**
     * Execute a query and get result resource
     *
     * @param string $sql
     * @return PDOStatement|mysqli_result|resource|bool
     */
    abstract protected function _query($sql);

    /**
     * Get number of rows in a result
     *
     * @param mixed $result
     * @return int
     */
    abstract protected function _numRows($result);

    /**
     * Get the ID generated from the previous INSERT operation
     *
     * @return int|string
     */
    abstract public function Insert_ID();

    /**
     * Get number of affected rows in previous database operation
     *
     * @return int
     */
    abstract public function Affected_Rows();

    /**
     * Get next row for a query which does not return an array
     *
     * @param PDOStatement|mysqli_result|resource|bool $result
     * @return array|object|false|null
     */
    abstract public function nextRow($result = false);

    /**
     * Get all rows for a query which return an array
     *
     * @param PDOStatement|mysqli_result|resource|bool|null $result
     * @return array
     */
    abstract protected function getAll($result = false);

    /**
     * Get database version
     *
     * @return string
     */
    abstract public function getVersion();

    /**
     * Protect string against SQL injections
     *
     * @param string $str
     * @return string
     */
    abstract public function _escape($str);

    /**
     * Returns the text of the error message from previous database operation
     *
     * @return string
     */
    abstract public function getMsgError();

    /**
     * Returns the number of the error from previous database operation
     *
     * @return int
     */
    abstract public function getNumberError();

    /**
     * Sets the current active database on the server that's associated with the specified link identifier.
     * Do not remove, useful for some modules.
     *
     * @param string $db_name
     * @return bool|int
     */
    abstract public function set_db($db_name);

    /**
     * Selects best table engine.
     *
     * @return string
     */
    abstract public function getBestEngine();

    /**
     * Returns database object instance.
     *
     * @param bool $master Decides whether the connection to be returned by the master server or the slave server
     * @return Db Singleton instance of Db object
     */
    public static function getInstance($master = true)
    {
        static $id = 0;

        // This MUST not be declared with the class members because some defines (like _DB_SERVER_) may not exist yet (the constructor can be called directly with params)
        if (!self::$_servers) {
            self::$_servers = array(
                array('server' => _DB_SERVER_, 'user' => _DB_USER_, 'password' => _DB_PASSWD_, 'database' => _DB_NAME_), /* MySQL Master server */
            );
        }

        if (!$master) {
            Db::loadSlaveServers();
        }

        $total_servers = count(self::$_servers);
        if ($master || $total_servers == 1) {
            $id_server = 0;
        } else {
            $id++;
            $id_server = ($total_servers > 2 && ($id % $total_servers) != 0) ? $id % $total_servers : 1;
        }

        if (!isset(self::$instance[$id_server])) {
            $class = Db::getClass();
            self::$instance[$id_server] = new $class(
                self::$_servers[$id_server]['server'],
                self::$_servers[$id_server]['user'],
                self::$_servers[$id_server]['password'],
                self::$_servers[$id_server]['database']
            );
        }

        return self::$instance[$id_server];
    }

    /**
     * @param $test_db Db
     * Unit testing purpose only
     */
    public static function setInstanceForTesting($test_db)
    {
        self::$instance[0] = $test_db;
    }

    /**
     * Unit testing purpose only
     */
    public static function deleteTestingInstance()
    {
        self::$instance = array();
    }

    /**
     * Loads configuration settings for slave servers if needed.
     */
    protected static function loadSlaveServers()
    {
        if (self::$_slave_servers_loaded !== null) {
            return;
        }

        // Add here your slave(s) server(s) in this file
        if (file_exists(_PS_ROOT_DIR_.'/config/db_slave_server.inc.php')) {
            self::$_servers = array_merge(self::$_servers, require(_PS_ROOT_DIR_.'/config/db_slave_server.inc.php'));
        }

        self::$_slave_servers_loaded = true;
    }

    /**
     * Returns the best child layer database class.
     *
     * @return string
     */
    public static function getClass()
    {
        $class = '';
        if (PHP_VERSION_ID >= 50200 && extension_loaded('pdo_mysql')) {
            $class = 'DbPDO';
        } elseif (extension_loaded('mysqli')) {
            $class = 'DbMySQLi';
        }

        if (empty($class)) {
            throw new PrestaShopException('Cannot select any valid SQL engine.');
        }

        return $class;
    }

    /**
     * Instantiates a database connection
     *
     * @param string $server Server address
     * @param string $user User login
     * @param string $password User password
     * @param string $database Database name
     * @param bool $connect If false, don't connect in constructor (since 1.5.0.1)
     */
    public function __construct($server, $user, $password, $database, $connect = true)
    {
        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->is_cache_enabled = (defined('_PS_CACHE_ENABLED_')) ? _PS_CACHE_ENABLED_ : false;

        if (!defined('_PS_DEBUG_SQL_')) {
            define('_PS_DEBUG_SQL_', false);
        }

        if ($connect) {
            $this->connect();
        }
    }

    /**
     * Disable the use of the cache
     *
     */
    public function disableCache()
    {
        $this->is_cache_enabled = false;
    }

    /**
     * Enable & flush the cache
     *
     */
    public function enableCache()
    {
        $this->is_cache_enabled = true;
        Cache::getInstance()->flush();
    }

    /**
     * Closes connection to database
     */
    public function __destruct()
    {
        if ($this->link) {
            $this->disconnect();
        }
    }

    /**
     * Executes SQL query based on selected type
     *
     * @deprecated 1.5.0.1 Use insert() or update() method instead.
     * @param string $table
     * @param array $data
     * @param string $type (INSERT, INSERT IGNORE, REPLACE, UPDATE).
     * @param string $where
     * @param int $limit
     * @param bool $use_cache
     * @param bool $use_null
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function autoExecute($table, $data, $type, $where = '', $limit = 0, $use_cache = true, $use_null = false)
    {
        $type = strtoupper($type);
        switch ($type) {
            case 'INSERT' :
                return $this->insert($table, $data, $use_null, $use_cache, Db::INSERT, false);

            case 'INSERT IGNORE' :
                return $this->insert($table, $data, $use_null, $use_cache, Db::INSERT_IGNORE, false);

            case 'REPLACE' :
                return $this->insert($table, $data, $use_null, $use_cache, Db::REPLACE, false);

            case 'UPDATE' :
                return $this->update($table, $data, $where, $limit, $use_null, $use_cache, false);

            default :
                throw new PrestaShopDatabaseException('Wrong argument (miss type) in Db::autoExecute()');
        }
    }

    /**
     * Filter SQL query within a blacklist
     *
     * @param string $table Table where insert/update data
     * @param array $values Data to insert/update
     * @param string $type INSERT or UPDATE
     * @param string $where WHERE clause, only for UPDATE (optional)
     * @param int $limit LIMIT clause (optional)
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function autoExecuteWithNullValues($table, $values, $type, $where = '', $limit = 0)
    {
        return $this->autoExecute($table, $values, $type, $where, $limit, 0, true);
    }

    /**
     * Execute a query and get result resource
     *
     * @param string|DbQuery $sql
     * @return bool|mysqli_result|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public function query($sql)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        $this->result = $this->_query($sql);

        if (!$this->result && $this->getNumberError() == 2006) {
            if ($this->connect()) {
                $this->result = $this->_query($sql);
            }
        }

        if (_PS_DEBUG_SQL_) {
            $this->displayError($sql);
        }

        return $this->result;
    }

    /**
     * Executes an INSERT query
     *
     * @param string $table Table name without prefix
     * @param array $data Data to insert as associative array. If $data is a list of arrays, multiple insert will be done
     * @param bool $null_values If we want to use NULL values instead of empty quotes
     * @param bool $use_cache
     * @param int $type Must be Db::INSERT or Db::INSERT_IGNORE or Db::REPLACE
     * @param bool $add_prefix Add or not _DB_PREFIX_ before table name
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public function insert($table, $data, $null_values = false, $use_cache = true, $type = Db::INSERT, $add_prefix = true)
    {
        if (!$data && !$null_values) {
            return true;
        }

        if ($add_prefix) {
            $table = _DB_PREFIX_.$table;
        }

        if ($type == Db::INSERT) {
            $insert_keyword = 'INSERT';
        } elseif ($type == Db::INSERT_IGNORE) {
            $insert_keyword = 'INSERT IGNORE';
        } elseif ($type == Db::REPLACE) {
            $insert_keyword = 'REPLACE';
        } elseif ($type == Db::ON_DUPLICATE_KEY) {
            $insert_keyword = 'INSERT';
        } else {
            throw new PrestaShopDatabaseException('Bad keyword, must be Db::INSERT or Db::INSERT_IGNORE or Db::REPLACE');
        }

        // Check if $data is a list of row
        $current = current($data);
        if (!is_array($current) || isset($current['type'])) {
            $data = array($data);
        }

        $keys = array();
        $values_stringified = array();
        $first_loop = true;
        $duplicate_key_stringified = '';
        foreach ($data as $row_data) {
            $values = array();
            foreach ($row_data as $key => $value) {
                if (!$first_loop) {
                    // Check if row array mapping are the same
                    if (!in_array("`$key`", $keys)) {
                        throw new PrestaShopDatabaseException('Keys form $data subarray don\'t match');
                    }

                    if ($duplicate_key_stringified != '') {
                        throw new PrestaShopDatabaseException('On duplicate key cannot be used on insert with more than 1 VALUE group');
                    }
                } else {
                    $keys[] = '`'.bqSQL($key).'`';
                }

                if (!is_array($value)) {
                    $value = array('type' => 'text', 'value' => $value);
                }
                if ($value['type'] == 'sql') {
                    $values[] = $string_value = $value['value'];
                } else {
                    $values[] = $string_value = $null_values && ($value['value'] === '' || is_null($value['value'])) ? 'NULL' : "'{$value['value']}'";
                }

                if ($type == Db::ON_DUPLICATE_KEY) {
                    $duplicate_key_stringified .= '`'.bqSQL($key).'` = '.$string_value.',';
                }
            }
            $first_loop = false;
            $values_stringified[] = '('.implode(', ', $values).')';
        }
        $keys_stringified = implode(', ', $keys);

        $sql = $insert_keyword.' INTO `'.$table.'` ('.$keys_stringified.') VALUES '.implode(', ', $values_stringified);
        if ($type == Db::ON_DUPLICATE_KEY) {
            $sql .= ' ON DUPLICATE KEY UPDATE '.substr($duplicate_key_stringified, 0, -1);
        }

        return (bool)$this->q($sql, $use_cache);
    }

    /**
     * Executes an UPDATE query
     *
     * @param string $table Table name without prefix
     * @param array $data Data to insert as associative array. If $data is a list of arrays, multiple insert will be done
     * @param string $where WHERE condition
     * @param int $limit
     * @param bool $null_values If we want to use NULL values instead of empty quotes
     * @param bool $use_cache
     * @param bool $add_prefix Add or not _DB_PREFIX_ before table name
     * @return bool
     */
    public function update($table, $data, $where = '', $limit = 0, $null_values = false, $use_cache = true, $add_prefix = true)
    {
        if (!$data) {
            return true;
        }

        if ($add_prefix) {
            $table = _DB_PREFIX_.$table;
        }

        $sql = 'UPDATE `'.bqSQL($table).'` SET ';
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $value = array('type' => 'text', 'value' => $value);
            }
            if ($value['type'] == 'sql') {
                $sql .= '`'.bqSQL($key)."` = {$value['value']},";
            } else {
                $sql .= ($null_values && ($value['value'] === '' || is_null($value['value']))) ? '`'.bqSQL($key).'` = NULL,' : '`'.bqSQL($key)."` = '{$value['value']}',";
            }
        }

        $sql = rtrim($sql, ',');
        if ($where) {
            $sql .= ' WHERE '.$where;
        }
        if ($limit) {
            $sql .= ' LIMIT '.(int)$limit;
        }

        return (bool)$this->q($sql, $use_cache);
    }

    /**
     * Executes a DELETE query
     *
     * @param string $table Name of the table to delete
     * @param string $where WHERE clause on query
     * @param int $limit Number max of rows to delete
     * @param bool $use_cache Use cache or not
     * @param bool $add_prefix Add or not _DB_PREFIX_ before table name
     * @return bool
     */
    public function delete($table, $where = '', $limit = 0, $use_cache = true, $add_prefix = true)
    {
        if ($add_prefix) {
            $table = _DB_PREFIX_.$table;
        }

        $this->result = false;
        $sql = 'DELETE FROM `'.bqSQL($table).'`'.($where ? ' WHERE '.$where : '').($limit ? ' LIMIT '.(int)$limit : '');
        $res = $this->query($sql);
        if ($use_cache && $this->is_cache_enabled) {
            Cache::getInstance()->deleteQuery($sql);
        }

        return (bool)$res;
    }

    /**
     * Executes a query
     *
     * @param string|DbQuery $sql
     * @param bool $use_cache
     * @return bool
     */
    public function execute($sql, $use_cache = true)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        $this->result = $this->query($sql);
        if ($use_cache && $this->is_cache_enabled) {
            Cache::getInstance()->deleteQuery($sql);
        }

        return (bool)$this->result;
    }

    /**
     * Executes return the result of $sql as array
     *
     * @param string|DbQuery $sql Query to execute
     * @param bool $array Return an array instead of a result object (deprecated since 1.5.0.1, use query method instead)
     * @param bool $use_cache
     * @return array|false|null|mysqli_result|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public function executeS($sql, $array = true, $use_cache = true)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        $this->result = false;
        $this->last_query = $sql;

        if ($use_cache && $this->is_cache_enabled && $array) {
            $this->last_query_hash = Cache::getInstance()->getQueryHash($sql);
            if (($result = Cache::getInstance()->get($this->last_query_hash)) !== false) {
                Cache::getInstance()->incrementQueryCounter($sql);
                $this->last_cached = true;
                return $result;
            }
        }

        // This method must be used only with queries which display results
        if (!preg_match('#^\s*\(?\s*(select|show|explain|describe|desc)\s#i', $sql)) {
            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                throw new PrestaShopDatabaseException('Db->executeS() must be used only with select, show, explain or describe queries');
            }
            return $this->execute($sql, $use_cache);
        }

        $this->result = $this->query($sql);

        if (!$this->result) {
            $result = false;
        } else {
            if (!$array) {
                $use_cache = false;
                $result = $this->result;
            } else {
                $result = $this->getAll($this->result);
            }
        }

        $this->last_cached = false;
        if ($use_cache && $this->is_cache_enabled && $array) {
            Cache::getInstance()->setQuery($sql, $result);
        }

        return $result;
    }

    /**
     * Returns an associative array containing the first row of the query
     * This function automatically adds "LIMIT 1" to the query
     *
     * @param string|DbQuery $sql the select query (without "LIMIT 1")
     * @param bool $use_cache Find it in cache first
     * @return array|bool|object|null
     */
    public function getRow($sql, $use_cache = true)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        $sql = rtrim($sql, " \t\n\r\0\x0B;").' LIMIT 1';
        $this->result = false;
        $this->last_query = $sql;

        if ($use_cache && $this->is_cache_enabled) {
            $this->last_query_hash = Cache::getInstance()->getQueryHash($sql);
            if (($result = Cache::getInstance()->get($this->last_query_hash)) !== false) {
                Cache::getInstance()->incrementQueryCounter($sql);
                $this->last_cached = true;
                return $result;
            }
        }

        $this->result = $this->query($sql);
        if (!$this->result) {
            $result = false;
        } else {
            $result = $this->nextRow($this->result);
        }

        $this->last_cached = false;

        if (is_null($result)) {
            $result = false;
        }

        if ($use_cache && $this->is_cache_enabled) {
            Cache::getInstance()->setQuery($sql, $result);
        }

        return $result;
    }

    /**
     * Returns a value from the first row, first column of a SELECT query
     *
     * @param string|DbQuery $sql
     * @param bool $use_cache
     * @return string|false|null
     */
    public function getValue($sql, $use_cache = true)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        if (!$result = $this->getRow($sql, $use_cache)) {
            return false;
        }

        return array_shift($result);
    }

    /**
     * Get number of rows for last result
     *
     * @return int
     */
    public function numRows()
    {
        if (!$this->last_cached && $this->result) {
            $nrows = $this->_numRows($this->result);
            if ($this->is_cache_enabled) {
                Cache::getInstance()->set($this->last_query_hash.'_nrows', $nrows);
            }
            return $nrows;
        } elseif ($this->is_cache_enabled && $this->last_cached) {
            return Cache::getInstance()->get($this->last_query_hash.'_nrows');
        }
    }

    /**
     * Executes a query
     *
     * @param string|DbQuery $sql
     * @param bool $use_cache
     * @return bool|mysqli_result|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    protected function q($sql, $use_cache = true)
    {
        if ($sql instanceof DbQuery) {
            $sql = $sql->build();
        }

        $this->result = false;
        $result = $this->query($sql);
        if ($use_cache && $this->is_cache_enabled) {
            Cache::getInstance()->deleteQuery($sql);
        }

        if (_PS_DEBUG_SQL_) {
            $this->displayError($sql);
        }

        return $result;
    }

    /**
     * Displays last SQL error
     *
     * @param string|bool $sql
     * @throws PrestaShopDatabaseException
     */
    public function displayError($sql = false)
    {
        global $webservice_call;

        $errno = $this->getNumberError();
        if ($webservice_call && $errno) {
            $dbg = debug_backtrace();
            WebserviceRequest::getInstance()->setError(500, '[SQL Error] '.$this->getMsgError().'. From '.(isset($dbg[3]['class']) ? $dbg[3]['class'] : '').'->'.$dbg[3]['function'].'() Query was : '.$sql, 97);
        } elseif (_PS_DEBUG_SQL_ && $errno && !defined('PS_INSTALLATION_IN_PROGRESS')) {
            if ($sql) {
                throw new PrestaShopDatabaseException($this->getMsgError().'<br /><br /><pre>'.$sql.'</pre>');
            }

            throw new PrestaShopDatabaseException($this->getMsgError());
        }
    }

    /**
     * Sanitize data which will be injected into SQL query
     *
     * @param string $string SQL data which will be injected into SQL query
     * @param bool $html_ok Does data contain HTML code ? (optional)
     * @return string Sanitized data
     */
    public function escape($string, $html_ok = false, $bq_sql = false)
    {
        if (_PS_MAGIC_QUOTES_GPC_) {
            $string = stripslashes($string);
        }

        if (!is_numeric($string)) {
            $string = $this->_escape($string);

            if (!$html_ok) {
                $string = strip_tags(Tools::nl2br($string));
            }

            if ($bq_sql === true) {
                $string = str_replace('`', '\`', $string);
            }
        }

        return $string;
    }

    /**
     * Try a connection to the database
     *
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @param string $db Database name
     * @param bool $new_db_link
     * @param string|bool $engine
     * @param int $timeout
     * @return int Error code or 0 if connection was successful
     */
    public static function checkConnection($server, $user, $pwd, $db, $new_db_link = true, $engine = null, $timeout = 5)
    {
        return call_user_func_array(array(Db::getClass(), 'tryToConnect'), array($server, $user, $pwd, $db, $new_db_link, $engine, $timeout));
    }

    /**
     * Try a connection to the database and set names to UTF-8
     *
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @return bool
     */
    public static function checkEncoding($server, $user, $pwd)
    {
        return call_user_func_array(array(Db::getClass(), 'tryUTF8'), array($server, $user, $pwd));
    }

    /**
     * Try a connection to the database and check if at least one table with same prefix exists
     *
     * @param string $server Server address
     * @param string $user Login for database connection
     * @param string $pwd Password for database connection
     * @param string $db Database name
     * @param string $prefix Tables prefix
     * @return bool
     */
    public static function hasTableWithSamePrefix($server, $user, $pwd, $db, $prefix)
    {
        return call_user_func_array(array(Db::getClass(), 'hasTableWithSamePrefix'), array($server, $user, $pwd, $db, $prefix));
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
        return call_user_func_array(array(Db::getClass(), 'checkCreatePrivilege'), array($server, $user, $pwd, $db, $prefix, $engine));
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
        return call_user_func_array(array(Db::getClass(), 'checkAutoIncrement'), array($server, $user, $pwd));
    }

    /**
     * Executes a query
     *
     * @deprecated 1.5.0.1
     * @param string|DbQuery $sql
     * @param bool $use_cache
     * @return array|bool|mysqli_result|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function s($sql, $use_cache = true)
    {
        Tools::displayAsDeprecated();
        return Db::getInstance()->executeS($sql, true, $use_cache);
    }

    /**
     * Executes a query
     *
     * @deprecated 1.5.0.1
     * @param $sql
     * @param int $use_cache
     * @return array|bool|mysqli_result|PDOStatement|resource
     */
    public static function ps($sql, $use_cache = 1)
    {
        Tools::displayAsDeprecated();
        $ret = Db::s($sql, $use_cache);
        return $ret;
    }

    /**
     * Executes a query and kills process (dies)
     *
     * @deprecated 1.5.0.1
     * @param $sql
     * @param int $use_cache
     */
    public static function ds($sql, $use_cache = 1)
    {
        Tools::displayAsDeprecated();
        Db::s($sql, $use_cache);
        die();
    }

    /**
     * Get used link instance
     *
     * @return PDO|mysqli|resource Resource
     */
    public function getLink()
    {
        return $this->link;
    }
}
