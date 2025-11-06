<?php

declare(strict_types=1);

namespace GeoIp2\Record;

/**
 * Contains data for the postal record associated with an IP address.
 *
 * This record is returned by all location databases and services besides
 * Country.
 */
class Postal implements \JsonSerializable
{
    /**
     * @var string|null The postal code of the location. Postal codes
     *                  are not available for all countries. In some countries, this will only
     *                  contain part of the postal code. This attribute is returned by all location
     *                  databases and services besides Country.
     */
    public readonly ?string $code;

    /**
     * @var int|null A value from 0-100 indicating MaxMind's
     *               confidence that the postal code is correct. This attribute is only
     *               available from the Insights service and the GeoIP2 Enterprise
     *               database.
     */
    public readonly ?int $confidence;

    /**
     * @ignore
     *
     * @param array<string, mixed> $record
     */
    public function __construct(array $record)
    {
        $this->code = $record['code'] ?? null;
        $this->confidence = $record['confidence'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = [];
        if ($this->code !== null) {
            $js['code'] = $this->code;
        }
        if ($this->confidence !== null) {
            $js['confidence'] = $this->confidence;
        }

        return $js;
    }
}
