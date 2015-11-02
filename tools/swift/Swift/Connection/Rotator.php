<?php

/**
 * Swift Mailer Multiple Redundant Cycling Connection component.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */
 
require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_ConnectionBase");

/**
 * Swift Rotator Connection
 * Switches through each connection in turn after sending each message
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Connection_Rotator extends Swift_ConnectionBase
{
  /**
   * The list of available connections
   * @var array
   */
  protected $connections = array();
  /**
   * The id of the active connection
   * @var int
   */
  protected $active = null;
  /**
   * Contains a list of any connections which were tried but found to be dead
   * @var array
   */
  protected $dead = array();
  
  /**
   * Constructor
   */
  public function __construct($connections=array())
  {
    foreach ($connections as $id => $conn)
    {
      $this->addConnection($connections[$id], $id);
    }
  }
  /**
   * Add a connection to the list of options
   * @param Swift_Connection An instance of the connection
   */
  public function addConnection(Swift_Connection $connection)
  {
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Adding new connection of type '" . get_class($connection) . "' to rotator.");
    }
    $this->connections[] = $connection;
  }
  /**
   * Rotate to the next working connection
   * @throws Swift_ConnectionException If no connections are available
   */
  public function nextConnection()
  {
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add(" <==> Rotating connection.");
    }
    
    $total = count($this->connections);
    $start = $this->active === null ? 0 : ($this->active + 1);
    if ($start >= $total) $start = 0;
    
    $fail_messages = array();
    for ($id = $start; $id < $total; $id++)
    {
      if (in_array($id, $this->dead)) continue; //The connection was previously found to be useless
      try {
        if (!$this->connections[$id]->isAlive()) $this->connections[$id]->start();
        if ($this->connections[$id]->isAlive())
        {
          $this->active = $id;
          return true;
        }
        else
        {
          $this->dead[] = $id;
          $this->connections[$id]->stop();
          throw new Swift_ConnectionException("The connection started but reported that it was not active");
        }
      } catch (Swift_ConnectionException $e) {
        $fail_messages[] = $id . ": " . $e->getMessage();
      }
    }
    
    $failure = implode("<br />", $fail_messages);
    throw new Swift_ConnectionException("No connections were started.<br />" . $failure);
  }
  /**
   * Read a full response from the buffer
   * @return string
   * @throws Swift_ConnectionException Upon failure to read
   */
  public function read()
  {
    if ($this->active === null)
    {
      throw new Swift_ConnectionException("None of the connections set have been started");
    }
    return $this->connections[$this->active]->read();
  }
  /**
   * Write a command to the server (leave off trailing CRLF)
   * @param string The command to send
   * @throws Swift_ConnectionException Upon failure to write
   */
  public function write($command, $end="\r\n")
  {
    if ($this->active === null)
    {
      throw new Swift_ConnectionException("None of the connections set have been started");
    }
    return $this->connections[$this->active]->write($command, $end);
  }
  /**
   * Try to start the connection
   * @throws Swift_ConnectionException Upon failure to start
   */
  public function start()
  {
    if ($this->active === null) $this->nextConnection();
  }
  /**
   * Try to close the connection
   * @throws Swift_ConnectionException Upon failure to close
   */
  public function stop()
  {
    foreach ($this->connections as $id => $conn)
    {
      if ($this->connections[$id]->isAlive()) $this->connections[$id]->stop();
    }
    $this->active = null;
  }
  /**
   * Check if the current connection is alive
   * @return boolean
   */
  public function isAlive()
  {
    return (($this->active !== null) && $this->connections[$this->active]->isAlive());
  }
  /**
   * Get the ID of the active connection
   * @return int
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Call the current connection's postConnect() method
   */
  public function postConnect(Swift $instance)
  {
    Swift_ClassLoader::load("Swift_Plugin_ConnectionRotator");
    if (!$instance->getPlugin("_ROTATOR")) $instance->attachPlugin(new Swift_Plugin_ConnectionRotator(), "_ROTATOR");
    $this->connections[$this->active]->postConnect($instance);
  }
  /**
   * Call the current connection's setExtension() method
   */
  public function setExtension($extension, $attributes=array())
  {
    $this->connections[$this->active]->setExtension($extension, $attributes);
  }
  /**
   * Call the current connection's hasExtension() method
   */
  public function hasExtension($name)
  {
    return $this->connections[$this->active]->hasExtension($name);
  }
  /**
   * Call the current connection's getAttributes() method
   */
  public function getAttributes($name)
  {
    return $this->connections[$this->active]->getAttributes($name);
  }
}
