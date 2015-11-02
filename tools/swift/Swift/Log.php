<?php

/**
 * Swift Mailer Logging Layer base class.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Log
 * @license GNU Lesser General Public License
 */

/**
 * The Logger class/interface.
 * @package Swift_Log
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
abstract class Swift_Log
{
  /**
   * A command type entry
   */
  const COMMAND = ">>";
  /**
   * A response type entry
   */
  const RESPONSE = "<<";
  /**
   * An error type entry
   */
  const ERROR = "!!";
  /**
   * A standard entry
   */
  const NORMAL = "++";
  /**
   * Logging is off.
   */
  const LOG_NOTHING = 0;
  /**
   * Only errors are logged.
   */
  const LOG_ERRORS = 1;
  /**
   * Errors + sending failures.
   */
  const LOG_FAILURES = 2;
  /**
   * All SMTP instructions + failures + errors.
   */
  const LOG_NETWORK = 3;
  /**
   * Runtime info + SMTP instructions + failures + errors.
   */
  const LOG_EVERYTHING = 4;
  /**
   * Failed recipients
   * @var array
   */
  protected $failedRecipients = array();
  /**
   * The maximum number of log entries
   * @var int
   */
  protected $maxSize = 50;
  /**
   * The level of logging currently set.
   * @var int
   */
  protected $logLevel = self::LOG_NOTHING;
  
  /**
   * Add a new entry to the log
   * @param string The information to log
   * @param string The type of entry (see the constants: COMMAND, RESPONSE, ERROR, NORMAL)
   */
  abstract public function add($text, $type = self::NORMAL);
  /**
   * Dump the contents of the log to the browser.
   * @param boolean True if the string should be returned rather than output.
   */
  abstract public function dump($return_only=false);
  /**
   * Empty the log contents
   */
  abstract public function clear();
  /**
   * Check if logging is enabled.
   */
  public function isEnabled()
  {
    return ($this->logLevel > self::LOG_NOTHING);
  }
  /**
   * Add a failed recipient to the list
   * @param string The address of the recipient
   */
  public function addFailedRecipient($address)
  {
    $this->failedRecipients[$address] = null;
    $this->add("Recipient '" . $address . "' rejected by connection.", self::ERROR);
  }
  /**
   * Get the list of failed recipients
   * @return array
   */
  public function getFailedRecipients()
  {
    return array_keys($this->failedRecipients);
  }
  /**
   * Set the maximum size of this log (zero is no limit)
   * @param int The maximum entries
   */
  public function setMaxSize($size)
  {
    $this->maxSize = (int) $size;
  }
  /**
   * Get the current maximum allowed log size
   * @return int
   */
  public function getMaxSize()
  {
    return $this->maxSize;
  }
  /**
   * Set the log level to one of the constants provided.
   * @param int Level
   */
  public function setLogLevel($level)
  {
    $level = (int)$level;
    $this->add("Log level changed to " . $level, self::NORMAL);
    $this->logLevel = $level;
  }
  /**
   * Get the current log level.
   * @return int
   */
  public function getLogLevel()
  {
    return $this->logLevel;
  }
  /**
   * Check if the log level includes the one given.
   * @param int Level
   * @return boolean
   */
  public function hasLevel($level)
  {
    return ($this->logLevel >= ((int)$level));
  }
}
