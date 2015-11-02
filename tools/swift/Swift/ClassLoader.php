<?php

/**
 * Swift Mailer Class Loader for includes
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

if (!defined("SWIFT_ABS_PATH")) define("SWIFT_ABS_PATH", dirname(__FILE__) . "/..");

/**
 * Locates and includes class files
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_ClassLoader
{
  /**
   * A list of files already located
   * @var array
   */
  protected static $located = array();
  
  /**
   * Load a new class into memory
   * @param string The name of the class, case SenSItivE
   */
  public static function load($name)
  {
    if (in_array($name, self::$located) || class_exists($name, false) || interface_exists($name, false))
      return;
    
    require_once SWIFT_ABS_PATH . "/" . str_replace("_", "/", $name) . ".php";
    self::$located[] = $name;
  }
}
