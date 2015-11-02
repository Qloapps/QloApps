<?php

/**
 * Swift Mailer Before Command Event Listener Interface
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */


/**
 * Contains the list of methods a plugin requiring the use of a CommandEvent, before it is sent must implement
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
interface Swift_Events_BeforeCommandListener extends Swift_Events_Listener
{
  /**
   * Executes just before Swift sends a command
   * @param Swift_Events_CommandEvent Information about the being command sent
   */
  public function beforeCommandSent(Swift_Events_CommandEvent $e);
}
