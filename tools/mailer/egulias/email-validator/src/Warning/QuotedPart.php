<?php

namespace Egulias\EmailValidator\Warning;

class QuotedPart extends Warning
{
    public const CODE = 36;

    /**
     * @param scalar|null $prevToken
     * @param scalar|null $postToken
     */
    public function __construct($prevToken, $postToken)
    {
        $this->message = "Deprecated Quoted String found between $prevToken and $postToken";
    }
}
