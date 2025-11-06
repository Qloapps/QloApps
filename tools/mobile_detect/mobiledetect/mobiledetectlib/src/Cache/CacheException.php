<?php

namespace Detection\Cache;

use Psr\SimpleCache\InvalidArgumentException;

class CacheException extends \Exception
{
    public function __construct($message, $code = 0, ?\Throwable $previous = null)
    {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}
