<?php
/**
 * ColumnDefinitionProcessor.php
 *
 * This file implements the processor for column definition part of a CREATE TABLE statement.
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
 * This class processes the column definition part of a CREATE TABLE statement.
 *
 * @author arothe
 *
 */
class ColumnDefinitionProcessor extends AbstractProcessor {

    protected function processExpressionList($parsed) {
        $processor = new ExpressionListProcessor($this->options);
        $expr = $this->removeParenthesisFromStart($parsed);
        $expr = $this->splitSQLIntoTokens($expr);
        $expr = $this->removeComma($expr);
        return $processor->process($expr);
    }

    protected function processReferenceDefinition($parsed) {
        $processor = new ReferenceDefinitionProcessor($this->options);
        return $processor->process($parsed);
    }

    protected function removeComma($tokens) {
        $res = array();
        foreach ($tokens as $token) {
            if (trim($token) !== ',') {
                $res[] = $token;
            }
        }
        return $res;
    }

    protected function buildColDef($expr, $base_expr, $options, $refs, $key) {
        $expr = array('expr_type' => ExpressionType::COLUMN_TYPE, 'base_expr' => $base_expr, 'sub_tree' => $expr);

        // add options first
        $expr['sub_tree'] = array_merge($expr['sub_tree'], $options['sub_tree']);
        unset($options['sub_tree']);
        $expr = array_merge($expr, $options);

        // followed by references
        if (sizeof($refs) !== 0) {
            $expr['sub_tree'] = array_merge($expr['sub_tree'], $refs);
        }

        $expr['till'] = $key;
        return $expr;
    }

    public function process($tokens) {

        $trim = '';
        $base_expr = '';
        $currCategory = '';
        $expr = array();
        $refs = array();
        $options = array('unique' => false, 'nullable' => true, 'auto_inc' => false, 'primary' => false,
                         'sub_tree' => array());
        $skip = 0;

        foreach ($tokens as $key => $token) {

            $trim = trim($token);
            $base_expr .= $token;

            if ($skip > 0) {
                $skip--;
                continue;
            }

            if ($skip < 0) {
                break;
            }

            if ($trim === '') {
                continue;
            }

            $upper = strtoupper($trim);

            switch ($upper) {

            case ',':
            // we stop on a single comma and return
            // the $expr entry and the index $key
                $expr = $this->buildColDef($expr, trim(substr($base_expr, 0, -strlen($token))), $options, $refs,
                    $key - 1);
                break 2;

            case 'VARCHAR':
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim, 'length' => false);
                $prevCategory = 'TEXT';
                $currCategory = 'SINGLE_PARAM_PARENTHESIS';
                continue 2;

            case 'VARBINARY':
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim, 'length' => false);
                $prevCategory = $upper;
                $currCategory = 'SINGLE_PARAM_PARENTHESIS';
                continue 2;

            case 'UNSIGNED':
                foreach (array_reverse(array_keys($expr)) as $i) {
                    if (isset($expr[$i]['expr_type']) && (ExpressionType::DATA_TYPE === $expr[$i]['expr_type'])) {
                        $expr[$i]['unsigned'] = true;
                        break;
                    }
                }
	            $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                continue 2;

            case 'ZEROFILL':
                $last = array_pop($expr);
                $last['zerofill'] = true;
                $expr[] = $last;
	            $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                continue 2;

            case 'BIT':
            case 'TINYBIT':
            case 'TINYINT':
            case 'SMALLINT':
            case 'MEDIUMINT':
            case 'INT':
            case 'INTEGER':
            case 'BIGINT':
            case 'BOOL':
            case 'BOOLEAN':
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim, 'unsigned' => false,
                                'zerofill' => false, 'length' => false);
                $currCategory = 'SINGLE_PARAM_PARENTHESIS';
                $prevCategory = $upper;
                continue 2;

            case 'BINARY':
                if ($currCategory === 'TEXT') {
                    $last = array_pop($expr);
                    $last['binary'] = true;
                    $last['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr[] = $last;
                    continue 2;
                }
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim, 'length' => false);
                $currCategory = 'SINGLE_PARAM_PARENTHESIS';
                $prevCategory = $upper;
                continue 2;

            case 'CHAR':
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim, 'length' => false);
                $currCategory = 'SINGLE_PARAM_PARENTHESIS';
                $prevCategory = 'TEXT';
                continue 2;

            case 'REAL':
            case 'DOUBLE':
            case 'FLOAT':
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim, 'unsigned' => false,
                                'zerofill' => false);
                $currCategory = 'TWO_PARAM_PARENTHESIS';
                $prevCategory = $upper;
                continue 2;

            case 'DECIMAL':
            case 'NUMERIC':
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim, 'unsigned' => false,
                                'zerofill' => false);
                $currCategory = 'TWO_PARAM_PARENTHESIS';
                $prevCategory = $upper;
                continue 2;

            case 'YEAR':
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim, 'length' => false);
                $currCategory = 'SINGLE_PARAM_PARENTHESIS';
                $prevCategory = $upper;
                continue 2;

            case 'DATE':
            case 'TIME':
            case 'TIMESTAMP':
            case 'DATETIME':
            case 'TINYBLOB':
            case 'BLOB':
            case 'MEDIUMBLOB':
            case 'LONGBLOB':
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim);
                $prevCategory = $currCategory = $upper;
                continue 2;

            // the next token can be BINARY
            case 'TINYTEXT':
            case 'TEXT':
            case 'MEDIUMTEXT':
            case 'LONGTEXT':
                $prevCategory = $currCategory = 'TEXT';
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim, 'binary' => false);
                continue 2;

            case 'ENUM':
                $currCategory = 'MULTIPLE_PARAM_PARENTHESIS';
                $prevCategory = 'TEXT';
                $expr[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim, 'sub_tree' => false);
                continue 2;

            case 'GEOMETRY':
            case 'POINT':
            case 'LINESTRING':
            case 'POLYGON':
            case 'MULTIPOINT':
            case 'MULTILINESTRING':
            case 'MULTIPOLYGON':
            case 'GEOMETRYCOLLECTION':
                $expr[] = array('expr_type' => ExpressionType::DATA_TYPE, 'base_expr' => $trim);
                $prevCategory = $currCategory = $upper;
                // TODO: is it right?
                // spatial types
                continue 2;

            case 'CHARACTER':
                $currCategory = 'CHARSET';
                $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                continue 2;

            case 'SET':
				if ($currCategory == 'CHARSET') {
    	            $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
				} else {
	                $currCategory = 'MULTIPLE_PARAM_PARENTHESIS';
    	            $prevCategory = 'TEXT';
        	        $expr[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim, 'sub_tree' => false);
				}
                continue 2;

            case 'COLLATE':
                $currCategory = $upper;
                $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                continue 2;

            case 'NOT':
            case 'NULL':
                $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                if ($options['nullable']) {
                    $options['nullable'] = ($upper === 'NOT' ? false : true);
                }
                continue 2;

            case 'DEFAULT':
            case 'COMMENT':
                $currCategory = $upper;
                $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                continue 2;

            case 'AUTO_INCREMENT':
                $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                $options['auto_inc'] = true;
                continue 2;

            case 'COLUMN_FORMAT':
            case 'STORAGE':
                $currCategory = $upper;
                $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                continue 2;

            case 'UNIQUE':
            // it can follow a KEY word
                $currCategory = $upper;
                $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                $options['unique'] = true;
                continue 2;

            case 'PRIMARY':
            // it must follow a KEY word
                $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                continue 2;

            case 'KEY':
                $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                if ($currCategory !== 'UNIQUE') {
                    $options['primary'] = true;
                }
                continue 2;

            case 'REFERENCES':
                $refs = $this->processReferenceDefinition(array_splice($tokens, $key - 1, null, true));
                $skip = $refs['till'] - $key;
                unset($refs['till']);
                // TODO: check this, we need the last comma
                continue 2;

            default:
                switch ($currCategory) {

                case 'STORAGE':
                    if ($upper === 'DISK' || $upper === 'MEMORY' || $upper === 'DEFAULT') {
                        $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                        $options['storage'] = $trim;
                        continue 3;
                    }
                    // else ?
                    break;

                case 'COLUMN_FORMAT':
                    if ($upper === 'FIXED' || $upper === 'DYNAMIC' || $upper === 'DEFAULT') {
                        $options['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                        $options['col_format'] = $trim;
                        continue 3;
                    }
                    // else ?
                    break;

                case 'COMMENT':
                // this is the comment string
                    $options['sub_tree'][] = array('expr_type' => ExpressionType::COMMENT, 'base_expr' => $trim);
                    $options['comment'] = $trim;
                    $currCategory = $prevCategory;
                    break;

                case 'DEFAULT':
                // this is the default value
                    $options['sub_tree'][] = array('expr_type' => ExpressionType::DEF_VALUE, 'base_expr' => $trim);
                    $options['default'] = $trim;
                    $currCategory = $prevCategory;
                    break;

                case 'COLLATE':
                // this is the collation name
                    $options['sub_tree'][] = array('expr_type' => ExpressionType::COLLATE, 'base_expr' => $trim);
                    $options['collate'] = $trim;
                    $currCategory = $prevCategory;
                    break;

                case 'CHARSET':
                // this is the character set name
                    $options['sub_tree'][] = array('expr_type' => ExpressionType::CHARSET, 'base_expr' => $trim);
                    $options['charset'] = $trim;
                    $currCategory = $prevCategory;
                  break;

                case 'SINGLE_PARAM_PARENTHESIS':
                    $parsed = $this->removeParenthesisFromStart($trim);
                    $parsed = array('expr_type' => ExpressionType::CONSTANT, 'base_expr' => trim($parsed));
                    $last = array_pop($expr);
                    $last['length'] = $parsed['base_expr'];

                    $expr[] = $last;
                    $expr[] = array('expr_type' => ExpressionType::BRACKET_EXPRESSION, 'base_expr' => $trim,
                                    'sub_tree' => array($parsed));
                    $currCategory = $prevCategory;
                    break;

                case 'TWO_PARAM_PARENTHESIS':
                // maximum of two parameters
                    $parsed = $this->processExpressionList($trim);

                    $last = array_pop($expr);
                    $last['length'] = $parsed[0]['base_expr'];
                    $last['decimals'] = isset($parsed[1]) ? $parsed[1]['base_expr'] : false;

                    $expr[] = $last;
                    $expr[] = array('expr_type' => ExpressionType::BRACKET_EXPRESSION, 'base_expr' => $trim,
                                    'sub_tree' => $parsed);
                    $currCategory = $prevCategory;
                    break;

                case 'MULTIPLE_PARAM_PARENTHESIS':
                // some parameters
                    $parsed = $this->processExpressionList($trim);

                    $last = array_pop($expr);
                    $subTree = array('expr_type' => ExpressionType::BRACKET_EXPRESSION, 'base_expr' => $trim,
                                     'sub_tree' => $parsed);

                    if ($this->options->getConsistentSubtrees()) {
                        $subTree = array($subTree);
                    }

                    $last['sub_tree'] = $subTree;
                    $expr[] = $last;
                    $currCategory = $prevCategory;
                    break;

                default:
                    break;
                }

            }
            $prevCategory = $currCategory;
            $currCategory = '';
        }

        if (!isset($expr['till'])) {
            // end of $tokens array
            $expr = $this->buildColDef($expr, trim($base_expr), $options, $refs, -1);
        }
        return $expr;
    }
}
?>
