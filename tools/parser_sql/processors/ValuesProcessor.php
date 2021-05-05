<?php
/**
 * ValuesProcessor.php
 *
 * This file implements the processor for the VALUES statements.
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
 * This class processes the VALUES statements.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class ValuesProcessor extends AbstractProcessor {

    protected function processExpressionList($unparsed) {
        $processor = new ExpressionListProcessor($this->options);
        return $processor->process($unparsed);
    }

    protected function processRecord($unparsed) {
        $processor = new RecordProcessor($this->options);
        return $processor->process($unparsed);
    }

    public function process($tokens) {

        $currCategory = '';
        $parsed = array();
        $base_expr = '';

        foreach ($tokens['VALUES'] as $k => $v) {
	        if ($this->isCommentToken($v)) {
		        $parsed[] = parent::processComment($v);
		        continue;
	        }

	        $base_expr .= $v;
	        $trim = trim($v);

            if ($this->isWhitespaceToken($v)) {
                continue;
            }

            $upper = strtoupper($trim);
            switch ($upper) {

            case 'ON':
                if ($currCategory === '') {

                    $base_expr = trim(substr($base_expr, 0, -strlen($v)));
                    $parsed[] = array('expr_type' => ExpressionType::RECORD, 'base_expr' => $base_expr,
                                      'data' => $this->processRecord($base_expr), 'delim' => false);
                    $base_expr = '';

                    $currCategory = 'DUPLICATE';
                    $parsed[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                }
                // else ?
                break;

            case 'DUPLICATE':
            case 'KEY':
            case 'UPDATE':
                if ($currCategory === 'DUPLICATE') {
                    $parsed[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    $base_expr = '';
                }
                // else ?
                break;

            case ',':
                if ($currCategory === 'DUPLICATE') {

                    $base_expr = trim(substr($base_expr, 0, -strlen($v)));
                    $res = $this->processExpressionList($this->splitSQLIntoTokens($base_expr));
                    $parsed[] = array('expr_type' => ExpressionType::EXPRESSION, 'base_expr' => $base_expr,
                                      'sub_tree' => (empty($res) ? false : $res), 'delim' => $trim);
                    $base_expr = '';
                    continue 2;
                }

                $parsed[] = array('expr_type' => ExpressionType::RECORD, 'base_expr' => trim($base_expr),
                                  'data' => $this->processRecord(trim($base_expr)), 'delim' => $trim);
                $base_expr = '';
                break;

            default:
                break;
            }

        }

        if (trim($base_expr) !== '') {
            if ($currCategory === '') {
                $parsed[] = array('expr_type' => ExpressionType::RECORD, 'base_expr' => trim($base_expr),
                                  'data' => $this->processRecord(trim($base_expr)), 'delim' => false);
            }
            if ($currCategory === 'DUPLICATE') {
                $res = $this->processExpressionList($this->splitSQLIntoTokens($base_expr));
                $parsed[] = array('expr_type' => ExpressionType::EXPRESSION, 'base_expr' => trim($base_expr),
                                  'sub_tree' => (empty($res) ? false : $res), 'delim' => false);
            }
        }

        $tokens['VALUES'] = $parsed;
        return $tokens;
    }

}
?>
