<?php
/**
 * FromProcessor.php
 *
 * This file implements the processor for the FROM statements.
 *
 * Copyright (c) 2010-2013, Justin Swanhart
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

require_once(dirname(__FILE__) . '/AbstractProcessor.php');
require_once(dirname(__FILE__) . '/ExpressionListProcessor.php');
require_once(dirname(__FILE__) . '/DefaultProcessor.php');
require_once(dirname(__FILE__) . '/../utils/ExpressionType.php');

/**
 * 
 * This class processes the FROM statements.
 * 
 * @author arothe
 * 
 */
class FromProcessor extends AbstractProcessor {

    protected function initParseInfo($parseInfo = false) {
        // first init
        if ($parseInfo === false) {
            $parseInfo = array('join_type' => "", 'saved_join_type' => "JOIN");
        }
        // loop init
        return array('expression' => "", 'token_count' => 0, 'table' => "", 'no_quotes' => "", 'alias' => false,
                     'join_type' => "", 'next_join_type' => "", 'saved_join_type' => $parseInfo['saved_join_type'],
                     'ref_type' => false, 'ref_expr' => false, 'base_expr' => false, 'sub_tree' => false,
                     'subquery' => "");
    }

    protected function processFromExpression(&$parseInfo) {
        $res = array();

        // exchange the join types (join_type is save now, saved_join_type holds the next one)
        $parseInfo['join_type'] = $parseInfo['saved_join_type']; // initialized with JOIN
        $parseInfo['saved_join_type'] = ($parseInfo['next_join_type'] ? $parseInfo['next_join_type'] : 'JOIN');

        // we have a reg_expr, so we have to parse it
        if ($parseInfo['ref_expr'] !== false) {
            $unparsed = $this->splitSQLIntoTokens($this->removeParenthesisFromStart($parseInfo['ref_expr']));

            // here we can get a comma separated list
            foreach ($unparsed as $k => $v) {
                if ($this->isCommaToken($v)) {
                    $unparsed[$k] = "";
                }
            }
            $processor = new ExpressionListProcessor();
            $parseInfo['ref_expr'] = $processor->process($unparsed);
        }

        // there is an expression, we have to parse it
        if (substr(trim($parseInfo['table']), 0, 1) == '(') {
            $parseInfo['expression'] = $this->removeParenthesisFromStart($parseInfo['table']);

            if (preg_match("/^\\s*select/i", $parseInfo['expression'])) {
                $processor = new DefaultProcessor();
                $parseInfo['sub_tree'] = $processor->process($parseInfo['expression']);
                $res['expr_type'] = ExpressionType::SUBQUERY;
            } else {
                $tmp = $this->splitSQLIntoTokens($parseInfo['expression']);
                $parseInfo['sub_tree'] = $this->process($tmp);
                $res['expr_type'] = ExpressionType::TABLE_EXPRESSION;
            }
        } else {
            $res['expr_type'] = ExpressionType::TABLE;
            $res['table'] = $parseInfo['table'];
            $res['no_quotes'] = $this->revokeQuotation($parseInfo['table']);
        }

        $res['alias'] = $parseInfo['alias'];
        $res['join_type'] = $parseInfo['join_type'];
        $res['ref_type'] = $parseInfo['ref_type'];
        $res['ref_clause'] = $parseInfo['ref_expr'];
        $res['base_expr'] = trim($parseInfo['expression']);
        $res['sub_tree'] = $parseInfo['sub_tree'];
        return $res;
    }

    public function process($tokens) {
        $parseInfo = $this->initParseInfo();
        $expr = array();

        $skip_next = false;
        $i = 0;

        foreach ($tokens as $token) {
            $upper = strtoupper(trim($token));

            if ($skip_next && $token !== "") {
                $parseInfo['token_count']++;
                $skip_next = false;
                continue;
            } else {
                if ($skip_next) {
                    continue;
                }
            }

            switch ($upper) {
            case 'OUTER':
            case 'LEFT':
            case 'RIGHT':
            case 'NATURAL':
            case 'CROSS':
            case ',':
            case 'JOIN':
            case 'INNER':
                break;

            default:
                $parseInfo['expression'] .= $token;
                if ($parseInfo['ref_type'] !== false) { // all after ON / USING
                    $parseInfo['ref_expr'] .= $token;
                }
                break;
            }

            switch ($upper) {
            case 'AS':
                $parseInfo['alias'] = array('as' => true, 'name' => "", 'base_expr' => $token);
                $parseInfo['token_count']++;
                $n = 1;
                $str = "";
                while ($str == "") {
                    $parseInfo['alias']['base_expr'] .= ($tokens[$i + $n] === "" ? " " : $tokens[$i + $n]);
                    $str = trim($tokens[$i + $n]);
                    ++$n;
                }
                $parseInfo['alias']['name'] = $str;
                $parseInfo['alias']['no_quotes'] = $this->revokeQuotation($str);
                $parseInfo['alias']['base_expr'] = trim($parseInfo['alias']['base_expr']);
                continue;

            case 'INDEX':
                if ($token_category == 'CREATE') {
                    $token_category = $upper;
                    continue 2;
                }

                break;

            case 'USING':
            case 'ON':
                $parseInfo['ref_type'] = $upper;
                $parseInfo['ref_expr'] = "";

            case 'CROSS':
            case 'USE':
            case 'FORCE':
            case 'IGNORE':
            case 'INNER':
            case 'OUTER':
                $parseInfo['token_count']++;
                continue;
                break;

            case 'FOR':
                $parseInfo['token_count']++;
                $skip_next = true;
                continue;
                break;

            case 'LEFT':
            case 'RIGHT':
            case 'STRAIGHT_JOIN':
                $parseInfo['next_join_type'] = $upper;
                break;

            case ',':
                $parseInfo['next_join_type'] = 'CROSS';

            case 'JOIN':
                if ($parseInfo['subquery']) {
                    $parseInfo['sub_tree'] = $this->parse($this->removeParenthesisFromStart($parseInfo['subquery']));
                    $parseInfo['expression'] = $parseInfo['subquery'];
                }

                $expr[] = $this->processFromExpression($parseInfo);
                $parseInfo = $this->initParseInfo($parseInfo);
                break;

            default:
                if ($upper === "") {
                    continue; // ends the switch statement!
                }

                if ($parseInfo['token_count'] === 0) {
                    if ($parseInfo['table'] === "") {
                        $parseInfo['table'] = $token;
                        $parseInfo['no_quotes'] = $this->revokeQuotation($token);
                    }
                } elseif ($parseInfo['token_count'] === 1) {
                    $parseInfo['alias'] = array('as' => false, 'name' => trim($token),
                                                'no_quotes' => $this->revokeQuotation($token),
                                                'base_expr' => trim($token));
                }
                $parseInfo['token_count']++;
                break;
            }
            ++$i;
        }

        $expr[] = $this->processFromExpression($parseInfo);
        return $expr;
    }

}
?>