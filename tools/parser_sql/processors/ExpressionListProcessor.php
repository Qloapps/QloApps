<?php
/**
 * ExpressionListProcessor.php
 *
 * This file implements the processor for expression lists.
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
use PHPSQLParser\utils\ExpressionType;
use PHPSQLParser\utils\ExpressionToken;
use PHPSQLParser\utils\PHPSQLParserConstants;

/**
 * This class processes expression lists.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class ExpressionListProcessor extends AbstractProcessor {

    public function process($tokens) {
        $resultList = array();
        $skip_next = false;
        $prev = new ExpressionToken();

        foreach ($tokens as $k => $v) {


            if ($this->isCommentToken($v)) {
                $resultList[] = parent::processComment($v);
                continue;
            }

            $curr = new ExpressionToken($k, $v);

            if ($curr->isWhitespaceToken()) {
                continue;
            }

            if ($skip_next) {
                // skip the next non-whitespace token
                $skip_next = false;
                continue;
            }

            /* is it a subquery? */
            if ($curr->isSubQueryToken()) {

                $processor = new DefaultProcessor($this->options);
                $curr->setSubTree($processor->process($this->removeParenthesisFromStart($curr->getTrim())));
                $curr->setTokenType(ExpressionType::SUBQUERY);

            } elseif ($curr->isEnclosedWithinParenthesis()) {
                /* is it an in-list? */

                $localTokenList = $this->splitSQLIntoTokens($this->removeParenthesisFromStart($curr->getTrim()));

                if ($prev->getUpper() === 'IN') {

                    foreach ($localTokenList as $k => $v) {
                        $tmpToken = new ExpressionToken($k, $v);
                        if ($tmpToken->isCommaToken()) {
                            unset($localTokenList[$k]);
                        }
                    }

                    $localTokenList = array_values($localTokenList);
                    $curr->setSubTree($this->process($localTokenList));
                    $curr->setTokenType(ExpressionType::IN_LIST);
                } elseif ($prev->getUpper() === 'AGAINST') {

                    $match_mode = false;
                    foreach ($localTokenList as $k => $v) {

                        $tmpToken = new ExpressionToken($k, $v);
                        switch ($tmpToken->getUpper()) {
                        case 'WITH':
                            $match_mode = 'WITH QUERY EXPANSION';
                            break;
                        case 'IN':
                            $match_mode = 'IN BOOLEAN MODE';
                            break;

                        default:
                        }

                        if ($match_mode !== false) {
                            unset($localTokenList[$k]);
                        }
                    }

                    $tmpToken = $this->process($localTokenList);

                    if ($match_mode !== false) {
                        $match_mode = new ExpressionToken(0, $match_mode);
                        $match_mode->setTokenType(ExpressionType::MATCH_MODE);
                        $tmpToken[] = $match_mode->toArray();
                    }

                    $curr->setSubTree($tmpToken);
                    $curr->setTokenType(ExpressionType::MATCH_ARGUMENTS);
                    $prev->setTokenType(ExpressionType::SIMPLE_FUNCTION);

                } elseif ($prev->isColumnReference() || $prev->isFunction() || $prev->isAggregateFunction()
                    || $prev->isCustomFunction()) {

                    // if we have a colref followed by a parenthesis pair,
                    // it isn't a colref, it is a user-function

                    // TODO: this should be a method, because we need the same code
                    // below for unspecified tokens (expressions).

                    $localExpr = new ExpressionToken();
                    $tmpExprList = array();

                    foreach ($localTokenList as $k => $v) {
                        $tmpToken = new ExpressionToken($k, $v);
                        if (!$tmpToken->isCommaToken()) {
                            $localExpr->addToken($v);
                            $tmpExprList[] = $v;
                        } else {
                            // an expression could have multiple parts split by operands
                            // if we have a comma, it is a split-point for expressions
                            $tmpExprList = array_values($tmpExprList);
                            $localExprList = $this->process($tmpExprList);

                            if (count($localExprList) > 1) {
                                $localExpr->setSubTree($localExprList);
                                $localExpr->setTokenType(ExpressionType::EXPRESSION);
                                $localExprList = $localExpr->toArray();
                                $localExprList['alias'] = false;
                                $localExprList = array($localExprList);
                            }

                            if (!$curr->getSubTree()) {
                                if (!empty($localExprList)) {
                                    $curr->setSubTree($localExprList);
                                }
                            } else {
                                $tmpExprList = $curr->getSubTree();
                                $curr->setSubTree(array_merge($tmpExprList, $localExprList));
                            }

                            $tmpExprList = array();
                            $localExpr = new ExpressionToken();
                        }
                    }

                    $tmpExprList = array_values($tmpExprList);
                    $localExprList = $this->process($tmpExprList);

                    if (count($localExprList) > 1) {
                        $localExpr->setSubTree($localExprList);
                        $localExpr->setTokenType(ExpressionType::EXPRESSION);
                        $localExprList = $localExpr->toArray();
                        $localExprList['alias'] = false;
                        $localExprList = array($localExprList);
                    }

                    if (!$curr->getSubTree()) {
                        if (!empty($localExprList)) {
                            $curr->setSubTree($localExprList);
                        }
                    } else {
                        $tmpExprList = $curr->getSubTree();
                        $curr->setSubTree(array_merge($tmpExprList, $localExprList));
                    }

                    $prev->setSubTree($curr->getSubTree());
                    if ($prev->isColumnReference()) {
                        if (PHPSQLParserConstants::getInstance()->isCustomFunction($prev->getUpper())) {
                            $prev->setTokenType(ExpressionType::CUSTOM_FUNCTION);
                        } else {
                            $prev->setTokenType(ExpressionType::SIMPLE_FUNCTION);
                        }
                        $prev->setNoQuotes(null, null, $this->options);
                    }

                    array_pop($resultList);
                    $curr = $prev;
                }

                // we have parenthesis, but it seems to be an expression
                if ($curr->isUnspecified()) {
                    $tmpExprList = array_values($localTokenList);
                    $localExprList = $this->process($tmpExprList);

                    $curr->setTokenType(ExpressionType::BRACKET_EXPRESSION);
                    if (!$curr->getSubTree()) {
                        if (!empty($localExprList)) {
                            $curr->setSubTree($localExprList);
                        }
                    } else {
                        $tmpExprList = $curr->getSubTree();
                        $curr->setSubTree(array_merge($tmpExprList, $localExprList));
                    }
                }

            } elseif ($curr->isVariableToken()) {

                # a variable
                # it can be quoted

                $curr->setTokenType($this->getVariableType($curr->getUpper()));
                $curr->setSubTree(false);
                $curr->setNoQuotes(trim(trim($curr->getToken()), '@'), "`'\"", $this->options);

            } else {
                /* it is either an operator, a colref or a constant */
                switch ($curr->getUpper()) {

                case '*':
                    $curr->setSubTree(false); // o subtree

                    // single or first element of expression list -> all-column-alias
                    if (empty($resultList)) {
                        $curr->setTokenType(ExpressionType::COLREF);
                        break;
                    }

                    // if the last token is colref, const or expression
                    // then * is an operator
                    // but if the previous colref ends with a dot, the * is the all-columns-alias
                    if (!$prev->isColumnReference() && !$prev->isConstant() && !$prev->isExpression()
                        && !$prev->isBracketExpression() && !$prev->isAggregateFunction() && !$prev->isVariable()) {
                        $curr->setTokenType(ExpressionType::COLREF);
                        break;
                    }

                    if ($prev->isColumnReference() && $prev->endsWith(".")) {
                        $prev->addToken('*'); // tablealias dot *
                        continue 2; // skip the current token
                    }

                    $curr->setTokenType(ExpressionType::OPERATOR);
                    break;

                case ':=':
                case 'AND':
                case '&&':
                case 'BETWEEN':
                case 'BINARY':
                case '&':
                case '~':
                case '|':
                case '^':
                case 'DIV':
                case '/':
                case '<=>':
                case '=':
                case '>=':
                case '>':
                case 'IS':
                case 'NOT':
                case '<<':
                case '<=':
                case '<':
                case 'LIKE':
                case '%':
                case '!=':
                case '<>':
                case 'REGEXP':
                case '!':
                case '||':
                case 'OR':
                case '>>':
                case 'RLIKE':
                case 'SOUNDS':
                case 'XOR':
                case 'IN':
                    $curr->setSubTree(false);
                    $curr->setTokenType(ExpressionType::OPERATOR);
                    break;

                case 'NULL':
                    $curr->setSubTree(false);
                    $curr->setTokenType(ExpressionType::CONSTANT);
                    break;

                case '-':
                case '+':
                // differ between preceding sign and operator
                    $curr->setSubTree(false);

                    if ($prev->isColumnReference() || $prev->isFunction() || $prev->isAggregateFunction()
                        || $prev->isConstant() || $prev->isSubQuery() || $prev->isExpression()
                        || $prev->isBracketExpression() || $prev->isVariable() || $prev->isCustomFunction()) {
                        $curr->setTokenType(ExpressionType::OPERATOR);
                    } else {
                        $curr->setTokenType(ExpressionType::SIGN);
                    }
                    break;

                default:
                    $curr->setSubTree(false);

                    switch ($curr->getToken(0)) {
                    case "'":
                    // it is a string literal
                        $curr->setTokenType(ExpressionType::CONSTANT);
                        break;
                    case '"':
                        if (!$this->options->getANSIQuotes()) {
                        // If we're not using ANSI quotes, this is a string literal.
                            $curr->setTokenType(ExpressionType::CONSTANT);
                            break;
                        }
                        // Otherwise continue to the next case
                    case '`':
                    // it is an escaped colum name
                        $curr->setTokenType(ExpressionType::COLREF);
                        $curr->setNoQuotes($curr->getToken(), null, $this->options);
                        break;

                    default:
                        if (is_numeric($curr->getToken())) {

                            if ($prev->isSign()) {
                                $prev->addToken($curr->getToken()); // it is a negative numeric constant
                                $prev->setTokenType(ExpressionType::CONSTANT);
                                continue 3;
                                // skip current token
                            } else {
                                $curr->setTokenType(ExpressionType::CONSTANT);
                            }
                        } else {
                            $curr->setTokenType(ExpressionType::COLREF);
                            $curr->setNoQuotes($curr->getToken(), null, $this->options);
                        }
                        break;
                    }
                }
            }

            /* is a reserved word? */
            if (!$curr->isOperator() && !$curr->isInList() && !$curr->isFunction() && !$curr->isAggregateFunction()
                && !$curr->isCustomFunction() && PHPSQLParserConstants::getInstance()->isReserved($curr->getUpper())) {

	            $next = isset( $tokens[ $k + 1 ] ) ? new ExpressionToken( $k + 1, $tokens[ $k + 1 ] ) : new ExpressionToken();
                $isEnclosedWithinParenthesis = $next->isEnclosedWithinParenthesis();
	            if ($isEnclosedWithinParenthesis && PHPSQLParserConstants::getInstance()->isCustomFunction($curr->getUpper())) {
                    $curr->setTokenType(ExpressionType::CUSTOM_FUNCTION);
                    $curr->setNoQuotes(null, null, $this->options);

                } elseif ($isEnclosedWithinParenthesis && PHPSQLParserConstants::getInstance()->isAggregateFunction($curr->getUpper())) {
                    $curr->setTokenType(ExpressionType::AGGREGATE_FUNCTION);
                    $curr->setNoQuotes(null, null, $this->options);

                } elseif ($curr->getUpper() === 'NULL') {
                    // it is a reserved word, but we would like to set it as constant
                    $curr->setTokenType(ExpressionType::CONSTANT);

                } else {
                    if ($isEnclosedWithinParenthesis && PHPSQLParserConstants::getInstance()->isParameterizedFunction($curr->getUpper())) {
                        // issue 60: check functions with parameters
                        // -> colref (we check parameters later)
                        // -> if there is no parameter, we leave the colref
                        $curr->setTokenType(ExpressionType::COLREF);

                    } elseif ($isEnclosedWithinParenthesis && PHPSQLParserConstants::getInstance()->isFunction($curr->getUpper())) {
                        $curr->setTokenType(ExpressionType::SIMPLE_FUNCTION);
                        $curr->setNoQuotes(null, null, $this->options);

                    }  elseif (!$isEnclosedWithinParenthesis && PHPSQLParserConstants::getInstance()->isFunction($curr->getUpper())) {
	                    // Colname using function name.
                    	$curr->setTokenType(ExpressionType::COLREF);
                    } else {
                        $curr->setTokenType(ExpressionType::RESERVED);
                        $curr->setNoQuotes(null, null, $this->options);
                    }
                }
            }

            // issue 94, INTERVAL 1 MONTH
            if ($curr->isConstant() && PHPSQLParserConstants::getInstance()->isParameterizedFunction($prev->getUpper())) {
                $prev->setTokenType(ExpressionType::RESERVED);
                $prev->setNoQuotes(null, null, $this->options);
            }

            if ($prev->isConstant() && PHPSQLParserConstants::getInstance()->isParameterizedFunction($curr->getUpper())) {
                $curr->setTokenType(ExpressionType::RESERVED);
                $curr->setNoQuotes(null, null, $this->options);
            }

            if ($curr->isUnspecified()) {
                $curr->setTokenType(ExpressionType::EXPRESSION);
                $curr->setNoQuotes(null, null, $this->options);
                $curr->setSubTree($this->process($this->splitSQLIntoTokens($curr->getTrim())));
            }

            $resultList[] = $curr;
            $prev = $curr;
        } // end of for-loop

        return $this->toArray($resultList);
    }
}
?>
