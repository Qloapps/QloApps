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
 * @version   SVN: $Id$
 * 
 */

namespace PHPSQLParser\lexer;

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

	/**
	 * @var string Regex string pattern of splitters.
	 */
    protected $splitterPattern;

    /**
     * Constructor.
     * 
     * It initializes some fields.
     */
    public function __construct() {
        $this->splitterPattern = $this->convertSplittersToRegexPattern( self::$splitters );
    }

	/**
	 * Get the regex pattern string of all the splitters
	 *
	 * @return string
	 */
    public function getSplittersRegexPattern () {
	    return $this->splitterPattern;
    }

	/**
	 * Convert an array of splitter tokens to a regex pattern string.
	 *
	 * @param array $splitters
	 *
	 * @return string
	 */
    public function convertSplittersToRegexPattern( $splitters ) {
	    $regex_parts = array();
	    foreach ( $splitters as $part ) {
		    $part = preg_quote( $part );

		    switch ( $part ) {
			    case "\r\n":
				    $part = '\r\n';
				    break;
			    case "\t":
				    $part = '\t';
				    break;
			    case "\n":
				    $part = '\n';
				    break;
			    case " ":
				    $part = '\s';
				    break;
			    case "/":
				    $part = "\/";
				    break;
			    case "/\*":
				    $part = "\/\*";
				    break;
			    case "\*/":
				    $part = "\*\/";
				    break;
		    }

		    $regex_parts[] = $part;
	    }

	    $pattern = implode( '|', $regex_parts );

	    return '/(' . $pattern . ')/';
    }
}

?>
