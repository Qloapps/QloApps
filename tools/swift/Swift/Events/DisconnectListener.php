<?php

/**
 * Swift Mailer Disconnect Event Listener Interface
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */


/**
 * Contains the list of methods a plugin requiring the use of a DisconnectEvent must implement
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
interface Swift_Events_DisconnectListener extends Swift_Events_Listener
{
  /**
   * Executes when Swift closes a connection
   * @param Swift_Events_DisconnectEvent Information about the connection
   */
  public function disconnectPerformed(Swift_Events_DisconnectEvent $e);
}
