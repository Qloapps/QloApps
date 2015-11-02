<?php

/**
 * Swift Mailer Runtime caching base component.
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Cache
 * @license GNU Lesser General Public License
 */
 
require_once dirname(__FILE__) . "/ClassLoader.php";

/**
 * The interface for any cache mechanisms to follow
 * @package Swift_Cache
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
abstract class Swift_Cache
{
  /**
   * Append bytes to the cache buffer identified by $key
   * @param string The Cache key
   * @param string The bytes to append
   */
  abstract public function write($key, $data);
  /**
   * Clear out the buffer for $key
   * @param string The cache key
   */
  abstract public function clear($key);
  /**
   * Check if there is something in the cache for $key
   * @param string The cache key
   * @return boolean
   */
  abstract public function has($key);
  /**
   * Read bytes from the cached buffer and seek forward in the buffer
   * Returns false once no more bytes are left to read
   * @param int The number of bytes to read (may be ignored)
   * @return string
   */
  abstract public function read($key, $size=null);
  /**
   * A factory method to return an output stream object for the relevant location in the cache
   * @param string The cache key to fetch the stream for
   * @return Swift_Cache_OutputStream
   */
  public function getOutputStream($key)
  {
    Swift_ClassLoader::load("Swift_Cache_OutputStream");
    $os = new Swift_Cache_OutputStream($this, $key);
    return $os;
  }
}
