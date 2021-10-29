<?php

/**
 * Fluent interface for validating the contents of member variables.
 * This should be immutable. See HTMLPurifier_ConfigSchema_Validator for
 * use-cases. We name this an 'atom' because it's ONLY for validations that
 * are independent and usually scalar.
 */
class HTMLPurifier_ConfigSchema_ValidatorAtom
{
    /**
     * @type string
     */
    protected $context;

    /**
     * @type object
     */
    protected $obj;

    /**
     * @type string
     */
    protected $member;

    /**
     * @type mixed
     */
    protected $contents;

    public function __construct($context, $obj, $member)
    {
        $this->context = $context;
        $this->obj = $obj;
        $this->member = $member;
        $this->contents =& $obj->$member;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     */
    public function assertIsString()
    {
        if (!is_string($this->contents)) {
            $this->error('must be a string');
        }
        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     */
    public function assertIsBool()
    {
        if (!is_bool($this->contents)) {
            $this->error('must be a boolean');
        }
        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     */
    public function assertIsArray()
    {
        if (!is_array($this->contents)) {
            $this->error('must be an array');
        }
        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     */
    public function assertNotNull()
    {
        if ($this->contents === null) {
            $this->error('must not be null');
        }
        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     */
    public function assertAlnum()
    {
        $this->assertIsString();
        if (!ctype_alnum($this->contents)) {
            $this->error('must be alphanumeric');
        }
        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     */
    public function assertNotEmpty()
    {
        if (empty($this->contents)) {
            $this->error('must not be empty');
        }
        return $this;
    }

    /**
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     */
    public function assertIsLookup()
    {
        $this->assertIsArray();
        foreach ($this->contents as $v) {
            if ($v !== true) {
                $this->error('must be a lookup array');
            }
        }
        return $this;
    }

    /**
     * @param string $msg
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    protected function error($msg)
    {
        throw new HTMLPurifier_ConfigSchema_Exception(ucfirst($this->member) . ' in ' . $this->context . ' ' . $msg);
    }
}

// vim: et sw=4 sts=4
