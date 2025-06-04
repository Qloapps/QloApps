<?php

/**
 * Smarty error handler to fix new error levels in PHP8 for backwards compatibility
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Simon Wisselink
 *
 */
class Smarty_Internal_ErrorHandler
{

    /**
     * Allows {$foo} where foo is unset.
     * @var bool
     */
    public $allowUndefinedVars = true;

    /**
     * Allows {$foo->propName} where propName is undefined.
     * @var bool
     */
    public $allowUndefinedProperties = true;

    /**
     * Allows {$foo.bar} where bar is unset and {$foo.bar1.bar2} where either bar1 or bar2 is unset.
     * @var bool
     */
    public $allowUndefinedArrayKeys = true;

    /**
     * Allows {$foo->bar} where bar is not an object (e.g. null or false).
     * @var bool
     */
    public $allowDereferencingNonObjects = true;

    private $previousErrorHandler = null;

    /**
     * Enable error handler to intercept errors
     */
    public function activate() {
        /*
            Error muting is done because some people implemented custom error_handlers using
            https://php.net/set_error_handler and for some reason did not understand the following paragraph:

            It is important to remember that the standard PHP error handler is completely bypassed for the
            error types specified by error_types unless the callback function returns FALSE.
            error_reporting() settings will have no effect and your error handler will be called regardless -
            however you are still able to read the current value of error_reporting and act appropriately.
            Of particular note is that this value will be 0 if the statement that caused the error was
            prepended by the @ error-control operator.
        */
        $this->previousErrorHandler = set_error_handler([$this, 'handleError']);
    }

    /**
     * Disable error handler
     */
    public function deactivate() {
        restore_error_handler();
        $this->previousErrorHandler = null;
    }

    /**
     * Error Handler to mute expected messages
     *
     * @link https://php.net/set_error_handler
     *
     * @param integer $errno Error level
     * @param         $errstr
     * @param         $errfile
     * @param         $errline
     * @param         $errcontext
     *
     * @return bool
     */
    public function handleError($errno, $errstr, $errfile, $errline, $errcontext = [])
    {

        if ($this->allowUndefinedVars && preg_match(
                '/^(Attempt to read property "value" on null|Trying to get property (\'value\' )?of non-object)/',
                $errstr
            )) {
            return; // suppresses this error
        }

        if ($this->allowUndefinedProperties && preg_match(
                '/^(Undefined property)/',
                $errstr
            )) {
            return; // suppresses this error
        }

        if ($this->allowUndefinedArrayKeys && preg_match(
            '/^(Undefined index|Undefined array key|Trying to access array offset on)/',
            $errstr
        )) {
            return; // suppresses this error
        }

        if ($this->allowDereferencingNonObjects && preg_match(
                '/^Attempt to read property ".+?" on/',
                $errstr
            )) {
            return; // suppresses this error
        }

        // pass all other errors through to the previous error handler or to the default PHP error handler
        return $this->previousErrorHandler ?
            call_user_func($this->previousErrorHandler, $errno, $errstr, $errfile, $errline, $errcontext) : false;
    }
}
