<?php

/**
 * Swift Mailer Events Layer
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

/**
 * Provides core functionality for Swift generated events for plugins
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
abstract class Swift_Events
{
  /**
   * An instance of Swift
   * @var Swift
   */
  protected $swift = null;
  
  /**
   * Provide a reference to te currently running Swift this event was generated from
   * @param Swift
   */
  public function setSwift(Swift $swift)
  {
    $this->swift = $swift;
  }
  /**
   * Get the current instance of swift
   * @return Swift
   */
  public function getSwift()
  {
    return $this->swift;
  }
}
