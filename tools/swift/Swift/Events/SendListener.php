<?php

/**
 * Swift Mailer Send Event Listener Interface
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */

/**
 * Contains the list of methods a plugin requiring the use of a SendEvent must implement
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
interface Swift_Events_SendListener extends Swift_Events_Listener
{
  /**
   * Executes when Swift sends a message
   * @param Swift_Events_SendEvent Information about the message being sent
   */
  public function sendPerformed(Swift_Events_SendEvent $e);
}
