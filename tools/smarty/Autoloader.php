<?php
/**
 * Smarty Autoloader
 *
 * @package Smarty
 */


if (!defined('SMARTY_HELPER_FUNCTIONS_LOADED')) {
	include __DIR__ . '/functions.php';
}

/**
 * Smarty Autoloader
 *
 * @package Smarty
 * @author  Uwe Tews
 *             Usage:
 *                  require_once '...path/Autoloader.php';
 *                  Smarty_Autoloader::register();
 *             or
 *                  include '...path/bootstrap.php';
 *
 *                  $smarty = new Smarty();
 */
class Smarty_Autoloader
{
    /**
     * Filepath to Smarty root
     *
     * @var string
     */
    public static $SMARTY_DIR = null;

    /**
     * Filepath to Smarty internal plugins
     *
     * @var string
     */
    public static $SMARTY_SYSPLUGINS_DIR = null;

    /**
     * Array with Smarty core classes and their filename
     *
     * @var array
     */
    public static $rootClasses = array('smarty' => 'Smarty.class.php');

    /**
     * Registers Smarty_Autoloader backward compatible to older installations.
     *
     * @param bool $prepend Whether to prepend the autoloader or not.
     */
    public static function registerBC($prepend = false)
    {
        /**
         * register the class autoloader
         */
        if (!defined('SMARTY_SPL_AUTOLOAD')) {
            define('SMARTY_SPL_AUTOLOAD', 0);
        }
        if (SMARTY_SPL_AUTOLOAD
            && set_include_path(get_include_path() . PATH_SEPARATOR . SMARTY_SYSPLUGINS_DIR) !== false
        ) {
            $registeredAutoLoadFunctions = spl_autoload_functions();
            if (!isset($registeredAutoLoadFunctions[ 'spl_autoload' ])) {
                spl_autoload_register();
            }
        } else {
            self::register($prepend);
        }
    }

    /**
     * Registers Smarty_Autoloader as an SPL autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not.
     */
    public static function register($prepend = false)
    {
        self::$SMARTY_DIR = defined('SMARTY_DIR') ? SMARTY_DIR : __DIR__ . DIRECTORY_SEPARATOR;
        self::$SMARTY_SYSPLUGINS_DIR = defined('SMARTY_SYSPLUGINS_DIR') ? SMARTY_SYSPLUGINS_DIR :
            self::$SMARTY_DIR . 'sysplugins' . DIRECTORY_SEPARATOR;
        spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
    }

    /**
     * Handles auto loading of classes.
     *
     * @param string $class A class name.
     */
    public static function autoload($class)
    {
        if ($class[ 0 ] !== 'S' || strpos($class, 'Smarty') !== 0) {
            return;
        }
        $_class = smarty_strtolower_ascii($class);
        if (isset(self::$rootClasses[ $_class ])) {
            $file = self::$SMARTY_DIR . self::$rootClasses[ $_class ];
            if (is_file($file)) {
                include $file;
            }
        } else {
            $file = self::$SMARTY_SYSPLUGINS_DIR . $_class . '.php';
            if (is_file($file)) {
                include $file;
            }
        }
        return;
    }
}
