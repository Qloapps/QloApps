<?php
/**
 * $Header$
 * $Horde: horde/lib/Log/composite.php,v 1.2 2000/06/28 21:36:13 jon Exp $
 *
 * @version $Revision$
 * @package Log
 */

/**
 * The Log_composite:: class implements a Composite pattern which
 * allows multiple Log implementations to receive the same events.
 *
 * @author  Chuck Hagenbuch <chuck@horde.org>
 * @author  Jon Parise <jon@php.net>
 *
 * @since Horde 1.3
 * @since Log 1.0
 * @package Log
 *
 * @example composite.php   Using the composite handler.
 */
class Log_composite extends Log
{
    /**
     * Array holding all of the Log instances to which log events should be
     * sent.
     *
     * @var array
     * @access private
     */
    var $_children = array();


    /**
     * Constructs a new composite Log object.
     *
     * @param string   $name       This parameter is ignored.
     * @param string   $ident      This parameter is ignored.
     * @param array    $conf       This parameter is ignored.
     * @param int      $level      This parameter is ignored.
     *
     * @access public
     */
    public function __construct($name, $ident = '', $conf = array(),
                                $level = PEAR_LOG_DEBUG)
    {
        $this->_ident = $ident;
    }

    /**
     * Opens all of the child instances.
     *
     * @return  True if all of the child instances were successfully opened.
     *
     * @access public
     */
    function open()
    {
        /* Attempt to open each of our children. */
        $this->_opened = true;
        foreach ($this->_children as $child) {
            $this->_opened &= $child->open();
        }

        /* If all children were opened, return success. */
        return $this->_opened;
    }

    /**
     * Closes all open child instances.
     *
     * @return  True if all of the opened child instances were successfully
     *          closed.
     *
     * @access public
     */
    function close()
    {
        /* If we haven't been opened, there's nothing more to do. */
        if (!$this->_opened) {
            return true;
        }

        /* Attempt to close each of our children. */
        $closed = true;
        foreach ($this->_children as $child) {
            if ($child->_opened) {
                $closed &= $child->close();
            }
        }

        /* Clear the opened state for consistency. */
        $this->_opened = false;

        /* If all children were closed, return success. */
        return $closed;
    }

    /**
     * Flushes all child instances.  It is assumed that all of the children
     * have been successfully opened.
     *
     * @return  True if all of the child instances were successfully flushed.
     *
     * @access public
     * @since Log 1.8.2
     */
    function flush()
    {
        /* Attempt to flush each of our children. */
        $flushed = true;
        foreach ($this->_children as $child) {
            $flushed &= $child->flush();
        }

        /* If all children were flushed, return success. */
        return $flushed;
    }

    /**
     * Sends $message and $priority to each child of this composite.  If the
     * appropriate children aren't already open, they will be opened here.
     *
     * @param mixed     $message    String or object containing the message
     *                              to log.
     * @param string    $priority   (optional) The priority of the message.
     *                              Valid values are: PEAR_LOG_EMERG,
     *                              PEAR_LOG_ALERT, PEAR_LOG_CRIT,
     *                              PEAR_LOG_ERR, PEAR_LOG_WARNING,
     *                              PEAR_LOG_NOTICE, PEAR_LOG_INFO, and
     *                              PEAR_LOG_DEBUG.
     *
     * @return boolean  True if the entry is successfully logged.
     *
     * @access public
     */
    function log($message, $priority = null)
    {
        /* If a priority hasn't been specified, use the default value. */
        if ($priority === null) {
            $priority = $this->_priority;
        }

        /*
         * Abort early if the priority is above the composite handler's
         * maximum logging level.
         *
         * XXX: Consider whether or not introducing this change would break
         * backwards compatibility.  Some users may be expecting composite
         * handlers to pass on all events to their children regardless of
         * their own priority.
         */
        #if (!$this->_isMasked($priority)) {
        #    return false;
        #}

        /*
         * Iterate over all of our children.  If a unopened child will respond
         * to this log event, we attempt to open it immediately.  The composite
         * handler's opened state will be enabled as soon as the first child
         * handler is successfully opened.
         *
         * We track an overall success state that indicates whether or not all
         * of the relevant child handlers were opened and successfully logged
         * the event.  If one handler fails, we still attempt any remaining
         * children, but we consider the overall result a failure.
         */
        $success = true;
        foreach ($this->_children as $child) {
            /* If this child won't respond to this event, skip it. */
            if (!$child->_isMasked($priority)) {
                continue;
            }

            /* If this child has yet to be opened, attempt to do so now. */
            if (!$child->_opened) {
                $success &= $child->open();

                /*
                 * If we've successfully opened our first handler, the
                 * composite handler itself is considered to be opened.
                 */
                if (!$this->_opened && $success) {
                    $this->_opened = true;
                }
            }

            /* Finally, attempt to log the message to the child handler. */
            if ($child->_opened) {
                $success &= $child->log($message, $priority);
            }
        }

        /* Notify the observers. */
        $this->_announce(array('priority' => $priority, 'message' => $message));

        /* Return success if all of the open children logged the event. */
        return $success;
    }

    /**
     * Returns true if this is a composite.
     *
     * @return boolean  True if this is a composite class.
     *
     * @access public
     */
    function isComposite()
    {
        return true;
    }

    /**
     * Sets this identification string for all of this composite's children.
     *
     * @param string    $ident      The new identification string.
     *
     * @access public
     * @since  Log 1.6.7
     */
    function setIdent($ident)
    {
        /* Call our base class's setIdent() method. */
        parent::setIdent($ident);

        /* ... and then call setIdent() on all of our children. */
        foreach ($this->_children as $child) {
            $child->setIdent($ident);
        }
    }

    /**
     * Adds a Log instance to the list of children.
     *
     * @param object    $child      The Log instance to add.
     *
     * @return boolean  True if the Log instance was successfully added.
     *
     * @access public
     */
    function addChild(&$child)
    {
        /* Make sure this is a Log instance. */
        if (!is_a($child, 'Log')) {
            return false;
        }

        $this->_children[$child->_id] = $child;

        return true;
    }

    /**
     * Removes a Log instance from the list of children.
     *
     * @param object    $child      The Log instance to remove.
     *
     * @return boolean  True if the Log instance was successfully removed.
     *
     * @access public
     */
    function removeChild($child)
    {
        if (!is_a($child, 'Log') || !isset($this->_children[$child->_id])) {
            return false;
        }

        unset($this->_children[$child->_id]);

        return true;
    }

}
