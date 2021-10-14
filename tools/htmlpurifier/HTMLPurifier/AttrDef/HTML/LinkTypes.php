<?php

/**
 * Validates a rel/rev link attribute against a directive of allowed values
 * @note We cannot use Enum because link types allow multiple
 *       values.
 * @note Assumes link types are ASCII text
 */
class HTMLPurifier_AttrDef_HTML_LinkTypes extends HTMLPurifier_AttrDef
{

    /**
     * Name config attribute to pull.
     * @type string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $configLookup = array(
            'rel' => 'AllowedRel',
            'rev' => 'AllowedRev'
        );
        if (!isset($configLookup[$name])) {
            trigger_error(
                'Unrecognized attribute name for link ' .
                'relationship.',
                E_USER_ERROR
            );
            return;
        }
        $this->name = $configLookup[$name];
    }

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $allowed = $config->get('Attr.' . $this->name);
        if (empty($allowed)) {
            return false;
        }

        $string = $this->parseCDATA($string);
        $parts = explode(' ', $string);

        // lookup to prevent duplicates
        $ret_lookup = array();
        foreach ($parts as $part) {
            $part = strtolower(trim($part));
            if (!isset($allowed[$part])) {
                continue;
            }
            $ret_lookup[$part] = true;
        }

        if (empty($ret_lookup)) {
            return false;
        }
        $string = implode(' ', array_keys($ret_lookup));
        return $string;
    }
}

// vim: et sw=4 sts=4
