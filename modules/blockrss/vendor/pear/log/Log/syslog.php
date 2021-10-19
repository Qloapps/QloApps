<?php
/**
 * $Header$
 * $Horde: horde/lib/Log/syslog.php,v 1.6 2000/06/28 21:36:13 jon Exp $
 *
 * @version $Revision$
 * @package Log
 */

/**
 * The Log_syslog class is a concrete implementation of the Log::
 * abstract class which sends messages to syslog on UNIX-like machines
 * (PHP emulates this with the Event Log on Windows machines).
 *
 * @author  Chuck Hagenbuch <chuck@horde.org>
 * @author  Jon Parise <jon@php.net>
 * @since   Horde 1.3
 * @since   Log 1.0
 * @package Log
 *
 * @example syslog.php      Using the syslog handler.
 */
class Log_syslog extends Log
{
    /**
     * Integer holding the log facility to use.
     * @var integer
     * @access private
     */
    var $_name = LOG_SYSLOG;

    /**
     * Should we inherit the current syslog connection for this process, or
     * should we call openlog() to start a new syslog connection?
     * @var boolean
     * @access private
     */
    var $_inherit = false;

    /**
     * Should we re-open the syslog connection for each log event?
     * @var boolean
     * @access private
     */
    var $_reopen = false;

    /**
     * Maximum message length that will be sent to syslog().  If the handler
     * receives a message longer than this length limit, it will be split into
     * multiple syslog() calls.
     * @var integer
     * @access private
     */
    var $_maxLength = 500;

    /**
     * String containing the format of a message.
     * @var string
     * @access private
     */
    var $_lineFormat = '%4$s';

    /**
     * String containing the timestamp format.  It will be passed directly to
     * strftime().  Note that the timestamp string will generated using the
     * current locale.
     * @var string
     * @access private
     */
    var $_timeFormat = '%b %d %H:%M:%S';

    /**
     * Constructs a new syslog object.
     *
     * @param string $name     The syslog facility.
     * @param string $ident    The identity string.
     * @param array  $conf     The configuration array.
     * @param int    $level    Log messages up to and including this level.
     * @access public
     */
    public function __construct($name, $ident = '', $conf = array(),
                                $level = PEAR_LOG_DEBUG)
    {
        /* Ensure we have a valid integer value for $name. */
        if (empty($name) || !is_int($name)) {
            $name = LOG_SYSLOG;
        }

        if (isset($conf['inherit'])) {
            $this->_inherit = $conf['inherit'];
            $this->_opened = $this->_inherit;
        }
        if (isset($conf['reopen'])) {
            $this->_reopen = $conf['reopen'];
        }
        if (isset($conf['maxLength'])) {
            $this->_maxLength = $conf['maxLength'];
        }
        if (!empty($conf['lineFormat'])) {
            $this->_lineFormat = str_replace(array_keys($this->_formatMap),
                                             array_values($this->_formatMap),
                                             $conf['lineFormat']);
        }
        if (!empty($conf['timeFormat'])) {
            $this->_timeFormat = $conf['timeFormat'];
        }

        $this->_id = md5(microtime().rand());
        $this->_name = $name;
        $this->_ident = $ident;
        $this->_mask = Log::UPTO($level);
    }

    /**
     * Opens a connection to the system logger, if it has not already
     * been opened.  This is implicitly called by log(), if necessary.
     * @access public
     */
    function open()
    {
        if (!$this->_opened || $this->_reopen) {
            $this->_opened = openlog($this->_ident, LOG_PID, $this->_name);
        }

        return $this->_opened;
    }

    /**
     * Closes the connection to the system logger, if it is open.
     * @access public
     */
    function close()
    {
        if ($this->_opened && !$this->_inherit) {
            closelog();
            $this->_opened = false;
        }

        return true;
    }

    /**
     * Sends $message to the currently open syslog connection.  Calls
     * open() if necessary. Also passes the message along to any Log_observer
     * instances that are observing this Log.
     *
     * @param mixed $message String or object containing the message to log.
     * @param int $priority (optional) The priority of the message.  Valid
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

        /* If we need to (re)open the connection and open() fails, abort. */
        if ((!$this->_opened || $this->_reopen) && !$this->open()) {
            return false;
        }

        /* Extract the string representation of the message. */
        $message = $this->_extractMessage($message);

        /* Build a syslog priority value based on our current configuration. */
        $syslogPriority = $this->_toSyslog($priority);
        if ($this->_inherit) {
            $syslogPriority |= $this->_name;
        }

        /* Apply the configured line format to the message string. */
        $message = $this->_format($this->_lineFormat,
                                  strftime($this->_timeFormat),
                                  $priority, $message);

        /* Split the string into parts based on our maximum length setting. */
        $parts = str_split($message, $this->_maxLength);
        if ($parts === false) {
            return false;
        }

        foreach ($parts as $part) {
            if (!syslog($syslogPriority, $part)) {
                return false;
            }
        }

        $this->_announce(array('priority' => $priority, 'message' => $message));

        return true;
    }

    /**
     * Converts a PEAR_LOG_* constant into a syslog LOG_* constant.
     *
     * This function exists because, under Windows, not all of the LOG_*
     * constants have unique values.  Instead, the PEAR_LOG_* were introduced
     * for global use, with the conversion to the LOG_* constants kept local to
     * to the syslog driver.
     *
     * @param int $priority     PEAR_LOG_* value to convert to LOG_* value.
     *
     * @return  The LOG_* representation of $priority.
     *
     * @access private
     */
    function _toSyslog($priority)
    {
        static $priorities = array(
            PEAR_LOG_EMERG   => LOG_EMERG,
            PEAR_LOG_ALERT   => LOG_ALERT,
            PEAR_LOG_CRIT    => LOG_CRIT,
            PEAR_LOG_ERR     => LOG_ERR,
            PEAR_LOG_WARNING => LOG_WARNING,
            PEAR_LOG_NOTICE  => LOG_NOTICE,
            PEAR_LOG_INFO    => LOG_INFO,
            PEAR_LOG_DEBUG   => LOG_DEBUG
        );

        /* If we're passed an unknown priority, default to LOG_INFO. */
        if (!is_int($priority) || !in_array($priority, $priorities)) {
            return LOG_INFO;
        }

        return $priorities[$priority];
    }

}
