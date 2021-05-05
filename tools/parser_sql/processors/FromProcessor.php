<?php
/**
 * FromProcessor.php
 *
 * This file implements the processor for the FROM statement.
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
 * @author    George Schneeloch <noisecapella@gmail.com>
 * @copyright 2010-2014 Justin Swanhart and André Rothe
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id$
 *
 */

namespace PHPSQLParser\processors;
use PHPSQLParser\utils\ExpressionType;

/**
 * This class processes the FROM statement.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @author  Marco Th. <marco64th@gmail.com>
 * @author  George Schneeloch <noisecapella@gmail.com>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class FromProcessor extends AbstractProcessor {

    protected function processExpressionList($unparsed) {
        $processor = new ExpressionListProcessor($this->options);
        return $processor->process($unparsed);
    }

    protected function processColumnList($unparsed) {
        $processor = new ColumnListProcessor($this->options);
        return $processor->process($unparsed);
    }

    protected function processSQLDefault($unparsed) {
        $processor = new DefaultProcessor($this->options);
        return $processor->process($unparsed);
    }

    protected function initParseInfo($parseInfo = false) {
        // first init
        if ($parseInfo === false) {
            $parseInfo = array('join_type' => "", 'saved_join_type' => "JOIN");
        }
        // loop init
        return array('expression' => "", 'token_count' => 0, 'table' => "", 'no_quotes' => "", 'alias' => false,
                     'hints' => false, 'join_type' => "", 'next_join_type' => "",
                     'saved_join_type' => $parseInfo['saved_join_type'], 'ref_type' => false, 'ref_expr' => false,
                     'base_expr' => false, 'sub_tree' => false, 'subquery' => "");
    }

    protected function processFromExpression(&$parseInfo) {
        $res = array();

        // exchange the join types (join_type is save now, saved_join_type holds the next one)
        $parseInfo['join_type'] = $parseInfo['saved_join_type']; // initialized with JOIN
        $parseInfo['saved_join_type'] = ($parseInfo['next_join_type'] ? $parseInfo['next_join_type'] : 'JOIN');

        // we have a reg_expr, so we have to parse it
        if ($parseInfo['ref_expr'] !== false) {
            $unparsed = $this->splitSQLIntoTokens($parseInfo['ref_expr']);

            // here we can get a comma separated list
            foreach ($unparsed as $k => $v) {
                if ($this->isCommaToken($v)) {
                    $unparsed[$k] = "";
                }
            }
            if ($parseInfo['ref_type'] === 'USING') {
            	// unparsed has only one entry, the column list
            	$ref = $this->processColumnList($this->removeParenthesisFromStart($unparsed[0]));
            	$ref = array(array('expr_type' => ExpressionType::COLUMN_LIST, 'base_expr' => $unparsed[0], 'sub_tree' => $ref));
            } else {
                $ref = $this->processExpressionList($unparsed);
            }
            $parseInfo['ref_expr'] = (empty($ref) ? false : $ref);
        }

        // there is an expression, we have to parse it
        if (substr(trim($parseInfo['table']), 0, 1) == '(') {
            $parseInfo['expression'] = $this->removeParenthesisFromStart($parseInfo['table']);

            if (preg_match("/^\\s*(-- [\\w\\s]+\\n)?\\s*SELECT/i", $parseInfo['expression'])) {
                $parseInfo['sub_tree'] = $this->processSQLDefault($parseInfo['expression']);
                $res['expr_type'] = ExpressionType::SUBQUERY;
            } else {
                $tmp = $this->splitSQLIntoTokens($parseInfo['expression']);
                $unionProcessor = new UnionProcessor($this->options);
                $unionQueries = $unionProcessor->process($tmp);

                // If there was no UNION or UNION ALL in the query, then the query is
                // stored at $queries[0].
                if (!empty($unionQueries) && !UnionProcessor::isUnion($unionQueries)) {
                    $sub_tree = $this->process($unionQueries[0]);
                }
                else {
                    $sub_tree = $unionQueries;
                }
                $parseInfo['sub_tree'] = $sub_tree;
                $res['expr_type'] = ExpressionType::TABLE_EXPRESSION;
            }
        } else {
            $res['expr_type'] = ExpressionType::TABLE;
            $res['table'] = $parseInfo['table'];
            $res['no_quotes'] = $this->revokeQuotation($parseInfo['table']);
        }

        $res['alias'] = $parseInfo['alias'];
        $res['hints'] = $parseInfo['hints'];
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
        $token_category = '';
        $prevToken = '';

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

            if ($this->isCommentToken($token)) {
                $expr[] = parent::processComment($token);
                continue;
            }

            switch ($upper) {
            case 'CROSS':
            case ',':
            case 'INNER':
            case 'STRAIGHT_JOIN':
                break;

            case 'OUTER':
            case 'JOIN':
                if ($token_category === 'LEFT' || $token_category === 'RIGHT' || $token_category === 'NATURAL') {
                    $token_category = '';
                    $parseInfo['next_join_type'] = strtoupper(trim($prevToken)); // it seems to be a join
                }
                break;

            case 'LEFT':
            case 'RIGHT':
            case 'NATURAL':
                $token_category = $upper;
                $prevToken = $token;
                $i++;
                continue 2;

            default:
                if ($token_category === 'LEFT' || $token_category === 'RIGHT') {
                    if ($upper === '') {
                        $prevToken .= $token;
                        break;
                    } else {
                        $token_category = '';     // it seems to be a function
                        $parseInfo['expression'] .= $prevToken;
                        if ($parseInfo['ref_type'] !== false) { // all after ON / USING
                            $parseInfo['ref_expr'] .= $prevToken;
                        }
                        $prevToken = '';
                    }
                }
                $parseInfo['expression'] .= $token;
                if ($parseInfo['ref_type'] !== false) { // all after ON / USING
                    $parseInfo['ref_expr'] .= $token;
                }
                break;
            }

            if ($upper === '') {
                $i++;
                continue;
            }

            switch ($upper) {
            case 'AS':
                $parseInfo['alias'] = array('as' => true, 'name' => "", 'base_expr' => $token);
                $parseInfo['token_count']++;
                $n = 1;
                $str = "";
                while ($str === "" && isset($tokens[$i + $n])) {
                    $parseInfo['alias']['base_expr'] .= ($tokens[$i + $n] === "" ? " " : $tokens[$i + $n]);
                    $str = trim($tokens[$i + $n]);
                    ++$n;
                }
                $parseInfo['alias']['name'] = $str;
                $parseInfo['alias']['no_quotes'] = $this->revokeQuotation($str);
                $parseInfo['alias']['base_expr'] = trim($parseInfo['alias']['base_expr']);
                break;

            case 'IGNORE':
            case 'USE':
            case 'FORCE':
                $token_category = 'IDX_HINT';
                $parseInfo['hints'][]['hint_type'] = $upper;
                continue 2;

            case 'KEY':
            case 'INDEX':
                if ($token_category === 'CREATE') {
                    $token_category = $upper; // TODO: what is it for a statement?
                    continue 2;
                }
                if ($token_category === 'IDX_HINT') {
                    $cur_hint = (count($parseInfo['hints']) - 1);
                    $parseInfo['hints'][$cur_hint]['hint_type'] .= " " . $upper;
                    continue 2;
                }
                break;

            case 'USING':
            case 'ON':
                $parseInfo['ref_type'] = $upper;
                $parseInfo['ref_expr'] = "";

            case 'CROSS':
            case 'INNER':
            case 'OUTER':
            case 'NATURAL':
                $parseInfo['token_count']++;
                break;

            case 'FOR':
                $parseInfo['token_count']++;
                $skip_next = true;
                break;

            case 'STRAIGHT_JOIN':
                $parseInfo['next_join_type'] = "STRAIGHT_JOIN";
                if ($parseInfo['subquery']) {
                    $parseInfo['sub_tree'] = $this->parse($this->removeParenthesisFromStart($parseInfo['subquery']));
                    $parseInfo['expression'] = $parseInfo['subquery'];
                }

                $expr[] = $this->processFromExpression($parseInfo);
                $parseInfo = $this->initParseInfo($parseInfo);
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
                // TODO: enhance it, so we can have base_expr to calculate the position of the keywords
                // build a subtree under "hints"
                if ($token_category === 'IDX_HINT') {
                    $token_category = '';
                    $cur_hint = (count($parseInfo['hints']) - 1);
                    $parseInfo['hints'][$cur_hint]['hint_list'] = $token;
                    break;
                }

                if ($parseInfo['token_count'] === 0) {
                    if ($parseInfo['table'] === "") {
                        $parseInfo['table'] = $token;
                        $parseInfo['no_quotes'] = $this->revokeQuotation($token);
                    }
                } else if ($parseInfo['token_count'] === 1) {
                    $parseInfo['alias'] = array('as' => false, 'name' => trim($token),
                                                'no_quotes' => $this->revokeQuotation($token),
                                                'base_expr' => trim($token));
                }
                $parseInfo['token_count']++;
                break;
            }
            $i++;
        }

        $expr[] = $this->processFromExpression($parseInfo);
        return $expr;
    }

}

?>
