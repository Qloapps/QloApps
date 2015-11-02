<?php
/**
 * UnableToCreateSQLException.php
 *
 * This file implements the UnableToCreateSQLException class which is used within the
 * PHPSQLParser package.
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
 * This exception will occur within the PHPSQLCreator, if the creator can not find a
 * method, which can handle the current expr_type field. It could be an error within the parser
 * output or a special case has not been modelled within the creator. Please create an issue
 * in such a case.
 *  
 * @author arothe
 *
 */
class UnableToCreateSQLException extends Exception {

    protected $part;
    protected $partkey;
    protected $entry;
    protected $entrykey;

    public function __construct($part, $partkey, $entry, $entrykey) {
        $this->part = $part;
        $this->partkey = $partkey;
        $this->entry = $entry;
        $this->entrykey = $entrykey;
        parent::__construct("unknown [" . $entrykey . "] = " .$entry[$entrykey] . " in \"" . $part . "\" [" . $partkey . "] ", 15);
    }

    public function getEntry() {
        return $this->entry;
    }

    public function getEntryKey() {
        return $this->entrykey;
    }

    public function getSQLPart() {
        return $this->part;
    }

    public function getSQLPartKey() {
        return $this->partkey;
    }
}

?>
