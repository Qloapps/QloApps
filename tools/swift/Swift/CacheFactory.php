<?php

/**
 * Swift Mailer Cache Factory class
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Cache
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";

/**
 * Makes instances of the cache the user has defined
 * @package Swift_Cache
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_CacheFactory
{
  /**
   * The name of the class which defines the cache
   * @var string Case SenSITivE
   */
  protected static $className = "Swift_Cache_Memory";
  
  /**
   * Set the name of the class which is supposed to be used
   * This also includes the file
   * @param string The class name
   */
  public static function setClassName($name)
  {
    Swift_ClassLoader::load($name);
    self::$className = $name;
  }
  /**
   * Return a new instance of the cache object
   * @return Swift_Cache
   */
  public static function getCache()
  {
    $className = self::$className;
    Swift_ClassLoader::load($className);
    $instance = new $className();
    return $instance;
  }
}
