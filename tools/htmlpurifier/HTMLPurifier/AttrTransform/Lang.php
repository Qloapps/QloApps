<?php

/**
 * Post-transform that copies lang's value to xml:lang (and vice-versa)
 * @note Theoretically speaking, this could be a pre-transform, but putting
 *       post is more efficient.
 */
class HTMLPurifier_AttrTransform_Lang extends HTMLPurifier_AttrTransform
{

    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        $lang = isset($attr['lang']) ? $attr['lang'] : false;
        $xml_lang = isset($attr['xml:lang']) ? $attr['xml:lang'] : false;

        if ($lang !== false && $xml_lang === false) {
            $attr['xml:lang'] = $lang;
        } elseif ($xml_lang !== false) {
            $attr['lang'] = $xml_lang;
        }
        return $attr;
    }
}

// vim: et sw=4 sts=4
