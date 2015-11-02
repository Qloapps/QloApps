<?php

/**
 * Swift Mailer Multiple Redundant Connection component.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */
 
require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_ConnectionBase");

/**
 * Swift Multi Connection
 * Tries to connect to a number of connections until one works successfully
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Connection_Multi extends Swift_ConnectionBase
{
  /**
   * The list of available connections
   * @var array
   */
  protected $connections = array();
  /**
   * The id of the active connection
   * @var string
   */
  protected $active = null;
  
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
   * @param string An ID to assign to the connection
   */
  public function addConnection(Swift_Connection $connection, $id=null)
  {
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Adding new connection of type '" . get_class($connection) . "' to the multi-redundant connection.");
    }
    if ($id !== null) $this->connections[$id] = $connection;
    else $this->connections[] = $connection;
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
    $log = Swift_LogContainer::getLog();
    $fail_messages = array();
    foreach ($this->connections as $id => $conn)
    {
      try {
        $this->connections[$id]->start();
        if ($this->connections[$id]->isAlive())
        {
          $this->active = $id;
          return true;
        }
        else
        {
          if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
          {
            $log->add("Connection (" . $id . ") failed. Will try next connection if available.");
          }
          throw new Swift_ConnectionException("The connection started but reported that it was not active");
        }
      } catch (Swift_ConnectionException $e) {
        $fail_messages[] = $id . ": " . $e->getMessage();
      }
    }
    $failure = implode("<br />", $fail_messages);
    throw new Swift_ConnectionException($failure);
  }
  /**
   * Try to close the connection
   * @throws Swift_ConnectionException Upon failure to close
   */
  public function stop()
  {
    if ($this->active !== null) $this->connections[$this->active]->stop();
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
   * Call the current connection's postConnect() method
   */
  public function postConnect(Swift $instance)
  {
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
