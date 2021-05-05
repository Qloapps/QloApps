<?php
/**
 * PartitionDefinitionProcessor.php
 *
 * This file implements the processor for the PARTITION statements
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
 * This class processes the PARTITION statements within CREATE TABLE.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class PartitionDefinitionProcessor extends AbstractProcessor {

    protected function processExpressionList($unparsed) {
        $processor = new ExpressionListProcessor($this->options);
        $expr = $this->removeParenthesisFromStart($unparsed);
        $expr = $this->splitSQLIntoTokens($expr);
        return $processor->process($expr);
    }

    protected function processSubpartitionDefinition($unparsed) {
        $processor = new SubpartitionDefinitionProcessor($this->options);
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

        $result = array();
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
                if ($currCategory === '') {
                    $expr[] = $this->getReservedType($trim);
                    $parsed = array('expr_type' => ExpressionType::PARTITION_DEF, 'base_expr' => trim($base_expr),
                                    'sub_tree' => false);
                    $currCategory = $upper;
                    continue 2;
                }
                // else ?
                break;

            case 'VALUES':
                if ($prevCategory === 'PARTITION') {
                    $expr[] = array('expr_type' => ExpressionType::PARTITION_VALUES, 'base_expr' => false,
                                    'sub_tree' => false, 'storage' => substr($base_expr, 0, -strlen($token)));
                    $parsed['sub_tree'] = $expr;

                    $base_expr = $token;
                    $expr = array($this->getReservedType($trim));

                    $currCategory = $upper;
                    continue 2;
                }
                // else ?
                break;

            case 'LESS':
                if ($currCategory === 'VALUES') {
                    $expr[] = $this->getReservedType($trim);
                    continue 2;
                }
                // else ?
                break;

            case 'THAN':
                if ($currCategory === 'VALUES') {
                    // followed by parenthesis and (value-list or expr)
                    $expr[] = $this->getReservedType($trim);
                    continue 2;
                }
                // else ?
                break;

            case 'MAXVALUE':
                if ($currCategory === 'VALUES') {
                    $expr[] = $this->getConstantType($trim);

                    $last = array_pop($parsed['sub_tree']);
                    $last['base_expr'] = $base_expr;
                    $last['sub_tree'] = $expr;

                    $base_expr = $last['storage'] . $base_expr;
                    unset($last['storage']);
                    $parsed['sub_tree'][] = $last;
                    $parsed['base_expr'] = trim($base_expr);

                    $expr = $parsed['sub_tree'];
                    unset($last);
                    $currCategory = $prevCategory;
                }
                // else ?
                break;

            case 'IN':
                if ($currCategory === 'VALUES') {
                    // followed by parenthesis and value-list
                    $expr[] = $this->getReservedType($trim);
                    continue 2;
                }
                break;

            case 'COMMENT':
                if ($prevCategory === 'PARTITION') {
                    $expr[] = array('expr_type' => ExpressionType::PARTITION_COMMENT, 'base_expr' => false,
                                    'sub_tree' => false, 'storage' => substr($base_expr, 0, -strlen($token)));

                    $parsed['sub_tree'] = $expr;
                    $base_expr = $token;
                    $expr = array($this->getReservedType($trim));

                    $currCategory = $upper;
                    continue 2;
                }
                // else ?
                break;

            case 'STORAGE':
                if ($prevCategory === 'PARTITION') {
                    // followed by ENGINE
                    $expr[] = array('expr_type' => ExpressionType::ENGINE, 'base_expr' => false, 'sub_tree' => false,
                                    'storage' => substr($base_expr, 0, -strlen($token)));

                    $parsed['sub_tree'] = $expr;
                    $base_expr = $token;
                    $expr = array($this->getReservedType($trim));

                    $currCategory = $upper;
                    continue 2;
                }
                // else ?
                break;

            case 'ENGINE':
                if ($currCategory === 'STORAGE') {
                    $expr[] = $this->getReservedType($trim);
                    $currCategory = $upper;
                    continue 2;
                }
                if ($prevCategory === 'PARTITION') {
                    $expr[] = array('expr_type' => ExpressionType::ENGINE, 'base_expr' => false, 'sub_tree' => false,
                                    'storage' => substr($base_expr, 0, -strlen($token)));

                    $parsed['sub_tree'] = $expr;
                    $base_expr = $token;
                    $expr = array($this->getReservedType($trim));

                    $currCategory = $upper;
                    continue 2;
                }
                // else ?
                break;

            case '=':
                if (in_array($currCategory, array('ENGINE', 'COMMENT', 'DIRECTORY', 'MAX_ROWS', 'MIN_ROWS'))) {
                    $expr[] = $this->getOperatorType($trim);
                    continue 2;
                }
                // else ?
                break;

            case ',':
                if ($prevCategory === 'PARTITION' && $currCategory === '') {
                    // it separates the partition-definitions
                    $result[] = $parsed;
                    $parsed = array();
                    $base_expr = '';
                    $expr = array();
                }
                break;

            case 'DATA':
            case 'INDEX':
                if ($prevCategory === 'PARTITION') {
                    // followed by DIRECTORY
                    $expr[] = array('expr_type' => constant('PHPSQLParser\utils\ExpressionType::PARTITION_' . $upper . '_DIR'),
                                    'base_expr' => false, 'sub_tree' => false,
                                    'storage' => substr($base_expr, 0, -strlen($token)));

                    $parsed['sub_tree'] = $expr;
                    $base_expr = $token;
                    $expr = array($this->getReservedType($trim));

                    $currCategory = $upper;
                    continue 2;
                }
                // else ?
                break;

            case 'DIRECTORY':
                if ($currCategory === 'DATA' || $currCategory === 'INDEX') {
                    $expr[] = $this->getReservedType($trim);
                    $currCategory = $upper;
                    continue 2;
                }
                // else ?
                break;

            case 'MAX_ROWS':
            case 'MIN_ROWS':
                if ($prevCategory === 'PARTITION') {
                    $expr[] = array('expr_type' => constant('PHPSQLParser\utils\ExpressionType::PARTITION_' . $upper),
                                    'base_expr' => false, 'sub_tree' => false,
                                    'storage' => substr($base_expr, 0, -strlen($token)));

                    $parsed['sub_tree'] = $expr;
                    $base_expr = $token;
                    $expr = array($this->getReservedType($trim));

                    $currCategory = $upper;
                    continue 2;
                }
                // else ?
                break;

            default:
                switch ($currCategory) {

                case 'MIN_ROWS':
                case 'MAX_ROWS':
                case 'ENGINE':
                case 'DIRECTORY':
                case 'COMMENT':
                    $expr[] = $this->getConstantType($trim);

                    $last = array_pop($parsed['sub_tree']);
                    $last['sub_tree'] = $expr;
                    $last['base_expr'] = trim($base_expr);
                    $base_expr = $last['storage'] . $base_expr;
                    unset($last['storage']);

                    $parsed['sub_tree'][] = $last;
                    $parsed['base_expr'] = trim($base_expr);

                    $expr = $parsed['sub_tree'];
                    unset($last);

                    $currCategory = $prevCategory;
                    break;

                case 'PARTITION':
                // that is the partition name
                    $last = array_pop($expr);
                    $last['name'] = $trim;
                    $expr[] = $last;
                    $expr[] = $this->getConstantType($trim);
                    $parsed['sub_tree'] = $expr;
                    $parsed['base_expr'] = trim($base_expr);
                    break;

                case 'VALUES':
                // we have parenthesis and have to process an expression/in-list
                    $last = $this->getBracketExpressionType($trim);

                    $res = $this->processExpressionList($trim);
                    $last['sub_tree'] = (empty($res) ? false : $res);
                    $expr[] = $last;

                    $last = array_pop($parsed['sub_tree']);
                    $last['base_expr'] = $base_expr;
                    $last['sub_tree'] = $expr;

                    $base_expr = $last['storage'] . $base_expr;
                    unset($last['storage']);
                    $parsed['sub_tree'][] = $last;
                    $parsed['base_expr'] = trim($base_expr);

                    $expr = $parsed['sub_tree'];
                    unset($last);

                    $currCategory = $prevCategory;
                    break;

                case '':
                    if ($prevCategory === 'PARTITION') {
                        // last part to process, it is only one token!
                        if ($upper[0] === '(' && substr($upper, -1) === ')') {
                            $last = $this->getBracketExpressionType($trim);
                            $last['sub_tree'] = $this->processSubpartitionDefinition($trim);
                            $expr[] = $last;
                            unset($last);

                            $parsed['base_expr'] = trim($base_expr);
                            $parsed['sub_tree'] = $expr;

                            $currCategory = $prevCategory;
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

        $result[] = $parsed;
        return $result;
    }
}
?>
