<?php
/**
 * DefaultProcessor.php
 *
 * This file implements the processor the unparsed sql string given by the user.
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
require_once(dirname(__FILE__) . '/UnionProcessor.php');
require_once(dirname(__FILE__) . '/SQLProcessor.php');

/**
 * 
 * This class processes the incoming sql string.
 * 
 * @author arothe
 * 
 */
class DefaultProcessor extends AbstractProcessor {

    public function process($sql) {

        $inputArray = $this->splitSQLIntoTokens($sql);

        // this is the highest level lexical analysis. This is the part of the
        // code which finds UNION and UNION ALL query parts
        $processor = new UnionProcessor();
        $queries = $processor->process($inputArray);

        // If there was no UNION or UNION ALL in the query, then the query is
        // stored at $queries[0].
        if (!$processor->isUnion($queries)) {
            $processor = new SQLProcessor();
            $queries = $processor->process($queries[0]);
        }

        return $queries;
    }
}
?>