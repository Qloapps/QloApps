<?php

/**
 * Swift Mailer Verbose-sending Plugin View Layer.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Plugin
 * @subpackage VerboseSending
 * @license GNU Lesser General Public License
 */

/**
 * The View layer for the Verbose Sending Plugin
 * @package Swift_Plugin
 * @subpackage VerboseSending
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
abstract class Swift_Plugin_VerboseSending_AbstractView
{
  /**
   * Paint the result of a send operation
   * @param string The email address that was tried
   * @param boolean True if the message was successfully sent
   */
  abstract public function paintResult($address, $result);
}
