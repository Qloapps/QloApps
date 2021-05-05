<?php
/**
 * WhereProcessor.php
 *
 * This file implements the processor for the UNION statements.
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

/**
 * This class processes the UNION statements.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class UnionProcessor extends AbstractProcessor {

    protected function processDefault($token) {
        $processor = new DefaultProcessor($this->options);
        return $processor->process($token);
    }

    protected function processSQL($token) {
        $processor = new SQLProcessor($this->options);
        return $processor->process($token);
    }

    public static function isUnion($queries) {
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
                        $queries[$unionType][$key] = $this->processDefault($this->removeParenthesisFromStart($token));
                        break;
                    }
                    $queries[$unionType][$key] = $this->processSQL($queries[$unionType][$key]);
                    break;
                }
            }
        }

        // it can be parsed or not
        return $queries;
    }

    /**
     * Moves the final union query into a separate output, so the remainder (such as ORDER BY) can
     * be processed separately.
     */
    protected function splitUnionRemainder($queries, $unionType, $outputArray)
    {
        $finalQuery = [];

        //If this token contains a matching pair of brackets at the start and end, use it as the final query
        $finalQueryFound = false;
        if (count($outputArray) === 1) {
            $tokenAsArray = str_split(trim($outputArray[0]));
            if ($tokenAsArray[0] == '(' && $tokenAsArray[count($tokenAsArray)-1] == ')') {
                $queries[$unionType][] = $outputArray;
                $finalQueryFound = true;
            }
        }

        if (!$finalQueryFound) {
            foreach ($outputArray as $key => $token) {
                if (strtoupper($token) == 'ORDER') {
                    break;
                } else {
                    $finalQuery[] = $token;
                    unset($outputArray[$key]);
                }
            }
        }


        $finalQueryString = trim(implode($finalQuery));

        if (!empty($finalQuery) && $finalQueryString != '') {
            $queries[$unionType][] = $finalQuery;
        }

        $defaultProcessor = new DefaultProcessor($this->options);
        $rePrepareSqlString = trim(implode($outputArray));

        if (!empty($rePrepareSqlString)) {
            $remainingQueries = $defaultProcessor->process($rePrepareSqlString);
            $queries[] = $remainingQueries;
        }

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
                $queries = $this->splitUnionRemainder($queries, $unionType, $outputArray);
            } else {
                $queries[] = $outputArray;
            }
        }

        return $this->processMySQLUnion($queries);
    }
}
?>
