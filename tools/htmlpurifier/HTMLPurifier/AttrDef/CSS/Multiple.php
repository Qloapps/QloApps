<?php

/**
 * Framework class for strings that involve multiple values.
 *
 * Certain CSS properties such as border-width and margin allow multiple
 * lengths to be specified.  This class can take a vanilla border-width
 * definition and multiply it, usually into a max of four.
 *
 * @note Even though the CSS specification isn't clear about it, inherit
 *       can only be used alone: it will never manifest as part of a multi
 *       shorthand declaration.  Thus, this class does not allow inherit.
 */
class HTMLPurifier_AttrDef_CSS_Multiple extends HTMLPurifier_AttrDef
{
    /**
     * Instance of component definition to defer validation to.
     * @type HTMLPurifier_AttrDef
     * @todo Make protected
     */
    public $single;

    /**
     * Max number of values allowed.
     * @todo Make protected
     */
    public $max;

    /**
     * @param HTMLPurifier_AttrDef $single HTMLPurifier_AttrDef to multiply
     * @param int $max Max number of values allowed (usually four)
     */
    public function __construct($single, $max = 4)
    {
        $this->single = $single;
        $this->max = $max;
    }

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = $this->mungeRgb($this->parseCDATA($string));
        if ($string === '') {
            return false;
        }
        $parts = explode(' ', $string); // parseCDATA replaced \r, \t and \n
        $length = count($parts);
        $final = '';
        for ($i = 0, $num = 0; $i < $length && $num < $this->max; $i++) {
            if (ctype_space($parts[$i])) {
                continue;
            }
            $result = $this->single->validate($parts[$i], $config, $context);
            if ($result !== false) {
                $final .= $result . ' ';
                $num++;
            }
        }
        if ($final === '') {
            return false;
        }
        return rtrim($final);
    }
}

// vim: et sw=4 sts=4
