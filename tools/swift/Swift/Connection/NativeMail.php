<?php

/**
 * Swift Mailer mail() connection component
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Connection
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_ConnectionBase");
Swift_ClassLoader::load("Swift_Plugin_MailSend");

/**
 * Swift mail() Connection
 * NOTE: This class is nothing more than a stub.  The MailSend plugin does the actual sending.
 * @package Swift_Connection
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Connection_NativeMail extends Swift_ConnectionBase
{
  /**
   * The response the stub will be giving next
   * @var string Response
   */
  protected $response = "220 Stubbed";
  /**
   * The 5th parameter in mail() is a sprintf() formatted string.
   * @var string
   */
  protected $pluginParams;
  /**
   * An instance of the MailSend plugin.
   * @var Swift_Plugin_MailSend
   */
  protected $plugin = null;
  
  /**
   * Ctor.
   * @param string The 5th parameter in mail() as a sprintf() formatted string where %s is the sender address. This only comes into effect if safe_mode is OFF.
   */
  public function __construct($additional_params="-oi -f %s")
  {
    $this->setAdditionalMailParams($additional_params);
  }
  /**
   * Sets the MailSend plugin in Swift once Swift has connected
   * @param Swift The current instance of Swift
   */
  public function postConnect(Swift $instance)
  {
    $this->plugin = new Swift_Plugin_MailSend($this->getAdditionalMailParams());
    $instance->attachPlugin($this->plugin, "_MAIL_SEND");
  }
  /**
   * Set the 5th parameter in mail() as a sprintf() formatted string. Only used if safe_mode is off.
   * @param string
   */
  public function setAdditionalMailParams($params)
  {
    $this->pluginParams = $params;
    if ($this->plugin !== null)
    {
      $this->plugin->setAdditionalParams($params);
    }
  }
  /**
   * Get the 5th parameter in mail() as a sprintf() formatted string.
   * @return string
   */
  public function getAdditionalMailParams()
  {
    return $this->pluginParams;
  }
  /**
   * Read a full response from the buffer (this is spoofed if running in -t mode)
   * @return string
   * @throws Swift_ConnectionException Upon failure to read
   */
  public function read()
  {
    return $this->response;
  }
  /**
   * Set the response this stub will return
   * @param string The response to send
   */
  public function setResponse($int)
  {
    $this->response = $int . " Stubbed";
  }
  /**
   * Write a command to the process (leave off trailing CRLF)
   * @param string The command to send
   * @throws Swift_ConnectionException Upon failure to write
   */
  public function write($command, $end="\r\n")
  {
    $command = strtoupper($command);
    if (strpos($command, " ")) $command = substr($command, 0, strpos($command, " "));
    switch ($command)
    {
      case "DATA":
        $this->setResponse(354);
        break;
      case "EHLO": case "MAIL": case "RCPT": case "QUIT": case "RSET": default:
        $this->setResponse(250);
        break;
    }
  }
  /**
   * Try to start the connection
   * @throws Swift_ConnectionException Upon failure to start
   */
  public function start()
  {
    $this->response = "220 Stubbed";
  }
  /**
   * Try to close the connection
   * @throws Swift_ConnectionException Upon failure to close
   */
  public function stop()
  {
    $this->response = "220 Stubbed";
  }
  /**
   * Check if the process is still alive
   * @return boolean
   */
  public function isAlive()
  {
    return function_exists("mail");
  }
}
