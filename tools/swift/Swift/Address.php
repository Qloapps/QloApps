<?php

/**
 * Swift Mailer Address Container (purely for rigid RFC conformance)
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_AddressContainer");

/**
 * Swift_Address is just a lone e-mail address reprsented as an object
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Address extends Swift_AddressContainer
{
  /**
   * The e-mail address portion
   * @var string
   */
  protected $address = null;
  /**
   * The personal name part
   * @var string
   */
  protected $name = null;
  
  /**
   * Constructor
   * @param string The address portion
   * @param string The personal name, optional
   */
  public function __construct($address, $name=null)
  {
    $this->setAddress($address);
    if ($name !== null) $this->setName($name);
  }
  /**
   * Set the email address
   * @param string
   */
  public function setAddress($address)
  {
    $this->address = trim((string)$address);
  }
  /**
   * Get the address portion
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Set the personal name
   * @param string
   */
  public function setName($name)
  {
    if ($name !== null) $this->name = (string) $name;
    else $this->name = null;
  }
  /**
   * Get personal name portion
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Build the address the way it should be structured
   * @param boolean If the string will be sent to a SMTP server as an envelope
   * @return string
   */
  public function build($smtp=false)
  {
    if ($smtp)
    {
      return "<" . $this->address . ">";
    }
    else
    {
      if (($this->name !== null))
      {
        return $this->name . " <" . $this->address . ">";
      }
      else return $this->address;
    }
  }
  /**
   * PHP's casting conversion
   * @return string
   */
  public function __toString()
  {
    return $this->build(true);
  }
}
