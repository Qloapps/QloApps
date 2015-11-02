<?php

/**
 * Swift Mailer Iterator Interface
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift
 * @license GNU Lesser General Public License
 */

/**
 * Swift Iterator Interface
 * Provides the interface for iterators used for retrieving addresses in batch sends.
 * @package Swift
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
interface Swift_Iterator
{
  /**
   * Check if there is a value in the list after the current one.
   * @return boolean
   */
  public function hasNext();
  /**
   * Move to the next position in the list if possible.
   * @return boolean
   */
  public function next();
  /**
   * Seek to the given numeric index in the list of possible.
   * @return boolean
   */
  public function seekTo($pos);
  /**
   * Get the value of the list at the current position.
   * @return mixed
   */
  public function getValue();
  /**
   * Get the current list position.
   * @return int
   */
  public function getPosition();
}
