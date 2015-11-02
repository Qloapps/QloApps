<?php

/**
 * Swift Mailer Rotating Connection Controller
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Plugin
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Events_SendListener");
Swift_ClassLoader::load("Swift_Events_DisconnectListener");

/**
 * Swift Rotating Connection Controller
 * Invokes the nextConnection() method of Swift_Connection_Rotator upon sending a given number of messages
 * @package Swift_Plugin
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Plugin_ConnectionRotator implements Swift_Events_SendListener, Swift_Events_DisconnectListener
{
  /**
   * The number of emails which must be sent before the connection is rotated
   * @var int Threshold number of emails
   */
  protected $threshold = 1;
  /**
   * The total number of emails sent on this connection
   * @var int
   */
  protected $count = 0;
  /**
   * The connections we have used thus far
   * @var array
   */
  protected $used = array();
  /**
   * Internal check to see if this plugin has yet been invoked
   * @var boolean
   */
  protected $called = false;
  
  /**
   * Constructor
   * @param int The number of emails to send before rotating
   */
  public function __construct($threshold=1)
  {
    $this->setThreshold($threshold);
  }
  /**
   * Set the number of emails to send before a connection rotation is tried
   * @param int Number of emails
   */
  public function setThreshold($threshold)
  {
    $this->threshold = (int) $threshold;
  }
  /**
   * Get the number of emails which must be sent before a rotation occurs
   * @return int
   */
  public function getThreshold()
  {
    return $this->threshold;
  }
  /**
   * Swift's SendEvent listener.
   * Invoked when Swift sends a message
   * @param Swift_Events_SendEvent The event information
   * @throws Swift_ConnectionException If the connection cannot be rotated
   */
  public function sendPerformed(Swift_Events_SendEvent $e)
  {
    if (!method_exists($e->getSwift()->connection, "nextConnection"))
    {
      throw new Swift_ConnectionException("The ConnectionRotator plugin cannot be used with connections other than Swift_Connection_Rotator.");
    }
    if (!$this->called)
    {
      $this->used[] = $e->getSwift()->connection->getActive();
    }
    $this->count++;
    if ($this->count >= $this->getThreshold())
    {
      $e->getSwift()->connection->nextConnection();
      if (!in_array(($id = $e->getSwift()->connection->getActive()), $this->used))
      {
        $e->getSwift()->connect();
        $this->used[] = $id;
      }
      $this->count = 0;
    }
    $this->called = true;
  }
  /**
   * Disconnect all the other connections
   * @param Swift_Events_DisconnectEvent The event info
   */
  public function disconnectPerformed(Swift_Events_DisconnectEvent $e)
  {
    $active = $e->getConnection()->getActive();
    $e->getConnection()->nextConnection();
    while ($e->getConnection()->getActive() != $active)
    {
      $e->getSwift()->command("QUIT", 221);
      $e->getConnection()->stop();
      $e->getConnection()->nextConnection();
    }
    $this->used = array();
  }
}
