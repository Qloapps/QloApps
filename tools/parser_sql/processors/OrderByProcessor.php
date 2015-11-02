<?php
/**
 * OrderByProcessor.php
 *
 * This file implements the processor for the ORDER-BY statements.
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

require_once(dirname(__FILE__) . '/AbstractProcessor.php');
require_once(dirname(__FILE__) . '/SelectExpressionProcessor.php');
require_once(dirname(__FILE__) . '/../utils/ExpressionType.php');

/**
 * 
 * This class processes the ORDER-BY statements.
 * 
 * @author arothe
 * 
 */
class OrderByProcessor extends AbstractProcessor {

    private $selectExpressionProcessor;

    public function __construct() {
        $this->selectExpressionProcessor = new SelectExpressionProcessor();
    }

    protected function initParseInfo() {
        return array('base_expr' => "", 'dir' => "ASC", 'expr_type' => ExpressionType::EXPRESSION);
    }

    protected function processOrderExpression(&$parseInfo, $select) {
        $parseInfo['base_expr'] = trim($parseInfo['base_expr']);

        if ($parseInfo['base_expr'] === "") {
            return false;
        }

        if (is_numeric($parseInfo['base_expr'])) {
            $parseInfo['expr_type'] = ExpressionType::POSITION;
        } else {
            $parseInfo['no_quotes'] = $this->revokeQuotation($parseInfo['base_expr']);
            // search to see if the expression matches an alias
            foreach ($select as $clause) {
                if (!$clause['alias']) {
                    continue;
                }

                if ($clause['alias']['no_quotes'] === $parseInfo['no_quotes']) {
                    $parseInfo['expr_type'] = ExpressionType::ALIAS;
                    break;
                }
            }
        }

        if ($parseInfo['expr_type'] === ExpressionType::EXPRESSION) {
            $expr = $this->selectExpressionProcessor->process($parseInfo['base_expr']);
            $expr['direction'] = $parseInfo['dir'];
            unset($expr['alias']);
            return $expr;
        }

        $result = array();
        $result['expr_type'] = $parseInfo['expr_type'];
        $result['base_expr'] = $parseInfo['base_expr'];
        if (isset($parseInfo['no_quotes'])) {
            $result['no_quotes'] = $parseInfo['no_quotes'];
        }
        $result['direction'] = $parseInfo['dir'];
        return $result;
    }

    public function process($tokens, $select = array()) {
        $out = array();
        $parseInfo = $this->initParseInfo();

        if (!$tokens) {
            return false;
        }

        foreach ($tokens as $token) {
            $upper = strtoupper(trim($token));
            switch ($upper) {
            case ',':
                $out[] = $this->processOrderExpression($parseInfo, $select);
                $parseInfo = $this->initParseInfo();
                break;

            case 'DESC':
                $parseInfo['dir'] = "DESC";
                break;

            case 'ASC':
                $parseInfo['dir'] = "ASC";
                break;

            default:
                $parseInfo['base_expr'] .= $token;
            }
        }

        $out[] = $this->processOrderExpression($parseInfo, $select);
        return $out;
    }
}
?>