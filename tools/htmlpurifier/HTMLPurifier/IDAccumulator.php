<?php

/**
 * Component of HTMLPurifier_AttrContext that accumulates IDs to prevent dupes
 * @note In Slashdot-speak, dupe means duplicate.
 * @note The default constructor does not accept $config or $context objects:
 *       use must use the static build() factory method to perform initialization.
 */
class HTMLPurifier_IDAccumulator
{

    /**
     * Lookup table of IDs we've accumulated.
     * @public
     */
    public $ids = array();

    /**
     * Builds an IDAccumulator, also initializing the default blacklist
     * @param HTMLPurifier_Config $config Instance of HTMLPurifier_Config
     * @param HTMLPurifier_Context $context Instance of HTMLPurifier_Context
     * @return HTMLPurifier_IDAccumulator Fully initialized HTMLPurifier_IDAccumulator
     */
    public static function build($config, $context)
    {
        $id_accumulator = new HTMLPurifier_IDAccumulator();
        $id_accumulator->load($config->get('Attr.IDBlacklist'));
        return $id_accumulator;
    }

    /**
     * Add an ID to the lookup table.
     * @param string $id ID to be added.
     * @return bool status, true if success, false if there's a dupe
     */
    public function add($id)
    {
        if (isset($this->ids[$id])) {
            return false;
        }
        return $this->ids[$id] = true;
    }

    /**
     * Load a list of IDs into the lookup table
     * @param $array_of_ids Array of IDs to load
     * @note This function doesn't care about duplicates
     */
    public function load($array_of_ids)
    {
        foreach ($array_of_ids as $id) {
            $this->ids[$id] = true;
        }
    }
}

// vim: et sw=4 sts=4
