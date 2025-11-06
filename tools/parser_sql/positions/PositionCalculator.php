<?php
/**
 * PositionCalculator.php
 *
 * This class implements the calculator for the string positions of the 
 * base_expr elements within the output of the PHPSQLParser.
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
 * @copyright 2010-2015 Justin Swanhart and André Rothe
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id$
 * 
 */

namespace PHPSQLParser\positions;
use PHPSQLParser\utils\PHPSQLParserConstants;
use PHPSQLParser\exceptions\UnableToCalculatePositionException;
use PHPSQLParser\utils\ExpressionType;

/**
 * This class implements the calculator for the string positions of the 
 * base_expr elements within the output of the PHPSQLParser.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class PositionCalculator {

    protected static $allowedOnOperator = array("\t", "\n", "\r", " ", ",", "(", ")", "_", "'", "\"", "?", "@", "0",
                                                "1", "2", "3", "4", "5", "6", "7", "8", "9");
    protected static $allowedOnOther = array("\t", "\n", "\r", " ", ",", "(", ")", "<", ">", "*", "+", "-", "/", "|",
                                             "&", "=", "!", ";");

    protected $flippedBacktrackingTypes;
    protected static $backtrackingTypes = array(ExpressionType::EXPRESSION, ExpressionType::SUBQUERY,
                                                ExpressionType::BRACKET_EXPRESSION, ExpressionType::TABLE_EXPRESSION,
                                                ExpressionType::RECORD, ExpressionType::IN_LIST,
                                                ExpressionType::MATCH_ARGUMENTS, ExpressionType::TABLE,
                                                ExpressionType::TEMPORARY_TABLE, ExpressionType::COLUMN_TYPE,
                                                ExpressionType::COLDEF, ExpressionType::PRIMARY_KEY,
                                                ExpressionType::CONSTRAINT, ExpressionType::COLUMN_LIST,
                                                ExpressionType::CHECK, ExpressionType::COLLATE, ExpressionType::LIKE,
                                                ExpressionType::INDEX, ExpressionType::INDEX_TYPE,
                                                ExpressionType::INDEX_SIZE, ExpressionType::INDEX_PARSER,
                                                ExpressionType::FOREIGN_KEY, ExpressionType::REFERENCE,
                                                ExpressionType::PARTITION, ExpressionType::PARTITION_HASH,
                                                ExpressionType::PARTITION_COUNT, ExpressionType::PARTITION_KEY,
                                                ExpressionType::PARTITION_KEY_ALGORITHM,
                                                ExpressionType::PARTITION_RANGE, ExpressionType::PARTITION_LIST,
                                                ExpressionType::PARTITION_DEF, ExpressionType::PARTITION_VALUES,
                                                ExpressionType::SUBPARTITION_DEF, ExpressionType::PARTITION_DATA_DIR,
                                                ExpressionType::PARTITION_INDEX_DIR, ExpressionType::PARTITION_COMMENT,
                                                ExpressionType::PARTITION_MAX_ROWS, ExpressionType::PARTITION_MIN_ROWS,
                                                ExpressionType::SUBPARTITION_COMMENT,
                                                ExpressionType::SUBPARTITION_DATA_DIR,
                                                ExpressionType::SUBPARTITION_INDEX_DIR,
                                                ExpressionType::SUBPARTITION_KEY,
                                                ExpressionType::SUBPARTITION_KEY_ALGORITHM,
                                                ExpressionType::SUBPARTITION_MAX_ROWS,
                                                ExpressionType::SUBPARTITION_MIN_ROWS, ExpressionType::SUBPARTITION,
                                                ExpressionType::SUBPARTITION_HASH, ExpressionType::SUBPARTITION_COUNT,
                                                ExpressionType::CHARSET, ExpressionType::ENGINE, ExpressionType::QUERY,
                                                ExpressionType::INDEX_ALGORITHM, ExpressionType::INDEX_LOCK,
    											ExpressionType::SUBQUERY_FACTORING, ExpressionType::CUSTOM_FUNCTION,
                                                ExpressionType::SIMPLE_FUNCTION
    );

    /**
     * Constructor.
     * 
     * It initializes some fields.
     */
    public function __construct() {
        $this->flippedBacktrackingTypes = array_flip(self::$backtrackingTypes);
    }

    protected function printPos($text, $sql, $charPos, $key, $parsed, $backtracking) {
        if (!isset($_SERVER['DEBUG'])) {
            return;
        }

        $spaces = "";
        $caller = debug_backtrace();
        $i = 1;
        while ($caller[$i]['function'] === 'lookForBaseExpression') {
            $spaces .= "   ";
            $i++;
        }
        $holdem = substr($sql, 0, $charPos) . "^" . substr($sql, $charPos);
        echo $spaces . $text . " key:" . $key . "  parsed:" . $parsed . " back:" . serialize($backtracking) . " "
            . $holdem . "\n";
    }

    public function setPositionsWithinSQL($sql, $parsed) {
        $charPos = 0;
        $backtracking = array();
        $this->lookForBaseExpression($sql, $charPos, $parsed, 0, $backtracking);
        return $parsed;
    }

    protected function findPositionWithinString($sql, $value, $expr_type) {
        if ($value === '') {
            return false;
        }

        $offset = 0;
        $ok = false;
        while (true) {

            $pos = strpos($sql, $value, $offset);
            // error_log("pos:$pos value:$value sql:$sql");
            
            if ($pos === false) {
                break;
            }

            $before = "";
            if ($pos > 0) {
                $before = $sql[$pos - 1];
            }

            // if we have a quoted string, we every character is allowed after it
            // see issues 137 and 361
            $quotedBefore = in_array($sql[$pos], array('`', '('), true);
            $quotedAfter = in_array($sql[$pos + strlen($value) - 1], array('`', ')'), true);
            $after = "";
            if (isset($sql[$pos + strlen($value)])) {
                $after = $sql[$pos + strlen($value)];
            }

            // if we have an operator, it should be surrounded by
            // whitespace, comma, parenthesis, digit or letter, end_of_string
            // an operator should not be surrounded by another operator

            if (in_array($expr_type,array('operator','column-list'),true)) {

                $ok = ($before === "" || in_array($before, self::$allowedOnOperator, true))
                    || (strtolower($before) >= 'a' && strtolower($before) <= 'z');
                $ok = $ok
                    && ($after === "" || in_array($after, self::$allowedOnOperator, true)
                        || (strtolower($after) >= 'a' && strtolower($after) <= 'z'));

                if (!$ok) {
                    $offset = $pos + 1;
                    continue;
                }

                break;
            }

            // in all other cases we accept
            // whitespace, comma, operators, parenthesis and end_of_string

            $ok = ($before === "" || in_array($before, self::$allowedOnOther, true)
                || ($quotedBefore && (strtolower($before) >= 'a' && strtolower($before) <= 'z')));
            $ok = $ok
                && ($after === "" || in_array($after, self::$allowedOnOther, true)
                    || ($quotedAfter && (strtolower($after) >= 'a' && strtolower($after) <= 'z')));

            if ($ok) {
                break;
            }

            $offset = $pos + 1;
        }

        return $pos;
    }

    protected function lookForBaseExpression($sql, &$charPos, &$parsed, $key, &$backtracking) {
        if (!is_numeric($key)) {
            if (($key === 'UNION' || $key === 'UNION ALL')
                || ($key === 'expr_type' && isset($this->flippedBacktrackingTypes[$parsed]))
                || ($key === 'select-option' && $parsed !== false) || ($key === 'alias' && $parsed !== false)) {
                // we hold the current position and come back after the next base_expr
                // we do this, because the next base_expr contains the complete expression/subquery/record
                // and we have to look into it too
                $backtracking[] = $charPos;

            } elseif (($key === 'ref_clause' || $key === 'columns') && $parsed !== false) {
                // we hold the current position and come back after n base_expr(s)
                // there is an array of sub-elements before (!) the base_expr clause of the current element
                // so we go through the sub-elements and must come at the end
                $backtracking[] = $charPos;
                for ($i = 1; $i < count($parsed); $i++) {
                    $backtracking[] = false; // backtracking only after n base_expr!
                }
            } elseif (($key === 'sub_tree' && $parsed !== false) || ($key === 'options' && $parsed !== false)) {
                // we prevent wrong backtracking on subtrees (too much array_pop())
                // there is an array of sub-elements after(!) the base_expr clause of the current element
                // so we go through the sub-elements and must not come back at the end
                for ($i = 1; $i < count($parsed); $i++) {
                    $backtracking[] = false;
                }
            } elseif (($key === 'TABLE') || ($key === 'create-def' && $parsed !== false)) {
                // do nothing
            } else {
                // move the current pos after the keyword
                // SELECT, WHERE, INSERT etc.
                if (PHPSQLParserConstants::getInstance()->isReserved($key)) {
                    $charPos = stripos($sql, $key, $charPos);
                    $charPos += strlen($key);
                }
            }
        }

        if (!is_array($parsed)) {
            return;
        }

        foreach ($parsed as $key => $value) {
            if ($key === 'base_expr') {

                //$this->printPos("0", $sql, $charPos, $key, $value, $backtracking);

                $subject = substr($sql, $charPos);
                $pos = $this->findPositionWithinString($subject, $value,
                    isset($parsed['expr_type']) ? $parsed['expr_type'] : 'alias');
                if ($pos === false) {
                    throw new UnableToCalculatePositionException($value, $subject);
                }

                $parsed['position'] = $charPos + $pos;
                $charPos += $pos + strlen($value);

                //$this->printPos("1", $sql, $charPos, $key, $value, $backtracking);

                $oldPos = array_pop($backtracking);
                if (isset($oldPos) && $oldPos !== false) {
                    $charPos = $oldPos;
                }

                //$this->printPos("2", $sql, $charPos, $key, $value, $backtracking);

            } else {
                $this->lookForBaseExpression($sql, $charPos, $parsed[$key], $key, $backtracking);
            }
        }
    }
}

?>
