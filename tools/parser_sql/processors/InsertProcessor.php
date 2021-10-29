<?php
/**
 * InsertProcessor.php
 *
 * This file implements the processor for the INSERT statements.
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
 * This class processes the INSERT statements.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class InsertProcessor extends AbstractProcessor {

    protected function processOptions($tokenList) {
        if (!isset($tokenList['OPTIONS'])) {
            return array();
        }
        $result = array();
        foreach ($tokenList['OPTIONS'] as $token) {
            $result[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => trim($token));
        }
        return $result;
    }

    protected function processKeyword($keyword, $tokenList) {
        if (!isset($tokenList[$keyword])) {
            return array('', false, array());
        }

        $table = '';
        $cols = false;
        $result = array();

        foreach ($tokenList[$keyword] as $token) {
            $trim = trim($token);

            if ($trim === '') {
                continue;
            }

            $upper = strtoupper($trim);
            switch ($upper) {
            case 'INTO':
                $result[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                break;

            case 'INSERT':
            case 'REPLACE':
                break;

            default:
                if ($table === '') {
                    $table = $trim;
                    break;
                }

                if ($cols === false) {
                    $cols = $trim;
                }
                break;
            }
        }
        return array($table, $cols, $result);
    }

    protected function processColumns($cols) {
        if ($cols === false) {
            return $cols;
        }
        if ($cols[0] === '(' && substr($cols, -1) === ')') {
            $parsed = array('expr_type' => ExpressionType::BRACKET_EXPRESSION, 'base_expr' => $cols,
                            'sub_tree' => false);
        }
        $cols = $this->removeParenthesisFromStart($cols);
        if (stripos($cols, 'SELECT') === 0) {
            $processor = new DefaultProcessor($this->options);
            $parsed['sub_tree'] = array(
                    array('expr_type' => ExpressionType::QUERY, 'base_expr' => $cols,
                            'sub_tree' => $processor->process($cols)));
        } else {
            $processor = new ColumnListProcessor($this->options);
            $parsed['sub_tree'] = $processor->process($cols);
            $parsed['expr_type'] = ExpressionType::COLUMN_LIST;
        }
        return $parsed;
    }

    public function process($tokenList, $token_category = 'INSERT') {
        $table = '';
        $cols = false;
        $comments = array();

        foreach ($tokenList as $key => &$token) {
            if ($key == 'VALUES') {
                continue;
            }
            foreach ($token as &$value) {
                if ($this->isCommentToken($value)) {
                     $comments[] = parent::processComment($value);
                     $value = '';
                }
            }
        }

        $parsed = $this->processOptions($tokenList);
        unset($tokenList['OPTIONS']);

        list($table, $cols, $key) = $this->processKeyword('INTO', $tokenList);
        $parsed = array_merge($parsed, $key);
        unset($tokenList['INTO']);

        if ($table === '' && in_array($token_category, array('INSERT', 'REPLACE'))) {
            list($table, $cols, $key) = $this->processKeyword($token_category, $tokenList);
        }

        $parsed[] = array('expr_type' => ExpressionType::TABLE, 'table' => $table,
                          'no_quotes' => $this->revokeQuotation($table), 'alias' => false, 'base_expr' => $table);

        $cols = $this->processColumns($cols);
        if ($cols !== false) {
            $parsed[] = $cols;
        }

        $parsed = array_merge($parsed, $comments);

        $tokenList[$token_category] = $parsed;
        return $tokenList;
    }
}
?>
