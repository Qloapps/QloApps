<?php

/**
 * Abstract base token class that all others inherit from.
 */
abstract class HTMLPurifier_Token
{
    /**
     * Line number node was on in source document. Null if unknown.
     * @type int
     */
    public $line;

    /**
     * Column of line node was on in source document. Null if unknown.
     * @type int
     */
    public $col;

    /**
     * Lookup array of processing that this token is exempt from.
     * Currently, valid values are "ValidateAttributes" and
     * "MakeWellFormed_TagClosedError"
     * @type array
     */
    public $armor = array();

    /**
     * Used during MakeWellFormed.  See Note [Injector skips]
     * @type
     */
    public $skip;

    /**
     * @type
     */
    public $rewind;

    /**
     * @type
     */
    public $carryover;

    /**
     * @param string $n
     * @return null|string
     */
    public function __get($n)
    {
        if ($n === 'type') {
            trigger_error('Deprecated type property called; use instanceof', E_USER_NOTICE);
            switch (get_class($this)) {
                case 'HTMLPurifier_Token_Start':
                    return 'start';
                case 'HTMLPurifier_Token_Empty':
                    return 'empty';
                case 'HTMLPurifier_Token_End':
                    return 'end';
                case 'HTMLPurifier_Token_Text':
                    return 'text';
                case 'HTMLPurifier_Token_Comment':
                    return 'comment';
                default:
                    return null;
            }
        }
    }

    /**
     * Sets the position of the token in the source document.
     * @param int $l
     * @param int $c
     */
    public function position($l = null, $c = null)
    {
        $this->line = $l;
        $this->col = $c;
    }

    /**
     * Convenience function for DirectLex settings line/col position.
     * @param int $l
     * @param int $c
     */
    public function rawPosition($l, $c)
    {
        if ($c === -1) {
            $l++;
        }
        $this->line = $l;
        $this->col = $c;
    }

    /**
     * Converts a token into its corresponding node.
     */
    abstract public function toNode();
}

// vim: et sw=4 sts=4
