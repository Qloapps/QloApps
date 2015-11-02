<?php
/**
 * CreateDefinitionProcessor.php
 *
 * This file implements the processor for the create definition within the TABLE statements.
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

require_once dirname(__FILE__) . '/AbstractProcessor.php';
require_once dirname(__FILE__) . '/ColumnDefinitionProcessor.php';
require_once dirname(__FILE__) . '/IndexColumnListProcessor.php';
require_once dirname(__FILE__) . '/ReferenceDefinitionProcessor.php';
require_once dirname(__FILE__) . '/../utils/ExpressionType.php';

/**
 *
 * This class processes the create definition of the TABLE statements.
 *
 * @author arothe
 *
 */
class CreateDefinitionProcessor extends AbstractProcessor {

    protected function correctExpressionType(&$expr) {
        $type = ExpressionType::EXPRESSION;
        if (!isset($expr[0]) || !isset($expr[0]['expr_type'])) {
            return $type;
        }

        # replace the constraint type with a more descriptive one
        switch ($expr[0]['expr_type']) {

        case ExpressionType::CONSTRAINT:
            $type = $expr[1]['expr_type'];
            $expr[1]['expr_type'] = ExpressionType::RESERVED;
            break;

        case ExpressionType::COLREF:
            $type = ExpressionType::COLDEF;
            break;

        default:
            $type = $expr[0]['expr_type'];
            $expr[0]['expr_type'] = ExpressionType::RESERVED;
            break;

        }
        return $type;
    }

    public function process($tokens) {

        $base_expr = "";
        $prevCategory = "";
        $currCategory = "";
        $expr = array();
        $result = array();
        $skip = 0;

        foreach ($tokens as $k => $token) {

            $trim = trim($token);
            $base_expr .= $token;

            if ($skip !== 0) {
                $skip--;
                continue;
            }

            if ($trim === "") {
                continue;
            }

            $upper = strtoupper($trim);

            switch ($upper) {

            case 'CONSTRAINT':
                $expr[] = array('expr_type' => ExpressionType::CONSTRAINT, 'base_expr' => $trim, 'sub_tree' => false);
                $currCategory = $prevCategory = $upper;
                continue 2;

            case 'LIKE':
                $expr[] = array('expr_type' => ExpressionType::LIKE, 'base_expr' => $trim);
                $currCategory = $prevCategory = $upper;
                continue 2;

            case 'FOREIGN':
                if ($prevCategory === "" || $prevCategory === "CONSTRAINT") {
                    $expr[] = array('expr_type' => ExpressionType::FOREIGN_KEY, 'base_expr' => $trim);
                    $currCategory = $upper;
                    continue 2;
                }
                # else ?
                break;

            case 'PRIMARY':
                if ($prevCategory === "" || $prevCategory === "CONSTRAINT") {
                    # next one is KEY
                    $expr[] = array('expr_type' => ExpressionType::PRIMARY_KEY, 'base_expr' => $trim);
                    $currCategory = $upper;
                    continue 2;
                }
                # else ?
                break;

            case 'UNIQUE':
                if ($prevCategory === "" || $prevCategory === "CONSTRAINT") {
                    # next one is KEY
                    $expr[] = array('expr_type' => ExpressionType::UNIQUE_IDX, 'base_expr' => $trim);
                    $currCategory = $upper;
                    continue 2;
                }
                # else ?
                break;

            case 'KEY':
            # the next one is an index name
                if ($currCategory === 'PRIMARY' || $currCategory === 'FOREIGN' || $currCategory === 'UNIQUE') {
                    $expr[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    continue 2;
                }
                $expr[] = array('expr_type' => ExpressionType::INDEX, 'base_expr' => $trim);
                $currCategory = $upper;
                continue 2;

            case 'CHECK':
                $expr[] = array('expr_type' => ExpressionType::CHECK, 'base_expr' => $trim);
                $currCategory = $upper;
                continue 2;

            case 'INDEX':
                if ($currCategory === 'UNIQUE' || $currCategory === 'FULLTEXT' || $currCategory === 'SPATIAL') {
                    $expr[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    continue 2;
                }
                $expr[] = array('expr_type' => ExpressionType::INDEX, 'base_expr' => $trim);
                $currCategory = $upper;
                continue 2;

            case 'FULLTEXT':
                $expr[] = array('expr_type' => ExpressionType::FULLTEXT_IDX, 'base_expr' => $trim);
                $currCategory = $prevCategory = $upper;
                continue 2;

            case 'SPATIAL':
                $expr[] = array('expr_type' => ExpressionType::SPATIAL_IDX, 'base_expr' => $trim);
                $currCategory = $prevCategory = $upper;
                continue 2;

            case 'WITH':
            # starts an index option
                if ($currCategory === 'INDEX_COL_LIST') {
                    $option = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr[] = array('expr_type' => ExpressionType::INDEX_PARSER,
                                    'base_expr' => substr($base_expr, 0, -strlen($token)),
                                    'sub_tree' => array($option));
                    $base_expr = $token;
                    $currCategory = 'INDEX_PARSER';
                    continue 2;
                }
                break;

            case 'KEY_BLOCK_SIZE':
            # starts an index option
                if ($currCategory === 'INDEX_COL_LIST') {
                    $option = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr[] = array('expr_type' => ExpressionType::INDEX_SIZE,
                                    'base_expr' => substr($base_expr, 0, -strlen($token)),
                                    'sub_tree' => array($option));
                    $base_expr = $token;
                    $currCategory = 'INDEX_SIZE';
                    continue 2;
                }
                break;

            case 'USING':
            # starts an index option
                if ($currCategory === 'INDEX_COL_LIST' || $currCategory === 'PRIMARY') {
                    $option = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr[] = array('base_expr' => substr($base_expr, 0, -strlen($token)), 'trim' => $trim,
                                    'category' => $currCategory, 'sub_tree' => array($option));
                    $base_expr = $token;
                    $currCategory = 'INDEX_TYPE';
                    continue 2;
                }
                # else ?
                break;

            case 'REFERENCES':
                if ($currCategory === 'INDEX_COL_LIST' && $prevCategory === 'FOREIGN') {
                    $processor = new ReferenceDefinitionProcessor();
                    $refs = $processor->process(array_slice($tokens, $k - 1, null, true));
                    $skip = $refs['till'] - $k;
                    unset($refs['till']);
                    $expr[] = $refs;
                    $currCategory = $upper;
                }
                break;

            case 'BTREE':
            case 'HASH':
                if ($currCategory === 'INDEX_TYPE') {
                    $last = array_pop($expr);
                    $last['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr[] = array('expr_type' => ExpressionType::INDEX_TYPE, 'base_expr' => $base_expr,
                                    'sub_tree' => $last['sub_tree']);
                    $base_expr = $last['base_expr'] . $base_expr;

                    # FIXME: it could be wrong for index_type within index_option
                    $currCategory = $last['category'];
                    continue 2;
                }
                #else
                break;

            case '=':
                if ($currCategory === 'INDEX_SIZE') {
                    # the optional character between KEY_BLOCK_SIZE and the numeric constant
                    $last = array_pop($expr);
                    $last['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr[] = $last;
                    continue 2;
                }
                break;

            case 'PARSER':
                if ($currCategory === 'INDEX_PARSER') {
                    $last = array_pop($expr);
                    $last['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr[] = $last;
                    continue 2;
                }
                # else?
                break;

            case ',':
            # this starts the next definition
                $type = $this->correctExpressionType($expr);
                $result['create-def'][] = array('expr_type' => $type,
                                                'base_expr' => trim(substr($base_expr, 0, -strlen($token))),
                                                'sub_tree' => $expr);
                $base_expr = "";
                $expr = array();
                break;

            default:
                switch ($currCategory) {

                case 'LIKE':
                # this is the tablename after LIKE
                    $expr[] = array('expr_type' => ExpressionType::TABLE, 'table' => $trim, 'base_expr' => $trim,
                                    'no_quotes' => $this->revokeQuotation($trim));
                    break;

                case 'PRIMARY':
                    if ($upper[0] === '(' && substr($upper, -1) === ')') {
                        # the column list
                        $processor = new IndexColumnListProcessor();
                        $cols = $processor->process($this->removeParenthesisFromStart($trim));
                        $expr[] = array('expr_type' => ExpressionType::COLUMN_LIST, 'base_expr' => $trim,
                                        'sub_tree' => $cols);
                        $prevCategory = $currCategory;
                        $currCategory = "INDEX_COL_LIST";
                        continue 3;
                    }
                    # else?
                    break;

                case 'FOREIGN':
                    if ($upper[0] === '(' && substr($upper, -1) === ')') {
                        $processor = new IndexColumnListProcessor();
                        $cols = $processor->process($this->removeParenthesisFromStart($trim));
                        $expr[] = array('expr_type' => ExpressionType::COLUMN_LIST, 'base_expr' => $trim,
                                        'sub_tree' => $cols);
                        $prevCategory = $currCategory;
                        $currCategory = "INDEX_COL_LIST";
                        continue 3;
                    }
                    # index name
                    $expr[] = array('expr_type' => ExpressionType::CONSTANT, 'base_expr' => $trim);
                    continue 3;

                case 'KEY':
                case 'UNIQUE':
                case 'INDEX':
                    if ($upper[0] === '(' && substr($upper, -1) === ')') {
                        $processor = new IndexColumnListProcessor();
                        $cols = $processor->process($this->removeParenthesisFromStart($trim));
                        $expr[] = array('expr_type' => ExpressionType::COLUMN_LIST, 'base_expr' => $trim,
                                        'sub_tree' => $cols);
                        $prevCategory = $currCategory;
                        $currCategory = "INDEX_COL_LIST";
                        continue 3;
                    }
                    # index name
                    $expr[] = array('expr_type' => ExpressionType::CONSTANT, 'base_expr' => $trim);
                    continue 3;

                case 'CONSTRAINT':
                # constraint name
                    $last = array_pop($expr);
                    $last['base_expr'] = $base_expr;
                    $last['sub_tree'] = array('expr_type' => ExpressionType::CONSTANT, 'base_expr' => $trim);
                    $expr[] = $last;
                    continue 3;

                case 'INDEX_PARSER':
                # index parser name
                    $last = array_pop($expr);
                    $last['sub_tree'][] = array('expr_type' => ExpressionType::CONSTANT, 'base_expr' => $trim);
                    $expr[] = array('expr_type' => ExpressionType::INDEX_PARSER, 'base_expr' => $base_expr,
                                    'sub_tree' => $last['sub_tree']);
                    $base_expr = $last['base_expr'] . $base_expr;
                    $currCategory = 'INDEX_COL_LIST';
                    continue 3;

                case 'INDEX_SIZE':
                # index key block size numeric constant
                    $last = array_pop($expr);
                    $last['sub_tree'][] = array('expr_type' => ExpressionType::CONSTANT, 'base_expr' => $trim);
                    $expr[] = array('expr_type' => ExpressionType::INDEX_SIZE, 'base_expr' => $base_expr,
                                    'sub_tree' => $last['sub_tree']);
                    $base_expr = $last['base_expr'] . $base_expr;
                    $currCategory = 'INDEX_COL_LIST';
                    continue 3;

                case 'CHECK':
                    if ($upper[0] === '(' && substr($upper, -1) === ')') {
                        $processor = new ExpressionListProcessor();
                        $unparsed = $this->splitSQLIntoTokens($this->removeParenthesisFromStart($trim));
                        $parsed = $processor->process($unparsed);
                        $expr[] = array('expr_type' => ExpressionType::BRACKET_EXPRESSION, 'base_expr' => $trim,
                                        'sub_tree' => $parsed);
                    }
                    # else?
                    break;

                case '':
                # if the currCategory is empty, we have an unknown token,
                # which is a column reference
                    $expr[] = array('expr_type' => ExpressionType::COLREF, 'base_expr' => $trim,
                                    'no_quotes' => $this->revokeQuotation($trim));
                    $currCategory = 'COLUMN_NAME';
                    continue 3;

                case 'COLUMN_NAME':
                # the column-definition
                # it stops on a comma or on a parenthesis
                    $processor = new ColumnDefinitionProcessor();
                    $parsed = $processor->process(array_slice($tokens, $k, null, true), $expr);
                    $skip = $parsed['till'] - $k;
                    unset($parsed['till']);
                    $expr[] = $parsed;
                    $currCategory = '';
                    break;

                default:
                # ?
                    break;
                }
                break;
            }
            $prevCategory = $currCategory;
            $currCategory = '';
        }

        $type = $this->correctExpressionType($expr);
        $result['create-def'][] = array('expr_type' => $type, 'base_expr' => trim($base_expr), 'sub_tree' => $expr);
        return $result;
    }
}
?>