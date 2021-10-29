<?php
/**
 * PartitionOptionsProcessor.php
 *
 * This file implements the processor for the PARTITION BY statements
 * within CREATE TABLE.
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

/**
 * This class processes the PARTITION BY statements within CREATE TABLE.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class PartitionOptionsProcessor extends AbstractProcessor {

    protected function processExpressionList($unparsed) {
        $processor = new ExpressionListProcessor($this->options);
        $expr = $this->removeParenthesisFromStart($unparsed);
        $expr = $this->splitSQLIntoTokens($expr);
        return $processor->process($expr);
    }

    protected function processColumnList($unparsed) {
        $processor = new ColumnListProcessor($this->options);
        $expr = $this->removeParenthesisFromStart($unparsed);
        return $processor->process($expr);
    }

    protected function processPartitionDefinition($unparsed) {
        $processor = new PartitionDefinitionProcessor($this->options);
        $expr = $this->removeParenthesisFromStart($unparsed);
        $expr = $this->splitSQLIntoTokens($expr);
        return $processor->process($expr);
    }

    protected function getReservedType($token) {
        return array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $token);
    }

    protected function getConstantType($token) {
        return array('expr_type' => ExpressionType::CONSTANT, 'base_expr' => $token);
    }

    protected function getOperatorType($token) {
        return array('expr_type' => ExpressionType::OPERATOR, 'base_expr' => $token);
    }

    protected function getBracketExpressionType($token) {
        return array('expr_type' => ExpressionType::BRACKET_EXPRESSION, 'base_expr' => $token, 'sub_tree' => false);
    }

    public function process($tokens) {

        $result = array('partition-options' => array(), 'last-parsed' => false);

        $prevCategory = '';
        $currCategory = '';
        $parsed = array();
        $expr = array();
        $base_expr = '';
        $skip = 0;

        foreach ($tokens as $tokenKey => $token) {
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

            case 'PARTITION':
                $currCategory = $upper;
                $expr[] = $this->getReservedType($trim);
                $parsed[] = array('expr_type' => ExpressionType::PARTITION, 'base_expr' => trim($base_expr),
                                  'sub_tree' => false);
                break;

            case 'SUBPARTITION':
                $currCategory = $upper;
                $expr[] = $this->getReservedType($trim);
                $parsed[] = array('expr_type' => ExpressionType::SUBPARTITION, 'base_expr' => trim($base_expr),
                                  'sub_tree' => false);
                break;

            case 'BY':
                if ($prevCategory === 'PARTITION' || $prevCategory === 'SUBPARTITION') {
                    $expr[] = $this->getReservedType($trim);
                    continue 2;
                }
                break;

            case 'PARTITIONS':
            case 'SUBPARTITIONS':
                $currCategory = 'PARTITION_NUM';
                $expr = array('expr_type' => constant('PHPSQLParser\utils\ExpressionType::' . substr($upper, 0, -1) . '_COUNT'),
                              'base_expr' => false, 'sub_tree' => array($this->getReservedType($trim)),
                              'storage' => substr($base_expr, 0, -strlen($token)));
                $base_expr = $token;
                continue 2;

            case 'LINEAR':
            // followed by HASH or KEY
                $currCategory = $upper;
                $expr[] = $this->getReservedType($trim);
                continue 2;

            case 'HASH':
            case 'KEY':
                $expr[] = array('expr_type' => constant('PHPSQLParser\utils\ExpressionType::' . $prevCategory . '_' . $upper),
                                'base_expr' => false, 'linear' => ($currCategory === 'LINEAR'), 'sub_tree' => false,
                                'storage' => substr($base_expr, 0, -strlen($token)));

                $last = array_pop($parsed);
                $last['by'] = trim($currCategory . ' ' . $upper); // $currCategory will be empty or LINEAR!
                $last['sub_tree'] = $expr;
                $parsed[] = $last;

                $base_expr = $token;
                $expr = array($this->getReservedType($trim));

                $currCategory = $upper;
                continue 2;

            case 'ALGORITHM':
                if ($currCategory === 'KEY') {
                    $expr[] = array('expr_type' => constant('PHPSQLParser\utils\ExpressionType::' . $prevCategory . '_KEY_ALGORITHM'),
                                    'base_expr' => false, 'sub_tree' => false,
                                    'storage' => substr($base_expr, 0, -strlen($token)));

                    $last = array_pop($parsed);
                    $subtree = array_pop($last['sub_tree']);
                    $subtree['sub_tree'] = $expr;
                    $last['sub_tree'][] = $subtree;
                    $parsed[] = $last;
                    unset($subtree);
                    unset($last);

                    $base_expr = $token;
                    $expr = array($this->getReservedType($trim));
                    $currCategory = $upper;
                    continue 2;
                }
                break;

            case 'RANGE':
            case 'LIST':
                $expr[] = array('expr_type' => constant('PHPSQLParser\utils\ExpressionType::PARTITION_' . $upper), 'base_expr' => false,
                                'sub_tree' => false, 'storage' => substr($base_expr, 0, -strlen($token)));

                $last = array_pop($parsed);
                $last['by'] = $upper;
                $last['sub_tree'] = $expr;
                $parsed[] = $last;
                unset($last);

                $base_expr = $token;
                $expr = array($this->getReservedType($trim));

                $currCategory = $upper . '_EXPR';
                continue 2;

            case 'COLUMNS':
                if ($currCategory === 'RANGE_EXPR' || $currCategory === 'LIST_EXPR') {
                    $expr[] = $this->getReservedType($trim);
                    $currCategory = substr($currCategory, 0, -4) . $upper;
                    continue 2;
                }
                break;

            case '=':
                if ($currCategory === 'ALGORITHM') {
                    // between ALGORITHM and a constant
                    $expr[] = $this->getOperatorType($trim);
                    continue 2;
                }
                break;

            default:
                switch ($currCategory) {

                case 'PARTITION_NUM':
                // the number behind PARTITIONS or SUBPARTITIONS
                    $expr['base_expr'] = trim($base_expr);
                    $expr['sub_tree'][] = $this->getConstantType($trim);
                    $base_expr = $expr['storage'] . $base_expr;
                    unset($expr['storage']);

                    $last = array_pop($parsed);
                    $last['count'] = $trim;
                    $last['sub_tree'][] = $expr;
                    $last['base_expr'] .= $base_expr;
                    $parsed[] = $last;
                    unset($last);

                    $expr = array();
                    $base_expr = '';
                    $currCategory = $prevCategory;
                    break;

                case 'ALGORITHM':
                // the number of the algorithm
                    $expr[] = $this->getConstantType($trim);

                    $last = array_pop($parsed);
                    $subtree = array_pop($last['sub_tree']);
                    $key = array_pop($subtree['sub_tree']);

                    $key['sub_tree'] = $expr;
                    $key['base_expr'] = trim($base_expr);

                    $base_expr = $key['storage'] . $base_expr;
                    unset($key['storage']);

                    $subtree['sub_tree'][] = $key;
                    unset($key);

                    $expr = $subtree['sub_tree'];
                    $subtree['sub_tree'] = false;
                    $subtree['algorithm'] = $trim;
                    $last['sub_tree'][] = $subtree;
                    unset($subtree);

                    $parsed[] = $last;
                    unset($last);
                    $currCategory = 'KEY';
                    continue 3;

                case 'LIST_EXPR':
                case 'RANGE_EXPR':
                case 'HASH':
                // parenthesis around an expression
                    $last = $this->getBracketExpressionType($trim);
                    $res = $this->processExpressionList($trim);
                    $last['sub_tree'] = (empty($res) ? false : $res);
                    $expr[] = $last;

                    $last = array_pop($parsed);
                    $subtree = array_pop($last['sub_tree']);
                    $subtree['base_expr'] = $base_expr;
                    $subtree['sub_tree'] = $expr;

                    $base_expr = $subtree['storage'] . $base_expr;
                    unset($subtree['storage']);
                    $last['sub_tree'][] = $subtree;
                    $last['base_expr'] = trim($base_expr);
                    $parsed[] = $last;
                    unset($last);
                    unset($subtree);

                    $expr = array();
                    $base_expr = '';
                    $currCategory = $prevCategory;
                    break;

                case 'LIST_COLUMNS':
                case 'RANGE_COLUMNS':
                case 'KEY':
                // the columnlist
                    $expr[] = array('expr_type' => ExpressionType::COLUMN_LIST, 'base_expr' => $trim,
                                    'sub_tree' => $this->processColumnList($trim));

                    $last = array_pop($parsed);
                    $subtree = array_pop($last['sub_tree']);
                    $subtree['base_expr'] = $base_expr;
                    $subtree['sub_tree'] = $expr;

                    $base_expr = $subtree['storage'] . $base_expr;
                    unset($subtree['storage']);
                    $last['sub_tree'][] = $subtree;
                    $last['base_expr'] = trim($base_expr);
                    $parsed[] = $last;
                    unset($last);
                    unset($subtree);

                    $expr = array();
                    $base_expr = '';
                    $currCategory = $prevCategory;
                    break;

                case '':
                    if ($prevCategory === 'PARTITION' || $prevCategory === 'SUBPARTITION') {
                        if ($upper[0] === '(' && substr($upper, -1) === ')') {
                            // last part to process, it is only one token!
                            $last = $this->getBracketExpressionType($trim);
                            $last['sub_tree'] = $this->processPartitionDefinition($trim);
                            $parsed[] = $last;
                            break;
                        }
                    }
                    // else ?
                    break;

                default:
                    break;
                }
                break;
            }

            $prevCategory = $currCategory;
            $currCategory = '';
        }

        $result['partition-options'] = $parsed;
        if ($result['last-parsed'] === false) {
            $result['last-parsed'] = $tokenKey;
        }
        return $result;
    }
}
?>
