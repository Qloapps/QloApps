<?php

/**
 * Swift Mailer Decorator Replacements Container
 * Please read the LICENSE file
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Plugin
 * @subpackage Decorator
 * @license GNU Lesser General Public License
 */


/**
 * Swift Decorator Plugin Replacements.
 * Provides and manages the list of replacements for the decorator plugin.
 * @package Swift_Plugin
 * @subpackage Decorator
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Plugin_Decorator_Replacements
{
  /**
   * The list of replacements as a 2-d array
   * @var array,array
   */
  protected $replacements;
  
  /**
   * Ctor.
   * @param array The replacements as a 2-d array, optional
   */
  public function __construct($replacements = array())
  {
    $this->setReplacements($replacements);
  }
  /**
   * Add a list of replacements for a given address.
   * @param string The e-mail address
   * @param array The replacements as (search => replacement) form.
   */
  public function addReplacements($address, $replacements)
  {
    $this->replacements[strtolower($address)] = (array)$replacements;
  }
  /**
   * Set the complete list of replacements as a 2-d array.
   * The array is formed thus (address => (search => replace), address => (search => replace))
   * @param array,array The replacements.
   */
  public function setReplacements($replacements)
  {
    $this->replacements = array_change_key_case((array) $replacements, CASE_LOWER);
  }
  /**
   * Get the entire list of replacements as a 2-d array
   * @return array,array
   */
  public function getReplacements()
  {
    return $this->replacements;
  }
  /**
   * Get the list of replacements for the address given.
   * Returns an array where (search => replacement).
   * @param string The address to get replacements for
   * @return array
   */
  public function getReplacementsFor($address)
  {
    $address = strtolower($address);
    if (array_key_exists($address, $this->replacements))
    {
      return (array)$this->replacements[$address];
    }
    else return array();
  }
}
