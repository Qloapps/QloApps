<?php
/**
 * SetProcessor.php
 *
 * This file implements the processor for the SET statements.
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
require_once(dirname(__FILE__) . '/ExpressionListProcessor.php');
require_once(dirname(__FILE__) . '/../utils/ExpressionType.php');

/**
 * 
 * This class processes the SET statements.
 * 
 * @author arothe
 * 
 */
class SetProcessor extends AbstractProcessor {

    private $expressionListProcessor;

    public function __construct() {
        $this->expressionListProcessor = new ExpressionListProcessor();
    }

    /**
     * A SET list is simply a list of key = value expressions separated by comma (,).
     * This function produces a list of the key/value expressions.
     */
    protected function getAssignment($base_expr) {
        $assignment = $this->expressionListProcessor->process($this->splitSQLIntoTokens($base_expr));
        return array('expr_type' => ExpressionType::EXPRESSION, 'base_expr' => trim($base_expr),
                     'sub_tree' => $assignment);
    }

    public function process($tokens, $isUpdate = false) {
        $result = array();
        $baseExpr = "";
        $assignment = false;
        $varType = false;

        foreach ($tokens as $token) {
            $upper = strtoupper(trim($token));

            switch ($upper) {
            case 'LOCAL':
            case 'SESSION':
            case 'GLOBAL':
                if (!$isUpdate) {
                    $varType = $this->getVariableType("@@" . $upper . ".");
                    $baseExpr = "";
                    continue 2;
                }
                break;

            case ',':
                $assignment = $this->getAssignment($baseExpr);
                if (!$isUpdate && $varType !== false) {
                    $assignment['sub_tree'][0]['expr_type'] = $varType;
                }
                $result[] = $assignment;
                $baseExpr = "";
                $varType = false;
                continue 2;

            default:
            }
            $baseExpr .= $token;
        }

        if (trim($baseExpr) !== "") {
            $assignment = $this->getAssignment($baseExpr);
            if (!$isUpdate && $varType !== false) {
                $assignment['sub_tree'][0]['expr_type'] = $varType;
            }
            $result[] = $assignment;
        }

        return $result;
    }

}
?>