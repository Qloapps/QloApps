<?php

// why is this a top level function? Because PHP 5.2.0 doesn't seem to
// understand how to interpret this filter if it's a static method.
// It's all really silly, but if we go this route it might be reasonable
// to coalesce all of these methods into one.
function htmlpurifier_filter_extractstyleblocks_muteerrorhandler()
{
}

/**
 * This filter extracts <style> blocks from input HTML, cleans them up
 * using CSSTidy, and then places them in $purifier->context->get('StyleBlocks')
 * so they can be used elsewhere in the document.
 *
 * @note
 *      See tests/HTMLPurifier/Filter/ExtractStyleBlocksTest.php for
 *      sample usage.
 *
 * @note
 *      This filter can also be used on stylesheets not included in the
 *      document--something purists would probably prefer. Just directly
 *      call HTMLPurifier_Filter_ExtractStyleBlocks->cleanCSS()
 */
class HTMLPurifier_Filter_ExtractStyleBlocks extends HTMLPurifier_Filter
{
    /**
     * @type string
     */
    public $name = 'ExtractStyleBlocks';

    /**
     * @type array
     */
    private $_styleMatches = array();

    /**
     * @type csstidy
     */
    private $_tidy;

    /**
     * @type HTMLPurifier_AttrDef_HTML_ID
     */
    private $_id_attrdef;

    /**
     * @type HTMLPurifier_AttrDef_CSS_Ident
     */
    private $_class_attrdef;

    /**
     * @type HTMLPurifier_AttrDef_Enum
     */
    private $_enum_attrdef;

    public function __construct()
    {
        $this->_tidy = new csstidy();
        $this->_tidy->set_cfg('lowercase_s', false);
        $this->_id_attrdef = new HTMLPurifier_AttrDef_HTML_ID(true);
        $this->_class_attrdef = new HTMLPurifier_AttrDef_CSS_Ident();
        $this->_enum_attrdef = new HTMLPurifier_AttrDef_Enum(
            array(
                'first-child',
                'link',
                'visited',
                'active',
                'hover',
                'focus'
            )
        );
    }

    /**
     * Save the contents of CSS blocks to style matches
     * @param array $matches preg_replace style $matches array
     */
    protected function styleCallback($matches)
    {
        $this->_styleMatches[] = $matches[1];
    }

    /**
     * Removes inline <style> tags from HTML, saves them for later use
     * @param string $html
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     * @todo Extend to indicate non-text/css style blocks
     */
    public function preFilter($html, $config, $context)
    {
        $tidy = $config->get('Filter.ExtractStyleBlocks.TidyImpl');
        if ($tidy !== null) {
            $this->_tidy = $tidy;
        }
        $html = preg_replace_callback('#<style(?:\s.*)?>(.+)</style>#isU', array($this, 'styleCallback'), $html);
        $style_blocks = $this->_styleMatches;
        $this->_styleMatches = array(); // reset
        $context->register('StyleBlocks', $style_blocks); // $context must not be reused
        if ($this->_tidy) {
            foreach ($style_blocks as &$style) {
                $style = $this->cleanCSS($style, $config, $context);
            }
        }
        return $html;
    }

    /**
     * Takes CSS (the stuff found in <style>) and cleans it.
     * @warning Requires CSSTidy <http://csstidy.sourceforge.net/>
     * @param string $css CSS styling to clean
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @throws HTMLPurifier_Exception
     * @return string Cleaned CSS
     */
    public function cleanCSS($css, $config, $context)
    {
        // prepare scope
        $scope = $config->get('Filter.ExtractStyleBlocks.Scope');
        if ($scope !== null) {
            $scopes = array_map('trim', explode(',', $scope));
        } else {
            $scopes = array();
        }
        // remove comments from CSS
        $css = trim($css);
        if (strncmp('<!--', $css, 4) === 0) {
            $css = substr($css, 4);
        }
        if (strlen($css) > 3 && substr($css, -3) == '-->') {
            $css = substr($css, 0, -3);
        }
        $css = trim($css);
        set_error_handler('htmlpurifier_filter_extractstyleblocks_muteerrorhandler');
        $this->_tidy->parse($css);
        restore_error_handler();
        $css_definition = $config->getDefinition('CSS');
        $html_definition = $config->getDefinition('HTML');
        $new_css = array();
        foreach ($this->_tidy->css as $k => $decls) {
            // $decls are all CSS declarations inside an @ selector
            $new_decls = array();
            foreach ($decls as $selector => $style) {
                $selector = trim($selector);
                if ($selector === '') {
                    continue;
                } // should not happen
                // Parse the selector
                // Here is the relevant part of the CSS grammar:
                //
                // ruleset
                //   : selector [ ',' S* selector ]* '{' ...
                // selector
                //   : simple_selector [ combinator selector | S+ [ combinator? selector ]? ]?
                // combinator
                //   : '+' S*
                //   : '>' S*
                // simple_selector
                //   : element_name [ HASH | class | attrib | pseudo ]*
                //   | [ HASH | class | attrib | pseudo ]+
                // element_name
                //   : IDENT | '*'
                //   ;
                // class
                //   : '.' IDENT
                //   ;
                // attrib
                //   : '[' S* IDENT S* [ [ '=' | INCLUDES | DASHMATCH ] S*
                //     [ IDENT | STRING ] S* ]? ']'
                //   ;
                // pseudo
                //   : ':' [ IDENT | FUNCTION S* [IDENT S*]? ')' ]
                //   ;
                //
                // For reference, here are the relevant tokens:
                //
                // HASH         #{name}
                // IDENT        {ident}
                // INCLUDES     ==
                // DASHMATCH    |=
                // STRING       {string}
                // FUNCTION     {ident}\(
                //
                // And the lexical scanner tokens
                //
                // name         {nmchar}+
                // nmchar       [_a-z0-9-]|{nonascii}|{escape}
                // nonascii     [\240-\377]
                // escape       {unicode}|\\[^\r\n\f0-9a-f]
                // unicode      \\{h}}{1,6}(\r\n|[ \t\r\n\f])?
                // ident        -?{nmstart}{nmchar*}
                // nmstart      [_a-z]|{nonascii}|{escape}
                // string       {string1}|{string2}
                // string1      \"([^\n\r\f\\"]|\\{nl}|{escape})*\"
                // string2      \'([^\n\r\f\\"]|\\{nl}|{escape})*\'
                //
                // We'll implement a subset (in order to reduce attack
                // surface); in particular:
                //
                //      - No Unicode support
                //      - No escapes support
                //      - No string support (by proxy no attrib support)
                //      - element_name is matched against allowed
                //        elements (some people might find this
                //        annoying...)
                //      - Pseudo-elements one of :first-child, :link,
                //        :visited, :active, :hover, :focus

                // handle ruleset
                $selectors = array_map('trim', explode(',', $selector));
                $new_selectors = array();
                foreach ($selectors as $sel) {
                    // split on +, > and spaces
                    $basic_selectors = preg_split('/\s*([+> ])\s*/', $sel, -1, PREG_SPLIT_DELIM_CAPTURE);
                    // even indices are chunks, odd indices are
                    // delimiters
                    $nsel = null;
                    $delim = null; // guaranteed to be non-null after
                    // two loop iterations
                    for ($i = 0, $c = count($basic_selectors); $i < $c; $i++) {
                        $x = $basic_selectors[$i];
                        if ($i % 2) {
                            // delimiter
                            if ($x === ' ') {
                                $delim = ' ';
                            } else {
                                $delim = ' ' . $x . ' ';
                            }
                        } else {
                            // simple selector
                            $components = preg_split('/([#.:])/', $x, -1, PREG_SPLIT_DELIM_CAPTURE);
                            $sdelim = null;
                            $nx = null;
                            for ($j = 0, $cc = count($components); $j < $cc; $j++) {
                                $y = $components[$j];
                                if ($j === 0) {
                                    if ($y === '*' || isset($html_definition->info[$y = strtolower($y)])) {
                                        $nx = $y;
                                    } else {
                                        // $nx stays null; this matters
                                        // if we don't manage to find
                                        // any valid selector content,
                                        // in which case we ignore the
                                        // outer $delim
                                    }
                                } elseif ($j % 2) {
                                    // set delimiter
                                    $sdelim = $y;
                                } else {
                                    $attrdef = null;
                                    if ($sdelim === '#') {
                                        $attrdef = $this->_id_attrdef;
                                    } elseif ($sdelim === '.') {
                                        $attrdef = $this->_class_attrdef;
                                    } elseif ($sdelim === ':') {
                                        $attrdef = $this->_enum_attrdef;
                                    } else {
                                        throw new HTMLPurifier_Exception('broken invariant sdelim and preg_split');
                                    }
                                    $r = $attrdef->validate($y, $config, $context);
                                    if ($r !== false) {
                                        if ($r !== true) {
                                            $y = $r;
                                        }
                                        if ($nx === null) {
                                            $nx = '';
                                        }
                                        $nx .= $sdelim . $y;
                                    }
                                }
                            }
                            if ($nx !== null) {
                                if ($nsel === null) {
                                    $nsel = $nx;
                                } else {
                                    $nsel .= $delim . $nx;
                                }
                            } else {
                                // delimiters to the left of invalid
                                // basic selector ignored
                            }
                        }
                    }
                    if ($nsel !== null) {
                        if (!empty($scopes)) {
                            foreach ($scopes as $s) {
                                $new_selectors[] = "$s $nsel";
                            }
                        } else {
                            $new_selectors[] = $nsel;
                        }
                    }
                }
                if (empty($new_selectors)) {
                    continue;
                }
                $selector = implode(', ', $new_selectors);
                foreach ($style as $name => $value) {
                    if (!isset($css_definition->info[$name])) {
                        unset($style[$name]);
                        continue;
                    }
                    $def = $css_definition->info[$name];
                    $ret = $def->validate($value, $config, $context);
                    if ($ret === false) {
                        unset($style[$name]);
                    } else {
                        $style[$name] = $ret;
                    }
                }
                $new_decls[$selector] = $style;
            }
            $new_css[$k] = $new_decls;
        }
        // remove stuff that shouldn't be used, could be reenabled
        // after security risks are analyzed
        $this->_tidy->css = $new_css;
        $this->_tidy->import = array();
        $this->_tidy->charset = null;
        $this->_tidy->namespace = null;
        $css = $this->_tidy->print->plain();
        // we are going to escape any special characters <>& to ensure
        // that no funny business occurs (i.e. </style> in a font-family prop).
        if ($config->get('Filter.ExtractStyleBlocks.Escaping')) {
            $css = str_replace(
                array('<', '>', '&'),
                array('\3C ', '\3E ', '\26 '),
                $css
            );
        }
        return $css;
    }
}

// vim: et sw=4 sts=4

