<?php
/**
 * $Header$
 *
 * @version $Revision$
 * @package Log
 */

/**
 * The Log_firebug class is a concrete implementation of the Log::
 * abstract class which writes message into Firebug console.
 *
 * http://www.getfirebug.com/
 *
 * @author  Mika Tuupola <tuupola@appelsiini.net>
 * @since   Log 1.9.11
 * @package Log
 *
 * @example firebug.php     Using the firebug handler.
 */
class Log_firebug extends Log
{
    /**
     * Should the output be buffered or displayed immediately?
     * @var string
     * @access private
     */
    var $_buffering = false;

    /**
     * String holding the buffered output.
     * @var string
     * @access private
     */
    var $_buffer = array();

    /**
     * String containing the format of a log line.
     * @var string
     * @access private
     */
    var $_lineFormat = '%2$s [%3$s] %4$s';

    /**
     * String containing the timestamp format.  It will be passed directly to
     * strftime().  Note that the timestamp string will generated using the
     * current locale.
     *
     * Note! Default lineFormat of this driver does not display time.
     *
     * @var string
     * @access private
     */
    var $_timeFormat = '%b %d %H:%M:%S';

    /**
     * Mapping of log priorities to Firebug methods.
     * @var array
     * @access private
     */
    var $_methods = array(
                        PEAR_LOG_EMERG   => 'error',
                        PEAR_LOG_ALERT   => 'error',
                        PEAR_LOG_CRIT    => 'error',
                        PEAR_LOG_ERR     => 'error',
                        PEAR_LOG_WARNING => 'warn',
                        PEAR_LOG_NOTICE  => 'info',
                        PEAR_LOG_INFO    => 'info',
                        PEAR_LOG_DEBUG   => 'debug'
                    );

    /**
     * Constructs a new Log_firebug object.
     *
     * @param string $name     Ignored.
     * @param string $ident    The identity string.
     * @param array  $conf     The configuration array.
     * @param int    $level    Log messages up to and including this level.
     * @access public
     */
    public function __construct($name = '', $ident = 'PHP', $conf = array(),
                                $level = PEAR_LOG_DEBUG)
    {
        $this->_id = md5(microtime().rand());
        $this->_ident = $ident;
        $this->_mask = Log::UPTO($level);
        if (isset($conf['buffering'])) {
            $this->_buffering = $conf['buffering'];
        }

        if ($this->_buffering) {
            register_shutdown_function(array(&$this, '_Log_firebug'));
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
     * Opens the firebug handler.
     *
     * @access  public
     */
    function open()
    {
        $this->_opened = true;
        return true;
    }

    /**
     * Destructor
     */
    function _Log_firebug()
    {
        $this->close();
    }

    /**
     * Closes the firebug handler.
     *
     * @access  public
     */
    function close()
    {
        $this->flush();
        $this->_opened = false;
        return true;
    }

    /**
     * Flushes all pending ("buffered") data.
     *
     * @access public
     */
    function flush() {
        if (count($this->_buffer)) {
            print '<script type="text/javascript">';
            print "\nif ('console' in window) {\n";
            foreach ($this->_buffer as $line) {
                print "  $line\n";
            }
            print "}\n";
            print "</script>\n";
        };
        $this->_buffer = array();
    }

    /**
     * Writes $message to Firebug console. Also, passes the message
     * along to any Log_observer instances that are observing this Log.
     *
     * @param mixed  $message    String or object containing the message to log.
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
        $method  = $this->_methods[$priority];

        /* normalize line breaks and escape quotes*/
        $message = preg_replace("/\r?\n/", "\\n", addslashes($message));

        /* Build the string containing the complete log line. */
        $line = $this->_format($this->_lineFormat,
                               strftime($this->_timeFormat),
                               $priority,
                               $message);

        if ($this->_buffering) {
            $this->_buffer[] = sprintf('console.%s("%s");', $method, $line);
        } else {
            print '<script type="text/javascript">';
            print "\nif ('console' in window) {\n";
            /* Build and output the complete log line. */
            printf('  console.%s("%s");', $method, $line);
            print "\n}\n";
            print "</script>\n";
        }
        /* Notify observers about this log message. */
        $this->_announce(array('priority' => $priority, 'message' => $message));

        return true;
    }
}
