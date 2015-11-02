<?php

/**
 * Swift Mailer Connection Interface
 * All connection handlers extend this abstract class
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */

/**
 * Swift Connection Interface
 * Lists methods which are required by any connections
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
interface Swift_Connection
{
  /**
   * Try to start the connection
   * @throws Swift_ConnectionException If the connection cannot be started
   */
  public function start();
  /**
   * Return the contents of the buffer
   * @return string
   * @throws Swift_ConnectionException If the buffer cannot be read
   */
  public function read();
  /**
   * Write a command to the buffer
   * @param string The command to send
   * @throws Swift_ConnectionException If the write fails
   */
  public function write($command, $end="\r\n");
  /**
   * Try to stop the connection
   * @throws Swift_ConnectionException If the connection cannot be closed/stopped
   */
  public function stop();
  /**
   * Check if the connection is up or not
   * @return boolean
   */
  public function isAlive();
  /**
   * Add an extension which is available on this connection
   * @param string The name of the extension
   * @param array The list of attributes for the extension
   */
  public function setExtension($name, $list=array());
  /**
   * Check if an extension exists by the name $name
   * @param string The name of the extension
   * @return boolean
   */
  public function hasExtension($name);
  /**
   * Get the list of attributes for the extension $name
   * @param string The name of the extension
   * @return array
   * @throws Swift_ConnectionException If no such extension can be found
   */
  public function getAttributes($name);
  /**
   * Execute logic needed after SMTP greetings
   * @param Swift An instance of Swift
   */
  public function postConnect(Swift $instance);
  /**
   * Returns TRUE if the connection needs a EHLO greeting.
   * @return boolean
   */
  public function getRequiresEHLO();
  /**
   * Set if the connection needs a EHLO greeting.
   * @param boolean
   */
  public function setRequiresEHLO($set);
}
