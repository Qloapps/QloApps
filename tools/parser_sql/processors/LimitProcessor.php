<?php
/**
 * LimitProcessor.php
 *
 * This file implements the processor for the LIMIT statements.
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

/**
 * 
 * This class processes the LIMIT statements.
 * 
 * @author arothe
 * 
 */
class LimitProcessor extends AbstractProcessor {

    public function process($tokens) {
        $rowcount = "";
        $offset = "";

        $comma = -1;
        $exchange = false;

        for ($i = 0; $i < count($tokens); ++$i) {
            $trim = trim($tokens[$i]);
            if ($trim === ",") {
                $comma = $i;
                break;
            }
            if ($trim === "OFFSET") {
                $comma = $i;
                $exchange = true;
                break;
            }
        }

        for ($i = 0; $i < $comma; ++$i) {
            if ($exchange) {
                $rowcount .= $tokens[$i];
            } else {
                $offset .= $tokens[$i];
            }
        }

        for ($i = $comma + 1; $i < count($tokens); ++$i) {
            if ($exchange) {
                $offset .= $tokens[$i];
            } else {
                $rowcount .= $tokens[$i];
            }
        }

        return array('offset' => trim($offset), 'rowcount' => trim($rowcount));
    }
}
?>