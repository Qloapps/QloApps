<?php
namespace Braintree;

use Countable;
use IteratorAggregate;
use ArrayAccess;
use OutOfRangeException;
use ArrayIterator;

/**
 * Braintree Generic collection
 *
 * PHP Version 5
 *
 * Based on Generic Collection class from:
 * {@link http://codeutopia.net/code/library/CU/Collection.php}
 *
 * @package   Braintree
 * @subpackage Utility
 * @copyright 2015 Braintree, a division of PayPal, Inc.
 */

class Collection implements Countable, IteratorAggregate, ArrayAccess
{
    /**
     *
     * @var array collection storage
     */
    protected $_collection = [];

    /**
     * Add a value into the collection
     * @param string $value
     */
    public function add($value)
    {
        $this->_collection[] = $value;
    }

    /**
     * Set index's value
     * @param integer $index
     * @param mixed $value
     * @throws OutOfRangeException
     */
    public function set($index, $value)
    {
        if($index >= $this->count())
            throw new OutOfRangeException('Index out of range');

        $this->_collection[$index] = $value;
    }

    /**
     * Remove a value from the collection
     * @param integer $index index to remove
     * @throws OutOfRangeException if index is out of range
     */
    public function remove($index)
    {
        if($index >= $this->count())
            throw new OutOfRangeException('Index out of range');

        array_splice($this->_collection, $index, 1);
    }

    /**
     * Return value at index
     * @param integer $index
     * @return mixed
     * @throws OutOfRangeException
     */
    public function get($index)
    {
        if($index >= $this->count())
            throw new OutOfRangeException('Index out of range');

        return $this->_collection[$index];
    }

    /**
     * Determine if index exists
     * @param integer $index
     * @return boolean
     */
    public function exists($index)
    {
        if($index >= $this->count())
            return false;

        return true;
    }
    /**
     * Return count of items in collection
     * Implements countable
     * @return integer
     */
    public function count()
    {
        return count($this->_collection);
    }


    /**
     * Return an iterator
     * Implements IteratorAggregate
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_collection);
    }

    /**
     * Set offset to value
     * Implements ArrayAccess
     * @see set
     * @param integer $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Unset offset
     * Implements ArrayAccess
     * @see remove
     * @param integer $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * get an offset's value
     * Implements ArrayAccess
     * @see get
     * @param integer $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Determine if offset exists
     * Implements ArrayAccess
     * @see exists
     * @param integer $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

}
class_alias('Braintree\Collection', 'Braintree_Collection');
