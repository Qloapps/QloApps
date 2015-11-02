<?php

/**
 * Swift Mailer File Stream Wrapper
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/ClassLoader.php";
Swift_ClassLoader::load("Swift_FileException");

/**
 * Swift File stream abstraction layer
 * Reads bytes from a file
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_File
{
  /**
   * The accessible path to the file
   * @var string
   */
  protected $path = null;
  /**
   * The name of the file
   * @var string
   */
  protected $name = null;
  /**
   * The resource returned by fopen() against the path
   * @var resource
   */
  protected $handle = null;
  /**
   * The status of magic_quotes in php.ini
   * @var boolean
   */
  protected $magic_quotes = false;
  
  /**
   * Constructor
   * @param string The path the the file
   * @throws Swift_FileException If the file cannot be found
   */
  public function __construct($path)
  {
    $this->setPath($path);
    $this->magic_quotes = get_magic_quotes_runtime();
  }
  /**
   * Set the path to the file
   * @param string The path to the file
   * @throws Swift_FileException If the file cannot be found
   */
  public function setPath($path)
  {
    if (!file_exists($path))
    {
      throw new Swift_FileException("No such file '" . $path ."'");
    }
    $this->handle = null;
    $this->path = $path;
    $this->name = null;
    $this->name = $this->getFileName();
  }
  /**
   * Get the path to the file
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Get the name of the file without it's full path
   * @return string
   */
  public function getFileName()
  {
    if ($this->name !== null)
    {
      return $this->name;
    }
    else
    {
      return basename($this->getPath());
    }
  }
  /**
   * Establish an open file handle on the file if the file is not yet opened
   * @throws Swift_FileException If the file cannot be opened for reading
   */
  protected function createHandle()
  {
    if ($this->handle === null)
    {
      if (!$this->handle = @fopen($this->path, "rb"))
      {
        throw new Swift_FileException("Unable to open file '" . $this->path . " for reading.  Check the file permissions.");
      }
    }
  }
  /**
   * Check if the pointer as at the end of the file
   * @return boolean
   * @throws Swift_FileException If the file cannot be read
   */
  public function EOF()
  {
    $this->createHandle();
    return feof($this->handle);
  }
  /**
   * Get a single byte from the file
   * Returns false past EOF
   * @return string
   * @throws Swift_FileException If the file cannot be read
   */
  public function getByte()
  {
    $this->createHandle();
    return $this->read(1);
  }
  /**
   * Read one full line from the file including the line ending
   * Returns false past EOF
   * @return string
   * @throws Swift_FileException If the file cannot be read
   */
  public function readln()
  {
    $this->createHandle();
    if (!$this->EOF())
    {
      $ret = fgets($this->handle);
    }
    else $ret = false;

    return $ret;
  }
  /**
   * Get the entire file contents as a string
   * @return string
   * @throws Swift_FileException If the file cannot be read
   */
  public function readFull()
  {
    $ret = "";
    while (false !== $chunk = $this->read(8192, false)) $ret .= $chunk;
    return $ret;
  }
  /**
   * Read a given number of bytes from the file
   * Returns false past EOF
   * @return string
   * @throws Swift_FileException If the file cannot be read
   */
  public function read($bytes, $unquote=true)
  {
    $this->createHandle();
    if (!$this->EOF())
    {
      $ret = fread($this->handle, $bytes);
    }
    else $ret = false;
    
    
    return $ret;
  }
  /**
   * Get the size of the file in bytes
   * @return int
   */
  public function length()
  {
    return filesize($this->path);
  }
  /**
   * Close the open handle on the file
   * @throws Swift_FileException If the file cannot be read
   */
  public function close()
  {
    $this->createHandle();
    @fclose($this->handle);
    $this->handle = null;
  }
  /**
   * Reset the file pointer back to zero
   */
  public function reset()
  {
    $this->createHandle();
    fseek($this->handle, 0);
  }
  /**
   * Destructor
   * Closes the file
   */
  public function __destruct()
  {
    if ($this->handle !== null) $this->close();
  }
}
