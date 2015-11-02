<?php
/**
 * RenameProcessor.php
 *
 * This file implements the processor for the RENAME statements.
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
require_once(dirname(__FILE__) . '/../utils/ExpressionToken.php');
require_once(dirname(__FILE__) . '/../utils/ExpressionType.php');

/**
 * 
 * This class processes the RENAME statements.
 * 
 * @author arothe
 * 
 */
class RenameProcessor extends AbstractProcessor {

    public function process($tokenList) {
        $base_expr = "";
        $resultList = array();
        $tablePair = array();

        foreach ($tokenList as $k => $v) {
            $token = new ExpressionToken($k, $v);

            if ($token->isWhitespaceToken()) {
                continue;
            }

            switch ($token->getUpper()) {
            case 'TO':
            // separate source table from destination
                $tablePair['source'] = array('expr_type' => ExpressionType::TABLE, 'table' => trim($base_expr),
                                             'no_quotes' => $this->revokeQuotation($base_expr),
                                             'base_expr' => $base_expr);
                $base_expr = "";
                break;

            case ',':
            // split rename operations
                $tablePair['destination'] = array('expr_type' => ExpressionType::TABLE, 'table' => trim($base_expr),
                                                  'no_quotes' => $this->revokeQuotation($base_expr),
                                                  'base_expr' => $base_expr);
                $resultList[] = $tablePair;
                $tablePair = array();
                $base_expr = "";
                break;

            default:
                $base_expr .= $token->getToken();
                break;
            }
        }

        if ($base_expr !== "") {
            $tablePair['destination'] = array('expr_type' => ExpressionType::TABLE, 'table' => trim($base_expr),
                                              'no_quotes' => $this->revokeQuotation($base_expr),
                                              'base_expr' => $base_expr);
            $resultList[] = $tablePair;
        }

        return $resultList;
    }

}
?>