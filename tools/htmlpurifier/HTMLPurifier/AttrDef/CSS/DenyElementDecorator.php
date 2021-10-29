<?php

/**
 * Decorator which enables CSS properties to be disabled for specific elements.
 */
class HTMLPurifier_AttrDef_CSS_DenyElementDecorator extends HTMLPurifier_AttrDef
{
    /**
     * @type HTMLPurifier_AttrDef
     */
    public $def;
    /**
     * @type string
     */
    public $element;

    /**
     * @param HTMLPurifier_AttrDef $def Definition to wrap
     * @param string $element Element to deny
     */
    public function __construct($def, $element)
    {
        $this->def = $def;
        $this->element = $element;
    }

    /**
     * Checks if CurrentToken is set and equal to $this->element
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $token = $context->get('CurrentToken', true);
        if ($token && $token->name == $this->element) {
            return false;
        }
        return $this->def->validate($string, $config, $context);
    }
}

// vim: et sw=4 sts=4
