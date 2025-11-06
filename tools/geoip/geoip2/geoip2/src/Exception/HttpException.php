<?php

declare(strict_types=1);

namespace GeoIp2\Exception;

/**
 *  This class represents an HTTP transport error.
 */
class HttpException extends GeoIp2Exception
{
    /**
     * The URI queried.
     */
    public string $uri;

    public function __construct(
        string $message,
        int $httpStatus,
        string $uri,
        ?\Exception $previous = null
    ) {
        $this->uri = $uri;
        parent::__construct($message, $httpStatus, $previous);
    }
}
