<?php
/**
 * ReferenceDefinitionProcessor.php
 *
 * This file implements the processor reference definition part of the CREATE TABLE statements.
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
 * This class processes the reference definition part of the CREATE TABLE statements.
 *
 * @author arothe
 */
class ReferenceDefinitionProcessor extends AbstractProcessor {

    protected function buildReferenceDef($expr, $base_expr, $key) {
        $expr['till'] = $key;
        $expr['base_expr'] = $base_expr;
        return $expr;
    }

    public function process($tokens) {

        $expr = array('expr_type' => ExpressionType::REFERENCE, 'base_expr' => false, 'sub_tree' => array());
        $base_expr = '';

        foreach ($tokens as $key => $token) {

            $trim = trim($token);
            $base_expr .= $token;

            if ($trim === '') {
                continue;
            }

            $upper = strtoupper($trim);

            switch ($upper) {

            case ',':
            # we stop on a single comma
            # or at the end of the array $tokens
                $expr = $this->buildReferenceDef($expr, trim(substr($base_expr, 0, -strlen($token))), $key - 1);
                break 2;

            case 'REFERENCES':
                $expr['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                $currCategory = $upper;
                break;

            case 'MATCH':
                if ($currCategory === 'REF_COL_LIST') {
                    $expr['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $currCategory = 'REF_MATCH';
                    continue 2;
                }
                # else?
                break;

            case 'FULL':
            case 'PARTIAL':
            case 'SIMPLE':
                if ($currCategory === 'REF_MATCH') {
                    $expr['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr['match'] = $upper;
                    $currCategory = 'REF_COL_LIST';
                    continue 2;
                }
                # else?
                break;

            case 'ON':
                if ($currCategory === 'REF_COL_LIST') {
                    $expr['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $currCategory = 'REF_ACTION';
                    continue 2;
                }
                # else ?
                break;

            case 'UPDATE':
            case 'DELETE':
                if ($currCategory === 'REF_ACTION') {
                    $expr['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $currCategory = 'REF_OPTION_' . $upper;
                    continue 2;
                }
                # else ?
                break;

            case 'RESTRICT':
            case 'CASCADE':
                if (strpos($currCategory, 'REF_OPTION_') === 0) {
                    $expr['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr['on_' . strtolower(substr($currCategory, -6))] = $upper;
                    continue 2;
                }
                # else ?
                break;

            case 'SET':
            case 'NO':
                if (strpos($currCategory, 'REF_OPTION_') === 0) {
                    $expr['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr['on_' . strtolower(substr($currCategory, -6))] = $upper;
                    $currCategory = 'SEC_' . $currCategory;
                    continue 2;
                }
                # else ?
                break;

            case 'NULL':
            case 'ACTION':
                if (strpos($currCategory, 'SEC_REF_OPTION_') === 0) {
                    $expr['sub_tree'][] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $expr['on_' . strtolower(substr($currCategory, -6))] .= ' ' . $upper;
                    $currCategory = 'REF_COL_LIST';
                    continue 2;
                }
                # else ?
                break;

            default:
                switch ($currCategory) {

                case 'REFERENCES':
                    if ($upper[0] === '(' && substr($upper, -1) === ')') {
                        # index_col_name list
                        $processor = new IndexColumnListProcessor($this->options);
                        $cols = $processor->process($this->removeParenthesisFromStart($trim));
                        $expr['sub_tree'][] = array('expr_type' => ExpressionType::COLUMN_LIST, 'base_expr' => $trim,
                                                    'sub_tree' => $cols);
                        $currCategory = 'REF_COL_LIST';
                        continue 3;
                    }
                    # foreign key reference table name
                    $expr['sub_tree'][] = array('expr_type' => ExpressionType::TABLE, 'table' => $trim,
                                                'base_expr' => $trim, 'no_quotes' => $this->revokeQuotation($trim));
                    continue 3;

                default:
                # else ?
                    break;
                }
                break;
            }
        }

        if (!isset($expr['till'])) {
            $expr = $this->buildReferenceDef($expr, trim($base_expr), -1);
        }
        return $expr;
    }
}
?>