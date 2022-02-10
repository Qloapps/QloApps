<?php
/**
 * ExplainProcessor.php
 *
 * This file implements the processor for the EXPLAIN statements.
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
 * This class processes the EXPLAIN statements.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class ExplainProcessor extends AbstractProcessor {

    protected function isStatement($keys, $needle = "EXPLAIN") {
        $pos = array_search($needle, $keys);
        if (isset($keys[$pos + 1])) {
            return in_array($keys[$pos + 1], array('SELECT', 'DELETE', 'INSERT', 'REPLACE', 'UPDATE'), true);
        }
        return false;
    }

    // TODO: refactor that function
    public function process($tokens, $keys = array()) {

        $base_expr = "";
        $expr = array();
        $currCategory = "";

        if ($this->isStatement($keys)) {
            foreach ($tokens as $token) {

                $trim = trim($token);
                $base_expr .= $token;

                if ($trim === '') {
                    continue;
                }

                $upper = strtoupper($trim);

                switch ($upper) {

                case 'EXTENDED':
                case 'PARTITIONS':
                    return array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $token);
                    break;

                case 'FORMAT':
                    if ($currCategory === '') {
                        $currCategory = $upper;
                        $expr[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                    }
                    // else?
                    break;

                case '=':
                    if ($currCategory === 'FORMAT') {
                        $expr[] = array('expr_type' => ExpressionType::OPERATOR, 'base_expr' => $trim);
                    }
                    // else?
                    break;

                case 'TRADITIONAL':
                case 'JSON':
                    if ($currCategory === 'FORMAT') {
                        $expr[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
                        return array('expr_type' => ExpressionType::EXPRESSION, 'base_expr' => trim($base_expr),
                                     'sub_tree' => $expr);
                    }
                    // else?
                    break;

                default:
                // ignore the other stuff
                    break;
                }
            }
            return empty($expr) ? null : $expr;
        }

        foreach ($tokens as $token) {

            $trim = trim($token);

            if ($trim === '') {
                continue;
            }

            switch ($currCategory) {

            case 'TABLENAME':
                $currCategory = 'WILD';
                $expr[] = array('expr_type' => ExpressionType::COLREF, 'base_expr' => $trim,
                                'no_quotes' => $this->revokeQuotation($trim));
                break;

            case '':
                $currCategory = 'TABLENAME';
                $expr[] = array('expr_type' => ExpressionType::TABLE, 'table' => $trim,
                                'no_quotes' => $this->revokeQuotation($trim), 'alias' => false, 'base_expr' => $trim);
                break;

            default:
                break;
            }
        }
        return empty($expr) ? null : $expr;
    }
}

?>
