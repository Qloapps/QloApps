<?php

/**
 * Swift Mailer disk runtime cache
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Cache
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Cache");

/**
 * Caches data in files on disk - this is the best approach if possible
 * @package Swift_Cache
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Cache_Disk extends Swift_Cache
{
  /**
   * Open file handles
   * @var array
   */
  protected $open = array();
  /**
   * The prefix to prepend to files
   * @var string
   */
  protected $prefix;
  /**
   * The save path on disk
   * @var string
   */
  protected static $save_path = "/tmp";
  
  /**
   * Ctor
   */
  public function __construct()
  {
    $this->prefix = md5(uniqid(microtime(), true));
  }
  /**
   * Set the save path of the disk - this is a global setting and called statically!
   * @param string The path to a writable directory
   */
  public static function setSavePath($path="/tmp")
  {
    self::$save_path = realpath($path);
  }
  /**
   * Write data to the cache
   * @param string The cache key
   * @param string The data to write
   */
  public function write($key, $data)
  {
    $handle = fopen(self::$save_path . "/" . $this->prefix . $key, "ab");
    if (false === fwrite($handle, $data))
    {
      Swift_ClassLoader::load("Swift_FileException");
      throw new Swift_FileException("Disk Caching failed.  Tried to write to file at [" .
        self::$save_path . "/" . $this->prefix . $key . "] but failed.  Check the permissions, or don't use disk caching.");
    }
    fclose($handle);
  }
  /**
   * Clear the cached data (unlink)
   * @param string The cache key
   */
  public function clear($key)
  {
    @unlink(self::$save_path . "/" . $this->prefix . $key);
  }
  /**
   * Check if data is cached for $key
   * @param string The cache key
   * @return boolean
   */
  public function has($key)
  {
    return file_exists(self::$save_path . "/" . $this->prefix . $key);
  }
  /**
   * Read data from the cache for $key
   * @param string The cache key
   * @param int The number of bytes to read
   * @return string
   */
  public function read($key, $size=null)
  {
    if ($size === null) $size = 8190;
    if (!$this->has($key)) return false;
    
    if (!isset($this->open[$key]))
    {
      $this->open[$key] = fopen(self::$save_path . "/" . $this->prefix . $key, "rb");
    }
    if (feof($this->open[$key]))
    {
      fclose($this->open[$key]);
      unset($this->open[$key]);
      return false;
    }
    $ret = fread($this->open[$key], $size);
    if ($ret !== false)
    {
      return $ret;
    }
    else
    {
      fclose($this->open[$key]);
      unset($this->open[$key]);
      return false;
    }
  }
  /**
   * Dtor.
   * Clear out cached data at end of script execution or cache destruction
   */
  public function __destruct()
  {
    $list = glob(self::$save_path . "/" . $this->prefix . "*");
    foreach ((array)$list as $f)
    {
      @unlink($f);
    }
  }
}
