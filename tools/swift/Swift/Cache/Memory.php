<?php

/**
 * Swift Mailer Native memory runtime cache
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Cache
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Cache");

/**
 * Caches data in variables - uses memory!
 * @package Swift_Cache
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Cache_Memory extends Swift_Cache
{
  /**
   * The storage container for this cache
   * @var array
   */
  protected $store = array();
  /**
   * The key which was last requested
   * @var string
   */
  protected $requested;
  
  /**
   * Write data to the cache
   * @param string The cache key
   * @param string The data to write
   */
  public function write($key, $data)
  {
    if (!isset($this->store[$key])) $this->store[$key] = $data;
    else $this->store[$key] .= $data;
  }
  /**
   * Clear the cached data (unset)
   * @param string The cache key
   */
  public function clear($key)
  {
    $this->store[$key] = null;
    unset($this->store[$key]);
  }
  /**
   * Check if data is cached for $key
   * @param string The cache key
   * @return boolean
   */
  public function has($key)
  {
    return array_key_exists($key, $this->store);
  }
  /**
   * Read data from the cache for $key
   * It makes no difference to memory/speed if data is read in chunks so arguments are ignored
   * @param string The cache key
   * @return string
   */
  public function read($key, $size=null)
  {
    if (!$this->has($key)) return false;
    
    if ($this->requested == $key)
    {
      $this->requested = null;
      return false;
    }
    $this->requested = $key;
    return $this->store[$key];
  }
}
