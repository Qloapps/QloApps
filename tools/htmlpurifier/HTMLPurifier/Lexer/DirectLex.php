<?php

/**
 * Our in-house implementation of a parser.
 *
 * A pure PHP parser, DirectLex has absolutely no dependencies, making
 * it a reasonably good default for PHP4.  Written with efficiency in mind,
 * it can be four times faster than HTMLPurifier_Lexer_PEARSax3, although it
 * pales in comparison to HTMLPurifier_Lexer_DOMLex.
 *
 * @todo Reread XML spec and document differences.
 */
class HTMLPurifier_Lexer_DirectLex extends HTMLPurifier_Lexer
{
    /**
     * @type bool
     */
    public $tracksLineNumbers = true;

    /**
     * Whitespace characters for str(c)spn.
     * @type string
     */
    protected $_whitespace = "\x20\x09\x0D\x0A";

    /**
     * Callback function for script CDATA fudge
     * @param array $matches, in form of array(opening tag, contents, closing tag)
     * @return string
     */
    protected function scriptCallback($matches)
    {
        return $matches[1] . htmlspecialchars($matches[2], ENT_COMPAT, 'UTF-8') . $matches[3];
    }

    /**
     * @param String $html
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array|HTMLPurifier_Token[]
     */
    public function tokenizeHTML($html, $config, $context)
    {
        // special normalization for script tags without any armor
        // our "armor" heurstic is a < sign any number of whitespaces after
        // the first script tag
        if ($config->get('HTML.Trusted')) {
            $html = preg_replace_callback(
                '#(<script[^>]*>)(\s*[^<].+?)(</script>)#si',
                array($this, 'scriptCallback'),
                $html
            );
        }

        $html = $this->normalize($html, $config, $context);

        $cursor = 0; // our location in the text
        $inside_tag = false; // whether or not we're parsing the inside of a tag
        $array = array(); // result array

        // This is also treated to mean maintain *column* numbers too
        $maintain_line_numbers = $config->get('Core.MaintainLineNumbers');

        if ($maintain_line_numbers === null) {
            // automatically determine line numbering by checking
            // if error collection is on
            $maintain_line_numbers = $config->get('Core.CollectErrors');
        }

        if ($maintain_line_numbers) {
            $current_line = 1;
            $current_col = 0;
            $length = strlen($html);
        } else {
            $current_line = false;
            $current_col = false;
            $length = false;
        }
        $context->register('CurrentLine', $current_line);
        $context->register('CurrentCol', $current_col);
        $nl = "\n";
        // how often to manually recalculate. This will ALWAYS be right,
        // but it's pretty wasteful. Set to 0 to turn off
        $synchronize_interval = $config->get('Core.DirectLexLineNumberSyncInterval');

        $e = false;
        if ($config->get('Core.CollectErrors')) {
            $e =& $context->get('ErrorCollector');
        }

        // for testing synchronization
        $loops = 0;

        while (++$loops) {
            // $cursor is either at the start of a token, or inside of
            // a tag (i.e. there was a < immediately before it), as indicated
            // by $inside_tag

            if ($maintain_line_numbers) {
                // $rcursor, however, is always at the start of a token.
                $rcursor = $cursor - (int)$inside_tag;

                // Column number is cheap, so we calculate it every round.
                // We're interested at the *end* of the newline string, so
                // we need to add strlen($nl) == 1 to $nl_pos before subtracting it
                // from our "rcursor" position.
                $nl_pos = strrpos($html, $nl, $rcursor - $length);
                $current_col = $rcursor - (is_bool($nl_pos) ? 0 : $nl_pos + 1);

                // recalculate lines
                if ($synchronize_interval && // synchronization is on
                    $cursor > 0 && // cursor is further than zero
                    $loops % $synchronize_interval === 0) { // time to synchronize!
                    $current_line = 1 + substr_count($html, $nl, 0, $cursor);
                }
            }

            $position_next_lt = strpos($html, '<', $cursor);
            $position_next_gt = strpos($html, '>', $cursor);

            // triggers on "<b>asdf</b>" but not "asdf <b></b>"
            // special case to set up context
            if ($position_next_lt === $cursor) {
                $inside_tag = true;
                $cursor++;
            }

            if (!$inside_tag && $position_next_lt !== false) {
                // We are not inside tag and there still is another tag to parse
                $token = new
                HTMLPurifier_Token_Text(
                    $this->parseText(
                        substr(
                            $html,
                            $cursor,
                            $position_next_lt - $cursor
                        ), $config
                    )
                );
                if ($maintain_line_numbers) {
                    $token->rawPosition($current_line, $current_col);
                    $current_line += substr_count($html, $nl, $cursor, $position_next_lt - $cursor);
                }
                $array[] = $token;
                $cursor = $position_next_lt + 1;
                $inside_tag = true;
                continue;
            } elseif (!$inside_tag) {
                // We are not inside tag but there are no more tags
                // If we're already at the end, break
                if ($cursor === strlen($html)) {
                    break;
                }
                // Create Text of rest of string
                $token = new
                HTMLPurifier_Token_Text(
                    $this->parseText(
                        substr(
                            $html,
                            $cursor
                        ), $config
                    )
                );
                if ($maintain_line_numbers) {
                    $token->rawPosition($current_line, $current_col);
                }
                $array[] = $token;
                break;
            } elseif ($inside_tag && $position_next_gt !== false) {
                // We are in tag and it is well formed
                // Grab the internals of the tag
                $strlen_segment = $position_next_gt - $cursor;

                if ($strlen_segment < 1) {
                    // there's nothing to process!
                    $token = new HTMLPurifier_Token_Text('<');
                    $cursor++;
                    continue;
                }

                $segment = substr($html, $cursor, $strlen_segment);

                if ($segment === false) {
                    // somehow, we attempted to access beyond the end of
                    // the string, defense-in-depth, reported by Nate Abele
                    break;
                }

                // Check if it's a comment
                if (substr($segment, 0, 3) === '!--') {
                    // re-determine segment length, looking for -->
                    $position_comment_end = strpos($html, '-->', $cursor);
                    if ($position_comment_end === false) {
                        // uh oh, we have a comment that extends to
                        // infinity. Can't be helped: set comment
                        // end position to end of string
                        if ($e) {
                            $e->send(E_WARNING, 'Lexer: Unclosed comment');
                        }
                        $position_comment_end = strlen($html);
                        $end = true;
                    } else {
                        $end = false;
                    }
                    $strlen_segment = $position_comment_end - $cursor;
                    $segment = substr($html, $cursor, $strlen_segment);
                    $token = new
                    HTMLPurifier_Token_Comment(
                        substr(
                            $segment,
                            3,
                            $strlen_segment - 3
                        )
                    );
                    if ($maintain_line_numbers) {
                        $token->rawPosition($current_line, $current_col);
                        $current_line += substr_count($html, $nl, $cursor, $strlen_segment);
                    }
                    $array[] = $token;
                    $cursor = $end ? $position_comment_end : $position_comment_end + 3;
                    $inside_tag = false;
                    continue;
                }

                // Check if it's an end tag
                $is_end_tag = (strpos($segment, '/') === 0);
                if ($is_end_tag) {
                    $type = substr($segment, 1);
                    $token = new HTMLPurifier_Token_End($type);
                    if ($maintain_line_numbers) {
                        $token->rawPosition($current_line, $current_col);
                        $current_line += substr_count($html, $nl, $cursor, $position_next_gt - $cursor);
                    }
                    $array[] = $token;
                    $inside_tag = false;
                    $cursor = $position_next_gt + 1;
                    continue;
                }

                // Check leading character is alnum, if not, we may
                // have accidently grabbed an emoticon. Translate into
                // text and go our merry way
                if (!ctype_alpha($segment[0])) {
                    // XML:  $segment[0] !== '_' && $segment[0] !== ':'
                    if ($e) {
                        $e->send(E_NOTICE, 'Lexer: Unescaped lt');
                    }
                    $token = new HTMLPurifier_Token_Text('<');
                    if ($maintain_line_numbers) {
                        $token->rawPosition($current_line, $current_col);
                        $current_line += substr_count($html, $nl, $cursor, $position_next_gt - $cursor);
                    }
                    $array[] = $token;
                    $inside_tag = false;
                    continue;
                }

                // Check if it is explicitly self closing, if so, remove
                // trailing slash. Remember, we could have a tag like <br>, so
                // any later token processing scripts must convert improperly
                // classified EmptyTags from StartTags.
                $is_self_closing = (strrpos($segment, '/') === $strlen_segment - 1);
                if ($is_self_closing) {
                    $strlen_segment--;
                    $segment = substr($segment, 0, $strlen_segment);
                }

                // Check if there are any attributes
                $position_first_space = strcspn($segment, $this->_whitespace);

                if ($position_first_space >= $strlen_segment) {
                    if ($is_self_closing) {
                        $token = new HTMLPurifier_Token_Empty($segment);
                    } else {
                        $token = new HTMLPurifier_Token_Start($segment);
                    }
                    if ($maintain_line_numbers) {
                        $token->rawPosition($current_line, $current_col);
                        $current_line += substr_count($html, $nl, $cursor, $position_next_gt - $cursor);
                    }
                    $array[] = $token;
                    $inside_tag = false;
                    $cursor = $position_next_gt + 1;
                    continue;
                }

                // Grab out all the data
                $type = substr($segment, 0, $position_first_space);
                $attribute_string =
                    trim(
                        substr(
                            $segment,
                            $position_first_space
                        )
                    );
                if ($attribute_string) {
                    $attr = $this->parseAttributeString(
                        $attribute_string,
                        $config,
                        $context
                    );
                } else {
                    $attr = array();
                }

                if ($is_self_closing) {
                    $token = new HTMLPurifier_Token_Empty($type, $attr);
                } else {
                    $token = new HTMLPurifier_Token_Start($type, $attr);
                }
                if ($maintain_line_numbers) {
                    $token->rawPosition($current_line, $current_col);
                    $current_line += substr_count($html, $nl, $cursor, $position_next_gt - $cursor);
                }
                $array[] = $token;
                $cursor = $position_next_gt + 1;
                $inside_tag = false;
                continue;
            } else {
                // inside tag, but there's no ending > sign
                if ($e) {
                    $e->send(E_WARNING, 'Lexer: Missing gt');
                }
                $token = new
                HTMLPurifier_Token_Text(
                    '<' .
                    $this->parseText(
                        substr($html, $cursor), $config
                    )
                );
                if ($maintain_line_numbers) {
                    $token->rawPosition($current_line, $current_col);
                }
                // no cursor scroll? Hmm...
                $array[] = $token;
                break;
            }
            break;
        }

        $context->destroy('CurrentLine');
        $context->destroy('CurrentCol');
        return $array;
    }

    /**
     * Takes the inside of an HTML tag and makes an assoc array of attributes.
     *
     * @param string $string Inside of tag excluding name.
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array Assoc array of attributes.
     */
    public function parseAttributeString($string, $config, $context)
    {
        $string = (string)$string; // quick typecast

        if ($string == '') {
            return array();
        } // no attributes

        $e = false;
        if ($config->get('Core.CollectErrors')) {
            $e =& $context->get('ErrorCollector');
        }

        // let's see if we can abort as quickly as possible
        // one equal sign, no spaces => one attribute
        $num_equal = substr_count($string, '=');
        $has_space = strpos($string, ' ');
        if ($num_equal === 0 && !$has_space) {
            // bool attribute
            return array($string => $string);
        } elseif ($num_equal === 1 && !$has_space) {
            // only one attribute
            list($key, $quoted_value) = explode('=', $string);
            $quoted_value = trim($quoted_value);
            if (!$key) {
                if ($e) {
                    $e->send(E_ERROR, 'Lexer: Missing attribute key');
                }
                return array();
            }
            if (!$quoted_value) {
                return array($key => '');
            }
            $first_char = @$quoted_value[0];
            $last_char = @$quoted_value[strlen($quoted_value) - 1];

            $same_quote = ($first_char == $last_char);
            $open_quote = ($first_char == '"' || $first_char == "'");

            if ($same_quote && $open_quote) {
                // well behaved
                $value = substr($quoted_value, 1, strlen($quoted_value) - 2);
            } else {
                // not well behaved
                if ($open_quote) {
                    if ($e) {
                        $e->send(E_ERROR, 'Lexer: Missing end quote');
                    }
                    $value = substr($quoted_value, 1);
                } else {
                    $value = $quoted_value;
                }
            }
            if ($value === false) {
                $value = '';
            }
            return array($key => $this->parseAttr($value, $config));
        }

        // setup loop environment
        $array = array(); // return assoc array of attributes
        $cursor = 0; // current position in string (moves forward)
        $size = strlen($string); // size of the string (stays the same)

        // if we have unquoted attributes, the parser expects a terminating
        // space, so let's guarantee that there's always a terminating space.
        $string .= ' ';

        $old_cursor = -1;
        while ($cursor < $size) {
            if ($old_cursor >= $cursor) {
                throw new Exception("Infinite loop detected");
            }
            $old_cursor = $cursor;

            $cursor += ($value = strspn($string, $this->_whitespace, $cursor));
            // grab the key

            $key_begin = $cursor; //we're currently at the start of the key

            // scroll past all characters that are the key (not whitespace or =)
            $cursor += strcspn($string, $this->_whitespace . '=', $cursor);

            $key_end = $cursor; // now at the end of the key

            $key = substr($string, $key_begin, $key_end - $key_begin);

            if (!$key) {
                if ($e) {
                    $e->send(E_ERROR, 'Lexer: Missing attribute key');
                }
                $cursor += 1 + strcspn($string, $this->_whitespace, $cursor + 1); // prevent infinite loop
                continue; // empty key
            }

            // scroll past all whitespace
            $cursor += strspn($string, $this->_whitespace, $cursor);

            if ($cursor >= $size) {
                $array[$key] = $key;
                break;
            }

            // if the next character is an equal sign, we've got a regular
            // pair, otherwise, it's a bool attribute
            $first_char = @$string[$cursor];

            if ($first_char == '=') {
                // key="value"

                $cursor++;
                $cursor += strspn($string, $this->_whitespace, $cursor);

                if ($cursor === false) {
                    $array[$key] = '';
                    break;
                }

                // we might be in front of a quote right now

                $char = @$string[$cursor];

                if ($char == '"' || $char == "'") {
                    // it's quoted, end bound is $char
                    $cursor++;
                    $value_begin = $cursor;
                    $cursor = strpos($string, $char, $cursor);
                    $value_end = $cursor;
                } else {
                    // it's not quoted, end bound is whitespace
                    $value_begin = $cursor;
                    $cursor += strcspn($string, $this->_whitespace, $cursor);
                    $value_end = $cursor;
                }

                // we reached a premature end
                if ($cursor === false) {
                    $cursor = $size;
                    $value_end = $cursor;
                }

                $value = substr($string, $value_begin, $value_end - $value_begin);
                if ($value === false) {
                    $value = '';
                }
                $array[$key] = $this->parseAttr($value, $config);
                $cursor++;
            } else {
                // boolattr
                if ($key !== '') {
                    $array[$key] = $key;
                } else {
                    // purely theoretical
                    if ($e) {
                        $e->send(E_ERROR, 'Lexer: Missing attribute key');
                    }
                }
            }
        }
        return $array;
    }
}

// vim: et sw=4 sts=4
