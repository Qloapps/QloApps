<?php

/**
 * A registry for the logger object.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Log
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_Log_DefaultLog");

/**
 * A registry holding the current instance of the log.
 * @package Swift_Log
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_LogContainer
{
  /**
   * The log instance.
   * @var Swift_Log
   */
  protected static $log = null;
  
  /**
   * Registers the logger.
   * @param Swift_Log The log
   */
  public static function setLog(Swift_Log $log)
  {
    self::$log = $log;
  }
  /**
   * Returns the current instance of the log, or lazy-loads the default one.
   * @return Swift_Log
   */
  public static function getLog()
  {
    if (self::$log === null)
    {
      self::setLog(new Swift_Log_DefaultLog());
    }
    return self::$log;
  }
}
