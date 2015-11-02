<?php

/**
 * Swift Mailer Response Event Listener Interface
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */


/**
 * Contains the list of methods a plugin requiring the use of a ResponseEvent must implement
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
interface Swift_Events_ResponseListener extends Swift_Events_Listener
{
  /**
   * Executes when Swift receives a response
   * @param Swift_Events_ResponseEvent Information about the response
   */
  public function responseReceived(Swift_Events_ResponseEvent $e);
}
