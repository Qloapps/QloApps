<?php

/**
 * Class that handles operations involving percent-encoding in URIs.
 *
 * @warning
 *      Be careful when reusing instances of PercentEncoder. The object
 *      you use for normalize() SHOULD NOT be used for encode(), or
 *      vice-versa.
 */
class HTMLPurifier_PercentEncoder
{

    /**
     * Reserved characters to preserve when using encode().
     * @type array
     */
    protected $preserve = array();

    /**
     * String of characters that should be preserved while using encode().
     * @param bool $preserve
     */
    public function __construct($preserve = false)
    {
        // unreserved letters, ought to const-ify
        for ($i = 48; $i <= 57; $i++) { // digits
            $this->preserve[$i] = true;
        }
        for ($i = 65; $i <= 90; $i++) { // upper-case
            $this->preserve[$i] = true;
        }
        for ($i = 97; $i <= 122; $i++) { // lower-case
            $this->preserve[$i] = true;
        }
        $this->preserve[45] = true; // Dash         -
        $this->preserve[46] = true; // Period       .
        $this->preserve[95] = true; // Underscore   _
        $this->preserve[126]= true; // Tilde        ~

        // extra letters not to escape
        if ($preserve !== false) {
            for ($i = 0, $c = strlen($preserve); $i < $c; $i++) {
                $this->preserve[ord($preserve[$i])] = true;
            }
        }
    }

    /**
     * Our replacement for urlencode, it encodes all non-reserved characters,
     * as well as any extra characters that were instructed to be preserved.
     * @note
     *      Assumes that the string has already been normalized, making any
     *      and all percent escape sequences valid. Percents will not be
     *      re-escaped, regardless of their status in $preserve
     * @param string $string String to be encoded
     * @return string Encoded string.
     */
    public function encode($string)
    {
        $ret = '';
        for ($i = 0, $c = strlen($string); $i < $c; $i++) {
            if ($string[$i] !== '%' && !isset($this->preserve[$int = ord($string[$i])])) {
                $ret .= '%' . sprintf('%02X', $int);
            } else {
                $ret .= $string[$i];
            }
        }
        return $ret;
    }

    /**
     * Fix up percent-encoding by decoding unreserved characters and normalizing.
     * @warning This function is affected by $preserve, even though the
     *          usual desired behavior is for this not to preserve those
     *          characters. Be careful when reusing instances of PercentEncoder!
     * @param string $string String to normalize
     * @return string
     */
    public function normalize($string)
    {
        if ($string == '') {
            return '';
        }
        $parts = explode('%', $string);
        $ret = array_shift($parts);
        foreach ($parts as $part) {
            $length = strlen($part);
            if ($length < 2) {
                $ret .= '%25' . $part;
                continue;
            }
            $encoding = substr($part, 0, 2);
            $text     = substr($part, 2);
            if (!ctype_xdigit($encoding)) {
                $ret .= '%25' . $part;
                continue;
            }
            $int = hexdec($encoding);
            if (isset($this->preserve[$int])) {
                $ret .= chr($int) . $text;
                continue;
            }
            $encoding = strtoupper($encoding);
            $ret .= '%' . $encoding . $text;
        }
        return $ret;
    }
}

// vim: et sw=4 sts=4
