<?php

declare(strict_types=1);

namespace GeoIp2\Exception;

/**
 * This class represents an error returned by MaxMind's GeoIP2
 * web service.
 */
class InvalidRequestException extends HttpException
{
    /**
     * The code returned by the MaxMind web service.
     */
    public string $error;

    public function __construct(
        string $message,
        string $error,
        int $httpStatus,
        string $uri,
        ?\Exception $previous = null
    ) {
        $this->error = $error;
        parent::__construct($message, $httpStatus, $uri, $previous);
    }
}
