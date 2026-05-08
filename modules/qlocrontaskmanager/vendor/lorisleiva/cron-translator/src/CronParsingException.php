<?php

namespace Lorisleiva\CronTranslator;

use Exception;

class CronParsingException extends Exception
{
    public function __construct($cron)
    {
        parent::__construct("Failed to parse the following CRON expression: {$cron}");
    }
}
