<?php
/**
 * UnionProcessor.php
 *
 * This file implements the processor for the UNION statements.
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
require_once(dirname(__FILE__) . '/SQLProcessor.php');
require_once(dirname(__FILE__) . '/DefaultProcessor.php');
require_once(dirname(__FILE__) . '/../utils/ExpressionType.php');

/**
 * 
 * This class processes the UNION statements.
 * 
 * @author arothe
 * 
 */
class UnionProcessor extends AbstractProcessor {

    public function isUnion($queries) {
        $unionTypes = array('UNION', 'UNION ALL');
        foreach ($unionTypes as $unionType) {
            if (!empty($queries[$unionType])) {
                return true;
            }
        }
        return false;
    }

    /**
     * MySQL supports a special form of UNION:
     * (select ...)
     * union
     * (select ...)
     *
     * This function handles this query syntax. Only one such subquery
     * is supported in each UNION block. (select)(select)union(select) is not legal.
     * The extra queries will be silently ignored.
     */
    protected function processMySQLUnion($queries) {
        $unionTypes = array('UNION', 'UNION ALL');
        foreach ($unionTypes as $unionType) {

            if (empty($queries[$unionType])) {
                continue;
            }

            foreach ($queries[$unionType] as $key => $tokenList) {
                foreach ($tokenList as $z => $token) {
                    $token = trim($token);
                    if ($token === "") {
                        continue;
                    }

                    // starts with "(select"
                    if (preg_match("/^\\(\\s*select\\s*/i", $token)) {
                        $processor = new DefaultProcessor();
                        $queries[$unionType][$key] = $processor->process($this->removeParenthesisFromStart($token));
                        break;
                    }

                    $processor = new SQLProcessor();
                    $queries[$unionType][$key] = $processor->process($queries[$unionType][$key]);
                    break;
                }
            }
        }
        // it can be parsed or not
        return $queries;
    }

    public function process($inputArray) {
        $outputArray = array();

        // ometimes the parser needs to skip ahead until a particular
        // oken is found
        $skipUntilToken = false;

        // his is the last type of union used (UNION or UNION ALL)
        // ndicates a) presence of at least one union in this query
        // b) the type of union if this is the first or last query
        $unionType = false;

        // ometimes a "query" consists of more than one query (like a UNION query)
        // his array holds all the queries
        $queries = array();

        foreach ($inputArray as $key => $token) {
            $trim = trim($token);

            // overread all tokens till that given token
            if ($skipUntilToken) {
                if ($trim === "") {
                    continue; // read the next token
                }
                if (strtoupper($trim) === $skipUntilToken) {
                    $skipUntilToken = false;
                    continue; // read the next token
                }
            }

            if (strtoupper($trim) !== "UNION") {
                $outputArray[] = $token; // here we get empty tokens, if we remove these, we get problems in parse_sql()
                continue;
            }

            $unionType = "UNION";

            // we are looking for an ALL token right after UNION
            for ($i = $key + 1; $i < count($inputArray); ++$i) {
                if (trim($inputArray[$i]) === "") {
                    continue;
                }
                if (strtoupper($inputArray[$i]) !== "ALL") {
                    break;
                }
                // the other for-loop should overread till "ALL"
                $skipUntilToken = "ALL";
                $unionType = "UNION ALL";
            }

            // store the tokens related to the unionType
            $queries[$unionType][] = $outputArray;
            $outputArray = array();
        }

        // the query tokens after the last UNION or UNION ALL
        // or we don't have an UNION/UNION ALL
        if (!empty($outputArray)) {
            if ($unionType) {
                $queries[$unionType][] = $outputArray;
            } else {
                $queries[] = $outputArray;
            }
        }

        return $this->processMySQLUnion($queries);
    }

}
?>