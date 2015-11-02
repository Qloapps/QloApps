<?php

/**
 * Swift Mailer Logging Layer Interface
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Log
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_LogContainer");

/**
 * The Logger Interface
 * @package Swift_Log
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Exception extends Exception
{
  /**
   * Constructor.
   * Creates the exception and appends log information if available.
   * @param string Message
   * @param int Code
   */
  public function __construct($message, $code = 0)
  {
    if (($log = Swift_LogContainer::getLog()) && $log->isEnabled())
    {
      $message .= "<h3>Log Information</h3>";
      $message .= "<pre>" . htmlentities($log->dump(true)) . "</pre>";
    }
    parent::__construct($message, $code);
  }
}
