<?php
/**
 * AbstractProcessor.php
 *
 * This file implements an abstract processor, which implements some helper functions.
 *
 * Copyright (c) 2010-2012, Justin Swanhart
 * with contributions by André Rothe <arothe@phosco.info, phosco@gmx.de>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 */

require_once dirname(__FILE__) . '/../utils/ExpressionType.php';
require_once dirname(__FILE__) . '/../lexer/PHPSQLLexer.php';

/**
 * 
 * This class processes contains some general functions for a processor.
 * 
 * @author arothe
 * 
 */
abstract class AbstractProcessor {

    /**
     * This function implements the main functionality of a processor class.
     * Always use default valuses for additional parameters within overridden functions.
     */
    abstract public function process($tokens);

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
     */
    protected function revokeQuotation($sql) {
        $result = trim($sql);

        if (($result[0] === '`') && ($result[strlen($result) - 1] === '`')) {
            $result = substr($result, 1, -1);
            return trim(str_replace('``', '`', $result));
        }

        if (($result[0] === "'") && ($result[strlen($result) - 1] === "'")) {
            $result = substr($result, 1, -1);
            return trim(str_replace("''", "'", $result));
        }

        if (($result[0] === "\"") && ($result[strlen($result) - 1] === "\"")) {
            $result = substr($result, 1, -1);
            return trim(str_replace("\"\"", "\"", $result));
        }

        return $sql;
    }

    /**
     * This method removes parenthesis from start of the given string.
     * It removes also the associated closing parenthesis.
     */
    protected function removeParenthesisFromStart($token) {
        $parenthesisRemoved = 0;

        $trim = trim($token);
        if ($trim !== "" && $trim[0] === "(") { // remove only one parenthesis pair now!
            $parenthesisRemoved++;
            $trim[0] = " ";
            $trim = trim($trim);
        }

        $parenthesis = $parenthesisRemoved;
        $i = 0;
        $string = 0;
        while ($i < strlen($trim)) {

            if ($trim[$i] === "\\") {
                $i += 2; # an escape character, the next character is irrelevant
                continue;
            }

            if ($trim[$i] === "'" || $trim[$i] === '"') {
                $string++;
            }

            if (($string % 2 === 0) && ($trim[$i] === "(")) {
                $parenthesis++;
            }

            if (($string % 2 === 0) && ($trim[$i] === ")")) {
                if ($parenthesis == $parenthesisRemoved) {
                    $trim[$i] = " ";
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
        if ($expression[1] !== "@") {
            return ExpressionType::USER_VARIABLE;
        }

        $type = substr($expression, 2, strpos($expression, ".", 2));

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
        return (trim($token) === ",");
    }

    protected function isWhitespaceToken($token) {
        return (trim($token) === "");
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

    /**
     * translates an array of objects into an associative array
     */
    public function toArray($tokenList) {
        $expr = array();
        foreach ($tokenList as $token) {
            $expr[] = $token->toArray();
        }
        return (empty($expr) ? false : $expr);
    }

    protected function array_insert_after($array, $key, $entry) {
        $idx = array_search($key, array_keys($array));
        $array = array_slice($array, 0, $idx + 1, true) + $entry
            + array_slice($array, $idx + 1, count($array) - 1, true);
        return $array;
    }
}
?>
