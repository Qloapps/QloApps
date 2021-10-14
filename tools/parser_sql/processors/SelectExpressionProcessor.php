<?php
/**
 * SelectExpressionProcessor.php
 *
 * This file implements the processor for SELECT expressions.
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

namespace PHPSQLParser\processors;
use PHPSQLParser\utils\ExpressionType;

/**
 *
 * This class processes the SELECT expressions.
 *
 * @author arothe
 *
 */
class SelectExpressionProcessor extends AbstractProcessor {

    protected function processExpressionList($unparsed) {
        $processor = new ExpressionListProcessor($this->options);
        return $processor->process($unparsed);
    }

    /**
     * This fuction processes each SELECT clause.
     * We determine what (if any) alias
     * is provided, and we set the type of expression.
     */
    public function process($expression) {
        $tokens = $this->splitSQLIntoTokens($expression);
        $token_count = count($tokens);
        if ($token_count === 0) {
            return null;
        }

        /*
         * Determine if there is an explicit alias after the AS clause.
         * If AS is found, then the next non-whitespace token is captured as the alias.
         * The tokens after (and including) the AS are removed.
         */
        $base_expr = "";
        $stripped = array();
        $capture = false;
        $alias = false;
        $processed = false;

        for ($i = 0; $i < $token_count; ++$i) {
            $token = $tokens[$i];
            $upper = strtoupper($token);

            if ($upper === 'AS') {
                $alias = array('as' => true, "name" => "", "base_expr" => $token);
                $tokens[$i] = "";
                $capture = true;
                continue;
            }

            if (!$this->isWhitespaceToken($upper)) {
                $stripped[] = $token;
            }

            // we have an explicit AS, next one can be the alias
            // but also a comment!
            if ($capture) {
                if (!$this->isWhitespaceToken($upper) && !$this->isCommentToken($upper)) {
                    $alias['name'] .= $token;
                    array_pop($stripped);
                }
                $alias['base_expr'] .= $token;
                $tokens[$i] = "";
                continue;
            }

            $base_expr .= $token;
        }

        if ($alias) {
            // remove quotation from the alias
            $alias['no_quotes'] = $this->revokeQuotation($alias['name']);
            $alias['name'] = trim($alias['name']);
            $alias['base_expr'] = trim($alias['base_expr']);
        }

        $stripped = $this->processExpressionList($stripped);

        // TODO: the last part can also be a comment, don't use array_pop

        // we remove the last token, if it is a colref,
        // it can be an alias without an AS
        $last = array_pop($stripped);
        if (!$alias && $this->isColumnReference($last)) {

            // TODO: it can be a comment, don't use array_pop

            // check the token before the colref
            $prev = array_pop($stripped);

            if ($this->isReserved($prev) || $this->isConstant($prev) || $this->isAggregateFunction($prev)
                    || $this->isFunction($prev) || $this->isExpression($prev) || $this->isSubQuery($prev)
                    || $this->isColumnReference($prev) || $this->isBracketExpression($prev)|| $this->isCustomFunction($prev)) {

                $alias = array('as' => false, 'name' => trim($last['base_expr']),
                               'no_quotes' => $this->revokeQuotation($last['base_expr']),
                               'base_expr' => trim($last['base_expr']));
                // remove the last token
                array_pop($tokens);
            }
        }

        $base_expr = $expression;

        // TODO: this is always done with $stripped, how we do it twice?
        $processed = $this->processExpressionList($tokens);

        // if there is only one part, we copy the expr_type
        // in all other cases we use "EXPRESSION" as global type
        $type = ExpressionType::EXPRESSION;
        if (count($processed) === 1) {
            if (!$this->isSubQuery($processed[0])) {
                $type = $processed[0]['expr_type'];
                $base_expr = $processed[0]['base_expr'];
                $no_quotes = isset($processed[0]['no_quotes']) ? $processed[0]['no_quotes'] : null;
                $processed = $processed[0]['sub_tree']; // it can be FALSE
            }
        }

        $result = array();
        $result['expr_type'] = $type;
        $result['alias'] = $alias;
        $result['base_expr'] = trim($base_expr);
        if (!empty($no_quotes)) {
            $result['no_quotes'] = $no_quotes;
        }
        $result['sub_tree'] = (empty($processed) ? false : $processed);
        return $result;
    }

}
?>
