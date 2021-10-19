<?php
/**
 * FunctionBuilder.php
 *
 * Builds function statements.
 *
 * PHP version 5
 *
 * LICENSE:
 * Copyright (c) 2010-2015 Justin Swanhart and André Rothe
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
 * @copyright 2010-2015 Justin Swanhart and André Rothe
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id$
 * 
 */

namespace PHPSQLParser\builders;
use PHPSQLParser\exceptions\UnableToCreateSQLException;
use PHPSQLParser\utils\ExpressionType;

/**
 * This class implements the builder for function calls. 
 * You can overwrite all functions to achieve another handling.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class FunctionBuilder implements Builder {

    protected function buildAlias($parsed) {
        $builder = new AliasBuilder();
        return $builder->build($parsed);
    }

    protected function buildColRef($parsed) {
        $builder = new ColumnReferenceBuilder();
        return $builder->build($parsed);
    }

    protected function buildConstant($parsed) {
        $builder = new ConstantBuilder();
        return $builder->build($parsed);
    }

    protected function buildReserved($parsed) {
        $builder = new ReservedBuilder();
        return $builder->build($parsed);
    }

    protected function isReserved($parsed) {
        $builder = new ReservedBuilder();
        return $builder->isReserved($parsed);
    }
    
    protected function buildSelectExpression($parsed) {
        $builder = new SelectExpressionBuilder();
        return $builder->build($parsed);
    }

    protected function buildSelectBracketExpression($parsed) {
        $builder = new SelectBracketExpressionBuilder();
        return $builder->build($parsed);
    }
    
    protected function buildSubQuery($parsed) {
        $builder = new SubQueryBuilder();
        return $builder->build($parsed);
    }
    
    public function build(array $parsed) {
        if (($parsed['expr_type'] !== ExpressionType::AGGREGATE_FUNCTION)
            && ($parsed['expr_type'] !== ExpressionType::SIMPLE_FUNCTION)
            && ($parsed['expr_type'] !== ExpressionType::CUSTOM_FUNCTION)) {
            return "";
        }

        if ($parsed['sub_tree'] === false) {
            return $parsed['base_expr'] . "()" . $this->buildAlias($parsed);
        }

        $sql = "";
        foreach ($parsed['sub_tree'] as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->build($v);
            $sql .= $this->buildConstant($v);
            $sql .= $this->buildSubQuery($v);
            $sql .= $this->buildColRef($v);
            $sql .= $this->buildReserved($v);
            $sql .= $this->buildSelectBracketExpression($v);
            $sql .= $this->buildSelectExpression($v);

            if ($len == strlen($sql)) {
                throw new UnableToCreateSQLException('function subtree', $k, $v, 'expr_type');
            }

            $sql .= ($this->isReserved($v) ? " " : ",");
        }
        return $parsed['base_expr'] . "(" . substr($sql, 0, -1) . ")" . $this->buildAlias($parsed);
    }

}
?>
