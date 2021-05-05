<?php
/**
 * LimitProcessor.php
 *
 * This file implements the processor for the LIMIT statements.
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
 * This class processes the LIMIT statements.
 * 
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * 
 */
class LimitProcessor extends AbstractProcessor {

    public function process($tokens) {
        $rowcount = "";
        $offset = "";

        $comma = -1;
        $exchange = false;
        
        $comments = array();
        
        foreach ($tokens as &$token) {
            if ($this->isCommentToken($token)) {
                 $comments[] = parent::processComment($token);
                 $token = '';
            }
        }
        
        for ($i = 0; $i < count($tokens); ++$i) {
            $trim = strtoupper(trim($tokens[$i]));
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

        $return = array('offset' => trim($offset), 'rowcount' => trim($rowcount));
        if (count($comments)) {
            $return['comments'] = $comments;
        }
        return $return;
    }
}
?>
