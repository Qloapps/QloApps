<?php

/**
 * Swift Mailer CRAM-MD5 Authenticator Mechanism
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Authenticator
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Authenticator");

/**
 * Swift CRAM-MD5 Authenticator
 * This form of authentication is a secure challenge-response method
 * @package Swift_Authenticator
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Authenticator_CRAMMD5 implements Swift_Authenticator
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
      $encoded_challenge = substr($swift->command("AUTH CRAM-MD5", 334)->getString(), 4);
      $challenge = base64_decode($encoded_challenge);
      $response = base64_encode($user . " " . self::generateCRAMMD5Hash($pass, $challenge));
      $swift->command($response, 235);
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
    return "CRAM-MD5";
  }
  /**
   * Generate a CRAM-MD5 hash from a challenge
   * @param string The string to get a hash from
   * @param string The challenge to use to make the hash
   * @return string
   */
  public static function generateCRAMMD5Hash($password, $challenge)
  {
    if (strlen($password) > 64)
      $password = pack('H32', md5($password));

    if (strlen($password) < 64)
      $password = str_pad($password, 64, chr(0));

    $k_ipad = substr($password, 0, 64) ^ str_repeat(chr(0x36), 64);
    $k_opad = substr($password, 0, 64) ^ str_repeat(chr(0x5C), 64);

    $inner  = pack('H32', md5($k_ipad.$challenge));
    $digest = md5($k_opad.$inner);

    return $digest;
  }
}
