<?php
/**
 * DefaultProcessor.php
 *
 * This file implements the processor the unparsed sql string given by the user.
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
 * This class processes the incoming sql string.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class DefaultProcessor extends AbstractProcessor {

    protected function isUnion($tokens) {
        return UnionProcessor::isUnion($tokens);
    }

    protected function processUnion($tokens) {
        // this is the highest level lexical analysis. This is the part of the
        // code which finds UNION and UNION ALL query parts
        $processor = new UnionProcessor($this->options);
        return $processor->process($tokens);
    }

    protected function processSQL($tokens) {
        $processor = new SQLProcessor($this->options);
        return $processor->process($tokens);
    }

    public function process($sql) {

        $inputArray = $this->splitSQLIntoTokens($sql);
        $queries = $this->processUnion($inputArray);

        // If there was no UNION or UNION ALL in the query, then the query is
        // stored at $queries[0].
        if (!empty($queries) && !$this->isUnion($queries)) {
            $queries = $this->processSQL($queries[0]);
        }

        return $queries;
    }

    public function revokeQuotation($sql) {
        return parent::revokeQuotation($sql);
    }
}

?>
