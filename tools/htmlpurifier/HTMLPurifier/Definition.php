<?php

/**
 * Super-class for definition datatype objects, implements serialization
 * functions for the class.
 */
abstract class HTMLPurifier_Definition
{

    /**
     * Has setup() been called yet?
     * @type bool
     */
    public $setup = false;

    /**
     * If true, write out the final definition object to the cache after
     * setup.  This will be true only if all invocations to get a raw
     * definition object are also optimized.  This does not cause file
     * system thrashing because on subsequent calls the cached object
     * is used and any writes to the raw definition object are short
     * circuited.  See enduser-customize.html for the high-level
     * picture.
     * @type bool
     */
    public $optimized = null;

    /**
     * What type of definition is it?
     * @type string
     */
    public $type;

    /**
     * Sets up the definition object into the final form, something
     * not done by the constructor
     * @param HTMLPurifier_Config $config
     */
    abstract protected function doSetup($config);

    /**
     * Setup function that aborts if already setup
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        if ($this->setup) {
            return;
        }
        $this->setup = true;
        $this->doSetup($config);
    }
}

// vim: et sw=4 sts=4
