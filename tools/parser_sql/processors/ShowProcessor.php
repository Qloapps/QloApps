<?php
/**
 * ShowProcessor.php
 *
 * This file implements the processor for the SHOW statements.
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
use PHPSQLParser\Options;
use PHPSQLParser\utils\ExpressionType;
use PHPSQLParser\utils\PHPSQLParserConstants;

/**
 *
 * This class processes the SHOW statements.
 *
 * @author arothe
 *
 */
class ShowProcessor extends AbstractProcessor {

    private $limitProcessor;

    public function __construct(Options $options) {
        parent::__construct($options);
        $this->limitProcessor = new LimitProcessor($options);
    }

    public function process($tokens) {
        $resultList = array();
        $category = "";
        $prev = "";

        foreach ($tokens as $k => $token) {
            $upper = strtoupper(trim($token));

            if ($this->isWhitespaceToken($token)) {
                continue;
            }

            switch ($upper) {

            case 'FROM':
                $resultList[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => trim($token));
                if ($prev === 'INDEX' || $prev === 'COLUMNS') {
                    break;
                }
                $category = $upper;
                break;

            case 'CREATE':
            case 'DATABASE':
            case 'SCHEMA':
            case 'FUNCTION':
            case 'PROCEDURE':
            case 'ENGINE':
            case 'TABLE':
            case 'FOR':
            case 'LIKE':
            case 'INDEX':
            case 'COLUMNS':
            case 'PLUGIN':
            case 'PRIVILEGES':
            case 'PROCESSLIST':
            case 'LOGS':
            case 'STATUS':
            case 'GLOBAL':
            case 'SESSION':
            case 'FULL':
            case 'GRANTS':
            case 'INNODB':
            case 'STORAGE':
            case 'ENGINES':
            case 'OPEN':
            case 'BDB':
            case 'TRIGGERS':
            case 'VARIABLES':
            case 'DATABASES':
            case 'SCHEMAS':
            case 'ERRORS':
            case 'TABLES':
            case 'WARNINGS':
            case 'CHARACTER':
            case 'SET':
            case 'COLLATION':
                $resultList[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => trim($token));
                $category = $upper;
                break;

            default:
                switch ($prev) {
                case 'LIKE':
                    $resultList[] = array('expr_type' => ExpressionType::CONSTANT, 'base_expr' => $token);
                    break;
                case 'LIMIT':
                    $limit = array_pop($resultList);
                    $limit['sub_tree'] = $this->limitProcessor->process(array_slice($tokens, $k));
                    $resultList[] = $limit;
                    break;
                case 'FROM':
                case 'SCHEMA':
                case 'DATABASE':
                    $resultList[] = array('expr_type' => ExpressionType::DATABASE, 'name' => $token,
                                          'no_quotes' => $this->revokeQuotation($token), 'base_expr' => $token);
                    break;
                case 'FOR':
                    $resultList[] = array('expr_type' => ExpressionType::USER, 'name' => $token,
                                          'no_quotes' => $this->revokeQuotation($token), 'base_expr' => $token);
                    break;
                case 'INDEX':
                case 'COLUMNS':
                case 'TABLE':
                    $resultList[] = array('expr_type' => ExpressionType::TABLE, 'table' => $token,
                                          'no_quotes' => $this->revokeQuotation($token), 'base_expr' => $token);
                    $category = "TABLENAME";
                    break;
                case 'FUNCTION':
                    if (PHPSQLParserConstants::getInstance()->isAggregateFunction($upper)) {
                        $expr_type = ExpressionType::AGGREGATE_FUNCTION;
                    } else {
                        $expr_type = ExpressionType::SIMPLE_FUNCTION;
                    }
                    $resultList[] = array('expr_type' => $expr_type, 'name' => $token,
                                          'no_quotes' => $this->revokeQuotation($token), 'base_expr' => $token);
                    break;
                case 'PROCEDURE':
                    $resultList[] = array('expr_type' => ExpressionType::PROCEDURE, 'name' => $token,
                                          'no_quotes' => $this->revokeQuotation($token), 'base_expr' => $token);
                    break;
                case 'ENGINE':
                    $resultList[] = array('expr_type' => ExpressionType::ENGINE, 'name' => $token,
                                          'no_quotes' => $this->revokeQuotation($token), 'base_expr' => $token);
                    break;
                default:
                // ignore
                    break;
                }
                break;
            }
            $prev = $category;
        }
        return $resultList;
    }
}
?>