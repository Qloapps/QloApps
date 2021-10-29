<?php

/**
 * Parser that uses PHP 5's DOM extension (part of the core).
 *
 * In PHP 5, the DOM XML extension was revamped into DOM and added to the core.
 * It gives us a forgiving HTML parser, which we use to transform the HTML
 * into a DOM, and then into the tokens.  It is blazingly fast (for large
 * documents, it performs twenty times faster than
 * HTMLPurifier_Lexer_DirectLex,and is the default choice for PHP 5.
 *
 * @note Any empty elements will have empty tokens associated with them, even if
 * this is prohibited by the spec. This is cannot be fixed until the spec
 * comes into play.
 *
 * @note PHP's DOM extension does not actually parse any entities, we use
 *       our own function to do that.
 *
 * @warning DOM tends to drop whitespace, which may wreak havoc on indenting.
 *          If this is a huge problem, due to the fact that HTML is hand
 *          edited and you are unable to get a parser cache that caches the
 *          the output of HTML Purifier while keeping the original HTML lying
 *          around, you may want to run Tidy on the resulting output or use
 *          HTMLPurifier_DirectLex
 */

class HTMLPurifier_Lexer_DOMLex extends HTMLPurifier_Lexer
{

    /**
     * @type HTMLPurifier_TokenFactory
     */
    private $factory;

    public function __construct()
    {
        // setup the factory
        parent::__construct();
        $this->factory = new HTMLPurifier_TokenFactory();
    }

    /**
     * @param string $html
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return HTMLPurifier_Token[]
     */
    public function tokenizeHTML($html, $config, $context)
    {
        $html = $this->normalize($html, $config, $context);

        // attempt to armor stray angled brackets that cannot possibly
        // form tags and thus are probably being used as emoticons
        if ($config->get('Core.AggressivelyFixLt')) {
            $char = '[^a-z!\/]';
            $comment = "/<!--(.*?)(-->|\z)/is";
            $html = preg_replace_callback($comment, array($this, 'callbackArmorCommentEntities'), $html);
            do {
                $old = $html;
                $html = preg_replace("/<($char)/i", '&lt;\\1', $html);
            } while ($html !== $old);
            $html = preg_replace_callback($comment, array($this, 'callbackUndoCommentSubst'), $html); // fix comments
        }

        // preprocess html, essential for UTF-8
        $html = $this->wrapHTML($html, $config, $context);

        $doc = new DOMDocument();
        $doc->encoding = 'UTF-8'; // theoretically, the above has this covered

        $options = 0;
        if ($config->get('Core.AllowParseManyTags') && defined('LIBXML_PARSEHUGE')) {
            $options |= LIBXML_PARSEHUGE;
        }

        set_error_handler(array($this, 'muteErrorHandler'));
        // loadHTML() fails on PHP 5.3 when second parameter is given
        if ($options) {
            $doc->loadHTML($html, $options);
        } else {
            $doc->loadHTML($html);
        }
        restore_error_handler();

        $body = $doc->getElementsByTagName('html')->item(0)-> // <html>
                      getElementsByTagName('body')->item(0);  // <body>

        $div = $body->getElementsByTagName('div')->item(0); // <div>
        $tokens = array();
        $this->tokenizeDOM($div, $tokens, $config);
        // If the div has a sibling, that means we tripped across
        // a premature </div> tag.  So remove the div we parsed,
        // and then tokenize the rest of body.  We can't tokenize
        // the sibling directly as we'll lose the tags in that case.
        if ($div->nextSibling) {
            $body->removeChild($div);
            $this->tokenizeDOM($body, $tokens, $config);
        }
        return $tokens;
    }

    /**
     * Iterative function that tokenizes a node, putting it into an accumulator.
     * To iterate is human, to recurse divine - L. Peter Deutsch
     * @param DOMNode $node DOMNode to be tokenized.
     * @param HTMLPurifier_Token[] $tokens   Array-list of already tokenized tokens.
     * @return HTMLPurifier_Token of node appended to previously passed tokens.
     */
    protected function tokenizeDOM($node, &$tokens, $config)
    {
        $level = 0;
        $nodes = array($level => new HTMLPurifier_Queue(array($node)));
        $closingNodes = array();
        do {
            while (!$nodes[$level]->isEmpty()) {
                $node = $nodes[$level]->shift(); // FIFO
                $collect = $level > 0 ? true : false;
                $needEndingTag = $this->createStartNode($node, $tokens, $collect, $config);
                if ($needEndingTag) {
                    $closingNodes[$level][] = $node;
                }
                if ($node->childNodes && $node->childNodes->length) {
                    $level++;
                    $nodes[$level] = new HTMLPurifier_Queue();
                    foreach ($node->childNodes as $childNode) {
                        $nodes[$level]->push($childNode);
                    }
                }
            }
            $level--;
            if ($level && isset($closingNodes[$level])) {
                while ($node = array_pop($closingNodes[$level])) {
                    $this->createEndNode($node, $tokens);
                }
            }
        } while ($level > 0);
    }

    /**
     * Portably retrieve the tag name of a node; deals with older versions
     * of libxml like 2.7.6
     * @param DOMNode $node
     */
    protected function getTagName($node)
    {
        if (isset($node->tagName)) {
            return $node->tagName;
        } else if (isset($node->nodeName)) {
            return $node->nodeName;
        } else if (isset($node->localName)) {
            return $node->localName;
        }
        return null;
    }

    /**
     * Portably retrieve the data of a node; deals with older versions
     * of libxml like 2.7.6
     * @param DOMNode $node
     */
    protected function getData($node)
    {
        if (isset($node->data)) {
            return $node->data;
        } else if (isset($node->nodeValue)) {
            return $node->nodeValue;
        } else if (isset($node->textContent)) {
            return $node->textContent;
        }
        return null;
    }


    /**
     * @param DOMNode $node DOMNode to be tokenized.
     * @param HTMLPurifier_Token[] $tokens   Array-list of already tokenized tokens.
     * @param bool $collect  Says whether or start and close are collected, set to
     *                    false at first recursion because it's the implicit DIV
     *                    tag you're dealing with.
     * @return bool if the token needs an endtoken
     * @todo data and tagName properties don't seem to exist in DOMNode?
     */
    protected function createStartNode($node, &$tokens, $collect, $config)
    {
        // intercept non element nodes. WE MUST catch all of them,
        // but we're not getting the character reference nodes because
        // those should have been preprocessed
        if ($node->nodeType === XML_TEXT_NODE) {
            $data = $this->getData($node); // Handle variable data property
            if ($data !== null) {
              $tokens[] = $this->factory->createText($data);
            }
            return false;
        } elseif ($node->nodeType === XML_CDATA_SECTION_NODE) {
            // undo libxml's special treatment of <script> and <style> tags
            $last = end($tokens);
            $data = $node->data;
            // (note $node->tagname is already normalized)
            if ($last instanceof HTMLPurifier_Token_Start && ($last->name == 'script' || $last->name == 'style')) {
                $new_data = trim($data);
                if (substr($new_data, 0, 4) === '<!--') {
                    $data = substr($new_data, 4);
                    if (substr($data, -3) === '-->') {
                        $data = substr($data, 0, -3);
                    } else {
                        // Highly suspicious! Not sure what to do...
                    }
                }
            }
            $tokens[] = $this->factory->createText($this->parseText($data, $config));
            return false;
        } elseif ($node->nodeType === XML_COMMENT_NODE) {
            // this is code is only invoked for comments in script/style in versions
            // of libxml pre-2.6.28 (regular comments, of course, are still
            // handled regularly)
            $tokens[] = $this->factory->createComment($node->data);
            return false;
        } elseif ($node->nodeType !== XML_ELEMENT_NODE) {
            // not-well tested: there may be other nodes we have to grab
            return false;
        }
        $attr = $node->hasAttributes() ? $this->transformAttrToAssoc($node->attributes) : array();
        $tag_name = $this->getTagName($node); // Handle variable tagName property
        if (empty($tag_name)) {
            return (bool) $node->childNodes->length;
        }
        // We still have to make sure that the element actually IS empty
        if (!$node->childNodes->length) {
            if ($collect) {
                $tokens[] = $this->factory->createEmpty($tag_name, $attr);
            }
            return false;
        } else {
            if ($collect) {
                $tokens[] = $this->factory->createStart($tag_name, $attr);
            }
            return true;
        }
    }

    /**
     * @param DOMNode $node
     * @param HTMLPurifier_Token[] $tokens
     */
    protected function createEndNode($node, &$tokens)
    {
        $tag_name = $this->getTagName($node); // Handle variable tagName property
        $tokens[] = $this->factory->createEnd($tag_name);
    }

    /**
     * Converts a DOMNamedNodeMap of DOMAttr objects into an assoc array.
     *
     * @param DOMNamedNodeMap $node_map DOMNamedNodeMap of DOMAttr objects.
     * @return array Associative array of attributes.
     */
    protected function transformAttrToAssoc($node_map)
    {
        // NamedNodeMap is documented very well, so we're using undocumented
        // features, namely, the fact that it implements Iterator and
        // has a ->length attribute
        if ($node_map->length === 0) {
            return array();
        }
        $array = array();
        foreach ($node_map as $attr) {
            $array[$attr->name] = $attr->value;
        }
        return $array;
    }

    /**
     * An error handler that mutes all errors
     * @param int $errno
     * @param string $errstr
     */
    public function muteErrorHandler($errno, $errstr)
    {
    }

    /**
     * Callback function for undoing escaping of stray angled brackets
     * in comments
     * @param array $matches
     * @return string
     */
    public function callbackUndoCommentSubst($matches)
    {
        return '<!--' . strtr($matches[1], array('&amp;' => '&', '&lt;' => '<')) . $matches[2];
    }

    /**
     * Callback function that entity-izes ampersands in comments so that
     * callbackUndoCommentSubst doesn't clobber them
     * @param array $matches
     * @return string
     */
    public function callbackArmorCommentEntities($matches)
    {
        return '<!--' . str_replace('&', '&amp;', $matches[1]) . $matches[2];
    }

    /**
     * Wraps an HTML fragment in the necessary HTML
     * @param string $html
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     */
    protected function wrapHTML($html, $config, $context, $use_div = true)
    {
        $def = $config->getDefinition('HTML');
        $ret = '';

        if (!empty($def->doctype->dtdPublic) || !empty($def->doctype->dtdSystem)) {
            $ret .= '<!DOCTYPE html ';
            if (!empty($def->doctype->dtdPublic)) {
                $ret .= 'PUBLIC "' . $def->doctype->dtdPublic . '" ';
            }
            if (!empty($def->doctype->dtdSystem)) {
                $ret .= '"' . $def->doctype->dtdSystem . '" ';
            }
            $ret .= '>';
        }

        $ret .= '<html><head>';
        $ret .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        // No protection if $html contains a stray </div>!
        $ret .= '</head><body>';
        if ($use_div) $ret .= '<div>';
        $ret .= $html;
        if ($use_div) $ret .= '</div>';
        $ret .= '</body></html>';
        return $ret;
    }
}

// vim: et sw=4 sts=4
