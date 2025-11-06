<?php

declare(strict_types=1);

namespace MaxMind\Exception;

/**
 * This exception is thrown when the service requires permission to access.
 */
// phpcs:disable
class PermissionRequiredException extends InvalidRequestException {}
