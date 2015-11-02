<?php
/**
 * LexerSplitter.php
 *
 * Defines the characters, which are used to split the given SQL string.
 * Part of PHPSQLParser.
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
 * @version   SVN: $Id: LexerSplitter.php 842 2013-12-30 08:57:53Z phosco@gmx.de $
 * 
 */

/**
 * This class holds a sorted array of characters, which are used as stop token.
 * On every part of the array the given SQL string will be split into single tokens.
 * The array must be sorted by element size, longest first (3 chars -> 2 chars -> 1 char).
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class LexerSplitter {

    protected static $splitters = array("<=>", "\r\n", "!=", ">=", "<=", "<>", "<<", ">>", ":=", "\\", "&&", "||", ":=",
                                       "/*", "*/", "--", ">", "<", "|", "=", "^", "(", ")", "\t", "\n", "'", "\"", "`",
                                       ",", "@", " ", "+", "-", "*", "/", ";");
    protected $tokenSize;
    protected $hashSet;

    /**
     * Constructor.
     * 
     * It initializes some fields.
     */
    public function __construct() {
        $this->tokenSize = strlen(self::$splitters[0]); // should be the largest one
        $this->hashSet = array_flip(self::$splitters);
    }

    /**
     * Get the maximum length of a split token.
     * 
     * The largest element must be on position 0 of the internal $_splitters array,
     * so the function returns the length of that token. It must be > 0.
     * 
     * @return int The number of characters for the largest split token.
     */
    public function getMaxLengthOfSplitter() {
        return $this->tokenSize;
    }

    /**
     * Looks into the internal split token array and compares the given token with
     * the array content. It returns true, if the token will be found, false otherwise. 
     *  
     * @param String $token a string, which could be a split token. 
     * 
     * @return boolean true, if the given string will be a split token, false otherwise
     */
    public function isSplitter($token) {
        return isset($this->hashSet[$token]);
    }
}

?>
