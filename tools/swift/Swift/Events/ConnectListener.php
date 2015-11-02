<?php

/**
 * Swift Mailer Connect Event Listener Interface
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */


/**
 * Contains the list of methods a plugin requiring the use of a ConnectEvent must implement
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
interface Swift_Events_ConnectListener extends Swift_Events_Listener
{
  /**
   * Executes when Swift initiates a connection
   * @param Swift_Events_ConnectEvent Information about the connection
   */
  public function connectPerformed(Swift_Events_ConnectEvent $e);
}
