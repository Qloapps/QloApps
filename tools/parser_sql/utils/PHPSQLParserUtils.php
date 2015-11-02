<?php
/**
 * PHPSQLParserUtils.php
 *
 * These are utility functions for the PHPSQLParser.
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

/**
 * This class implements some helper functions.
 * 
 * @author arothe
 * @deprecated
 */
class PHPSQLParserUtils {

    /**
     * Prints an array only if debug mode is on.
     * 
     * @param array $s
     * @param boolean $return, if true, the formatted array is returned via return parameter
     */
    protected function preprint($arr, $return = false) {
        $x = "<pre>";
        $x .= print_r($arr, 1);
        $x .= "</pre>";
        if ($return) {
            return $x;
        } else {
            if (isset($_ENV['DEBUG'])) {
                print $x . "\n";
            }
        }
    }

    /**
     * Ends the given string $haystack with the string $needle?
     * 
     * @param string $haystack
     * @param string $needle
     */
    protected function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    /**
     * Revokes the quoting characters from an expression
     */
    protected function revokeQuotation($sql) {
        $result = trim($sql);
        if (($result[0] === '`') && ($result[strlen($result) - 1] === '`')) {
            $result = substr($result, 1, -1);
            return trim(str_replace('``', '`', $result));
        }
        return $sql;
    }

    /**
     * This method removes parenthesis from start of the given string.
     * It removes also the associated closing parenthesis.
     */
    protected function removeParenthesisFromStart($token) {

        $parenthesisRemoved = 0;

        $trim = trim($token);
        if ($trim !== "" && $trim[0] === "(") { // remove only one parenthesis pair now!
            $parenthesisRemoved++;
            $trim[0] = " ";
            $trim = trim($trim);
        }

        $parenthesis = $parenthesisRemoved;
        $i = 0;
        $string = 0;
        while ($i < strlen($trim)) {

            if ($trim[$i] === "\\") {
                $i += 2; # an escape character, the next character is irrelevant
                continue;
            }

            if ($trim[$i] === "'" || $trim[$i] === '"') {
                $string++;
            }

            if (($string % 2 === 0) && ($trim[$i] === "(")) {
                $parenthesis++;
            }

            if (($string % 2 === 0) && ($trim[$i] === ")")) {
                if ($parenthesis == $parenthesisRemoved) {
                    $trim[$i] = " ";
                    $parenthesisRemoved--;
                }
                $parenthesis--;
            }
            $i++;
        }
        return trim($trim);
    }

    public function getLastOf($array) {
        // $array is a copy of the original array, so we can change it without sideeffects
        if (!is_array($array)) {
            return false;
        }
        return array_pop($array);
    }

    /**
     * translates an array of objects into an associative array
     */
    public function toArray($tokenList) {
        $expr = array();
        foreach ($tokenList as $token) {
            $expr[] = $token->toArray();
        }
        return (empty($expr) ? false : $expr);
    }
}
?>
