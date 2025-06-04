<?php

/**
 * Abstract class of a tag token (start, end or empty), and its behavior.
 */
abstract class HTMLPurifier_Token_Tag extends HTMLPurifier_Token
{
    /**
     * Static bool marker that indicates the class is a tag.
     *
     * This allows us to check objects with <tt>!empty($obj->is_tag)</tt>
     * without having to use a function call <tt>is_a()</tt>.
     * @type bool
     */
    public $is_tag = true;

    /**
     * The lower-case name of the tag, like 'a', 'b' or 'blockquote'.
     *
     * @note Strictly speaking, XML tags are case sensitive, so we shouldn't
     * be lower-casing them, but these tokens cater to HTML tags, which are
     * insensitive.
     * @type string
     */
    public $name;

    /**
     * Associative array of the tag's attributes.
     * @type array
     */
    public $attr = array();

    /**
     * Non-overloaded constructor, which lower-cases passed tag name.
     *
     * @param string $name String name.
     * @param array $attr Associative array of attributes.
     * @param int $line
     * @param int $col
     * @param array $armor
     */
    public function __construct($name, $attr = array(), $line = null, $col = null, $armor = array())
    {
        $this->name = ctype_lower($name) ? $name : strtolower($name);
        foreach ($attr as $key => $value) {
            // normalization only necessary when key is not lowercase
            if (!ctype_lower((string)$key)) {
                $new_key = strtolower($key);
                if (!isset($attr[$new_key])) {
                    $attr[$new_key] = $attr[$key];
                }
                if ($new_key !== $key) {
                    unset($attr[$key]);
                }
            }
        }
        $this->attr = $attr;
        $this->line = $line;
        $this->col = $col;
        $this->armor = $armor;
    }

    public function toNode() {
        return new HTMLPurifier_Node_Element($this->name, $this->attr, $this->line, $this->col, $this->armor);
    }
}

// vim: et sw=4 sts=4
