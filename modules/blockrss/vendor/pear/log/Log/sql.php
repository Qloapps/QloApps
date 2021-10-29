<?php
/**
 * $Header$
 * $Horde: horde/lib/Log/sql.php,v 1.12 2000/08/16 20:27:34 chuck Exp $
 *
 * @version $Revision$
 * @package Log
 */

/**
 * We require the PEAR DB class.  This is generally defined in the DB.php file,
 * but it's possible that the caller may have provided the DB class, or a
 * compatible wrapper (such as the one shipped with MDB2), so we first check
 * for an existing 'DB' class before including 'DB.php'.
 */
if (!class_exists('DB')) {
    require_once 'DB.php';
}

/**
 * The Log_sql class is a concrete implementation of the Log::
 * abstract class which sends messages to an SQL server.  Each entry
 * occupies a separate row in the database.
 *
 * This implementation uses PHP's PEAR database abstraction layer.
 *
 * CREATE TABLE log_table (
 *  id          INT NOT NULL,
 *  logtime     TIMESTAMP NOT NULL,
 *  ident       CHAR(16) NOT NULL,
 *  priority    INT NOT NULL,
 *  message     VARCHAR(200),
 *  PRIMARY KEY (id)
 * );
 *
 * @author  Jon Parise <jon@php.net>
 * @since   Horde 1.3
 * @since   Log 1.0
 * @package Log
 *
 * @example sql.php     Using the SQL handler.
 */
class Log_sql extends Log
{
    /**
     * Variable containing the DSN information.
     * @var mixed
     * @access private
     */
    var $_dsn = '';

    /**
     * String containing the SQL insertion statement.
     *
     * @var string
     * @access private
     */
    var $_sql = '';

    /**
     * Array containing our set of DB configuration options.
     * @var array
     * @access private
     */
    var $_options = array('persistent' => true);

    /**
     * Object holding the database handle.
     * @var object
     * @access private
     */
    var $_db = null;

    /**
     * Resource holding the prepared statement handle.
     * @var resource
     * @access private
     */
    var $_statement = null;

    /**
     * Flag indicating that we're using an existing database connection.
     * @var boolean
     * @access private
     */
    var $_existingConnection = false;

    /**
     * String holding the database table to use.
     * @var string
     * @access private
     */
    var $_table = 'log_table';

    /**
     * String holding the name of the ID sequence.
     * @var string
     * @access private
     */
    var $_sequence = 'log_id';

    /**
     * Maximum length of the $ident string.  This corresponds to the size of
     * the 'ident' column in the SQL table.
     * @var integer
     * @access private
     */
    var $_identLimit = 16;

    /**
     * Constructs a new sql logging object.
     *
     * @param string $name         The target SQL table.
     * @param string $ident        The identification field.
     * @param array $conf          The connection configuration array.
     * @param int $level           Log messages up to and including this level.
     * @access public
     */
    public function __construct($name, $ident = '', $conf = array(),
                                $level = PEAR_LOG_DEBUG)
    {
        $this->_id = md5(microtime().rand());
        $this->_table = $name;
        $this->_mask = Log::UPTO($level);

        /* Now that we have a table name, assign our SQL statement. */
        if (!empty($conf['sql'])) {
            $this->_sql = $conf['sql'];
        } else {
            $this->_sql = 'INSERT INTO ' . $this->_table .
                          ' (id, logtime, ident, priority, message)' .
                          ' VALUES(?, CURRENT_TIMESTAMP, ?, ?, ?)';
        }

        /* If an options array was provided, use it. */
        if (isset($conf['options']) && is_array($conf['options'])) {
            $this->_options = $conf['options'];
        }

        /* If a specific sequence name was provided, use it. */
        if (!empty($conf['sequence'])) {
            $this->_sequence = $conf['sequence'];
        }

        /* If a specific sequence name was provided, use it. */
        if (isset($conf['identLimit'])) {
            $this->_identLimit = $conf['identLimit'];
        }

        /* Now that the ident limit is confirmed, set the ident string. */
        $this->setIdent($ident);

        /* If an existing database connection was provided, use it. */
        if (isset($conf['db'])) {
            $this->_db = &$conf['db'];
            $this->_existingConnection = true;
            $this->_opened = true;
        } else {
            $this->_dsn = $conf['dsn'];
        }
    }

    /**
     * Opens a connection to the database, if it has not already
     * been opened. This is implicitly called by log(), if necessary.
     *
     * @return boolean   True on success, false on failure.
     * @access public
     */
    function open()
    {
        if (!$this->_opened) {
            /* Use the DSN and options to create a database connection. */
            $this->_db = &DB::connect($this->_dsn, $this->_options);
            if (DB::isError($this->_db)) {
                return false;
            }

            /* Create a prepared statement for repeated use in log(). */
            if (!$this->_prepareStatement()) {
                return false;
            }

            /* We now consider out connection open. */
            $this->_opened = true;
        }

        return $this->_opened;
    }

    /**
     * Closes the connection to the database if it is still open and we were
     * the ones that opened it.  It is the caller's responsible to close an
     * existing connection that was passed to us via $conf['db'].
     *
     * @return boolean   True on success, false on failure.
     * @access public
     */
    function close()
    {
        if ($this->_opened && !$this->_existingConnection) {
            $this->_opened = false;
            $this->_db->freePrepared($this->_statement);
            return $this->_db->disconnect();
        }

        return ($this->_opened === false);
    }

    /**
     * Sets this Log instance's identification string.  Note that this
     * SQL-specific implementation will limit the length of the $ident string
     * to sixteen (16) characters.
     *
     * @param string    $ident      The new identification string.
     *
     * @access  public
     * @since   Log 1.8.5
     */
    function setIdent($ident)
    {
        $this->_ident = substr($ident, 0, $this->_identLimit);
    }

    /**
     * Inserts $message to the currently open database.  Calls open(),
     * if necessary.  Also passes the message along to any Log_observer
     * instances that are observing this Log.
     *
     * @param mixed  $message  String or object containing the message to log.
     * @param string $priority The priority of the message.  Valid
     *                  values are: PEAR_LOG_EMERG, PEAR_LOG_ALERT,
     *                  PEAR_LOG_CRIT, PEAR_LOG_ERR, PEAR_LOG_WARNING,
     *                  PEAR_LOG_NOTICE, PEAR_LOG_INFO, and PEAR_LOG_DEBUG.
     * @return boolean  True on success or false on failure.
     * @access public
     */
    function log($message, $priority = null)
    {
        /* If a priority hasn't been specified, use the default value. */
        if ($priority === null) {
            $priority = $this->_priority;
        }

        /* Abort early if the priority is above the maximum logging level. */
        if (!$this->_isMasked($priority)) {
            return false;
        }

        /* If the connection isn't open and can't be opened, return failure. */
        if (!$this->_opened && !$this->open()) {
            return false;
        }

        /* If we don't already have our statement object yet, create it. */
        if (!is_object($this->_statement) && !$this->_prepareStatement()) {
            return false;
        }

        /* Extract the string representation of the message. */
        $message = $this->_extractMessage($message);

        /* Build our set of values for this log entry. */
        $id = $this->_db->nextId($this->_sequence);
        $values = array($id, $this->_ident, $priority, $message);

        /* Execute the SQL query for this log entry insertion. */
        $result =& $this->_db->execute($this->_statement, $values);
        if (DB::isError($result)) {
            return false;
        }

        $this->_announce(array('priority' => $priority, 'message' => $message));

        return true;
    }

    /**
     * Prepare the SQL insertion statement.
     *
     * @return boolean  True if the statement was successfully created.
     *
     * @access  private
     * @since   Log 1.9.1
     */
    function _prepareStatement()
    {
        $this->_statement = $this->_db->prepare($this->_sql);

        /* Return success if we didn't generate an error. */
        return (DB::isError($this->_statement) === false);
    }
}
