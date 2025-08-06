<?php
/**
 * SQLProcessor.php
 *
 * This file implements the processor for the base SQL statements.
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
 * This class processes the base SQL statements.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @author  Marco Th. <marco64th@gmail.com>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class SQLProcessor extends SQLChunkProcessor {

    /**
     * This function breaks up the SQL statement into logical sections. 
     * Some sections are delegated to specialized processors.
     */
    public function process($tokens) {
        $prev_category = "";
        $token_category = "";
        $skip_next = 0;
        $out = false;

	// $tokens may come as a numeric indexed array starting with an index greater than 0 (or as a boolean)
	$tokenCount = count($tokens);
        if ( is_array($tokens) ){
          $tokens = array_values($tokens);
        }
        for ($tokenNumber = 0; $tokenNumber < $tokenCount; ++$tokenNumber) {

            // https://github.com/greenlion/PHP-SQL-Parser/issues/279
            // https://github.com/sinri/PHP-SQL-Parser/commit/eac592a0e19f1df6f420af3777a6d5504837faa7
            // as there is no pull request for 279 by the user. His solution works and tested.
            if (!isset($tokens[$tokenNumber])) continue;// as a fix by Sinri 20180528
            $token = $tokens[$tokenNumber];
            $trim = trim($token); // this removes also \n and \t!

            // if it starts with an "(", it should follow a SELECT
            if ($trim !== "" && $trim[0] === "(" && $token_category === "") {
                $token_category = 'BRACKET';
                $prev_category = $token_category;
            }

            /*
             * If it isn't obvious, when $skip_next is set, then we ignore the next real token, that is we ignore whitespace.
             */
            if ($skip_next > 0) {
                if ($trim === "") {
                    if ($token_category !== "") { // is this correct??
                        $out[$token_category][] = $token;
                    }
                    continue;
                }
                // to skip the token we replace it with whitespace
                $trim = "";
                $token = "";
                $skip_next--;
                if ($skip_next > 0) {
                    continue;
                }
            }

            $upper = strtoupper($trim);
            switch ($upper) {

            /* Tokens that get their own sections. These keywords have subclauses. */
            case 'SELECT':
            case 'ORDER':
            case 'VALUES':
            case 'GROUP':
            case 'HAVING':
            case 'WHERE':
            case 'CALL':
            case 'PROCEDURE':
            case 'FUNCTION':
            case 'SERVER':
            case 'LOGFILE':
            case 'DEFINER':
            case 'RETURNS':
            case 'TABLESPACE':
            case 'TRIGGER':
            case 'DO':
            case 'FLUSH':
            case 'KILL':
            case 'RESET':
            case 'STOP':
            case 'PURGE':
            case 'EXECUTE':
            case 'PREPARE':
                $token_category = $upper;
                break;

            case 'DEALLOCATE':
                if ($trim === 'DEALLOCATE') {
                    $skip_next = 1;
                }
                $token_category = $upper;
                break;

            case 'DUPLICATE':
                if ($token_category !== 'VALUES') {
                    $token_category = $upper;
                }
                break;

            case 'SET':
                if ($token_category !== 'TABLE') {
                    $token_category = $upper;
                }
                break;

            case 'LIMIT':
            case 'PLUGIN':
            // no separate section
                if ($token_category === 'SHOW') {
                    break;
                }
                $token_category = $upper;
                break;

            case 'FROM':
            // this FROM is different from FROM in other DML (not join related)
                if ($token_category === 'PREPARE') {
                    continue 2;
                }
                // no separate section
                if ($token_category === 'SHOW') {
                    break;
                }
                $token_category = $upper;
                break;

            case 'EXPLAIN':
            case 'DESCRIBE':
            case 'SHOW':
                $token_category = $upper;
                break;

            case 'DESC':
                if ($token_category === '') {
                    // short version of DESCRIBE
                    $token_category = $upper;
                }
                // else direction of ORDER-BY
                break;

            case 'RENAME':
                $token_category = $upper;
                break;

            case 'DATABASE':
            case 'SCHEMA':
                if ($prev_category === 'DROP') {
                    break;
                }
                if ($prev_category === 'SHOW') {
                    break;
                }
                $token_category = $upper;
                break;

            case 'EVENT':
            // issue 71
                if ($prev_category === 'DROP' || $prev_category === 'ALTER' || $prev_category === 'CREATE') {
                    $token_category = $upper;
                }
                break;

            case 'DATA':
            // prevent wrong handling of DATA as keyword
                if ($prev_category === 'LOAD') {
                    $token_category = $upper;
                }
                break;

            case 'INTO':
            // prevent wrong handling of CACHE within LOAD INDEX INTO CACHE...
                if ($prev_category === 'LOAD') {
                    $out[$prev_category][] = $trim;
                    continue 2;
                }
                $token_category = $prev_category = $upper;
                break;

            case 'USER':
            // prevent wrong processing as keyword
                if ($prev_category === 'CREATE' || $prev_category === 'RENAME' || $prev_category === 'DROP') {
                    $token_category = $upper;
                }
                break;

            case 'VIEW':
            // prevent wrong processing as keyword
                if ($prev_category === 'CREATE' || $prev_category === 'ALTER' || $prev_category === 'DROP') {
                    $token_category = $upper;
                }
                break;

            /*
             * These tokens get their own section, but have no subclauses. These tokens identify the statement but have no specific subclauses of their own.
             */
            case 'DELETE':
            case 'ALTER':
            case 'INSERT':
            case 'TRUNCATE':
            case 'OPTIMIZE':
            case 'GRANT':
            case 'REVOKE':
            case 'HANDLER':
            case 'LOAD':
            case 'ROLLBACK':
            case 'SAVEPOINT':
            case 'UNLOCK':
            case 'INSTALL':
            case 'UNINSTALL':
            case 'ANALZYE':
            case 'BACKUP':
            case 'CHECKSUM':
            case 'REPAIR':
            case 'RESTORE':
            case 'HELP':
                $token_category = $upper;
                // set the category in case these get subclauses in a future version of MySQL
                $out[$upper][0] = $trim;
                continue 2;

            case 'REPLACE':
            	if ($prev_category === '') {
            		// set the category in case these get subclauses in a future version of MySQL
            		$token_category = $upper;
            		$out[$upper][0] = $trim;
            		continue 2;
            	}
                // part of the CREATE TABLE statement or a function
                $out[$prev_category][] = $trim;
                continue 2;

            case 'IGNORE':
                if ($prev_category === 'TABLE') {
                    // part of the CREATE TABLE statement
                    $out[$prev_category][] = $trim;
                    continue 2;
                }
                if ($token_category === 'FROM') {
                    // part of the FROM statement (index hint)
                    $out[$token_category][] = $trim;
                    continue 2;
                }
                $out['OPTIONS'][] = $upper;
                continue 2;

            case 'CHECK':
                if ($prev_category === 'TABLE') {
                    $out[$prev_category][] = $trim;
                    continue 2;
                }
                $token_category = $upper;
                $out[$upper][0] = $trim;
                continue 2;

            case 'CREATE':
                if ($prev_category === 'SHOW') {
                    break;
                }
                $token_category = $upper;
                break;

            case 'INDEX':
	            if ( in_array( $prev_category, array( 'CREATE', 'DROP' ) ) ) {
		            $out[ $prev_category ][] = $trim;
		            $token_category          = $upper;
	            }
	            break;

            case 'TABLE':
                if ($prev_category === 'CREATE') {
                    $out[$prev_category][] = $trim;
                    $token_category = $upper;
                }
                if ($prev_category === 'TRUNCATE') {
                    $out[$prev_category][] = $trim;
                    $token_category = $upper;
                }
                break;

            case 'TEMPORARY':
                if ($prev_category === 'CREATE') {
                    $out[$prev_category][] = $trim;
                    $token_category = $prev_category;
                    continue 2;
                }
                break;

            case 'IF':
                if ($prev_category === 'TABLE') {
                    $token_category = 'CREATE';
                    $out[$token_category] = array_merge($out[$token_category], $out[$prev_category]);
                    $out[$prev_category] = array();
                    $out[$token_category][] = $trim;
                    $prev_category = $token_category;
                    continue 2;
                }
                break;

            case 'NOT':
                if ($prev_category === 'CREATE') {
                    $token_category = $prev_category;
                    $out[$prev_category][] = $trim;
                    continue 2;
                }
                break;

            case 'EXISTS':
                if ($prev_category === 'CREATE') {
                    $out[$prev_category][] = $trim;
                    $prev_category = $token_category = 'TABLE';
                    continue 2;
                }
                break;

            case 'CACHE':
                if ($prev_category === "" || $prev_category === 'RESET' || $prev_category === 'FLUSH'
                    || $prev_category === 'LOAD') {
                    $token_category = $upper;
                    continue 2;
                }
                break;

            /* This is either LOCK TABLES or SELECT ... LOCK IN SHARE MODE */
            case 'LOCK':
                if ($token_category === "") {
                    $token_category = $upper;
                    $out[$upper][0] = $trim;
                } elseif ($token_category === 'INDEX') {
                    break;
                } else {
                    $trim = 'LOCK IN SHARE MODE';
                    $skip_next = 3;
                    $out['OPTIONS'][] = $trim;
                }
                continue 2;

            case 'USING': /* USING in FROM clause is different from USING w/ prepared statement*/
                if ($token_category === 'EXECUTE') {
                    $token_category = $upper;
                    continue 2;
                }
                if ($token_category === 'FROM' && !empty($out['DELETE'])) {
                    $token_category = $upper;
                    continue 2;
                }
                break;

            /* DROP TABLE is different from ALTER TABLE DROP ... */
            case 'DROP':
                if ($token_category !== 'ALTER') {
                    $token_category = $upper;
                    continue 2;
                }
                break;

            case 'FOR':
                if ($prev_category === 'SHOW') {
                    break;
                }
                $skip_next = 1;
                $out['OPTIONS'][] = 'FOR UPDATE'; // TODO: this could be generate problems within the position calculator
                continue 2;

            case 'UPDATE':
                if ($token_category === "") {
                    $token_category = $upper;
                    continue 2;
                }
                if ($token_category === 'DUPLICATE') {
                    continue 2;
                }
                break;

            case 'START':
                $trim = "BEGIN";
                $out[$upper][0] = $upper; // TODO: this could be generate problems within the position calculator
                $skip_next = 1;
                break;

            // This token is ignored, except within RENAME
            case 'TO':
                if ($token_category === 'RENAME') {
                    break;
                }
                continue 2;

            // This token is ignored, except within CREATE TABLE
            case 'BY':
                if ($prev_category === 'TABLE') {
                    break;
                }
                continue 2;

            // These tokens are ignored.
            case 'ALL':
            case 'SHARE':
            case 'MODE':
            case ';':
                continue 2;

            case 'KEY':
                if ($token_category === 'DUPLICATE') {
                    continue 2;
                }
                break;

            /* These tokens set particular options for the statement. */
            case 'LOW_PRIORITY':
            case 'DELAYED':
            case 'QUICK':
            case 'HIGH_PRIORITY':
                $out['OPTIONS'][] = $trim;
                continue 2;

            case 'USE':
                if ($token_category === 'FROM') {
                    // index hint within FROM clause
                    $out[$token_category][] = $trim;
                    continue 2;
                }
                // set the category in case these get subclauses in a future version of MySQL
                $token_category = $upper;
                $out[$upper][0] = $trim;
                continue 2;

            case 'FORCE':
                if ($token_category === 'FROM') {
                    // index hint within FROM clause
                    $out[$token_category][] = $trim;
                    continue 2;
                }
                $out['OPTIONS'][] = $trim;
                continue 2;

            case 'WITH':
                if ($token_category === 'GROUP') {
                    $skip_next = 1;
                    $out['OPTIONS'][] = 'WITH ROLLUP'; // TODO: this could be generate problems within the position calculator
                    continue 2;
                }
                if ($token_category === '') {
                	$token_category = $upper;
                }
                break;

            case 'AS':
                break;

            case '':
            case ',':
                break;

            default:
                break;
            }

            // remove obsolete category after union (empty category because of
            // empty token before select)
            if ($token_category !== "" && ($prev_category === $token_category)) {
                $out[$token_category][] = $token;
            }

            $prev_category = $token_category;
        }

        return parent::process($out);
    }
}
?>
