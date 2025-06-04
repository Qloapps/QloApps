<?php

namespace Egulias\EmailValidator\Result\Reason;

class NoDNSRecord implements Reason 
{
    public function code() : int
    {
        return 5;
    }

    public function description() : string
    {
        return 'No MX or A DSN record was found for this email';
    }
}
