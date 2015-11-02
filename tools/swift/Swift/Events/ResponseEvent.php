<?php

/**
 * Swift Mailer Response Event
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

/**
 * Generated when Swift receives a server response
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events_ResponseEvent extends Swift_Events
{
  /**
   * Contains the response received
   * @var string
   */
  protected $string = null;
  /**
   * Contains the response code
   * @var int
   */
  protected $code = null;
  
  /**
   * Constructor
   * @param string The received response
   */
  public function __construct($string)
  {
    $this->setString($string);
    $this->setCode(substr($string, 0, 3));
  }
  /**
   * Set the response received
   * @param string The response
   */
  public function setString($string)
  {
    $this->string = (string) $string;
  }
  /**
   * Get the received response
   * @return string
   */
  public function getString()
  {
    return $this->string;
  }
  /**
   * Set response code
   * @param int The response code
   */
  public function setCode($code)
  {
    $this->code = (int) $code;
  }
  /**
   * Get the response code
   * @return int
   */
  public function getCode()
  {
    return $this->code;
  }
}
