<?php

/**
 * Swift Mailer Array Iterator Interface
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Iterator");

/**
 * Swift Array Iterator Interface
 * Iterates over a standard PHP array.
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Iterator_Array implements Swift_Iterator
{
  /**
   * All keys in this array.
   * @var array
   */
  protected $keys;
  /**
   * All values in this array.
   * @var array
   */
  protected $values;
  /**
   * The current array position.
   * @var int
   */
  protected $pos = -1;
  
  /**
   * Ctor.
   * @param array The array to iterate over.
   */
  public function __construct($input)
  {
	  $input = (array) $input;
    $this->keys = array_keys($input);
    $this->values = array_values($input);
  }
  /**
   * Returns the original array.
   * @return array
   */
  public function getArray()
  {
    return array_combine($this->keys, $this->values);
  }
  /**
   * Returns true if there is a value after the current one.
   * @return boolean
   */
  public function hasNext()
  {
    return array_key_exists($this->pos + 1, $this->keys);
  }
  /**
   * Moves to the next array element if possible.
   * @return boolean
   */
  public function next()
  {
    if ($this->hasNext())
    {
      ++$this->pos;
      return true;
    }
    
    return false;
  }
  /**
   * Goes directly to the given element in the array if possible.
   * @param int Numeric position
   * @return boolean
   */
  public function seekTo($pos)
  {
    if (array_key_exists($pos, $this->keys))
    {
      $this->pos = $pos;
      return true;
    }
    
    return false;
  }
  /**
   * Returns the value at the current position, or NULL otherwise.
   * @return mixed.
   */
  public function getValue()
  {
    if (array_key_exists($this->pos, $this->values))
      return $this->values[$this->pos];
    else return null;
  }
  /**
   * Gets the current numeric position within the array.
   * @return int
   */
  public function getPosition()
  {
    return $this->pos;
  }
}
