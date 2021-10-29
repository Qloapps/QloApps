<?php
/**
 * $Header$
 *
 * @version $Revision$
 * @package Log
 */

/** PEAR's MDB2 package */
require_once 'MDB2.php';
MDB2::loadFile('Date');

/**
 * The Log_mdb2 class is a concrete implementation of the Log:: abstract class
 * which sends messages to an SQL server.  Each entry occupies a separate row
 * in the database.
 *
 * This implementation uses PEAR's MDB2 database abstraction layer.
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
 * @author  Lukas Smith <smith@backendmedia.com>
 * @author  Jon Parise <jon@php.net>
 * @since   Log 1.9.0
 * @package Log
 */
class Log_mdb2 extends Log
{
    /**
     * Variable containing the DSN information.
     * @var mixed
     * @access private
     */
    var $_dsn = '';

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
     * Set of field types used in the database table.
     * @var array
     * @access private
     */
    var $_types = array(
        'id'        => 'integer',
        'logtime'   => 'timestamp',
        'ident'     => 'text',
        'priority'  => 'text',
        'message'   => 'clob'
    );

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
        } elseif (isset($conf['singleton'])) {
            $this->_db = &MDB2::singleton($conf['singleton'], $this->_options);
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
            $this->_db = &MDB2::connect($this->_dsn, $this->_options);
            if (PEAR::isError($this->_db)) {
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
        /* If we have a statement object, free it. */
        if (is_object($this->_statement)) {
            $this->_statement->free();
            $this->_statement = null;
        }

        /* If we opened the database connection, disconnect it. */
        if ($this->_opened && !$this->_existingConnection) {
            $this->_opened = false;
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

        /* If we don't already have a statement object, create one. */
        if (!is_object($this->_statement) && !$this->_prepareStatement()) {
            return false;
        }

        /* Extract the string representation of the message. */
        $message = $this->_extractMessage($message);

        /* Build our set of values for this log entry. */
        $values = array(
            'id'       => $this->_db->nextId($this->_sequence),
            'logtime'  => MDB2_Date::mdbNow(),
            'ident'    => $this->_ident,
            'priority' => $priority,
            'message'  => $message
        );

        /* Execute the SQL query for this log entry insertion. */
        $this->_db->expectError(MDB2_ERROR_NOSUCHTABLE);
        $result = &$this->_statement->execute($values);
        $this->_db->popExpect();

        /* Attempt to handle any errors. */
        if (PEAR::isError($result)) {
            /* We can only handle MDB2_ERROR_NOSUCHTABLE errors. */
            if ($result->getCode() != MDB2_ERROR_NOSUCHTABLE) {
                return false;
            }

            /* Attempt to create the target table. */
            if (!$this->_createTable()) {
                return false;
            }

            /* Recreate our prepared statement resource. */
            $this->_statement->free();
            if (!$this->_prepareStatement()) {
                return false;
            }

            /* Attempt to re-execute the insertion query. */
            $result = $this->_statement->execute($values);
            if (PEAR::isError($result)) {
                return false;
            }
        }

        $this->_announce(array('priority' => $priority, 'message' => $message));

        return true;
    }

    /**
     * Create the log table in the database.
     *
     * @return boolean  True on success or false on failure.
     * @access private
     */
    function _createTable()
    {
        $this->_db->loadModule('Manager', null, true);
        $result = $this->_db->manager->createTable(
            $this->_table,
            array(
                'id'        => array('type' => $this->_types['id']),
                'logtime'   => array('type' => $this->_types['logtime']),
                'ident'     => array('type' => $this->_types['ident']),
                'priority'  => array('type' => $this->_types['priority']),
                'message'   => array('type' => $this->_types['message'])
            )
        );
        if (PEAR::isError($result)) {
            return false;
        }

        $result = $this->_db->manager->createIndex(
            $this->_table,
            'unique_id',
            array('fields' => array('id' => true), 'unique' => true)
        );
        if (PEAR::isError($result)) {
            return false;
        }

        return true;
    }

    /**
     * Prepare the SQL insertion statement.
     *
     * @return boolean  True if the statement was successfully created.
     *
     * @access  private
     * @since   Log 1.9.0
     */
    function _prepareStatement()
    {
        $this->_statement = &$this->_db->prepare(
                'INSERT INTO ' . $this->_table .
                ' (id, logtime, ident, priority, message)' .
                ' VALUES(:id, :logtime, :ident, :priority, :message)',
                $this->_types, MDB2_PREPARE_MANIP);

        /* Return success if we didn't generate an error. */
        return (PEAR::isError($this->_statement) === false);
    }
}
