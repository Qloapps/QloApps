<?php
/**
 * $Header$
 *
 * @version $Revision$
 * @package Log
 */

/**
 * The Log_error_log class is a concrete implementation of the Log abstract
 * class that logs messages using PHP's error_log() function.
 *
 * @author  Jon Parise <jon@php.net>
 * @since   Log 1.7.0
 * @package Log
 *
 * @example error_log.php   Using the error_log handler.
 */
class Log_error_log extends Log
{
    /**
     * The error_log() log type.
     * @var integer
     * @access private
     */
    var $_type = PEAR_LOG_TYPE_SYSTEM;

    /**
     * The type-specific destination value.
     * @var string
     * @access private
     */
    var $_destination = '';

    /**
     * Additional headers to pass to the mail() function when the
     * PEAR_LOG_TYPE_MAIL type is used.
     * @var string
     * @access private
     */
    var $_extra_headers = '';

    /**
     * String containing the format of a log line.
     * @var string
     * @access private
     */
    var $_lineFormat = '%2$s: %4$s';

    /**
     * String containing the timestamp format.  It will be passed directly to
     * strftime().  Note that the timestamp string will generated using the
     * current locale.
     * @var string
     * @access private
     */
    var $_timeFormat = '%b %d %H:%M:%S';

    /**
     * Constructs a new Log_error_log object.
     *
     * @param string $name     One of the PEAR_LOG_TYPE_* constants.
     * @param string $ident    The identity string.
     * @param array  $conf     The configuration array.
     * @param int    $level    Log messages up to and including this level.
     * @access public
     */
    public function __construct($name, $ident = '', $conf = array(),
                                $level = PEAR_LOG_DEBUG)
    {
        $this->_id = md5(microtime().rand());
        $this->_type = $name;
        $this->_ident = $ident;
        $this->_mask = Log::UPTO($level);

        if (!empty($conf['destination'])) {
            $this->_destination = $conf['destination'];
        }

        if (!empty($conf['extra_headers'])) {
            $this->_extra_headers = $conf['extra_headers'];
        }

        if (!empty($conf['lineFormat'])) {
            $this->_lineFormat = str_replace(array_keys($this->_formatMap),
                                             array_values($this->_formatMap),
                                             $conf['lineFormat']);
        }

        if (!empty($conf['timeFormat'])) {
            $this->_timeFormat = $conf['timeFormat'];
        }
    }

    /**
     * Opens the handler.
     *
     * @access  public
     * @since   Log 1.9.6
     */
    function open()
    {
        $this->_opened = true;
        return true;
    }

    /**
     * Closes the handler.
     *
     * @access  public
     * @since   Log 1.9.6
     */
    function close()
    {
        $this->_opened = false;
        return true;
    }

    /**
     * Logs $message using PHP's error_log() function.  The message is also
     * passed along to any Log_observer instances that are observing this Log.
     *
     * @param mixed  $message   String or object containing the message to log.
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

        /* Extract the string representation of the message. */
        $message = $this->_extractMessage($message);

        /* Build the string containing the complete log line. */
        $line = $this->_format($this->_lineFormat,
                               strftime($this->_timeFormat),
                               $priority, $message);

        /* Pass the log line and parameters to the error_log() function. */
        $success = error_log($line, $this->_type, $this->_destination,
                             $this->_extra_headers);

        $this->_announce(array('priority' => $priority, 'message' => $message));

        return $success;
    }

}
