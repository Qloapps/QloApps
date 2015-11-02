<?php
/**
 * CreateTableOptionsBuilder.php
 *
 * Builds the table-options statement part of CREATE TABLE.
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
 * @version   SVN: $Id: CreateTableOptionsBuilder.php 923 2014-01-08 12:20:30Z phosco@gmx.de $
 * 
 */

require_once dirname(__FILE__) . '/../utils/ExpressionType.php';
require_once dirname(__FILE__) . '/SelectExpressionBuilder.php';
require_once dirname(__FILE__) . '/CharacterSetBuilder.php';
require_once dirname(__FILE__) . '/CollationBuilder.php';

/**
 * This class implements the builder for the table-options statement part of CREATE TABLE. 
 * You can overwrite all functions to achieve another handling.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class CreateTableOptionsBuilder {

    protected function buildExpression($parsed) {
        $builder = new SelectExpressionBuilder();
        return $builder->build($parsed);
    }
	
    protected function buildCharacterSet($parsed) {
        $builder = new CharacterSetBuilder();
        return $builder->build($parsed);
    }
    
    protected function buildCollation($parsed) {
        $builder = new CollationBuilder();
        return $builder->build($parsed);
    }
    
    /**
     * Returns a well-formatted delimiter string. If you don't need nice SQL,
     * you could simply return $parsed['delim'].
     * 
     * @param array $parsed The part of the output array, which contains the current expression.
     * @return a string, which is added right after the expression
     */
    protected function getDelimiter($parsed) {
        return ($parsed['delim'] === false ? '' : (trim($parsed['delim']) . ' '));
    }
     
    public function build($parsed) {
        if (!isset($parsed['options']) || $parsed['options'] === false) {
            return "";
        }
        $options = $parsed['options'];
        $sql = "";
        foreach ($options as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->buildExpression($v);
            $sql .= $this->buildCharacterSet($v);
            $sql .= $this->buildCollation($v);

            if ($len == strlen($sql)) {
                throw new UnableToCreateSQLException('CREATE TABLE options', $k, $v, 'expr_type');
            }

            $sql .= $this->getDelimiter($v);
        }
        return " " . substr($sql, 0, -1);
    }
}
?>
