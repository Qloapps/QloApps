<?php

/**
 * Swift Mailer Disconnect Event
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Events
 * @license GNU Lesser General Public License
 */


/**
 * Generated every time Swift disconnects from a MTA
 * @package Swift_Events
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Events_DisconnectEvent extends Swift_Events
{
  /**
   * A reference to the connection object
   * @var Swift_Connection
   */
  protected $connection = null;
  
  /**
   * Constructor
   * @param Swift_Connection The dead connection
   */
  public function __construct(Swift_Connection $connection)
  {
    $this->connection = $connection;
  }
  /**
   * Get the connection object
   * @return Swift_Connection
   */
  public function getConnection()
  {
    return $this->connection;
  }
}
