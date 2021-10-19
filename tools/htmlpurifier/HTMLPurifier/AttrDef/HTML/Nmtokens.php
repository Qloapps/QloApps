<?php

/**
 * Validates contents based on NMTOKENS attribute type.
 */
class HTMLPurifier_AttrDef_HTML_Nmtokens extends HTMLPurifier_AttrDef
{

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = trim($string);

        // early abort: '' and '0' (strings that convert to false) are invalid
        if (!$string) {
            return false;
        }

        $tokens = $this->split($string, $config, $context);
        $tokens = $this->filter($tokens, $config, $context);
        if (empty($tokens)) {
            return false;
        }
        return implode(' ', $tokens);
    }

    /**
     * Splits a space separated list of tokens into its constituent parts.
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    protected function split($string, $config, $context)
    {
        // OPTIMIZABLE!
        // do the preg_match, capture all subpatterns for reformulation

        // we don't support U+00A1 and up codepoints or
        // escaping because I don't know how to do that with regexps
        // and plus it would complicate optimization efforts (you never
        // see that anyway).
        $pattern = '/(?:(?<=\s)|\A)' . // look behind for space or string start
            '((?:--|-?[A-Za-z_])[A-Za-z_\-0-9]*)' .
            '(?:(?=\s)|\z)/'; // look ahead for space or string end
        preg_match_all($pattern, $string, $matches);
        return $matches[1];
    }

    /**
     * Template method for removing certain tokens based on arbitrary criteria.
     * @note If we wanted to be really functional, we'd do an array_filter
     *       with a callback. But... we're not.
     * @param array $tokens
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    protected function filter($tokens, $config, $context)
    {
        return $tokens;
    }
}

// vim: et sw=4 sts=4
