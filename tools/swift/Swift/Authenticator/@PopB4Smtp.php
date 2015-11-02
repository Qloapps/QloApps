<?php

/**
 * Swift Mailer PopB4Smtp Authenticator Mechanism
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Authenticator
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Authenticator");
Swift_ClassLoader::load("Swift_LogContainer");

/**
 * Swift PopB4Smtp Authenticator
 * This form of authentication requires a quick connection to be made with a POP3 server before finally connecting to SMTP
 * @package Swift_Authenticator
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Authenticator_PopB4Smtp implements Swift_Authenticator
{
  protected $connection = null;
  /**
   * Constructor
   * @param mixed Swift_Authenticator_PopB4Smtp_Pop3Connection or string FQDN of POP3 server
   * @param int The remote port number
   * @param int The level of encryption to use
   */
  public function __construct($conn=null, $port=110, $encryption=0)
  {
    if (is_object($conn)) $this->connection = $conn;
    else
    {
      Swift_ClassLoader::load("Swift_Authenticator_PopB4Smtp_Pop3Connection");
      $this->connection = new Swift_Authenticator_PopB4Smtp_Pop3Connection($conn, $port, $encryption);
    }
  }
  /**
   * Try to authenticate using the username and password
   * Returns false on failure
   * @param string The username
   * @param string The password
   * @param Swift The instance of Swift this authenticator is used in
   * @return boolean
   */
  public function isAuthenticated($user, $pass, Swift $swift)
  {
    $log = Swift_LogContainer::getLog();
    if ($log->hasLevel(Swift_Log::LOG_EVERYTHING))
    {
      $log->add("Trying POP3 Before SMTP authentication.  Disconnecting from SMTP first.");
    }
    $swift->disconnect();
    try {
      $this->connection->start();
      $this->connection->assertOk($this->connection->read());
      $this->connection->write("USER " . $user);
      $this->connection->assertOk($this->connection->read());
      $this->connection->write("PASS " . $pass);
      $this->connection->assertOk($this->connection->read());
      $this->connection->write("QUIT");
      $this->connection->assertOk($this->connection->read());
      $this->connection->stop();
    } catch (Swift_ConnectionException $e) {
      if ($log->hasLevel(Swift_Log::LOG_ERRORS))
      {
        $log->add("POP3 authentication failed.");
      }
      return false;
    }
    $options = $swift->getOptions();
    $swift->setOptions($options | Swift::NO_POST_CONNECT);
    $swift->connect();
    $swift->setOptions($options);
    return true;
  }
  /**
   * Return the name of the AUTH extension this is for
   * @return string
   */
  public function getAuthExtensionName()
  {
    return "*PopB4Smtp";
  }
}
