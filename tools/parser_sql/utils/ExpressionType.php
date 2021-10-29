<?php
/**
 * ExpressionType.php
 *
 * Defines all values, which are possible for the [expr_type] field 
 * within the parser output.
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

namespace PHPSQLParser\utils;

/**
 * This class defines all values, which are possible for the [expr_type] field 
 * within the parser output.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class ExpressionType {

    const USER_VARIABLE = 'user_variable';
    const SESSION_VARIABLE = 'session_variable';
    const GLOBAL_VARIABLE = 'global_variable';
    const LOCAL_VARIABLE = 'local_variable';

    const COLDEF = 'column-def';
    const COLREF = 'colref';
    const RESERVED = 'reserved';
    const CONSTANT = 'const';

    const AGGREGATE_FUNCTION = 'aggregate_function';
    const CUSTOM_FUNCTION = 'custom_function';

    const SIMPLE_FUNCTION = 'function';

    const EXPRESSION = 'expression';
    const BRACKET_EXPRESSION = 'bracket_expression';
    const TABLE_EXPRESSION = 'table_expression';

    const SUBQUERY = 'subquery';
    const IN_LIST = 'in-list';
    const OPERATOR = 'operator';
    const SIGN = 'sign';
    const RECORD = 'record';

    const MATCH_ARGUMENTS = 'match-arguments';
    const MATCH_MODE = 'match-mode';

    const ALIAS = 'alias';
    const POSITION = 'pos';

    const TEMPORARY_TABLE = 'temporary-table';
    const TABLE = 'table';
    const VIEW = 'view';
    const DATABASE = 'database';
    const SCHEMA = 'schema';

    const PROCEDURE = 'procedure';
    const ENGINE = 'engine';
    const USER = 'user';
    const DIRECTORY = 'directory';
    const UNION = 'union';
    const CHARSET = 'character-set';
    const COLLATE = 'collation';

    const LIKE = 'like';
    const CONSTRAINT = 'constraint';
    const PRIMARY_KEY = 'primary-key';
    const FOREIGN_KEY = 'foreign-key';
    const UNIQUE_IDX = 'unique-index';
    const INDEX = 'index';
    const FULLTEXT_IDX = 'fulltext-index';
    const SPATIAL_IDX = 'spatial-index';
    const INDEX_TYPE = 'index-type';
    const CHECK = 'check';
    const COLUMN_LIST = 'column-list';
    const INDEX_COLUMN = 'index-column';
    const INDEX_SIZE = 'index-size';
    const INDEX_PARSER = 'index-parser';
    const INDEX_ALGORITHM = 'index-algorithm';
    const INDEX_LOCK = 'index-lock';
    const REFERENCE = 'foreign-ref';

    const DATA_TYPE = 'data-type';
    const COLUMN_TYPE = 'column-type';
    const DEF_VALUE = 'default-value';
    const COMMENT = 'comment';
    
    const PARTITION = 'partition';
    const PARTITION_LIST = 'partition-list';
    const PARTITION_RANGE = 'partition-range';
    const PARTITION_HASH = 'partition-hash';
    const PARTITION_KEY = 'partition-key';
    const PARTITION_COUNT = 'partition-count';
    const PARTITION_DEF = 'partition-def';
    const PARTITION_VALUES = 'partition-values';
    const PARTITION_COMMENT = 'partition-comment';
    const PARTITION_INDEX_DIR = 'partition-index-dir';
    const PARTITION_DATA_DIR = 'partition-data-dir';
    const PARTITION_MAX_ROWS = 'partition-max-rows';
    const PARTITION_MIN_ROWS = 'partition-min-rows';
    const PARTITION_KEY_ALGORITHM = 'partition-key-algorithm';
    
    const SUBPARTITION = 'sub-partition';
    const SUBPARTITION_DEF = 'sub-partition-def';
    const SUBPARTITION_HASH = 'sub-partition-hash';
    const SUBPARTITION_KEY = 'sub-partition-key';
    const SUBPARTITION_COUNT = 'sub-partition-count';
    const SUBPARTITION_COMMENT = 'sub-partition-comment';
    const SUBPARTITION_INDEX_DIR = 'sub-partition-index-dir';
    const SUBPARTITION_DATA_DIR = 'sub-partition-data-dir';
    const SUBPARTITION_MAX_ROWS = 'sub-partition-max-rows';
    const SUBPARTITION_MIN_ROWS = 'sub-partition-min-rows';
    const SUBPARTITION_KEY_ALGORITHM = 'sub-partition-key-algorithm';
    
    const QUERY = 'query';
    const SUBQUERY_FACTORING = 'subquery-factoring';
}
?>
