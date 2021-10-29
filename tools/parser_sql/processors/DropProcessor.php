<?php
/**
 * DropProcessor.php
 *
 * This file implements the processor for the DROP statements.
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
use PHPSQLParser\utils\ExpressionType;

/**
 * This class processes the DROP statements.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class DropProcessor extends AbstractProcessor {

    public function process($tokenList) {
        $exists = false;
        $base_expr = '';
        $objectType = '';
        $subTree = array();
        $option = false;

        foreach ($tokenList as $token) {
            $base_expr .= $token;
            $trim = trim($token);

            if ($trim === '') {
                continue;
            }

            $upper = strtoupper($trim);
            switch ($upper) {
            case 'VIEW':
            case 'SCHEMA':
            case 'DATABASE':
            case 'TABLE':
                if ($objectType === '') {
                    $objectType = constant('PHPSQLParser\utils\ExpressionType::' . $upper);
                }
                $base_expr = '';
                break;
            case 'INDEX':
	            if ( $objectType === '' ) {
		            $objectType = constant( 'PHPSQLParser\utils\ExpressionType::' . $upper );
	            }
	            $base_expr = '';
	            break;
            case 'IF':
            case 'EXISTS':
                $exists = true;
                $base_expr = '';
                break;

            case 'TEMPORARY':
                $objectType = ExpressionType::TEMPORARY_TABLE;
                $base_expr = '';
                break;

            case 'RESTRICT':
            case 'CASCADE':
                $option = $upper;
                if (!empty($objectList)) {
                    $subTree[] = array('expr_type' => ExpressionType::EXPRESSION,
                                       'base_expr' => trim(substr($base_expr, 0, -strlen($token))),
                                       'sub_tree' => $objectList);
                    $objectList = array();
                }
                $base_expr = '';
                break;

            case ',':
                $last = array_pop($objectList);
                $last['delim'] = $trim;
                $objectList[] = $last;
                continue 2;

            default:
                $object = array();
                $object['expr_type'] = $objectType;
                if ($objectType === ExpressionType::TABLE || $objectType === ExpressionType::TEMPORARY_TABLE) {
                    $object['table'] = $trim;
                    $object['no_quotes'] = false;
                    $object['alias'] = false;
                }
                $object['base_expr'] = $trim;
                $object['no_quotes'] = $this->revokeQuotation($trim);
                $object['delim'] = false;

                $objectList[] = $object;
                continue 2;
            }

            $subTree[] = array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $trim);
        }

        if (!empty($objectList)) {
            $subTree[] = array('expr_type' => ExpressionType::EXPRESSION, 'base_expr' => trim($base_expr),
                               'sub_tree' => $objectList);
        }

        return array('expr_type' => $objectType, 'option' => $option, 'if-exists' => $exists, 'sub_tree' => $subTree);
    }
}
?>
