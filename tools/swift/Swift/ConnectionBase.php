<?php

/**
 * Swift Mailer Connection Base Class
 * All connection handlers extend this abstract class
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_LogContainer");
Swift_ClassLoader::load("Swift_Connection");
Swift_ClassLoader::load("Swift_ConnectionException");

/**
 * Swift Connection Base Class
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
abstract class Swift_ConnectionBase implements Swift_Connection
{
  /**
   * Any extensions the server might support
   * @var array
   */
  protected $extensions = array();
  /**
   * True if the connection is ESMTP.
   * @var boolean
   */
  protected $isESMTP = false;
  
  /**
   * Set an extension which the connection reports to support
   * @param string Extension name
   * @param array Attributes of the extension
   */
  public function setExtension($name, $options=array())
  {
    $this->extensions[$name] = $options;
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("SMTP extension '" . $name . "' reported with attributes [" . implode(", ", $options) . "].");
    }
  }
  /**
   * Check if a given extension has been set as available
   * @param string The name of the extension
   * @return boolean
   */
  public function hasExtension($name)
  {
    return array_key_exists($name, $this->extensions);
  }
  /**
   * Execute any needed logic after connecting and handshaking
   */
  public function postConnect(Swift $instance) {}
  /**
   * Get the list of attributes supported by the given extension
   * @param string The name of the connection
   * @return array The list of attributes
   * @throws Swift_ConnectionException If the extension cannot be found
   */
  public function getAttributes($extension)
  {
    if ($this->hasExtension($extension))
    {
      return $this->extensions[$extension];
    }
    else
    {
      throw new Swift_ConnectionException(
      "Unable to locate any attributes for the extension '" . $extension . "' since the extension cannot be found. " .
      "Consider using hasExtension() to check.");
    }
  }
  /**
   * Returns TRUE if the connection needs a EHLO greeting.
   * @return boolean
   */
  public function getRequiresEHLO()
  {
    return $this->isESMTP;
  }
  /**
   * Set TRUE if the connection needs a EHLO greeting.
   * @param boolean
   */
  public function setRequiresEHLO($set)
  {
    $this->isESMTP = (bool) $set;
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Forcing ESMTP mode.  HELO is EHLO.");
    }
  }
}
