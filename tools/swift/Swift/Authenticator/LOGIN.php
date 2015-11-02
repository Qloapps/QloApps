<?php

/**
 * Swift Mailer LOGIN Authenticator Mechanism
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Authenticator
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Authenticator");

/**
 * Swift LOGIN Authenticator
 * @package Swift_Authenticator
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Authenticator_LOGIN implements Swift_Authenticator
{
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
    try {
      $swift->command("AUTH LOGIN", 334);
      $swift->command(base64_encode($user), 334);
      $swift->command(base64_encode($pass), 235);
    } catch (Swift_ConnectionException $e) {
      $swift->reset();
      return false;
    }
    return true;
  }
  /**
   * Return the name of the AUTH extension this is for
   * @return string
   */
  public function getAuthExtensionName()
  {
    return "LOGIN";
  }
}
