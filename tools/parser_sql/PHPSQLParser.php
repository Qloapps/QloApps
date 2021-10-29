<?php

/**
 * PHPSQLParser.php
 *
 * A pure PHP SQL (non validating) parser w/ focus on MySQL dialect of SQL
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
 */

namespace PHPSQLParser;
use PHPSQLParser\positions\PositionCalculator;
use PHPSQLParser\processors\DefaultProcessor;
use PHPSQLParser\utils\PHPSQLParserConstants;

/**
 * This class implements the parser functionality.
 *
 * @author  Justin Swanhart <greenlion@gmail.com>
 * @author  André Rothe <arothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 */
class PHPSQLParser {

    public $parsed;

    /**
     * @var Options
     */
    private $options;

    /**
     * Constructor. It simply calls the parse() function.
     * Use the public variable $parsed to get the output.
     *
     * @param String|bool  $sql           The SQL statement.
     * @param bool $calcPositions True, if the output should contain [position], false otherwise.
     * @param array $options
     */
    public function __construct($sql = false, $calcPositions = false, array $options = array()) {
        $this->options = new Options($options);

        if ($sql) {
            $this->parse($sql, $calcPositions);
        }
    }

    /**
     * It parses the given SQL statement and generates a detailled
     * output array for every part of the statement. The method can
     * also generate [position] fields within the output, which hold
     * the character position for every statement part. The calculation
     * of the positions needs some time, if you don't need positions in
     * your application, set the parameter to false.
     *
     * @param String  $sql           The SQL statement.
     * @param boolean $calcPositions True, if the output should contain [position], false otherwise.
     *
     * @return array An associative array with all meta information about the SQL statement.
     */
    public function parse($sql, $calcPositions = false) {

        $processor = new DefaultProcessor($this->options);
        $queries = $processor->process($sql);

        // calc the positions of some important tokens
        if ($calcPositions) {
            $calculator = new PositionCalculator();
            $queries = $calculator->setPositionsWithinSQL($sql, $queries);
        }

        // store the parsed queries
        $this->parsed = $queries;
        return $this->parsed;
    }

    /**
     * Add a custom function to the parser.  no return value
     *
     * @param String $token The name of the function to add
     *
     * @return null
     */
    public function addCustomFunction($token) {
        PHPSQLParserConstants::getInstance()->addCustomFunction($token);
    }

    /**
     * Remove a custom function from the parser.  no return value
     *
     * @param String $token The name of the function to remove
     *
     * @return null
     */
    public function removeCustomFunction($token) {
        PHPSQLParserConstants::getInstance()->removeCustomFunction($token);
    }

    /**
     * Returns the list of custom functions
     *
     * @return array Returns an array of all custom functions
     */
    public function getCustomFunctions() {
        return PHPSQLParserConstants::getInstance()->getCustomFunctions();
    }
}
?>
