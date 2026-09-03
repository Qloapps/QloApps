<?php

/**
 * Validates a font family list according to CSS spec
 */
class HTMLPurifier_AttrDef_CSS_FontFamily extends HTMLPurifier_AttrDef
{

    protected $mask = null;

    public function __construct()
    {
        // Lowercase letters
        $l = range('a', 'z');
        // Uppercase letters
        $u = range('A', 'Z');
        // Digits
        $d = range('0', '9');
        // Special bytes used by UTF-8
        $b = array_map('chr', range(0x80, 0xFF));
        // All valid characters for the mask
        $c = array_merge($l, $u, $d, $b);
        // Concatenate all valid characters into a string 
        // Use '_- ' as an initial value
        $this->mask = array_reduce($c, function ($carry, $value) {
            return $carry . $value;
        }, '_- ');

        /*
            PHP's internal strcspn implementation is
            O(length of string * length of mask), making it inefficient
            for large masks.  However, it's still faster than
            preg_match 8)
          for (p = s1;;) {
            spanp = s2;
            do {
              if (*spanp == c || p == s1_end) {
                return p - s1;
              }
            } while (spanp++ < (s2_end - 1));
            c = *++p;
          }
         */
        // possible optimization: invert the mask.
    }

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        static $generic_names = array(
            'serif' => true,
            'sans-serif' => true,
            'monospace' => true,
            'fantasy' => true,
            'cursive' => true
        );
        $allowed_fonts = $config->get('CSS.AllowedFonts');

        // assume that no font names contain commas in them
        $fonts = explode(',', $string);
        $final = '';
        foreach ($fonts as $font) {
            $font = trim($font);
            if ($font === '') {
                continue;
            }
            // match a generic name
            if (isset($generic_names[$font])) {
                if ($allowed_fonts === null || isset($allowed_fonts[$font])) {
                    $final .= $font . ', ';
                }
                continue;
            }
            // match a quoted name
            if ($font[0] === '"' || $font[0] === "'") {
                $length = strlen($font);
                if ($length <= 2) {
                    continue;
                }
                $quote = $font[0];
                if ($font[$length - 1] !== $quote) {
                    continue;
                }
                $font = substr($font, 1, $length - 2);
            }

            $font = $this->expandCSSEscape($font);

            // $font is a pure representation of the font name

            if ($allowed_fonts !== null && !isset($allowed_fonts[$font])) {
                continue;
            }

            if (ctype_alnum($font) && $font !== '') {
                // very simple font, allow it in unharmed
                $final .= $font . ', ';
                continue;
            }

            // bugger out on whitespace.  form feed (0C) really
            // shouldn't show up regardless
            $font = str_replace(array("\n", "\t", "\r", "\x0C"), ' ', $font);

            // Here, there are various classes of characters which need
            // to be treated differently:
            //  - Alphanumeric characters are essentially safe.  We
            //    handled these above.
            //  - Spaces require quoting, though most parsers will do
            //    the right thing if there aren't any characters that
            //    can be misinterpreted
            //  - Dashes rarely occur, but they fairly unproblematic
            //    for parsing/rendering purposes.
            //  The above characters cover the majority of Western font
            //  names.
            //  - Arbitrary Unicode characters not in ASCII.  Because
            //    most parsers give little thought to Unicode, treatment
            //    of these codepoints is basically uniform, even for
            //    punctuation-like codepoints.  These characters can
            //    show up in non-Western pages and are supported by most
            //    major browsers, for example: "ＭＳ 明朝" is a
            //    legitimate font-name
            //    <http://ja.wikipedia.org/wiki/MS_明朝>.  See
            //    the CSS3 spec for more examples:
            //    <http://www.w3.org/TR/2011/WD-css3-fonts-20110324/localizedfamilynames.png>
            //    You can see live samples of these on the Internet:
            //    <http://www.google.co.jp/search?q=font-family+ＭＳ+明朝|ゴシック>
            //    However, most of these fonts have ASCII equivalents:
            //    for example, 'MS Mincho', and it's considered
            //    professional to use ASCII font names instead of
            //    Unicode font names.  Thanks Takeshi Terada for
            //    providing this information.
            //  The following characters, to my knowledge, have not been
            //  used to name font names.
            //  - Single quote.  While theoretically you might find a
            //    font name that has a single quote in its name (serving
            //    as an apostrophe, e.g. Dave's Scribble), I haven't
            //    been able to find any actual examples of this.
            //    Internet Explorer's cssText translation (which I
            //    believe is invoked by innerHTML) normalizes any
            //    quoting to single quotes, and fails to escape single
            //    quotes.  (Note that this is not IE's behavior for all
            //    CSS properties, just some sort of special casing for
            //    font-family).  So a single quote *cannot* be used
            //    safely in the font-family context if there will be an
            //    innerHTML/cssText translation.  Note that Firefox 3.x
            //    does this too.
            //  - Double quote.  In IE, these get normalized to
            //    single-quotes, no matter what the encoding.  (Fun
            //    fact, in IE8, the 'content' CSS property gained
            //    support, where they special cased to preserve encoded
            //    double quotes, but still translate unadorned double
            //    quotes into single quotes.)  So, because their
            //    fixpoint behavior is identical to single quotes, they
            //    cannot be allowed either.  Firefox 3.x displays
            //    single-quote style behavior.
            //  - Backslashes are reduced by one (so \\ -> \) every
            //    iteration, so they cannot be used safely.  This shows
            //    up in IE7, IE8 and FF3
            //  - Semicolons, commas and backticks are handled properly.
            //  - The rest of the ASCII punctuation is handled properly.
            // We haven't checked what browsers do to unadorned
            // versions, but this is not important as long as the
            // browser doesn't /remove/ surrounding quotes (as IE does
            // for HTML).
            //
            // With these results in hand, we conclude that there are
            // various levels of safety:
            //  - Paranoid: alphanumeric, spaces and dashes(?)
            //  - International: Paranoid + non-ASCII Unicode
            //  - Edgy: Everything except quotes, backslashes
            //  - NoJS: Standards compliance, e.g. sod IE. Note that
            //    with some judicious character escaping (since certain
            //    types of escaping doesn't work) this is theoretically
            //    OK as long as innerHTML/cssText is not called.
            // We believe that international is a reasonable default
            // (that we will implement now), and once we do more
            // extensive research, we may feel comfortable with dropping
            // it down to edgy.

            // Edgy: alphanumeric, spaces, dashes, underscores and Unicode.  Use of
            // str(c)spn assumes that the string was already well formed
            // Unicode (which of course it is).
            if (strspn($font, $this->mask) !== strlen($font)) {
                continue;
            }

            // Historical:
            // In the absence of innerHTML/cssText, these ugly
            // transforms don't pose a security risk (as \\ and \"
            // might--these escapes are not supported by most browsers).
            // We could try to be clever and use single-quote wrapping
            // when there is a double quote present, but I have chosen
            // not to implement that.  (NOTE: you can reduce the amount
            // of escapes by one depending on what quoting style you use)
            // $font = str_replace('\\', '\\5C ', $font);
            // $font = str_replace('"',  '\\22 ', $font);
            // $font = str_replace("'",  '\\27 ', $font);

            // font possibly with spaces, requires quoting
            $final .= "'$font', ";
        }
        $final = rtrim($final, ', ');
        if ($final === '') {
            return false;
        }
        return $final;
    }

}

// vim: et sw=4 sts=4
