<?php

/**
 * Swift Mailer Output stream to read bytes from cached data
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Cache
 * @license GNU Lesser General Public License
 */

/**
 * The wraps the streaming functionality of the cache
 * @package Swift_Cache
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Cache_OutputStream
{
  /**
   * The key to read in the actual cache
   * @var string
   */
  protected $key;
  /**
   * The cache object to read
   * @var Swift_Cache
   */
  protected $cache;
  
  /**
   * Ctor.
   * @param Swift_Cache The cache to read from
   * @param string The key for the cached data
   */
  public function __construct(Swift_Cache $cache, $key)
  {
    $this->cache = $cache;
    $this->key = $key;
  }
  /**
   * Read bytes from the cache and seek through the buffer
   * Returns false if EOF is reached
   * @param int The number of bytes to read (could be ignored)
   * @return string The read bytes
   */
  public function read($size=null)
  {
    return $this->cache->read($this->key, $size);
  }
  /**
   * Read the entire cached data as one string
   * @return string
   */
  public function readFull()
  {
    $ret = "";
    while (false !== $bytes = $this->read())
      $ret .= $bytes;
    return $ret;
  }
}
