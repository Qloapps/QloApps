<?php

/**
 * Swift Mailer Authenticator Interface
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Authenticator
 * @license GNU Lesser General Public License
 */

/**
 * Swift Authenticator Interface
 * Lists the methods all authenticators must implement
 * @package Swift_Authenticator
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
interface Swift_Authenticator
{
  /**
   * Try to authenticate using the username and password
   * Returns false on failure
   * @param string The username
   * @param string The password
   * @param Swift The instance of Swift this authenticator is used in
   * @return boolean
   */
  public function isAuthenticated($username, $password, Swift $instance);
  /**
   * Return the name of the AUTH extension this is for
   * @return string
   */
  public function getAuthExtensionName();
}
