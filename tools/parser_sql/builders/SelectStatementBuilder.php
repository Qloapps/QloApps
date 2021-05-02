<?php
/**
 * SelectStatement.php
 *
 * Builds the SELECT statement
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

namespace PHPSQLParser\builders;

/**
 * This class implements the builder for the whole Select statement. You can overwrite
 * all functions to achieve another handling.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class SelectStatementBuilder implements Builder {

    protected function buildSELECT($parsed) {
        $builder = new SelectBuilder();
        return $builder->build($parsed);
    }

    protected function buildFROM($parsed) {
        $builder = new FromBuilder();
        return $builder->build($parsed);
    }

    protected function buildWHERE($parsed) {
        $builder = new WhereBuilder();
        return $builder->build($parsed);
    }

    protected function buildGROUP($parsed) {
        $builder = new GroupByBuilder();
        return $builder->build($parsed);
    }

    protected function buildHAVING($parsed) {
        $builder = new HavingBuilder();
        return $builder->build($parsed);
    }

    protected function buildORDER($parsed) {
        $builder = new OrderByBuilder();
        return $builder->build($parsed);
    }

    protected function buildLIMIT($parsed) {
        $builder = new LimitBuilder();
        return $builder->build($parsed);
    }
    
    protected function buildUNION($parsed) {
    	$builder = new UnionStatementBuilder();
    	return $builder->build($parsed);
    }
    
    protected function buildUNIONALL($parsed) {
    	$builder = new UnionAllStatementBuilder();
    	return $builder->build($parsed);
    }

    public function build(array $parsed) {
        $sql = "";
        if (isset($parsed['SELECT'])) {
            $sql .= $this->buildSELECT($parsed['SELECT']);
        }
        if (isset($parsed['FROM'])) {
            $sql .= " " . $this->buildFROM($parsed['FROM']);
        }
        if (isset($parsed['WHERE'])) {
            $sql .= " " . $this->buildWHERE($parsed['WHERE']);
        }
        if (isset($parsed['GROUP'])) {
            $sql .= " " . $this->buildGROUP($parsed['GROUP']);
        }
        if (isset($parsed['HAVING'])) {
            $sql .= " " . $this->buildHAVING($parsed['HAVING']);
        }
        if (isset($parsed['ORDER'])) {
            $sql .= " " . $this->buildORDER($parsed['ORDER']);
        }
        if (isset($parsed['LIMIT'])) {
            $sql .= " " . $this->buildLIMIT($parsed['LIMIT']);
        }       
        if (isset($parsed['UNION'])) {
            $sql .= " " . $this->buildUNION($parsed);
        }
        if (isset($parsed['UNION ALL'])) {
        	$sql .= " " . $this->buildUNIONALL($parsed);
        }
        return $sql;
    }

}
?>
