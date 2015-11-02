<?php
/**
 * PHPSQLCreator.php
 *
 * A creator, which generates SQL from the output of PHPSQLParser.
 *
 * PHP version 5
 *
 * LICENSE:
 * Copyright (c) 2010-2014 André Rothe
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
 * @copyright 2010-2014 André Rothe
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id: PHPSQLCreator.php 790 2013-12-17 12:16:48Z phosco@gmx.de $
 * 
 */

require_once dirname(__FILE__) . '/exceptions/UnsupportedFeatureException.php';
require_once dirname(__FILE__) . '/builders/SelectStatementBuilder.php';
require_once dirname(__FILE__) . '/builders/DeleteStatementBuilder.php';
require_once dirname(__FILE__) . '/builders/UpdateStatementBuilder.php';
require_once dirname(__FILE__) . '/builders/InsertStatementBuilder.php';
require_once dirname(__FILE__) . '/builders/CreateStatementBuilder.php';
require_once dirname(__FILE__) . '/builders/ShowStatementBuilder.php';

/**
 * This class generates SQL from the output of the PHPSQLParser. 
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class PHPSQLCreator {

    public function __construct($parsed = false) {
        if ($parsed) {
            $this->create($parsed);
        }
    }

    public function create($parsed) {
        $k = key($parsed);
        switch ($k) {

        case "UNION":
        case "UNION ALL":
            throw new UnsupportedFeatureException($k);
            break;
        case "SELECT":
            $builder = new SelectStatementBuilder($parsed);
            $this->created = $builder->build($parsed);
            break;
        case "INSERT":
            $builder = new InsertStatementBuilder($parsed);
            $this->created = $builder->build($parsed);
            break;
        case "DELETE":
            $builder = new DeleteStatementBuilder($parsed);
            $this->created = $builder->build($parsed);
            break;
        case "UPDATE":
            $builder = new UpdateStatementBuilder($parsed);
            $this->created = $builder->build($parsed);
            break;
        case "RENAME":
            $this->created = $this->processRenameTableStatement($parsed);
            break;
        case "SHOW":
            $builder = new ShowStatementBuilder($parsed);
            $this->created = $builder->build($parsed);
            break;
        case "CREATE":
            $builder = new CreateStatementBuilder($parsed);
            $this->created = $builder->build($parsed);
            break;
        default:
            throw new UnsupportedFeatureException($k);
            break;
        }
        return $this->created;
    }

    // TODO: we should change that, there are multiple "rename objects" as
    // table, user, database
    protected function processRenameTableStatement($parsed) {
        $rename = $parsed['RENAME'];
        $sql = "";
        foreach ($rename as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->processSourceAndDestTable($v);

            if ($len == strlen($sql)) {
                throw new UnableToCreateSQLException('RENAME', $k, $v, 'expr_type');
            }

            $sql .= ",";
        }
        $sql = substr($sql, 0, -1);
        return "RENAME TABLE " . $sql;
    }

    protected function processSourceAndDestTable($v) {
        if (!isset($v['source']) || !isset($v['destination'])) {
            return "";
        }
        return $v['source']['base_expr'] . " TO " . $v['destination']['base_expr'];
    }
}
?>
