<?php

declare(strict_types=1);

namespace GeoIp2\Record;

/**
 * Contains data for the subdivisions associated with an IP address.
 *
 * This record is returned by all location databases and services besides
 * Country.
 */
class Subdivision extends AbstractPlaceRecord
{
    /**
     * @var string|null This is a string up to three characters long
     *                  contain the subdivision portion of the ISO 3166-2 code. See
     *                  https://en.wikipedia.org/wiki/ISO_3166-2. This attribute is returned by all
     *                  location databases and services except Country.
     */
    public readonly ?string $isoCode;

    /**
     * @ignore
     *
     * @param array<string, mixed> $record
     * @param list<string>         $locales
     */
    public function __construct(array $record, array $locales = ['en'])
    {
        parent::__construct($record, $locales);

        $this->isoCode = $record['iso_code'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $js = parent::jsonSerialize();
        if ($this->isoCode !== null) {
            $js['iso_code'] = $this->isoCode;
        }

        return $js;
    }
}
