<?php

// if want to implement error collecting here, we'll need to use some sort
// of global data (probably trigger_error) because it's impossible to pass
// $config or $context to the callback functions.

/**
 * Handles referencing and dereferencing character entities
 */
class HTMLPurifier_EntityParser
{

    /**
     * Reference to entity lookup table.
     * @type HTMLPurifier_EntityLookup
     */
    protected $_entity_lookup;

    /**
     * Callback regex string for entities in text.
     * @type string
     */
    protected $_textEntitiesRegex;

    /**
     * Callback regex string for entities in attributes.
     * @type string
     */
    protected $_attrEntitiesRegex;

    /**
     * Tests if the beginning of a string is a semi-optional regex
     */
    protected $_semiOptionalPrefixRegex;

    public function __construct() {
        // From
        // http://stackoverflow.com/questions/15532252/why-is-reg-being-rendered-as-without-the-bounding-semicolon
        $semi_optional = "quot|QUOT|lt|LT|gt|GT|amp|AMP|AElig|Aacute|Acirc|Agrave|Aring|Atilde|Auml|COPY|Ccedil|ETH|Eacute|Ecirc|Egrave|Euml|Iacute|Icirc|Igrave|Iuml|Ntilde|Oacute|Ocirc|Ograve|Oslash|Otilde|Ouml|REG|THORN|Uacute|Ucirc|Ugrave|Uuml|Yacute|aacute|acirc|acute|aelig|agrave|aring|atilde|auml|brvbar|ccedil|cedil|cent|copy|curren|deg|divide|eacute|ecirc|egrave|eth|euml|frac12|frac14|frac34|iacute|icirc|iexcl|igrave|iquest|iuml|laquo|macr|micro|middot|nbsp|not|ntilde|oacute|ocirc|ograve|ordf|ordm|oslash|otilde|ouml|para|plusmn|pound|raquo|reg|sect|shy|sup1|sup2|sup3|szlig|thorn|times|uacute|ucirc|ugrave|uml|uuml|yacute|yen|yuml";

        // NB: three empty captures to put the fourth match in the right
        // place
        $this->_semiOptionalPrefixRegex = "/&()()()($semi_optional)/";

        $this->_textEntitiesRegex =
            '/&(?:'.
            // hex
            '[#]x([a-fA-F0-9]+);?|'.
            // dec
            '[#]0*(\d+);?|'.
            // string (mandatory semicolon)
            // NB: order matters: match semicolon preferentially
            '([A-Za-z_:][A-Za-z0-9.\-_:]*);|'.
            // string (optional semicolon)
            "($semi_optional)".
            ')/';

        $this->_attrEntitiesRegex =
            '/&(?:'.
            // hex
            '[#]x([a-fA-F0-9]+);?|'.
            // dec
            '[#]0*(\d+);?|'.
            // string (mandatory semicolon)
            // NB: order matters: match semicolon preferentially
            '([A-Za-z_:][A-Za-z0-9.\-_:]*);|'.
            // string (optional semicolon)
            // don't match if trailing is equals or alphanumeric (URL
            // like)
            "($semi_optional)(?![=;A-Za-z0-9])".
            ')/';

    }

    /**
     * Substitute entities with the parsed equivalents.  Use this on
     * textual data in an HTML document (as opposed to attributes.)
     *
     * @param string $string String to have entities parsed.
     * @return string Parsed string.
     */
    public function substituteTextEntities($string)
    {
        return preg_replace_callback(
            $this->_textEntitiesRegex,
            array($this, 'entityCallback'),
            $string
        );
    }

    /**
     * Substitute entities with the parsed equivalents.  Use this on
     * attribute contents in documents.
     *
     * @param string $string String to have entities parsed.
     * @return string Parsed string.
     */
    public function substituteAttrEntities($string)
    {
        return preg_replace_callback(
            $this->_attrEntitiesRegex,
            array($this, 'entityCallback'),
            $string
        );
    }

    /**
     * Callback function for substituteNonSpecialEntities() that does the work.
     *
     * @param array $matches  PCRE matches array, with 0 the entire match, and
     *                  either index 1, 2 or 3 set with a hex value, dec value,
     *                  or string (respectively).
     * @return string Replacement string.
     */

    protected function entityCallback($matches)
    {
        $entity = $matches[0];
        $hex_part = isset($matches[1]) ? $matches[1] : null;
        $dec_part = isset($matches[2]) ? $matches[2] : null;
        $named_part = empty($matches[3]) ? (empty($matches[4]) ? "" : $matches[4]) : $matches[3];
        if ($hex_part !== NULL && $hex_part !== "") {
            return HTMLPurifier_Encoder::unichr(hexdec($hex_part));
        } elseif ($dec_part !== NULL && $dec_part !== "") {
            return HTMLPurifier_Encoder::unichr((int) $dec_part);
        } else {
            if (!$this->_entity_lookup) {
                $this->_entity_lookup = HTMLPurifier_EntityLookup::instance();
            }
            if (isset($this->_entity_lookup->table[$named_part])) {
                return $this->_entity_lookup->table[$named_part];
            } else {
                // exact match didn't match anything, so test if
                // any of the semicolon optional match the prefix.
                // Test that this is an EXACT match is important to
                // prevent infinite loop
                if (!empty($matches[3])) {
                    return preg_replace_callback(
                        $this->_semiOptionalPrefixRegex,
                        array($this, 'entityCallback'),
                        $entity
                    );
                }
                return $entity;
            }
        }
    }

    // LEGACY CODE BELOW

    /**
     * Callback regex string for parsing entities.
     * @type string
     */
    protected $_substituteEntitiesRegex =
        '/&(?:[#]x([a-fA-F0-9]+)|[#]0*(\d+)|([A-Za-z_:][A-Za-z0-9.\-_:]*));?/';
        //     1. hex             2. dec      3. string (XML style)

    /**
     * Decimal to parsed string conversion table for special entities.
     * @type array
     */
    protected $_special_dec2str =
            array(
                    34 => '"',
                    38 => '&',
                    39 => "'",
                    60 => '<',
                    62 => '>'
            );

    /**
     * Stripped entity names to decimal conversion table for special entities.
     * @type array
     */
    protected $_special_ent2dec =
            array(
                    'quot' => 34,
                    'amp'  => 38,
                    'lt'   => 60,
                    'gt'   => 62
            );

    /**
     * Substitutes non-special entities with their parsed equivalents. Since
     * running this whenever you have parsed character is t3h 5uck, we run
     * it before everything else.
     *
     * @param string $string String to have non-special entities parsed.
     * @return string Parsed string.
     */
    public function substituteNonSpecialEntities($string)
    {
        // it will try to detect missing semicolons, but don't rely on it
        return preg_replace_callback(
            $this->_substituteEntitiesRegex,
            array($this, 'nonSpecialEntityCallback'),
            $string
        );
    }

    /**
     * Callback function for substituteNonSpecialEntities() that does the work.
     *
     * @param array $matches  PCRE matches array, with 0 the entire match, and
     *                  either index 1, 2 or 3 set with a hex value, dec value,
     *                  or string (respectively).
     * @return string Replacement string.
     */

    protected function nonSpecialEntityCallback($matches)
    {
        // replaces all but big five
        $entity = $matches[0];
        $is_num = (@$matches[0][1] === '#');
        if ($is_num) {
            $is_hex = (@$entity[2] === 'x');
            $code = $is_hex ? hexdec($matches[1]) : (int) $matches[2];
            // abort for special characters
            if (isset($this->_special_dec2str[$code])) {
                return $entity;
            }
            return HTMLPurifier_Encoder::unichr($code);
        } else {
            if (isset($this->_special_ent2dec[$matches[3]])) {
                return $entity;
            }
            if (!$this->_entity_lookup) {
                $this->_entity_lookup = HTMLPurifier_EntityLookup::instance();
            }
            if (isset($this->_entity_lookup->table[$matches[3]])) {
                return $this->_entity_lookup->table[$matches[3]];
            } else {
                return $entity;
            }
        }
    }

    /**
     * Substitutes only special entities with their parsed equivalents.
     *
     * @notice We try to avoid calling this function because otherwise, it
     * would have to be called a lot (for every parsed section).
     *
     * @param string $string String to have non-special entities parsed.
     * @return string Parsed string.
     */
    public function substituteSpecialEntities($string)
    {
        return preg_replace_callback(
            $this->_substituteEntitiesRegex,
            array($this, 'specialEntityCallback'),
            $string
        );
    }

    /**
     * Callback function for substituteSpecialEntities() that does the work.
     *
     * This callback has same syntax as nonSpecialEntityCallback().
     *
     * @param array $matches  PCRE-style matches array, with 0 the entire match, and
     *                  either index 1, 2 or 3 set with a hex value, dec value,
     *                  or string (respectively).
     * @return string Replacement string.
     */
    protected function specialEntityCallback($matches)
    {
        $entity = $matches[0];
        $is_num = (@$matches[0][1] === '#');
        if ($is_num) {
            $is_hex = (@$entity[2] === 'x');
            $int = $is_hex ? hexdec($matches[1]) : (int) $matches[2];
            return isset($this->_special_dec2str[$int]) ?
                $this->_special_dec2str[$int] :
                $entity;
        } else {
            return isset($this->_special_ent2dec[$matches[3]]) ?
                $this->_special_dec2str[$this->_special_ent2dec[$matches[3]]] :
                $entity;
        }
    }
}

// vim: et sw=4 sts=4
