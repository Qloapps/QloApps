<?php

/**
 * Smarty error handler
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 *
 * @deprecated
Smarty does no longer use @filemtime()
 */
class Smarty_Internal_ErrorHandler
{
    /**
     * contains directories outside of SMARTY_DIR that are to be muted by muteExpectedErrors()
     */
    public static $mutedDirectories = array();

    /**
     * error handler returned by set_error_handler() in self::muteExpectedErrors()
     */
    private static $previousErrorHandler = null;

    /**
     * Enable error handler to mute expected messages
     *
     */
    public static function muteExpectedErrors()
    {
        /*
            error muting is done because some people implemented custom error_handlers using
            http://php.net/set_error_handler and for some reason did not understand the following paragraph:

                It is important to remember that the standard PHP error handler is completely bypassed for the
                error types specified by error_types unless the callback function returns FALSE.
                error_reporting() settings will have no effect and your error handler will be called regardless -
                however you are still able to read the current value of error_reporting and act appropriately.
                Of particular note is that this value will be 0 if the statement that caused the error was
                prepended by the @ error-control operator.

            Smarty deliberately uses @filemtime() over file_exists() and filemtime() in some places. Reasons include
                - @filemtime() is almost twice as fast as using an additional file_exists()
                - between file_exists() and filemtime() a possible race condition is opened,
                  which does not exist using the simple @filemtime() approach.
        */
        $error_handler = array('Smarty_Internal_ErrorHandler', 'mutingErrorHandler');
        $previous = set_error_handler($error_handler);
        // avoid dead loops
        if ($previous !== $error_handler) {
            self::$previousErrorHandler = $previous;
        }
    }

    /**
     * Error Handler to mute expected messages
     *
     * @link http://php.net/set_error_handler
     *
     * @param integer $errno Error level
     * @param         $errstr
     * @param         $errfile
     * @param         $errline
     * @param         $errcontext
     *
     * @return bool
     */
    public static function mutingErrorHandler($errno, $errstr, $errfile, $errline, $errcontext = array())
    {
        $_is_muted_directory = false;
        // add the SMARTY_DIR to the list of muted directories
        if (!isset(self::$mutedDirectories[ SMARTY_DIR ])) {
            $smarty_dir = realpath(SMARTY_DIR);
            if ($smarty_dir !== false) {
                self::$mutedDirectories[ SMARTY_DIR ] =
                    array('file' => $smarty_dir, 'length' => strlen($smarty_dir),);
            }
        }
        // walk the muted directories and test against $errfile
        foreach (self::$mutedDirectories as $key => &$dir) {
            if (!$dir) {
                // resolve directory and length for speedy comparisons
                $file = realpath($key);
                if ($file === false) {
                    // this directory does not exist, remove and skip it
                    unset(self::$mutedDirectories[ $key ]);
                    continue;
                }
                $dir = array('file' => $file, 'length' => strlen($file),);
            }
            if (!strncmp($errfile, $dir[ 'file' ], $dir[ 'length' ])) {
                $_is_muted_directory = true;
                break;
            }
        }
        // pass to next error handler if this error did not occur inside SMARTY_DIR
        // or the error was within smarty but masked to be ignored
        if (!$_is_muted_directory || ($errno && $errno & error_reporting())) {
            if (self::$previousErrorHandler) {
                return call_user_func(
                    self::$previousErrorHandler,
                    $errno,
                    $errstr,
                    $errfile,
                    $errline,
                    $errcontext
                );
            } else {
                return false;
            }
        }
    }
}
