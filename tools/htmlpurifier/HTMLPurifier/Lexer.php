<?php

/**
 * Forgivingly lexes HTML (SGML-style) markup into tokens.
 *
 * A lexer parses a string of SGML-style markup and converts them into
 * corresponding tokens.  It doesn't check for well-formedness, although its
 * internal mechanism may make this automatic (such as the case of
 * HTMLPurifier_Lexer_DOMLex).  There are several implementations to choose
 * from.
 *
 * A lexer is HTML-oriented: it might work with XML, but it's not
 * recommended, as we adhere to a subset of the specification for optimization
 * reasons. This might change in the future. Also, most tokenizers are not
 * expected to handle DTDs or PIs.
 *
 * This class should not be directly instantiated, but you may use create() to
 * retrieve a default copy of the lexer.  Being a supertype, this class
 * does not actually define any implementation, but offers commonly used
 * convenience functions for subclasses.
 *
 * @note The unit tests will instantiate this class for testing purposes, as
 *       many of the utility functions require a class to be instantiated.
 *       This means that, even though this class is not runnable, it will
 *       not be declared abstract.
 *
 * @par
 *
 * @note
 * We use tokens rather than create a DOM representation because DOM would:
 *
 * @par
 *  -# Require more processing and memory to create,
 *  -# Is not streamable, and
 *  -# Has the entire document structure (html and body not needed).
 *
 * @par
 * However, DOM is helpful in that it makes it easy to move around nodes
 * without a lot of lookaheads to see when a tag is closed. This is a
 * limitation of the token system and some workarounds would be nice.
 */
class HTMLPurifier_Lexer
{

    /**
     * Whether or not this lexer implements line-number/column-number tracking.
     * If it does, set to true.
     */
    public $tracksLineNumbers = false;

    /**
     * @type HTMLPurifier_EntityParser
     */
    private $_entity_parser;

    // -- STATIC ----------------------------------------------------------

    /**
     * Retrieves or sets the default Lexer as a Prototype Factory.
     *
     * By default HTMLPurifier_Lexer_DOMLex will be returned. There are
     * a few exceptions involving special features that only DirectLex
     * implements.
     *
     * @note The behavior of this class has changed, rather than accepting
     *       a prototype object, it now accepts a configuration object.
     *       To specify your own prototype, set %Core.LexerImpl to it.
     *       This change in behavior de-singletonizes the lexer object.
     *
     * @param HTMLPurifier_Config $config
     * @return HTMLPurifier_Lexer
     * @throws HTMLPurifier_Exception
     */
    public static function create($config)
    {
        if (!($config instanceof HTMLPurifier_Config)) {
            $lexer = $config;
            trigger_error(
                "Passing a prototype to
                HTMLPurifier_Lexer::create() is deprecated, please instead
                use %Core.LexerImpl",
                E_USER_WARNING
            );
        } else {
            $lexer = $config->get('Core.LexerImpl');
        }

        $needs_tracking =
            $config->get('Core.MaintainLineNumbers') ||
            $config->get('Core.CollectErrors');

        $inst = null;
        if (is_object($lexer)) {
            $inst = $lexer;
        } else {
            if (is_null($lexer)) {
                do {
                    // auto-detection algorithm
                    if ($needs_tracking) {
                        $lexer = 'DirectLex';
                        break;
                    }

                    if (class_exists('DOMDocument') &&
                        method_exists('DOMDocument', 'loadHTML') &&
                        !extension_loaded('domxml')
                    ) {
                        // check for DOM support, because while it's part of the
                        // core, it can be disabled compile time. Also, the PECL
                        // domxml extension overrides the default DOM, and is evil
                        // and nasty and we shan't bother to support it
                        $lexer = 'DOMLex';
                    } else {
                        $lexer = 'DirectLex';
                    }
                } while (0);
            } // do..while so we can break

            // instantiate recognized string names
            switch ($lexer) {
                case 'DOMLex':
                    $inst = new HTMLPurifier_Lexer_DOMLex();
                    break;
                case 'DirectLex':
                    $inst = new HTMLPurifier_Lexer_DirectLex();
                    break;
                case 'PH5P':
                    $inst = new HTMLPurifier_Lexer_PH5P();
                    break;
                default:
                    throw new HTMLPurifier_Exception(
                        "Cannot instantiate unrecognized Lexer type " .
                        htmlspecialchars($lexer)
                    );
            }
        }

        if (!$inst) {
            throw new HTMLPurifier_Exception('No lexer was instantiated');
        }

        // once PHP DOM implements native line numbers, or we
        // hack out something using XSLT, remove this stipulation
        if ($needs_tracking && !$inst->tracksLineNumbers) {
            throw new HTMLPurifier_Exception(
                'Cannot use lexer that does not support line numbers with ' .
                'Core.MaintainLineNumbers or Core.CollectErrors (use DirectLex instead)'
            );
        }

        return $inst;

    }

    // -- CONVENIENCE MEMBERS ---------------------------------------------

    public function __construct()
    {
        $this->_entity_parser = new HTMLPurifier_EntityParser();
    }

    /**
     * Most common entity to raw value conversion table for special entities.
     * @type array
     */
    protected $_special_entity2str =
        array(
            '&quot;' => '"',
            '&amp;' => '&',
            '&lt;' => '<',
            '&gt;' => '>',
            '&#39;' => "'",
            '&#039;' => "'",
            '&#x27;' => "'"
        );

    public function parseText($string, $config) {
        return $this->parseData($string, false, $config);
    }

    public function parseAttr($string, $config) {
        return $this->parseData($string, true, $config);
    }

    /**
     * Parses special entities into the proper characters.
     *
     * This string will translate escaped versions of the special characters
     * into the correct ones.
     *
     * @param string $string String character data to be parsed.
     * @return string Parsed character data.
     */
    public function parseData($string, $is_attr, $config)
    {
        // following functions require at least one character
        if ($string === '') {
            return '';
        }

        // subtracts amps that cannot possibly be escaped
        $num_amp = substr_count($string, '&') - substr_count($string, '& ') -
            ($string[strlen($string) - 1] === '&' ? 1 : 0);

        if (!$num_amp) {
            return $string;
        } // abort if no entities
        $num_esc_amp = substr_count($string, '&amp;');
        $string = strtr($string, $this->_special_entity2str);

        // code duplication for sake of optimization, see above
        $num_amp_2 = substr_count($string, '&') - substr_count($string, '& ') -
            ($string[strlen($string) - 1] === '&' ? 1 : 0);

        if ($num_amp_2 <= $num_esc_amp) {
            return $string;
        }

        // hmm... now we have some uncommon entities. Use the callback.
        if ($config->get('Core.LegacyEntityDecoder')) {
            $string = $this->_entity_parser->substituteSpecialEntities($string);
        } else {
            if ($is_attr) {
                $string = $this->_entity_parser->substituteAttrEntities($string);
            } else {
                $string = $this->_entity_parser->substituteTextEntities($string);
            }
        }
        return $string;
    }

    /**
     * Lexes an HTML string into tokens.
     * @param $string String HTML.
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return HTMLPurifier_Token[] array representation of HTML.
     */
    public function tokenizeHTML($string, $config, $context)
    {
        trigger_error('Call to abstract class', E_USER_ERROR);
    }

    /**
     * Translates CDATA sections into regular sections (through escaping).
     * @param string $string HTML string to process.
     * @return string HTML with CDATA sections escaped.
     */
    protected static function escapeCDATA($string)
    {
        return preg_replace_callback(
            '/<!\[CDATA\[(.+?)\]\]>/s',
            array('HTMLPurifier_Lexer', 'CDATACallback'),
            $string
        );
    }

    /**
     * Special CDATA case that is especially convoluted for <script>
     * @param string $string HTML string to process.
     * @return string HTML with CDATA sections escaped.
     */
    protected static function escapeCommentedCDATA($string)
    {
        return preg_replace_callback(
            '#<!--//--><!\[CDATA\[//><!--(.+?)//--><!\]\]>#s',
            array('HTMLPurifier_Lexer', 'CDATACallback'),
            $string
        );
    }

    /**
     * Callback function for escapeCDATA() that does the work.
     *
     * @warning Though this is public in order to let the callback happen,
     *          calling it directly is not recommended.
     * @param array $matches PCRE matches array, with index 0 the entire match
     *                  and 1 the inside of the CDATA section.
     * @return string Escaped internals of the CDATA section.
     */
    protected static function CDATACallback($matches)
    {
        // not exactly sure why the character set is needed, but whatever
        return htmlspecialchars($matches[1], ENT_COMPAT, 'UTF-8');
    }

    /**
     * Takes a piece of HTML and normalizes it by converting entities, fixing
     * encoding, extracting bits, and other good stuff.
     * @param string $html HTML.
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     * @todo Consider making protected
     */
    public function normalize($html, $config, $context)
    {
        // normalize newlines to \n
        if ($config->get('Core.NormalizeNewlines')) {
            $html = str_replace("\r\n", "\n", (string)$html);
            $html = str_replace("\r", "\n", (string)$html);
        }

        if ($config->get('HTML.Trusted')) {
            // escape convoluted CDATA
            $html = $this->escapeCommentedCDATA($html);
        }

        // escape CDATA
        $html = $this->escapeCDATA($html);

        // extract body from document if applicable
        if ($config->get('Core.ConvertDocumentToFragment')) {
            $e = false;
            if ($config->get('Core.CollectErrors')) {
                $e =& $context->get('ErrorCollector');
            }
            $new_html = $this->extractBody($html);
            if ($e && $new_html != $html) {
                $e->send(E_WARNING, 'Lexer: Extracted body');
            }
            $html = $new_html;
        }

        // expand entities that aren't the big five
        if ($config->get('Core.LegacyEntityDecoder')) {
            $html = $this->_entity_parser->substituteNonSpecialEntities($html);
        }

        // clean into wellformed UTF-8 string for an SGML context: this has
        // to be done after entity expansion because the entities sometimes
        // represent non-SGML characters (horror, horror!)
        $html = HTMLPurifier_Encoder::cleanUTF8($html);

        // if processing instructions are to removed, remove them now
        if ($config->get('Core.RemoveProcessingInstructions')) {
            $html = preg_replace('#<\?.+?\?>#s', '', $html);
        }

        $hidden_elements = $config->get('Core.HiddenElements');
        if ($config->get('Core.AggressivelyRemoveScript') &&
            !($config->get('HTML.Trusted') || !$config->get('Core.RemoveScriptContents')
            || empty($hidden_elements["script"]))) {
            $html = preg_replace('#<script[^>]*>.*?</script>#i', '', $html);
        }

        return $html;
    }

    /**
     * Takes a string of HTML (fragment or document) and returns the content
     * @todo Consider making protected
     */
    public function extractBody($html)
    {
        $matches = array();
        $result = preg_match('|(.*?)<body[^>]*>(.*)</body>|is', $html, $matches);
        if ($result) {
            // Make sure it's not in a comment
            $comment_start = strrpos($matches[1], '<!--');
            $comment_end   = strrpos($matches[1], '-->');
            if ($comment_start === false ||
                ($comment_end !== false && $comment_end > $comment_start)) {
                return $matches[2];
            }
        }
        return $html;
    }
}

// vim: et sw=4 sts=4
