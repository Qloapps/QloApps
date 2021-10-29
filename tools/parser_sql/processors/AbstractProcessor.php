<?php
/**
 * AbstractProcessor.php
 *
 * This file implements an abstract processor, which implements some helper functions.
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

namespace PHPSQLParser\processors;

use PHPSQLParser\lexer\PHPSQLLexer;
use PHPSQLParser\Options;
use PHPSQLParser\utils\ExpressionType;

/**
 * This class contains some general functions for a processor.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
abstract class AbstractProcessor {

    /**
     * @var Options
     */
    protected $options;

    /**
     * AbstractProcessor constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options = null)
    {
        $this->options = $options;
    }

    /**
     * This function implements the main functionality of a processor class.
     * Always use default valuses for additional parameters within overridden functions.
     */
    public abstract function process($tokens);

    /**
     * this function splits up a SQL statement into easy to "parse"
     * tokens for the SQL processor
     */
    public function splitSQLIntoTokens($sql) {
        $lexer = new PHPSQLLexer();
        return $lexer->split($sql);
    }

    /**
     * Revokes the quoting characters from an expression
     * Possibibilies:
     *   `a`
     *   'a'
     *   "a"
     *   `a`.`b`
     *   `a.b`
     *   a.`b`
     *   `a`.b
     * It is also possible to have escaped quoting characters
     * within an expression part:
     *   `a``b` => a`b
     * And you can use whitespace between the parts:
     *   a  .  `b` => [a,b]
     */
    protected function revokeQuotation($sql) {
        $tmp = trim($sql);
        $result = array();

        $quote = false;
        $start = 0;
        $i = 0;
        $len = strlen($tmp);

        while ($i < $len) {

            $char = $tmp[$i];
            switch ($char) {
            case '`':
            case '\'':
            case '"':
                if ($quote === false) {
                    // start
                    $quote = $char;
                    $start = $i + 1;
                    break;
                }
                if ($quote !== $char) {
                    break;
                }
                if (isset($tmp[$i + 1]) && ($quote === $tmp[$i + 1])) {
                    // escaped
                    $i++;
                    break;
                }
                // end
                $char = substr($tmp, $start, $i - $start);
                $result[] = str_replace($quote . $quote, $quote, $char);
                $start = $i + 1;
                $quote = false;
                break;

            case '.':
                if ($quote === false) {
                    // we have found a separator
                    $char = trim(substr($tmp, $start, $i - $start));
                    if ($char !== '') {
                        $result[] = $char;
                    }
                    $start = $i + 1;
                }
                break;

            default:
            // ignore
                break;
            }
            $i++;
        }

        if ($quote === false && ($start < $len)) {
            $char = trim(substr($tmp, $start, $i - $start));
            if ($char !== '') {
                $result[] = $char;
            }
        }

        return array('delim' => (count($result) === 1 ? false : '.'), 'parts' => $result);
    }

    /**
     * This method removes parenthesis from start of the given string.
     * It removes also the associated closing parenthesis.
     */
    protected function removeParenthesisFromStart($token) {
        $parenthesisRemoved = 0;

        $trim = trim($token);
        if ($trim !== '' && $trim[0] === '(') { // remove only one parenthesis pair now!
            $parenthesisRemoved++;
            $trim[0] = ' ';
            $trim = trim($trim);
        }

        $parenthesis = $parenthesisRemoved;
        $i = 0;
        $string = 0;
        // Whether a string was opened or not, and with which character it was open (' or ")
        $stringOpened = '';
        while ($i < strlen($trim)) {

            if ($trim[$i] === "\\") {
                $i += 2; // an escape character, the next character is irrelevant
                continue;
            }

            if ($trim[$i] === "'") {
                if ($stringOpened === '') {
                    $stringOpened = "'";
                } elseif ($stringOpened === "'") {
                    $stringOpened = '';
                }
            }

            if ($trim[$i] === '"') {
                if ($stringOpened === '') {
                    $stringOpened = '"';
                } elseif ($stringOpened === '"') {
                    $stringOpened = '';
                }
            }

            if (($stringOpened === '') && ($trim[$i] === '(')) {
                $parenthesis++;
            }

            if (($stringOpened === '') && ($trim[$i] === ')')) {
                if ($parenthesis == $parenthesisRemoved) {
                    $trim[$i] = ' ';
                    $parenthesisRemoved--;
                }
                $parenthesis--;
            }
            $i++;
        }
        return trim($trim);
    }

    protected function getVariableType($expression) {
        // $expression must contain only upper-case characters
        if ($expression[1] !== '@') {
            return ExpressionType::USER_VARIABLE;
        }

        $type = substr($expression, 2, strpos($expression, '.', 2));

        switch ($type) {
        case 'GLOBAL':
            $type = ExpressionType::GLOBAL_VARIABLE;
            break;
        case 'LOCAL':
            $type = ExpressionType::LOCAL_VARIABLE;
            break;
        case 'SESSION':
        default:
            $type = ExpressionType::SESSION_VARIABLE;
            break;
        }
        return $type;
    }

    protected function isCommaToken($token) {
        return (trim($token) === ',');
    }

    protected function isWhitespaceToken($token) {
        return (trim($token) === '');
    }

    protected function isCommentToken($token) {
        return isset($token[0]) && isset($token[1])
                && (($token[0] === '-' && $token[1] === '-') || ($token[0] === '/' && $token[1] === '*'));
    }

    protected function isColumnReference($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::COLREF);
    }

    protected function isReserved($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::RESERVED);
    }

    protected function isConstant($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::CONSTANT);
    }

    protected function isAggregateFunction($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::AGGREGATE_FUNCTION);
    }

    protected function isCustomFunction($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::CUSTOM_FUNCTION);
    }

    protected function isFunction($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::SIMPLE_FUNCTION);
    }

    protected function isExpression($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::EXPRESSION);
    }

    protected function isBracketExpression($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::BRACKET_EXPRESSION);
    }

    protected function isSubQuery($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::SUBQUERY);
    }

    protected function isComment($out) {
        return (isset($out['expr_type']) && $out['expr_type'] === ExpressionType::COMMENT);
    }

    public function processComment($expression) {
        $result = array();
        $result['expr_type'] = ExpressionType::COMMENT;
        $result['value'] = $expression;
        return $result;
    }

    /**
     * translates an array of objects into an associative array
     */
    public function toArray($tokenList) {
        $expr = array();
        foreach ($tokenList as $token) {
            if ($token instanceof \PHPSQLParser\utils\ExpressionToken) {
                $expr[] = $token->toArray();
            } else {
                $expr[] = $token;
            }
        }
        return $expr;
    }

    protected function array_insert_after($array, $key, $entry) {
        $idx = array_search($key, array_keys($array));
        $array = array_slice($array, 0, $idx + 1, true) + $entry
                + array_slice($array, $idx + 1, count($array) - 1, true);
        return $array;
    }
}
?>
