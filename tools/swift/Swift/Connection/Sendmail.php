<?php

/**
 * Swift Mailer Sendmail Connection component.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */
 
require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_ConnectionBase");

/**
 * Swift Sendmail Connection
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Connection_Sendmail extends Swift_ConnectionBase
{
  /**
   * Constant for auto-detection of paths
   */
  const AUTO_DETECT = -2;
  /**
   * Flags for the MTA (options such as bs or t)
   * @var string
   */
  protected $flags = null;
  /**
   * The full path to the MTA
   * @var string
   */
  protected $path = null;
  /**
   * The type of last request sent
   * For example MAIL, RCPT, DATA
   * @var string
   */
  protected $request = null;
  /**
   * The process handle
   * @var resource
   */
  protected $proc;
  /**
   * I/O pipes for the process
   * @var array
   */
  protected $pipes;
  /**
   * Switches to true for just one command when DATA has been issued
   * @var boolean
   */
  protected $send = false;
  /**
   * The timeout in seconds before giving up
   * @var int Seconds
   */
  protected $timeout = 10;
  
  /**
   * Constructor
   * @param string The command to execute
   * @param int The timeout in seconds before giving up
   */
  public function __construct($command="/usr/sbin/sendmail -bs", $timeout=10)
  {
    $this->setCommand($command);
    $this->setTimeout($timeout);
  }
  /**
   * Set the timeout on the process
   * @param int The number of seconds
   */
  public function setTimeout($secs)
  {
    $this->timeout = (int)$secs;
  }
  /**
   * Get the timeout on the process
   * @return int
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Set the operating flags for the MTA
   * @param string
   */
  public function setFlags($flags)
  {
    $this->flags = $flags;
  }
  /**
   * Get the operating flags for the MTA
   * @return string
   */
  public function getFlags()
  {
    return $this->flags;
  }
  /**
   * Set the path to the binary
   * @param string The path (must be absolute!)
   */
  public function setPath($path)
  {
    if ($path == self::AUTO_DETECT) $path = $this->findSendmail();
    $this->path = $path;
  }
  /**
   * Get the path to the binary
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * For auto-detection of sendmail path
   * Thanks to "Joe Cotroneo" for providing the enhancement
   * @return string
   */
  public function findSendmail()
  {
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Sendmail path auto-detection in progress.  Trying `which sendmail`");
    }
    $path = @trim(shell_exec('which sendmail'));
    if (!is_executable($path))
    {
      if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
      {
        $log->add("No luck so far, trying some common paths...");
      }
      $common_locations = array(
        '/usr/bin/sendmail',
        '/usr/lib/sendmail',
        '/var/qmail/bin/sendmail',
        '/bin/sendmail',
        '/usr/sbin/sendmail',
        '/sbin/sendmail'
      );
      foreach ($common_locations as $path)
      {
        if (is_executable($path)) return $path;
      }
      if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
      {
        $log->add("Falling back to /usr/sbin/sendmail (but it doesn't look good)!");
      }
      //Fallback (swift will still throw an error)
      return "/usr/sbin/sendmail";
    }
    else return $path;
  }
  /**
   * Set the sendmail command (path + flags)
   * @param string Command
   * @throws Swift_ConnectionException If the command is not correctly structured
   */
  public function setCommand($command)
  {
    if ($command == self::AUTO_DETECT) $command = $this->findSendmail() . " -bs";
    
    if (!strrpos($command, " -"))
    {
      throw new Swift_ConnectionException("Cannot set sendmail command with no command line flags. e.g. /usr/sbin/sendmail -t");
    }
    $path = substr($command, 0, strrpos($command, " -"));
    $flags = substr($command, strrpos($command, " -")+2);
    $this->setPath($path);
    $this->setFlags($flags);
  }
  /**
   * Get the sendmail command (path + flags)
   * @return string
   */
  public function getCommand()
  {
    return $this->getPath() . " -" . $this->getFlags();
  }
  /**
   * Write a command to the open pipe
   * @param string The command to write
   * @throws Swift_ConnectionException If the pipe cannot be written to
   */
  protected function pipeIn($command, $end="\r\n")
  {
    if (!$this->isAlive()) throw new Swift_ConnectionException("The sendmail process is not alive and cannot be written to.");
    if (!@fwrite($this->pipes[0], $command . $end)  && !empty($command)) throw new Swift_ConnectionException("The sendmail process did not allow the command '" . $command . "' to be sent.");
    fflush($this->pipes[0]);
  }
  /**
   * Read data from the open pipe
   * @return string
   * @throws Swift_ConnectionException If the pipe is not operating as expected
   */
  protected function pipeOut()
  {
    if (strpos($this->getFlags(), "t") !== false) return;
    if (!$this->isAlive()) throw new Swift_ConnectionException("The sendmail process is not alive and cannot be read from.");
    $ret = "";
    $line = 0;
    while (true)
    {
      $line++;
      stream_set_timeout($this->pipes[1], $this->timeout);
      $tmp = @fgets($this->pipes[1]);
      if ($tmp === false)
      {
        throw new Swift_ConnectionException("There was a problem reading line " . $line . " of a sendmail SMTP response. The response so far was:<br />[" . $ret . "].  It appears the process has died.");
      }
      $ret .= trim($tmp) . "\r\n";
      if ($tmp{3} == " ") break;
    }
    fflush($this->pipes[1]);
    return $ret = substr($ret, 0, -2);
  }
  /**
   * Read a full response from the buffer (this is spoofed if running in -t mode)
   * @return string
   * @throws Swift_ConnectionException Upon failure to read
   */
  public function read()
  {
    if (strpos($this->getFlags(), "t") !== false)
    {
      switch (strtolower($this->request))
      {
        case null:
          return "220 Greetings";
        case "helo": case "ehlo":
          return "250 hello";
        case "mail": case "rcpt": case "rset":
          return "250 ok";
        case "quit":
          return "221 bye";
        case "data":
          $this->send = true;
          return "354 go ahead";
        default:
          return "250 ok";
      }
    }
    else return $this->pipeOut();
  }
  /**
   * Write a command to the process (leave off trailing CRLF)
   * @param string The command to send
   * @throws Swift_ConnectionException Upon failure to write
   */
  public function write($command, $end="\r\n")
  {
    if (strpos($this->getFlags(), "t") !== false)
    {
      if (!$this->send && strpos($command, " ")) $command = substr($command, strpos($command, " ")+1);
      elseif ($this->send)
      {
        $this->pipeIn($command);
      }
      $this->request = $command;
      $this->send = (strtolower($command) == "data");
    }
    else $this->pipeIn($command, $end);
  }
  /**
   * Try to start the connection
   * @throws Swift_ConnectionException Upon failure to start
   */
  public function start()
  {
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Trying to start a sendmail process.");
    }
    if (!$this->getPath() || !$this->getFlags())
    {
      throw new Swift_ConnectionException("Sendmail cannot be started without a path to the binary including flags.");
    }
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Trying to stat the executable '" . $this->getPath() . "'.");
    }
    if (!@lstat($this->getPath()))
    {
      throw new Swift_ConnectionException(
        "Sendmail cannot be seen with lstat().  The command given [" . $this->getCommand() . "] does not appear to be valid.");
    }
    
    $pipes_spec = array(
      array("pipe", "r"),
      array("pipe", "w"),
      array("pipe", "w")
    );
    
    $this->proc = proc_open($this->getCommand(), $pipes_spec, $this->pipes);

    if (!$this->isAlive())
    {
      throw new Swift_ConnectionException(
      "The sendmail process failed to start.  Please verify that the path exists and PHP has permission to execute it.");
    }
  }
  /**
   * Try to close the connection
   */
  public function stop()
  {
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Terminating sendmail process.");
    }
    foreach ((array)$this->pipes as $pipe)
    {
      @fclose($pipe);
    }
    
    if ($this->proc)
    {
      proc_close($this->proc);
      $this->pipes = null;
      $this->proc = null;
    }
  }
  /**
   * Check if the process is still alive
   * @return boolean
   */
  public function isAlive()
  {
    return ($this->proc !== false
      && is_resource($this->proc)
      && is_resource($this->pipes[0])
      && is_resource($this->pipes[1])
      && $this->proc !== null);
  }
  /**
   * Destructor.
   * Cleans up by stopping any running processes.
   */
  public function __destruct()
  {
    $this->stop();
  }
}
