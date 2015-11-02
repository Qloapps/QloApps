<?php

/**
 * Swift Mailer SMTP Connection component.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_ConnectionBase");
Swift_ClassLoader::load("Swift_Authenticator");

/**
 * Swift SMTP Connection
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Connection_SMTP extends Swift_ConnectionBase
{
  /**
   * Constant for TLS connections
   */
  const ENC_TLS = 2;
  /**
   * Constant for SSL connections
   */
  const ENC_SSL = 4;
  /**
   * Constant for unencrypted connections
   */
  const ENC_OFF = 8;
  /**
   * Constant for the default SMTP port
   */
  const PORT_DEFAULT = 25;
  /**
   * Constant for the default secure SMTP port
   */
  const PORT_SECURE = 465;
  /**
   * Constant for auto-detection of paramters
   */
  const AUTO_DETECT = -2;
  /**
   * A connection handle
   * @var resource
   */
  protected $handle = null;
  /**
   * The remote port number
   * @var int
   */
  protected $port = null;
  /**
   * Encryption type to use
   * @var int
   */
  protected $encryption = null;
  /**
   * A connection timeout
   * @var int
   */
  protected $timeout = 15;
  /**
   * A username to authenticate with
   * @var string
   */
  protected $username = false;
  /**
   * A password to authenticate with
   * @var string
   */
  protected $password = false;
  /**
   * Loaded authentication mechanisms
   * @var array
   */
  protected $authenticators = array();
  /**
   * Fsockopen() error codes.
   * @var int
   */
  protected $errno;
  /**
   * Fsockopen() error codes.
   * @var string
   */
  protected $errstr;
  
  /**
   * Constructor
   * @param string The remote server to connect to
   * @param int The remote port to connect to
   * @param int The encryption level to use
   */
  public function __construct($server="localhost", $port=null, $encryption=null)
  {
    $this->setServer($server);
    $this->setEncryption($encryption);
    $this->setPort($port);
  }
  /**
   * Set the timeout to connect in seconds
   * @param int Timeout to use
   */
  public function setTimeout($time)
  {
    $this->timeout = (int) $time;
  }
  /**
   * Get the timeout currently set for connecting
   * @return int
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Set the remote server to connect to as a FQDN
   * @param string Server name
   */
  public function setServer($server)
  {
    if ($server == self::AUTO_DETECT)
    {
      $server = @ini_get("SMTP");
      if (!$server) $server = "localhost";
    }
    $this->server = (string) $server;
  }
  /**
   * Get the remote server name
   * @return string
   */
  public function getServer()
  {
    return $this->server;
  }
  /**
   * Set the remote port number to connect to
   * @param int Port number
   */
  public function setPort($port)
  {
    if ($port == self::AUTO_DETECT)
    {
      $port = @ini_get("SMTP_PORT");
    }
    if (!$port) $port = ($this->getEncryption() == self::ENC_OFF) ? self::PORT_DEFAULT : self::PORT_SECURE;
    $this->port = (int) $port;
  }
  /**
   * Get the remote port number currently used to connect
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Provide a username for authentication
   * @param string The username
   */
  public function setUsername($user)
  {
    $this->setRequiresEHLO(true);
    $this->username = $user;
  }
  /**
   * Get the username for authentication
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
  /**
   * Set the password for SMTP authentication
   * @param string Password to use
   */
  public function setPassword($pass)
  {
    $this->setRequiresEHLO(true);
    $this->password = $pass;
  }
  /**
   * Get the password for authentication
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * Add an authentication mechanism to authenticate with
   * @param Swift_Authenticator
   */
  public function attachAuthenticator(Swift_Authenticator $auth)
  {
    $this->authenticators[$auth->getAuthExtensionName()] = $auth;
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Authentication mechanism '" . $auth->getAuthExtensionName() . "' attached.");
    }
  }
  /**
   * Set the encryption level to use on the connection
   * See the constants ENC_TLS, ENC_SSL and ENC_OFF
   * NOTE: PHP needs to have been compiled with OpenSSL for SSL and TLS to work
   * NOTE: Some PHP installations will not have the TLS stream wrapper
   * @param int Level of encryption
   */
  public function setEncryption($enc)
  {
    if (!$enc) $enc = self::ENC_OFF;
    $this->encryption = (int) $enc;
  }
  /**
   * Get the current encryption level used
   * This method returns an integer corresponding to one of the constants ENC_TLS, ENC_SSL or ENC_OFF
   * @return int
   */
  public function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * Read a full response from the buffer
   * inner !feof() patch provided by Christian Rodriguez:
   * <a href="http://www.flyspray.org/">www.flyspray.org</a>
   * @return string
   * @throws Swift_ConnectionException Upon failure to read
   */
  public function read()
  {
    if (!$this->handle) throw new Swift_ConnectionException(
      "The SMTP connection is not alive and cannot be read from."  . $this->smtpErrors());
    $ret = "";
    $line = 0;
    while (!feof($this->handle))
    {
      $line++;
      stream_set_timeout($this->handle, $this->timeout);
      $tmp = @fgets($this->handle);
      if ($tmp === false && !feof($this->handle))
      {
        throw new Swift_ConnectionException(
        "There was a problem reading line " . $line . " of an SMTP response. The response so far was:<br />[" . $ret .
        "].  It appears the connection has died without saying goodbye to us! Too many emails in one go perhaps?"  .
        $this->smtpErrors());
      }
      $ret .= trim($tmp) . "\r\n";
      if ($tmp{3} == " ") break;
    }
    return $ret = substr($ret, 0, -2);
  }
  /**
   * Write a command to the server (leave off trailing CRLF)
   * @param string The command to send
   * @throws Swift_ConnectionException Upon failure to write
   */
  public function write($command, $end="\r\n")
  {
    if (!$this->handle) throw new Swift_ConnectionException(
      "The SMTP connection is not alive and cannot be written to."  .
      $this->smtpErrors());
    if (!@fwrite($this->handle, $command . $end) && !empty($command)) throw new Swift_ConnectionException("The SMTP connection did not allow the command '" . $command . "' to be sent." . $this->smtpErrors());
  }
  /**
   * Try to start the connection
   * @throws Swift_ConnectionException Upon failure to start
   */
  public function start()
  {
    if ($this->port === null)
    {
      switch ($this->encryption)
      {
        case self::ENC_TLS: case self::ENC_SSL:
          $this->port = 465;
        break;
        case null: default:
          $this->port = 25;
        break;
      }
    }
    
    $server = $this->server;
    if ($this->encryption == self::ENC_TLS) $server = "tls://" . $server;
    elseif ($this->encryption == self::ENC_SSL) $server = "ssl://" . $server;
    
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_log::LOG_EVERYTHING))
    {
      $log->add("Trying to connect to SMTP server at '" . $server . ":" . $this->port);
    }
    
    if (!$this->handle = @fsockopen($server, $this->port, $errno, $errstr, $this->timeout))
    {
      $error_msg = "The SMTP connection failed to start [" . $server . ":" . $this->port . "]: fsockopen returned Error Number " . $errno . " and Error String '" . $errstr . "'";
      if ($log->isEnabled())
      {
        $log->add($error_msg, Swift_Log::ERROR);
      }
      $this->handle = null;
      throw new Swift_ConnectionException($error_msg);
    }
    $this->errno =& $errno;
    $this->errstr =& $errstr;
  }
  /**
   * Get the smtp error string as recorded by fsockopen()
   * @return string
   */
  public function smtpErrors()
  {
    return " (fsockopen: " . $this->errstr . "#" . $this->errno . ") ";
  }
  /**
   * Authenticate if required to do so
   * @param Swift An instance of Swift
   * @throws Swift_ConnectionException If authentication fails
   */
  public function postConnect(Swift $instance)
  {
    if ($this->getUsername() && $this->getPassword())
    {
      $this->runAuthenticators($this->getUsername(), $this->getPassword(), $instance);
    }
  }
  /**
   * Run each authenticator in turn an try for a successful login
   * If none works, throw an exception
   * @param string Username
   * @param string Password
   * @param Swift An instance of swift
   * @throws Swift_ConnectionException Upon failure to authenticate
   */
  public function runAuthenticators($user, $pass, Swift $swift)
  {
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Trying to authenticate with username '" . $user . "'.");
    }
    //Load in defaults
    if (empty($this->authenticators))
    {
      if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
      {
        $log->add("No authenticators loaded; looking for defaults.");
      }
      $dir = dirname(__FILE__) . "/../Authenticator";
      $handle = opendir($dir);
      while (false !== $file = readdir($handle))
      {
		if (preg_match("/^[A-Za-z0-9-]+\\.php\$/", $file) && $file != "index.php")
        {
          $name = preg_replace('/[^a-zA-Z0-9]+/', '', substr($file, 0, -4));
          require_once $dir . "/" . $file;
          $class = "Swift_Authenticator_" . $name;
          $this->attachAuthenticator(new $class());
        }
      }
      closedir($handle);
    }
    
    $tried = 0;
    $looks_supported = true;
    
    //Allow everything we have if the server has the audacity not to help us out.
    if (!$this->hasExtension("AUTH"))
    {
      if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
      {
        $log->add("Server (perhaps wrongly) is not advertising AUTH... manually overriding.");
      }
      $looks_supported = false;
      $this->setExtension("AUTH", array_keys($this->authenticators));
    }
    
    foreach ($this->authenticators as $name => $obj)
    {
      //Server supports this authentication mechanism
      if (in_array($name, $this->getAttributes("AUTH")) || $name{0} == "*")
      {
        $tried++;
        if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
        {
          $log->add("Trying '" . $name . "' authentication...");
        }
        if ($this->authenticators[$name]->isAuthenticated($user, $pass, $swift))
        {
          if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
          {
            $log->add("Success! Authentication accepted.");
          }
          return true;
        }
      }
    }
    
    //Server doesn't support authentication
    if (!$looks_supported && $tried == 0)
      throw new Swift_ConnectionException("Authentication is not supported by the server but a username and password was given.");
    
    if ($tried == 0)
      throw new Swift_ConnectionException("No authentication mechanisms were tried since the server did not support any of the ones loaded. " .
      "Loaded authenticators: [" . implode(", ", array_keys($this->authenticators)) . "]");
    else
      throw new Swift_ConnectionException("Authentication failed using username '" . $user . "' and password '". str_repeat("*", strlen($pass)) . "'");
  }
  /**
   * Try to close the connection
   * @throws Swift_ConnectionException Upon failure to close
   */
  public function stop()
  {
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Closing down SMTP connection.");
    }
    if ($this->handle)
    {
      if (!fclose($this->handle))
      {
        throw new Swift_ConnectionException("The SMTP connection could not be closed for an unknown reason." . $this->smtpErrors());
      }
      $this->handle = null;
    }
  }
  /**
   * Check if the SMTP connection is alive
   * @return boolean
   */
  public function isAlive()
  {
    return ($this->handle !== null);
  }
  /**
   * Destructor.
   * Cleans up any open connections.
   */
  public function __destruct()
  {
    $this->stop();
  }
}
