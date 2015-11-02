<?php

/**
 * Swift Mailer Joint Output stream to chain multiple output streams together
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Cache
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Cache_OutputStream");

/**
 * Makes multiple output streams act as one super sream
 * @package Swift_Cache
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Cache_JointOutputStream extends Swift_Cache_OutputStream
{
  /**
   * The streams to join
   * @var array
   */
  protected $streams = array();
  /**
   * The current stream in use
   * @var int
   */
  protected $pointer = 0;
  
  /**
   * Ctor
   * @param array An array of Swift_Cache_OutputStream instances
   */
  public function __construct($streams=array())
  {
    $this->streams = $streams;
  }
  /**
   * Add a new output stream
   * @param Swift_Cache_OutputStream
   */
  public function addStream(Swift_Cache_OutputStream $stream)
  {
    $this->streams[] = $stream;
  }
  /**
   * Read data from all streams as if they are one stream
   * @param int The number of bytes to read from each stream
   * @return string
   */
  public function read($size=null)
  {
    $ret = $this->streams[$this->pointer]->read($size);
    if ($ret !== false)
    {
      return $ret;
    }
    else
    {
      if (isset($this->streams[($this->pointer+1)]))
      {
        $this->pointer++;
        return $this->read($size);
      }
      else
      {
        $this->pointer = 0;
        return false;
      }
    }
  }
}
