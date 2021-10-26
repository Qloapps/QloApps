<?php
/**
 * $Header$
 * $Horde: horde/lib/Log/observer.php,v 1.5 2000/06/28 21:36:13 jon Exp $
 *
 * @version $Revision$
 * @package Log
 */

/**
 * The Log_observer:: class implements the Observer end of a Subject-Observer
 * pattern for watching log activity and taking actions on exceptional events.
 *
 * @author  Chuck Hagenbuch <chuck@horde.org>
 * @since   Horde 1.3
 * @since   Log 1.0
 * @package Log
 *
 * @example observer_mail.php   An example Log_observer implementation.
 */
class Log_observer
{
    /**
     * Instance-specific unique identification number.
     *
     * @var integer
     * @access private
     */
    var $_id = 0;

    /**
     * The minimum priority level of message that we want to hear about.
     * PEAR_LOG_EMERG is the highest priority, so we will only hear messages
     * with an integer priority value less than or equal to ours.  It defaults
     * to PEAR_LOG_INFO, which listens to everything except PEAR_LOG_DEBUG.
     *
     * @var string
     * @access private
     */
    var $_priority = PEAR_LOG_INFO;

    /**
     * Creates a new basic Log_observer instance.
     *
     * @param integer   $priority   The highest priority at which to receive
     *                              log event notifications.
     *
     * @access public
     */
    public function __construct($priority = PEAR_LOG_INFO)
    {
        $this->_id = md5(microtime().rand());
        $this->_priority = $priority;
    }

    /**
     * Attempts to return a new concrete Log_observer instance of the requested
     * type.
     *
     * @param string    $type       The type of concreate Log_observer subclass
     *                              to return.
     * @param integer   $priority   The highest priority at which to receive
     *                              log event notifications.
     * @param array     $conf       Optional associative array of additional
     *                              configuration values.
     *
     * @return object               The newly created concrete Log_observer
     *                              instance, or null on an error.
     */
    function &factory($type, $priority = PEAR_LOG_INFO, $conf = array())
    {
        $type = strtolower($type);
        $class = 'Log_observer_' . $type;

        /*
         * If the desired class already exists (because the caller has supplied
         * it from some custom location), simply instantiate and return a new
         * instance.
         */
        if (class_exists($class)) {
            $object = new $class($priority, $conf);
            return $object;
        }

        /* Support both the new-style and old-style file naming conventions. */
        $newstyle = true;
        $classfile = dirname(__FILE__) . '/observer_' . $type . '.php';

        if (!file_exists($classfile)) {
            $classfile = 'Log/' . $type . '.php';
            $newstyle = false;
        }

        /*
         * Attempt to include our version of the named class, but don't treat
         * a failure as fatal.  The caller may have already included their own
         * version of the named class.
         */
        @include_once $classfile;

        /* If the class exists, return a new instance of it. */
        if (class_exists($class)) {
            /* Support both new-style and old-style construction. */
            if ($newstyle) {
                $object = new $class($priority, $conf);
            } else {
                $object = new $class($priority);
            }
            return $object;
        }

        $null = null;
        return $null;
    }

    /**
     * This is a stub method to make sure that Log_Observer classes do
     * something when they are notified of a message.  The default behavior
     * is to just print the message, which is obviously not desireable in
     * practically any situation - which is why you need to override this
     * method. :)
     *
     * @param array     $event      A hash describing the log event.
     */
    function notify($event)
    {
        print_r($event);
    }
}
