<?php

/**
 * Generic pre-transform that converts an attribute with a fixed number of
 * values (enumerated) to CSS.
 */
class HTMLPurifier_AttrTransform_EnumToCSS extends HTMLPurifier_AttrTransform
{
    /**
     * Name of attribute to transform from.
     * @type string
     */
    protected $attr;

    /**
     * Lookup array of attribute values to CSS.
     * @type array
     */
    protected $enumToCSS = array();

    /**
     * Case sensitivity of the matching.
     * @type bool
     * @warning Currently can only be guaranteed to work with ASCII
     *          values.
     */
    protected $caseSensitive = false;

    /**
     * @param string $attr Attribute name to transform from
     * @param array $enum_to_css Lookup array of attribute values to CSS
     * @param bool $case_sensitive Case sensitivity indicator, default false
     */
    public function __construct($attr, $enum_to_css, $case_sensitive = false)
    {
        $this->attr = $attr;
        $this->enumToCSS = $enum_to_css;
        $this->caseSensitive = (bool)$case_sensitive;
    }

    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->attr])) {
            return $attr;
        }

        $value = trim($attr[$this->attr]);
        unset($attr[$this->attr]);

        if (!$this->caseSensitive) {
            $value = strtolower($value);
        }

        if (!isset($this->enumToCSS[$value])) {
            return $attr;
        }
        $this->prependCSS($attr, $this->enumToCSS[$value]);
        return $attr;
    }
}

// vim: et sw=4 sts=4
