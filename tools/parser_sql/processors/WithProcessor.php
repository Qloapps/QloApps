<?php
/**
 * WithProcessor.php
 *
 * This file implements the processor for Oracle's WITH statements.
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
 * This class processes Oracle's WITH statements.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class WithProcessor extends AbstractProcessor {

    protected function processTopLevel($sql) {
    	$processor = new DefaultProcessor($this->options);
    	return $processor->process($sql);
    }

    protected function buildTableName($token) {
    	return array('expr_type' => ExpressionType::TEMPORARY_TABLE, 'name'=>$token, 'base_expr' => $token, 'no_quotes' => $this->revokeQuotation($token));
    }

    public function process($tokens) {
    	$out = array();
        $resultList = array();
        $category = '';
        $base_expr = '';
        $prev = '';

        foreach ($tokens as $token) {
        	$base_expr .= $token;
            $upper = strtoupper(trim($token));

            if ($this->isWhitespaceToken($token)) {
                continue;
            }

			$trim = trim($token);
            switch ($upper) {

            case 'AS':
            	if ($prev !== 'TABLENAME') {
            		// error or tablename is AS
            		$resultList[] = $this->buildTableName($trim);
            		$category = 'TABLENAME';
            		break;
            	}

            	$resultList[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
            	$category = $upper;
                break;

            case ',':
            	// ignore
            	$base_expr = '';
            	break;

            default:
                switch ($prev) {
                	case 'AS':
                		// it follows a parentheses pair
                		$subtree = $this->processTopLevel($this->removeParenthesisFromStart($token));
                		$resultList[] = array('expr_type' => ExpressionType::BRACKET_EXPRESSION, 'base_expr' => $trim, 'sub_tree' => $subtree);

                		$out[] = array('expr_type' => ExpressionType::SUBQUERY_FACTORING, 'base_expr' => trim($base_expr), 'sub_tree' => $resultList);
                		$resultList = array();
                		$category = '';
                	break;

                	case '':
                		// we have the name of the table
                		$resultList[] = $this->buildTableName($trim);
                		$category = 'TABLENAME';
                		break;

                default:
                // ignore
                    break;
                }
                break;
            }
            $prev = $category;
        }
        return $out;
    }
}
?>