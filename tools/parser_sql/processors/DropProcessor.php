<?php
/**
 * DropProcessor.php
 *
 * This file implements the processor for the DROP statements.
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

require_once(dirname(__FILE__) . '/../utils/ExpressionToken.php');
require_once(dirname(__FILE__) . '/../utils/ExpressionType.php');
require_once(dirname(__FILE__) . '/AbstractProcessor.php');

/**
 * 
 * This class processes the DROP statements.
 * 
 * @author arothe
 * 
 */
class DropProcessor extends AbstractProcessor {

    // TODO: we should enhance it to get the positions for the IF EXISTS keywords
    // look into the CreateProcessor to get an idea.
    public function process($tokenList) {
        $skip = 0;
        $warning = true;
        $base_expr = "";
        $expr_type = false;
        $option = false;
        $resultList = array();

        foreach ($tokenList as $k => $v) {
            $token = new ExpressionToken($k, $v);

            if ($token->isWhitespaceToken()) {
                continue;
            }

            if ($skip > 0) {
                $skip --;
                continue;
            }

            switch ($token->getUpper()) {
            case 'VIEW':
            case 'SCHEMA':
            case 'DATABASE':
            case 'TABLE':
                $expr_type = strtolower($token->getTrim());
                break;

            case 'IF':
                $warning = false;
                $skip = 1;
                break;

            case 'TEMPORARY':
                $expr_type = ExpressionType::TEMPORARY_TABLE;
                $skip = 1;
                break;

            case 'RESTRICT':
            case 'CASCADE':
                $option = $token->getUpper();
                break;

            case ',':
                $resultList[] = array('expr_type' => $expr_type, 'base_expr' => $base_expr);
                $base_expr = "";
                break;

            default:
                $base_expr .= $token->getToken();
            }
        }

        if ($base_expr !== "") {
            $resultList[] = array('expr_type' => $expr_type, 'base_expr' => $base_expr);
        }

        return array('option' => $option, 'warning' => $warning, 'object_list' => $resultList);
    }
}
?>