<?php

/**
 * Takes tokens makes them well-formed (balance end tags, etc.)
 *
 * Specification of the armor attributes this strategy uses:
 *
 *      - MakeWellFormed_TagClosedError: This armor field is used to
 *        suppress tag closed errors for certain tokens [TagClosedSuppress],
 *        in particular, if a tag was generated automatically by HTML
 *        Purifier, we may rely on our infrastructure to close it for us
 *        and shouldn't report an error to the user [TagClosedAuto].
 */
class HTMLPurifier_Strategy_MakeWellFormed extends HTMLPurifier_Strategy
{

    /**
     * Array stream of tokens being processed.
     * @type HTMLPurifier_Token[]
     */
    protected $tokens;

    /**
     * Current token.
     * @type HTMLPurifier_Token
     */
    protected $token;

    /**
     * Zipper managing the true state.
     * @type HTMLPurifier_Zipper
     */
    protected $zipper;

    /**
     * Current nesting of elements.
     * @type array
     */
    protected $stack;

    /**
     * Injectors active in this stream processing.
     * @type HTMLPurifier_Injector[]
     */
    protected $injectors;

    /**
     * Current instance of HTMLPurifier_Config.
     * @type HTMLPurifier_Config
     */
    protected $config;

    /**
     * Current instance of HTMLPurifier_Context.
     * @type HTMLPurifier_Context
     */
    protected $context;

    /**
     * @param HTMLPurifier_Token[] $tokens
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return HTMLPurifier_Token[]
     * @throws HTMLPurifier_Exception
     */
    public function execute($tokens, $config, $context)
    {
        $definition = $config->getHTMLDefinition();

        // local variables
        $generator = new HTMLPurifier_Generator($config, $context);
        $escape_invalid_tags = $config->get('Core.EscapeInvalidTags');
        // used for autoclose early abortion
        $global_parent_allowed_elements = $definition->info_parent_def->child->getAllowedElements($config);
        $e = $context->get('ErrorCollector', true);
        $i = false; // injector index
        list($zipper, $token) = HTMLPurifier_Zipper::fromArray($tokens);
        if ($token === NULL) {
            return array();
        }
        $reprocess = false; // whether or not to reprocess the same token
        $stack = array();

        // member variables
        $this->stack =& $stack;
        $this->tokens =& $tokens;
        $this->token =& $token;
        $this->zipper =& $zipper;
        $this->config = $config;
        $this->context = $context;

        // context variables
        $context->register('CurrentNesting', $stack);
        $context->register('InputZipper', $zipper);
        $context->register('CurrentToken', $token);

        // -- begin INJECTOR --

        $this->injectors = array();

        $injectors = $config->getBatch('AutoFormat');
        $def_injectors = $definition->info_injector;
        $custom_injectors = $injectors['Custom'];
        unset($injectors['Custom']); // special case
        foreach ($injectors as $injector => $b) {
            // XXX: Fix with a legitimate lookup table of enabled filters
            if (strpos($injector, '.') !== false) {
                continue;
            }
            $injector = "HTMLPurifier_Injector_$injector";
            if (!$b) {
                continue;
            }
            $this->injectors[] = new $injector;
        }
        foreach ($def_injectors as $injector) {
            // assumed to be objects
            $this->injectors[] = $injector;
        }
        foreach ($custom_injectors as $injector) {
            if (!$injector) {
                continue;
            }
            if (is_string($injector)) {
                $injector = "HTMLPurifier_Injector_$injector";
                $injector = new $injector;
            }
            $this->injectors[] = $injector;
        }

        // give the injectors references to the definition and context
        // variables for performance reasons
        foreach ($this->injectors as $ix => $injector) {
            $error = $injector->prepare($config, $context);
            if (!$error) {
                continue;
            }
            array_splice($this->injectors, $ix, 1); // rm the injector
            trigger_error("Cannot enable {$injector->name} injector because $error is not allowed", E_USER_WARNING);
        }

        // -- end INJECTOR --

        // a note on reprocessing:
        //      In order to reduce code duplication, whenever some code needs
        //      to make HTML changes in order to make things "correct", the
        //      new HTML gets sent through the purifier, regardless of its
        //      status. This means that if we add a start token, because it
        //      was totally necessary, we don't have to update nesting; we just
        //      punt ($reprocess = true; continue;) and it does that for us.

        // isset is in loop because $tokens size changes during loop exec
        for (;;
             // only increment if we don't need to reprocess
             $reprocess ? $reprocess = false : $token = $zipper->next($token)) {

            // check for a rewind
            if (is_int($i)) {
                // possibility: disable rewinding if the current token has a
                // rewind set on it already. This would offer protection from
                // infinite loop, but might hinder some advanced rewinding.
                $rewind_offset = $this->injectors[$i]->getRewindOffset();
                if (is_int($rewind_offset)) {
                    for ($j = 0; $j < $rewind_offset; $j++) {
                        if (empty($zipper->front)) break;
                        $token = $zipper->prev($token);
                        // indicate that other injectors should not process this token,
                        // but we need to reprocess it.  See Note [Injector skips]
                        unset($token->skip[$i]);
                        $token->rewind = $i;
                        if ($token instanceof HTMLPurifier_Token_Start) {
                            array_pop($this->stack);
                        } elseif ($token instanceof HTMLPurifier_Token_End) {
                            $this->stack[] = $token->start;
                        }
                    }
                }
                $i = false;
            }

            // handle case of document end
            if ($token === NULL) {
                // kill processing if stack is empty
                if (empty($this->stack)) {
                    break;
                }

                // peek
                $top_nesting = array_pop($this->stack);
                $this->stack[] = $top_nesting;

                // send error [TagClosedSuppress]
                if ($e && !isset($top_nesting->armor['MakeWellFormed_TagClosedError'])) {
                    $e->send(E_NOTICE, 'Strategy_MakeWellFormed: Tag closed by document end', $top_nesting);
                }

                // append, don't splice, since this is the end
                $token = new HTMLPurifier_Token_End($top_nesting->name);

                // punt!
                $reprocess = true;
                continue;
            }

            //echo '<br>'; printZipper($zipper, $token);//printTokens($this->stack);
            //flush();

            // quick-check: if it's not a tag, no need to process
            if (empty($token->is_tag)) {
                if ($token instanceof HTMLPurifier_Token_Text) {
                    foreach ($this->injectors as $i => $injector) {
                        if (isset($token->skip[$i])) {
                            // See Note [Injector skips]
                            continue;
                        }
                        if ($token->rewind !== null && $token->rewind !== $i) {
                            continue;
                        }
                        // XXX fuckup
                        $r = $token;
                        $injector->handleText($r);
                        $token = $this->processToken($r, $i);
                        $reprocess = true;
                        break;
                    }
                }
                // another possibility is a comment
                continue;
            }

            if (isset($definition->info[$token->name])) {
                $type = $definition->info[$token->name]->child->type;
            } else {
                $type = false; // Type is unknown, treat accordingly
            }

            // quick tag checks: anything that's *not* an end tag
            $ok = false;
            if ($type === 'empty' && $token instanceof HTMLPurifier_Token_Start) {
                // claims to be a start tag but is empty
                $token = new HTMLPurifier_Token_Empty(
                    $token->name,
                    $token->attr,
                    $token->line,
                    $token->col,
                    $token->armor
                );
                $ok = true;
            } elseif ($type && $type !== 'empty' && $token instanceof HTMLPurifier_Token_Empty) {
                // claims to be empty but really is a start tag
                // NB: this assignment is required
                $old_token = $token;
                $token = new HTMLPurifier_Token_End($token->name);
                $token = $this->insertBefore(
                    new HTMLPurifier_Token_Start($old_token->name, $old_token->attr, $old_token->line, $old_token->col, $old_token->armor)
                );
                // punt (since we had to modify the input stream in a non-trivial way)
                $reprocess = true;
                continue;
            } elseif ($token instanceof HTMLPurifier_Token_Empty) {
                // real empty token
                $ok = true;
            } elseif ($token instanceof HTMLPurifier_Token_Start) {
                // start tag

                // ...unless they also have to close their parent
                if (!empty($this->stack)) {

                    // Performance note: you might think that it's rather
                    // inefficient, recalculating the autoclose information
                    // for every tag that a token closes (since when we
                    // do an autoclose, we push a new token into the
                    // stream and then /process/ that, before
                    // re-processing this token.)  But this is
                    // necessary, because an injector can make an
                    // arbitrary transformations to the autoclosing
                    // tokens we introduce, so things may have changed
                    // in the meantime.  Also, doing the inefficient thing is
                    // "easy" to reason about (for certain perverse definitions
                    // of "easy")

                    $parent = array_pop($this->stack);
                    $this->stack[] = $parent;

                    $parent_def = null;
                    $parent_elements = null;
                    $autoclose = false;
                    if (isset($definition->info[$parent->name])) {
                        $parent_def = $definition->info[$parent->name];
                        $parent_elements = $parent_def->child->getAllowedElements($config);
                        $autoclose = !isset($parent_elements[$token->name]);
                    }

                    if ($autoclose && $definition->info[$token->name]->wrap) {
                        // Check if an element can be wrapped by another
                        // element to make it valid in a context (for
                        // example, <ul><ul> needs a <li> in between)
                        $wrapname = $definition->info[$token->name]->wrap;
                        $wrapdef = $definition->info[$wrapname];
                        $elements = $wrapdef->child->getAllowedElements($config);
                        if (isset($elements[$token->name]) && isset($parent_elements[$wrapname])) {
                            $newtoken = new HTMLPurifier_Token_Start($wrapname);
                            $token = $this->insertBefore($newtoken);
                            $reprocess = true;
                            continue;
                        }
                    }

                    $carryover = false;
                    if ($autoclose && $parent_def->formatting) {
                        $carryover = true;
                    }

                    if ($autoclose) {
                        // check if this autoclose is doomed to fail
                        // (this rechecks $parent, which his harmless)
                        $autoclose_ok = isset($global_parent_allowed_elements[$token->name]);
                        if (!$autoclose_ok) {
                            foreach ($this->stack as $ancestor) {
                                $elements = $definition->info[$ancestor->name]->child->getAllowedElements($config);
                                if (isset($elements[$token->name])) {
                                    $autoclose_ok = true;
                                    break;
                                }
                                if ($definition->info[$token->name]->wrap) {
                                    $wrapname = $definition->info[$token->name]->wrap;
                                    $wrapdef = $definition->info[$wrapname];
                                    $wrap_elements = $wrapdef->child->getAllowedElements($config);
                                    if (isset($wrap_elements[$token->name]) && isset($elements[$wrapname])) {
                                        $autoclose_ok = true;
                                        break;
                                    }
                                }
                            }
                        }
                        if ($autoclose_ok) {
                            // errors need to be updated
                            $new_token = new HTMLPurifier_Token_End($parent->name);
                            $new_token->start = $parent;
                            // [TagClosedSuppress]
                            if ($e && !isset($parent->armor['MakeWellFormed_TagClosedError'])) {
                                if (!$carryover) {
                                    $e->send(E_NOTICE, 'Strategy_MakeWellFormed: Tag auto closed', $parent);
                                } else {
                                    $e->send(E_NOTICE, 'Strategy_MakeWellFormed: Tag carryover', $parent);
                                }
                            }
                            if ($carryover) {
                                $element = clone $parent;
                                // [TagClosedAuto]
                                $element->armor['MakeWellFormed_TagClosedError'] = true;
                                $element->carryover = true;
                                $token = $this->processToken(array($new_token, $token, $element));
                            } else {
                                $token = $this->insertBefore($new_token);
                            }
                        } else {
                            $token = $this->remove();
                        }
                        $reprocess = true;
                        continue;
                    }

                }
                $ok = true;
            }

            if ($ok) {
                foreach ($this->injectors as $i => $injector) {
                    if (isset($token->skip[$i])) {
                        // See Note [Injector skips]
                        continue;
                    }
                    if ($token->rewind !== null && $token->rewind !== $i) {
                        continue;
                    }
                    $r = $token;
                    $injector->handleElement($r);
                    $token = $this->processToken($r, $i);
                    $reprocess = true;
                    break;
                }
                if (!$reprocess) {
                    // ah, nothing interesting happened; do normal processing
                    if ($token instanceof HTMLPurifier_Token_Start) {
                        $this->stack[] = $token;
                    } elseif ($token instanceof HTMLPurifier_Token_End) {
                        throw new HTMLPurifier_Exception(
                            'Improper handling of end tag in start code; possible error in MakeWellFormed'
                        );
                    }
                }
                continue;
            }

            // sanity check: we should be dealing with a closing tag
            if (!$token instanceof HTMLPurifier_Token_End) {
                throw new HTMLPurifier_Exception('Unaccounted for tag token in input stream, bug in HTML Purifier');
            }

            // make sure that we have something open
            if (empty($this->stack)) {
                if ($escape_invalid_tags) {
                    if ($e) {
                        $e->send(E_WARNING, 'Strategy_MakeWellFormed: Unnecessary end tag to text');
                    }
                    $token = new HTMLPurifier_Token_Text($generator->generateFromToken($token));
                } else {
                    if ($e) {
                        $e->send(E_WARNING, 'Strategy_MakeWellFormed: Unnecessary end tag removed');
                    }
                    $token = $this->remove();
                }
                $reprocess = true;
                continue;
            }

            // first, check for the simplest case: everything closes neatly.
            // Eventually, everything passes through here; if there are problems
            // we modify the input stream accordingly and then punt, so that
            // the tokens get processed again.
            $current_parent = array_pop($this->stack);
            if ($current_parent->name == $token->name) {
                $token->start = $current_parent;
                foreach ($this->injectors as $i => $injector) {
                    if (isset($token->skip[$i])) {
                        // See Note [Injector skips]
                        continue;
                    }
                    if ($token->rewind !== null && $token->rewind !== $i) {
                        continue;
                    }
                    $r = $token;
                    $injector->handleEnd($r);
                    $token = $this->processToken($r, $i);
                    $this->stack[] = $current_parent;
                    $reprocess = true;
                    break;
                }
                continue;
            }

            // okay, so we're trying to close the wrong tag

            // undo the pop previous pop
            $this->stack[] = $current_parent;

            // scroll back the entire nest, trying to find our tag.
            // (feature could be to specify how far you'd like to go)
            $size = count($this->stack);
            // -2 because -1 is the last element, but we already checked that
            $skipped_tags = false;
            for ($j = $size - 2; $j >= 0; $j--) {
                if ($this->stack[$j]->name == $token->name) {
                    $skipped_tags = array_slice($this->stack, $j);
                    break;
                }
            }

            // we didn't find the tag, so remove
            if ($skipped_tags === false) {
                if ($escape_invalid_tags) {
                    if ($e) {
                        $e->send(E_WARNING, 'Strategy_MakeWellFormed: Stray end tag to text');
                    }
                    $token = new HTMLPurifier_Token_Text($generator->generateFromToken($token));
                } else {
                    if ($e) {
                        $e->send(E_WARNING, 'Strategy_MakeWellFormed: Stray end tag removed');
                    }
                    $token = $this->remove();
                }
                $reprocess = true;
                continue;
            }

            // do errors, in REVERSE $j order: a,b,c with </a></b></c>
            $c = count($skipped_tags);
            if ($e) {
                for ($j = $c - 1; $j > 0; $j--) {
                    // notice we exclude $j == 0, i.e. the current ending tag, from
                    // the errors... [TagClosedSuppress]
                    if (!isset($skipped_tags[$j]->armor['MakeWellFormed_TagClosedError'])) {
                        $e->send(E_NOTICE, 'Strategy_MakeWellFormed: Tag closed by element end', $skipped_tags[$j]);
                    }
                }
            }

            // insert tags, in FORWARD $j order: c,b,a with </a></b></c>
            $replace = array($token);
            for ($j = 1; $j < $c; $j++) {
                // ...as well as from the insertions
                $new_token = new HTMLPurifier_Token_End($skipped_tags[$j]->name);
                $new_token->start = $skipped_tags[$j];
                array_unshift($replace, $new_token);
                if (isset($definition->info[$new_token->name]) && $definition->info[$new_token->name]->formatting) {
                    // [TagClosedAuto]
                    $element = clone $skipped_tags[$j];
                    $element->carryover = true;
                    $element->armor['MakeWellFormed_TagClosedError'] = true;
                    $replace[] = $element;
                }
            }
            $token = $this->processToken($replace);
            $reprocess = true;
            continue;
        }

        $context->destroy('CurrentToken');
        $context->destroy('CurrentNesting');
        $context->destroy('InputZipper');

        unset($this->injectors, $this->stack, $this->tokens);
        return $zipper->toArray($token);
    }

    /**
     * Processes arbitrary token values for complicated substitution patterns.
     * In general:
     *
     * If $token is an array, it is a list of tokens to substitute for the
     * current token. These tokens then get individually processed. If there
     * is a leading integer in the list, that integer determines how many
     * tokens from the stream should be removed.
     *
     * If $token is a regular token, it is swapped with the current token.
     *
     * If $token is false, the current token is deleted.
     *
     * If $token is an integer, that number of tokens (with the first token
     * being the current one) will be deleted.
     *
     * @param HTMLPurifier_Token|array|int|bool $token Token substitution value
     * @param HTMLPurifier_Injector|int $injector Injector that performed the substitution; default is if
     *        this is not an injector related operation.
     * @throws HTMLPurifier_Exception
     */
    protected function processToken($token, $injector = -1)
    {
        // Zend OpCache miscompiles $token = array($token), so
        // avoid this pattern.  See: https://github.com/ezyang/htmlpurifier/issues/108

        // normalize forms of token
        if (is_object($token)) {
            $tmp = $token;
            $token = array(1, $tmp);
        }
        if (is_int($token)) {
            $tmp = $token;
            $token = array($tmp);
        }
        if ($token === false) {
            $token = array(1);
        }
        if (!is_array($token)) {
            throw new HTMLPurifier_Exception('Invalid token type from injector');
        }
        if (!is_int($token[0])) {
            array_unshift($token, 1);
        }
        if ($token[0] === 0) {
            throw new HTMLPurifier_Exception('Deleting zero tokens is not valid');
        }

        // $token is now an array with the following form:
        // array(number nodes to delete, new node 1, new node 2, ...)

        $delete = array_shift($token);
        list($old, $r) = $this->zipper->splice($this->token, $delete, $token);

        if ($injector > -1) {
            // See Note [Injector skips]
            // Determine appropriate skips.  Here's what the code does:
            //  *If* we deleted one or more tokens, copy the skips
            //  of those tokens into the skips of the new tokens (in $token).
            //  Also, mark the newly inserted tokens as having come from
            //  $injector.
            $oldskip = isset($old[0]) ? $old[0]->skip : array();
            foreach ($token as $object) {
                $object->skip = $oldskip;
                $object->skip[$injector] = true;
            }
        }

        return $r;

    }

    /**
     * Inserts a token before the current token. Cursor now points to
     * this token.  You must reprocess after this.
     * @param HTMLPurifier_Token $token
     */
    private function insertBefore($token)
    {
        // NB not $this->zipper->insertBefore(), due to positioning
        // differences
        $splice = $this->zipper->splice($this->token, 0, array($token));

        return $splice[1];
    }

    /**
     * Removes current token. Cursor now points to new token occupying previously
     * occupied space.  You must reprocess after this.
     */
    private function remove()
    {
        return $this->zipper->delete();
    }
}

// Note [Injector skips]
// ~~~~~~~~~~~~~~~~~~~~~
// When I originally designed this class, the idea behind the 'skip'
// property of HTMLPurifier_Token was to help avoid infinite loops
// in injector processing.  For example, suppose you wrote an injector
// that bolded swear words.  Naively, you might write it so that
// whenever you saw ****, you replaced it with <strong>****</strong>.
//
// When this happens, we will reprocess all of the tokens with the
// other injectors.  Now there is an opportunity for infinite loop:
// if we rerun the swear-word injector on these tokens, we might
// see **** and then reprocess again to get
// <strong><strong>****</strong></strong> ad infinitum.
//
// Thus, the idea of a skip is that once we process a token with
// an injector, we mark all of those tokens as having "come from"
// the injector, and we never run the injector again on these
// tokens.
//
// There were two more complications, however:
//
//  - With HTMLPurifier_Injector_RemoveEmpty, we noticed that if
//    you had <b><i></i></b>, after you removed the <i></i>, you
//    really would like this injector to go back and reprocess
//    the <b> tag, discovering that it is now empty and can be
//    removed.  So we reintroduced the possibility of infinite looping
//    by adding a "rewind" function, which let you go back to an
//    earlier point in the token stream and reprocess it with injectors.
//    Needless to say, we need to UN-skip the token so it gets
//    reprocessed.
//
//  - Suppose that you successfully process a token, replace it with
//    one with your skip mark, but now another injector wants to
//    process the skipped token with another token.  Should you continue
//    to skip that new token, or reprocess it?  If you reprocess,
//    you can end up with an infinite loop where one injector converts
//    <a> to <b>, and then another injector converts it back.  So
//    we inherit the skips, but for some reason, I thought that we
//    should inherit the skip from the first token of the token
//    that we deleted.  Why?  Well, it seems to work OK.
//
// If I were to redesign this functionality, I would absolutely not
// go about doing it this way: the semantics are just not very well
// defined, and in any case you probably wanted to operate on trees,
// not token streams.

// vim: et sw=4 sts=4
