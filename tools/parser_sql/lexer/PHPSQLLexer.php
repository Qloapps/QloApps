<?php
/**
 * PHPSQLLexer.php
 *
 * This file contains the lexer, which splits and recombines parts of the
 * SQL statement just before parsing.
 *
 * PHP version 5
 *
 * LICENSE:
 * Copyright (c) 2010-2014 Justin Swanhart and André Rothe
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author    André Rothe <andre.rothe@phosco.info>
 * @copyright 2010-2014 Justin Swanhart and André Rothe
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id$
 *
 */

namespace PHPSQLParser\lexer;
use PHPSQLParser\exceptions\InvalidParameterException;

/**
 * This class splits the SQL string into little parts, which the parser can
 * use to build the result array.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class PHPSQLLexer {

    protected $splitters;

    /**
     * Constructor.
     *
     * It initializes some fields.
     */
    public function __construct() {
        $this->splitters = new LexerSplitter();
    }

    /**
     * Ends the given string $haystack with the string $needle?
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return boolean true, if the parameter $haystack ends with the character sequences $needle, false otherwise
     */
    protected function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    public function split($sql) {
        if (!is_string($sql)) {
            throw new InvalidParameterException($sql);
        }
        $tokens = preg_split($this->splitters->getSplittersRegexPattern(), $sql, null, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $tokens = $this->concatComments($tokens);
        $tokens = $this->concatEscapeSequences($tokens);
        $tokens = $this->balanceBackticks($tokens);
        $tokens = $this->concatColReferences($tokens);
        $tokens = $this->balanceParenthesis($tokens);
        $tokens = $this->concatUserDefinedVariables($tokens);
        $tokens = $this->concatScientificNotations($tokens);
        $tokens = $this->concatNegativeNumbers($tokens);
        return $tokens;
    }

    protected function concatNegativeNumbers($tokens) {

    	$i = 0;
    	$cnt = count($tokens);
    	$possibleSign = true;

    	while ($i < $cnt) {

    		if (!isset($tokens[$i])) {
    			$i++;
    			continue;
    		}

    		$token = $tokens[$i];

    		// a sign is also possible on the first position of the tokenlist
    		if ($possibleSign === true) {
				if ($token === '-' || $token === '+') {
					if (is_numeric($tokens[$i + 1])) {
						$tokens[$i + 1] = $token . $tokens[$i + 1];
						unset($tokens[$i]);
					}
				}
				$possibleSign = false;
				continue;
    		}

    		// TODO: we can have sign of a number after "(" and ",", are others possible?
    		if (substr($token, -1, 1) === "," || substr($token, -1, 1) === "(") {
    			$possibleSign = true;
    		}

    		$i++;
   		}

   		return array_values($tokens);
    }

    protected function concatScientificNotations($tokens) {

        $i = 0;
        $cnt = count($tokens);
        $scientific = false;

        while ($i < $cnt) {

            if (!isset($tokens[$i])) {
                $i++;
                continue;
            }

            $token = $tokens[$i];

            if ($scientific === true) {
                if ($token === '-' || $token === '+') {
                    $tokens[$i - 1] .= $tokens[$i];
                    $tokens[$i - 1] .= $tokens[$i + 1];
                    unset($tokens[$i]);
                    unset($tokens[$i + 1]);

                } elseif (is_numeric($token)) {
                    $tokens[$i - 1] .= $tokens[$i];
                    unset($tokens[$i]);
                }
                $scientific = false;
                continue;
            }

            if (strtoupper(substr($token, -1, 1)) === 'E') {
                $scientific = is_numeric(substr($token, 0, -1));
            }

            $i++;
        }

        return array_values($tokens);
    }

    protected function concatUserDefinedVariables($tokens) {
        $i = 0;
        $cnt = count($tokens);
        $userdef = false;

        while ($i < $cnt) {

            if (!isset($tokens[$i])) {
                $i++;
                continue;
            }

            $token = $tokens[$i];

            if ($userdef !== false) {
                $tokens[$userdef] .= $token;
                unset($tokens[$i]);
                if ($token !== "@") {
                    $userdef = false;
                }
            }

            if ($userdef === false && $token === "@") {
                $userdef = $i;
            }

            $i++;
        }

        return array_values($tokens);
    }

    protected function concatComments($tokens) {

        $i = 0;
        $cnt = count($tokens);
        $comment = false;
        $backTicks = [];
        $in_string = false;
        $inline = false;

        while ($i < $cnt) {

            if (!isset($tokens[$i])) {
                $i++;
                continue;
            }

            $token = $tokens[$i];

            /*
             * Check to see if we're inside a value (i.e. back ticks).
             * If so inline comments are not valid.
             */
            if ($comment === false && $this->isBacktick($token)) {
                if (!empty($backTicks)) {
                    $lastBacktick = array_pop($backTicks);
                    if ($lastBacktick != $token) {
                        $backTicks[] = $lastBacktick; // Re-add last back tick
                        $backTicks[] = $token;
                    }
                } else {
                    $backTicks[] = $token;
                }
            }

            if($comment === false && ($token == "\"" || $token == "'")) {
                $in_string = !$in_string;
            }
            if(!$in_string) {
                if ($comment !== false) {
                    if ($inline === true && ($token === "\n" || $token === "\r\n")) {
                        $comment = false;
                    } else {
                        unset($tokens[$i]);
                        $tokens[$comment] .= $token;
                    }
                    if ($inline === false && ($token === "*/")) {
                        $comment = false;
                    }
                }

                if (($comment === false) && ($token === "--") && empty($backTicks)) {
                    $comment = $i;
                    $inline = true;
                }

                if (($comment === false) && (substr($token, 0, 1) === "#") && empty($backTicks)) {
                    $comment = $i;
                    $inline = true;
                }

                if (($comment === false) && ($token === "/*")) {
                    $comment = $i;
                    $inline = false;
                }
            }

            $i++;
        }

        return array_values($tokens);
    }

    protected function isBacktick($token) {
        return ($token === "'" || $token === "\"" || $token === "`");
    }

    protected function balanceBackticks($tokens) {
        $i = 0;
        $cnt = count($tokens);
        while ($i < $cnt) {

            if (!isset($tokens[$i])) {
                $i++;
                continue;
            }

            $token = $tokens[$i];

            if ($this->isBacktick($token)) {
                $tokens = $this->balanceCharacter($tokens, $i, $token);
            }

            $i++;
        }

        return $tokens;
    }

    // backticks are not balanced within one token, so we have
    // to re-combine some tokens
    protected function balanceCharacter($tokens, $idx, $char) {

        $token_count = count($tokens);
        $i = $idx + 1;
        while ($i < $token_count) {

            if (!isset($tokens[$i])) {
                $i++;
                continue;
            }

            $token = $tokens[$i];
            $tokens[$idx] .= $token;
            unset($tokens[$i]);

            if ($token === $char) {
                break;
            }

            $i++;
        }
        return array_values($tokens);
    }

    /**
     * This function concats some tokens to a column reference.
     * There are two different cases:
     *
     * 1. If the current token ends with a dot, we will add the next token
     * 2. If the next token starts with a dot, we will add it to the previous token
     *
     */
    protected function concatColReferences($tokens) {

        $cnt = count($tokens);
        $i = 0;
        while ($i < $cnt) {

            if (!isset($tokens[$i])) {
                $i++;
                continue;
            }

            if ($tokens[$i][0] === ".") {

                // concat the previous tokens, till the token has been changed
                $k = $i - 1;
                $len = strlen($tokens[$i]);
                while (($k >= 0) && ($len == strlen($tokens[$i]))) {
                    if (!isset($tokens[$k])) { // FIXME: this can be wrong if we have schema . table . column
                        $k--;
                        continue;
                    }
                    $tokens[$i] = $tokens[$k] . $tokens[$i];
                    unset($tokens[$k]);
                    $k--;
                }
            }

            if ($this->endsWith($tokens[$i], '.') && !is_numeric($tokens[$i])) {

                // concat the next tokens, till the token has been changed
                $k = $i + 1;
                $len = strlen($tokens[$i]);
                while (($k < $cnt) && ($len == strlen($tokens[$i]))) {
                    if (!isset($tokens[$k])) {
                        $k++;
                        continue;
                    }
                    $tokens[$i] .= $tokens[$k];
                    unset($tokens[$k]);
                    $k++;
                }
            }

            $i++;
        }

        return array_values($tokens);
    }

    protected function concatEscapeSequences($tokens) {
        $tokenCount = count($tokens);
        $i = 0;
        while ($i < $tokenCount) {

            if ($this->endsWith($tokens[$i], "\\")) {
                $i++;
                if (isset($tokens[$i])) {
                    $tokens[$i - 1] .= $tokens[$i];
                    unset($tokens[$i]);
                }
            }
            $i++;
        }
        return array_values($tokens);
    }

    protected function balanceParenthesis($tokens) {
        $token_count = count($tokens);
        $i = 0;
        while ($i < $token_count) {
            if ($tokens[$i] !== '(') {
                $i++;
                continue;
            }
            $count = 1;
            for ($n = $i + 1; $n < $token_count; $n++) {
                $token = $tokens[$n];
                if ($token === '(') {
                    $count++;
                }
                if ($token === ')') {
                    $count--;
                }
                $tokens[$i] .= $token;
                unset($tokens[$n]);
                if ($count === 0) {
                    $n++;
                    break;
                }
            }
            $i = $n;
        }
        return array_values($tokens);
    }
}

?>
