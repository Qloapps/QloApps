<?php

namespace Lorisleiva\CronTranslator;

use Exception;

class TranslationFileMissingException extends Exception
{
    public function __construct($locale, $file)
    {
        parent::__construct("Failed to load the translation file [{$file}] from the locale [{$locale}].");
    }
}
