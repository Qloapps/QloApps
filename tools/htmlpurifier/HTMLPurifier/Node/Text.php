<?php

/**
 * Concrete text token class.
 *
 * Text tokens comprise of regular parsed character data (PCDATA) and raw
 * character data (from the CDATA sections). Internally, their
 * data is parsed with all entities expanded. Surprisingly, the text token
 * does have a "tag name" called #PCDATA, which is how the DTD represents it
 * in permissible child nodes.
 */
class HTMLPurifier_Node_Text extends HTMLPurifier_Node
{

    /**
     * PCDATA tag name compatible with DTD, see
     * HTMLPurifier_ChildDef_Custom for details.
     * @type string
     */
    public $name = '#PCDATA';

    /**
     * @type string
     */
    public $data;
    /**< Parsed character data of text. */

    /**
     * @type bool
     */
    public $is_whitespace;

    /**< Bool indicating if node is whitespace. */

    /**
     * Constructor, accepts data and determines if it is whitespace.
     * @param string $data String parsed character data.
     * @param int $line
     * @param int $col
     */
    public function __construct($data, $is_whitespace, $line = null, $col = null)
    {
        $this->data = $data;
        $this->is_whitespace = $is_whitespace;
        $this->line = $line;
        $this->col = $col;
    }

    public function toTokenPair() {
        return array(new HTMLPurifier_Token_Text($this->data, $this->line, $this->col), null);
    }
}

// vim: et sw=4 sts=4
