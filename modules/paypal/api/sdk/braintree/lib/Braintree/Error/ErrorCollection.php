<?php
namespace Braintree\Error;

use Braintree\Util;

/**
 *
 * Error handler
 * Handles validation errors
 *
 * Contains a read-only property $error which is a ValidationErrorCollection
 *
 * @package    Braintree
 * @subpackage Errors
 * @category   Errors
 * @copyright  2015 Braintree, a division of PayPal, Inc.
 *
 * @property-read object $errors
 */
class ErrorCollection
{
    private $_errors;

    public function __construct($errorData)
    {
        $this->_errors =
                new ValidationErrorCollection($errorData);
    }


    /**
     * Returns all of the validation errors at all levels of nesting in a single, flat array.
     */
    public function deepAll()
    {
        return $this->_errors->deepAll();
    }

    /**
     * Returns the total number of validation errors at all levels of nesting. For example,
     *if creating a customer with a credit card and a billing address, and each of the customer,
     * credit card, and billing address has 1 error, this method will return 3.
     *
     * @return int size
     */
    public function deepSize()
    {
        $size = $this->_errors->deepSize();
        return $size;
    }

    /**
     * return errors for the passed key name
     *
     * @param string $key
     * @return mixed
     */
    public function forKey($key)
    {
        return $this->_errors->forKey($key);
    }

    /**
     * return errors for the passed html field.
     * For example, $result->errors->onHtmlField("transaction[customer][last_name]")
     *
     * @param string $field
     * @return array
     */
    public function onHtmlField($field)
    {
        $pieces = preg_split("/[\[\]]+/", $field, 0, PREG_SPLIT_NO_EMPTY);
        $errors = $this;
        foreach(array_slice($pieces, 0, -1) as $key) {
            $errors = $errors->forKey(Util::delimiterToCamelCase($key));
            if (!isset($errors)) { return []; }
        }
        $finalKey = Util::delimiterToCamelCase(end($pieces));
        return $errors->onAttribute($finalKey);
    }

    /**
     * Returns the errors at the given nesting level (see forKey) in a single, flat array:
     *
     * <code>
     *   $result = Customer::create(...);
     *   $customerErrors = $result->errors->forKey('customer')->shallowAll();
     * </code>
     */
    public function shallowAll()
    {
        return $this->_errors->shallowAll();
    }

    /**
     *
     * @ignore
     */
    public function  __get($name)
    {
        $varName = "_$name";
        return isset($this->$varName) ? $this->$varName : null;
    }

    /**
     *
     * @ignore
     */
    public function  __toString()
    {
        return sprintf('%s', $this->_errors);
    }
}
class_alias('Braintree\Error\ErrorCollection', 'Braintree_Error_ErrorCollection');
