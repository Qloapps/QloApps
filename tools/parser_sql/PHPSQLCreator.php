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
 * @version   SVN: $Id$
 * 
 */

namespace PHPSQLParser;
use PHPSQLParser\exceptions\UnsupportedFeatureException;
use PHPSQLParser\builders\SelectStatementBuilder;
use PHPSQLParser\builders\DeleteStatementBuilder;
use PHPSQLParser\builders\TruncateStatementBuilder;
use PHPSQLParser\builders\UpdateStatementBuilder;
use PHPSQLParser\builders\InsertStatementBuilder;
use PHPSQLParser\builders\CreateStatementBuilder;
use PHPSQLParser\builders\DropStatementBuilder;
use PHPSQLParser\builders\RenameStatementBuilder;
use PHPSQLParser\builders\ReplaceStatementBuilder;
use PHPSQLParser\builders\ShowStatementBuilder;
use PHPSQLParser\builders\BracketStatementBuilder;
use PHPSQLParser\builders\UnionStatementBuilder;
use PHPSQLParser\builders\UnionAllStatementBuilder;
use PHPSQLParser\builders\AlterStatementBuilder;

/**
 * This class generates SQL from the output of the PHPSQLParser. 
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *  
 */
class PHPSQLCreator {

    public $created;

    public function __construct($parsed = false) {
        if ($parsed) {
            $this->create($parsed);
        }
    }

    public function create($parsed) {
        $k = key($parsed);
        switch ($k) {

        case 'UNION':
			$builder = new UnionStatementBuilder();
			$this->created = $builder->build($parsed);
			break;
        case 'UNION ALL':
            $builder = new UnionAllStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'SELECT':
            $builder = new SelectStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'INSERT':
            $builder = new InsertStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'REPLACE':
            $builder = new ReplaceStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'DELETE':
            $builder = new DeleteStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'TRUNCATE':
            $builder = new TruncateStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'UPDATE':
            $builder = new UpdateStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'RENAME':
            $builder = new RenameStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'SHOW':
            $builder = new ShowStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'CREATE':
            $builder = new CreateStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'BRACKET':
            $builder = new BracketStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'DROP':
            $builder = new DropStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        case 'ALTER':
            $builder = new AlterStatementBuilder();
            $this->created = $builder->build($parsed);
            break;
        default:
            throw new UnsupportedFeatureException($k);
            break;
        }
        return $this->created;
    }
}

?>
