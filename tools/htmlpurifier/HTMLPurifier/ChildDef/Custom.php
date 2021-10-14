<?php

/**
 * Custom validation class, accepts DTD child definitions
 *
 * @warning Currently this class is an all or nothing proposition, that is,
 *          it will only give a bool return value.
 */
class HTMLPurifier_ChildDef_Custom extends HTMLPurifier_ChildDef
{
    /**
     * @type string
     */
    public $type = 'custom';

    /**
     * @type bool
     */
    public $allow_empty = false;

    /**
     * Allowed child pattern as defined by the DTD.
     * @type string
     */
    public $dtd_regex;

    /**
     * PCRE regex derived from $dtd_regex.
     * @type string
     */
    private $_pcre_regex;

    /**
     * @param $dtd_regex Allowed child pattern from the DTD
     */
    public function __construct($dtd_regex)
    {
        $this->dtd_regex = $dtd_regex;
        $this->_compileRegex();
    }

    /**
     * Compiles the PCRE regex from a DTD regex ($dtd_regex to $_pcre_regex)
     */
    protected function _compileRegex()
    {
        $raw = str_replace(' ', '', $this->dtd_regex);
        if ($raw[0] != '(') {
            $raw = "($raw)";
        }
        $el = '[#a-zA-Z0-9_.-]+';
        $reg = $raw;

        // COMPLICATED! AND MIGHT BE BUGGY! I HAVE NO CLUE WHAT I'M
        // DOING! Seriously: if there's problems, please report them.

        // collect all elements into the $elements array
        preg_match_all("/$el/", $reg, $matches);
        foreach ($matches[0] as $match) {
            $this->elements[$match] = true;
        }

        // setup all elements as parentheticals with leading commas
        $reg = preg_replace("/$el/", '(,\\0)', $reg);

        // remove commas when they were not solicited
        $reg = preg_replace("/([^,(|]\(+),/", '\\1', $reg);

        // remove all non-paranthetical commas: they are handled by first regex
        $reg = preg_replace("/,\(/", '(', $reg);

        $this->_pcre_regex = $reg;
    }

    /**
     * @param HTMLPurifier_Node[] $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool
     */
    public function validateChildren($children, $config, $context)
    {
        $list_of_children = '';
        $nesting = 0; // depth into the nest
        foreach ($children as $node) {
            if (!empty($node->is_whitespace)) {
                continue;
            }
            $list_of_children .= $node->name . ',';
        }
        // add leading comma to deal with stray comma declarations
        $list_of_children = ',' . rtrim($list_of_children, ',');
        $okay =
            preg_match(
                '/^,?' . $this->_pcre_regex . '$/',
                $list_of_children
            );
        return (bool)$okay;
    }
}

// vim: et sw=4 sts=4
